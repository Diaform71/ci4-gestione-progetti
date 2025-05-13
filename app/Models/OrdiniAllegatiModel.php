<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

final class OrdiniAllegatiModel extends Model
{
    protected $table            = 'ordini_materiale_allegati';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_ordine_materiale', 'nome_file', 'file_originale', 'dimensione', 
        'tipo_mime', 'descrizione', 'data_caricamento', 'id_utente'
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
        'id_ordine_materiale' => 'required|integer',
        'nome_file'           => 'required|max_length[255]',
        'file_originale'      => 'required|max_length[255]'
    ];
    protected $validationMessages   = [
        'id_ordine_materiale' => [
            'required' => 'L\'ordine materiale è obbligatorio',
            'integer'  => 'L\'ordine materiale deve essere un valore numerico'
        ],
        'nome_file' => [
            'required' => 'Il nome del file è obbligatorio',
            'max_length' => 'Il nome del file non può superare {param} caratteri'
        ],
        'file_originale' => [
            'required' => 'Il percorso del file è obbligatorio',
            'max_length' => 'Il percorso del file non può superare {param} caratteri'
        ]
    ];
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
     * Ottiene tutti gli allegati di uno specifico ordine materiale
     */
    public function getAllegatiByOrdine(int $idOrdine)
    {
        return $this->select('ordini_materiale_allegati.*, utenti.nome as nome_utente, utenti.cognome as cognome_utente')
            ->join('utenti', 'utenti.id = ordini_materiale_allegati.id_utente', 'left')
            ->where('ordini_materiale_allegati.id_ordine_materiale', $idOrdine)
            ->orderBy('ordini_materiale_allegati.data_caricamento', 'DESC')
            ->findAll();
    }

    /**
     * Carica un file e lo salva nel sistema
     *
     * @param int $idOrdine ID dell'ordine materiale
     * @param object $file Oggetto file ($_FILES)
     * @param int $idUtente ID dell'utente che sta caricando il file
     * @param string $descrizione Descrizione del file (opzionale)
     * @return array|bool Array con i dati del file salvato o false in caso di errore
     */
    public function uploadFile(int $idOrdine, $file, int $idUtente, string $descrizione = '')
    {
        // Verifica che il file sia valido
        if (!$file->isValid() || $file->hasMoved()) {
            return false;
        }
        
        // Genera un nome univoco per il file
        $newName = $file->getRandomName();
        
        // Crea la cartella se non esiste
        $uploadPath = FCPATH . 'uploads/ordini_materiale/' . $idOrdine;
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }
        
        // Sposta il file nella cartella designata
        try {
            $file->move($uploadPath, $newName);
        } catch (\Exception $e) {
            log_message('error', 'Errore nel caricamento dell\'allegato: ' . $e->getMessage());
            return false;
        }
        
        // Prepara i dati per il salvataggio
        $data = [
            'id_ordine_materiale' => $idOrdine,
            'nome_file'            => $file->getName(),
            'file_originale'       => $newName,
            'dimensione'           => $file->getSize(),
            'tipo_mime'            => $file->getClientMimeType(),
            'descrizione'          => $descrizione,
            'data_caricamento'     => date('Y-m-d H:i:s'),
            'id_utente'            => $idUtente
        ];
        
        // Salva nel database
        if ($this->insert($data)) {
            $data['id'] = $this->insertID();
            return $data;
        }
        
        // In caso di errore, elimina il file
        unlink($uploadPath . '/' . $newName);
        return false;
    }
    
    /**
     * Elimina un allegato dal database e dal filesystem
     */
    public function deleteAllegato(int $id): bool
    {
        $allegato = $this->find($id);
        
        if (!$allegato) {
            return false;
        }
        
        // Percorso del file
        $filePath = FCPATH . 'uploads/ordini_materiale/' . $allegato['id_ordine_materiale'] . '/' . $allegato['file_originale'];
        
        // Elimina il record dal database
        $result = $this->delete($id);
        
        // Se l'eliminazione dal database è riuscita, elimina anche il file
        if ($result && file_exists($filePath)) {
            unlink($filePath);
        }
        
        return $result;
    }
} 