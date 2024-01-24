const rutaSQL = "pages/mtto/alumnos/sql.php";
var menu = "";

//=========================funciones para Personas============================
function appAlumnosGrid(){
  document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="8"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  let txtBuscar = document.querySelector("#txtBuscar").value.toUpperCase();
  let datos = { TipoQuery: 'selAlumnos', buscar: txtBuscar };

  appFetch(datos,rutaSQL).then(resp => {
    let disabledDelete = (menu.mtto.submenu.alumnos.cmdDelete===1) ? "" : "disabled";
    document.querySelector("#chk_All").disabled = (menu.mtto.submenu.alumnos.cmdDelete===1) ? false : true;
    if(resp.tabla.length>0){
      let fila = "";
      resp.tabla.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td><input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'" '+disabledDelete+'/></td>'+
                '<td>'+(valor.codigo)+'</td>'+
                '<td>'+(moment(valor.fecha).format("DD/MM/YYYY"))+'</td>'+
                '<td>'+(valor.nro_dui)+'</td>'+
                '<td><a href="javascript:appAlumnoView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.alumno)+'</a></td>'+
                '<td>'+(valor.direccion)+'</td>'+
                '</tr>';
      });
      document.querySelector('#grdDatos').innerHTML = (fila);
    } else {
      let res = (txtBuscar==="") ? ("") : ("para "+txtBuscar);
      document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="8" style="text-align:center;color:red;">Sin Resultados '+(res)+'</td></tr>');
    }
    document.querySelector('#grdCount').innerHTML = (resp.tabla.length+"/"+resp.cuenta);
  });
}

function appAlumnosReset(){
  appFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php").then(resp => {
    menu = JSON.parse(resp.menu);
    document.querySelector("#btn_DEL").style.display = (menu.mtto.submenu.alumnos.cmdDelete==1)?('inline'):('none');
    document.querySelector("#btn_NEW").style.display = (menu.mtto.submenu.alumnos.cmdInsert==1)?('inline'):('none');

    document.querySelector("#txtBuscar").value = ("");
    document.querySelector("#grdDatos").innerHTML = ("");
    document.querySelector("#div_PersAuditoria").style.display = ((resp.rolID==101)?('block'):('none'));
    appAlumnosGrid();
  });
}

function appAlumnosBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { appAlumnosGrid(); }
}

function appAlumnosBotonCancel(){
  appAlumnosGrid();
  document.querySelector("#grid").style.display = 'block';
  document.querySelector("#edit").style.display = 'none';
}

function appAlumnosBotonInsert(){
  let datos = appAlumnoGetDatosToDatabase();
  
  if(datos!=""){
    datos.TipoQuery = "insAlumno";
    appFetch(datos,rutaSQL).then(resp => {
      appAlumnosBotonCancel();
    });
  }
}

function appAlumnosBotonUpdate(){
  let datos = appAlumnoGetDatosToDatabase();

  if(datos!=""){
    datos.TipoQuery = "updAlumno";
    appFetch(datos,rutaSQL).then(resp => {
      appAlumnosBotonCancel();
    });
  }
}

function appAlumnosBotonNuevo(){
  Persona.openBuscar('VerifyAlumno',rutaSQL,true,true,false);
  $('#btn_modPersInsert').off('click');
  
  $('#btn_modPersInsert').on('click',handlerAlumnosInsert_Click);
  $('#btn_modPersAddToForm').on('click',handlerAlumnosAddToForm_Click);
}

function handlerAlumnosInsert_Click(e){
  if(Persona.sinErrores()){ //sin errores
    console.log("en alumnos ==> nuevos");
    document.querySelector('#grid').style.display = 'none';
    document.querySelector('#edit').style.display = 'block';
    Persona.ejecutaSQL().then(resp => {
      appPersonaSetData(resp.tablaPers);
      appAlumnoClear();
      Persona.close();
    });
  } else {
    alert("!!!Faltan llenar Datos!!!");
  }
  e.stopImmediatePropagation();
  $('#btn_modPersInsert').off('click');
}

