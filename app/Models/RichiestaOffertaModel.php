<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

final class RichiestaOffertaModel extends Model
{
    protected $table = 'richieste_offerta';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    
    protected $allowedFields = [
        'numero', 'data', 'oggetto', 'descrizione', 'id_anagrafica', 
        'id_referente', 'id_progetto', 'stato', 'id_utente_creatore',
        'data_invio', 'data_accettazione', 'note'
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
        'stato'             => 'required|in_list[bozza,inviata,accettata,rifiutata,annullata]',
        'id_utente_creatore' => 'required|integer'
    ];
    
    protected $validationMessages = [
        'numero' => [
            'required' => 'Il numero della richiesta è obbligatorio',
            'min_length' => 'Il numero deve essere lungo almeno {param} caratteri',
            'max_length' => 'Il numero non può superare {param} caratteri'
        ],
        'data' => [
            'required' => 'La data è obbligatoria',
            'valid_date' => 'La data deve essere valida'
        ],
        'oggetto' => [
            'required' => 'L\'oggetto della richiesta è obbligatorio',
            'min_length' => 'L\'oggetto deve essere lungo almeno {param} caratteri',
            'max_length' => 'L\'oggetto non può superare {param} caratteri'
        ],
        'id_anagrafica' => [
            'required' => 'Il fornitore è obbligatorio',
            'integer' => 'Il fornitore deve essere un valore numerico'
        ],
        'stato' => [
            'required' => 'Lo stato è obbligatorio',
            'in_list' => 'Lo stato deve essere uno tra: bozza, inviata, accettata, rifiutata, annullata'
        ],
        'id_utente_creatore' => [
            'required' => 'L\'utente creatore è obbligatorio',
            'integer' => 'L\'utente creatore deve essere un valore numerico'
        ]
    ];
    
    protected $skipValidation = false;
    
    /**
     * Ottiene tutte le richieste d'offerta con le relazioni
     */
    public function getRichiesteWithRelations()
    {
        return $this->select('richieste_offerta.*, anagrafiche.ragione_sociale as nome_fornitore, 
                             utenti.nome as nome_utente, utenti.cognome as cognome_utente,
                             progetti.nome as nome_progetto')
            ->join('anagrafiche', 'anagrafiche.id = richieste_offerta.id_anagrafica', 'left')
            ->join('utenti', 'utenti.id = richieste_offerta.id_utente_creatore', 'left')
            ->join('progetti', 'progetti.id = richieste_offerta.id_progetto', 'left')
            ->orderBy('richieste_offerta.data', 'DESC')
            ->findAll();
    }
    
    /**
     * Ottiene una specifica richiesta d'offerta con le relazioni
     */
    public function getRichiestaWithRelations(int $id)
    {
        return $this->select('richieste_offerta.*, anagrafiche.ragione_sociale as nome_fornitore, 
                             anagrafiche.indirizzo, anagrafiche.cap, anagrafiche.citta, anagrafiche.nazione,
                             anagrafiche.partita_iva, anagrafiche.codice_fiscale, anagrafiche.email, anagrafiche.telefono,
                             utenti.nome as nome_utente, utenti.cognome as cognome_utente,
                             utenti.email as email_utente,
                             progetti.nome as nome_progetto,
                             contatti.nome as nome_referente, contatti.cognome as cognome_referente,
                             contatti.email as email_referente, contatti.telefono as telefono_referente')
            ->join('anagrafiche', 'anagrafiche.id = richieste_offerta.id_anagrafica', 'left')
            ->join('utenti', 'utenti.id = richieste_offerta.id_utente_creatore', 'left')
            ->join('progetti', 'progetti.id = richieste_offerta.id_progetto', 'left')
            ->join('contatti', 'contatti.id = richieste_offerta.id_referente', 'left')
            ->where('richieste_offerta.id', $id)
            ->first();
    }
    
    /**
     * Ottiene le richieste d'offerta per un fornitore specifico
     */
    public function getRichiesteByFornitore(int $idAnagrafica)
    {
        return $this->select('richieste_offerta.*, utenti.nome as nome_utente, utenti.cognome as cognome_utente')
            ->join('utenti', 'utenti.id = richieste_offerta.id_utente_creatore', 'left')
            ->where('richieste_offerta.id_anagrafica', $idAnagrafica)
            ->orderBy('richieste_offerta.data', 'DESC')
            ->findAll();
    }
    
    /**
     * Ottiene le richieste d'offerta per un progetto specifico
     */
    public function getRichiesteByProgetto(int $idProgetto)
    {
        return $this->select('richieste_offerta.*, anagrafiche.ragione_sociale as nome_fornitore')
            ->join('anagrafiche', 'anagrafiche.id = richieste_offerta.id_anagrafica', 'left')
            ->where('richieste_offerta.id_progetto', $idProgetto)
            ->orderBy('richieste_offerta.data', 'DESC')
            ->findAll();
    }
    
    /**
     * Ottiene le richieste d'offerta in un determinato stato
     */
    public function getRichiesteByStato(string $stato)
    {
        return $this->select('richieste_offerta.*, anagrafiche.ragione_sociale as nome_fornitore')
            ->join('anagrafiche', 'anagrafiche.id = richieste_offerta.id_anagrafica', 'left')
            ->where('richieste_offerta.stato', $stato)
            ->orderBy('richieste_offerta.data', 'DESC')
            ->findAll();
    }
    
    /**
     * Genera automaticamente un numero di richiesta
     */
    public function generateNumeroRichiesta(): string
    {
        $anno = date('Y');
        $prefix = 'RDO-' . $anno . '-';
        
        // Trova l'ultimo numero di richiesta per quest'anno
        $ultimaRichiesta = $this->select('numero')
            ->like('numero', $prefix, 'after')
            ->orderBy('id', 'DESC')
            ->first();
        
        if (!$ultimaRichiesta) {
            // Prima richiesta dell'anno
            return $prefix . '001';
        }
        
        // Estrae il numero dalla stringa RDO-YYYY-XXX
        $numero = (int) substr($ultimaRichiesta['numero'], strlen($prefix));
        $numero++;
        
        // Formato con leading zeros (es. 001, 002, ecc.)
        return $prefix . sprintf('%03d', $numero);
    }
    
    /**
     * Ottiene le richieste d'offerta in attesa di risposta (stato "inviata")
     * con i dati del fornitore
     *
     * @param int $limit Numero massimo di risultati da restituire
     * @return array
     */
    public function getRichiesteInAttesa(int $limit = 5): array
    {
        return $this->select('richieste_offerta.*, anagrafiche.ragione_sociale')
            ->join('anagrafiche', 'anagrafiche.id = richieste_offerta.id_anagrafica', 'left')
            ->where('richieste_offerta.stato', 'inviata')
            ->orderBy('richieste_offerta.data_invio', 'DESC')
            ->limit($limit)
            ->findAll();
    }
} 