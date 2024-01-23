const rutaSQL = "pages/master/tipos/sql.php";
var menu = "";

//=========================funciones para Personas============================
function appTiposGrid(){
  document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="9"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  let txtBuscar = document.querySelector("#txtBuscar").value.toUpperCase();
  let cboTipo = document.querySelector("#cbo_Tipos").value;
  let datos = { TipoQuery: 'selTipos', tipo:cboTipo, buscar:txtBuscar };

  appFetch(datos,rutaSQL).then(resp => {
    if(resp.tipos.length>0){
      let fila = "";
      resp.tipos.forEach((valor,key)=>{
        fila += '<tr>';
        fila += '<td>'+(valor.ID)+'</td>';
        fila += '<td style="text-align:center;">'+((valor.tipoID!=null)?('<i class="fa fa-info-circle" title="Este ID esta habilitado para esta coopac" style="color:#0097BC;"></i>'):(''))+'</td>';
        fila += '<td>'+(valor.nombre)+'</td>';
        fila += '<td>'+(valor.codigo)+'</td>';
        fila += '<td>'+(valor.abrevia)+'</td>';
        fila += '<td style="text-align:center;">'+(valor.tipo)+'</td>';
        fila += '<td style="text-align:center;">'+(valor.padreID)+'</td>';
        fila += '<td>'+(valor.nivel)+'</td>';
        fila += '<td></td>';
        fila += '</tr>';
      });
      document.querySelector('#grdDatos').innerHTML = (fila);
    }else{
      document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="9" style="text-align:center;color:red;">Sin Resultados '+((txtBuscar=="")?(""):("para "+txtBuscar))+'</td></tr>');
    }
    document.querySelector('#grdCount').innerHTML = (resp.tipos.length);
  });
}

function appTiposReset(){
  appFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php").then(resp => {
    menu = JSON.parse(resp.menu);
    
    document.querySelector("#txtBuscar").value = ("");
    appFetch({ TipoQuery:'startTipos' },rutaSQL).then(resp => {
      appLlenarDataEnComboBox(resp.comboTipos,"#cbo_Tipos",0); //tipos de pago
      appTiposGrid();
    });
  });
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

function appTipoView(tipoID){
  document.querySelector('#grid').style.display = 'none';
  document.querySelector('#edit').style.display = 'block';
  document.querySelector('#btnInsert').style.display = 'none';
  document.querySelector('#btnUpdate').style.display = 'inline';
  $(".form-group").removeClass("has-error");

  let datos = {
    TipoQuery : 'viewTipo',
    ID : tipoID
  }
  appFetch(datos,rutaSQL).then(resp => {
    document.querySelector("#hid_tipoID").value = (resp.tipo.ID);
    document.querySelector("#txt_Codigo").value = (resp.tipo.codigo);
    document.querySelector("#txt_Abrev").value = (resp.tipo.abrevia);
    document.querySelector("#txt_Nombre").value = (resp.tipo.nombre);
    document.querySelector("#txt_Tipo").value = (resp.tipo.tipo);
    appLlenarDataEnComboBox(resp.comboTipos,"#cbo_Padre",resp.padreID);
  });
}
