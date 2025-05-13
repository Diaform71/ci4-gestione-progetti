<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AttivitaModel;
use App\Models\ProgettoModel;
use App\Models\SottoAttivitaModel;
use App\Models\UtentiModel;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Services;
use CodeIgniter\Database\BaseConnection;

final class Attivita extends BaseController
{
    protected $attivitaModel;
    protected $sottoAttivitaModel;
    protected $progettoModel;
    protected $utenteModel;
    protected $session;
    protected $validator;
    protected $db;
    
    public function __construct()
    {
        $this->attivitaModel = new AttivitaModel();
        $this->sottoAttivitaModel = new SottoAttivitaModel();
        $this->progettoModel = new ProgettoModel();
        $this->utenteModel = new UtentiModel();
        $this->session = Services::session();
        $this->validator = Services::validation();
        $this->db = \Config\Database::connect();
    }
    
    /**
     * Mostra l'elenco delle attività
     */
    public function index()
    {
        // Ottieni l'elenco delle attività con informazioni sul progetto e sull'utente assegnato
        $attivita = $this->attivitaModel->getAttivitaWithDetails();
        
        // Per ogni attività, aggiungi il conteggio delle sottoattività
        foreach ($attivita as &$task) {
            // Ottieni le sottoattività per questa attività
            $sottoAttivita = $this->sottoAttivitaModel->where('id_attivita', $task['id'])->findAll();
            
            // Conta il totale delle sottoattività
            $task['sotto_attivita_totali'] = count($sottoAttivita);
            
            // Conta le sottoattività completate
            $completate = 0;
            foreach ($sottoAttivita as $subTask) {
                if ($subTask['stato'] === 'completata') {
                    $completate++;
                }
            }
            $task['sotto_attivita_completate'] = $completate;
        }
        
        $data = [
            'titolo' => 'Gestione Attività',
            'attivita' => $attivita,
            'is_admin' => $this->session->get('is_admin') ?? false
        ];
        
        return view('attivita/index', $data);
    }
    
    /**
     * Mostra una singola attività
     */
    public function view($id = null)
    {
        if (!$id) {
            $this->session->setFlashdata('error', 'ID attività non specificato.');
            return redirect()->to('/attivita');
        }
        
        $attivita = $this->attivitaModel->getAttivitaCompleta((int)$id);
        
        if (!$attivita) {
            $this->session->setFlashdata('error', 'Attività non trovata.');
            return redirect()->to('/attivita');
        }
        
        $data = [
            'titolo' => 'Dettaglio Attività',
            'attivita' => $attivita,
            'utenti' => $this->utenteModel->findAll()
        ];
        
        return view('attivita/view', $data);
    }
    
    /**
     * Mostra il form per creare una nuova attività
     */
    public function new($idProgetto = null)
    {
        // Controlla se l'utente è admin
        $isAdmin = $this->session->get('is_admin') ?? false;
        
        if (!$isAdmin) {
            $this->session->setFlashdata('error', 'Non hai i permessi per creare attività.');
            return redirect()->to('/attivita');
        }
        
        $data = [
            'titolo' => 'Nuova Attività',
            'progetti' => $this->progettoModel->findAll(),
            'utenti' => $this->utenteModel->findAll(),
            'id_progetto_selezionato' => $idProgetto
        ];
        
        return view('attivita/create', $data);
    }
    
