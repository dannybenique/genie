const rutaSQL = "pages/master/personas/sql.php";

//=========================funciones para Personas============================
async function appPersonasGrid(){
  document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="6"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  const txtBuscar = document.querySelector("#txtBuscar").value.toUpperCase();
  try{
    const resp = await appAsynFetch({TipoQuery:'persona_sel', buscar:txtBuscar},rutaSQL);
    
    const disabledDelete = (resp.rolID===resp.rootID) ? (""):("disabled");
    document.querySelector("#chk_All").disabled = (resp.rolID===resp.rootID) ? false : true;
    if(resp.tabla.length>0){
      let fila = "";
      resp.tabla.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td><input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'" '+(disabledDelete)+'/></td>'+
                '<td>'+(valor.DNI)+'</td>'+
                '<td><a href="javascript:appPersonaView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.persona)+'</a></td>'+
                '<td>'+(valor.direccion)+'</td>'+
                '</tr>';
      });
      document.querySelector('#grdDatos').innerHTML = fila;
    }else{
      document.querySelector('#grdDatos').innerHTML = '<tr><td colspan="6" style="text-align:center;color:red;">Sin Resultados para '+txtBuscar+'</td></tr>';
    }
    document.querySelector('#grdCount').innerHTML = resp.tabla.length+"/"+resp.cuenta;
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
  
  
}

function appPersonasBuscar(e){
  if(e.keyCode === 13) { 
    document.querySelector('#grdDatos').innerHTML = "";
    appPersonasGrid();
  }
}

function appPersonaBoton_Reset(){
  document.querySelector('#txtBuscar').value = "";
  appPersonasGrid();
}

function appPersonaBoton_Nuevo(){
  Persona.openBuscar('VerifyPersona',rutaSQL,true,false,false);
  $('#btn_modPersInsert').on('click',async function(e) {
    if(Persona.sinErrores()){
      try{
        const resp = await Persona.ejecutaSQL();
        appPersonaSetData(resp.tablaPers);
        appLaboralClear();
        appConyugeClear();
        document.querySelector('#grid').style.display = 'none';
        document.querySelector('#edit').style.display = 'block';
        Persona.close();
        e.stopImmediatePropagation();
        $('#btn_modPersInsert').off('click');
      } catch(err){
        console.error('Error al cargar datos:', err);
      }
    } else {
      alert("!!!Faltan llenar Datos!!!");
    }
  });
}

async function appPersonaBoton_Borrar(){
  let arr = Array.from(document.querySelectorAll('[name="chk_Borrar"]:checked')).map(function(obj){return obj.attributes[2].nodeValue});
  if(arr.length>0){
    if(confirm("Esta accion borra FISICAMENTE el registro... ¿Esta seguro de continuar?")) {
      try{
        const resp = await appAsynFetch({ TipoQuery:'persona_del', arr:arr },rutaSQL);
        if (!resp.error) { appPersonasGrid(); }
      } catch(err){
        console.error('Error al cargar datos:', err);
      }
    }
  } else {
    alert("NO eligio borrar ninguno");
  }
}

function appPersonasBoton_Cancel(){
  appPersonasGrid();
  document.querySelector('#grid').style.display = 'block';
  document.querySelector('#edit').style.display = 'none';
}

async function appPersonasBoton_Auditoria(personaID){ //falta implementar
  // try{
  //   const resp = await appAsynFetch({
  //     TipoQuery: 'persona_audit',
  //     personaID: personaID
  //   },rutaSQL);

  //   //respuesta
  //   document.querySelector("#lbl_modAuditoriaTitulo").innerHTML = ((resp.tablaPers.tipoPersona==2) ? (resp.tablaPers.nombres) : (resp.tablaPers.persona));
  //   if(resp.tablaLog.length>0){
  //     let fila = "";
  //     resp.tablaLog.forEach((valor,key)=>{
  //       fila += '<tr>';
  //       fila += '<td>'+(valor.codigo)+'</td>';
  //       fila += '<td>'+(valor.tabla)+'</td>';
  //       fila += '<td>'+(valor.accion)+'</td>';
  //       fila += '<td>'+(valor.campo)+'</td>';
  //       fila += '<td>'+(valor.observac)+'</td>';
  //       fila += '<td>'+(valor.usuario)+'</td>';
  //       fila += '<td>'+(valor.sysIP)+'</td>';
  //       fila += '<td style="text-align:center;">'+(valor.sysagencia)+'</td>';
  //       fila += '<td style="text-align:right;">'+(valor.sysfecha)+'</td>';
  //       fila += '<td style="text-align:right;">'+(valor.syshora)+'</td>';
  //       fila += '</tr>';
  //     });
  //     document.querySelector('#grdAuditoriaBody').innerHTML = (fila);
  //   }else{
  //     document.querySelector('#grdAuditoriaBody').innerHTML = ('<tr><td colspan="10" style="text-align:center;color:red;">****** Sin Resultados ******</td></tr>');
  //   }
  //   $('#modalAuditoria').modal();
  // } catch(err){
  //   console.error('Error al cargar datos:', err);
  // }
}

