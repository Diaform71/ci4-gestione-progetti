<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\OrdineMaterialeModel;
use App\Models\AnagraficaModel;
use App\Models\ContattoModel;
use App\Models\AnagraficaContattoModel;
use App\Models\ProgettoModel;
use App\Models\UtentiModel;
use App\Models\OrdineMaterialeVoceModel;
use App\Models\Materiale;
use App\Models\OffertaFornitoreModel;
use App\Models\OffertaFornitoreVoceModel;
use App\Models\CondizioniPagamentoModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

final class OrdiniMaterialeController extends BaseController
{
    protected $ordineMaterialeModel;
    protected $anagraficaModel;
    protected $contattoModel;
    protected $anagraficaContattoModel;
    protected $progettoModel;
    protected $utentiModel;
    protected $materialeModel;
    protected $ordineMaterialeVoceModel;
    protected $offertaFornitoreModel;
    protected $offertaFornitoreVoceModel;
    protected $condizioniPagamentoModel;
    
    public function __construct()
    {
        helper(['form', 'date']);
        $this->ordineMaterialeModel = new OrdineMaterialeModel();
        $this->anagraficaModel = new AnagraficaModel();
        $this->contattoModel = new ContattoModel();
        $this->anagraficaContattoModel = new AnagraficaContattoModel();
        $this->progettoModel = new ProgettoModel();
        $this->utentiModel = new UtentiModel();
        $this->materialeModel = new Materiale();
        $this->ordineMaterialeVoceModel = new OrdineMaterialeVoceModel();
        $this->offertaFornitoreModel = new OffertaFornitoreModel();
        $this->offertaFornitoreVoceModel = new OffertaFornitoreVoceModel();
        $this->condizioniPagamentoModel = new CondizioniPagamentoModel();
    }
    
    /**
     * Mostra la lista degli ordini
     */
    public function index()
    {
        $data = [
            'title' => 'Ordini di Acquisto',
            'ordini' => $this->ordineMaterialeModel->getOrdiniWithRelations()
        ];
        
        return view('ordini_materiale/index', $data);
    }
    
