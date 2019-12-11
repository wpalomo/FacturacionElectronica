<?php

include_once 'fpdf181/fpdf.php';
include_once 'barcodegen.1d-php5.v5.2.0/class/BCGFontFile.php';
include_once 'barcodegen.1d-php5.v5.2.0/class/BCGColor.php';
include_once 'barcodegen.1d-php5.v5.2.0/class/BCGDrawing.php';
include_once 'barcodegen.1d-php5.v5.2.0/class/BCGcode128.barcode.php';

/**
 * Description of ClaseGeneraPdf2
 *
 * @author jpsanchez
 */
class ClaseGeneraPdf {

    protected $pdf;
    protected $tipoReporte;
    protected $datosCabecera = array();
    protected $datosDetalle;
    protected $datosPagos;
    protected $dataVencimientos;

    public function __construct($tipoReporte, $datosCabecera, $datosDetalle, $datosPagos, $dataVencimientos) {
        $this->pdf = new FPDF();
        $this->pdf->SetMargins(5, 5, 5);
        $this->pdf->AddPage();
        $this->pdf->SetAutoPageBreak(true, 5);
        $this->pdf->SetFont('Arial', 'B', 16);
        $this->tipoReporte = $tipoReporte;
        $this->datosCabecera = $datosCabecera;
        $this->datosDetalle = $datosDetalle;
        $this->datosPagos = $datosPagos;
        $this->dataVencimientos = $dataVencimientos;

        echo '<span>GENERANDO PDF ' . $tipoReporte . ': ' . $this->datosCabecera['CCI_EMPRESA'] . ' - ' . $this->datosCabecera['NCI_DOCUMENTO'] . '</span><br>';
    }

    protected function generarLogo($x, $y, $w, $h) {
        $this->pdf->Image($this->datosCabecera['CCI_RUTA_LOGO'], $x, $y, $w, $h);
    }

