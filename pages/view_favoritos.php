<?php

if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

$user = unserialize($_SESSION['usuario']);
include_once "../classes/alimentos.php";
$alimentos = new Alimentos();

?>

<div class="container">

    <div class="contenido1">

        <h3>Añadir alimento a favoritos</h3>
        <p>Buscar alimento: <input type="text" name="n_alimento" id="n_alimento" onchange="listFavorites('con1')" />
            <select name="a_marca" id="a_marca" onchange="listFavorites('con1')">
                <option value="hacendado" selected>Hacendado</option>
                <option value="aldi">Aldi</option>
                <option value="alcampo">Alcampo</option>
                <option value="dia">DIA</option>
                <option value="carrefour">Carrefour</option>

            </select>
        <div class="con1" id="con1">
            <div id="c2"></div>
        </div>
        </p>


    </div>


    <div class="contenido2" id="contenido2">
        <?php
        $code = $alimentos->list_favorites($user->getUser()['id']);
        echo $code;
        ?>
    </div>
</div>