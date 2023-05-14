<?php

#Creacion de la funcion para conectar con la base de datos

function conectarDB()
{

    #Ponemos el nombre de usuario de nuestra base de datos
    #Contraseña y el nombre de la base de datos
    $username = "root";
    $password = "";
    $database = "eatright";

    #Creamos la conexión poniendo todos los datos incluyendo nuestro hosting
    $mysqli = new mysqli("localhost", $username, $password, $database) or die("No se ha podido conectar con la base de datos");

    return $mysqli;
}




?>