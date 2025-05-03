<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Codeigniter\Database\Config;

class InstallController extends BaseController
{
    protected $session;
    protected $validation;
    protected $installer_version = '1.0.0';
    protected $db_connection = false;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url', 'filesystem']);
        
        // Controllo se siamo in una rotta di installazione
        $uri = service('uri');
        $currentPath = $uri->getPath();
        
        // Se il sistema è già installato e si tenta di accedere a una rotta di installazione (tranne complete)
        if ($this->isInstalled() && strpos($currentPath, 'install') === 0 && $currentPath != 'install/complete') {
            header('Location: ' . base_url());
            exit;
        }
    }
    
    /**
     * Verifica se l'applicazione è già stata installata
     *
     * @return bool
     */
    private function isInstalled()
    {
        return file_exists(WRITEPATH . 'installed.txt');
    }
    
    public function index()
    {
        // Verifica se è già installato
        if ($this->isInstalled()) {
            return redirect()->to(base_url());
        }

        return view('install/welcome', [
            'title' => 'Installazione Applicazione',
            'version' => $this->installer_version
        ]);
    }

    public function requirements()
    {
        if ($this->isInstalled()) {
            return redirect()->to(base_url());
        }

        // Verifica requisiti PHP
        $requirements = [
            'php_version' => version_compare(PHP_VERSION, '7.4.0', '>='),
            'curl' => extension_loaded('curl'),
            'intl' => extension_loaded('intl'),
            'json' => extension_loaded('json'),
            'mbstring' => extension_loaded('mbstring'),
            'xml' => extension_loaded('xml'),
            'writable_env' => is_really_writable(ROOTPATH . '.env'),
            'writable_writepath' => is_really_writable(WRITEPATH),
            'writable_uploadpath' => is_really_writable(ROOTPATH . 'public/uploads'),
        ];

        // Verifica se tutti i requisiti sono soddisfatti
        $requirements_satisfied = !in_array(false, $requirements);

        return view('install/requirements', [
            'title' => 'Verifica Requisiti',
            'requirements' => $requirements,
            'requirements_satisfied' => $requirements_satisfied
        ]);
    }

    public function database()
    {
        $request = \Config\Services::request();
        if ($this->isInstalled()) {
            return redirect()->to(base_url());
        }

        if ($request->getMethod() == 'POST') {
            $rules = [
                'hostname' => 'required',
                'username' => 'required',
                'database' => 'required',
                'port' => 'required|numeric'
            ];

            if ($this->validate($rules)) {
                // Salva i parametri di connessione nella sessione
                $dbConfig = [
                    'hostname' => $request->getVar('hostname'),
                    'username' => $request->getVar('username'),
                    'password' => $request->getVar('password'),
                    'database' => $request->getVar('database'),
                    'DBDriver' => 'MySQLi',
                    'port' => (int)($request->getVar('port') ?? 3306),
                    'prefix' => $request->getVar('prefix') ?? '',
                    'charset' => 'utf8',
                    'DBCollat' => 'utf8_general_ci'
                ];

                // Tenta la connessione al server database (senza specificare il database)
                try {
                    // Primo tentativo: connessione al server MySQL senza specificare il database
                    $dbServer = [
                        'hostname' => $dbConfig['hostname'],
                        'username' => $dbConfig['username'],
                        'password' => $dbConfig['password'],
                        'database' => '',
                        'DBDriver' => 'MySQLi',
                        'port'     => $dbConfig['port'],
                        'DBPrefix' => $dbConfig['prefix'],
                        'charset'  => 'utf8',
                        'DBCollat' => 'utf8_general_ci'
                    ];
                    
                    $db = \Config\Database::connect($dbServer);
                    $db->initialize();
                    
                    if ($db->connID) {
                        // Verifica se il database esiste
                        $dbName = $dbConfig['database'];
                        $query = $db->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbName'");
                        
                        if ($query->getNumRows() === 0) {
                            // Il database non esiste, proviamo a crearlo
                            try {
                                $db->query("CREATE DATABASE `$dbName` CHARACTER SET utf8 COLLATE utf8_general_ci");
                                // Registriamo nel log il successo
                                log_message('info', "Database '$dbName' creato con successo.");
                            } catch (\Exception $e) {
                                return view('install/database', [
                                    'title' => 'Configurazione Database',
                                    'error' => "Impossibile creare il database '$dbName'. Errore: " . $e->getMessage(),
                                    'validation' => $this->validator
                                ]);
                            }
                        }
                        
                        // Aggiorna il file .env
                        $this->updateEnvFile($dbConfig);
                        $this->session->set('db_config', $dbConfig);
                        
                        // Ora tentiamo di connetterci al database appena creato/esistente
                        try {
                            $db = \Config\Database::connect($dbConfig);
                            $db->initialize();
                            
                            if ($db->connID) {
                                return redirect()->to(site_url('install/migrate'));
                            }
                        } catch (\Exception $e) {
                            return view('install/database', [
                                'title' => 'Configurazione Database',
                                'error' => "Connessione al database fallita: " . $e->getMessage(),
                                'validation' => $this->validator
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    return view('install/database', [
                        'title' => 'Configurazione Database',
                        'error' => "Connessione al server database fallita: " . $e->getMessage(),
                        'validation' => $this->validator
                    ]);
                }
            }

            return view('install/database', [
                'title' => 'Configurazione Database',
                'validation' => $this->validator
            ]);
        }

        return view('install/database', [
            'title' => 'Configurazione Database'
        ]);
    }

    public function migrate()
    {
        $request = \Config\Services::request();
        if ($this->isInstalled()) {
            return redirect()->to(base_url());
        }

        $dbConfig = $this->session->get('db_config');

        if (!$dbConfig) {
            return redirect()->to(site_url('install/database'));
        }

        if ($request->getMethod() == 'POST') {
            // Esegui migrazione
            try {
                // Aggiorna il file .env con le impostazioni del database
                $this->updateEnvFile($dbConfig);

                sleep(1); // Attendi 1 secondo per dare tempo al sistema di rilevare le modifiche

                // \Config\Services::reset(true); // Reset dei servizi

                // Esegui le migrazioni
                $migrate = \Config\Services::migrations();
                $migrate->setNamespace(null)->latest();

                // Esegui i seeder iniziali
                $seeder = \Config\Database::seeder();
                $seeder->call('ImpostazioniSeeder');
                $seeder->call('CondizioniPagamentoSeeder');

                // Crea l'account amministratore
                $adminData = [
                    'username' => $this->request->getPost('admin_username'),
                    'email' => $this->request->getPost('admin_email'),
                    'password' => password_hash($this->request->getPost('admin_password'), PASSWORD_DEFAULT),
                    'created_at' => date('Y-m-d H:i:s'),
                    'ruolo' => 'admin'
                ];

                $db = \Config\Database::connect();
                $db->table('utenti')->insert($adminData);

                // Verifica se l'utente ha richiesto i dati demo
                if ($this->request->getPost('insert_demo_data')) {
                    // Carica i seeder demo
                    $seeder->call('DemoSeeder');
                }

                // Crea il file di installazione completata
                $data = [
                    'app_version' => $this->installer_version,
                    'installed_date' => date('Y-m-d H:i:s')
                ];

                write_file(WRITEPATH . 'installed.txt', json_encode($data));

                return redirect()->to(site_url('install/complete'));
            } catch (\Exception $e) {
                return view('install/migrate', [
                    'title' => 'Migrazione Database',
                    'error' => $e->getMessage()
                ]);
            }
        }

        return view('install/migrate', [
            'title' => 'Migrazione Database'
        ]);
    }

    public function complete()
    {
        if (!$this->isInstalled()) {
            return redirect()->to(site_url('install'));
        }

        return view('install/complete', [
            'title' => 'Installazione Completata'
        ]);
    }

    public function checkInstallation()
    {
        // Prepara i dati per la risposta
        $data = [
            'installed' => $this->isInstalled(),
            'installer_version' => $this->installer_version,
            'php_version' => PHP_VERSION,
            'time' => date('Y-m-d H:i:s'),
            'install_file_path' => WRITEPATH . 'installed.txt',
            'writepath_writable' => is_really_writable(WRITEPATH),
            'writepath_exists' => is_dir(WRITEPATH) ? 'SI' : 'NO',
            'env_writable' => is_really_writable(ROOTPATH . '.env')
        ];
        
        // Se è una richiesta AJAX, restituisci JSON
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($data);
        }
        
        // Altrimenti mostra una pagina HTML
        $html = '<html><head><title>Stato Installazione</title></head><body>';
        $html .= '<h1>Stato dell\'installazione</h1>';
        $html .= '<ul>';
        foreach ($data as $key => $value) {
            $html .= '<li><strong>' . $key . ':</strong> ' . (is_bool($value) ? ($value ? 'SI' : 'NO') : $value) . '</li>';
        }
        $html .= '</ul>';
        
        if (!$this->isInstalled()) {
            $html .= '<p><a href="' . site_url('install') . '">Vai alla pagina di installazione</a></p>';
        } else {
            $html .= '<p><a href="' . site_url() . '">Vai alla home</a></p>';
        }
        
        $html .= '</body></html>';
        
        return $this->response->setBody($html);
    }

    private function updateEnvFile($dbConfig)
    {
        $envFile = ROOTPATH . '.env';
        $envContent = file_get_contents($envFile);

        // Array per tenere traccia di quali chiavi sono state aggiornate
        $updatedKeys = [];

        // Definisci le coppie chiave-valore
        $dbSettings = [
            'database.default.hostname' => $dbConfig['hostname'],
            'database.default.database' => $dbConfig['database'],
            'database.default.username' => $dbConfig['username'],
            'database.default.password' => $dbConfig['password'],
            'database.default.DBPrefix' => $dbConfig['prefix'],
            'database.default.port' => $dbConfig['port']
        ];

        // Dividi il file in linee
        $lines = explode("\n", $envContent);
        $newLines = [];
        
        // Flag per verificare se CI_ENVIRONMENT = production è stato trovato o gestito
        $productionEnvHandled = false;

        // Esamina ogni linea e aggiorna quelle esistenti
        foreach ($lines as $line) {
            $updated = false;

            foreach ($dbSettings as $key => $value) {
                // Verifica se la linea contiene la chiave di configurazione, anche se commentata
                if (preg_match('/^\s*#?\s*' . preg_quote($key, '/') . '\s*=/', $line)) {
                    $newLines[] = "$key = $value";
                    $updatedKeys[] = $key;
                    $updated = true;
                    break;
                }
            }

            // Gestisci le righe CI_ENVIRONMENT
            if (!$updated) {
                // Se trova la riga CI_ENVIRONMENT = development, commentala
                if (preg_match('/^\s*CI_ENVIRONMENT\s*=\s*development\s*$/', $line)) {
                    $newLines[] = '# ' . $line;  // Commenta la linea
                    $updated = true;
                }
                // Se trova la riga CI_ENVIRONMENT = production, usa quella
                else if (preg_match('/^\s*CI_ENVIRONMENT\s*=\s*production\s*$/', $line)) {
                    $newLines[] = 'CI_ENVIRONMENT = production';
                    $productionEnvHandled = true;
                    $updated = true;
                }
                // Se trova la riga # CI_ENVIRONMENT = production, decommentala
                else if (preg_match('/^\s*#\s*CI_ENVIRONMENT\s*=\s*production\s*$/', $line)) {
                    $newLines[] = 'CI_ENVIRONMENT = production';
                    $productionEnvHandled = true;
                    $updated = true;
                }
                // Per altre righe CI_ENVIRONMENT, non aggiungerle (verranno sostituite)
                else if (preg_match('/^\s*CI_ENVIRONMENT\s*=/', $line)) {
                    $updated = true;  // Marcala come gestita ma non aggiungerla
                }
                // Mantieni le altre righe commentate di CI_ENVIRONMENT
                else if (preg_match('/^\s*#\s*CI_ENVIRONMENT\s*=/', $line)) {
                    $newLines[] = $line;
                    $updated = true;
                }
                // Tutte le altre righe, mantienile invariate
                else {
                    $newLines[] = $line;
                }
            }
        }

        // Aggiungi le chiavi che non sono state trovate/aggiornate
        if (count($updatedKeys) < count($dbSettings)) {
            $newLines[] = "\n# Database";
            foreach ($dbSettings as $key => $value) {
                if (!in_array($key, $updatedKeys)) {
                    $newLines[] = "$key = $value";
                }
            }
        }

        // Aggiungi la linea CI_ENVIRONMENT = production se non è stata gestita
        if (!$productionEnvHandled) {
            $newLines[] = "\nCI_ENVIRONMENT = production";
        }

        // Ricostruisci il contenuto del file
        $newEnvContent = implode("\n", $newLines);

        // Salva il nuovo contenuto nel file .env
        file_put_contents($envFile, $newEnvContent);
    }
}
