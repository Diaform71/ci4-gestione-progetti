<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\ProgettoModel;
use App\Models\AnagraficaModel;
use App\Models\UtentiModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

final class ProgettiController extends BaseController
{
    protected $progettoModel;
    protected $anagraficaModel;
    protected $utentiModel;
    protected $db;
    
    public function __construct()
    {
        $this->progettoModel = new ProgettoModel();
        $this->anagraficaModel = new AnagraficaModel();
        $this->utentiModel = new UtentiModel();
        $this->db = \Config\Database::connect();

        helper('progetto_helper');
        // Caricamento dell'helper text per utilizzare character_limiter()
        helper('text');
    }
    
    /**
     * Mostra la lista dei progetti
     */
    public function index()
    {
        $mostraDisattivati = $this->request->getGet('mostra_disattivati') ?? '0';
        
        $data = [
            'title' => 'Progetti',
            'progetti' => $mostraDisattivati === '1' 
                ? $this->progettoModel->getAllProjects()
                : $this->progettoModel->getActiveProjects(),
            'mostraDisattivati' => $mostraDisattivati
        ];
        
        return view('progetti/index', $data);
    }
    
    /**
     * Mostra i dettagli di un progetto specifico
     */
    public function show($id = null)
    {
        $progetto = $this->progettoModel->getProgettoWithRelations((int)$id);
        
        if (empty($progetto)) {
            throw new PageNotFoundException('Progetto non trovato');
        }
        
        // Carica i documenti del progetto
        $documentoModel = new \App\Models\DocumentoModel();
        $documenti = $documentoModel->getDocumentiByProgetto((int)$id);
        
        // Carica i materiali del progetto
        $progettoMaterialeModel = new \App\Models\ProgettoMaterialeModel();
        $materiali = $progettoMaterialeModel->getMaterialiByProgetto((int)$id);
        
        $data = [
            'title' => 'Dettagli Progetto',
            'progetto' => $progetto,
            'documenti' => $documenti,
            'materiali' => $materiali,
        ];
        
        return view('progetti/show', $data);
    }
    