function handlerAlumnosAddToForm_Click(e){
  console.log("desde alumnos");
  document.querySelector('#grid').style.display = 'none';
  document.querySelector('#edit').style.display = 'block';
  appPersonaSetData(Persona.tablaPers); //pestaña Personales
  appAlumnoClear();
  Persona.close();
  e.stopImmediatePropagation();
  $('#btn_modPersAddToForm').off('click');
}

function appAlumnosBotonBorrar(){
  let arr = Array.from(document.querySelectorAll('[name="chk_Borrar"]:checked')).map(function(obj){return obj.attributes[2].nodeValue});
  if(arr.length>0){
    if(confirm("¿Esta seguro de continuar?")) {
      appFetch({ TipoQuery:'delAlumnos', arr:arr },rutaSQL).then(resp => {
        if (resp.error == false) { //sin errores
          console.log(resp);
          appAlumnosBotonCancel();
        }
      });
    }
  } else {
    alert("NO eligio borrar ninguno");
  }
}

function appAlumnoView(personaID){
  let datos = {
    TipoQuery : 'viewAlumno',
    personaID : personaID,
    fullQuery : 2
  };
  
  //tabs default en primer tab
  $('.nav-tabs li').removeClass('active');
  $('.tab-content .tab-pane').removeClass('active');
  $('a[href="#datosAlumno"]').closest('li').addClass('active');
  $('#datosAlumno').addClass('active');
  document.querySelector("#div_SocAuditoria").style.display = 'block';
  document.querySelector("#btnUpdate").style.display = (menu.mtto.submenu.alumnos.cmdUpdate==1)?('inline'):('none');
  document.querySelector("#btnInsert").style.display = 'none';

  appFetch(datos,rutaSQL).then(resp => {
    appAlumnoSetData(resp.tablaAlumno);  //pestaña Alumno
    appPersonaSetData(resp.tablaPers); //pestaña Personales
    
    document.querySelector('#grid').style.display = 'none';
    document.querySelector('#edit').style.display = 'block';
  });
}

function appAlumnoSetData(data){
  //info corta
  document.querySelector("#lbl_Codigo").innerHTML = (data.codigo);
  document.querySelector("#lbl_Agencia").innerHTML = (data.agencia);
  //pestaña de Alumno
  appLlenarDataEnComboBox(data.comboAgencias,"#cbo_SocAgencia",data.agenciaID);
  document.querySelector('#txt_SocFechaIng').value = (moment(data.fecha).format("DD/MM/YYYY"));
  document.querySelector("#txt_SocCodigo").value = (data.codigo);
  document.querySelector("#txt_SocObserv").value = (data.observac);
  document.querySelector("#lbl_SocSysFecha").innerHTML = (moment(data.sys_fecha).format("DD/MM/YYYY"));
  document.querySelector("#lbl_SocSysUser").innerHTML = (data.usermod);
}

function appAlumnoClear(){
  let datos = {
    TipoQuery : 'startAlumno'
  }

  //tabs default en primer tab
  $('.nav-tabs li').removeClass('active');
  $('.tab-content .tab-pane').removeClass('active');
  $('a[href="#datosAlumno"]').closest('li').addClass('active');
  $('#datosAlumno').addClass('active');

  //todos los inputs sin error y panel error deshabilitado
  $('.form-group').removeClass('has-error');
  document.querySelector("#div_SocAuditoria").style.display = 'none';
  document.querySelector("#btnInsert").style.display = (menu.mtto.submenu.alumnos.cmdInsert==1)?('inline'):('none');
  document.querySelector("#btnUpdate").style.display = 'none';

  appFetch(datos,rutaSQL).then(resp => {
    //pestaña de Alumno
    document.querySelector('#txt_SocFechaIng').value = (moment(resp.fecha).format("DD/MM/YYYY"));
    document.querySelector("#txt_SocCodigo").placeholder = ("00-000000");
    document.querySelector("#txt_SocCodigo").value = ("");
  });
}