    protected function generarInformacionEmpresa($x, $y) {
        $auxX = $x;
        $auxY = $y;
        $xRectanguloDatosEmpresa = $x - 5;
        $yRectanguloDatosEmpresa = $y - 4;
        $wRectanguloDatosEmpresa = 100;
        $hRectanguloDatosEmpresa = 40;

        $this->pdf->SetFont('Arial', '', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $this->datosCabecera['CNO_EMPRESA']);
        
        $x = $auxX + 85; //25;
        $this->pdf->SetFont('Arial', '', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $this->datosCabecera['CCI_SUCURSAL_AUX']);

        $y = $y + 7;
        $x = $auxX;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode('Dirección'));
        $y = $y + 5;
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, 'Matriz:');

        $x = $auxX + 20; //25;
        $y = $auxY + 7; //75;
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $this->datosCabecera['CTX_DIRECCION_EMPRESA']);

        $x = $auxX; //10;
        $y = $auxY + 19; //87;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode('Dirección'));
        $y = $y + 5;
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, 'Sucursal:');

        $x = $auxX + 20; //25;
        $y = $auxY + 19; //87;
        $this->pdf->SetFont('');
        $this->pdf->SetFontSize(7);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $this->datosCabecera['CTX_DIRECCION_SUCURSAL']);

        $x = $auxX;
        $y = $auxY + 31; //99;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode('OBLIGADO A LLEVAR CONTABILIDAD'));

        $x = $auxX + 60; //70;
        $y = $auxY + 31; //99;
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $this->datosCabecera['CTX_OBLIGADO_CONTABILIDAD']);

        $this->pdf->Rect($xRectanguloDatosEmpresa, $yRectanguloDatosEmpresa, $wRectanguloDatosEmpresa, $hRectanguloDatosEmpresa);
    }

    protected function generarInformacionFactura($x, $y) {
        $auxX = $x;
        $auxY = $y;
        $xRectanguloRucEmisor = $x - 3; //107;
        $yRectanguloRucEmisor = $y - 5; //5;
        $wRectanguloRucEmisor = 98;
        $hRectanguloRucEmisor = 79;
        $cciClaveAcceso = $this->datosCabecera['CCI_CLAVE_ACCESO'];

        $tipoDocumento = "";
        switch ($this->tipoReporte) {
            case 'FAC':
                $tipoDocumento = utf8_decode("F A C T U R A");
                break;
            case 'NC':
                $tipoDocumento = utf8_decode("N O T A   D E   C R É D I T O");
                break;
            case 'RET':
                $tipoDocumento = utf8_decode("COMPROBANTE DE RETENCIÓN");
                break;
            case 'GUI':
                $tipoDocumento = utf8_decode("GUIA DE REMISIÓN");
                break;
        }

        $this->pdf->SetFont('Arial', 'B', 11);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, "R.U.C.:");

        $x = $auxX + 20;
        $this->pdf->SetFont(''); //remove font bold
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $this->datosCabecera['CCI_RUC_EMPRESA']);

        $x = $auxX;
        $y = $y + 7;
        $this->pdf->SetFont('Arial', 'B', 11);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $tipoDocumento);

        $y = $y + 5;
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, "No.   " . $this->datosCabecera['NCI_DOCUMENTO_COMPLETO']);

        $y = $y + 7;
        $this->pdf->SetFont('Arial', 'B', 11);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("NUMERO DE AUTORIZACIÓN"));

        $y = $y + 5;
        $this->pdf->SetFont('Arial', 'B', 9);
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $this->datosCabecera['NUMERO_AUTORIZACION_WS']);

        $y = $y + 7;
        $this->pdf->SetFont('Arial', 'B', 11);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, "FECHA Y HORA DE");
        $y = $y + 5;
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("AUTORIZACIÓN"));

        $x = $auxX + 45; //155;
        $y = $y - 3;
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $this->datosCabecera['FECHA_AUTORIZACION_WS']);

        $x = $auxX;
        $y = $y + 10;
        $this->pdf->SetFont('Arial', 'B', 11);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, "AMBIENTE:");

        $x = $auxX + 30; //140;
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode($this->datosCabecera['AMBIENTE_WS']));

        $x = $auxX;
        $y = $y + 7;
        $this->pdf->SetFont('Arial', 'B', 11);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("EMISIÓN: ") . $this->datosCabecera['EMISION']);

        /*         * ** */
        //generar codigo de barras
        $font = new BCGFontFile('librerias/barcodegen.1d-php5.v5.2.0/font/Arial.ttf', 14);

        // Don't forget to sanitize user inputs
        // The arguments are R, G, B for color.
        $color_black = new BCGColor(0, 0, 0);
        $color_white = new BCGColor(255, 255, 255);

        $drawException = null;
        try {
            //$code = new BCGcode39();
            $code = new BCGcode128();
            $code->setScale(2); // Resolution
            $code->setThickness(30); // Thickness
            $code->setForegroundColor($color_black); // Color of bars
            $code->setBackgroundColor($color_white); // Color of spaces
            //$code->setFont($font); // Font (or 0)
            $code->setFont(0); // Font (or 0)
            $code->parse($cciClaveAcceso); // Text
        } catch (Exception $exception) {
            $drawException = $exception;
        }

        /* Here is the list of the arguments
          1 - Filename (empty : display on screen)
          2 - Background color */
        $drawing = new BCGDrawing('imagenes/codigosBarra/' . $cciClaveAcceso . '.png', $color_white);
        if ($drawException) {
            $drawing->drawException($drawException);
        } else {
            $drawing->setBarcode($code);
            $drawing->draw();
        }

        // Draw (or save) the image into PNG format.
        $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);

        /*         * */

        $y = $y + 7;
        $this->pdf->SetXY($x, $y);
        $this->pdf->SetFont('');
        $this->pdf->Cell(10, 0, "CLAVE DE ACCESO");

        $y = $y + 2;
        $this->pdf->Image('imagenes/codigosBarra/' . $cciClaveAcceso . '.png', $x, $y, 90, 8);

        $y = $y + 11;

        $this->pdf->SetFont('Arial', 'B', 9);
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $this->datosCabecera['NUMERO_AUTORIZACION_WS']);

        $this->pdf->Rect($xRectanguloRucEmisor, $yRectanguloRucEmisor, $wRectanguloRucEmisor, $hRectanguloRucEmisor);
    }

    protected function generarInformacionCliente($x, $y) {
        $auxX = $x;
        $auxY = $y;
        $xRectanguloDatosCliente = $x - 5;
        $yRectanguloDatosCliente = $y - 4;
        $wRectanguloDatosCliente = 200;
        $hRectanguloDatosCliente = 20;

        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Razón Social/Nombres y Apellidos:"));

        $x = $auxX + 50;
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode($this->datosCabecera['CNO_CLIENTE']));

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
        $y = $y + 6;
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
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Guia Remisión:"));

        $x = $auxX + 165; //175;   
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $this->datosCabecera['NCI_GUIA_REM']);

        $x = $auxX;
        $y = $y + 6;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Dirección:"));

        $x = $auxX + 15; //25;
        $this->pdf->SetFont('');
        $this->pdf->SetFontSize(7);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $this->datosCabecera['CTX_DIRECCION_CLIENTE']);

        $x = $auxX + 105; //175;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Ciudad:"));

        $x = $auxX + 117; //175;
        $this->pdf->SetFont('');
        $this->pdf->SetFontSize(7);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $this->datosCabecera['CNO_CIUDAD']);

        $x = $auxX + 165; //175;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Código:"));

        $x = $auxX + 182; //192;   
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $this->datosCabecera['CCI_CLIENTE']);

        $this->pdf->Rect($xRectanguloDatosCliente, $yRectanguloDatosCliente, $wRectanguloDatosCliente, $hRectanguloDatosCliente);
    }

    private function generarInformacionClienteNC($x, $y, $datosCliente) {
        $auxX = $x;
        $auxY = $y;
        $xRectanguloDatosCliente = $x - 5;
        $yRectanguloDatosCliente = $y - 4;
        $wRectanguloDatosCliente = 200;
        $hRectanguloDatosCliente = 35;

        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Razón Social/Nombres y Apellidos:"));

        $x = $auxX + 50;
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $datosCliente['CNO_CLIENTE']);

        $x = $auxX;
        $y = $y + 6;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Fecha de Emisión:"));

        $x = $auxX + 30; //40;        
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $datosCliente['DFM_FECHA']);

        $x = $auxX + 140; //150;
        $y = $auxY;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Identificación:"));

        $x = $auxX + 165; //175;   
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $datosCliente['CCI_RUC_CLIENTE']);

        $x = $auxX;
        $y = $auxY + 10;
        $this->pdf->SetXY($x, $y);
        $this->pdf->line($x, $y, $x + 190, $y);

        $x = $auxX;
        $y = $y + 4;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Comprobante que se modifica:"));

        $this->pdf->SetFont('');
        $y = $y + 2;
        $x = $auxX + 120; //130;
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $datosCliente['COMPROBANTE_MODIFICA']);

        $x = $auxX + 150; //160;
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $datosCliente['NCI_DOCUMENTO_MODIFICA']);

        $x = $auxX;
        $y = $y + 6;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Fecha Emisión(Comprobante a modificar):"));

        $x = $auxX + 120; //130;
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $datosCliente['DFM_FECHA_MODIFICA']);

        $x = $auxX + 140; //150;      
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Código:"));

        $x = $auxX + 160; //170;
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $datosCliente['CCI_CLIENTE']);

        $x = $auxX;
        $y = $y + 6;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Razón de Modificación:"));

        $x = $auxX + 120; //130;
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $datosCliente['CTX_DESCRIPCION']);

        $this->pdf->Rect($xRectanguloDatosCliente, $yRectanguloDatosCliente, $wRectanguloDatosCliente, $hRectanguloDatosCliente);
    }

    private function generarInformacionClienteRetencion($x, $y, $datosCliente) {
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
        $this->pdf->Cell(10, 0, $datosCliente['CNO_CLIENTE']);

        $x = $auxX;
        $y = $y + 6;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Fecha de Emisión:"));

        $x = $auxX + 30; //40;        
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $datosCliente['DFM_FECHA']);

        $x = $auxX + 140; //150;
        $y = $auxY;
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, utf8_decode("Identificación:"));

        $x = $auxX + 165; //175;   
        $this->pdf->SetFont('');
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(10, 0, $datosCliente['CCI_RUC_CLIENTE']);

        $this->pdf->Rect($xRectanguloDatosCliente, $yRectanguloDatosCliente, $wRectanguloDatosCliente, $hRectanguloDatosCliente);
    }

    protected function generarInformacionDetalle($x, $y) {
        $auxX = $x;
        $auxY = $y;

        $this->pdf->SetXY($x, $y);
        $this->myCell(20, 10, 'Cod. Principal');
        $this->pdf->Cell(13, 10, 'Piezas', 1, 0, 'C');
        $this->pdf->Cell(92, 10, utf8_decode('Descripción'), 1, 0, 'C');
        $this->pdf->Cell(15, 10, 'Cant', 1, 0, 'C');
        $this->myCell(20, 10, 'Precio Unitario');
        $this->pdf->Cell(20, 10, utf8_decode('Descuento'), 1, 0, 'C');
        $this->pdf->Cell(20, 10, utf8_decode('Precio Total'), 1, 0, 'C');

        $x = $auxX;
        $y = $this->pdf->GetY() + 10;
        $this->pdf->SetXY($x, $y);
        $altoCelda = 5;

        $totalPiezas = 0;
        $totalCantidad = 0;
        foreach ($this->datosDetalle as $keyDetalle => $valueDetalle) {
            $codigoPrincipal = $valueDetalle['CCI_ITEM'];
            $codigoAuxiliar = $valueDetalle['PIEZAS'];
            $descripcion = utf8_decode($valueDetalle['CTX_DESCRIPCION']);
            $detalleAdicional = '';
            $cantidad = $valueDetalle['NQN_CANTIDAD'];
            $precioUnitario = $valueDetalle['NVA_PRECIO_UNITARIO'];
            $descuento = number_format($valueDetalle['NVA_DESCUENTO'], 4, '.', ',');
            $valorIva = $valueDetalle['NVA_IVA'];
            $precioTotal = number_format($valueDetalle['NVA_PRECIO_TOTAL'], 4, '.', ',');

            $totalPiezas = $totalPiezas + $valueDetalle['PIEZAS'];
            $totalCantidad = $totalCantidad + $valueDetalle['NQN_CANTIDAD'];

            $this->pdf->Cell(20, $altoCelda, $codigoPrincipal, 1, 0, 'C');
            $this->pdf->Cell(13, $altoCelda, $codigoAuxiliar, 1, 0, 'C');

            if (strlen($descripcion) <= 58) {
                $this->pdf->Cell(92, $altoCelda, $descripcion, 1, 0, 'L');
            } else {
                $this->pdf->SetFontSize(7.4);
                $this->myCell2(92, $altoCelda, $descripcion);
                $this->pdf->SetFontSize(8);
            }

            $this->pdf->Cell(15, $altoCelda, $cantidad, 1, 0, 'R');
            $this->pdf->Cell(20, $altoCelda, $precioUnitario, 1, 0, 'R');
            $this->pdf->Cell(20, $altoCelda, $descuento, 1, 0, 'R');
            $this->pdf->Cell(20, $altoCelda, $precioTotal, 1, 0, 'R');

            $this->pdf->Ln();
        }

        $totalCantidad = number_format($totalCantidad, 2, '.', ',');

        $this->pdf->Cell(20, $altoCelda, 'Tot. Piezas', 1, 0, 'C');
        $this->pdf->Cell(13, $altoCelda, $totalPiezas, 1, 0, 'C');
        $this->pdf->Cell(78, $altoCelda, '', 'TBL', 0, 'C');
        $this->pdf->Cell(14, $altoCelda, 'Tot. Mts', 'B', 0, 'C');
        $this->pdf->Cell(15, $altoCelda, $totalCantidad, 1, 0, 'R');
        $this->pdf->Cell(60, $altoCelda, '', 1, 0, 'C');
        $this->pdf->Ln();
    }

    private function generarInformacionDetalleRetencion($x, $y, $datosDetalle) {
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

        foreach ($datosDetalle as $keyDetalle => $valueDetalle) {
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

    protected function generarInformacionAdicional($x, $y) {
        $auxX = $x;
        $auxY = $y;

        $hRectanguloPieInformacionAdicional = 0;

        $direccionCliente = $this->datosCabecera['CTX_DIRECCION_CLIENTE'];
        $telefonoCliente = $this->datosCabecera['CTX_TELEFONO_CLIENTE'];
        $emailCliente = $this->datosCabecera['CTX_MAIL_CLIENTE'];

        if (strlen($direccionCliente) > 0 || strlen($telefonoCliente) > 0 || strlen($emailCliente) > 0) {
            $yRectanguloPieInformacionAdicional = $y;
            $hRectanguloPieInformacionAdicional = 6;

            if (strlen($direccionCliente) == 0 && strlen($telefonoCliente) == 0 && strlen($emailCliente) == 0) {
                return;
            }

            $tituloAdicional = utf8_decode('Información Adicional');
            $tituloDireccion = utf8_decode('Dirección');
            $tituloTelefono = utf8_decode('Teléfono');
            $tituloEmail = utf8_decode('Email');

            $this->pdf->SetXY($x, $y);
            $this->pdf->SetFont('Arial', 'B', 8);
            $this->pdf->Cell(125, 10, $tituloAdicional, 0, 0, 'L');
            $this->pdf->SetFont('');

            $altoCelda = 5;

            if ($direccionCliente != '') {
                $y = $y + 7;
                $this->pdf->SetXY($x, $y);
                $this->pdf->Cell(30, $altoCelda, $tituloDireccion, 0, 0, 'L');
                $this->pdf->SetFontSize(7);
                $this->pdf->Cell(95, $altoCelda, $direccionCliente, 0, 0, 'L');
                $this->pdf->SetFontSize(8);

                $hRectanguloPieInformacionAdicional = $hRectanguloPieInformacionAdicional + 9;
            }

            if ($telefonoCliente != '') {
                $x = $auxX;
                $y = $y + 4;
                $this->pdf->SetXY($x, $y);
                $this->pdf->Cell(30, $altoCelda, $tituloTelefono, 0, 0, 'L');
                $this->pdf->Cell(95, $altoCelda, $telefonoCliente, 0, 0, 'L');

                $hRectanguloPieInformacionAdicional = $hRectanguloPieInformacionAdicional + 7;
            }

            if ($emailCliente != '') {
                $x = $auxX;
                $y = $y + 4;
                $this->pdf->SetXY($x, $y);
                $this->pdf->Cell(30, $altoCelda, $tituloEmail, 0, 0, 'L');
                $this->pdf->Cell(95, $altoCelda, $emailCliente, 0, 0, 'L');

                $hRectanguloPieInformacionAdicional = $hRectanguloPieInformacionAdicional + 3;
            }

            $xRectanguloPieInformacionAdicional = $auxX;
            $wRectanguloPieInformacionAdicional = 125;

            $this->pdf->Rect($xRectanguloPieInformacionAdicional, $yRectanguloPieInformacionAdicional, $wRectanguloPieInformacionAdicional, $hRectanguloPieInformacionAdicional);
        }

        return $hRectanguloPieInformacionAdicional;
    }

    protected function generarInformacionTotales($x, $y) {
        $auxX = $x;
        $auxY = $y;

        $tituloSubtotal = "SUBTOTAL " . $this->datosCabecera['NQT_PORC_IVA'] . '%';
        $tituloIva = "IVA " . $this->datosCabecera['NQT_PORC_IVA'] . '%';

        $subtotal = number_format($this->datosCabecera['NVA_SUBTOTAL_12'], 2, '.', ',');
        $subtotalIvaCero = number_format($this->datosCabecera['SUBTOTAL_IVA_0'], 2, '.', ',');
        $subtotalNoObjeto = number_format($this->datosCabecera['SUBTOTAL_NO_OBJETO_IVA'], 2, '.', ',');
        $subtotalExento = number_format($this->datosCabecera['SUBTOTAL_EXENTO_IVA'], 2, '.', ',');
        $subtotalSinImpuestos = number_format($this->datosCabecera['SUBTOTAL_SIN_IMPUESTOS'], 2, '.', ',');
        $descuento = number_format($this->datosCabecera['NVA_DESCUENTO'], 2, '.', ',');
        $ice = number_format($this->datosCabecera['ICE'], 2, '.', ',');
        $iva = number_format($this->datosCabecera['NVA_IVA'], 2, '.', ',');
        $irbpnr = number_format($this->datosCabecera['IRBPNR'], 2, '.', ',');
        $propina = number_format($this->datosCabecera['PROPINA'], 2, '.', ',');
        $valorTotal = number_format($this->datosCabecera['NVA_TOTAL'], 2, '.', ',');

        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(50, 5, $tituloSubtotal, 1, 0, 'L');
        $this->pdf->Cell(21, 5, $subtotal, 1, 0, 'R');

        $x = 134;
        $y = $y + 5;
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(50, 5, "SUBTOTAL IVA 0%", 1, 0, 'L');
        $this->pdf->Cell(21, 5, $subtotalIvaCero, 1, 0, 'R');

        $x = 134;
        $y = $y + 5;
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(50, 5, "SUBTOTAL NO OBJETO IVA", 1, 0, 'L');
        $this->pdf->Cell(21, 5, $subtotalNoObjeto, 1, 0, 'R');

        $x = 134;
        $y = $y + 5;
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(50, 5, "SUBTOTAL EXENTO IVA", 1, 0, 'L');
        $this->pdf->Cell(21, 5, $subtotalExento, 1, 0, 'R');

        $x = 134;
        $y = $y + 5;
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(50, 5, "SUBTOTAL SIN IMPUESTOS", 1, 0, 'L');
        $this->pdf->Cell(21, 5, $subtotalSinImpuestos, 1, 0, 'R');

        if ($this->tipoReporte == 'FAC') {
            $x = 134;
            $y = $y + 5;
            $this->pdf->SetXY($x, $y);
            $this->pdf->Cell(50, 5, "DESCUENTO", 1, 0, 'L');
            $this->pdf->Cell(21, 5, $descuento, 1, 0, 'R');
        }

        $x = 134;
        $y = $y + 5;
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(50, 5, "ICE", 1, 0, 'L');
        $this->pdf->Cell(21, 5, $ice, 1, 0, 'R');

        $x = 134;
        $y = $y + 5;
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(50, 5, $tituloIva, 1, 0, 'L');
        $this->pdf->Cell(21, 5, $iva, 1, 0, 'R');

        $x = 134;
        $y = $y + 5;
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(50, 5, "IRBPNR", 1, 0, 'L');
        $this->pdf->Cell(21, 5, $irbpnr, 1, 0, 'R');

        if ($this->tipoReporte == 'FAC') {
            $x = 134;
            $y = $y + 5;
            $this->pdf->SetXY($x, $y);
            $this->pdf->Cell(50, 5, "PROPINA", 1, 0, 'L');
            $this->pdf->Cell(21, 5, $propina, 1, 0, 'R');
        }

        $x = 134;
        $y = $y + 5;
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(50, 5, "VALOR TOTAL", 1, 0, 'L');
        $this->pdf->Cell(21, 5, $valorTotal, 1, 0, 'R');
    }

    protected function generarInformacionFormasPago($x, $y) {
        $auxX = $x;
        $auxY = $y;

        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell(80, 5, "Forma de Pago", 1, 0, 'C');
        $this->pdf->Cell(20, 5, "Valor", 1, 0, 'C');

        foreach ($this->datosPagos as $keyPagos => $valuePagos) {
            $x = $auxX;
            $y = $y + 5;
            $this->pdf->SetXY($x, $y);
            $this->pdf->Cell(80, 5, utf8_decode($valuePagos['FORMA_PAGO']), 1, 0, 'L');
            $this->pdf->Cell(20, 5, number_format($valuePagos['NVA_VALOR_PAGO'], 2, '.', ','), 1, 0, 'R');
        }
    }

    protected function grabarPdf() {
        $this->pdf->Output($this->datosCabecera['CCI_RUTA_PDF_COMPLETA'], 'F');
        echo '<hr>';
    }

    protected function myCell($w, $h, $t) {
        $pieces = explode(" ", $t);

        if (count($pieces) > 1) {
            $height = $h / 2;

            $texto1 = $pieces[0];
            $texto2 = $pieces[1];

            $x = $this->pdf->GetX();
            $y = $this->pdf->GetY();
            $this->pdf->SetXY($x, $y);
            $this->pdf->Cell($w, $height, $texto1, 'LTR', 0, 'C');

            $y = $this->pdf->GetY() + $height;
            $this->pdf->SetXY($x, $y);
            $this->pdf->Cell($w, $height, $texto2, 'LRB', 0, 'C');

            $x = $this->pdf->GetX();
            $this->pdf->SetXY($x, $y - $height);
        } else {
            
        }
    }

    function myCell2($w, $h, $t) {
        $pieces = explode(" ", $t);

        if (count($pieces) > 1) {
            $height = $h / 2;
        }

        $texto1 = '';
        $texto2 = '';
        for ($i = 0; $i < count($pieces); $i++) {
            if ((strlen($texto1) + strlen($pieces[$i])) <= 29) {
                $texto1 = $texto1 . $pieces[$i] . ' ';
            } else {
                $texto2 = $texto2 . $pieces[$i] . ' ';
            }
        }

        $x = $this->pdf->GetX();
        $y = $this->pdf->GetY();
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell($w, $height, $texto1, 'LTR', 0, 'L');

        $y = $this->pdf->GetY() + $height;
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell($w, $height, $texto2, 'LRB', 0, 'L');

        $x = $this->pdf->GetX();
        $this->pdf->SetXY($x, $y - $height);
    }

    function myCell3($w, $h, $t, $l) {
        $pieces = explode(" ", $t);

        if (count($pieces) > 1) {
            $height = $h / 2;
        }

        $texto1 = '';
        $texto2 = '';
        for ($i = 0; $i < count($pieces); $i++) {
            if ((strlen($texto1) + strlen($pieces[$i])) <= $l) {
                $texto1 = $texto1 . $pieces[$i] . ' ';
            } else {
                $texto2 = $texto2 . $pieces[$i] . ' ';
            }
        }

        $x = $this->pdf->GetX();
        $y = $this->pdf->GetY();
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell($w, $height, $texto1, '', 0, 'L');

        $y = $this->pdf->GetY() + $height;
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell($w, $height, $texto2, '', 0, 'L');

        $x = $this->pdf->GetX();
        $this->pdf->SetXY($x, $y - $height);
    }

    public function getPageBreakTrigger() {
        return $this->pdf->GetPageBreakTrigger();
    }

}
