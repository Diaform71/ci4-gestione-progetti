<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\I18n\Time;
use App\Models\ProgettoModel;

final class ScadenzaModel extends Model
{
    protected $table            = 'scadenze';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'titolo',
        'descrizione',
        'data_scadenza',
        'data_promemoria',
        'id_progetto',
        'id_attivita',
        'id_utente_assegnato',
        'id_utente_creatore',
        'completata',
        'completata_il',
        'priorita',
        'stato',
        'id_ordine_materiale'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'titolo' => 'required|min_length[3]|max_length[255]',
        'data_scadenza' => 'required|valid_date',
        'id_utente_assegnato' => 'required|integer',
        'id_utente_creatore' => 'required|integer',
        'priorita' => 'required|in_list[bassa,media,alta,urgente]',
        'stato' => 'required|in_list[da_iniziare,in_corso,completata,annullata]',
    ];
    
    protected $validationMessages = [
        'titolo' => [
            'required' => 'Il titolo della scadenza è obbligatorio',
            'min_length' => 'Il titolo deve contenere almeno {param} caratteri',
            'max_length' => 'Il titolo non può superare {param} caratteri',
        ],
        'data_scadenza' => [
            'required' => 'La data di scadenza è obbligatoria',
            'valid_date' => 'La data di scadenza non è valida',
        ],
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Ottiene tutte le scadenze con relazioni
     *
     * @param array $filters Filtri opzionali
     * @return array
     */
    public function getScadenzeWithRelations(array $filters = []): array
    {
        $builder = $this->db->table('scadenze s')
            ->select('s.*, p.nome as nome_progetto, a.titolo as titolo_attivita, 
                    u1.nome as nome_assegnato, u1.cognome as cognome_assegnato, 
                    u2.nome as nome_creatore, u2.cognome as cognome_creatore')
            ->join('progetti p', 'p.id = s.id_progetto', 'left')
            ->join('attivita a', 'a.id = s.id_attivita', 'left')
            ->join('utenti u1', 'u1.id = s.id_utente_assegnato', 'left')
            ->join('utenti u2', 'u2.id = s.id_utente_creatore', 'left');
            
        // Applica filtri
        if (!empty($filters['id_progetto'])) {
            $builder->where('s.id_progetto', $filters['id_progetto']);
        }
        
        if (!empty($filters['id_attivita'])) {
            $builder->where('s.id_attivita', $filters['id_attivita']);
        }
        
        if (!empty($filters['id_utente_assegnato'])) {
            $builder->where('s.id_utente_assegnato', $filters['id_utente_assegnato']);
        }
        
        if (!empty($filters['stato'])) {
            $builder->where('s.stato', $filters['stato']);
        }
        
        if (!empty($filters['completata'])) {
            $builder->where('s.completata', $filters['completata']);
        }
        
        if (isset($filters['data_da']) && !empty($filters['data_da'])) {
            $builder->where('s.data_scadenza >=', $filters['data_da']);
        }
        
        if (isset($filters['data_a']) && !empty($filters['data_a'])) {
            $builder->where('s.data_scadenza <=', $filters['data_a']);
        }
        
        // Ordina per data scadenza e priorità
        $builder->orderBy('s.data_scadenza', 'ASC');
        $builder->orderBy('FIELD(s.priorita, "urgente", "alta", "media", "bassa")', '', false);
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Ottiene tutte le scadenze per la vista principale
     *
     * @param array $filters Filtri opzionali
     * @return array
     */
    public function getScadenzeWithDetails(array $filters = []): array
    {
        // Ottieni tutte le scadenze con dati correlati
        $builder = $this->db->table('scadenze s')
            ->select('s.*, p.nome as nome_progetto, a.titolo as titolo_attivita, 
                    u1.nome as nome_assegnato, u1.cognome as cognome_assegnato, 
                    u2.nome as nome_creatore, u2.cognome as cognome_creatore')
            ->join('progetti p', 'p.id = s.id_progetto', 'left')
            ->join('attivita a', 'a.id = s.id_attivita', 'left')
            ->join('utenti u1', 'u1.id = s.id_utente_assegnato', 'left')
            ->join('utenti u2', 'u2.id = s.id_utente_creatore', 'left');

        // Gestisci filtri per progetti, includendo i sottoprogetti
        if (!empty($filters['id_progetto'])) {
            $idProgetto = $filters['id_progetto'];
            
            // Carichiamo il modello dei progetti per ottenere i sottoprogetti
            $progettoModel = new ProgettoModel();
            $sottoprogetti = $progettoModel->getSottoprogetti((int)$idProgetto);
            
            // Se ci sono sottoprogetti, includiamo anche le loro scadenze
            if (!empty($sottoprogetti)) {
                $idsProgetti = array_column($sottoprogetti, 'id');
                // Aggiungiamo l'ID del progetto principale all'array
                $idsProgetti[] = $idProgetto;
                
                // Utilizziamo WHERE IN per includere tutte le scadenze dei progetti e sottoprogetti
                $builder->whereIn('s.id_progetto', $idsProgetti);
            } else {
                // Se non ci sono sottoprogetti, filtriamo solo per il progetto selezionato
                $builder->where('s.id_progetto', $idProgetto);
            }
        } else {
            // Gestione degli altri filtri come prima
            if (!empty($filters['id_attivita'])) {
                $builder->where('s.id_attivita', $filters['id_attivita']);
            }
            
            if (!empty($filters['id_utente_assegnato'])) {
                $builder->where('s.id_utente_assegnato', $filters['id_utente_assegnato']);
            }
            
            if (!empty($filters['stato'])) {
                $builder->where('s.stato', $filters['stato']);
            }
            
            if (!empty($filters['priorita'])) {
                $builder->where('s.priorita', $filters['priorita']);
            }
            
            if (isset($filters['completata']) && $filters['completata'] !== '') {
                $builder->where('s.completata', $filters['completata']);
            }
            
            if (isset($filters['data_da']) && !empty($filters['data_da'])) {
                $builder->where('s.data_scadenza >=', $filters['data_da']);
            }
            
            if (isset($filters['data_a']) && !empty($filters['data_a'])) {
                $builder->where('s.data_scadenza <=', $filters['data_a']);
            }
        }

        // Controlla se l'utente è admin o utente normale
        $isAdmin = session()->get('is_admin') ?? false;
        $utenteId = session()->get('utente_id');
        
        // Se non è admin, mostra solo le scadenze assegnate all'utente o create da lui
        if (!$isAdmin && $utenteId) {
            $builder->where("(s.id_utente_assegnato = $utenteId OR s.id_utente_creatore = $utenteId)");
        }
        
        // Ordina per data di scadenza (le più vicine prima), quindi per priorità
        $builder->orderBy('s.data_scadenza', 'ASC');
        $builder->orderBy('FIELD(s.priorita, "urgente", "alta", "media", "bassa")', '', false);
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Ottiene una scadenza specifica con tutti i dati correlati
     *
     * @param int $id ID della scadenza
     * @return array|null
     */
    public function getScadenzaCompleta(int $id): ?array
    {
        $scadenza = $this->db->table('scadenze s')
            ->select('s.*, p.nome as nome_progetto, a.titolo as titolo_attivita, 
                    u1.nome as nome_assegnato, u1.cognome as cognome_assegnato, 
                    u2.nome as nome_creatore, u2.cognome as cognome_creatore')
            ->join('progetti p', 'p.id = s.id_progetto', 'left')
            ->join('attivita a', 'a.id = s.id_attivita', 'left')
            ->join('utenti u1', 'u1.id = s.id_utente_assegnato', 'left')
            ->join('utenti u2', 'u2.id = s.id_utente_creatore', 'left')
            ->where('s.id', $id)
            ->get()
            ->getRowArray();
            
        return $scadenza;
    }
    
    /**
     * Ottiene le scadenze in arrivo nei prossimi X giorni
     *
     * @param int $giorni Numero di giorni da considerare
     * @param int|null $idUtente ID dell'utente (se null, considera tutti gli utenti)
     * @return array
     */
    public function getScadenzeInArrivo(int $giorni = 7, ?int $idUtente = null): array
    {
        $dataOggi = date('Y-m-d');
        $dataLimite = date('Y-m-d', strtotime("+$giorni days"));
        
        $builder = $this->db->table('scadenze s')
            ->select('s.*, p.nome as nome_progetto, a.titolo as titolo_attivita, 
                    u1.nome as nome_assegnato, u1.cognome as cognome_assegnato')
            ->join('progetti p', 'p.id = s.id_progetto', 'left')
            ->join('attivita a', 'a.id = s.id_attivita', 'left')
            ->join('utenti u1', 'u1.id = s.id_utente_assegnato', 'left')
            ->where('s.data_scadenza >=', $dataOggi)
            ->where('s.data_scadenza <=', $dataLimite)
            ->where('s.completata', 0)
            ->where('s.stato !=', 'annullata');
        
        if ($idUtente) {
            $builder->where('s.id_utente_assegnato', $idUtente);
        }
        
        return $builder->orderBy('s.data_scadenza', 'ASC')
                      ->orderBy('FIELD(s.priorita, "urgente", "alta", "media", "bassa")', '', false)
                      ->get()
                      ->getResultArray();
    }
    
    /**
     * Crea una nuova scadenza
     *
     * @param array $data Dati della scadenza
     * @return int|false ID della scadenza creata o false se fallisce
     */
    public function creaScadenza(array $data)
    {
        if ($this->insert($data)) {
            return $this->getInsertID();
        }
        
        return false;
    }
    
    /**
     * Aggiorna lo stato di completamento di una scadenza
     *
     * @param int $id ID della scadenza
     * @param bool $completata Se la scadenza è completata
     * @return bool
     */
    public function completaScadenza(int $id, bool $completata = true): bool
    {
        $data = [
            'completata' => $completata,
            'completata_il' => $completata ? Time::now()->toDateTimeString() : null,
            'stato' => $completata ? 'completata' : 'in_corso',
        ];
        
        return $this->update($id, $data);
    }
    
    /**
     * Modifica lo stato di una scadenza
     *
     * @param int $id ID della scadenza
     * @param string $stato Nuovo stato
     * @return bool
     */
    public function cambiaStato(int $id, string $stato): bool
    {
        if (!in_array($stato, ['da_iniziare', 'in_corso', 'completata', 'annullata'])) {
            return false;
        }
        
        $data = [
            'stato' => $stato
        ];
        
        // Se completata, aggiorna anche i campi di completamento
        if ($stato === 'completata') {
            $data['completata'] = true;
            $data['completata_il'] = Time::now()->toDateTimeString();
        } else if ($stato === 'annullata') {
            $data['completata'] = false;
            $data['completata_il'] = null;
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Ottiene le scadenze scadute e non completate
     * 
     * @param int|null $idUtente ID dell'utente (se null, considera tutti gli utenti)
     * @return array
     */
    public function getScadenzeScadute(?int $idUtente = null): array
    {
        $dataOggi = date('Y-m-d');
        
        $builder = $this->db->table('scadenze s')
            ->select('s.*, p.nome as nome_progetto, a.titolo as titolo_attivita, 
                    u1.nome as nome_assegnato, u1.cognome as cognome_assegnato')
            ->join('progetti p', 'p.id = s.id_progetto', 'left')
            ->join('attivita a', 'a.id = s.id_attivita', 'left')
            ->join('utenti u1', 'u1.id = s.id_utente_assegnato', 'left')
            ->where('s.data_scadenza <', $dataOggi)
            ->where('s.completata', 0)
            ->where('s.stato !=', 'annullata');
        
        if ($idUtente) {
            $builder->where('s.id_utente_assegnato', $idUtente);
        }
        
        return $builder->orderBy('s.data_scadenza', 'ASC')
                      ->get()
                      ->getResultArray();
    }
} 