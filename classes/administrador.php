<?php

require_once(__DIR__ . "/../config/connect.php");
require_once("user.php");


class Administrador extends User
{

    function alimentos($marca, $n_alimento)
    {
        $conn = conectarDB();

        $sql = "SELECT * FROM alimentos WHERE marca LIKE CONCAT('%',?,'%') AND nombre_alimen LIKE CONCAT('%',?,'%') LIMIT 30";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param('ss', $marca, $n_alimento);

        if (!$stmt->execute()) {

            return 0;
        }


        $results = $stmt->get_result();

        if ($results->num_rows <= 0) {
            $stmt->close();
            $conn->close();
            return 0;
        }

        $data = [];

        while ($row = $results->fetch_assoc()) {

            $data[] = $row;

        }
        $stmt->close();
        $conn->close();
        return $data;
    }



    function editarAlimento($id_alimento, $n_alimento, $marca, $porcion, $kcal, $grasas, $g_saturadas, $carbos, $azucar, $proteina, $sal)
    {

        $conn = conectarDB();

        $sql = "UPDATE alimentos SET nombre_alimen=?, marca=?, porcion=?, kcal=?, grasas=?, g_saturadas=?, carbohidratos=?, azucar=?, proteina=?,sal=? WHERE id_alimento=?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param('ssddddddddi', $n_alimento, $marca, $porcion, $kcal, $grasas, $g_saturadas, $carbos, $azucar, $proteina, $sal, $id_alimento);

        if (!$stmt->execute()) {
            $stmt->close();
            $conn->close();

            return 0;
        }

        $stmt->close();
        $conn->close();
        return 1;

    }

    function eliminar_alimento($id_alimento)
    {

        $conn = conectarDB();

        $sql = "DELETE FROM alimentos WHERE id_alimento=?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param('i', $id_alimento);

        if (!$stmt->execute()) {
            $stmt->close();
            $conn->close();
            return 0;
        }

        $stmt->close();
        $conn->close();
        return 1;

    }


    function add_alimento($n_alimento, $marca, $porcion, $kcal, $grasas, $g_saturadas, $carbos, $azucar, $proteina, $sal)
    {

        $conn = conectarDB();
        $sql = 'INSERT INTO alimentos (nombre_alimen,marca,porcion,kcal,grasas,g_saturadas,carbohidratos,azucar,proteina,sal) VALUES(?,?,?,?,?,?,?,?,?,?)';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssdddddddd', $n_alimento, $marca, $porcion, $kcal, $grasas, $g_saturadas, $carbos, $azucar, $proteina, $sal);

        if (!$stmt->execute()) {
            $stmt->close();
            $conn->close();
            return 0;
        }

        $stmt->close();
        $conn->close();
        return 1;
    }


}



?>