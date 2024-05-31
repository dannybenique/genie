//formatea un numero a 2 decimales y con separador de miles
function appFormatMoney(num, c) {
  const n = new Number(num).toFixed(c);
  return (new Intl.NumberFormat("en-US").format(Math.trunc(n))+"."+(n+"").split(".")[1]);
};

//selecciona todas las filas en una Grid
function SelectAll(CheckBox, chkChild, appGridName) {
  var TargetBaseControl = document.getElementById(appGridName);
  var TargetChildControl = chkChild;
  var Inputs = TargetBaseControl.getElementsByTagName("input");
  for(var iCount = 0; iCount < Inputs.length; ++iCount)  {
    if(Inputs[iCount].type == 'checkbox' && Inputs[iCount].id.indexOf(TargetChildControl,0) >= 0)
    Inputs[iCount].checked = CheckBox.checked;
  }
}

//selecciona todas las filas en una Grid
function toggleAll(source,name){
  let checkboxes = document.getElementsByName(name);
  for(let i=0; i<checkboxes.length; i++) {
    checkboxes[i].checked = source.checked;
  }
}

//devuelve la URL absoluta del servidor
function appUrlServer(){
  let loc = window.location;
  let pathName = loc.pathname.substring(0, loc.pathname.lastIndexOf('/') + 1);
  return loc.href.substring(0, loc.href.length - ((loc.pathname + loc.search + loc.hash).length - pathName.length));
}

//convierte una fecha dd/mm/yyyy a yyyymmdd o yyyy-mm-dd (dependiendo del simbolo= "" "-")
function appConvertToFecha(miFecha,simbolo=""){
  return miFecha.split("/").reverse().join(simbolo);
}

//convierte un numero formateado con comas a numero
function appConvertToNumero(numFormateado){
  return Number(numFormateado.split(",").join(""));
}

//llenar un combobox con la data YA extraida de la DB
function appLlenarDataEnComboBox(data,miComboBox,valorSelect){
  let fila = "";
  data.forEach((valor,key)=>{
    fila += '<option value="'+(valor.ID)+'" '+((valor.ID==valorSelect) ? ("selected") : (""))+'>'+(valor.nombre)+'</option>'; 
  });
  document.querySelector(miComboBox).innerHTML = (fila);
}

//ejecutar ajax desde vanilla javascript
function appFetch(datos,rutaSQL){
  let data = new FormData();
  data.append('appSQL',JSON.stringify(datos));
  let rpta = fetch(rutaSQL, { method:'POST', body:data })
    .then(rpta => rpta.json())
    .catch(err => console.log(err));
  return rpta;
}

//ejecutar ajax desde vanilla javascript
async function appAsynFetch(datos, rutaSQL, method='POST') {
  let data = new FormData();
  data.append('appSQL', JSON.stringify(datos));

  try {
    const resp = (method=='GET') ? await fetch(rutaSQL, { method: method}) : await fetch(rutaSQL, { method: method, body: data });
    const rpta = await resp.json();
    return rpta;
  } catch (err) {
      console.log(err);
      return null;
  }
}