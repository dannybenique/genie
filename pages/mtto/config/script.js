const rutaSQL = "pages/mtto/config/sql.php";
var menu = null;
var config = null;
var strLoader = '<tr><td colspan="7"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>';

//=========================funciones de Formulario============================
function appConfigReset(){
  appFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php").then(resp => {
    menu = JSON.parse(resp.menu);
    let datos = { TipoQuery:'config_start' }
    appFetch(datos,rutaSQL).then(resp => {
      config = resp.config;
      resp.comboYEAR.map(function(valor){ valor.nombre=valor.ID; return valor; });
      appLlenarDataEnComboBox(resp.comboYEAR,"#cbo_currYEAR",config.YearCurrentMatricula);
    })
  });
}

function appHabilitarGuardar(){
  config.YearCurrentMatricula = document.querySelector("#cbo_currYEAR").value;
  document.querySelector("#btn_SAVE").disabled = false;
}

function appCoonfigUpdate(){
  let datos = {
    TipoQuery : "config_update",
    config : JSON.stringify(config)
  }
  appFetch(datos,rutaSQL).then(resp => {
    document.querySelector("#btn_SAVE").disabled = true;
  });
}

