<div class="wrapper">
    <header>
        <div class="logo">
            <h1>EatRight</h1>
        </div>
        <nav>
            <ul>
                <li><a href="alimentos.php">Alimentos</a></li>
                <?php
                include('./user.php');

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

                    <?php
                }
                ?>
        </nav>
    </header>
</div>