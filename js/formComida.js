//Funcion que calcula el total de kcal segun la porcion del usuario y la porcion del alimento
const calculaKcal = (p_user, porcion) => {
  p_user = p_user.target.value;

  calc = (p_user * kcal) / porcion;

  document.getElementById("t_kcal").value = calc;
};

//Funcion que muestra los datos de el alimento para que pueda registrarse a la comida
const muestraAlimentos = (vals) => {
  fecha = new Date().toISOString().split("T")[0];
  porcion = vals["porcion"];
  kcal = vals["kcal"];
  code =
    "<form action='./pages/registra_comida.php' method='POST'><p>" +
    vals["nombre"] +
    " (" +
    vals["marca"] +
    ")</p><p>Porcion (gr o ml): <input onchange='calculaKcal(event,porcion,kcal)' value='0' type='number' step='.01' id='nporcion' name='uporcion'/></p>";

  code +=
    "<p>Numero total de calorias: <input readonly value='0' type='number' step='.01' id='t_kcal' name='total_kcal'/></p>";

  code +=
    "<p>Comida: <select name='comidas'><option value='desayuno' selected>Desayuno</option><option value='almuerzo'>Almuerzo</option><option value='comida'>Comida</option><option value='merienda'>Merienda</option><option value='cena'>Cena</option></select></p>";

  code += "<p>Fecha: <input type='date' value=" + fecha + " name='fecha'/></p>";

  code += "<input type='hidden' name='id_alimen' value='" + vals["id"] + "'/>";

  code += "<input type='submit' name='send_food' value='Insertar comida'/>";
  document.getElementById("c2").innerHTML = code;
};

//Funcion que hace una peticion para obtener la lista de los alimentos segun la busqueda que queramos hacer
const listAlimentos = (container) => {
  let nAlimento = document.getElementById("n_alimento").value;
  let marca = document.getElementById("a_marca").value;
  let checkFavoritos = document.getElementById("check-fav");
  let checkFavs = checkFavoritos.checked;
  let content = document.getElementById(container);
  let url = "../utils/add_comida.php";
  let formData = new FormData();
  let checkFav = checkFavs ? "v" : "f";
  formData.append("n_alimento", nAlimento);
  formData.append("a_marca", marca);
  formData.append("check_fav", checkFav);

  fetch(url, { method: "POST", body: formData })
    .then((response) => response.json())
    .then((data) => {
      content.innerHTML = data;
    })
    .catch((err) => console.log(err));
};

//Recibe un formulario para cambiar el tamaño de la porcion del alimento que va a comer, a traves de una peticion
const changeAlimento = (id, e, idComida) => {
  e.preventDefault();
  let content = document.getElementById("container2");

  let url = "../utils/editar_comida.php";
  content.style.display = "flex";

  let formData = new FormData();
  formData.append("id_alimento", id);
  formData.append("id_comida", idComida);

  fetch(url, { method: "POST", body: formData })
    .then((response) => response.json())
    .then((data) => {
      content.innerHTML = "<div id='c2'>" + data + "</div>";
      window.location.href = "#c2";
    })
    .catch((err) => console.log(err));
};

//Formulario para buscar los alimentos que el usuario quiera ingerir
const addComida = () => {
  code =
    "<h1>Añadir alimento</h1>" +
    '<div id="contain1">' +
    "<h3>Busqueda de los alimentos:</h3>" +
    '<form action="">' +
    '<p>Favoritos: <input type="checkbox" id="check-fav" onclick="listAlimentos(\'contain2\')" value="okfav"></p>' +
    '<input type="text" name="n_alimento" id="n_alimento" onchange="listAlimentos(\'contain2\')"/>' +
    '<select name="a_marca" id="a_marca" onchange="listAlimentos(\'contain2\')">' +
    '<option value="hacendado" selected>Hacendado</option>' +
    '<option value="aldi">Aldi</option>' +
    '<option value="alcampo">Alcampo</option>' +
    '<option value="dia">DIA</option>' +
    '<option value="carrefour">Carrefour</option>' +
    "</select>" +
    "</form></div><div id='contain2'></div>";

  document.getElementById("container2").style.display = "flex";

  document.getElementById("container2").innerHTML = code;
};

//Funcion que calcula los valores segun la porcion introducida por el usuario
const calculaValores = (vals) => {
  let p_u = document.getElementById("p_u").value;
  let p_a = vals[0];

  let valores = vals.slice(1);

  let newVals = valores.map((val) => {
    return (p_u * val) / p_a;
  });

  console.log(newVals);
  document.getElementById("v_kcal").value = newVals[0];
  document.getElementById("v_grasas").value = newVals[1];
  document.getElementById("v_gsatu").value = newVals[2];
  document.getElementById("v_carbos").value = newVals[3];
  document.getElementById("v_azucar").value = newVals[4];
  document.getElementById("v_prote").value = newVals[5];
  document.getElementById("v_sal").value = newVals[6];
};

//Muestra la lista de alimentos que puedo añadir a favoritos
const listFavorites = (container) => {
  let nAlimento = document.getElementById("n_alimento").value;
  let marca = document.getElementById("a_marca").value;
  let content = document.getElementById(container);
  let url = "../utils/add_favoritos.php";
  let formData = new FormData();
  formData.append("n_alimento", nAlimento);
  formData.append("a_marca", marca);
  fetch(url, { method: "POST", body: formData })
    .then((response) => response.json())
    .then((data) => {
      content.innerHTML = data;
    })
    .catch((err) => console.log(err));
};

//Registra un alimento a favoritos y lo muestra
const addFavorites = (id) => {
  let content = document.getElementById("f-content");
  let url = "../utils/registra_favoritos.php";
  let formData = new FormData();
  formData.append("id_alimento", id.id);
  fetch(url, { method: "POST", body: formData })
    .then((response) => response.json())
    .then((data) => {
      content.innerHTML = data;
      listFavorites("con1");
    })
    .catch((err) => console.log(err));
};

//Elimina un alimento de mis favoritos
const deleteFavorites = (id_alimento) => {
  let content = document.getElementById("f-content");
  let url = "../utils/eliminar_favorito.php";
  let formData = new FormData();
  formData.append("id_alimento", id_alimento);

  fetch(url, { method: "POST", body: formData })
    .then((response) => response.json())
    .then((data) => {
      content.innerHTML = data;
    })
    .catch((err) => console.log(err));
};

//Muestra los alimentos que estan en mis favoritos con un parametro de busqueda por nombre o marca
const buscarFavoritos = () => {
  let content = document.getElementById("f-content");
  let url = "../utils/buscar_favoritos.php";
  let nAlimento = document.getElementById("f_alimento").value;
  let marca = document.getElementById("f_marca").value;
  let formData = new FormData();
  formData.append("name_ali", nAlimento);
  formData.append("marca", marca);

  fetch(url, { method: "POST", body: formData })
    .then((response) => response.json())
    .then((data) => {
      content.innerHTML = data;
    })
    .catch((err) => console.log(err));
};

const graficaPie = (alimentos, datos, canva) => {
  let can = document.getElementById(canva);

  const colores = [
    "#FF6384",
    "#36A2EB",
    "#FFCE56",
    "#4BC0C0",
    "#9966FF",
    "#FF9F40",
    "#FFD700",
    "#00CED1",
    "#FF00FF",
    "#008000",
  ];

  let myChart = new Chart(can, {
    type: "pie",
    data: {
      labels: alimentos,
      datasets: [
        {
          label: "Favoritos",
          data: datos,
          backgroundColor: colores.slice(0, datos.length),
        },
      ],
    },
  });
};
