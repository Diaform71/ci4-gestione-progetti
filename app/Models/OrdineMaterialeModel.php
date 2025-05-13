<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

final class OrdineMaterialeModel extends Model
{
    protected $table = 'ordini_materiale';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    
    protected $allowedFields = [
        'numero', 'data', 'oggetto', 'descrizione', 'id_anagrafica', 
        'id_referente', 'id_progetto', 'stato', 'id_utente_creatore',
        'data_invio', 'data_accettazione', 'data_consegna_prevista', 'data_consegna_effettiva',
        'note', 'condizioni_pagamento', 'id_condizione_pagamento', 'condizioni_consegna', 'id_offerta_fornitore',
        'importo_totale', 'sconto_totale', 'sconto_fisso', 'costo_trasporto', 'valuta'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'numero'            => 'required|min_length[1]|max_length[50]',
        'data'              => 'required|valid_date',
        'oggetto'           => 'required|min_length[3]|max_length[255]',
        'id_anagrafica'     => 'required|integer',
        'stato'             => 'required|in_list[bozza,in_attesa,inviato,confermato,in_consegna,consegnato,completato,annullato]',
        'id_utente_creatore' => 'required|integer'
    ];
    
    protected $validationMessages = [
        'numero' => [
            'required' => 'Il numero dell\'ordine è obbligatorio',
            'min_length' => 'Il numero deve essere lungo almeno {param} caratteri',
            'max_length' => 'Il numero non può superare {param} caratteri'
        ],
        'data' => [
            'required' => 'La data è obbligatoria',
            'valid_date' => 'La data deve essere valida'
        ],
        'oggetto' => [
            'required' => 'L\'oggetto dell\'ordine è obbligatorio',
            'min_length' => 'L\'oggetto deve essere lungo almeno {param} caratteri',
            'max_length' => 'L\'oggetto non può superare {param} caratteri'
        ],
        'id_anagrafica' => [
            'required' => 'Il fornitore è obbligatorio',
            'integer' => 'Il fornitore deve essere un valore numerico'
        ],
        'stato' => [
            'required' => 'Lo stato è obbligatorio',
            'in_list' => 'Lo stato deve essere uno tra: bozza, inviato, confermato, in_consegna, consegnato, annullato'
        ],
        'id_utente_creatore' => [
            'required' => 'L\'utente creatore è obbligatorio',
            'integer' => 'L\'utente creatore deve essere un valore numerico'
        ]
    ];
    
    protected $skipValidation = false;
    
