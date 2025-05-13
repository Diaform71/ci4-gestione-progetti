<?php

namespace App\Controllers;

use App\Models\ScadenzaModel;
use App\Models\ProgettoModel;
use App\Models\AttivitaModel;
use App\Models\UtentiModel;
use CodeIgniter\I18n\Time;
use CodeIgniter\API\ResponseTrait;

class Scadenze extends BaseController
{
    use ResponseTrait;
    
    protected $scadenzeModel;
    protected $progettiModel;
    protected $attivitaModel;
    protected $utentiModel;
    
    public function __construct()
    {
        $this->scadenzeModel = new ScadenzaModel();
        $this->progettiModel = new ProgettoModel();
        $this->attivitaModel = new AttivitaModel();
        $this->utentiModel = new UtentiModel();
        
        // Verifica che l'utente sia autenticato
        if (!session()->get('utente_id')) {
            return redirect()->to('/login');
        }
    }
    
    /**
     * Visualizza la lista di tutte le scadenze
     */
    public function index()
    {
        // Recupera i parametri di filtro dalla richiesta GET
        $filtri = [
            'priorita' => $this->request->getGet('priorita'),
            'stato' => $this->request->getGet('stato'),
            'data_da' => $this->request->getGet('data_da'),
            'data_a' => $this->request->getGet('data_a'),
            'id_utente_assegnato' => $this->request->getGet('id_utente_assegnato'),
            'id_progetto' => $this->request->getGet('id_progetto'),
            'completata' => $this->request->getGet('completata')
        ];
        
        // Rimuovi filtri vuoti
        $filtri = array_filter($filtri, function($value) {
            return $value !== null && $value !== '';
        });
        
        // Titolo dinamico per indicare se ci sono filtri attivi
        $title = 'Scadenze';
        if (!empty($filtri)) {
            $title = 'Scadenze filtrate';
        }
        
        // Recupera i dati per la vista
        $data = [
            'title' => $title,
            'scadenze' => $this->scadenzeModel->getScadenzeWithDetails($filtri),
            'isAdmin' => session()->get('is_admin'),
            'utenti' => $this->utentiModel->where('attivo', 1)->findAll(),
            'progetti' => $this->progettiModel->getActiveProjects()
        ];
        
        // Assicuriamoci che non ci siano riferimenti a progetti o attività specifiche
        // quando visualizziamo tutte le scadenze
        if (session()->has('progetto_id') || session()->has('attivita_id')) {
            session()->remove(['progetto_id', 'attivita_id']);
        }
        
        return view('scadenze/index', $data);
    }
    
    /**
     * Visualizza la form per creare una nuova scadenza
     */
    public function nuovo()
    {
        // Dati per la vista
        $data = [
            'title' => 'Nuova Scadenza',
            'progetti' => $this->progettiModel->getActiveProjects(),
            'attivita' => $this->attivitaModel->findAll(),
            'utenti' => $this->utentiModel->where('attivo', 1)->findAll(),
            'validation' => \Config\Services::validation()
        ];
        
        return view('scadenze/form', $data);
    }
    
