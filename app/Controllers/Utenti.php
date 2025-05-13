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
     * Visualizza la lista degli utenti (solo admin)
     */
    public function index()
    {
        // Verifica che l'utente sia un amministratore
        if (!$this->utentiModel->isAdmin()) {
            return redirect()->to('/')->with('error', 'Non hai i permessi per accedere a questa sezione.');
        }
        
        $data = [
            'title' => 'Gestione Utenti',
            'utenti' => $this->utentiModel->findAll()
        ];
        
        return view('utenti/index', $data);
    }
    
    /**
     * Mostra il form per creare un nuovo utente (solo admin)
     */
    public function new()
    {
        // Verifica che l'utente sia un amministratore
        if (!$this->utentiModel->isAdmin()) {
            return redirect()->to('/')->with('error', 'Non hai i permessi per accedere a questa sezione.');
        }
        
        $data = [
            'title' => 'Nuovo Utente',
            'validation' => \Config\Services::validation()
        ];
        
        return view('utenti/create', $data);
    }
    
    /**
     * Processa la creazione di un nuovo utente (solo admin)
     */
    public function create()
    {
        // Verifica che l'utente sia un amministratore
        if (!$this->utentiModel->isAdmin()) {
            return redirect()->to('/')->with('error', 'Non hai i permessi per accedere a questa sezione.');
        }
        
        // Validazione dei dati
        if (!$this->validate($this->utentiModel->validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Dati da inserire
        $data = [
            'username' => $this->request->getPost('username'),
            'password' => $this->request->getPost('password'),
            'email' => $this->request->getPost('email'),
            'nome' => $this->request->getPost('nome'),
            'cognome' => $this->request->getPost('cognome'),
            'ruolo' => $this->request->getPost('ruolo'),
            'attivo' => $this->request->getPost('attivo') ? 1 : 0
        ];
        
        // Crea il nuovo utente
        if ($this->utentiModel->createUtente($data)) {
            return redirect()->to('/utenti')->with('success', 'Utente creato con successo.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Errore durante la creazione dell\'utente.');
        }
    }
    
    /**
     * Mostra il form per modificare un utente (solo admin)
     */
    public function edit($id = null)
    {
        // Verifica che l'utente sia un amministratore
        if (!$this->utentiModel->isAdmin()) {
            return redirect()->to('/')->with('error', 'Non hai i permessi per accedere a questa sezione.');
        }
        
        $utente = $this->utentiModel->find($id);
        
        if (empty($utente)) {
            throw new PageNotFoundException('Utente non trovato');
        }
        
        $data = [
            'title' => 'Modifica Utente',
            'utente' => $utente,
            'validation' => \Config\Services::validation()
        ];
        
        return view('utenti/edit', $data);
    }
    
    /**
     * Aggiorna i dati di un utente (solo admin)
     */
    public function update($id = null)
    {
        // Verifica che l'utente sia un amministratore
        if (!$this->utentiModel->isAdmin()) {
            return redirect()->to('/')->with('error', 'Non hai i permessi per accedere a questa sezione.');
        }
        
        $utente = $this->utentiModel->find($id);
        
        if (empty($utente)) {
            throw new PageNotFoundException('Utente non trovato');
        }
        
        // Ottieni i dati dal form
        $username = $this->request->getPost('username');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $nome = $this->request->getPost('nome');
        $cognome = $this->request->getPost('cognome');
        $ruolo = $this->request->getPost('ruolo');
        $attivo = $this->request->getPost('attivo') ? 1 : 0;
        
        // Array per memorizzare gli errori
        $errors = [];
        
        // Validazione username (controllo manuale di unicità)
        if (empty($username)) {
            $errors['username'] = 'Il campo username è obbligatorio.';
        } elseif (strlen($username) < 3) {
            $errors['username'] = 'L\'username deve contenere almeno 3 caratteri.';
        } elseif (strlen($username) > 100) {
            $errors['username'] = 'L\'username non può superare i 100 caratteri.';
        } else {
            // Controllo unicità solo se l'username è cambiato
            if ($username !== $utente['username']) {
                $usernameExists = $this->utentiModel->where('username', $username)
                                                    ->where('id !=', $id)
                                                    ->countAllResults() > 0;
                if ($usernameExists) {
                    $errors['username'] = 'Questo username è già in uso.';
                }
            }
        }
        
        // Validazione email (controllo manuale di unicità)
        if (!empty($email)) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Inserisci un indirizzo email valido.';
            } elseif (strlen($email) > 100) {
                $errors['email'] = 'L\'email non può superare i 100 caratteri.';
            } else {
                // Controllo unicità solo se l'email è cambiata
                if ($email !== $utente['email']) {
                    $emailExists = $this->utentiModel->where('email', $email)
                                                    ->where('id !=', $id)
                                                    ->countAllResults() > 0;
                    if ($emailExists) {
                        $errors['email'] = 'Questo indirizzo email è già in uso.';
                    }
                }
            }
        }
        
        // Validazione password
        if (!empty($password) && strlen($password) < 8) {
            $errors['password'] = 'La password deve contenere almeno 8 caratteri.';
        }
        
        // Validazione ruolo
        if (empty($ruolo) || !in_array($ruolo, ['admin', 'user'])) {
            $errors['ruolo'] = 'Seleziona un ruolo valido.';
        }
        
        // Se ci sono errori, torna indietro con gli errori
        if (!empty($errors)) {
            log_message('debug', 'Errori di validazione utente: ' . json_encode($errors));
            return redirect()->back()->withInput()->with('errors', $errors);
        }
        
        // Tutti i controlli sono passati, prepara i dati per l'aggiornamento
        $data = [
            'username' => $username,
            'email' => $email,
            'nome' => $nome,
            'cognome' => $cognome,
            'ruolo' => $ruolo,
            'attivo' => $attivo
        ];
        
        // Aggiungi la password solo se fornita
        if (!empty($password)) {
            $data['password'] = $password;
        }
        
        // Log dei dati che verranno inseriti
        log_message('debug', 'Dati da aggiornare per utente ' . $id . ': ' . json_encode($data));
        
        // Prova ad aggiornare l'utente e gestisci il risultato
        try {
            $result = $this->utentiModel->updateUtente((int)$id, $data);
            
            if ($result) {
                return redirect()->to('/utenti')->with('success', 'Utente aggiornato con successo.');
            } else {
                log_message('error', "Fallimento nell'aggiornamento utente con ID: $id");
                return redirect()->back()->withInput()
                                ->with('error', 'Errore durante l\'aggiornamento dell\'utente. Verifica che i dati inseriti siano corretti.');
            }
        } catch (\Exception $e) {
            log_message('error', "Eccezione nell'aggiornamento utente: " . $e->getMessage());
            return redirect()->back()->withInput()
                            ->with('error', 'Si è verificato un errore durante l\'aggiornamento dell\'utente.');
        }
    }
    
    /**
     * Elimina un utente (solo admin)
     */
    public function delete($id = null)
    {
        // Verifica che l'utente sia un amministratore
        if (!$this->utentiModel->isAdmin()) {
            return redirect()->to('/')->with('error', 'Non hai i permessi per accedere a questa sezione.');
        }
        
        $utente = $this->utentiModel->find($id);
        
        if (empty($utente)) {
            throw new PageNotFoundException('Utente non trovato');
        }
        
        // Non permettere di eliminare il proprio account
        if ((int)$id === (int)session('utente_id')) {
            return redirect()->to('/utenti')->with('error', 'Non puoi eliminare il tuo account.');
        }
        
        // Eliminazione soft (usa il soft delete di CodeIgniter)
        if ($this->utentiModel->delete($id)) {
            return redirect()->to('/utenti')->with('success', 'Utente eliminato con successo.');
        } else {
            return redirect()->to('/utenti')->with('error', 'Errore durante l\'eliminazione dell\'utente.');
        }
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
            'email' => "valid_email|max_length[100]|is_unique[utenti.email,id,$userId]",
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