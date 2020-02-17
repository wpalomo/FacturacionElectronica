<?php

include_once 'librerias/header.php';
include_once 'librerias/ClaseEmpresa.php';

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
    $objetoEmpresa = new ClaseEmpresa();

    $result = $objetoEmpresa->getEmpresas();

    $data = ClaseJson::getJson($result);

    echo $data;
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