<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\RichiestaOffertaModel;
use App\Models\AnagraficaModel;
use App\Models\ContattoModel;
use App\Models\AnagraficaContattoModel;
use App\Models\ProgettoModel;
use App\Models\UtentiModel;
use App\Models\RichiestaMaterialeModel;
use App\Models\Materiale;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

final class RichiesteOffertaController extends BaseController
{
    protected $richiestaOffertaModel;
    protected $anagraficaModel;
    protected $contattoModel;
    protected $anagraficaContattoModel;
    protected $progettoModel;
    protected $utentiModel;
    protected $materialeModel;
    protected $richiestaMaterialeModel;
    
    public function __construct()
    {
        helper(['form', 'date']);
        $this->richiestaOffertaModel = new \App\Models\RichiestaOffertaModel();
        $this->anagraficaModel = new \App\Models\AnagraficaModel();
        $this->contattoModel = new ContattoModel();
        $this->anagraficaContattoModel = new \App\Models\AnagraficaContattoModel();
        $this->progettoModel = new \App\Models\ProgettoModel();
        $this->utentiModel = new \App\Models\UtentiModel();
        $this->materialeModel = new \App\Models\Materiale();
        $this->richiestaMaterialeModel = new \App\Models\RichiestaMaterialeModel();
    }
    
    /**
     * Mostra la lista delle richieste d'offerta
     */
    public function index()
    {
        $data = [
            'title' => 'Richieste d\'Offerta',
            'richieste' => $this->richiestaOffertaModel->getRichiesteWithRelations()
        ];
        
        return view('richieste_offerta/index', $data);
    }
    
