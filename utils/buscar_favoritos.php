<?php
session_start();

include_once "../classes/alimentos.php";
include_once "../classes/user.php";

if (!isset($_SESSION['usuario'])) {

    echo json_encode("<script>alert('Usuario no registrado, inicie sesion.');window.location.href='../index.php'</script>");
}

$alimentos = new Alimentos();
$user = unserialize($_SESSION['usuario']);

$id_u = $user->getUser()['id'];

$code = $alimentos->buscar_favoritos($_POST['name_ali'], $_POST['marca'], $id_u);

if ($code == 0) {

    echo json_encode("No se ha encontrado ningun favorito.");


} else {


    echo json_encode($code);

}




?>