<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DocumentoModel;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Services;

class Documenti extends BaseController
{
    protected $documentoModel;
    protected $session;
    protected $validator;
    
    public function __construct()
    {
        $this->documentoModel = new DocumentoModel();
        $this->session = Services::session();
        $this->validator = Services::validation();
    }
    
    /**
     * Carica un nuovo documento
     */
    public function upload(): RedirectResponse
    {
        // Verifica CSRF
        if (!$this->validate(['csrf_test_name' => 'required'])) {
            $this->session->setFlashdata('error', 'Errore di sicurezza. Riprova.');
            return redirect()->back();
        }
        
        // Validazione
        $rules = [
            'id_progetto' => 'required|numeric',
            'file' => [
                'uploaded[file]',
                'max_size[file,20480]', // 20MB
                'ext_in[file,pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip,rar,txt]',
            ],
            'descrizione' => 'permit_empty|string|max_length[500]',
        ];
        
        if (!$this->validate($rules)) {
            $this->session->setFlashdata('error', implode('<br>', $this->validator->getErrors()));
            return redirect()->back();
        }
        
        // Ottieni i dati dal form
        $id_progetto = $this->request->getPost('id_progetto');
        $descrizione = $this->request->getPost('descrizione');
        $file = $this->request->getFile('file');
        
        if (!$file->isValid() || $file->hasMoved()) {
            $this->session->setFlashdata('error', 'Errore durante il caricamento del file.');
            return redirect()->back();
        }
        
        // Debug della sessione
        $sessionData = $this->session->get();
        log_message('debug', 'Dati di sessione: ' . json_encode($sessionData));
        
        // Recupero più sicuro dell'ID utente
        $id_utente = $this->session->get('utente_id');
        
        // Se non è disponibile, tentiamo con session()
        if (empty($id_utente)) {
            $id_utente = session('utente_id');
            log_message('debug', 'Secondo tentativo per ID utente (usando session()): ' . ($id_utente ?? 'non trovato'));
        }
        
        // Prepara dati per il modello
        $data = [
            'id_progetto' => $id_progetto,
            'id_utente' => $id_utente,
            'descrizione' => $descrizione
        ];
        
        // Se l'ID utente non è presente in sessione, gestisci l'errore
        if (empty($data['id_utente'])) {
            log_message('error', 'Upload documento fallito: ID utente non trovato in sessione');
            $this->session->setFlashdata('error', 'Errore: impossibile identificare l\'utente. Effettua nuovamente il login.');
            return redirect()->back();
        }
        
        // Carica documento
        $result = $this->documentoModel->uploadDocumento($data, $file);
        
        if (!$result) {
            $this->session->setFlashdata('error', 'Errore durante il salvataggio del documento.');
            return redirect()->back();
        }
        
        $this->session->setFlashdata('success', 'Documento caricato con successo.');
        return redirect()->to("progetti/{$id_progetto}");
    }
    
    /**
     * Aggiorna le informazioni di un documento
     */
    public function update(): RedirectResponse
    {
        // Verifica CSRF
        if (!$this->validate(['csrf_test_name' => 'required'])) {
            $this->session->setFlashdata('error', 'Errore di sicurezza. Riprova.');
            return redirect()->back();
        }
        
        // Validazione
        $rules = [
            'id_documento' => 'required|numeric',
            'nome' => 'required|string|max_length[255]',
            'descrizione' => 'permit_empty|string|max_length[500]',
        ];
        
        if (!$this->validate($rules)) {
            $this->session->setFlashdata('error', implode('<br>', $this->validator->getErrors()));
            return redirect()->back();
        }
        
        // Ottieni i dati dal form
        $id_documento = $this->request->getPost('id_documento');
        $nome = $this->request->getPost('nome');
        $descrizione = $this->request->getPost('descrizione');
        
        // Ottieni il documento per il redirect successivo
        $documento = $this->documentoModel->find($id_documento);
        if (!$documento) {
            $this->session->setFlashdata('error', 'Documento non trovato.');
            return redirect()->back();
        }
        
        $id_progetto = $documento['id_progetto'];
        
        // Aggiorna documento
        $result = $this->documentoModel->updateDocumento((int)$id_documento, [
            'nome_originale' => $nome,
            'descrizione' => $descrizione
        ]);
        
        if (!$result) {
            $this->session->setFlashdata('error', 'Errore durante l\'aggiornamento del documento.');
            return redirect()->back();
        }
        
        $this->session->setFlashdata('success', 'Documento aggiornato con successo.');
        return redirect()->to("progetti/{$id_progetto}");
    }
    
    /**
     * Scarica un documento
     */
    public function download($id = null)
    {
        if (!$id) {
            $this->session->setFlashdata('error', 'ID documento non specificato.');
            return redirect()->back();
        }
        
        $documento = $this->documentoModel->find($id);
        if (!$documento) {
            $this->session->setFlashdata('error', 'Documento non trovato.');
            return redirect()->back();
        }
        
        $path = $documento['path'];
        
        if (!file_exists($path)) {
            $this->session->setFlashdata('error', 'File non trovato sul server.');
            return redirect()->back();
        }
        
        return $this->response->download($path, null)
                             ->setFileName($documento['nome_originale']);
    }
    
    /**
     * Elimina un documento
     */
    public function delete($id = null): RedirectResponse
    {
        if (!$id) {
            $this->session->setFlashdata('error', 'ID documento non specificato.');
            return redirect()->back();
        }
        
        // Ottieni il documento per il redirect successivo
        $documento = $this->documentoModel->find($id);
        if (!$documento) {
            $this->session->setFlashdata('error', 'Documento non trovato.');
            return redirect()->back();
        }
        
        $id_progetto = $documento['id_progetto'];
        
        // Elimina documento - conversione esplicita dell'ID a intero
        $result = $this->documentoModel->deleteDocumento((int)$id);
        
        if (!$result) {
            $this->session->setFlashdata('error', 'Errore durante l\'eliminazione del documento.');
            return redirect()->back();
        }
        
        $this->session->setFlashdata('success', 'Documento eliminato con successo.');
        return redirect()->to("progetti/{$id_progetto}");
    }
} 