    /**
     * Elabora la form di creazione/modifica
     */
    public function salva()
    {
        $post = $this->request->getPost();
        
        // Validazione
        $rules = [
            'titolo' => 'required|min_length[3]|max_length[255]',
            'data_scadenza' => 'required',  // Rimuovo valid_date perché verificheremo con l'helper
            'id_utente_assegnato' => 'required|integer',
            'priorita' => 'required|in_list[bassa,media,alta,urgente]',
            'stato' => 'required|in_list[da_iniziare,in_corso,completata,annullata]',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Verifica validità data scadenza con l'helper
        $dataScadenza = $this->request->getPost('data_scadenza');
        if (!empty($dataScadenza) && !isValidItalianDate($dataScadenza)) {
            return redirect()->back()->withInput()->with('errors', ['data_scadenza' => 'Il campo data scadenza deve contenere una data valida nel formato GG/MM/AAAA.']);
        }
        
        // Verifica validità data promemoria con l'helper
        $dataPromemoria = $this->request->getPost('data_promemoria');
        if (!empty($dataPromemoria) && !isValidItalianDate($dataPromemoria)) {
            return redirect()->back()->withInput()->with('errors', ['data_promemoria' => 'Il campo data promemoria deve contenere una data valida nel formato GG/MM/AAAA.']);
        }
        
        // Preparazione dati
        $data = [
            'titolo' => $this->request->getPost('titolo'),
            'priorita' => $this->request->getPost('priorita'),
            'descrizione' => $this->request->getPost('descrizione'),
            'id_progetto' => $this->request->getPost('id_progetto') ?: null,
            'id_attivita' => $this->request->getPost('id_attivita') ?: null,
            'stato' => $this->request->getPost('stato'),
            'id_utente_assegnato' => $this->request->getPost('id_utente_assegnato'),
            'id_utente_creatore' => session()->get('utente_id'),
        ];
        
        // Controlla se c'è un ID (modifica di una scadenza esistente)
        $id = $this->request->getPost('id');
        
        // Converti le date usando l'helper
        if (!empty($dataScadenza)) {
            $data['data_scadenza'] = formatDateToISO($dataScadenza);
        }
        
        if (!empty($dataPromemoria)) {
            $data['data_promemoria'] = formatDateToISO($dataPromemoria);
        }
        
        // Gestisci stato completato
        if ($data['stato'] === 'completata') {
            $data['completata'] = 1;
            $data['completata_il'] = Time::now()->toDateTimeString();
        } else {
            $data['completata'] = 0;
            $data['completata_il'] = null;
        }
        
        // Salva o aggiorna
        if (!empty($id)) {
            // Aggiorna scadenza esistente
            if ($this->scadenzeModel->update($id, $data)) {
                return redirect()->to('/scadenze')->with('message', 'Scadenza aggiornata con successo');
            } else {
                return redirect()->back()->withInput()->with('error', 'Errore durante l\'aggiornamento della scadenza');
            }
        } else {
            // Crea nuova scadenza
            if ($this->scadenzeModel->creaScadenza($data)) {
                return redirect()->to('/scadenze')->with('message', 'Scadenza creata con successo');
            } else {
                return redirect()->back()->withInput()->with('error', 'Errore durante la creazione della scadenza');
            }
        }
    }
    
    /**
     * Visualizza la form per modificare una scadenza
     */
    public function modifica($id)
    {
        $scadenza = $this->scadenzeModel->find($id);
        
        if (!$scadenza) {
            return redirect()->to('/scadenze')->with('error', 'Scadenza non trovata');
        }
        
        // Controllo autorizzazione
        if (!session()->get('is_admin') && session()->get('utente_id') != $scadenza['id_utente_creatore'] && session()->get('utente_id') != $scadenza['id_utente_assegnato']) {
            return redirect()->to('/scadenze')->with('error', 'Non hai l\'autorizzazione per modificare questa scadenza');
        }
        
        // Dati per la vista
        $data = [
            'title' => 'Modifica Scadenza',
            'scadenza' => $scadenza,
            'progetti' => $this->progettiModel->getActiveProjects(),
            'attivita' => $this->attivitaModel->findAll(),
            'utenti' => $this->utentiModel->where('attivo', 1)->findAll(),
            'validation' => \Config\Services::validation()
        ];
        
        return view('scadenze/form', $data);
    }
    
    /**
     * Visualizza i dettagli di una singola scadenza
     */
    public function dettaglio($id)
    {
        $scadenza = $this->scadenzeModel->getScadenzaCompleta($id);
        
        if (!$scadenza) {
            return redirect()->to('/scadenze')->with('error', 'Scadenza non trovata');
        }
        
        // Controllo autorizzazione
        if (!session()->get('is_admin') && session()->get('utente_id') != $scadenza['id_utente_creatore'] && session()->get('utente_id') != $scadenza['id_utente_assegnato']) {
            return redirect()->to('/scadenze')->with('error', 'Non hai l\'autorizzazione per visualizzare questa scadenza');
        }
        
        $data = [
            'title' => 'Dettaglio Scadenza',
            'scadenza' => $scadenza
        ];
        
        return view('scadenze/dettaglio', $data);
    }
    
    /**
     * Elimina una scadenza
     */
    public function elimina($id = null)
    {
        // Controlla che sia una richiesta POST con CSRF valido
        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('/scadenze')->with('error', 'Metodo non consentito');
        }
        
        // Controlla che l'ID sia valido
        if (!$id) {
            return redirect()->to('/scadenze')->with('error', 'ID scadenza non specificato');
        }
        
        $scadenza = $this->scadenzeModel->find($id);
        
        if (!$scadenza) {
            return redirect()->to('/scadenze')->with('error', 'Scadenza non trovata');
        }
        
        // Solo admin o creatore possono eliminare
        if (!session()->get('is_admin') && session()->get('utente_id') != $scadenza['id_utente_creatore']) {
            return redirect()->to('/scadenze')->with('error', 'Non hai l\'autorizzazione per eliminare questa scadenza');
        }
        
        if ($this->scadenzeModel->delete($id)) {
            return redirect()->to('/scadenze')->with('message', 'Scadenza eliminata con successo');
        } else {
            return redirect()->to('/scadenze')->with('error', 'Errore durante l\'eliminazione della scadenza');
        }
    }
    
