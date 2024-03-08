const rutaSQL = "pages/master/tipos/sql.php";
var menu = "";

//=========================funciones para Personas============================
async function appTiposGrid(){
  document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="9"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  try{
    const txtBuscar = document.querySelector("#txtBuscar").value.toUpperCase();
    const cboTipo = document.querySelector("#cbo_Tipos").value;
    const resp = await appAsynFetch({ TipoQuery: 'selTipos', tipo:cboTipo, buscar:txtBuscar }, rutaSQL);
    
    //respuesta
    if(resp.tipos.length>0){
      let fila = "";
      resp.tipos.forEach((valor,key)=>{
        fila += '<tr style="'+((valor.estado==0)?("color:#bfbfbf;"):(""))+'">'+
                '<td>'+(valor.ID)+'</td>'+
                '<td style="text-align:center;">'+((valor.tipoID!=null)?('<i class="fa fa-info-circle" title="Este ID esta habilitado para esta coopac" style="color:#0097BC;"></i>'):(''))+'</td>'+
                '<td>'+(valor.nombre)+'</td>'+
                '<td>'+(valor.codigo)+'</td>'+
                '<td>'+(valor.abrevia)+'</td>'+
                '<td style="text-align:center;">'+(valor.tipo)+'</td>'+
                '<td style="text-align:center;">'+(valor.padreID)+'</td>'+
                '<td>'+(valor.nivel)+'</td>'+
                '<td></td>'+
                '</tr>';
      });
      document.querySelector('#grdDatos').innerHTML = (fila);
    }else{
      document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="9" style="text-align:center;color:red;">Sin Resultados '+((txtBuscar=="")?(""):("para "+txtBuscar))+'</td></tr>');
    }
    document.querySelector('#grdCount').innerHTML = (resp.tipos.length);
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appTiposReset(){
  try{
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    menu = JSON.parse(resp.menu);
    document.querySelector("#txtBuscar").value = ("");

    const rpta = await appAsynFetch({ TipoQuery:'startTipos' },rutaSQL);
    appLlenarDataEnComboBox(rpta.comboTipos,"#cbo_Tipos",0); //tipos de pago
    appTiposGrid();
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appTiposBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { appTiposGrid(); }
}

function appTiposCancel(){
  document.querySelector('#grid').style.display = 'block';
  document.querySelector('#edit').style.display = 'none';
  appTiposGrid();
}

async function appTipoView(tipoID){
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
