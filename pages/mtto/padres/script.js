const rutaSQL = "pages/mtto/padres/sql.php";
var menu = "";

//=========================funciones para Personas============================
function appPadresGrid(){
  document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="8"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  let txtBuscar = document.querySelector("#txtBuscar").value.toUpperCase();
  let datos = { TipoQuery: 'selPadres', buscar: txtBuscar };

  appFetch(datos,rutaSQL).then(resp => {
    let disabledDelete = (menu.mtto.submenu.padres.cmdDelete===1) ? "" : "disabled";
    document.querySelector("#chk_All").disabled = (menu.mtto.submenu.padres.cmdDelete===1) ? false : true;
    if(resp.tabla.length>0){
      let fila = "";
      resp.tabla.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td><input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'" '+disabledDelete+'/></td>'+
                '<td><i class="fa fa-paperclip" title="Auditoria"></i></td>'+
                '<td>'+(valor.nro_dui)+'</td>'+
                '<td><a href="javascript:appPadreView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.padre)+'</a></td>'+
                '<td>'+(valor.direccion)+'</td>'+
                '</tr>';
      });
      document.querySelector('#grdDatos').innerHTML = (fila);
    } else {
      let rpta = (txtBuscar==="") ? ("") : ("para "+txtBuscar);
      document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="8" style="text-align:center;color:red;">Sin Resultados '+(rpta)+'</td></tr>');
    }
    document.querySelector('#grdCount').innerHTML = (resp.tabla.length+"/"+resp.cuenta);
  });
}

function appPadresReset(){
  appFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php").then(resp => {
    menu = JSON.parse(resp.menu);
    document.querySelector("#btn_DEL").style.display = (menu.mtto.submenu.padres.cmdDelete==1)?('inline'):('none');
    document.querySelector("#btn_NEW").style.display = (menu.mtto.submenu.padres.cmdInsert==1)?('inline'):('none');

    document.querySelector("#txtBuscar").value = ("");
    document.querySelector("#grdDatos").innerHTML = ("");
    document.querySelector("#div_PersAuditoria").style.display = ((resp.rolID==101)?('block'):('none'));
    appPadresGrid();
  });
}

function appPadresBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { appPadresGrid(); }
}

function appPadresBotonCancel(){
  appPadresGrid();
  document.querySelector("#grid").style.display = 'block';
  document.querySelector("#edit").style.display = 'none';
}

function appPadresBotonInsert(){
  let datos = appPadreGetDatosToDatabase();
  
  if(datos!=""){
    datos.TipoQuery = "insPadre";
    appFetch(datos,rutaSQL).then(resp => {
      appPadresBotonCancel();
    });
  }
}

function appPadresBotonUpdate(){
  let datos = appPadreGetDatosToDatabase();

  if(datos!=""){
    datos.TipoQuery = "updPadre";
    appFetch(datos,rutaSQL).then(resp => {
      appPadresBotonCancel();
    });
  }
}

function appPadresBotonNuevo(){
  Persona.openBuscar('VerifyPadre',rutaSQL,true,true,false);
  $('#btn_modPersInsert').off('click');
  
  $('#btn_modPersInsert').on('click',handlerPadresInsert_Click);
  $('#btn_modPersAddToForm').on('click',handlerPadresAddToForm_Click);
}

function handlerPadresInsert_Click(e){
  if(Persona.sinErrores()){ //sin errores
    document.querySelector('#grid').style.display = 'none';
    document.querySelector('#edit').style.display = 'block';
    Persona.ejecutaSQL().then(resp => {
      appPersonaSetData(resp.tablaPers);
      appLaboralClear();
      appConyugeClear();
      appPadreClear();
      Persona.close();
      e.stopImmediatePropagation();
      $('#btn_modPersInsert').off('click');
    });
  } else {
    alert("!!!Faltan llenar Datos!!!");
  }
}

function handlerPadresAddToForm_Click(e){
  document.querySelector('#grid').style.display = 'none';
  document.querySelector('#edit').style.display = 'block';
  appPersonaSetData(Persona.tablaPers); //pestaña Personales
  appLaboralSetData(Persona.tablaPers.tablaLabo); //pestaña laborales
  appConyugeSetData(Persona.tablaPers.tablaCony); //pestaña Conyuge
  appPadreClear();
  Persona.close();
  e.stopImmediatePropagation();
  $('#btn_modPersAddToForm').off('click');
}

function appPadresBotonBorrar(){
  let arr = Array.from(document.querySelectorAll('[name="chk_Borrar"]:checked')).map(function(obj){return obj.attributes[2].nodeValue});
  if(arr.length>0){
    if(confirm("¿Esta seguro de continuar?")) {
      appFetch({ TipoQuery:'delPadres', arr:arr },rutaSQL).then(resp => {
        if (resp.error == false) { //sin errores
          console.log(resp);
          appPadresBotonCancel();
        }
      });
    }
  } else {
    alert("NO eligio borrar ninguno");
  }
}

