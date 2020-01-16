<?php

include_once 'config.inc.php';
include_once 'ClaseBaseDatos.php';
include_once 'ClaseJson.php';

/**
 * Description of ClaseGenerarXml
 *
 * @author jpsanchez
 */
class ClaseGenerarXml {

    private $codDoc;
    private $dataLog = array();
    public $errorDB;

    public function generaXmlFactura($dataEmpresa, $dataDocumento, $dataPagos, $dataDetalle) {
        $this->codDoc = '01';

//        echo '<hr>';
//        print_r($dataDocumento);
//        echo '<hr>';

        $cci_empresa = $dataDocumento['CCI_EMPRESA'];
        $cci_sucursal = $dataDocumento['CCI_SUCURSAL'];
        $cci_cliente = $dataDocumento['CCI_CLIENTE'];
        $nci_documento = $dataDocumento['NCI_DOCUMENTO'];
        $estab = $dataDocumento['ESTAB'];
        $ptoEmi = $dataDocumento['PTOEMI'];
        $secuencial = $dataDocumento['SECUENCIAL'];
        $dfmFechaAux = $dataDocumento['DFM_FECHA_AUX'];
        $fechaEmision = $dataDocumento['DFM_FECHA'];
        $tipoIdentificacionComprador = $dataDocumento['TIPO_IDENTIFICACION'];
        $guiaRemision = $dataDocumento['NCI_GUIA_REM'];
        $razonSocialComprador = $this->xmlEscape($dataDocumento['CNO_CLIENTE']);
        $identificacionComprador = $dataDocumento['CCI_RUC'];
        $direccionComprador = $dataDocumento['CTX_DIRECCION'];
        $totalSinImpuestos = $dataDocumento['NVA_SUBTOTAL'];
        $totalDescuento = $dataDocumento['NVA_DESCUENTO'];
        $codigoIva = 2;
        $codigoPorcentajeIva = $dataDocumento['CODIGO_PORCENTAJE_IVA'];
        $baseImponible = $dataDocumento['NVA_SUBTOTAL'];
        $tarifa = $dataDocumento['NQT_PORC_IVA'];
        $valor = $dataDocumento['NVA_IVA'];
        $propina = 0;
        $importeTotal = $dataDocumento['NVA_TOTAL'];
        $total = $dataDocumento['NVA_TOTAL'];
        $ctx_telefono = $dataDocumento['CTX_TELEFONO'];
        $ctx_mail = $dataDocumento['CTX_MAIL'];

        $rutaGenerados = $dataEmpresa['CCI_RUTA_GENERADOS'] . 'FAC\\';

        //echo '<span>GENERANDO XML FACTURA: ' . $cci_empresa . ' - ' . $nci_documento . '</span><br>';

        $nombreArchivo = 'xml_' . $secuencial;

        $numero = $dfmFechaAux . $this->codDoc . $dataEmpresa['CCI_RUC'] . $dataEmpresa['AMBIENTE'] . $estab . $ptoEmi . $secuencial . $dataEmpresa['CODIGO_NUMERICO'] . $dataEmpresa['TIPO_EMISION'];

        $claveAcceso = $this->getClaveAcceso($numero);

        $xml = new DomDocument('1.0', 'UTF-8');

        $facturaXml = $xml->createElement('factura');
        $facturaXml->setAttribute('xmlns:ns2', 'http://www.w3.org/2000/09/xmldsig#');
        $facturaXml->setAttribute('id', 'comprobante');
        $facturaXml->setAttribute('version', '2.1.0');
        $facturaXml = $xml->appendChild($facturaXml);

        $infoTributariaXml = $xml->createElement('infoTributaria');
        $infoTributariaXml = $facturaXml->appendChild($infoTributariaXml);

        $ambienteXml = $xml->createElement('ambiente', $dataEmpresa['AMBIENTE']);
        $ambienteXml = $infoTributariaXml->appendChild($ambienteXml);

        $tipoEmisionXml = $xml->createElement('tipoEmision', $dataEmpresa['TIPO_EMISION']);
        $tipoEmisionXml = $infoTributariaXml->appendChild($tipoEmisionXml);

        $razonSocialXml = $xml->createElement('razonSocial', $dataEmpresa['CNO_EMPRESA']);
        $razonSocialXml = $infoTributariaXml->appendChild($razonSocialXml);

        $rucXml = $xml->createElement('ruc', $dataEmpresa['CCI_RUC']);
        $rucXml = $infoTributariaXml->appendChild($rucXml);

        $claveAccesoXml = $xml->createElement('claveAcceso', $claveAcceso);
        $claveAccesoXml = $infoTributariaXml->appendChild($claveAccesoXml);

        $codDocXml = $xml->createElement('codDoc', $this->codDoc);
        $codDocXml = $infoTributariaXml->appendChild($codDocXml);

        $estabXml = $xml->createElement('estab', $estab);
        $estabXml = $infoTributariaXml->appendChild($estabXml);

        $ptoEmiXml = $xml->createElement('ptoEmi', $ptoEmi);
        $ptoEmiXml = $infoTributariaXml->appendChild($ptoEmiXml);

        $secuencialXml = $xml->createElement('secuencial', $secuencial);
        $secuencialXml = $infoTributariaXml->appendChild($secuencialXml);

        $dirMatrizXml = $xml->createElement('dirMatriz', $dataEmpresa['CTX_DIRECCION']);
        $dirMatrizXml = $infoTributariaXml->appendChild($dirMatrizXml);

        $infoFacturaXml = $xml->createElement('infoFactura');
        $infoFacturaXml = $facturaXml->appendChild($infoFacturaXml);

        $fechaEmisionXml = $xml->createElement('fechaEmision', $fechaEmision);
        $fechaEmisionXml = $infoFacturaXml->appendChild($fechaEmisionXml);

        $dirEstablecimientoXml = $xml->createElement('dirEstablecimiento', $dataDocumento['CTX_DIRECCION_SUCURSAL']);
        $dirEstablecimientoXml = $infoFacturaXml->appendChild($dirEstablecimientoXml);

        $obligadoContabilidadXml = $xml->createElement('obligadoContabilidad', $dataEmpresa['CTX_OBLIGADO_CONTABILIDAD']);
        $obligadoContabilidadXml = $infoFacturaXml->appendChild($obligadoContabilidadXml);

        $tipoIdentificacionCompradorXml = $xml->createElement('tipoIdentificacionComprador', $tipoIdentificacionComprador);
        $tipoIdentificacionCompradorXml = $infoFacturaXml->appendChild($tipoIdentificacionCompradorXml);

        if ($guiaRemision != '') {
            $guiaRemisionXml = $xml->createElement('guiaRemision', $guiaRemision);
            $guiaRemisionXml = $infoFacturaXml->appendChild($guiaRemisionXml);
        }

        $razonSocialCompradorXml = $xml->createElement('razonSocialComprador', $razonSocialComprador);
        $razonSocialCompradorXml = $infoFacturaXml->appendChild($razonSocialCompradorXml);

        $identificacionCompradorXml = $xml->createElement('identificacionComprador', $identificacionComprador);
        $identificacionCompradorXml = $infoFacturaXml->appendChild($identificacionCompradorXml);

        $direccionCompradorXml = $xml->createElement('direccionComprador', $direccionComprador);
        $direccionCompradorXml = $infoFacturaXml->appendChild($direccionCompradorXml);

        $totalSinImpuestosXml = $xml->createElement('totalSinImpuestos', $totalSinImpuestos);
        $totalSinImpuestosXml = $infoFacturaXml->appendChild($totalSinImpuestosXml);

        $totalDescuentoXml = $xml->createElement('totalDescuento', $totalDescuento);
        $totalDescuentoXml = $infoFacturaXml->appendChild($totalDescuentoXml);

        $totalConImpuestosXml = $xml->createElement('totalConImpuestos');
        $totalConImpuestosXml = $infoFacturaXml->appendChild($totalConImpuestosXml);

        $totalImpuestoXml = $xml->createElement('totalImpuesto');
        $totalImpuestoXml = $totalConImpuestosXml->appendChild($totalImpuestoXml);

        $codigoXml = $xml->createElement('codigo', $codigoIva);
        $codigoXml = $totalImpuestoXml->appendChild($codigoXml);

        $codigoPorcentajeXml = $xml->createElement('codigoPorcentaje', $codigoPorcentajeIva);
        $codigoPorcentajeXml = $totalImpuestoXml->appendChild($codigoPorcentajeXml);

        $baseImponibleXml = $xml->createElement('baseImponible', $baseImponible);
        $baseImponibleXml = $totalImpuestoXml->appendChild($baseImponibleXml);

        $tarifaXml = $xml->createElement('tarifa', $tarifa);
        $tarifaXml = $totalImpuestoXml->appendChild($tarifaXml);

        $valorXml = $xml->createElement('valor', $valor);
        $valorXml = $totalImpuestoXml->appendChild($valorXml);

        $propinaXml = $xml->createElement('propina', $propina);
        $propinaXml = $infoFacturaXml->appendChild($propinaXml);

        $importeTotalXml = $xml->createElement('importeTotal', $importeTotal);
        $importeTotalXml = $infoFacturaXml->appendChild($importeTotalXml);

        $monedaXml = $xml->createElement('moneda', $dataEmpresa['MONEDA']);
        $monedaXml = $infoFacturaXml->appendChild($monedaXml);

        $pagosXml = $xml->createElement('pagos');
        $pagosXml = $infoFacturaXml->appendChild($pagosXml);

        foreach ($dataPagos as $keyPagos => $valuePagos) {
            $formaPago = $valuePagos['COD_FORMA_PAGO'];
            $totalPago = $valuePagos['NVA_VALOR_PAGO'];
            $plazo = $valuePagos['NQN_PLAZO'];
            $unidadTiempo = $valuePagos['TIEMPO'];

            $pagoXml = $xml->createElement('pago');
            $pagoXml = $pagosXml->appendChild($pagoXml);

            $formaPagoXml = $xml->createElement('formaPago', $formaPago);
            $formaPagoXml = $pagoXml->appendChild($formaPagoXml);

            $totalXml = $xml->createElement('total', $totalPago);
            $totalXml = $pagoXml->appendChild($totalXml);

            if ($plazo > 0) {
                $plazoXml = $xml->createElement('plazo', $plazo);
                $plazoXml = $pagoXml->appendChild($plazoXml);

                $unidadTiempoXml = $xml->createElement('unidadTiempo', $unidadTiempo);
                $unidadTiempoXml = $pagoXml->appendChild($unidadTiempoXml);
            }
        }

        $detallesXml = $xml->createElement('detalles');
        $detallesXml = $facturaXml->appendChild($detallesXml);

        foreach ($dataDetalle as $keyDetalle => $valueDetalle) {
            $detalleXml = $xml->createElement('detalle');
            $detalleXml = $detallesXml->appendChild($detalleXml);

            $codigoPrincipal = $valueDetalle['CCI_ITEM'];
            $descripcion = $valueDetalle['CTX_DESCRIPCION'];
            $cantidad = $valueDetalle['NQN_CANTIDAD'];
            $precioUnitario = $valueDetalle['NVA_PRECIO_UNITARIO'];
            $descuento = $valueDetalle['NVA_DESCUENTO'];
            $valorIva = $valueDetalle['NVA_IVA'];
            $precioTotalSinImpuesto = $valueDetalle['NVA_PRECIO_TOTAL'];

            $codigoPrincipalXml = $xml->createElement('codigoPrincipal', $codigoPrincipal);
            $codigoPrincipalXml = $detalleXml->appendChild($codigoPrincipalXml);

            $descripcionXml = $xml->createElement('descripcion', $descripcion);
            $descripcionXml = $detalleXml->appendChild($descripcionXml);

            $cantidadXml = $xml->createElement('cantidad', $cantidad);
            $cantidadXml = $detalleXml->appendChild($cantidadXml);

            $precioUnitarioXml = $xml->createElement('precioUnitario', $precioUnitario);
            $precioUnitarioXml = $detalleXml->appendChild($precioUnitarioXml);

            $descuentoXml = $xml->createElement('descuento', $descuento);
            $descuentoXml = $detalleXml->appendChild($descuentoXml);

            $precioTotalSinImpuestoXml = $xml->createElement('precioTotalSinImpuesto', $precioTotalSinImpuesto);
            $precioTotalSinImpuestoXml = $detalleXml->appendChild($precioTotalSinImpuestoXml);

            $impuestosXml = $xml->createElement('impuestos');
            $impuestosXml = $detalleXml->appendChild($impuestosXml);

            $impuestoXml = $xml->createElement('impuesto');
            $impuestoXml = $impuestosXml->appendChild($impuestoXml);

            $codigoXml = $xml->createElement('codigo', $codigoIva);
            $codigoXml = $impuestoXml->appendChild($codigoXml);

            $codigoPorcentajeIvaXml = $xml->createElement('codigoPorcentaje', $codigoPorcentajeIva);
            $codigoPorcentajeIvaXml = $impuestoXml->appendChild($codigoPorcentajeIvaXml);

            $tarifaXml = $xml->createElement('tarifa', $tarifa);
            $tarifaXml = $impuestoXml->appendChild($tarifaXml);

            $baseImponibleXml = $xml->createElement('baseImponible', $precioTotalSinImpuesto);
            $baseImponibleXml = $impuestoXml->appendChild($baseImponibleXml);

            $valorXml = $xml->createElement('valor', $valorIva);
            $valorXml = $impuestoXml->appendChild($valorXml);
        }

        if (strlen($direccionComprador) > 0 && strlen($ctx_telefono) > 0 && strlen($ctx_mail) > 0) {
            $infoAdicionalXml = $xml->createElement('infoAdicional');
            $infoAdicionalXml = $facturaXml->appendChild($infoAdicionalXml);

            if ($direccionComprador != '') {
                $campoAdicionalXml = $xml->createElement('campoAdicional', $direccionComprador);
                $campoAdicionalXml = $infoAdicionalXml->appendChild($campoAdicionalXml);
                $campoAdicionalXml->setAttribute('nombre', 'Dirección');
            }

            if ($ctx_telefono != '') {
                $campoAdicionalXml = $xml->createElement('campoAdicional', $ctx_telefono);
                $campoAdicionalXml = $infoAdicionalXml->appendChild($campoAdicionalXml);
                $campoAdicionalXml->setAttribute('nombre', 'Teléfono');
            }

            if ($ctx_mail != '') {
                $campoAdicionalXml = $xml->createElement('campoAdicional', $ctx_mail);
                $campoAdicionalXml = $infoAdicionalXml->appendChild($campoAdicionalXml);
                $campoAdicionalXml->setAttribute('nombre', 'Email');
            }
        }

        $xml->formatOutput = true;
        $xml->xmlStandalone = true;
        $xml->saveXML();
        $xml->save($rutaGenerados . $claveAcceso . '.xml');
        
        if (file_exists($rutaGenerados . $claveAcceso . '.xml')) {
            $this->setDataLog($cci_empresa, $cci_sucursal, $cci_cliente, 'FAC', $nci_documento, $claveAcceso, 'GENERAR', 'G');
        } else {
            echo 'error al grabar el archivo';
        }

        //echo '<hr>';
    }

