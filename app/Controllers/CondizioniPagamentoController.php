<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CondizioniPagamentoModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class CondizioniPagamentoController extends BaseController
{
    protected $condizioniModel;
    protected $validation;

    public function __construct()
    {
        $this->condizioniModel = new CondizioniPagamentoModel();
        $this->validation = \Config\Services::validation();
    }

    /**
     * Visualizza la lista delle condizioni di pagamento
     */
    public function index()
    {
        // Controllo accesso solo per admin
        if (!session()->get('is_admin')) {
            return redirect()->to(base_url())->with('error', 'Accesso non autorizzato');
        }

        $data = [
            'title' => 'Condizioni di Pagamento',
            'condizioni' => $this->condizioniModel->findAll()
        ];

        return view('condizioni_pagamento/index', $data);
    }

    /**
     * Visualizza il form per creare una nuova condizione di pagamento
     */
    public function new()
    {
        // Controllo accesso solo per admin
        if (!session()->get('is_admin')) {
            return redirect()->to(base_url())->with('error', 'Accesso non autorizzato');
        }

        $data = [
            'title' => 'Nuova Condizione di Pagamento',
            'condizione' => [
                'id' => null,
                'nome' => '',
                'nome_breadcrumb' => 'Nuova',
                'descrizione' => '',
                'giorni' => 0,
                'fine_mese' => 0,
                'attivo' => 1
            ]
        ];

        return view('condizioni_pagamento/form', $data);
    }

    /**
     * Processa il form per la creazione di una nuova condizione di pagamento
     */
    public function create()
    {
        // Controllo accesso solo per admin
        if (!session()->get('is_admin')) {
            return redirect()->to(base_url())->with('error', 'Accesso non autorizzato');
        }

        // Validazione
        $rules = [
            'nome' => [
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'Il nome è obbligatorio',
                    'min_length' => 'Il nome deve essere di almeno 3 caratteri',
                    'max_length' => 'Il nome non può superare i 100 caratteri'
                ]
            ],
            'giorni' => [
                'rules' => 'permit_empty|integer|greater_than_equal_to[0]',
                'errors' => [
                    'integer' => 'Il campo giorni deve essere un numero intero',
                    'greater_than_equal_to' => 'Il campo giorni deve essere maggiore o uguale a 0'
                ]
            ],
            'fine_mese' => [
                'rules' => 'required|in_list[0,1]',
                'errors' => [
                    'required' => 'Il campo fine mese è obbligatorio',
                    'in_list' => 'Il campo fine mese deve essere 0 o 1'
                ]
            ],
            'attivo' => [
                'rules' => 'required|in_list[0,1]',
                'errors' => [
                    'required' => 'Il campo attivo è obbligatorio',
                    'in_list' => 'Il campo attivo deve essere 0 o 1'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Verifica esplicita del nome
        $nome = trim($this->request->getPost('nome'));
        if (empty($nome)) {
            return redirect()->back()->withInput()->with('errors', ['nome' => 'Il nome è obbligatorio']);
        }

        // Salvataggio dati
        $data = [
            'nome' => $nome,
            'descrizione' => $this->request->getPost('descrizione'),
            'giorni' => (int)$this->request->getPost('giorni') ?: 0,
            'fine_mese' => (int)$this->request->getPost('fine_mese') ?: 0,
            'attivo' => (int)$this->request->getPost('attivo') ?: 0
        ];

        $this->condizioniModel->insert($data);

        return redirect()->to(base_url('condizioni-pagamento'))->with('message', 'Condizione di pagamento creata con successo');
    }

    /**
     * Visualizza il form per modificare una condizione di pagamento
     */
    public function edit($id = null)
    {
        // Controllo accesso solo per admin
        if (!session()->get('is_admin')) {
            return redirect()->to(base_url())->with('error', 'Accesso non autorizzato');
        }

        $condizione = $this->condizioniModel->find($id);

        if (empty($condizione)) {
            throw new PageNotFoundException('Condizione di pagamento non trovata');
        }

        $data = [
            'title' => 'Modifica Condizione di Pagamento',
            'condizione' => [
                'id' => $condizione['id'],
                'nome' => $condizione['nome'],
                'nome_breadcrumb' => $condizione['nome'],
                'descrizione' => $condizione['descrizione'],
                'giorni' => $condizione['giorni'],
                'fine_mese' => $condizione['fine_mese'],
                'attivo' => $condizione['attivo']
            ]
        ];

        return view('condizioni_pagamento/form', $data);
    }

    /**
     * Processa il form per l'aggiornamento di una condizione di pagamento
     */
    public function update($id = null)
    {
        // Controllo accesso solo per admin
        if (!session()->get('is_admin')) {
            return redirect()->to(base_url())->with('error', 'Accesso non autorizzato');
        }

        // Validazione
        $rules = [
            'nome' => [
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'Il nome è obbligatorio',
                    'min_length' => 'Il nome deve essere di almeno 3 caratteri',
                    'max_length' => 'Il nome non può superare i 100 caratteri'
                ]
            ],
            'giorni' => [
                'rules' => 'permit_empty|integer|greater_than_equal_to[0]',
                'errors' => [
                    'integer' => 'Il campo giorni deve essere un numero intero',
                    'greater_than_equal_to' => 'Il campo giorni deve essere maggiore o uguale a 0'
                ]
            ],
            'fine_mese' => [
                'rules' => 'required|in_list[0,1]',
                'errors' => [
                    'required' => 'Il campo fine mese è obbligatorio',
                    'in_list' => 'Il campo fine mese deve essere 0 o 1'
                ]
            ],
            'attivo' => [
                'rules' => 'required|in_list[0,1]',
                'errors' => [
                    'required' => 'Il campo attivo è obbligatorio',
                    'in_list' => 'Il campo attivo deve essere 0 o 1'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Verifica esplicita del nome
        $nome = trim($this->request->getPost('nome'));
        if (empty($nome)) {
            return redirect()->back()->withInput()->with('errors', ['nome' => 'Il nome è obbligatorio']);
        }

        // Aggiornamento dati
        $data = [
            'nome' => $nome,
            'descrizione' => $this->request->getPost('descrizione'),
            'giorni' => (int)$this->request->getPost('giorni') ?: 0,
            'fine_mese' => (int)$this->request->getPost('fine_mese') ?: 0,
            'attivo' => (int)$this->request->getPost('attivo') ?: 0
        ];

        $this->condizioniModel->update($id, $data);

        return redirect()->to(base_url('condizioni-pagamento'))->with('message', 'Condizione di pagamento aggiornata con successo');
    }

    /**
     * Elimina una condizione di pagamento (soft delete)
     */
    public function delete($id = null)
    {
        // Controllo accesso solo per admin
        if (!session()->get('is_admin')) {
            return redirect()->to(base_url())->with('error', 'Accesso non autorizzato');
        }

        $this->condizioniModel->delete($id);

        return redirect()->to(base_url('condizioni-pagamento'))->with('message', 'Condizione di pagamento eliminata con successo');
    }
} 