function appPadreView(personaID){
  let datos = {
    TipoQuery : 'viewPadre',
    personaID : personaID,
    fullQuery : 2
  };
  
  //tabs default en primer tab
  $('.nav-tabs li').removeClass('active');
  $('.tab-content .tab-pane').removeClass('active');
  $('a[href="#datosPersonal"]').closest('li').addClass('active');
  $('#datosPersonal').addClass('active');
  document.querySelector("#div_PersAuditoria").style.display = 'block';
  document.querySelector("#btnUpdate").style.display = (menu.mtto.submenu.padres.cmdUpdate==1)?('inline'):('none');
  document.querySelector("#btnInsert").style.display = 'none';

  appFetch(datos,rutaSQL).then(resp => {
    document.querySelector("#lbl_Celular").innerHTML = (resp.tablaPers.celular);  //info Corta
    appPersonaSetData(resp.tablaPers); //pestaña Personales
    appLaboralSetData(resp.tablaLabo); //pestaña laborales
    appConyugeSetData(resp.tablaCony); //pestaña Conyuge

    document.querySelector('#grid').style.display = 'none';
    document.querySelector('#edit').style.display = 'block';
  });
}

function appPadreClear(){
  //tabs default en primer tab
  $('.nav-tabs li').removeClass('active');
  $('.tab-content .tab-pane').removeClass('active');
  $('a[href="#datosPersonal"]').closest('li').addClass('active');
  $('#datosPersonal').addClass('active');

  //todos los inputs sin error y panel error deshabilitado
  $('.form-group').removeClass('has-error');
  document.querySelector("#div_PersAuditoria").style.display = 'none';
  document.querySelector("#btnInsert").style.display = (menu.mtto.submenu.padres.cmdInsert==1)?('inline'):('none');
  document.querySelector("#btnUpdate").style.display = 'none';
}

function appPadreGetDatosToDatabase(){
  let datos = "";
  let EsError = false;
  $('.form-group').removeClass('has-error');
  
  if(!EsError){
    datos = {
      padreID : document.querySelector("#lbl_ID").innerHTML
    }
  }
  return datos;
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
  document.querySelector("#lbl_Celular").innerHTML = (data.celular);

  //pestaña datos personales
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

//datos laboral
function appLaboralSetData(data){
  if(data.length>0){
    let fila = "";
    data.forEach((valor,key)=>{
      fila += '<tr>';
      fila += '<td><a href="javascript:appLaboralDelete('+(valor.ID)+');"><i class="fa fa-trash" style="color:#D73925;"></i></a></td>';
      fila += '<td>'+((valor.condicion==1)?("Dependiente"):("Independiente"))+'</td>';
      fila += '<td>'+(valor.ruc)+'</td>';
      fila += '<td><a href="javascript:appLaboralEditar('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.empresa)+'</a></td>';
      fila += '<td>'+(valor.cargo)+'</td>';
      fila += '<td style="text-align:right;">'+(appFormatMoney(valor.ingreso,2))+'</td>';
      fila += '</tr>';
    });
    $('#grdLaboDatosBody').html(fila);
  } else {
    $('#grdLaboDatosBody').html('');
  }
}

function appLaboralClear(){
  document.querySelector("#grdLaboDatosBody").innerHTML = '';
  document.querySelector("#div_LaboEdit").style.display = 'none';
  document.querySelector("#btn_LaboDelete").style.display = 'none';
  document.querySelector("#btn_LaboUpdate").style.display = 'none';
  document.querySelector("#btn_LaboPermiso").style.display = 'none';
  document.querySelector("#btn_LaboInsert").style.display = 'inline';
}

function appLaboralNuevo(){
  Laboral.nuevo(document.querySelector('#lbl_ID').innerHTML);
  $('#btn_modLaboInsert').on('click',function(e) {
    if(Laboral.sinErrores()){
      Laboral.ejecutaSQL().then(resp => {
        appLaboralSetData(resp.tablaLabo);
        Laboral.close();
      });
    } else {
      alert("!!!Faltan llenar Datos!!!");
    }
    e.stopImmediatePropagation();
    $('#btn_modLaboInsert').off('click');
  });
}

function appLaboralEditar(laboralID){
  Laboral.editar(laboralID);
  $('#btn_modLaboUpdate').on('click',function(e) {
    if(Laboral.sinErrores()){ 
      Laboral.ejecutaSQL().then(resp => {
        appLaboralSetData(resp.tablaLabo);
        Laboral.close();
      });
    } else {
      alert("!!!Faltan llenar Datos!!!");
    }
    e.stopImmediatePropagation();
    $('#btn_modLaboUpdate').off('click');
  });
}