    /**
     * Processa la creazione di una nuova attività
     */
    public function create(): RedirectResponse
    {
        // Verifica CSRF
        if (!$this->validate(['csrf_test_name' => 'required'])) {
            $this->session->setFlashdata('error', 'Errore di sicurezza. Riprova.');
            return redirect()->back();
        }
        
        // Controlla se l'utente è admin
        $isAdmin = $this->session->get('is_admin') ?? false;
        
        if (!$isAdmin) {
            $this->session->setFlashdata('error', 'Non hai i permessi per creare attività.');
            return redirect()->to('/attivita');
        }
        
        // Validazione
        $rules = [
            'id_progetto' => 'required|integer',
            'id_utente_assegnato' => 'required|integer',
            'titolo' => 'required|string|max_length[255]',
            'descrizione' => 'permit_empty|string',
            'priorita' => 'required|in_list[bassa,media,alta,urgente]',
            'stato' => 'required|in_list[da_iniziare,in_corso,in_pausa,completata,annullata]',
            'data_scadenza' => 'permit_empty',
        ];
        
        if (!$this->validate($rules)) {
            $this->session->setFlashdata('error', implode('<br>', $this->validator->getErrors()));
            return redirect()->back()->withInput();
        }
        
        // Prepara i dati
        $data = [
            'id_progetto' => $this->request->getPost('id_progetto'),
            'id_utente_assegnato' => $this->request->getPost('id_utente_assegnato'),
            'id_utente_creatore' => $this->session->get('utente_id'),
            'titolo' => $this->request->getPost('titolo'),
            'descrizione' => $this->request->getPost('descrizione'),
            'priorita' => $this->request->getPost('priorita'),
            'stato' => $this->request->getPost('stato'),
            'data_scadenza' => null,
            'completata' => ($this->request->getPost('stato') === 'completata'),
        ];
        
        // Converti la data dal formato italiano (dd/mm/yyyy) al formato ISO (yyyy-mm-dd)
        $dataScadenza = $this->request->getPost('data_scadenza');
        if (!empty($dataScadenza)) {
            $parti = explode('/', $dataScadenza);
            if (count($parti) === 3) {
                $data['data_scadenza'] = $parti[2] . '-' . $parti[1] . '-' . $parti[0];
            }
        }
        
        // Crea l'attività
        $idAttivita = $this->attivitaModel->creaAttivita($data);
        
        if (!$idAttivita) {
            $this->session->setFlashdata('error', 'Errore durante la creazione dell\'attività.');
            return redirect()->back()->withInput();
        }
        
        $this->session->setFlashdata('success', 'Attività creata con successo.');
        return redirect()->to("/attivita/view/{$idAttivita}");
    }
    
    /**
     * Mostra il form per modificare un'attività
     */
    public function edit($id = null)
    {
        if (!$id) {
            $this->session->setFlashdata('error', 'ID attività non specificato.');
            return redirect()->to('/attivita');
        }
        
        // Converti esplicitamente l'ID in intero
        $attivita = $this->attivitaModel->getAttivitaCompleta((int)$id);
        
        if (!$attivita) {
            $this->session->setFlashdata('error', 'Attività non trovata.');
            return redirect()->to('/attivita');
        }
        
        // Controlla se l'utente è admin o è il creatore dell'attività
        $isAdmin = $this->session->get('is_admin') ?? false;
        $isCreator = (int)$attivita['id_utente_creatore'] === (int)$this->session->get('utente_id');
        
        if (!$isAdmin && !$isCreator) {
            $this->session->setFlashdata('error', 'Non hai i permessi per modificare questa attività.');
            return redirect()->to('/attivita');
        }
        
        $data = [
            'titolo' => 'Modifica Attività',
            'attivita' => $attivita,
            'progetti' => $this->progettoModel->findAll(),
            'utenti' => $this->utenteModel->findAll(),
        ];
        
        return view('attivita/edit', $data);
    }
    