    /**
     * Cambia lo stato di completamento di una scadenza
     */
    public function completa($id)
    {
        $scadenza = $this->scadenzeModel->find($id);
        
        if (!$scadenza) {
            return redirect()->to('/scadenze')->with('error', 'Scadenza non trovata');
        }
        
        // Solo admin o utente assegnato possono completare
        if (!session()->get('is_admin') && session()->get('utente_id') != $scadenza['id_utente_assegnato']) {
            return redirect()->to('/scadenze')->with('error', 'Non hai l\'autorizzazione per completare questa scadenza');
        }
        
        // Inverti lo stato di completamento
        $nuovoStato = !$scadenza['completata'];
        
        if ($this->scadenzeModel->completaScadenza($id, $nuovoStato)) {
            $messaggio = $nuovoStato ? 'Scadenza completata con successo' : 'Scadenza riaperta con successo';
            return redirect()->to('/scadenze')->with('message', $messaggio);
        } else {
            return redirect()->to('/scadenze')->with('error', 'Errore durante l\'aggiornamento della scadenza');
        }
    }
    
    /**
     * Visualizza le scadenze legate a un progetto specifico
     */
    public function progetto($id)
    {
        // Verifica che il progetto esista
        $progetto = $this->progettiModel->find($id);
        
        if (!$progetto) {
            return redirect()->to('/scadenze')->with('error', 'Progetto non trovato');
        }
        
        // Ottieni le scadenze filtrate per progetto
        // Questo ora include automaticamente anche i sottoprogetti
        $scadenze = $this->scadenzeModel->getScadenzeWithDetails(['id_progetto' => $id]);
        
        // Se non è admin, filtra solo le proprie scadenze
        if (!session()->get('is_admin')) {
            $utenteId = session()->get('utente_id');
            $scadenze = array_filter($scadenze, function($s) use ($utenteId) {
                return $s['id_utente_assegnato'] == $utenteId || $s['id_utente_creatore'] == $utenteId;
            });
        }
        
        $data = [
            'title' => 'Scadenze del Progetto: ' . $progetto['nome'],
            'scadenze' => $scadenze,
            'progetto' => $progetto,
            'isAdmin' => session()->get('is_admin'),
            'utenti' => $this->utentiModel->where('attivo', 1)->findAll(),
            'progetti' => $this->progettiModel->getActiveProjects()
        ];
        
        return view('scadenze/index', $data);
    }
    
    /**
     * Visualizza le scadenze legate a un'attività specifica
     */
    public function attivita($id)
    {
        // Verifica che l'attività esista
        $attivita = $this->attivitaModel->find($id);
        
        if (!$attivita) {
            return redirect()->to('/scadenze')->with('error', 'Attività non trovata');
        }
        
        // Ottieni le scadenze filtrate per attività
        $scadenze = $this->scadenzeModel->getScadenzeWithRelations(['id_attivita' => $id]);
        
        // Se non è admin, filtra solo le proprie scadenze
        if (!session()->get('is_admin')) {
            $utenteId = session()->get('utente_id');
            $scadenze = array_filter($scadenze, function($s) use ($utenteId) {
                return $s['id_utente_assegnato'] == $utenteId || $s['id_utente_creatore'] == $utenteId;
            });
        }
        
        $data = [
            'title' => 'Scadenze dell\'Attività: ' . $attivita['titolo'],
            'scadenze' => $scadenze,
            'attivita' => $attivita,
            'isAdmin' => session()->get('is_admin')
        ];
        
        return view('scadenze/index', $data);
    }
    
