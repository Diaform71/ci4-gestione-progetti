<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\UtentiModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Utenti extends BaseController
{
    protected $utentiModel;
    
    public function __construct()
    {
        $this->utentiModel = new UtentiModel();
    }
    
    /**
     * Visualizza il profilo dell'utente corrente
     */
    public function profilo()
    {
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Devi effettuare il login per accedere al tuo profilo.');
        }
        
        $userId = session('utente_id');
        $utente = $this->utentiModel->find((int)$userId);
        
        if (empty($utente)) {
            throw new PageNotFoundException('Utente non trovato');
        }
        
        $data = [
            'title' => 'Il Mio Profilo',
            'utente' => $utente
        ];
        
        return view('utenti/profilo', $data);
    }
    
    /**
     * Mostra il form per modificare il profilo
     */
    public function modificaProfilo()
    {
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Devi effettuare il login per modificare il tuo profilo.');
        }
        
        $userId = session('utente_id');
        $utente = $this->utentiModel->find((int)$userId);
        
        if (empty($utente)) {
            throw new PageNotFoundException('Utente non trovato');
        }
        
        $data = [
            'title' => 'Modifica Profilo',
            'utente' => $utente,
            'validation' => \Config\Services::validation()
        ];
        
        return view('utenti/modifica_profilo', $data);
    }
    
    /**
     * Aggiorna i dati del profilo
     */
    public function aggiornaProfilo()
    {
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Devi effettuare il login per modificare il tuo profilo.');
        }
        
        $userId = session('utente_id');
        $utente = $this->utentiModel->find((int)$userId);
        
        if (empty($utente)) {
            throw new PageNotFoundException('Utente non trovato');
        }
        
        // Validazione dei dati
        $rules = [
            'email' => 'valid_email|max_length[100]|is_unique[utenti.email,id,' . (int)$userId . ']',
            'nome' => 'permit_empty|max_length[100]',
            'cognome' => 'permit_empty|max_length[100]',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Dati da aggiornare
        $data = [
            'email' => $this->request->getPost('email'),
            'nome' => $this->request->getPost('nome'),
            'cognome' => $this->request->getPost('cognome'),
        ];
        
        // Aggiorna il profilo
        $this->utentiModel->updateUtente((int)$userId, $data);
        
        // Aggiorna i dati di sessione
        session()->set([
            'utente_nome' => $data['nome'],
            'utente_cognome' => $data['cognome'],
            'utente_email' => $data['email']
        ]);
        
        return redirect()->to('/profilo')->with('success', 'Profilo aggiornato con successo.');
    }
} 