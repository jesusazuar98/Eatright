const calculaKcal = (p_user, porcion, kcal) => {
  p_user = p_user.target.value;

  calc = (p_user * kcal) / porcion;

  document.getElementById("t_kcal").value = calc;
};

const muestraAlimentos = (vals) => {
  fecha = new Date().toISOString().split("T")[0];
  porcion = vals["porcion"];
  kcal = vals["kcal"];
  code =
    "<form action='registra_comida.php' method='POST'><p>" +
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
