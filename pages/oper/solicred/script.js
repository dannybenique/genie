const rutaSQL = "pages/oper/solicred/sql.php";
var menu = "";

//=========================funciones para Personas============================
function appSoliCredGrid(){
  document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="10"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  let txtBuscar = document.querySelector("#txtBuscar").value;
  let datos = {
    TipoQuery: 'selSoliCred',
    buscar: txtBuscar
  };

  appFetch(datos,rutaSQL).then(resp => {
    let disabledDelete = (menu.oper.submenu.solicred.cmdDelete===1) ? "" : "disabled";
    document.querySelector("#chk_All").disabled = (menu.oper.submenu.solicred.cmdDelete===1) ? false : true;
    if(resp.tabla.length>0){
      let fila = "";
      resp.tabla.forEach((valor,key)=>{
        fila += '<tr>';
        fila += '<td><input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'" '+disabledDelete+'/></td>';
        fila += '<td>'+((menu.oper.submenu.solicred.aprueba===1)?('<a href="javascript:appSoliCredAprueba('+(valor.ID)+');"><i class="fa fa-thumbs-up" style="color:#FF0084;"></i></a>'):(''))+'</td>';
        fila += '<td>'+(moment(valor.fecha).format("DD/MM/YYYY"))+'</td>';
        fila += '<td>'+(valor.nro_dui)+'</td>';
        fila += '<td>'+(valor.socio)+'</td>';
        fila += '<td><a href="javascript:appSoliCredView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.codigo)+(' &raquo; ')+(valor.producto)+'; '+(valor.mon_abrevia)+'; '+appFormatMoney(valor.tasa,2)+'%</a></td>';
        fila += '<td>'+(valor.tiposbs)+'</td>';
        fila += '<td>'+(moment(valor.otorga).format("DD/MM/YYYY"))+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.importe,2)+'</td>';
        fila += '<td style="text-align:center;">'+(valor.nro_cuotas)+'</td>';
        fila += '</tr>';
      });
      $('#grdDatos').html(fila);
    }else{
      let res = (txtBuscar==="") ? ("") : ("para "+txtBuscar);
      $('#grdDatos').html('<tr><td colspan="10" style="text-align:center;color:red;">Sin Resultados '+(res)+'</td></tr>');
    }
    $('#grdCount').html(resp.tabla.length+"/"+resp.cuenta);
  });
}

function appSoliCredReset(){
  appFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php").then(resp => {
    menu = JSON.parse(resp.menu);
    document.querySelector("#btn_DEL").style.display = (menu.oper.submenu.solicred.cmdDelete==1)?('inline'):('none');
    document.querySelector("#btn_NEW").style.display = (menu.oper.submenu.solicred.cmdInsert==1)?('inline'):('none');

    document.querySelector("#txtBuscar").value = ("");
    document.querySelector("#grdDatos").innerHTML = ("");
    document.querySelector("#div_PersAuditoria").style.display = ((resp.rolID==resp.rolROOT)?('block'):('none'));
    appSoliCredGrid();
  });
}

function appSoliCredBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { load_flag = 0; $('#grdDatosBody').html(""); appSoliCredGrid(); }
}

function appSoliCredBotonCancel(){
  appSoliCredGrid();
  $('#grid').show();
  $('#edit').hide();
}

function appSoliCredBotonInsert(){
  if(appSoliCredValidarCampos()){
    alert("¡¡¡Faltan datos!!!");
  } else {
    let datos = appSoliCredGetDatosToDatabase();
    datos.TipoExec = "INS";
    appFetch(datos,rutaSQL).then(resp => {
      appSoliCredBotonCancel();
    });
  }
}

function appSoliCredBotonUpdate(){
  if(appSoliCredValidarCampos()){
    alert("¡¡¡Faltan datos!!!");
  } else {
    let datos = appSoliCredGetDatosToDatabase();
    datos.TipoExec = "UPD";
    appFetch(datos,rutaSQL).then(resp => {
      appSoliCredBotonCancel();
    });
  }
}

