<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\I18n\Time;

final class SottoAttivitaModel extends Model
{
    protected $table            = 'sotto_attivita';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_attivita',
        'id_utente_assegnato',
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
        'id_attivita'        => 'required|integer',
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
     * Ottiene tutte le sottoattività di un'attività specifica
     *
     * @param int $idAttivita ID dell'attività padre
     * @return array
     */
    public function getSottoAttivitaByAttivita(int $idAttivita): array
    {
        return $this->db->table('sotto_attivita sa')
            ->select('sa.*, u.nome as nome_assegnato, u.cognome as cognome_assegnato')
            ->join('utenti u', 'u.id = sa.id_utente_assegnato', 'left')
            ->where('sa.id_attivita', $idAttivita)
            ->get()
            ->getResultArray();
    }
    
    /**
     * Crea una nuova sottoattività
     *
     * @param array $data Dati della sottoattività
     * @return int|false ID della sottoattività creata o false se fallisce
     */
    public function creaSottoAttivita(array $data)
    {
        // Imposta la data di creazione
        $data['data_creazione'] = Time::now()->toDateTimeString();
        
        if ($this->insert($data)) {
            return $this->getInsertID();
        }
        
        return false;
    }
    
    /**
     * Aggiorna lo stato di completamento di una sottoattività
     *
     * @param int $id ID della sottoattività
     * @param bool $completata Se la sottoattività è completata
     * @return bool
     */
    public function completaSottoAttivita(int $id, bool $completata = true): bool
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
     * Verifica se tutte le sottoattività di un'attività sono completate
     *
     * @param int $idAttivita ID dell'attività
     * @return bool
     */
    public function sonoTutteCompletate(int $idAttivita): bool
    {
        $count = $this->where('id_attivita', $idAttivita)->countAllResults();
        
        if ($count === 0) {
            return false; // Non ci sono sottoattività
        }
        
        $completate = $this->where('id_attivita', $idAttivita)
                          ->where('completata', true)
                          ->countAllResults();
                          
        return $count === $completate;
    }
    
    /**
     * Modifica lo stato di una sottoattività
     *
     * @param int $id ID della sottoattività
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
} 