    /**
     * Ottiene tutti gli ordini di materiale con le relazioni
     */
    public function getOrdiniWithRelations()
    {
        return $this->select('ordini_materiale.*, anagrafiche.ragione_sociale as nome_fornitore, 
                             utenti.nome as nome_utente, utenti.cognome as cognome_utente,
                             progetti.nome as nome_progetto,
                             contatti.nome as nome_referente, contatti.cognome as cognome_referente,
                             contatti.email as email_referente, contatti.telefono as telefono_referente')
            ->join('anagrafiche', 'anagrafiche.id = ordini_materiale.id_anagrafica', 'left')
            ->join('utenti', 'utenti.id = ordini_materiale.id_utente_creatore', 'left')
            ->join('progetti', 'progetti.id = ordini_materiale.id_progetto', 'left')
            ->join('contatti', 'contatti.id = ordini_materiale.id_referente', 'left')
            ->orderBy('ordini_materiale.data', 'DESC')
            ->findAll();
    }
    
    /**
     * Ottiene un specifico ordine di materiale con le relazioni
     */
    public function getOrdineWithRelations(int $id)
    {
        return $this->select('ordini_materiale.*, anagrafiche.ragione_sociale as nome_fornitore, 
                             anagrafiche.indirizzo, anagrafiche.cap, anagrafiche.citta, anagrafiche.nazione,
                             anagrafiche.partita_iva, anagrafiche.codice_fiscale, anagrafiche.email, anagrafiche.telefono,
                             utenti.nome as nome_utente, utenti.cognome as cognome_utente,
                             utenti.email as email_utente,
                             progetti.nome as nome_progetto,
                             contatti.nome as nome_referente, contatti.cognome as cognome_referente,
                             contatti.email as email_referente, contatti.telefono as telefono_referente')
            ->join('anagrafiche', 'anagrafiche.id = ordini_materiale.id_anagrafica', 'left')
            ->join('utenti', 'utenti.id = ordini_materiale.id_utente_creatore', 'left')
            ->join('progetti', 'progetti.id = ordini_materiale.id_progetto', 'left')
            ->join('contatti', 'contatti.id = ordini_materiale.id_referente', 'left')
            ->where('ordini_materiale.id', $id)
            ->first();
    }
    
    /**
     * Ottiene gli ordini per un fornitore specifico
     */
    public function getOrdiniByFornitore(int $idAnagrafica)
    {
        return $this->select('ordini_materiale.*, utenti.nome as nome_utente, utenti.cognome as cognome_utente')
            ->join('utenti', 'utenti.id = ordini_materiale.id_utente_creatore', 'left')
            ->where('ordini_materiale.id_anagrafica', $idAnagrafica)
            ->orderBy('ordini_materiale.data', 'DESC')
            ->findAll();
    }
    
    /**
     * Ottiene gli ordini per un progetto specifico
     */
    public function getOrdiniByProgetto(int $idProgetto)
    {
        return $this->select('ordini_materiale.*, anagrafiche.ragione_sociale as nome_fornitore')
            ->join('anagrafiche', 'anagrafiche.id = ordini_materiale.id_anagrafica', 'left')
            ->where('ordini_materiale.id_progetto', $idProgetto)
            ->orderBy('ordini_materiale.data', 'DESC')
            ->findAll();
    }
    
    /**
     * Ottiene gli ordini in un determinato stato
     */
    public function getOrdiniByStato(string $stato)
    {
        return $this->select('ordini_materiale.*, anagrafiche.ragione_sociale as nome_fornitore')
            ->join('anagrafiche', 'anagrafiche.id = ordini_materiale.id_anagrafica', 'left')
            ->where('ordini_materiale.stato', $stato)
            ->orderBy('ordini_materiale.data', 'DESC')
            ->findAll();
    }
    
    /**
     * Genera automaticamente un numero di ordine
     */
    public function generateNumeroOrdine(): string
    {
        $anno = date('Y');
        $prefix = 'ORD-' . $anno . '-';
        
        // Trova l'ultimo numero di ordine per quest'anno
        $ultimoOrdine = $this->select('numero')
            ->like('numero', $prefix, 'after')
            ->orderBy('id', 'DESC')
            ->first();
        
        if (!$ultimoOrdine) {
            // Primo ordine dell'anno
            return $prefix . '001';
        }
        
        // Estrae il numero dalla stringa ORD-YYYY-XXX
        $numero = (int) substr($ultimoOrdine['numero'], strlen($prefix));
        $numero++;
        
        // Formato con leading zeros (es. 001, 002, ecc.)
        return $prefix . sprintf('%03d', $numero);
    }
    
    /**
     * Ottiene gli ordini in attesa di conferma (stato "inviato")
     * con i dati del fornitore
     *
     * @param int $limit Numero massimo di risultati da restituire
     * @return array
     */
    public function getOrdiniInAttesa(int $limit = 5): array
    {
        return $this->select('ordini_materiale.*, anagrafiche.ragione_sociale')
            ->join('anagrafiche', 'anagrafiche.id = ordini_materiale.id_anagrafica', 'left')
            ->where('ordini_materiale.stato', 'inviato')
            ->orderBy('ordini_materiale.data_invio', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Ottiene gli ordini in scadenza (consegna prevista entro una settimana)
     *
     * @param int $limit Numero massimo di risultati da restituire
     * @return array
     */
    public function getOrdiniInScadenza(int $limit = 5): array
    {
        $oggi = date('Y-m-d');
        $traUnaSettimana = date('Y-m-d', strtotime('+7 days'));
        
        return $this->select('ordini_materiale.*, anagrafiche.ragione_sociale')
            ->join('anagrafiche', 'anagrafiche.id = ordini_materiale.id_anagrafica', 'left')
            ->where('ordini_materiale.stato', 'confermato')
            ->where('ordini_materiale.data_consegna_prevista >=', $oggi)
            ->where('ordini_materiale.data_consegna_prevista <=', $traUnaSettimana)
            ->orderBy('ordini_materiale.data_consegna_prevista', 'ASC')
            ->limit($limit)
            ->findAll();
    }
}
