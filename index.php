<?php

session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Abel&display=swap" rel="stylesheet" />
    <link href="styles/index.css" rel="stylesheet" />
    <title>Eatright</title>
</head>

<body>
<div class="wrapper">
    <header>
        <div class="logo">
            <h1><a href="#">EatRight</a></h1>
        </div>
        <nav>
          <ul>
            <li><a href="alimentos.php">Alimentos</a></li>
            <?php
            include('./user.php');

            if (isset($_SESSION['usuario'])) {

                $user = unserialize($_SESSION['usuario']);

                $data = $user->getUser();
                echo "<p>" . $data['id'] . " " . $data['username'] . "</p>";

                ?>
                <li><a href="logout.php">Cerrar sesión</a></li>

                <?php
            } else {


                ?>
                <li><a href="login.php">Iniciar sesión</a><li>

                <?php
            }
            ?>
            </nav>
    </header>
</div>
<footer class="footer">
    <p>Eatright&#169</p>
    <p>Todos los derechos reservados a Jesus y Domingo</p>
    <a href="#">Sobre Nosotros</a>
</footer>
</body>

</html>