    /**
     * Mostra il form per creare un nuovo progetto
     */
    public function new()
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per creare un progetto.');
            return redirect()->to('/login');
        }
        
        $data = [
            'title' => 'Nuovo Progetto',
            'anagrafiche' => $this->anagraficaModel->getActiveAnagrafiche(),
            'utenti' => $this->utentiModel->where('attivo', 1)->findAll(),
            'progetti_disponibili' => $this->progettoModel->where('attivo', 1)->findAll(), // Per selezionare il progetto padre
        ];
        
        return view('progetti/create', $data);
    }
    
    /**
     * Crea un nuovo progetto
     */
    public function create()
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per creare un progetto.');
            return redirect()->to('/login');
        }
        
        // Imposta l'utente corrente come creatore del progetto
        $idUtente = $this->request->getPost('id_creato_da');
        
        // Se non è stato passato dal form, prova a prenderlo dalla sessione
        if (empty($idUtente)) {
            $idUtente = $session->get('utente_id');
            
            // Se l'utente non è in sessione, imposta un valore predefinito (1 - admin)
            if (empty($idUtente)) {
                $idUtente = 1; // Assumiamo che l'ID 1 sia l'amministratore di sistema
            }
        }
        
        // Dati del form
        $data = [
            'nome' => $this->request->getPost('nome'),
            'descrizione' => $this->request->getPost('descrizione'),
            'fase_kanban' => $this->request->getPost('fase_kanban') ?: 'backlog',
            'id_anagrafica' => $this->request->getPost('id_anagrafica') ? (int)$this->request->getPost('id_anagrafica') : null,
            'data_inizio' => $this->request->getPost('data_inizio') ?: null,
            'data_scadenza' => $this->request->getPost('data_scadenza') ?: null,
            'id_creato_da' => $idUtente,
            'id_responsabile' => $this->request->getPost('id_responsabile') ? (int)$this->request->getPost('id_responsabile') : null,
            'priorita' => $this->request->getPost('priorita') ?: 'media',
            'stato' => $this->request->getPost('stato') ?: 'in_corso',
            'budget' => $this->request->getPost('budget') ? (float)$this->request->getPost('budget') : null,
            'attivo' => 1,
            'id_progetto_padre' => $this->request->getPost('id_progetto_padre') ? (int)$this->request->getPost('id_progetto_padre') : null,
        ];
        
        // Validazione usando il modello per ottenere messaggi personalizzati
        if (!$this->progettoModel->validate($data)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->progettoModel->errors());
        }
        
        // Rimuovi i valori nulli
        $data = array_filter($data, function($value) {
            return $value !== null;
        });
        
        // Salva il progetto
        $this->progettoModel->insert($data);
        $idProgetto = $this->progettoModel->getInsertID();
        
        // Verifica se le notifiche email sono attive
        $impostazioniModel = new \App\Models\ImpostazioniModel();
        $notificheAttive = $impostazioniModel->getImpSistema('notifiche_email_attive', false);
        $notificaProgettoCreato = $impostazioniModel->getImpSistema('notifica_progetto_creato', false);
        
        // Se le notifiche sono attive e la notifica per la creazione progetto è attiva
        if ($notificheAttive && $notificaProgettoCreato) {
            // Carica il progetto con tutte le relazioni
            $progetto = $this->progettoModel->getProgettoWithRelations($idProgetto);
            
            if ($progetto) {
                // Determina i destinatari dell'email
                $destinatari = [];
                
                // Aggiungi il responsabile del progetto se esiste
                if (!empty($progetto['id_responsabile']) && !empty($progetto['responsabile']) && !empty($progetto['responsabile']['email'])) {
                    // Verifica le impostazioni personali del responsabile
                    $responsabileId = $progetto['id_responsabile'];
                    $notificheAttivoResponsabile = $impostazioniModel->getImpUtente('notifiche_email_attive', $responsabileId, $notificheAttive);
                    $notificaProgettoCreatpResponsabile = $impostazioniModel->getImpUtente('notifica_progetto_creato', $responsabileId, $notificaProgettoCreato);
                    
                    if ($notificheAttivoResponsabile && $notificaProgettoCreatpResponsabile) {
                        $destinatari[] = $progetto['responsabile']['email'];
                    }
                }
                
                // Aggiungi il creatore del progetto se esiste e non è già stato aggiunto
                if (!empty($progetto['id_creato_da']) && !empty($progetto['creatore']) && !empty($progetto['creatore']['email']) && 
                    $progetto['id_creato_da'] != $progetto['id_responsabile']) {
                    
                    // Verifica le impostazioni personali del creatore
                    $creatoreId = $progetto['id_creato_da'];
                    $notificheAttivoCreatore = $impostazioniModel->getImpUtente('notifiche_email_attive', $creatoreId, $notificheAttive);
                    $notificaProgettoCreatpCreatore = $impostazioniModel->getImpUtente('notifica_progetto_creato', $creatoreId, $notificaProgettoCreato);
                    
                    if ($notificheAttivoCreatore && $notificaProgettoCreatpCreatore) {
                        $destinatari[] = $progetto['creatore']['email'];
                    }
                }
                
                // Se ci sono destinatari validi
                if (!empty($destinatari)) {
                    // Carica l'helper per l'invio email
                    helper('CIMail');
                    
                    // Prepara l'oggetto dell'email
                    $oggetto = "Nuovo progetto creato: " . $progetto['nome'];
                    
                    // Prepara il corpo dell'email
                    $corpo = "<h2>Nuovo progetto creato</h2>";
                    $corpo .= "<p>È stato creato un nuovo progetto nel sistema:</p>";
                    $corpo .= "<ul>";
                    $corpo .= "<li><strong>Nome:</strong> " . $progetto['nome'] . "</li>";
                    $corpo .= "<li><strong>Descrizione:</strong> " . ($progetto['descrizione'] ?? 'Non specificata') . "</li>";
                    
                    // Aggiungi dettagli sulle date se disponibili
                    if (!empty($progetto['data_inizio'])) {
                        $corpo .= "<li><strong>Data inizio:</strong> " . date('d/m/Y', strtotime($progetto['data_inizio'])) . "</li>";
                    }
                    
                    if (!empty($progetto['data_scadenza'])) {
                        $corpo .= "<li><strong>Data scadenza:</strong> " . date('d/m/Y', strtotime($progetto['data_scadenza'])) . "</li>";
                    }
                    
                    $corpo .= "<li><strong>Priorità:</strong> " . ucfirst($progetto['priorita']) . "</li>";
                    $corpo .= "<li><strong>Stato:</strong> " . ucfirst(str_replace('_', ' ', $progetto['stato'])) . "</li>";
                    
                    // Aggiungi dettagli sul cliente se disponibili
                    if (!empty($progetto['anagrafica']['ragione_sociale'])) {
                        $corpo .= "<li><strong>Cliente:</strong> " . $progetto['anagrafica']['ragione_sociale'] . "</li>";
                    }
                    
                    // Aggiungi link al progetto
                    $corpo .= "</ul>";
                    $corpo .= "<p>Per visualizzare i dettagli del progetto, <a href='" . base_url("/progetti/{$idProgetto}") . "'>clicca qui</a>.</p>";
                    
                    // Ottieni i dati per il mittente (l'utente che ha creato il progetto)
                    $utente = $this->utentiModel->find($idUtente);
                    $from = [
                        'email' => $utente['email'] ?? '',
                        'name' => ($utente['nome'] ?? '') . ' ' . ($utente['cognome'] ?? '')
                    ];
                    
                    // Recupera configurazione SMTP
                    $smtpConfig = [
                        'host' => $impostazioniModel->getImpSistema('smtp_host'),
                        'port' => $impostazioniModel->getImpSistema('smtp_port'),
                        'user' => $impostazioniModel->getImpSistema('smtp_user'),
                        'pass' => $impostazioniModel->getImpSistema('smtp_pass'),
                        'from_email' => $impostazioniModel->getImpSistema('email_from'),
                        'from_name' => $impostazioniModel->getImpSistema('email_from_name')
                    ];
                    
                    // Invia l'email passando la configurazione SMTP
                    $risultato = send_email($destinatari, $oggetto, $corpo, $from, [], [], [], [], $smtpConfig);
                    
                    // Registra l'esito dell'invio nei log
                    if ($risultato['status']) {
                        log_message('info', "Email di notifica per nuovo progetto [{$idProgetto}] inviata con successo a: " . implode(', ', $destinatari));
                    } else {
                        log_message('error', "Errore nell'invio email di notifica per nuovo progetto [{$idProgetto}]: " . $risultato['msg']);
                    }
                } else {
                    log_message('warning', "Nessun destinatario valido trovato per la notifica del nuovo progetto [{$idProgetto}]");
                }
            } else {
                log_message('error', "Impossibile caricare i dettagli del progetto [{$idProgetto}] per l'invio della notifica");
            }
        }
        
        $session->setFlashdata('success', 'Progetto creato con successo.');
        return redirect()->to('/progetti/' . $idProgetto);
    }
    
    /**
     * Mostra il form per modificare un progetto
     */
    public function edit($id = null)
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per modificare un progetto.');
            return redirect()->to('/login');
        }
        
        // Converti l'ID in intero
        $id = (int)$id;
        
        $progetto = $this->progettoModel->find($id);
        
        if (empty($progetto)) {
            throw new PageNotFoundException('Progetto non trovato');
        }
        
        // Ottiene tutti i progetti attivi che possono essere usati come padre
        // Esclude:
        // 1. Il progetto corrente
        // 2. I sottoprogetti diretti del progetto corrente
        $sottoprogetti = $this->progettoModel->getSottoprogetti($id);
        $idsSottoprogetti = array_column($sottoprogetti, 'id');
        $idsEsclusi = array_merge([$id], $idsSottoprogetti);
        
        $progettiDisponibili = $this->progettoModel
            ->where('attivo', 1)
            ->whereNotIn('id', $idsEsclusi)
            ->orderBy('nome', 'ASC')
            ->findAll();
        
        // Debug per verificare i progetti disponibili
        log_message('debug', 'ID progetto: ' . $id);
        log_message('debug', 'IDs esclusi: ' . json_encode($idsEsclusi));
        log_message('debug', 'Progetti disponibili come padre: ' . json_encode($progettiDisponibili));
        
        $data = [
            'title' => 'Modifica Progetto',
            'progetto' => $progetto,
            'anagrafiche' => $this->anagraficaModel->getActiveAnagrafiche(),
            'utenti' => $this->utentiModel->where('attivo', 1)->findAll(),
            'progetti_disponibili' => $progettiDisponibili,
        ];
        
        return view('progetti/edit', $data);
    }
    
    /**
     * Aggiorna un progetto esistente
     */
    public function update($id = null)
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per modificare un progetto.');
            return redirect()->to('/login');
        }
        
        $progetto = $this->progettoModel->find($id);
        
        if (empty($progetto)) {
            throw new PageNotFoundException('Progetto non trovato');
        }
        
        // Dati del form
        $data = [
            'nome' => $this->request->getPost('nome'),
            'descrizione' => $this->request->getPost('descrizione'),
            'fase_kanban' => $this->request->getPost('fase_kanban'),
            'id_anagrafica' => $this->request->getPost('id_anagrafica') ? (int)$this->request->getPost('id_anagrafica') : null,
            'data_inizio' => $this->request->getPost('data_inizio'),
            'data_scadenza' => $this->request->getPost('data_scadenza'),
            'data_fine' => $this->request->getPost('data_fine'),
            'id_creato_da' => $this->request->getPost('id_creato_da'),
            'id_responsabile' => $this->request->getPost('id_responsabile') ? (int)$this->request->getPost('id_responsabile') : null,
            'priorita' => $this->request->getPost('priorita'),
            'stato' => $this->request->getPost('stato'),
            'budget' => $this->request->getPost('budget') ? (float)$this->request->getPost('budget') : null,
            'id_progetto_padre' => $this->request->getPost('id_progetto_padre') ? (int)$this->request->getPost('id_progetto_padre') : null,
        ];
        
        // Validazione usando il modello per ottenere messaggi personalizzati
        if (!$this->progettoModel->validate($data)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->progettoModel->errors());
        }
        
        // Gestione delle date
        foreach (['data_inizio', 'data_scadenza', 'data_fine'] as $dateField) {
            // Se il campo è vuoto o contiene una data non valida, impostiamo null
            if (empty($data[$dateField]) || $data[$dateField] === '0000-00-00' || !strtotime($data[$dateField])) {
                $data[$dateField] = null;
            }
        }
        
        // Aggiorna il progetto
        $this->progettoModel->update($id, $data);
        
        $session->setFlashdata('success', 'Progetto aggiornato con successo.');
        return redirect()->to('/progetti/' . $id);
    }
    
    /**
     * Aggiorna lo stato del progetto
     */
    public function updateStato($id = null)
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per modificare un progetto.');
            return redirect()->to('/login');
        }
        
        $progetto = $this->progettoModel->find($id);
        
        if (empty($progetto)) {
            throw new PageNotFoundException('Progetto non trovato');
        }
        
        $nuovoStato = $this->request->getPost('stato');
        
        if (in_array($nuovoStato, ['in_corso', 'completato', 'sospeso', 'annullato'])) {
            $data = ['stato' => $nuovoStato];
            
            // Se completato, imposta la data di fine
            if ($nuovoStato === 'completato' && empty($progetto['data_fine'])) {
                $data['data_fine'] = date('Y-m-d');
            }
            
            $this->progettoModel->update($id, $data);
            $session->setFlashdata('success', 'Stato del progetto aggiornato con successo.');
        } else {
            $session->setFlashdata('error', 'Stato del progetto non valido.');
        }
        
        return redirect()->back();
    }
    
    /**
     * Aggiorna la fase Kanban del progetto
     */
    public function updateFaseKanban($id = null)
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per modificare un progetto.');
            return redirect()->to('/login');
        }
        
        $progetto = $this->progettoModel->find($id);
        
        if (empty($progetto)) {
            throw new PageNotFoundException('Progetto non trovato');
        }
        
        $nuovaFase = $this->request->getPost('fase_kanban');
        
        if (!empty($nuovaFase)) {
            $this->progettoModel->update($id, ['fase_kanban' => $nuovaFase]);
            $session->setFlashdata('success', 'Fase Kanban del progetto aggiornata con successo.');
        } else {
            $session->setFlashdata('error', 'Fase Kanban non valida.');
        }
        
        return redirect()->back();
    }
    
    /**
     * Elimina un progetto (soft delete)
     */
    public function delete($id = null)
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per eliminare un progetto.');
            return redirect()->to('/login');
        }
        
        $progetto = $this->progettoModel->find($id);
        
        if (empty($progetto)) {
            throw new PageNotFoundException('Progetto non trovato');
        }
        
        $this->progettoModel->delete($id);
        
        $session->setFlashdata('success', 'Progetto eliminato con successo.');
        return redirect()->to('/progetti');
    }
    
    /**
     * Attiva/disattiva un progetto
     */
    public function toggleAttivo($id = null)
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per modificare un progetto.');
            return redirect()->to('/login');
        }
        
        $progetto = $this->progettoModel->find($id);
        
        if (empty($progetto)) {
            throw new PageNotFoundException('Progetto non trovato');
        }
        
        $nuovoStato = $progetto['attivo'] ? 0 : 1;
        $this->progettoModel->update($id, ['attivo' => $nuovoStato]);
        
        $messaggio = $nuovoStato ? 'Progetto attivato con successo.' : 'Progetto disattivato con successo.';
        $session->setFlashdata('success', $messaggio);
        
        return redirect()->back();
    }
    
    /**
     * Vista Kanban dei progetti
     */
    public function kanban()
    {
        // Ottieni le fasi Kanban disponibili (in questo caso usiamo un array predefinito, ma potrebbe essere una tabella nel DB)
        $fasiKanban = ['backlog', 'da_iniziare', 'in_corso', 'in_revisione', 'completato'];
        
        $progetti = [];
        
        // Raggruppa i progetti per fase Kanban
        foreach ($fasiKanban as $fase) {
            $progetti[$fase] = $this->progettoModel->getProjectsByFaseKanban($fase);
        }
        
        $data = [
            'title' => 'Progetti - Kanban',
            'fasiKanban' => $fasiKanban,
            'progetti' => $progetti,
        ];
        
        return view('progetti/kanban', $data);
    }
    
    /**
     * Mostra i progetti in scadenza
     */
    public function inScadenza($giorni = 7)
    {
        $data = [
            'title' => 'Progetti in Scadenza',
            'progetti' => $this->progettoModel->getProjectsInScadenza((int)$giorni),
            'giorni' => $giorni,
        ];
        
        return view('progetti/in_scadenza', $data);
    }
    
    /**
     * Mostra i progetti per anagrafica
     */
    public function perAnagrafica($idAnagrafica = null)
    {
        $anagrafica = $this->anagraficaModel->find($idAnagrafica);
        
        if (empty($anagrafica)) {
            throw new PageNotFoundException('Anagrafica non trovata');
        }
        
        $data = [
            'title' => 'Progetti per ' . $anagrafica['ragione_sociale'],
            'anagrafica' => $anagrafica,
            'progetti' => $this->progettoModel->getProjectsByAnagrafica((int)$idAnagrafica),
        ];
        
        return view('progetti/per_anagrafica', $data);
    }

    /**
     * Aggiunge un materiale esistente al progetto
     */
    public function aggiungiMateriale($id_progetto)
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per modificare un progetto.');
            return redirect()->to('/login');
        }
        
        // Verifica che il progetto esista
        $progetto = $this->progettoModel->find($id_progetto);
        if (empty($progetto)) {
            return redirect()->to('progetti')->with('error', 'Progetto non trovato');
        }

        // Validazione del form
        $rules = [
            'id_materiale' => 'required|numeric',
            'quantita' => 'required|numeric|greater_than[0]',
            'unita_misura' => 'permit_empty|max_length[20]',
            'note' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Dati non validi. Verifica i campi e riprova.');
        }

        $id_materiale = $this->request->getPost('id_materiale');
        $quantita = $this->request->getPost('quantita');
        $unita_misura = $this->request->getPost('unita_misura') ?: 'pz';
        $note = $this->request->getPost('note');

        // Verifica che il materiale esista
        $materialeModel = new \App\Models\Materiale();
        $materiale = $materialeModel->find($id_materiale);
        if (empty($materiale)) {
            return redirect()->back()->with('error', 'Materiale non trovato');
        }

        // Verifica se il materiale è già associato al progetto
        $progettoMaterialeModel = new \App\Models\ProgettoMaterialeModel();
        if ($progettoMaterialeModel->esisteMateriale((int)$id_progetto, (int)$id_materiale)) {
            return redirect()->back()->with('error', 'Questo materiale è già associato al progetto');
        }

        // Dati da inserire
        $data = [
            'id_progetto' => $id_progetto,
            'id_materiale' => $id_materiale,
            'quantita' => $quantita,
            'unita_misura' => $unita_misura,
            'note' => $note
        ];

        // Inserisci l'associazione
        if ($progettoMaterialeModel->insert($data)) {
            return redirect()->to("progetti/{$id_progetto}")->with('success', 'Materiale aggiunto con successo');
        } else {
            return redirect()->back()->with('error', 'Si è verificato un errore durante l\'aggiunta del materiale');
        }
    }

    /**
     * Aggiunge un nuovo materiale e lo associa al progetto
     */
    public function aggiungiNuovoMateriale($id_progetto)
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per modificare un progetto.');
            return redirect()->to('/login');
        }
        
        // Verifica che il progetto esista
        $progetto = $this->progettoModel->find($id_progetto);
        if (empty($progetto)) {
            return redirect()->to('progetti')->with('error', 'Progetto non trovato');
        }

        // Validazione del form per il nuovo materiale
        $rules = [
            'codice' => 'required|min_length[1]|max_length[50]|is_unique[materiali.codice]',
            'descrizione' => 'required',
            'quantita' => 'required|numeric|greater_than[0]',
            'unita_misura' => 'permit_empty|max_length[20]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Dati non validi. Verifica i campi e riprova.')
                             ->withInput()
                             ->with('errors', $this->validator->getErrors());
        }

        // Dati per il nuovo materiale
        $datiMateriale = [
            'codice' => $this->request->getPost('codice'),
            'descrizione' => $this->request->getPost('descrizione'),
            'materiale' => $this->request->getPost('materiale_tipo'),
            'produttore' => $this->request->getPost('produttore'),
            'commerciale' => $this->request->getPost('commerciale') ? 1 : 0,
            'meccanica' => $this->request->getPost('meccanica') ? 1 : 0,
            'elettrica' => $this->request->getPost('elettrica') ? 1 : 0,
            'pneumatica' => $this->request->getPost('pneumatica') ? 1 : 0,
            'in_produzione' => 1 // Nuovo materiale è in produzione di default
        ];

        // Salva il nuovo materiale
        $materialeModel = new \App\Models\Materiale();
        if (!$materialeModel->insert($datiMateriale)) {
            return redirect()->back()->with('error', 'Errore durante il salvataggio del nuovo materiale: ' . json_encode($materialeModel->errors()))->withInput();
        }

        // Recupera l'ID del materiale appena inserito
        $id_materiale = $materialeModel->getInsertID();

        // Dati per l'associazione al progetto
        $quantita = $this->request->getPost('quantita');
        $unita_misura = $this->request->getPost('unita_misura') ?: 'pz';
        $note = $this->request->getPost('note');

        // Inserimento nella tabella progetti_materiali
        $data = [
            'id_progetto' => $id_progetto,
            'id_materiale' => $id_materiale,
            'quantita' => $quantita,
            'unita_misura' => $unita_misura,
            'note' => $note
        ];

        $progettoMaterialeModel = new \App\Models\ProgettoMaterialeModel();
        if ($progettoMaterialeModel->insert($data)) {
            return redirect()->to("progetti/{$id_progetto}")->with('success', 'Nuovo materiale creato e aggiunto al progetto con successo');
        } else {
            return redirect()->back()->with('error', 'Materiale creato ma errore durante l\'associazione al progetto')->withInput();
        }
    }

    /**
     * Aggiorna un materiale associato a un progetto
     */
    public function aggiornaMateriale($id_progetto)
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per modificare un progetto.');
            return redirect()->to('/login');
        }
        
        // Verifica che il progetto esista
        $progetto = $this->progettoModel->find($id_progetto);
        if (empty($progetto)) {
            return redirect()->to('progetti')->with('error', 'Progetto non trovato');
        }

        // Validazione del form
        $rules = [
            'id' => 'required|numeric',
            'id_materiale' => 'required|numeric',
            'quantita' => 'required|numeric|greater_than[0]',
            'unita_misura' => 'permit_empty|max_length[20]',
            'note' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Dati non validi. Verifica i campi e riprova.');
        }

        $id = (int)$this->request->getPost('id');
        $id_materiale = (int)$this->request->getPost('id_materiale');
        $quantita = $this->request->getPost('quantita');
        $unita_misura = $this->request->getPost('unita_misura') ?: 'pz';
        $note = $this->request->getPost('note');

        // Log dei dati ricevuti
        log_message('debug', 'Dati modifica materiale - ID associazione: ' . $id . ' - ID materiale: ' . $id_materiale . ' - ID Progetto: ' . $id_progetto);
        log_message('debug', 'POST data: ' . json_encode($this->request->getPost()));
        
        // Verifica che il record esista
        $progettoMaterialeModel = new \App\Models\ProgettoMaterialeModel();
        
        // Cerca prima per ID associazione
        $record = $progettoMaterialeModel->withDeleted()->find($id);
        
        if (empty($record)) {
            log_message('debug', 'Record non trovato con ID: ' . $id);
            
            // Prova a cercare un record che abbia l'id_materiale e id_progetto specificati
            $recordAlt = $progettoMaterialeModel->withDeleted()
                ->where('id_materiale', $id_materiale)
                ->where('id_progetto', $id_progetto)
                ->first();
            
            if ($recordAlt) {
                log_message('debug', 'Trovato record alternativo con ID: ' . $recordAlt['id'] . ', usando questo record');
                $record = $recordAlt;
                $id = $recordAlt['id']; // Aggiorna l'ID per utilizzarlo nell'update
            } else {
                log_message('debug', 'Nessun record trovato nemmeno con filtri alternativi');
                return redirect()->back()->with('error', 'Record non trovato');
            }
        } else if ($record['id_progetto'] != $id_progetto) {
            log_message('debug', 'Record trovato ma id_progetto non corrisponde: ' . $record['id_progetto'] . ' vs ' . $id_progetto);
            return redirect()->back()->with('error', 'Record non autorizzato');
        }

        // Aggiorna il record
        $data = [
            'quantita' => $quantita,
            'unita_misura' => $unita_misura,
            'note' => $note,
            'deleted_at' => null // Ripristina il record se era stato eliminato
        ];

        if ($progettoMaterialeModel->update($id, $data)) {
            log_message('debug', 'Materiale aggiornato con successo: ID ' . $id);
            return redirect()->to("progetti/{$id_progetto}")->with('success', 'Materiale aggiornato con successo');
        } else {
            log_message('error', 'Errore durante l\'aggiornamento del materiale: ' . json_encode($progettoMaterialeModel->errors()));
            return redirect()->back()->with('error', 'Si è verificato un errore durante l\'aggiornamento del materiale');
        }
    }

    /**
     * Rimuove un materiale dal progetto
     */
    public function rimuoviMateriale($id_progetto, $id_record)
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per modificare un progetto.');
            return redirect()->to('/login');
        }
        
        // Verifica che il progetto esista
        $progetto = $this->progettoModel->find($id_progetto);
        if (empty($progetto)) {
            return redirect()->to('progetti')->with('error', 'Progetto non trovato');
        }

        // Verifica che il record esista
        $progettoMaterialeModel = new \App\Models\ProgettoMaterialeModel();
        $record = $progettoMaterialeModel->withDeleted()->find($id_record);
        if (empty($record) || $record['id_progetto'] != $id_progetto) {
            log_message('debug', 'Errore rimozione materiale - ID: ' . $id_record . ' - ID Progetto: ' . $id_progetto);
            return redirect()->back()->with('error', 'Record non trovato');
        }

        // Elimina il record (soft delete)
        if ($progettoMaterialeModel->delete($id_record)) {
            return redirect()->to("progetti/{$id_progetto}")->with('success', 'Materiale rimosso con successo');
        } else {
            return redirect()->back()->with('error', 'Si è verificato un errore durante la rimozione del materiale');
        }
    }

    /**
     * Analizza il file Excel caricato e restituisce la lista dei fogli
     */
    public function analizzaExcel()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Richiesta non valida']);
        }

        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'File non valido']);
        }

        try {
            // Crea una directory temporanea se non esiste
            $tempDir = WRITEPATH . 'temp/excel';
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0777, true);
            }

            // Genera un nome file univoco
            $tempFile = $tempDir . '/' . uniqid('excel_') . '.xlsx';
            
            // Sposta il file caricato nella directory temporanea
            $file->move($tempDir, basename($tempFile));

            // Leggi i fogli dal file
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($tempFile);
            $sheets = $spreadsheet->getSheetNames();

            // Salva il percorso del file temporaneo nella sessione
            $session = session();
            $session->set('excel_temp_file', $tempFile);

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'sheets' => $sheets
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', '[Importazione Excel] Errore durante l\'analisi del file: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Errore durante l\'analisi del file']);
        }
    }

    /**
     * Analizza il foglio selezionato e restituisce le intestazioni delle colonne
     */
    public function analizzaFoglio()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Richiesta non valida']);
        }

        $foglio = $this->request->getPost('foglio');
        if (!$foglio) {
            return $this->response->setJSON(['success' => false, 'message' => 'Foglio non specificato']);
        }

        // Recupera il percorso del file temporaneo dalla sessione
        $session = session();
        $tempFile = $session->get('excel_temp_file');
        
        if (!$tempFile || !file_exists($tempFile)) {
            return $this->response->setJSON(['success' => false, 'message' => 'File non trovato. Ricarica il file.']);
        }

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($tempFile);
            $worksheet = $spreadsheet->getSheetByName($foglio);
            
            if (!$worksheet) {
                return $this->response->setJSON(['success' => false, 'message' => 'Foglio non trovato']);
            }

            // Leggi la prima riga per ottenere le intestazioni
            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
            
            $colonne = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $value = $worksheet->getCell([$col, 1])->getValue();
                $colonne[] = $value ?: "Colonna $col";
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'colonne' => $colonne
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', '[Importazione Excel] Errore durante l\'analisi del foglio: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Errore durante l\'analisi del foglio']);
        }
    }

    /**
     * Genera l'anteprima dei dati da importare
     */
    public function anteprimaImportazione()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Richiesta non valida']);
        }

        $mappatura = $this->request->getPost('mappatura');
        $rigaInizio = (int)$this->request->getPost('riga_inizio');
        
        if (!$mappatura || !$rigaInizio) {
            return $this->response->setJSON(['success' => false, 'message' => 'Parametri mancanti']);
        }

        // Recupera il percorso del file temporaneo dalla sessione
        $session = session();
        $tempFile = $session->get('excel_temp_file');
        
        if (!$tempFile || !file_exists($tempFile)) {
            return $this->response->setJSON(['success' => false, 'message' => 'File non trovato. Ricarica il file.']);
        }

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($tempFile);
            $worksheet = $spreadsheet->getActiveSheet();
            
            $highestRow = $worksheet->getHighestRow();
            $dati = [];

            // Leggi le righe a partire dalla riga specificata
            for ($row = $rigaInizio; $row <= $highestRow; $row++) {
                $riga = [
                    'codice' => $worksheet->getCell([$mappatura['codice'] + 1, $row])->getValue(),
                    'descrizione' => $worksheet->getCell([$mappatura['descrizione'] + 1, $row])->getValue(),
                    'materiale' => $mappatura['materiale'] ? $worksheet->getCell([$mappatura['materiale'] + 1, $row])->getValue() : null,
                    'produttore' => $mappatura['produttore'] ? $worksheet->getCell([$mappatura['produttore'] + 1, $row])->getValue() : null,
                    'quantita' => $worksheet->getCell([$mappatura['quantita'] + 1, $row])->getValue(),
                    'unita_misura' => $mappatura['unita_misura'] ? $worksheet->getCell([$mappatura['unita_misura'] + 1, $row])->getValue() : null
                ];

                // Salta le righe vuote
                if (empty($riga['codice']) && empty($riga['descrizione'])) {
                    continue;
                }

                $dati[] = $riga;
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $dati
            ]);
        } catch (\Exception $e) {
            log_message('error', '[Importazione Excel] Errore durante la generazione dell\'anteprima: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Errore durante la generazione dell\'anteprima']);
        }
    }

    /**
     * Importa i materiali dal file Excel
     */
    public function importaMaterialiExcel($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Richiesta non valida']);
        }

        $progetto = $this->progettoModel->find($id);
        if (!$progetto) {
            return $this->response->setJSON(['success' => false, 'message' => 'Progetto non trovato']);
        }

        $dati = $this->request->getPost('dati');
        $skipDuplicati = (bool)$this->request->getPost('skip_duplicati');

        if (!$dati || !is_array($dati)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Dati non validi']);
        }

        $importati = 0;
        $saltati = 0;

        try {
            $this->db->transStart();

            // Carica il modello dei materiali
            $materialeModel = new \App\Models\Materiale();

            foreach ($dati as $riga) {
                // Verifica se il materiale è già presente nel database principale
                $materiale = $materialeModel->where('codice', $riga['codice'])->first();
                
                if (!$materiale) {
                    // Se il materiale non esiste, lo creiamo
                    $nuovoMateriale = [
                        'codice' => $riga['codice'],
                        'descrizione' => $riga['descrizione'],
                        'materiale' => $riga['materiale'],
                        'produttore' => $riga['produttore'],
                        'in_produzione' => 1
                    ];
                    
                    $materialeModel->insert($nuovoMateriale);
                    $idMateriale = $materialeModel->getInsertID();
                } else {
                    $idMateriale = $materiale['id'];
                }
                
                // Verifica se il materiale esiste già nel progetto
                if ($skipDuplicati) {
                    $query = $this->db->table('progetti_materiali')
                        ->where('id_progetto', $id)
                        ->where('id_materiale', $idMateriale)
                        ->get();
                    
                    if ($query && $query->getNumRows() > 0) {
                        $saltati++;
                        continue;
                    }
                }

                // Inserisci l'associazione tra progetto e materiale
                $this->db->table('progetti_materiali')->insert([
                    'id_progetto' => $id,
                    'id_materiale' => $idMateriale,
                    'quantita' => $riga['quantita'],
                    'unita_misura' => $riga['unita_misura'] ?? 'pz',
                    'note' => null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                $importati++;
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Errore durante l\'importazione dei materiali');
            }

            // Rimuovi il file temporaneo dopo l'importazione
            $session = session();
            $tempFile = $session->get('excel_temp_file');
            if ($tempFile && file_exists($tempFile)) {
                unlink($tempFile);
            }
            $session->remove('excel_temp_file');

            return $this->response->setJSON([
                'success' => true,
                'importati' => $importati,
                'saltati' => $saltati
            ]);
        } catch (\Exception $e) {
            log_message('error', '[Importazione Excel] Errore durante l\'importazione: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Errore durante l\'importazione dei materiali: ' . $e->getMessage()]);
        }
    }

    /**
     * Genera un codice a barre PDF per l'associazione materiale-progetto
     * 
     * @param integer|null $idProgetto ID del progetto
     * @param integer|null $idMateriale ID del materiale nel progetto (id della tabella progetti_materiali)
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function barcodeMateriale($idProgetto = null, $idMateriale = null)
    {
        // Verifica che il progetto esista
        $progetto = $this->progettoModel->find($idProgetto);
        if (empty($progetto)) {
            return redirect()->to('progetti')->with('error', 'Progetto non trovato');
        }

        // Verifica che l'associazione materiale-progetto esista
        $progettoMaterialeModel = new \App\Models\ProgettoMaterialeModel();
        $associazione = $progettoMaterialeModel->find($idMateriale);

        if (empty($associazione) || $associazione['id_progetto'] != $idProgetto) {
            return redirect()->to("progetti/{$idProgetto}")->with('error', 'Materiale non trovato nel progetto');
        }

        // Carica i dettagli del materiale
        $materialeModel = new \App\Models\Materiale();
        $materiale = $materialeModel->find($associazione['id_materiale']);
        
        if (empty($materiale)) {
            return redirect()->to("progetti/{$idProgetto}")->with('error', 'Dettagli materiale non trovati');
        }

        // Sanitizza il codice del materiale per evitare problemi di lettura con il barcode
        $codiceSanitizzato = $this->sanitizzaCodicePerBarcode($materiale['codice']);

        // Debug - Log dei valori per diagnostica
        log_message('debug', 'Generazione barcode - ID Progetto: ' . $idProgetto);
        log_message('debug', 'Generazione barcode - ID Materiale: ' . $idMateriale);
        log_message('debug', 'Generazione barcode - Codice originale: ' . $materiale['codice']);
        log_message('debug', 'Generazione barcode - Codice sanitizzato: ' . $codiceSanitizzato);

        try {
            // Carica il file di configurazione TCPDF
            require_once(APPPATH . 'ThirdParty/TCPDF-main/config/tcpdf_config.php');
            
            // Definisci una directory temporanea specifica per TCPDF
            $tempDir = WRITEPATH . 'temp/tcpdf';
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0777, true);
            }
            if (!defined('K_PATH_CACHE')) {
                define('K_PATH_CACHE', $tempDir . '/');
            }
            
            // Carichiamo TCPDF
            $tcpdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, array(80, 50), true, 'UTF-8', false);
            
            // Configurazione base del documento
            $tcpdf->SetCreator('Gestione Progetti');
            $tcpdf->SetAuthor('Sistema');
            $tcpdf->SetTitle('Etichetta Materiale - Progetto ' . $progetto['nome']);
            
            // Rimuovi header e footer
            $tcpdf->setPrintHeader(false);
            $tcpdf->setPrintFooter(false);
            
            // Imposta margini
            $tcpdf->SetMargins(5, 5, 5);
            
            // Aggiungi pagina
            $tcpdf->AddPage();
            
            // Genera il codice a barre
            $style = array(
                'position' => '',
                'align' => 'C',
                'stretch' => false,
                'fitwidth' => true,
                'cellfitalign' => '',
                'border' => false,
                'hpadding' => 'auto',
                'vpadding' => 'auto',
                'fgcolor' => array(0, 0, 0),
                'bgcolor' => false,
                'text' => true,
                'font' => 'helvetica',
                'fontsize' => 8,
                'stretchtext' => 4
            );
            
            // Codice univoco per l'associazione (formato: P{id_progetto}-M{id_materiale})
            $codiceAssociazione = "P{$idProgetto}M{$associazione['id_materiale']}";
            
            // Nome progetto in alto
            $tcpdf->SetFont('helvetica', 'B', 9);
            $tcpdf->Cell(0, 0, "Progetto: " . mb_substr($progetto['nome'], 0, 30), 0, 1, 'C');
            $tcpdf->Ln(1);
            
            // Codice e descrizione materiale (troncati se troppo lunghi)
            $tcpdf->SetFont('helvetica', 'B', 8);
            $tcpdf->Cell(0, 0, "Cod: " . $materiale['codice'], 0, 1, 'L');
            $tcpdf->Ln(1);
            
            $tcpdf->SetFont('helvetica', '', 7);
            $descrizione = character_limiter($materiale['descrizione'], 50);
            $tcpdf->Cell(0, 0, $descrizione, 0, 1, 'L');
            $tcpdf->Ln(1);
            
            // Quantità e unità di misura
            $tcpdf->SetFont('helvetica', '', 8);
            $tcpdf->Cell(0, 0, "Q.tà: {$associazione['quantita']} {$associazione['unita_misura']}", 0, 1, 'L');
            $tcpdf->Ln(1);
            
            // Aggiungi dettagli produttore se disponibile
            if (!empty($materiale['produttore'])) {
                $tcpdf->SetFont('helvetica', 'I', 6);
                $tcpdf->Cell(0, 0, "Prod: " . $materiale['produttore'], 0, 1, 'L');
                $tcpdf->Ln(1);
            }
            
            // Codice originale e codice sanitizzato per il barcode (se diversi)
            if ($materiale['codice'] !== $codiceSanitizzato) {
                $tcpdf->SetFont('helvetica', '', 6);
                $tcpdf->Cell(0, 0, "Codice barcode: " . $codiceSanitizzato, 0, 1, 'L');
            }
            
            // Genera il codice a barre (Code 128)
            try {
                $tcpdf->write1DBarcode($codiceSanitizzato, 'C128', '', '', '', 10, 0.4, $style, 'N');
                log_message('debug', 'Generazione barcode completata con successo');
            } catch (\Exception $e) {
                log_message('error', 'Errore nella generazione del barcode: ' . $e->getMessage());
            }
            
            // Aggiungi ID associazione in piccolo sotto al barcode
            $tcpdf->Ln(10);
            $tcpdf->SetFont('helvetica', '', 6);
            $tcpdf->Cell(0, 0, "ID: {$idMateriale}", 0, 1, 'C');
            
            // Output del PDF
            $filename = "etichetta_P{$idProgetto}_M{$associazione['id_materiale']}.pdf";
            
            // Imposta gli header corretti per il download del PDF
            $response = service('response');
            $response->setHeader('Content-Type', 'application/pdf');
            $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
            $response->setHeader('Cache-Control', 'max-age=0');
            
            // Genera il PDF e lo invia direttamente al browser
            $pdfContent = $tcpdf->Output($filename, 'S'); // 'S' per ottenere il contenuto come stringa
            
            return $response->setBody($pdfContent);
        } catch (\Exception $e) {
            // Log dell'errore
            log_message('error', 'Errore generazione barcode: ' . $e->getMessage());
            
            // Reindirizza con messaggio di errore
            return redirect()->to("progetti/{$idProgetto}")->with('error', 'Errore nella generazione del barcode: ' . $e->getMessage());
        }
    }

    /**
     * Genera etichette per tutti i materiali selezionati in un progetto
     * 
     * @param integer|null $idProgetto ID del progetto
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function barcodeProgettoMateriali($idProgetto = null)
    {
        // Verifica che il progetto esista
        $progetto = $this->progettoModel->find($idProgetto);
        if (empty($progetto)) {
            return redirect()->to('progetti')->with('error', 'Progetto non trovato');
        }

        // Ottiene gli ID dei materiali selezionati
        $materialiIds = $this->request->getPost('materiali_ids');
        if (empty($materialiIds)) {
            return redirect()->to("progetti/{$idProgetto}")->with('error', 'Nessun materiale selezionato');
        }

        $ids = json_decode($materialiIds, true);
        if (empty($ids) || !is_array($ids)) {
            return redirect()->to("progetti/{$idProgetto}")->with('error', 'Errore nella selezione dei materiali');
        }

        // Verifica che ci siano materiali selezionati
        if (count($ids) === 0) {
            return redirect()->to("progetti/{$idProgetto}")->with('error', 'Nessun materiale selezionato');
        }

        // Configurazione della pagina e delle etichette
        $pageWidth = 210;   // Larghezza A4 in mm
        $pageHeight = 297;  // Altezza A4 in mm
        $etichettaWidth = 60;  // Larghezza etichetta in mm
        $etichettaHeight = 30; // Altezza etichetta in mm
        $marginX = 10;      // Margine orizzontale in mm
        $marginY = 10;      // Margine verticale in mm
        $spacingX = 5;      // Spazio orizzontale tra etichette in mm
        $spacingY = 5;      // Spazio verticale tra etichette in mm

        // Calcola quante etichette per riga e quante righe per pagina
        $etichettesPerRow = floor(($pageWidth - 2 * $marginX + $spacingX) / ($etichettaWidth + $spacingX));
        $rowsPerPage = floor(($pageHeight - 2 * $marginY + $spacingY) / ($etichettaHeight + $spacingY));
        $etichettesPerPage = $etichettesPerRow * $rowsPerPage;

        // Carichiamo TCPDF
        require_once(APPPATH . 'ThirdParty/TCPDF-main/config/tcpdf_config.php');
        
        // Definisci una directory temporanea specifica per TCPDF
        $tempDir = WRITEPATH . 'temp/tcpdf';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }
        if (!defined('K_PATH_CACHE')) {
            define('K_PATH_CACHE', $tempDir . '/');
        }
        
        $tcpdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        
        // Configurazione base del documento
        $tcpdf->SetCreator('Gestione Progetti');
        $tcpdf->SetAuthor('Sistema');
        $tcpdf->SetTitle('Etichette Materiali - Progetto ' . $progetto['nome']);
        
        // Rimuovi header e footer
        $tcpdf->setPrintHeader(false);
        $tcpdf->setPrintFooter(false);
        
        // Imposta margini
        $tcpdf->SetMargins($marginX, $marginY, $marginX);
        $tcpdf->SetAutoPageBreak(false);
        
        // Configurazione del codice a barre
        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false,
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 7,
            'stretchtext' => 4
        );

        // Carica i modelli necessari
        $progettoMaterialeModel = new \App\Models\ProgettoMaterialeModel();
        $materialeModel = new \App\Models\Materiale();
        
        // Aggiungi la prima pagina
        $tcpdf->AddPage();
        
        // Contatori per posizionamento
        $currentEtichetta = 0;
        
        // Aggiungi un bordo sottile attorno a ciascuna etichetta (commentato per default)
        $drawBorder = false; // Impostare a true per visualizzare i bordi delle etichette
        
        // Generazione delle etichette
        foreach ($ids as $idMateriale) {
            // Ottieni l'associazione materiale-progetto
            $associazione = $progettoMaterialeModel->find($idMateriale);
            if (empty($associazione) || $associazione['id_progetto'] != $idProgetto) {
                continue; // Salta questo materiale e passa al prossimo
            }
            
            // Ottieni i dettagli del materiale
            $materiale = $materialeModel->find($associazione['id_materiale']);
            if (empty($materiale)) {
                continue; // Salta questo materiale e passa al prossimo
            }
            
            // Sanitizza il codice del materiale per evitare problemi di lettura con il barcode
            $codiceSanitizzato = $this->sanitizzaCodicePerBarcode($materiale['codice']);
            
            // Calcola la posizione di questa etichetta nella griglia
            $row = floor($currentEtichetta / $etichettesPerRow);
            $col = $currentEtichetta % $etichettesPerRow;
            
            // Controlla se abbiamo bisogno di una nuova pagina
            if ($currentEtichetta > 0 && $currentEtichetta % $etichettesPerPage == 0) {
                $tcpdf->AddPage();
                $row = 0;
                $col = 0;
            }
            
            // Calcola le coordinate per questa etichetta
            $x = $marginX + $col * ($etichettaWidth + $spacingX);
            $y = $marginY + $row * ($etichettaHeight + $spacingY);
            
            // Disegna il bordo dell'etichetta (opzionale)
            if ($drawBorder) {
                $tcpdf->SetDrawColor(200, 200, 200);
                $tcpdf->Rect($x, $y, $etichettaWidth, $etichettaHeight);
            }
            
            // Prepara i contenuti dell'etichetta
            
            // Salva la posizione corrente
            $tcpdf->setXY($x, $y);
            
            // Codice materiale
            $tcpdf->SetFont('helvetica', 'B', 8);
            $tcpdf->Cell($etichettaWidth, 4, $materiale['codice'], 0, 2, 'C');
            
            // Descrizione troncata
            $tcpdf->SetFont('helvetica', '', 7);
            $descrizione = character_limiter($materiale['descrizione'], 30);
            $tcpdf->Cell($etichettaWidth, 4, $descrizione, 0, 2, 'C');
            
            // Quantità
            $tcpdf->SetFont('helvetica', '', 7);
            $tcpdf->Cell($etichettaWidth, 4, "Q.tà: {$associazione['quantita']} {$associazione['unita_misura']}", 0, 2, 'C');
            
            // Codice originale e codice sanitizzato per il barcode (se diversi)
            if ($materiale['codice'] !== $codiceSanitizzato) {
                $tcpdf->SetFont('helvetica', '', 6);
                $tcpdf->Cell($etichettaWidth, 3, "Codice barcode: " . $codiceSanitizzato, 0, 2, 'C');
            }
            
            // Genera il codice a barre
            $barcodeY = $y + 15; // Posiziona il codice a barre a circa metà dell'etichetta
            $tcpdf->write1DBarcode(
                $codiceSanitizzato, 
                'C128', 
                $x + 2, // Piccolo margine interno
                $barcodeY,
                $etichettaWidth - 4, // Larghezza ridotta per margini interni
                10, 
                0.3, 
                $style
            );
            
            // Incrementa il contatore
            $currentEtichetta++;
        }
        
        // Output del PDF
        $filename = "etichette_progetto_{$idProgetto}.pdf";
        
        // Imposta gli header corretti per il download del PDF
        $response = service('response');
        $response->setHeader('Content-Type', 'application/pdf');
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->setHeader('Cache-Control', 'max-age=0');
        
        // Genera il PDF e lo invia direttamente al browser
        $pdfContent = $tcpdf->Output($filename, 'S'); // 'S' per ottenere il contenuto come stringa
        
        return $response->setBody($pdfContent);
    }

    /**
     * Sanitizza un codice per renderlo compatibile con i lettori di codici a barre
     * sostituendo i caratteri problematici
     * 
     * @param string $codice Il codice originale
     * @return string Il codice sanitizzato
     */
    private function sanitizzaCodicePerBarcode($codice) 
    {
        // Mappatura dei caratteri problematici
        $caratteriProblematici = [
            '/' => '-', // Sostituisci la barra con un trattino
            '\'' => '', // Rimuovi gli apici singoli
            '\\' => '', // Rimuovi i backslash
            '"' => '', // Rimuovi gli apici doppi
            '?' => '', // Rimuovi i punti interrogativi
            '*' => 'x', // Sostituisci gli asterischi con una x
            '#' => 'n', // Sostituisci il cancelletto con n
            '&' => 'e', // Sostituisci & con e
        ];
        
        // Applica le sostituzioni
        $codiceSanitizzato = str_replace(
            array_keys($caratteriProblematici), 
            array_values($caratteriProblematici), 
            $codice
        );
        
        return $codiceSanitizzato;
    }
} 