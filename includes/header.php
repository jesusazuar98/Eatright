<header>
    <div class="logo">
        <h1><a href="../index.php">EatRight</a></h1>
    </div>
    <nav>
        <ul>
            <li><a href="../pages/muestra_alimentos.php">Alimentos</a></li>
            <?php
            require_once(__DIR__ . '/../classes/user.php');

            if (isset($_SESSION['usuario'])) {

                $user = unserialize($_SESSION['usuario']);

                $data = $user->getUser();

                ?>
                <li><a href="../pages/favoritos.php">Favoritos</a></li>
                <li><a href="../pages/logout.php">Cerrar sesión</a></li>

                <?php
            } else {


                ?>
                <li><a href="../pages/login.php">Iniciar sesión</a></li>
                <li><a href="../pages/sign-in.php">Registrarse</a></li>


                <?php
            }
            ?>
    </nav>
</header>