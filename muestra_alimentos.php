<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/index.css">
    <title>Alimentos de Eatright</title>

    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            font-size: 16px;
            line-height: 1.5;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            vertical-align: middle;
            border: 1px solid #ccc;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
    </style>

</head>


<body>

    <?php
    include_once "./utils/header.php";
    include_once "./alimentos.php";

    $alimentos = new Alimentos();
    ?>

    <form action="muestra_alimentos.php" method="GET">
        <p>Ordenar por marca:</p>
        <p><input type="checkbox" name="marca[]" value="Hacendado"> Hacendado</p>
        <p><input type="checkbox" name="marca[]" value="Aldi"> Aldi</p>
        <p><input type="checkbox" name="marca[]" value="Alcampo"> Alcampo</p>
        <p><input type="checkbox" name="marca[]" value="DIA"> DIA</p>
        <p><input type="checkbox" name="marca[]" value="Carrefour"> Carrefour</p>
        <p><input type="submit" name="envio_orden" value="Buscar"> <input type="reset" value="Limpiar filtros"></p>


    </form>

    <?php

    if (isset($_GET['marca'])) {

        if (is_array($_GET['marca'])) {
            $alimentos->muestraAlimentos("'" . implode("','", $_GET['marca']) . "'");

        } else {

            $alimentos->muestraAlimentos($_GET['marca']);
        }


    } else {


        $alimentos->muestraAlimentos();
    }
    ?>
    <?php include "./utils/footer.php" ?>
</body>

</html>