<?php
session_start();
include_once "./user.php";
if (isset($_SESSION['usuario'])) {

    header("Location:./index.php");
}

if (isset($_POST['envio'])) {

    $user = new User($_POST['n_user'], $_POST['password']);

    $registrar = $user->registro($_POST['mail'], $_POST['r_password'], $_POST['sexo'], $_POST['nacimiento'], $_POST['peso'], $_POST['altura'], $_POST['n_completo']);

    if ($registrar != 1) {

        echo "<script>alert('" . $registrar . "')</script>";
    }

    if ($registrar == 1) {

        echo "<script>alert('El registro se ha completado correctamente, puede iniciar sesion.')</script>";
        echo "<script>window.location.href = './login.php'</script>";
        exit;
    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrate</title>
    <link rel="stylesheet" href="./styles/sign-in.css">
</head>

<body>
    <div id="container">
        <form action="sign-in.php" method="POST">
            <div>
                <label for="n_user">Nombre de usuario: </label>
                <input type="text" name="n_user" placeholder="Nuevo nombre de usuario" required />
            </div>
            <div>
                <label for="mail">Email: </label>
                <input type="email" name="mail" placeholder="Introduce tu email" required />
            </div>
            <div>
                <label for="password">Contraseña: </label>
                <input type="password" name="password" placeholder="Contraseña" minlength="8" />
            </div>
            <div>
                <label for="r_password">Repita la contraseña: </label>
                <input type="password" name="r_password" placeholder="Repita la contraseña" minlength="8" />
            </div>
            <div>
                <label for="sexo">Selecciona un sexo: </label>
                <input type="radio" id="M" name="sexo" value="M" checked /><label for="M">Masculino</label>
                <input type="radio" id="F" name="sexo" value="F" /><label for="F">Femenino</label>
            </div>
            <div>
                <label for="nacimiento">Fecha de nacimiento:</label>
                <input type="date" id="nacimiento" name="nacimiento" required />
            </div>
            <div>
                <label for="peso">Peso</label>
                <input type="number" id="peso" name="peso" step="0.01" value="0" required />
            </div>
            <div>
                <label for="altura">Altura</label>
                <input type="number" id="altura" name="altura" step="0.01" value="0" required />
            </div>
            <div>
                <label for="n_completo">Nombre completo</label>
                <input type="text" id="n_completo" name="n_completo" placeholder="Introduce tu nombre" required />
            </div>
            <input type="submit" name="envio" id="envio" value="Enviar" />


        </form>
    </div>

</body>

</html>