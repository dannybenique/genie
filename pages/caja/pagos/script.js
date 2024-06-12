const rutaSQL = "pages/caja/pagos/sql.php";
var menu = null;
var pago = null;
var matriculas = null;

//=========================funciones para Personas============================
async function appPagosReset(){
  $(".form-group").removeClass("has-error");
  document.querySelector("#lbl_matriAtraso").style.color = "#777";
  document.querySelector('#lbl_matriAtraso').innerHTML = ("");
  document.querySelector('#lbl_matriAlumno').innerHTML = ("");
  document.querySelector('#lbl_matriNroDUI').innerHTML = ("");
  document.querySelector('#lbl_matriFecha').innerHTML = ("");
  document.querySelector('#lbl_matriCodigo').innerHTML = ("");
  document.querySelector('#lbl_matriYYYY').innerHTML = ("");
  document.querySelector('#lbl_matriNivel').innerHTML = ("");
  document.querySelector('#lbl_matriGrado').innerHTML = ("");
  document.querySelector('#lbl_matriSeccion').innerHTML = ("");
  document.querySelector('#lbl_matriSaldo').innerHTML = ("");

  document.querySelector('#txt_DeudaFecha').value = ("");
  document.querySelector('#txt_DeudaTotalNeto').value = ("");
  document.querySelector('#txt_DeudaImporte').value = ("");
  document.querySelector('#cbo_DeudaMedioPago').innerHTML = ("");
  document.querySelector("#btn_PAGAR").style.display = 'none';
  document.querySelector("#btn_NEW").style.display = 'none';

  pago = null;
  matriculas = null;
  
  try{
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    
    menu = JSON.parse(resp.menu);
    if(menu.caja.submenu.pagos.cmdInsert){
      document.querySelector("#btn_NEW").style.display = 'inline';
      document.querySelector("#btn_PAGAR").style.display = 'inline';  
      document.querySelector("#btn_PAGAR").disabled = true;
    } 
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appPagosBotonNuevo(){
  document.querySelector("#modalMatric_Titulo").innerHTML = ("Verificar Matricula por Doc. Identidad");
  document.querySelector("#modalMatric_Grid").style.display = 'none';
  document.querySelector("#modalMatric_Wait").innerHTML = ("");
  document.querySelector("#modalMatric_TxtBuscar").value = ("");
  $('#modalMatric').modal({keyboard:true});
  $('#modalMatric').on('shown.bs.modal', ()=> { document.querySelector("#modalMatric_TxtBuscar").focus(); });
}

async function appPagosBotonPagar(){
  $(".form-group").removeClass("has-error");
  const importe = appConvertToNumero(document.querySelector("#txt_DeudaImporte").value);
  if(!isNaN(importe)){
    if(importe>0){
      if(confirm("¿Esta seguro de continuar con el PAGO?")){
        const datos = {
          TipoQuery : 'insPago',
          alumnoID : pago.alumnoID,
          matriculaID : pago.matriculaID,
          productoID : pago.productoID,
          medioPagoID : document.querySelector("#cbo_DeudaMedioPago").value*1,
          importe : importe
        }
        // console.log(datos);
        const resp = await appAsynFetch(datos,rutaSQL);
        if (!resp.error) { 
          if(confirm("¿Desea Imprimir el pago?")){
            $("#modalPrint").modal("show");
            let urlServer = appUrlServer()+"pages/caja/pagos/rpt.voucher.php?movimID="+resp.movimID;
            $("#contenedorFrame").html('<object id="objPDF" type="text/html" data="'+urlServer+'" width="100%" height="500px"></object>');
          }
          appPagosReset();
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
    if(document.querySelector("#modalMatric_TxtBuscar").value.trim().length>=4){ 
      modalMatricGrid();
    } else { 
      document.querySelector("#modalMatric_Grid").style.display = 'none';
      document.querySelector('#modalMatric_Wait').innerHTML = ('<div class="callout callout-warning"><h4>Demasiado Corto</h4><p>El NRO de documento de Identidad debe tener como minimo <b>4 numeros</b></p></div>'); 
    }
  }
}

async function modalMatricGrid(){
  document.querySelector('#modalMatric_Wait').innerHTML = "";
  document.querySelector("#modalMatric_Grid").style.display = 'block';
  document.querySelector('#modalMatric_GridBody').innerHTML = ('<tr><td colspan="5"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></div></td></tr>');
  const txtBuscar = document.querySelector("#modalMatric_TxtBuscar").value;
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
      document.querySelector('#modalMatric_GridBody').innerHTML = (fila);
    }else{
      document.querySelector('#modalMatric_GridBody').innerHTML = ('<tr><td colspan="5" style="text-align:center;color:red;">Sin Resultados para '+txtBuscar+'</td></tr>');
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

    appCredi_SetData(matriculas.find(matricula => matricula.ID===matriculaID));
    appLlenarDataEnComboBox(resp.comboTipoPago,"#cbo_DeudaMedioPago",0); //medios de pago
    $('#txt_DeudaFecha').datepicker("setDate",moment(resp.fecha).format("DD/MM/YYYY"));
    document.querySelector("#btn_PAGAR").style.display = 'inline';
    document.querySelector("#btn_PAGAR").disabled = false;
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appCredi_SetData(data){
  document.querySelector("#txt_DeudaFecha").disabled = (data.rolUser==data.rolROOT) ? (false):(true);
  document.querySelector("#lbl_matriAtraso").style.color = (data.atraso>0)?("#D00"):("#777");
  document.querySelector('#lbl_matriAtraso').innerHTML = (data.atraso)+" dia"+((data.atraso>1)?"s":"");
  pago = {
    alumnoID : data.alumnoID,
    matriculaID : data.ID,
    productoID : data.productoID
  }
  console.log(pago);
  document.querySelector('#lbl_matriAlumno').innerHTML = (data.alumno);
  document.querySelector('#lbl_matriNroDUI').innerHTML = (data.nro_dui);
  document.querySelector('#lbl_matriFecha').innerHTML = (moment(data.fecha_matricula).format("DD/MM/YYYY"));
  document.querySelector('#lbl_matriCodigo').innerHTML = (data.codigo);
  document.querySelector('#lbl_matriYYYY').innerHTML = (data.yyyy);
  document.querySelector('#lbl_matriNivel').innerHTML = (data.nivel);
  document.querySelector('#lbl_matriGrado').innerHTML = (data.grado);
  document.querySelector('#lbl_matriSeccion').innerHTML = (data.seccion);
  document.querySelector('#lbl_matriSaldo').innerHTML = (appFormatMoney(data.saldo,2));

  let total = (data.atraso>0) ? (data.saldo_det):(0);
  document.querySelector('#txt_DeudaTotalNeto').value = (appFormatMoney(total,2));
  document.querySelector('#txt_DeudaImporte').value = (appFormatMoney(total,2));
}
