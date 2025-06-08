<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PickupDeliveryModel;
use App\Models\AnagraficaModel;
use App\Models\ContattoModel;
use App\Models\AttivitaModel;
use App\Models\UtentiModel;
use App\Models\AnagraficaContattoModel;
use CodeIgniter\HTTP\ResponseInterface;

final class PickupDelivery extends BaseController
{
    private PickupDeliveryModel $pickupDeliveryModel;
    private AnagraficaModel $anagraficaModel;
    private ContattoModel $contattoModel;
    private AttivitaModel $attivitaModel;
    private UtentiModel $utentiModel;
    private AnagraficaContattoModel $anagraficaContattoModel;
    
    public function __construct()
    {
        $this->pickupDeliveryModel = new PickupDeliveryModel();
        $this->anagraficaModel = new AnagraficaModel();
        $this->contattoModel = new ContattoModel();
        $this->attivitaModel = new AttivitaModel();
        $this->utentiModel = new UtentiModel();
        $this->anagraficaContattoModel = new AnagraficaContattoModel();
    }
    
    /**
     * Mostra l'elenco di tutte le operazioni di pickup & delivery
     */
    public function index()
    {
        $operazioni = $this->pickupDeliveryModel->getAllWithRelations();
        $utenti = $this->utentiModel->findAll();
        $statistiche = $this->pickupDeliveryModel->getStatistiche();
        
        $data = [
            'title' => 'Pickup & Delivery',
            'operazioni' => $operazioni,
            'utenti' => $utenti,
            'statistiche' => $statistiche
        ];
        
        return view('pickup_delivery/index', $data);
    }
    
    /**
     * Mostra solo i ritiri
     */
    public function ritiri()
    {
        $ritiri = $this->pickupDeliveryModel->getByTipo('ritiro');
        
        $data = [
            'title' => 'Ritiri',
            'operazioni' => $ritiri,
            'tipo' => 'ritiro'
        ];
        
        return view('pickup_delivery/index', $data);
    }
    
    /**
     * Mostra solo le consegne
     */
    public function consegne()
    {
        $consegne = $this->pickupDeliveryModel->getByTipo('consegna');
        
        $data = [
            'title' => 'Consegne',
            'operazioni' => $consegne,
            'tipo' => 'consegna'
        ];
        
        return view('pickup_delivery/index', $data);
    }
    
    /**
     * Mostra la vista calendario
     */
    public function calendario()
    {
        $operazioni = $this->pickupDeliveryModel->getAllWithRelations();
        
        // Prepara i dati per il calendario
        $eventi = [];
        foreach ($operazioni as $operazione) {
            $colore = $this->getColorByStato($operazione['stato']);
            $icona = $operazione['tipo'] === 'ritiro' ? 'fa-truck-loading' : 'fa-truck';
            
            $eventi[] = [
                'id' => $operazione['id'],
                'title' => $operazione['titolo'],
                'start' => $operazione['data_programmata'],
                'end' => $operazione['data_completata'] ?? $operazione['data_programmata'],
                'backgroundColor' => $colore,
                'borderColor' => $colore,
                'extendedProps' => [
                    'tipo' => $operazione['tipo'],
                    'stato' => $operazione['stato'],
                    'priorita' => $operazione['priorita'],
                    'anagrafica' => $operazione['ragione_sociale'],
                    'icona' => $icona
                ]
            ];
        }
        
        $data = [
            'title' => 'Calendario Pickup & Delivery',
            'eventi' => json_encode($eventi)
        ];
        
        return view('pickup_delivery/calendario', $data);
    }
    
    /**
     * Mostra il form per creare una nuova operazione
     */
    public function new()
    {
        $data = [
            'title' => 'Nuova Operazione Pickup & Delivery',
            'anagrafiche' => $this->anagraficaModel->getActiveAnagrafiche(),
            'attivita' => $this->attivitaModel->getActiveAttivita(),
            'utenti' => $this->utentiModel->getActiveUtenti()
        ];
        
        return view('pickup_delivery/form', $data);
    }
    
