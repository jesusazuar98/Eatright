<?php
include_once "../classes/alimentos.php";
include_once "../classes/user.php";
session_start();

if (!isset($_SESSION['usuario'])) {
    echo "<script>alert('Debe iniciar sesion.'); window.location.href='../index.php'</script>";
}

$alimentos = new Alimentos();
$user = unserialize($_SESSION['usuario']);
$id_u = $user->getUser()['id'];


if ($_POST['check_fav'] == 'f') {
    $result = $alimentos->buscar_alimentos($_POST['n_alimento'], $_POST['a_marca']);

} else {

    $result = $alimentos->buscar_favoritos($_POST['n_alimento'], $_POST['a_marca'], $id_u, true);

}





if ($result == 0) {

    $code = "<div id='c1'>No se ha encontrado ningun alimento</div>";
} else {
    $code = "<div id='c1'><ul>";

    if ($_POST['check_fav'] == 'f') {
        for ($i = 0; $i < sizeof($result); $i++) {

            $r = json_encode($result[$i]);

            $text = $result[$i]['marca'] . ", " . $result[$i]['porcion'] . " (gr o ml), " . $result[$i]['kcal'] . "kcal";

            $code .= "<li onclick='muestraAlimentos($r)'><a href='#c1'>" . $result[$i]['nombre'] . "</a><p>" . $text . "</p></li>";
        }
    } else {

        $code .= $result;
    }


    $code .= "</ul>";

}

$code .= "</div><div id='c2'></div>";

echo json_encode($code, JSON_UNESCAPED_UNICODE);



?>