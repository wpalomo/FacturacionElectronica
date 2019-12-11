<?php

include_once 'ClaseGeneraPdf.php';

/**
 * Description of ClaseGeneraPdfRetencion
 *
 * @author jpsanchez
 */
class ClaseGeneraPdfRetencion extends ClaseGeneraPdf {

    public function __construct($tipoReporte, $datosCabecera, $datosDetalle, $datosPagos, $dataVencimientos) {
        parent::__construct($tipoReporte, $datosCabecera, $datosDetalle, $datosPagos, $dataVencimientos);
    }

    public function generaPdf() {
        $x = 10;
        $y = 10;
        $w = 80;
        $h = 20;
        $this->generarLogo($x, $y, $w, $h);

        $x = 10;
        $y = 48;
        $this->generarInformacionEmpresa($x, $y);

        $x = 110;
        $y = 10;
        $this->generarInformacionFactura($x, $y);

        $x = 10;
        $y = 90;
        $this->generarInformacionCliente($x, $y);

        $x = 5;
        $y = 103;
        $this->generarInformacionDetalle($x, $y);

        $x = 5;
        $y = $this->pdf->GetY() + 2;

        if ($y >= 275) {
            $this->pdf->AddPage();
            $x = 5;
            $y = 20;
        }

        $auxY = $y;
        $auxH = 0;
        $auxH = $this->generarInformacionAdicional($x, $y);

        $this->grabarPdf();
    }

    protected function generarInformacionCliente($x, $y) {
        $auxX = $x;
        $auxY = $y;
        $xRectanguloDatosCliente = $x - 5;
        $yRectanguloDatosCliente = $y - 4;
        $wRectanguloDatosCliente = 200;
        $hRectanguloDatosCliente = 15;

        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Razón Social/Nombres y Apellidos:"));

        $x = $auxX + 50;
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $this->datosCabecera['CNO_CLIENTE']);

        $x = $auxX;
        $y = $y + 6;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Fecha de Emisión:"));

        $x = $auxX + 30; //40;        
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $this->datosCabecera['DFM_FECHA']);

        $x = $auxX + 140; //150;
        $y = $auxY;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Identificación:"));

        $x = $auxX + 165; //175;   
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $this->datosCabecera['CCI_RUC_CLIENTE']);

        $this->pdf->Rect($xRectanguloDatosCliente, $yRectanguloDatosCliente, $wRectanguloDatosCliente, $hRectanguloDatosCliente);
    }

    protected function generarInformacionDetalle($x, $y) {
        $auxX = $x;
        $auxY = $y;

        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(20, 10, 'Comprobante', 1, 0, 'C');
        $this->pdf->Cell(35, 10, utf8_decode('Número'), 1, 0, 'C');
        $this->pdf->Cell(25, 10, utf8_decode('Fecha Emisión'), 1, 0, 'C');
        $this->myCell(20, 10, 'Ejercicio Fiscal');
        $this->myCell(30, 10, utf8_decode('Base_Imponible_para la_Retención'));
        $this->pdf->Cell(25, 10, 'IMPUESTO', 1, 0, 'C');
        $this->myCell(20, 10, utf8_decode('Porcentaje Retención'));
        $this->pdf->Cell(25, 10, 'Valor Retenido', 1, 0, 'C');

        $x = $auxX;
        $y = $this->pdf->GetY() + 10;
        $this->pdf->SetXY($x, $y);

        foreach ($this->datosDetalle as $keyDetalle => $valueDetalle) {
            $nvaBase = number_format($valueDetalle['NVA_BASE_RETENCION'], 2, '.', ',');
            $nvaRetencion = number_format($valueDetalle['NVA_RETENCION'], 2, '.', ',');

            $this->pdf->Cell(20, 10, $valueDetalle['COMPROBANTE'], 1, 0, 'C');
            $this->pdf->Cell(35, 10, $valueDetalle['NCI_FACTURA'], 1, 0, 'C');
            $this->pdf->Cell(25, 10, $valueDetalle['FECHA_EMISION'], 1, 0, 'C');
            $this->pdf->Cell(20, 10, $valueDetalle['PERIODO_FISCAL'], 1, 0, 'C');
            $this->pdf->Cell(30, 10, $nvaBase, 1, 0, 'R');
            $this->pdf->Cell(25, 10, $valueDetalle['IMPUESTO'], 1, 0, 'C');
            $this->pdf->Cell(20, 10, $valueDetalle['NVA_PORCENTAJE'], 1, 0, 'C');
            $this->pdf->Cell(25, 10, $nvaRetencion, 1, 0, 'R');

            $this->pdf->Ln();
        }
    }

}
