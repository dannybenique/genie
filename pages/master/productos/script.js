const rutaSQL = "pages/master/productos/sql.php";
var menu = "";

//=========================funciones para Personas============================
function appProductosGrid(){
  document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="7"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  let txtBuscar = document.querySelector("#txtBuscar").value;
  let datos = { TipoQuery: 'selProductos', buscar:txtBuscar };

  appFetch(datos,rutaSQL).then(resp => {
    let disabledDelete = (menu.master.submenu.productos.cmdDelete===1) ? "" : "disabled";
    document.querySelector("#chk_All").disabled = (menu.master.submenu.productos.cmdDelete===1) ? false : true;
    if(resp.productos.length>0){
      let fila = "";
      resp.productos.forEach((valor,key)=>{
        fila += '<tr>';
        fila += '<td><input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'" '+(disabledDelete)+'/></td>';
        fila += '<td style="text-align:center;">'+(valor.codigo)+'</td>';
        fila += '<td style="text-align:center;">'+((valor.obliga==1)?('<i class="fa fa-info-circle" style="color:#AF2031;" title="Obligatorio"></i>'):(''))+'</td>';
        fila += '<td><a href="javascript:appProductoView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.producto)+'</a></td>';
        fila += '<td>'+(valor.abrevia)+'</td>';
        fila += '<td></td>';
        fila += '</tr>';
      });
      document.querySelector('#grdDatos').innerHTML = (fila);
    }else{
      document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="7" style="text-align:center;color:red;">Sin Resultados para '+txtBuscar+'</td></tr>');
    }
    document.querySelector('#grdCount').innerHTML = (resp.productos.length);
  });
}

function appProductosReset(){
  appFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php").then(resp => {
    menu = JSON.parse(resp.menu);
    document.querySelector("#btn_DEL").style.display = (menu.master.submenu.productos.cmdDelete==1)?('inline'):('none');
    document.querySelector("#btn_NEW").style.display = (menu.master.submenu.productos.cmdInsert==1)?('inline'):('none');
    
    document.querySelector("#txtBuscar").value = ("");
    appProductosGrid();
  });
}

function appProductosBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { appProductosGrid(); }
}

function appProductoNuevo(){
  document.querySelector("#btnInsert").style.display = (menu.master.submenu.productos.cmdInsert==1)?('inline'):('none');
  document.querySelector("#btnUpdate").style.display = 'none';
  appFetch({ TipoQuery:'startProducto' },rutaSQL).then(resp => {
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

function appProductoView(productoID){
  document.querySelector("#btnUpdate").style.display = (menu.master.submenu.productos.cmdUpdate==1)?('inline'):('none');
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

function appProductoInsert(){
  let datos = modGetDataToDataBase();
  if(datos!=""){
    datos.TipoQuery = 'insProducto';
    appFetch(datos,rutaSQL).then(resp => {
      appProductoCancel();
    });
  } else {
    alert("!!!Faltan Datos!!!");
  }
}

function appProductoUpdate(){
  let datos = modGetDataToDataBase();
  if(datos!=""){
    datos.TipoQuery = 'updProducto';
    appFetch(datos,rutaSQL).then(resp => {
      appProductoCancel();
    });
  } else {
    alert("!!!Faltan Datos!!!");
  }
}

function appProductosBorrar(){
  //let arr = $('[name="chk_Borrar"]:checked').map(function(){return this.value}).get();
  let arr = Array.from(document.querySelectorAll('[name="chk_Borrar"]:checked')).map(function(obj){return obj.attributes[2].nodeValue});
  if(arr.length>0){
    if(confirm("¿Esta seguro de continuar?")) {
      appFetch({ TipoQuery:'delProductos', arr:arr },rutaSQL).then(resp => {
        if (resp.error == false) { //sin errores
          appProductoCancel();
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

function appProductoCancel(){
  appProductosGrid();
  document.querySelector('#grid').style.display = 'block';
  document.querySelector('#edit').style.display = 'none';
}
