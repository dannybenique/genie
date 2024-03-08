const rutaSQL = "pages/caja/aportes/sql.php";
const cIngreso = 1;
const cRetiro = 0;
var menu = "";
var agenciaID = null;
var tipoOperAporte = null;
var aporte = null;

//=========================funciones SISTEMA============================
async function appPagosReset(){
  document.querySelector('#lbl_aporteSocio').innerHTML = ("");
  document.querySelector('#lbl_aporteTipoDUI').innerHTML = ("DUI");
  document.querySelector('#lbl_aporteNroDUI').innerHTML = ("");
  document.querySelector('#lbl_aporteSaldo').innerHTML = ("");

  document.querySelector('#txt_aporteFecha').value = ("");
  document.querySelector('#txt_aporteImporte').value = ("");
  document.querySelector('#cbo_aporteMedioPago').innerHTML = ("");
  document.querySelector('#cbo_aporteMonedas').innerHTML = ("");
  try{
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    
    //respuesta
    menu = JSON.parse(resp.menu);
    agenciaID = resp.agenciaID;
    tipoOperAporte = null;
    aporte = {
      id : null,
      saldo : null,
      socioID : null,
      productoID : null,
      esObligatorio : null
    }

    document.querySelector("#btn_NEW").style.display = (menu.caja.submenu.aportes.cmdInsert==1)?('inline'):('none');
    document.querySelector("#btn_RET").style.display = (menu.caja.submenu.aportes.cmdInsert==1)?('inline'):('none');
    document.querySelector("#btn_EXEC").disabled = true;
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appAportesBotonIngreso(){
  tipoOperAporte = cIngreso;
  appPreparaModalPersonas();
}

function appAportesBotonRetiro(){
  tipoOperAporte = cRetiro;
  appPreparaModalPersonas();
}

async function appAportesBotonExec(){
  let importe = appConvertToNumero(document.querySelector("#txt_aporteImporte").value);
  if(isNaN(importe)){
    alert("el IMPORTE debe ser una cantidad valida");
  } else {
    if(importe<=0){
      alert("el IMPORTE debe ser mayor a cero 0.00");
    } else {
      if(tipoOperAporte==cRetiro && importe>aporte.saldo){ //controlamos el saldo mayor a cero
        alert("!!!NO se puede retirar un monto mayor al SALDO!!!");
      } else {
        if(tipoOperAporte==cRetiro && aporte.esObligatorio==1 && importe==aporte.saldo){ //controlamos si es obligatorio que tenga saldo
          alert("!!!El saldo NO puede quedar en CERO 0.00!!!");
        } else {
          if(confirm("¿Esta seguro de continuar con la operacion?")){
            try{
              const resp = await appAsynFetch({
                TipoQuery : 'insOperacion',
                agenciaID : agenciaID,
                saldoID : aporte.id,
                socioID : aporte.socioID,
                productoID : aporte.productoID,
                medioPagoID : document.querySelector("#cbo_aporteMedioPago").value*1,
                monedaID : document.querySelector("#cbo_aporteMonedas").value*1,
                tipoOperAporte : tipoOperAporte,
                importe : importe
              }, rutaSQL);
              
              //respuesta
              if (!resp.error) { 
                if(confirm("¿Desea Imprimir el pago?")){
                  $("#modalPrint").modal("show");
                  let urlServer = appUrlServer()+"pages/caja/aportes/rpt.voucher.php?movimID="+resp.movimID;
                  $("#contenedorFrame").html('<object id="objPDF" type="text/html" data="'+urlServer+'" width="100%" height="500px"></object>');
                }
                appPagosReset();
              }
            }catch(err){
              console.error('Error al cargar datos:', err);
            }
          }
        }
      }
    }
  }
}

function modalAporte_keyBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { modalAporteBuscar(); }
}

function modalAporteBuscar(){
  document.querySelector("#modalAporte_Grid").style.display = 'none';
  if(document.querySelector("#modalAporte_TxtBuscar").value.length>=3){ 
    modalAporteGrid();
  } else { 
    document.querySelector('#modalAporte_Wait').innerHTML = ('<div class="callout callout-warning"><h4>Demasiado Corto</h4><p>El NRO de documento de Identidad debe tener como minimo <b>4 numeros</b></p></div>'); 
  }
}

async function modalAporteGrid(){
  //loader
  document.querySelector('#modalAporte_Wait').innerHTML = ('<div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></div>');

  try{
    const txtBuscar = document.querySelector("#modalAporte_TxtBuscar").value;
    const resp = await appAsynFetch({ TipoQuery: 'selAportes', buscar:txtBuscar },rutaSQL);

    //respuesta
    document.querySelector('#modalAporte_Wait').innerHTML = "";
    document.querySelector("#modalAporte_Grid").style.display = 'block';
    if(resp.aportes.length>0){
      let fila = "";
      resp.aportes.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td>'+(valor.nro_DUI)+'</td>'+
                '<td>'+(valor.socio)+'</td>'+
                '<td><a href="javascript:appAportesOperView('+(valor.ID)+');">'+(valor.codigo+' &raquo; '+valor.producto+'; '+valor.mon_abrevia+';')+'</a></td>'+
                '<td style="text-align:right;">'+(appFormatMoney(valor.saldo,2))+'</td>'+
                '</tr>';
      });
      document.querySelector('#modalAporte_GridBody').innerHTML = (fila);
    }else{
      document.querySelector('#modalAporte_GridBody').innerHTML = ('<tr><td colspan="5" style="text-align:center;color:red;">Sin Resultados para '+txtBuscar+'</td></tr>');
    }
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appAportesOperView(saldoID){
  $('#modalAporte').modal('hide');
  try{
    const resp = await appAsynFetch({
      TipoQuery : 'viewAporte',
      saldoID : saldoID
    }, rutaSQL);
    
    //respuesta
    aporte.id = (saldoID);
    aporte.saldo = (resp.aporte.saldo);
    aporte.socioID = (resp.aporte.socioID);
    aporte.productoID = (resp.aporte.productoID);
    aporte.esObligatorio = (resp.aporte.obliga);

    document.querySelector('#lbl_aporteSocio').innerHTML = (resp.aporte.socio);
    document.querySelector('#lbl_aporteTipoDUI').innerHTML = (resp.aporte.DUI);
    document.querySelector('#lbl_aporteNroDUI').innerHTML = (resp.aporte.nro_dui);
    document.querySelector('#lbl_aporteSaldo').innerHTML = appFormatMoney(resp.aporte.saldo,2);
    appLlenarDataEnComboBox(resp.comboTipoPago,"#cbo_aporteMedioPago",0); //medios de pago
    appLlenarDataEnComboBox(resp.comboMonedas,"#cbo_aporteMonedas",0); //monedas
    document.querySelector('#txt_aporteFecha').value = (moment(resp.fecha).format("DD/MM/YYYY"));
    document.querySelector("#btn_EXEC").disabled = false;
    document.querySelector("#btn_EXEC").innerHTML = (tipoOperAporte==cIngreso) ? '<i class="fa fa-plus"></i> Aplicar Ingreso' : '<i class="fa fa-minus"></i> Aplicar Retiro';
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appPreparaModalPersonas(){
  document.querySelector("#modalAporte_Titulo").innerHTML = ("Verificar Aportes por Doc. Identidad");
  document.querySelector("#modalAporte_Grid").style.display = 'none';
  document.querySelector("#modalAporte_Wait").innerHTML = ("");
  document.querySelector("#modalAporte_TxtBuscar").value = ("");
  $('#modalAporte').modal({keyboard:true});
  $('#modalAporte').on('shown.bs.modal', ()=> { document.querySelector("#modalAporte_TxtBuscar").focus(); });
}