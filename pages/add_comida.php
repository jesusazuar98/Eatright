<?php
include_once("../classes/alimentos.php");
session_start();


if (!isset($_SESSION['usuario'])) {
    header("Location: ./login.php");
    exit();

}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/header.css">
    <link rel="stylesheet" href="../styles/footer.css">
    <link rel="stylesheet" href="../styles/add_comida.css">
    <title>Insertar Comida</title>
</head>

<body>
    <?php include_once "../includes/header.php"; ?>
    <div id="container">
        <h1>Añadir alimento</h1>

        <div id="contain1">
            <h3>Busqueda de los alimentos por nombre:</h3>
            <form action="add_comida.php" method="post">
                <input type="text" name="n_alimento" />
                <input type="submit" name="b_alimento" value="Buscar" />
        </div>
        </form>

        <div id="contain2">
            <div id="c1">
                <?php

                if (isset($_POST['b_alimento'])) {

                    $code = "";


                    $alimentos = new Alimentos();

                    $result = $alimentos->buscar_alimentos($_POST['n_alimento']);

                    if ($result == 0) {

                        $code = "No se ha encontrado ningun registro";
                    } else {
                        $code = "<ul>";
                        for ($i = 0; $i < sizeof($result); $i++) {

                            $r = json_encode($result[$i]);

                            $text = $result[$i]['marca'] . ", " . $result[$i]['porcion'] . " (gr o ml), " . $result[$i]['kcal'] . "kcal";

                            $code .= "<li onclick='muestraAlimentos($r)'><a href='#'>" . $result[$i]['nombre'] . "</a><p>" . $text . "</p></li>";
                        }

                        $code .= "</ul>";

                    }

                    echo $code;


                }
                ?>

            </div>

            <div id="c2">


            </div>

        </div>


    </div>
    <script src="../js/formComida.js"></script>

    <?php include "../includes/footer.php"; ?>
</body>

</html>