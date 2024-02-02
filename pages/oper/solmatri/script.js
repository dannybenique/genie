const rutaSQL = "pages/oper/solmatri/sql.php";
var menu = "";

//=========================funciones para Personas============================
function appSolMatriGrid(){
  document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="10"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  let txtBuscar = document.querySelector("#txtBuscar").value;
  let datos = {
    TipoQuery: 'selSolMatri',
    buscar: txtBuscar
  };

  appFetch(datos,rutaSQL).then(resp => {
    let disabledDelete = (menu.oper.submenu.solmatri.cmdDelete===1) ? "" : "disabled";
    document.querySelector("#chk_All").disabled = (menu.oper.submenu.solmatri.cmdDelete===1) ? false : true;
    if(resp.tabla.length>0){
      let fila = "";
      resp.tabla.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td><input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'" '+disabledDelete+'/></td>'+
                '<td>'+((menu.oper.submenu.solmatri.aprueba===1)?('<a href="javascript:appSolMatriAprueba('+(valor.ID)+');"><i class="fa fa-thumbs-up" style="color:#FF0084;"></i></a>'):(''))+'</td>'+
                '<td style="text-align:center;"><a href="javascript:appSolMatriView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.codigo)+'</a></td>'+
                '<td>'+(moment(valor.fecha_solmatri).format("DD/MM/YYYY"))+'</td>'+
                '<td>'+(valor.nro_dui)+'</td>'+
                '<td>'+(valor.alumno)+'</td>'+
                '<td>'+(valor.nivel)+' &raquo; '+(valor.grado)+' &raquo; '+(valor.seccion)+'</td>'+
                '</tr>';
      });
      $('#grdDatos').html(fila);
    } else {
      let res = (txtBuscar==="") ? ("") : ("para "+txtBuscar);
      $('#grdDatos').html('<tr><td colspan="10" style="text-align:center;color:red;">Sin Resultados '+(res)+'</td></tr>');
    }
    $('#grdCount').html(resp.tabla.length+"/"+resp.cuenta);
  });
}

function appSolMatriReset(){
  appFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php").then(resp => {
    menu = JSON.parse(resp.menu);
    document.querySelector("#btn_DEL").style.display = (menu.oper.submenu.solmatri.cmdDelete==1)?('inline'):('none');
    document.querySelector("#btn_NEW").style.display = (menu.oper.submenu.solmatri.cmdInsert==1)?('inline'):('none');

    document.querySelector("#txtBuscar").value = ("");
    document.querySelector("#grdDatos").innerHTML = ("");
    appSolMatriGrid();
  });
}

function appSolMatriBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { load_flag = 0; $('#grdDatosBody').html(""); appSolMatriGrid(); }
}

function appSolMatriBotonCancel(){
  appSolMatriGrid();
  $('#grid').show();
  $('#edit').hide();
}

function appSolMatriBotonInsert(){
  if(appSolMatriValidarCampos()){
    alert("¡¡¡Faltan datos!!!");
  } else {
    let datos = appSolMatriGetDatosToDatabase();
    datos.TipoExec = "INS";
    appFetch(datos,rutaSQL).then(resp => {
      appSolMatriBotonCancel();
    });
  }
}

function appSolMatriBotonUpdate(){
  if(appSolMatriValidarCampos()){
    alert("¡¡¡Faltan datos!!!");
  } else {
    let datos = appSolMatriGetDatosToDatabase();
    datos.TipoExec = "UPD";
    appFetch(datos,rutaSQL).then(resp => {
      appSolMatriBotonCancel();
    });
  }
}

