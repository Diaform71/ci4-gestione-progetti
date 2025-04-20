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
    
    public function __construct()
    {
        $this->progettoModel = new ProgettoModel();
        $this->anagraficaModel = new AnagraficaModel();
        $this->utentiModel = new UtentiModel();

        helper('progetto_helper');
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
        
        $data = [
            'title' => 'Dettagli Progetto',
            'progetto' => $progetto,
            'documenti' => $documenti,
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
} 