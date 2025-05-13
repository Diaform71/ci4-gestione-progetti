<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AliquotaIvaModel;
use CodeIgniter\HTTP\ResponseInterface;

final class AliquoteIva extends BaseController
{
    private AliquotaIvaModel $aliquotaIvaModel;
    
    public function __construct()
    {
        $this->aliquotaIvaModel = new AliquotaIvaModel();
    }
    
    /**
     * Mostra l'elenco delle aliquote IVA
     */
    public function index()
    {
        $data = [
            'title' => 'Aliquote IVA',
            'aliquote_iva' => $this->aliquotaIvaModel->getAllOrderedByPercentuale()
        ];
        
        return view('aliquote_iva/index', $data);
    }
    
    /**
     * Mostra il form per creare una nuova aliquota IVA
     */
    public function new()
    {
        $data = [
            'title' => 'Nuova Aliquota IVA'
        ];
        
        return view('aliquote_iva/form', $data);
    }
    
    /**
     * Processa la creazione di una nuova aliquota IVA
     */
    public function create()
    {
        if (!$this->validate($this->aliquotaIvaModel->validationRules, $this->aliquotaIvaModel->validationMessages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = $this->request->getPost();
        
        $this->aliquotaIvaModel->insert($data);
        
        return redirect()->to('/aliquote-iva')->with('message', 'Aliquota IVA creata con successo');
    }
    
    /**
     * Mostra i dettagli di un'aliquota IVA
     */
    public function show($id = null)
    {
        $aliquotaIva = $this->aliquotaIvaModel->find($id);
        
        if (!$aliquotaIva) {
            return redirect()->to('/aliquote-iva')->with('error', 'Aliquota IVA non trovata');
        }
        
        $data = [
            'title' => 'Dettaglio Aliquota IVA',
            'aliquota_iva' => $aliquotaIva
        ];
        
        return view('aliquote_iva/show', $data);
    }
    
    /**
     * Mostra il form per modificare un'aliquota IVA
     */
    public function edit($id = null)
    {
        $aliquotaIva = $this->aliquotaIvaModel->find($id);
        
        if (!$aliquotaIva) {
            return redirect()->to('/aliquote-iva')->with('error', 'Aliquota IVA non trovata');
        }
        
        $data = [
            'title' => 'Modifica Aliquota IVA',
            'aliquota_iva' => $aliquotaIva
        ];
        
        return view('aliquote_iva/form', $data);
    }
    
    /**
     * Processa l'aggiornamento di un'aliquota IVA
     */
    public function update($id = null)
    {
        $aliquotaIva = $this->aliquotaIvaModel->find($id);
        
        if (!$aliquotaIva) {
            return redirect()->to('/aliquote-iva')->with('error', 'Aliquota IVA non trovata');
        }
        
        // Ottieni le regole di validazione con l'ID del record attuale
        $rules = $this->aliquotaIvaModel->getValidationRulesWithId($id);
        
        if (!$this->validate($rules, $this->aliquotaIvaModel->validationMessages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = $this->request->getPost();
        
        $this->aliquotaIvaModel->update($id, $data);
        
        return redirect()->to('/aliquote-iva')->with('message', 'Aliquota IVA aggiornata con successo');
    }
    
    /**
     * Elimina un'aliquota IVA
     */
    public function delete($id = null)
    {
        $aliquotaIva = $this->aliquotaIvaModel->find($id);
        
        if (!$aliquotaIva) {
            return redirect()->to('/aliquote-iva')->with('error', 'Aliquota IVA non trovata');
        }
        
        // Prima di eliminare, verificare se è associata a delle anagrafiche
        $db = \Config\Database::connect();
        $anagraficheCount = $db->table('anagrafiche')->where('id_iva', $id)->countAllResults();
        
        if ($anagraficheCount > 0) {
            return redirect()->to('/aliquote-iva')->with('error', 'Impossibile eliminare: questa aliquota IVA è utilizzata da ' . $anagraficheCount . ' anagrafiche');
        }
        
        $this->aliquotaIvaModel->delete($id);
        
        return redirect()->to('/aliquote-iva')->with('message', 'Aliquota IVA eliminata con successo');
    }
    
    /**
     * Restituisce la lista delle aliquote IVA in formato JSON per le API
     */
    public function getAliquoteIva()
    {
        $aliquoteIva = $this->aliquotaIvaModel->findAll();
        
        return $this->response->setJSON($aliquoteIva);
    }
}
