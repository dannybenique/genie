const rutaSQL = "pages/mtto/alumnos/sql.php";
var menu = null;

//=========================funciones para Personas============================
async function appAlumnosGrid(){
  //precarga
  document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="8"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  const txtBuscar = document.querySelector("#txtBuscar").value.toUpperCase();
  try{
    const disabledDelete = (menu.mtto.submenu.alumnos.cmdDelete===1) ? "" : "disabled";
    const resp = await appAsynFetch({ 
      TipoQuery: 'selAlumnos', 
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
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { appAlumnosGrid(); }
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
      datos.TipoQuery = "insAlumno";
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
      datos.TipoQuery = "updAlumno";
      const resp = await appAsynFetch(datos,rutaSQL);
      if(!resp.error) {appAlumnosBotonCancel();}
    } catch(err){
      console.error('Error al cargar datos:', err);
    }
  }
}

function appAlumnosBotonNuevo(){
  Persona.openBuscar('VerifyAlumno',rutaSQL,true,true,false);
  $('#btn_modPersInsert').off('click');
  
  $('#btn_modPersInsert').on('click',handlerAlumnosInsert_Click);
  $('#btn_modPersAddToForm').on('click',handlerAlumnosAddToForm_Click);
}

function appAlumnosBotonViewAll(){ //mostrar inclusive los alumnos eliminados
  document.querySelector("#icoViewAll").classList.toggle("fa-toggle-on");
  document.querySelector("#icoViewAll").classList.toggle("fa-toggle-off");
  document.querySelector("#hidViewAll").value = (document.querySelector("#hidViewAll").value==1) ? (0) : (1);
  appAlumnosGrid();
}

async function handlerAlumnosInsert_Click(e){
  if(Persona.sinErrores()){
    document.querySelector('#grid').style.display = 'none';
    document.querySelector('#edit').style.display = 'block';
    try{
      const resp = await Persona.ejecutaSQL();
      appPersonaSetData(resp.tablaPers);
      appAlumnoClear();
      Persona.close();
    } catch(err){
      console.error('Error al cargar datos:', err);
    }
  } else {
    alert("!!!Faltan llenar Datos!!!");
  }
  e.stopImmediatePropagation();
  $('#btn_modPersInsert').off('click');
}

function handlerAlumnosAddToForm_Click(e){
  document.querySelector('#grid').style.display = 'none';
  document.querySelector('#edit').style.display = 'block';
  appPersonaSetData(Persona.tablaPers); //pestaña Personales
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
        const resp = await appAsynFetch({ TipoQuery:'delAlumnos', arr:arr },rutaSQL);
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
      TipoQuery : 'addAlumno', //quitar el soft delete (estado)
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
      TipoQuery : 'viewAlumno',
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
    const resp = await appAsynFetch({ TipoQuery : 'startAlumno' },rutaSQL); 
    
    //pestaña de Alumno
    document.querySelector('#txt_AlumnoFechaIng').value = (moment(resp.fecha).format("DD/MM/YYYY"));
    document.querySelector("#txt_AlumnoCodigo").placeholder = ("00-000000");
    document.querySelector("#txt_AlumnoCodigo").value = ("");

    document.querySelector("#hid_alumnoFamiPadreID").value = ""
    document.querySelector("#lbl_alumnoFamiPadreNombre").innerHTML = ""
    document.querySelector("#lbl_alumnoFamiPadreDNI").innerHTML = ""
    document.querySelector("#lbl_alumnoFamiPadreDireccion").innerHTML = ""

    document.querySelector("#hid_alumnoFamiMadreID").value = ""
    document.querySelector("#lbl_alumnoFamiMadreNombre").innerHTML = ""
    document.querySelector("#lbl_alumnoFamiMadreDNI").innerHTML = ""
    document.querySelector("#lbl_alumnoFamiMadreDireccion").innerHTML = ""

    document.querySelector("#hid_alumnoFamiApoderaID").value = ""
    document.querySelector("#lbl_alumnoFamiApoderaNombre").innerHTML = ""
    document.querySelector("#lbl_alumnoFamiApoderaDNI").innerHTML = ""
    document.querySelector("#lbl_alumnoFamiApoderaDireccion").innerHTML = ""
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appFamiPadreAdd(){
  Persona.openBuscar('VerifyPadre',rutaSQL,true,true,false);
  $('#btn_modPersInsert').off('click');

  $('#btn_modPersInsert').on('click',handlerFamiPadreInsert_Click);
  $('#btn_modPersAddToForm').on('click',handlerFamiPadreAddToForm_Click);
}

async function handlerFamiPadreInsert_Click(e){
  if(Persona.sinErrores()){
    try{
      const resp = await Persona.ejecutaSQL();
      appFamiPadreSetData(resp.tablaPers);
      Persona.close();
    } catch(err){
      console.error('Error al cargar datos:', err);
    }
  } else {
    alert("!!!Faltan llenar Datos!!!");
  }
  e.stopImmediatePropagation();
  $('#btn_modPersInsert').off('click');
}

function handlerFamiPadreAddToForm_Click(e){
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

async function handlerFamiMadreInsert_Click(e){
  if(Persona.sinErrores()){
    try{
      const resp = await Persona.ejecutaSQL();
      appFamiMadreSetData(resp.tablaPers);
      Persona.close();
    } catch(err){
      console.error('Error al cargar datos:', err);
    }
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

async function handlerFamiApoderaInsert_Click(e){
  if(Persona.sinErrores()){
    try{
      const resp = await Persona.ejecutaSQL();
      appFamiApoderaSetData(resp.tablaPers);
      Persona.close();
    } catch(err){
      console.error('Error al cargar datos:', err);
    }
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
      alumnoCodigo  : "",
      alumnoFecha   : appConvertToFecha(document.querySelector("#txt_AlumnoFechaIng").value,""),
      alumnoPadreID : document.querySelector("#hid_alumnoFamiPadreID").value,
      alumnoMadreID : document.querySelector("#hid_alumnoFamiMadreID").value,
      alumnoApoderaID : document.querySelector("#hid_alumnoFamiApoderaID").value,
      alumnoObservac : ""
    }
  }
  return datos;
}

function appFamiPadreSetData(data){
  document.querySelector("#hid_alumnoFamiPadreID").value = data.ID
  document.querySelector("#lbl_alumnoFamiPadreNombre").innerHTML = data.ap_paterno + " " + data.ap_materno + ", " + data.nombres;
  document.querySelector("#lbl_alumnoFamiPadreDNI").innerHTML = "DNI: " + data.nroDUI
  document.querySelector("#lbl_alumnoFamiPadreDireccion").innerHTML = "Direccion: " + data.direccion
}

function appFamiMadreSetData(data){
  document.querySelector("#hid_alumnoFamiMadreID").value = data.ID
  document.querySelector("#lbl_alumnoFamiMadreNombre").innerHTML = data.ap_paterno + " " + data.ap_materno + ", " + data.nombres;
  document.querySelector("#lbl_alumnoFamiMadreDNI").innerHTML = "DNI: " + data.nroDUI
  document.querySelector("#lbl_alumnoFamiMadreDireccion").innerHTML = "Direccion: " + data.direccion
}

function appFamiApoderaSetData(data){
  document.querySelector("#hid_alumnoFamiApoderaID").value = data.ID
  document.querySelector("#lbl_alumnoFamiApoderaNombre").innerHTML = data.ap_paterno + " " + data.ap_materno + ", " + data.nombres;
  document.querySelector("#lbl_alumnoFamiApoderaDNI").innerHTML = "DNI: " + data.nroDUI
  document.querySelector("#lbl_alumnoFamiApoderaDireccion").innerHTML = "Direccion: " + data.direccion
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
