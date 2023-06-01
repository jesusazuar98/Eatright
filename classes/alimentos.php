<?php

include_once(__DIR__ . "/../config/connect.php");


class Alimentos
{

    private function numeroAlimentos($i_sql)
    {

        $connect = conectarDB();

        $sql = $i_sql;

        $result = $connect->query($i_sql);

        $row = $result->fetch_assoc();

        mysqli_close($connect);

        return $row["total"];

    }

    function muestraAlimentos($marca = '')
    {



        if (isset($_GET["pagina"])) {
            if ($_GET["pagina"] == 1) {

                header("Location:muestra_alimentos.php");
                exit();
            } else {

                $pagina = $_GET["pagina"];
            }

        } else {

            $pagina = 1;
        }



        $sql = "SELECT * FROM alimentos WHERE 1=1";
        $cuentasql = "SELECT COUNT(*) AS total FROM alimentos WHERE 1=1";

        if ($marca != '') {

            $sql .= " AND marca IN ($marca)";
            $cuentasql .= " AND marca IN ($marca)";
        }



        $tamano_pag = 10;
        $empieza_desde = ($pagina - 1) * $tamano_pag;

        $n_registros = $this->numeroAlimentos($cuentasql);
        $total_paginas = ceil($n_registros / $tamano_pag);

        $sql .= " LIMIT $empieza_desde,$tamano_pag";


        $connect = conectarDB();
        $result = $connect->query($sql);
        mysqli_close($connect);

        $code = "<table>\n<tr>\n<td>Nombre</td>\n<td>Marca</td>\n<td>Porcion</td>\n<td>Kcal</td>\n<td>Grasa</td>\n<td>Grasas saturadas</td>\n<td>Carbohidratos</td>\n<td>Azúcar</td>\n<td>Proteina</td>\n<td>Sal</td>\n</tr>";
        while ($data = mysqli_fetch_array($result)) {

            $code .= "<tr><td>" . $data[1] . "</td><td>" . $data[2] . "</td><td>" . $data[3] . "</td><td>" . $data[4] . "</td><td>" . $data[5] . "</td><td>" . $data[6] . "</td><td>" . $data[7] . "</td><td>" . $data[8] . "</td><td>" . $data[9] . "</td><td>" . $data[10] . "</td></tr>";

        }
        $codigo_paginacion = $this->paginacion_alimentos($pagina, $total_paginas, $marca);

        echo $code . $codigo_paginacion . "</table>";

    }


    function paginacion_alimentos($pagina, $total_paginas, $marca = "")
    {

        $marca_parametro = ($marca != "") ? "&marca=" . $marca : "";

        $code_paginacion = "";

        if ($pagina > 1) {
            $code_paginacion .= '<tr><td><a href="muestra_alimentos.php?pagina=' . ($pagina - 1) . $marca_parametro . '">Anterior</a> ... </td>';
        }

        for ($i = max(1, $pagina - 5); $i <= min($total_paginas, $pagina + 5); $i++) {

            if ($i <= $total_paginas) {
                $code_paginacion .= '<td><a href="muestra_alimentos.php?pagina=' . $i . $marca_parametro . '">' . $i . '</a></td>';
            }
        }
        if ($pagina < $total_paginas) {
            $code_paginacion .= '<td> ...<a href="muestra_alimentos.php?pagina=' . ($pagina + 1) . $marca_parametro . '"> Siguiente</a></td></tr>';
        }

        return $code_paginacion;


    }

    #Metodo que busca los alimentos de una base de datos por su nombre y marca
    #Guarda todas las coincidencias en un array y muestra su nombre, marca, porcion y kcal
    function buscar_alimentos($name, $marca)
    {

        $conn = conectarDB();

        $sql = "SELECT * FROM alimentos WHERE nombre_alimen LIKE CONCAT('%', ?, '%') AND marca=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $name, $marca);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows <= 0) {
            return 0;
        }

        $alimentos = array();

        while ($row = $result->fetch_assoc()) {
            $alimento = array(
                "id" => $row["id_alimento"],
                "nombre" => $row['nombre_alimen'],
                "marca" => $row['marca'],
                "porcion" => $row['porcion'],
                "kcal" => $row['kcal']
            );
            $alimentos[] = $alimento;
        }

        $stmt->close();
        $conn->close();

