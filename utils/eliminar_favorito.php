<?php
session_start();
if (!isset($_SESSION['usuario'])) {

    echo "<script>alert('Usuario no registrado, inicie sesion.');window.location.href='../index.php'</script>";
}

include_once "../classes/alimentos.php";
include_once "../classes/user.php";

$user = unserialize($_SESSION['usuario']);

$id_u = $user->getUser()['id'];

$alimentos = new Alimentos();

$result = $alimentos->borrar_favorito($id_u, $_POST['id_alimento']);


if ($result != 1) {

    echo json_encode($result);
} else {


    $code = $alimentos->list_favorites($id_u);

    echo json_encode($code, JSON_UNESCAPED_UNICODE);

}

?>