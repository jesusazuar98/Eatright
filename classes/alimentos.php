<?php

require_once "../config/connect.php";

class Alimentos
{

    function chooseFood($n_ali)
    {
        $connect = conectarDB();
        $sql = "SELECT * FROM alimentos WHERE nombre_alimen LIKE %" . $n_ali . "% LIMIT 10;";



    }


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

    #Metodo que busca los alimentos de una base de datos por su nombre
    #Guarda todas las coincidencias en un array y muestra su nombre, marca, porcion y kcal
    function buscar_alimentos($name)
    {

        $conn = conectarDB();

        $sql = "SELECT * FROM alimentos WHERE nombre_alimen LIKE '%" . $name . "%'";

        $result = $conn->query($sql);

        if ($result->num_rows <= 0) {

            return 0;

        }

        $alimentos = array();

        while ($row = $result->fetch_assoc()) {

            $alimento = array(
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


}

?>