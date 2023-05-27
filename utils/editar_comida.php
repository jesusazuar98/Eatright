<?php
include_once "../classes/alimentos.php";
session_start();

if (!isset($_SESSION['usuario'])) {
    echo false;
    return;
}

$alimentos = new Alimentos();


$req = $alimentos->data_Alimento($_POST['id_alimento']);

if ($req == false) {
    echo false;
    return;
} else {

    $data = json_encode(array_slice($req, 3, 8));

    $code = "<form action='./utils/change_comida.php'><h3>" . $req[1] . "</h3><p>Porcion: <input id='p_u' onchange='calculaValores(" . $data . ")' type='number' value='0'/></p></form>";

    echo json_encode($code, JSON_UNESCAPED_UNICODE);
    return;

}

?>