function appSolMatriBotonNuevo(){
  Persona.openBuscar('VerifySolMatri',rutaSQL,false,true,true);
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

function appSolMatriBotonBorrar(){
  let arr = Array.from(document.querySelectorAll('[name="chk_Borrar"]:checked')).map(function(obj){return obj.attributes[2].nodeValue});
  if(arr.length>0){
    if(confirm("¿Esta seguro de continuar?")) {
      appFetch({ TipoQuery:'delSolMatri', arr:arr },rutaSQL).then(resp => {
        console.log(resp);
        if (!resp.error) { appSolMatriBotonCancel(); }
      });
    }
  } else {
    alert("NO eligio borrar ninguno");
  }
}

function appSolMatriAprueba(solmatriID){
  $("#modalAprueba").modal("show");
  let datos = {
    TipoQuery: 'viewApruebaSolMatri',
    SolMatriID: solmatriID
  }
  appFetch(datos,rutaSQL).then(resp => {
    document.querySelector("#txt_modApruebaFechaAprueba").disabled = (resp.rolUser==resp.rolROOT) ? (false):(true);
    $("#txt_modApruebaFechaAprueba").datepicker("setDate",moment(resp.fecha_aprueba).format("DD/MM/YYYY"));

    document.querySelector("#hid_modApruebaID").value = (resp.ID);
    document.querySelector("#lbl_modApruebaSocio").innerHTML = (resp.socio);
    document.querySelector("#lbl_modApruebaFechaSolMatri").innerHTML = (moment(resp.fecha_solicred).format("DD/MM/YYYY"));
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

function appSolMatriView(matriculaID){
  let datos = {
    TipoQuery : 'viewSolMatri',
    matriculaID : matriculaID
  };
  
  //tabs default en primer tab
  $('.nav-tabs li').removeClass('active');
  $('.tab-content .tab-pane').removeClass('active');
  $('a[href="#datosSolMatri"]').closest('li').addClass('active');
  $('#datosSolMatri').addClass('active');
  appFetch(datos,rutaSQL).then(resp => {
    document.querySelector("#btnUpdate").style.display = (menu.oper.submenu.solmatri.cmdUpdate==1)?('inline'):('none');
    document.querySelector("#btnInsert").style.display = 'none';
    resp.tablaSolMatri.persona = resp.tablaPers.persona;
    appSolMatriSetData(resp.tablaSolMatri);  //pestaña Solicitud de Matricula
    appPersonaSetData(resp.tablaPers); //pestaña Personales

    document.querySelector('#grid').style.display = 'none';
    document.querySelector('#edit').style.display = 'block';
  });
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
  document.querySelector("#txt_SolMatriObservac").value = (data.observac);
  $("#txt_SolMatriFechaSolicita").datepicker("setDate",moment(data.fecha).format("DD/MM/YYYY"));
  
  document.querySelector("#btnUpdate").style.display = 'none';
  document.querySelector("#btnInsert").style.display = 'inline';
}

function appSolMatriClear(txtSocio){
  let datos = {
    TipoQuery : 'newSolMatri'
  }

  //todos los inputs sin error y panel error deshabilitado
  $('.form-group').removeClass('has-error');
    
  //tabs default en primer tab
  $('.nav-tabs li').removeClass('active');
  $('.tab-content .tab-pane').removeClass('active');
  $('a[href="#datosSolMatri"]').closest('li').addClass('active');
  $('#datosSolMatri').addClass('active');
  document.querySelector("#btnUpdate").style.display = 'none';
  document.querySelector("#btnInsert").style.display = 'inline';
  
  appFetch(datos,rutaSQL).then(resp => {
    appLlenarDataEnComboBox(resp.comboNiveles,"#cbo_SolMatriNiveles",2); //seteado a primaria
    appLlenarDataEnComboBox(resp.comboGrados,"#cbo_SolMatriGrados",0); //prmer grado
    appLlenarDataEnComboBox(resp.comboSecciones,"#cbo_SolMatriSecciones",0); //seccion A
    
    document.querySelector("#hid_SolMatriID").value = (0);
    document.querySelector("#txt_SolMatriAlumno").value = (txtSocio);
    document.querySelector("#txt_SolMatriFechaSolicita").disabled = (resp.rolUser==resp.rolROOT) ? (false):(true);
    document.querySelector("#txt_SolMatriCodigo").value = ("");
    document.querySelector("#txt_SolMatriObservac").value = ("");

    $("#txt_SolMatriFechaSolicita").datepicker("setDate",moment(resp.fecha).format("DD/MM/YYYY"));
  });
}

function appSolMatriValidarCampos(){
  let esError = false;
  $('.form-group').removeClass('has-error');
  if(document.querySelector("#txt_SolMatriFechaSolicita").value=="")  { document.querySelector("#div_SolMatriFechaSolicita").className = "form-group has-error"; esError = true; }
  return esError;
}

function appSolMatriGetDatosToDatabase(){
  let rpta = {
    TipoQuery : "execSolMatri",
    TipoExec : null,
    ID : document.querySelector('#hid_SolMatriID').value,
    alumnoID : document.querySelector("#hid_PersID").value,
    seccionID : document.querySelector("#cbo_SolMatriSecciones").value,
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
function comboGrados(){
  let datos = {
    TipoQuery : "comboNivel",
    tipoID  : 3, //grados
    padreID : document.querySelector("#cbo_SolMatriNiveles").value
  }
  appFetch(datos,rutaSQL).then(resp => {
    appLlenarDataEnComboBox(resp.grados,"#cbo_SolMatriGrados",0); //grados
    appLlenarDataEnComboBox(resp.secciones,"#cbo_SolMatriSecciones",0); //secciones
  });
}

function comboSecciones(){
  let datos = {
    TipoQuery : "comboNivel",
    tipoID  : 4, //secciones
    padreID : document.querySelector("#cbo_SolMatriGrados").value
  }
  appFetch(datos,rutaSQL).then(resp => {
    appLlenarDataEnComboBox(resp.secciones,"#cbo_SolMatriSecciones",0); //secciones
  });
}

//modal
function modAprueba_BotonAprobar(){
  if(confirm("¿Esta seguro de continuar?")) {
    let datos = {
      ID : document.querySelector("#hid_modApruebaID").value,
      FechaAprueba : appConvertToFecha(document.querySelector("#txt_modApruebaFechaAprueba").value),
      TipoQuery : "aprobarSolMatri",
      TipoExec : "APRU" //aprueba solicitud de credito
    }
    appFetch(datos,rutaSQL).then(resp => {
      if (!resp.error) { 
        appSolMatriBotonCancel();
        $("#modalAprueba").modal("hide");
      }
    });
  }
}