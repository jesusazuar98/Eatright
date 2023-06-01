<?php

include_once "../classes/alimentos.php";
include_once "../classes/user.php";
session_start();

if (!isset($_SESSION['usuario'])) {


    echo "<script>alert('Usuario no registrado, inicie sesion.');window.location.href='../index.php'</script>";
}


$alimentos = new Alimentos();
$user = unserialize($_SESSION['usuario']);

$add_favoritos = $alimentos->add_favorite($user->getUser()['id'], $_POST['id_alimento']);

if ($add_favoritos != 1) {

    echo json_encode($add_favoritos);
}
$code = $alimentos->list_favorites($user->getUser()['id']);

echo json_encode($code, JSON_UNESCAPED_UNICODE);

?>