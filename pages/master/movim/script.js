const rutaSQL = "pages/master/movim/sql.php";
var menu = "";

//=========================funciones para Personas============================
async function appMovimGrid(){
  document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="8"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  try{
    const txtBuscar = document.querySelector("#txtBuscar").value.toUpperCase();
    const resp = await appAsynFetch({ TipoQuery: 'selMovims', buscar:txtBuscar }, rutaSQL);
    
    //respuesta
    if(resp.movs.length>0){
      let fila = "";
      resp.movs.forEach((valor,key)=>{
        fila += '<tr>';
        fila += '<td>'+(valor.ID)+'</td>';
        fila += '<td>'+(valor.nombre)+'</td>';
        fila += '<td>'+(valor.codigo)+'</td>';
        fila += '<td>'+(valor.abrevia)+'</td>';
        fila += '<td>'+(valor.tipo_operID)+'</td>';
        fila += '<td>'+(valor.in_out)+'</td>';
        fila += '<td>'+(valor.afec_prod)+'</td>';
        fila += '</tr>';
      });
      document.querySelector('#grdDatos').innerHTML = (fila);
    }else{
      document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="8" style="text-align:center;color:red;">Sin Resultados '+((txtBuscar=="")?(""):("para "+txtBuscar))+'</td></tr>');
    }
    document.querySelector('#grdCount').innerHTML = (resp.movs.length);
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appMovimReset(){
  try{
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    menu = JSON.parse(resp.menu);
    document.querySelector("#txtBuscar").value = ("");
    appMovimGrid();
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appMovimBuscar(e){
  if(e.keyCode === 13) { appMovimGrid(); }
}

function appMovimCancel(){
  document.querySelector('#grid').style.display = 'block';
  document.querySelector('#edit').style.display = 'none';
  appMovimGrid();
}

async function appMovimView(tipoID){
  document.querySelector('#grid').style.display = 'none';
  document.querySelector('#edit').style.display = 'block';
  document.querySelector('#btnInsert').style.display = 'none';
  document.querySelector('#btnUpdate').style.display = 'inline';
  $(".form-group").removeClass("has-error");

  try{
    const resp = await appAsynFetch({
      TipoQuery : 'viewTipo',
      ID : tipoID
    }, rutaSQL);
    
    //respuesta
    document.querySelector("#hid_tipoID").value = (resp.tipo.ID);
    document.querySelector("#txt_Codigo").value = (resp.tipo.codigo);
    document.querySelector("#txt_Abrev").value = (resp.tipo.abrevia);
    document.querySelector("#txt_Nombre").value = (resp.tipo.nombre);
    document.querySelector("#txt_Tipo").value = (resp.tipo.tipo);
    appLlenarDataEnComboBox(resp.comboTipos,"#cbo_Padre",resp.padreID);
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}
