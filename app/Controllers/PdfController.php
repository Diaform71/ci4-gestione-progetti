<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\DSpdf;

class PdfController extends BaseController
{
    protected $helpers = ['CIFunctions'];
    protected $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function index()
    {
        //
    }

    //pdf richiesta
    public function openRDO($id)
    {
        // Assicuriamoci che non ci sia output prima
        if (ob_get_length()) {
            ob_end_clean();
        }
        
        // Generiamo il PDF
        $pdf = $this->pdfRichiesta($id);
        
        // Imposta gli header HTTP corretti per un PDF
        header('Content-Type: application/pdf');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        // Output del PDF
        $pdf->Output('RDO_' . $id . '.pdf', 'I');
        exit; // Assicuriamoci che nulla venga eseguito dopo l'output del PDF
    }

    public function openOffertaFornitore($id)
    {
        // Assicuriamoci che non ci sia output prima
        if (ob_get_length()) {
            ob_end_clean();
        }
        
        // Generiamo il PDF
        $pdf = $this->pdfOffertaFornitore($id);
        
        // Imposta gli header HTTP corretti per un PDF
        header('Content-Type: application/pdf');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        // Output del PDF
        $pdf->Output('OffertaFornitore_' . $id . '.pdf', 'I');
        exit;
    }

    public function pdfRichiesta($id, $returnPath = false)
    {
        $db = \Config\Database::connect();
        
        $builder = $db->table('richieste_offerta AS r');
        $header = $builder->select([
            'r.id', 'r.id_anagrafica', 'r.numero', 'r.data', 'r.oggetto',
            'r.descrizione', 'r.stato', 'a.ragione_sociale',
            'a.indirizzo', 'a.citta', 'a.cap', 'a.nazione', 'a.telefono',
            'a.fax', 'a.partita_iva'
        ])
            ->join('anagrafiche AS a', 'a.id = r.id_anagrafica')
            ->where('r.id', $id)
            ->get()->getRowArray();

        if (empty($header)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Richiesta d\'offerta non trovata');
        }
            
        //sql for body
        $builder = $db->table('richieste_materiali AS rm');
        $items = $builder->select([
            'rm.id', 'rm.id_materiale', 'm.codice', 'm.descrizione',
            'm.produttore', 'rm.quantita', 'rm.unita_misura', 'rm.id_progetto', 'p.nome AS nome_progetto'
        ])
            ->join('materiali AS m', 'm.id = rm.id_materiale')
            ->join('progetti AS p', 'p.id = rm.id_progetto', 'left')
            ->where('rm.id_richiesta', $id)
            ->get()->getResultArray();

        //TCPDF
        $pdf = new DSpdf('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('CI4 Gestione Progetti');
        $pdf->SetAuthor('Amministratore');
        $pdf->SetTitle('Richiesta d\'offerta ' . $header['numero']);
        $pdf->SetSubject('Richiesta d\'offerta');
        $pdf->SetKeywords('RDO, Richiesta, Offerta');
        
        // Disattiva il logo completamente
        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(true);

        // set header and footer fonts
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 30); //da regolare in base all'altezza del footer
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // es.variabili da definire
        $pdf->tipo = 'Richiesta d\'offerta';
        $pdf->numero = $header['numero'];
        $pdf->data = date('d/m/Y', strtotime($header['data']));
        $pdf->fornitore = $header['ragione_sociale'];
        $pdf->indirizzo = $header['indirizzo'];
        $pdf->cap = $header['cap'];
        $pdf->citta = $header['citta'];
        $pdf->nazione = $header['nazione'];
        $pdf->telefono = $header['telefono'];
        $pdf->fax = $header['fax'] ?? '';
        $pdf->piva = $header['partita_iva'];
        $pdf->descrizione = $header['descrizione'];

        //abbasso il body
        $pdf->SetMargins(15, 70, 15);
        // set font
        $pdf->SetFont('times', '', 10);
        // add a page
        $pdf->AddPage();
        // set some text to print
        //$html = view('Pdf/RdO', $items);
        $html = '';

        //descrizione
        $pdf->SetFont('times', '', 11);
        $pdf->MultiCell('', 4, $pdf->descrizione, '', 'L', FALSE, 1, '', '', 0);
        $pdf->Ln(10);
        $pdf->Cell(0, 0, '', 'T', 1); //riga

