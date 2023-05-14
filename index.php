<?php

session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eatright</title>
</head>

<body>

    <a href="alimentos.php">Alimentos</a>
    <?php
    include('./user.php');

    if (isset($_SESSION['usuario'])) {

        $user = unserialize($_SESSION['usuario']);

        $data = $user->getUser();
        echo "<p>" . $data['id'] . " " . $data['username'] . "</p>";

        ?>
        <a href="logout.php">Cerrar sesión</a>

        <?php
    } else {


        ?>
        <a href="login.php">Iniciar sesión</a>

        <?php
    }
    ?>
</body>

</html>