const rutaSQL = "pages/mtto/config/sql.php";
var menu = null;
var config = null;
var strLoader = '<tr><td colspan="7"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>';

//=========================funciones de Formulario============================
async function appConfigReset(){
  try{
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    menu = JSON.parse(resp.menu);

    const rpta = await appAsynFetch({ TipoQuery:'config_start' },rutaSQL);
    config = rpta.config;
    rpta.comboYEAR.map(function(valor){ valor.nombre=valor.ID; return valor; });
    appLlenarDataEnComboBox(rpta.comboYEAR,"#cbo_currYEAR",config.YearCurrentMatricula);
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appHabilitarGuardar(){
  config.YearCurrentMatricula = document.querySelector("#cbo_currYEAR").value;
  document.querySelector("#btn_SAVE").disabled = false;
}

async function appConfigUpdate(){
  try{
    const resp = await appAsynFetch({
      TipoQuery : "config_update",
      config : JSON.stringify(config)
    }, rutaSQL);
    
    //respuesta
    if(!resp.error){ document.querySelector("#btn_SAVE").disabled = true; }
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
  
}

