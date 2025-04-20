<?php

namespace App\Models;

use CodeIgniter\Model;

class EmailLogModel extends Model
{
    protected $table            = 'email_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'destinatario', 
        'cc', 
        'ccn', 
        'oggetto', 
        'corpo', 
        'id_riferimento', 
        'tipo_riferimento', 
        'data_invio', 
        'stato', 
        'error_message', 
        'allegati',
        'id_utente'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Recupera le email per una specifica richiesta d'offerta
     *
     * @param int $idRichiesta ID della richiesta d'offerta
     * @return array Lista di email per la richiesta specificata
     */
    public function getByRichiesta($idRichiesta)
    {
        return $this->where('id_riferimento', $idRichiesta)
                    ->where('tipo_riferimento', 'RDO')
                    ->orderBy('data_invio', 'DESC')
                    ->findAll();
    }

    /**
     * Recupera le email per un specifico ordine
     *
     * @param int $idOrdine ID dell'ordine
     * @return array Lista di email per l'ordine specificato
     */
    public function getByOrdine(int $idOrdine): array
    {
        return $this->where('id_riferimento', $idOrdine)
                    ->where('tipo_riferimento', 'ORDINE')
                    ->orderBy('data_invio', 'DESC')
                    ->findAll();
    }

    /**
     * Recupera lo storico di tutte le email inviate
     *
     * @param int $limit Numero massimo di risultati
     * @param int $offset Offset per la paginazione
     * @return array Lista di email
     */
    public function getAll($limit = 50, $offset = 0)
    {
        return $this->orderBy('data_invio', 'DESC')
                    ->limit($limit, $offset)
                    ->findAll();
    }

    /**
     * Recupera le statistiche sulle email inviate
     *
     * @return array Statistiche sulle email
     */
    public function getStats()
    {
        $db = \Config\Database::connect();
        
        $totaleMese = $db->table('email_logs')
                         ->where('MONTH(data_invio)', date('m'))
                         ->where('YEAR(data_invio)', date('Y'))
                         ->countAllResults();
        
        $totaleOggi = $db->table('email_logs')
                         ->where('DATE(data_invio)', date('Y-m-d'))
                         ->countAllResults();
        
        $success = $db->table('email_logs')
                     ->where('stato', 'inviato')
                     ->countAllResults();
        
        $error = $db->table('email_logs')
                   ->where('stato', 'errore')
                   ->countAllResults();
        
        return [
            'totale' => $success + $error,
            'totale_mese' => $totaleMese,
            'totale_oggi' => $totaleOggi,
            'success' => $success,
            'error' => $error,
            'success_rate' => ($success + $error > 0) ? round(($success / ($success + $error)) * 100, 2) : 0
        ];
    }
}
