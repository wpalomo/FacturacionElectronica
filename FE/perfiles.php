<?php

include_once 'librerias/header.php';
include_once 'librerias/ClasePerfil.php';

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : null);

$objetoPerfil = new ClasePerfil(1, 1, 1);

$result = $objetoPerfil->getPerfiles();

$data = ClaseJson::getJson($result);

echo $data;

switch ($action) {
    case 'getPerfiles':
        getPerfiles();
        break;
}

function getPerfiles() {
    
}

//echo 'hola';
