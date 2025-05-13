<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ProgettoModel;
use App\Models\UtentiModel;
use App\Models\ImpostazioniModel;

class NotificheScadenzeCommand extends BaseCommand
{
    protected $group       = 'Notifiche';
    protected $name        = 'notifiche:scadenze';
    protected $description = 'Invia notifiche email per i progetti in scadenza';
    protected $usage       = 'notifiche:scadenze';

    public function run(array $params)
    {
        // Inizializza il modello delle impostazioni
        $impostazioniModel = new ImpostazioniModel();
        
        // Verifica se le notifiche email sono attive
        $notificheAttive = $impostazioniModel->getImpSistema('notifiche_email_attive', false);
        
        if (!$notificheAttive) {
            CLI::write('Le notifiche email sono disattivate nelle impostazioni di sistema.', 'yellow');
            return;
        }
        
        // Verifica se le notifiche per scadenza progetto sono attive
        $notificaScadenzaProgetto = $impostazioniModel->getImpSistema('notifica_scadenza_progetto', false);
        
        if (!$notificaScadenzaProgetto) {
            CLI::write('Le notifiche per progetti in scadenza sono disattivate nelle impostazioni.', 'yellow');
            return;
        }
        
        // Ottieni il numero di giorni di anticipo per le notifiche
        $giorniAnticipo = $impostazioniModel->getImpSistema('giorni_anticipo_notifica_scadenza', 3);
        
        // Debug delle impostazioni email del sistema
        CLI::write("Recupero impostazioni SMTP dal sistema...", 'green');
        
        // Recupera tutte le impostazioni del gruppo 'email'
        $impostazioniEmail = $impostazioniModel->getImpostazioniSistemaByGruppo();
        if (isset($impostazioniEmail['email'])) {
            foreach ($impostazioniEmail['email'] as $setting) {
                CLI::write("Impostazione: {$setting['chiave']} = " . (empty($setting['valore']) ? 'vuoto' : 'configurato'), 'green');
            }
        } else {
            CLI::write("Nessuna impostazione nel gruppo 'email' trovata", 'yellow');
        }
        
        // Cerca chiavi specifiche (potrebbero avere nomi diversi nel tuo sistema)
        $smtpHost = $impostazioniModel->getImpSistema('smtp_host');
        $smtpPort = $impostazioniModel->getImpSistema('smtp_port');
        $smtpUser = $impostazioniModel->getImpSistema('smtp_user');
        $smtpPass = $impostazioniModel->getImpSistema('smtp_pass');
        $emailFrom = $impostazioniModel->getImpSistema('email_from');
        $emailFromName = $impostazioniModel->getImpSistema('email_from_name');
        
        // Stampa per debug
        CLI::write("SMTP Host: " . ($smtpHost ?: 'non configurato'), 'yellow');
        CLI::write("SMTP Port: " . ($smtpPort ?: 'non configurato'), 'yellow');
        CLI::write("SMTP User: " . ($smtpUser ?: 'non configurato'), 'yellow');
        CLI::write("SMTP Pass: " . ($smtpPass ? 'configurato' : 'non configurato'), 'yellow');
        CLI::write("Email From: " . ($emailFrom ?: 'non configurato'), 'yellow');
        CLI::write("Email From Name: " . ($emailFromName ?: 'non configurato'), 'yellow');
        
        // Forza alcuni valori se mancanti, solo per test
        if (empty($smtpHost)) {
            $smtpHost = 'smtp.gmail.com';
            CLI::write("Forzato SMTP Host a: {$smtpHost}", 'yellow');
        }
        if (empty($smtpPort)) {
            $smtpPort = '587';
            CLI::write("Forzato SMTP Port a: {$smtpPort}", 'yellow');
        }
        if (empty($emailFrom)) {
            $emailFrom = 'noreply@example.com';
            CLI::write("Forzato Email From a: {$emailFrom}", 'yellow');
        }
        if (empty($emailFromName)) {
            $emailFromName = 'Sistema Notifiche';
            CLI::write("Forzato Email From Name a: {$emailFromName}", 'yellow');
        }
        
        CLI::write("Invio notifiche per progetti in scadenza nei prossimi {$giorniAnticipo} giorni...", 'green');
        
        // Inizializza i modelli necessari
        $progettoModel = new ProgettoModel();
        $utentiModel = new UtentiModel();
        
        // Ottieni i progetti in scadenza
        $progettiInScadenza = $progettoModel->getProjectsInScadenza($giorniAnticipo);
        
        if (empty($progettiInScadenza)) {
            CLI::write('Nessun progetto in scadenza nei prossimi giorni.', 'yellow');
            return;
        }
        
        CLI::write('Trovati ' . count($progettiInScadenza) . ' progetti in scadenza.', 'green');
        
        // Assicura che le classi PHPMailer siano incluse correttamente
        // Verifica se le classi PHPMailer sono già state caricate
        CLI::write("Verifica dell'inclusione delle classi PHPMailer...", 'green');
        
        // Imposta il percorso base corretto per PHPMailer
        // Per i comandi CLI, si deve usare il percorso assoluto dal FCPATH
        $phpmailerPath = FCPATH . 'PHPMailer/src/';
        
        // Verifica che i file esistano nel percorso specificato
        CLI::write("Percorso PHPMailer: " . $phpmailerPath, 'yellow');
        CLI::write("File Exception.php esiste: " . (file_exists($phpmailerPath . 'Exception.php') ? 'Sì' : 'No'), 'yellow');
        CLI::write("File PHPMailer.php esiste: " . (file_exists($phpmailerPath . 'PHPMailer.php') ? 'Sì' : 'No'), 'yellow');
        CLI::write("File SMTP.php esiste: " . (file_exists($phpmailerPath . 'SMTP.php') ? 'Sì' : 'No'), 'yellow');
        
        // Pre-includi le classi PHPMailer per evitare problemi di ridichiarazione
        if (!class_exists('PHPMailer\\PHPMailer\\Exception')) {
            require_once $phpmailerPath . 'Exception.php';
            CLI::write("Classe Exception caricata manualmente", 'green');
        } else {
            CLI::write("Classe Exception già caricata", 'green');
        }
        
        if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
            require_once $phpmailerPath . 'PHPMailer.php';
            CLI::write("Classe PHPMailer caricata manualmente", 'green');
        } else {
            CLI::write("Classe PHPMailer già caricata", 'green');
        }
        
