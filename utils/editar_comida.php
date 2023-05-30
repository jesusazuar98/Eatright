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

    $code = "<form action='./utils/change_comida.php' method='POST'><h3>" . $req[1] . "</h3><input type='hidden' name='id_comida' readonly value=" . $_POST['id_comida'] . "><p>Porcion: <input id='p_u' name='p_u' step='any' onchange='calculaValores(" . $data . ")' type='number' value='0'/></p><p>Kcal: <input step='any' id='v_kcal' readonly type='number' value='0'/></p><p>Grasas: <input step='any' id='v_grasas' readonly type='number' value='0'/></p><p>Grasas saturadas: <input step='any' readonly id='v_gsatu' type='number' value='0'/></p><p>Carbohidratos: <input step='any' readonly id='v_carbos' type='number' value='0'/></p><p>Azúcar: <input step='any' readonly id='v_azucar' type='number' value='0'/></p><p>Proteina: <input step='any' readonly id='v_prote' type='number' value='0'/></p><p>Sal: <input step='any' readonly id='v_sal' type='number' value='0'/></p><p><input type='submit' name='changePorcion' value='Cambiar porcion'/></p></form>";

    echo json_encode($code, JSON_UNESCAPED_UNICODE);
    return;

}

?>