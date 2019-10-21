<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, X-EXAMPLE-HEADER, authorization');

include_once 'librerias/ClaseMenu.php';

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : null);
$action = 'getMenuPermiso';

switch ($action) {
    case 'getMenu':
        getMenu();
        break;
    case 'getMenuFavoritos':
        getMenuFavoritos();
        break;
    case 'getMenuUsuario':
        getMenuUsuario();
        break;
    case 'getMenuPermiso':
        getMenuPermiso();
        break;
}

function getMenu() {
    //if (isset($_POST['id_usuario'])) {
    //$objetoMenu = new ClaseMenu($_POST['id_usuario']);
    $objetoMenu = new ClaseMenu(1);
    $result = $objetoMenu->getMenu();

    if ($result['error'] == 'N') {
        //$data = ClaseJson::getJson($result);
    }


//    } else {
//        $data = ClaseJson::getMessageJson(false, 'Los campos Login o Clave estan vacios');
    //}

    echo $result;
}

function getMenuFavoritos() {
    if (isset($_POST['id_usuario'])) {
        $objetoMenu = new ClaseMenu($_POST['id_usuario']);
        //$objetoMenu = new ClaseMenu(1);
        $result = $objetoMenu->getMenuFavoritos();

        if ($result['error'] == 'N') {
            //$data = ClaseJson::getJson($result);
        }


//    } else {
//        $data = ClaseJson::getMessageJson(false, 'Los campos Login o Clave estan vacios');
    }

    echo $result;
}

function getMenuUsuario() {
    if (isset($_POST['id_usuario'])) {
        $objetoMenu = new ClaseMenu($_POST['id_usuario']);
        //$objetoMenu = new ClaseMenu(1);
        $result = $objetoMenu->getMenuUsuario();

        if ($result['error'] == 'N') {
            //$data = ClaseJson::getJson($result);
        }


//    } else {
//        $data = ClaseJson::getMessageJson(false, 'Los campos Login o Clave estan vacios');
    }
    echo $result;

//    echo ' 
//        [
//        {
//        "text": "Favoritos",
//        "iconCls": "fa fa-star",
//        "state": "open"
//        }
//      ]
//    ';
//    echo "
//    {
//      text: 'Favoritos',
//      iconCls: 'fa fa-star',
//      state: 'open',
//      children: [
//        {
//          text: 'Cambio de Clave',
//          routerLink: '/cambio-clave'
//        },
//        {
//          text: 'Favoritos',
//          routerLink: '/favoritos'
//        },
//        {
//          text: 'Ambiente',
//          routerLink: '/ambiente'
//        },
//        {
//          text: 'Parametros',
//          routerLink: '/parametros'
//        },
//        {
//          text: 'Unidades de Tiempo',
//          routerLink: '/ambiente'
//        },
//        {
//          text: 'Formas de Pago',
//          routerLink: '/ambiente'
//        },
//        {
//          text: 'Procesar Documentos Electronicos',
//          routerLink: '/parametros'
//        },
//        {
//          text: 'Consulta de Documentos Electrónicos',
//          routerLink: '/parametros'
//        }
//      ]
//    },
//    {
//      text: 'Modulo General',
//      iconCls: 'fa fa-home',
//      children: [
//        {
//          text: 'Usuarios',
//          children: [
//            {
//              text: 'Mantenimiento de Usuarios',
//              routerLink: '/mantenimiento-usuarios'
//            },
//            {
//              text: 'Cambio de Clave',
//              routerLink: '/cambio-clave'
//            },
//            {
//              text: 'Favoritos',
//              routerLink: '/favoritos'
//            }
//          ]
//        },
//        {
//          text: 'Seguridades',
//          children: [
//            {
//              text: 'Mantenimiento de Perfil',
//              routerLink: '/ambiente'
//            },
//            {
//              text: 'Permisos',
//              routerLink: '/ambiente'
//            }
//          ]
//        }
//      ]
//    },
//    {
//      text: 'Parametros',
//      iconCls: 'fa fa-wpforms',
//      children: [
//        {
//          text: 'Ambiente',
//          routerLink: '/ambiente'
//        },
//        {
//          text: 'Parametros',
//          routerLink: '/parametros'
//        },
//        {
//          text: 'Unidades de Tiempo',
//          routerLink: '/ambiente'
//        },
//        {
//          text: 'Formas de Pago',
//          routerLink: '/ambiente'
//        }
//      ]
//    },
//    {
//      text: 'Transacciones',
//      iconCls: 'fa fa-at',
//      selected: true,
//      children: [
//        {
//          text: 'Procesar Documentos Electronicos',
//          routerLink: '/parametros'
//        }
//      ]
//    }, {
//      text: 'Consultas',
//      iconCls: 'fa fa-table',
//      children: [
//        {
//          text: 'Consulta de Documentos Electrónicos'
//        }
//      ]
//    }
//  ";
}

function getMenuPermiso() {
    //if (isset($_POST['id_usuario'])) {
    //$objetoMenu = new ClaseMenu($_POST['id_usuario']);
    $objetoMenu = new ClaseMenu(1);
    $result = $objetoMenu->getMenuPermiso();

    if ($result['error'] == 'N') {
        //$data = ClaseJson::getJson($result);
    }


//    } else {
//        $data = ClaseJson::getMessageJson(false, 'Los campos Login o Clave estan vacios');
    //}

    echo $result;
}
