<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UtentiModel;
use CodeIgniter\HTTP\RedirectResponse;

class Auth extends BaseController
{
    protected $utentiModel;
    
    public function __construct()
    {
        $this->utentiModel = new UtentiModel();
    }
    
    /**
     * Mostra la pagina di login
     */
    public function index()
    {
        // Se l'utente è già autenticato, reindirizza alla home
        if ($this->utentiModel->isLoggedIn()) {
            return redirect()->to('/');
        }
        
        return view('auth/login', [
            'validation' => \Config\Services::validation()
        ]);
    }
    
    /**
     * Gestisce il processo di login
     */
    public function login()
    {
        // Valida i dati di input
        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        
        // Tenta il login
        $utente = $this->utentiModel->tentativoLogin($username, $password);
        
        if (is_null($utente)) {
            return redirect()->back()->withInput()->with('error', 'Username o password non validi');
        }
        
        // Imposta i dati di sessione
        $session = session();
        $session->set([
            'utente_id' => $utente['id'],
            'utente_username' => $utente['username'],
            'utente_nome' => $utente['nome'],
            'utente_cognome' => $utente['cognome'],
            'utente_email' => $utente['email'],
            'utente_logged_in' => true,
            'is_admin' => $utente['ruolo'] === 'admin'
        ]);
        
        return redirect()->to('/')->with('message', 'Login effettuato con successo');
    }
    
    /**
     * Gestisce il processo di logout
     */
    public function logout(): RedirectResponse
    {
        // Distrugge la sessione
        session()->destroy();
        
        return redirect()->to('/login')->with('message', 'Logout effettuato con successo');
    }
    
    /**
     * Mostra la pagina per il cambio password
     */
    public function cambioPassword()
    {
        // Verifica se l'utente è autenticato
        if (!$this->utentiModel->isLoggedIn()) {
            return redirect()->to('/login');
        }
        
        return view('auth/cambio_password', [
            'validation' => \Config\Services::validation()
        ]);
    }
    
    /**
     * Gestisce il processo di cambio password
     */
    public function aggiornaPassword(): RedirectResponse
    {
        // Verifica se l'utente è autenticato
        if (!$this->utentiModel->isLoggedIn()) {
            return redirect()->to('/login');
        }
        
        // Valida i dati di input
        $rules = [
            'password_attuale' => 'required',
            'password_nuova' => 'required|min_length[8]',
            'password_conferma' => 'required|matches[password_nuova]'
        ];
        
        $messages = [
            'password_attuale' => [
                'required' => 'La password attuale è obbligatoria'
            ],
            'password_nuova' => [
                'required' => 'La nuova password è obbligatoria',
                'min_length' => 'La nuova password deve contenere almeno 8 caratteri'
            ],
            'password_conferma' => [
                'required' => 'La conferma della password è obbligatoria',
                'matches' => 'Le password non corrispondono'
            ]
        ];
        
        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Ottieni l'utente corrente
        $utente_id = session('utente_id');
        $utente = $this->utentiModel->find($utente_id);
        
        // Verifica che la password attuale sia corretta
        if (!password_verify($this->request->getPost('password_attuale'), $utente['password'])) {
            return redirect()->back()->with('error', 'La password attuale non è corretta');
        }
        
        // Aggiorna la password
        $this->utentiModel->updateUtente($utente_id, [
            'password' => $this->request->getPost('password_nuova')
        ]);
        
        return redirect()->to('/')->with('message', 'Password aggiornata con successo');
    }
}
