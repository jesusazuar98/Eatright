<?php
include_once "../classes/alimentos.php";
session_start();

if (!isset($_SESSION['usuario'])) {
    return false;
}

$alimentos = new Alimentos();

$result = $alimentos->buscar_alimentos($_POST['n_alimento'], $_POST['a_marca']);



if ($result == 0) {

    $code = "<div id='c1'>No se ha encontrado ningun alimento</div>";
} else {
    $code = "<div id='c1'><ul>";
    for ($i = 0; $i < sizeof($result); $i++) {

        $r = json_encode($result[$i]);

        $text = $result[$i]['marca'] . ", " . $result[$i]['porcion'] . " (gr o ml), " . $result[$i]['kcal'] . "kcal";

        $code .= "<li onclick='muestraAlimentos($r)'><a href='#c1'>" . $result[$i]['nombre'] . "</a><p>" . $text . "</p></li>";
    }

    $code .= "</ul>";

}

$code .= "</div><div id='c2'></div>";

echo json_encode($code, JSON_UNESCAPED_UNICODE);



?>