    /**
     * Visualizza le scadenze in scadenza per la dashboard
     */
    public function inScadenza()
    {
        $utenteId = null;
        
        // Se non è admin, mostra solo le proprie scadenze
        if (!session()->get('is_admin')) {
            $utenteId = session()->get('utente_id');
        }
        
        $data = [
            'title' => 'Scadenze in Arrivo',
            'scadenze' => $this->scadenzeModel->getScadenzeInArrivo(7, $utenteId),
            'isAdmin' => session()->get('is_admin'),
            'vistaSpeciale' => 'in_scadenza'
        ];
        
        return view('scadenze/index', $data);
    }
    
    /**
     * Visualizza le scadenze scadute
     */
    public function scadute()
    {
        $utenteId = null;
        
        // Se non è admin, mostra solo le proprie scadenze
        if (!session()->get('is_admin')) {
            $utenteId = session()->get('utente_id');
        }
        
        $data = [
            'title' => 'Scadenze Scadute',
            'scadenze' => $this->scadenzeModel->getScadenzeScadute($utenteId),
            'isAdmin' => session()->get('is_admin'),
            'vistaSpeciale' => 'scadute'
        ];
        
        return view('scadenze/index', $data);
    }
    
    /**
     * Fornisce gli eventi per FullCalendar in formato JSON
     */
    public function calendarioEventi()
    {
        // Verifica se è una vista speciale
        $vistaSpeciale = $this->request->getGet('vista_speciale');
        $scadenze = [];
        $utenteId = null;
        
        // Se non è admin, mostra solo le proprie scadenze
        if (!session()->get('is_admin')) {
            $utenteId = session()->get('utente_id');
        }
        
        // Seleziona il metodo di recupero dati in base alla vista speciale
        if ($vistaSpeciale === 'scadute') {
            // Usa lo stesso metodo del controller scadute()
            $scadenze = $this->scadenzeModel->getScadenzeScadute($utenteId);
        } else if ($vistaSpeciale === 'in_scadenza') {
            // Usa lo stesso metodo del controller inScadenza()
            $scadenze = $this->scadenzeModel->getScadenzeInArrivo(7, $utenteId);
        } else {
            // Vista normale, usa il metodo standard con filtri
            // Recupera i parametri di filtro dalla richiesta GET (gli stessi di index)
            $filtri = [
                'priorita' => $this->request->getGet('priorita'),
                'stato' => $this->request->getGet('stato'),
                'data_da' => $this->request->getGet('data_da'),
                'data_a' => $this->request->getGet('data_a'),
                'id_utente_assegnato' => $this->request->getGet('id_utente_assegnato'),
                'id_progetto' => $this->request->getGet('id_progetto'),
                'completata' => $this->request->getGet('completata')
            ];
            
            // Rimuovi filtri vuoti
            $filtri = array_filter($filtri, function($value) {
                return $value !== null && $value !== '';
            });
            
            // Recupera le scadenze filtrate
            $scadenze = $this->scadenzeModel->getScadenzeWithDetails($filtri);
        }
        
        $eventi = [];
        foreach ($scadenze as $scadenza) {
            $className = 'fc-event-' . $scadenza['priorita']; // Classe base per priorità
            if ($scadenza['completata']) {
                $className = 'fc-event-completata';
            } elseif ($scadenza['stato'] === 'annullata') {
                $className = 'fc-event-annullata';
            }
            
            // Descrizione per tooltip (usare <br> per i newline in HTML)
            $description = "Progetto: " . esc($scadenza['nome_progetto'] ?? 'N/D') . "<br>";
            $description .= "Attività: " . esc($scadenza['titolo_attivita'] ?? 'N/D') . "<br>";
            $description .= "Assegnato a: " . esc($scadenza['nome_assegnato'] ?? '') . ' ' . esc($scadenza['cognome_assegnato'] ?? '') . "<br>";
            $description .= "Stato: " . ucfirst(str_replace('_', ' ', esc($scadenza['stato']))) . "<br>";
            $description .= "Priorità: " . ucfirst(esc($scadenza['priorita']));
            
            $eventi[] = [
                'id' => $scadenza['id'],
                'title' => $scadenza['titolo'],
                'start' => $scadenza['data_scadenza'], // FullCalendar gestisce il formato YYYY-MM-DD
                'url' => base_url('scadenze/dettaglio/' . $scadenza['id']),
                'className' => $className,
                'description' => $description // Dati extra per tooltip
            ];
        }
        
        // Restituisci JSON
        return $this->respond($eventi);
    }
} 