const rutaSQL = "pages/caja/extornos/sql.php";
var menu = "";
var agenciaID = 0;

//=========================funciones para Personas============================
function appPagosReset(){
  appFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php").then(resp => {
    menu = JSON.parse(resp.menu);
    agenciaID = resp.agenciaID;
    document.querySelector("#btn_NEW").style.display = (menu.caja.submenu.pagos.cmdInsert==1)?('inline'):('none');
    document.querySelector("#btn_PAGAR").disabled = true;
    document.querySelector("#lbl_crediAtraso").style.color = "#777";
    document.querySelector('#lbl_crediAtraso').innerHTML = ("");

    document.querySelector('#hid_crediID').value = ("");
    document.querySelector('#hid_crediProductoID').value = ("");
    document.querySelector('#hid_crediTasaMora').value = ("");
    document.querySelector('#hid_crediSocioID').value = ("");
    document.querySelector('#lbl_crediSocio').innerHTML = ("");
    document.querySelector('#lbl_crediTipoDUI').innerHTML = ("DUI");
    document.querySelector('#lbl_crediNroDUI').innerHTML = ("");
    document.querySelector('#lbl_crediFecha').innerHTML = ("");
    document.querySelector('#lbl_crediMoneda').innerHTML = ("");
    document.querySelector('#lbl_crediProducto').innerHTML = ("");
    document.querySelector('#lbl_crediCodigo').innerHTML = ("");
    document.querySelector('#lbl_crediTasaCred').innerHTML = ("%");
    document.querySelector('#lbl_crediTasaMora').innerHTML = ("%");
    document.querySelector('#lbl_crediAgencia').innerHTML = ("");
    document.querySelector('#lbl_crediPromotor').innerHTML = ("");
    document.querySelector('#lbl_crediAnalista').innerHTML = ("");
    document.querySelector('#lbl_crediImporte').innerHTML = ("");
    document.querySelector('#lbl_crediSaldo').innerHTML = ("");

    document.querySelector('#txt_DeudaCapital').value = ("");
    document.querySelector('#txt_DeudaInteres').value = ("");
    document.querySelector('#txt_DeudaMora').value = ("");
    document.querySelector('#txt_DeudaOtros').value = ("");
    document.querySelector('#txt_DeudaFecha').value = ("");
    document.querySelector('#txt_DeudaTotalNeto').value = ("");
    document.querySelector('#txt_DeudaImporte').value = ("");
    document.querySelector('#cbo_DeudaMedioPago').innerHTML = ("");
    document.querySelector('#cbo_DeudaMonedas').innerHTML = ("");
  });
}

function appPagosBotonNuevo(){
  document.querySelector("#modalCredi_Titulo").innerHTML = ("Verificar Creditos por Doc. Identidad");
  document.querySelector("#modalCredi_Grid").style.display = 'none';
  document.querySelector("#modalCredi_Wait").innerHTML = ("");
  document.querySelector("#modalCredi_Buscar").value = ("");
  $('#modalCredi').modal({keyboard:true});
  $('#modalCredi').on('shown.bs.modal', ()=> { document.querySelector("#modalCredi_Buscar").focus(); });
}

