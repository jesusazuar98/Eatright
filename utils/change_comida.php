<?php

include_once("../classes/alimentos.php");


if (!isset($_POST['changePorcion'])) {


    echo "<script>window.location.href='../index.php'</script>";

}

$alimentos = new Alimentos();

echo $alimentos->change_comida($_POST['id_comida'], $_POST['p_u']);


?>