    /**
     * Processa l'aggiornamento di un'attività
     */
    public function update(): RedirectResponse
    {
        // Verifica CSRF
        if (!$this->validate(['csrf_test_name' => 'required'])) {
            $this->session->setFlashdata('error', 'Errore di sicurezza. Riprova.');
            return redirect()->back();
        }
        
        $id = $this->request->getPost('id');
        
        if (!$id) {
            $this->session->setFlashdata('error', 'ID attività non specificato.');
            return redirect()->to('/attivita');
        }
        
        $attivita = $this->attivitaModel->find($id);
        
        if (!$attivita) {
            $this->session->setFlashdata('error', 'Attività non trovata.');
            return redirect()->to('/attivita');
        }
        
        // Controlla se l'utente è admin o è il creatore dell'attività
        $isAdmin = $this->session->get('is_admin') ?? false;
        $isCreator = (int)$attivita['id_utente_creatore'] === (int)$this->session->get('utente_id');
        
        if (!$isAdmin && !$isCreator) {
            $this->session->setFlashdata('error', 'Non hai i permessi per modificare questa attività.');
            return redirect()->to('/attivita');
        }
        
        // Validazione
        $rules = [
            'id_progetto' => 'required|integer',
            'id_utente_assegnato' => 'required|integer',
            'titolo' => 'required|string|max_length[255]',
            'descrizione' => 'permit_empty|string',
            'priorita' => 'required|in_list[bassa,media,alta,urgente]',
            'stato' => 'required|in_list[da_iniziare,in_corso,in_pausa,completata,annullata]',
            'data_scadenza' => 'permit_empty',
        ];
        
        if (!$this->validate($rules)) {
            $this->session->setFlashdata('error', implode('<br>', $this->validator->getErrors()));
            return redirect()->back()->withInput();
        }
        
        // Prepara i dati
        $data = [
            'id_progetto' => $this->request->getPost('id_progetto'),
            'id_utente_assegnato' => $this->request->getPost('id_utente_assegnato'),
            'titolo' => $this->request->getPost('titolo'),
            'descrizione' => $this->request->getPost('descrizione'),
            'priorita' => $this->request->getPost('priorita'),
            'stato' => $this->request->getPost('stato'),
            'data_scadenza' => null,
            'data_aggiornamento' => date('Y-m-d H:i:s'),
        ];
        
        // Converti la data dal formato italiano (dd/mm/yyyy) al formato ISO (yyyy-mm-dd)
        $dataScadenza = $this->request->getPost('data_scadenza');
        if (!empty($dataScadenza)) {
            $parti = explode('/', $dataScadenza);
            if (count($parti) === 3) {
                $data['data_scadenza'] = $parti[2] . '-' . $parti[1] . '-' . $parti[0];
            }
        }
        
        // Se lo stato è cambiato a "completata", aggiorna anche il flag di completamento
        if ($this->request->getPost('stato') === 'completata' && $attivita['stato'] !== 'completata') {
            $data['completata'] = true;
            $data['completata_il'] = date('Y-m-d H:i:s');
        } elseif ($this->request->getPost('stato') !== 'completata' && $attivita['stato'] === 'completata') {
            $data['completata'] = false;
            $data['completata_il'] = null;
        }
        
        // Aggiorna l'attività
        if (!$this->attivitaModel->update($id, $data)) {
            $this->session->setFlashdata('error', 'Errore durante l\'aggiornamento dell\'attività.');
            return redirect()->back()->withInput();
        }
        
        $this->session->setFlashdata('success', 'Attività aggiornata con successo.');
        return redirect()->to("/attivita/view/{$id}");
    }
    
    /**
     * Elimina un'attività
     */
    public function delete($id = null): RedirectResponse
    {
        if (!$id) {
            $this->session->setFlashdata('error', 'ID attività non specificato.');
            return redirect()->to('/attivita');
        }
        
        $attivita = $this->attivitaModel->find($id);
        
        if (!$attivita) {
            $this->session->setFlashdata('error', 'Attività non trovata.');
            return redirect()->to('/attivita');
        }
        
        // Controlla se l'utente è admin o è il creatore dell'attività
        $isAdmin = $this->session->get('is_admin') ?? false;
        $isCreator = (int)$attivita['id_utente_creatore'] === (int)$this->session->get('utente_id');
        
        if (!$isAdmin && !$isCreator) {
            $this->session->setFlashdata('error', 'Non hai i permessi per eliminare questa attività.');
            return redirect()->to('/attivita');
        }
        
        // Elimina anche tutte le sottoattività associate
        $this->db->table('sotto_attivita')->where('id_attivita', $id)->delete();
        
        // Elimina l'attività
        if (!$this->attivitaModel->delete($id)) {
            $this->session->setFlashdata('error', 'Errore durante l\'eliminazione dell\'attività.');
            return redirect()->back();
        }
        
        $this->session->setFlashdata('success', 'Attività eliminata con successo.');
        return redirect()->to('/attivita');
    }
    
    /**
     * Cambia lo stato di un'attività
     */
    public function cambiaStato(): RedirectResponse
    {
        // Verifica CSRF
        if (!$this->validate(['csrf_test_name' => 'required'])) {
            $this->session->setFlashdata('error', 'Errore di sicurezza. Riprova.');
            return redirect()->back();
        }
        
        $id = $this->request->getPost('id');
        $stato = $this->request->getPost('stato');
        
        if (!$id || !$stato) {
            $this->session->setFlashdata('error', 'Parametri mancanti.');
            return redirect()->back();
        }
        
        $attivita = $this->attivitaModel->find($id);
        
        if (!$attivita) {
            $this->session->setFlashdata('error', 'Attività non trovata.');
            return redirect()->to('/attivita');
        }
        
        // Controlla se l'utente è admin, creatore o assegnato all'attività
        $isAdmin = $this->session->get('is_admin') ?? false;
        $isCreator = (int)$attivita['id_utente_creatore'] === (int)$this->session->get('utente_id');
        $isAssigned = (int)$attivita['id_utente_assegnato'] === (int)$this->session->get('utente_id');
        
        if (!$isAdmin && !$isCreator && !$isAssigned) {
            $this->session->setFlashdata('error', 'Non hai i permessi per modificare lo stato di questa attività.');
            return redirect()->to('/attivita');
        }
        
        // Aggiorna lo stato
        if (!$this->attivitaModel->cambiaStato((int)$id, $stato)) {
            $this->session->setFlashdata('error', 'Errore durante l\'aggiornamento dello stato.');
            return redirect()->back();
        }
        
        $this->session->setFlashdata('success', 'Stato aggiornato con successo.');
        return redirect()->to("/attivita/view/{$id}");
    }
    
