<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AnagraficaContattoModel;
use App\Models\AnagraficaModel;
use App\Models\ContattoModel;
use CodeIgniter\HTTP\ResponseInterface;

final class AnagraficheContatti extends BaseController
{
    protected AnagraficaContattoModel $model;
    protected AnagraficaModel $anagraficaModel;
    protected ContattoModel $contattoModel;

    public function __construct()
    {
        $this->model = model(AnagraficaContattoModel::class);
        $this->anagraficaModel = model(AnagraficaModel::class);
        $this->contattoModel = model(ContattoModel::class);
    }

    /**
     * Ottiene tutti i contatti associati ad un'anagrafica
     */
    public function getByAnagrafica(int $id_anagrafica): ResponseInterface
    {
        try {
            // Verifica che l'anagrafica esista
            if (!$this->anagraficaModel->find($id_anagrafica)) {
                return $this->response->setStatusCode(404)
                    ->setJSON(['success' => false, 'message' => 'Anagrafica non trovata']);
            }

            $contatti = $this->model->getContattiByAnagrafica($id_anagrafica);
            return $this->response->setJSON([
                'success' => true,
                'data' => $contatti
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)
                ->setJSON(['success' => false, 'message' => 'Errore nel recuperare i contatti: ' . $e->getMessage()]);
        }
    }

    /**
     * Ottiene tutte le anagrafiche associate ad un contatto
     */
    public function getByContatto(int $id_contatto): ResponseInterface
    {
        try {
            // Verifica che il contatto esista
            if (!$this->contattoModel->find($id_contatto)) {
                return $this->response->setStatusCode(404)
                    ->setJSON(['success' => false, 'message' => 'Contatto non trovato']);
            }

            $anagrafiche = $this->model->getAnagraficheByContatto($id_contatto);
            return $this->response->setJSON([
                'success' => true,
                'data' => $anagrafiche
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)
                ->setJSON(['success' => false, 'message' => 'Errore nel recuperare le anagrafiche: ' . $e->getMessage()]);
        }
    }

    /**
     * Associa un contatto ad un'anagrafica
     */
    public function create(): ResponseInterface
    {
        try {
            $data = $this->request->getJSON(true);
            
            if (!$this->model->save($data)) {
                return $this->response->setStatusCode(400)
                    ->setJSON([
                        'success' => false, 
                        'message' => 'Errore nella validazione dei dati',
                        'errors' => $this->model->errors()
                    ]);
            }
            
            // Imposta come principale se richiesto o se è l'unico contatto
            if (isset($data['principale']) && $data['principale']) {
                $this->model->setPrincipale((int)$data['id_anagrafica'], (int)$data['id_contatto']);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Contatto associato all\'anagrafica con successo',
                'id' => $this->model->getInsertID()
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)
                ->setJSON(['success' => false, 'message' => 'Errore nell\'associare il contatto: ' . $e->getMessage()]);
        }
    }

    /**
     * Aggiorna l'associazione tra un contatto e un'anagrafica
     */
    public function update(int $id): ResponseInterface
    {
        try {
            $data = $this->request->getJSON(true);
            
            // Verifica che l'associazione esista
            if (!$this->model->find($id)) {
                return $this->response->setStatusCode(404)
                    ->setJSON(['success' => false, 'message' => 'Associazione non trovata']);
            }
            
            // Aggiorna solo i campi consentiti
            $updateData = array_intersect_key($data, array_flip($this->model->allowedFields));
            $updateData['id'] = $id;
            
            if (!$this->model->save($updateData)) {
                return $this->response->setStatusCode(400)
                    ->setJSON([
                        'success' => false, 
                        'message' => 'Errore nella validazione dei dati',
                        'errors' => $this->model->errors()
                    ]);
            }
            
            // Imposta come principale se richiesto
            if (isset($data['principale']) && $data['principale']) {
                $relazione = $this->model->find($id);
                $this->model->setPrincipale($relazione['id_anagrafica'], $relazione['id_contatto']);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Associazione aggiornata con successo'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)
                ->setJSON(['success' => false, 'message' => 'Errore nell\'aggiornare l\'associazione: ' . $e->getMessage()]);
        }
    }

    /**
     * Imposta un contatto come principale per un'anagrafica
     */
    public function setPrincipale(int $id_anagrafica, int $id_contatto): ResponseInterface
    {
        try {
            // Verifica che l'anagrafica esista
            if (!$this->anagraficaModel->find($id_anagrafica)) {
                return $this->response->setStatusCode(404)
                    ->setJSON(['success' => false, 'message' => 'Anagrafica non trovata']);
            }
            
            // Verifica che il contatto esista
            if (!$this->contattoModel->find($id_contatto)) {
                return $this->response->setStatusCode(404)
                    ->setJSON(['success' => false, 'message' => 'Contatto non trovato']);
            }
            
            // Verifica che l'associazione esista
            $check = $this->model->where('id_anagrafica', $id_anagrafica)
                                 ->where('id_contatto', $id_contatto)
                                 ->first();
            
            if (!$check) {
                return $this->response->setStatusCode(404)
                    ->setJSON(['success' => false, 'message' => 'Associazione non trovata']);
            }
            
            if ($this->model->setPrincipale($id_anagrafica, $id_contatto)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Contatto impostato come principale'
                ]);
            } else {
                return $this->response->setStatusCode(500)
                    ->setJSON(['success' => false, 'message' => 'Errore nell\'impostare il contatto come principale']);
            }
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)
                ->setJSON(['success' => false, 'message' => 'Errore: ' . $e->getMessage()]);
        }
    }

    /**
     * Rimuove l'associazione tra un contatto e un'anagrafica
     */
    public function delete(int $id): ResponseInterface
    {
        try {
            // Verifica che l'associazione esista
            if (!$this->model->find($id)) {
                return $this->response->setStatusCode(404)
                    ->setJSON(['success' => false, 'message' => 'Associazione non trovata']);
            }
            
            if ($this->model->delete($id)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Associazione rimossa con successo'
                ]);
            } else {
                return $this->response->setStatusCode(500)
                    ->setJSON(['success' => false, 'message' => 'Errore nella rimozione dell\'associazione']);
            }
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)
                ->setJSON(['success' => false, 'message' => 'Errore: ' . $e->getMessage()]);
        }
    }

    /**
     * Ottiene tutte le anagrafiche per le select
     */
    public function getAnagrafiche(): ResponseInterface
    {
        try {
            $anagrafiche = $this->anagraficaModel->where('attivo', 1)
                                                ->select('id, ragione_sociale, citta')
                                                ->orderBy('ragione_sociale', 'ASC')
                                                ->findAll();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $anagrafiche
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)
                ->setJSON(['success' => false, 'message' => 'Errore nel recuperare le anagrafiche: ' . $e->getMessage()]);
        }
    }
}