function appSoliCredBotonNuevo(){
  Persona.openBuscar('VerifySoliCred',rutaSQL,false,true,true);
  $('#btn_modPersAddToForm').on('click',function(e) {
    let datos = {
      TipoQuery : 'viewPersona',
      personaID : Persona.tablaPers.ID,
      fullQuery : 0
    }
    appFetch(datos,'pages/mtto/personas/sql.php').then(resp => {
      appSoliCredClear(Persona.tablaPers.persona);
      appPersonaSetData(Persona.tablaPers); //pestaña Personales
      document.querySelector("#grid").style.display = 'none';
      document.querySelector("#edit").style.display = 'block';
      Persona.close();
    });
    e.stopImmediatePropagation();
    $('#btn_modPersAddToForm').off('click');
  });
}

function appSoliCredBotonBorrar(){
  let arr = Array.from(document.querySelectorAll('[name="chk_Borrar"]:checked')).map(function(obj){return obj.attributes[2].nodeValue});
  if(arr.length>0){
    if(confirm("¿Esta seguro de continuar?")) {
      appFetch({ TipoQuery:'delSoliCred', arr:arr },rutaSQL).then(resp => {
        console.log(resp);
        if (!resp.error) { appSoliCredBotonCancel(); }
      });
    }
  } else {
    alert("NO eligio borrar ninguno");
  }
}

function appSoliCredAprueba(solicredID){
  $("#modalAprueba").modal("show");
  let datos = {
    TipoQuery: 'viewApruebaSoliCred',
    SoliCredID: solicredID
  }
  appFetch(datos,rutaSQL).then(resp => {
    document.querySelector("#txt_modApruebaFechaAprueba").disabled = (resp.rolUser==resp.rolROOT) ? (false):(true);
    $("#txt_modApruebaFechaAprueba").datepicker("setDate",moment(resp.fecha_aprueba).format("DD/MM/YYYY"));

    document.querySelector("#hid_modApruebaID").value = (resp.ID);
    document.querySelector("#lbl_modApruebaSocio").innerHTML = (resp.socio);
    document.querySelector("#lbl_modApruebaFechaSoliCred").innerHTML = (moment(resp.fecha_solicred).format("DD/MM/YYYY"));
    document.querySelector("#lbl_modApruebaCodigo").innerHTML = (resp.codigo);
    document.querySelector("#lbl_modApruebaMoneda").innerHTML = (resp.moneda);
    document.querySelector("#lbl_modApruebaClasifica").innerHTML = (resp.clasifica);
    document.querySelector("#lbl_modApruebaCondicion").innerHTML = (resp.condicion);
    document.querySelector("#lbl_modApruebaAgencia").innerHTML = (resp.agencia);
    document.querySelector("#lbl_modApruebaPromotor").innerHTML = (resp.promotor);
    document.querySelector("#lbl_modApruebaAnalista").innerHTML = (resp.analista);
    document.querySelector("#lbl_modApruebaTipoSBS").innerHTML = (resp.tiposbs);
    document.querySelector("#lbl_modApruebaDestinoSBS").innerHTML = (resp.destsbs);
    document.querySelector("#lbl_modApruebaTipoCredito").innerHTML = (resp.tipocred);
    document.querySelector("#lbl_modApruebaProducto").innerHTML = (resp.producto);
    document.querySelector("#lbl_modApruebaImporte").innerHTML = (appFormatMoney(resp.importe,2));
    document.querySelector("#lbl_modApruebaNrocuotas").innerHTML = (resp.nrocuotas);
    document.querySelector("#lbl_modApruebaTasaCred").innerHTML = (appFormatMoney(resp.tasa,2));
    document.querySelector("#lbl_modApruebaTasaMora").innerHTML = (appFormatMoney(resp.mora,2));
    document.querySelector("#lbl_modApruebaTasaDesgr").innerHTML = (appFormatMoney(resp.desgr,2));
    document.querySelector("#lbl_modApruebaFechaOtorga").innerHTML = (moment(resp.fecha_otorga).format("DD/MM/YYYY"));
    document.querySelector("#lbl_modApruebaFechaPriCuota").innerHTML = (moment(resp.fecha_pricuota).format("DD/MM/YYYY"));
    document.querySelector("#lbl_modApruebaFrecuencia").innerHTML = (resp.frecuencia+" dias");
    document.querySelector("#lbl_modApruebaCuota").innerHTML = ("&nbsp;<small style='font-size:10px;'>"+resp.mon_abrevia+"</small>&nbsp;&nbsp;"+resp.cuota+"&nbsp;");
    document.querySelector("#lbl_modApruebaObservac").innerHTML = (resp.observac);
    document.querySelector("#lbl_modEtiqFrecuencia").style.display = (resp.tipocredID==1)?('none'):('inherit');
  });
}

