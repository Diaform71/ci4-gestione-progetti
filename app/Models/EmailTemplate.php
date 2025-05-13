<?php

namespace App\Models;

use CodeIgniter\Model;

class EmailTemplate extends Model
{
    protected $table = 'email_templates';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nome', 'oggetto', 'corpo', 'tipo'];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $returnType = 'array';
    
    // Tipi di template predefiniti
    const TIPO_RDO = 'RDO';
    const TIPO_ORDINE = 'ORDINE';
    const TIPO_OFFERTA = 'OFFERTA';
    
    /**
     * Sostituisce i placeholder nel template con i dati effettivi
     */
    public function compilaTemplate($template, $dati)
    {
        if (!is_array($template) || !isset($template['oggetto']) || !isset($template['corpo'])) {
            throw new \Exception('Template non valido');
        }

        // Log dei dati in ingresso per debug
        log_message('debug', 'Template originale - Oggetto: ' . $template['oggetto']);
        log_message('debug', 'Template originale - Corpo: ' . $template['corpo']);
        log_message('debug', 'Dati per sostituzione: ' . print_r($dati, true));

        $oggetto = html_entity_decode($template['oggetto'], ENT_QUOTES, 'UTF-8');
        $corpo = html_entity_decode($template['corpo'], ENT_QUOTES, 'UTF-8');
        
        // Sostituisce i placeholder nel formato {{variabile}}
        foreach ($dati as $key => $value) {
            // Controlla che il valore non sia null o array prima della decodifica
            if (is_null($value)) {
                $value = '';
            } elseif (is_array($value)) {
                $value = json_encode($value);
            } elseif (is_string($value)) {
                $value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
            }
            
            // Log per il debug
            log_message('debug', "Sostituzione segnaposto: chiave = {{$key}}, valore = " . (is_string($value) ? substr($value, 0, 30) . '...' : gettype($value)));
            
            // Conta quante occorrenze ci sono
            $countInOggetto = substr_count($oggetto, '{{' . $key . '}}');
            $countInCorpo = substr_count($corpo, '{{' . $key . '}}');
            log_message('debug', "Occorrenze di {{$key}}: $countInOggetto in oggetto, $countInCorpo in corpo");
            
            if ($key === 'materiali') {
                $corpo = str_replace('{{' . $key . '}}', $value, $corpo);
            } else {
                $oggetto = str_replace('{{' . $key . '}}', $value, $oggetto);
                $corpo = str_replace('{{' . $key . '}}', $value, $corpo);
            }
        }
        
        // Log del risultato finale
        log_message('debug', 'Template compilato - Oggetto: ' . $oggetto);
        log_message('debug', 'Template compilato - Corpo: inizio ' . substr($corpo, 0, 100) . '... fine');
        
        return [
            'oggetto' => $oggetto,
            'corpo' => $corpo
        ];
    }
} 