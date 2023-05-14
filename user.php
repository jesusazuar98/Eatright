<?php
#Se incluye la conexion con la base de datos

require_once "./connect.php";


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
        #Devulve un array con las propiedades del usuario
        return [
            "id" => $this->id_user,
            "username" => $this->username
        ];
    }


    #Metodo que hace un check del login
    function checkLogin()
    {

        #Conectar con la base de datos
        $conn = conectarDB();

        #Consulta sql que saca el id, pass y intentos del usuario
        $sql = "SELECT id_cli,pass,intentos FROM clientes WHERE (n_user=? OR email=?)";

        #Se prepara la consulta
        $resultado = mysqli_prepare($conn, $sql);

        #Se consulta los tipos de datos que ha introducido el usuario
        #Comprobando que sean los dos strings
        $comprobacion = mysqli_stmt_bind_param($resultado, "ss", $this->username, $this->username);

        #La comprobacion de que los tipos de datos son correctos
        $comprobacion = mysqli_stmt_execute($resultado);


        #Devuelve 0 si la comprobacion da falso
        if ($comprobacion == false) {
            return 0;
        }

        #Si no devuelve falso guardara los resultados en las variables correspondientes
        $comprobacion = mysqli_stmt_bind_result($resultado, $id_user, $pass, $intentos);


        #Si el numero de resultados es nulo significa que no hay ningun registro
        #Devolvera 0
        if (is_null(mysqli_stmt_fetch($resultado))) {

            return 0;
        }

        #Comprobacion del numero de intentos que devolvera 2 en caso de que sean 0
        if ($intentos == 0) {
            return 2;
        }

        #Si la contraseña introducida por el usuario no es igual llamara a una funcion que restara intentos
        #Devuelve el numero de intentos que le quedan
        if ($this->password != $pass) {
            $this->setAttempts($id_user, $intentos, false);
            return ["intentos" => $intentos - 1];
        }

        #Introduce el id del usuario en la propiedad
        $this->id_user = $id_user;

        #Intruce de nuevo 3 intentos
        $this->setAttempts($id_user, 3, true);

        #Cierra la conexion mysql
        mysqli_close($conn);

        #Si todo ha salido correctamente devulve 1
        return 1;


    }

    #Funcion privada que inserta intentos en la base de datos
    private function setAttempts($id, $intentos, $comp)
    {
        #Si la variable $comp es positiva introduce los intentos que ha metido el usuario
        #En caso de que no sea positiva resta 1 a los intentos y los inserta segun el id del usuario

        $intentos = $comp ? $intentos : $intentos - 1;
        $conn = conectarDB();
        $sql = "UPDATE clientes SET intentos=? WHERE id_cli=?";
        $resultado = mysqli_prepare($conn, $sql);

        $comprobacion = mysqli_stmt_bind_param($resultado, "ii", $intentos, $id);

        $comprobacion = mysqli_stmt_execute($resultado);

        mysqli_close($conn);


    }


}


?>