const calculaKcal = (p_user, porcion) => {
  p_user = p_user.target.value;

  calc = (p_user * kcal) / porcion;

  document.getElementById("t_kcal").value = calc;
};

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

const listAlimentos = () => {
  let nAlimento = document.getElementById("n_alimento").value;
  let marca = document.getElementById("a_marca").value;
  let content = document.getElementById("contain2");
  let url = "../utils/add_comida.php";
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

const addComida = () => {
  code =
    "<h1>Añadir alimento</h1>" +
    '<div id="contain1">' +
    "<h3>Busqueda de los alimentos:</h3>" +
    '<form action="">' +
    '<input type="text" name="n_alimento" id="n_alimento" onchange="listAlimentos()"/>' +
    '<select name="a_marca" id="a_marca" onchange="listAlimentos()">' +
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
