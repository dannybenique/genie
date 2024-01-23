const rutaSQL = "pages/oper/creditos/sql.php";
var menu = "";

//=========================funciones para Personas============================
function appCreditosGrid(){
  document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="10"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  let txtBuscar = document.querySelector("#txtBuscar").value;
  let datos = {
    TipoQuery: 'selCreditos',
    buscar: txtBuscar
  };

  appFetch(datos,rutaSQL).then(resp => {
    if(resp.tabla.length>0){
      let fila = "";
      resp.tabla.forEach((valor,key)=>{
        fila += '<tr>';
        fila += '<td>'+(moment(valor.fecha).format("DD/MM/YYYY"))+'</td>';
        fila += '<td>'+(valor.nro_dui)+'</td>';
        fila += '<td>'+(valor.socio)+'</td>';
        fila += '<td><a href="javascript:appCreditosView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.producto+' :: '+valor.codigo+'; '+valor.mon_abrevia+'; '+appFormatMoney(valor.tasa,2))+'%</a></td>';
        fila += '<td>'+(valor.tiposbs)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.importe,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.saldo,2)+'</td>';
        fila += '<td style="text-align:center;">'+(valor.nro_cuotas)+'</td>';
        fila += '</tr>';
      });
      $('#grdDatos').html(fila);
    }else{
      let res = (txtBuscar==="") ? ("") : ("para "+txtBuscar);
      $('#grdDatos').html('<tr><td colspan="10" style="text-align:center;color:red;">Sin Resultados '+(res)+'</td></tr>');
    }
    $('#grdCount').html(resp.tabla.length+"/"+resp.cuenta);
  });
}

function appCreditosReset(){
  appFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php").then(resp => {
    menu = JSON.parse(resp.menu);
    document.querySelector("#txtBuscar").value = ("");
    appCreditosGrid();
  });
}

function appCreditosBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { load_flag = 0; $('#grdDatosBody').html(""); appCreditosGrid(); }
}

function appCreditosRefresh(){
  let prestamoID = document.querySelector('#hid_crediID').value;
  appCreditosView(prestamoID);
}

function appCreditosBotonCancel(){
  appCreditosGrid();
  $('#grid').show();
  $('#edit').hide();
}

function appCreditosView(prestamoID){
  let datos = {
    TipoQuery : 'viewCredito',
    prestamoID : prestamoID
  };
  
  appFetch(datos,rutaSQL).then(resp => {
    appCreditoSetData(resp.prestamo);
    appDetalleSetData(resp.detalle);
    document.querySelector('#grid').style.display = 'none';
    document.querySelector('#edit').style.display = 'block';
  });
}

function appCreditoSetData(data){
  document.querySelector('#hid_crediID').value = (data.ID);
  document.querySelector('#lbl_crediSocio').innerHTML = (data.socio);
  document.querySelector('#lbl_crediTipoDUI').innerHTML = (data.dui);
  document.querySelector('#lbl_crediNroDUI').innerHTML = (data.nro_dui);
  document.querySelector('#lbl_crediFecha').innerHTML = (moment(data.fecha_otorga).format("DD/MM/YYYY"));
  document.querySelector('#lbl_crediProducto').innerHTML = (data.producto);
  document.querySelector('#lbl_crediCodigo').innerHTML = (data.codigo);
  document.querySelector('#lbl_crediTasa').innerHTML = (appFormatMoney(data.tasa,2)+"%");
  document.querySelector('#lbl_crediAgencia').innerHTML = (data.agencia);
  document.querySelector('#lbl_crediPromotor').innerHTML = (data.promotor);
  document.querySelector('#lbl_crediAnalista').innerHTML = (data.analista);
  document.querySelector('#lbl_crediImporte').innerHTML = (appFormatMoney(data.importe,2));
  document.querySelector('#lbl_crediSaldo').innerHTML = (appFormatMoney(data.saldo,2));
}

function appDetalleSetData(data){
  //console.log(data);
  let totTotal = 0;
  let totCapital = 0;
  let totInteres = 0;
  let totSeguro = 0;
  let totGastos = 0;
  let fila = "";
  data.forEach((valor,key)=>{
    totTotal += valor.total;
    totCapital += valor.capital;
    totInteres += valor.interes;
    totSeguro += valor.seguro;
    fila += '<tr>';
    fila += '<td>'+(valor.numero)+'</td>';
    fila += '<td>'+(moment(valor.fecha).format("DD/MM/YYYY"))+'</td>';
    fila += '<td style="text-align:right;">'+appFormatMoney(valor.total,2)+'</td>';
    fila += '<td style="text-align:right;">'+appFormatMoney(valor.capital,2)+'</td>';
    fila += '<td style="text-align:right;">'+appFormatMoney(valor.interes,2)+'</td>';
    fila += '<td style="text-align:right;">'+appFormatMoney(valor.seguro,2)+'</td>';
    fila += '<td style="text-align:right;">'+appFormatMoney(valor.gastos,2)+'</td>';
    fila += '<td style="text-align:right;">'+appFormatMoney(valor.saldo,2)+'</td>';
    fila += '<td style="text-align:center;">0</td>';
    fila += '<td></td>';
    fila += '</tr>';
  });
  fila += '<tr>';
  fila += '<td colspan="2" style="text-align:center;"><b>Total</b></td>';
  fila += '<td style="text-align:right;"><b>'+appFormatMoney(totTotal,2)+'</b></td>';
  fila += '<td style="text-align:right;"><b>'+appFormatMoney(totCapital,2)+'</b></td>';
  fila += '<td style="text-align:right;"><b>'+appFormatMoney(totInteres,2)+'</b></td>';
  fila += '<td style="text-align:right;"><b>'+appFormatMoney(totSeguro,2)+'</b></td>';
  fila += '<td style="text-align:right;"><b>'+appFormatMoney(totGastos,2)+'</b></td>';
  fila += '<td colspan="3"></td>';
  fila += '</tr>';
  $('#grdDetalleDatos').html(fila);
}