function appPersonaEditar(){
  Persona.editar(document.querySelector("#lbl_ID").innerHTML,'P');
  $('#btn_modPersUpdate').on('click',async function(e) {
    if(Persona.sinErrores()){
      try{
        const resp = await Persona.ejecutaSQL();
        appPersonaSetData(resp.tablaPers);
        Persona.close();
      } catch(err){
        console.error('Error al cargar datos:', err);
      }
    } else {
      alert("!!!Faltan llenar Datos!!!");
    }
    e.stopImmediatePropagation();
    $('#btn_modPersUpdate').off('click');
  });
}
async function appPersonaView(personaID){
  //tabs default en primer tab
  $('.nav-tabs li').removeClass('active');
  $('.tab-content .tab-pane').removeClass('active');
  $('a[href="#datosPersonal"]').closest('li').addClass('active');
  $('#datosPersonal').addClass('active');
  
  try{
    const resp = await appAsynFetch({
      TipoQuery : 'persona_view',
      personaID : personaID,
      fullQuery : 2
    },rutaSQL);

    //respuesta
    appPersonaSetData(resp.tablaPers); //pestaña Personales
    appLaboralSetData(resp.tablaLabo); //pestaña laborales
    appConyugeSetData(resp.tablaCony); //pestaña de Conyuge

    document.querySelector('#grid').style.display = 'none';
    document.querySelector('#edit').style.display = 'block';
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}
function appPersonaSetData(data){
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

  //permisos
  document.querySelector("#btn_PersPermiso").style.display = 'none';
  document.querySelector("#btn_PersUpdate").style.display = 'block';
}

function appLaboralSetData(data){
  if(data.length>0){
    let fila = "";
    data.forEach((valor,key)=>{
      fila += '<tr>'+
              '<td><a href="javascript:appLaboralDelete('+(valor.ID)+');"><i class="fa fa-trash" style="color:#D73925;"></i></a></td>'+
              '<td>'+((valor.condicion==1)?("Dependiente"):("Independiente"))+'</td>'+
              '<td>'+(valor.ruc)+'</td>'+
              '<td><a href="javascript:appLaboralEditar('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.empresa)+'</a></td>'+
              '<td>'+(valor.cargo)+'</td>'+
              '<td style="text-align:right;">'+(appFormatMoney(valor.ingreso,2))+'</td>'+
              '</tr>';
    });
    document.querySelector('#grdLaboDatosBody').innerHTML = (fila);
  } else {
    document.querySelector('#grdLaboDatosBody').innerHTML = ('');
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

function appFormatosGenerarPDF(tipo){
  let urlServer = document.querySelector("#hid_FormUrlServer").value;
  $("#contenedorFrame").show();
  switch(tipo){
    case "SolicitudIngrSocio": $("#objPDF").prop("data",urlServer+"/includes/pdf/plantilla/rpt.solicIngrSoc.php?nroDNI="+document.querySelector("#lbl_FormNroDNI").innerHTML+"&ciudad="+document.querySelector("#cbo_ciudades").value); break;
    case "FichaInscripcion": $("#objPDF").prop("data",urlServer+"/includes/pdf/plantilla/rpt.fichaInscrip.php?personaID="+document.querySelector("#hid_FormPersonaID").value); break;
    case "DeclJuraConviv": $("#objPDF").prop("data",urlServer+"/includes/pdf/plantilla/rpt.declaracion.jurada.convivencia.php?personaID="+document.querySelector("#hid_FormPersonaID").value); break;
  }
}
