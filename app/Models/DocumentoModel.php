<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;
use App\Models\UtentiModel;

class DocumentoModel extends Model
{
    protected $table            = 'documenti';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_progetto', 
        'id_utente', 
        'nome_file', 
        'nome_originale', 
        'path', 
        'mime_type', 
        'dimensione', 
        'descrizione',
        'attivo'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Ottiene i documenti relativi a un progetto
     *
     * @param int $id_progetto
     * @return array
     */
    public function getDocumentiByProgetto(int $id_progetto): array
    {
        $documenti = $this->where('id_progetto', $id_progetto)
                         ->where('attivo', 1)
                         ->orderBy('created_at', 'DESC')
                         ->findAll();
        
        if (empty($documenti)) {
            return [];
        }
        
        // Caricamento dati utente
        $utentiModel = new UtentiModel();
        
        foreach ($documenti as &$doc) {
            $doc['utente'] = $utentiModel->find($doc['id_utente']) ?? [
                'nome' => 'Utente',
                'cognome' => 'Sconosciuto'
            ];
        }
        
        return $documenti;
    }
    
    /**
     * Carica un documento sul server
     *
     * @param array $data Dati del documento (id_progetto, id_utente, descrizione)
     * @param object $file File caricato
     * @return bool|int ID del documento inserito o false in caso di errore
     */
    public function uploadDocumento(array $data, object $file)
    {
        // Genera nome univoco per il file
        $newName = $file->getRandomName();
        
        // Percorso di destinazione
        $uploadPath = WRITEPATH . 'uploads/documenti/' . date('Y') . '/' . date('m');
        
        // Crea directory se non esiste
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }
        
        // Sposta il file nella directory di destinazione
        if (!$file->move($uploadPath, $newName)) {
            return false;
        }
        
        // Dati da inserire nel database
        $insertData = [
            'id_progetto' => $data['id_progetto'],
            'id_utente' => $data['id_utente'],
            'nome_file' => $newName,
            'nome_originale' => $file->getClientName(),
            'path' => $uploadPath . '/' . $newName,
            'mime_type' => $file->getClientMimeType(),
            'dimensione' => $file->getSize(),
            'descrizione' => $data['descrizione'] ?? null,
            'attivo' => 1
        ];
        
        $this->insert($insertData);
        return $this->getInsertID();
    }
    
    /**
     * Aggiorna le informazioni di un documento
     *
     * @param int $id_documento
     * @param array $data
     * @return bool
     */
    public function updateDocumento(int $id_documento, array $data): bool
    {
        return $this->update($id_documento, $data);
    }
    
    /**
     * Elimina un documento
     *
     * @param int $id_documento
     * @return bool
     */
    public function deleteDocumento(int $id_documento): bool
    {
        // Prima di eliminare il record, verifichiamo se esiste
        $documento = $this->find($id_documento);
        if (!$documento) {
            return false;
        }
        
        // Se esiste, proviamo a eliminare il file fisico
        if (file_exists($documento['path']) && is_file($documento['path'])) {
            unlink($documento['path']);
        }
        
        // Quindi eliminiamo il record
        return $this->delete($id_documento);
    }
} 