    /**
     * Crea una nuova sottoattività
     */
    public function creaSottoAttivita(): RedirectResponse
    {
        // Verifica CSRF
        if (!$this->validate(['csrf_test_name' => 'required'])) {
            $this->session->setFlashdata('error', 'Errore di sicurezza. Riprova.');
            return redirect()->back();
        }
        
        $idAttivita = $this->request->getPost('id_attivita');
        
        if (!$idAttivita) {
            $this->session->setFlashdata('error', 'ID attività non specificato.');
            return redirect()->to('/attivita');
        }
        
        $attivita = $this->attivitaModel->find($idAttivita);
        
        if (!$attivita) {
            $this->session->setFlashdata('error', 'Attività non trovata.');
            return redirect()->to('/attivita');
        }
        
        // Controlla se l'utente è admin o è assegnato all'attività
        $isAdmin = $this->session->get('is_admin') ?? false;
        $isAssigned = (int)$attivita['id_utente_assegnato'] === (int)$this->session->get('utente_id');
        
        if (!$isAdmin && !$isAssigned) {
            $this->session->setFlashdata('error', 'Non hai i permessi per creare sottoattività.');
            return redirect()->to('/attivita');
        }
        
        // Validazione
        $rules = [
            'titolo' => 'required|string|max_length[255]',
            'descrizione' => 'permit_empty|string',
            'priorita' => 'required|in_list[bassa,media,alta,urgente]',
            'stato' => 'required|in_list[da_iniziare,in_corso,in_pausa,completata,annullata]',
            'data_scadenza' => 'permit_empty',
            'id_utente_assegnato' => 'permit_empty|integer'
        ];
        
        if (!$this->validate($rules)) {
            $this->session->setFlashdata('error', implode('<br>', $this->validator->getErrors()));
            return redirect()->back()->withInput();
        }
        
        // Prepara i dati
        $data = [
            'id_attivita' => $idAttivita,
            'id_utente_assegnato' => $this->request->getPost('id_utente_assegnato') ?: null,
            'titolo' => $this->request->getPost('titolo'),
            'descrizione' => $this->request->getPost('descrizione'),
            'priorita' => $this->request->getPost('priorita'),
            'stato' => $this->request->getPost('stato'),
            'data_scadenza' => null,
            'completata' => ($this->request->getPost('stato') === 'completata'),
        ];
        
        // Converti la data dal formato italiano (dd/mm/yyyy) al formato ISO (yyyy-mm-dd)
        $dataScadenza = $this->request->getPost('data_scadenza');
        if (!empty($dataScadenza)) {
            $parti = explode('/', $dataScadenza);
            if (count($parti) === 3) {
                $data['data_scadenza'] = $parti[2] . '-' . $parti[1] . '-' . $parti[0];
            }
        }
        
        // Crea la sottoattività
        if (!$this->sottoAttivitaModel->creaSottoAttivita($data)) {
            $this->session->setFlashdata('error', 'Errore durante la creazione della sottoattività.');
            return redirect()->back()->withInput();
        }
        
        $this->session->setFlashdata('success', 'Sottoattività creata con successo.');
        return redirect()->to("/attivita/view/{$idAttivita}");
    }
    
