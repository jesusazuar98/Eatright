<?php
include_once(__DIR__ . "/../classes/alimentos.php");

if (isset($_SESSION['usuario'])) {

    $user = unserialize($_SESSION['usuario']);

    $datos = $user->getUser();

    $alimentos = new Alimentos();

    $fecha_actual = date("Y-m-d");

    $comidas = $alimentos->get_comidas($datos['id'], (isset($_GET['fecha'])) ? $_GET['fecha'] : $fecha_actual);

    $r_diario = $alimentos->total_diario($datos['id'], (isset($_GET['fecha'])) ? $_GET['fecha'] : $fecha_actual, $comidas);




    ?>

    <div class="content">

        <div class="fecha">
            <a
                href="../index.php?fecha=<?php echo isset($_GET['fecha']) ? $fecha = date("Y-m-d", strtotime($_GET['fecha'] . " -1 day")) : $fecha = date("Y-m-d", strtotime($fecha_actual . " -1 day")); ?>">
                <img alt="left" src="./images/flecha-izquierda.png" />
            </a>



            <input type="date" id="calendar" name="calendar" onchange="cambiaFecha(event)"
                value="<?php echo isset($_GET['fecha']) ? $_GET['fecha'] : $fecha_actual ?>">

            <a
                href="../index.php?fecha=<?php echo isset($_GET['fecha']) ? $fecha = date("Y-m-d", strtotime($_GET['fecha'] . " +1 day")) : $fecha = date("Y-m-d", strtotime($fecha_actual . " +1 day")); ?>">
                <img alt="left" src="./images/flecha-derecha.png" />
            </a>
        </div>
        <script src="./js/changeDate.js"></script>

        <div class="r-comidas">

            <div class="comida">

                <div class="header-comida">

                    <h2>Desayuno</h2>

                    <div class="valores">
                        <p>Calorias (kcal)</p>
                        <p>Carbohidratos</p>
                        <p>Grasas</p>
                        <p>Grasas Saturadas</p>
                        <p>Azucar</p>
                        <p>Proteina</p>
                        <p>Sal</p>


                    </div>

                </div>
                <?php
                if (isset($comidas['desayuno'])) {

                    $r = $alimentos->get_comida($comidas['desayuno']);
                    echo $r;
                } else {

                    echo "<a href='#container2' onclick='addComida()' class='add_alimen'>Añadir alimento</a>";
                    echo "<hr></hr>";
                }

                ?>

            </div>

            <div class="comida">

                <div class="header-comida">

                    <h2>Almuerzo</h2>

                </div>
                <?php
                if (isset($comidas['almuerzo'])) {

                    $r = $alimentos->get_comida($comidas['almuerzo']);
                    echo $r;
                } else {

                    echo "<a href='#container2' onclick='addComida()' class='add_alimen'>Añadir alimento</a>";
                    echo "<hr></hr>";
                }
                ?>


            </div>

            <div class="comida">

                <div class="header-comida">

                    <h2>Comida</h2>

                </div>
                <?php
                if (isset($comidas['comida'])) {

                    $r = $alimentos->get_comida($comidas['comida']);
                    echo $r;
                } else {

                    echo "<a href='#container2' onclick='addComida()' class='add_alimen'>Añadir alimento</a>";
                    echo "<hr></hr>";
                }

                ?>

            </div>

            <div class="comida">

                <div class="header-comida">

                    <h2>Merienda</h2>

                </div>
                <?php
                if (isset($comidas['merienda'])) {

                    $r = $alimentos->get_comida($comidas['merienda']);
                    echo $r;
                } else {

                    echo "<a href='#container2' onclick='addComida()' class='add_alimen'>Añadir alimento</a>";
                    echo "<hr></hr>";
                }

                ?>

            </div>

            <div class="comida">

                <div class="header-comida">

                    <h2>Cena</h2>

                </div>
                <?php
                if (isset($comidas['cena'])) {

                    $r = $alimentos->get_comida($comidas['cena']);
                    echo $r;
                } else {

                    echo "<a href='#container2' onclick='addComida()' class='add_alimen'>Añadir alimento</a>";
                    echo "<hr></hr>";
                }

                ?>

                <div class="resumen-diario">
                    <h2>Resumen Diario</h2>
                    <div class="valores-comida">
                        <?php
                        if ($r_diario != 0) {
                            echo "
                        <p>" . $r_diario[0]['calc_kcal'] . "</p>
                        <p>" . $r_diario[0]['calc_carbos'] . "</p>
                        <p>" . $r_diario[0]['calc_grasas'] . "</p>
                        <p>" . $r_diario[0]['calc_saturadas'] . "</p>
                        <p>" . $r_diario[0]['calc_azucar'] . "</p>
                        <p>" . $r_diario[0]['calc_proteina'] . "</p>
                        <p>" . $r_diario[0]['calc_sal'] . "</p>
                        ";
                        }
                        ?>
                    </div>
                </div>

            </div>










        </div>

        <div class="container2" id="container2">


        </div>
        <script src="./js/formComida.js"></script>
    </div>
    <?php
}
?>