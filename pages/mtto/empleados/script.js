const rutaSQL = "pages/mtto/empleados/sql.php";
var menu = "";
var zTreeObj = null;
var zMnuEmpleado = null;
var zSetting = { 
  check : {
    enable : true
  },
  view : {
    addDiyDom : null,
    showIcon : showIconForTree
  },
  callback: {
    beforeDrag : beforeDrag
  },
  edit: {
    enable : true,
    showRemoveBtn : true,
    showRenameBtn : true
  }
};

//=========================funciones============================
async function appWorkersGrid(){
  document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="9"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  const txtBuscar = document.querySelector("#txtBuscar").value.toUpperCase();
  const disabledDelete = (menu.mtto.submenu.empleados.cmdDelete===1) ? "" : "disabled";
  try{
    const resp = await appAsynFetch({ 
      TipoQuery: 'selWorkers', 
      buscar: txtBuscar, 
      verTodos: document.querySelector("#hidViewAll").value 
    }, rutaSQL);

    //respuesta
    document.querySelector("#chk_All").disabled = (menu.mtto.submenu.empleados.cmdDelete===1) ? false : true;
    if(resp.tabla.length>0){
      let fila = "";
      resp.tabla.forEach((valor,key)=>{
        fila += '<tr style="'+((valor.estado==0)?('color:#bfbfbf;'):(''))+'">' +
                '<td><input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'" '+disabledDelete+'/></td>'+
                '<td><i class="fa fa-paperclip"></i></td>'+
                '<td>'+((valor.login!=null)?('<a href="javascript:appUserCambioPassw('+(valor.ID)+')" style="'+((valor.estado==0)?('color:#bfbfbf;'):(''))+'"><i class="fa fa-lock"></i></a>'):(''))+'</td>'+
                '<td>'+(valor.codigo)+'</td>'+
                '<td>'+(valor.nro_dui)+'</td>'+
                '<td><a href="javascript:appWorkerView('+(valor.ID)+');" title="'+(valor.ID)+'" style="'+((valor.estado==0)?('color:#bfbfbf;'):(''))+'">'+(valor.empleado)+'</a></td>'+
                '<td>'+(valor.nombrecorto)+'</td>'+
                '<td>'+(valor.cargo)+'</td>'+
                '<td></td>'+
                '</tr>';
      });
      document.querySelector('#grdDatos').innerHTML = (fila);
    } else {
      let res = (txtBuscar==="") ? ("") : ("para "+txtBuscar);
      document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="9" style="text-align:center;color:red;">Sin Resultados '+(res)+'</td></tr>');
    }
    document.querySelector('#grdCount').innerHTML = (resp.tabla.length+"/"+resp.cuenta);
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appWorkersReset(){
  //configurar treeview
  zTreeObj = $.fn.zTree.init($("#appTreeView"), zSetting, null);
  try{
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    menu = JSON.parse(resp.menu);
    document.querySelector("#btn_DEL").style.display = (menu.mtto.submenu.empleados.cmdDelete==1)?('inline'):('none');
    document.querySelector("#btn_NEW").style.display = (menu.mtto.submenu.empleados.cmdInsert==1)?('inline'):('none');

    document.querySelector("#txtBuscar").value = ("");
    document.querySelector("#grdDatos").innerHTML = ("");
    document.querySelector("#div_PersAuditoria").style.display = ((resp.rolID==101)?('block'):('none'));
    appWorkersGrid();
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appWorkersBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { appWorkersGrid(); }
}

function appWorkersBotonCancel(){
  appWorkersGrid();
  document.querySelector("#grid").style.display = 'block';
  document.querySelector("#edit").style.display = 'none';
}

async function appWorkersBotonInsert(){
  const datos = appWorkerGetDatosToDatabase();

  if(datos!=null){
    datos.TipoQuery = "insWorker";
    datos.usuario = appUserGetDatosToDatabase();
    try{
      const resp = await appAsynFetch(datos,rutaSQL);
      if(!resp.error){appWorkersBotonCancel();}
    } catch(err){
      console.error('Error al cargar datos:', err);
    }
  }
}

async function appWorkersBotonUpdate(){
  const datos = appWorkerGetDatosToDatabase();
  
  if(datos!=null){
    datos.TipoQuery = "updWorker";
    datos.usuario = appUserGetDatosToDatabase();
    try{
      const resp = await appAsynFetch(datos,rutaSQL);
      if(!resp.error){appWorkersBotonCancel();}
    } catch(err){
      console.error('Error al cargar datos:', err);
    }
  }
}

function appWorkersBotonNuevo(){
  Persona.openBuscar('VerifyWorker',rutaSQL,true,true,false);
  $('#btn_modPersInsert').off('click');

  $('#btn_modPersInsert').on('click',handlerWorkersInsert_Click);
  $('#btn_modPersAddToForm').on('click',handlerWorkersAddToForm_Click);
}

function appWorkersBotonViewAll(){ //mostrar inclusive los empleados eliminados
  document.querySelector("#icoViewAll").classList.toggle("fa-toggle-on");
  document.querySelector("#icoViewAll").classList.toggle("fa-toggle-off");
  document.querySelector("#hidViewAll").value = (document.querySelector("#hidViewAll").value==1) ? (0) : (1);
  appWorkersGrid();
}

function handlerWorkersInsert_Click(e){
  if(Persona.sinErrores()){ //sin errores
    Persona.ejecutaSQL().then(resp => {
      appPersonaSetData(resp.tablaPers);
      appFetch({TipoQuery : 'startWorker'},rutaSQL).then(resp => {
        appWorkerClear(resp);
        appUserClear(resp);
        document.querySelector('#grid').style.display = 'none';
        document.querySelector('#edit').style.display = 'block';
        Persona.close();
      });
    });
  } else {
    alert("!!!Faltan llenar Datos!!!");
  }
  e.stopImmediatePropagation();
  $('#btn_modPersInsert').off('click');
}

function handlerWorkersAddToForm_Click(e){
  appPersonaSetData(Persona.tablaPers); //pestaña Personales
  appUserClear({"comboRoles":Persona.tablaPers.comboRoles});
  appWorkerClear({"comboCargos":Persona.tablaPers.comboCargos,"fecha":Persona.tablaPers.fechaActual});
  document.querySelector('#grid').style.display = 'none';
  document.querySelector('#edit').style.display = 'block';
  Persona.close();
  
  e.stopImmediatePropagation();
  $('#btn_modPersAddToForm').off('click');
}

async function appWorkersBotonBorrar(){
  let arr = Array.from(document.querySelectorAll('[name="chk_Borrar"]:checked')).map(function(obj){return obj.attributes[2].nodeValue});
  if(arr.length>0){
    if(confirm("¿Esta seguro de continuar?")) {
      try{
        const resp = await appAsynFetch({ TipoQuery:'delWorkers', arr:arr },rutaSQL);
        if (!resp.error) { appWorkersBotonCancel(); }
      } catch(err){
        console.error('Error al cargar datos:', err);
      }
    }
  } else {
    alert("NO eligio borrar ninguno");
  }
}

async function appWorkersBotonEstado(){
  try{
    const resp = await appAsynFetch({
      TipoQuery : 'addWorker', //quitar el soft delete (estado)
      workerID : document.querySelector("#lbl_ID").innerHTML
    }, rutaSQL);
    
    //respuesta
    if(!resp.error){ document.querySelector("#div_Estado").innerHTML = ""; }
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appWorkerView(personaID){
  //tabs default en primer tab
  $('.nav-tabs li').removeClass('active');
  $('.tab-content .tab-pane').removeClass('active');
  $('a[href="#datosWorker"]').closest('li').addClass('active');
  $('#datosWorker').addClass('active');
  document.querySelector("#div_WorkerAuditoria").style.display = 'block';
  document.querySelector("#btnUpdate").style.display = (menu.mtto.submenu.empleados.cmdUpdate==1)?('inline'):('none');
  document.querySelector("#btnInsert").style.display = 'none';

  try{
    const resp = await appAsynFetch({
      TipoQuery : 'viewWorker',
      personaID : personaID,
      fullQuery : 2
    }, rutaSQL);
    
    //respuesta
    appPersonaSetData(resp.tablaPers); //pestaña Personales
    appWorkerSetData(resp.tablaWorker);  //pestaña Empleado
    appUserSetData(resp.tablaUser); //pestaña usuario
    document.querySelector('#grid').style.display = 'none';
    document.querySelector('#edit').style.display = 'block';
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appWorkerSetData(data){
  //info corta
  document.querySelector("#lbl_Codigo").innerHTML = (data.codigo);
  if (data.estado==0) { document.querySelector("#div_Estado").innerHTML = '<button type="button" class="btn btn-success pull-right" onclick="javascript:appWorkersBotonEstado();"><i class="fa fa-flash"></i> Habilitar Empleado</button>'; }

  //pestaña de Empleado
  appLlenarDataEnComboBox(data.comboCargos,"#cbo_WorkerCargo",data.cargoID);
  document.querySelector('#txt_WorkerFechaIng').value = (moment(data.fecha_ing).format("DD/MM/YYYY"));
  document.querySelector("#txt_WorkerCodigo").value = (data.codigo);
  document.querySelector("#txt_WorkerNombreCorto").value = (data.nombrecorto);
  document.querySelector("#txt_WorkerCorreo").value = (data.correo);
  document.querySelector("#txt_WorkerObserv").value = (data.observac);
  document.querySelector("#lbl_WorkerSysFecha").innerHTML = (moment(data.sys_fecha).format("DD/MM/YYYY"));
  document.querySelector("#lbl_WorkerSysUser").innerHTML = (data.usermod);
}

function appWorkerClear(data){
  //todos los inputs sin error y panel error deshabilitado
  $('.form-group').removeClass('has-error');
  document.querySelector("#div_WorkerAuditoria").style.display = 'none';
  document.querySelector("#btnInsert").style.display = (menu.mtto.submenu.empleados.cmdInsert==1)?('inline'):('none');
  document.querySelector("#btnUpdate").style.display = 'none';

  //tabs default en primer tab
  $('.nav-tabs li').removeClass('active');
  $('.tab-content .tab-pane').removeClass('active');
  $('a[href="#datosWorker"]').closest('li').addClass('active');
  $('#datosWorker').addClass('active');

  //pestaña de Empleado
  appLlenarDataEnComboBox(data.comboCargos,"#cbo_WorkerCargo",0);
  document.querySelector('#txt_WorkerFechaIng').value = (moment(data.fecha).format("DD/MM/YYYY"));
  document.querySelector("#txt_WorkerCodigo").placeholder = ("00-000000");
  document.querySelector("#txt_WorkerCodigo").value = ("");
  document.querySelector("#txt_WorkerNombreCorto").value = ("");
  document.querySelector("#txt_WorkerObserv").value = ("");
}

function appWorkerGetDatosToDatabase(){
  let rpta = null;
  let esError = false;
  $('.form-group').removeClass('has-error');
  if(document.querySelector("#txt_WorkerNombreCorto").value=="") { 
    document.querySelector("#div_WorkerNombreCorto").className = "form-group has-error"; 
    esError = true; 
    alert("!!!Falta Nombre Corto en el Empleado!!!");
  }

  if(!esError){
    rpta = {
      workerID : document.querySelector("#lbl_ID").innerHTML,
      cargoID : document.querySelector("#cbo_WorkerCargo").value,
      nombrecorto : document.querySelector("#txt_WorkerNombreCorto").value,
      correo : document.querySelector("#txt_WorkerCorreo").value,
      fecha : appConvertToFecha(document.querySelector("#txt_WorkerFechaIng").value,""),
      observac : document.querySelector("#txt_WorkerObserv").value,
      usuario : null
    }
  }
  return rpta;
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

  //permisos
  document.querySelector("#btn_PersUpdate").style.display = 'block';
  document.querySelector("#btn_PersPermiso").style.display = 'none';
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

function appUserSetData(data){
  if(data.ID!=null){
    document.querySelector("#chk_UserEsUsuario").checked = true;
    document.querySelector("#txt_UserLogin").value = (data.login);
    document.querySelector("#txt_UserRePassword").value = document.querySelector("#txt_UserPassword").value = 'demo';
    document.querySelector("#div_UserRePassword").style.display = document.querySelector("#div_UserPassword").style.display = 'none';
    appLlenarDataEnComboBox(data.comboRoles,"#cbo_UserRol",data.rolID);

    zMnuEmpleado = data.menu = JSON.parse(data.menu);
    appUserEsUsuario();
  } else { //no tiene usuario
    zMnuEmpleado = null;
    appUserClear(data);
  }
}

function appUserClear(data){
  appLlenarDataEnComboBox(data.comboRoles,"#cbo_UserRol",0);
  document.querySelector("#chk_UserEsUsuario").checked = false;
  document.querySelector('#txt_UserLogin').value = ("");
  document.querySelector('#txt_UserPassword').value = ("");
  document.querySelector('#txt_UserRePassword').value = ("");
  document.querySelector("#div_UserPassword").style.display = 'block';
  document.querySelector("#div_UserRePassword").style.display = 'block';
  appUserEsUsuario();
}

async function appUserCambioPassw(userID){
  $("#modalChangePassw").modal("show");
  try{
    const resp = await appAsynFetch({
      TipoQuery:"selUserPass",
      userID:userID
    }, rutaSQL);
    
    //respuesta
    document.querySelector("#hid_PassID").value = resp.ID;
    document.querySelector("#hid_PassColegioID").value = resp.colegioID;
    document.querySelector("#lbl_PassNombrecorto").innerHTML = resp.nombrecorto;
    document.querySelector("#lbl_PassLogin").innerHTML = resp.login;
    document.querySelector("#txt_PassPassNew").value = "";
    document.querySelector("#txt_PassPassRe").value = "";
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function modUserBotonUpdatePassw(){
  if(document.querySelector("#txt_PassPassNew").value!=""){
    if(document.querySelector("#txt_PassPassNew").value===document.querySelector("#txt_PassPassRe").value){
      try{
        const resp = await appAsynFetch({
          TipoQuery:"changeUserPass",
          userID : document.querySelector("#hid_PassID").value,
          colegioID : document.querySelector("#hid_PassColegioID").value,
          passw : SHA1(document.querySelector("#txt_PassPassNew").value).toString().toUpperCase()
        }, rutaSQL);

        //respuesta
        if (!resp.error) { 
          alert("El PASSWORD se modifico correctamente");
          $("#modalChangePassw").modal("hide");
        }
      } catch(err){
        console.error('Error al cargar datos:', err);
      }
    } else {
      alert("!!!El PASSWORD es distintos en ambos campos!!!");
    }
  } else {
    alert("!!!NO pueden quedar vacios los campos!!!");
  }
}

function appUserEsUsuario(){
  let estado = document.querySelector("#chk_UserEsUsuario").checked;
  document.querySelector("#txt_UserLogin").disabled = !estado;
  document.querySelector("#txt_UserPassword").disabled = !estado;
  document.querySelector("#txt_UserRePassword").disabled = !estado;
  document.querySelector("#cbo_UserRol").disabled = !estado;
  document.querySelector("#btn_UserPerfilRoot").disabled = !estado;
  document.querySelector("#btn_UserPerfilCaja").disabled = !estado;
  
  //menu
  zTreeObj = $.fn.zTree.init($("#appTreeView"), zSetting, (estado==false)?(null):(transformData(zMnuEmpleado)));
}

function appUserGetDatosToDatabase(){
  let rpta = null;
  let esError = false;
  let esUsuario = document.querySelector("#chk_UserEsUsuario").checked;

  $('.form-group').removeClass('has-error');
  if(esUsuario){
    if(document.querySelector("#txt_UserLogin").value=="") { document.querySelector("#div_UserLogin").className = "form-group has-error"; esError = true; }
    if(document.querySelector("#txt_UserPassword").value=="") { document.querySelector("#div_UserPassword").className = "form-group has-error"; esError = true; }
    if(document.querySelector("#txt_UserRePassword").value != document.querySelector("#txt_UserPassword").value) { 
      document.querySelector("#div_UserPassword").className = "form-group has-error";
      document.querySelector("#div_UserRePassword").className = "form-group has-error";
      alert("el Password NO coincide");
      esError = true;
    }
    if(zTreeObj.getNodes().length==0) { alert("Debe configurar un PERFIL de usuario"); esError = true; }
    
  }

  if(esError==false && esUsuario==true){
    rpta = {
      login : document.querySelector("#txt_UserLogin").value,
      passw : SHA1(document.querySelector("#txt_UserPassword").value).toString().toUpperCase(),
      rolID : document.querySelector("#cbo_UserRol").value,
      menu : JSON.stringify(getTreeJSON(zTreeObj))
    }
  }
  return rpta;
}

async function appUserPerfilMenu(perfilID){
  try{
    const resp = await appAsynFetch({
      TipoQuery : "selSisMenu",
      perfilID : perfilID
    }, rutaSQL);
    
    //respuesta
    let mnu = JSON.parse(resp.menu);
    zTreeObj = $.fn.zTree.init($("#appTreeView"), zSetting, transformData(mnu));
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

//menu
function transformData(data) {
  var nodes = [];
  
  for (var key in data) {
    if (typeof data[key] === 'object') {
      let node = {
        name: key,
        nocheck : true,
        children: transformData(data[key])
      };
      nodes.push(node);
    } else {
      let node ={
        name : ($.isNumeric(data[key]))?key:key+" : "+data[key],
        nocheck : ($.isNumeric(data[key]))?false:true,
        checked : data[key]
      }
      nodes.push(node);
    }
  }
  return nodes;
}

function showIconForTree(treeId, treeNode) {
  return treeNode.nocheck;
};

function getTreeJSON(treeOBJ){
  var arr = {};
  for(let node of treeOBJ.getNodes()){
    if(node.children){ arr[node.name] = getTreeNodes(node.children) }
  }
  return arr;
}

function getTreeNodes(nodes) {
  let arr = {};
  for (let node of nodes) {
    let cad = node.name.split(":");
    if(node.children) { 
      arr[$.trim(cad[0])] = getTreeNodes(node.children); 
    } else {
      if(node.nocheck){
        arr[$.trim(cad[0])] = $.trim(cad[1]);
      } else {
        arr[$.trim(cad[0])] = (node.checked)?1:0;
      }
    }
  }
  return arr;
}

function beforeDrag(treeId, treeNodes) { 
  return false; 
}




//permisos para personas
function appPermisoPersonas(){
  let datos = { TipoQuery:'insNotifi', tabla:'tb_personas', personaID:$("#lbl_ID").html() }
  appAjaxInsert(datos,"pages/global/notifi/sql.php").done(function(resp){
    if(!resp.error){ $("#btn_PersPermiso").hide(); }
    else { alert("!!!Hubo un error... "+(resp.mensaje)+"!!!"); }
  });
}

function appPermisoLaboral(){
  let datos = { TipoQuery:'OneNotificacion', tabla:'tb_personas_labo', personaID:$("#lbl_ID").html() }
  appAjaxInsert(datos).done(function(resp){
    if(resp.error==false){ $("#btn_LaboPermiso").hide(); }
    else { alert("!!!Hubo un error... "+(resp.mensaje)+"!!!"); }
  });
}

function appPermisoConyuge(){
  let datos = { TipoQuery:'OneNotificacion', tabla:'tb_personas_cony', personaID:$("#lbl_ID").html() }
  appAjaxInsert(datos).done(function(resp){
    if(resp.error==false){ $("#btn_ConyPermiso").hide(); }
    else { alert("!!!Hubo un error... "+(resp.mensaje)+"!!!"); }
  });
}
