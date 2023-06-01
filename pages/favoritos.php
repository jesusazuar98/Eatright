<?php
session_start();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favoritos</title>
    <link href="https://fonts.googleapis.com/css?family=Abel&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="../styles/header.css">
    <link rel="stylesheet" href="../styles/footer.css">
    <link rel="stylesheet" href="../styles/favoritos.css">
    <link rel="stylesheet" href="../styles/add_comida.css">

</head>

<body>
    <?php

    include "../includes/header.php";
    include "./view_favoritos.php";
    include "../includes/footer.php";

    ?>
    <script src="../js/formComida.js"></script>
</body>

</html>