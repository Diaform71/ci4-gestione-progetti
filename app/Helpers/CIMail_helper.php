<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// use Config\Services;

if (!function_exists('send_email')) {
    function send_email($to, $subject, $message, $from = null, $attachments = [], $attach_pdf = [], $cc = [], $ccn = [], $smtp_config = null)
    {
        // Debug iniziale per tracciare i parametri di input
        log_message('debug', '==== INIZIO INVIO EMAIL ====');
        log_message('debug', 'Destinatario: ' . (is_array($to) ? json_encode($to) : $to));
        log_message('debug', 'CC: ' . (is_array($cc) ? json_encode($cc) : ($cc ?? 'nessuno')));
        log_message('debug', 'CCN: ' . (is_array($ccn) ? json_encode($ccn) : ($ccn ?? 'nessuno')));
        log_message('debug', 'Oggetto: ' . $subject);
        log_message('debug', 'Lunghezza messaggio: ' . strlen($message));
        log_message('debug', 'Mittente: ' . json_encode($from));
        log_message('debug', 'Allegati: ' . json_encode($attachments));
        log_message('debug', 'Allega PDF: ' . json_encode($attach_pdf));
        log_message('debug', 'Config SMTP custom: ' . ($smtp_config ? 'Sì' : 'No'));
        
        // Carica ImpostazioniModel per recuperare la configurazione dal database
        $impostazioniModel = new \App\Models\ImpostazioniModel();
        
        // Recupera l'ID utente dalla sessione o dalla richiesta da $from
        $user_id = null;
        if (session()->has('utente_id')) {
            $user_id = session()->get('utente_id');
            log_message('debug', 'Utente trovato in sessione: ' . $user_id);
        } else {
            log_message('debug', 'Nessun utente in sessione');
        }
        
        // Recupera le impostazioni email dal database, considerando prima le impostazioni utente se disponibili
        $email_host = null;
        $email_port = null;
        $email_username = null;
        $email_password = null;
        $email_encryption = null;
        $system_from_email = null;
        $system_from_name = null;
        
        // Se è stata passata una configurazione SMTP personalizzata, usala
        if ($smtp_config && is_array($smtp_config)) {
            log_message('debug', 'Utilizzo configurazione SMTP personalizzata');
            $email_host = $smtp_config['host'] ?? null;
            $email_port = $smtp_config['port'] ?? null;
            $email_username = $smtp_config['user'] ?? null;
            $email_password = $smtp_config['pass'] ?? null;
            $email_encryption = $smtp_config['encryption'] ?? 'tls';
            $system_from_email = $smtp_config['from_email'] ?? null;
            $system_from_name = $smtp_config['from_name'] ?? null;
            
            log_message('debug', 'Config SMTP personalizzata - Host: ' . $email_host);
            log_message('debug', 'Config SMTP personalizzata - Port: ' . $email_port);
            log_message('debug', 'Config SMTP personalizzata - Username: ' . $email_username);
            log_message('debug', 'Config SMTP personalizzata - From Email: ' . $system_from_email);
            log_message('debug', 'Config SMTP personalizzata - From Name: ' . $system_from_name);
        }
        // Altrimenti, procedi normalmente con il recupero delle impostazioni
        else {
        // Se abbiamo un ID utente, tenta di recuperare le impostazioni personalizzate
        if ($user_id) {
            log_message('debug', 'Tentativo di recuperare impostazioni personalizzate per utente ID: ' . $user_id);
            $email_host = $impostazioniModel->getImpUtente('smtp_host', (int)$user_id, null);
            $email_port = $impostazioniModel->getImpUtente('smtp_port', (int)$user_id, null);
            $email_username = $impostazioniModel->getImpUtente('smtp_user', (int)$user_id, null);
            $email_password = $impostazioniModel->getImpUtente('smtp_pass', (int)$user_id, null);
            $email_encryption = $impostazioniModel->getImpUtente('email_encryption', (int)$user_id, null);
            $system_from_email = $impostazioniModel->getImpUtente('email_from', (int)$user_id, null);
            $system_from_name = $impostazioniModel->getImpUtente('email_from_name', (int)$user_id, null);
            
            // Debug delle impostazioni utente trovate
            log_message('debug', 'Impostazioni utente trovate - Host: ' . ($email_host ? 'Sì' : 'No') . 
                                ', Port: ' . ($email_port ? 'Sì' : 'No') . 
                                ', Username: ' . ($email_username ? 'Sì' : 'No') . 
                                ', Password: ' . ($email_password ? 'Sì' : 'No') . 
                                ', Encryption: ' . ($email_encryption ? 'Sì' : 'No') . 
                                ', From Email: ' . ($system_from_email ? 'Sì' : 'No') . 
                                ', From Name: ' . ($system_from_name ? 'Sì' : 'No'));
            }
        }
        
        // Recupera le impostazioni di sistema per qualsiasi impostazione mancante
        if (empty($email_host)) {
            $email_host = $impostazioniModel->getImpSistema('smtp_host', env('EMAIL_HOST', ''));
            log_message('debug', 'Usando smtp_host di sistema: ' . $email_host);
        }
        if (empty($email_port)) {
            $email_port = $impostazioniModel->getImpSistema('smtp_port', env('EMAIL_PORT', '587'));
            log_message('debug', 'Usando smtp_port di sistema: ' . $email_port);
        }
        if (empty($email_username)) {
            // Prova con diverse possibili chiavi
            $email_username = $impostazioniModel->getImpSistema('smtp_user', null);
        if (empty($email_username)) {
            $email_username = $impostazioniModel->getImpSistema('email_username', env('EMAIL_USERNAME', ''));
            }
            log_message('debug', 'Usando smtp_user di sistema: ' . $email_username);
        }
        if (empty($email_password)) {
            // Prova con diverse possibili chiavi
            $email_password = $impostazioniModel->getImpSistema('smtp_pass', null);
        if (empty($email_password)) {
            $email_password = $impostazioniModel->getImpSistema('email_password', env('EMAIL_PASSWORD', ''));
            }
            log_message('debug', 'Password SMTP di sistema: ' . ($email_password ? 'Configurata' : 'Non configurata'));
        }
        if (empty($email_encryption)) {
            $email_encryption = $impostazioniModel->getImpSistema('email_encryption', env('EMAIL_ENCRYPTION', 'tls'));
            log_message('debug', 'Usando email_encryption di sistema: ' . $email_encryption);
        }
        if (empty($system_from_email)) {
            // Prova con diverse possibili chiavi
            $system_from_email = $impostazioniModel->getImpSistema('email_from', null);
        if (empty($system_from_email)) {
            $system_from_email = $impostazioniModel->getImpSistema('email_from_address', env('EMAIL_FROM_ADDRESS', ''));
            }
            log_message('debug', 'Usando email_from di sistema: ' . $system_from_email);
        }
        if (empty($system_from_name)) {
            $system_from_name = $impostazioniModel->getImpSistema('email_from_name', env('EMAIL_FROM_NAME', ''));
            log_message('debug', 'Usando email_from_name di sistema: ' . $system_from_name);
        }
        
        log_message('debug', 'Impostazioni finali - Host: ' . $email_host . 
                            ', Port: ' . $email_port . 
                            ', Username: ' . $email_username . 
                            ', From Email: ' . $system_from_email . 
                            ', From Name: ' . $system_from_name);
        
        // Verifica del percorso dei file di PHPMailer
        $phpmailer_base = 'PHPMailer/src/';
        $exception_path = $phpmailer_base . 'Exception.php';
        $phpmailer_path = $phpmailer_base . 'PHPMailer.php';
        $smtp_path = $phpmailer_base . 'SMTP.php';
        
        log_message('debug', 'File PHPMailer - Exception: ' . $exception_path . ' (Esiste: ' . (file_exists($exception_path) ? 'Sì' : 'No') . ')');
        log_message('debug', 'File PHPMailer - PHPMailer: ' . $phpmailer_path . ' (Esiste: ' . (file_exists($phpmailer_path) ? 'Sì' : 'No') . ')');
        log_message('debug', 'File PHPMailer - SMTP: ' . $smtp_path . ' (Esiste: ' . (file_exists($smtp_path) ? 'Sì' : 'No') . ')');

        // Verifica se le classi PHPMailer sono già caricate prima di includerle
        if (!class_exists('PHPMailer\\PHPMailer\\Exception')) {
        require 'PHPMailer/src/Exception.php';
        }
        if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
        require 'PHPMailer/src/PHPMailer.php';
        }
        if (!class_exists('PHPMailer\\PHPMailer\\SMTP')) {
        require 'PHPMailer/src/SMTP.php';
        }

        try {
            $mail = new PHPMailer(true);
            $mail->CharSet = 'UTF-8';

            // Configurazione SMTP
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            
            // Debug configurazione SMTP recuperata dal database o da .env
            log_message('debug', 'Config SMTP - Host: ' . $email_host);
            log_message('debug', 'Config SMTP - Username: ' . $email_username);
            log_message('debug', 'Config SMTP - Password: ' . ($email_password ? '[PASSWORD IMPOSTATA]' : '[PASSWORD MANCANTE]'));
            log_message('debug', 'Config SMTP - Encryption: ' . $email_encryption);
            log_message('debug', 'Config SMTP - Port: ' . $email_port);
            log_message('debug', 'Config SMTP - From Address: ' . $system_from_email);
            log_message('debug', 'Config SMTP - From Name: ' . $system_from_name);
            
            // Verifica che le informazioni critiche siano disponibili
            if (empty($email_host) || empty($email_username) || empty($email_password) || empty($system_from_email)) {
                throw new \Exception("Configurazione SMTP incompleta. Verifica le impostazioni di sistema per l'email.");
            }
            
            // Configura SMTP
            $mail->Host = $email_host;
            $mail->SMTPAuth = true;
            $mail->Username = $email_username;
            $mail->Password = $email_password;
            $mail->SMTPSecure = $email_encryption;
            $mail->Port = $email_port;
            
            // Abilita debug dettagliato
            $mail->SMTPDebug = 4; // Livello di debug elevato
            $mail->Debugoutput = function($str, $level) {
                log_message('debug', "PHPMailer [$level]: $str");
            };

            // Gestione del mittente
            if ($from && is_array($from) && isset($from['email']) && isset($from['name'])) {
                // Usa i dati passati come parametro
                $fromEmail = $from['email'];
                $fromName = $from['name'];
                
                // Verifica la validità dell'indirizzo email del mittente
                if (!filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
                    log_message('warning', "Email mittente non valida: {$fromEmail}, uso indirizzo di sistema");
                    $fromEmail = $system_from_email;
                }
                
                // Imposta il Reply-To con l'email dell'utente
                $mail->addReplyTo($fromEmail, $fromName);
                log_message('debug', 'Mittente personalizzato - Email: ' . $fromEmail . ', Nome: ' . $fromName);
            } else {
                // Fallback sui valori di sistema
                $fromEmail = $system_from_email;
                $fromName = $system_from_name;
                log_message('debug', 'Mittente di default - Email: ' . $fromEmail . ', Nome: ' . $fromName);
            }

            // Il setFrom usa sempre l'email di sistema per garantire la consegna
            log_message('debug', 'Email mittente di sistema utilizzata: ' . $system_from_email);
            
            // Verifica che l'indirizzo mittente sia valido
            if (!filter_var($system_from_email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception("Indirizzo email mittente non valido: {$system_from_email}");
            }
            
            $mail->setFrom($system_from_email, $fromName);
            
            // Gestione destinatari
            if (is_array($to)) {
                foreach ($to as $recipient) {
                    // Verifica validità indirizzo destinatario
                    if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                        log_message('warning', "Indirizzo destinatario non valido ignorato: {$recipient}");
                        continue;
                    }
                    $mail->addAddress($recipient);
                    log_message('debug', 'Aggiunto destinatario (array): ' . $recipient);
                }
            } else {
                // Verifica validità indirizzo destinatario
                if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception("Indirizzo destinatario non valido: {$to}");
                }
                $mail->addAddress($to);
                log_message('debug', 'Aggiunto destinatario (singolo): ' . $to);
            }

            // Gestione destinatari in CC
            if (!empty($cc)) {
                if (is_array($cc)) {
                    foreach ($cc as $recipient) {
                        // Verifica validità indirizzo CC
                        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                            log_message('warning', "Indirizzo CC non valido ignorato: {$recipient}");
                            continue;
                        }
                        $mail->addCC($recipient);
                        log_message('debug', 'Aggiunto destinatario CC: ' . $recipient);
                    }
                } else {
                    // Verifica validità indirizzo CC
                    if (filter_var($cc, FILTER_VALIDATE_EMAIL)) {
                        $mail->addCC($cc);
                        log_message('debug', 'Aggiunto destinatario CC (singolo): ' . $cc);
                    } else {
                        log_message('warning', "Indirizzo CC non valido ignorato: {$cc}");
                    }
                }
            }
            
            // Gestione destinatari in CCN
            if (!empty($ccn)) {
                if (is_array($ccn)) {
                    foreach ($ccn as $recipient) {
                        // Verifica validità indirizzo CCN
                        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                            log_message('warning', "Indirizzo CCN non valido ignorato: {$recipient}");
                            continue;
                        }
                        $mail->addBCC($recipient);
                        log_message('debug', 'Aggiunto destinatario CCN: ' . $recipient);
                    }
                } else {
                    // Verifica validità indirizzo CCN
                    if (filter_var($ccn, FILTER_VALIDATE_EMAIL)) {
                        $mail->addBCC($ccn);
                        log_message('debug', 'Aggiunto destinatario CCN (singolo): ' . $ccn);
                    } else {
                        log_message('warning', "Indirizzo CCN non valido ignorato: {$ccn}");
                    }
                }
            }

            // Logo
            $logo = get_settings()->email_logo ?? null;
            if ($logo && file_exists('images/system/' . $logo)) {
                $logo_path = 'images/system/' . $logo;
                log_message('debug', 'Logo trovato: ' . $logo_path);
                $mail->AddEmbeddedImage($logo_path, 'logo_2u');
            } else {
                log_message('debug', 'Logo non trovato o non configurato.');
            }

            // Decodifica il contenuto HTML
            $subject = html_entity_decode($subject, ENT_QUOTES, 'UTF-8');
            $message = html_entity_decode($message, ENT_QUOTES, 'UTF-8');

            // Aggiungi la firma HTML
            $firma_html = "<p>Cordiali saluti,<br><br>{$fromName}<br>";
            if (!empty($fromEmail)) {
                $firma_html .= "{$fromEmail}<br><br>";
            }
            if ($logo) {
                $firma_html .= "<img src='cid:logo_2u' alt='Logo' width='75px;'>";
            }
            $firma_html .= "</p>";

            $message .= $firma_html;
            log_message('debug', 'Firma aggiunta al messaggio.');

            // Gestione allegato PDF della richiesta
            if(!empty($attach_pdf)){
                if ($attach_pdf['pdf'] == 'true') {
                    // Verifica se stiamo lavorando con una richiesta d'offerta o un ordine
                    if(isset($attach_pdf['id_richiesta'])) {
                        // È una richiesta d'offerta
                        log_message('debug', 'Generazione PDF richiesta richiesto per ID: ' . $attach_pdf['id_richiesta']);
                        $pdfPath = generate_richiesta_pdf($attach_pdf['id_richiesta']);
                        log_message('debug', 'PDF generato: ' . ($pdfPath ?: 'Fallito'));
        
                        if ($pdfPath && file_exists($pdfPath)) {
                            // Crea un nome file significativo per l'allegato
                            $pdfFileName = 'RDO_' . $attach_pdf['numero'] . '.pdf';
                            log_message('debug', 'Allegato PDF: ' . $pdfPath . ' come ' . $pdfFileName);
        
                            $mail->addAttachment($pdfPath, $pdfFileName);
        
                            // Registra il file temporaneo per la pulizia
                            if (!isset($tempFiles)) {
                                $tempFiles = [];
                            }
                            $tempFiles[] = $pdfPath;
                        } else {
                            log_message('error', 'PDF non generato o non trovato: ' . $pdfPath);
                        }
                    } elseif(isset($attach_pdf['id_ordine'])) {
                        // È un ordine
                        log_message('debug', 'Generazione PDF ordine richiesto per ID: ' . $attach_pdf['id_ordine']);
                        $pdfPath = generate_ordine_pdf($attach_pdf['id_ordine']);
                        log_message('debug', 'PDF generato: ' . ($pdfPath ?: 'Fallito'));
        
                        if ($pdfPath && file_exists($pdfPath)) {
                            // Crea un nome file significativo per l'allegato
                            $pdfFileName = 'ORDINE_' . $attach_pdf['numero'] . '.pdf';
                            log_message('debug', 'Allegato PDF: ' . $pdfPath . ' come ' . $pdfFileName);
        
                            $mail->addAttachment($pdfPath, $pdfFileName);
        
                            // Registra il file temporaneo per la pulizia
                            if (!isset($tempFiles)) {
                                $tempFiles = [];
                            }
                            $tempFiles[] = $pdfPath;
                        } else {
                            log_message('error', 'PDF non generato o non trovato: ' . $pdfPath);
                        }
                    } else {
                        log_message('error', 'Impossibile generare PDF: ID riferimento mancante nell\'array attach_pdf');
                    }
                }
            }

            // Gestione allegati
            if (!empty($attachments) && is_array($attachments)) {
                $temp_path = TEMP_UPLOAD_PATH;
                log_message('debug', 'Percorso temporaneo allegati: ' . $temp_path . ' (Esiste: ' . (is_dir($temp_path) ? 'Sì' : 'No') . ')');
                
                foreach ($attachments as $attachment) {
                    // Se viene passato solo il nome del file
                    if (is_string($attachment)) {
                        $filePath = $temp_path . $attachment;
                        $fileName = $attachment;
                    }
                    // Se viene passato un array con path e name
                    else if (is_array($attachment)) {
                        $filePath = isset($attachment['path']) ? $attachment['path'] : $temp_path . $attachment['name'];
                        $fileName = $attachment['name'];
                    }

                    log_message('debug', 'Tentativo di allegare file. Path: ' . $filePath . ', Nome: ' . $fileName);

                    if (file_exists($filePath)) {
                        log_message('debug', 'File trovato: ' . $filePath . ' (Size: ' . filesize($filePath) . ')');
                        $mail->addAttachment($filePath, $fileName);
                    } else {
                        log_message('error', 'File non trovato: ' . $filePath);
                    }
                }
            }

            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->Body = $message;

            log_message('debug', 'Email pronta per l\'invio. Tentativo di invio...');
            $send_result = $mail->send();
            log_message('debug', 'Risultato invio: ' . ($send_result ? 'Successo' : 'Fallito'));
            
            if ($send_result) {
                // Pulizia dei file temporanei
                if (!empty($attachments)) {
                    foreach ($attachments as $attachment) {
                        if (isset($attachment['temp']) && $attachment['temp'] && file_exists($attachment['path'])) {
                            unlink($attachment['path']);
                            log_message('debug', 'File temporaneo eliminato: ' . $attachment['path']);
                        }
                    }
                }

                // Pulizia del PDF temporaneo se esiste
                if (!empty($tempFiles)) {
                    foreach ($tempFiles as $tempFile) {
                        if (file_exists($tempFile)) {
                            unlink($tempFile);
                            log_message('debug', 'PDF temporaneo eliminato: ' . $tempFile);
                        }
                    }
                }
                
                log_message('debug', '==== FINE INVIO EMAIL (SUCCESSO) ====');
                return [
                    'status' => 1,
                    'msg' => 'Email inviata con successo'
                ];
            } else {
                log_message('error', 'Email non inviata. Errore: ' . $mail->ErrorInfo);
                log_message('debug', '==== FINE INVIO EMAIL (FALLITO) ====');
                return [
                    'status' => 0,
                    'msg' => 'Errore nell\'invio dell\'email: ' . $mail->ErrorInfo
                ];
            }
        } catch (Exception $e) {
            log_message('error', 'Eccezione nell\'invio email: ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            log_message('debug', '==== FINE INVIO EMAIL (ECCEZIONE) ====');
            return [
                'status' => 0,
                'msg' => 'Errore nell\'invio dell\'email: ' . $e->getMessage()
            ];
        }
    }
}

