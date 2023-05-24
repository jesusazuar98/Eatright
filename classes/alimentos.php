<?php

include_once(__DIR__ . "/../config/connect.php");


class Alimentos
{

    function numeroAlimentos($i_sql)
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

        $sql = "SELECT * FROM alimentos WHERE nombre_alimen LIKE '%" . $name . "%' AND marca='" . $marca . "'";

        $result = $conn->query($sql);

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


        $conn->close();

        return $alimentos;

    }



    function registrar_comida($u_id, $id_alimento, $fecha, $u_porcion, $comida)
    {
        $comprobacion = $this->numeroAlimentos("SELECT COUNT(*) AS total FROM alimentos WHERE id_alimento=" . $id_alimento . "");

        if ($comprobacion != 1) {
            return "El alimento no existe y no se ha podido registrar.";
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
            $code .= "<div class='option'><form action='./pages/options_comen.php' method='POST'><input type='hidden' value=" . $alimento['id_comen'] . "><input type='image' src='./images/eliminar.png' name='borrar'/></form></div>";
            $code .= "<div class='option'><form action='./pages/options_comen.php' method='POST'><input type='hidden' value=" . $alimento['id_comen'] . "><input type='image' src='./images/eliminar.png' name='editar'/></form></div>";

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
}

?>