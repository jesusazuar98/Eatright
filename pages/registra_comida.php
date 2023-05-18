<?php
session_start();
require_once('../classes/alimentos.php');
require_once "../classes/user.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: ./login.php");
    exit();

}


if (!isset($_POST['send_food'])) {


    header("Location: ./index.php");
    exit();
}



$alimento = new Alimentos();

$user = unserialize($_SESSION['usuario']);

$data = $user->getUser();


$comp = $alimento->registrar_comida($data['id'], $_POST['id_alimen'], $_POST['fecha'], $_POST['uporcion'], $_POST['comidas']);

if ($comp != 1) {

    echo "<script>alert('" . $comp . "')</script>";
    echo "<script>window.location.href = './add_comida.php'</script>";
    exit;

}

echo "<script>alert('Se ha registrado la comida correctamente.')</script>";
echo "<script>window.location.href = './add_comida.php'</script>";
exit;




?>