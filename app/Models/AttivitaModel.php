<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\I18n\Time;

final class AttivitaModel extends Model
{
    protected $table            = 'attivita';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_progetto',
        'id_utente_assegnato',
        'id_utente_creatore',
        'titolo',
        'descrizione',
        'priorita',
        'stato',
        'data_scadenza',
        'data_creazione',
        'data_aggiornamento',
        'completata',
        'completata_il'
    ];

    // Validation
    protected $validationRules      = [
        'id_progetto'        => 'required|integer',
        'id_utente_assegnato' => 'required|integer',
        'id_utente_creatore'  => 'required|integer',
        'titolo'             => 'required|string|max_length[255]',
        'priorita'           => 'required|in_list[bassa,media,alta,urgente]',
        'stato'              => 'required|in_list[da_iniziare,in_corso,in_pausa,completata,annullata]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'data_creazione';
    protected $updatedField  = 'data_aggiornamento';

    /**
     * Ottiene tutte le attività con i dati correlati
     *
     * @param array $filters Filtri opzionali
     * @return array
     */
    public function getAttivitaWithRelations(array $filters = []): array
    {
        $builder = $this->db->table('attivita a')
            ->select('a.*, p.nome as nome_progetto, u1.nome as nome_assegnato, u1.cognome as cognome_assegnato, 
                    u2.nome as nome_creatore, u2.cognome as cognome_creatore')
            ->join('progetti p', 'p.id = a.id_progetto')
            ->join('utenti u1', 'u1.id = a.id_utente_assegnato', 'left')
            ->join('utenti u2', 'u2.id = a.id_utente_creatore', 'left');
            
        // Applica filtri
        if (!empty($filters['id_progetto'])) {
            $builder->where('a.id_progetto', $filters['id_progetto']);
        }
        
        if (!empty($filters['id_utente_assegnato'])) {
            $builder->where('a.id_utente_assegnato', $filters['id_utente_assegnato']);
        }
        
        if (!empty($filters['stato'])) {
            $builder->where('a.stato', $filters['stato']);
        }
        
        if (!empty($filters['completata'])) {
            $builder->where('a.completata', $filters['completata']);
        }
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Ottiene tutte le attività con i dati correlati per la vista index
     *
     * @return array
     */
    public function getAttivitaWithDetails(): array
    {
        // Ottieni tutte le attività con dati correlati
        $builder = $this->db->table('attivita a')
            ->select('a.*, p.nome as nome_progetto, u1.nome as nome_assegnato, u1.cognome as cognome_assegnato, 
                    u2.nome as nome_creatore, u2.cognome as cognome_creatore')
            ->join('progetti p', 'p.id = a.id_progetto')
            ->join('utenti u1', 'u1.id = a.id_utente_assegnato', 'left')
            ->join('utenti u2', 'u2.id = a.id_utente_creatore', 'left');

        // Controlla se l'utente è admin o utente normale
        $isAdmin = session()->get('is_admin') ?? false;
        $utenteId = session()->get('utente_id');
        
        // Se non è admin, mostra solo le attività assegnate all'utente
        if (!$isAdmin && $utenteId) {
            $builder->where('a.id_utente_assegnato', $utenteId);
        }
        
        // Ordina per priorità e stato
        $builder->orderBy('FIELD(a.priorita, "urgente", "alta", "media", "bassa")', '', false);
        $builder->orderBy('FIELD(a.stato, "in_corso", "da_iniziare", "in_pausa", "completata", "annullata")', '', false);
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Ottiene un'attività specifica con tutti i dati correlati
     *
     * @param int $id ID dell'attività
     * @return array|null
     */
    public function getAttivitaCompleta(int $id): ?array
    {
        $attivita = $this->db->table('attivita a')
            ->select('a.*, p.nome as nome_progetto, u1.nome as nome_assegnato, u1.cognome as cognome_assegnato, 
                    u2.nome as nome_creatore, u2.cognome as cognome_creatore')
            ->join('progetti p', 'p.id = a.id_progetto')
            ->join('utenti u1', 'u1.id = a.id_utente_assegnato', 'left')
            ->join('utenti u2', 'u2.id = a.id_utente_creatore', 'left')
            ->where('a.id', $id)
            ->get()
            ->getRowArray();
            
        if (empty($attivita)) {
            return null;
        }
        
        // Carica le sottoattività
        $sottoAttivitaModel = new SottoAttivitaModel();
        $attivita['sotto_attivita'] = $sottoAttivitaModel->getSottoAttivitaByAttivita($id);
        
        return $attivita;
    }
    
    /**
     * Crea una nuova attività
     *
     * @param array $data Dati dell'attività
     * @return int|false ID dell'attività creata o false se fallisce
     */
    public function creaAttivita(array $data)
    {
        // Imposta la data di creazione
        $data['data_creazione'] = Time::now()->toDateTimeString();
        
        if ($this->insert($data)) {
            return $this->getInsertID();
        }
        
        return false;
    }
    
    /**
     * Aggiorna lo stato di completamento di un'attività
     *
     * @param int $id ID dell'attività
     * @param bool $completata Se l'attività è completata
     * @return bool
     */
    public function completaAttivita(int $id, bool $completata = true): bool
    {
        $data = [
            'completata' => $completata,
            'completata_il' => $completata ? Time::now()->toDateTimeString() : null,
            'stato' => $completata ? 'completata' : 'in_corso',
            'data_aggiornamento' => Time::now()->toDateTimeString()
        ];
        
        return $this->update($id, $data);
    }
    
    /**
     * Modifica lo stato di un'attività
     *
     * @param int $id ID dell'attività
     * @param string $stato Nuovo stato
     * @return bool
     */
    public function cambiaStato(int $id, string $stato): bool
    {
        if (!in_array($stato, ['da_iniziare', 'in_corso', 'in_pausa', 'completata', 'annullata'])) {
            return false;
        }
        
        $data = [
            'stato' => $stato,
            'data_aggiornamento' => Time::now()->toDateTimeString()
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
     * Conta le attività per utente
     *
     * @return array
     */
    public function contaAttivitaPerUtente(): array
    {
        return $this->db->table('attivita a')
            ->select('u.nome, u.cognome, COUNT(a.id) as totale_attivita')
            ->join('utenti u', 'u.id = a.id_utente_assegnato')
            ->groupBy('a.id_utente_assegnato')
            ->get()
            ->getResultArray();
    }
    
    /**
     * Ottiene la lista delle attività attive
     *
     * @return array
     */
    public function getActiveAttivita(): array
    {
        return $this->select('id, titolo, descrizione, priorita, stato')
                    ->where('stato !=', 'completata')
                    ->where('stato !=', 'annullata')
                    ->orderBy('priorita', 'DESC')
                    ->orderBy('titolo', 'ASC')
                    ->findAll();
    }
} 