function appSoliCredView(solicredID){
  let datos = {
    TipoQuery : 'viewSoliCred',
    SoliCredID : solicredID
  };
  
  appFetch(datos,rutaSQL).then(resp => {
    //tabs default en primer tab
    $('.nav-tabs li').removeClass('active');
    $('.tab-content .tab-pane').removeClass('active');
    $('a[href="#datosSoliCred"]').closest('li').addClass('active');
    $('#datosSoliCred').addClass('active');
    document.querySelector("#btnUpdate").style.display = (menu.oper.submenu.solicred.cmdUpdate==1)?('inline'):('none');
    document.querySelector("#btnInsert").style.display = 'none';

    appSoliCredSetData(resp.tablaSoliCred,resp.tablaPers.persona);  //pestaña Solicitud de credito
    appPersonaSetData(resp.tablaPers); //pestaña Personales

    document.querySelector('#grid').style.display = 'none';
    document.querySelector('#edit').style.display = 'block';
  });
}

function appSoliCredSetData(data,txtSocio){
  //pestaña de solicred
  appLlenarDataEnComboBox(data.comboAgencias,"#cbo_SoliCredAgencia",data.agenciaID);
  appLlenarDataEnComboBox(data.comboEmpleados,"#cbo_SoliCredPromotor",data.promotorID);
  appLlenarDataEnComboBox(data.comboEmpleados,"#cbo_SoliCredAnalista",data.analistaID);
  appLlenarDataEnComboBox(data.comboProductos,"#cbo_SoliCredProducto",data.productoID);
  appLlenarDataEnComboBox(data.comboTipoSBS,"#cbo_SoliCredTipoSBS",data.tiposbsID);
  appLlenarDataEnComboBox(data.comboDestSBS,"#cbo_SoliCredDestSBS",data.destsbsID);
  appLlenarDataEnComboBox(data.comboClasifica,"#cbo_SoliCredClasifica",data.clasificaID);
  appLlenarDataEnComboBox(data.comboCondicion,"#cbo_SoliCredCondicion",data.condicionID);
  appLlenarDataEnComboBox(data.comboMoneda,"#cbo_SoliCredMoneda",data.monedaID);
  $("#cbo_SoliCredTipo").val(data.tipocredID);
  $("#txt_SoliCredFechaSolici").datepicker("setDate",moment(data.fecha_solicred).format("DD/MM/YYYY"));
  $("#txt_SoliCredFechaOtorga").datepicker("setDate",moment(data.fecha_otorga).format("DD/MM/YYYY"));
  $("#txt_SoliCredFechaPriCuota").datepicker("setDate",moment(data.fecha_pricuota).format("DD/MM/YYYY"));
  $("#txt_SoliCredFrecuencia").val(data.frecuencia);
  $('#txt_SoliCredFechaOtorga').datepicker().on('changeDate', function(e) { appSoliCredUpdatePriCuotaByFechaOtorga(); });
  $('#txt_SoliCredFrecuencia').on('input', function(e) { appSoliCredUpdatePriCuotaByFrecuencia(); });
  $("#txt_SoliCredFrecuencia").val(data.frecuencia);
  appSoliCredCambiarTipoCredito();
  
  document.querySelector('#hid_SoliCredID').value = (data.ID);
  document.querySelector('#txt_SoliCredSocio').value = (txtSocio);
  document.querySelector("#txt_SoliCredCodigo").value = (data.codigo);
  document.querySelector("#txt_SoliCredImporte").value = (Number(data.importe).toFixed(2));
  document.querySelector("#txt_SoliCredTasa").value = (Number(data.tasa).toFixed(2));
  document.querySelector("#txt_SoliCredMora").value = (Number(data.mora).toFixed(2));
  document.querySelector("#txt_SoliCredSegDesgr").value = (Number(data.desgr).toFixed(2));
  document.querySelector("#txt_SoliCredNroCuotas").value = (data.nrocuotas);
  document.querySelector("#txt_SoliCredCuota").value = (data.cuota);
  document.querySelector("#txt_SoliCredObserv").value = (data.observac);
}

