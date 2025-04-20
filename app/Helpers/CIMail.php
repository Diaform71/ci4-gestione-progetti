/**
 * Registra un'email inviata nel log
 * 
 * @param array|string $destinatari Destinatari dell'email
 * @param array|string $cc Destinatari in copia
 * @param array|string $ccn Destinatari in copia nascosta
 * @param string $oggetto Oggetto dell'email
 * @param string $corpo Corpo dell'email
 * @param int $id_riferimento ID della richiesta d'offerta o ordine a cui si riferisce l'email
 * @param string $tipo_riferimento Tipo di riferimento (RDO, ORDINE, ecc.)
 * @param string $stato Stato dell'email (inviato, errore, ecc.)
 * @param string $errore Eventuale messaggio di errore
 * @param array $allegati Array di allegati
 * @return bool True se il log è stato registrato, False altrimenti
 */
function log_email($destinatari, $cc = null, $ccn = null, $oggetto, $corpo, $id_riferimento, $tipo_riferimento = 'RDO', $stato = 'inviato', $errore = null, $allegati = [])
{
    $db = \Config\Database::connect();
    
    // Converte gli array in stringhe
    if (is_array($destinatari)) {
        $destinatari = implode(', ', $destinatari);
    }
    
    if (is_array($cc)) {
        $cc = implode(', ', $cc);
    }
    
    if (is_array($ccn)) {
        $ccn = implode(', ', $ccn);
    }
    
    // Converte gli allegati in formato JSON
    $allegati_json = !empty($allegati) ? json_encode($allegati) : null;
    
    // Dati da inserire
    $data = [
        'destinatario' => $destinatari,
        'cc' => $cc,
        'ccn' => $ccn,
        'oggetto' => $oggetto,
        'corpo' => $corpo,
        'data_invio' => date('Y-m-d H:i:s'),
        'id_riferimento' => $id_riferimento,
        'tipo_riferimento' => $tipo_riferimento,
        'stato' => $stato,
        'errore' => $errore,
        'allegati' => $allegati_json,
        'id_utente' => session()->get('utente_id') ?: null
    ];
    
    try {
        $db->table('email_logs')->insert($data);
        return true;
    } catch (\Exception $e) {
        log_message('error', "Errore durante la registrazione dell'email nel log: " . $e->getMessage());
        return false;
    }
} 