    /**
     * Aggiorna una sottoattività
     */
    public function aggiornaSottoAttivita(): RedirectResponse
    {
        // Verifica CSRF
        if (!$this->validate(['csrf_test_name' => 'required'])) {
            $this->session->setFlashdata('error', 'Errore di sicurezza. Riprova.');
            return redirect()->back();
        }
        
        $id = $this->request->getPost('id');
        
        if (!$id) {
            $this->session->setFlashdata('error', 'ID sottoattività non specificato.');
            return redirect()->to('/attivita');
        }
        
        $sottoAttivita = $this->sottoAttivitaModel->find($id);
        
        if (!$sottoAttivita) {
            $this->session->setFlashdata('error', 'Sottoattività non trovata.');
            return redirect()->to('/attivita');
        }
        
        $attivita = $this->attivitaModel->find($sottoAttivita['id_attivita']);
        
        // Controlla se l'utente è admin o è assegnato all'attività principale
        $isAdmin = $this->session->get('is_admin') ?? false;
        $isAssigned = (int)$attivita['id_utente_assegnato'] === (int)$this->session->get('utente_id');
        $isSottoAttivitaAssigned = (int)$sottoAttivita['id_utente_assegnato'] === (int)$this->session->get('utente_id');
        
        if (!$isAdmin && !$isAssigned && !$isSottoAttivitaAssigned) {
            $this->session->setFlashdata('error', 'Non hai i permessi per modificare questa sottoattività.');
            return redirect()->to('/attivita');
        }
        
        // Validazione
        $rules = [
            'titolo' => 'required|string|max_length[255]',
            'descrizione' => 'permit_empty|string',
            'priorita' => 'required|in_list[bassa,media,alta,urgente]',
            'stato' => 'required|in_list[da_iniziare,in_corso,in_pausa,completata,annullata]',
            'data_scadenza' => 'permit_empty',
            'id_utente_assegnato' => 'permit_empty|integer'
        ];
        
        if (!$this->validate($rules)) {
            $this->session->setFlashdata('error', implode('<br>', $this->validator->getErrors()));
            return redirect()->back()->withInput();
        }
        
        // Prepara i dati
        $data = [
            'id_utente_assegnato' => $this->request->getPost('id_utente_assegnato') ?: null,
            'titolo' => $this->request->getPost('titolo'),
            'descrizione' => $this->request->getPost('descrizione'),
            'priorita' => $this->request->getPost('priorita'),
            'stato' => $this->request->getPost('stato'),
            'data_scadenza' => null,
            'data_aggiornamento' => date('Y-m-d H:i:s'),
        ];
        
        // Converti la data dal formato italiano (dd/mm/yyyy) al formato ISO (yyyy-mm-dd)
        $dataScadenza = $this->request->getPost('data_scadenza');
        if (!empty($dataScadenza)) {
            $parti = explode('/', $dataScadenza);
            if (count($parti) === 3) {
                $data['data_scadenza'] = $parti[2] . '-' . $parti[1] . '-' . $parti[0];
            }
        }
        
        // Se lo stato è cambiato a "completata", aggiorna anche il flag di completamento
        if ($this->request->getPost('stato') === 'completata' && $sottoAttivita['stato'] !== 'completata') {
            $data['completata'] = true;
            $data['completata_il'] = date('Y-m-d H:i:s');
        } elseif ($this->request->getPost('stato') !== 'completata' && $sottoAttivita['stato'] === 'completata') {
            $data['completata'] = false;
            $data['completata_il'] = null;
        }
        
        // Aggiorna la sottoattività
        if (!$this->sottoAttivitaModel->update($id, $data)) {
            $this->session->setFlashdata('error', 'Errore durante l\'aggiornamento della sottoattività.');
            return redirect()->back()->withInput();
        }
        
        $this->session->setFlashdata('success', 'Sottoattività aggiornata con successo.');
        return redirect()->to("/attivita/view/{$sottoAttivita['id_attivita']}");
    }
    
    /**
     * Elimina una sottoattività
     */
    public function eliminaSottoAttivita($id)
    {
        $sottoAttivitaModel = new SottoAttivitaModel();
        $sottoAttivita = $sottoAttivitaModel->find($id);
        
        if (empty($sottoAttivita)) {
            return redirect()->back()->with('error', 'Sottoattività non trovata');
        }
        
        $idAttivita = $sottoAttivita['id_attivita'];
        
        if ($sottoAttivitaModel->delete($id)) {
            return redirect()->to('attivita/view/' . $idAttivita)->with('message', 'Sottoattività eliminata con successo');
        } else {
            return redirect()->back()->with('error', 'Errore durante l\'eliminazione della sottoattività');
        }
    }
    
    /**
     * Restituisce le attività per un determinato progetto (per AJAX)
     */
    public function perProgetto($idProgetto)
    {
        $attivitaModel = new AttivitaModel();
        $attivita = $attivitaModel->where('id_progetto', $idProgetto)->findAll();
        
        return $this->response->setJSON([
            'success' => true,
            'attivita' => $attivita
        ]);
    }
} 