    /**
     * Mostra i dettagli di un ordine
     */
    public function show($id = null)
    {
        $id = ($id) ? $id : $this->request->getPost('id');
        
        if (!$id) {
            return redirect()->to('/ordini-materiale')->with('error', 'ID ordine non specificato');
        }
        
        $ordine = $this->ordineMaterialeModel->getOrdineWithRelations((int)$id);
        
        if (!$ordine) {
            return redirect()->to('/ordini-materiale')->with('error', 'Ordine non trovato');
        }
        
        // Se la richiesta è AJAX, restituisci i dati in formato JSON
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'ordine' => $ordine
            ]);
        }
        
        // Ottieni le voci associate all'ordine
        $voci = $this->ordineMaterialeVoceModel->getVociByOrdine((int)$id);
        
        // Calcola totale dell'ordine
        $totale = $this->ordineMaterialeVoceModel->calcolaTotaleOrdine((int)$id);
        
        // Ottieni i progetti per i dropdown
        $progetti = $this->progettoModel->findAll();
        
        // Ottieni i template email di tipo ORDINE
        $emailTemplates = \Config\Database::connect()
                               ->table('email_templates')
                               ->where('tipo', 'ORDINE')
                               ->get()
                               ->getResultArray();
        
        // Ottieni tutti i contatti dell'anagrafica
        $contatti = [];
        $contattoPrincipale = null;
        if (!empty($ordine['id_anagrafica'])) {
            $contatti = $this->anagraficaContattoModel->getContattiByAnagrafica((int)$ordine['id_anagrafica']);
            
            // Trova il contatto principale
            foreach ($contatti as $contatto) {
                if (isset($contatto['principale']) && $contatto['principale'] == 1) {
                    $contattoPrincipale = $contatto;
                    break;
                }
            }
            
            // Se non troviamo un contatto principale, usa il primo contatto se disponibile
            if (!$contattoPrincipale && !empty($contatti)) {
                $contattoPrincipale = $contatti[0];
            }
        }
        
        // Carica lo storico delle email inviate
        $emailLogModel = new \App\Models\EmailLogModel();
        $email_logs = $emailLogModel->getByOrdine((int)$id);
        
        // Carica gli allegati dell'ordine
        $allegatoModel = new \App\Models\OrdiniAllegatiModel();
        $allegati = $allegatoModel->getAllegatiByOrdine((int)$id);
        
        $data = [
            'title' => 'Dettaglio Ordine di Acquisto',
            'ordine' => $ordine,
            'voci' => $voci,
            'totale' => $totale,
            'progetti' => $progetti,
            'emailTemplates' => $emailTemplates,
            'contatti' => $contatti,
            'contattoPrincipale' => $contattoPrincipale,
            'email_logs' => $email_logs,
            'allegati' => $allegati
        ];
        
        return view('ordini_materiale/show', $data);
    }
    
    /**
     * Mostra il form per creare un nuovo ordine
     */
    public function new()
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per creare un ordine.');
            return redirect()->to('/login');
        }
        
        // Ottieni tutte le offerte fornitore per il dropdown (rimuovo filtro per stato)
        $offerteFornitore = $this->offertaFornitoreModel->select('offerte_fornitore.*, anagrafiche.ragione_sociale as nome_fornitore')
            ->join('anagrafiche', 'anagrafiche.id = offerte_fornitore.id_anagrafica', 'left')
            // Filtro per stato rimosso per debugging
            ->orderBy('offerte_fornitore.data', 'DESC')
            ->findAll();
            
        // Debug: conta quante offerte sono state trovate
        $session->setFlashdata('info', 'Trovate ' . count($offerteFornitore) . ' offerte totali');
        
        // Ottieni le condizioni di pagamento
        $condizioniPagamento = $this->condizioniPagamentoModel->getCondizioni();
        
        $data = [
            'title' => 'Nuovo Ordine di Acquisto',
            'fornitori' => $this->anagraficaModel->where('fornitore', 1)->where('attivo', 1)->findAll(),
            'progetti' => $this->progettoModel->where('attivo', 1)->findAll(),
            'offerteFornitore' => $offerteFornitore,
            'condizioniPagamento' => $condizioniPagamento
        ];
        
        return view('ordini_materiale/create', $data);
    }
    
    /**
     * Crea un nuovo ordine
     */
    public function create()
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per creare un ordine.');
            return redirect()->to('/login');
        }
        
        // Imposta l'utente corrente come creatore dell'ordine
        $idUtente = $session->get('utente_id');
        
        if (empty($idUtente)) {
            $idUtente = 1; // Valore predefinito se l'utente non è in sessione
        }
        
        // Ottieni la data dal form e convertila in formato ISO
        $dataItaliana = $this->request->getPost('data') ?: date('d/m/Y');
        $dataISO = formatDateToISO($dataItaliana) ?: date('Y-m-d');
        
        // Ottieni la data di consegna prevista e convertila in formato ISO
        $dataConsegnaPrevistaItaliana = $this->request->getPost('data_consegna_prevista');
        $dataConsegnaPrevistaISO = empty($dataConsegnaPrevistaItaliana) ? null : formatDateToISO($dataConsegnaPrevistaItaliana);
        
        // Dati del form
        $data = [
            'numero' => $this->ordineMaterialeModel->generateNumeroOrdine(),
            'data' => $dataISO,
            'oggetto' => $this->request->getPost('oggetto'),
            'descrizione' => $this->request->getPost('descrizione'),
            'id_anagrafica' => $this->request->getPost('id_anagrafica') ? (int)$this->request->getPost('id_anagrafica') : null,
            'id_referente' => $this->request->getPost('id_referente') ? (int)$this->request->getPost('id_referente') : null,
            'id_progetto' => $this->request->getPost('id_progetto') ? (int)$this->request->getPost('id_progetto') : null,
            'stato' => 'bozza',
            'id_utente_creatore' => $idUtente,
            'condizioni_pagamento' => $this->request->getPost('condizioni_pagamento'),
            'id_condizione_pagamento' => $this->request->getPost('id_condizione_pagamento') ? (int)$this->request->getPost('id_condizione_pagamento') : null,
            'condizioni_consegna' => $this->request->getPost('condizioni_consegna'),
            'data_consegna_prevista' => $dataConsegnaPrevistaISO,
            'id_offerta_fornitore' => $this->request->getPost('id_offerta_fornitore') ? (int)$this->request->getPost('id_offerta_fornitore') : null,
            'note' => $this->request->getPost('note')
        ];
        
        // Validazione
        if (!$this->ordineMaterialeModel->validate($data)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->ordineMaterialeModel->errors());
        }
        
        // Salva l'ordine
        if ($this->ordineMaterialeModel->insert($data)) {
            $idOrdine = $this->ordineMaterialeModel->getInsertID();
            
            // Se è stata selezionata un'offerta fornitore, importa le voci
            if (!empty($data['id_offerta_fornitore'])) {
                $this->ordineMaterialeVoceModel->importaVociOfferta($idOrdine, $data['id_offerta_fornitore']);
            }
            
            // Aggiorna l'importo totale dell'ordine
            $this->aggiornaImportoTotale($idOrdine);
            
            // Verifica se è richiesta la creazione di una scadenza
            if ($this->request->getPost('crea_scadenza')) {
                $this->creaScadenzaPerOrdine($idOrdine);
            }
            
            $session->setFlashdata('success', 'Ordine creato con successo.');
            return redirect()->to('/ordini-materiale/' . $idOrdine);
        } else {
            return redirect()->back()
                            ->withInput()
                            ->with('errors', $this->ordineMaterialeModel->errors());
        }
    }
    
    /**
     * Crea una scadenza associata ad un ordine
     */
    private function creaScadenzaPerOrdine($idOrdine)
    {
        // Recupera i dati dell'ordine
        $ordine = $this->ordineMaterialeModel->find($idOrdine);
        if (!$ordine) {
            return false;
        }
        
        // Recupera i dati dal form
        $dataScadenzaItaliana = $this->request->getPost('data_scadenza');
        $dataScadenzaISO = formatDateToISO($dataScadenzaItaliana);
        
        // Prepara i dati per la creazione della scadenza
        $datiScadenza = [
            'titolo' => $this->request->getPost('titolo_scadenza'),
            'descrizione' => $this->request->getPost('descrizione_scadenza') . "\n\nOrdine #" . $ordine['numero'],
            'data_scadenza' => $dataScadenzaISO,
            'priorita' => $this->request->getPost('priorita_scadenza') ?: 'media',
            'stato' => $this->request->getPost('stato_scadenza') ?: 'da_iniziare',
            'id_utente_assegnato' => (int)$this->request->getPost('id_utente_assegnato'),
            'id_utente_creatore' => session()->get('utente_id'),
            'id_progetto' => $ordine['id_progetto'],
            'id_ordine_materiale' => $idOrdine,
            'completata' => 0
        ];
        
        // Crea la scadenza utilizzando il modello
        $scadenzaModel = new \App\Models\ScadenzaModel();
        $risultato = $scadenzaModel->creaScadenza($datiScadenza);
        
        if ($risultato) {
            session()->setFlashdata('message', 'Scadenza creata automaticamente per questo ordine.');
            return true;
        } else {
            session()->setFlashdata('warning', 'Impossibile creare la scadenza automatica.');
            return false;
        }
    }
    
    /**
     * Mostra il form per modificare un ordine
     */
    public function edit($id = null)
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per modificare un ordine.');
            return redirect()->to('/login');
        }
        
        $ordine = $this->ordineMaterialeModel->find($id);
        
        if (empty($ordine)) {
            throw new PageNotFoundException('Ordine non trovato');
        }
        
        // Se l'ordine non è più in stato bozza, non è possibile modificarlo
        if ($ordine['stato'] !== 'bozza') {
            $session->setFlashdata('error', 'Non è possibile modificare un ordine che non è in stato bozza.');
            return redirect()->to('/ordini-materiale/' . $id);
        }
        
        // Ottieni i contatti dell'anagrafica selezionata
        $contatti = $this->anagraficaContattoModel->getContattiByAnagrafica((int)$ordine['id_anagrafica']);
        
        // Ottieni tutte le offerte fornitore per il dropdown (rimuovo filtro per stato)
        $offerteFornitore = $this->offertaFornitoreModel->select('offerte_fornitore.*, anagrafiche.ragione_sociale as nome_fornitore')
            ->join('anagrafiche', 'anagrafiche.id = offerte_fornitore.id_anagrafica', 'left')
            // Filtro per stato rimosso per debugging
            ->orderBy('offerte_fornitore.data', 'DESC')
            ->findAll();
        
        $data = [
            'title' => 'Modifica Ordine di Acquisto',
            'ordine' => $ordine,
            'fornitori' => $this->anagraficaModel->where('fornitore', 1)->where('attivo', 1)->findAll(),
            'progetti' => $this->progettoModel->where('attivo', 1)->findAll(),
            'contatti' => $contatti,
            'offerteFornitore' => $offerteFornitore
        ];
        
        return view('ordini_materiale/edit', $data);
    }
    
    /**
     * Aggiorna un ordine esistente
     */
    public function update($id = null)
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per modificare un ordine.');
            return redirect()->to('/login');
        }
        
        $ordine = $this->ordineMaterialeModel->find($id);
        
        if (empty($ordine)) {
            throw new PageNotFoundException('Ordine non trovato');
        }
        
        // Se l'ordine non è più in stato bozza, non è possibile modificarlo
        if ($ordine['stato'] !== 'bozza') {
            $session->setFlashdata('error', 'Non è possibile modificare un ordine che non è in stato bozza.');
            return redirect()->to('/ordini-materiale/' . $id);
        }
        
        // Dati del form
        $dataItaliana = $this->request->getPost('data');
        $dataISO = formatDateToISO($dataItaliana) ?: $ordine['data'];
        
        // Ottieni la data di consegna prevista e convertila in formato ISO
        $dataConsegnaPrevistaItaliana = $this->request->getPost('data_consegna_prevista');
        $dataConsegnaPrevistaISO = empty($dataConsegnaPrevistaItaliana) ? null : formatDateToISO($dataConsegnaPrevistaItaliana);
        
        $data = [
            'data' => $dataISO,
            'oggetto' => $this->request->getPost('oggetto'),
            'descrizione' => $this->request->getPost('descrizione'),
            'id_anagrafica' => $this->request->getPost('id_anagrafica') ? (int)$this->request->getPost('id_anagrafica') : null,
            'id_referente' => $this->request->getPost('id_referente') ? (int)$this->request->getPost('id_referente') : null,
            'id_progetto' => $this->request->getPost('id_progetto') ? (int)$this->request->getPost('id_progetto') : null,
            'condizioni_pagamento' => $this->request->getPost('condizioni_pagamento'),
            'condizioni_consegna' => $this->request->getPost('condizioni_consegna'),
            'data_consegna_prevista' => $dataConsegnaPrevistaISO,
            'note' => $this->request->getPost('note')
        ];
        
        // Validazione
        if (!$this->ordineMaterialeModel->validate(array_merge($ordine, $data))) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->ordineMaterialeModel->errors());
        }
        
        // Aggiorna l'ordine
        if ($this->ordineMaterialeModel->update($id, $data)) {
            // Aggiorna l'importo totale dell'ordine
            $this->aggiornaImportoTotale($id);
            
            $session->setFlashdata('success', 'Ordine aggiornato con successo.');
            return redirect()->to('/ordini-materiale/' . $id);
        } else {
            return redirect()->back()
                            ->withInput()
                            ->with('errors', $this->ordineMaterialeModel->errors());
        }
    }
    
    /**
     * Cambia lo stato di un ordine
     */
    public function cambiaStato($id = null)
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per cambiare lo stato di un ordine.');
            return redirect()->to('/login');
        }
        
        $ordine = $this->ordineMaterialeModel->find($id);
        
        if (empty($ordine)) {
            throw new PageNotFoundException('Ordine non trovato');
        }
        
        // Per supportare sia il metodo POST che GET (dai link della UI)
        $nuovoStato = $this->request->getPost('stato') ?? $this->request->getGet('stato');
        
        // Verifica che lo stato sia valido
        $statiValidi = ['bozza', 'in_attesa', 'confermato', 'inviato', 'completato', 'annullato', 'in_consegna', 'consegnato'];
        if (!in_array($nuovoStato, $statiValidi)) {
            $session->setFlashdata('error', 'Stato non valido. Gli stati consentiti sono: ' . implode(', ', $statiValidi));
            return redirect()->to('/ordini-materiale/' . $id);
        }
        
        $data = ['stato' => $nuovoStato];
        
        // Gestione campi data in base allo stato
        switch ($nuovoStato) {
            case 'in_attesa':
                // Quando un ordine viene messo in attesa, aggiungiamo questa informazione nel log dell'ordine
                log_message('info', 'Ordine ID ' . $id . ' impostato in stato "in attesa" dall\'utente ' . $session->get('utente_id'));
                // Nessun campo data da aggiornare per lo stato in_attesa
                break;
                
            case 'inviato':
                // Se lo stato è "inviato", imposta la data di invio
                if (empty($ordine['data_invio'])) {
                    $data['data_invio'] = date('Y-m-d H:i:s');
                }
                break;
                
            case 'confermato':
                // Se lo stato è "confermato", imposta la data di accettazione
                if (empty($ordine['data_accettazione'])) {
                    $data['data_accettazione'] = date('Y-m-d H:i:s');
                }
                break;
                
            case 'completato':
                // Se lo stato è "completato", imposta la data di completamento
                $data['data_completamento'] = date('Y-m-d H:i:s');
                
                // Se non è stata impostata una data di consegna effettiva, la impostiamo alla data odierna
                if (empty($ordine['data_consegna_effettiva'])) {
                    $data['data_consegna_effettiva'] = date('Y-m-d');
                }
                break;
 
            case 'in_consegna':
                // Se lo stato è "in consegna", imposta la data di consegna prevista
                if (empty($ordine['data_consegna_prevista'])) {
                    $data['data_consegna_prevista'] = date('Y-m-d');
                }   
                break;

            case 'consegnato':
                // Se lo stato è "consegnato", imposta la data di consegna effettiva
                $data['data_consegna_effettiva'] = date('Y-m-d');
                break;    
                
            case 'annullato':
                // Quando un ordine viene annullato, registriamo la data di annullamento
                $data['data_annullamento'] = date('Y-m-d H:i:s');
                break;
        }
        
        // Debug - registriamo i dati che stiamo per salvare
        log_message('debug', 'Aggiornamento stato ordine ' . $id . ' a ' . $nuovoStato . '. Dati: ' . print_r($data, true));
        
        // Aggiorna lo stato
        if (!$this->ordineMaterialeModel->update($id, $data)) {
            log_message('error', 'Errore nell\'aggiornamento dello stato dell\'ordine ' . $id . ': ' . print_r($this->ordineMaterialeModel->errors(), true));
            $session->setFlashdata('error', 'Si è verificato un errore durante l\'aggiornamento dello stato.');
            return redirect()->to('/ordini-materiale/' . $id);
        }
        
        $messaggi = [
            'bozza' => 'Ordine impostato come bozza.',
            'in_attesa' => 'Ordine impostato in attesa.',
            'confermato' => 'Ordine confermato con successo.',
            'inviato' => 'Ordine impostato come inviato al fornitore.',
            'completato' => 'Ordine contrassegnato come completato.',
            'annullato' => 'Ordine annullato con successo.',
            'in_consegna' => 'Ordine impostato come in consegna.',
            'consegnato' => 'Ordine contrassegnato come consegnato.'
        ];
        
        $message = $messaggi[$nuovoStato] ?? 'Stato dell\'ordine aggiornato con successo.';
        
        $session->setFlashdata('success', $message);
        return redirect()->to('/ordini-materiale/' . $id);
    }
    
    /**
     * Elimina un ordine (soft delete)
     */
    public function delete($id = null)
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per eliminare un ordine.');
            return redirect()->to('/login');
        }
        
        $ordine = $this->ordineMaterialeModel->find($id);
        
        if (empty($ordine)) {
            throw new PageNotFoundException('Ordine non trovato');
        }
        
        // Non permettere l'eliminazione di ordini inviati o consegnati
        if (in_array($ordine['stato'], ['inviato', 'confermato', 'in_consegna', 'consegnato'])) {
            $session->setFlashdata('error', 'Non è possibile eliminare un ordine che è stato inviato, confermato o consegnato.');
            return redirect()->to('/ordini-materiale/' . $id);
        }
        
        $this->ordineMaterialeModel->delete($id);
        
        $session->setFlashdata('success', 'Ordine eliminato con successo.');
        return redirect()->to('/ordini-materiale');
    }
    
    /**
     * Visualizza gli ordini per un fornitore specifico
     */
    public function perFornitore($idAnagrafica = null)
    {
        $fornitore = $this->anagraficaModel->find($idAnagrafica);
        
        if (empty($fornitore)) {
            throw new PageNotFoundException('Fornitore non trovato');
        }
        
        $data = [
            'title' => 'Ordini per ' . $fornitore['ragione_sociale'],
            'fornitore' => $fornitore,
            'ordini' => $this->ordineMaterialeModel->getOrdiniByFornitore((int)$idAnagrafica)
        ];
        
        return view('ordini_materiale/per_fornitore', $data);
    }
    
    /**
     * Visualizza gli ordini per un progetto specifico
     */
    public function perProgetto($idProgetto = null)
    {
        $progetto = $this->progettoModel->find($idProgetto);
        
        if (empty($progetto)) {
            throw new PageNotFoundException('Progetto non trovato');
        }
        
        $data = [
            'title' => 'Ordini per il progetto ' . $progetto['nome'],
            'progetto' => $progetto,
            'ordini' => $this->ordineMaterialeModel->getOrdiniByProgetto((int)$idProgetto)
        ];
        
        return view('ordini_materiale/per_progetto', $data);
    }
    
    /**
     * Carica i contatti di un'anagrafica tramite AJAX
     */
    public function getContattiByAnagrafica()
    {
        $idAnagrafica = $this->request->getPost('id_anagrafica');
        
        if (!$idAnagrafica) {
            return $this->response->setJSON(['success' => false, 'contatti' => [], 'error' => 'ID anagrafica non fornito']);
        }
        
        $contatti = $this->anagraficaContattoModel->getContattiByAnagrafica((int)$idAnagrafica);
        
        // Log per debug
        log_message('info', 'Richiesta contatti per id_anagrafica: ' . $idAnagrafica . ' - Trovati: ' . count($contatti));
        
        return $this->response->setJSON([
            'success' => true,
            'contatti' => $contatti,
            'count' => count($contatti),
            'id_anagrafica' => $idAnagrafica
        ]);
    }
    
    /**
     * Carica gli ordini di un fornitore tramite AJAX
     */
    public function getOrdiniByFornitore()
    {
        $idAnagrafica = $this->request->getPost('id_anagrafica');
        
        if (!$idAnagrafica) {
            return $this->response->setJSON(['success' => false, 'ordini' => [], 'error' => 'ID anagrafica non fornito']);
        }
        
        $ordini = $this->ordineMaterialeModel->getOrdiniByFornitore((int)$idAnagrafica);
        
        // Log per debug
        log_message('info', 'Richiesta ordini per id_anagrafica: ' . $idAnagrafica . ' - Trovati: ' . count($ordini));
        
        return $this->response->setJSON([
            'success' => true,
            'ordini' => $ordini,
            'count' => count($ordini),
            'id_anagrafica' => $idAnagrafica
        ]);
    }

    /**
     * Aggiunge un materiale all'ordine
     */
    public function aggiungiMateriale($id_ordine)
    {
        // Verifica che l'ordine esista
        $ordine = $this->ordineMaterialeModel->find($id_ordine);
        if (empty($ordine)) {
            return redirect()->to('ordini-materiale')->with('error', 'Ordine non trovato');
        }

        // Validazione del form
        $rules = [
            'id_materiale' => 'required|numeric',
            'quantita' => 'required|numeric|greater_than[0]',
            'id_progetto' => 'permit_empty|numeric',
            'unita_misura' => 'permit_empty|max_length[20]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Dati non validi. Verifica i campi e riprova.');
        }

        $id_materiale = $this->request->getPost('id_materiale');
        $quantita = $this->request->getPost('quantita');
        $id_progetto = $this->request->getPost('id_progetto') ?: null;
        $unita_misura = $this->request->getPost('unita_misura') ?: 'pz';
        $prezzo_unitario = $this->request->getPost('prezzo_unitario') ?: 0;
        $sconto = $this->request->getPost('sconto') ?: 0;
        $note = $this->request->getPost('note');

        // Verifica che il materiale esista
        $materialeModel = new Materiale();
        $materiale = $materialeModel->find($id_materiale);
        if (empty($materiale)) {
            return redirect()->back()->with('error', 'Materiale non trovato');
        }

        // Verifica se il progetto esiste (se specificato)
        if ($id_progetto) {
            $progetto = $this->progettoModel->find($id_progetto);
            if (empty($progetto)) {
                return redirect()->back()->with('error', 'Progetto non trovato');
            }
        }

        // Verifica se il materiale è già presente in questo ordine
        if ($this->ordineMaterialeVoceModel->esisteMateriale((int)$id_ordine, (int)$id_materiale)) {
            return redirect()->back()->with('error', 'Questo materiale è già presente nell\'ordine');
        }

        // Calcola l'importo
        $importo = $this->ordineMaterialeVoceModel->calcolaImporto(
            (float)$prezzo_unitario,
            (float)$quantita,
            (float)$sconto
        );

        // Inserimento nella tabella ordini_materiale_voci
        $data = [
            'id_ordine' => $id_ordine,
            'id_materiale' => $id_materiale,
            'codice' => $materiale['codice'],
            'descrizione' => $materiale['descrizione'],
            'quantita' => $quantita,
            'prezzo_unitario' => $prezzo_unitario,
            'importo' => $importo,
            'unita_misura' => $unita_misura,
            'sconto' => $sconto,
            'id_progetto' => $id_progetto,
            'note' => $note
        ];

        if ($this->ordineMaterialeVoceModel->insert($data)) {
            // Aggiorna l'importo totale dell'ordine
            $this->aggiornaImportoTotale($id_ordine);
            
            return redirect()->to("ordini-materiale/{$id_ordine}")->with('success', 'Materiale aggiunto con successo');
        } else {
            return redirect()->back()->with('error', 'Si è verificato un errore durante l\'aggiunta del materiale');
        }
    }

    /**
     * Aggiunge un nuovo materiale all'archivio e lo associa all'ordine
     */
    public function aggiungiNuovoMateriale($id_ordine)
    {
        // Verifica che l'ordine esista
        $ordine = $this->ordineMaterialeModel->find($id_ordine);
        if (empty($ordine)) {
            return redirect()->to('ordini-materiale')->with('error', 'Ordine non trovato');
        }

        // Validazione del form per il nuovo materiale e la quantità
        $rules = [
            'codice' => 'required|min_length[1]|max_length[50]|is_unique[materiali.codice]',
            'descrizione' => 'required',
            'quantita' => 'required|numeric|greater_than[0]',
            'id_progetto' => 'permit_empty|numeric',
            'unita_misura' => 'permit_empty|max_length[20]',
            'prezzo_unitario' => 'permit_empty|numeric',
            'sconto' => 'permit_empty|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Dati non validi. Verifica i campi e riprova.')->withInput()->with('errors', $this->validator->getErrors());
        }

        // Dati per il nuovo materiale
        $datiMateriale = [
            'codice' => $this->request->getPost('codice'),
            'descrizione' => $this->request->getPost('descrizione'),
            'materiale' => $this->request->getPost('materiale'),
            'produttore' => $this->request->getPost('produttore'),
            'commerciale' => $this->request->getPost('commerciale') ? 1 : 0,
            'meccanica' => $this->request->getPost('meccanica') ? 1 : 0,
            'elettrica' => $this->request->getPost('elettrica') ? 1 : 0,
            'pneumatica' => $this->request->getPost('pneumatica') ? 1 : 0,
            'in_produzione' => 1 // Nuovo materiale è in produzione di default
        ];

        // Salva il nuovo materiale
        $materialeModel = new Materiale();
        if (!$materialeModel->insert($datiMateriale)) {
            return redirect()->back()->with('error', 'Errore durante il salvataggio del nuovo materiale: ' . json_encode($materialeModel->errors()))->withInput();
        }

        // Recupera l'ID del materiale appena inserito
        $id_materiale = $materialeModel->getInsertID();

        // Dati per l'associazione all'ordine
        $quantita = $this->request->getPost('quantita');
        $id_progetto = $this->request->getPost('id_progetto') ?: null;
        $unita_misura = $this->request->getPost('unita_misura') ?: 'pz';
        $prezzo_unitario = $this->request->getPost('prezzo_unitario') ?: 0;
        $sconto = $this->request->getPost('sconto') ?: 0;
        $note = $this->request->getPost('note');

        // Calcola l'importo
        $importo = $this->ordineMaterialeVoceModel->calcolaImporto(
            (float)$prezzo_unitario,
            (float)$quantita,
            (float)$sconto
        );

        // Inserimento nella tabella ordini_materiale_voci
        $dataVoce = [
            'id_ordine' => $id_ordine,
            'id_materiale' => $id_materiale,
            'codice' => $datiMateriale['codice'],
            'descrizione' => $datiMateriale['descrizione'],
            'quantita' => $quantita,
            'prezzo_unitario' => $prezzo_unitario,
            'importo' => $importo,
            'unita_misura' => $unita_misura,
            'sconto' => $sconto,
            'id_progetto' => $id_progetto,
            'note' => $note
        ];
        
        if ($this->ordineMaterialeVoceModel->insert($dataVoce)) {
            // Aggiorna l'importo totale dell'ordine
            $this->aggiornaImportoTotale($id_ordine);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Nuovo materiale creato e aggiunto all\'ordine con successo',
                'redirect' => site_url("ordini-materiale/{$id_ordine}")
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Errore durante l\'aggiunta alla voce dell\'ordine: ' . json_encode($this->ordineMaterialeVoceModel->errors())
            ]);
        }
    }

    /**
     * Aggiorna un materiale nell'ordine
     */
    public function aggiornaMateriale($id_ordine)
    {
        // Verifica che l'ordine esista
        $ordine = $this->ordineMaterialeModel->find($id_ordine);
        if (empty($ordine)) {
            return redirect()->to('ordini-materiale')->with('error', 'Ordine non trovato');
        }

        // Validazione del form
        $rules = [
            'id' => 'required|numeric',
            'id_materiale' => 'required|numeric',
            'quantita' => 'required|numeric|greater_than[0]',
            'id_progetto' => 'permit_empty|numeric',
            'unita_misura' => 'permit_empty|max_length[20]',
            'prezzo_unitario' => 'permit_empty|numeric',
            'sconto' => 'permit_empty|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Dati non validi. Verifica i campi e riprova.');
        }

        $id = $this->request->getPost('id');
        $id_materiale = $this->request->getPost('id_materiale');
        $quantita = $this->request->getPost('quantita');
        $id_progetto = $this->request->getPost('id_progetto') ?: null;
        $unita_misura = $this->request->getPost('unita_misura') ?: 'pz';
        $prezzo_unitario = $this->request->getPost('prezzo_unitario') ?: 0;
        $sconto = $this->request->getPost('sconto') ?: 0;
        $note = $this->request->getPost('note');

        // Verifica che il record esista
        $record = $this->ordineMaterialeVoceModel->find($id);
        if (empty($record) || $record['id_ordine'] != $id_ordine) {
            return redirect()->back()->with('error', 'Record non trovato');
        }

        // Calcola l'importo
        $importo = $this->ordineMaterialeVoceModel->calcolaImporto(
            (float)$prezzo_unitario,
            (float)$quantita,
            (float)$sconto
        );

        // Aggiorna il record
        $data = [
            'quantita' => $quantita,
            'id_progetto' => $id_progetto,
            'unita_misura' => $unita_misura,
            'prezzo_unitario' => $prezzo_unitario,
            'sconto' => $sconto,
            'importo' => $importo,
            'note' => $note
        ];

        if ($this->ordineMaterialeVoceModel->update($id, $data)) {
            // Aggiorna l'importo totale dell'ordine
            $this->aggiornaImportoTotale($id_ordine);
            
            return redirect()->to("ordini-materiale/{$id_ordine}")->with('success', 'Materiale aggiornato con successo');
        } else {
            return redirect()->back()->with('error', 'Si è verificato un errore durante l\'aggiornamento del materiale');
        }
    }

    /**
     * Rimuove un materiale dall'ordine
     */
    public function rimuoviMateriale($id_ordine, $id_record)
    {
        $session = session();
        
        // Controlla che l'ordine esista
        $ordine = $this->ordineMaterialeModel->find($id_ordine);
        if (!$ordine) {
            $session->setFlashdata('error', 'Ordine non trovato');
            return redirect()->to('/ordini-materiale');
        }
        
        // Se l'ordine è stato già inviato o confermato, non è possibile modificarlo
        if (in_array($ordine['stato'], ['inviato', 'confermato', 'completato', 'in_consegna', 'consegnato'])) {
            $session->setFlashdata('error', 'Non è possibile modificare un ordine in stato ' . $ordine['stato']);
            return redirect()->to('/ordini-materiale/' . $id_ordine);
        }
        
        // Ottieni il record da eliminare per verificare che appartenga all'ordine
        $voce = $this->ordineMaterialeVoceModel->find($id_record);
        if (!$voce || $voce['id_ordine'] != $id_ordine) {
            $session->setFlashdata('error', 'Articolo non trovato o non appartiene a questo ordine');
            return redirect()->to('/ordini-materiale/' . $id_ordine);
        }
        
        // Usa una transazione per garantire l'integrità dei dati
        $db = \Config\Database::connect();
        $db->transStart();
        
        // Rimuovi il materiale
        $deleted = $this->ordineMaterialeVoceModel->delete($id_record);
        
        // Aggiorna l'importo totale dell'ordine
        $importoAggiornato = $this->aggiornaImportoTotale($id_ordine);
        
        $db->transComplete();
        
        // Verifica se entrambe le operazioni sono andate a buon fine
        if ($deleted && $importoAggiornato && $db->transStatus()) {
            $session->setFlashdata('success', 'Materiale rimosso dall\'ordine e importo totale aggiornato');
            
            // Aggiungi informazioni di debug
            log_message('debug', "Articolo ID: {$id_record} rimosso. Importo totale aggiornato: " . 
                        ($this->ordineMaterialeModel->find($id_ordine)['importo_totale'] ?? 'N/A'));
        } else {
            $session->setFlashdata('error', 'Errore durante la rimozione del materiale o l\'aggiornamento dell\'importo');
            
            // Aggiungi informazioni di debug
            log_message('error', "Errore nella rimozione dell'articolo ID: {$id_record}. " . 
                        "Deleted: " . ($deleted ? 'true' : 'false') . 
                        ", ImportoAggiornato: " . ($importoAggiornato ? 'true' : 'false') . 
                        ", TransStatus: " . ($db->transStatus() ? 'true' : 'false'));
        }
        
        return redirect()->to('/ordini-materiale/' . $id_ordine);
    }

    /**
     * Invia una email con l'ordine di acquisto
     */
    public function inviaEmail($id)
    {
        // Controllo che l'ordine esista
        $ordine = $this->ordineMaterialeModel->find($id);
        if (!$ordine) {
            return redirect()->to('/ordini-materiale')->with('error', 'Ordine non trovato');
        }
        
        // Controllo che sia una richiesta POST con CSRF valido
        if ($this->request->getMethod() !== 'POST') {
            log_message('error', "InviaEmail - Metodo non consentito: " . $this->request->getMethod());
            return redirect()->to('/ordini-materiale/' . $id)->with('error', 'Metodo non consentito');
        }
        
        // Debug dettagliato
        log_message('debug', "InviaEmail - Dati sessione utente: " . json_encode([
            'id' => session()->get('utente_id'),
            'email' => session()->get('utente_email'),
            'nome' => session()->get('utente_nome'),
            'cognome' => session()->get('utente_cognome')
        ]));
        
        // Validazione
        $rules = [
            'destinatario' => 'required',
            'oggetto' => 'required|min_length[3]|max_length[255]',
            'corpo' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->to('/ordini-materiale/' . $id)->with('errors', $this->validator->getErrors());
        }
        
        // Prepara i dati dell'email
        $destinatari = $this->request->getPost('destinatario');
        if (!is_array($destinatari)) {
            $destinatari = explode(',', $destinatari);
        }
        
        $cc = $this->request->getPost('cc');
        if (!empty($cc) && !is_array($cc)) {
            $cc = explode(',', $cc);
        }
        
        $ccn = $this->request->getPost('ccn');
        if (!empty($ccn) && !is_array($ccn)) {
            $ccn = explode(',', $ccn);
        }
        
        $oggetto = $this->request->getPost('oggetto');
        $corpo = $this->request->getPost('corpo');
        
        // Verifica se la tabella dei materiali deve essere inclusa
        $tabellaMateriali = $this->request->getPost('tabella_materiali') == '1';
        
        // Se la tabella dei materiali è richiesta e non c'è già il segnaposto {{materiali}} nel corpo
        if ($tabellaMateriali && strpos($corpo, '{{materiali}}') === false) {
            // Ottieni i materiali dell'ordine
            $voci = $this->ordineMaterialeVoceModel->getVociByOrdine((int)$id);
            
            // Aggiungi la tabella dei materiali solo se ci sono voci
            if (!empty($voci)) {
                $materialiHtml = '<hr><h4>Materiali Ordinati</h4>';
                $materialiHtml .= '<table style="border-collapse: collapse; width: 100%; margin-top: 10px; margin-bottom: 20px;">';
                $materialiHtml .= '<thead style="background-color: #f2f2f2;">';
                $materialiHtml .= '<tr>';
                $materialiHtml .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Codice</th>';
                $materialiHtml .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Descrizione</th>';
                $materialiHtml .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Quantità</th>';
                $materialiHtml .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Unità</th>';
                $materialiHtml .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Prezzo</th>';
                $materialiHtml .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Importo</th>';
                $materialiHtml .= '</tr>';
                $materialiHtml .= '</thead>';
                $materialiHtml .= '<tbody>';
                
                $totale = 0;
                foreach ($voci as $materiale) {
                    $materialiHtml .= '<tr>';
                    $materialiHtml .= '<td style="border: 1px solid #ddd; padding: 8px;">' . esc($materiale['codice']) . '</td>';
                    $materialiHtml .= '<td style="border: 1px solid #ddd; padding: 8px;">' . esc($materiale['descrizione']) . '</td>';
                    $materialiHtml .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: right;">' . esc($materiale['quantita']) . '</td>';
                    $materialiHtml .= '<td style="border: 1px solid #ddd; padding: 8px;">' . esc($materiale['unita_misura']) . '</td>';
                    $materialiHtml .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: right;">' . number_format((float)$materiale['prezzo_unitario'], 2, ',', '.') . ' €</td>';
                    $materialiHtml .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: right;">' . number_format((float)$materiale['importo'], 2, ',', '.') . ' €</td>';
                    $materialiHtml .= '</tr>';
                    $totale += (float)$materiale['importo'];
                }
                
                $materialiHtml .= '</tbody>';
                $materialiHtml .= '<tfoot>';
                $materialiHtml .= '<tr>';
                $materialiHtml .= '<td colspan="5" style="border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold;">Totale:</td>';
                $materialiHtml .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold;">' . number_format($totale, 2, ',', '.') . ' €</td>';
                $materialiHtml .= '</tr>';
                $materialiHtml .= '</tfoot>';
                $materialiHtml .= '</table>';
                
                $corpo .= $materialiHtml;
            }
        }
        
        // Prepara allegati
        $allegati = [];
        
        // Gestione allegati caricati
        if ($files = $this->request->getFiles()) {
            foreach ($files['allegati'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move(TEMP_UPLOAD_PATH, $newName);
                    $allegati[] = $newName;
                }
            }
        }
        
        // Allegato PDF
        $attach_pdf = ['pdf' => 'false'];
        if ($this->request->getPost('allega_pdf')) {
            $attach_pdf = [
                'pdf' => 'true',
                'id_ordine' => $id,
                'numero' => $ordine['numero']
            ];
        }
        
        // Carica ImpostazioniModel per recuperare l'email di sistema
        $impostazioniModel = new \App\Models\ImpostazioniModel();
        $systemEmail = $impostazioniModel->getImpSistema('email_from_address', '');
        $systemName = $impostazioniModel->getImpSistema('email_from_name', '');
        
        // Ottieni i dati dell'utente corrente
        $userId = session()->get('utente_id');
        $userEmail = session()->get('utente_email');
        $userName = session()->get('utente_nome') . ' ' . session()->get('utente_cognome');
        
        // Se l'email dell'utente non è valida, verifica nel database e nelle impostazioni
        if (empty($userEmail) || !filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            log_message('warning', "InviaEmail - Email utente in sessione non valida o mancante: " . ($userEmail ?? 'vuota'));
            
            // Cerca nelle impostazioni personalizzate dell'utente
            if (!empty($userId)) {
                $emailUtente = $impostazioniModel->getImpUtente('email_from_address', (int)$userId, '');
                if (!empty($emailUtente) && filter_var($emailUtente, FILTER_VALIDATE_EMAIL)) {
                    $userEmail = $emailUtente;
                    $nomeUtente = $impostazioniModel->getImpUtente('email_from_name', (int)$userId, $userName);
                    if (!empty($nomeUtente)) {
                        $userName = $nomeUtente;
                    }
                    // Aggiorna la sessione
                    session()->set('utente_email', $userEmail);
                    log_message('info', "InviaEmail - Email utente recuperata dalle impostazioni: {$userEmail}");
                } else {
                    // Cerca l'email dell'utente nel database
                    $utentiModel = new \App\Models\UtentiModel();
                    $utente = $utentiModel->find($userId);
                    if ($utente && !empty($utente['email']) && filter_var($utente['email'], FILTER_VALIDATE_EMAIL)) {
                        $userEmail = $utente['email'];
                        // Aggiorna la sessione
                        session()->set('utente_email', $userEmail);
                        log_message('info', "InviaEmail - Email utente recuperata dal DB: {$userEmail}");
                    }
                }
            }
        }
        
        // Se ancora non è valida, usa l'email di sistema
        if (empty($userEmail) || !filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            if (!empty($systemEmail) && filter_var($systemEmail, FILTER_VALIDATE_EMAIL)) {
                $userEmail = $systemEmail;
                log_message('notice', "InviaEmail - Usando email di sistema come fallback: {$systemEmail}");
            } else {
                log_message('error', "InviaEmail - Nessuna email valida disponibile per inviare l'email");
                return redirect()->to('/ordini-materiale/' . $id)->with('error', 'Impossibile inviare l\'email: nessun mittente valido disponibile. Contatta l\'amministratore.');
            }
        }
        
        // Configura il mittente
        $from = [
            'email' => $userEmail,
            'name' => $userName
        ];
        
        log_message('debug', "InviaEmail - Dettagli mittente: " . json_encode($from));
        
        // Carica l'helper per l'invio email
        helper('CIMail');
        
        // Invia l'email
        $result = send_email($destinatari[0], $oggetto, $corpo, $from, $allegati, $attach_pdf, $cc, $ccn);
        
        if ($result['status']) {
            // Registra l'email nel log
            log_email($destinatari, $cc, $ccn, $oggetto, $corpo, $id, 'ORDINE', 'inviato', null, $allegati);
            
            // Aggiorna lo stato dell'ordine
            $this->ordineMaterialeModel->update($id, [
                'stato' => 'inviato',
                'data_invio' => date('Y-m-d H:i:s')
            ]);
            
            return redirect()->to('/ordini-materiale/' . $id)->with('success', 'Email inviata con successo');
        } else {
            // Registra l'errore nel log
            log_email($destinatari, $cc, $ccn, $oggetto, $corpo, $id, 'ORDINE', 'errore', $result['msg'], $allegati);
            
            return redirect()->to('/ordini-materiale/' . $id)->with('error', 'Errore nell\'invio dell\'email: ' . $result['msg']);
        }
    }

    /**
     * Visualizza lo storico delle email per un specifico ordine
     */
    public function emailLog($id)
    {
        // Verifica che l'ordine esista
        $ordine = $this->ordineMaterialeModel->find($id);
        if (!$ordine) {
            return redirect()->to('/ordini-materiale')->with('error', 'Ordine non trovato');
        }
        
        // Carica il modello delle email logs
        $emailLogModel = new \App\Models\EmailLogModel();
        $emails = $emailLogModel->getByOrdine((int)$id);
        
        $data = [
            'title' => 'Storico Email - Ordine #' . $ordine['numero'],
            'ordine' => $ordine,
            'emails' => $emails
        ];
        
        return view('ordini_materiale/email_log', $data);
    }
    
    /**
     * Visualizza i dettagli di una specifica email
     */
    public function visualizzaEmail($id)
    {
        // Carica il modello delle email logs
        $emailLogModel = new \App\Models\EmailLogModel();
        $email = $emailLogModel->find($id);
        
        if (!$email) {
            return redirect()->back()->with('error', 'Email non trovata');
        }
        
        // Ottieni l'ordine associato
        $ordine = $this->ordineMaterialeModel->find($email['id_riferimento']);
        if (!$ordine) {
            return redirect()->to('/ordini-materiale')->with('error', 'Ordine non trovato');
        }
        
        $data = [
            'title' => 'Dettaglio Email',
            'email' => $email,
            'ordine' => $ordine
        ];
        
        return view('ordini_materiale/visualizza_email', $data);
    }
    
    /**
     * Risponde a una email precedentemente inviata
     */
    public function rispondiEmail($id)
    {
        // Debug: Traccia l'ingresso nella funzione e il metodo HTTP
        log_message('debug', "RispondiEmail - Inizio elaborazione per email ID: {$id}");
        log_message('debug', "RispondiEmail - Metodo HTTP: " . $this->request->getMethod());
        
        // Debug dettagliato sessione
        log_message('debug', "RispondiEmail - Dati sessione utente: " . json_encode([
            'id' => session()->get('utente_id'),
            'email' => session()->get('utente_email'),
            'nome' => session()->get('utente_nome'),
            'cognome' => session()->get('utente_cognome')
        ]));
        
        // Carica il modello delle email logs
        $emailLogModel = new \App\Models\EmailLogModel();
        $emailOriginale = $emailLogModel->find($id);
        
        if (!$emailOriginale) {
            log_message('error', "RispondiEmail - Email originale non trovata con ID: {$id}");
            return redirect()->back()->with('error', 'Email originale non trovata');
        }
        
        // Debug: traccia l'email originale trovata
        log_message('debug', "RispondiEmail - Email originale trovata: " . json_encode($emailOriginale));
        
        // Ottieni l'ordine associato
        $id_ordine = $emailOriginale['id_riferimento'];
        $ordine = $this->ordineMaterialeModel->find($id_ordine);
        if (!$ordine) {
            log_message('error', "RispondiEmail - Ordine non trovato con ID: {$id_ordine}");
            return redirect()->to('/ordini-materiale')->with('error', 'Ordine non trovato');
        }
        
        // Debug: traccia l'ordine trovato
        log_message('debug', "RispondiEmail - Ordine trovato: " . json_encode($ordine));
        
        // Controllo che sia una richiesta POST con CSRF valido
        if ($this->request->getMethod() !== 'POST') {
            log_message('error', "RispondiEmail - Metodo non consentito: " . $this->request->getMethod());
            return redirect()->to('/ordini-materiale/' . $id_ordine)->with('error', 'Metodo non consentito');
        }
        
        // Debug: Traccia tutti i dati inviati dal form
        log_message('debug', "RispondiEmail - Dati POST ricevuti: " . json_encode($this->request->getPost()));
        log_message('debug', "RispondiEmail - Dati FILES ricevuti: " . json_encode($this->request->getFiles()));
        
        // Validazione
        $rules = [
            'destinatario' => 'required',
            'oggetto' => 'required|min_length[3]|max_length[255]',
            'corpo' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            log_message('error', "RispondiEmail - Errori di validazione: " . json_encode($this->validator->getErrors()));
            return redirect()->to('/ordini-materiale/' . $id_ordine)->with('errors', $this->validator->getErrors());
        }
        
        // Prepara i dati della nuova email
        $destinatari = $this->request->getPost('destinatario');
        log_message('debug', "RispondiEmail - Destinatari originali: " . json_encode($destinatari));
        
        if (!is_array($destinatari)) {
            $destinatari = explode(',', $destinatari);
            log_message('debug', "RispondiEmail - Destinatari convertiti in array: " . json_encode($destinatari));
        }
        
        $cc = $this->request->getPost('cc');
        if (!empty($cc) && !is_array($cc)) {
            $cc = explode(',', $cc);
            log_message('debug', "RispondiEmail - CC convertiti in array: " . json_encode($cc));
        }
        
        $ccn = $this->request->getPost('ccn');
        if (!empty($ccn) && !is_array($ccn)) {
            $ccn = explode(',', $ccn);
            log_message('debug', "RispondiEmail - CCN convertiti in array: " . json_encode($ccn));
        }
        
        $oggetto = $this->request->getPost('oggetto');
        $corpo = $this->request->getPost('corpo');
        
        // Debug: log dell'oggetto e del corpo
        log_message('debug', "RispondiEmail - Oggetto email: {$oggetto}");
        log_message('debug', "RispondiEmail - Corpo email (lunghezza): " . strlen($corpo));
        
        // Aggiungi citazione dell'email originale se richiesto
        $includeCitazione = $this->request->getPost('include_citazione') == '1';
        log_message('debug', "RispondiEmail - Includere citazione: " . ($includeCitazione ? 'Sì' : 'No'));
        
        if ($includeCitazione) {
            $citazione = '<br><br><hr><p><strong>Da:</strong> ' . session()->get('utente_nome') . ' ' . session()->get('utente_cognome') . '<br>';
            $citazione .= '<strong>Inviato:</strong> ' . date('d/m/Y H:i', strtotime($emailOriginale['data_invio'])) . '<br>';
            $citazione .= '<strong>A:</strong> ' . $emailOriginale['destinatario'] . '<br>';
            if (!empty($emailOriginale['cc'])) {
                $citazione .= '<strong>Cc:</strong> ' . $emailOriginale['cc'] . '<br>';
            }
            $citazione .= '<strong>Oggetto:</strong> ' . $emailOriginale['oggetto'] . '</p>';
            $citazione .= '<blockquote style="margin:0px 0px 0px 0.8ex;border-left:1px solid #ccc;padding-left:1ex">';
            $citazione .= $emailOriginale['corpo'];
            $citazione .= '</blockquote>';
            
            $corpo .= $citazione;
            log_message('debug', "RispondiEmail - Citazione aggiunta al corpo");
        }
        
        // Prepara allegati
        $allegati = [];
        
        // Gestione allegati caricati
        if ($files = $this->request->getFiles()) {
            log_message('debug', "RispondiEmail - Elaborazione allegati");
            foreach ($files['allegati'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    try {
                        $file->move(TEMP_UPLOAD_PATH, $newName);
                        $allegati[] = $newName;
                        log_message('debug', "RispondiEmail - Allegato caricato: {$newName} (Size: {$file->getSize()})");
                    } catch (\Exception $e) {
                        log_message('error', "RispondiEmail - Errore caricamento allegato: " . $e->getMessage());
                    }
                } else if ($file->getError() > 0) {
                    log_message('error', "RispondiEmail - Errore file: " . $file->getErrorString());
                }
            }
        }
        
        // Allegato PDF
        $attach_pdf = ['pdf' => 'false'];
        if ($this->request->getPost('allega_pdf')) {
            $attach_pdf = [
                'pdf' => 'true',
                'id_ordine' => $id_ordine,
                'numero' => $ordine['numero']
            ];
            log_message('debug', "RispondiEmail - Allegare PDF dell'ordine: " . json_encode($attach_pdf));
        }
        
        // Carica ImpostazioniModel per recuperare l'email di sistema
        $impostazioniModel = new \App\Models\ImpostazioniModel();
        $systemEmail = $impostazioniModel->getImpSistema('email_from_address', '');
        $systemName = $impostazioniModel->getImpSistema('email_from_name', '');
        
        // Ottieni i dati dell'utente corrente
        $userId = session()->get('utente_id');
        $userEmail = session()->get('utente_email');
        $userName = session()->get('utente_nome') . ' ' . session()->get('utente_cognome');
        
        // Se l'email dell'utente non è valida, verifica nel database e nelle impostazioni
        if (empty($userEmail) || !filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            log_message('warning', "RispondiEmail - Email utente in sessione non valida o mancante: " . ($userEmail ?? 'vuota'));
            
            // Cerca nelle impostazioni personalizzate dell'utente
            if (!empty($userId)) {
                $emailUtente = $impostazioniModel->getImpUtente('email_from_address', (int)$userId, '');
                if (!empty($emailUtente) && filter_var($emailUtente, FILTER_VALIDATE_EMAIL)) {
                    $userEmail = $emailUtente;
                    $nomeUtente = $impostazioniModel->getImpUtente('email_from_name', (int)$userId, $userName);
                    if (!empty($nomeUtente)) {
                        $userName = $nomeUtente;
                    }
                    // Aggiorna la sessione
                    session()->set('utente_email', $userEmail);
                    log_message('info', "RispondiEmail - Email utente recuperata dalle impostazioni: {$userEmail}");
                } else {
                    // Cerca l'email dell'utente nel database
                    $utentiModel = new \App\Models\UtentiModel();
                    $utente = $utentiModel->find($userId);
                    if ($utente && !empty($utente['email']) && filter_var($utente['email'], FILTER_VALIDATE_EMAIL)) {
                        $userEmail = $utente['email'];
                        // Aggiorna la sessione
                        session()->set('utente_email', $userEmail);
                        log_message('info', "RispondiEmail - Email utente recuperata dal DB: {$userEmail}");
                    }
                }
            }
        }
        
        // Se ancora non è valida, usa l'email di sistema
        if (empty($userEmail) || !filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            if (!empty($systemEmail) && filter_var($systemEmail, FILTER_VALIDATE_EMAIL)) {
                $userEmail = $systemEmail;
                log_message('notice', "RispondiEmail - Usando email di sistema come fallback: {$systemEmail}");
            } else {
                log_message('error', "RispondiEmail - Nessuna email valida disponibile per inviare l'email");
                return redirect()->to('/ordini-materiale/' . $id_ordine)->with('error', 'Impossibile inviare l\'email: nessun mittente valido disponibile. Contatta l\'amministratore.');
            }
        }
        
        // Configura il mittente
        $from = [
            'email' => $userEmail,
            'name' => $userName
        ];
        
        log_message('debug', "RispondiEmail - Dettagli mittente finali: " . json_encode($from));
        
        // Carica l'helper per l'invio email
        helper('CIMail');
        
        try {
            // Debug: verificare la presenza del destinatario
            if (empty($destinatari) || empty($destinatari[0])) {
                throw new \Exception("Destinatario non specificato");
            }
            
            log_message('debug', "RispondiEmail - Prima di invio email - Destinatario: {$destinatari[0]}, Oggetto: {$oggetto}");
            
            // Invia l'email
            $result = send_email($destinatari[0], $oggetto, $corpo, $from, $allegati, $attach_pdf, $cc, $ccn);
            
            // Debug: risultato dell'invio email
            log_message('debug', "RispondiEmail - Risultato invio email: " . json_encode($result));
            
            if ($result['status']) {
                // Registra l'email nel log
                log_email($destinatari, $cc, $ccn, $oggetto, $corpo, $id_ordine, 'ORDINE', 'inviato', null, $allegati);
                log_message('info', "RispondiEmail - Email inviata con successo e registrata nel log");
                
                return redirect()->to('/ordini-materiale/' . $id_ordine)->with('success', 'Risposta inviata con successo');
            } else {
                // Registra l'errore nel log
                log_email($destinatari, $cc, $ccn, $oggetto, $corpo, $id_ordine, 'ORDINE', 'errore', $result['msg'], $allegati);
                log_message('error', "RispondiEmail - Errore invio email: " . $result['msg']);
                
                return redirect()->to('/ordini-materiale/' . $id_ordine)->with('error', 'Errore nell\'invio della risposta: ' . $result['msg']);
            }
        } catch (\Exception $e) {
            log_message('error', "RispondiEmail - Eccezione durante l'invio: " . $e->getMessage());
            return redirect()->to('/ordini-materiale/' . $id_ordine)->with('error', 'Errore nell\'invio della risposta: ' . $e->getMessage());
        }
    }
    
    /**
     * Importa voci da un'offerta fornitore all'ordine
     */
    public function importaVociOfferta($id_ordine)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Richiesta non valida']);
        }

        $ordine = $this->ordineMaterialeModel->find($id_ordine);
        if (!$ordine) {
            return $this->response->setJSON(['success' => false, 'message' => 'Ordine non trovato']);
        }

        $voci = $this->request->getPost('voci');
        if (empty($voci) || !is_array($voci)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Nessuna voce selezionata']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            foreach ($voci as $voce) {
                $data = [
                    'id_ordine' => $id_ordine,
                    'codice' => $voce['codice'],
                    'descrizione' => $voce['descrizione'],
                    'quantita' => $voce['quantita'],
                    'unita_misura' => $voce['unita_misura'],
                    'prezzo_unitario' => $voce['prezzo_unitario'],
                    'sconto' => $voce['sconto'] ?? 0,
                    'importo' => ($voce['quantita'] * $voce['prezzo_unitario']) * (1 - ($voce['sconto'] ?? 0) / 100)
                ];

                $this->ordineMaterialeVoceModel->insert($data);
            }

            // Aggiorna l'importo totale dell'ordine
            $this->aggiornaImportoTotale($id_ordine);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON(['success' => false, 'message' => 'Errore durante l\'importazione delle voci']);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Voci importate con successo']);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', '[OrdiniMaterialeController::importaVociOfferta] ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Errore durante l\'importazione delle voci: ' . $e->getMessage()]);
        }
    }

    /**
     * Carica un allegato all'ordine
     */
    public function caricaAllegato($id_ordine)
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per caricare allegati.');
            return redirect()->to('/login');
        }
        
        // Verifica che l'ordine esista
        $ordine = $this->ordineMaterialeModel->find($id_ordine);
        if (!$ordine) {
            $session->setFlashdata('error', 'Ordine non trovato');
            return redirect()->to('/ordini-materiale');
        }
        
        // Gestisci il file caricato
        $file = $this->request->getFile('allegato');
        
        if (!$file->isValid() || $file->hasMoved()) {
            $session->setFlashdata('error', 'Errore durante il caricamento del file: ' . $file->getErrorString());
            return redirect()->to('/ordini-materiale/' . $id_ordine);
        }
        
        // Salva l'allegato usando il model dedicato
        $allegatoModel = new \App\Models\OrdiniAllegatiModel();
        $result = $allegatoModel->uploadFile(
            (int)$id_ordine,
            $file,
            (int)$session->get('utente_id'),
            $this->request->getPost('descrizione')
        );
        
        if ($result) {
            $session->setFlashdata('success', 'Allegato caricato con successo');
        } else {
            $session->setFlashdata('error', 'Errore durante il salvataggio dell\'allegato');
        }
        
        return redirect()->to('/ordini-materiale/' . $id_ordine);
    }
    
    /**
     * Scarica un allegato
     */
    public function downloadAllegato($id_allegato)
    {
        $allegatoModel = new \App\Models\OrdiniAllegatiModel();
        $allegato = $allegatoModel->find($id_allegato);
        
        if (!$allegato) {
            return redirect()->back()->with('error', 'Allegato non trovato');
        }
        
        $filePath = FCPATH . 'uploads/ordini_materiale/' . $allegato['id_ordine_materiale'] . '/' . $allegato['file_originale'];
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File non trovato');
        }
        
        return $this->response->download($filePath, null)->setFileName($allegato['nome_file']);
    }
    
    /**
     * Elimina un allegato
     */
    public function deleteAllegato($id_allegato)
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per eliminare allegati.');
            return redirect()->to('/login');
        }
        
        $allegatoModel = new \App\Models\OrdiniAllegatiModel();
        
        if ($allegatoModel->deleteAllegato((int)$id_allegato)) {
            $session->setFlashdata('success', 'Allegato eliminato con successo');
        } else {
            $session->setFlashdata('error', 'Errore durante l\'eliminazione dell\'allegato');
        }
        
        return redirect()->back();
    }

    /**
     * Aggiorna gli sconti e i costi di trasporto dell'ordine
     */
    public function aggiornaCosti($id_ordine)
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per modificare i costi dell\'ordine.');
            return redirect()->to('/login');
        }
        
        // Verifica che l'ordine esista
        $ordine = $this->ordineMaterialeModel->find($id_ordine);
        if (empty($ordine)) {
            $session->setFlashdata('error', 'Ordine non trovato');
            return redirect()->to('/ordini-materiale');
        }
        
        // Validazione del form
        $rules = [
            'sconto_totale' => 'permit_empty|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'sconto_fisso' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'costo_trasporto' => 'permit_empty|numeric|greater_than_equal_to[0]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Dati non validi. Verifica i campi e riprova.');
        }
        
        // Ottieni i dati dal form
        $scontoTotale = (float)($this->request->getPost('sconto_totale') ?: 0);
        $scontoFisso = (float)($this->request->getPost('sconto_fisso') ?: 0);
        $costoTrasporto = (float)($this->request->getPost('costo_trasporto') ?: 0);
        
        // Aggiorna i costi
        $data = [
            'sconto_totale' => $scontoTotale,
            'sconto_fisso' => $scontoFisso,
            'costo_trasporto' => $costoTrasporto
        ];
        
        if ($this->ordineMaterialeModel->update($id_ordine, $data)) {
            // Aggiorna l'importo totale dell'ordine
            $this->aggiornaImportoTotale($id_ordine);
            
            $session->setFlashdata('success', 'Costi aggiornati con successo');
        } else {
            $session->setFlashdata('error', 'Si è verificato un errore durante l\'aggiornamento dei costi');
        }
        
        return redirect()->to('/ordini-materiale/' . $id_ordine);
    }
    
    /**
     * Aggiorna l'importo totale dell'ordine considerando sconti e trasporti
     */
    private function aggiornaImportoTotale($id_ordine)
    {
        // Log di debug
        log_message('debug', "Aggiornamento importo totale per ordine ID: {$id_ordine}");
        
        // Ottieni l'ordine
        $ordine = $this->ordineMaterialeModel->find($id_ordine);
        if (!$ordine) {
            log_message('error', "Ordine non trovato con ID: {$id_ordine}");
            return false;
        }
        
        // Ottieni tutte le voci dell'ordine attive (non eliminate)
        $voci = $this->ordineMaterialeVoceModel->where('id_ordine', $id_ordine)
                                             ->where('deleted_at IS NULL')
                                             ->findAll();
        
        log_message('debug', "Trovate " . count($voci) . " voci attive per l'ordine");
        
        // Come alternativa, possiamo usare la funzione del modello che garantisce il rispetto del soft delete
        $importoTotaleCalcolato = $this->ordineMaterialeVoceModel->calcolaTotaleOrdine((int)$id_ordine);
        log_message('debug', "Importo totale calcolato dal modello: {$importoTotaleCalcolato}");
        
        // Calcola il totale delle voci
        $importoVoci = 0;
        foreach ($voci as $voce) {
            $importoVoci += (float)($voce['importo'] ?? 0);
            log_message('debug', "Voce ID: " . ($voce['id'] ?? 'N/A') . ", Importo: " . ($voce['importo'] ?? 0));
        }
        log_message('debug', "Importo totale voci calcolato: {$importoVoci}");
        
        // Verifica la coerenza tra i due metodi di calcolo
        if (abs($importoVoci - $importoTotaleCalcolato) > 0.01) {
            log_message('warning', "Discrepanza tra i metodi di calcolo dell'importo: {$importoVoci} vs {$importoTotaleCalcolato}");
        }
        
        // Usa l'importo calcolato dal modello per maggiore sicurezza
        $importoVoci = $importoTotaleCalcolato;
        
        // Calcola l'importo totale considerando sconti e trasporto
        $scontoTotale = (float)($ordine['sconto_totale'] ?? 0);
        $scontoFisso = (float)($ordine['sconto_fisso'] ?? 0);
        $costoTrasporto = (float)($ordine['costo_trasporto'] ?? 0);
        
        log_message('debug', "Parametri calcolo: sconto_totale={$scontoTotale}%, sconto_fisso={$scontoFisso}, costo_trasporto={$costoTrasporto}");
        
        // Applica lo sconto percentuale
        $importoScontatoPerc = round($importoVoci * (1 - ($scontoTotale / 100)), 2);
        log_message('debug', "Dopo sconto percentuale: {$importoScontatoPerc}");
        
        // Applica lo sconto fisso
        $importoScontato = round($importoScontatoPerc - $scontoFisso, 2);
        log_message('debug', "Dopo sconto fisso: {$importoScontato}");
        
        // Se l'importo risulta negativo dopo gli sconti, imposta a zero
        if ($importoScontato < 0) {
            $importoScontato = 0;
            log_message('debug', "Importo negativo dopo sconti, impostato a zero");
        }
        
        // Aggiungi il costo di trasporto
        $importoFinale = round($importoScontato + $costoTrasporto, 2);
        log_message('debug', "Importo finale dopo costo trasporto: {$importoFinale}");
        
        // Verifica se l'importo è cambiato
        if (abs(($ordine['importo_totale'] ?? 0) - $importoFinale) < 0.01) {
            log_message('debug', "Importo non cambiato, nessun aggiornamento necessario");
            return true; // L'importo non è cambiato, consideriamo l'operazione come completata
        }
        
        // Aggiorna l'importo totale dell'ordine
        $risultato = $this->ordineMaterialeModel->update($id_ordine, ['importo_totale' => $importoFinale]);
        
        if ($risultato) {
            log_message('debug', "Aggiornamento importo totale riuscito: {$importoFinale}");
        } else {
            log_message('error', "Errore nell'aggiornamento dell'importo totale. Errori: " . json_encode($this->ordineMaterialeModel->errors()));
        }
        
        return $risultato;
    }

    /**
     * Aggiorna manualmente l'importo totale di un ordine (per debug)
     */
    public function forzaAggiornaImporto($id_ordine)
    {
        $session = session();
        
        // Verifica che l'ordine esista
        $ordine = $this->ordineMaterialeModel->find($id_ordine);
        if (!$ordine) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Ordine non trovato'
            ]);
        }
        
        // Esegui l'aggiornamento dell'importo
        if ($this->aggiornaImportoTotale($id_ordine)) {
            // Ricarica l'ordine per ottenere l'importo aggiornato
            $ordineAggiornato = $this->ordineMaterialeModel->find($id_ordine);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Importo aggiornato con successo',
                'ordine' => [
                    'id' => $ordineAggiornato['id'],
                    'importo_totale' => $ordineAggiornato['importo_totale'],
                    'sconto_totale' => $ordineAggiornato['sconto_totale'] ?? 0,
                    'sconto_fisso' => $ordineAggiornato['sconto_fisso'] ?? 0,
                    'costo_trasporto' => $ordineAggiornato['costo_trasporto'] ?? 0
                ]
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Errore nell\'aggiornamento dell\'importo totale',
                'errors' => $this->ordineMaterialeModel->errors()
            ]);
        }
    }

    /**
     * Mostra il form per creare un nuovo ordine
     */
    public function showCreateForm()
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per creare un ordine.');
            return redirect()->to('/login');
        }
        
        // Ottieni tutte le offerte fornitore per il dropdown (rimuovo filtro per stato)
        $offerteFornitore = $this->offertaFornitoreModel->select('offerte_fornitore.*, anagrafiche.ragione_sociale as nome_fornitore')
            ->join('anagrafiche', 'anagrafiche.id = offerte_fornitore.id_anagrafica', 'left')
            // Filtro per stato rimosso per debugging
            ->orderBy('offerte_fornitore.data', 'DESC')
            ->findAll();
            
        // Debug: conta quante offerte sono state trovate
        $session->setFlashdata('info', 'Trovate ' . count($offerteFornitore) . ' offerte totali');
        
        // Ottieni le condizioni di pagamento
        $condizioniPagamento = $this->condizioniPagamentoModel->getCondizioni();
        
        $data = [
            'title' => 'Nuovo Ordine di Acquisto',
            'fornitori' => $this->anagraficaModel->where('fornitore', 1)->where('attivo', 1)->findAll(),
            'progetti' => $this->progettoModel->where('attivo', 1)->findAll(),
            'offerteFornitore' => $offerteFornitore,
            'condizioniPagamento' => $condizioniPagamento
        ];
        
        return view('ordini_materiale/create', $data);
    }
}
