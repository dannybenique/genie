const rutaSQL = "pages/caja/pagos/sql.php";
var menu = null;
var pago = null;
var matriculas = null;

//=========================funciones para Personas============================
async function appPagosReset(){
  $(".form-group").removeClass("has-error");
  $("#lbl_matriAtraso").css('color',"#777");
  $('#lbl_matriAtraso, #lbl_matriAlumno, #lbl_matriNroDUI, #lbl_matriFecha, #lbl_matriCodigo, #lbl_matriYYYY, #lbl_matriNivel, #lbl_matriGrado, #lbl_matriSeccion, #lbl_matriSaldo, #cbo_DeudaMedioPago').text("");
  $('#txt_DeudaFecha, #txt_DeudaTotalNeto, #txt_DeudaImporte').val("");
  $("#btn_PAGAR, #btn_NEW").hide();

  pago = null;
  matriculas = null;
  
  try{
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    
    menu = JSON.parse(resp.menu);
    if(menu.caja.submenu.pagos.cmdInsert){
      $("#btn_NEW, #btn_PAGAR").show();
      $("#btn_PAGAR").prop("disabled", true);
    } 
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appPagosBotonNuevo(){
  $("#modalMatric_Titulo").text("Verificar Matricula por Doc. Identidad");
  $("#modalMatric_Grid").hide();
  $("#modalMatric_Wait").text("");
  $("#modalMatric_TxtBuscar").val("");
  $('#modalMatric').modal({keyboard:true}).on('shown.bs.modal', () => { $("#modalMatric_TxtBuscar").focus(); });
}

async function appPagosBotonPagar(){
  $(".form-group").removeClass("has-error");
  const importe = appConvertToNumero($("#txt_DeudaImporte").val());
  if(!isNaN(importe)){
    if(importe>0){
      if(confirm("¿Esta seguro de continuar con el PAGO?")){
        const datos = {
          TipoQuery : 'pago_ins',
          alumnoID : pago.alumnoID,
          matriculaID : pago.matriculaID,
          productoID : pago.productoID,
          medioPagoID : $("#cbo_DeudaMedioPago").val()*1,
          importe : importe
        }
        const resp = await appAsynFetch(datos,rutaSQL);

        if (!resp.error) { 
          if(confirm("¿Desea Imprimir el pago?")){
            $("#modalPrint").modal("show");
            let urlServer = appUrlServer()+"pages/caja/pagos/rpt.voucher.php?movimID="+resp.movimID;
            $("#contenedorFrame").html('<object id="objPDF" type="text/html" data="'+urlServer+'" width="100%" height="500px"></object>');
          }
          appPagosReset();
        }else{
          alert("Error al guardar el pago: "+resp.error);
          document.querySelector("#div_DeudaImporte").className = "form-group has-error";
        }
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

function modalMatric_keyBuscar(e){
  if(e.keyCode === 13) {
    if($("#modalMatric_TxtBuscar").val().trim().length>=4){ 
      modalMatricGrid();
    } else { 
      $("#modalMatric_Grid").hide();
      $('#modalMatric_Wait').html('<div class="callout callout-warning"><h4>Demasiado Corto</h4><p>El NRO de documento de Identidad debe tener como minimo <b>4 numeros</b></p></div>'); 
    }
  }
}

async function modalMatricGrid(){
  $('#modalMatric_Wait').html("");
  $("#modalMatric_Grid").show();
  $('#modalMatric_GridBody').html('<tr><td colspan="5"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></div></td></tr>');
  const txtBuscar = $("#modalMatric_TxtBuscar").val();
  try{
    const resp = await appAsynFetch({TipoQuery:'matricula_select', buscar:txtBuscar},rutaSQL);
    //respuesta
    if(resp.matriculas.length>0){
      let fila = "";
      matriculas = resp.matriculas;
      matriculas.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td>'+(valor.html_nro_DUI)+'</td>'+
                '<td>'+(valor.alumno)+'</td>'+
                '<td><a href="javascript:appCreditoPagoView('+(valor.ID)+');">'+(valor.codigo+' &raquo; '+valor.nivel+'; '+valor.grado+'; '+valor.seccion)+'</a></td>'+
                '<td style="text-align:right;">'+(appFormatMoney(valor.importe,2))+'</td>'+
                '<td style="text-align:right;">'+(appFormatMoney(valor.saldo,2))+'</td>'+
                '</tr>';
      });
      $('#modalMatric_GridBody').html(fila);
    }else{
      $('#modalMatric_GridBody').html('<tr><td colspan="5" style="text-align:center;color:red;">Sin Resultados para '+txtBuscar+'</td></tr>');
    }
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appCreditoPagoView(matriculaID){
  $('#modalMatric').modal('hide');
  
  try{
    const resp = await appAsynFetch({
      TipoQuery : 'matricula_view',
      matriculaID : matriculaID
    },rutaSQL);

    appCredi_SetData(matriculas.find(e => e.ID===matriculaID));
    appLlenarDataEnComboBox(resp.comboTipoPago,"#cbo_DeudaMedioPago",0); //medios de pago
    $('#txt_DeudaFecha').datepicker("setDate",moment(resp.fecha).format("DD/MM/YYYY"));
    $("#btn_PAGAR").show().prop("disabled", false);
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appCredi_SetData(data){
  document.querySelector("#txt_DeudaFecha").disabled = (data.rolUser==data.rolROOT) ? (false):(true);
  $("#lbl_matriAtraso").css('color',(data.atraso>0)?("#D00"):("#777")).text((data.atraso)+" dia"+((data.atraso>1)?"s":""));
  
  $('#lbl_matriAlumno').html(data.alumno);
  $('#lbl_matriNroDUI').html(data.nro_dui);
  $('#lbl_matriFecha').html(moment(data.fecha_matricula).format("DD/MM/YYYY"));
  $('#lbl_matriCodigo').html(data.codigo);
  $('#lbl_matriYYYY').html(data.yyyy);
  $('#lbl_matriNivel').html(data.nivel);
  $('#lbl_matriGrado').html(data.grado);
  $('#lbl_matriSeccion').html(data.seccion);
  $('#lbl_matriSaldo').html(appFormatMoney(data.saldo,2));
  
  pago = {
    alumnoID : data.alumnoID,
    matriculaID : data.ID,
    productoID : data.productoID
  }
  // console.log(pago);
  const total = (data.atraso>0) ? (data.saldo_det):(0);
  $('#txt_DeudaTotalNeto').val(appFormatMoney(total,2));
  $('#txt_DeudaImporte').val(appFormatMoney(total,2));
}