    public function generaXmlNC($dataEmpresa, $dataDocumento, $dataDetalle) {
        $this->codDoc = '04';
        $cci_empresa = $dataDocumento['CCI_EMPRESA'];
        $cci_sucursal = $dataDocumento['CCI_SUCURSAL'];
        $cci_cliente = $dataDocumento['CCI_CLIENTE'];
        $nci_documento = $dataDocumento['NCI_DOCUMENTO'];
        $estab = $dataDocumento['ESTAB'];
        $ptoEmi = $dataDocumento['PTOEMI'];
        $secuencial = $dataDocumento['SECUENCIAL'];
        $dfmFechaAux = $dataDocumento['DFM_FECHA_AUX'];
        $fechaEmision = $dataDocumento['DFM_FECHA'];
        $motivo = $dataDocumento['CTX_DESCRIPCION'];
        $tipoIdentificacionComprador = $dataDocumento['TIPO_IDENTIFICACION'];
        $razonSocialComprador = $this->xmlEscape($dataDocumento['CNO_CLIENTE']);
        $identificacionComprador = $dataDocumento['CCI_RUC'];
        $direccionComprador = $dataDocumento['CTX_DIRECCION'];
        $codDocModificado = '01'; //factura;
        $numDocModificado = $dataDocumento['NCI_COMPR_ORIGEN'];
        $fechaEmisionDocSustento = $dataDocumento['DFI_FECHA_ORIGEN'];

        $totalSinImpuestos = $dataDocumento['NVA_SUBTOTAL'];
        $totalDescuento = $dataDocumento['NVA_DESCUENTO'];
        $valorModificacion = $dataDocumento['NVA_TOTAL'];

        $codigoIva = 2;
        $codigoPorcentajeIva = $dataDocumento['CODIGO_PORCENTAJE_IVA'];
        $baseImponible = $dataDocumento['NVA_SUBTOTAL'];
        $tarifa = $dataDocumento['NQT_PORC_IVA'];
        $valor = $dataDocumento['NVA_IVA'];
        $propina = 0;

        $ctx_telefono = $dataDocumento['CTX_TELEFONO'];
        $ctx_mail = $dataDocumento['CTX_MAIL'];

        if ($ctx_mail == '') {
            $ctx_mail = $dataEmpresa['mailDefault'];
        }

        $rutaGenerados = $dataEmpresa['CCI_RUTA_GENERADOS'] . 'NC\\';

        //echo '<span>GENERANDO XML NC: ' . $cci_empresa . ' - ' . $nci_documento . '</span><br>';

        $nombreArchivo = 'xml_' . $secuencial;

        $numero = $dfmFechaAux . $this->codDoc . $dataEmpresa['CCI_RUC'] . $dataEmpresa['AMBIENTE'] . $estab . $ptoEmi . $secuencial . $dataEmpresa['CODIGO_NUMERICO'] . $dataEmpresa['TIPO_EMISION'];

        $claveAcceso = $this->getClaveAcceso($numero);

        $xml = new DomDocument('1.0', 'UTF-8');

        /*

          $facturaXml = $xml->createElement('factura');
          $facturaXml->setAttribute('xmlns:ns2', 'http://www.w3.org/2000/09/xmldsig#');
          $facturaXml->setAttribute('id', 'comprobante');
          $facturaXml->setAttribute('version', '2.1.0');
          $facturaXml = $xml->appendChild($facturaXml);
         */

        $ncXml = $xml->createElement('notaCredito');
        $ncXml->setAttribute('xmlns:ns2', 'http://www.w3.org/2000/09/xmldsig#');
        $ncXml->setAttribute('id', 'comprobante');
        $ncXml->setAttribute('version', '1.1.0');
        $ncXml = $xml->appendChild($ncXml);

        $infoTributariaXml = $xml->createElement('infoTributaria');
        $infoTributariaXml = $ncXml->appendChild($infoTributariaXml);

        $ambienteXml = $xml->createElement('ambiente', $dataEmpresa['AMBIENTE']);
        $ambienteXml = $infoTributariaXml->appendChild($ambienteXml);

        $tipoEmisionXml = $xml->createElement('tipoEmision', $dataEmpresa['TIPO_EMISION']);
        $tipoEmisionXml = $infoTributariaXml->appendChild($tipoEmisionXml);

        $razonSocialXml = $xml->createElement('razonSocial', $dataEmpresa['CNO_EMPRESA']);
        $razonSocialXml = $infoTributariaXml->appendChild($razonSocialXml);

        $rucXml = $xml->createElement('ruc', $dataEmpresa['CCI_RUC']);
        $rucXml = $infoTributariaXml->appendChild($rucXml);

        $claveAccesoXml = $xml->createElement('claveAcceso', $claveAcceso);
        $claveAccesoXml = $infoTributariaXml->appendChild($claveAccesoXml);

        $codDocXml = $xml->createElement('codDoc', $this->codDoc);
        $codDocXml = $infoTributariaXml->appendChild($codDocXml);

        $estabXml = $xml->createElement('estab', $estab);
        $estabXml = $infoTributariaXml->appendChild($estabXml);

        $ptoEmiXml = $xml->createElement('ptoEmi', $ptoEmi);
        $ptoEmiXml = $infoTributariaXml->appendChild($ptoEmiXml);

        $secuencialXml = $xml->createElement('secuencial', $secuencial);
        $secuencialXml = $infoTributariaXml->appendChild($secuencialXml);

        $dirMatrizXml = $xml->createElement('dirMatriz', $dataEmpresa['CTX_DIRECCION']);
        $dirMatrizXml = $infoTributariaXml->appendChild($dirMatrizXml);

        $infoNotaCreditoXml = $xml->createElement('infoNotaCredito');
        $infoNotaCreditoXml = $ncXml->appendChild($infoNotaCreditoXml);

        $fechaEmisionXml = $xml->createElement('fechaEmision', $fechaEmision);
        $fechaEmisionXml = $infoNotaCreditoXml->appendChild($fechaEmisionXml);

        $dirEstablecimientoXml = $xml->createElement('dirEstablecimiento', $dataDocumento['CTX_DIRECCION_SUCURSAL']);
        $dirEstablecimientoXml = $infoNotaCreditoXml->appendChild($dirEstablecimientoXml);

        $tipoIdentificacionCompradorXml = $xml->createElement('tipoIdentificacionComprador', $tipoIdentificacionComprador);
        $tipoIdentificacionCompradorXml = $infoNotaCreditoXml->appendChild($tipoIdentificacionCompradorXml);

        $razonSocialCompradorXml = $xml->createElement('razonSocialComprador', $razonSocialComprador);
        $razonSocialCompradorXml = $infoNotaCreditoXml->appendChild($razonSocialCompradorXml);

        $identificacionCompradorXml = $xml->createElement('identificacionComprador', $identificacionComprador);
        $identificacionCompradorXml = $infoNotaCreditoXml->appendChild($identificacionCompradorXml);

        $obligadoContabilidadXml = $xml->createElement('obligadoContabilidad', $dataEmpresa['CTX_OBLIGADO_CONTABILIDAD']);
        $obligadoContabilidadXml = $infoNotaCreditoXml->appendChild($obligadoContabilidadXml);

        $codDocModificadoXml = $xml->createElement('codDocModificado', $codDocModificado);
        $codDocModificadoXml = $infoNotaCreditoXml->appendChild($codDocModificadoXml);

        $numDocModificadoXml = $xml->createElement('numDocModificado', $numDocModificado);
        $numDocModificadoXml = $infoNotaCreditoXml->appendChild($numDocModificadoXml);

        $fechaEmisionDocSustentoXml = $xml->createElement('fechaEmisionDocSustento', $fechaEmisionDocSustento);
        $fechaEmisionDocSustentoXml = $infoNotaCreditoXml->appendChild($fechaEmisionDocSustentoXml);

        $totalSinImpuestosXml = $xml->createElement('totalSinImpuestos', $totalSinImpuestos);
        $totalSinImpuestosXml = $infoNotaCreditoXml->appendChild($totalSinImpuestosXml);

        $valorModificacionXml = $xml->createElement('valorModificacion', $valorModificacion);
        $valorModificacionXml = $infoNotaCreditoXml->appendChild($valorModificacionXml);

        $monedaXml = $xml->createElement('moneda', $dataEmpresa['MONEDA']);
        $monedaXml = $infoNotaCreditoXml->appendChild($monedaXml);

        $totalConImpuestosXml = $xml->createElement('totalConImpuestos');
        $totalConImpuestosXml = $infoNotaCreditoXml->appendChild($totalConImpuestosXml);

        $totalImpuestoXml = $xml->createElement('totalImpuesto');
        $totalImpuestoXml = $totalConImpuestosXml->appendChild($totalImpuestoXml);

        $codigoXml = $xml->createElement('codigo', $codigoIva);
        $codigoXml = $totalImpuestoXml->appendChild($codigoXml);

        $codigoPorcentajeXml = $xml->createElement('codigoPorcentaje', $codigoPorcentajeIva);
        $codigoPorcentajeXml = $totalImpuestoXml->appendChild($codigoPorcentajeXml);

        $baseImponibleXml = $xml->createElement('baseImponible', $baseImponible);
        $baseImponibleXml = $totalImpuestoXml->appendChild($baseImponibleXml);

        $valorXml = $xml->createElement('valor', $valor);
        $valorXml = $totalImpuestoXml->appendChild($valorXml);

        $motivoXml = $xml->createElement('motivo', $motivo);
        $motivoXml = $infoNotaCreditoXml->appendChild($motivoXml);

        $detallesXml = $xml->createElement('detalles');
        $detallesXml = $ncXml->appendChild($detallesXml);

        foreach ($dataDetalle as $keyDetalle => $valueDetalle) {
            $detalleXml = $xml->createElement('detalle');
            $detalleXml = $detallesXml->appendChild($detalleXml);

            $codigoInterno = $valueDetalle['CCI_ITEM'];
            $descripcion = $valueDetalle['CTX_DESCRIPCION'];
            $cantidad = $valueDetalle['NQN_CANTIDAD'];
            $precioUnitario = $valueDetalle['NVA_PRECIO_UNITARIO'];
            $descuento = $valueDetalle['NVA_DESCUENTO'];
            $valorIva = $valueDetalle['NVA_IVA'];
            $precioTotalSinImpuesto = $valueDetalle['NVA_PRECIO_TOTAL'];

            $codigoInternoXml = $xml->createElement('codigoInterno', $codigoInterno);
            $codigoInternoXml = $detalleXml->appendChild($codigoInternoXml);

            $descripcionXml = $xml->createElement('descripcion', $descripcion);
            $descripcionXml = $detalleXml->appendChild($descripcionXml);

            $cantidadXml = $xml->createElement('cantidad', $cantidad);
            $cantidadXml = $detalleXml->appendChild($cantidadXml);

            $precioUnitarioXml = $xml->createElement('precioUnitario', $precioUnitario);
            $precioUnitarioXml = $detalleXml->appendChild($precioUnitarioXml);

            $descuentoXml = $xml->createElement('descuento', $descuento);
            $descuentoXml = $detalleXml->appendChild($descuentoXml);

            $precioTotalSinImpuestoXml = $xml->createElement('precioTotalSinImpuesto', $precioTotalSinImpuesto);
            $precioTotalSinImpuestoXml = $detalleXml->appendChild($precioTotalSinImpuestoXml);

            $impuestosXml = $xml->createElement('impuestos');
            $impuestosXml = $detalleXml->appendChild($impuestosXml);

            $impuestoXml = $xml->createElement('impuesto');
            $impuestoXml = $impuestosXml->appendChild($impuestoXml);

            $codigoXml = $xml->createElement('codigo', $codigoIva);
            $codigoXml = $impuestoXml->appendChild($codigoXml);

            $codigoPorcentajeIvaXml = $xml->createElement('codigoPorcentaje', $codigoPorcentajeIva);
            $codigoPorcentajeIvaXml = $impuestoXml->appendChild($codigoPorcentajeIvaXml);

            $tarifaXml = $xml->createElement('tarifa', $tarifa);
            $tarifaXml = $impuestoXml->appendChild($tarifaXml);

            $baseImponibleXml = $xml->createElement('baseImponible', $precioTotalSinImpuesto);
            $baseImponibleXml = $impuestoXml->appendChild($baseImponibleXml);

            $valorXml = $xml->createElement('valor', $valorIva);
            $valorXml = $impuestoXml->appendChild($valorXml);
        }

        if (strlen($direccionComprador) > 0 && strlen($ctx_telefono) > 0 && strlen($ctx_mail) > 0) {
            $infoAdicionalXml = $xml->createElement('infoAdicional');
            $infoAdicionalXml = $ncXml->appendChild($infoAdicionalXml);

            if ($direccionComprador != '') {
                $campoAdicionalXml = $xml->createElement('campoAdicional', $direccionComprador);
                $campoAdicionalXml = $infoAdicionalXml->appendChild($campoAdicionalXml);
                $campoAdicionalXml->setAttribute('nombre', 'Dirección');
            }

            if ($ctx_telefono != '') {
                $campoAdicionalXml = $xml->createElement('campoAdicional', $ctx_telefono);
                $campoAdicionalXml = $infoAdicionalXml->appendChild($campoAdicionalXml);
                $campoAdicionalXml->setAttribute('nombre', 'Teléfono');
            }

            if ($ctx_mail != '') {
                $campoAdicionalXml = $xml->createElement('campoAdicional', $ctx_mail);
                $campoAdicionalXml = $infoAdicionalXml->appendChild($campoAdicionalXml);
                $campoAdicionalXml->setAttribute('nombre', 'Email');
            }
        }

        $xml->formatOutput = true;
        $xml->xmlStandalone = true;
        $xml->saveXML();
        $xml->save($rutaGenerados . $claveAcceso . '.xml');

        if (file_exists($rutaGenerados . $claveAcceso . '.xml')) {
            $this->setDataLog($cci_empresa, $cci_sucursal, $cci_cliente, 'NC', $nci_documento, $claveAcceso, 'GENERAR', 'G');
        } else {
            echo 'error al grabar el archivo';
        }

        //echo '<hr>';
    }