        if (!class_exists('PHPMailer\\PHPMailer\\SMTP')) {
            require_once $phpmailerPath . 'SMTP.php';
            CLI::write("Classe SMTP caricata manualmente", 'green');
        } else {
            CLI::write("Classe SMTP già caricata", 'green');
        }
        
        // Carica l'helper per l'invio email
        helper('CIMail');
        
        $emailInviate = 0;
        $errori = 0;
        
        // Crea un array con la configurazione SMTP esplicita
        $smtpConfig = [
            'host' => $smtpHost,
            'port' => $smtpPort,
            'user' => $smtpUser,
            'pass' => $smtpPass,
            'from_email' => $emailFrom,
            'from_name' => $emailFromName
        ];
        
        // Definisci mittente standard
        $from = [
            'email' => $emailFrom,
            'name' => $emailFromName
        ];
        
        // Iteriamo su ogni progetto in scadenza
        foreach ($progettiInScadenza as $progetto) {
            // Ottieni il progetto con tutte le relazioni
            $progettoCompleto = $progettoModel->getProgettoWithRelations($progetto['id']);
            
            if (!$progettoCompleto) {
                CLI::error("Impossibile caricare i dettagli del progetto ID {$progetto['id']}.");
                continue;
            }
            
            // Determina i destinatari dell'email
            $destinatari = [];
            
            // Aggiungi il responsabile del progetto se esiste
            if (!empty($progettoCompleto['id_responsabile']) && !empty($progettoCompleto['responsabile']['email'])) {
                // Verifica le impostazioni personali del responsabile
                $responsabileId = $progettoCompleto['id_responsabile'];
                $notificheAttivoResponsabile = $impostazioniModel->getImpUtente('notifiche_email_attive', $responsabileId, $notificheAttive);
                $notificaScadenzaResponsabile = $impostazioniModel->getImpUtente('notifica_scadenza_progetto', $responsabileId, $notificaScadenzaProgetto);
                
                if ($notificheAttivoResponsabile && $notificaScadenzaResponsabile) {
                    $destinatari[] = [
                        'email' => $progettoCompleto['responsabile']['email'],
                        'id_utente' => $responsabileId
                    ];
                } else {
                    CLI::write("Il responsabile {$progettoCompleto['responsabile']['nome']} {$progettoCompleto['responsabile']['cognome']} ha disabilitato le notifiche di scadenza.", 'yellow');
                }
            }
            
            // Aggiungi il creatore del progetto se esiste e non è già stato aggiunto
            if (!empty($progettoCompleto['id_creato_da']) && !empty($progettoCompleto['creatore']['email'])) {
                $creatoreId = $progettoCompleto['id_creato_da'];
                
                // Verifica che non sia già stato aggiunto (potrebbe essere anche il responsabile)
                $giaAggiunto = false;
                foreach ($destinatari as $dest) {
                    if ($dest['id_utente'] == $creatoreId) {
                        $giaAggiunto = true;
                        break;
                    }
                }
                
                if (!$giaAggiunto) {
                    // Verifica le impostazioni personali del creatore
                    $notificheAttivoCreatore = $impostazioniModel->getImpUtente('notifiche_email_attive', $creatoreId, $notificheAttive);
                    $notificaScadenzaCreatore = $impostazioniModel->getImpUtente('notifica_scadenza_progetto', $creatoreId, $notificaScadenzaProgetto);
                    
                    if ($notificheAttivoCreatore && $notificaScadenzaCreatore) {
                        $destinatari[] = [
                            'email' => $progettoCompleto['creatore']['email'],
                            'id_utente' => $creatoreId
                        ];
                    } else {
                        CLI::write("Il creatore {$progettoCompleto['creatore']['nome']} {$progettoCompleto['creatore']['cognome']} ha disabilitato le notifiche di scadenza.", 'yellow');
                    }
                }
            }
            
            // Se non ci sono destinatari validi, continua con il prossimo progetto
            if (empty($destinatari)) {
                CLI::write("Nessun destinatario valido trovato per il progetto \"{$progettoCompleto['nome']}\" (ID: {$progettoCompleto['id']}).", 'yellow');
                continue;
            }
            
            // Calcola i giorni rimanenti alla scadenza
            $dataScadenza = new \DateTime($progettoCompleto['data_scadenza']);
            $oggi = new \DateTime();
            $giorniRimanenti = $dataScadenza->diff($oggi)->days;
            
            // Prepara l'oggetto dell'email
            $oggetto = "Progetto in scadenza: {$progettoCompleto['nome']}";
            
            // Prepara il corpo dell'email
            $corpo = "<h2>Notifica di scadenza progetto</h2>";
            $corpo .= "<p>Il seguente progetto è in scadenza ";
            
            if ($giorniRimanenti == 0) {
                $corpo .= "<strong>oggi</strong>:";
            } elseif ($giorniRimanenti == 1) {
                $corpo .= "tra <strong>1 giorno</strong>:";
            } else {
                $corpo .= "tra <strong>{$giorniRimanenti} giorni</strong>:";
            }
            
            $corpo .= "</p><ul>";
            $corpo .= "<li><strong>Nome:</strong> {$progettoCompleto['nome']}</li>";
            $corpo .= "<li><strong>Descrizione:</strong> " . ($progettoCompleto['descrizione'] ?? 'Non specificata') . "</li>";
            $corpo .= "<li><strong>Data scadenza:</strong> " . date('d/m/Y', strtotime($progettoCompleto['data_scadenza'])) . "</li>";
            $corpo .= "<li><strong>Priorità:</strong> " . ucfirst($progettoCompleto['priorita']) . "</li>";
            $corpo .= "<li><strong>Stato:</strong> " . ucfirst(str_replace('_', ' ', $progettoCompleto['stato'])) . "</li>";
            
            // Aggiungi dettagli sul cliente se disponibili
            if (!empty($progettoCompleto['anagrafica']['ragione_sociale'])) {
                $corpo .= "<li><strong>Cliente:</strong> {$progettoCompleto['anagrafica']['ragione_sociale']}</li>";
            }
            
            $corpo .= "</ul>";
            $corpo .= "<p>Per visualizzare i dettagli del progetto, <a href='" . base_url("/progetti/{$progettoCompleto['id']}") . "'>clicca qui</a>.</p>";
            
            // Per ogni destinatario, verifica il numero di giorni di anticipo personalizzato
            foreach ($destinatari as $destinatario) {
                $idUtente = $destinatario['id_utente'];
                $email = $destinatario['email'];
                
                // Ottieni le impostazioni personalizzate dell'utente per i giorni di anticipo
                $giorniAnticipoUtente = $impostazioniModel->getImpUtente('giorni_anticipo_notifica_scadenza', $idUtente, $giorniAnticipo);
                
                // Verifica se il progetto è in scadenza in base alle impostazioni personalizzate dell'utente
                if ($giorniRimanenti <= $giorniAnticipoUtente) {
                    // Invia l'email a questo utente
                    CLI::write("Invio notifica per \"{$progettoCompleto['nome']}\" a: {$email} (anticipo: {$giorniAnticipoUtente} giorni)");
                    
                    try {
                        // Passare esplicitamente la configurazione SMTP
                        $risultato = send_email([$email], $oggetto, $corpo, $from, [], [], [], [], $smtpConfig);
                        
                        if ($risultato['status']) {
                            $emailInviate++;
                            CLI::write("Email inviata con successo.", 'green');
                            // Registra l'invio nel log
                            log_message('info', "Email di notifica per progetto in scadenza [{$progettoCompleto['id']}] inviata con successo a: {$email}");
                        } else {
                            $errori++;
                            CLI::error("Errore nell'invio dell'email a {$email}: " . $risultato['error']);
                            // Registra l'errore nel log
                            log_message('error', "Errore nell'invio email di notifica per progetto in scadenza [{$progettoCompleto['id']}] a {$email}: " . $risultato['error']);
                        }
                    } catch (\Exception $e) {
                        $errori++;
                        CLI::error("Eccezione durante l'invio dell'email a {$email}: " . $e->getMessage());
                        log_message('error', "Eccezione durante l'invio email di notifica per progetto in scadenza [{$progettoCompleto['id']}] a {$email}: " . $e->getMessage());
                    }
                } else {
                    CLI::write("Notifica non inviata a {$email} - il progetto non è ancora in scadenza secondo le impostazioni dell'utente (anticipo: {$giorniAnticipoUtente} giorni).", 'yellow');
                }
            }
        }
        
        // Mostra il riepilogo
        CLI::write("");
        CLI::write("Riepilogo operazione:", 'green');
        CLI::write("- Progetti in scadenza: " . count($progettiInScadenza));
        CLI::write("- Email inviate con successo: {$emailInviate}");
        CLI::write("- Errori: {$errori}");
        
        return 0;
    }
} 