<?php

include_once "../classes/alimentos.php";

session_start();


if(!isset($_SESSION['usuario'])){

    header("Location:../index.php");
    exit;
}




?>