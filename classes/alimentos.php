<?php

#Ruta del archivo de la conexion
include_once(__DIR__ . "/../config/connect.php");

#Clase Alimentos
class Alimentos
{

    #Metodo privado que regresa el total de registros de una consulta
    private function numeroAlimentos($i_sql)
    {

        $connect = conectarDB();

        $sql = $i_sql;

        $result = $connect->query($i_sql);

        $row = $result->fetch_assoc();

        mysqli_close($connect);

        return $row["total"];

    }

    #Metodo que muestra todos los alimentos segun la marca
    function muestraAlimentos($marca = '')
    {


        #Comprueba si existe una pagina y en caso de que sea la primera lleva a la pagina
        #En caso de que no introduce por el metodo get la pagina elegida por el usuario
        if (isset($_GET["pagina"])) {
            if ($_GET["pagina"] == 1) {

                header("Location:muestra_alimentos.php");
                exit();
            } else {

                $pagina = $_GET["pagina"];
            }
            #Si no existe ninguna pagina devuelve a la primera pagina
        } else {

            $pagina = 1;
        }


        #Crea la consulta sql y cuentasql tambien para saber el numero de registros
        $sql = "SELECT * FROM alimentos WHERE 1=1";
        $cuentasql = "SELECT COUNT(*) AS total FROM alimentos WHERE 1=1";

        #Si el parametro marca no esta vacio añade la marca a las dos consultas anteriores
        if ($marca != '') {

            $sql .= " AND marca IN ($marca)";
            $cuentasql .= " AND marca IN ($marca)";
        }


        #El tamaño que va a mostrar cada vez va a ser de 10 en 10 alimentos y para saber desde donde empieza le resta 1 a la pagina y lo multiplica por el numero de alimentos que queramos mostrar
        $tamano_pag = 10;
        $empieza_desde = ($pagina - 1) * $tamano_pag;

        #Comprueba el numero total de registros y con el ceil hace la division para sacar el numero total de paginas
        $n_registros = $this->numeroAlimentos($cuentasql);
        $total_paginas = ceil($n_registros / $tamano_pag);

        #En el limit se pone desde donde empieza y el numero de registros que queremos
        $sql .= " LIMIT $empieza_desde,$tamano_pag";

        #Se crea la conexion con la base de datos, se obtiene los resultados y se cierra la conexion
        $connect = conectarDB();
        $result = $connect->query($sql);
        mysqli_close($connect);

        #Se mete los indices de la tabla con los datos de los alimentos
        $code = "<table>\n<tr>\n<td>Nombre</td>\n<td>Marca</td>\n<td>Porcion</td>\n<td>Kcal</td>\n<td>Grasa</td>\n<td>Grasas saturadas</td>\n<td>Carbohidratos</td>\n<td>Azúcar</td>\n<td>Proteina</td>\n<td>Sal</td>\n</tr>";

        #Se recorre cada resultado y se extraen los datos del alimento y se añaden al codigo en forma de tabla segun la posicion de su indice
        while ($data = mysqli_fetch_array($result)) {

            $code .= "<tr><td>" . $data[1] . "</td><td>" . $data[2] . "</td><td>" . $data[3] . "</td><td>" . $data[4] . "</td><td>" . $data[5] . "</td><td>" . $data[6] . "</td><td>" . $data[7] . "</td><td>" . $data[8] . "</td><td>" . $data[9] . "</td><td>" . $data[10] . "</td></tr>";

        }

        #Se llama al metodo privado paginacion alimentos y se le pasa por parametro la pagina actual, el numero total de paginas y la marca si existe
        $codigo_paginacion = $this->paginacion_alimentos($pagina, $total_paginas, $marca);


        #Al final del todo se junta el codigo con el de la paginacion y se muestra la tabla
        echo $code . $codigo_paginacion . "</table>";

    }