function appSoliCredClear(txtSocio){
  let datos = {
    TipoQuery : 'newSoliCred'
  }

  //todos los inputs sin error y panel error deshabilitado
  $('.form-group').removeClass('has-error');
    
  //tabs default en primer tab
  $('.nav-tabs li').removeClass('active');
  $('.tab-content .tab-pane').removeClass('active');
  $('a[href="#datosSoliCred"]').closest('li').addClass('active');
  $('#datosSoliCred').addClass('active');
  document.querySelector("#btnUpdate").style.display = 'none';
  document.querySelector("#btnInsert").style.display = 'inline';
  
  appFetch(datos,rutaSQL).then(resp => {
    appLlenarDataEnComboBox(resp.comboAgencias,"#cbo_SoliCredAgencia",0);
    appLlenarDataEnComboBox(resp.comboEmpleados,"#cbo_SoliCredPromotor",0);
    appLlenarDataEnComboBox(resp.comboEmpleados,"#cbo_SoliCredAnalista",0);
    appLlenarDataEnComboBox(resp.comboProductos,"#cbo_SoliCredProducto",0);
    appLlenarDataEnComboBox(resp.comboTipoSBS,"#cbo_SoliCredTipoSBS",0);
    appLlenarDataEnComboBox(resp.comboDestSBS,"#cbo_SoliCredDestSBS",0);
    appLlenarDataEnComboBox(resp.comboClasifica,"#cbo_SoliCredClasifica",131);
    appLlenarDataEnComboBox(resp.comboCondicion,"#cbo_SoliCredCondicion",141);
    appLlenarDataEnComboBox(resp.comboMoneda,"#cbo_SoliCredMoneda",0);
    
    document.querySelector("#hid_SoliCredID").value = (0);
    document.querySelector("#txt_SoliCredSocio").value = (txtSocio);
    document.querySelector("#txt_SoliCredFechaSolici").disabled = (resp.rolUser==resp.rolROOT) ? (false):(true);
    document.querySelector("#txt_SoliCredCodigo").value = ("");
    document.querySelector("#txt_SoliCredImporte").value = ("100.00");
    document.querySelector("#txt_SoliCredTasa").value = ("100.00");
    document.querySelector("#txt_SoliCredMora").value = ("100.00");
    document.querySelector("#txt_SoliCredSegDesgr").value = ("0.1");
    document.querySelector("#txt_SoliCredNroCuotas").value = ("12");
    document.querySelector("#txt_SoliCredFrecuencia").value = ("");
    document.querySelector("#txt_SoliCredObserv").value = ("");

    $("#txt_SoliCredFechaSolici").datepicker("setDate",moment(resp.fecha).format("DD/MM/YYYY"));
    $('#txt_SoliCredFechaOtorga').datepicker("setDate",moment(resp.fecha).format("DD/MM/YYYY"));
    $('#txt_SoliCredFechaOtorga').datepicker().on('changeDate', function(e) { appSoliCredUpdatePriCuotaByFechaOtorga(); });
    $('#txt_SoliCredFechaPriCuota').datepicker("setDate",moment(resp.fecha).add(1,'M').format("DD/MM/YYYY"));
    $('#txt_SoliCredFrecuencia').on('input', function(e) { appSoliCredUpdatePriCuotaByFrecuencia(); });
  });
}

