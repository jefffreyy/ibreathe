<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'libraries/fpdf/fpdf.php';
require_once APPPATH . 'libraries/fpdi/src/autoload.php';
class Pdf extends FPDF {
    public function __construct() {
        parent::__construct();
    }
    public function OpenExistingPDF($filename) {
        $pdf = new \setasign\Fpdi\Fpdi();
        $pageCount = $pdf->setSourceFile($filename);
        for ($i = 1; $i <= $pageCount; $i++) {
            $tplIdx = $pdf->importPage($i);
            $pdf->AddPage();
            $pdf->useTemplate($tplIdx, 13, 10, 10, 10, true);
        }
        return $pdf;
    }
}