function appFamiPadreAdd(){
  Persona.openBuscar('VerifyPadre',rutaSQL,true,true,false);
  $('#btn_modPersInsert').off('click');

  $('#btn_modPersInsert').on('click',handlerFamiPadreInsert_Click);
  $('#btn_modPersAddToForm').on('click',handlerFamiPadreAddToForm_Click);
}

function handlerFamiPadreInsert_Click(e){
  if(Persona.sinErrores()){ //sin errores
    console.log("padres nuevos");
    Persona.ejecutaSQL().then(resp => {
      appFamiPadreSetData(resp.tablaPers);
      Persona.close();
    });
  } else {
    alert("!!!Faltan llenar Datos!!!");
  }
  e.stopImmediatePropagation();
  $('#btn_modPersInsert').off('click');
}

function handlerFamiPadreAddToForm_Click(e){
  console.log("desde famipadre");
  appFamiPadreSetData(Persona.tablaPers);
  Persona.close();
  e.stopImmediatePropagation();
  $('#btn_modPersAddToForm').off('click');
}

function appFamiMadreAdd(){
  Persona.openBuscar('VerifyMadre',rutaSQL,true,true,false);
  $('#btn_modPersInsert').off('click');

  $('#btn_modPersInsert').on('click',handlerFamiMadreInsert_Click);
  $('#btn_modPersAddToForm').on('click',handlerFamiMadreAddToForm_Click);
}

function handlerFamiMadreInsert_Click(e){
  if(Persona.sinErrores()){ //sin errores
    Persona.ejecutaSQL().then(resp => {
      appFamiMadreSetData(resp.tablaPers);
      Persona.close();
    });
  } else {
    alert("!!!Faltan llenar Datos!!!");
  }
  e.stopImmediatePropagation();
  $('#btn_modPersInsert').off('click');
}

function handlerFamiMadreAddToForm_Click(e){
  appFamiMadreSetData(Persona.tablaPers);
  Persona.close();
  e.stopImmediatePropagation();
  $('#btn_modPersAddToForm').off('click');
}

function appFamiApoderaAdd(){
  Persona.openBuscar('VerifyApodera',rutaSQL,true,true,false);
  $('#btn_modPersInsert').off('click');

  $('#btn_modPersInsert').on('click',handlerFamiApoderaInsert_Click);
  $('#btn_modPersAddToForm').on('click',handlerFamiApoderaAddToForm_Click);
}

function handlerFamiApoderaInsert_Click(e){
  if(Persona.sinErrores()){ //sin errores
    Persona.ejecutaSQL().then(resp => {
      appFamiApoderaSetData(resp.tablaPers);
      Persona.close();
    });
  } else {
    alert("!!!Faltan llenar Datos!!!");
  }
  e.stopImmediatePropagation();
  $('#btn_modPersInsert').off('click');
}

function handlerFamiApoderaAddToForm_Click(e){
  appFamiApoderaSetData(Persona.tablaPers);
  Persona.close();
  e.stopImmediatePropagation();
  $('#btn_modPersAddToForm').off('click');
}

function appAlumnoGetDatosToDatabase(){
  let datos = "";
  let EsError = false;
  $('.form-group').removeClass('has-error');
  
  if(!EsError){
    datos = {
      alumnoID : document.querySelector("#lbl_ID").innerHTML,
      socCodigo : "",
      socFecha : appConvertToFecha(document.querySelector("#txt_SocFechaIng").value,""),
      socAgenciaID : document.querySelector("#cbo_SocAgencia").value,
      socObservac : document.querySelector("#txt_SocObserv").value
    }
  }
  return datos;
}

function appFamiPadreSetData(data){
  document.querySelector("#hid_alumnoFamiPadreID").value = data.ID
  document.querySelector("#lbl_alumnoFamiPadre").innerHTML = data.ap_paterno + " " + data.ap_materno + ", " + data.nombres + "<br/>" + data.direccion
}

