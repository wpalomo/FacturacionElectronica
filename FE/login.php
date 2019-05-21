<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, X-EXAMPLE-HEADER, authorization');

include_once 'librerias/ClaseLogin.php';

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : null);

switch ($action) {
    case 'login':
        login();
        break;
    case 'cerrarSesiones':
        cerrarSesiones();
        break;
}

function login() {
    if (isset($_POST['login']) && isset($_POST['clave'])) {
        $objetoLogin = new ClaseLogin($_POST['login'], $_POST['clave']);
        $result = $objetoLogin->login();

        $data = ClaseJson::getJson($result);
    } else {
        $data = ClaseJson::getMessageJson(false, 'Los campos Login o Clave estan vacios');
    }

    echo $data;
}

//$data = $result['data'];
//print_r($data);
//echo json_encode($data);
//echo '<hr>';



//print_r($result);
//
//echo '<hr>';
//
//$x = json_decode('{
//    "data": [
//        {
//            "empresa": "005",
//            "nombre": "AMERICAN",
//            "tipo": "FAC",
//            "serieNumero": "001001-0000001",
//            "fecha": "01/01/2019",
//            "fechaAutorizacion": "01/01/2019"
//        },
//        {
//            "empresa": "008",
//            "nombre": "GLOBALTEX",
//            "tipo": "NC",
//            "serieNumero": "001001-0000001",
//            "fecha": "01/01/2019",
//            "fechaAutorizacion": "01/01/2019"
//        },
//        {
//            "empresa": "009",
//            "nombre": "TEXFASHION",
//            "tipo": "GUI",
//            "serieNumero": "001001-0000001",
//            "fecha": "01/01/2019",
//            "fechaAutorizacion": "01/01/2019"
//        },
//        {
//            "empresa": "011",
//            "nombre": "SURIGAT",
//            "tipo": "RET",
//            "serieNumero": "001001-0000001",
//            "fecha": "01/01/2019",
//            "fechaAutorizacion": "01/01/2019"
//        }
//    ]
//}', true);
//
//print_r($x);

