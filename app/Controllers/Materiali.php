<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Materiale;
use CodeIgniter\HTTP\ResponseInterface;

class Materiali extends BaseController
{
    protected $materialeModel;
    
    public function __construct()
    {
        $this->materialeModel = new Materiale();
        // Caricamento del text helper per usare character_limiter()
        helper('text');
    }
    
    public function index()
    {
        // Recupero parametri di filtro
        $search = $this->request->getGet('search');
        $category = $this->request->getGet('category');
        $status = $this->request->getGet('status');
        
        // Preparo la query di base
        $builder = $this->materialeModel;
        
        // Applico i filtri
        if (!empty($search)) {
            $builder = $builder->groupStart()
                ->like('codice', $search)
                ->orLike('descrizione', $search)
                ->orLike('produttore', $search)
                ->groupEnd();
        }
        
        if (!empty($category)) {
            $builder = $builder->where($category, 1);
        }
        
        if ($status !== null && $status !== '') {
            $builder = $builder->where('in_produzione', $status);
        }
        
        // Imposto l'ordinamento predefinito
        $builder = $builder->orderBy('codice', 'ASC');
        
        // Eseguo la query con paginazione
        $perPage = 10; // Materiali per pagina
        $materiali = $builder->paginate($perPage);
        $pager = $builder->pager;
        
        // Preparo i dati per la vista
        $data = [
            'title' => 'Gestione Materiali',
            'materiali' => $materiali,
            'pager' => $pager,
            'search' => $search,
            'category' => $category,
            'status' => $status
        ];
        
        return view('materiali/index', $data);
    }
    
    public function new()
    {
        $data = [
            'title' => 'Nuovo Materiale'
        ];
        
        return view('materiali/form', $data);
    }
    
    public function create()
    {
        // Gestione del checkbox
        $this->request->getPost();
        $post = $this->request->getPost();
        
        // Gestione dei campi booleani
        $post['commerciale'] = isset($post['commerciale']) ? 1 : 0;
        $post['meccanica'] = isset($post['meccanica']) ? 1 : 0;
        $post['elettrica'] = isset($post['elettrica']) ? 1 : 0;
        $post['pneumatica'] = isset($post['pneumatica']) ? 1 : 0;
        $post['in_produzione'] = isset($post['in_produzione']) ? 1 : 0;
        
        // Gestione dell'upload dell'immagine
        $img = $this->request->getFile('immagine');
        if ($img && $img->isValid() && !$img->hasMoved()) {
            $newName = $img->getRandomName();
            $img->move(FCPATH . 'uploads/materiali', $newName);
            $post['immagine'] = $newName;
        }
        
        if ($this->materialeModel->insert($post)) {
            return redirect()->to(base_url('materiali'))->with('success', 'Materiale creato con successo');
        }
        
        return redirect()->back()->withInput()->with('errors', $this->materialeModel->errors());
    }
    
    public function edit($id = null)
    {
        $materiale = $this->materialeModel->find($id);
        
        if (!$materiale) {
            return redirect()->to(base_url('materiali'))->with('error', 'Materiale non trovato');
        }
        
        $data = [
            'title' => 'Modifica Materiale',
            'materiale' => $materiale
        ];
        
        return view('materiali/form', $data);
    }
    
    public function update($id = null)
    {
        $materiale = $this->materialeModel->find($id);
        
        if (!$materiale) {
            return redirect()->to(base_url('materiali'))->with('error', 'Materiale non trovato');
        }
        
        // Gestione del form
        $post = $this->request->getPost();
        // Aggiungiamo l'ID per la validazione
        $post['id'] = $id;
        
        // Gestione dei campi booleani
        $post['commerciale'] = isset($post['commerciale']) ? 1 : 0;
        $post['meccanica'] = isset($post['meccanica']) ? 1 : 0;
        $post['elettrica'] = isset($post['elettrica']) ? 1 : 0;
        $post['pneumatica'] = isset($post['pneumatica']) ? 1 : 0;
        $post['in_produzione'] = isset($post['in_produzione']) ? 1 : 0;
        
        // Gestione dell'upload dell'immagine
        $img = $this->request->getFile('immagine');
        if ($img && $img->isValid() && !$img->hasMoved()) {
            // Se c'è già un'immagine, eliminiamola
            if (!empty($materiale['immagine']) && file_exists(FCPATH . 'uploads/materiali/' . $materiale['immagine'])) {
                unlink(FCPATH . 'uploads/materiali/' . $materiale['immagine']);
            }
            
            // Carichiamo la nuova immagine
            $newName = $img->getRandomName();
            $img->move(FCPATH . 'uploads/materiali', $newName);
            $post['immagine'] = $newName;
        }
        
        if ($this->materialeModel->update($id, $post)) {
            return redirect()->to(base_url('materiali'))->with('success', 'Materiale aggiornato con successo');
        }
        
        return redirect()->back()->withInput()->with('errors', $this->materialeModel->errors());
    }
    