    public function generaXmlRetencion($dataEmpresa, $dataDocumento, $dataDetalle) {
        $this->codDoc = '07';
        $cci_empresa = $dataDocumento['CCI_EMPRESA'];
        $cci_sucursal = $dataDocumento['CCI_SUCURSAL'];
        $cci_cliente = $dataDocumento['COD_PROV'];
        $estab = $dataDocumento['ESTAB'];
        $ptoEmi = $dataDocumento['PTOEMI'];
        $secuencial = $dataDocumento['SECUENCIAL'];
        $nci_documento = $dataDocumento['NCI_DOCUMENTO'];
        $dfiFechaAux = $dataDocumento['DFM_RETENCION_AUX'];
        $fechaEmision = $dataDocumento['DFM_RETENCION'];
        $dirEstablecimiento = $dataDocumento['CTX_DIRECCION'];
        $tipoIdentificacionSujetoRetenido = $dataDocumento['TIPO_IDENTIFICACION'];
        $razonSocialSujetoRetenido = $this->xmlEscape($dataDocumento['CNO_CLIENTE']);
        $identificacionSujetoRetenido = $dataDocumento['CCI_RUC'];
        $direccionComprador = $dataDocumento['CTX_DIRECCION'];
        $periodoFiscal = $dataDocumento['PERIODO_FISCAL'];

        $ctx_telefono = $dataDocumento['CTX_TELEFONO'];
        $ctx_mail = $dataDocumento['CTX_MAIL'];

        $rutaGenerados = $dataEmpresa['CCI_RUTA_GENERADOS'] . 'RET\\';

        //echo '<span>GENERANDO XML RET: ' . $cci_empresa . ' - ' . $nci_documento . '</span><br>';

        $numero = $dfiFechaAux . $this->codDoc . $dataEmpresa['CCI_RUC'] . $dataEmpresa['AMBIENTE'] . $estab . $ptoEmi . $secuencial . $dataEmpresa['CODIGO_NUMERICO'] . $dataEmpresa['TIPO_EMISION'];

        $claveAcceso = $this->getClaveAcceso($numero);

        $xml = new DomDocument('1.0', 'UTF-8');

        $retencionXml = $xml->createElement('comprobanteRetencion');
        $retencionXml->setAttribute('id', 'comprobante');
        $retencionXml->setAttribute('version', '1.0.0');
        $retencionXml = $xml->appendChild($retencionXml);

        $infoTributariaXml = $xml->createElement('infoTributaria');
        $infoTributariaXml = $retencionXml->appendChild($infoTributariaXml);

        $ambienteXml = $xml->createElement('ambiente', $dataEmpresa['AMBIENTE']);
        $ambienteXml = $infoTributariaXml->appendChild($ambienteXml);

        $tipoEmisionXml = $xml->createElement('tipoEmision', $dataEmpresa['TIPO_EMISION']);
        $tipoEmisionXml = $infoTributariaXml->appendChild($tipoEmisionXml);

        $razonSocialXml = $xml->createElement('razonSocial', $dataEmpresa['CNO_EMPRESA']);
        $razonSocialXml = $infoTributariaXml->appendChild($razonSocialXml);

        $rucXml = $xml->createElement('ruc', $dataEmpresa['CCI_RUC']);
        $rucXml = $infoTributariaXml->appendChild($rucXml);

        $claveAccesoXml = $xml->createElement('claveAcceso', $claveAcceso);
        $claveAccesoXml = $infoTributariaXml->appendChild($claveAccesoXml);

        $codDocXml = $xml->createElement('codDoc', $this->codDoc);
        $codDocXml = $infoTributariaXml->appendChild($codDocXml);

        $estabXml = $xml->createElement('estab', $estab);
        $estabXml = $infoTributariaXml->appendChild($estabXml);

        $ptoEmiXml = $xml->createElement('ptoEmi', $ptoEmi);
        $ptoEmiXml = $infoTributariaXml->appendChild($ptoEmiXml);

        $secuencialXml = $xml->createElement('secuencial', $secuencial);
        $secuencialXml = $infoTributariaXml->appendChild($secuencialXml);

        $dirMatrizXml = $xml->createElement('dirMatriz', $dataEmpresa['CTX_DIRECCION']);
        $dirMatrizXml = $infoTributariaXml->appendChild($dirMatrizXml);

        $infoCompRetencion = $xml->createElement('infoCompRetencion');
        $infoCompRetencion = $retencionXml->appendChild($infoCompRetencion);

        $fechaEmisionXml = $xml->createElement('fechaEmision', $fechaEmision);
        $fechaEmisionXml = $infoCompRetencion->appendChild($fechaEmisionXml);

        $dirEstablecimientoXml = $xml->createElement('dirEstablecimiento', $dataDocumento['CTX_DIRECCION_SUCURSAL']);
        $dirEstablecimientoXml = $infoCompRetencion->appendChild($dirEstablecimientoXml);

        $obligadoContabilidadXml = $xml->createElement('obligadoContabilidad', $dataEmpresa['CTX_OBLIGADO_CONTABILIDAD']);
        $obligadoContabilidadXml = $infoCompRetencion->appendChild($obligadoContabilidadXml);

        $tipoIdentificacionSujetoRetenidoXml = $xml->createElement('tipoIdentificacionSujetoRetenido', $tipoIdentificacionSujetoRetenido);
        $tipoIdentificacionSujetoRetenidoXml = $infoCompRetencion->appendChild($tipoIdentificacionSujetoRetenidoXml);

        $razonSocialSujetoRetenidoXml = $xml->createElement('razonSocialSujetoRetenido', $razonSocialSujetoRetenido);
        $razonSocialSujetoRetenidoXml = $infoCompRetencion->appendChild($razonSocialSujetoRetenidoXml);

        $identificacionSujetoRetenidoXml = $xml->createElement('identificacionSujetoRetenido', $identificacionSujetoRetenido);
        $identificacionSujetoRetenidoXml = $infoCompRetencion->appendChild($identificacionSujetoRetenidoXml);

        $periodoFiscalXml = $xml->createElement('periodoFiscal', $periodoFiscal);
        $periodoFiscalXml = $infoCompRetencion->appendChild($periodoFiscalXml);

        $impuestosXml = $xml->createElement('impuestos');
        $impuestosXml = $retencionXml->appendChild($impuestosXml);

        foreach ($dataDetalle as $keyDetalle => $valueDetalle) {
            $codigo = $valueDetalle['CODIGO'];
            $codigoRetencion = $valueDetalle['CODIGO_RETENCION'];
            $baseImponible = $valueDetalle['NVA_BASE_RETENCION'];
            $porcentajeRetener = $valueDetalle['NVA_PORCENTAJE'];
            $valorRetenido = $valueDetalle['NVA_RETENCION'];
            $codDocSustento = $valueDetalle['CCI_SUSTENTO'];
            $numDocSustento = $valueDetalle['NCI_DOCUMENTO'];

            $impuestoXml = $xml->createElement('impuesto');
            $impuestoXml = $impuestosXml->appendChild($impuestoXml);

            $codigoXml = $xml->createElement('codigo', $codigo);
            $codigoXml = $impuestoXml->appendChild($codigoXml);

            $codigoRetencionXml = $xml->createElement('codigoRetencion', $codigoRetencion);
            $codigoRetencionXml = $impuestoXml->appendChild($codigoRetencionXml);

            $baseImponibleXml = $xml->createElement('baseImponible', $baseImponible);
            $baseImponibleXml = $impuestoXml->appendChild($baseImponibleXml);

            $porcentajeRetenerXml = $xml->createElement('porcentajeRetener', $porcentajeRetener);
            $porcentajeRetenerXml = $impuestoXml->appendChild($porcentajeRetenerXml);

            $valorRetenidoXml = $xml->createElement('valorRetenido', $valorRetenido);
            $valorRetenidoXml = $impuestoXml->appendChild($valorRetenidoXml);

            $codDocSustentoXml = $xml->createElement('codDocSustento', $codDocSustento);
            $codDocSustentoXml = $impuestoXml->appendChild($codDocSustentoXml);

            $numDocSustentoXml = $xml->createElement('numDocSustento', $numDocSustento);
            $numDocSustentoXml = $impuestoXml->appendChild($numDocSustentoXml);

            $fechaEmisionDocSustentoXml = $xml->createElement('fechaEmisionDocSustento', $fechaEmision);
            $fechaEmisionDocSustentoXml = $impuestoXml->appendChild($fechaEmisionDocSustentoXml);
        }

        if (strlen($direccionComprador) > 0 && strlen($ctx_telefono) > 0 && strlen($ctx_mail) > 0) {
            $infoAdicionalXml = $xml->createElement('infoAdicional');
            $infoAdicionalXml = $retencionXml->appendChild($infoAdicionalXml);

            if ($direccionComprador != '') {
                $campoAdicionalXml = $xml->createElement('campoAdicional', $direccionComprador);
                $campoAdicionalXml = $infoAdicionalXml->appendChild($campoAdicionalXml);
                $campoAdicionalXml->setAttribute('nombre', 'Dirección');
            }

            if ($ctx_telefono != '') {
                $campoAdicionalXml = $xml->createElement('campoAdicional', $ctx_telefono);
                $campoAdicionalXml = $infoAdicionalXml->appendChild($campoAdicionalXml);
                $campoAdicionalXml->setAttribute('nombre', 'Teléfono');
            }

            if ($ctx_mail != '') {
                $campoAdicionalXml = $xml->createElement('campoAdicional', $ctx_mail);
                $campoAdicionalXml = $infoAdicionalXml->appendChild($campoAdicionalXml);
                $campoAdicionalXml->setAttribute('nombre', 'Email');
            }
        }

        $xml->formatOutput = true;
        $xml->xmlStandalone = true;
        $xml->saveXML();
        $xml->save($rutaGenerados . $claveAcceso . '.xml');

        if (file_exists($rutaGenerados . $claveAcceso . '.xml')) {
            $this->setDataLog($cci_empresa, $cci_sucursal, $cci_cliente, 'RET', $nci_documento, $claveAcceso, 'GENERAR', 'G');
        } else {
            echo 'error al grabar el archivo';
        }

        //echo '<hr>';
    }

