<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

final class OrdineMaterialeVoceModel extends Model
{
    protected $table = 'ordini_materiale_voci';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'id_ordine', 'id_materiale', 'codice', 'descrizione', 'quantita', 
        'prezzo_unitario', 'importo', 'unita_misura', 'sconto', 
        'id_progetto', 'id_offerta_voce', 'note', 'data_consegna_prevista'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'id_ordine' => 'required|integer',
        'descrizione' => 'required',
        'quantita' => 'required|numeric|greater_than[0]'
    ];
    
    protected $validationMessages = [
        'id_ordine' => [
            'required' => 'L\'ordine è obbligatorio',
            'integer' => 'L\'ordine deve essere un valore numerico'
        ],
        'descrizione' => [
            'required' => 'La descrizione è obbligatoria'
        ],
        'quantita' => [
            'required' => 'La quantità è obbligatoria',
            'numeric' => 'La quantità deve essere un valore numerico',
            'greater_than' => 'La quantità deve essere maggiore di zero'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Ottiene tutte le voci di un specifico ordine con i dettagli del materiale e progetto
     */
    public function getVociByOrdine(int $idOrdine)
    {
        return $this->select('ordini_materiale_voci.*, 
                              materiali.descrizione as nome_materiale, 
                              materiali.codice as codice_materiale, 
                              progetti.nome as nome_progetto')
            ->join('materiali', 'materiali.id = ordini_materiale_voci.id_materiale', 'left')
            ->join('progetti', 'progetti.id = ordini_materiale_voci.id_progetto', 'left')
            ->where('ordini_materiale_voci.id_ordine', $idOrdine)
            ->orderBy('ordini_materiale_voci.id', 'ASC')
            ->findAll();
    }

    /**
     * Verifica se un materiale è già presente nell'ordine
     */
    public function esisteMateriale(int $idOrdine, int $idMateriale): bool
    {
        $count = $this->where('id_ordine', $idOrdine)
                      ->where('id_materiale', $idMateriale)
                      ->where('deleted_at IS NULL')
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
     * Aggiorna automaticamente l'importo di una voce
     */
    public function aggiornaImporti(int $idVoce): bool
    {
        $voce = $this->find($idVoce);
        
        if (!$voce) {
            return false;
        }
        
        // Calcola il nuovo importo
        $importo = $this->calcolaImporto(
            (float)$voce['prezzo_unitario'],
            (float)$voce['quantita'],
            (float)($voce['sconto'] ?? 0)
        );
        
        // Aggiorna l'importo della voce
        $this->update($idVoce, ['importo' => $importo]);
        
        return true;
    }

    /**
     * Importa voci da un'offerta fornitore
     */
    public function importaVociOfferta(int $idOrdine, int $idOfferta, array $vociSelezionate = []): array
    {
        // Ottieni le voci dall'offerta
        $offertaVoceModel = new \App\Models\OffertaFornitoreVoceModel();
        $vociOfferta = $offertaVoceModel->getVociByOfferta($idOfferta);
        
        $vociImportate = [];
        $errori = [];
        
        foreach ($vociOfferta as $voce) {
            // Se ci sono voci selezionate, verifica che questa voce sia selezionata
            if (!empty($vociSelezionate) && !in_array($voce['id'], $vociSelezionate)) {
                continue; // Salta questa voce se non è selezionata
            }
            
            // Verifica se il materiale è già presente nell'ordine
            if ($this->esisteMateriale($idOrdine, (int)$voce['id_materiale'])) {
                $errori[] = "Il materiale {$voce['codice']} - {$voce['descrizione']} è già presente nell'ordine";
                continue;
            }
            
            // Prepara i dati per la nuova voce
            $data = [
                'id_ordine' => $idOrdine,
                'id_materiale' => $voce['id_materiale'],
                'codice' => $voce['codice'] ?? $voce['codice_materiale'],
                'descrizione' => $voce['descrizione'],
                'quantita' => $voce['quantita'],
                'prezzo_unitario' => $voce['prezzo_unitario'],
                'importo' => $voce['importo'],
                'unita_misura' => $voce['unita_misura'],
                'sconto' => $voce['sconto'],
                'id_progetto' => $voce['id_progetto'],
                'id_offerta_voce' => $voce['id']
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

    /**
     * Calcola il totale dell'ordine
     */
    public function calcolaTotaleOrdine(int $idOrdine): float
    {
        $result = $this->selectSum('importo')
                       ->where('id_ordine', $idOrdine)
                       ->where('deleted_at IS NULL')
                       ->get()
                       ->getRowArray();
                       
        return (float)($result['importo'] ?? 0);
    }
} 