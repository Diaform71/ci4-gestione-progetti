<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

final class AnagraficaContattoModel extends Model
{
    protected $table            = 'anagrafiche_contatti';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_anagrafica',
        'id_contatto',
        'ruolo',
        'principale',
        'note'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id_anagrafica' => 'integer',
        'id_contatto' => 'integer',
        'principale' => 'boolean',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'id_anagrafica' => 'required|numeric|is_not_unique[anagrafiche.id]',
        'id_contatto' => 'required|numeric|is_not_unique[contatti.id]',
    ];
    
    protected $validationMessages = [
        'id_anagrafica' => [
            'required' => 'L\'ID dell\'anagrafica è obbligatorio',
            'numeric' => 'L\'ID dell\'anagrafica deve essere un numero',
            'is_not_unique' => 'L\'anagrafica specificata non esiste',
        ],
        'id_contatto' => [
            'required' => 'L\'ID del contatto è obbligatorio',
            'numeric' => 'L\'ID del contatto deve essere un numero',
            'is_not_unique' => 'Il contatto specificato non esiste',
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
     * Ottiene tutti i contatti associati ad un'anagrafica con le relative informazioni
     *
     * @param int $id_anagrafica ID dell'anagrafica
     * @return array
     */
    public function getContattiByAnagrafica(int $id_anagrafica): array
    {
        $builder = $this->db->table($this->table);
        $builder->select('anagrafiche_contatti.*, contatti.id as id, contatti.nome, contatti.cognome, contatti.email, contatti.telefono, contatti.cellulare, contatti.interno, contatti.immagine, contatti.note as note_contatto, contatti.attivo');
        $builder->join('contatti', 'contatti.id = anagrafiche_contatti.id_contatto');
        $builder->where('anagrafiche_contatti.id_anagrafica', $id_anagrafica);
        $builder->orderBy('anagrafiche_contatti.principale', 'DESC');
        $builder->orderBy('contatti.cognome', 'ASC');
        $builder->orderBy('contatti.nome', 'ASC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Ottiene tutte le anagrafiche associate ad un contatto con le relative informazioni
     *
     * @param int $id_contatto ID del contatto
     * @return array
     */
    public function getAnagraficheByContatto(int $id_contatto): array
    {
        $builder = $this->db->table($this->table);
        $builder->select('anagrafiche_contatti.*, anagrafiche.ragione_sociale, anagrafiche.citta, anagrafiche.email, anagrafiche.telefono');
        $builder->join('anagrafiche', 'anagrafiche.id = anagrafiche_contatti.id_anagrafica');
        $builder->where('anagrafiche_contatti.id_contatto', $id_contatto);
        $builder->where('anagrafiche.attivo', 1);
        $builder->orderBy('anagrafiche.ragione_sociale', 'ASC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Imposta un contatto come principale per un'anagrafica
     *
     * @param int $id_anagrafica ID dell'anagrafica
     * @param int $id_contatto ID del contatto
     * @return bool
     */
    public function setPrincipale(int $id_anagrafica, int $id_contatto): bool
    {
        // Prima rimuoviamo il flag principale da tutti i contatti dell'anagrafica
        $this->db->table($this->table)
                 ->where('id_anagrafica', $id_anagrafica)
                 ->update(['principale' => 0]);
        
        // Poi impostiamo il contatto specificato come principale
        return $this->db->table($this->table)
                        ->where('id_anagrafica', $id_anagrafica)
                        ->where('id_contatto', $id_contatto)
                        ->update(['principale' => 1]);
    }
}
