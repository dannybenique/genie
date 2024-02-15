const rutaSQL = "pages/caja/pagos/sql.php";
var menu = "";
var pago = null;
var agenciaID = null;

//=========================funciones para Personas============================
function appPagosReset(){
  $(".form-group").removeClass("has-error");
  appFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php").then(resp => {
    pago = null;
    agenciaID = resp.agenciaID;
    menu = JSON.parse(resp.menu);
    
    document.querySelector("#btn_PAGAR").disabled = true;
    document.querySelector("#btn_NEW").style.display = (menu.caja.submenu.pagos.cmdInsert==1)?('inline'):('none');
    document.querySelector("#lbl_crediAtraso").style.color = "#777";
    
    document.querySelector('#lbl_crediAtraso').innerHTML = ("");
    document.querySelector('#lbl_crediAlumno').innerHTML = ("");
    document.querySelector('#lbl_crediTipoDUI').innerHTML = ("DUI");
    document.querySelector('#lbl_crediNroDUI').innerHTML = ("");
    document.querySelector('#lbl_crediFecha').innerHTML = ("");
    document.querySelector('#lbl_crediCodigo').innerHTML = ("");
    document.querySelector('#lbl_crediNivel').innerHTML = ("");
    document.querySelector('#lbl_crediGrado').innerHTML = ("");
    document.querySelector('#lbl_crediSeccion').innerHTML = ("");
    document.querySelector('#lbl_crediSaldo').innerHTML = ("");

    document.querySelector('#txt_DeudaCapital').value = ("");
    document.querySelector('#txt_DeudaFecha').value = ("");
    document.querySelector('#txt_DeudaTotalNeto').value = ("");
    document.querySelector('#txt_DeudaImporte').value = ("");
    document.querySelector('#cbo_DeudaMedioPago').innerHTML = ("");
  });
}

function appPagosBotonNuevo(){
  document.querySelector("#modalCredi_Titulo").innerHTML = ("Verificar Creditos por Doc. Identidad");
  document.querySelector("#modalCredi_Grid").style.display = 'none';
  document.querySelector("#modalCredi_Wait").innerHTML = ("");
  document.querySelector("#modalCredi_TxtBuscar").value = ("");
  $('#modalCredi').modal({keyboard:true});
  $('#modalCredi').on('shown.bs.modal', ()=> { document.querySelector("#modalCredi_TxtBuscar").focus(); });
}

function appPagosBotonPagar(){
  let importe = appConvertToNumero(document.querySelector("#txt_DeudaImporte").value);
  $(".form-group").removeClass("has-error");
  if(!isNaN(importe)){
    if(importe>0){
      if(confirm("¿Esta seguro de continuar con el PAGO?")){
        let datos = {
          TipoQuery : 'insPago',
          agenciaID : agenciaID*1,
          socioID : pago.socioID,
          tasaMora : pago.tasaMora,
          prestamoID : pago.prestamoID,
          productoID : pago.productoID,
          codprod : document.querySelector("#lbl_crediCodigo").innerHTML,
          medioPagoID : document.querySelector("#cbo_DeudaMedioPago").value*1,
          importe : importe*1
        };
        appFetch(datos,rutaSQL).then(resp => {
          if (!resp.error) { 
            if(confirm("¿Desea Imprimir el pago?")){
              $("#modalPrint").modal("show");
              let urlServer = appUrlServer()+"pages/caja/pagos/rpt.voucher.php?movimID="+resp.movimID;
              $("#contenedorFrame").html('<object id="objPDF" type="text/html" data="'+urlServer+'" width="100%" height="500px"></object>');
            }
            appPagosReset();
          }
        });
      }
    } else {
      alert("el IMPORTE debe ser mayor a cero 0.00");
      document.querySelector("#div_DeudaImporte").className = "form-group has-error";

    }
  } else {
    alert("el IMPORTE debe ser una cantidad valida");
    document.querySelector("#div_DeudaImporte").className = "form-group has-error";
  }
}

function modalCredi_keyBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { modalCrediBuscar(); }
}