    public function delete($id = null)
    {
        $materiale = $this->materialeModel->find($id);
        
        if (!$materiale) {
            return redirect()->to(base_url('materiali'))->with('error', 'Materiale non trovato');
        }
        
        // Eliminiamo l'immagine associata se esiste
        if (!empty($materiale['immagine']) && file_exists(FCPATH . 'uploads/materiali/' . $materiale['immagine'])) {
            unlink(FCPATH . 'uploads/materiali/' . $materiale['immagine']);
        }
        
        if ($this->materialeModel->delete($id)) {
            return redirect()->to(base_url('materiali'))->with('success', 'Materiale eliminato con successo');
        }
        
        return redirect()->to(base_url('materiali'))->with('error', 'Impossibile eliminare il materiale');
    }
    
    public function show($id = null)
    {
        $materiale = $this->materialeModel->find($id);
        
        if (!$materiale) {
            return redirect()->to(base_url('materiali'))->with('error', 'Materiale non trovato');
        }
        
        // Recupera le richieste d'offerta associate a questo materiale
        $richiestaMaterialeModel = new \App\Models\RichiestaMaterialeModel();
        $richiesteOfferta = $richiestaMaterialeModel->getRichiesteByMateriale((int)$id);
        
        $data = [
            'title' => 'Dettaglio Materiale',
            'materiale' => $materiale,
            'richiesteOfferta' => $richiesteOfferta
        ];
        
        return view('materiali/show', $data);
    }

    /**
     * Cerca materiali per codice o descrizione
     * Utilizzato nella ricerca AJAX
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function search()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Richiesta non valida']);
        }

        $term = $this->request->getGet('term');
        
        if (empty($term) || strlen($term) < 2) {
            return $this->response->setJSON([]);
        }

        $materiali = $this->materialeModel
            ->select('id, codice, descrizione, produttore')
            ->groupStart()
                ->like('codice', $term)
                ->orLike('descrizione', $term)
                ->orLike('produttore', $term)
            ->groupEnd()
            ->orderBy('codice', 'ASC')
            ->findAll(20); // Limitiamo i risultati a 20

        return $this->response->setJSON($materiali);
    }

    /**
     * Genera un codice a barre PDF per il materiale
     *
     * @param integer|null $id
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function barcode($id = null)
    {
        $materiale = $this->materialeModel->find($id);
        
        if (!$materiale) {
            return redirect()->to(base_url('materiali'))->with('error', 'Materiale non trovato');
        }
        
        // Carichiamo TCPDF
        $tcpdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, array(60, 30), true, 'UTF-8', false);
        
        // Configurazione base del documento
        $tcpdf->SetCreator('Gestione Progetti');
        $tcpdf->SetAuthor('Sistema');
        $tcpdf->SetTitle('Codice a Barre - ' . $materiale['codice']);
        
        // Rimuovi header e footer
        $tcpdf->setPrintHeader(false);
        $tcpdf->setPrintFooter(false);
        
        // Imposta margini
        $tcpdf->SetMargins(5, 5, 5);
        
        // Aggiungi pagina
        $tcpdf->AddPage();
        
        // Genera il codice a barre
        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false,
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 4
        );
        
        // Codice del materiale centrato nella pagina
        $tcpdf->SetFont('helvetica', 'B', 10);
        $tcpdf->Cell(0, 0, $materiale['codice'], 0, 1, 'C');
        $tcpdf->Ln(2);
        
        // Genera il codice a barre (Code 128 è un formato comune e versatile)
        $tcpdf->write1DBarcode($materiale['codice'], 'C128', '', '', '', 18, 0.4, $style, 'N');
        
        // Aggiungi descrizione sotto (troncata se troppo lunga)
        $tcpdf->Ln(1);
        $tcpdf->SetFont('helvetica', '', 7);
        $descrizione = character_limiter($materiale['descrizione'], 50);
        $tcpdf->Cell(0, 0, $descrizione, 0, 1, 'C');
        
        // Aggiungi il produttore se disponibile
        if (!empty($materiale['produttore'])) {
            $tcpdf->Ln(1);
            $tcpdf->SetFont('helvetica', 'I', 6);
            $tcpdf->Cell(0, 0, $materiale['produttore'], 0, 1, 'C');
        }
        
        // Output del PDF
        $filename = 'barcode_' . preg_replace('/[^A-Za-z0-9_-]/', '', $materiale['codice']) . '.pdf';
        
        // Imposta gli header corretti per il download del PDF
        $response = service('response');
        $response->setHeader('Content-Type', 'application/pdf');
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->setHeader('Cache-Control', 'max-age=0');
        
        // Genera il PDF e lo invia direttamente al browser
        $pdfContent = $tcpdf->Output($filename, 'S'); // 'S' per ottenere il contenuto come stringa
        
        return $response->setBody($pdfContent);
    }
}
