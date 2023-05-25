<?php
session_start();
include_once "../classes/alimentos.php";
include_once "../classes/user.php";
$alimentos = new Alimentos();




if (isset($_POST['borrar_x']) && isset($_POST['borrar_y']) && isset($_SESSION['usuario'])) {

    $user = unserialize($_SESSION['usuario']);
    $data_user = $user->getUser();

    echo $alimentos->checkComida($data_user['id'], $_POST['id_comida']);
}

?>