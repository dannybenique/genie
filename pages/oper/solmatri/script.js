const rutaSQL = "pages/oper/solmatri/sql.php";
var menu = "";
var objModalPagos = null;
var objAddPagos = null;


async function appSolMatriGrid(){
  document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="10"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  const disabledDelete = (menu.oper.submenu.solmatri.cmdDelete===1) ? "" : "disabled";
  const txtBuscar = document.querySelector("#txtBuscar").value;
  try{
    const resp = await appAsynFetch({
      TipoQuery: 'SolicMatric_sel',
      buscar: txtBuscar 
    },rutaSQL);
  
    //respuesta  
    document.querySelector("#chk_All").disabled = (menu.oper.submenu.solmatri.cmdDelete===1) ? false : true;
    if(resp.tabla.length>0){
      let fila = "";
      resp.tabla.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td><input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'" '+disabledDelete+'/></td>'+
                '<td>'+((menu.oper.submenu.solmatri.aprueba===1)?('<a href="javascript:appSolMatriAprueba('+(valor.ID)+');"><i class="fa fa-thumbs-up" style="color:#FF0084;"></i></a>'):(''))+'</td>'+
                '<td style="text-align:center;"><a href="javascript:appSolMatriView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.codigo)+'</a></td>'+
                '<td>'+(moment(valor.fecha_solicita).format("DD/MM/YYYY"))+'</td>'+
                '<td>'+(valor.nro_dui)+'</td>'+
                '<td>'+(valor.alumno)+'</td>'+
                '<td style="text-align:center;">'+(valor.yyyy)+'</td>'+
                '<td>'+(valor.nivel)+' &raquo; '+(valor.grado)+' &raquo; '+(valor.seccion)+'</td>'+
                '</tr>';
      });
      $('#grdDatos').html(fila);
    } else {
      let res = (txtBuscar==="") ? ("") : ("para "+txtBuscar);
      $('#grdDatos').html('<tr><td colspan="10" style="text-align:center;color:red;">Sin Resultados '+(res)+'</td></tr>');
    }
    $('#grdCount').html(resp.tabla.length + "/" + resp.cuenta);
  } catch(err) {
    console.error('Error al cargar datos:', err);
  }
}

