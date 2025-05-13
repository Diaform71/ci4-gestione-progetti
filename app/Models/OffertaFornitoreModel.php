<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

final class OffertaFornitoreModel extends Model
{
    protected $table            = 'offerte_fornitore';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'numero', 'data', 'oggetto', 'descrizione', 'id_anagrafica', 
        'id_referente', 'id_richiesta_offerta', 'id_progetto', 'stato', 
        'id_utente_creatore', 'data_ricezione', 'data_approvazione', 
        'importo_totale', 'valuta', 'note', 'sconto_totale', 'sconto_fisso', 'costo_trasporto'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'numero'            => 'required|min_length[1]|max_length[50]',
        'data'              => 'required|valid_date',
        'oggetto'           => 'required|min_length[3]|max_length[255]',
        'id_anagrafica'     => 'required|integer',
        'stato'             => 'required|in_list[ricevuta,in_valutazione,approvata,rifiutata,scaduta]',
        'id_utente_creatore' => 'required|integer'
    ];
    protected $validationMessages = [
        'numero' => [
            'required' => 'Il numero dell\'offerta è obbligatorio',
            'min_length' => 'Il numero deve essere lungo almeno {param} caratteri',
            'max_length' => 'Il numero non può superare {param} caratteri'
        ],
        'data' => [
            'required' => 'La data è obbligatoria',
            'valid_date' => 'La data deve essere valida'
        ],
        'oggetto' => [
            'required' => 'L\'oggetto dell\'offerta è obbligatorio',
            'min_length' => 'L\'oggetto deve essere lungo almeno {param} caratteri',
            'max_length' => 'L\'oggetto non può superare {param} caratteri'
        ],
        'id_anagrafica' => [
            'required' => 'Il fornitore è obbligatorio',
            'integer' => 'Il fornitore deve essere un valore numerico'
        ],
        'stato' => [
            'required' => 'Lo stato è obbligatorio',
            'in_list' => 'Lo stato deve essere uno tra: ricevuta, in_valutazione, approvata, rifiutata, scaduta'
        ],
        'id_utente_creatore' => [
            'required' => 'L\'utente creatore è obbligatorio',
            'integer' => 'L\'utente creatore deve essere un valore numerico'
        ]
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Ottiene tutte le offerte fornitore con le relazioni
     */
    public function getOfferteWithRelations()
    {
        return $this->select('offerte_fornitore.*, anagrafiche.ragione_sociale as nome_fornitore, 
                             utenti.nome as nome_utente, utenti.cognome as cognome_utente,
                             progetti.nome as nome_progetto,
                             richieste_offerta.numero as numero_rdo')
            ->join('anagrafiche', 'anagrafiche.id = offerte_fornitore.id_anagrafica', 'left')
            ->join('utenti', 'utenti.id = offerte_fornitore.id_utente_creatore', 'left')
            ->join('progetti', 'progetti.id = offerte_fornitore.id_progetto', 'left')
            ->join('richieste_offerta', 'richieste_offerta.id = offerte_fornitore.id_richiesta_offerta', 'left')
            ->orderBy('offerte_fornitore.data', 'DESC')
            ->findAll();
    }
    
    /**
     * Ottiene una specifica offerta fornitore con le relazioni
     */
    public function getOffertaWithRelations(int $id)
    {
        return $this->select('offerte_fornitore.*, anagrafiche.ragione_sociale as nome_fornitore, 
                             anagrafiche.indirizzo, anagrafiche.cap, anagrafiche.citta, anagrafiche.nazione,
                             anagrafiche.partita_iva, anagrafiche.codice_fiscale, anagrafiche.email, anagrafiche.telefono,
                             utenti.nome as nome_utente, utenti.cognome as cognome_utente,
                             utenti.email as email_utente,
                             progetti.nome as nome_progetto,
                             richieste_offerta.numero as numero_rdo, richieste_offerta.oggetto as oggetto_rdo,
                             contatti.nome as nome_referente, contatti.cognome as cognome_referente,
                             contatti.email as email_referente, contatti.telefono as telefono_referente')
            ->join('anagrafiche', 'anagrafiche.id = offerte_fornitore.id_anagrafica', 'left')
            ->join('utenti', 'utenti.id = offerte_fornitore.id_utente_creatore', 'left')
            ->join('progetti', 'progetti.id = offerte_fornitore.id_progetto', 'left')
            ->join('richieste_offerta', 'richieste_offerta.id = offerte_fornitore.id_richiesta_offerta', 'left')
            ->join('contatti', 'contatti.id = offerte_fornitore.id_referente', 'left')
            ->where('offerte_fornitore.id', $id)
            ->first();
    }
    
    /**
     * Ottiene le offerte fornitore per un fornitore specifico
     */
    public function getOfferteByFornitore(int $idAnagrafica)
    {
        return $this->select('offerte_fornitore.*, utenti.nome as nome_utente, utenti.cognome as cognome_utente')
            ->join('utenti', 'utenti.id = offerte_fornitore.id_utente_creatore', 'left')
            ->where('offerte_fornitore.id_anagrafica', $idAnagrafica)
            ->orderBy('offerte_fornitore.data', 'DESC')
            ->findAll();
    }
    
    /**
     * Ottiene le offerte fornitore per un progetto specifico
     */
    public function getOfferteByProgetto(int $idProgetto)
    {
        return $this->select('offerte_fornitore.*, anagrafiche.ragione_sociale as nome_fornitore')
            ->join('anagrafiche', 'anagrafiche.id = offerte_fornitore.id_anagrafica', 'left')
            ->where('offerte_fornitore.id_progetto', $idProgetto)
            ->orderBy('offerte_fornitore.data', 'DESC')
            ->findAll();
    }
    
    /**
     * Ottiene le offerte fornitore per una richiesta d'offerta specifica
     */
    public function getOfferteByRichiestaOfferta(int $idRichiestaOfferta)
    {
        return $this->select('offerte_fornitore.*, anagrafiche.ragione_sociale as nome_fornitore')
            ->join('anagrafiche', 'anagrafiche.id = offerte_fornitore.id_anagrafica', 'left')
            ->where('offerte_fornitore.id_richiesta_offerta', $idRichiestaOfferta)
            ->orderBy('offerte_fornitore.data', 'DESC')
            ->findAll();
    }
    
    /**
     * Ottiene le offerte fornitore in un determinato stato
     */
    public function getOfferteByStato(string $stato)
    {
        return $this->select('offerte_fornitore.*, anagrafiche.ragione_sociale as nome_fornitore')
            ->join('anagrafiche', 'anagrafiche.id = offerte_fornitore.id_anagrafica', 'left')
            ->where('offerte_fornitore.stato', $stato)
            ->orderBy('offerte_fornitore.data', 'DESC')
            ->findAll();
    }
    
    /**
     * Genera automaticamente un numero di offerta
     */
    public function generateNumeroOfferta(): string
    {
        $anno = date('Y');
        $prefix = 'ODF-' . $anno . '-';
        
        // Trova l'ultimo numero di offerta per quest'anno
        $ultimaOfferta = $this->select('numero')
            ->like('numero', $prefix, 'after')
            ->orderBy('id', 'DESC')
            ->first();
        
        if (!$ultimaOfferta) {
            // Prima offerta dell'anno
            return $prefix . '001';
        }
        
        // Estrae il numero dalla stringa ODF-YYYY-XXX
        $numero = (int) substr($ultimaOfferta['numero'], strlen($prefix));
        $numero++;
        
        // Formato con leading zeros (es. 001, 002, ecc.)
        return $prefix . sprintf('%03d', $numero);
    }
    
    /**
     * Calcola l'importo totale delle voci di un'offerta
     */
    public function calcolaImportoTotale(int $idOfferta): float
    {
        $db = db_connect();
        
        // Ottieni la somma degli importi delle voci
        $result = $db->table('offerte_fornitore_voci')
                    ->selectSum('importo')
                    ->where('id_offerta_fornitore', $idOfferta)
                    ->get()
                    ->getRow();
        
        $importoVoci = round((float)($result->importo ?? 0), 2);
        
        // Ottieni i dati dell'offerta per sconti e costo trasporto
        $offerta = $this->select('sconto_totale, sconto_fisso, costo_trasporto')
                       ->where('id', $idOfferta)
                       ->first();
        
        $scontoTotale = round((float)($offerta['sconto_totale'] ?? 0), 2);
        $scontoFisso = round((float)($offerta['sconto_fisso'] ?? 0), 2);
        $costoTrasporto = round((float)($offerta['costo_trasporto'] ?? 0), 2);
        
        // Calcola l'importo totale considerando:
        // 1. Importo voci
        // 2. Sconto percentuale
        // 3. Sconto fisso
        // 4. Costo trasporto
        
        // Applica lo sconto percentuale
        $importoScontatoPerc = round($importoVoci * (1 - ($scontoTotale / 100)), 2);
        
        // Applica lo sconto fisso
        $importoScontato = round($importoScontatoPerc - $scontoFisso, 2);
        
        // Se l'importo risulta negativo dopo gli sconti, imposta a zero
        if ($importoScontato < 0) {
            $importoScontato = 0;
        }
        
        // Aggiungi il costo di trasporto
        $importoFinale = round($importoScontato + $costoTrasporto, 2);
        
        return $importoFinale;
    }
    
    /**
     * Aggiorna l'importo totale dell'offerta
     */
    public function aggiornaImportoTotale(int $idOfferta): bool
    {
        $importoTotale = $this->calcolaImportoTotale($idOfferta);
        
        return $this->update($idOfferta, ['importo_totale' => $importoTotale]);
    }
    
    /**
     * Ottiene le offerte in attesa di valutazione (stato "ricevuta" o "in_valutazione")
     *
     * @param int $limit Numero massimo di risultati da restituire
     * @return array
     */
    public function getOfferteInValutazione(int $limit = 5): array
    {
        return $this->select('offerte_fornitore.*, anagrafiche.ragione_sociale')
            ->join('anagrafiche', 'anagrafiche.id = offerte_fornitore.id_anagrafica', 'left')
            ->whereIn('offerte_fornitore.stato', ['ricevuta', 'in_valutazione'])
            ->orderBy('offerte_fornitore.data_ricezione', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}
