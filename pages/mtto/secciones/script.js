const rutaSQL = "pages/mtto/secciones/sql.php";
var menu = null;
var strLoader = '<tr><td colspan="7"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>';

//=========================funciones============================
async function appNivelesGrid(){
  //preloader
  document.querySelector('#grdColNiv').innerHTML = (strLoader);
  try {
    const resp = await appAsynFetch({ 
      TipoQuery : 'nivel_select',
      nivelID : document.querySelector('#cboNiveles').value
    }, rutaSQL);
    
    //respuesta
    document.querySelector("#chk_All").disabled = (menu.mtto.submenu.secciones.cmdDelete===1) ? false : true;
    fn_LlenarGridColNiv(resp.colniv); //colniv
  } catch(err) {
    console.error('Error al cargar datos:', err);
  }
}

function fn_LlenarGridColNiv(data){
  let disabledDelete = (menu.mtto.submenu.secciones.cmdDelete===1) ? "" : "disabled";
  if(data.length>0){
    let fila = "";
    let rowspan = 0;
    let gradoID = 0;
    data.forEach((valor,key)=>{
      rowspan = (gradoID!=valor.gradoID) ? (data.filter((xx)=>xx.gradoID===valor.gradoID).length) : 0;
      fila += '<tr>'+
              ((gradoID!=valor.gradoID) ? ('<td rowspan='+rowspan+'>'+(valor.nivel)+' &raquo; '+(valor.grado)+'</td>'):(''))+
              '<td><input type="checkbox" name="chk_ColNivBorrar" value="'+(valor.seccionID)+'" '+(disabledDelete)+'/></td>'+
              '<td style="text-align:center;"><a href="javascript:appColNivEdit('+(valor.seccionID)+');"><span data-toggle="tooltip" class="badge bg-green">'+(valor.seccion)+'</span></a></td>'+
              '<td>'+(valor.alias)+'</td>'+
              '<td style="text-align:center;">'+(valor.capacidad)+'</td>'+
              '<td></td>'+
              '</tr>';
      gradoID = valor.gradoID;
    });
    document.querySelector('#grdColNiv').innerHTML = (fila);
  }else{
    document.querySelector('#grdColNiv').innerHTML = ('<tr><td colspan="7" style="text-align:center;color:red;">Sin Resultados</td></tr>');
  }
  document.querySelector('#grdColNivCount').innerHTML = (data.length);
}

async function appNivelesReset(){
  try{
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    menu = JSON.parse(resp.menu);
    document.querySelector("#btn_DEL").style.display = (menu.mtto.submenu.secciones.cmdDelete==1)?('inline'):('none');
    document.querySelector("#btn_NEW").style.display = (menu.mtto.submenu.secciones.cmdInsert==1)?('inline'):('none');
    
    const rpta = await appAsynFetch({ TipoQuery:'nivel_start' }, rutaSQL);
    appLlenarDataEnComboBox(rpta.comboNiveles,"#cboNiveles",0);
    appNivelesGrid();
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appColNivBorrar(){
  let arr = Array.from(document.querySelectorAll('[name="chk_ColNivBorrar"]:checked')).map(function(obj){return obj.attributes[2].nodeValue});
  if(arr.length>0){
    if(confirm("¿Esta seguro de continuar?")) {
      try{
        const resp = await appAsynFetch({ TipoQuery:'colniv_delete', arr:arr },rutaSQL);
        if (resp.error == false) { appNivelesGrid(); } //sin errores
      } catch(err){
        console.error('Error al cargar datos:', err);
      }
    }
  } else {
    alert("NO eligio borrar ninguno");
  }
}

async function appColNivEdit(nivelID){
  try{
    const resp = await appAsynFetch({ 
      TipoQuery : 'colniv_edit',
      nivelID : nivelID,
    }, rutaSQL);

    document.querySelector("#hid_colnivnivelID").value = resp.seccionID;
    document.querySelector("#txt_modColNivNombre").value = resp.nivel + " - " + resp.grado + " - " + resp.seccion;
    document.querySelector("#txt_modColNivAlias").value = resp.alias;
    document.querySelector("#txt_modColNivCapacidad").value = resp.capacidad;
    $("#modalColNiv").modal("show");
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appColNivUpdate(){
  //preloader
  document.querySelector('#grdColNiv').innerHTML = (strLoader);
  try{
    const resp = await appAsynFetch({
      TipoQuery : "colniv_update",
      nivelID : document.querySelector('#cboNiveles').value,
      seccionID : document.querySelector("#hid_colnivnivelID").value,
      alias : document.querySelector("#txt_modColNivAlias").value,
      capacidad : document.querySelector("#txt_modColNivCapacidad").value
    }, rutaSQL);
    
    //respuesta
    $("#modalColNiv").modal("hide");
    fn_LlenarGridColNiv(resp.colniv); //colniv
  }catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appColNivRefresh(){
  //preloader
  document.querySelector('#grdColNiv').innerHTML = (strLoader);
  try{
    const resp = await appAsynFetch({
      TipoQuery : "colniv_refresh",
      nivelID : document.querySelector('#cboNiveles').value
    }, rutaSQL);
    
    //respuesta
    fn_LlenarGridColNiv(resp.colniv); //colniv
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}