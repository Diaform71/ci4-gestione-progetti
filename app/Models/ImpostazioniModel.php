<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

final class ImpostazioniModel extends Model
{
    protected $table            = 'impostazioni';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'chiave', 
        'valore', 
        'id_utente', 
        'tipo', 
        'descrizione',
        'gruppo'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'chiave'       => 'required|alpha_dash|max_length[50]',
        'valore'       => 'required|max_length[255]',
        'tipo'         => 'required|in_list[stringa,intero,decimale,booleano,data,datetime,json]',
        'gruppo'       => 'required|max_length[50]'
    ];
    
    protected $validationMessages = [
        'chiave' => [
            'required' => 'La chiave dell\'impostazione è obbligatoria',
            'alpha_dash' => 'La chiave può contenere solo caratteri alfanumerici, trattini bassi e trattini',
            'max_length' => 'La chiave non può superare {param} caratteri'
        ],
        'valore' => [
            'required' => 'Il valore dell\'impostazione è obbligatorio',
            'max_length' => 'Il valore non può superare {param} caratteri'
        ],
        'tipo' => [
            'required' => 'Il tipo di dato è obbligatorio',
            'in_list' => 'Il tipo deve essere uno tra: stringa, intero, decimale, booleano, data, datetime, json'
        ],
        'gruppo' => [
            'required' => 'Il gruppo è obbligatorio',
            'max_length' => 'Il gruppo non può superare {param} caratteri'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Ottiene il valore di un'impostazione di sistema
     *
     * @param string $chiave Chiave dell'impostazione
     * @param mixed $default Valore predefinito se l'impostazione non esiste
     * @return mixed Valore dell'impostazione o valore predefinito
     */
    public function getImpSistema(string $chiave, $default = null)
    {
        $impostazione = $this->where('chiave', $chiave)
                              ->where('id_utente IS NULL')
                              ->first();
        
        if (empty($impostazione)) {
            return $default;
        }
        
        return $this->convertiValore($impostazione);
    }
    
    /**
     * Ottiene il valore di un'impostazione utente
     *
     * @param string $chiave Chiave dell'impostazione
     * @param int $idUtente ID dell'utente
     * @param mixed $default Valore predefinito se l'impostazione non esiste
     * @return mixed Valore dell'impostazione, o impostazione di sistema, o valore predefinito
     */
    public function getImpUtente(string $chiave, int $idUtente, $default = null)
    {
        // Prima cerca nelle impostazioni utente
        $impostazione = $this->where('chiave', $chiave)
                              ->where('id_utente', $idUtente)
                              ->first();
        
        if (!empty($impostazione)) {
            return $this->convertiValore($impostazione);
        }
        
        // Se non trovata, cerca nelle impostazioni di sistema
        return $this->getImpSistema($chiave, $default);
    }
    
    /**
     * Imposta o aggiorna un'impostazione di sistema
     *
     * @param string $chiave Chiave dell'impostazione
     * @param mixed $valore Valore dell'impostazione
     * @param string $tipo Tipo di dato (stringa, intero, decimale, booleano, data, datetime, json)
     * @param string $descrizione Descrizione dell'impostazione
     * @param string $gruppo Gruppo di appartenenza dell'impostazione
     * @return bool Esito dell'operazione
     */
    public function setImpSistema(string $chiave, $valore, string $tipo = 'stringa', string $descrizione = '', string $gruppo = 'sistema'): bool
    {
        // Converte il valore in stringa per il salvataggio
        $valore = $this->convertiValoreString($valore, $tipo);
        
        // Cerca se l'impostazione esiste già
        $esistente = $this->where('chiave', $chiave)
                           ->where('id_utente IS NULL')
                           ->first();
        
        $data = [
            'chiave' => $chiave,
            'valore' => $valore,
            'tipo' => $tipo,
            'descrizione' => $descrizione,
            'gruppo' => $gruppo
        ];
        
        if ($esistente) {
            return $this->update($esistente['id'], $data);
        } else {
            return (bool)$this->insert($data);
        }
    }
    
    /**
     * Imposta o aggiorna un'impostazione utente
     *
     * @param string $chiave Chiave dell'impostazione
     * @param mixed $valore Valore dell'impostazione
     * @param int $idUtente ID dell'utente
     * @param string $tipo Tipo di dato (stringa, intero, decimale, booleano, data, datetime, json)
     * @param string $descrizione Descrizione dell'impostazione
     * @param string $gruppo Gruppo di appartenenza dell'impostazione
     * @return bool Esito dell'operazione
     */
    public function setImpUtente(string $chiave, $valore, int $idUtente, string $tipo = 'stringa', string $descrizione = '', string $gruppo = 'utente'): bool
    {
        // Converte il valore in stringa per il salvataggio
        $valore = $this->convertiValoreString($valore, $tipo);
        
        // Cerca se l'impostazione esiste già
        $esistente = $this->where('chiave', $chiave)
                           ->where('id_utente', $idUtente)
                           ->first();
        
        $data = [
            'chiave' => $chiave,
            'valore' => $valore,
            'id_utente' => $idUtente,
            'tipo' => $tipo,
            'descrizione' => $descrizione,
            'gruppo' => $gruppo
        ];
        
        if ($esistente) {
            return $this->update($esistente['id'], $data);
        } else {
            return (bool)$this->insert($data);
        }
    }
    
    /**
     * Elimina un'impostazione di sistema
     *
     * @param string $chiave Chiave dell'impostazione
     * @return bool Esito dell'operazione
     */
    public function deleteImpSistema(string $chiave): bool
    {
        $impostazione = $this->where('chiave', $chiave)
                              ->where('id_utente IS NULL')
                              ->first();
        
        if ($impostazione) {
            return (bool)$this->delete($impostazione['id']);
        }
        
        return false;
    }
    
    /**
     * Elimina un'impostazione utente
     *
     * @param string $chiave Chiave dell'impostazione
     * @param int $idUtente ID dell'utente
     * @return bool Esito dell'operazione
     */
    public function deleteImpUtente(string $chiave, int $idUtente): bool
    {
        $impostazione = $this->where('chiave', $chiave)
                              ->where('id_utente', $idUtente)
                              ->first();
        
        if ($impostazione) {
            return (bool)$this->delete($impostazione['id']);
        }
        
        return false;
    }
    
    /**
     * Ottiene tutte le impostazioni di sistema raggruppate per gruppo
     *
     * @return array Impostazioni di sistema raggruppate
     */
    public function getImpostazioniSistemaByGruppo(): array
    {
        $impostazioni = $this->where('id_utente IS NULL')
                              ->orderBy('gruppo', 'ASC')
                              ->orderBy('chiave', 'ASC')
                              ->findAll();
        
        $result = [];
        foreach ($impostazioni as $impostazione) {
            $gruppo = $impostazione['gruppo'];
            if (!isset($result[$gruppo])) {
                $result[$gruppo] = [];
            }
            
            $result[$gruppo][] = [
                'id' => $impostazione['id'],
                'chiave' => $impostazione['chiave'],
                'valore' => $this->convertiValore($impostazione),
                'valore_raw' => $impostazione['valore'],
                'tipo' => $impostazione['tipo'],
                'descrizione' => $impostazione['descrizione'],
                'gruppo' => $impostazione['gruppo']
            ];
        }
        
        return $result;
    }
    
    /**
     * Ottiene tutte le impostazioni utente raggruppate per gruppo
     *
     * @param int $idUtente ID dell'utente
     * @return array Impostazioni utente raggruppate
     */
    public function getImpostazioniUtenteByGruppo(int $idUtente): array
    {
        // Prima otteniamo tutte le impostazioni di sistema
        $impostazioniSistema = $this->where('id_utente IS NULL')
                                   ->orderBy('gruppo', 'ASC')
                                   ->orderBy('chiave', 'ASC')
                                   ->findAll();
        
        // Poi otteniamo le impostazioni personalizzate dall'utente
        $impostazioniUtente = $this->where('id_utente', $idUtente)
                                  ->findAll();
        
        // Organizziamo le impostazioni utente per chiave per un facile accesso
        $impostazioniUtentePerChiave = [];
        foreach ($impostazioniUtente as $impostazione) {
            $impostazioniUtentePerChiave[$impostazione['chiave']] = $impostazione;
        }
        
        $result = [];
        
        // Processiamo tutte le impostazioni di sistema
        foreach ($impostazioniSistema as $impostazione) {
            $gruppo = $impostazione['gruppo'];
            $chiave = $impostazione['chiave'];
            
            if (!isset($result[$gruppo])) {
                $result[$gruppo] = [];
            }
            
            // Verifica se l'utente ha personalizzato questa impostazione
            if (isset($impostazioniUtentePerChiave[$chiave])) {
                // Usa l'impostazione personalizzata dell'utente
                $impUtente = $impostazioniUtentePerChiave[$chiave];
                $result[$gruppo][] = [
                    'id' => $impUtente['id'],
                    'chiave' => $impUtente['chiave'],
                    'valore' => $this->convertiValore($impUtente),
                    'valore_raw' => $impUtente['valore'],
                    'tipo' => $impUtente['tipo'],
                    'descrizione' => $impUtente['descrizione'],
                    'gruppo' => $impUtente['gruppo'],
                    'personalizzata' => true
                ];
            } else {
                // Usa l'impostazione di sistema come predefinita
                $result[$gruppo][] = [
                    'chiave' => $impostazione['chiave'],
                    'valore' => $this->convertiValore($impostazione),
                    'valore_raw' => $impostazione['valore'],
                    'tipo' => $impostazione['tipo'],
                    'descrizione' => $impostazione['descrizione'],
                    'gruppo' => $impostazione['gruppo'],
                    'personalizzata' => false
                ];
            }
        }
        
        return $result;
    }
    
    /**
     * Converte il valore dell'impostazione nel suo tipo originale
     *
     * @param array $impostazione Impostazione da convertire
     * @return mixed Valore convertito nel tipo appropriato
     */
    private function convertiValore(array $impostazione)
    {
        $valore = $impostazione['valore'];
        
        switch ($impostazione['tipo']) {
            case 'intero':
                return (int)$valore;
            
            case 'decimale':
                return (float)$valore;
            
            case 'booleano':
                return filter_var($valore, FILTER_VALIDATE_BOOLEAN);
            
            case 'data':
                return date('Y-m-d', strtotime($valore));
            
            case 'datetime':
                return date('Y-m-d H:i:s', strtotime($valore));
            
            case 'json':
                return json_decode($valore, true);
            
            case 'stringa':
            default:
                return $valore;
        }
    }
    
    /**
     * Converte un valore nel formato stringa per il salvataggio
     *
     * @param mixed $valore Valore da convertire
     * @param string $tipo Tipo di dato
     * @return string Valore convertito in stringa
     */
    private function convertiValoreString($valore, string $tipo): string
    {
        switch ($tipo) {
            case 'booleano':
                return $valore ? '1' : '0';
            
            case 'json':
                return json_encode($valore);
            
            case 'data':
                if ($valore instanceof \DateTime || $valore instanceof \CodeIgniter\I18n\Time) {
                    return $valore->format('Y-m-d');
                } elseif (is_string($valore)) {
                    return date('Y-m-d', strtotime($valore));
                }
                return (string)$valore;
            
            case 'datetime':
                if ($valore instanceof \DateTime || $valore instanceof \CodeIgniter\I18n\Time) {
                    return $valore->format('Y-m-d H:i:s');
                } elseif (is_string($valore)) {
                    return date('Y-m-d H:i:s', strtotime($valore));
                }
                return (string)$valore;
            
            case 'intero':
            case 'decimale':
            case 'stringa':
            default:
                return (string)$valore;
        }
    }
} 