function appSoliCredCambiarTipoCredito(){
  switch(document.querySelector("#cbo_SoliCredTipo").value){
    case "1":
      document.querySelector("#txt_SoliCredFechaPriCuota").disabled = false;
      document.querySelector("#txt_SoliCredFrecuencia").disabled = true;
      document.querySelector("#txt_SoliCredFrecuencia").value = "";
      appSoliCredUpdatePriCuotaByFechaOtorga();
      break;
    case "2":
      document.querySelector("#txt_SoliCredFechaPriCuota").disabled = true;
      document.querySelector("#txt_SoliCredFrecuencia").disabled = false;
      document.querySelector("#txt_SoliCredFrecuencia").value = 14;
      appSoliCredUpdatePriCuotaByFrecuencia();
      break;
  }
}

function appSoliCredUpdatePriCuotaByFechaOtorga(){
  if(document.querySelector("#cbo_SoliCredTipo").value==1){ //fecha fija
    let fecha = appConvertToFecha(document.querySelector("#txt_SoliCredFechaOtorga").value,'-');
    $('#txt_SoliCredFechaPriCuota').datepicker("setDate",moment(fecha).add(1,'M').format("DD/MM/YYYY"));
    document.querySelector('#txt_SoliCredCuota').value = "";
  } else {
    appSoliCredUpdatePriCuotaByFrecuencia();
  }
}

function appSoliCredUpdatePriCuotaByFrecuencia(){
  if(document.querySelector("#cbo_SoliCredTipo").value==2){ //plazo fijo
    let fecha = appConvertToFecha(document.querySelector("#txt_SoliCredFechaOtorga").value,'-');
    let frecuencia = document.querySelector("#txt_SoliCredFrecuencia").value;
    $('#txt_SoliCredFechaPriCuota').datepicker("setDate",moment(fecha).add(frecuencia,'d').format("DD/MM/YYYY"));
    document.querySelector('#txt_SoliCredCuota').value = "";
  }
}

function appSoliCredCambiarTipoSBS(){ 
  //se debe afinar 
  //aun no esta en produccion
  let datos = {
    TipoQuery : "cambiarTipoSBS",
    padreID : document.querySelector("#cbo_SoliCredTipoSBS").value
  }
  appFetch(datos,rutaSQL).then(resp => {
    //appLlenarDataEnComboBox(resp,"#cbo_SoliCredDestSBS",0); //destino SBS
  });
}

function appSoliCredGenerarPlanPagos(){
  if(appSoliCredValidarCampos()){
    alert("¡¡¡Faltan datos!!!");
    document.querySelector("#txt_SoliCredCuota").value = "";
  } else {
    let datos = {
      TipoQuery : "simulaCredito",
      TipoCredito : document.querySelector("#cbo_SoliCredTipo").value,
      importe : appConvertToNumero(document.querySelector("#txt_SoliCredImporte").value),
      TEA : appConvertToNumero(document.querySelector("#txt_SoliCredTasa").value),
      mora : appConvertToNumero(document.querySelector("#txt_SoliCredMora").value),
      segDesgr : appConvertToNumero(document.querySelector("#txt_SoliCredSegDesgr").value),
      nroCuotas: appConvertToNumero(document.querySelector("#txt_SoliCredNroCuotas").value),
      fecha : appConvertToFecha(document.querySelector("#txt_SoliCredFechaOtorga").value,""),
      pricuota : appConvertToFecha(document.querySelector("#txt_SoliCredFechaPriCuota").value,""),
      frecuencia : appConvertToNumero(document.querySelector("#txt_SoliCredFrecuencia").value)
    }
  
    appFetch(datos,rutaSQL).then(resp => {
      if(resp!=undefined){
        document.querySelector("#txt_SoliCredCuota").value = resp.tabla.cuota;
      } else {
        alert("Sucedio un Error");
      }
    });
  }
}

