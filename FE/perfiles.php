<?php

include_once 'librerias/header.php';
include_once 'librerias/ClasePerfil.php';

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : null);
$tipo = '';

switch ($action) {
    case 'getPerfiles':
        getPerfiles();
        break;
    case 'insert':
        $tipo = 'I';
        insert();
        break;
    case 'update':
        $tipo = 'U';
        update();
        break;
    case 'delete':
        $tipo = 'D';
        delete();
        break;
}


/*
  echo $_POST['start'];
  echo $_POST['limit'];
  echo $_POST['filters'];

  $records = json_decode(stripslashes($_POST['filters']), true);

  print_r($records);

  echo '<hr>';
 */

//foreach ($records as $record => $value) {
//    //echo $value;
//    //echo '1';
//    print_r($record);
//
//    $key = key((array) $record);
//
//    echo $key;
//}
//foreach ($records as $row) {
//    echo $row[0];
//    foreach ($row as $key => $val) {
//        echo $key . ': ' . $val;
//        echo '<br>';
//    }
//}
//foreach($records as $key => $val) {
//    echo "KEY IS: $key<br/>";
//    foreach((array)$records)[$key] as $val2) {
//        echo "VALUE IS: $val2<br/>";
//    }
//}
//foreach($records as $key => $val) {
//    echo "KEY IS: $key<br/>";
//    foreach(((array)$records)[$key] as $val2) {
//        echo "VALUE IS: $val2<br/>";
//    }
//}
//foreach ($records as $key => $val) {
//    echo 'KEY IS:' . $key . '<br/>';
//    foreach ($val as $_key => $_val) {
//        echo 'VALUE IS: ' . $_val . '<br/>';
//    }
//}
//foreach ($records as $key => $val) {
//    echo 'KEY IS:' . $key . '<br/>';
//    foreach ($records[$key] as $_key => $_val) {
//        echo 'KEY IS:' . $_key . '<br/>';
//        echo 'VALUE IS: ' . $_val . '<br/>';
//    }
//}

/*
  $select = "
  select *
  from TB_GEN_PERFILES
  ";

  $where = ' WHERE id_perfil > 0 ';

  foreach ($records as $key => $val) {
  echo 'KEY IS:' . $key . '<br/>';
  foreach ($records[$key] as $_key => $_val) {
  echo 'KEY IS:' . $_key . '<br/>';
  echo 'VALUE IS: ' . $_val . '<br/>';

  if ($_key == 'value') {
  $where = $where . " AND " . $key . " = '$_val' ";
  }
  }
  }

  $order = 'ORDER BY ' . $_POST['sortField'] . ' ';
  $offset = 'OFFSET ' . ($_POST['start'] * $_POST['limit']) . ' ROWS ';
  $fetch = 'FETCH NEXT ' . $_POST['limit'] . ' ROWS ONLY';


  $query = $select . $where . $order . $offset . $fetch;

  echo $query;

  switch ($action) {
  case 'getPerfiles':
  getPerfiles();
  break;
  }
 */
function getPerfiles() {

    $parametros = array(
        'start' => $_POST['start'],
        'limit' => $_POST['limit'],
        'sortField' => isset($_POST['sortField']) ? $_POST['sortField'] : 'id_perfil',
        'sortOrder' => isset($_POST['sortOrder']) ? $_POST['sortOrder'] : '1',
        'filters' => $_POST['filters'],
    );



    $objetoPerfil = new ClasePerfil();

    $result = $objetoPerfil->getPerfiles($parametros);

    $data = ClaseJson::getJson($result);

    echo $data;
}

function insert() {
    if (isset($_POST['perfil'])) {
        $parametros = array(
            'perfil' => $_POST['perfil'],
            'tipo' => $tipo
        );

        $objetoPerfil = new ClasePerfil();

        $result = $objetoPerfil->insert($parametros);

        $data = ClaseJson::getJson($result);
    } else {
        $data = ClaseJson::getMessageJson(false, 'Error en el envio de información');
    }

    echo $data;
}

function update() {
    if (isset($_POST['perfil'])) {
        $parametros = array(
            'perfil' => $_POST['perfil'],
            'tipo' => $tipo
        );

        $objetoPerfil = new ClasePerfil();

        $result = $objetoPerfil->update($parametros);

        $data = ClaseJson::getJson($result);
    } else {
        $data = ClaseJson::getMessageJson(false, 'Error en el envio de información');
    }

    echo $data;
}

function delete() {
    if (isset($_POST['perfil'])) {
        $parametros = array(
            'perfil' => $_POST['perfil'],
            'tipo' => $tipo
        );

        $objetoPerfil = new ClasePerfil();

        $result = $objetoPerfil->delete($parametros);

        $data = ClaseJson::getJson($result);
    } else {
        $data = ClaseJson::getMessageJson(false, 'Error en el envio de información');
    }

    echo $data;
}

//echo 'hola';