function modalCrediBuscar(){
  document.querySelector("#modalCredi_Grid").style.display = 'none';
  if(document.querySelector("#modalCredi_TxtBuscar").value.length>=3){ 
    modalCrediGrid();
  } else { 
    document.querySelector('#modalCredi_Wait').innerHTML = ('<div class="callout callout-warning"><h4>Demasiado Corto</h4><p>El NRO de documento de Identidad debe tener como minimo <b>4 numeros</b></p></div>'); 
  }
}

function modalCrediGrid(){
  document.querySelector('#modalCredi_Wait').innerHTML = ('<div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></div>');
  let txtBuscar = document.querySelector("#modalCredi_TxtBuscar").value;
  let datos = { TipoQuery: 'selCreditos', buscar:txtBuscar };

  appFetch(datos,rutaSQL).then(resp => {
    document.querySelector('#modalCredi_Wait').innerHTML = "";
    document.querySelector("#modalCredi_Grid").style.display = 'block';
    if(resp.prestamos.length>0){
      let fila = "";
      resp.prestamos.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td>'+(valor.nro_DUI)+'</td>'+
                '<td>'+(valor.socio)+'</td>'+
                '<td><a href="javascript:appCreditoPagoView('+(valor.ID)+');">'+(valor.codigo+' &raquo; '+valor.producto+'; '+valor.mon_abrevia+'; '+appFormatMoney(valor.tasa,2)+'%')+'</a></td>'+
                '<td style="text-align:right;">'+(appFormatMoney(valor.importe,2))+'</td>'+
                '<td style="text-align:right;">'+(appFormatMoney(valor.saldo,2))+'</td>'+
                '</tr>';
      });
      document.querySelector('#modalCredi_GridBody').innerHTML = (fila);
    }else{
      document.querySelector('#modalCredi_GridBody').innerHTML = ('<tr><td colspan="5" style="text-align:center;color:red;">Sin Resultados para '+txtBuscar+'</td></tr>');
    }
  });
}

function appCreditoPagoView(prestamoID){
  $('#modalCredi').modal('hide');
  let datos = {
    TipoQuery : 'viewCredito',
    prestamoID : prestamoID
  };
  
  appFetch(datos,rutaSQL).then(resp => {
    // console.log(resp);
    appCredi_Cabecera_SetData(resp.cabecera);
    appCredi_Detalle_SetData(resp.detalle);
    appLlenarDataEnComboBox(resp.comboTipoPago,"#cbo_DeudaMedioPago",0); //medios de pago
    $('#txt_DeudaFecha').datepicker("setDate",moment(resp.fecha).format("DD/MM/YYYY"));
    document.querySelector("#btn_PAGAR").disabled = false;
  });
}

function appCredi_Cabecera_SetData(data){
  document.querySelector("#txt_DeudaFecha").disabled = (data.rolUser==data.rolROOT) ? (false):(true);
  document.querySelector("#lbl_crediAtraso").style.color = (data.atraso>0)?("#D00"):("#777");
  document.querySelector('#lbl_crediAtraso').innerHTML = (data.atraso);

  pago = {
    tasaMora : data.mora,
    socioID : data.socioID,
    prestamoID : data.prestamoID,
    productoID : data.productoID
  }
  document.querySelector('#lbl_crediAlumno').innerHTML = (data.socio);
  document.querySelector('#lbl_crediTipoDUI').innerHTML = (data.dui);
  document.querySelector('#lbl_crediNroDUI').innerHTML = (data.nro_dui);
  document.querySelector('#lbl_crediFecha').innerHTML = (moment(data.fecha_otorga).format("DD/MM/YYYY"));
  document.querySelector('#lbl_crediCodigo').innerHTML = (data.codigo);
  document.querySelector('#lbl_crediNivel').innerHTML = (data.agencia);
  document.querySelector('#lbl_crediGrado').innerHTML = (data.promotor);
  document.querySelector('#lbl_crediSeccion').innerHTML = (data.analista);
  document.querySelector('#lbl_crediSaldo').innerHTML = (appFormatMoney(data.saldo,2));
}

function appCredi_Detalle_SetData(data){
  let total = data.capital+data.interes+data.mora+data.otros;
  document.querySelector('#txt_DeudaCapital').value = (appFormatMoney(data.capital,2));
  document.querySelector('#txt_DeudaTotalNeto').value = (appFormatMoney(total,2));
  document.querySelector('#txt_DeudaImporte').value = (appFormatMoney(total,2));
}
