<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\OffertaFornitoreModel;
use App\Models\OffertaFornitoreAllegatoModel;
use App\Models\OffertaFornitoreVoceModel;
use App\Models\AnagraficaModel;
use App\Models\ContattoModel;
use App\Models\AnagraficaContattoModel;
use App\Models\ProgettoModel;
use App\Models\UtentiModel;
use App\Models\RichiestaOffertaModel;
use App\Models\RichiestaMaterialeModel;
use App\Models\Materiale;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

final class OfferteFornitoreController extends BaseController
{
    protected $offertaFornitoreModel;
    protected $offertaFornitoreAllegatoModel;
    protected $offertaFornitoreVoceModel;
    protected $anagraficaModel;
    protected $contattoModel;
    protected $anagraficaContattoModel;
    protected $progettoModel;
    protected $utentiModel;
    protected $richiestaOffertaModel;
    protected $richiestaMaterialeModel;
    protected $materialeModel;
    
    public function __construct()
    {
        helper(['form', 'date']);
        $this->offertaFornitoreModel = new OffertaFornitoreModel();
        $this->offertaFornitoreAllegatoModel = new OffertaFornitoreAllegatoModel();
        $this->offertaFornitoreVoceModel = new OffertaFornitoreVoceModel();
        $this->anagraficaModel = new AnagraficaModel();
        $this->contattoModel = new ContattoModel();
        $this->anagraficaContattoModel = new AnagraficaContattoModel();
        $this->progettoModel = new ProgettoModel();
        $this->utentiModel = new UtentiModel();
        $this->richiestaOffertaModel = new RichiestaOffertaModel();
        $this->richiestaMaterialeModel = new RichiestaMaterialeModel();
        $this->materialeModel = new Materiale();
    }
    
    /**
     * Mostra la lista delle offerte fornitore
     */
    public function index()
    {
        $data = [
            'title' => 'Offerte Fornitore',
            'offerte' => $this->offertaFornitoreModel->getOfferteWithRelations()
        ];
        
        return view('offerte_fornitore/index', $data);
    }
    
    /**
     * Mostra i dettagli di un'offerta fornitore
     */
    public function show($id = null)
    {
        $id = ($id) ? $id : $this->request->getPost('id');
        
        if (!$id) {
            return redirect()->to('/offerte-fornitore')->with('error', 'ID offerta non specificato');
        }
        
        $offertaFornitore = $this->offertaFornitoreModel->getOffertaWithRelations((int)$id);
        
        if (!$offertaFornitore) {
            return redirect()->to('/offerte-fornitore')->with('error', 'Offerta fornitore non trovata');
        }
        
        // Ottieni i materiali/voci dell'offerta
        $voci = $this->offertaFornitoreVoceModel->getVociByOfferta((int)$id);
        
        // Ottieni gli allegati
        $allegati = $this->offertaFornitoreAllegatoModel->getAllegatiByOfferta((int)$id);
        
        // Ottieni i progetti per i dropdown
        $progetti = $this->progettoModel->findAll();
        
        // Ottieni tutti i contatti dell'anagrafica
        $contatti = [];
        if (!empty($offertaFornitore['id_anagrafica'])) {
            $contatti = $this->anagraficaContattoModel->getContattiByAnagrafica((int)$offertaFornitore['id_anagrafica']);
        }
        
        // Se c'è una richiesta d'offerta collegata, ottieni le voci
        $vociRichiesta = [];
        if (!empty($offertaFornitore['id_richiesta_offerta'])) {
            $vociRichiesta = $this->richiestaMaterialeModel->getMaterialiByRichiesta((int)$offertaFornitore['id_richiesta_offerta']);
        }
        
        $data = [
            'title' => 'Dettaglio Offerta Fornitore',
            'offerta' => $offertaFornitore,
            'voci' => $voci,
            'allegati' => $allegati,
            'progetti' => $progetti,
            'contatti' => $contatti,
            'vociRichiesta' => $vociRichiesta
        ];
        
        return view('offerte_fornitore/show', $data);
    }
    
