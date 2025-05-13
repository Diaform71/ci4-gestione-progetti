<?php

namespace App\Controllers;

use App\Models\ProgettoModel;
use App\Models\AttivitaModel;
use App\Models\ScadenzaModel;
use App\Models\RichiestaOffertaModel;
use App\Models\AnagraficaModel;

class Home extends BaseController
{
    public function index()
    {
        // Inizializza i modelli
        $progettoModel = new ProgettoModel();
        $attivitaModel = new AttivitaModel();
        $scadenzaModel = new ScadenzaModel();
        $richiestaOffertaModel = new RichiestaOffertaModel();
        $anagraficaModel = new AnagraficaModel();
        
        // Recupera l'ID dell'utente corrente
        $idUtente = session()->get('utente_id');
        $isAdmin = session()->get('is_admin') ?? false;
        
        // Recupera gli ultimi 5 progetti inseriti ancora da completare
        $progetti = $progettoModel->where('stato !=', 'completato')
                                 ->where('attivo', 1)
                                 ->orderBy('created_at', 'DESC')
                                 ->limit(5)
                                 ->findAll();
        
        // Recupera le ultime 5 attività ancora da completare
        $query = $attivitaModel->where('completata', 0)
                              ->orderBy('data_scadenza', 'ASC')
                              ->orderBy('priorita', 'DESC');
        
        // Se non è admin, filtra per l'utente attuale
        if (!$isAdmin && $idUtente) {
            $query->where('id_utente_assegnato', $idUtente);
        }
        
        $attivita = $query->limit(5)->findAll();
        
        // Recupera le prossime 5 scadenze
        $scadenze = $scadenzaModel->getScadenzeInArrivo(15, $isAdmin ? null : $idUtente);
        $scadenze = array_slice($scadenze, 0, 5);
        
        // Recupera le ultime 5 richieste d'offerta in attesa di risposta
        $richiesteOfferta = $richiestaOffertaModel->getRichiesteInAttesa(5);
        
        // Carica i dati anagrafici per ogni richiesta di offerta
        foreach ($richiesteOfferta as &$richiesta) {
            if (!empty($richiesta['id_anagrafica']) && !isset($richiesta['ragione_sociale'])) {
                $anagrafica = $anagraficaModel->find($richiesta['id_anagrafica']);
                if ($anagrafica) {
                    $richiesta['ragione_sociale'] = $anagrafica['ragione_sociale'];
                }
            }
        }
        
        // Passa i dati alla vista
        $data = [
            'progetti' => $progetti,
            'attivita' => $attivita,
            'scadenze' => $scadenze,
            'richiesteOfferta' => $richiesteOfferta
        ];
                
        return view('dashboard', $data);
    }
}