        return $alimentos;


    }



    function registrar_comida($u_id, $id_alimento, $fecha, $u_porcion, $comida)
    {
        $comprobacion = $this->numeroAlimentos("SELECT COUNT(*) AS total FROM alimentos WHERE id_alimento=" . $id_alimento . "");
        $comprobacion_comida = $this->numeroAlimentos("SELECT COUNT(*) AS total FROM comen WHERE alimen_id = '" . $id_alimento . "' AND fecha = '" . $fecha . "' AND moment_comida = '" . $comida . "' AND cli_id = " . $u_id);



        if ($comprobacion != 1) {
            return "El alimento no existe y no se ha podido registrar.";
        }

        if ($comprobacion_comida > 0) {

            return "Ya has introducido este alimento en esta comida, ve a " . $comida . " para editarlo.";
        }

        $datos = $u_id . "," . $id_alimento . ",'" . $fecha . "'," . $u_porcion . ",'" . $comida . "'";
        $conn = conectarDB();

        $sql = "INSERT INTO comen (cli_id,alimen_id,fecha, cantidad, moment_comida) VALUES(" . $datos . ")";

        if (!$conn->query($sql)) {

            return "Ha ocurrido un error al insertar los datos:" . $conn->error;

        }

        $conn->close();

        return 1;




    }

    function get_comidas($u_id, $fecha)
    {
        $connect = conectarDB();

        $sql = $connect->prepare("SELECT id_comen,cli_id,alimen_id,nombre_alimen,fecha,cantidad,moment_comida,(cantidad*kcal)/porcion AS calc_kcal,(cantidad*grasas)/porcion AS calc_grasas,(cantidad*g_saturadas)/porcion AS calc_saturadas,(cantidad*carbohidratos)/porcion AS calc_carbos,(cantidad*azucar)/porcion AS calc_azucar,(cantidad*proteina)/porcion AS calc_proteina,(cantidad*sal)/porcion AS calc_sal FROM comen INNER JOIN alimentos ON alimen_id=alimentos.id_alimento WHERE cli_id=? AND fecha=?");

        $sql->bind_param("is", $u_id, $fecha);

        $sql->execute();

        $resultado = $sql->get_result();

        $valores = [
            "desayuno" => null,
            "almuerzo" => null,
            "comida" => null,
            "merienda" => null,
            "cena" => null
        ];

        if ($resultado->num_rows <= 0) {

            return 0;
        }

        while ($fila = $resultado->fetch_assoc()) {

            $momento_comidas = $fila['moment_comida'];


            $valores[$momento_comidas][] = $fila;

        }

        $sql->close();
        $connect->close();
        return $valores;


    }

    function get_comida($comida)
    {
        $code = "";

        $total_kcal = 0;
        $total_carbos = 0;
        $total_grasas = 0;
        $total_saturadas = 0;
        $total_azucar = 0;
        $total_proteina = 0;
        $total_sal = 0;


        foreach ($comida as $alimento) {

            $total_kcal += $alimento['calc_kcal'];
            $total_carbos += $alimento['calc_carbos'];
            $total_grasas += $alimento['calc_grasas'];
            $total_saturadas += $alimento['calc_saturadas'];
            $total_azucar += $alimento['calc_azucar'];
            $total_proteina += $alimento['calc_proteina'];
            $total_sal += $alimento['calc_sal'];


            $code .= "<div class='content-comida'><p class='alimen'>" . $alimento['nombre_alimen'] . " (" . $alimento['cantidad'] . " gr o ml)</p>";
            $code .= "<div class='options'>";
            $code .= "<div class='option'><form action='./pages/options_comen.php' method='POST'><input type='hidden' name='id_comida' value=" . $alimento['id_comen'] . "><input type='image' src='./images/eliminar.png' name='borrar'/></form></div>";
            $code .= "<div class='option'><form action='' method='POST'><a href='#container2' onclick='changeAlimento(" . $alimento['alimen_id'] . ",event," . $alimento['id_comen'] . ")'><img  src='./images/editar.png'/></a></form></div>";

            $code .= "</div>";

            $code .= "<div class='valores-comida'>
            <p>" . round($alimento['calc_kcal'], 2) . "</p>
            <p>" . round($alimento['calc_carbos'], 2) . "</p>
            <p>" . round($alimento['calc_grasas'], 2) . "</p>
            <p>" . round($alimento['calc_saturadas'], 2) . "</p>
            <p>" . round($alimento['calc_azucar'], 2) . "</p>
            <p>" . round($alimento['calc_proteina'], 2) . "</p>
            <p>" . round($alimento['calc_sal'], 2) . "</p>


        </div></div>";
        }

        $code .= "<div class='content-comida'><p><a href='#container2' onclick='addComida()' class='add_alimen'>Añadir alimento</a> | Total:</p>";
        $code .= "<div class='valores-comida'>
            <p>" . round($total_kcal, 2) . "</p>
            <p>" . round($total_carbos, 2) . "</p>
            <p>" . round($total_grasas, 2) . "</p>
            <p>" . round($total_saturadas, 2) . "</p>
            <p>" . round($total_azucar, 2) . "</p>
            <p>" . round($total_proteina, 2) . "</p>
            <p>" . round($total_sal, 2) . "</p>


        </div></div>";

        return $code;
    }


    function total_diario($u_id, $fecha, $data)
    {

        if ($data == 0) {
            return 0;
        }

        $connect = conectarDB();

        $sql = $connect->prepare("SELECT ROUND(SUM((cantidad*kcal)/porcion), 2) AS calc_kcal,
        ROUND(SUM((cantidad*grasas)/porcion), 2) AS calc_grasas,
        ROUND(SUM((cantidad*g_saturadas)/porcion), 2) AS calc_saturadas,
        ROUND(SUM((cantidad*carbohidratos)/porcion), 2) AS calc_carbos,
        ROUND(SUM((cantidad*azucar)/porcion), 2) AS calc_azucar,
        ROUND(SUM((cantidad*proteina)/porcion), 2) AS calc_proteina,
        ROUND(SUM((cantidad*sal)/porcion), 2) AS calc_sal
        FROM comen
        INNER JOIN alimentos ON alimen_id = alimentos.id_alimento
        WHERE cli_id = ? AND fecha =?;
        ");

        $sql->bind_param("is", $u_id, $fecha);

        $sql->execute();

        $resultado = $sql->get_result();
        $resumen = $resultado->fetch_all(MYSQLI_ASSOC);


        $sql->close();
        $connect->close();
        return $resumen;

    }
    private function deleteComida($id_comida)
    {
        $conn = conectarDB();
        $sql = "DELETE FROM comen WHERE id_comen=?";

        $data = $conn->prepare($sql);
        $data->bind_param('i', $id_comida);
        $data = $data->execute();

        if ($data == false) {

            return "<script>alert('Ha ocurrido un error en el momento de eliminar'); window.location.href='../index.php'</script>";

        }
        $conn->close();
        return "<script>alert('El alimento se ha eliminado correctamente de tu comida.'); window.location.href='../index.php'</script>";

    }
    function checkComida($u_id, $id_comida)
    {
        $conn = conectarDB();

        $sql = "SELECT id_comen,cli_id FROM comen WHERE id_comen=?";

        $data = $conn->prepare($sql);

        $data->bind_param('i', $id_comida);

        $data->execute();

        $result = $data->get_result();

        $result = $result->fetch_array();

        if ($result[1] != $u_id) {

            $data->close();
            $conn->close();

            return "<script>alert('El usuario y la comida no coinciden'); window.location.href='../index.php'</script>";
        }
        $data->close();
        $conn->close();

        $r = $this->deleteComida($id_comida);
        return $r;
    }

    function data_Alimento($id_alimento)
    {

        $conn = conectarDB();
        $sql = "SELECT * FROM alimentos WHERE id_alimento=?";

        $data = $conn->prepare($sql);
        $data->bind_param('i', $id_alimento);


        if ($data->execute() == false) {
            return false;
        }



        $result = $data->get_result();

        $result = $result->fetch_row();

        return $result;

    }

    function change_comida($id_comida, $porcion)
    {

        $conn = conectarDB();
        $sql = "UPDATE comen SET cantidad=? WHERE id_comen=?";

        $data = $conn->prepare($sql);
        $data->bind_param('ii', $porcion, $id_comida);

        if ($data->execute() == false) {

            return "<script>alert('Ha ocurrido un error al intentar actualizar la comida'); window.location.href='../index.php'</script>";
        } else {
            $data->close();
            $conn->close();
            return "<script>alert('La comida se ha actualizado correctamente'); window.location.href='../index.php'</script>";
        }

    }



    function list_notfavorites($name, $marca, $idcli)
    {

        $conn = conectarDB();
        $sql = "SELECT * FROM alimentos WHERE nombre_alimen LIKE CONCAT('%', ?, '%') AND marca=? AND id_alimento NOT IN (SELECT id_alimefav FROM favoritos WHERE id_clifav=?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssi', $name, $marca, $idcli);

        if (!$stmt->execute()) {
            return 0;
        }

        $result = $stmt->get_result();

        if ($result->num_rows <= 0) {

            return 0;

        }

        $alimentos = array();

        while ($row = $result->fetch_assoc()) {
            $alimento = array(
                "id" => $row["id_alimento"],
                "nombre" => $row['nombre_alimen'],
                "marca" => $row['marca'],
                "porcion" => $row['porcion'],
                "kcal" => $row['kcal']
            );
            $alimentos[] = $alimento;
        }

        $stmt->close();
        $conn->close();

        return $alimentos;

    }

    function list_favorites($id_u)
    {

        $conn = conectarDB();
        $sql = "SELECT id_alimefav FROM favoritos WHERE id_clifav=?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_u);

        if (!$stmt->execute()) {

            return 0;

        }

        $result = $stmt->get_result();

        if ($result->num_rows <= 0) {

            return 0;
        }

        $stmt->close();
        $conn->close();

        $code = "";

        while ($row = $result->fetch_array()) {

            $data_alimento = $this->data_Alimento($row[0]);

            $code .= "<p>" . $data_alimento[1] . "</p>";
        }

        return $code;
    }

    function add_favorite($id_u, $id_alimento)
    {

        $conn = conectarDB();

        $sql = "SELECT * FROM favoritos WHERE id_clifav=? AND id_alimefav=?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param('ii', $id_u, $id_alimento);


        if (!$stmt->execute()) {

            return "Error en la ejecucion de la consulta: " . $stmt->error;
        }

        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {

            return "<script>alert('El alimento ya estaba marcado como favorito.')</script>";
        }

        $stmt->close();
        $conn->close();


        $conn = conectarDB();
        $sql = "INSERT INTO favoritos VALUES(?,?)";
        $stmt = $conn->prepare($sql);

        $stmt->bind_param("ii", $id_u, $id_alimento);

        if (!$stmt->execute()) {
            return "Error al agregar a favoritos: " . $stmt->error;
        }

        return 1;

    }


}

?>