    /**
     * Mostra il form per creare una nuova offerta fornitore
     */
    public function new()
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per creare un\'offerta fornitore.');
            return redirect()->to('/login');
        }
        
        // Recupera eventuali id di richiesta d'offerta dalla query string
        $idRichiesta = $this->request->getGet('id_richiesta') ? (int)$this->request->getGet('id_richiesta') : null;
        $richiestaData = null;
        
        if ($idRichiesta) {
            $richiestaData = $this->richiestaOffertaModel->find($idRichiesta);
        }
        
        $data = [
            'title' => 'Nuova Offerta Fornitore',
            'fornitori' => $this->anagraficaModel->where('fornitore', 1)->where('attivo', 1)->findAll(),
            'progetti' => $this->progettoModel->where('attivo', 1)->findAll(),
            'richieste' => $this->richiestaOffertaModel->findAll(),
            'richiestaData' => $richiestaData
        ];
        
        return view('offerte_fornitore/create', $data);
    }
    
    /**
     * Crea una nuova offerta fornitore
     */
    public function create()
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per creare un\'offerta fornitore.');
            return redirect()->to('/login');
        }
        
        // Imposta l'utente corrente come creatore dell'offerta
        $idUtente = $session->get('utente_id');
        
        if (empty($idUtente)) {
            $idUtente = 1; // Valore predefinito se l'utente non è in sessione
        }
        
        // Ottieni la data dal form e convertila in formato ISO
        $dataItaliana = $this->request->getPost('data') ?: date('d/m/Y');
        $dataISO = formatDateToISO($dataItaliana) ?: date('Y-m-d');
        
        // Gestisci il referente
        $idReferente = $this->request->getPost('id_referente');
        if (empty($idReferente)) {
            $idReferente = null; // Assicurati che sia null se non specificato
        } else {
            $idReferente = (int)$idReferente;
        }
        
        // Dati del form
        $data = [
            'numero' => $this->request->getPost('numero') ?: $this->offertaFornitoreModel->generateNumeroOfferta(),
            'data' => $dataISO,
            'oggetto' => $this->request->getPost('oggetto'),
            'descrizione' => $this->request->getPost('descrizione'),
            'id_anagrafica' => $this->request->getPost('id_anagrafica') ? (int)$this->request->getPost('id_anagrafica') : null,
            'id_referente' => $idReferente,
            'id_richiesta_offerta' => $this->request->getPost('id_richiesta_offerta') ? (int)$this->request->getPost('id_richiesta_offerta') : null,
            'id_progetto' => $this->request->getPost('id_progetto') ? (int)$this->request->getPost('id_progetto') : null,
            'stato' => 'ricevuta',
            'id_utente_creatore' => $idUtente,
            'data_ricezione' => date('Y-m-d H:i:s'),
            'importo_totale' => 0,
            'valuta' => $this->request->getPost('valuta') ?: 'EUR',
            'note' => $this->request->getPost('note')
        ];
        
        // Validazione
        if (!$this->offertaFornitoreModel->validate($data)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->offertaFornitoreModel->errors());
        }
        
        // Salva l'offerta fornitore
        $this->offertaFornitoreModel->insert($data);
        $idOfferta = $this->offertaFornitoreModel->getInsertID();
        
        // Se l'offerta è collegata a una richiesta, importa le voci
        if (!empty($data['id_richiesta_offerta'])) {
            $this->offertaFornitoreVoceModel->importaVociDaRichiesta($idOfferta, $data['id_richiesta_offerta']);
        }
        
        // Carica eventuali allegati
        $this->caricaAllegati($idOfferta);
        
        $session->setFlashdata('success', 'Offerta fornitore registrata con successo.');
        return redirect()->to('/offerte-fornitore');
    }
    
    /**
     * Carica gli allegati per un'offerta
     */
    private function caricaAllegati($idOfferta)
    {
        $files = $this->request->getFiles();
        
        if (empty($files) || empty($files['allegati'])) {
            return;
        }
        
        $idUtente = session()->get('utente_id') ?: 1;
        
        foreach ($files['allegati'] as $file) {
            if ($file->isValid() && !$file->hasMoved()) {
                $descrizione = $this->request->getPost('descrizione_allegato') ?: '';
                $this->offertaFornitoreAllegatoModel->uploadFile($idOfferta, $file, $idUtente, $descrizione);
            }
        }
    }
    
    /**
     * Mostra il form per modificare un'offerta fornitore
     */
    public function edit($id = null)
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per modificare un\'offerta fornitore.');
            return redirect()->to('/login');
        }
        
        $offerta = $this->offertaFornitoreModel->find($id);
        
        if (empty($offerta)) {
            throw new PageNotFoundException('Offerta fornitore non trovata');
        }
        
        // Ottieni i contatti dell'anagrafica selezionata
        $contatti = $this->anagraficaContattoModel->getContattiByAnagrafica((int)$offerta['id_anagrafica']);
        
        $data = [
            'title' => 'Modifica Offerta Fornitore',
            'offerta' => $offerta,
            'fornitori' => $this->anagraficaModel->where('fornitore', 1)->where('attivo', 1)->findAll(),
            'progetti' => $this->progettoModel->where('attivo', 1)->findAll(),
            'richieste' => $this->richiestaOffertaModel->findAll(),
            'contatti' => $contatti
        ];
        
        return view('offerte_fornitore/edit', $data);
    }
    
    /**
     * Aggiorna un'offerta fornitore esistente
     */
    public function update($id = null)
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per modificare un\'offerta fornitore.');
            return redirect()->to('/login');
        }
        
        $offerta = $this->offertaFornitoreModel->find($id);
        
        if (empty($offerta)) {
            throw new PageNotFoundException('Offerta fornitore non trovata');
        }
        
        // Dati del form
        $dataItaliana = $this->request->getPost('data');
        $dataISO = formatDateToISO($dataItaliana) ?: $offerta['data'];
        
        $data = [
            'numero' => $this->request->getPost('numero'),
            'data' => $dataISO,
            'oggetto' => $this->request->getPost('oggetto'),
            'descrizione' => $this->request->getPost('descrizione'),
            'id_anagrafica' => $this->request->getPost('id_anagrafica') ? (int)$this->request->getPost('id_anagrafica') : null,
            'id_referente' => $this->request->getPost('id_referente') ? (int)$this->request->getPost('id_referente') : null,
            'id_richiesta_offerta' => $this->request->getPost('id_richiesta_offerta') ? (int)$this->request->getPost('id_richiesta_offerta') : null,
            'id_progetto' => $this->request->getPost('id_progetto') ? (int)$this->request->getPost('id_progetto') : null,
            'valuta' => $this->request->getPost('valuta') ?: 'EUR',
            'note' => $this->request->getPost('note')
        ];
        
        // Validazione
        if (!$this->offertaFornitoreModel->validate(array_merge($offerta, $data))) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->offertaFornitoreModel->errors());
        }
        
        // Aggiorna l'offerta fornitore
        $this->offertaFornitoreModel->update($id, $data);
        
        // Carica eventuali nuovi allegati
        $this->caricaAllegati($id);
        
        $session->setFlashdata('success', 'Offerta fornitore aggiornata con successo.');
        return redirect()->to('/offerte-fornitore/' . $id);
    }
    
    /**
     * Cambia lo stato di un'offerta fornitore
     */
    public function cambiaStato($id = null)
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per cambiare lo stato di un\'offerta fornitore.');
            return redirect()->to('/login');
        }
        
        $offerta = $this->offertaFornitoreModel->find($id);
        
        if (empty($offerta)) {
            throw new PageNotFoundException('Offerta fornitore non trovata');
        }
        
        // Accetta lo stato sia da POST che da GET
        $nuovoStato = $this->request->getPost('stato') ?? $this->request->getGet('stato');
        
        // Verifica che lo stato sia valido
        if (!in_array($nuovoStato, ['ricevuta', 'in_valutazione', 'approvata', 'rifiutata', 'scaduta'])) {
            $session->setFlashdata('error', 'Stato non valido.');
            return redirect()->to('/offerte-fornitore/' . $id);
        }
        
        $data = ['stato' => $nuovoStato];
        
        // Se lo stato è "approvata", imposta la data di approvazione
        if ($nuovoStato === 'approvata' && empty($offerta['data_approvazione'])) {
            $data['data_approvazione'] = date('Y-m-d H:i:s');
        }
        
        // Aggiorna lo stato
        $this->offertaFornitoreModel->update($id, $data);
        
        $session->setFlashdata('success', 'Stato dell\'offerta fornitore aggiornato con successo.');
        return redirect()->to('/offerte-fornitore/' . $id);
    }
    
    /**
     * Elimina un'offerta fornitore (soft delete)
     */
    public function delete($id = null)
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per eliminare un\'offerta fornitore.');
            return redirect()->to('/login');
        }
        
        $offerta = $this->offertaFornitoreModel->find($id);
        
        if (empty($offerta)) {
            throw new PageNotFoundException('Offerta fornitore non trovata');
        }
        
        // Non permettere l'eliminazione di offerte approvate
        if ($offerta['stato'] === 'approvata') {
            $session->setFlashdata('error', 'Non è possibile eliminare un\'offerta che è stata approvata.');
            return redirect()->to('/offerte-fornitore/' . $id);
        }
        
        $this->offertaFornitoreModel->delete($id);
        
        $session->setFlashdata('success', 'Offerta fornitore eliminata con successo.');
        return redirect()->to('/offerte-fornitore');
    }
    
    /**
     * Scarica un allegato
     */
    public function downloadAllegato($id = null)
    {
        if (!$id) {
            return redirect()->back()->with('error', 'ID allegato non specificato');
        }
        
        $allegato = $this->offertaFornitoreAllegatoModel->find($id);
        
        if (!$allegato) {
            return redirect()->back()->with('error', 'Allegato non trovato');
        }
        
        $filePath = FCPATH . 'uploads/offerte_fornitore/' . $allegato['id_offerta_fornitore'] . '/' . $allegato['file_originale'];
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File non trovato sul server');
        }
        
        return $this->response->download($filePath, null)->setFileName($allegato['nome_file']);
    }
    
    /**
     * Elimina un allegato
     */
    public function deleteAllegato($id = null)
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per eliminare un allegato.');
            return redirect()->to('/login');
        }
        
        if (!$id) {
            return redirect()->back()->with('error', 'ID allegato non specificato');
        }
        
        $allegato = $this->offertaFornitoreAllegatoModel->find($id);
        
        if (!$allegato) {
            return redirect()->back()->with('error', 'Allegato non trovato');
        }
        
        $idOfferta = $allegato['id_offerta_fornitore'];
        $nomeFile = $allegato['nome_file'];
        
        // Elimina l'allegato
        $result = $this->offertaFornitoreAllegatoModel->deleteAllegato((int)$id);
        
        if ($result) {
            $session->setFlashdata('success', 'Allegato "' . $nomeFile . '" eliminato con successo.');
        } else {
            $session->setFlashdata('error', 'Errore durante l\'eliminazione dell\'allegato "' . $nomeFile . '".');
        }
        
        return redirect()->to('/offerte-fornitore/' . $idOfferta);
    }
    
    /**
     * Aggiunge una voce all'offerta fornitore
     */
    public function aggiungiVoce($id_offerta)
    {
        // Verifica che l'offerta esista
        $offerta = $this->offertaFornitoreModel->find($id_offerta);
        if (empty($offerta)) {
            return redirect()->to('offerte-fornitore')->with('error', 'Offerta non trovata');
        }
        
        // Validazione del form
        $rules = [
            'descrizione' => 'required|max_length[512]',
            'quantita' => 'required|numeric|greater_than[0]',
            'prezzo_unitario' => 'required|numeric|greater_than_equal_to[0]',
            'unita_misura' => 'permit_empty|max_length[20]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Dati non validi. Verifica i campi e riprova.');
        }
        
        $id_materiale = $this->request->getPost('id_materiale') ?: null;
        $codice = $this->request->getPost('codice') ?: null;
        $descrizione = $this->request->getPost('descrizione');
        $quantita = (float)$this->request->getPost('quantita');
        $prezzo_unitario = (float)$this->request->getPost('prezzo_unitario');
        $unita_misura = $this->request->getPost('unita_misura') ?: 'pz';
        $id_progetto = $this->request->getPost('id_progetto') ?: null;
        $sconto = (float)($this->request->getPost('sconto') ?: 0);
        $note = $this->request->getPost('note') ?: null;
        
        // Se è specificato un ID materiale, verifica che esista
        if ($id_materiale) {
            $materiale = $this->materialeModel->find($id_materiale);
            if (empty($materiale)) {
                return redirect()->back()->with('error', 'Materiale non trovato');
            }
            
            // Se è specificato un materiale, usa il suo codice se non è specificato
            if (!$codice) {
                $codice = $materiale['codice'];
            }
        }
        
        // Calcola l'importo
        $importo = $this->offertaFornitoreVoceModel->calcolaImporto($prezzo_unitario, $quantita, $sconto);
        
        // Inserimento nella tabella offerte_fornitore_voci
        $data = [
            'id_offerta_fornitore' => $id_offerta,
            'id_materiale' => $id_materiale,
            'codice' => $codice,
            'descrizione' => $descrizione,
            'quantita' => $quantita,
            'prezzo_unitario' => $prezzo_unitario,
            'importo' => $importo,
            'unita_misura' => $unita_misura,
            'sconto' => $sconto,
            'note' => $note,
            'id_progetto' => $id_progetto
        ];
        
        if ($this->offertaFornitoreVoceModel->insert($data)) {
            // Aggiorna l'importo totale dell'offerta
            $this->offertaFornitoreModel->aggiornaImportoTotale((int)$id_offerta);
            
            return redirect()->to("offerte-fornitore/{$id_offerta}")->with('success', 'Voce aggiunta con successo');
        } else {
            return redirect()->back()->with('error', 'Si è verificato un errore durante l\'aggiunta della voce');
        }
    }
    
    /**
     * Aggiorna una voce dell'offerta fornitore
     */
    public function aggiornaVoce($id_offerta)
    {
        // Verifica che l'offerta esista
        $offerta = $this->offertaFornitoreModel->find($id_offerta);
        if (empty($offerta)) {
            return redirect()->to('offerte-fornitore')->with('error', 'Offerta non trovata');
        }
        
        // Validazione del form
        $rules = [
            'id' => 'required|numeric',
            'descrizione' => 'required|max_length[512]',
            'quantita' => 'required|numeric|greater_than[0]',
            'prezzo_unitario' => 'required|numeric|greater_than_equal_to[0]',
            'unita_misura' => 'permit_empty|max_length[20]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Dati non validi. Verifica i campi e riprova.');
        }
        
        $id = (int)$this->request->getPost('id');
        $id_materiale = $this->request->getPost('id_materiale') ? (int)$this->request->getPost('id_materiale') : null;
        $codice = $this->request->getPost('codice') ?: null;
        $descrizione = $this->request->getPost('descrizione');
        $quantita = (float)$this->request->getPost('quantita');
        $prezzo_unitario = (float)$this->request->getPost('prezzo_unitario');
        $unita_misura = $this->request->getPost('unita_misura') ?: 'pz';
        $id_progetto = $this->request->getPost('id_progetto') ? (int)$this->request->getPost('id_progetto') : null;
        $sconto = (float)($this->request->getPost('sconto') ?: 0);
        $note = $this->request->getPost('note') ?: null;
        
        // Verifica che il record esista
        $voce = $this->offertaFornitoreVoceModel->find($id);
        if (empty($voce) || $voce['id_offerta_fornitore'] != $id_offerta) {
            return redirect()->back()->with('error', 'Voce non trovata');
        }
        
        // Calcola l'importo
        $importo = $this->offertaFornitoreVoceModel->calcolaImporto($prezzo_unitario, $quantita, $sconto);
        
        // Aggiorna il record
        $data = [
            'id_materiale' => $id_materiale,
            'codice' => $codice,
            'descrizione' => $descrizione,
            'quantita' => $quantita,
            'prezzo_unitario' => $prezzo_unitario,
            'importo' => $importo,
            'unita_misura' => $unita_misura,
            'sconto' => $sconto,
            'note' => $note,
            'id_progetto' => $id_progetto
        ];
        
        if ($this->offertaFornitoreVoceModel->update($id, $data)) {
            // Aggiorna l'importo totale dell'offerta
            $this->offertaFornitoreModel->aggiornaImportoTotale((int)$id_offerta);
            
            return redirect()->to("offerte-fornitore/{$id_offerta}")->with('success', 'Voce aggiornata con successo');
        } else {
            return redirect()->back()->with('error', 'Si è verificato un errore durante l\'aggiornamento della voce');
        }
    }
    
    /**
     * Rimuove una voce dall'offerta fornitore
     */
    public function rimuoviVoce($id_offerta, $id_voce)
    {
        $session = session();
        
        // Controlla che l'offerta esista
        $offerta = $this->offertaFornitoreModel->find($id_offerta);
        if (!$offerta) {
            $session->setFlashdata('error', 'Offerta fornitore non trovata');
            return redirect()->to('/offerte-fornitore');
        }
        
        // Controlla che la voce esista e appartenga a questa offerta
        $voce = $this->offertaFornitoreVoceModel->find($id_voce);
        if (!$voce || $voce['id_offerta_fornitore'] != $id_offerta) {
            $session->setFlashdata('error', 'Voce non trovata o non appartiene a questa offerta');
            return redirect()->to('/offerte-fornitore/' . $id_offerta);
        }
        
        // Rimuovi la voce
        if ($this->offertaFornitoreVoceModel->delete($id_voce)) {
            // Aggiorna l'importo totale dell'offerta
            $this->offertaFornitoreModel->aggiornaImportoTotale((int)$id_offerta);
            
            $session->setFlashdata('success', 'Voce rimossa dall\'offerta fornitore');
        } else {
            $session->setFlashdata('error', 'Errore durante la rimozione della voce');
        }
        
        return redirect()->to('/offerte-fornitore/' . $id_offerta);
    }
    
    /**
     * Importa voci da una richiesta d'offerta
     */
    public function importaVociRichiesta($id_offerta)
    {
        $session = session();
        
        // Verifica che l'offerta esista
        $offerta = $this->offertaFornitoreModel->find($id_offerta);
        if (!$offerta) {
            return redirect()->to('/offerte-fornitore')->with('error', 'Offerta fornitore non trovata');
        }
        
        // Verifica che sia specificato un ID richiesta (dal POST o dall'offerta)
        $id_richiesta = $this->request->getPost('id_richiesta_offerta') ?: $offerta['id_richiesta_offerta'];
        if (empty($id_richiesta)) {
            return redirect()->to('/offerte-fornitore/' . $id_offerta)->with('error', 'Richiesta d\'offerta non specificata');
        }
        
        // Verifica che la richiesta esista
        $richiesta = $this->richiestaOffertaModel->find($id_richiesta);
        if (!$richiesta) {
            return redirect()->to('/offerte-fornitore/' . $id_offerta)->with('error', 'Richiesta d\'offerta non trovata');
        }
        
        // Verifica che ci siano voci selezionate da importare
        $voci_ids = $this->request->getPost('voci');
        if (empty($voci_ids) || !is_array($voci_ids)) {
            return redirect()->to('/offerte-fornitore/' . $id_offerta)->with('error', 'Nessuna voce selezionata per l\'importazione');
        }
        
        // Importa le voci
        $risultato = $this->offertaFornitoreVoceModel->importaVociDaRichiesta((int)$id_offerta, (int)$id_richiesta, $voci_ids);
        
        // Se ci sono stati errori
        if (!empty($risultato['errori'])) {
            $messaggioErrori = implode('. ', $risultato['errori']);
            $session->setFlashdata('error', 'Errori durante l\'importazione: ' . $messaggioErrori);
            
            // Ma se sono stati importati dei materiali, mostra anche un messaggio di successo
            if (!empty($risultato['importate'])) {
                $session->setFlashdata('success', 'Importati ' . count($risultato['importate']) . ' materiali dalla richiesta d\'offerta');
            }
        } elseif (!empty($risultato['importate'])) {
            // Se sono stati importati materiali senza errori
            $session->setFlashdata('success', 'Importati con successo ' . count($risultato['importate']) . ' materiali dalla richiesta d\'offerta');
            
            // Aggiorna l'importo totale dell'offerta
            $this->offertaFornitoreModel->aggiornaImportoTotale((int)$id_offerta);
        } else {
            $session->setFlashdata('info', 'Nessun materiale da importare dalla richiesta d\'offerta');
        }
        
        return redirect()->to('/offerte-fornitore/' . $id_offerta);
    }
    
    /**
     * Visualizza le offerte fornitore per un fornitore specifico
     */
    public function perFornitore($idAnagrafica = null)
    {
        $fornitore = $this->anagraficaModel->find($idAnagrafica);
        
        if (empty($fornitore)) {
            throw new PageNotFoundException('Fornitore non trovato');
        }
        
        $data = [
            'title' => 'Offerte da ' . $fornitore['ragione_sociale'],
            'fornitore' => $fornitore,
            'offerte' => $this->offertaFornitoreModel->getOfferteByFornitore((int)$idAnagrafica)
        ];
        
        return view('offerte_fornitore/per_fornitore', $data);
    }
    
    /**
     * Visualizza le offerte fornitore per un progetto specifico
     */
    public function perProgetto($idProgetto = null)
    {
        $progetto = $this->progettoModel->find($idProgetto);
        
        if (empty($progetto)) {
            throw new PageNotFoundException('Progetto non trovato');
        }
        
        $data = [
            'title' => 'Offerte per il progetto ' . $progetto['nome'],
            'progetto' => $progetto,
            'offerte' => $this->offertaFornitoreModel->getOfferteByProgetto((int)$idProgetto)
        ];
        
        return view('offerte_fornitore/per_progetto', $data);
    }
    
    /**
     * Visualizza le offerte fornitore per una richiesta d'offerta specifica
     */
    public function perRichiesta($idRichiesta = null)
    {
        $richiesta = $this->richiestaOffertaModel->find($idRichiesta);
        
        if (empty($richiesta)) {
            throw new PageNotFoundException('Richiesta d\'offerta non trovata');
        }
        
        $richiestaCompleta = $this->richiestaOffertaModel->getRichiestaWithRelations((int)$idRichiesta);
        
        $data = [
            'title' => 'Offerte per la richiesta ' . $richiesta['numero'],
            'richiesta' => $richiestaCompleta,
            'offerte' => $this->offertaFornitoreModel->getOfferteByRichiestaOfferta((int)$idRichiesta)
        ];
        
        return view('offerte_fornitore/per_richiesta', $data);
    }
    
    /**
     * Carica un allegato tramite il form standard
     */
    public function caricaAllegato($id_offerta)
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per caricare allegati.');
            return redirect()->to('/login');
        }
        
        // Verifica che l'offerta esista
        $offerta = $this->offertaFornitoreModel->find($id_offerta);
        if (empty($offerta)) {
            return redirect()->back()->with('error', 'Offerta non trovata');
        }
        
        $idUtente = $session->get('utente_id') ?: 1;
        
        // Ottieni il file caricato
        $file = $this->request->getFile('allegato');
        
        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return redirect()->back()->with('error', 'File non valido o non specificato');
        }
        
        // Descrizione dell'allegato
        $descrizione = $this->request->getPost('descrizione') ?: '';
        
        // Carica il file
        $result = $this->offertaFornitoreAllegatoModel->uploadFile((int)$id_offerta, $file,(int) $idUtente, $descrizione);
        
        if ($result) {
            $session->setFlashdata('success', 'Allegato caricato con successo');
        } else {
            $session->setFlashdata('error', 'Errore durante il caricamento dell\'allegato');
        }
        
        return redirect()->to('/offerte-fornitore/' . $id_offerta);
    }
    
    /**
     * Carica un singolo allegato tramite Dropzone
     */
    public function caricaAllegatoDropzone($id_offerta)
    {
        // Verifica che l'offerta esista
        $offerta = $this->offertaFornitoreModel->find($id_offerta);
        if (empty($offerta)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Offerta non trovata'
            ])->setStatusCode(404);
        }
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Devi effettuare il login per caricare allegati'
            ])->setStatusCode(401);
        }
        
        $idUtente = session()->get('utente_id') ?: 1;
        
        // Ottieni il file caricato
        $file = $this->request->getFile('allegato');
        
        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File non valido o non specificato'
            ])->setStatusCode(400);
        }
        
        // Carica il file
        $descrizione = $this->request->getPost('descrizione') ?: '';
        $result = $this->offertaFornitoreAllegatoModel->uploadFile((int)$id_offerta, $file, $idUtente, $descrizione);
        
        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Allegato caricato con successo',
                'file' => [
                    'id' => $result['id'],
                    'name' => $result['nome_file'],
                    'size' => $result['dimensione'],
                    'type' => $result['tipo_mime']
                ]
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Errore durante il caricamento dell\'allegato'
            ])->setStatusCode(500);
        }
    }
    
    /**
     * Aggiorna i costi aggiuntivi (sconto totale e costo di trasporto)
     */
    public function aggiornaCosti($id_offerta)
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per modificare i costi dell\'offerta.');
            return redirect()->to('/login');
        }
        
        // Verifica che l'offerta esista
        $offerta = $this->offertaFornitoreModel->find($id_offerta);
        if (empty($offerta)) {
            $session->setFlashdata('error', 'Offerta non trovata');
            return redirect()->to('/offerte-fornitore');
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
        
        if ($this->offertaFornitoreModel->update($id_offerta, $data)) {
            // Aggiorna l'importo totale dell'offerta
            $this->offertaFornitoreModel->aggiornaImportoTotale((int)$id_offerta);
            
            $session->setFlashdata('success', 'Costi aggiornati con successo');
        } else {
            $session->setFlashdata('error', 'Si è verificato un errore durante l\'aggiornamento dei costi');
        }
        
        return redirect()->to('/offerte-fornitore/' . $id_offerta);
    }
    
    /**
     * Aggiunge un nuovo materiale al catalogo e lo collega a una voce dell'offerta
     */
    public function aggiungiMaterialeVoce($id_offerta)
    {
        // Verifica che l'offerta esista
        $offerta = $this->offertaFornitoreModel->find($id_offerta);
        if (empty($offerta)) {
            return redirect()->to('offerte-fornitore')->with('error', 'Offerta non trovata');
        }
        
        // Validazione del form per il nuovo materiale
        $rulesMateriale = [
            'new_codice' => 'required|min_length[1]|max_length[50]|is_unique[materiali.codice]',
            'new_descrizione' => 'required',
            'quantita' => 'required|numeric|greater_than[0]',
            'prezzo_unitario' => 'required|numeric|greater_than_equal_to[0]',
            'unita_misura' => 'permit_empty|max_length[20]'
        ];
        
        if (!$this->validate($rulesMateriale)) {
            return redirect()->back()->with('error', 'Dati non validi. ' . json_encode($this->validator->getErrors()))->withInput();
        }
        
        // Prepara i dati per il nuovo materiale
        $datiMateriale = [
            'codice' => $this->request->getPost('new_codice'),
            'descrizione' => $this->request->getPost('new_descrizione'),
            'materiale' => $this->request->getPost('new_materiale'),
            'produttore' => $this->request->getPost('new_produttore'),
            'commerciale' => $this->request->getPost('new_commerciale') ? 1 : 0,
            'meccanica' => $this->request->getPost('new_meccanica') ? 1 : 0,
            'elettrica' => $this->request->getPost('new_elettrica') ? 1 : 0,
            'pneumatica' => $this->request->getPost('new_pneumatica') ? 1 : 0,
            'in_produzione' => 1 // Nuovo materiale è in produzione di default
        ];
        
        // Salva il nuovo materiale
        if (!$this->materialeModel->insert($datiMateriale)) {
            return redirect()->back()->with('error', 'Errore durante il salvataggio del nuovo materiale: ' . json_encode($this->materialeModel->errors()))->withInput();
        }
        
        // Recupera l'ID del materiale appena inserito
        $id_materiale = $this->materialeModel->getInsertID();
        
        // Prepara i dati per la voce dell'offerta
        $quantita = (float)$this->request->getPost('quantita');
        $prezzo_unitario = (float)$this->request->getPost('prezzo_unitario');
        $unita_misura = $this->request->getPost('unita_misura') ?: 'pz';
        $id_progetto = $this->request->getPost('id_progetto') ?: null;
        $sconto = (float)($this->request->getPost('sconto') ?: 0);
        $note = $this->request->getPost('note') ?: null;
        
        // Calcola l'importo
        $importo = $this->offertaFornitoreVoceModel->calcolaImporto($prezzo_unitario, $quantita, $sconto);
        
        // Inserimento nella tabella offerte_fornitore_voci
        $dataVoce = [
            'id_offerta_fornitore' => $id_offerta,
            'id_materiale' => $id_materiale,
            'codice' => $datiMateriale['codice'],
            'descrizione' => $datiMateriale['descrizione'],
            'quantita' => $quantita,
            'prezzo_unitario' => $prezzo_unitario,
            'importo' => $importo,
            'unita_misura' => $unita_misura,
            'sconto' => $sconto,
            'note' => $note,
            'id_progetto' => $id_progetto
        ];
        
        if ($this->offertaFornitoreVoceModel->insert($dataVoce)) {
            // Aggiorna l'importo totale dell'offerta
            $this->offertaFornitoreModel->aggiornaImportoTotale((int)$id_offerta);
            
            return redirect()->to("offerte-fornitore/{$id_offerta}")->with('success', 'Nuovo materiale creato e aggiunto all\'offerta con successo');
        } else {
            return redirect()->back()->with('error', 'Materiale creato ma errore durante l\'aggiunta alla voce dell\'offerta');
        }
    }

    public function search()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([]);
        }

        $term = $this->request->getGet('term');
        if (empty($term) || strlen($term) < 2) {
            return $this->response->setJSON([]);
        }

        $offerte = $this->offertaFornitoreModel
            ->select('offerte_fornitore.*, anagrafiche.ragione_sociale as nome_fornitore')
            ->join('anagrafiche', 'anagrafiche.id = offerte_fornitore.id_anagrafica')
            ->groupStart()
                ->like('offerte_fornitore.numero', $term)
                ->orLike('offerte_fornitore.oggetto', $term)
                ->orLike('anagrafiche.ragione_sociale', $term)
            ->groupEnd()
            ->where('offerte_fornitore.stato !=', 'annullata')
            ->orderBy('offerte_fornitore.data', 'DESC')
            ->limit(10)
            ->find();

        $result = [];
        foreach ($offerte as $offerta) {
            $result[] = [
                'id' => $offerta['id'],
                'numero' => $offerta['numero'],
                'data' => date('d/m/Y', strtotime($offerta['data'])),
                'oggetto' => $offerta['oggetto'],
                'nome_fornitore' => $offerta['nome_fornitore']
            ];
        }

        return $this->response->setJSON($result);
    }

    public function getVoci($id_offerta)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([]);
        }

        $voci = $this->offertaFornitoreVoceModel->getVociByOfferta((int)$id_offerta);

        return $this->response->setJSON($voci);
    }
}
