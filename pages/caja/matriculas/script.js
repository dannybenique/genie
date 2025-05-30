const rutaSQL = "pages/caja/matriculas/sql.php";
var menu = "";
var objPagos = null;
var objAddPagos = null;
var objTotales = null;
var objMatricula = null;

//=========================funciones para Personas============================
async function appDesembGrid(){
  try {
    document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="9"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
    const txtBuscar = document.querySelector("#txtBuscar").value;
    const datos = {
      TipoQuery: 'desemb_Select',
      buscar: txtBuscar
    };
    const resp = await appAsynFetch(datos,rutaSQL);
    const disabledDelete = (menu.caja.submenu.matriculas.cmdDelete===1) ? (""):("disabled");
    document.querySelector("#chk_All").disabled = (menu.caja.submenu.matriculas.cmdDelete===1) ? false : true;
    if(resp.tabla.length>0){
      let fila = "";
      resp.tabla.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td><input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'" '+disabledDelete+'/></td>'+
                '<td style="text-align:center;"><a href="javascript:appDesembView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.codigo)+'</a></td>'+
                '<td>'+(moment(valor.fecha_solicita).format("DD/MM/YYYY"))+'</td>'+
                '<td>'+(moment(valor.fecha_aprueba).format("DD/MM/YYYY"))+'</td>'+
                '<td>'+(valor.nro_dui)+'</td>'+
                '<td>'+(valor.alumno)+'</td>'+
                '<td style="text-align:center;">'+(valor.yyyy)+'</td>'+
                '<td>'+(valor.nivel)+' &raquo; '+(valor.grado)+' &raquo; '+(valor.seccion)+'</td>'+
                '</tr>';
      });
      document.querySelector('#grdDatos').innerHTML =fila;
    } else {
      document.querySelector('#grdDatos').innerHTML = '<tr><td colspan="9" style="text-align:center;color:red;">Sin Resultados '+((txtBuscar==="")?(""):("para "+txtBuscar))+'</td></tr>';
    }
    document.querySelector('#grdCount').innerHTML = resp.tabla.length+"/"+resp.cuenta;
  } catch (err) {
    console.error('Error al cargar datos:', err);
  }
}

