<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

final class OffertaFornitoreVoceModel extends Model
{
    protected $table            = 'offerte_fornitore_voci';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_offerta_fornitore', 'id_materiale', 'codice', 'descrizione', 
        'quantita', 'prezzo_unitario', 'importo', 'unita_misura', 
        'sconto', 'note', 'id_progetto', 'id_richiesta_materiale'
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
    protected $validationRules      = [
        'id_offerta_fornitore' => 'required|integer',
        'descrizione'          => 'required',
        'quantita'             => 'required|numeric|greater_than[0]'
    ];
    protected $validationMessages   = [
        'id_offerta_fornitore' => [
            'required' => 'L\'offerta fornitore è obbligatoria',
            'integer'  => 'L\'offerta fornitore deve essere un valore numerico'
        ],
        'descrizione' => [
            'required' => 'La descrizione è obbligatoria'
        ],
        'quantita' => [
            'required' => 'La quantità è obbligatoria',
            'numeric'  => 'La quantità deve essere un valore numerico',
            'greater_than' => 'La quantità deve essere maggiore di zero'
        ]
    ];
    protected $skipValidation       = false;
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
     * Ottiene tutte le voci di una specifica offerta fornitore con i dettagli del materiale
     */
    public function getVociByOfferta(int $idOfferta)
    {
        return $this->select('offerte_fornitore_voci.*, materiali.descrizione as nome_materiale, materiali.codice as codice_materiale, progetti.nome as nome_progetto')
            ->join('materiali', 'materiali.id = offerte_fornitore_voci.id_materiale', 'left')
            ->join('progetti', 'progetti.id = offerte_fornitore_voci.id_progetto', 'left')
            ->where('offerte_fornitore_voci.id_offerta_fornitore', $idOfferta)
            ->orderBy('offerte_fornitore_voci.id', 'ASC')
            ->findAll();
    }

    /**
     * Verifica se un materiale è già presente nell'offerta
     */
    public function esisteMateriale(int $idOfferta, int $idMateriale): bool
    {
        $count = $this->where('id_offerta_fornitore', $idOfferta)
                      ->where('id_materiale', $idMateriale)
                      ->countAllResults();
                      
        return $count > 0;
    }

    /**
     * Calcola l'importo di una voce (prezzo_unitario * quantita - sconto)
     */
    public function calcolaImporto(float $prezzoUnitario, float $quantita, float $sconto = 0): float
    {
        $importoLordo = $prezzoUnitario * $quantita;
        $importoSconto = ($importoLordo * $sconto) / 100;
        return $importoLordo - $importoSconto;
    }

    /**
     * Aggiorna automaticamente l'importo di una voce e l'importo totale dell'offerta
     */
    public function aggiornaImporti(int $idVoce): bool
    {
        $voce = $this->find($idVoce);
        
        if (!$voce) {
            return false;
        }
        
        // Calcola l'importo della voce
        $importo = $this->calcolaImporto(
            (float)$voce['prezzo_unitario'], 
            (float)$voce['quantita'], 
            (float)$voce['sconto']
        );
        
        // Aggiorna l'importo della voce
        $updated = $this->update($idVoce, ['importo' => $importo]);
        
        if ($updated) {
            // Aggiorna l'importo totale dell'offerta
            $offertaModel = new OffertaFornitoreModel();
            $offertaModel->aggiornaImportoTotale((int)$voce['id_offerta_fornitore']);
        }
        
        return $updated;
    }

    /**
     * Importa le voci selezionate da una richiesta d'offerta
     */
    public function importaVociDaRichiesta(int $idOfferta, int $idRichiesta, array $vociSelezionate = []): array
    {
        $db = db_connect();
        
        // Ottieni le voci dalla richiesta
        $richiestaMaterialeModel = new \App\Models\RichiestaMaterialeModel();
        $vociRichiesta = $richiestaMaterialeModel->getMaterialiByRichiesta($idRichiesta);
        
        $vociImportate = [];
        $errori = [];
        
        foreach ($vociRichiesta as $voce) {
            // Se ci sono voci selezionate, verifica che questa voce sia selezionata
            if (!empty($vociSelezionate) && !in_array($voce['id'], $vociSelezionate)) {
                continue; // Salta questa voce se non è selezionata
            }
            
            // Verifica se il materiale è già presente
            if ($this->esisteMateriale($idOfferta, (int)$voce['id_materiale'])) {
                $errori[] = "Il materiale {$voce['codice']} - {$voce['descrizione']} è già presente nell'offerta";
                continue;
            }
            
            // Prepara i dati per la nuova voce
            $data = [
                'id_offerta_fornitore' => $idOfferta,
                'id_materiale' => $voce['id_materiale'],
                'codice' => $voce['codice'],
                'descrizione' => $voce['descrizione'],
                'quantita' => $voce['quantita'],
                'unita_misura' => $voce['unita_misura'],
                'id_progetto' => $voce['id_progetto'],
                'id_richiesta_materiale' => $voce['id']
            ];
            
            // Inserisci la voce
            if ($this->insert($data)) {
                $vociImportate[] = $data;
            } else {
                $errori[] = "Errore nell'importazione del materiale {$voce['codice']} - {$voce['descrizione']}";
            }
        }
        
        return [
            'importate' => $vociImportate,
            'errori' => $errori
        ];
    }
}
