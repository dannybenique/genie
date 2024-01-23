const rutaSQL = "pages/fondos/aportes/sql.php";
var menu = "";

//=========================funciones para Personas============================
function appAportesGrid(){
  document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="5"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  let txtBuscar = document.querySelector("#txtBuscar").value;
  let datos = { TipoQuery: 'selAportes', buscar: txtBuscar };

  appFetch(datos,rutaSQL).then(resp => {
    console.log(resp);
    let disabledDelete = (menu.fondos.submenu.aportes.cmdDelete===1) ? "" : "disabled";
    document.querySelector("#chk_All").disabled = (menu.fondos.submenu.aportes.cmdDelete===1) ? false : true;
    if(resp.tabla.length>0){
      let fila = "";
      resp.tabla.forEach((valor,key)=>{
        fila += '<tr style="color:'+((resp.obliga)?((valor.saldo>0)?("black"):("red")):("black"))+'">';
        fila += '<td>'+((valor.num_movim==0)?('<input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'" '+disabledDelete+'/>'):(''))+'</td>';
        fila += '<td>'+(valor.nro_dui)+'</td>';
        fila += '<td>'+(valor.socio)+'</td>';
        fila += '<td><a href="javascript:appAportesView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.codigo+' &raquo; '+valor.producto+'; '+valor.m_abrevia+';')+'</a></td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.saldo,2)+'</td>';
        fila += '</tr>';
      });
      $('#grdDatos').html(fila);
    }else{
      let res = (txtBuscar==="") ? ("") : ("para "+txtBuscar);
      $('#grdDatos').html('<tr><td colspan="5" style="text-align:center;color:red;">Sin Resultados '+(res)+'</td></tr>');
    }
    $('#grdCount').html(resp.tabla.length+"/"+resp.cuenta);
  });
}

function appAportesReset(){
  appFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php").then(resp => {
    menu = JSON.parse(resp.menu);
    document.querySelector("#btn_DEL").style.display = (menu.fondos.submenu.aportes.cmdDelete==1)?('inline'):('none');
    document.querySelector("#btn_NEW").style.display = (menu.fondos.submenu.aportes.cmdInsert==1)?('inline'):('none');
    document.querySelector("#txtBuscar").value = ("");
    appAportesGrid();
  });
}

function appAportesBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { load_flag = 0; $('#grdDatosBody').html(""); appAportesGrid(); }
}

function appAportesRefresh(){
  let aporteID = document.querySelector('#hid_aporteID').value;
  appAportesView(aporteID);
}

function appAportesBotonCancel(){
  appAportesGrid();
  $('#grid').show();
  $('#edit').hide();
}

function appAportesBotonNuevo(){
  Persona.openBuscar('VerifyAportes',rutaSQL,false,true,false);
  $('#btn_modPersAddToForm').on('click',function(e) {
    if(confirm('Confirme que realmente desea agregar APORTES a este socio')){
      let datos = {
        TipoQuery : 'insAportes',
        socioID : Persona.tablaPers.ID
      }
      appFetch(datos,rutaSQL).then(resp => {
        appAportesGrid();
        Persona.close();
      });
      e.stopImmediatePropagation();
      $('#btn_modPersAddToForm').off('click');
    }
  });
}

function appAportesBotonBorrar(){
  let arr = Array.from(document.querySelectorAll('[name="chk_Borrar"]:checked')).map(function(obj){return obj.attributes[2].nodeValue});
  if(arr.length>0){
    if(confirm("¿Esta seguro de continuar?")) {
      appFetch({ TipoQuery:'delAportes', arr:arr },rutaSQL).then(resp => {
        if (resp.error == false) { //sin errores
          console.log(resp);
          appAportesBotonCancel();
        }
      });
    }
  } else {
    alert("NO eligio borrar ninguno");
  }
}

function appAportesView(aporteID){
  let datos = {
    TipoQuery : 'viewAporte',
    aporteID : aporteID
  };
  
  appFetch(datos,rutaSQL).then(resp => {
    appAportesSetData(resp.aporte);
    appMovimSetData(resp.movim);
    document.querySelector('#grid').style.display = 'none';
    document.querySelector('#edit').style.display = 'block';
  });
}

function appAportesSetData(data){
  document.querySelector('#hid_aporteID').value = (data.ID);
  document.querySelector('#lbl_aporteSocio').innerHTML = (data.socio);
  document.querySelector('#lbl_aporteTipoDUI').innerHTML = (data.dui);
  document.querySelector('#lbl_aporteNroDUI').innerHTML = (data.nro_dui);
  document.querySelector('#lbl_aporteCodigo').innerHTML = (data.cod_prod);
  document.querySelector('#lbl_aporteSaldo').innerHTML = (appFormatMoney(data.saldo,2));
}

function appMovimSetData(data){
  // console.log(data);
  let totIngresos = 0;
  let totSalidas = 0;
  let totOtros = 0;
  let fila = "";
  data.forEach((valor,key)=>{
    totIngresos += valor.ingresos;
    totSalidas += valor.salidas;
    totOtros += valor.otros;
    fila += '<tr>';
    fila += '<td>'+(valor.ag)+'</td>';
    fila += '<td>'+(valor.us)+'</td>';
    fila += '<td style="text-align:center;">'+(moment(valor.fecha).format("DD/MM/YYYY"))+'</td>';
    fila += '<td style="text-align:center;">'+(valor.codigo)+'</td>';
    fila += '<td>'+(valor.codmov+' '+valor.movim)+'</td>';
    fila += '<td style="text-align:right;">'+((valor.ingresos>0)?(appFormatMoney(valor.ingresos,2)):(''))+'</td>';
    fila += '<td style="text-align:right;">'+((valor.salidas>0)?(appFormatMoney(valor.salidas,2)):(''))+'</td>';
    fila += '<td style="text-align:right;">'+((valor.otros>0)?appFormatMoney(valor.otros,2):(''))+'</td>';
    fila += '</tr>';
  });
  fila += '<tr>';
  fila += '<td colspan="5" style="text-align:center;"><b>Total</b></td>';
  fila += '<td style="text-align:right;"><b>'+appFormatMoney(totIngresos,2)+'</b></td>';
  fila += '<td style="text-align:right;"><b>'+appFormatMoney(totSalidas,2)+'</b></td>';
  fila += '<td style="text-align:right;"><b>'+appFormatMoney(totOtros,2)+'</b></td>';
  fila += '</tr>';
  document.querySelector('#grdDetalleDatos').innerHTML = fila;
  document.querySelector('#lbl_movimSaldo').innerHTML = appFormatMoney(totIngresos-totSalidas,2);
}