        $pdf->SetFont('times', '', 10);
        //css
        $html = '
            <style type="text/css">
                .left{
                    text-align: left;
                }    
                .ctr{
                    text-align: center;
                }
                .right{
                    text-align: right;
                }  

                table {
                    width: 100%;
                }

                tr th {
                    font-size: 15px;
                    font-family:arial;
                    border-bottom-style: 1px solid black;
                }

                tr td {
                    font-size: 11px;
                    line-height: 1.5;
                    font-family:arial;
                }

                .bold {
                    font-weight: bold;
                }

                .bottom {
                    border-bottom-style: 1em solid black;
                }

            </style>
            ';

        $html .= '
            <table>
                <thead>
                    <tr class="bold">
                        <th width="7%">Pos.</th>
                        <th width="13%">Codice</th>
                        <th width="35%">Descrizione</th>
                        <th width="20%">Progetto</th>
                        <th width="15%">Quantit&agrave;</th>
                        <th width="10%">U.M.</th>
                    </tr>
                </thead>
            ';
        //posizione riga
        $pos = 10;
        if (!empty($items)) {
            foreach ($items as $item) {
                $html .= '
                    <tr>
                        <td width="7%">' . $pos . '</td>
                        <td width="13%">' . $item['codice'] . '</td>
                        <td width="35%">' . $item['descrizione'] . '</td>
                        <td width="20%">' . ($item['nome_progetto'] ?? '-') . '</td>
                        <td width="15%" class="right">' . $item['quantita'] . '</td>
                        <td width="10%" class="ctr">' . $item['unita_misura'] . '</td>
                    </tr>
                    <tr><td colspan="6" style="border-bottom: 0.1px solid #cccccc;"></td></tr>
                ';
                //incremento il num.pos.
                $pos += 10;
            }
        } else {
            $html .= '<tr><td colspan="6" class="ctr">Nessun materiale associato a questa richiesta</td></tr>';
        }

        $html .= "</table>";
        // output the HTML content
        $pdf->writeHTML($html, true, false, false, false, '');

        if ($returnPath) {
            $tempPath = WRITEPATH . 'uploads/temp/RDO_' . $id . '_' . time() . '.pdf';
            $pdf->Output($tempPath, 'F');
            return $tempPath;
        }