async function appDesembReset(){
  objMatricula = null;
  objPagos = null;
  objTotales = { PagosActual:0, ImporteMatricula:0 }
  document.querySelector("#txtBuscar").value = ("");
  document.querySelector("#grdDatos").innerHTML = ("");
  
  try{
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
  
    menu = JSON.parse(resp.menu);
    document.querySelector("#btn_DEL").style.display = (menu.caja.submenu.matriculas.cmdDelete==1)?('inline'):('none');
    document.querySelector("#div_PersAuditoria").style.display = ((resp.rolID==101)?('block'):('none'));
    appDesembGrid();
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appDesembBuscar(e){
  if(e.keyCode === 13) { load_flag = 0; $('#grdDatos').html(""); appDesembGrid(); }
}

function appDesembBotonCancel(){
  appDesembGrid();
  $('#grid').show();
  $('#edit').hide();
}

function appDesembBotonDesembolsar(){
  if(objTotales.PagosActual>0){
    if(confirm("El importe total a pagar en esta matricula sera de "+appFormatMoney(objTotales.PagosActual,2)+" ¿Desea continuar?")){
      fn_EjecutarDesembolso();
    }
  } else {
    if(confirm("El importe total a pagar es CERO 0.00 ¿Desea continuar?")){
      fn_EjecutarDesembolso();
    }
  }
}

async function appDesembBotonAgregarPagos(){
  try{
    const resp = await appAsynFetch({ TipoQuery : "desemb_AddPago" },rutaSQL);
    
    //respuesta
    let fila = "";
    let filterID = objPagos.map(obj => obj.productoID);
    objAddPagos = resp.tablaPagos.filter(obj => !filterID.includes(obj.productoID));
    objAddPagos.forEach((valor,key)=>{
      fila += '<tr>'+
              '<td><input type="checkbox" name="chk_modaladdpagos" value="'+(valor.productoID)+'"/></td>'+
              '<td>'+(valor.abrevia)+'</td>'+
              '<td>'+(valor.producto)+'</td>'+
              '<td>'+moment(valor.vencimiento).format("DD/MM/YYYY")+'</td>'+
              '<td style="text-align:right;">'+appFormatMoney(valor.importe,2)+'</td>'+
              '</tr>';
    });
    document.querySelector('#grdaddpagosDatos').innerHTML = fila;
    $("#modalAddPagos").modal("show");
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function fn_EjecutarDesembolso(){
  try{
    const datos = {
      TipoQuery : 'desemb_Execute',
      matriculaID : objMatricula.matriculaID,
      observac : objMatricula.observac,
      pagos : objPagos,
      total : objTotales.PagosActual,
      saldo : objTotales.ImporteMatricula - objTotales.PagosActual,
      importe : objTotales.ImporteMatricula,
      fecha : appConvertToFecha(document.querySelector("#txt_DesembFecha").value)
    }
    const resp = await appAsynFetch(datos,rutaSQL);

    //respuesta
    if (!resp.error) { 
      if(confirm("¿Desea Imprimir la Matricula?")){
        $("#modalPrint").modal("show");
        let urlServer = appUrlServer()+"pages/caja/matriculas/rpt.voucher.php?movimID="+resp.movimID;
        $("#contenedorFrame").html('<object id="objPDF" type="text/html" data="'+urlServer+'" width="100%" height="500px"></object>');
      }
      appDesembBotonCancel(); 
    }
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appDesembBotonBorrar(){
  let arr = Array.from(document.querySelectorAll('[name="chk_Borrar"]:checked')).map(function(obj){return obj.attributes[2].nodeValue});
  if(arr.length>0){
    if(confirm("¿Esta seguro de continuar?")) {
      try{
        const resp = await appAsynFetch({ TipoQuery:'desemb_Delete', arr:arr },rutaSQL);
        if (!resp.error) { appDesembGrid(); }
      } catch(err){
        console.error('Error al cargar datos:', err);
      }
    }
  } else {
    alert("NO eligio borrar ninguno");
  }
}

async function appDesembView(matriculaID){
  //tabs default en primer tab
  $('.nav-tabs li').removeClass('active');
  $('.tab-content .tab-pane').removeClass('active');
  $('a[href="#datosMatricula"]').closest('li').addClass('active');
  $('#datosMatricula').addClass('active');

  try{
    const resp = await appAsynFetch({
      TipoQuery : 'desemb_View',
      matriculaID : matriculaID
    },rutaSQL);
      
    document.querySelector("#btnInsert").style.display = (menu.caja.submenu.matriculas.cmdUpdate==1)?('inline'):('none');
    document.querySelector('#grid').style.display = 'none';
    document.querySelector('#edit').style.display = 'block';

    objPagos = resp.tablaPagos;
    appDesembSetData(resp.tablaDesembolso);  //pestaña matricula
    appPagosSetData(objPagos);
    appPersonaSetData(resp.tablaPers);
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appDesembSetData(data){
  //pestaña de desembolso
  objMatricula = {
    matriculaID : data.ID,
    alumnoID : data.alumnoID,
    observac : data.observac
  }

  //info corta
  document.querySelector('#lbl_DesembAlumno').innerHTML = (data.alumno);
  document.querySelector('#lbl_DesembAlumnoDNI').innerHTML = (data.nro_dui);
  document.querySelector("#lbl_DesembCodigo").innerHTML = (data.codigo);
  document.querySelector("#lbl_DesembCodigo").title = (data.ID);
  document.querySelector("#lbl_DesembYYYY").innerHTML = (data.yyyy);
  document.querySelector("#lbl_DesembNivel").innerHTML = (data.nivel);
  document.querySelector("#lbl_DesembGrado").innerHTML = (data.grado);
  document.querySelector("#lbl_DesembSeccion").innerHTML = (data.seccion);
  document.querySelector("#lbl_DesembPagoTotal").innerHTML = "0.00";

  //pestaña matricula
  document.querySelector("#txt_DesembFecha").disabled = (data.rolUser==data.rolROOT) ? (false):(true);
  $('#txt_DesembFecha').datepicker("setDate",moment(data.fecha_desemb).format("DD/MM/YYYY"));

  document.querySelector('#lbl_DesembFechaSolicita').innerHTML = (moment(data.fecha_solicita).format("DD/MM/YYYY") + " &raquo; " + data.user_solicita);
  document.querySelector('#lbl_DesembFechaAprueba').innerHTML = (moment(data.fecha_aprueba).format("DD/MM/YYYY") + " &raquo; " + data.user_aprueba);
  document.querySelector("#lbl_DesembObservac").innerHTML = (data.observac);
}

function appPagosSetData(data){
  objTotales = { PagosActual:0, ImporteMatricula:0 }
  if(data.length>0){
    let fila = "";
    data.forEach((valor,key)=>{
      objTotales.ImporteMatricula += valor.importe;
      objTotales.PagosActual += (valor.checked==1) ? (valor.importe):(0);
      fila += '<tr style="'+((valor.checked) ? ("color;black;"):("color:#aaa;"))+'">'+
              '<td style="color:#999;text-align:center;font-size:11px;">'+(key+1)+'</td>'+
              '<td><a href="javascript:fnPagosDeleteItem('+(valor.productoID)+')"><i style="color:red;" class="fa fa-trash"></i></a></td>'+
              '<td><input type="checkbox" name="chk_BorrarPagos" value="'+(valor.productoID)+'" '+((valor.checked) ? ("checked "):(""))+((valor.disabled) ? ("disabled "):(""))+' onclick="javascript:fnPagosCheck(this);"/></td>'+
              '<td>'+(valor.abrevia)+'</td>'+
              '<td>'+(valor.producto)+'</td>'+
              '<td>'+moment(valor.vencimiento).format("DD/MM/YYYY")+'</td>'+
              '<td style="text-align:right;">'+appFormatMoney(valor.importe,2)+'</td>'+
              '<td style="padding:0;line-height:30px;">'+((valor.bloqueo) ? ('<i style="font-size:9px;" class="fa fa-lock"></i>'):(''))+'</td>'+
              '</tr>';
    });
    fila += '<tfoot><tr>'+
            '<td colspan="5" style="text-align:center;"><b>TOTAL A PAGAR EN MATRICULA</b></td>'+
            '<td colspan="2" style="text-align:right;border-bottom-style:double;"><span id="lbl_DesembTotal">'+appFormatMoney(objTotales.PagosActual,2)+'</span></td>'+
            '<td></td>'+
            '</tr></tfoot>';
    document.querySelector('#grdPagos').innerHTML = fila;
    document.querySelector('#lbl_DesembPagoTotal').innerHTML = appFormatMoney(objTotales.PagosActual,2);
  } else {
    document.querySelector('#grdPagos').innerHTML = '<tr><td colspan="6" style="text-align:center;color:red;">Sin Resultados</td></tr>';
  }
}

function appPersonaSetData(data){
  document.querySelector("#lbl_PersTipoNombres").innerHTML = ("Nombres");
  document.querySelector("#lbl_PersTipoProfesion").innerHTML = ("Profesion");
  document.querySelector("#lbl_PersTipoApellidos").style.display = 'block';
  document.querySelector("#lbl_PersTipoSexo").style.display = 'block';
  document.querySelector("#lbl_PersTipoECivil").style.display = 'block';
  document.querySelector("#lbl_PersTipoGIntruc").style.display = 'block';
  document.querySelector("#hid_PersID").value = (data.ID);
  document.querySelector("#lbl_PersNombres").innerHTML = (data.nombres);
  document.querySelector("#lbl_PersApellidos").innerHTML = (data.ap_paterno+" "+data.ap_materno);
  document.querySelector("#lbl_PersTipoDNI").innerHTML = (data.tipoDUI);
  document.querySelector("#lbl_PersNroDNI").innerHTML = (data.nroDUI);
  document.querySelector("#lbl_PersFechaNac").innerHTML = (moment(data.fechanac).format("DD/MM/YYYY"));
  document.querySelector("#lbl_PersEdad").innerHTML = (moment().diff(moment(data.fechanac),"years")+" años");
  document.querySelector("#lbl_PersPaisNac").innerHTML = (data.paisnac);
  document.querySelector("#lbl_PersLugarNac").innerHTML = (data.lugarnac);
  document.querySelector("#lbl_PersSexo").innerHTML = (data.sexo);
  document.querySelector("#lbl_PersEcivil").innerHTML = (data.ecivil);
  document.querySelector("#lbl_PersCelular").innerHTML = (data.celular);
  document.querySelector("#lbl_PersTelefijo").innerHTML = (data.telefijo);
  document.querySelector("#lbl_PersEmail").innerHTML = (data.correo);
  document.querySelector("#lbl_PersGInstruccion").innerHTML = (data.ginstruc);
  document.querySelector("#lbl_PersProfesion").innerHTML = (data.profesion);
  document.querySelector("#lbl_PersOcupacion").innerHTML = (data.ocupacion);
  document.querySelector("#lbl_PersUbicacion").innerHTML = (data.region+" - "+data.provincia+" - "+data.distrito);
  document.querySelector("#lbl_PersDireccion").innerHTML = (data.direccion);
  document.querySelector("#lbl_PersReferencia").innerHTML = (data.referencia);
  document.querySelector("#lbl_PersMedidorluz").innerHTML = (data.medidorluz);
  document.querySelector("#lbl_PersMedidorAgua").innerHTML = (data.medidoragua);
  document.querySelector("#lbl_PersTipovivienda").innerHTML = (data.tipovivienda);
  document.querySelector("#lbl_PersObservac").innerHTML = (data.observPers);
  document.querySelector("#lbl_PersSysFecha").innerHTML = (moment(data.sysfechaPers).format("DD/MM/YYYY HH:mm:ss"));
  document.querySelector("#lbl_PersSysUser").innerHTML = (data.sysuserPers);
}

function fnPagosCheck(e){
  let idx = objPagos.findIndex(elemento => elemento.productoID === Number(e.value));
  
  // if(e.checked){ objTotales.PagosActual += objPagos[idx]["importe"]; } 
  // else { objTotales.PagosActual -= objPagos[idx]["importe"]; }
  objPagos[idx]["checked"] = (e.checked);
  appPagosSetData(objPagos);
  // document.querySelector('#lbl_DesembPagoTotal').innerHTML = appFormatMoney(objTotales.PagosActual,2);
  // document.querySelector('#lbl_DesembTotal').innerHTML = appFormatMoney(objTotales.PagosActual,2);
}

function fnPagosDeleteItem(productoID){
  let idx = objPagos.findIndex(elemento => elemento.productoID === productoID); 
  if(confirm("¿Desea eliminar "+objPagos[idx]["producto"]+" de la lista de pagos?")){
    objPagos.splice(idx,1);
    appPagosSetData(objPagos);
  }
}

function modaddpagos_BotonAgregar(){
  let arrSelect = Array.from(document.querySelectorAll('[name="chk_modaladdpagos"]:checked')).map(obj => parseInt(obj.value,10));
  let arrFilter = objAddPagos.filter(elem => arrSelect.includes(elem.productoID));
  let arrTemp = [...objPagos,...arrFilter];

  arrTemp.sort((a,b )=> new Date(a.orden)- new Date(b.orden));
  objPagos = arrTemp;
  appPagosSetData(objPagos);
  objAddPagos = null;
  $("#modalAddPagos").modal("hide");
}