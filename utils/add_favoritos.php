<?php
include_once "../classes/alimentos.php";
include_once "../classes/user.php";
session_start();

if (!isset($_SESSION['usuario'])) {

    echo json_encode("Debe iniciar sesion", JSON_UNESCAPED_UNICODE);
}

$alimentos = new Alimentos();
$user = unserialize($_SESSION['usuario']);


$result = $alimentos->list_notfavorites($_POST['n_alimento'], $_POST['a_marca'], $user->getUser()['id']);



if ($result == 0) {

    $code = "<div id='c1'>No se ha encontrado ningun alimento</div>";
} else {
    $code = "<div id='c1'><ul>";
    for ($i = 0; $i < sizeof($result); $i++) {

        $r = json_encode($result[$i]);

        $text = $result[$i]['marca'] . ", " . $result[$i]['porcion'] . " (gr o ml), " . $result[$i]['kcal'] . "kcal";

        $code .= "<li><a href='#'>" . $result[$i]['nombre'] . "</a><div class='favorite-content'><p>" . $text . "</p><span onclick='addFavorites($r)'><a href='#'><img src='../images/estrella.png' alt='estrella'/></a></span></div></li>";
    }

    $code .= "</ul>";

}


echo json_encode($code, JSON_UNESCAPED_UNICODE);



?>