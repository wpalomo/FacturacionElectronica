<?php

include_once 'ClaseGeneraPdf.php';

/**
 * Description of ClaseGeneraPdfNC
 *
 * @author jpsanchez
 */
class ClaseGeneraPdfNC extends ClaseGeneraPdf {

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
        //metodo sobreescrito
        $this->generarInformacionCliente($x, $y);

        $x = 5;
        $y = 113;
        $this->generarInformacionDetalle($x, $y);

        $x = 5;
        $y = $this->pdf->GetY() + 2;

        if ($y >= 225 || $y >= 275) {
            $this->pdf->AddPage();
            $x = 5;
            $y = 20;
        }

        $auxY = $y;
        $auxH = 0;
        $auxH = $this->generarInformacionAdicional($x, $y);

        $x = 134;
        $y = $auxY;
        $this->generarInformacionTotales($x, $y);

        $this->grabarPdf();
    }

    //metodo sobreescrito
    protected function generarInformacionCliente($x, $y) {
        $auxX = $x;
        $auxY = $y;
        $xRectanguloDatosCliente = $x - 5;
        $yRectanguloDatosCliente = $y - 4;
        $wRectanguloDatosCliente = 200;
        $hRectanguloDatosCliente = 27;

        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Razón Social/Nombres y Apellidos:"));

        $x = $auxX + 50;
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode($this->datosCabecera['CNO_CLIENTE']));

        $x = $auxX;
        $y = $y + 4;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Fecha de Emisión:"));

        $x = $auxX + 30; //40;        
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $this->datosCabecera['DFM_FECHA']);

        $x = $auxX + 75; //150;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Vendedor:"));

        $x = $auxX + 95; //175;   
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $this->datosCabecera['CNO_VENDEDOR']);

        $x = $auxX + 140; //150;
        $y = $auxY;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Identificación:"));

        $x = $auxX + 165; //175;   
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $this->datosCabecera['CCI_RUC_CLIENTE']);

        $x = $auxX;
        $y = $auxY + 8;
        $this->pdf->SetXY($x, $y);
        $this->pdf->line($x, $y, $x + 190, $y);

        $x = $auxX;
        $y = $y + 3;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Comprobante que se modifica:"));

        $this->pdf->SetFont('');
        $x = $auxX + 120; //130;
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $this->datosCabecera['COMPROBANTE_MODIFICA']);

        $x = $auxX + 150; //160;
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $this->datosCabecera['NCI_DOCUMENTO_MODIFICA']);

        $x = $auxX;
        $y = $y + 4;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Fecha Emisión(Comprobante a modificar):"));

        $x = $auxX + 120; //130;
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $this->datosCabecera['DFM_FECHA_MODIFICA']);

        $x = $auxX + 150; //160;      
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Código:"));

        $x = $auxX + 170; //180;
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $this->datosCabecera['CCI_CLIENTE']);

        $x = $auxX;
        $y = $y + 4;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Razón de Modificación:"));

        $x = $auxX + 120; //130;
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $this->datosCabecera['CTX_DESCRIPCION']);

        $this->pdf->Rect($xRectanguloDatosCliente, $yRectanguloDatosCliente, $wRectanguloDatosCliente, $hRectanguloDatosCliente);
    }

}