function appLaboralDelete(laboralID){
  if(confirm("¿Realmente desea eliminar los datos laborales?")) {
    let personaID = document.querySelector("#lbl_ID").innerHTML;
    Laboral.borrar(personaID,laboralID).then(resp => {
      if(!resp.error){
        appLaboralSetData(resp.tablaLabo);
        Laboral.close();
      } else {
        alert("!!!Hubo un error al momento de eliminar estos datos!!!");
      }
    });
  }
}

//datos conyuge
function appConyugeSetData(data){
  if(data.id_conyuge>0){
    let conyuge = data.persona;
    $('.form-group').removeClass("has-error");
    document.querySelector("#lbl_ConyNombres").innerHTML = (conyuge.nombres);
    document.querySelector("#lbl_ConyApellidos").innerHTML = (conyuge.ap_paterno+' '+conyuge.ap_materno);
    document.querySelector("#lbl_ConyTipoDNI").innerHTML = (conyuge.tipoDUI);
    document.querySelector("#lbl_ConyNroDNI").innerHTML = (conyuge.nroDUI);
    document.querySelector("#lbl_ConyFechaNac").innerHTML = (moment(conyuge.fechanac).format("DD/MM/YYYY"));
    document.querySelector("#lbl_ConyEcivil").innerHTML = (conyuge.ecivil);
    document.querySelector("#lbl_ConyCelular").innerHTML = (conyuge.celular);
    document.querySelector("#lbl_ConyTelefijo").innerHTML = (conyuge.telefijo);
    document.querySelector("#lbl_ConyEmail").innerHTML = (conyuge.correo);
    document.querySelector("#lbl_ConyGInstruccion").innerHTML = (conyuge.ginstruc);
    document.querySelector("#lbl_ConyProfesion").innerHTML = (conyuge.profesion);
    document.querySelector("#lbl_ConyOcupacion").innerHTML = (conyuge.ocupacion);
    document.querySelector("#lbl_ConyUbicacion").innerHTML = (conyuge.region+" - "+conyuge.provincia+" - "+conyuge.distrito);
    document.querySelector("#lbl_ConyDireccion").innerHTML = (conyuge.direccion);
    document.querySelector("#lbl_ConyReferencia").innerHTML = (conyuge.referencia);
    document.querySelector("#lbl_ConyMedidorluz").innerHTML = (conyuge.medidorluz);
    document.querySelector("#lbl_ConyMedidoragua").innerHTML = (conyuge.medidoragua);
    document.querySelector("#lbl_ConyTipovivienda").innerHTML = (conyuge.tipovivienda);
    document.querySelector("#div_Conyuge").style.display = 'block';
    document.querySelector("#lbl_ConyTiempoRel").innerHTML = (data.tiempoRelacion+(" (años)"));
    //permisos
    document.querySelector("#btn_ConyInsert").style.display = 'none';
    document.querySelector("#btn_ConyDelete").style.display = 'inline';
    document.querySelector("#btn_ConyUpdate").style.display = 'inline';
    document.querySelector("#btn_ConyPermiso").style.display = 'none';
  } else {
    appConyugeClear();
  }
}

function appConyugeClear(){
  $('.form-group').removeClass('has-error');
  document.querySelector("#lbl_ConyTiempoRel").value = ("0");

  document.querySelector("#div_Conyuge").style.display = 'none';
  document.querySelector("#btn_ConyDelete").style.display = 'none';
  document.querySelector("#btn_ConyUpdate").style.display = 'none';
  document.querySelector("#btn_ConyPermiso").style.display = 'none';
  document.querySelector("#btn_ConyInsert").style.display = 'inline';
}

function appConyugeNuevo(){
  Conyuge.nuevo(document.querySelector('#lbl_ID').innerHTML,rutaSQL);
  $('#btn_modConyInsert').on('click',function(e) {
    if(Conyuge.sinErrores()){ //guardamos datos del conyuge
      Conyuge.ejecutaSQL().then(resp => {
        appConyugeSetData(resp.tablaCony);
        Conyuge.close();
      });
    } else {
      alert("!!!Faltan llenar Datos!!!");
    }
    e.stopImmediatePropagation();
    $('#btn_modConyInsert').off('click');
  });
}

function appConyugeEditar(){
  Conyuge.editar(document.querySelector('#lbl_ID').innerHTML);
  $('#btn_modConyUpdate').on('click',function(e) {
    if(Conyuge.sinErrores()){ 
      Conyuge.ejecutaSQL().then(resp => {
        appConyugeSetData(resp.tablaCony);
        Conyuge.close();
      });
    } else {
      alert("!!!Faltan llenar Datos!!!");
    }
    e.stopImmediatePropagation();
    $('#btn_modConyUpdate').off('click');
  });
}

function appConyugeDelete(){
  if(confirm("¿Realmente desea eliminar los datos de Conyuge?")) {
    Conyuge.borrar(document.querySelector('#lbl_ID').innerHTML).then(resp => {
      if(!resp.error){
        appConyugeClear();
        Conyuge.close();
      } else {
        alert("!!!Hubo un error al momento de eliminar estos datos!!!");
      }
    });
  }
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
