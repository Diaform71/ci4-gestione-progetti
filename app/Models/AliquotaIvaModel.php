<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

final class AliquotaIvaModel extends Model
{
    protected $table            = 'aliquote_iva';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'codice',
        'descrizione',
        'percentuale',
        'note'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'percentuale' => 'float',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'codice' => 'required|max_length[10]|is_unique[aliquote_iva.codice,id,{id}]',
        'descrizione' => 'required|max_length[255]',
        'percentuale' => 'required|numeric',
    ];
    
    protected $validationMessages = [
        'codice' => [
            'required' => 'Il campo Codice è obbligatorio',
            'max_length' => 'Il campo Codice non può superare {param} caratteri',
            'is_unique' => 'Questo codice è già utilizzato',
        ],
        'descrizione' => [
            'required' => 'Il campo Descrizione è obbligatorio',
            'max_length' => 'Il campo Descrizione non può superare {param} caratteri',
        ],
        'percentuale' => [
            'required' => 'Il campo Percentuale è obbligatorio',
            'numeric' => 'Il campo Percentuale deve essere un numero',
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
     * Ottiene tutte le aliquote IVA ordinate per percentuale crescente
     *
     * @return array
     */
    public function getAllOrderedByPercentuale(): array
    {
        return $this->orderBy('percentuale', 'ASC')->findAll();
    }
    
    /**
     * Ottiene un array associativo di aliquote IVA per le select
     *
     * @return array
     */
    public function getForDropdown(): array
    {
        $aliquote = $this->findAll();
        $dropdown = [];
        
        foreach ($aliquote as $aliquota) {
            $dropdown[$aliquota['id']] = $aliquota['codice'] . ' - ' . $aliquota['descrizione'] . ' (' . $aliquota['percentuale'] . '%)';
        }
        
        return $dropdown;
    }

    /**
     * Imposta le regole di validazione sostituendo il placeholder {id} con il valore corretto
     *
     * @param string|null $id ID del record in fase di aggiornamento
     * @return array Regole di validazione con ID sostituito
     */
    public function getValidationRulesWithId($id = null): array
    {
        $rules = $this->validationRules;
        
        if ($id !== null) {
            $rules['codice'] = str_replace('{id}', $id, $rules['codice']);
        }
        
        return $rules;
    }
}
