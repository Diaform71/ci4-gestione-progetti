<?php

namespace App\Controllers;

use App\Models\EmailTemplate;

class EmailTemplateController extends BaseController
{
    protected $emailTemplateModel;
    
    public function __construct()
    {
        $this->emailTemplateModel = new EmailTemplate();
        
        // Verifica che l'utente sia autenticato
        if (!session()->get('utente_id')) {
            return redirect()->to('/login');
        }
        
        // Verifica che l'utente sia admin
        if (!session()->get('is_admin')) {
            return redirect()->to('/dashboard')->with('error', 'Non hai i permessi per accedere a questa sezione');
        }
    }
    
    /**
     * Recupera un template email tramite il suo ID per le richieste AJAX
     */
    public function get($id = null)
    {
        // Verifica che l'ID sia fornito
        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID del template non specificato'
            ]);
        }
        
        // Recupera il template
        $template = $this->emailTemplateModel->find($id);
        
        // Verifica che il template esista
        if (!$template) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Template non trovato'
            ]);
        }
        
        // Ritorna il template come JSON
        return $this->response->setJSON([
            'success' => true,
            'data' => $template
        ]);
    }
    
    /**
     * Visualizza elenco dei template email
     */
    public function index()
    {
        $data = [
            'title' => 'Template Email',
            'templates' => $this->emailTemplateModel->findAll()
        ];
        
        return view('email_templates/index', $data);
    }
    
    /**
     * Mostra form per nuovo template
     */
    public function nuovo()
    {
        $data = [
            'title' => 'Nuovo Template Email',
            'tipi' => [
                EmailTemplate::TIPO_RDO => 'Richiesta di Offerta',
                EmailTemplate::TIPO_ORDINE => 'Ordine',
                EmailTemplate::TIPO_OFFERTA => 'Offerta'
            ],
            'validation' => \Config\Services::validation()
        ];
        
        return view('email_templates/form', $data);
    }
    
    /**
     * Salva un nuovo template o aggiorna uno esistente
     */
    public function salva()
    {
        // Validazione
        $rules = [
            'nome' => 'required|min_length[3]|max_length[255]',
            'oggetto' => 'required|min_length[3]|max_length[255]',
            'corpo' => 'required',
            'tipo' => 'required|in_list[RDO,ORDINE,OFFERTA]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Preparazione dati
        $data = [
            'nome' => $this->request->getPost('nome'),
            'oggetto' => $this->request->getPost('oggetto'),
            'corpo' => $this->request->getPost('corpo'),
            'tipo' => $this->request->getPost('tipo')
        ];
        
        // Controlla se c'è un ID (modifica di un template esistente)
        $id = $this->request->getPost('id');
        
        if (!empty($id)) {
            // Aggiorna template esistente
            if ($this->emailTemplateModel->update($id, $data)) {
                return redirect()->to('/email-templates')->with('success', 'Template aggiornato con successo');
            } else {
                return redirect()->back()->withInput()->with('error', 'Errore durante l\'aggiornamento del template');
            }
        } else {
            // Crea nuovo template
            if ($this->emailTemplateModel->insert($data)) {
                return redirect()->to('/email-templates')->with('success', 'Template creato con successo');
            } else {
                return redirect()->back()->withInput()->with('error', 'Errore durante la creazione del template');
            }
        }
    }
    
    /**
     * Visualizza form per modificare un template
     */
    public function modifica($id)
    {
        $template = $this->emailTemplateModel->find($id);
        
        if (!$template) {
            return redirect()->to('/email-templates')->with('error', 'Template non trovato');
        }
        
        $data = [
            'title' => 'Modifica Template Email',
            'template' => $template,
            'tipi' => [
                EmailTemplate::TIPO_RDO => 'Richiesta di Offerta',
                EmailTemplate::TIPO_ORDINE => 'Ordine',
                EmailTemplate::TIPO_OFFERTA => 'Offerta'
            ],
            'validation' => \Config\Services::validation()
        ];
        
        return view('email_templates/form', $data);
    }
    
    /**
     * Visualizza dettagli di un template
     */
    public function dettaglio($id)
    {
        $template = $this->emailTemplateModel->find($id);
        
        if (!$template) {
            return redirect()->to('/email-templates')->with('error', 'Template non trovato');
        }
        
        $data = [
            'title' => 'Dettaglio Template Email',
            'template' => $template
        ];
        
        return view('email_templates/dettaglio', $data);
    }
    
    /**
     * Elimina un template
     */
    public function elimina($id = null)
    {
        // Controlla che sia una richiesta POST con CSRF valido
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('/email-templates')->with('error', 'Metodo non consentito');
        }
        
        if (!$id) {
            return redirect()->to('/email-templates')->with('error', 'ID template non specificato');
        }
        
        $template = $this->emailTemplateModel->find($id);
        
        if (!$template) {
            return redirect()->to('/email-templates')->with('error', 'Template non trovato');
        }
        
        if ($this->emailTemplateModel->delete($id)) {
            return redirect()->to('/email-templates')->with('success', 'Template eliminato con successo');
        } else {
            return redirect()->to('/email-templates')->with('error', 'Errore durante l\'eliminazione del template');
        }
    }
    
    /**
     * Mostra anteprima del template con dati di esempio
     */
    public function anteprima($id)
    {
        $template = $this->emailTemplateModel->find($id);
        
        if (!$template) {
            return redirect()->to('/email-templates')->with('error', 'Template non trovato');
        }
        
        // Dati di esempio in base al tipo di template
        $datiEsempio = $this->getDatiEsempioPerTipo($template['tipo']);
        
        try {
            $templateCompilato = $this->emailTemplateModel->compilaTemplate($template, $datiEsempio);
            
            $data = [
                'title' => 'Anteprima Template Email',
                'template' => $template,
                'templateCompilato' => $templateCompilato,
                'datiEsempio' => $datiEsempio
            ];
            
            return view('email_templates/anteprima', $data);
        } catch (\Exception $e) {
            return redirect()->to('/email-templates')->with('error', 'Errore durante la generazione dell\'anteprima: ' . $e->getMessage());
        }
    }
    
    /**
     * Genera dati di esempio per i diversi tipi di template
     */
    private function getDatiEsempioPerTipo($tipo)
    {
        $datiComuni = [
            'azienda' => 'Azienda Demo S.p.A.',
            'cliente' => 'Cliente Demo S.r.l.',
            'data' => date('d/m/Y'),
            'riferimento' => 'RIF-' . date('Ymd'),
            'utente' => 'Mario Rossi'
        ];
        
        switch ($tipo) {
            case EmailTemplate::TIPO_RDO:
                return array_merge($datiComuni, [
                    'progetto' => 'Progetto Demo',
                    'scadenza' => date('d/m/Y', strtotime('+10 days')),
                    'descrizione' => 'Richiesta preventivo per il progetto Demo'
                ]);
                
            case EmailTemplate::TIPO_ORDINE:
                return array_merge($datiComuni, [
                    'numero_ordine' => 'ORD-' . date('Ymd'),
                    'totale' => '€ 1.250,00',
                    'materiali' => '<ul><li>Prodotto 1 - € 500,00</li><li>Prodotto 2 - € 750,00</li></ul>'
                ]);
                
            case EmailTemplate::TIPO_OFFERTA:
                return array_merge($datiComuni, [
                    'numero_offerta' => 'OFF-' . date('Ymd'),
                    'totale' => '€ 1.500,00',
                    'validita' => date('d/m/Y', strtotime('+30 days')),
                    'materiali' => '<ul><li>Servizio 1 - € 800,00</li><li>Servizio 2 - € 700,00</li></ul>'
                ]);
                
            default:
                return $datiComuni;
        }
    }
    
    /**
     * API per ottenere i template email in base al tipo
     */
    public function getByType($tipo = null)
    {
        // Controllo del tipo
        if (!$tipo || !in_array($tipo, [EmailTemplate::TIPO_RDO, EmailTemplate::TIPO_ORDINE, EmailTemplate::TIPO_OFFERTA])) {
            return $this->response->setJSON([]);
        }
        
        // Recupera i template del tipo specificato
        $templates = $this->emailTemplateModel
            ->where('tipo', $tipo)
            ->findAll();
        
        return $this->response->setJSON($templates);
    }
    
    /**
     * API per compilare un template con i dati di una richiesta specifica
     */
    public function compila($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID template non specificato'
            ]);
        }
        
        $template = $this->emailTemplateModel->find($id);
        
        if (!$template) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Template non trovato'
            ]);
        }
        
        // Ottieni l'ID della richiesta d'offerta
        $idRichiesta = $this->request->getPost('id_richiesta');
        
        if (!$idRichiesta) {
            // Se non è specificata la richiesta, usa dati di esempio
            $dati = $this->getDatiEsempioPerTipo($template['tipo']);
        } else {
            // Ottieni i dati effettivi della richiesta d'offerta
            $dati = $this->getDatiRichiestaOfferta((int)$idRichiesta);
        }
        
        try {
            $templateCompilato = $this->emailTemplateModel->compilaTemplate($template, $dati);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $templateCompilato
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Errore durante la compilazione del template: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * API per compilare un template con i dati di un ordine specifico
     */
    public function compilaOrdine($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID template non specificato'
            ]);
        }
        
        $template = $this->emailTemplateModel->find($id);
        
        if (!$template) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Template non trovato'
            ]);
        }
        
        // Ottieni l'ID dell'ordine
        $idOrdine = $this->request->getPost('id_ordine');
        
        if (!$idOrdine) {
            // Se non è specificato l'ordine, usa dati di esempio
            $dati = $this->getDatiEsempioPerTipo($template['tipo']);
        } else {
            // Ottieni i dati effettivi dell'ordine
            $dati = $this->getDatiOrdine((int)$idOrdine);
        }
        
        try {
            $templateCompilato = $this->emailTemplateModel->compilaTemplate($template, $dati);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $templateCompilato
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Errore durante la compilazione del template: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Recupera i dati di una richiesta d'offerta da utilizzare nel template
     */
    private function getDatiRichiestaOfferta($idRichiesta)
    {
        // Carica i modelli necessari
        $richiestaOffertaModel = new \App\Models\RichiestaOffertaModel();
        $richiestaMaterialeModel = new \App\Models\RichiestaMaterialeModel();
        
        // Ottieni i dati della richiesta
        $richiesta = $richiestaOffertaModel->getRichiestaWithRelations($idRichiesta);
        
        if (empty($richiesta)) {
            throw new \Exception('Richiesta d\'offerta non trovata');
        }
        
        // Ottieni i materiali della richiesta
        $materiali = $richiestaMaterialeModel->getMaterialiByRichiesta($idRichiesta);
        
        // Preparazione HTML dei materiali
        $materialiHtml = '';
        if (!empty($materiali)) {
            $materialiHtml = '<ul>';
            foreach ($materiali as $materiale) {
                $materialiHtml .= '<li>' . esc($materiale['codice']) . ' - ' . esc($materiale['descrizione']) . ' - ' . 
                                  esc($materiale['quantita']) . ' ' . esc($materiale['unita_misura']) . '</li>';
            }
            $materialiHtml .= '</ul>';
        } else {
            $materialiHtml = '<p>Nessun materiale associato a questa richiesta</p>';
        }
        
        // Prepara i dati per la sostituzione
        $dati = [
            'numero' => $richiesta['numero'],
            'data' => date('d/m/Y', strtotime($richiesta['data'])),
            'oggetto' => $richiesta['oggetto'],
            'descrizione' => $richiesta['descrizione'] ?? '',
            'fornitore' => $richiesta['nome_fornitore'] ?? '',
            'azienda' => $richiesta['nome_fornitore'] ?? '',
            'cliente' => !empty($richiesta['nome_referente']) ? $richiesta['nome_referente'] . ' ' . $richiesta['cognome_referente'] : '',
            'referente' => !empty($richiesta['nome_referente']) ? $richiesta['nome_referente'] . ' ' . $richiesta['cognome_referente'] : '',
            'utente' => $richiesta['nome_utente'] . ' ' . $richiesta['cognome_utente'],
            'email_utente' => $richiesta['email_utente'] ?? '',
            'progetto' => $richiesta['nome_progetto'] ?? '',
            'materiali' => $materialiHtml,
            'note' => $richiesta['note'] ?? ''
        ];
        
        return $dati;
    }
    
    /**
     * Recupera i dati di un ordine di materiale da utilizzare nel template
     */
    private function getDatiOrdine($idOrdine)
    {
        // Carica i modelli necessari
        $ordineMaterialeModel = new \App\Models\OrdineMaterialeModel();
        $ordineMaterialeVoceModel = new \App\Models\OrdineMaterialeVoceModel();
        
        // Ottieni i dati dell'ordine
        $ordine = $ordineMaterialeModel->getOrdineWithRelations((int)$idOrdine);
        
        if (empty($ordine)) {
            throw new \Exception('Ordine materiale non trovato');
        }
        
        // Debug dei dati dell'ordine
        log_message('debug', 'Dati ordine recuperati: ' . print_r($ordine, true));
        
        // Ottieni le voci dell'ordine
        $voci = $ordineMaterialeVoceModel->getVociByOrdine((int)$idOrdine);
        
        // Preparazione HTML delle voci
        $materialiHtml = '';
        if (!empty($voci)) {
            $materialiHtml = '<table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Codice</th>
                        <th>Descrizione</th>
                        <th>Quantità</th>
                        <th>Prezzo unitario</th>
                        <th>Importo</th>
                    </tr>
                </thead>
                <tbody>';
            foreach ($voci as $voce) {
                $prezzo = number_format((float)$voce['prezzo_unitario'], 2, ',', '.');
                $importo = number_format((float)$voce['importo'], 2, ',', '.');
                $materialiHtml .= '<tr>
                    <td>' . esc($voce['codice']) . '</td>
                    <td>' . esc($voce['descrizione']) . '</td>
                    <td>' . esc($voce['quantita']) . ' ' . esc($voce['unita_misura']) . '</td>
                    <td>€ ' . $prezzo . '</td>
                    <td>€ ' . $importo . '</td>
                </tr>';
            }
            $materialiHtml .= '</tbody></table>';
        } else {
            $materialiHtml = '<p>Nessun materiale associato a questo ordine</p>';
        }
        
        // Formatta l'importo totale
        $importoTotale = number_format((float)$ordine['importo_totale'], 2, ',', '.');
        
        // Prepara i dati per la sostituzione
        $dati = [
            'numero_ordine' => $ordine['numero'],
            'data' => date('d/m/Y', strtotime($ordine['data'])),
            'data_ordine' => date('d/m/Y', strtotime($ordine['data'])),
            'oggetto' => $ordine['oggetto'],
            'descrizione' => $ordine['descrizione'] ?? '',
            'cliente' => !empty($ordine['nome_referente']) ? $ordine['nome_referente'] . ' ' . $ordine['cognome_referente'] : $ordine['nome_fornitore'],
            'referente' => !empty($ordine['nome_referente']) ? $ordine['nome_referente'] . ' ' . $ordine['cognome_referente'] : $ordine['nome_fornitore'],
            'nome_referente' => !empty($ordine['nome_referente']) ? $ordine['nome_referente'] . ' ' . $ordine['cognome_referente'] : '',
            'utente' => $ordine['nome_utente'] . ' ' . $ordine['cognome_utente'],
            'nome_utente' => $ordine['nome_utente'] . ' ' . $ordine['cognome_utente'],
            'email_utente' => $ordine['email_utente'] ?? '',
            'progetto' => $ordine['nome_progetto'] ?? '',
            'nome_progetto' => $ordine['nome_progetto'] ?? '',
            'materiali' => $materialiHtml,
            'importo_totale' => '€ ' . $importoTotale,
            'totale' => '€ ' . $importoTotale,
            'data_consegna_prevista' => !empty($ordine['data_consegna_prevista']) ? date('d/m/Y', strtotime($ordine['data_consegna_prevista'])) : 'Non specificata',
            'condizioni_pagamento' => $ordine['condizioni_pagamento'] ?? '',
            'condizioni_consegna' => $ordine['condizioni_consegna'] ?? '',
            'note' => $ordine['note'] ?? ''
        ];
        
        // Debug dei dati che verranno usati per la sostituzione
        log_message('debug', 'Dati preparati per template: ' . print_r($dati, true));
        
        return $dati;
    }
} 