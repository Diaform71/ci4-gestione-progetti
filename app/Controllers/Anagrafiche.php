<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AnagraficaModel;
use App\Models\AliquotaIvaModel;
use CodeIgniter\HTTP\ResponseInterface;

final class Anagrafiche extends BaseController
{
    private AnagraficaModel $anagraficaModel;
    private AliquotaIvaModel $aliquotaIvaModel;
    
    public function __construct()
    {
        $this->anagraficaModel = new AnagraficaModel();
        $this->aliquotaIvaModel = new AliquotaIvaModel();
    }
    
    /**
     * Mostra l'elenco delle anagrafiche
     */
    public function index()
    {
        $data = [
            'title' => 'Anagrafiche',
            'anagrafiche' => $this->anagraficaModel->findAll()
        ];
        
        return view('anagrafiche/index', $data);
    }
    
    /**
     * Mostra il form per creare una nuova anagrafica
     */
    public function new()
    {
        $data = [
            'title' => 'Nuova Anagrafica',
            'aliquote_iva' => $this->aliquotaIvaModel->getForDropdown()
        ];
        
        return view('anagrafiche/form', $data);
    }
    
    /**
     * Processa la creazione di una nuova anagrafica
     */
    public function create()
    {
        if (!$this->validate($this->anagraficaModel->validationRules, $this->anagraficaModel->validationMessages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = $this->request->getPost();
        
        // Gestione checkbox
        $data['fornitore'] = $this->request->getPost('fornitore') ? 1 : 0;
        $data['cliente'] = $this->request->getPost('cliente') ? 1 : 0;
        $data['attivo'] = $this->request->getPost('attivo') ? 1 : 0;
        
        // Gestione upload logo
        $logo = $this->request->getFile('logo');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $newName = $logo->getRandomName();
            $logo->move(ROOTPATH . 'public/uploads/logos', $newName);
            $data['logo'] = $newName;
        }
        
        $this->anagraficaModel->insert($data);
        
        return redirect()->to('/anagrafiche')->with('message', 'Anagrafica creata con successo');
    }
    
    /**
     * Mostra i dettagli di una anagrafica
     */
    public function show($id = null)
    {
        $anagrafica = $this->anagraficaModel->find($id);
        
        if (!$anagrafica) {
            return redirect()->to('/anagrafiche')->with('error', 'Anagrafica non trovata');
        }
        
        // Recupera l'aliquota IVA se presente
        $aliquotaIva = null;
        if (!empty($anagrafica['id_iva'])) {
            $aliquotaIva = $this->aliquotaIvaModel->find($anagrafica['id_iva']);
        }
        
        $data = [
            'title' => 'Dettaglio Anagrafica',
            'anagrafica' => $anagrafica,
            'aliquota_iva' => $aliquotaIva
        ];
        
        return view('anagrafiche/show', $data);
    }
    
    /**
     * Mostra il form per modificare una anagrafica
     */
    public function edit($id = null)
    {
        $anagrafica = $this->anagraficaModel->find($id);
        
        if (!$anagrafica) {
            return redirect()->to('/anagrafiche')->with('error', 'Anagrafica non trovata');
        }
        
        $data = [
            'title' => 'Modifica Anagrafica',
            'anagrafica' => $anagrafica,
            'aliquote_iva' => $this->aliquotaIvaModel->getForDropdown()
        ];
        
        return view('anagrafiche/form', $data);
    }
    
    /**
     * Processa l'aggiornamento di una anagrafica
     */
    public function update($id = null)
    {
        $anagrafica = $this->anagraficaModel->find($id);
        
        if (!$anagrafica) {
            return redirect()->to('/anagrafiche')->with('error', 'Anagrafica non trovata');
        }
        
        if (!$this->validate($this->anagraficaModel->validationRules, $this->anagraficaModel->validationMessages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = $this->request->getPost();
        
        // Gestione checkbox
        $data['fornitore'] = $this->request->getPost('fornitore') ? 1 : 0;
        $data['cliente'] = $this->request->getPost('cliente') ? 1 : 0;
        $data['attivo'] = $this->request->getPost('attivo') ? 1 : 0;
        
        // Gestione upload logo
        $logo = $this->request->getFile('logo');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            // Elimina il logo precedente se esiste
            if (!empty($anagrafica['logo'])) {
                $logoPath = ROOTPATH . 'public/uploads/logos/' . $anagrafica['logo'];
                if (file_exists($logoPath)) {
                    unlink($logoPath);
                }
            }
            
            $newName = $logo->getRandomName();
            $logo->move(ROOTPATH . 'public/uploads/logos', $newName);
            $data['logo'] = $newName;
        }
        
        $this->anagraficaModel->update($id, $data);
        
        return redirect()->to('/anagrafiche')->with('message', 'Anagrafica aggiornata con successo');
    }
    
    /**
     * Elimina una anagrafica (soft delete)
     */
    public function delete($id = null)
    {
        $anagrafica = $this->anagraficaModel->find($id);
        
        if (!$anagrafica) {
            return redirect()->to('/anagrafiche')->with('error', 'Anagrafica non trovata');
        }
        
        $this->anagraficaModel->delete($id);
        
        return redirect()->to('/anagrafiche')->with('message', 'Anagrafica eliminata con successo');
    }
    
    /**
     * Restituisce la lista dei fornitori in formato JSON
     */
    public function getFornitori()
    {
        $fornitori = $this->anagraficaModel->getActiveFornitori();
        
        return $this->response->setJSON($fornitori);
    }
    
    /**
     * Restituisce la lista dei clienti in formato JSON
     */
    public function getClienti()
    {
        $clienti = $this->anagraficaModel->getActiveClienti();
        
        return $this->response->setJSON($clienti);
    }
}