    /**
     * Mostra i dettagli di una richiesta d'offerta
     */
    public function show($id = null)
    {
        $id = ($id) ? $id : $this->request->getPost('id');
        
        if (!$id) {
            return redirect()->to('/richieste-offerta')->with('error', 'ID richiesta non specificato');
        }
        
        $richiestaOfferta = $this->richiestaOffertaModel->getRichiestaWithRelations((int)$id);
        
        if (!$richiestaOfferta) {
            return redirect()->to('/richieste-offerta')->with('error', 'Richiesta d\'offerta non trovata');
        }
        
        // Se la richiesta è AJAX, restituisci i dati in formato JSON
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'richiesta' => $richiestaOfferta
            ]);
        }
        
        // Ottieni i materiali associati alla richiesta
        $voci = $this->richiestaMaterialeModel->getMaterialiByRichiesta((int)$id);
        
        // Ottieni i progetti per i dropdown
        $progetti = $this->progettoModel->findAll();
        
        // Ottieni i template email di tipo RDO
        $emailTemplates = \Config\Database::connect()
                               ->table('email_templates')
                               ->where('tipo', 'RDO')
                               ->get()
                               ->getResultArray();
        
        // Ottieni tutti i contatti dell'anagrafica
        $contatti = [];
        $contattoPrincipale = null;
        if (!empty($richiestaOfferta['id_anagrafica'])) {
            $contatti = $this->anagraficaContattoModel->getContattiByAnagrafica((int)$richiestaOfferta['id_anagrafica']);
            
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
        $email_logs = $emailLogModel->getByRichiesta($id);
        
        $data = [
            'title' => 'Dettaglio Richiesta di Offerta',
            'richiesta' => $richiestaOfferta,
            'voci' => $voci,
            'progetti' => $progetti,
            'emailTemplates' => $emailTemplates,
            'contatti' => $contatti,
            'contattoPrincipale' => $contattoPrincipale,
            'email_logs' => $email_logs
        ];
        
        return view('richieste_offerta/show', $data);
    }
    
    /**
     * Mostra il form per creare una nuova richiesta d'offerta
     */
    public function new()
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per creare una richiesta d\'offerta.');
            return redirect()->to('/login');
        }
        
        $data = [
            'title' => 'Nuova Richiesta d\'Offerta',
            'fornitori' => $this->anagraficaModel->where('fornitore', 1)->where('attivo', 1)->findAll(),
            'progetti' => $this->progettoModel->where('attivo', 1)->findAll(),
        ];
        
        return view('richieste_offerta/create', $data);
    }
    
    /**
     * Crea una nuova richiesta d'offerta
     */
    public function create()
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per creare una richiesta d\'offerta.');
            return redirect()->to('/login');
        }
        
        // Imposta l'utente corrente come creatore della richiesta
        $idUtente = $session->get('utente_id');
        
        if (empty($idUtente)) {
            $idUtente = 1; // Valore predefinito se l'utente non è in sessione
        }
        
        // Ottieni la data dal form e convertila in formato ISO
        $dataItaliana = $this->request->getPost('data') ?: date('d/m/Y');
        $dataISO = formatDateToISO($dataItaliana) ?: date('Y-m-d');
        
        // Dati del form
        $data = [
            'numero' => $this->richiestaOffertaModel->generateNumeroRichiesta(),
            'data' => $dataISO,
            'oggetto' => $this->request->getPost('oggetto'),
            'descrizione' => $this->request->getPost('descrizione'),
            'id_anagrafica' => $this->request->getPost('id_anagrafica') ? (int)$this->request->getPost('id_anagrafica') : null,
            'id_referente' => $this->request->getPost('id_referente') ? (int)$this->request->getPost('id_referente') : null,
            'id_progetto' => $this->request->getPost('id_progetto') ? (int)$this->request->getPost('id_progetto') : null,
            'stato' => 'bozza',
            'id_utente_creatore' => $idUtente,
            'note' => $this->request->getPost('note')
        ];
        
        // Validazione
        if (!$this->richiestaOffertaModel->validate($data)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->richiestaOffertaModel->errors());
        }
        
        // Salva la richiesta d'offerta
        $this->richiestaOffertaModel->insert($data);
        $idRichiesta = $this->richiestaOffertaModel->getInsertID();
        
        $session->setFlashdata('success', 'Richiesta d\'offerta creata con successo.');
        return redirect()->to('/richieste-offerta/' . $idRichiesta);
    }
    
    /**
     * Mostra il form per modificare una richiesta d'offerta
     */
    public function edit($id = null)
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per modificare una richiesta d\'offerta.');
            return redirect()->to('/login');
        }
        
        $richiesta = $this->richiestaOffertaModel->find($id);
        
        if (empty($richiesta)) {
            throw new PageNotFoundException('Richiesta d\'offerta non trovata');
        }
        
        // Se la richiesta non è più in stato bozza, non è possibile modificarla
        if ($richiesta['stato'] !== 'bozza') {
            $session->setFlashdata('error', 'Non è possibile modificare una richiesta d\'offerta che non è in stato bozza.');
            return redirect()->to('/richieste-offerta/' . $id);
        }
        
        // Ottieni i contatti dell'anagrafica selezionata
        $contatti = $this->anagraficaContattoModel->getContattiByAnagrafica((int)$richiesta['id_anagrafica']);
        
        $data = [
            'title' => 'Modifica Richiesta d\'Offerta',
            'richiesta' => $richiesta,
            'fornitori' => $this->anagraficaModel->where('fornitore', 1)->where('attivo', 1)->findAll(),
            'progetti' => $this->progettoModel->where('attivo', 1)->findAll(),
            'contatti' => $contatti
        ];
        
        return view('richieste_offerta/edit', $data);
    }
    
    /**
     * Aggiorna una richiesta d'offerta esistente
     */
    public function update($id = null)
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per modificare una richiesta d\'offerta.');
            return redirect()->to('/login');
        }
        
        $richiesta = $this->richiestaOffertaModel->find($id);
        
        if (empty($richiesta)) {
            throw new PageNotFoundException('Richiesta d\'offerta non trovata');
        }
        
        // Se la richiesta non è più in stato bozza, non è possibile modificarla
        if ($richiesta['stato'] !== 'bozza') {
            $session->setFlashdata('error', 'Non è possibile modificare una richiesta d\'offerta che non è in stato bozza.');
            return redirect()->to('/richieste-offerta/' . $id);
        }
        
        // Dati del form
        $dataItaliana = $this->request->getPost('data');
        $dataISO = formatDateToISO($dataItaliana) ?: $richiesta['data'];
        
        $data = [
            'data' => $dataISO,
            'oggetto' => $this->request->getPost('oggetto'),
            'descrizione' => $this->request->getPost('descrizione'),
            'id_anagrafica' => $this->request->getPost('id_anagrafica') ? (int)$this->request->getPost('id_anagrafica') : null,
            'id_referente' => $this->request->getPost('id_referente') ? (int)$this->request->getPost('id_referente') : null,
            'id_progetto' => $this->request->getPost('id_progetto') ? (int)$this->request->getPost('id_progetto') : null,
            'note' => $this->request->getPost('note')
        ];
        
        // Validazione
        if (!$this->richiestaOffertaModel->validate(array_merge($richiesta, $data))) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->richiestaOffertaModel->errors());
        }
        
        // Aggiorna la richiesta d'offerta
        $this->richiestaOffertaModel->update($id, $data);
        
        $session->setFlashdata('success', 'Richiesta d\'offerta aggiornata con successo.');
        return redirect()->to('/richieste-offerta/' . $id);
    }
    
    /**
     * Cambia lo stato di una richiesta d'offerta
     */
    public function cambiaStato($id = null)
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per cambiare lo stato di una richiesta d\'offerta.');
            return redirect()->to('/login');
        }
        
        $richiesta = $this->richiestaOffertaModel->find($id);
        
        if (empty($richiesta)) {
            throw new PageNotFoundException('Richiesta d\'offerta non trovata');
        }
        
        $nuovoStato = $this->request->getPost('stato');
        
        // Verifica che lo stato sia valido
        if (!in_array($nuovoStato, ['bozza', 'inviata', 'accettata', 'rifiutata', 'annullata'])) {
            $session->setFlashdata('error', 'Stato non valido.');
            return redirect()->to('/richieste-offerta/' . $id);
        }
        
        $data = ['stato' => $nuovoStato];
        
        // Se lo stato è "inviata", imposta la data di invio
        if ($nuovoStato === 'inviata' && empty($richiesta['data_invio'])) {
            $data['data_invio'] = date('Y-m-d H:i:s');
        }
        
        // Se lo stato è "accettata", imposta la data di accettazione
        if ($nuovoStato === 'accettata' && empty($richiesta['data_accettazione'])) {
            $data['data_accettazione'] = date('Y-m-d H:i:s');
        }
        
        // Aggiorna lo stato
        $this->richiestaOffertaModel->update($id, $data);
        
        $session->setFlashdata('success', 'Stato della richiesta d\'offerta aggiornato con successo.');
        return redirect()->to('/richieste-offerta/' . $id);
    }
    
    /**
     * Elimina una richiesta d'offerta (soft delete)
     */
    public function delete($id = null)
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per eliminare una richiesta d\'offerta.');
            return redirect()->to('/login');
        }
        
        $richiesta = $this->richiestaOffertaModel->find($id);
        
        if (empty($richiesta)) {
            throw new PageNotFoundException('Richiesta d\'offerta non trovata');
        }
        
        // Non permettere l'eliminazione di richieste inviate o accettate
        if (in_array($richiesta['stato'], ['inviata', 'accettata'])) {
            $session->setFlashdata('error', 'Non è possibile eliminare una richiesta d\'offerta che è stata inviata o accettata.');
            return redirect()->to('/richieste-offerta/' . $id);
        }
        
        $this->richiestaOffertaModel->delete($id);
        
        $session->setFlashdata('success', 'Richiesta d\'offerta eliminata con successo.');
        return redirect()->to('/richieste-offerta');
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
     * Carica le richieste d'offerta di un fornitore tramite AJAX
     */
    public function getRichiesteByFornitore()
    {
        $idAnagrafica = $this->request->getPost('id_anagrafica');
        
        if (!$idAnagrafica) {
            return $this->response->setJSON(['success' => false, 'richieste' => [], 'error' => 'ID anagrafica non fornito']);
        }
        
        $richieste = $this->richiestaOffertaModel->getRichiesteByFornitore((int)$idAnagrafica);
        
        // Log per debug
        log_message('info', 'Richiesta offerte per id_anagrafica: ' . $idAnagrafica . ' - Trovate: ' . count($richieste));
        
        return $this->response->setJSON([
            'success' => true,
            'richieste' => $richieste,
            'count' => count($richieste),
            'id_anagrafica' => $idAnagrafica
        ]);
    }
    
    /**
     * Visualizza le richieste d'offerta per un fornitore specifico
     */
    public function perFornitore($idAnagrafica = null)
    {
        $fornitore = $this->anagraficaModel->find($idAnagrafica);
        
        if (empty($fornitore)) {
            throw new PageNotFoundException('Fornitore non trovato');
        }
        
        $data = [
            'title' => 'Richieste d\'Offerta per ' . $fornitore['ragione_sociale'],
            'fornitore' => $fornitore,
            'richieste' => $this->richiestaOffertaModel->getRichiesteByFornitore((int)$idAnagrafica)
        ];
        
        return view('richieste_offerta/per_fornitore', $data);
    }
    
    /**
     * Visualizza le richieste d'offerta per un progetto specifico
     */
    public function perProgetto($idProgetto = null)
    {
        $progetto = $this->progettoModel->find($idProgetto);
        
        if (empty($progetto)) {
            throw new PageNotFoundException('Progetto non trovato');
        }
        
        $data = [
            'title' => 'Richieste d\'Offerta per il progetto ' . $progetto['nome'],
            'progetto' => $progetto,
            'richieste' => $this->richiestaOffertaModel->getRichiesteByProgetto((int)$idProgetto)
        ];
        
        return view('richieste_offerta/per_progetto', $data);
    }

    /**
     * Crea una nuova richiesta d'offerta a partire dai materiali selezionati in un progetto
     */
    public function createFromProject()
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per creare una richiesta d\'offerta.');
            return redirect()->to('/login');
        }
        
        // Imposta l'utente corrente come creatore della richiesta
        $idUtente = $session->get('utente_id');
        
        if (empty($idUtente)) {
            $idUtente = 1; // Valore predefinito se l'utente non è in sessione
        }
        
        // Ottieni la data corrente in formato ISO
        $dataISO = date('Y-m-d');
        
        // Dati del form
        $data = [
            'numero' => $this->richiestaOffertaModel->generateNumeroRichiesta(),
            'data' => $dataISO,
            'oggetto' => $this->request->getPost('oggetto'),
            'descrizione' => $this->request->getPost('descrizione'),
            'id_anagrafica' => $this->request->getPost('id_anagrafica') ? (int)$this->request->getPost('id_anagrafica') : null,
            'id_progetto' => $this->request->getPost('id_progetto') ? (int)$this->request->getPost('id_progetto') : null,
            'stato' => 'bozza',
            'id_utente_creatore' => $idUtente,
            'note' => $this->request->getPost('note')
        ];
        
        // Validazione
        if (!$this->richiestaOffertaModel->validate($data)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->richiestaOffertaModel->errors());
        }
        
        // Salva la richiesta d'offerta
        $this->richiestaOffertaModel->insert($data);
        $idRichiesta = $this->richiestaOffertaModel->getInsertID();
        
        // Materiali selezionati
        $materialiSelezionati = $this->request->getPost('materiali_selezionati');
        
        if (!empty($materialiSelezionati)) {
            try {
                $materialiArray = json_decode($materialiSelezionati, true);
                
                if (is_array($materialiArray) && count($materialiArray) > 0) {
                    // Recupera i dettagli dei materiali dal database
                    $progettoMaterialiModel = new \App\Models\ProgettoMaterialeModel();
                    $materialiDettagli = $progettoMaterialiModel->getDettagliMateriali($materialiArray);
                    
                    // Aggiungi ciascun materiale alla richiesta
                    foreach ($materialiDettagli as $materiale) {
                        $this->richiestaMaterialeModel->insert([
                            'id_richiesta' => $idRichiesta,
                            'id_materiale' => $materiale['id_materiale'],
                            'quantita' => $materiale['quantita'],
                            'unita_misura' => $materiale['unita_misura'],
                            'id_progetto' => $data['id_progetto']
                        ]);
                    }
                }
            } catch (\Exception $e) {
                log_message('error', 'Errore nell\'elaborazione dei materiali: ' . $e->getMessage());
                $session->setFlashdata('warning', 'Richiesta creata, ma si è verificato un errore nell\'aggiunta dei materiali.');
            }
        }
        
        $session->setFlashdata('success', 'Richiesta d\'offerta creata con successo.');
        return redirect()->to('/richieste-offerta/' . $idRichiesta);
    }

    /**
     * Aggiunge un materiale alla richiesta d'offerta
     */
    public function aggiungiMateriale($id_richiesta)
    {
        // Verifica che la richiesta esista
        $richiesta = $this->richiestaOffertaModel->find($id_richiesta);
        if (empty($richiesta)) {
            return redirect()->to('richieste-offerta')->with('error', 'Richiesta non trovata');
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

        // Verifica se il materiale è già presente in questa richiesta
        $richiestaMaterialeModel = new RichiestaMaterialeModel();
        if ($richiestaMaterialeModel->esisteMateriale((int)$id_richiesta, (int)$id_materiale)) {
            return redirect()->back()->with('error', 'Questo materiale è già presente nella richiesta d\'offerta');
        }

        // Inserimento nella tabella richieste_materiali
        $data = [
            'id_richiesta' => $id_richiesta,
            'id_materiale' => $id_materiale,
            'quantita' => $quantita,
            'id_progetto' => $id_progetto,
            'unita_misura' => $unita_misura
        ];

        if ($richiestaMaterialeModel->insert($data)) {
            return redirect()->to("richieste-offerta/{$id_richiesta}")->with('success', 'Materiale aggiunto con successo');
        } else {
            return redirect()->back()->with('error', 'Si è verificato un errore durante l\'aggiunta del materiale');
        }
    }

    /**
     * Aggiunge un nuovo materiale all'archivio e lo associa alla richiesta d'offerta
     */
    public function aggiungiNuovoMateriale($id_richiesta)
    {
        // Verifica che la richiesta esista
        $richiesta = $this->richiestaOffertaModel->find($id_richiesta);
        if (empty($richiesta)) {
            return redirect()->to('richieste-offerta')->with('error', 'Richiesta non trovata');
        }

        // Validazione del form per il nuovo materiale e la quantità
        $rules = [
            'codice' => 'required|min_length[1]|max_length[50]|is_unique[materiali.codice]',
            'descrizione' => 'required',
            'quantita' => 'required|numeric|greater_than[0]',
            'id_progetto' => 'permit_empty|numeric',
            'unita_misura' => 'permit_empty|max_length[20]'
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

        // Dati per l'associazione alla richiesta
        $quantita = $this->request->getPost('quantita');
        $id_progetto = $this->request->getPost('id_progetto') ?: null;
        $unita_misura = $this->request->getPost('unita_misura') ?: 'pz';

        // Verifica se il progetto esiste (se specificato)
        if ($id_progetto) {
            $progetto = $this->progettoModel->find($id_progetto);
            if (empty($progetto)) {
                return redirect()->back()->with('error', 'Progetto non trovato');
            }
        }

        // Inserimento nella tabella richieste_materiali
        $data = [
            'id_richiesta' => $id_richiesta,
            'id_materiale' => $id_materiale,
            'quantita' => $quantita,
            'id_progetto' => $id_progetto,
            'unita_misura' => $unita_misura
        ];

        $richiestaMaterialeModel = new RichiestaMaterialeModel();
        if ($richiestaMaterialeModel->insert($data)) {
            return redirect()->to("richieste-offerta/{$id_richiesta}")->with('success', 'Nuovo materiale creato e aggiunto alla richiesta con successo');
        } else {
            return redirect()->back()->with('error', 'Materiale creato ma errore durante l\'associazione alla richiesta')->withInput();
        }
    }

    /**
     * Aggiorna un materiale nella richiesta d'offerta
     */
    public function aggiornaMateriale($id_richiesta)
    {
        // Verifica che la richiesta esista
        $richiesta = $this->richiestaOffertaModel->find($id_richiesta);
        if (empty($richiesta)) {
            return redirect()->to('richieste-offerta')->with('error', 'Richiesta non trovata');
        }

        // Validazione del form
        $rules = [
            'id' => 'required|numeric',
            'id_materiale' => 'required|numeric',
            'quantita' => 'required|numeric|greater_than[0]',
            'id_progetto' => 'permit_empty|numeric',
            'unita_misura' => 'permit_empty|max_length[20]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Dati non validi. Verifica i campi e riprova.');
        }

        $id = $this->request->getPost('id');
        $id_materiale = $this->request->getPost('id_materiale');
        $quantita = $this->request->getPost('quantita');
        $id_progetto = $this->request->getPost('id_progetto') ?: null;
        $unita_misura = $this->request->getPost('unita_misura') ?: 'pz';

        // Verifica che il record esista
        $richiestaMaterialeModel = new RichiestaMaterialeModel();
        $record = $richiestaMaterialeModel->find($id);
        if (empty($record) || $record['id_richiesta'] != $id_richiesta) {
            return redirect()->back()->with('error', 'Record non trovato');
        }

        // Aggiorna il record
        $data = [
            'quantita' => $quantita,
            'id_progetto' => $id_progetto,
            'unita_misura' => $unita_misura
        ];

        if ($richiestaMaterialeModel->update($id, $data)) {
            return redirect()->to("richieste-offerta/{$id_richiesta}")->with('success', 'Materiale aggiornato con successo');
        } else {
            return redirect()->back()->with('error', 'Si è verificato un errore durante l\'aggiornamento del materiale');
        }
    }

    /**
     * Rimuove un materiale dalla richiesta d'offerta
     */
    public function rimuoviMateriale($id_richiesta, $id_record)
    {
        $session = session();
        
        // Controlla che la richiesta esista
        $richiesta = $this->richiestaOffertaModel->find($id_richiesta);
        if (!$richiesta) {
            $session->setFlashdata('error', 'Richiesta d\'offerta non trovata');
            return redirect()->to('/richieste-offerta');
        }
        
        // Se la richiesta è stata già inviata o accettata, non è possibile modificarla
        if (in_array($richiesta['stato'], ['inviata', 'accettata'])) {
            $session->setFlashdata('error', 'Non è possibile modificare una richiesta d\'offerta in stato ' . $richiesta['stato']);
            return redirect()->to('/richieste-offerta/' . $id_richiesta);
        }
        
        // Rimuovi il materiale
        $richiestaMaterialeModel = new RichiestaMaterialeModel();
        if ($richiestaMaterialeModel->delete($id_record)) {
            $session->setFlashdata('success', 'Materiale rimosso dalla richiesta d\'offerta');
        } else {
            $session->setFlashdata('error', 'Errore durante la rimozione del materiale');
        }
        
        return redirect()->to('/richieste-offerta/' . $id_richiesta);
    }
    
    /**
     * Invia una email con la richiesta d'offerta
     */
    public function inviaEmail($id)
    {
        // Controllo che la richiesta d'offerta esista
        $richiestaOfferta = $this->richiestaOffertaModel->find($id);
        if (!$richiestaOfferta) {
            return redirect()->to('/richieste-offerta')->with('error', 'Richiesta d\'offerta non trovata');
        }
        
        // Controllo che sia una richiesta POST con CSRF valido
        if ($this->request->getMethod() !== 'POST') {
            log_message('error', "InviaEmail - Metodo non consentito: " . $this->request->getMethod());
            return redirect()->to('/richieste-offerta/' . $id)->with('error', 'Metodo non consentito');
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
            return redirect()->to('/richieste-offerta/' . $id)->with('errors', $this->validator->getErrors());
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
            // Ottieni i materiali della richiesta
            $voci = $this->richiestaMaterialeModel->getMaterialiByRichiesta((int)$id);
            
            // Aggiungi la tabella dei materiali solo se ci sono voci
            if (!empty($voci)) {
                $materialiHtml = '<hr><h4>Materiali Richiesti</h4>';
                $materialiHtml .= '<table style="border-collapse: collapse; width: 100%; margin-top: 10px; margin-bottom: 20px;">';
                $materialiHtml .= '<thead style="background-color: #f2f2f2;">';
                $materialiHtml .= '<tr>';
                $materialiHtml .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Codice</th>';
                $materialiHtml .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Descrizione</th>';
                $materialiHtml .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Quantità</th>';
                $materialiHtml .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Unità</th>';
                $materialiHtml .= '</tr>';
                $materialiHtml .= '</thead>';
                $materialiHtml .= '<tbody>';
                
                foreach ($voci as $materiale) {
                    $materialiHtml .= '<tr>';
                    $materialiHtml .= '<td style="border: 1px solid #ddd; padding: 8px;">' . esc($materiale['codice']) . '</td>';
                    $materialiHtml .= '<td style="border: 1px solid #ddd; padding: 8px;">' . esc($materiale['descrizione']) . '</td>';
                    $materialiHtml .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: right;">' . esc($materiale['quantita']) . '</td>';
                    $materialiHtml .= '<td style="border: 1px solid #ddd; padding: 8px;">' . esc($materiale['unita_misura']) . '</td>';
                    $materialiHtml .= '</tr>';
                }
                
                $materialiHtml .= '</tbody>';
                $materialiHtml .= '</table>';
                
                $corpo .= $materialiHtml;
            }
        }
        
        // Prepara allegati
        $allegati = [];
        
        // Gestione allegati caricati
        if ($files = $this->request->getFiles()) {
            if (isset($files['allegati'])) {
                foreach ($files['allegati'] as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $newName = $file->getRandomName();
                        $file->move(TEMP_UPLOAD_PATH, $newName);
                        $allegati[] = $newName;
                    }
                }
            }
        }
        
        // Allegato PDF
        $attach_pdf = ['pdf' => 'false'];
        if ($this->request->getPost('allega_pdf')) {
            $attach_pdf = [
                'pdf' => 'true',
                'id_richiesta' => $id,
                'numero' => $richiestaOfferta['numero']
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
                return redirect()->to('/richieste-offerta/' . $id)->with('error', 'Impossibile inviare l\'email: nessun mittente valido disponibile. Contatta l\'amministratore.');
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
            log_email($destinatari, $cc, $ccn, $oggetto, $corpo, $id, 'RDO', 'inviato', null, $allegati);
            
            // Aggiorna lo stato della richiesta
            $this->richiestaOffertaModel->update($id, [
                'stato' => 'inviata',
                'data_invio' => date('Y-m-d H:i:s')
            ]);
            
            return redirect()->to('/richieste-offerta/' . $id)->with('success', 'Email inviata con successo');
        } else {
            // Registra l'errore nel log
            log_email($destinatari, $cc, $ccn, $oggetto, $corpo, $id, 'RDO', 'errore', $result['msg'], $allegati);
            
            return redirect()->to('/richieste-offerta/' . $id)->with('error', 'Errore nell\'invio dell\'email: ' . $result['msg']);
        }
    }

    /**
     * Visualizza lo storico delle email per una specifica richiesta d'offerta
     */
    public function emailLog($id)
    {
        // Verifica che la richiesta esista
        $richiesta = $this->richiestaOffertaModel->find($id);
        if (!$richiesta) {
            return redirect()->to('/richieste-offerta')->with('error', 'Richiesta d\'offerta non trovata');
        }
        
        // Carica il modello delle email logs
        $emailLogModel = new \App\Models\EmailLogModel();
        $emails = $emailLogModel->getByRichiesta($id);
        
        $data = [
            'title' => 'Storico Email - Richiesta d\'offerta #' . $richiesta['numero'],
            'richiesta' => $richiesta,
            'emails' => $emails
        ];
        
        return view('richieste_offerta/email_log', $data);
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
        
        // Ottieni la richiesta d'offerta associata
        $richiesta = $this->richiestaOffertaModel->find($email['id_riferimento']);
        if (!$richiesta) {
            return redirect()->to('/richieste-offerta')->with('error', 'Richiesta d\'offerta non trovata');
        }
        
        $data = [
            'title' => 'Dettaglio Email',
            'email' => $email,
            'richiesta' => $richiesta
        ];
        
        return view('richieste_offerta/visualizza_email', $data);
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
        
        // Ottieni la richiesta d'offerta associata
        $id_richiesta = $emailOriginale['id_riferimento'];
        $richiesta = $this->richiestaOffertaModel->find($id_richiesta);
        if (!$richiesta) {
            log_message('error', "RispondiEmail - Richiesta offerta non trovata con ID: {$id_richiesta}");
            return redirect()->to('/richieste-offerta')->with('error', 'Richiesta d\'offerta non trovata');
        }
        
        // Debug: traccia la richiesta trovata
        log_message('debug', "RispondiEmail - Richiesta offerta trovata: " . json_encode($richiesta));
        
        // Controllo che sia una richiesta POST con CSRF valido
        if ($this->request->getMethod() !== 'POST') {
            log_message('error', "RispondiEmail - Metodo non consentito: " . $this->request->getMethod());
            return redirect()->to('/richieste-offerta/' . $id_richiesta)->with('error', 'Metodo non consentito');
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
            return redirect()->to('/richieste-offerta/' . $id_richiesta)->with('errors', $this->validator->getErrors());
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
            if (isset($files['allegati'])) {
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
        }
        
        // Allegato PDF
        $attach_pdf = ['pdf' => 'false'];
        if ($this->request->getPost('allega_pdf')) {
            $attach_pdf = [
                'pdf' => 'true',
                'id_richiesta' => $id_richiesta,
                'numero' => $richiesta['numero']
            ];
            log_message('debug', "RispondiEmail - Allegare PDF della richiesta: " . json_encode($attach_pdf));
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
                return redirect()->to('/richieste-offerta/' . $id_richiesta)->with('error', 'Impossibile inviare l\'email: nessun mittente valido disponibile. Contatta l\'amministratore.');
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
                log_email($destinatari, $cc, $ccn, $oggetto, $corpo, $id_richiesta, 'RDO', 'inviato', null, $allegati);
                log_message('info', "RispondiEmail - Email inviata con successo e registrata nel log");
                
                return redirect()->to('/richieste-offerta/' . $id_richiesta)->with('success', 'Risposta inviata con successo');
            } else {
                // Registra l'errore nel log
                log_email($destinatari, $cc, $ccn, $oggetto, $corpo, $id_richiesta, 'RDO', 'errore', $result['msg'], $allegati);
                log_message('error', "RispondiEmail - Errore invio email: " . $result['msg']);
                
                return redirect()->to('/richieste-offerta/' . $id_richiesta)->with('error', 'Errore nell\'invio della risposta: ' . $result['msg']);
            }
        } catch (\Exception $e) {
            log_message('error', "RispondiEmail - Eccezione durante l'invio: " . $e->getMessage());
            return redirect()->to('/richieste-offerta/' . $id_richiesta)->with('error', 'Errore nell\'invio della risposta: ' . $e->getMessage());
        }
    }
} 