    public function generarXmlGuia($dataEmpresa, $dataDocumento, $dataDetalle) {
        $this->codDoc = '06';
        $cci_empresa = $dataDocumento['CCI_EMPRESA'];
        $cci_sucursal = $dataDocumento['CCI_SUCURSAL'];
        $cci_cliente = $dataDocumento['CCI_CLIENTE'];
        $estab = $dataDocumento['ESTAB'];
        $ptoEmi = $dataDocumento['PTOEMI'];
        $secuencial = $dataDocumento['SECUENCIAL'];
        $nci_documento = $dataDocumento['NCI_DOCUMENTO'];
        $dfiFechaAux = $dataDocumento['DFM_EMISION_AUX'];
        $fechaEmision = $dataDocumento['DFM_RETENCION'];
        $dirPartida = $dataDocumento['CTX_PTO_PARTIDA'];
        $razonSocialTransportista = $this->xmlEscape($dataDocumento['CNO_PERSONA_TRANSP']);
        $tipoIdentificacionTransportista = $dataDocumento['TIPO_IDENTIFICACION_TRANSPORTISTA'];
        $rucTransportista = $dataDocumento['CTX_RUC_TRANSP'];
        $fechaIniTransporte = $dataDocumento['DFM_INI_TRASLADO'];
        $fechaFinTransporte = $dataDocumento['DFM_TER_TRASLADO'];
        $placa = $dataDocumento['CTX_PLACA_TRANSP'];
        $identificacionDestinatario = $dataDocumento['CCI_RUC'];
        $razonSocialDestinatario = $this->xmlEscape($dataDocumento['CNO_CLIENTE']);
        $dirDestinatario = $dataDocumento['CTX_DIRECCION'];
        $motivoTraslado = $dataDocumento['MOTIVO_TRASLADO'];
        $ruta = $dataDocumento['RUTA'];

        $rutaGenerados = $dataEmpresa['CCI_RUTA_GENERADOS'] . 'GUI\\';

        //echo 'GENERANDO XML GUIA REMISION: ' . $cci_empresa . ' - ' . $nci_documento . '<br>';

        $numero = $dfiFechaAux . $this->codDoc . $dataEmpresa['CCI_RUC'] . $dataEmpresa['AMBIENTE'] . $estab . $ptoEmi . $secuencial . $dataEmpresa['CODIGO_NUMERICO'] . $dataEmpresa['TIPO_EMISION'];

        $claveAcceso = $this->getClaveAcceso($numero);

        $xml = new DomDocument('1.0', 'UTF-8');

        $guiaXml = $xml->createElement('guiaRemision');
        $guiaXml->setAttribute('id', 'comprobante');
        $guiaXml->setAttribute('version', '1.0.0');
        $guiaXml = $xml->appendChild($guiaXml);

        $infoTributariaXml = $xml->createElement('infoTributaria');
        $infoTributariaXml = $guiaXml->appendChild($infoTributariaXml);

        $ambienteXml = $xml->createElement('ambiente', $dataEmpresa['AMBIENTE']);
        $ambienteXml = $infoTributariaXml->appendChild($ambienteXml);

        $tipoEmisionXml = $xml->createElement('tipoEmision', $dataEmpresa['TIPO_EMISION']);
        $tipoEmisionXml = $infoTributariaXml->appendChild($tipoEmisionXml);

        $razonSocialXml = $xml->createElement('razonSocial', $dataEmpresa['CNO_EMPRESA']);
        $razonSocialXml = $infoTributariaXml->appendChild($razonSocialXml);

        $rucXml = $xml->createElement('ruc', $dataEmpresa['CCI_RUC']);
        $rucXml = $infoTributariaXml->appendChild($rucXml);

        $claveAccesoXml = $xml->createElement('claveAcceso', $claveAcceso);
        $claveAccesoXml = $infoTributariaXml->appendChild($claveAccesoXml);

        $codDocXml = $xml->createElement('codDoc', $this->codDoc);
        $codDocXml = $infoTributariaXml->appendChild($codDocXml);

        $estabXml = $xml->createElement('estab', $estab);
        $estabXml = $infoTributariaXml->appendChild($estabXml);

        $ptoEmiXml = $xml->createElement('ptoEmi', $ptoEmi);
        $ptoEmiXml = $infoTributariaXml->appendChild($ptoEmiXml);

        $secuencialXml = $xml->createElement('secuencial', $secuencial);
        $secuencialXml = $infoTributariaXml->appendChild($secuencialXml);

        $dirMatrizXml = $xml->createElement('dirMatriz', $dataEmpresa['CTX_DIRECCION']);
        $dirMatrizXml = $infoTributariaXml->appendChild($dirMatrizXml);

        $infoGuiaRemision = $xml->createElement('infoGuiaRemision');
        $infoGuiaRemision = $guiaXml->appendChild($infoGuiaRemision);

        $dirEstablecimientoXml = $xml->createElement('dirEstablecimiento', $dataDocumento['CTX_DIRECCION_SUCURSAL']);
        $dirEstablecimientoXml = $infoGuiaRemision->appendChild($dirEstablecimientoXml);

        $dirPartidaXml = $xml->createElement('dirPartida', $dirPartida);
        $dirPartidaXml = $infoGuiaRemision->appendChild($dirPartidaXml);

        $razonSocialTransportistaXml = $xml->createElement('razonSocialTransportista', $razonSocialTransportista);
        $razonSocialTransportistaXml = $infoGuiaRemision->appendChild($razonSocialTransportistaXml);

        $tipoIdentificacionTransportistaXml = $xml->createElement('tipoIdentificacionTransportista', $tipoIdentificacionTransportista);
        $tipoIdentificacionTransportistaXml = $infoGuiaRemision->appendChild($tipoIdentificacionTransportistaXml);

        $rucTransportistaXml = $xml->createElement('rucTransportista', $rucTransportista);
        $rucTransportistaXml = $infoGuiaRemision->appendChild($rucTransportistaXml);

        $obligadoContabilidadXml = $xml->createElement('obligadoContabilidad', $dataEmpresa['CTX_OBLIGADO_CONTABILIDAD']);
        $obligadoContabilidadXml = $infoGuiaRemision->appendChild($obligadoContabilidadXml);

        $fechaIniTransporteXml = $xml->createElement('fechaIniTransporte', $fechaIniTransporte);
        $fechaIniTransporteXml = $infoGuiaRemision->appendChild($fechaIniTransporteXml);

        $fechaFinTransporteXml = $xml->createElement('fechaFinTransporte', $fechaFinTransporte);
        $fechaFinTransporteXml = $infoGuiaRemision->appendChild($fechaFinTransporteXml);

        $placaXml = $xml->createElement('placa', $placa);
        $placaXml = $infoGuiaRemision->appendChild($placaXml);

        $destinatariosXml = $xml->createElement('destinatarios');
        $destinatariosXml = $guiaXml->appendChild($destinatariosXml);

        $destinatarioXml = $xml->createElement('destinatario');
        $destinatarioXml = $destinatariosXml->appendChild($destinatarioXml);

        $identificacionDestinatarioXml = $xml->createElement('identificacionDestinatario', $identificacionDestinatario);
        $identificacionDestinatarioXml = $destinatarioXml->appendChild($identificacionDestinatarioXml);

        $razonSocialDestinatarioXml = $xml->createElement('razonSocialDestinatario', $razonSocialDestinatario);
        $razonSocialDestinatarioXml = $destinatarioXml->appendChild($razonSocialDestinatarioXml);

        $dirDestinatarioXml = $xml->createElement('dirDestinatario', $dirDestinatario);
        $dirDestinatarioXml = $destinatarioXml->appendChild($dirDestinatarioXml);

        $motivoTrasladoXml = $xml->createElement('motivoTraslado', $motivoTraslado);
        $motivoTrasladoXml = $destinatarioXml->appendChild($motivoTrasladoXml);

        $rutaXml = $xml->createElement('ruta', $ruta);
        $rutaXml = $destinatarioXml->appendChild($rutaXml);

        $detallesXml = $xml->createElement('detalles');
        $detallesXml = $destinatarioXml->appendChild($detallesXml);

        foreach ($dataDetalle as $keyDetalle => $valueDetalle) {
            $detalleXml = $xml->createElement('detalle');
            $detalleXml = $detallesXml->appendChild($detalleXml);

            $codigoInternoXml = $xml->createElement('codigoInterno', $valueDetalle['CCI_ITEM']);
            $codigoInternoXml = $detalleXml->appendChild($codigoInternoXml);

            $descripcionXml = $xml->createElement('descripcion', $valueDetalle['CNO_ITEM']);
            $descripcionXml = $detalleXml->appendChild($descripcionXml);

            $cantidadXml = $xml->createElement('cantidad', $valueDetalle['NQN_SOLICITADA']);
            $cantidadXml = $detalleXml->appendChild($cantidadXml);
        }

        $xml->formatOutput = true;
        $xml->xmlStandalone = true;
        $xml->saveXML();
        $xml->save($rutaGenerados . $claveAcceso . '.xml');

        if (file_exists($rutaGenerados . $claveAcceso . '.xml')) {
            $this->setDataLog($cci_empresa, $cci_sucursal, $cci_cliente, 'GUI', $nci_documento, $claveAcceso, 'GENERAR', 'G');
        } else {
            $mensajeWS = 'ERROR EN GENERACION DE ARCHIVO XML(ERROR-LOCAL)';
            $informacionAdicionalWS = 'Error en la generacion del archivo xml';
            $this->setDataLog($cci_empresa, $cci_sucursal, $cci_cliente, $cci_tipocmpr, $nci_documento, $claveAcceso, 'GENERAR', 'R', '', '', '', '', '', $mensajeWS, $informacionAdicionalWS, '');
            echo '<br>' . $mensajeWS . ' - ' . $informacionAdicionalWS;
        }

        //echo '<hr>';
    }

