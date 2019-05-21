<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, X-EXAMPLE-HEADER, authorization');

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : null);

$action = 'prueba1';
$action = 'error';

switch ($action) {
    case 'prueba1':
        prueba1();
        break;
    case 'error':
        error();
        break;
}

function prueba1() {
    $o = array(
        "success" => true,
        "data" => array(
            array(
                "codigo" => "01",
                "forma_pago" => "SIN UTILIZACION DEL SISTEMA FINANCIERO",
                "fecha_inicio" => "01/01/2013",
                "fecha_fin" => "",
                "estado" => "A"
            ),
            array(
                "codigo" => "15",
                "forma_pago" => "COMPENSACIÓN DE DEUDAS",
                "fecha_inicio" => "01/06/2013",
                "fecha_fin" => "",
                "estado" => "A"
            ),
            array(
                "codigo" => "16",
                "forma_pago" => "TARJETA DE DÉBITO",
                "fecha_inicio" => "01/06/2016",
                "fecha_fin" => "",
                "estado" => "A"
            ),
            array(
                "codigo" => "17",
                "forma_pago" => "DINERO ELECTRÓNICO",
                "fecha_inicio" => "01/06/2016",
                "fecha_fin" => "",
                "estado" => "A"
            ),
            array(
                "codigo" => "18",
                "forma_pago" => "TARJETA PREPAGO",
                "fecha_inicio" => "01/06/2016",
                "fecha_fin" => "",
                "estado" => "A"
            ),
            array(
                "codigo" => "19",
                "forma_pago" => "TARJETA DE CRÉDITO",
                "fecha_inicio" => "01/06/2016",
                "fecha_fin" => "",
                "estado" => "A"
            ),
            array(
                "codigo" => "20",
                "forma_pago" => "OTROS CON UTILIZACION DEL SISTEMA FINANCIERO",
                "fecha_inicio" => "01/06/2016",
                "fecha_fin" => "",
                "estado" => "A"
            ),
            array(
                "codigo" => "21",
                "forma_pago" => "ENDOSO DE TÍTULOS",
                "fecha_inicio" => "01/06/2016",
                "fecha_fin" => "",
                "estado" => "A"
            )
        )
    );

    echo json_encode($o);
}

function error() {
    //http_response_code(404);
    //http_response_code(422);

    $o = array(
        "success" => false,
        "mensaje" => "Error en el mensaje"
    );

    echo json_encode($o);
}

?>