if (!function_exists('log_email')) {
    function log_email($to, $cc, $bcc, $subject, $message, $id_riferimento = null, $tipo_riferimento = 'RDO', $stato = 'inviato', $error = null, $allegati = [])
    {
        $db = \Config\Database::connect();
        $builder = $db->table('email_logs');
        
        // Converti array in stringhe
        $to_str = is_array($to) ? implode(', ', $to) : $to;
        $cc_str = is_array($cc) ? implode(', ', $cc) : $cc;
        $bcc_str = is_array($bcc) ? implode(', ', $bcc) : $bcc;
        $allegati_str = is_array($allegati) ? json_encode($allegati) : '';
        
        $data = [
            'destinatario' => $to_str,
            'cc' => $cc_str,
            'ccn' => $bcc_str,
            'oggetto' => $subject,
            'corpo' => $message,
            'id_riferimento' => $id_riferimento,
            'tipo_riferimento' => $tipo_riferimento,
            'data_invio' => date('Y-m-d H:i:s'),
            'stato' => $stato,
            'error_message' => $error,
            'allegati' => $allegati_str,
            'id_utente' => session()->get('utente_id'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        return $builder->insert($data);
    }
}

if (!function_exists('get_temp_file')) {
    function get_temp_file($fileId)
    {
        $filePath = TEMP_UPLOAD_PATH . $fileId;

        if (file_exists($filePath)) {
            return [
                'path' => $filePath,
                'name' => $fileId // o estrai il nome originale se lo memorizzi
            ];
        }

        return null;
    }
}

if (!function_exists('get_contatto')) {
    function get_contatto($id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('contatti');
        $contatto = $builder->where('id', $id)->get()->getRow();
        return $contatto;
    }
}

if (!function_exists('generate_richiesta_pdf')) {
    function generate_richiesta_pdf($id_richiesta)
    {
        try {
            // Utilizziamo il PdfController per generare il PDF
            $pdfController = new \App\Controllers\PdfController();
            $pdfPath = $pdfController->pdfRichiesta($id_richiesta, true);

            // Aggiungi log per debug
            log_message('debug', 'PDF generato: ' . ($pdfPath ?: 'null'));

            return $pdfPath;
        } catch (\Exception $e) {
            log_message('error', 'Errore nella generazione del PDF: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('generate_ordine_pdf')) {
    function generate_ordine_pdf($id_ordine)
    {
        try {
            // Utilizziamo il PdfController per generare il PDF
            $pdfController = new \App\Controllers\PdfController();
            $pdfPath = $pdfController->pdfOrdineMateriale($id_ordine, true);

            // Aggiungi log per debug
            log_message('debug', 'PDF ordine generato: ' . ($pdfPath ?: 'null'));

            return $pdfPath;
        } catch (\Exception $e) {
            log_message('error', 'Errore nella generazione del PDF ordine: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('get_settings')) {
    function get_settings()
    {
        // Implementazione base, da espandere se necessario
        $settings = new \stdClass();
        $settings->email_logo = 'logo.png'; // Placeholder, sostituire con logica reale
        
        return $settings;
    }
}
