<?php

#Incluimos el fichero user.php

include "./user.php";

#Comprobamos si el usuario le ha dado al boton de login
if (isset($_POST['log'])) {

    #Creamos un nuevo objeto user
    #Le pasamos como parametros el nombre de usuario y la contraseña proporcionada por el usuario
    $user = new User($_POST['user'], $_POST['pass']);

    #Guardamos el resultado del metodo checkLogin en una variable
    $log = $user->checkLogin();

    if ($log == 2) {
        echo "<script>alert('El usuario introducido se encuentra bloqueado, contacte con el administrador para volver a acceder.')</script>";

    }

    #Si el log es igual a 0 significa que el usuario no existe
    if ($log == 0) {

        echo "<script>alert('El usuario introducido no existe')</script>";


    }

    if (is_array($log)) {

        $palabra = ($log['intentos'] == 1) ? 'intento' : 'intentos';

        echo "<script>alert('La contraseña introducida es incorrecta, le queda " . $log['intentos'] . " " . $palabra . "')</script>";

    }

    if ($log == 1) {
        #Creamos una session
        session_start();

        #En la session usuario serializamos los datos del objeto user
        #Para poder usar despues sus propiedades y metodos
        $_SESSION['usuario'] = serialize($user);

        #Enviamos al usuario a la pagina del index
        header("Location: ./index.php");
    }


}
?>


<!DOCTYPE html>
<html>

<head>
    <link href="https://fonts.googleapis.com/css?family=Abel&display=swap" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href=".\styles\login.css" />
    <title>Login Eatright</title>
</head>

<body>

    <div class="fondo" id="formulario">
        <form action="login.php" method="POST">
            <div class="logo"></div>
            <span class="titulo">EAT RIGHT</span>
            <input class="login_button" type="text" id="user" name="user" placeholder="User name" required>
            <input class="password_button" type="password" id="pass" name="pass" placeholder="Password" required>
            <input class="inicia_sesion_button" type="submit" id="log" name="log" value="Iniciar sesion">
        </form>
    </div>
    <button class="back" onclick="location.href='index.php'" type="button">Pagina Principal</button>
</body>

</html>