function appPagosBotonPagar(){
  let importe = appConvertToNumero(document.querySelector("#txt_DeudaImporte").value);
  if(!isNaN(importe)){
    if(importe>0){
      if(confirm("¿Esta seguro de continuar con el PAGO?")){
        let datos = {
          TipoQuery : 'insPago',
          agenciaID : agenciaID*1,
          codprod : document.querySelector("#lbl_crediCodigo").innerHTML,
          prestamoID : document.querySelector("#hid_crediID").value*1,
          medioPagoID : document.querySelector("#cbo_DeudaMedioPago").value*1,
          productoID : document.querySelector("#hid_crediProductoID").value*1,
          tasaMora : document.querySelector('#hid_crediTasaMora').value*1,
          socioID : document.querySelector("#hid_crediSocioID").value*1,
          monedaID : document.querySelector("#cbo_DeudaMonedas").value*1,
          importe : importe*1
        };
        console.log(datos);
        appFetch(datos,rutaSQL).then(resp => {
          if (!resp.error) { 
            if(confirm("¿Desea Imprimir el desembolso?")){
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
    }
  } else {
    alert("el IMPORTE debe ser una cantidad valida");
  }
}

function modalCredi_keyBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) {
    document.querySelector("#modalCredi_Grid").style.display = 'none';
    if(document.querySelector("#"+e.srcElement.id).value.length>=3){ 
      modalCrediGrid();
    } else { 
      document.querySelector('#modalCredi_Wait').innerHTML = ('<div class="callout callout-warning"><h4>Demasiado Corto</h4><p>El NRO de documento de Identidad debe tener como minimo <b>4 numeros</b></p></div>'); 
    }
  }
}

function modalCrediGrid(){
  document.querySelector('#modalCredi_Wait').innerHTML = ('<div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></div>');
  let txtBuscar = document.querySelector("#modalCredi_Buscar").value;
  let datos = { TipoQuery: 'selCreditos', buscar:txtBuscar };

  appFetch(datos,rutaSQL).then(resp => {
    document.querySelector('#modalCredi_Wait').innerHTML = "";
    document.querySelector("#modalCredi_Grid").style.display = 'block';
    if(resp.prestamos.length>0){
      let fila = "";
      resp.prestamos.forEach((valor,key)=>{
        fila += '<tr>';
        fila += '<td>'+(valor.nro_DUI)+'</td>';
        fila += '<td>'+(valor.socio)+'</td>';
        fila += '<td><a href="javascript:appCreditoPagoView('+(valor.ID)+');">'+(valor.codigo+' &raquo; '+valor.producto+'; '+valor.mon_abrevia+'; '+appFormatMoney(valor.tasa,2)+'%')+'</a></td>';
        fila += '<td style="text-align:right;">'+(appFormatMoney(valor.importe,2))+'</td>';
        fila += '<td style="text-align:right;">'+(appFormatMoney(valor.saldo,2))+'</td>';
        fila += '</tr>';
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
    //console.log(resp);
    appCredi_Cabecera_SetData(resp.cabecera);
    appCredi_Detalle_SetData(resp.detalle);
    appLlenarDataEnComboBox(resp.comboTipoPago,"#cbo_DeudaMedioPago",0); //medios de pago
    appLlenarDataEnComboBox(resp.comboMonedas,"#cbo_DeudaMonedas",0); //monedas
    document.querySelector('#txt_DeudaFecha').value = (moment(resp.fecha).format("DD/MM/YYYY"));
    document.querySelector("#btn_PAGAR").disabled = false;
  });
}

function appCredi_Cabecera_SetData(data){
  document.querySelector("#lbl_crediAtraso").style.color = (data.atraso>0)?("#D00"):("#777");
  document.querySelector('#lbl_crediAtraso').innerHTML = (data.atraso);

  document.querySelector('#hid_crediID').value = (data.ID);
  document.querySelector('#hid_crediSocioID').value = (data.socioID);
  document.querySelector('#hid_crediProductoID').value = (data.productoID);
  document.querySelector('#hid_crediTasaMora').value = (data.mora),
  document.querySelector('#lbl_crediSocio').innerHTML = (data.socio);
  document.querySelector('#lbl_crediTipoDUI').innerHTML = (data.dui);
  document.querySelector('#lbl_crediNroDUI').innerHTML = (data.nro_dui);
  document.querySelector('#lbl_crediFecha').innerHTML = (moment(data.fecha_otorga).format("DD/MM/YYYY"));
  document.querySelector('#lbl_crediMoneda').innerHTML = (data.moneda+' <span style="font-size:10px;">('+data.mon_abrevia+')</span>');
  document.querySelector('#lbl_crediProducto').innerHTML = (data.producto);
  document.querySelector('#lbl_crediCodigo').innerHTML = (data.codigo);
  document.querySelector('#lbl_crediTasaCred').innerHTML = (appFormatMoney(data.tasa,2)+'% <span style="font-size:10px;">(TEA)</span>');
  document.querySelector('#lbl_crediTasaMora').innerHTML = (appFormatMoney(data.mora,2)+'% <span style="font-size:10px;">(TEA)</span>');
  document.querySelector('#lbl_crediAgencia').innerHTML = (data.agencia);
  document.querySelector('#lbl_crediPromotor').innerHTML = (data.promotor);
  document.querySelector('#lbl_crediAnalista').innerHTML = (data.analista);
  document.querySelector('#lbl_crediImporte').innerHTML = (appFormatMoney(data.importe,2));
  document.querySelector('#lbl_crediSaldo').innerHTML = (appFormatMoney(data.saldo,2));
}

function appCredi_Detalle_SetData(data){
  let total = data.capital+data.interes+data.mora+data.otros;
  document.querySelector('#txt_DeudaCapital').value = (appFormatMoney(data.capital,2));
  document.querySelector('#txt_DeudaInteres').value = (appFormatMoney(data.interes,2));
  document.querySelector('#txt_DeudaMora').value = (appFormatMoney(data.mora,2));
  document.querySelector('#txt_DeudaOtros').value = (appFormatMoney(data.otros,2));
  document.querySelector('#txt_DeudaTotalNeto').value = (appFormatMoney(total,2));
  document.querySelector('#txt_DeudaImporte').value = (appFormatMoney(total,2));
}
