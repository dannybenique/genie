const rutaSQL = "pages/oper/creditos/sql.php";
var viewTotalPagado = false;
var viewTotalPorVencer = false;
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
        fila += '<td><a href="javascript:appCreditosView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.codigo+' &raquo; '+valor.producto+'; '+valor.mon_abrevia+'; '+appFormatMoney(valor.tasa,2))+'%</a></td>';
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
    console.log(resp);
    appCabeceraSetData(resp.prestamo);
    appDetalleSetData(resp.detalle);
    document.querySelector('#grid').style.display = 'none';
    document.querySelector('#edit').style.display = 'block';
  });
}

function appCabeceraSetData(data){
  document.querySelector('#hid_crediID').value = (data.ID);
  document.querySelector('#lbl_crediSocio').innerHTML = (data.socio);
  document.querySelector('#lbl_crediTipoDUI').innerHTML = (data.dui);
  document.querySelector('#lbl_crediNroDUI').innerHTML = (data.nro_dui);
  document.querySelector('#lbl_crediID').innerHTML = (data.ID);
  document.querySelector('#lbl_crediFecha').innerHTML = (moment(data.fecha_otorga).format("DD/MM/YYYY"));
  document.querySelector('#lbl_crediProducto').innerHTML = (data.producto);
  document.querySelector('#lbl_crediCodigo').innerHTML = (data.codigo);
  document.querySelector('#lbl_crediCodigo').title = (data.ID);
  document.querySelector('#lbl_crediTasaCred').innerHTML = (appFormatMoney(data.tasa,2)+'% <span style="font-size:10px;">(TEA)</span>');
  document.querySelector('#lbl_crediTasaMora').innerHTML = (appFormatMoney(data.mora,2)+'% <span style="font-size:10px;">(TEA)</span>');
  document.querySelector('#lbl_crediMoneda').innerHTML = (data.moneda+' <span style="font-size:10px;">('+data.mon_abrevia+')</span>');
  document.querySelector('#lbl_crediAgencia').innerHTML = (data.agencia);
  document.querySelector('#lbl_crediPromotor').innerHTML = (data.promotor);
  document.querySelector('#lbl_crediAnalista').innerHTML = (data.analista);
  document.querySelector('#lbl_crediImporte').innerHTML = (appFormatMoney(data.importe,2));
  document.querySelector('#lbl_crediSaldo').innerHTML = (appFormatMoney(data.saldo,2));
}

