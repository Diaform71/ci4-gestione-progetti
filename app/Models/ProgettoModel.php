<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;
use App\Models\AnagraficaModel;
use App\Models\UtentiModel;

final class ProgettoModel extends Model
{
    protected $table            = 'progetti';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nome',
        'descrizione',
        'fase_kanban',
        'id_anagrafica',
        'data_inizio',
        'data_scadenza',
        'data_fine',
        'id_creato_da',
        'id_responsabile',
        'priorita',
        'stato',
        'budget',
        'attivo',
        'id_progetto_padre'
    ];

    // Definizione dei valori di default per i campi
    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Definizione dei campi nullable
    protected array $nullableFields = [
        'descrizione',
        // 'id_anagrafica',
        'data_inizio',
        'data_scadenza',
        'data_fine',
        'id_responsabile',
        'budget',
        'id_progetto_padre'
    ];

    protected array $casts = [
        'id_anagrafica' => '?integer',
        'id_creato_da' => 'integer',
        'id_responsabile' => '?integer',
        'budget' => '?float',
        'attivo' => 'boolean',
        'id_progetto_padre' => '?integer'
    ];
    
    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Aggiungi i campi data personalizzati
    protected $dates = [
        'data_inizio',
        'data_scadenza',
        'data_fine',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Validation
    protected $validationRules = [
        'nome' => 'required|min_length[3]|max_length[255]',
        'fase_kanban' => 'required|max_length[100]',
        'id_creato_da' => 'required|integer',
        'priorita' => 'required|in_list[bassa,media,alta,critica]',
        'stato' => 'required|in_list[in_corso,completato,sospeso,annullato]',
        'id_progetto_padre' => 'permit_empty|integer',
        'id_anagrafica' => 'required|integer',
        'id_responsabile' => 'permit_empty|integer',
        'budget' => 'permit_empty|decimal',
    ];
    
    protected $validationMessages = [
        'nome' => [
            'required' => 'Il nome del progetto è obbligatorio',
            'min_length' => 'Il nome del progetto deve contenere almeno {param} caratteri',
            'max_length' => 'Il nome del progetto non può superare {param} caratteri',
        ],
        'fase_kanban' => [
            'required' => 'La fase Kanban è obbligatoria',
        ],
        'id_creato_da' => [
            'required' => 'Il campo "Creato da" è obbligatorio',
        ],
        'id_anagrafica' => [
            'required' => 'Il cliente è obbligatorio',
        ],
    ];
    
    protected $skipValidation = false;
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
     * Ottiene tutti i progetti attivi
     *
     * @return array
     */
    public function getActiveProjects(): array
    {
        return $this->where('attivo', 1)
                    ->findAll();
    }
    
    /**
     * Ottiene tutti i progetti, inclusi quelli disattivati
     *
     * @return array
     */
    public function getAllProjects(): array
    {
        return $this->findAll();
    }
    
    /**
     * Ottiene tutti i progetti attivi di un'anagrafica
     * 
     * @param int $idAnagrafica
     * @return array
     */
    public function getProjectsByAnagrafica(int $idAnagrafica): array
    {
        return $this->where('id_anagrafica', $idAnagrafica)
                    ->where('attivo', 1)
                    ->findAll();
    }
    
    /**
     * Ottiene tutti i progetti assegnati a un responsabile
     *
     * @param int $idResponsabile
     * @return array
     */
    public function getProjectsByResponsabile(int $idResponsabile): array
    {
        return $this->where('id_responsabile', $idResponsabile)
                    ->where('attivo', 1)
                    ->findAll();
    }
    
    /**
     * Ottiene i progetti in base alla fase Kanban
     *
     * @param string $faseKanban
     * @return array
     */
    public function getProjectsByFaseKanban(string $faseKanban): array
    {
        return $this->where('fase_kanban', $faseKanban)
                    ->where('attivo', 1)
                    ->findAll();
    }
    
    /**
     * Ottiene i progetti in base allo stato
     *
     * @param string $stato in_corso|completato|sospeso|annullato
     * @return array
     */
    public function getProjectsByStato(string $stato): array
    {
        return $this->where('stato', $stato)
                    ->where('attivo', 1)
                    ->findAll();
    }
    
    /**
     * Ottiene i progetti in scadenza entro un determinato numero di giorni
     *
     * @param int $giorni
     * @return array
     */
    public function getProjectsInScadenza(int $giorni = null): array
    {
        // Se non viene fornito un valore per i giorni, utilizziamo l'impostazione di sistema
        if ($giorni === null) {
            $impostazioniModel = new \App\Models\ImpostazioniModel();
            $giorni = $impostazioniModel->getImpSistema('giorni_anticipo_notifica_scadenza', 3);
        }
        
        $dataLimite = date('Y-m-d', strtotime('+' . $giorni . ' days'));
        
        return $this->where('data_scadenza <=', $dataLimite)
                    ->where('data_scadenza >=', date('Y-m-d'))
                    ->where('stato', 'in_corso')
                    ->where('attivo', 1)
                    ->findAll();
    }
    
    /**
     * Ottiene tutti i sottoprogetti di un progetto
     *
     * @param int $idProgettoPadre
     * @return array
     */
    public function getSottoprogetti(int $idProgettoPadre): array
    {
        return $this->where('id_progetto_padre', $idProgettoPadre)
                    ->where('attivo', 1)
                    ->findAll();
    }

    /**
     * Ottiene tutti i progetti principali (senza progetto padre)
     *
     * @return array
     */
    public function getProgettiPrincipali(): array
    {
        return $this->where('id_progetto_padre IS NULL', null, false)
                    ->where('attivo', 1)
                    ->findAll();
    }

    /**
     * Verifica se un progetto ha sottoprogetti
     *
     * @param int $idProgetto
     * @return bool
     */
    public function hasSottoprogetti(int $idProgetto): bool
    {
        return $this->where('id_progetto_padre', $idProgetto)
                    ->where('attivo', 1)
                    ->countAllResults() > 0;
    }

    /**
     * Ottiene il progetto padre
     *
     * @param int $idProgetto
     * @return array|null
     */
    public function getProgettoPadre(int $idProgetto): ?array
    {
        $progetto = $this->find($idProgetto);
        if (empty($progetto) || empty($progetto['id_progetto_padre'])) {
            return null;
        }
        return $this->find($progetto['id_progetto_padre']);
    }

    /**
     * Ottiene un progetto con i dati dell'anagrafica e del responsabile
     *
     * @param int $id
     * @return array|null
     */
    public function getProgettoWithRelations(int $id): ?array
    {
        $progetto = $this->find($id);
        
        if (empty($progetto)) {
            return null;
        }
        
        // Carica anagrafica
        if (!empty($progetto['id_anagrafica'])) {
            $anagraficaModel = new AnagraficaModel();
            $progetto['anagrafica'] = $anagraficaModel->find($progetto['id_anagrafica']);
        }
        
        // Carica creatore
        if (!empty($progetto['id_creato_da'])) {
            $utentiModel = new UtentiModel();
            $progetto['creatore'] = $utentiModel->find($progetto['id_creato_da']);
            unset($progetto['creatore']['password']);
        }
        
        // Carica responsabile
        if (!empty($progetto['id_responsabile'])) {
            $utentiModel = new UtentiModel();
            $progetto['responsabile'] = $utentiModel->find($progetto['id_responsabile']);
            unset($progetto['responsabile']['password']);
        }
        
        // Aggiunge informazioni sui sottoprogetti
        $progetto['sottoprogetti'] = $this->getSottoprogetti($id);
        
        // Aggiunge informazioni sul progetto padre
        if (!empty($progetto['id_progetto_padre'])) {
            $progetto['progetto_padre'] = $this->find($progetto['id_progetto_padre']);
        }
        
        return $progetto;
    }

    /**
     * Converte una data dal formato italiano (DD/MM/YYYY) al formato database (YYYY-MM-DD)
     */
    private function convertDateFormat($date)
    {
        if (empty($date) || $date === '0000-00-00') {
            return null;
        }

        try {
            // Prima prova il formato italiano (DD/MM/YYYY)
            $dateObj = \DateTime::createFromFormat('d/m/Y', $date);
            if ($dateObj) {
                return $dateObj->format('Y-m-d');
            }

            // Poi prova il formato YYYY-MM-DD
            $dateObj = \DateTime::createFromFormat('Y-m-d', $date);
            if ($dateObj) {
                return $dateObj->format('Y-m-d');
            }

            // Ultimo tentativo con strtotime
            $timestamp = strtotime($date);
            if ($timestamp) {
                return date('Y-m-d', $timestamp);
            }
        } catch (\Exception $e) {
            log_message('error', "Errore nella conversione della data: " . $e->getMessage());
        }

        return null;
    }

    // Override del metodo update per gestire correttamente le date
    public function update($id = null, $data = null): bool
    {
        foreach (['data_inizio', 'data_scadenza', 'data_fine'] as $dateField) {
            if (isset($data[$dateField])) {
                $data[$dateField] = $this->convertDateFormat($data[$dateField]);
                log_message('debug', "Campo {$dateField}: valore originale = {$data[$dateField]}, convertito = {$data[$dateField]}");
            }
        }

        if (isset($data['stato']) && $data['stato'] === 'completato' && empty($data['data_fine'])) {
            $data['data_fine'] = date('Y-m-d');
        }

        return parent::update($id, $data);
    }

    // Override del metodo insert per gestire i campi nullable e le date
    public function insert($data = null, bool $returnID = true)
    {
        foreach (['data_inizio', 'data_scadenza', 'data_fine'] as $dateField) {
            if (isset($data[$dateField])) {
                $data[$dateField] = $this->convertDateFormat($data[$dateField]);
                log_message('debug', "Campo {$dateField}: valore originale = {$data[$dateField]}, convertito = {$data[$dateField]}");
            }
        }

        if (isset($data['stato']) && $data['stato'] === 'completato' && empty($data['data_fine'])) {
            $data['data_fine'] = date('Y-m-d');
        }

        return parent::insert($data, $returnID);
    }
} 