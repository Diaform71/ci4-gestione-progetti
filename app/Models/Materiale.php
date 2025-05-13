<?php

namespace App\Models;

use CodeIgniter\Model;

class Materiale extends Model
{
    protected $table            = 'materiali';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'codice', 
        'descrizione', 
        'materiale', 
        'produttore', 
        'immagine',
        'commerciale',
        'meccanica',
        'elettrica',
        'pneumatica',
        'in_produzione'
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
    
    protected $validationMessages   = [
        'codice' => [
            'required' => 'Il campo Codice è obbligatorio',
            'is_unique' => 'Questo codice è già utilizzato da un altro materiale'
        ]
    ];
    
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setupValidationRules'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['setupValidationRules'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
    
    protected function setupValidationRules(array $data)
    {
        // Impostiamo regole di validazione dinamiche in base all'operazione
        if (isset($data['data']['id'])) {
            // Update - escludiamo l'ID corrente dalla verifica di unicità
            $this->validationRules['codice'] = 'required|min_length[1]|max_length[50]|is_unique[materiali.codice,id,' . $data['data']['id'] . ']';
        } else {
            // Insert - verifichiamo l'unicità rispetto a tutti i record
            $this->validationRules['codice'] = 'required|min_length[1]|max_length[50]|is_unique[materiali.codice]';
        }
        
        // Regole per altri campi (statiche)
        $this->validationRules['commerciale'] = 'permit_empty|in_list[0,1]';
        $this->validationRules['meccanica'] = 'permit_empty|in_list[0,1]';
        $this->validationRules['elettrica'] = 'permit_empty|in_list[0,1]';
        $this->validationRules['pneumatica'] = 'permit_empty|in_list[0,1]';
        $this->validationRules['in_produzione'] = 'permit_empty|in_list[0,1]';
        
        return $data;
    }
}
