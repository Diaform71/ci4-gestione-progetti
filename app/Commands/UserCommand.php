<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\UtentiModel;

class UserCommand extends BaseCommand
{
    protected $group       = 'Custom';
    protected $name        = 'user';
    protected $description = 'Utility per la gestione degli utenti';
    protected $usage       = 'user [opzione] [parametri]';
    protected $arguments   = [
        'updateEmail' => 'Aggiorna l\'email di un utente esistente',
        'list'        => 'Elenca tutti gli utenti nel sistema',
    ];

    public function run(array $params)
    {
        if (empty($params[0])) {
            $this->showHelp();
            return;
        }

        switch ($params[0]) {
            case 'updateEmail':
                $this->updateEmail($params);
                break;
            case 'list':
                $this->listUsers();
                break;
            default:
                $this->showHelp();
                break;
        }
    }

    /**
     * Mostra l'help del comando
     */
    public function showHelp()
    {
        CLI::write('Utilizzo: ' . $this->usage);
        CLI::write('');
        CLI::write('Opzioni:');
        foreach ($this->arguments as $arg => $description) {
            CLI::write("  {$arg}: {$description}", 'yellow');
        }
        CLI::write('');
        CLI::write('Esempi:');
        CLI::write('  php spark user updateEmail 1 admin@example.com', 'green');
        CLI::write('  php spark user list', 'green');

        return 0;
    }

    /**
     * Aggiorna l'email di un utente esistente
     */
    private function updateEmail(array $params)
    {
        if (empty($params[1]) || empty($params[2])) {
            CLI::error('Errore: Specificare l\'ID utente e l\'indirizzo email.');
            CLI::write('Esempio: php spark user updateEmail 1 admin@example.com', 'yellow');
            return;
        }

        $userId = $params[1];
        $email = $params[2];

        // Verifica che l'email sia valida
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            CLI::error('Errore: Indirizzo email non valido.');
            return;
        }

        $utentiModel = new UtentiModel();
        $utente = $utentiModel->find($userId);

        if (empty($utente)) {
            CLI::error("Errore: Utente con ID {$userId} non trovato.");
            return;
        }

        // Aggiorna l'email dell'utente
        $result = $utentiModel->update($userId, ['email' => $email]);

        if ($result) {
            CLI::write("Email dell'utente {$utente['username']} aggiornata con successo a: {$email}", 'green');
        } else {
            CLI::error("Errore durante l'aggiornamento dell'email: " . implode(', ', $utentiModel->errors()));
        }
    }

    /**
     * Elenca tutti gli utenti nel sistema
     */
    private function listUsers()
    {
        $utentiModel = new UtentiModel();
        $utenti = $utentiModel->findAll();

        if (empty($utenti)) {
            CLI::write('Nessun utente trovato nel sistema.', 'yellow');
            return;
        }

        CLI::write('Elenco utenti:', 'green');
        CLI::write(str_repeat('-', 100));
        CLI::write(
            CLI::color('ID', 'yellow') . "\t" .
            CLI::color('Username', 'yellow') . "\t" .
            CLI::color('Email', 'yellow') . "\t" .
            CLI::color('Nome', 'yellow') . "\t" .
            CLI::color('Cognome', 'yellow') . "\t" .
            CLI::color('Ruolo', 'yellow')
        );
        CLI::write(str_repeat('-', 100));

        foreach ($utenti as $utente) {
            CLI::write(
                $utente['id'] . "\t" .
                $utente['username'] . "\t" .
                ($utente['email'] ?? 'N/A') . "\t" .
                ($utente['nome'] ?? 'N/A') . "\t" .
                ($utente['cognome'] ?? 'N/A') . "\t" .
                $utente['ruolo']
            );
        }
        CLI::write(str_repeat('-', 100));
    }
} 