    /**
     * Processa la creazione di una nuova operazione
     */
    public function create()
    {
        if (!$this->validate($this->pickupDeliveryModel->validationRules, $this->pickupDeliveryModel->validationMessages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = $this->request->getPost();
        
        // Imposta l'utente creatore
        $data['id_utente_creatore'] = session('utente_id');
        
        // Gestione foreign key opzionali - converte valori vuoti in NULL
        $optionalForeignKeys = ['id_contatto', 'id_attivita', 'id_utente_assegnato'];
        foreach ($optionalForeignKeys as $field) {
            if (isset($data[$field]) && (empty($data[$field]) || $data[$field] === '')) {
                $data[$field] = null;
            }
        }
        
        // Gestione campi numerici opzionali - imposta NULL se vuoti
        $optionalNumericFields = ['costo_stimato', 'costo_effettivo'];
        foreach ($optionalNumericFields as $field) {
            if (isset($data[$field]) && (empty($data[$field]) || $data[$field] === '')) {
                $data[$field] = null;
            }
        }
        
        // Gestione checkbox
        $data['richiesta_ddt'] = $this->request->getPost('richiesta_ddt') ? 1 : 0;
        
        // Valori di default
        if (empty($data['priorita'])) {
            $data['priorita'] = 'normale';
        }
        if (empty($data['stato'])) {
            $data['stato'] = 'programmata';
        }
        if (empty($data['nazione'])) {
            $data['nazione'] = 'Italia';
        }
        
        $this->pickupDeliveryModel->insert($data);
        
        return redirect()->to('/pickup-delivery')->with('message', 'Operazione creata con successo');
    }
    
    /**
     * Mostra i dettagli di una operazione
     */
    public function show($id = null)
    {
        $operazione = $this->pickupDeliveryModel->find($id);
        
        if (!$operazione) {
            return redirect()->to('/pickup-delivery')->with('error', 'Operazione non trovata');
        }
        
        // Recupera i dati correlati
        $anagrafica = $this->anagraficaModel->find($operazione['id_anagrafica']);
        $contatto = $operazione['id_contatto'] ? $this->contattoModel->find($operazione['id_contatto']) : null;
        $attivita = $operazione['id_attivita'] ? $this->attivitaModel->find($operazione['id_attivita']) : null;
        $utenteAssegnato = $operazione['id_utente_assegnato'] ? $this->utentiModel->find($operazione['id_utente_assegnato']) : null;
        $utenteCreatore = $this->utentiModel->find($operazione['id_utente_creatore']);
        
        $data = [
            'title' => 'Dettaglio Operazione',
            'operazione' => $operazione,
            'anagrafica' => $anagrafica,
            'contatto' => $contatto,
            'attivita' => $attivita,
            'utente_assegnato' => $utenteAssegnato,
            'utente_creatore' => $utenteCreatore
        ];
        
        return view('pickup_delivery/show', $data);
    }
    
    /**
     * Mostra il form per modificare una operazione
     */
    public function edit($id = null)
    {
        $operazione = $this->pickupDeliveryModel->find($id);
        
        if (!$operazione) {
            return redirect()->to('/pickup-delivery')->with('error', 'Operazione non trovata');
        }
        
        $data = [
            'title' => 'Modifica Operazione',
            'operazione' => $operazione,
            'anagrafiche' => $this->anagraficaModel->getActiveAnagrafiche(),
            'attivita' => $this->attivitaModel->getActiveAttivita(),
            'utenti' => $this->utentiModel->getActiveUtenti()
        ];
        
        return view('pickup_delivery/form', $data);
    }
    
    /**
     * Processa l'aggiornamento di una operazione
     */
    public function update($id = null)
    {
        $operazione = $this->pickupDeliveryModel->find($id);
        
        if (!$operazione) {
            return redirect()->to('/pickup-delivery')->with('error', 'Operazione non trovata');
        }
        
        // Regole di validazione per l'aggiornamento (senza id_utente_creatore)
        $updateValidationRules = $this->pickupDeliveryModel->validationRules;
        unset($updateValidationRules['id_utente_creatore']);
        
        if (!$this->validate($updateValidationRules, $this->pickupDeliveryModel->validationMessages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = $this->request->getPost();
        
        // Rimuovi id_utente_creatore dai dati se presente (non deve essere modificato)
        unset($data['id_utente_creatore']);
        
        // Gestione foreign key opzionali - converte valori vuoti in NULL
        $optionalForeignKeys = ['id_contatto', 'id_attivita', 'id_utente_assegnato'];
        foreach ($optionalForeignKeys as $field) {
            if (isset($data[$field]) && (empty($data[$field]) || $data[$field] === '')) {
                $data[$field] = null;
            }
        }
        
        // Gestione campi numerici opzionali - imposta NULL se vuoti
        $optionalNumericFields = ['costo_stimato', 'costo_effettivo'];
        foreach ($optionalNumericFields as $field) {
            if (isset($data[$field]) && (empty($data[$field]) || $data[$field] === '')) {
                $data[$field] = null;
            }
        }
        
        // Gestione checkbox
        $data['richiesta_ddt'] = $this->request->getPost('richiesta_ddt') ? 1 : 0;
        
        // Assicurati che il campo stato sia presente
        if (!isset($data['stato']) || empty($data['stato'])) {
            $data['stato'] = $operazione['stato']; // Mantieni lo stato attuale se non specificato
        }
        
        // Se lo stato viene cambiato a "completata", imposta la data di completamento
        if ($data['stato'] === 'completata' && $operazione['stato'] !== 'completata') {
            $data['data_completata'] = date('Y-m-d H:i:s');
        } elseif ($data['stato'] !== 'completata') {
            $data['data_completata'] = null;
        }
        
        $this->pickupDeliveryModel->update($id, $data);
        
        return redirect()->to('/pickup-delivery')->with('message', 'Operazione aggiornata con successo');
    }
    
    /**
     * Elimina una operazione (soft delete)
     */
    public function delete($id = null)
    {
        $operazione = $this->pickupDeliveryModel->find($id);
        
        if (!$operazione) {
            return redirect()->to('/pickup-delivery')->with('error', 'Operazione non trovata');
        }
        
        $this->pickupDeliveryModel->delete($id);
        
        return redirect()->to('/pickup-delivery')->with('message', 'Operazione eliminata con successo');
    }
    
    /**
     * Cambia lo stato di una operazione via AJAX
     */
    public function cambiaStato($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }
        
        $operazione = $this->pickupDeliveryModel->find($id);
        
        if (!$operazione) {
            return $this->response->setJSON(['success' => false, 'message' => 'Operazione non trovata']);
        }
        
        $nuovoStato = $this->request->getPost('stato');
        
        if (!in_array($nuovoStato, ['programmata', 'in_corso', 'completata', 'annullata'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Stato non valido']);
        }
        
        $data = ['stato' => $nuovoStato];
        
        // Se lo stato viene cambiato a "completata", imposta la data di completamento
        if ($nuovoStato === 'completata' && $operazione['stato'] !== 'completata') {
            $data['data_completata'] = date('Y-m-d H:i:s');
        } elseif ($nuovoStato !== 'completata') {
            $data['data_completata'] = null;
        }
        
        $this->pickupDeliveryModel->update($id, $data);
        
        return $this->response->setJSON(['success' => true, 'message' => 'Stato aggiornato con successo']);
    }
    
    /**
     * Restituisce i contatti di una anagrafica in formato JSON
     */
    public function getContatti($idAnagrafica = null)
    {
        if (!$idAnagrafica) {
            return $this->response->setJSON([]);
        }
        
        try {
            $contatti = $this->anagraficaContattoModel->getContattiByAnagrafica((int)$idAnagrafica);
            return $this->response->setJSON($contatti);
        } catch (\Exception $e) {
            log_message('error', 'Errore nel recuperare i contatti per anagrafica ' . $idAnagrafica . ': ' . $e->getMessage());
            return $this->response->setJSON([]);
        }
    }
    
    /**
     * Aggiorna la data di un'operazione via AJAX (per drag & drop calendario)
     */
    public function updateDate($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }
        
        $operazione = $this->pickupDeliveryModel->find($id);
        
        if (!$operazione) {
            return $this->response->setJSON(['success' => false, 'message' => 'Operazione non trovata']);
        }
        
        $nuovaData = $this->request->getPost('data_programmata');
        
        if (!$nuovaData) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data non valida']);
        }
        
        // Validazione formato data
        if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $nuovaData)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Formato data non valido']);
        }
        
        try {
            $data = ['data_programmata' => $nuovaData];
            $this->pickupDeliveryModel->update($id, $data);
            
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Data aggiornata con successo',
                'new_date' => $nuovaData
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Errore nell\'aggiornare la data per operazione ' . $id . ': ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Errore durante l\'aggiornamento']);
        }
    }
    
    /**
     * Stampa promemoria per fattorino (vista di stampa)
     */
    public function stampa($id = null)
    {
        $operazione = $this->pickupDeliveryModel->find($id);
        
        if (!$operazione) {
            return redirect()->to('/pickup-delivery')->with('error', 'Operazione non trovata');
        }
        
        // Recupera i dati correlati
        $anagrafica = $this->anagraficaModel->find($operazione['id_anagrafica']);
        $contatto = $operazione['id_contatto'] ? $this->contattoModel->find($operazione['id_contatto']) : null;
        $attivita = $operazione['id_attivita'] ? $this->attivitaModel->find($operazione['id_attivita']) : null;
        $utenteAssegnato = $operazione['id_utente_assegnato'] ? $this->utentiModel->find($operazione['id_utente_assegnato']) : null;
        $utenteCreatore = $this->utentiModel->find($operazione['id_utente_creatore']);
        
        $data = [
            'title' => 'Promemoria Fattorino',
            'operazione' => $operazione,
            'anagrafica' => $anagrafica,
            'contatto' => $contatto,
            'attivita' => $attivita,
            'utente_assegnato' => $utenteAssegnato,
            'utente_creatore' => $utenteCreatore
        ];
        
        return view('pickup_delivery/stampa', $data);
    }
    
    /**
     * Stampa lista operazioni per data (vista di stampa)
     */
    public function stampaLista()
    {
        $dataInizio = $this->request->getGet('data_inizio');
        $dataFine = $this->request->getGet('data_fine');
        $tipo = $this->request->getGet('tipo');
        $stato = $this->request->getGet('stato');
        $utente = $this->request->getGet('utente');
        
        // Se non specificata, usa la data di oggi
        if (!$dataInizio) {
            $dataInizio = date('Y-m-d');
        }
        if (!$dataFine) {
            $dataFine = $dataInizio;
        }
        
        // Costruisci la query con filtri
        $builder = $this->pickupDeliveryModel->getAllWithRelationsBuilder();
        
        // Filtro per data
        $builder->where('DATE(pickup_delivery.data_programmata) >=', $dataInizio);
        $builder->where('DATE(pickup_delivery.data_programmata) <=', $dataFine);
        
        // Filtri opzionali
        if ($tipo && $tipo !== 'tutti') {
            $builder->where('pickup_delivery.tipo', $tipo);
        }
        
        if ($stato && $stato !== 'tutti') {
            $builder->where('pickup_delivery.stato', $stato);
        }
        
        if ($utente && $utente !== 'tutti') {
            $builder->where('pickup_delivery.id_utente_assegnato', $utente);
        }
        
        $builder->orderBy('pickup_delivery.data_programmata', 'ASC');
        $operazioni = $builder->get()->getResultArray();
        
        $data = [
            'title' => 'Lista Operazioni per Fattorino',
            'operazioni' => $operazioni,
            'data_inizio' => $dataInizio,
            'data_fine' => $dataFine,
            'filtri' => [
                'tipo' => $tipo,
                'stato' => $stato,
                'utente' => $utente
            ]
        ];
        
        return view('pickup_delivery/stampa_lista', $data);
    }
    
    /**
     * Restituisce il colore per lo stato
     */
    private function getColorByStato(string $stato): string
    {
        return match ($stato) {
            'programmata' => '#007bff',
            'in_corso' => '#ffc107',
            'completata' => '#28a745',
            'annullata' => '#dc3545',
            default => '#6c757d'
        };
    }
} 