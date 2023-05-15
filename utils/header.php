<div class="wrapper">
    <header>
        <div class="logo">
            <h1><a href="./index.php">EatRight</a></h1>
        </div>
        <nav>
            <ul>
                <li><a href="muestra_alimentos.php">Alimentos</a></li>
                <?php
                require_once('./user.php');

                if (isset($_SESSION['usuario'])) {

                    $user = unserialize($_SESSION['usuario']);

                    $data = $user->getUser();

                    ?>
                    <li><a href="add_comida.php">Añadir comida</a></li>

                    <li><a href="logout.php">Cerrar sesión</a></li>

                    <?php
                } else {


                    ?>
                    <li><a href="login.php">Iniciar sesión</a></li>
                    <li><a href="sign-in.php">Registrarse</a></li>


                    <?php
                }
                ?>
        </nav>
    </header>
</div>