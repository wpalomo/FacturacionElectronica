<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, X-EXAMPLE-HEADER, authorization');

include_once 'librerias/ClaseLogin.php';
include_once 'librerias/ClaseSesion.php';

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : null);

switch ($action) {
    case 'login':
        login();
        break;
    case 'cerrarSesiones':
        cerrarSesiones();
        break;
    case 'cerrarSesionActual':
        cerrarSesionActual();
        break;
    case 'vsp':
        vsp();
        break;
}

function login() {
    if (isset($_POST['login']) && isset($_POST['clave'])) {
        $clave = substr(crypt($_POST['clave'], strtoupper($_POST['login'])), 3);

        //echo $clave;

        $objetoLogin = new ClaseLogin($_POST['login'], $clave);
        $result = $objetoLogin->login();

        $data = ClaseJson::getJson($result);
    } else {
        $data = ClaseJson::getMessageJson(false, 'Los campos Login o Clave estan vacios');
    }

    echo $data;
}

function vsp() {
    $objetoSesion = new ClaseSesion();
    $result = $objetoSesion->verificaSesionPermiso($_POST['id_sesion'], $_POST['id_usuario'], $_POST['id_menu']);

    $data = ClaseJson::getJson($result);

    echo $data;
}

function cerrarSesionActual() {
    $objetoSesion = new ClaseSesion();

    $result = $objetoSesion->cerrarSesionActual($_POST['id_sesion'], $_POST['id_usuario']);

    $data = ClaseJson::getJson($result);

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

