<?php

namespace App\Models;

use CodeIgniter\Model;

class UtentiModel extends Model
{
    protected $table            = 'utenti';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'username', 'password', 'email', 'nome', 'cognome', 
        'ultimo_accesso', 'attivo', 'ruolo'
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
    protected $validationRules      = [
        'username' => 'required|min_length[3]|max_length[100]|is_unique[utenti.username,id,{id}]',
        'password' => 'required|min_length[8]',
        'email'    => 'permit_empty|valid_email|max_length[100]|is_unique[utenti.email,id,{id}]',
        'nome'     => 'permit_empty|max_length[100]',
        'cognome'  => 'permit_empty|max_length[100]',
        'ruolo'    => 'required|in_list[admin,user]',
    ];
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
     * Tenta di autenticare un utente con le credenziali fornite
     * 
     * @param string $username
     * @param string $password
     * @return array|null Array con i dati dell'utente se l'autenticazione ha successo, null altrimenti
     */
    public function tentativoLogin(string $username, string $password): ?array
    {
        $utente = $this->where('username', $username)
                      ->where('attivo', 1)
                      ->first();
        
        if (is_null($utente)) {
            return null;
        }
        
        if (!password_verify($password, $utente['password'])) {
            return null;
        }
        
        // Aggiorna l'ultimo accesso
        $this->update($utente['id'], [
            'ultimo_accesso' => date('Y-m-d H:i:s')
        ]);
        
        // Rimuove la password dall'array dell'utente per sicurezza
        unset($utente['password']);
        
        return $utente;
    }
    
    /**
     * Verifica se l'utente corrente è autenticato nella sessione
     * 
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        $session = session();
        return $session->has('utente_id');
    }
    
    /**
     * Crea un nuovo utente con password criptata
     * 
     * @param array $data
     * @return int|bool
     */
    public function createUtente(array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        return $this->insert($data);
    }
    
    /**
     * Aggiorna i dati dell'utente, criptando la password se presente
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateUtente(int $id, array $data)
    {
        try {
            // Controlliamo che l'utente esista
            $utente = $this->find($id);
            if (empty($utente)) {
                log_message('error', "Errore updateUtente: utente con ID {$id} non trovato.");
                return false;
            }
            
            // Gestiamo la password
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            } else {
                unset($data['password']);
            }
            
            // Accesso diretto al database per bypassare validazione e protezione campi
            $db = \Config\Database::connect();
            $builder = $db->table($this->table);
            
            // Assicurati che l'utente non sia stato eliminato (soft delete)
            $builder->where('id', $id)
                   ->where('deleted_at', null);
            
            // Aggiungi timestamp di aggiornamento
            $data[$this->updatedField] = date('Y-m-d H:i:s');
            
            // Esegui l'aggiornamento
            $result = $builder->update($data);
            
            if (!$result) {
                log_message('error', "Errore updateUtente per ID {$id}: " . $db->error()['message']);
            }
            
            return $result;
        } catch (\Exception $e) {
            log_message('error', "Eccezione in updateUtente: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica se l'utente è un amministratore
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        $session = session();
        
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        $idUtente = $session->get('utente_id');
        $utente = $this->find($idUtente);
        
        if (empty($utente)) {
            return false;
        }
        
        return $utente['ruolo'] === 'admin';
    }
}
