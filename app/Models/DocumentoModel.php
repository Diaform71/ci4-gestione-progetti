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
        // Recuperiamo prima gli utenti per essere sicuri di avere i dati completi
        $utentiModel = new UtentiModel();
        
        // Query diretta più semplice che funziona anche con utenti eliminati
        $sql = "SELECT d.*, u.nome, u.cognome 
                FROM {$this->table} d 
                LEFT JOIN utenti u ON u.id = d.id_utente
                WHERE d.id_progetto = ? 
                AND d.attivo = 1 
                AND d.deleted_at IS NULL 
                ORDER BY d.created_at DESC";
        
        $documenti = $this->db->query($sql, [$id_progetto])->getResultArray();
        
        if (empty($documenti)) {
            return [];
        }
        
        // Log per debug - Quanti documenti abbiamo trovato
        log_message('debug', 'DocumentoModel::getDocumentiByProgetto - Documenti trovati: ' . count($documenti));
        
        foreach ($documenti as &$doc) {
            $id_utente = $doc['id_utente'];
            log_message('debug', "Elaborazione documento ID: {$doc['id']}, ID Utente: {$id_utente}");
            
            // Recuperiamo direttamente l'utente dal database per essere sicuri
            $utente = $utentiModel->withDeleted()->find($id_utente);
            
            if ($utente) {
                log_message('debug', "Utente trovato nel DB: {$utente['nome']} {$utente['cognome']}");
                
                // Assegnazione diretta dal risultato del database
                $doc['utente'] = [
                    'id' => $id_utente,
                    'nome' => $utente['nome'],
                    'cognome' => $utente['cognome']
                ];
            } else {
                // Fallback se l'utente non esiste nel database
                log_message('debug', "Utente non trovato nel DB. Usando valori dalla JOIN: Nome=" . 
                    (isset($doc['nome']) ? $doc['nome'] : 'NULL') . 
                    ", Cognome=" . (isset($doc['cognome']) ? $doc['cognome'] : 'NULL'));
                
                $doc['utente'] = [
                    'id' => $id_utente,
                    'nome' => !empty($doc['nome']) ? $doc['nome'] : 'Admin',
                    'cognome' => !empty($doc['cognome']) ? $doc['cognome'] : ''
                ];
            }
            
            // Rimuove i campi dell'utente dall'array principale
            unset($doc['nome']);
            unset($doc['cognome']);
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

    /**
     * Metodo di utilità per il debug delle associazioni documento-utente
     * 
     * @return array
     */
    public function debugDocumentiUtenti(): array
    {
        $sql = "SELECT d.id, d.id_progetto, d.id_utente, d.nome_originale, 
                       d.created_at, u.id as utente_id, u.username, u.nome, u.cognome, u.attivo, u.deleted_at
                FROM {$this->table} d 
                LEFT JOIN utenti u ON u.id = d.id_utente
                WHERE d.deleted_at IS NULL 
                ORDER BY d.id";
        
        $documenti = $this->db->query($sql)->getResultArray();
        
        // Aggiungiamo informazioni aggiuntive per aiutare nella diagnosi
        foreach ($documenti as &$doc) {
            $doc['utente_trovato'] = $doc['utente_id'] ? 'Sì' : 'No';
            $doc['utente_eliminato'] = isset($doc['deleted_at']) && $doc['deleted_at'] ? 'Sì' : 'No';
            $doc['utente_attivo'] = isset($doc['attivo']) && $doc['attivo'] ? 'Sì' : 'No';
            $doc['nome_visualizzato'] = ($doc['nome'] ?? 'Utente') . ' ' . ($doc['cognome'] ?? 'Sconosciuto');
        }
        
        return $documenti;
    }
} 