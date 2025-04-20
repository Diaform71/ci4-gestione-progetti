<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ContattoModel;
use CodeIgniter\HTTP\ResponseInterface;

final class Contatti extends BaseController
{
    private ContattoModel $contattoModel;
    
    public function __construct()
    {
        $this->contattoModel = new ContattoModel();
    }
    
    /**
     * Mostra l'elenco dei contatti
     */
    public function index()
    {
        $data = [
            'title' => 'Contatti',
            'contatti' => $this->contattoModel->findAll()
        ];
        
        return view('contatti/index', $data);
    }
    
    /**
     * Mostra il form per creare un nuovo contatto
     */
    public function new()
    {
        $data = [
            'title' => 'Nuovo Contatto'
        ];
        
        return view('contatti/form', $data);
    }
    
    /**
     * Processa la creazione di un nuovo contatto
     */
    public function create()
    {
        if (!$this->validate($this->contattoModel->validationRules, $this->contattoModel->validationMessages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = $this->request->getPost();
        
        // Gestione checkbox attivo
        $data['attivo'] = $this->request->getPost('attivo') ? 1 : 0;
        
        // Gestione upload immagine
        $file = $this->request->getFile('immagine');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads/contatti', $newName);
            $data['immagine'] = $newName;
        }
        
        $this->contattoModel->insert($data);
        $id = $this->contattoModel->getInsertID();
        
        return redirect()->to("contatti/show/{$id}")
                         ->with('success', 'Contatto creato con successo');
    }
    
    /**
     * Mostra i dettagli di un contatto
     */
    public function show($id = null)
    {
        $contatto = $this->contattoModel->find($id);
        
        if (!$contatto) {
            return redirect()->to('contatti')->with('error', 'Contatto non trovato');
        }
        
        $data = [
            'title' => 'Dettaglio Contatto',
            'contatto' => $contatto
        ];
        
        return view('contatti/show', $data);
    }
    
    /**
     * Mostra il form per modificare un contatto
     */
    public function edit($id = null)
    {
        $contatto = $this->contattoModel->find($id);
        
        if (!$contatto) {
            return redirect()->to('contatti')->with('error', 'Contatto non trovato');
        }
        
        $data = [
            'title' => 'Modifica Contatto',
            'contatto' => $contatto
        ];
        
        return view('contatti/form', $data);
    }
    
    /**
     * Processa l'aggiornamento di un contatto
     */
    public function update($id = null)
    {
        $contatto = $this->contattoModel->find($id);
        
        if (!$contatto) {
            return redirect()->to('contatti')->with('error', 'Contatto non trovato');
        }
        
        if (!$this->validate($this->contattoModel->validationRules, $this->contattoModel->validationMessages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = $this->request->getPost();
        
        // Gestione checkbox attivo
        $data['attivo'] = $this->request->getPost('attivo') ? 1 : 0;
        
        // Gestione upload immagine
        $file = $this->request->getFile('immagine');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Elimina l'immagine precedente se esiste
            if (!empty($contatto['immagine']) && file_exists(ROOTPATH . 'public/uploads/contatti/' . $contatto['immagine'])) {
                unlink(ROOTPATH . 'public/uploads/contatti/' . $contatto['immagine']);
            }
            
            $newName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads/contatti', $newName);
            $data['immagine'] = $newName;
        }
        
        $this->contattoModel->update($id, $data);
        
        // Reindirizza alla lista dei contatti dopo l'aggiornamento
        return redirect()->to('contatti')
                         ->with('success', 'Contatto aggiornato con successo');
    }
    
    /**
     * Elimina un contatto
     */
    public function delete($id = null)
    {
        $contatto = $this->contattoModel->find($id);
        
        if (!$contatto) {
            return redirect()->to('contatti')->with('error', 'Contatto non trovato');
        }
        
        // Elimina l'immagine se esiste
        if (!empty($contatto['immagine']) && file_exists(ROOTPATH . 'public/uploads/contatti/' . $contatto['immagine'])) {
            unlink(ROOTPATH . 'public/uploads/contatti/' . $contatto['immagine']);
        }
        
        $this->contattoModel->delete($id);
        
        return redirect()->to('contatti')
                         ->with('success', 'Contatto eliminato con successo');
    }
    
    /**
     * Restituisce la lista dei contatti in formato JSON (API)
     */
    public function getContatti(): ResponseInterface
    {
        $contatti = $this->contattoModel->findAll();
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Elenco contatti recuperato con successo',
            'data' => $contatti
        ]);
    }
    
    /**
     * Restituisce la lista dei contatti attivi in formato JSON (API)
     */
    public function getActiveContatti(): ResponseInterface
    {
        $contatti = $this->contattoModel->getActiveContatti();
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Elenco contatti attivi recuperato con successo',
            'data' => $contatti
        ]);
    }
}