async function appSolMatriReset(){
  document.querySelector("#txtBuscar").value = ("");
  document.querySelector("#grdDatos").innerHTML = ("");
  try{
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    menu = JSON.parse(resp.menu);
    objModalPagos = null;
    document.querySelector("#btn_DEL").style.display = (menu.oper.submenu.solmatri.cmdDelete==1)?('inline'):('none');
    document.querySelector("#btn_NEW").style.display = (menu.oper.submenu.solmatri.cmdInsert==1)?('inline'):('none');
    appSolMatriGrid();
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appSolMatriBuscar(e){
  if(e.keyCode === 13) { load_flag = 0; $('#grdDatosBody').html(""); appSolMatriGrid(); }
}

function appSolMatriBotonCancel(){
  appSolMatriGrid();
  $('#grid').show();
  $('#edit').hide();
}

async function appSolMatriBotonInsert(){
  if(appSolMatriValidarCampos()){
    alert("¡¡¡Faltan datos!!!");
  } else {
    const datos = appSolMatriGetDatosToDatabase();
    datos.TipoExec = "INS";
    try{
      const resp = await appAsynFetch(datos,rutaSQL);
      if(!resp.error) { appSolMatriBotonCancel(); }
    } catch(err){
      console.error('Error al cargar datos:', err);
    }
  }
}

async function appSolMatriBotonUpdate(){
  if(appSolMatriValidarCampos()){
    alert("¡¡¡Faltan datos!!!");
  } else {
    const datos = appSolMatriGetDatosToDatabase();
    datos.TipoExec = "UPD";
    try{
      const resp = await appAsynFetch(datos,rutaSQL);
      if(!resp.error) { appSolMatriBotonCancel(); }
    } catch(err){
      console.error('Error al cargar datos:', err);
    }
  }
}

function appSolMatriBotonNuevo(){
  const addNewPers = false; //se permite agregar nueva persona si no existe
  const addNewLista = true; //se permite agregar nuevos a la lista 
  const addRepLista = false; //se permite agregar repetidos a la lista
  Persona.openBuscar('SolicMatric_verify',rutaSQL,addNewPers,addNewLista,addRepLista);
  $('#btn_modPersAddToForm').off('click');
  $('#btn_modPersAddToForm').on('click',handlerSolMatriAddToForm_Click);
}

function handlerSolMatriAddToForm_Click(e){
  appSolMatriClear(Persona.tablaPers.persona);
  appPersonaSetData(Persona.tablaPers); //pestaña Personales
  document.querySelector("#grid").style.display = 'none';
  document.querySelector("#edit").style.display = 'block';
  Persona.close();
  e.stopImmediatePropagation();
  $('#btn_modPersAddToForm').off('click');
}

async function appSolMatriBotonBorrar(){
  let arr = Array.from(document.querySelectorAll('[name="chk_Borrar"]:checked')).map(function(obj){return obj.attributes[2].nodeValue});
  if(arr.length>0){
    if(confirm("¿Esta seguro de continuar?")) {
      try{
        const resp = await appAsynFetch({ TipoQuery:'SolicMatric_del', arr:arr },rutaSQL);
        if (!resp.error) { appSolMatriBotonCancel(); }
      } catch(err){
        console.error('Error al cargar datos:', err);
      }
    }
  } else {
    alert("NO eligio borrar ninguno");
  }
}

async function appSolMatriAprueba(matriculaID){
  $("#modalAprueba").modal("show");
  document.querySelector('#grdPagos').innerHTML = ('<tr><td colspan="6"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  try{
    const resp = await appAsynFetch({
      TipoQuery: 'aprobarMatric_view',
      matriculaID: matriculaID
    },rutaSQL);
    
    //respuesta
    let data = resp.tablaSolMatri;
    document.querySelector("#txt_modapruebaFechaAprueba").disabled = (data.rolUser==data.rolROOT) ? (false):(true);    
    $("#txt_modapruebaFechaAprueba").datepicker("setDate",moment(data.fecha_aprueba).format("DD/MM/YYYY"));
    document.querySelector("#hid_modapruebaID").value = (data.ID);
    document.querySelector("#lbl_modapruebaAlumnoApellidos").innerHTML = (data.apellidos);
    document.querySelector("#lbl_modapruebaAlumnoNombres").innerHTML = (data.nombres);
    document.querySelector("#lbl_modapruebaDNI").innerHTML = (data.nro_dui);
    document.querySelector("#lbl_modapruebaFechaSolMatri").innerHTML = (moment(data.fecha_solicita).format("DD/MM/YYYY"));
    document.querySelector("#lbl_modapruebaCodigo").innerHTML = (data.codigo);
    document.querySelector("#lbl_modapruebaYYYY").innerHTML = (data.yyyy);
    document.querySelector("#lbl_modapruebaNivel").innerHTML = (data.nivel);
    document.querySelector("#lbl_modapruebaGrado").innerHTML = (data.grado);
    document.querySelector("#lbl_modapruebaSeccion").innerHTML = (data.seccion);
    document.querySelector("#lbl_modapruebaObservac").innerHTML = (data.observac);
    
    objModalPagos = resp.tablaPagos;
    modaprueba_Pagos_SetData(objModalPagos);

    //botones del footer del modal
    document.querySelector("#div_modapruebaFooter").innerHTML = '<button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>'+
      '<button type="button" class="btn btn-primary" onclick="javascript:modaprueba_BotonAprobarSolic();"><i class="fa fa-thumbs-up"></i> Aprobar Solicitud</button>';
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appSolMatriView(matriculaID){
  //tabs default en primer tab - loader
  $('.nav-tabs li').removeClass('active');
  $('.tab-content .tab-pane').removeClass('active');
  $('a[href="#datosSolMatri"]').closest('li').addClass('active');
  $('#datosSolMatri').addClass('active');
  
  try{
    const resp = await appAsynFetch({
      TipoQuery : 'SolicMatric_view',
      matriculaID : matriculaID
    },rutaSQL);
    document.querySelector("#btnUpdate").style.display = (menu.oper.submenu.solmatri.cmdUpdate==1)?('inline'):('none');
    document.querySelector("#btnInsert").style.display = 'none';
    resp.tablaSolMatri.persona = resp.tablaPers.persona;
    appSolMatriSetData(resp.tablaSolMatri);  //pestaña Solicitud de Matricula
    appPersonaSetData(resp.tablaPers); //pestaña Personales

    document.querySelector('#grid').style.display = 'none';
    document.querySelector('#edit').style.display = 'block';
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appSolMatriSetData(data){
  //todos los inputs sin error y panel error deshabilitado
  $('.form-group').removeClass('has-error');
    
  //tabs default en primer tab
  $('.nav-tabs li').removeClass('active');
  $('.tab-content .tab-pane').removeClass('active');
  $('a[href="#datosSolMatri"]').closest('li').addClass('active');
  $('#datosSolMatri').addClass('active');
  
  //pestaña de SolMatri
  appLlenarDataEnComboBox(data.comboNiveles,"#cbo_SolMatriNiveles",data.nivelID); //seteado a primaria
  appLlenarDataEnComboBox(data.comboGrados,"#cbo_SolMatriGrados",data.gradoID); //prmer grado
  appLlenarDataEnComboBox(data.comboSecciones,"#cbo_SolMatriSecciones",data.seccionID); //seccion A
  document.querySelector("#hid_SolMatriID").value = (data.ID);
  document.querySelector("#txt_SolMatriAlumno").value = (data.persona);
  document.querySelector("#txt_SolMatriFechaSolicita").disabled = (data.rolUser==data.rolROOT) ? (false):(true);
  document.querySelector("#txt_SolMatriCodigo").value = (data.codigo);
  document.querySelector("#txt_SolMatriYYYY").value = (data.yyyy); //año de matricula
  document.querySelector("#txt_SolMatriObservac").value = (data.observac);
  $("#txt_SolMatriFechaSolicita").datepicker("setDate",moment(data.fecha_solicita).format("DD/MM/YYYY"));
}

async function appSolMatriClear(txtSocio){
  //todos los inputs sin error y panel error deshabilitado
  $('.form-group').removeClass('has-error');
    
  //tabs default en primer tab
  $('.nav-tabs li').removeClass('active');
  $('.tab-content .tab-pane').removeClass('active');
  $('a[href="#datosSolMatri"]').closest('li').addClass('active');
  $('#datosSolMatri').addClass('active');
  document.querySelector("#btnUpdate").style.display = 'none';
  document.querySelector("#btnInsert").style.display = 'inline';
  document.querySelector("#hid_SolMatriID").value = (0);
  document.querySelector("#txt_SolMatriAlumno").value = (txtSocio);
  document.querySelector("#txt_SolMatriCodigo").value = ("");
  document.querySelector("#txt_SolMatriObservac").value = ("");

  try{
    const resp = await appAsynFetch({ TipoQuery : 'SolicMatric_new' }, rutaSQL);

    appLlenarDataEnComboBox(resp.comboNiveles,"#cbo_SolMatriNiveles",0); //seteado a primaria
    appLlenarDataEnComboBox(resp.comboGrados,"#cbo_SolMatriGrados",0); //prmer grado
    appLlenarDataEnComboBox(resp.comboSecciones,"#cbo_SolMatriSecciones",0); //seccion A
    document.querySelector("#txt_SolMatriFechaSolicita").disabled = (resp.rolUser==resp.rolROOT) ? (false):(true);
    document.querySelector("#txt_SolMatriYYYY").value = (resp.yyyy); //año de matricula
    $("#txt_SolMatriFechaSolicita").datepicker("setDate",moment(resp.fecha).format("DD/MM/YYYY"));
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appSolMatriValidarCampos(){
  let esError = false;
  $('.form-group').removeClass('has-error');
  if(document.querySelector("#txt_SolMatriFechaSolicita").value=="")  { document.querySelector("#div_SolMatriFechaSolicita").className = "form-group has-error"; esError = true; }
  return esError;
}

function appSolMatriGetDatosToDatabase(){
  let rpta = {
    TipoQuery : "SolicMatric_exec",
    TipoExec : null,
    ID : document.querySelector('#hid_SolMatriID').value,
    alumnoID : document.querySelector("#hid_PersID").value,
    seccionID : document.querySelector("#cbo_SolMatriSecciones").value,
    yyyy : document.querySelector("#txt_SolMatriYYYY").value,
    fecha_solicita : appConvertToFecha(document.querySelector("#txt_SolMatriFechaSolicita").value),
    observac : document.querySelector("#txt_SolMatriObservac").value
  }
  return rpta;
}

function appPersonaSetData(data){
  //pestaña datos personales
  document.querySelector("#hid_PersID").value = (data.ID);
  document.querySelector("#lbl_PersTipoNombres").innerHTML = ("Nombres");
  document.querySelector("#lbl_PersTipoProfesion").innerHTML = ("Profesion");
  document.querySelector("#lbl_PersTipoApellidos").style.display = 'block';
  document.querySelector("#lbl_PersTipoSexo").style.display = 'block';
  document.querySelector("#lbl_PersTipoECivil").style.display = 'block';
  document.querySelector("#lbl_PersTipoGIntruc").style.display = 'block';
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


//niveles
async function comboGrados(){
  document.querySelector("#cbo_SolMatriGrados").disabled = true;
  document.querySelector("#cbo_SolMatriSecciones").disabled = true;
  try{
    const resp = await appAsynFetch({
      TipoQuery : "comboNivel",
      tipoID  : 3, //grados
      padreID : document.querySelector("#cbo_SolMatriNiveles").value
    },rutaSQL);
    
    //respuesta
    appLlenarDataEnComboBox(resp.grados,"#cbo_SolMatriGrados",0); //grados
    appLlenarDataEnComboBox(resp.secciones,"#cbo_SolMatriSecciones",0); //secciones
    document.querySelector("#cbo_SolMatriGrados").disabled = false;
    document.querySelector("#cbo_SolMatriSecciones").disabled = false;
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function comboSecciones(){
  //loader
  document.querySelector("#cbo_SolMatriSecciones").disabled = true;
  try{
    const resp = await appAsynFetch({
      TipoQuery : "comboNivel",
      tipoID  : 4, //secciones
      padreID : document.querySelector("#cbo_SolMatriGrados").value
    }, rutaSQL);
    
    //respuesta
    appLlenarDataEnComboBox(resp.secciones,"#cbo_SolMatriSecciones",0); //secciones
    document.querySelector("#cbo_SolMatriSecciones").disabled = false;
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}


//modal aprobacion de solicitud
async function modaprueba_BotonAprobarSolic(){
  const importeTotal = objModalPagos.reduce((acc, obj) => acc + parseFloat(obj.importe), 0);
  
  if(importeTotal<=0) { alert("No se han agregado pagos!"); return; }
  if(confirm("¿Esta seguro de continuar?")) {
    try{
      const datos = {
        TipoQuery : "aprobarMatric_exec",
        matriculaID : document.querySelector("#hid_modapruebaID").value,
        fecha_aprueba : appConvertToFecha(document.querySelector("#txt_modapruebaFechaAprueba").value),
        pagos : objModalPagos,
        importeTotal : importeTotal,
        TipoExec : "APRU" //aprueba solicitud de credito
      }
      const resp = await appAsynFetch(datos, rutaSQL);
      
      //respuesta
      if (!resp.error) { 
        appSolMatriBotonCancel();
        $("#modalAprueba").modal("hide");
      }
    } catch(err){
      console.error('Error al cargar datos:', err);
    }
  }
}

async function modaprueba_BotonPagosAdd(){
  try{
    const resp = await appAsynFetch({ TipoQuery : "aprobarMatric_PagoAdd" },rutaSQL);
    
    //respuesta
    let fila = "";
    let filterID = objModalPagos.map(obj => obj.productoID);
    objAddPagos = resp.tablaPagos.filter(obj => !filterID.includes(obj.productoID));
    objAddPagos.forEach((valor,key)=>{
      fila += '<tr>'+
              '<td><input type="checkbox" name="chk_modalpagosadd" value="'+(valor.productoID)+'"/></td>'+
              '<td>'+(valor.abrevia)+'</td>'+
              '<td>'+(valor.producto)+'</td>'+
              '<td>'+moment(valor.vencimiento).format("DD/MM/YYYY")+'</td>'+
              '<td style="text-align:right;">'+appFormatMoney(valor.importe,2)+'</td>'+
              '</tr>';
    });
    document.querySelector('#grdaddpagosDatos').innerHTML = fila;
    $("#modalPagosAdd").modal("show");
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function modaprueba_Pagos_SetData(data){
  if(data.length>0){
    let fila = "";
    data.forEach((valor,key)=>{
      fila += '<tr style="color:#555;">'+
              '<td style="color:#999;text-align:center;font-size:11px;">'+(key+1)+'</td>'+
              '<td><a href="javascript:modaprueba_Pagos_DeleteItem('+(valor.productoID)+')"><i style="color:red;" class="fa fa-trash"></i></a></td>'+
              '<td>'+(valor.abrevia)+'</td>'+
              '<td><a href="javascript:modaprueba_Pagos_EditItem('+(valor.productoID)+')">'+(valor.producto)+'</a></td>'+
              '<td>'+moment(valor.vencimiento).format("DD/MM/YYYY")+'</td>'+
              '<td style="text-align:right;">'+appFormatMoney(valor.importe,2)+'</td>'+
              '</tr>';
    });
    document.querySelector('#grdPagos').innerHTML = fila;
  } else {
    document.querySelector('#grdPagos').innerHTML = '<tr><td colspan="7" style="text-align:center;color:red;">Sin Resultados</td></tr>';
  }
}

function modaprueba_Pagos_DeleteItem(productoID){
  let idx = objModalPagos.findIndex(elemento => elemento.productoID === productoID); 
  if(confirm("¿Desea eliminar "+objModalPagos[idx]["producto"]+" de la lista de pagos?")){
    objModalPagos.splice(idx,1);
    modaprueba_Pagos_SetData(objModalPagos);
  }
}

function modaprueba_Pagos_EditItem(productoID){
  try{
    const reg = objModalPagos.find(elem => elem.productoID === productoID);

    document.querySelector("#hid_modPagoID").value = productoID;
    document.querySelector("#txt_modPagoNombre").value = reg.producto;
    document.querySelector("#txt_modPagoImporte").value = reg.importe;
    $("#txt_modPagoVencimiento").datepicker("setDate",moment(reg.vencimiento).format("DD/MM/YYYY"));
    $("#modalPagosEdit").modal({ keyboard: true }).on('shown.bs.modal', () => $("#txt_modPagoImporte").focus());
    //$("#modalPagosEdit").modal("show");
  }catch(err){
    console.error('Error al cargar datos:', err);
  }
}


//modal agregar pagos
function modpagosadd_BotonAdd(){
  let arrSelect = Array.from(document.querySelectorAll('[name="chk_modalpagosadd"]:checked')).map(obj => parseInt(obj.value,10));
  let arrFilter = objAddPagos.filter(elem => arrSelect.includes(elem.productoID));
  let arrTemp = [...objModalPagos,...arrFilter];

  arrTemp.sort((a,b )=> new Date(a.orden)- new Date(b.orden));
  objModalPagos = arrTemp;
  modaprueba_Pagos_SetData(objModalPagos);
  objAddPagos = null;
  $("#modalPagosAdd").modal("hide");
}


//modal editar pagos
function modpagosedit_BotonModif(){
  let importe = document.querySelector("#txt_modPagoImporte").value;
  let vencimiento = appConvertToFecha(document.querySelector("#txt_modPagoVencimiento").value,"-");
  let productoID = parseInt(document.querySelector("#hid_modPagoID").value);
  let idx = objModalPagos.findIndex(elem => elem.productoID === productoID);

  if(idx>=0){
    objModalPagos[idx].importe = importe;
    objModalPagos[idx].vencimiento = vencimiento;
    modaprueba_Pagos_SetData(objModalPagos);
    $("#modalPagosEdit").modal("hide");
  } else {
    alert("Error al editar el pago!");
  }
}