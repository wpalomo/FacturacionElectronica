<?php

include_once 'config.inc.php';
include_once 'ClaseBaseDatos.php';
include_once 'ClaseJson.php';
include_once 'funciones.php';

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ClaseMenu
 *
 * @author jpsanchez
 */
class ClaseMenu {

    private $id_usuario;

    public function __construct($id_usuario) {
        $this->id_usuario = mssql_real_escape_string($id_usuario);
    }

    private function adj_tree(&$tree, $item) {
        $i = $item['id_menu'];
        $p = $item['id_menu_padre'];
        $tree[$i] = isset($tree[$i]) ? $item + $tree[$i] : $item;

        $tree[$p]['children'][] = &$tree[$i];
    }

    public function getMenu() {
        $query = "
            EXEC SP_GEN_MENU
            @in_id_usuario = '$this->id_usuario',	     	     
            @in_operacion = 'QMU'
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);
        //print_r($result);
        if ($result['error'] == 'N') {
            $tree = array();

            $data = $result['data'];

            //print_r($data);

            foreach ($data as $key => $rows) {
                $leaf = true;
                $expanded = false;
                if ($rows['tipo'] == 'P') {
                    $leaf = false;
                }

                $arr = array(
                    'id_menu' => $rows['id_menu'],
                    'id_menu_padre' => $rows['id_menu_padre'],
                    //'text' => $rows['nombre_menu'],
                    'nombre_menu' => $rows['nombre_menu'],
                    'tipo' => $rows['tipo'],
                    'iconCls' => trim($rows['icono']),
                    //'MN_ICONO' => trim($rows['MN_ICONO']),
                    //'mn_clase' => trim($rows['mn_clase']),
                    'ruta' => trim($rows['ruta']),
                    'leaf' => $leaf,
                    'expanded' => $expanded,
                );

                if (($rows['tipo'] == 'P')) {
                    //$arr = $arr + array('children' => array());
                }

                $this->adj_tree($tree, $arr);
            }
            $nodes = $tree[1];

            return json_encode($nodes);
        }
    }

    function getMenuFavoritos() {
        $query = "
            EXEC SP_GEN_MENU_FAVORITOS
            @in_id_usuario = '$this->id_usuario',                                
            @in_operacion = 'QMF'               
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        if ($result['error'] == 'N') {
            $tree = array();
            $data = $result['data'];

            foreach ($data as $key => $rows) {
                $leaf = true;
                $expanded = false;

                $arr = array(
                    'id_menu' => $rows['id_menu'],
                    'id_menu_padre' => $rows['id_menu_padre'],
                    'text' => $rows['mn_nombre'],
                    'tipo' => $rows['tipo'],
                    'iconCls' => trim($rows['icono']),
                    //'mn_clase' => trim($rows['mn_clase']),
                    'ruta' => trim($rows['ruta']),
                    'leaf' => $leaf,
                    'expanded' => $expanded,
                );

                array_push($tree, $arr);
            }

            $data = json_encode($tree);
            return $data;
        }
    }

    public function getMenuUsuario() {
        $dataMenuFavoritos = $this->getMenuFavoritos();
        $dataMenuFavoritos = json_decode($dataMenuFavoritos, true);

        $dataMenu = $this->getMenu();

        //echo $dataMenu;



//        $Replacement = '***';
//$Source = '1234 abcdefg 1234 abcdefg 1234 abcdefg';
//        echo preg_replace('/{"children":[/', $Replacement, $dataMenu, 1);
//        echo preg_replace('/[/', $Replacement, $dataMenu, 1);
//        echo $dataMenu;

        $dataMenu = $this->str_replace_once('{"children":[', "", $dataMenu);
        //$dataMenu = $this->str_replace_once(":", "...", $dataMenu);
        //$dataMenu = $this->str_replace_once("[", "...", $dataMenu);

        //echo $dataMenu;

        //echo '<hr>';

        $dataMenu = substr_replace($dataMenu, '', -2);

        //echo $dataMenu;
        //die();

        $dataMenu = json_decode($dataMenu, true);

        //print_r($dataMenu);

        $favoritos = array(
            'text' => 'Favoritos',
            'iconCls' => 'fa fa-star',
            'state' => 'open',
            'children' => $dataMenuFavoritos
        );

        $menu = array($favoritos);

        array_push($menu, $dataMenu);

        //print_r($menu);

        //echo json_encode($menu);

        return json_encode($menu);
    }

    private function str_replace_once($str_pattern, $str_replacement, $string) {

        if (strpos($string, $str_pattern) !== false) {
            $occurrence = strpos($string, $str_pattern);
            return substr_replace($string, $str_replacement, strpos($string, $str_pattern), strlen($str_pattern));
        }

        return $string;
    }

}