function appFamiMadreSetData(data){
  document.querySelector("#hid_alumnoFamiMadreID").value = data.ID
  document.querySelector("#lbl_alumnoFamiMadre").innerHTML = data.ap_paterno + " " + data.ap_materno + ", " + data.nombres + "<br/>" + data.direccion
}

function appFamiApoderaSetData(data){
  document.querySelector("#hid_alumnoFamiApoderaID").value = data.ID
  document.querySelector("#lbl_alumnoFamiApodera").innerHTML = data.ap_paterno + " " + data.ap_materno + ", " + data.nombres + "<br/>" + data.direccion
}

function appPersonaSetData(data){
  //permisos
  document.querySelector("#btn_PersUpdate").style.display = 'block';
  document.querySelector("#btn_PersPermiso").style.display = 'none';

  //info corta
  document.querySelector('#img_Foto').src = (data.urlfoto=="")?("data/personas/fotouser.jpg"):(data.urlfoto);
  document.querySelector("#lbl_Nombres").innerHTML = (data.nombres);
  document.querySelector("#lbl_Apellidos").innerHTML = (data.ap_paterno+" "+data.ap_materno);
  document.querySelector("#lbl_ID").innerHTML = (data.ID);
  document.querySelector("#lbl_TipoDNI").innerHTML = (data.tipoDUI);
  document.querySelector("#lbl_DNI").innerHTML = (data.nroDUI);

  //pestaña datos personales
  if(data.tipoPersona==2){ //persona juridica
    document.querySelector("#lbl_PersTipoNombres").innerHTML = ("Razon Social");
    document.querySelector("#lbl_PersTipoProfesion").innerHTML = ("Rubro");
    document.querySelector("#lbl_PersTipoApellidos").style.display = 'none';
    document.querySelector("#lbl_PersTipoSexo").style.display = 'none';
    document.querySelector("#lbl_PersTipoECivil").style.display = 'none';
    document.querySelector("#lbl_PersTipoGIntruc").style.display = 'none';
  }else{
    document.querySelector("#lbl_PersTipoNombres").innerHTML = ("Nombres");
    document.querySelector("#lbl_PersTipoProfesion").innerHTML = ("Profesion");
    document.querySelector("#lbl_PersTipoApellidos").style.display = 'block';
    document.querySelector("#lbl_PersTipoSexo").style.display = 'block';
    document.querySelector("#lbl_PersTipoECivil").style.display = 'block';
    document.querySelector("#lbl_PersTipoGIntruc").style.display = 'block';
  }
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

function appPersonaEditar(){
  Persona.editar(document.querySelector('#lbl_ID').innerHTML,'S');
  $('#btn_modPersUpdate').on('click',function(e) {
    if(Persona.sinErrores()){ //sin errores
      Persona.ejecutaSQL().then(resp => {
        appPersonaSetData(resp.tablaPers);
        Persona.close();
      });
    } else {
      alert("!!!Faltan llenar Datos!!!");
    }
    e.stopImmediatePropagation();
    $('#btn_modPersUpdate').off('click');
  });
}

//otras funciones
function appCheckOnOff(check,span,textbox){
  if(check.checked){
    $(span).css("background","#FFFFFF");
    $(textbox).removeAttr("disabled");
    if($(textbox).val().trim()=="") { $(textbox).datepicker("setDate",moment().format("DD/MM/YYYY")); }
  } else {
    $(span).css("background","#EEEEEE");
    $(textbox).attr("disabled","disabled");
  }
}

function appPermisoPersonas(){
  let datos = { TipoQuery:'insNotifi', tabla:'tb_personas', personaID:$("#lbl_ID").html() }
  appAjaxInsert(datos,"pages/global/notifi/sql.php").done(function(resp){
    if(!resp.error){ $("#btn_PersPermiso").hide(); }
    else { alert("!!!Hubo un error... "+(resp.mensaje)+"!!!"); }
  });
}
