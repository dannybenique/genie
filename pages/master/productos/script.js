const rutaSQL = "pages/master/productos/sql.php";

var menu = "";
var orden = 0;

//=========================funciones para Personas============================
async function appProductosGrid(){
  try {
    document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="7"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
    const txtBuscar = document.querySelector("#txtBuscar").value;
    const resp = await appAsynFetch({ TipoQuery: 'producto_sel', buscar:txtBuscar }, rutaSQL);
    const disabledDelete = (menu.master.submenu.productos.cmdDelete===1) ? "" : "disabled";
    document.querySelector("#chk_All").disabled = (menu.master.submenu.productos.cmdDelete===1) ? false : true;
    
    if(resp.productos.length>0){
      let fila = "";
      resp.productos.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td><input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'" '+(disabledDelete)+'/></td>'+
                '<td style="text-align:center;">'+(valor.codigo)+'</td>'+
                '<td style="text-align:center;">'+((valor.obliga==1)?('<i class="fa fa-info-circle" style="color:#AF2031;" title="Obligatorio"></i>'):(''))+'</td>'+
                '<td><a href="javascript:appProductoView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.producto)+'</a></td>'+
                '<td>'+(valor.abrevia)+'</td>'+
                '<td style="text-align:center;">'+(valor.orden)+'</td>'+
                '<td></td>'+
                '</tr>';
      });
      document.querySelector('#grdDatos').innerHTML = (fila);
    }else{
      document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="7" style="text-align:center;color:red;">Sin Resultados para '+txtBuscar+'</td></tr>');
    }
    document.querySelector('#grdCount').innerHTML = (resp.productos.length);
  } catch(err) {
    console.error('Error al cargar datos:', err);
  }
}

async function appProductosReset(){
  try {
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    
    orden = 0;
    menu = JSON.parse(resp.menu);
    document.querySelector("#btn_DEL").style.display = (menu.master.submenu.productos.cmdDelete==1)?('inline'):('none');
    document.querySelector("#btn_NEW").style.display = (menu.master.submenu.productos.cmdInsert==1)?('inline'):('none');
    
    document.querySelector("#txtBuscar").value = ("");
    appProductosGrid();
  } catch(err) {
    console.error('Error al cargar datos:', err);
  }
}

function appProductosBuscar(e){
  if(e.keyCode === 13) { appProductosGrid(); }
}

async function appProductoNuevo(){
  document.querySelector("#btnInsert").style.display = (menu.master.submenu.productos.cmdInsert==1)?('inline'):('none');
  document.querySelector("#btnUpdate").style.display = 'none';
  try {
    const resp = await appAsynFetch({ TipoQuery:'producto_start' }, rutaSQL);

    $(".form-group").removeClass("has-error");
    document.querySelector("#hid_productoID").value = ("0");
    document.querySelector("#txt_Codigo").value = ("");
    document.querySelector("#txt_Abrev").value = ("");
    document.querySelector("#txt_Nombre").value = ("");
    document.querySelector('#div_Orden').style.display = 'none';
    document.querySelector("#grid").style.display = 'none';
    document.querySelector("#edit").style.display = 'block';
  } catch (err){
    console.error('Error al cargar datos:', err);
  }
}

async function appProductoView(productoID){
  document.querySelector("#btnUpdate").style.display = (menu.master.submenu.productos.cmdUpdate==1)?('inline'):('none');
  document.querySelector("#btnInsert").style.display = 'none';
  $(".form-group").removeClass("has-error");

  try{
    const resp = await appAsynFetch({
      TipoQuery : 'producto_edit',
      productoID : productoID
    }, rutaSQL);

    document.querySelector("#hid_productoID").value = (resp.ID);
    document.querySelector("#txt_Codigo").value = (resp.codigo);
    document.querySelector("#txt_Abrev").value = (resp.abrev);
    document.querySelector("#txt_Nombre").value = (resp.nombre);
    document.querySelector('#grid').style.display = 'none';
    document.querySelector('#edit').style.display = 'block';
    //llenar el orden de los registros
    document.querySelector('#div_Orden').style.display = 'block';
    appLlenarDataEnComboBox(Array.from({ length: resp.totalregs }, (_, i) => ({ID:i+1,nombre:i+1})),'#cbo_Orden',resp.orden);
    orden = resp.orden;

  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appProductoInsert(){
  try{
    const datos = modGetDataToDataBase();
    if(datos!=""){
      datos.TipoQuery = 'producto_ins';
      const resp = await appAsynFetch(datos,rutaSQL);
      if(!resp.error) { appProductoCancel(); }
    } else {
      alert("!!!Faltan Datos!!!");
    }
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appProductoUpdate(){
  try{
    const datos = modGetDataToDataBase();
    if(datos!=""){
      datos.TipoQuery = 'producto_upd';
      const resp = await appAsynFetch(datos,rutaSQL);
      if(!resp.error) { appProductoCancel(); }
    } else {
      alert("!!!Faltan Datos!!!");
    }
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appProductosBorrar(){
  //let arr = $('[name="chk_Borrar"]:checked').map(function(){return this.value}).get();
  let arr = Array.from(document.querySelectorAll('[name="chk_Borrar"]:checked')).map(function(obj){return obj.attributes[2].nodeValue});
  if(arr.length>0){
    if(confirm("¿Esta seguro de continuar?")) {
      try{
        const resp = await appAsynFetch({ TipoQuery:'producto_del', arr:arr },rutaSQL);
        if (!resp.error) { appProductoCancel(); }
      }catch(err){
        console.error('Error al cargar datos:', err);
      }
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
      new_orden : document.querySelector("#cbo_Orden").value,
      old_orden : orden
    }
  }
  return rpta;
}

function appProductoCancel(){
  appProductosGrid();
  document.querySelector('#grid').style.display = 'block';
  document.querySelector('#edit').style.display = 'none';
}
