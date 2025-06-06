<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

final class PickupDeliveryModel extends Model
{
    protected $table            = 'pickup_delivery';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_anagrafica',
        'id_contatto',
        'id_attivita',
        'titolo',
        'tipo',
        'data_programmata',
        'data_completata',
        'indirizzo',
        'citta',
        'cap',
        'provincia',
        'nazione',
        'nome_contatto',
        'telefono_contatto',
        'email_contatto',
        'id_utente_assegnato',
        'priorita',
        'stato',
        'id_utente_creatore',
        'descrizione',
        'note',
        'richiesta_ddt',
        'numero_ddt',
        'data_ddt',
        'orario_preferito',
        'note_trasportatore',
        'costo_stimato',
        'costo_effettivo'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id_anagrafica' => 'integer',
        'id_contatto' => '?integer',
        'id_attivita' => '?integer',
        'id_utente_assegnato' => '?integer',
        'id_utente_creatore' => 'integer',
        'richiesta_ddt' => 'boolean',
        'costo_stimato' => 'float',
        'costo_effettivo' => 'float',
        'data_ddt' => '?date',
    ];
    
    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'id_anagrafica' => 'required|integer|is_not_unique[anagrafiche.id]',
        'id_contatto' => 'permit_empty|integer|is_not_unique[contatti.id]',
        'id_attivita' => 'permit_empty|integer|is_not_unique[attivita.id]',
        'id_utente_assegnato' => 'permit_empty|integer|is_not_unique[utenti.id]',
        'titolo' => 'required|min_length[3]|max_length[255]',
        'tipo' => 'required|in_list[ritiro,consegna]',
        'data_programmata' => 'required|valid_date',
        'indirizzo' => 'required|min_length[5]',
        'priorita' => 'permit_empty|in_list[bassa,normale,alta,urgente]',
        'stato' => 'permit_empty|in_list[programmata,in_corso,completata,annullata]',
        'id_utente_creatore' => 'required|integer|is_not_unique[utenti.id]',
        'email_contatto' => 'permit_empty|valid_email',
        'costo_stimato' => 'permit_empty|decimal',
        'costo_effettivo' => 'permit_empty|decimal',
    ];
    
    protected $validationMessages = [
        'id_anagrafica' => [
            'required' => 'Il campo Anagrafica è obbligatorio',
            'is_not_unique' => 'L\'anagrafica selezionata non esiste',
        ],
        'id_contatto' => [
            'is_not_unique' => 'Il contatto selezionato non esiste',
        ],
        'id_attivita' => [
            'is_not_unique' => 'L\'attività selezionata non esiste',
        ],
        'id_utente_assegnato' => [
            'is_not_unique' => 'L\'utente selezionato non esiste',
        ],
        'titolo' => [
            'required' => 'Il campo Titolo è obbligatorio',
            'min_length' => 'Il Titolo deve contenere almeno {param} caratteri',
            'max_length' => 'Il Titolo non può superare {param} caratteri',
        ],
        'tipo' => [
            'required' => 'Il campo Tipo è obbligatorio',
            'in_list' => 'Il Tipo deve essere ritiro o consegna',
        ],
        'data_programmata' => [
            'required' => 'Il campo Data Programmata è obbligatorio',
            'valid_date' => 'Inserire una data valida',
        ],
        'indirizzo' => [
            'required' => 'Il campo Indirizzo è obbligatorio',
            'min_length' => 'L\'Indirizzo deve contenere almeno {param} caratteri',
        ],
        'email_contatto' => [
            'valid_email' => 'Inserire un indirizzo email valido',
        ],
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['convertDatetimeFormat'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['convertDatetimeFormat'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
    
    /**
     * Converte il formato datetime-local in formato datetime standard
     *
     * @param array $data
     * @return array
     */
    protected function convertDatetimeFormat(array $data): array
    {
        // Controlla se esiste il campo data_programmata nei dati
        if (isset($data['data']['data_programmata']) && !empty($data['data']['data_programmata'])) {
            $datetime = $data['data']['data_programmata'];
            
            // Se il formato è datetime-local (2025-06-06T12:00), convertilo
            if (is_string($datetime) && preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $datetime)) {
                $data['data']['data_programmata'] = str_replace('T', ' ', $datetime) . ':00';
            }
        }
        
        // Gestisci anche data_completata se presente (viene impostata dal controller)
        if (isset($data['data']['data_completata']) && !empty($data['data']['data_completata'])) {
            $datetime = $data['data']['data_completata'];
            
            // Se il formato è datetime-local, convertilo
            if (is_string($datetime) && preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $datetime)) {
                $data['data']['data_completata'] = str_replace('T', ' ', $datetime) . ':00';
            }
            // Se è già nel formato corretto (YYYY-MM-DD HH:MM:SS), lascialo così
        }
        
        return $data;
    }
    
    /**
     * Ottiene tutte le operazioni con relazioni
     *
     * @return array
     */
    public function getAllWithRelations(): array
    {
        return $this->select('pickup_delivery.*, 
                             anagrafiche.ragione_sociale,
                             attivita.titolo as titolo_attivita,
                             ua.nome as nome_utente_assegnato, ua.cognome as cognome_utente_assegnato,
                             uc.nome as nome_utente_creatore, uc.cognome as cognome_utente_creatore')
                    ->join('anagrafiche', 'anagrafiche.id = pickup_delivery.id_anagrafica', 'left')
                    ->join('attivita', 'attivita.id = pickup_delivery.id_attivita', 'left')
                    ->join('utenti ua', 'ua.id = pickup_delivery.id_utente_assegnato', 'left')
                    ->join('utenti uc', 'uc.id = pickup_delivery.id_utente_creatore', 'left')
                    ->orderBy('pickup_delivery.data_programmata', 'DESC')
                    ->findAll();
    }
    
    /**
     * Ottiene il builder per query personalizzate con relazioni
     *
     * @return \CodeIgniter\Database\BaseBuilder
     */
    public function getAllWithRelationsBuilder()
    {
        return $this->select('pickup_delivery.*, 
                             anagrafiche.ragione_sociale,
                             attivita.titolo as titolo_attivita,
                             ua.nome as nome_utente_assegnato, ua.cognome as cognome_utente_assegnato,
                             uc.nome as nome_utente_creatore, uc.cognome as cognome_utente_creatore')
                    ->join('anagrafiche', 'anagrafiche.id = pickup_delivery.id_anagrafica', 'left')
                    ->join('attivita', 'attivita.id = pickup_delivery.id_attivita', 'left')
                    ->join('utenti ua', 'ua.id = pickup_delivery.id_utente_assegnato', 'left')
                    ->join('utenti uc', 'uc.id = pickup_delivery.id_utente_creatore', 'left');
    }
    
    /**
     * Ottiene le operazioni per tipo
     *
     * @param string $tipo
     * @return array
     */
    public function getByTipo(string $tipo): array
    {
        return $this->select('pickup_delivery.*, 
                             anagrafiche.ragione_sociale,
                             utenti_assegnato.nome as nome_utente_assegnato,
                             utenti_assegnato.cognome as cognome_utente_assegnato')
                    ->join('anagrafiche', 'anagrafiche.id = pickup_delivery.id_anagrafica')
                    ->join('utenti as utenti_assegnato', 'utenti_assegnato.id = pickup_delivery.id_utente_assegnato', 'left')
                    ->where('pickup_delivery.tipo', $tipo)
                    ->orderBy('pickup_delivery.data_programmata', 'ASC')
                    ->findAll();
    }
    
    /**
     * Ottiene le operazioni per stato
     *
     * @param string $stato
     * @return array
     */
    public function getByStato(string $stato): array
    {
        return $this->select('pickup_delivery.*, 
                             anagrafiche.ragione_sociale,
                             contatti.nome as nome_contatto_db,
                             contatti.telefono as telefono_contatto_db,
                             contatti.email as email_contatto_db,
                             attivita.titolo as titolo_attivita,
                             utenti_assegnato.nome as nome_utente_assegnato,
                             utenti_assegnato.cognome as cognome_utente_assegnato,
                             utenti_creatore.nome as nome_utente_creatore,
                             utenti_creatore.cognome as cognome_utente_creatore')
                    ->join('anagrafiche', 'anagrafiche.id = pickup_delivery.id_anagrafica')
                    ->join('contatti', 'contatti.id = pickup_delivery.id_contatto', 'left')
                    ->join('attivita', 'attivita.id = pickup_delivery.id_attivita', 'left')
                    ->join('utenti as utenti_assegnato', 'utenti_assegnato.id = pickup_delivery.id_utente_assegnato', 'left')
                    ->join('utenti as utenti_creatore', 'utenti_creatore.id = pickup_delivery.id_utente_creatore')
                    ->where('pickup_delivery.stato', $stato)
                    ->orderBy('pickup_delivery.data_programmata', 'ASC')
                    ->findAll();
    }
    
    /**
     * Ottiene le operazioni in scadenza (entro i prossimi giorni specificati)
     *
     * @param int $giorni
     * @return array
     */
    public function getInScadenza(int $giorni = 7): array
    {
        $dataLimite = date('Y-m-d H:i:s', strtotime("+{$giorni} days"));
        
        return $this->select('pickup_delivery.*, 
                             anagrafiche.ragione_sociale,
                             utenti_assegnato.nome as nome_utente_assegnato,
                             utenti_assegnato.cognome as cognome_utente_assegnato')
                    ->join('anagrafiche', 'anagrafiche.id = pickup_delivery.id_anagrafica')
                    ->join('utenti as utenti_assegnato', 'utenti_assegnato.id = pickup_delivery.id_utente_assegnato', 'left')
                    ->where('pickup_delivery.data_programmata <=', $dataLimite)
                    ->where('pickup_delivery.stato !=', 'completata')
                    ->where('pickup_delivery.stato !=', 'annullata')
                    ->orderBy('pickup_delivery.data_programmata', 'ASC')
                    ->findAll();
    }
    
    /**
     * Ottiene le operazioni assegnate a un utente
     *
     * @param int $idUtente
     * @return array
     */
    public function getByUtente(int $idUtente): array
    {
        return $this->select('pickup_delivery.*, 
                             anagrafiche.ragione_sociale,
                             contatti.nome as nome_contatto_db,
                             contatti.telefono as telefono_contatto_db,
                             contatti.email as email_contatto_db,
                             attivita.titolo as titolo_attivita,
                             utenti_assegnato.nome as nome_utente_assegnato,
                             utenti_assegnato.cognome as cognome_utente_assegnato,
                             utenti_creatore.nome as nome_utente_creatore,
                             utenti_creatore.cognome as cognome_utente_creatore')
                    ->join('anagrafiche', 'anagrafiche.id = pickup_delivery.id_anagrafica')
                    ->join('contatti', 'contatti.id = pickup_delivery.id_contatto', 'left')
                    ->join('attivita', 'attivita.id = pickup_delivery.id_attivita', 'left')
                    ->join('utenti as utenti_assegnato', 'utenti_assegnato.id = pickup_delivery.id_utente_assegnato', 'left')
                    ->join('utenti as utenti_creatore', 'utenti_creatore.id = pickup_delivery.id_utente_creatore')
                    ->where('pickup_delivery.id_utente_assegnato', $idUtente)
                    ->orderBy('pickup_delivery.data_programmata', 'ASC')
                    ->findAll();
    }
    
    /**
     * Ottiene statistiche per dashboard
     *
     * @return array
     */
    public function getStatistiche(): array
    {
        $stats = [];
        
        // Totale operazioni (senza soft delete)
        $stats['totale'] = $this->countAllResults();
        
        // Per stato - usa query separate per evitare accumulo di condizioni
        $stats['programmata'] = $this->where('stato', 'programmata')->countAllResults();
        $stats['in_corso'] = $this->where('stato', 'in_corso')->countAllResults();
        $stats['completata'] = $this->where('stato', 'completata')->countAllResults();
        $stats['annullata'] = $this->where('stato', 'annullata')->countAllResults();
        
        // Per tipo - usa query separate
        $stats['ritiri'] = $this->where('tipo', 'ritiro')->countAllResults();
        $stats['consegne'] = $this->where('tipo', 'consegna')->countAllResults();
        
        // In scadenza (prossimi 7 giorni) - usa query separata
        $dataLimite = date('Y-m-d H:i:s', strtotime('+7 days'));
        $stats['in_scadenza'] = $this->where('data_programmata <=', $dataLimite)
                                     ->where('stato !=', 'completata')
                                     ->where('stato !=', 'annullata')
                                     ->countAllResults();
        
        return $stats;
    }
} 