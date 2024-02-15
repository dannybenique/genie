const rutaSQL = "pages/master/niveles/sql.php";
var menu = "";

//=========================funciones para Personas============================
function appNivelesGrid(){
  document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="7"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  let txtBuscar = document.querySelector("#txtBuscar").value;
  let datos = { TipoQuery: 'selNiveles', buscar:txtBuscar };

  appFetch(datos,rutaSQL).then(resp => {
    let disabledDelete = (menu.master.submenu.niveles.cmdDelete===1) ? "" : "disabled";
    document.querySelector("#chk_All").disabled = (menu.master.submenu.niveles.cmdDelete===1) ? false : true;
    if(resp.productos.length>0){
      let fila = "";
      resp.productos.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td><input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'" '+(disabledDelete)+'/></td>'+
                '<td style="text-align:center;">'+(valor.codigo)+'</td>'+
                '<td style="text-align:center;">'+((valor.obliga==1)?('<i class="fa fa-info-circle" style="color:#AF2031;" title="Obligatorio"></i>'):(''))+'</td>'+
                '<td><a href="javascript:appNivelView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.producto)+'</a></td>'+
                '<td>'+(valor.abrevia)+'</td>'+
                '<td></td>'+
                '</tr>';
      });
      document.querySelector('#grdDatos').innerHTML = (fila);
    }else{
      document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="7" style="text-align:center;color:red;">Sin Resultados para '+txtBuscar+'</td></tr>');
    }
    document.querySelector('#grdCount').innerHTML = (resp.productos.length);
  });
}

function appNivelesReset(){
  appFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php").then(resp => {
    menu = JSON.parse(resp.menu);
    document.querySelector("#btn_DEL").style.display = (menu.master.submenu.niveles.cmdDelete==1)?('inline'):('none');
    document.querySelector("#btn_NEW").style.display = (menu.master.submenu.niveles.cmdInsert==1)?('inline'):('none');
    
    document.querySelector("#txtBuscar").value = ("");
    appNivelesGrid();
  });
}

function appNivelesBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { appNivelesGrid(); }
}

function appNivelNuevo(){
  document.querySelector("#btnInsert").style.display = (menu.master.submenu.niveles.cmdInsert==1)?('inline'):('none');
  document.querySelector("#btnUpdate").style.display = 'none';
  appFetch({ TipoQuery:'startNivel' },rutaSQL).then(resp => {
    try{
      $(".form-group").removeClass("has-error");
      document.querySelector("#hid_productoID").value = ("0");
      document.querySelector("#txt_Codigo").value = ("");
      document.querySelector("#txt_Abrev").value = ("");
      document.querySelector("#txt_Nombre").value = ("");
      document.querySelector("#grid").style.display = 'none';
      document.querySelector("#edit").style.display = 'block';
    } catch (err){
      console.log(err);
    }
  });
}

function appNivelView(productoID){
  document.querySelector("#btnUpdate").style.display = (menu.master.submenu.niveles.cmdUpdate==1)?('inline'):('none');
  document.querySelector("#btnInsert").style.display = 'none';
  $(".form-group").removeClass("has-error");

  let datos = {
    TipoQuery : 'editProducto',
    productoID : productoID
  }

  appFetch(datos,rutaSQL).then(resp => {
    try{
      document.querySelector("#hid_productoID").value = resp.ID;
      document.querySelector("#txt_Codigo").value = (resp.codigo);
      document.querySelector("#txt_Abrev").value = (resp.abrev);
      document.querySelector("#txt_Nombre").value = (resp.nombre);
      document.querySelector('#grid').style.display = 'none';
      document.querySelector('#edit').style.display = 'block';
    } catch(err){
      console.log(err);
    }
  });
}

function appNivelInsert(){
  let datos = modGetDataToDataBase();
  if(datos!=""){
    datos.TipoQuery = 'insNivel';
    appFetch(datos,rutaSQL).then(resp => {
      appNivelCancel();
    });
  } else {
    alert("!!!Faltan Datos!!!");
  }
}

function appNivelUpdate(){
  let datos = modGetDataToDataBase();
  if(datos!=""){
    datos.TipoQuery = 'updNivel';
    appFetch(datos,rutaSQL).then(resp => {
      appNivelCancel();
    });
  } else {
    alert("!!!Faltan Datos!!!");
  }
}

function appNivelesBorrar(){
  //let arr = $('[name="chk_Borrar"]:checked').map(function(){return this.value}).get();
  let arr = Array.from(document.querySelectorAll('[name="chk_Borrar"]:checked')).map(function(obj){return obj.attributes[2].nodeValue});
  if(arr.length>0){
    if(confirm("¿Esta seguro de continuar?")) {
      appFetch({ TipoQuery:'delNiveles', arr:arr },rutaSQL).then(resp => {
        if (resp.error == false) { //sin errores
          appNivelCancel();
        }
      });
    }
  } else {
    alert("NO eligio borrar ninguno");
  }
}

function modGetDataToDataBase(){
  let rpta = "";
  let esError = false;

  $(".form-group").removeClass("has-error");
  if(document.querySelector("#txt_Abrev").value=="")  { document.querySelector("#div_Abrev").className = "form-group has-error"; esError = true; }
  if(document.querySelector("#txt_Nombre").value=="") { document.querySelector("#div_Nombre").className = "form-group has-error"; esError = true; }

  if(!esError){
    rpta = {
      ID : document.querySelector("#hid_productoID").value,
      codigo : document.querySelector("#txt_Codigo").value,
      abrevia : document.querySelector("#txt_Abrev").value,
      nombre : document.querySelector("#txt_Nombre").value,
    }
  }
  return rpta;
}

function appNivelCancel(){
  appNivelesGrid();
  document.querySelector('#grid').style.display = 'block';
  document.querySelector('#edit').style.display = 'none';
}
