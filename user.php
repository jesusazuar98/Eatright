<?php
#Se incluye la conexion con la base de datos

include "./connect.php";


#Creamos la clase User
class User
{
    #Usamos dos propiedades privadas que seran el nombre de usuario y la contraseña
    private $id_user;
    private $username;
    private $email;
    private $password;
    private $sexo;
    private $f_cumple;
    private $peso;
    private $altura;
    private $nombre;
    private $estado;


    #La funcion constructora tendra como parametros
    #El nombre de usuario y la contraseña que se asignaran a las propiedades privadas
    function __construct($username, $password)
    {

        $this->username = $username;
        $this->password = $password;

    }

    #Metodo que muestra los datos del usuario
    function getUser()
    {

        return [
            "id" => $this->id_user,
            "username" => $this->username
        ];
    }

    function checkLogin()
    {

        $conn = conectarDB();
        $sql = "SELECT id_cli,pass,intentos FROM clientes WHERE (n_user=? OR email=?)";

        $resultado = mysqli_prepare($conn, $sql);

        $comprobacion = mysqli_stmt_bind_param($resultado, "ss", $this->username, $this->username);

        $comprobacion = mysqli_stmt_execute($resultado);

        if ($comprobacion == false) {
            return 0;
        }


        $comprobacion = mysqli_stmt_bind_result($resultado, $id_user, $pass, $intentos);
        echo "<script>console.log(" . $intentos . ")</script>";


        if (is_null(mysqli_stmt_fetch($resultado))) {

            return 0;
        }

        if ($intentos == 0) {
            return 2;
        }
        if ($this->password != $pass) {
            $this->setAttempts($id_user, $intentos, false);
            return ["intentos" => $intentos - 1];
        }

        $this->id_user = $id_user;
        $this->setAttempts($id_user, 3, true);

        mysqli_close($conn);

        return 1;


    }

    private function setAttempts($id, $intentos, $comp)
    {
        $intentos = $comp ? $intentos : $intentos - 1;
        $conn = conectarDB();
        $sql = "UPDATE clientes SET intentos=? WHERE id_cli=?";
        $resultado = mysqli_prepare($conn, $sql);

        $comprobacion = mysqli_stmt_bind_param($resultado, "ii", $intentos, $id);

        $comprobacion = mysqli_stmt_execute($resultado);




    }


}


?>