    #Metodo que sirve para paginar los alimentos, se introducen tres parametros
    #La pagina actual, el total de paginas y la marca si existe
    private function paginacion_alimentos($pagina, $total_paginas, $marca = "")
    {
        #Si la marca no esta vacia entonces añade &marca a la url
        $marca_parametro = ($marca != "") ? "&marca=" . $marca : "";

        #Codigo de la paginacion
        $code_paginacion = '<tr class="paginacion">';

        #Si es mayor que 1 mostrara la opcion de anterior
        if ($pagina > 1) {
            $code_paginacion .= '<td><a href="muestra_alimentos.php?pagina=' . ($pagina - 1) . $marca_parametro . '">Anterior</a> ... </td>';
        }

        # Genera enlaces numerados de páginas en un rango específico alrededor de la página actual
        for ($i = max(1, $pagina - 5); $i <= min($total_paginas, $pagina + 5); $i++) {
            if ($i <= $total_paginas) {
                #Agrega un enlace de página a la variable $code_paginacion
                $code_paginacion .= '<td><a href="muestra_alimentos.php?pagina=' . $i . $marca_parametro . '">' . $i . '</a></td>';
            }
        }

        if ($pagina < $total_paginas) {
            #Agrega un enlace "Siguiente" a la variable $code_paginacion si hay más páginas disponibles
            $code_paginacion .= '<td> ...<a href="muestra_alimentos.php?pagina=' . ($pagina + 1) . $marca_parametro . '"> Siguiente</a></td>';
        }

        $code_paginacion .= "</tr>";
        #Devuelve el contenido de $code_paginacion, que contiene todos los enlaces generados
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


    #Metodo que registra la comida del usuario, recibiendo como parametros los datos de la comida del usuario y los datos del propio usuario
    function registrar_comida($u_id, $id_alimento, $fecha, $u_porcion, $comida)
    {
        #Comprueba que existe el alimento y comprueba que existe la comida
        $comprobacion_alimento = $this->numeroAlimentos("SELECT COUNT(*) AS total FROM alimentos WHERE id_alimento=" . $id_alimento . "");
        $comprobacion_comida = $this->numeroAlimentos("SELECT COUNT(*) AS total FROM comen WHERE alimen_id = '" . $id_alimento . "' AND fecha = '" . $fecha . "' AND moment_comida = '" . $comida . "' AND cli_id = " . $u_id);


        #Si la comprobacion del alimento no es 1 entonces el alimento no existe
        if ($comprobacion_alimento != 1) {
            return "El alimento no existe y no se ha podido registrar.";
        }


        #Si la comprobacion de la comida es mayor a 0 significa que tiene que editarlo
        if ($comprobacion_comida > 0) {

            return "Ya has introducido este alimento en esta comida, ve a " . $comida . " para editarlo.";
        }

        #Se introducen los datos de los parametros y se crea la conexion y la consulta sql
        $datos = $u_id . "," . $id_alimento . ",'" . $fecha . "'," . $u_porcion . ",'" . $comida . "'";
        $conn = conectarDB();

        $sql = "INSERT INTO comen (cli_id,alimen_id,fecha, cantidad, moment_comida) VALUES(" . $datos . ")";

        #Si ha ocurrido algun error devolvera el error sino retornara 1
        if (!$conn->query($sql)) {

            return "Ha ocurrido un error al insertar los datos:" . $conn->error;

        }

        $conn->close();

        return 1;




    }


    #Funcion para obtener las comidas del usuario segun la fecha
    function get_comidas($u_id, $fecha)
    {
        #Se crea la conexion y la consulta a la base de datos
        $connect = conectarDB();

        $sql = $connect->prepare("SELECT id_comen,cli_id,alimen_id,nombre_alimen,fecha,cantidad,moment_comida,(cantidad*kcal)/porcion AS calc_kcal,(cantidad*grasas)/porcion AS calc_grasas,(cantidad*g_saturadas)/porcion AS calc_saturadas,(cantidad*carbohidratos)/porcion AS calc_carbos,(cantidad*azucar)/porcion AS calc_azucar,(cantidad*proteina)/porcion AS calc_proteina,(cantidad*sal)/porcion AS calc_sal FROM comen INNER JOIN alimentos ON alimen_id=alimentos.id_alimento WHERE cli_id=? AND fecha=?");

        #Se comprueba el tipo de datos
        $sql->bind_param("is", $u_id, $fecha);

        #Se ejecuta la consulta
        $sql->execute();

        #Se obtienen los resultados
        $resultado = $sql->get_result();

        #Se crea un array para cada comida
        $valores = [
            "desayuno" => null,
            "almuerzo" => null,
            "comida" => null,
            "merienda" => null,
            "cena" => null
        ];

        #Si el numero de resultados es 0 o menos, devolvera un 0
        if ($resultado->num_rows <= 0) {

            return 0;
        }


        #Recorre cada registro y lo añade segun la comida que sea al array valores
        while ($fila = $resultado->fetch_assoc()) {

            $momento_comidas = $fila['moment_comida'];


            $valores[$momento_comidas][] = $fila;

        }

        #Se cierran las conexiones y devuelve el array
        $sql->close();
        $connect->close();
        return $valores;


    }


    #Metodo que obtiene los datos de la comida
    function get_comida($comida)
    {


        $code = "";

        #Se pone el total de cada una de las comidas para calcularlo
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
        $sql = "SELECT * FROM alimentos WHERE nombre_alimen LIKE CONCAT('%', ?, '%') AND marca=? AND id_alimento NOT IN (SELECT id_alimefav FROM favoritos WHERE id_clifav=?) LIMIT 10";
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

            return "No tiene ningun alimento en favoritos.";
        }

        $stmt->close();
        $conn->close();

        $code = "";

        while ($row = $result->fetch_array()) {

            $data_alimento = $this->data_Alimento($row[0]);

            $code .= "<li><span>" . $data_alimento[1] . " (" . $data_alimento[2] . ")</span> <a href='#' onclick='deleteFavorites(" . $row[0] . ")'><img src='../images/estrella_luz.png'/></a></li>";
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

        $stmt->close();
        $conn->close();

        return 1;

    }

    function borrar_favorito($id_u, $id_alimen)
    {
        $conn = conectarDB();
        $sql = "DELETE FROM favoritos WHERE id_clifav=? AND id_alimefav=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $id_u, $id_alimen);

        if (!$stmt->execute()) {


            return "Ha ocurrido un error al quitar el alimento de favoritos, recarga la pagina";
        }


        return 1;
    }


    function buscar_favoritos($name, $marca, $id_u, $add = false)
    {

        $conn = conectarDB();

        $sql = "SELECT * FROM alimentos WHERE nombre_alimen LIKE CONCAT('%', ?, '%') AND marca=? AND id_alimento IN (SELECT id_alimefav FROM favoritos WHERE id_clifav=?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $name, $marca, $id_u);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows <= 0) {
            return 0;
        }

        $code = "";
        while ($row = $result->fetch_array()) {


            if (!$add) {
                $code .= "<li><span>" . $row[1] . " (" . $row[2] . ")</span> <a href='#' onclick='deleteFavorites(" . $row[0] . ")'><img src='../images/estrella_luz.png'/></a></li>";
            } else {
                $alimento = array(
                    "id" => $row[0],
                    "nombre" => $row[1],
                    "marca" => $row[2],
                    "porcion" => $row[3],
                    "kcal" => $row[4]
                );
                $r = json_encode($alimento);
                $text = $row[2] . ", " . $row[3] . " (gr o ml), " . $row[4] . "kcal";
                $code .= "<li onclick='muestraAlimentos($r)'><a href='#c1'>" . $row[1] . "</a><p>" . $text . "</p></li>";

            }
        }

        $stmt->close();
        $conn->close();

        return $code;


    }

    function top_ten_favorites()
    {

        $conn = conectarDB();

        $result = $conn->query("SELECT id_alimefav, COUNT(id_clifav) AS number FROM favoritos GROUP BY id_alimefav ORDER BY number DESC LIMIT 10");

        $nombre_alimentos = [];
        $times_added = [];

        $code = "";
        $num = 1;
        while ($row = $result->fetch_assoc()) {
            $alimento = $this->data_Alimento($row['id_alimefav']);

            $nombre_alimentos[] = $alimento[1];
            $times_added[] = $row['number'];

            $code .= "<tr>";
            $code .= "<td>" . $num . "</td>";
            $code .= "<td>" . $alimento[1] . " (" . $alimento[2] . ")</td>";
            $code .= "<td>" . $row['number'] . "</td>";

            $code .= "</tr>";
            $num += 1;
        }




        return [$code, $nombre_alimentos, $times_added];
    }


}

?>