function appSoliCredValidarCampos(){
  let esError = false;
  $('.form-group').removeClass('has-error');
  if(document.querySelector("#txt_SoliCredImporte").value=="") { document.querySelector("#div_SoliCredImporte").className = "form-group has-error"; esError = true; }
  if(document.querySelector("#txt_SoliCredNroCuotas").value=="")  { document.querySelector("#div_SoliCredNroCuotas").className = "form-group has-error"; esError = true; }
  if(document.querySelector("#txt_SoliCredTasa").value=="")  { document.querySelector("#div_SoliCredTasa").className = "form-group has-error"; esError = true; }
  if(document.querySelector("#txt_SoliCredMora").value=="")  { document.querySelector("#div_SoliCredMora").className = "form-group has-error"; esError = true; }
  if(document.querySelector("#txt_SoliCredSegDesgr").value=="")  { document.querySelector("#div_SoliCredSegDesgr").className = "form-group has-error"; esError = true; }
  if(document.querySelector("#txt_SoliCredFechaOtorga").value=="")  { document.querySelector("#div_SoliCredFechaOtorga").className = "form-group has-error"; esError = true; }
  if(document.querySelector("#txt_SoliCredFechaPriCuota").value=="")  { document.querySelector("#div_SoliCredFechaPriCuota").className = "form-group has-error"; esError = true; }
  if(document.querySelector("#cbo_SoliCredTipo").value==2 && document.querySelector("#txt_SoliCredFrecuencia").value=="")  { document.querySelector("#div_SoliCredFrecuencia").className = "form-group has-error"; esError = true; }
  return esError;
}

function appSoliCredGetDatosToDatabase(){
  let rpta = {
    TipoQuery : "execSoliCred",
    TipoExec : null,
    ID : document.querySelector('#hid_SoliCredID').value,
    socioID : document.querySelector("#hid_PersID").value,
    agenciaID : document.querySelector("#cbo_SoliCredAgencia").value,
    promotorID : document.querySelector("#cbo_SoliCredPromotor").value,
    analistaID : document.querySelector("#cbo_SoliCredAnalista").value,
    productoID : document.querySelector("#cbo_SoliCredProducto").value,
    tiposbsID : document.querySelector("#cbo_SoliCredTipoSBS").value,
    destsbsID : document.querySelector("#cbo_SoliCredDestSBS").value,
    clasificaID : document.querySelector("#cbo_SoliCredClasifica").value,
    condicionID : document.querySelector("#cbo_SoliCredCondicion").value,
    monedaID : document.querySelector("#cbo_SoliCredMoneda").value,
    importe : appConvertToNumero(document.querySelector("#txt_SoliCredImporte").value),
    saldo : appConvertToNumero(document.querySelector("#txt_SoliCredImporte").value),
    tasa : appConvertToNumero(document.querySelector("#txt_SoliCredTasa").value),
    mora : appConvertToNumero(document.querySelector("#txt_SoliCredMora").value),
    desgr : appConvertToNumero(document.querySelector("#txt_SoliCredSegDesgr").value),
    nrocuotas : appConvertToNumero(document.querySelector("#txt_SoliCredNroCuotas").value),
    fecha_solicred : appConvertToFecha(document.querySelector("#txt_SoliCredFechaSolici").value),
    fecha_otorga : appConvertToFecha(document.querySelector("#txt_SoliCredFechaOtorga").value),
    fecha_pricuota : appConvertToFecha(document.querySelector("#txt_SoliCredFechaPriCuota").value),
    frecuencia : appConvertToNumero(document.querySelector("#txt_SoliCredFrecuencia").value),
    tipocredID : document.querySelector("#cbo_SoliCredTipo").value,
    observac : document.querySelector("#txt_SoliCredObserv").value
  }
  return rpta;
}

function appPersonaSetData(data){
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

function modAprueba_BotonAprobar(){
  if(confirm("¿Esta seguro de continuar?")) {
    let datos = {
      ID : document.querySelector("#hid_modApruebaID").value,
      FechaAprueba : appConvertToFecha(document.querySelector("#txt_modApruebaFechaAprueba").value),
      TipoQuery : "aprobarSoliCred",
      TipoExec : "APRU" //aprueba solicitud de credito
    }
    appFetch(datos,rutaSQL).then(resp => {
      if (!resp.error) { 
        appSoliCredBotonCancel();
        $("#modalAprueba").modal("hide");
      }
    });
  }
}