function appDetalleSetData(data){
  // console.log(data);
  let cuoTotal = 0;
  let cuoCapital = 0;
  let cuoInteres = 0;
  let cuoMora = 0;
  let cuoOtros = 0;
  let totGrayTotal = 0;
  let totGrayCapital = 0;
  let totGrayInteres = 0;
  let totGrayMora = 0;
  let totGrayOtros = 0;
  let totRedTotal = 0;
  let totRedCapital = 0;
  let totRedInteres = 0;
  let totRedMora = 0;
  let totRedOtros = 0;
  let totBlackTotal = 0;
  let totBlackCapital = 0;
  let totBlackInteres = 0;
  let totBlackMora = 0;
  let totBlackOtros = 0;
  let fecha = "";
  let fila = "";

  data.forEach((valor,key)=>{
    if(valor.capital==valor.pg_capital){ //cuota pagada
      totGrayTotal += valor.total+valor.pg_mora;
      totGrayCapital += valor.pg_capital;
      totGrayInteres += valor.pg_interes;
      totGrayMora += valor.pg_mora;
      totGrayOtros += valor.pg_otros;
    } else {
      if(valor.atraso>=0){ //cuota en deuda
        totRedTotal += valor.total+valor.mora;
        totRedCapital += valor.capital-valor.pg_capital;
        totRedInteres += valor.interes-valor.pg_interes;
        totRedMora += valor.mora-valor.pg_mora;
        totRedOtros += valor.otros-valor.pg_otros;
      } else { //cuota por vencer
        totBlackTotal += valor.total;
        totBlackCapital += valor.capital;
        totBlackInteres += valor.interes;
        totBlackMora += valor.mora;
        totBlackOtros += valor.otros;
      }
    }
    cuoOtros = (valor.capital==valor.pg_capital)?(valor.pg_otros):(valor.otros);
    cuoMora = (valor.capital==valor.pg_capital)?(valor.pg_mora):(valor.mora);
    cuoInteres = (valor.capital==valor.pg_capital)?(valor.pg_interes):(valor.interes-valor.pg_interes);
    cuoCapital = (valor.capital==valor.pg_capital)?(valor.pg_capital):(valor.capital-valor.pg_capital);
    cuoTotal = cuoCapital + cuoInteres + cuoMora + cuoOtros;
    fila += '<tr style="'+((valor.numero==0)?('color:#bbb;'):((valor.capital==valor.pg_capital)?('color:#bbb;'):((valor.atraso>=0)?("color:#f00;"):(""))))+'">'+
            '<td>'+(valor.numero)+'</td>'+
            '<td>'+((valor.numero>0)?(moment(valor.fecha).diff(fecha,"days")):(0))+'</td>'+
            '<td>'+(moment(valor.fecha).format("DD/MM/YYYY"))+'</td>'+
            '<td style="text-align:right;">'+appFormatMoney(cuoTotal,2)+'</td>'+
            '<td style="text-align:right;" title="Inicial:&nbsp;'+appFormatMoney(valor.capital,2)+'\nA Cta:&nbsp;'+appFormatMoney(valor.pg_capital,2)+'\nActual:&nbsp;'+appFormatMoney(valor.capital-valor.pg_capital,2)+'">'+appFormatMoney((cuoCapital),2)+'</td>'+
            '<td style="text-align:right;" title="Inicial:&nbsp;'+appFormatMoney(valor.interes,2)+'\nA Cta:&nbsp;'+appFormatMoney(valor.pg_interes,2)+'\nActual:&nbsp;'+appFormatMoney(valor.interes-valor.pg_interes,2)+'">'+appFormatMoney((cuoInteres),2)+'</td>'+
            '<td style="text-align:right;">'+appFormatMoney((cuoMora),2)+'</td>'+
            '<td style="text-align:right;">'+appFormatMoney((cuoOtros),2)+'</td>'+
            '<td style="text-align:right;">'+appFormatMoney(valor.saldo,2)+'</td>'+
            '<td style="text-align:center;">'+((valor.numero==0)?(0):((valor.atraso<0)?(0):(valor.atraso)))+'</td>'+
            '<td></td></tr>';
    fecha = valor.fecha;
  });
  
  if(viewTotalPagado){ //totales GRAY
    fila += '<tr style="color:#bbb;">'+
            '<td colspan="3" style="text-align:center;"><b>Total Pagado</b></td>'+
            '<td style="text-align:right;"><b>'+appFormatMoney(totGrayTotal,2)+'</b></td>'+
            '<td style="text-align:right;"><b>'+appFormatMoney(totGrayCapital,2)+'</b></td>'+
            '<td style="text-align:right;"><b>'+appFormatMoney(totGrayInteres,2)+'</b></td>'+
            '<td style="text-align:right;"><b>'+appFormatMoney(totGrayMora,2)+'</b></td>'+
            '<td style="text-align:right;"><b>'+appFormatMoney(totGrayOtros,2)+'</b></td>'+
            '<td colspan="3"></td>'+
            '</tr>';
  }
  //totales RED
  fila += '<tr style="color:red;">'+
          '<td colspan="3" style="text-align:center;"><b>Total Vencido</b></td>'+
          '<td style="text-align:right;"><b>'+appFormatMoney(totRedTotal,2)+'</b></td>'+
          '<td style="text-align:right;"><b>'+appFormatMoney(totRedCapital,2)+'</b></td>'+
          '<td style="text-align:right;"><b>'+appFormatMoney(totRedInteres,2)+'</b></td>'+
          '<td style="text-align:right;"><b>'+appFormatMoney(totRedMora,2)+'</b></td>'+
          '<td style="text-align:right;"><b>'+appFormatMoney(totRedOtros,2)+'</b></td>'+
          '<td colspan="3"></td>'+
          '</tr>';
  if(viewTotalPorVencer){ //totales BLACK
    fila += '<tr id="trTotalPorVencer">'+
            '<td colspan="3" style="text-align:center;"><b>Total por Vencer</b></td>'+
            '<td style="text-align:right;"><b>'+appFormatMoney(totBlackTotal,2)+'</b></td>'+
            '<td style="text-align:right;"><b>'+appFormatMoney(totBlackCapital,2)+'</b></td>'+
            '<td style="text-align:right;"><b>'+appFormatMoney(totBlackInteres,2)+'</b></td>'+
            '<td style="text-align:right;"><b>'+appFormatMoney(totBlackMora,2)+'</b></td>'+
            '<td style="text-align:right;"><b>'+appFormatMoney(totBlackOtros,2)+'</b></td>'+
            '<td colspan="3"></td>'+
            '</tr>';
  }
  
  $('#grdDetalleDatos').html(fila);
}

function appCreditosViewTotalPagado(){
  document.querySelector("#iconTotalPagado").innerHTML = (viewTotalPagado==true)?('<i class="fa fa-toggle-off"></i>'):('<i class="fa fa-toggle-on"></i>');
  viewTotalPagado = !viewTotalPagado;
  appCreditosRefresh();
}

function appCreditosViewTotalPorVencer(){
  document.querySelector("#iconTotalPorVencer").innerHTML = (viewTotalPorVencer==true)?('<i class="fa fa-toggle-off"></i>'):('<i class="fa fa-toggle-on"></i>');
  viewTotalPorVencer = !viewTotalPorVencer;
  appCreditosRefresh();
}