<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\ImpostazioniModel;
use App\Models\UtentiModel;
use CodeIgniter\Exceptions\PageNotFoundException;

final class ImpostazioniController extends BaseController
{
    protected $impostazioniModel;
    protected $utentiModel;
    
    public function __construct()
    {
        $this->impostazioniModel = new ImpostazioniModel();
        $this->utentiModel = new UtentiModel();
    }
    
    /**
     * Visualizza le impostazioni di sistema
     */
    public function index()
    {
        $session = session();
        
        // Verifica che l'utente sia un amministratore
        if (!$this->utentiModel->isAdmin()) {
            $session->setFlashdata('error', 'Non hai i permessi per accedere alle impostazioni di sistema');
            return redirect()->to('/');
        }
        
        $data = [
            'title' => 'Impostazioni di Sistema',
            'impostazioni' => $this->impostazioniModel->getImpostazioniSistemaByGruppo()
        ];
        
        return view('impostazioni/index', $data);
    }
    
    /**
     * Visualizza le impostazioni dell'utente
     */
    public function utente()
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per accedere alle impostazioni personali');
            return redirect()->to('/login');
        }
        
        $idUtente = $session->get('utente_id');
        
        $data = [
            'title' => 'Impostazioni Personali',
            'impostazioni' => $this->impostazioniModel->getImpostazioniUtenteByGruppo((int)$idUtente)
        ];
        
        return view('impostazioni/utente', $data);
    }
    
    /**
     * Mostra il form per aggiungere una nuova impostazione di sistema
     */
    public function nuova()
    {
        $session = session();
        
        // Verifica che l'utente sia un amministratore
        if (!$this->utentiModel->isAdmin()) {
            $session->setFlashdata('error', 'Non hai i permessi per aggiungere impostazioni di sistema');
            return redirect()->to('/');
        }
        
        $data = [
            'title' => 'Aggiungi Impostazione di Sistema'
        ];
        
        return view('impostazioni/nuova', $data);
    }
    
    /**
     * Mostra il form per modificare un'impostazione di sistema
     */
    public function modifica($id = null)
    {
        $session = session();
        
        // Verifica che l'utente sia un amministratore
        if (!$this->utentiModel->isAdmin()) {
            $session->setFlashdata('error', 'Non hai i permessi per modificare impostazioni di sistema');
            return redirect()->to('/');
        }
        
        $impostazione = $this->impostazioniModel->find($id);
        
        if (empty($impostazione)) {
            throw new PageNotFoundException('Impostazione non trovata');
        }
        
        if (!empty($impostazione['id_utente'])) {
            $session->setFlashdata('error', 'Non puoi modificare un\'impostazione utente da qui');
            return redirect()->to('/impostazioni');
        }
        
        $data = [
            'title' => 'Modifica Impostazione',
            'impostazione' => $impostazione
        ];
        
        return view('impostazioni/modifica', $data);
    }
    
    /**
     * Salva una nuova impostazione di sistema
     */
    public function salva()
    {
        $session = session();
        
        // Verifica che l'utente sia un amministratore
        if (!$this->utentiModel->isAdmin()) {
            $session->setFlashdata('error', 'Non hai i permessi per salvare impostazioni di sistema');
            return redirect()->to('/');
        }
        
        $rules = [
            'chiave' => 'required|alpha_dash|max_length[50]',
            'valore' => 'required',
            'tipo' => 'required|in_list[stringa,intero,decimale,booleano,data,datetime,json]',
            'descrizione' => 'permit_empty|max_length[255]',
            'gruppo' => 'required|max_length[50]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->validator->getErrors());
        }
        
        $chiave = $this->request->getPost('chiave');
        $valore = $this->request->getPost('valore');
        $tipo = $this->request->getPost('tipo');
        $descrizione = $this->request->getPost('descrizione');
        $gruppo = $this->request->getPost('gruppo');
        
        // Verifica che la chiave non esista già
        $esistente = $this->impostazioniModel->where('chiave', $chiave)
                                          ->where('id_utente IS NULL')
                                          ->first();
        
        if ($esistente) {
            $session->setFlashdata('error', 'Un\'impostazione con questa chiave esiste già');
            return redirect()->back()->withInput();
        }
        
        if ($this->impostazioniModel->setImpSistema($chiave, $valore, $tipo, $descrizione, $gruppo)) {
            $session->setFlashdata('success', 'Impostazione salvata con successo');
            return redirect()->to('/impostazioni');
        } else {
            $session->setFlashdata('error', 'Errore durante il salvataggio dell\'impostazione');
            return redirect()->back()->withInput();
        }
    }
    
    /**
     * Aggiorna un'impostazione di sistema esistente
     */
    public function aggiorna($id = null)
    {
        $session = session();
        
        // Verifica che l'utente sia un amministratore
        if (!$this->utentiModel->isAdmin()) {
            $session->setFlashdata('error', 'Non hai i permessi per aggiornare impostazioni di sistema');
            return redirect()->to('/');
        }
        
        $impostazione = $this->impostazioniModel->find($id);
        
        if (empty($impostazione)) {
            throw new PageNotFoundException('Impostazione non trovata');
        }
        
        if (!empty($impostazione['id_utente'])) {
            $session->setFlashdata('error', 'Non puoi modificare un\'impostazione utente da qui');
            return redirect()->to('/impostazioni');
        }
        
        $rules = [
            'valore' => 'required',
            'descrizione' => 'permit_empty|max_length[255]',
            'gruppo' => 'required|max_length[50]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->validator->getErrors());
        }
        
        $valore = $this->request->getPost('valore');
        $descrizione = $this->request->getPost('descrizione');
        $gruppo = $this->request->getPost('gruppo');
        
        // Aggiorna i dati dell'impostazione
        $data = [
            'valore' => $valore,
            'descrizione' => $descrizione,
            'gruppo' => $gruppo
        ];
        
        if ($this->impostazioniModel->update($id, $data)) {
            $session->setFlashdata('success', 'Impostazione aggiornata con successo');
            return redirect()->to('/impostazioni');
        } else {
            $session->setFlashdata('error', 'Errore durante l\'aggiornamento dell\'impostazione');
            return redirect()->back()->withInput();
        }
    }
    
    /**
     * Elimina un'impostazione di sistema
     */
    public function elimina($id = null)
    {
        $session = session();
        
        // Verifica che l'utente sia un amministratore
        if (!$this->utentiModel->isAdmin()) {
            $session->setFlashdata('error', 'Non hai i permessi per eliminare impostazioni di sistema');
            return redirect()->to('/');
        }
        
        $impostazione = $this->impostazioniModel->find($id);
        
        if (empty($impostazione)) {
            throw new PageNotFoundException('Impostazione non trovata');
        }
        
        if (!empty($impostazione['id_utente'])) {
            $session->setFlashdata('error', 'Non puoi eliminare un\'impostazione utente da qui');
            return redirect()->to('/impostazioni');
        }
        
        if ($this->impostazioniModel->delete($id)) {
            $session->setFlashdata('success', 'Impostazione eliminata con successo');
        } else {
            $session->setFlashdata('error', 'Errore durante l\'eliminazione dell\'impostazione');
        }
        
        return redirect()->to('/impostazioni');
    }
    
    /**
     * Salva le impostazioni personali dell'utente
     */
    public function salvaImpostazioniUtente()
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per salvare le impostazioni personali');
            return redirect()->to('/login');
        }
        
        $idUtente = (int)$session->get('utente_id');
        $impostazioniPost = $this->request->getPost('impostazioni');
        
        if (empty($impostazioniPost) || !is_array($impostazioniPost)) {
            $session->setFlashdata('error', 'Nessuna impostazione da salvare');
            return redirect()->back();
        }
        
        $errori = [];
        $salvate = 0;
        
        foreach ($impostazioniPost as $chiave => $valore) {
            // Ottieni i dettagli dell'impostazione esistente per conoscere il tipo
            $impostazione = $this->impostazioniModel->where('chiave', $chiave)
                                               ->where('id_utente', $idUtente)
                                               ->first();
            
            if ($impostazione) {
                // Aggiorna l'impostazione esistente
                if ($this->impostazioniModel->update($impostazione['id'], ['valore' => $valore])) {
                    $salvate++;
                } else {
                    $errori[] = 'Errore durante l\'aggiornamento dell\'impostazione ' . $chiave;
                }
            } else {
                // Ottieni i dettagli dell'impostazione di sistema per conoscere il tipo
                $impSistema = $this->impostazioniModel->where('chiave', $chiave)
                                                ->where('id_utente IS NULL')
                                                ->first();
                
                if ($impSistema) {
                    // Crea una nuova impostazione utente
                    if ($this->impostazioniModel->setImpUtente(
                        $chiave,
                        $valore,
                        $idUtente,
                        $impSistema['tipo'],
                        $impSistema['descrizione'],
                        $impSistema['gruppo']
                    )) {
                        $salvate++;
                    } else {
                        $errori[] = 'Errore durante il salvataggio dell\'impostazione ' . $chiave;
                    }
                } else {
                    $errori[] = 'Impostazione ' . $chiave . ' non trovata nel sistema';
                }
            }
        }
        
        if (count($errori) > 0) {
            $session->setFlashdata('error', implode('<br>', $errori));
        } else {
            $session->setFlashdata('success', $salvate . ' impostazioni salvate con successo');
        }
        
        return redirect()->to('/impostazioni/utente');
    }
    
    /**
     * Reimposta un'impostazione utente al valore di sistema
     */
    public function reimpostaDefault($id = null)
    {
        $session = session();
        
        // Verifica che l'utente sia loggato
        if (!$this->utentiModel->isLoggedIn()) {
            $session->setFlashdata('error', 'Devi effettuare il login per reimpostare le impostazioni personali');
            return redirect()->to('/login');
        }
        
        $idUtente = (int)$session->get('utente_id');
        
        $impostazione = $this->impostazioniModel->find($id);
        
        if (empty($impostazione)) {
            throw new PageNotFoundException('Impostazione non trovata');
        }
        
        // Verifica che l'impostazione appartenga all'utente corrente
        if (empty($impostazione['id_utente']) || $impostazione['id_utente'] != $idUtente) {
            $session->setFlashdata('error', 'Non puoi reimpostare questa impostazione');
            return redirect()->to('/impostazioni/utente');
        }
        
        if ($this->impostazioniModel->delete($id)) {
            $session->setFlashdata('success', 'Impostazione reimpostata al valore predefinito');
        } else {
            $session->setFlashdata('error', 'Errore durante la reimpostazione dell\'impostazione');
        }
        
        return redirect()->to('/impostazioni/utente');
    }
} 