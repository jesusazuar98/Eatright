<?php
include_once(__DIR__ . "/../classes/alimentos.php");

if (isset($_SESSION['usuario'])) {

    $user = unserialize($_SESSION['usuario']);

    $datos = $user->getUser();

    $alimentos = new Alimentos();

    $comidas = $alimentos->get_comidas($datos['id'], (isset($_GET['fecha'])) ? $_GET['fecha'] : date("Y-m-d"));


    ?>

    <div class="content">

        <!-- <div class="fecha">
            <h3>Registro de comida de la fecha: <p></p>
            </h3>

        </div> -->

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
                    echo $r[0];
                }

                ?>
                <a href="./pages/add_comida.php" class="add_alimen">Añadir alimento</a>
                <hr>
                </hr>
            </div>








        </div>

        <div class="container2">


        </div>
    </div>
    <?php
}
?>