    public function getErrorDB() {
        return $this->errorDB;
    }

    public function setErrorDB($errorDB) {
        $this->errorDB = $errorDB;
    }

    private function xmlEscape($string) {
        return str_replace(array('&', '<', '>', '\'', '"'), array('&amp;', '&lt;', '&gt;', '&apos;', '&quot;'), $string);
    }

    private function getClaveAcceso($numero) {
        $numero_rev = strrev($numero);
        $total = 0;
        $multiplo = 2;

        for ($i = 0; $i < strlen($numero_rev); $i++) {
            if ($multiplo > 7) {
                $multiplo = 2;
            }

            $total = $total + ($numero_rev[$i] * $multiplo);

            $multiplo++;
        }

        $modulo = $total % 11;

        $verificador = 11 - $modulo;

        if ($verificador == 11) {
            $verificador = 0;
        }

        if ($verificador == 10) {
            $verificador = 1;
        }

        $numero = $numero . $verificador;

        return $numero;
    }

    function getDataLog() {
        return $this->dataLog;
    }

    private function setDataLog($cci_empresa, $cci_sucursal, $cci_cliente, $cci_tipocmpr, $nci_documento, $claveAcceso, $proceso, $estado, $estadoWS = '', $identificadorWS = '', $numeroAutorizacionWS = '', $fechaAutorizacionWS = '', $ambienteWS = '', $mensajeWS = '', $informacionAdicionalWS = '', $tipoWS = '') {
        $this->dataLog = array(
            'CCI_EMPRESA' => $cci_empresa,
            'CCI_SUCURSAL' => $cci_sucursal,
            'CCI_CLIENTE' => $cci_cliente,
            'CCI_TIPOCMPR' => $cci_tipocmpr,
            'NCI_DOCUMENTO' => $nci_documento,
            'CCI_CLAVE_ACCESO' => $claveAcceso,
            'CCI_PROCESO' => $proceso,
            'CES_FE' => $estado,
            'ESTADO_WS' => $estadoWS,
            'NUMERO_AUTORIZACION_WS' => $numeroAutorizacionWS,
            'FECHA_AUTORIZACION_WS' => $fechaAutorizacionWS,
            'AMBIENTE_WS' => $ambienteWS,
            'IDENTIFICADOR_WS' => $identificadorWS,
            'MENSAJE_WS' => $mensajeWS,
            'INFORMACION_ADICIONAL_WS' => $informacionAdicionalWS,
            'TIPO_WS' => $tipoWS,
        );

        //print_r($this->dataLog);
    }

}
