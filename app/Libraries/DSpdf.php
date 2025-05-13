<?php

namespace App\Libraries;

use TCPDF;
use Config\TCPDF\FooterData;

class DSpdf extends TCPDF {
    //variabili 
    public $tipo = '';
    public $numero = '';
    public $data = '';
    public $fornitore = '';
    public $indirizzo = '';
    public $cap = '';
    public $citta = '';
    public $nazione = '';
    public $telefono = '';
    public $fax = '';
    public $piva = '';
    public $descrizione = '';
    public $status = '';
    public $commessa = '';


    public function __construct()
    {
        parent::__construct();
    }

    //page header
    function header() {
        // Intestazione con colore di sfondo blu
        $this->setFillColor(8, 75, 138);
        $this->MultiCell(130, 12, '', '', '', TRUE);
        
        $this->setFillColor(255, 255, 255);
        $this->MultiCell(0, 6, '', '', '', TRUE);

        $this->setFillColor(255, 255, 255);
        $this->MultiCell(98, 4, '', '', '', TRUE, 0);
        $this->setFillColor(212, 216, 218);
        $this->setFont('helvetica', 'B', 11);
        $this->MultiCell(82, 4, $this->tipo , '', 'L', TRUE, 1, '', '', 0);
        //riga font in grassetto
        $this->SetFont('times', '', 9);
        $this->MultiCell(98, 4, '', '', 'L', FALSE, 0, '', '', 0);
        $this->SetFont('times', 'B', 9);
        $this->MultiCell(42, 4, 'Numero', 0, 'L', FALSE, '0', '', '', 0);
        $this->MultiCell(56, 4, 'Data', 0, 'L', FALSE, '1', '', '', 0);
        $this->SetFont('times', '', 9);
        //riga font normale
        $this->MultiCell(98, 4, '', '', 'L', FALSE, 0, '', '', 0);
        $this->SetFont('times', '', 9);
        $this->MultiCell(42, 4, $this->numero, 0, 'L', FALSE, '0', '', '', 0, '', FALSE); //numero
        $this->MultiCell(56, 4, $this->data, 0, 'L', FALSE, '1', '', '', 0, '', FALSE); //data
        $this->SetFont('times', '', 9);

        //fornitore
        $this->SetFont('times', 'B', 10);
        $this->MultiCell(98, 4, $this->fornitore, '', 'L', FALSE, 0, '', '', 0);
        //$this->SetFont('times', 'B', 9);
        $this->MultiCell(42, 4, 'Telefono', 0, 'L', FALSE, '0', '', '', 0);
        $this->MultiCell(56, 4, 'Fax', 0, 'L', FALSE, '1', '', '', 0);
        //indirizzo
        $this->SetFont('times', '', 10);
        $this->MultiCell(98, 4, $this->indirizzo, '', 'L', FALSE, 0, '', '', 0);
        $this->SetFont('times', '', 10);
        $this->MultiCell(42, 4, $this->telefono, 0, 'L', FALSE, '0', '', '', 0, '', FALSE);
        $this->MultiCell(56, 4, $this->fax, 0, 'L', FALSE, '1', '', '', 0, '', FALSE);

        //cap città
        $this->SetFont('times', '', 9);
        $this->MultiCell(98, 4, $this->cap . ' ' . $this->citta . ' - ' . $this->nazione, '', 'L', FALSE, 0, '', '', 0);
        $this->SetFont('times', 'B', 9);
        $this->MultiCell(15, 4, 'P.IVA:', 0, 'L', FALSE, '0', '', '', 0);
        $this->SetFont('times', '', 9);
        $this->MultiCell(41, 4, $this->piva, 0, 'L', FALSE, '1', '', '', 0);

        $this->Ln(10);
        //descrizione
        //$this->SetFont('times', '', 9);
        //$this->MultiCell('', 4, $this->descrizione, '', 'L', FALSE, 0, '', '', 0);
    }

    // Page footer
    function Footer()
    {
        // Position at 2cm from bottom
        $this->SetY(-20);
        //$this->ln();
        $this->Cell(0, 0, '', 'T', 1); //riga
        //riga1
        // set font
        $this->SetFont('helvetica', 'B', 8);
        $this->Cell(65, 0, FooterData::companyName, 0, 0, 'L', 0, '', 1);
        // set font
        $this->SetFont('helvetica', '', 8);
        $this->Cell(70, 0, FooterData::companyPhone, 0, 0, 'L', 0, '', 1);
        $this->Cell(45, 0, FooterData::companyWebsite, 0, 1, 'L', 0, '', 1);
        //seconda riga
        $this->Cell(65, 0, FooterData::companyAddress, 0, 0, 'L', 0, '', 1);
        $this->Cell(70, 0, FooterData::companyEmail, 0, 0, 'L', 0, '', 1);
        $this->Cell(45, 0, FooterData::companyCf, 0, 1, 'L', 0, '', 1);
        //terza riga
        $this->Cell(65, 0, FooterData::companyCity, 0, 0, 'L', 0, '', 1);
        $this->Cell(70, 0, FooterData::companyPec, 0, 0, 'L', 0, '', 1);

        // Numero di pagina sulla stessa riga del footer
        $this->SetFont('helvetica', '', 8);
        $pageNumberText = 'Pagina '.$this->getAliasNumPage().' / '.$this->getAliasNbPages();
        $this->Cell(45, 0, $pageNumberText, 0, 1, 'R', 0, '', 1);
    }
    
}