        return $pdf;
    }

    public function pdfOffertaFornitore($id, $returnPath = false)
    {
        $db = \Config\Database::connect();
        
        // Query per l'header dell'offerta
        $builder = $db->table('offerte_fornitore AS o');
        $header = $builder->select([
            'o.id', 'o.id_anagrafica', 'o.numero', 'o.data', 'o.oggetto',
            'o.descrizione', 'o.stato', 'o.valuta', 'o.importo_totale',
            'o.sconto_totale', 'o.sconto_fisso', 'o.costo_trasporto', 'o.note',
            'a.ragione_sociale', 'a.indirizzo', 'a.citta', 'a.cap', 
            'a.nazione', 'a.telefono', 'a.partita_iva', 'a.codice_fiscale',
            'c.nome AS nome_referente', 'c.cognome AS cognome_referente',
            'c.email AS email_referente', 'c.telefono AS telefono_referente',
            'p.nome AS nome_progetto', 'r.numero AS numero_richiesta'
        ])
            ->join('anagrafiche AS a', 'a.id = o.id_anagrafica', 'left')
            ->join('anagrafiche_contatti AS ac', 'ac.id_anagrafica = o.id_anagrafica AND ac.id_contatto = o.id_referente', 'left')
            ->join('contatti AS c', 'c.id = o.id_referente', 'left')
            ->join('progetti AS p', 'p.id = o.id_progetto', 'left')
            ->join('richieste_offerta AS r', 'r.id = o.id_richiesta_offerta', 'left')
            ->where('o.id', $id)
            ->get()->getRowArray();

        if (empty($header)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Offerta fornitore non trovata');
        }
            
        // Query per le voci dell'offerta
        $builder = $db->table('offerte_fornitore_voci AS v');
        $voci = $builder->select([
            'v.id', 'v.id_materiale', 'v.codice', 'v.descrizione',
            'v.quantita', 'v.prezzo_unitario', 'v.importo', 'v.sconto',
            'v.unita_misura', 'v.id_progetto', 'p.nome AS nome_progetto'
        ])
            ->join('progetti AS p', 'p.id = v.id_progetto', 'left')
            ->where('v.id_offerta_fornitore', $id)
            ->get()->getResultArray();

        // Inizializzazione TCPDF
        $pdf = new DSpdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('CI4 Gestione Progetti');
        $pdf->SetAuthor('Amministratore');
        $pdf->SetTitle('Offerta Fornitore ' . $header['numero']);
        $pdf->SetSubject('Offerta Fornitore');
        $pdf->SetKeywords('Offerta, Fornitore');
        
        // Impostazioni header e footer
        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(true);
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, 30);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Variabili per il template
        $pdf->tipo = 'Offerta Fornitore';
        $pdf->numero = $header['numero'];
        $pdf->data = date('d/m/Y', strtotime($header['data']));
        $pdf->fornitore = $header['ragione_sociale'];
        $pdf->indirizzo = $header['indirizzo'];
        $pdf->cap = $header['cap'];
        $pdf->citta = $header['citta'];
        $pdf->nazione = $header['nazione'];
        $pdf->telefono = $header['telefono'];
        $pdf->piva = $header['partita_iva'];
        $pdf->descrizione = $header['descrizione'];

        // Impostazioni pagina
        $pdf->SetMargins(15, 70, 15);
        $pdf->SetFont('times', '', 10);
        $pdf->AddPage();

        // Sezione intestazione
        // $pdf->SetFont('times', 'B', 14);
        // $pdf->Cell(0, 10, 'OFFERTA FORNITORE N. ' . $header['numero'], 0, 1, 'C');
        // $pdf->SetFont('times', '', 10);
        // $pdf->Cell(0, 6, 'Data: ' . date('d/m/Y', strtotime($header['data'])), 0, 1);
        
        // if (!empty($header['nome_progetto'])) {
        //     $pdf->Cell(0, 6, 'Progetto: ' . $header['nome_progetto'], 0, 1);
        // }
        
        // if (!empty($header['numero_richiesta'])) {
        //     $pdf->Cell(0, 6, 'Richiesta d\'offerta N.: ' . $header['numero_richiesta'], 0, 1);
        // }
        
        // $pdf->Ln(1);
        
        // Sezione fornitore
        // $pdf->SetFont('times', 'B', 12);
        // $pdf->Cell(0, 8, 'FORNITORE', 0, 1);
        // $pdf->SetFont('times', '', 10);
        // $pdf->Cell(0, 6, $header['ragione_sociale'], 0, 1);
        
        // if (!empty($header['indirizzo'])) {
        //     $pdf->Cell(0, 6, $header['indirizzo'], 0, 1);
        //     $pdf->Cell(0, 6, $header['cap'] . ' ' . $header['citta'] . ' - ' . $header['nazione'], 0, 1);
        // }
        
        // if (!empty($header['partita_iva'])) {
        //     $pdf->Cell(0, 6, 'P.IVA: ' . $header['partita_iva'], 0, 1);
        // }
        
        // if (!empty($header['codice_fiscale'])) {
        //     $pdf->Cell(0, 6, 'C.F.: ' . $header['codice_fiscale'], 0, 1);
        // }
        
        // if (!empty($header['telefono'])) {
        //     $pdf->Cell(0, 6, 'Tel: ' . $header['telefono'], 0, 1);
        // }
        
        // Sezione referente
        // if (!empty($header['nome_referente'])) {
        //     $pdf->Ln(5);
        //     $pdf->SetFont('times', 'B', 12);
        //     $pdf->Cell(0, 8, 'REFERENTE', 0, 1);
        //     $pdf->SetFont('times', '', 10);
        //     $pdf->Cell(0, 6, $header['nome_referente'] . ' ' . $header['cognome_referente'], 0, 1);
            
        //     if (!empty($header['email_referente'])) {
        //         $pdf->Cell(0, 6, 'Email: ' . $header['email_referente'], 0, 1);
        //     }
            
        //     if (!empty($header['telefono_referente'])) {
        //         $pdf->Cell(0, 6, 'Tel: ' . $header['telefono_referente'], 0, 1);
        //     }
        // }
        
        // Oggetto e descrizione
        $pdf->Ln(3);
        $pdf->SetFont('times', 'B', 12);
        $pdf->Cell(0, 8, 'OGGETTO: ' . $header['oggetto'], 0, 1);
        
        if (!empty($header['descrizione'])) {
            $pdf->SetFont('times', '', 10);
            $pdf->writeHTML('<p>' . nl2br($header['descrizione']) . '</p>', true, false, false, false, '');
        }
        
        // Tabella voci
        $pdf->Ln(5);
        $pdf->SetFont('times', 'B', 12);
        $pdf->Cell(0, 8, 'VOCI DELL\'OFFERTA', 0, 1);
        
        // Stile tabella
        $html = '
        <style>
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 5px;
            }
            th {
                font-size: 10px;
                font-weight: bold;
                border-bottom: 1px solid #000;
                padding: 5px;
                text-align: left;
                background-color: #f2f2f2;
            }
            td {
                font-size: 9px;
                padding: 5px;
                border-bottom: 0.1px solid #ddd;
            }
            .text-right {
                text-align: right;
            }
            .totale-row {
                font-weight: bold;
                border-top: 1px solid #000;
            }
        </style>
        ';
        
        // Intestazione tabella
        $html .= '
        <table cellpadding="5">
            <thead>
                <tr>
                    <th width="6%">Pos.</th>
                    <th width="12%">Codice</th>
                    <th width="32%">Descrizione</th>
                    <th width="10%">Quantità</th>
                    <th width="12%">Prezzo Unit.</th>
                    <th width="8%">Sconto</th>
                    <th width="20%">Importo</th>
                </tr>
            </thead>
            <tbody>
        ';
        
        // Righe tabella
        if (!empty($voci)) {
            $totale_voci = 0;
            
            foreach ($voci as $i => $voce) {
                $totale_voci += $voce['importo'];
                
                $html .= '
                <tr>
                    <td width="6%">' . ($i + 1) . '</td>
                    <td width="12%">' . $voce['codice'] . '</td>
                    <td width="32%">' . $voce['descrizione'];
                
                if (!empty($voce['nome_progetto'])) {
                    $html .= '<br><small>Progetto: ' . $voce['nome_progetto'] . '</small>';
                }
                
                $html .= '</td>
                    <td width="10%" class="text-right">' . number_format($voce['quantita'], 2, ',', '.') . ' ' . $voce['unita_misura'] . '</td>
                    <td width="12%" class="text-right">' . number_format($voce['prezzo_unitario'], 2, ',', '.') . ' ' . $header['valuta'] . '</td>
                    <td width="8%" class="text-right">' . ($voce['sconto'] > 0 ? number_format($voce['sconto'], 2, ',', '.') . '%' : '-') . '</td>
                    <td width="20%" class="text-right">' . number_format($voce['importo'], 2, ',', '.') . ' ' . $header['valuta'] . '</td>
                </tr>
                ';
            }
            
            // Calcolo totali
            $importo_voci = round($totale_voci, 2);
            $sconto_totale = round(($importo_voci * ($header['sconto_totale'] ?? 0)) / 100, 2);
            $importo_scontato_perc = round($importo_voci - $sconto_totale, 2);
            $sconto_fisso = round((float)($header['sconto_fisso'] ?? 0), 2);
            $importo_scontato = round($importo_scontato_perc - $sconto_fisso, 2);
            // Se l'importo risulta negativo dopo gli sconti, imposta a zero
            if ($importo_scontato < 0) {
                $importo_scontato = 0;
            }
            $costo_trasporto = round((float)($header['costo_trasporto'] ?? 0), 2);
            $importo_finale = round($importo_scontato + $costo_trasporto, 2);
            
            // Riga totale voci
            $html .= '
            <tr>
                <td colspan="6" class="text-right totale-row">Totale Voci:</td>
                <td class="text-right totale-row">' . number_format($importo_voci, 2, ',', '.') . ' ' . $header['valuta'] . '</td>
            </tr>
            ';
            
            // Riga sconto totale (se presente)
            if (!empty($header['sconto_totale']) && $header['sconto_totale'] > 0) {
                $html .= '
                <tr>
                    <td colspan="6" class="text-right">Sconto ' . number_format($header['sconto_totale'], 2, ',', '.') . '%:</td>
                    <td class="text-right">-' . number_format($sconto_totale, 2, ',', '.') . ' ' . $header['valuta'] . '</td>
                </tr>
                ';
            }
            
            // Riga sconto fisso (se presente)
            if (!empty($header['sconto_fisso']) && (float)$header['sconto_fisso'] > 0) {
                $html .= '
                <tr>
                    <td colspan="6" class="text-right">Sconto Fisso:</td>
                    <td class="text-right">-' . number_format($sconto_fisso, 2, ',', '.') . ' ' . $header['valuta'] . '</td>
                </tr>
                ';
            }
            
            // Riga costo trasporto (se presente)
            if (!empty($header['costo_trasporto']) && $header['costo_trasporto'] > 0) {
                $html .= '
                <tr>
                    <td colspan="6" class="text-right">Costo Trasporto:</td>
                    <td class="text-right">' . number_format($costo_trasporto, 2, ',', '.') . ' ' . $header['valuta'] . '</td>
                </tr>
                ';
            }
            
            // Riga importo totale finale
            $html .= '
            <tr>
                <td colspan="6" class="text-right totale-row"><strong>Importo Totale:</strong></td>
                <td class="text-right totale-row"><strong>' . number_format($importo_finale, 2, ',', '.') . ' ' . $header['valuta'] . '</strong></td>
            </tr>
            ';
        } else {
            $html .= '
            <tr>
                <td colspan="7" style="text-align: center;">Nessuna voce presente</td>
            </tr>
            ';
        }
        
        $html .= '
            </tbody>
        </table>
        ';
        
        // Output tabella
        $pdf->writeHTML($html, true, false, false, false, '');
        
        // Note (se presenti)
        if (!empty($header['note'])) {
            $pdf->Ln(5);
            $pdf->SetFont('times', 'B', 12);
            $pdf->Cell(0, 8, 'NOTE', 0, 1);
            $pdf->SetFont('times', '', 10);
            $pdf->writeHTML('<p>' . nl2br($header['note']) . '</p>', true, false, false, false, '');
        }
        
        // Se richiesto il percorso temporaneo
        if ($returnPath) {
            $tempPath = WRITEPATH . 'uploads/temp/OffertaFornitore_' . $id . '_' . time() . '.pdf';
            $pdf->Output($tempPath, 'F');
            return $tempPath;
        }

        return $pdf;
    }

    public function openOrdineMateriale($id)
    {
        // Assicuriamoci che non ci sia output prima
        if (ob_get_length()) {
            ob_end_clean();
        }
        
        // Generiamo il PDF
        $pdf = $this->pdfOrdineMateriale($id);
        
        // Imposta gli header HTTP corretti per un PDF
        header('Content-Type: application/pdf');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        // Output del PDF
        $pdf->Output('Ordine_' . $id . '.pdf', 'I');
        exit;
    }

    public function pdfOrdineMateriale($id, $returnPath = false)
    {
        $db = \Config\Database::connect();
        
        // Query per l'header dell'ordine
        $builder = $db->table('ordini_materiale AS o');
        $header = $builder->select([
            'o.id', 'o.id_anagrafica', 'o.numero', 'o.data', 'o.oggetto',
            'o.descrizione', 'o.stato', 'o.importo_totale', 'o.condizioni_pagamento',
            'o.condizioni_consegna', 'o.data_consegna_prevista', 'o.data_consegna_effettiva',
            'o.sconto_totale', 'o.sconto_fisso', 'o.costo_trasporto', 'o.note',
            'a.ragione_sociale', 'a.indirizzo', 'a.citta', 'a.cap', 
            'a.nazione', 'a.telefono', 'a.partita_iva', 'a.codice_fiscale',
            'c.nome AS nome_referente', 'c.cognome AS cognome_referente',
            'c.email AS email_referente', 'c.telefono AS telefono_referente',
            'p.nome AS nome_progetto'
        ])
            ->join('anagrafiche AS a', 'a.id = o.id_anagrafica', 'left')
            ->join('contatti AS c', 'c.id = o.id_referente', 'left')
            ->join('progetti AS p', 'p.id = o.id_progetto', 'left')
            ->where('o.id', $id)
            ->get()->getRowArray();

        if (empty($header)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Ordine materiale non trovato');
        }
            
        // Query per le voci dell'ordine
        $builder = $db->table('ordini_materiale_voci AS v');
        $voci = $builder->select([
            'v.id', 'v.id_materiale', 'v.codice', 'v.descrizione',
            'v.quantita', 'v.prezzo_unitario', 'v.importo', 'v.sconto',
            'v.unita_misura', 'v.id_progetto', 'p.nome AS nome_progetto', 'v.note'
        ])
            ->join('progetti AS p', 'p.id = v.id_progetto', 'left')
            ->where('v.id_ordine', $id)
            ->get()->getResultArray();

        // Inizializzazione TCPDF
        $pdf = new DSpdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('CI4 Gestione Progetti');
        $pdf->SetAuthor('Amministratore');
        $pdf->SetTitle('Ordine di Acquisto ' . $header['numero']);
        $pdf->SetSubject('Ordine di Acquisto');
        $pdf->SetKeywords('Ordine, Acquisto, Materiale');
        
        // Impostazioni header e footer
        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(true);
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, 30);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Variabili per il template
        $pdf->tipo = 'Ordine di Acquisto';
        $pdf->numero = $header['numero'];
        $pdf->data = date('d/m/Y', strtotime($header['data']));
        $pdf->fornitore = $header['ragione_sociale'];
        $pdf->indirizzo = $header['indirizzo'];
        $pdf->cap = $header['cap'];
        $pdf->citta = $header['citta'];
        $pdf->nazione = $header['nazione'];
        $pdf->telefono = $header['telefono'];
        $pdf->piva = $header['partita_iva'];
        $pdf->descrizione = $header['descrizione'];

        // Impostazioni pagina
        $pdf->SetMargins(15, 70, 15);
        $pdf->SetFont('times', '', 10);
        $pdf->AddPage();

        // Oggetto e descrizione
        $pdf->Ln(3);
        $pdf->SetFont('times', 'B', 12);
        $pdf->Cell(0, 8, 'OGGETTO: ' . $header['oggetto'], 0, 1);
        
        if (!empty($header['descrizione'])) {
            $pdf->SetFont('times', '', 10);
            $pdf->writeHTML('<p>' . nl2br($header['descrizione']) . '</p>', true, false, false, false, '');
        }
        
        // Informazioni aggiuntive dell'ordine
        $pdf->Ln(3);
        $pdf->SetFont('times', 'B', 11);
        $pdf->Cell(0, 8, 'Informazioni Ordine:', 0, 1);
        $pdf->SetFont('times', '', 10);
        
        if (!empty($header['condizioni_pagamento'])) {
            $pdf->Cell(0, 6, 'Condizioni di pagamento: ' . $header['condizioni_pagamento'], 0, 1);
        }
        
        if (!empty($header['condizioni_consegna'])) {
            $pdf->Cell(0, 6, 'Condizioni di consegna: ' . $header['condizioni_consegna'], 0, 1);
        }
        
        if (!empty($header['data_consegna_prevista'])) {
            $pdf->Cell(0, 6, 'Data consegna prevista: ' . date('d/m/Y', strtotime($header['data_consegna_prevista'])), 0, 1);
        }
        
        if (!empty($header['nome_progetto'])) {
            $pdf->Cell(0, 6, 'Progetto: ' . $header['nome_progetto'], 0, 1);
        }
        
        // Tabella voci
        $pdf->Ln(5);
        $pdf->SetFont('times', 'B', 12);
        $pdf->Cell(0, 8, 'ELENCO MATERIALI', 0, 1);
        
        // Stile tabella
        $html = '
        <style>
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 5px;
            }
            th {
                font-size: 10px;
                font-weight: bold;
                border-bottom: 1px solid #000;
                padding: 5px;
                text-align: left;
                background-color: #f2f2f2;
            }
            td {
                font-size: 9px;
                padding: 5px;
                border-bottom: 0.1px solid #ddd;
            }
            .text-right {
                text-align: right;
            }
            .totale-row {
                font-weight: bold;
                border-top: 1px solid #000;
            }
        </style>
        ';
        
        // Intestazione tabella
        $html .= '
        <table cellpadding="5">
            <thead>
                <tr>
                    <th width="6%">Pos.</th>
                    <th width="12%">Codice</th>
                    <th width="32%">Descrizione</th>
                    <th width="10%">Quantità</th>
                    <th width="12%">Prezzo Unit.</th>
                    <th width="8%">Sconto</th>
                    <th width="20%">Importo</th>
                </tr>
            </thead>
            <tbody>
        ';
        
        // Righe tabella
        if (!empty($voci)) {
            $totale_voci = 0;
            
            foreach ($voci as $i => $voce) {
                $totale_voci += $voce['importo'];
                
                $html .= '
                <tr>
                    <td width="6%">' . ($i + 1) . '</td>
                    <td width="12%">' . $voce['codice'] . '</td>
                    <td width="32%">' . $voce['descrizione'];
                
                if (!empty($voce['nome_progetto'])) {
                    $html .= '<br><small>Progetto: ' . $voce['nome_progetto'] . '</small>';
                }
                
                if (!empty($voce['note'])) {
                    $html .= '<br><small>Note: ' . $voce['note'] . '</small>';
                }
                
                $html .= '</td>
                    <td width="10%" class="text-right">' . number_format($voce['quantita'], 2, ',', '.') . ' ' . $voce['unita_misura'] . '</td>
                    <td width="12%" class="text-right">' . number_format($voce['prezzo_unitario'], 2, ',', '.') . ' €</td>
                    <td width="8%" class="text-right">' . ($voce['sconto'] > 0 ? number_format($voce['sconto'], 2, ',', '.') . '%' : '-') . '</td>
                    <td width="20%" class="text-right">' . number_format($voce['importo'], 2, ',', '.') . ' €</td>
                </tr>
                ';
            }
            
            // Calcolo totali
            $importo_voci = round($totale_voci, 2);
            $sconto_totale = round(($importo_voci * ($header['sconto_totale'] ?? 0)) / 100, 2);
            $importo_scontato_perc = round($importo_voci - $sconto_totale, 2);
            $sconto_fisso = round((float)($header['sconto_fisso'] ?? 0), 2);
            $importo_scontato = round($importo_scontato_perc - $sconto_fisso, 2);
            // Se l'importo risulta negativo dopo gli sconti, imposta a zero
            if ($importo_scontato < 0) {
                $importo_scontato = 0;
            }
            $costo_trasporto = round((float)($header['costo_trasporto'] ?? 0), 2);
            $importo_finale = round($importo_scontato + $costo_trasporto, 2);
            
            // Riga totale voci
            $html .= '
            <tr>
                <td colspan="6" class="text-right totale-row">Totale Voci:</td>
                <td class="text-right totale-row">' . number_format($importo_voci, 2, ',', '.') . ' €</td>
            </tr>
            ';
            
            // Riga sconto totale (se presente)
            if (!empty($header['sconto_totale']) && $header['sconto_totale'] > 0) {
                $html .= '
                <tr>
                    <td colspan="6" class="text-right">Sconto ' . number_format($header['sconto_totale'], 2, ',', '.') . '%:</td>
                    <td class="text-right">-' . number_format($sconto_totale, 2, ',', '.') . ' €</td>
                </tr>
                ';
            }
            
            // Riga sconto fisso (se presente)
            if (!empty($header['sconto_fisso']) && (float)$header['sconto_fisso'] > 0) {
                $html .= '
                <tr>
                    <td colspan="6" class="text-right">Sconto Fisso:</td>
                    <td class="text-right">-' . number_format($sconto_fisso, 2, ',', '.') . ' €</td>
                </tr>
                ';
            }
            
            // Riga costo trasporto (se presente)
            if (!empty($header['costo_trasporto']) && $header['costo_trasporto'] > 0) {
                $html .= '
                <tr>
                    <td colspan="6" class="text-right">Costo Trasporto:</td>
                    <td class="text-right">' . number_format($costo_trasporto, 2, ',', '.') . ' €</td>
                </tr>
                ';
            }
            
            // Riga importo totale finale
            $html .= '
            <tr>
                <td colspan="6" class="text-right totale-row"><strong>Importo Totale:</strong></td>
                <td class="text-right totale-row"><strong>' . number_format($importo_finale, 2, ',', '.') . ' €</strong></td>
            </tr>
            ';
        } else {
            $html .= '
            <tr>
                <td colspan="7" style="text-align: center;">Nessun materiale presente</td>
            </tr>
            ';
        }
        
        $html .= '
            </tbody>
        </table>
        ';
        
        // Output tabella
        $pdf->writeHTML($html, true, false, false, false, '');
        
        // Note (se presenti)
        if (!empty($header['note'])) {
            $pdf->Ln(5);
            $pdf->SetFont('times', 'B', 12);
            $pdf->Cell(0, 8, 'NOTE', 0, 1);
            $pdf->SetFont('times', '', 10);
            $pdf->writeHTML('<p>' . nl2br($header['note']) . '</p>', true, false, false, false, '');
        }
        
        // Se richiesto il percorso temporaneo
        if ($returnPath) {
            $tempPath = WRITEPATH . 'uploads/temp/Ordine_' . $id . '_' . time() . '.pdf';
            $pdf->Output($tempPath, 'F');
            return $tempPath;
        }

        return $pdf;
    }
}
