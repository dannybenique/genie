const rutaSQL = "pages/mtto/alumnos/sql.php";
var menu = null;
var alumno = null;

//=========================funciones para Personas============================
async function appAlumnosGrid(){
  //precarga
  document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="8"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  const txtBuscar = document.querySelector("#txtBuscar").value.toUpperCase();
  try{
    const disabledDelete = (menu.mtto.submenu.alumnos.cmdDelete===1) ? "" : "disabled";
    const resp = await appAsynFetch({ 
      TipoQuery: 'alumno_sel', 
      buscar: txtBuscar,
      verTodos: document.querySelector("#hidViewAll").value 
    },rutaSQL);
    //respuesta
    document.querySelector("#chk_All").disabled = (menu.mtto.submenu.alumnos.cmdDelete===1) ? false : true;
    if(resp.tabla.length>0){
      let fila = "";
      resp.tabla.forEach((valor,key)=>{
        fila += '<tr style="'+((valor.estado==0)?('color:#bfbfbf;'):(''))+'">' +
                '<td><input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'" '+disabledDelete+'/></td>'+
                '<td>'+(valor.codigo)+'</td>'+
                '<td>'+(moment(valor.fecha).format("DD/MM/YYYY"))+'</td>'+
                '<td>'+(valor.nro_dui)+'</td>'+
                '<td><a href="javascript:appAlumnoView('+(valor.ID)+');" title="'+(valor.ID)+'" style="'+((valor.estado==0)?('color:#bfbfbf;'):(''))+'">'+(valor.alumno)+'</a></td>'+
                '<td>'+(valor.direccion)+'</td>'+
                '</tr>';
      });
      document.querySelector('#grdDatos').innerHTML = (fila);
    } else {
      let res = (txtBuscar==="") ? ("") : ("para "+txtBuscar);
      document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="8" style="text-align:center;color:red;">Sin Resultados '+(res)+'</td></tr>');
    }
    document.querySelector('#grdCount').innerHTML = (resp.tabla.length+"/"+resp.cuenta);
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appAlumnosReset(){
  try{
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    menu = JSON.parse(resp.menu);
    alumno = {
      ID : null,
      padre : null,
      madre : null,
      apodera : null
    };
    document.querySelector("#btn_DEL").style.display = (menu.mtto.submenu.alumnos.cmdDelete==1)?('inline'):('none');
    document.querySelector("#btn_NEW").style.display = (menu.mtto.submenu.alumnos.cmdInsert==1)?('inline'):('none');

    document.querySelector("#txtBuscar").value = ("");
    document.querySelector("#grdDatos").innerHTML = ("");
    document.querySelector("#div_PersAuditoria").style.display = ((resp.rolID==101)?('block'):('none'));
    appAlumnosGrid();
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appAlumnosBuscar(e){
  if(e.keyCode === 13) { appAlumnosGrid(); }
}

function appAlumnosBotonViewAll(){ //mostrar inclusive los alumnos eliminados
  document.querySelector("#icoViewAll").classList.toggle("fa-toggle-on");
  document.querySelector("#icoViewAll").classList.toggle("fa-toggle-off");
  document.querySelector("#hidViewAll").value = (document.querySelector("#hidViewAll").value==1) ? (0) : (1);
  appAlumnosGrid();
}

function appAlumnosBotonCancel(){
  appAlumnosGrid();
  document.querySelector("#grid").style.display = 'block';
  document.querySelector("#edit").style.display = 'none';
}

async function appAlumnosBotonInsert(){
  const datos = appAlumnoGetDatosToDatabase();
  
  if(datos!=""){
    try{
      datos.TipoQuery = "alumno_ins";
      const resp = await appAsynFetch(datos,rutaSQL);
      if(!resp.error) {appAlumnosBotonCancel();}
    } catch(err){
      console.error('Error al cargar datos:', err);
    }
  }
}

async function appAlumnosBotonUpdate(){
  const datos = appAlumnoGetDatosToDatabase();

  if(datos!=""){
    try{
      datos.TipoQuery = "alumno_upd";
      const resp = await appAsynFetch(datos,rutaSQL);
      if(!resp.error) {appAlumnosBotonCancel();}
    } catch(err){
      console.error('Error al cargar datos:', err);
    }
  }
}

function appAlumnosBotonNuevo(){
  Persona.openBuscar('verifica_Alumno',rutaSQL,true,true,false);
  $('#btn_modPersInsert').off('click').on('click',handlerAlumnosInsert_Click);
  $('#btn_modPersAddToForm').off('click').on('click',handlerAlumnosAddToForm_Click);
}

async function handlerAlumnosInsert_Click(e){
  if(Persona.sinErrores()){
    document.querySelector('#grid').style.display = 'none';
    document.querySelector('#edit').style.display = 'block';
    try{
      const resp = await Persona.ejecutaSQL();
      
      if (!resp.error) {
        appPersonaSetData(resp.tablaPers);
        appAlumnoClear();
        Persona.close();
        alumno.ID = resp.tablaPers.ID;
        e.stopImmediatePropagation();
        $('#btn_modPersInsert').off('click');
      }
    } catch(err){
      console.error('Error al cargar datos:', err);
    }
  } else {
    alert("!!!Faltan llenar Datos en Personas!!!");
  }
}

function handlerAlumnosAddToForm_Click(e){
  document.querySelector('#grid').style.display = 'none';
  document.querySelector('#edit').style.display = 'block';
  appPersonaSetData(Persona.tablaPers); //pestaña Personales
  alumno.ID = Persona.personaID;
  appAlumnoClear();
  Persona.close();
  e.stopImmediatePropagation();
  $('#btn_modPersAddToForm').off('click');
}

async function appAlumnosBotonBorrar(){
  let arr = Array.from(document.querySelectorAll('[name="chk_Borrar"]:checked')).map(function(obj){return obj.attributes[2].nodeValue});
  if(arr.length>0){
    if(confirm("¿Esta seguro de continuar?")) {
      try{
        const resp = await appAsynFetch({ TipoQuery:'alumno_del', arr:arr },rutaSQL);
        if (!resp.error) { appAlumnosBotonCancel(); }
      } catch(err){
        console.error('Error al cargar datos:', err);
      }
    }
  } else {
    alert("NO eligio borrar ninguno");
  }
}

async function appAlumnosBotonEstado(){
  try{
    const resp = await appAsynFetch({
      TipoQuery : 'alumno_add', //quitar el soft delete (estado)
      alumnoID : document.querySelector("#lbl_ID").innerHTML
    },rutaSQL);
    if(!resp.error){document.querySelector("#div_Estado").innerHTML = "";}
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appAlumnoView(personaID){
  //tabs default en primer tab
  $('.nav-tabs li').removeClass('active');
  $('.tab-content .tab-pane').removeClass('active');
  $('a[href="#datosAlumno"]').closest('li').addClass('active');
  $('#datosAlumno').addClass('active');
  document.querySelector("#div_AlumnoAuditoria").style.display = 'block';
  document.querySelector("#btnUpdate").style.display = (menu.mtto.submenu.alumnos.cmdUpdate==1)?('inline'):('none');
  document.querySelector("#btnInsert").style.display = 'none';

  try{
    const resp = await appAsynFetch({
      TipoQuery : 'alumno_view',
      personaID : personaID,
      fullQuery : 2
    }, rutaSQL);
    //respuesta
    appAlumnoSetData(resp.tablaAlumno);  //pestaña Alumno
    appPersonaSetData(resp.tablaPers); //pestaña Personales
    
    document.querySelector('#grid').style.display = 'none';
    document.querySelector('#edit').style.display = 'block';
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appAlumnoSetData(data){
  //info corta
  document.querySelector("#lbl_Codigo").innerHTML = (data.codigo);
  if (data.estado==0) { document.querySelector("#div_Estado").innerHTML = '<button type="button" class="btn btn-success pull-right" onclick="javascript:appAlumnosBotonEstado();"><i class="fa fa-flash"></i> Habilitar Alumno</button>'; }

  //pestaña de Alumno
  document.querySelector('#txt_AlumnoFechaIng').value = (moment(data.fecha).format("DD/MM/YYYY"));
  document.querySelector("#txt_AlumnoCodigo").value = (data.codigo);

  document.querySelector("#hid_alumnoFamiPadreID").value = data.pdID
  document.querySelector("#lbl_alumnoFamiPadreNombre").innerHTML = data.pd_nombre
  document.querySelector("#lbl_alumnoFamiPadreDNI").innerHTML = data.pd_nrodni
  document.querySelector("#lbl_alumnoFamiPadreDireccion").innerHTML = data.pd_direccion

  document.querySelector("#hid_alumnoFamiMadreID").value = data.mdID
  document.querySelector("#lbl_alumnoFamiMadreNombre").innerHTML = data.md_nombre
  document.querySelector("#lbl_alumnoFamiMadreDNI").innerHTML = data.md_nrodni
  document.querySelector("#lbl_alumnoFamiMadreDireccion").innerHTML = data.md_direccion

  document.querySelector("#hid_alumnoFamiApoderaID").value = data.apID
  document.querySelector("#lbl_alumnoFamiApoderaNombre").innerHTML = data.ap_nombre
  document.querySelector("#lbl_alumnoFamiApoderaDNI").innerHTML = data.ap_nrodni
  document.querySelector("#lbl_alumnoFamiApoderaDireccion").innerHTML = data.ap_direccion

  document.querySelector("#lbl_AlumnoSysFecha").innerHTML = (moment(data.sys_fecha).format("DD/MM/YYYY"));
  document.querySelector("#lbl_AlumnoSysUser").innerHTML = (data.usermod);
}

async function appAlumnoClear(){
  //tabs default en primer tab
  $('.nav-tabs li').removeClass('active');
  $('.tab-content .tab-pane').removeClass('active');
  $('a[href="#datosAlumno"]').closest('li').addClass('active');
  $('#datosAlumno').addClass('active');

  //todos los inputs sin error y panel error deshabilitado
  $('.form-group').removeClass('has-error');
  document.querySelector("#div_AlumnoAuditoria").style.display = 'none';
  document.querySelector("#btnInsert").style.display = (menu.mtto.submenu.alumnos.cmdInsert==1)?('inline'):('none');
  document.querySelector("#btnUpdate").style.display = 'none';
  try{
    const resp = await appAsynFetch({ TipoQuery : 'alumno_start' },rutaSQL); 
    
    //pestaña de Alumno
    document.querySelector('#txt_AlumnoFechaIng').value = (moment(resp.fecha).format("DD/MM/YYYY"));
    document.querySelector("#txt_AlumnoCodigo").placeholder = ("00-000000");
    document.querySelector("#txt_AlumnoCodigo").value = ("");

    document.querySelector("#lbl_alumnoFamiPadreNombre").innerHTML = ""
    document.querySelector("#lbl_alumnoFamiPadreDNI").innerHTML = ""
    document.querySelector("#lbl_alumnoFamiPadreDireccion").innerHTML = ""

    document.querySelector("#lbl_alumnoFamiMadreNombre").innerHTML = ""
    document.querySelector("#lbl_alumnoFamiMadreDNI").innerHTML = ""
    document.querySelector("#lbl_alumnoFamiMadreDireccion").innerHTML = ""

    document.querySelector("#lbl_alumnoFamiApoderaNombre").innerHTML = ""
    document.querySelector("#lbl_alumnoFamiApoderaDNI").innerHTML = ""
    document.querySelector("#lbl_alumnoFamiApoderaDireccion").innerHTML = ""
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}


//familiar padre
function appFamiPadreAdd(){
  Persona.openBuscar('verifica_Padre',rutaSQL,true,true,false);
  $('#btn_modPersInsert').off('click').on('click',handlerFamiPadreInsert_Click);
  $('#btn_modPersAddToForm').off('click').on('click',handlerFamiPadreAddToForm_Click);
}
async function handlerFamiPadreInsert_Click(e){
  if(Persona.sinErrores()){
    try{
      const resp = await Persona.ejecutaSQL();
      
      if(!resp.error){
        alumno.padre = resp.tablaPers;
        appFamiPadreSetData(alumno.padre);
        Persona.close();
        e.stopImmediatePropagation();
        $('#btn_modPersInsert').off('click');
      }
    } catch(err){
      console.error('Error al cargar datos:', err);
    }
  } else {
    alert("!!!Faltan llenar Datos!!!");
  }
}
function handlerFamiPadreAddToForm_Click(e){
  alumno.padre = Persona.tablaPers;
  appFamiPadreSetData(alumno.padre);
  Persona.close();
  e.stopImmediatePropagation();
  $('#btn_modPersAddToForm').off('click');
}
function appFamiPadreSetData(data){
  document.querySelector("#lbl_alumnoFamiPadreNombre").innerHTML = data.ap_paterno + " " + data.ap_materno + ", " + data.nombres;
  document.querySelector("#lbl_alumnoFamiPadreDNI").innerHTML = "DNI: " + data.nroDUI
  document.querySelector("#lbl_alumnoFamiPadreDireccion").innerHTML = "Direccion: " + data.direccion
}


//familiar madre
function appFamiMadreAdd(){
  Persona.openBuscar('verifica_Madre',rutaSQL,true,true,false);
  $('#btn_modPersInsert').off('click').on('click',handlerFamiMadreInsert_Click);
  $('#btn_modPersAddToForm').off('click').on('click',handlerFamiMadreAddToForm_Click);
}
async function handlerFamiMadreInsert_Click(e){
  if(Persona.sinErrores()){
    try{
      const resp = await Persona.ejecutaSQL();

      if(!resp.error){
        alumno.madre = resp.tablaPers;
        appFamiMadreSetData(alumno.madre);
        Persona.close();
        e.stopImmediatePropagation();
        $('#btn_modPersInsert').off('click');
      }
    } catch(err){
      console.error('Error al cargar datos:', err);
    }
  } else {
    alert("!!!Faltan llenar Datos!!!");
  }
}
function handlerFamiMadreAddToForm_Click(e){
  alumno.madre = Persona.tablaPers;
  appFamiMadreSetData(alumno.madre);
  Persona.close();
  e.stopImmediatePropagation();
  $('#btn_modPersAddToForm').off('click');
}
function appFamiMadreSetData(data){
  document.querySelector("#lbl_alumnoFamiMadreNombre").innerHTML = data.ap_paterno + " " + data.ap_materno + ", " + data.nombres;
  document.querySelector("#lbl_alumnoFamiMadreDNI").innerHTML = "DNI: " + data.nroDUI
  document.querySelector("#lbl_alumnoFamiMadreDireccion").innerHTML = "Direccion: " + data.direccion
}

//familiar apoderado
function appFamiApoderaAdd(){
  Persona.openBuscar('verifica_Apodera',rutaSQL,true,true,false);
  $('#btn_modPersInsert').off('click').on('click',handlerFamiApoderaInsert_Click);
  $('#btn_modPersAddToForm').off('click').on('click',handlerFamiApoderaAddToForm_Click);
}
async function handlerFamiApoderaInsert_Click(e){
  if(Persona.sinErrores()){
    try{
      const resp = await Persona.ejecutaSQL();

      if(!resp.error){
        alumno.apodera = resp.tablaPers;
        appFamiApoderaSetData(alumno.apodera);
        Persona.close();
        e.stopImmediatePropagation();
        $('#btn_modPersInsert').off('click');
      }
    } catch(err){
      console.error('Error al cargar datos:', err);
    }
  } else {
    alert("!!!Faltan llenar Datos!!!");
  }
}
function handlerFamiApoderaAddToForm_Click(e){
  alumno.apodera = Persona.tablaPers;
  appFamiApoderaSetData(alumno.apodera);
  Persona.close();
  e.stopImmediatePropagation();
  $('#btn_modPersAddToForm').off('click');
}
function appFamiApoderaSetData(data){
  document.querySelector("#lbl_alumnoFamiApoderaNombre").innerHTML = data.ap_paterno + " " + data.ap_materno + ", " + data.nombres;
  document.querySelector("#lbl_alumnoFamiApoderaDNI").innerHTML = "DNI: " + data.nroDUI
  document.querySelector("#lbl_alumnoFamiApoderaDireccion").innerHTML = "Direccion: " + data.direccion
}


function appAlumnoGetDatosToDatabase(){
  let datos = "";
  let EsError = false;
  $('.form-group').removeClass('has-error');
  
  if(!EsError){
    datos = {
      ID : alumno.ID,
      codigo  : "",
      fecha   : appConvertToFecha(document.querySelector("#txt_AlumnoFechaIng").value,""),
      padreID : (alumno.padre==null) ? (null):(alumno.padre.ID),
      madreID : (alumno.madre==null) ? (null):(alumno.madre.ID),
      apoderaID : (alumno.apodera==null) ? (null):(alumno.apodera.ID),
      observac : ""
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

//otras funciones
async function appLinkFamiliar(tipo){
  try{
    let familiarID = 0;
    switch(tipo){
      case 1: familiarID = alumno.padre.ID; break; //padre
      case 2: familiarID = alumno.madre.ID; break; //madre
      case 3: familiarID = alumno.apodera.ID; break; //apoderado
    }
    const resp = await appAsynFetch({
      TipoQuery : "familiar_view",
      familiarID : familiarID,
      tipo : tipo
    },rutaSQL);

    //formulario modal de familiares
    document.querySelector("#lbl_modfamTitulo").innerHTML = "Datos Personales de "+resp.tipoFami.toUpperCase();
    document.querySelector("#lbl_modfamNombres").innerHTML = resp.tablaPers.nombres;
    document.querySelector("#lbl_modfamApellidos").innerHTML = resp.tablaPers.ap_paterno+" "+resp.tablaPers.ap_materno;
    document.querySelector("#lbl_modfamNroDNI").innerHTML = resp.tablaPers.nroDUI;
    document.querySelector("#lbl_modfamPaisNac").innerHTML = resp.tablaPers.paisnac;
    document.querySelector("#lbl_modfamLugarNac").innerHTML = resp.tablaPers.lugarnac;
    document.querySelector("#lbl_modfamFechaNac").innerHTML = moment(resp.tablaPers.fechanac).format("DD/MM/YYYY");
    document.querySelector("#lbl_modfamEdad").innerHTML = moment().diff(moment(resp.tablaPers.fechanac),"years")+" años";
    document.querySelector("#lbl_modfamSexo").innerHTML = resp.tablaPers.sexo;
    document.querySelector("#lbl_modfamEcivil").innerHTML = resp.tablaPers.ecivil;
    document.querySelector("#lbl_modfamCelular").innerHTML = resp.tablaPers.celular;
    document.querySelector("#lbl_modfamTelefijo").innerHTML = resp.tablaPers.telefijo;
    document.querySelector("#lbl_modfamEmail").innerHTML = resp.tablaPers.correo;
    document.querySelector("#lbl_modfamGInstruccion").innerHTML = resp.tablaPers.ginstruc;
    document.querySelector("#lbl_modfamProfesion").innerHTML = resp.tablaPers.profesion;
    document.querySelector("#lbl_modfamOcupacion").innerHTML = resp.tablaPers.ocupacion;
    document.querySelector("#lbl_modfamUbicacion").innerHTML = resp.tablaPers.region+" - "+resp.tablaPers.provincia+" - "+resp.tablaPers.distrito;
    document.querySelector("#lbl_modfamDireccion").innerHTML = resp.tablaPers.direccion;
    document.querySelector("#lbl_modfamReferencia").innerHTML = resp.tablaPers.referencia;
    document.querySelector("#lbl_modfamMedidorluz").innerHTML = resp.tablaPers.medidorluz;
    document.querySelector("#lbl_modfamMedidorAgua").innerHTML = resp.tablaPers.medidoragua;
    document.querySelector("#lbl_modfamTipovivienda").innerHTML = resp.tablaPers.tipovivienda;
    document.querySelector("#lbl_modfamObservac").innerHTML = resp.tablaPers.observPers;

    $("#modalFamiliar").modal("show");
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

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

async function appPermisoPersonas(){ //falta corregir
  // const datos = { TipoQuery:'insNotifi', tabla:'tb_personas', personaID:$("#lbl_ID").html() }
  // appAjaxInsert(datos,"pages/global/notifi/sql.php").done(function(resp){
  //   if(!resp.error){ $("#btn_PersPermiso").hide(); }
  //   else { alert("!!!Hubo un error... "+(resp.mensaje)+"!!!"); }
  // });
}
