<?php

include_once 'librerias/header.php';
include_once 'librerias/ClaseEmpresa.php';
include_once 'librerias/ClasePermisos.php';

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : null);

//$action = 'getEmpresas';

switch ($action) {
    case 'getEmpresas':
        getEmpresas();
        break;
    case 'getEmpresasNoRegistradas':
        getEmpresasNoRegistradas();
        break;
    case 'getEmpresasLocal':
        getEmpresasLocal();
        break;
}

function getEmpresas() {
    $id_perfil = $_POST['id_perfil'];
    $id_menu = $_POST['id_menu'];

    $objetoPermiso = new ClasePermisos();

    $resultPermisos = $objetoPermiso->getEmpresasOpcion($id_perfil, $id_menu);

    //print_r($resultPermisos);

    if ($resultPermisos['error'] == 'N') {
        $dataPermisos = $resultPermisos['data'];
        //print_r($dataPermisos);

        $cadenaEmpresas = '';
        $last_key = end(array_keys($dataPermisos));
        foreach ($dataPermisos as $key => $value) {
            //echo 'value: ' . $value['codigo_auxiliar'];
            $codigo_auxiliar = $value['codigo_auxiliar'];
            $cadenaEmpresas = $cadenaEmpresas . "*$codigo_auxiliar*";

            //if ($key !== array_key_last($dataPermisos)) {
            if ($key !== $last_key) {
                $cadenaEmpresas = $cadenaEmpresas . ',';
            }
        }

        //echo $cadena;

        $objetoEmpresa = new ClaseEmpresa();

        $result = $objetoEmpresa->getEmpresas($cadenaEmpresas);

        $data = ClaseJson::getJson($result);

        echo $data;
    }
}

function getEmpresasNoRegistradas() {
    $objetoEmpresa = new ClaseEmpresa();

    $result = $objetoEmpresa->getEmpresasNoRegistradas();

    $data = ClaseJson::getJson($result);

    echo $data;
}

function getEmpresasLocal() {
    $objetoEmpresa = new ClaseEmpresa();

    $result = $objetoEmpresa->getEmpresasLocal();

    $data = ClaseJson::getJson($result);

    echo $data;
}
