const rutaSQL = "pages/master/niveles/sql.php";
var menu = "";
var strLoader = '<tr><td colspan="7"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>';

//=========================funciones para Personas============================
function appNivelesGrid(){
  let datos = { 
    TipoQuery : 'nivel_select',
    nivelID : document.querySelector('#cboNiveles').value
  };

  //preloader
  document.querySelector('#grdDatos').innerHTML = (strLoader);
  document.querySelector('#grdColNiv').innerHTML = (strLoader);
  
  //respuesta
  appFetch(datos,rutaSQL).then(resp => {
    document.querySelector("#chk_All").disabled = (menu.master.submenu.niveles.cmdDelete===1) ? false : true;
    LlenarGridNiveles(resp.niveles); //niveles
    LlenarGridColNiv(resp.colniv); //colniv
  });
}

function LlenarGridNiveles(data){
  let disabledDelete = (menu.master.submenu.niveles.cmdDelete===1) ? "" : "disabled";
  if(data.length>0){
    let fila = "";
    let rowspan = 0;
    let gradoID = 0;
    data.forEach((valor,key)=>{
      rowspan = (gradoID!=valor.gradoID) ? (data.filter((xx)=>xx.gradoID===valor.gradoID).length) : 0;
      fila += '<tr>'+
              ((gradoID!=valor.gradoID) ? ('<td rowspan='+rowspan+'>'+(valor.nivel)+' &raquo; '+(valor.grado)+'</td>'):(''))+
              '<td><input type="checkbox" name="chk_Send" value="'+(valor.seccionID)+'" '+(disabledDelete)+'/></td>'+
              '<td style="text-align:center;"><span data-toggle="tooltip" class="badge bg-light-blue">'+(valor.seccion)+'</span></td>'+
              '<td></td>'+
              '</tr>';
      gradoID = valor.gradoID;
    });
    document.querySelector('#grdDatos').innerHTML = (fila);
  }else{
    document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="3" style="text-align:center;color:red;">Sin Resultados</td></tr>');
  }
  document.querySelector('#grdCount').innerHTML = (data.length);
}

function LlenarGridColNiv(data){
  let disabledDelete = (menu.master.submenu.niveles.cmdDelete===1) ? "" : "disabled";
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

function appNivelesReset(){
  appFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php").then(resp => {
    menu = JSON.parse(resp.menu);
    document.querySelector("#btn_DEL").style.display = (menu.master.submenu.niveles.cmdDelete==1)?('inline'):('none');
    document.querySelector("#btn_NEW").style.display = (menu.master.submenu.niveles.cmdInsert==1)?('inline'):('none');
    let datos = { TipoQuery:'nivel_start' }
    appFetch(datos,rutaSQL).then(resp => {
      appLlenarDataEnComboBox(resp.comboNiveles,"#cboNiveles",0);
      appNivelesGrid();
    })
  });
}

function appNivelesRefresh(){
  let datos = {
    TipoQuery : "nivel_refresh",
    nivelID : document.querySelector('#cboNiveles').value
  }
  //preloader
  document.querySelector('#grdDatos').innerHTML = (strLoader);
  appFetch(datos,rutaSQL).then(resp => {
    LlenarGridNiveles(resp.niveles); //niveles
  });
}

function appNivelNuevo(){ //falta corregir
  document.querySelector("#btnInsert").style.display = (menu.master.submenu.niveles.cmdInsert==1)?('inline'):('none');
  document.querySelector("#btnUpdate").style.display = 'none';
  appFetch({ TipoQuery:'nivel_start' },rutaSQL).then(resp => {
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

function appNivelInsert(){ //falta corregir
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

function appNivelSend(){
  let arr = Array.from(document.querySelectorAll('[name="chk_Send"]:checked')).map(function(obj){return obj.attributes[2].nodeValue});
  if(arr.length>0){
    if(confirm("¿Esta seguro de continuar?")) {
      appFetch({ TipoQuery:'nivel_send', arr:arr },rutaSQL).then(resp => {
        if (resp.error == false) { //sin errores
          appNivelesGrid();
        }
      });
    }
  } else {
    alert("NO eligio agregar ninguna seccion al colegio actual");
  }
}

function modGetDataToDataBase(){ //falta corregir
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

function appColNivBorrar(){
  let arr = Array.from(document.querySelectorAll('[name="chk_ColNivBorrar"]:checked')).map(function(obj){return obj.attributes[2].nodeValue});
  if(arr.length>0){
    if(confirm("¿Esta seguro de continuar?")) {
      appFetch({ TipoQuery:'colniv_delete', arr:arr },rutaSQL).then(resp => {
        if (resp.error == false) { //sin errores
          appNivelesGrid();
        }
      });
    }
  } else {
    alert("NO eligio borrar ninguno");
  }
}

function appColNivEdit(nivelID){
  let datos = { 
    TipoQuery : 'colniv_edit',
    nivelID : nivelID,
  };
  appFetch(datos,rutaSQL).then(resp => {
    document.querySelector("#hid_colnivnivelID").value = resp.seccionID;
    document.querySelector("#txt_modColNivNombre").value = resp.nivel + " - " + resp.grado + " - " + resp.seccion;
    document.querySelector("#txt_modColNivAlias").value = resp.alias;
    document.querySelector("#txt_modColNivCapacidad").value = resp.capacidad;
    $("#modalColNiv").modal("show");
  }); 
}

function appColNivUpdate(){
  let datos = {
    TipoQuery : "colniv_update",
    nivelID : document.querySelector('#cboNiveles').value,
    seccionID : document.querySelector("#hid_colnivnivelID").value,
    alias : document.querySelector("#txt_modColNivAlias").value,
    capacidad : document.querySelector("#txt_modColNivCapacidad").value
  }
  //preloader
  document.querySelector('#grdColNiv').innerHTML = (strLoader);
  
  appFetch(datos,rutaSQL).then(resp => {
    $("#modalColNiv").modal("hide");
    LlenarGridColNiv(resp.colniv); //colniv
  });
}

function appColNivRefresh(){
  let datos = {
    TipoQuery : "colniv_refresh",
    nivelID : document.querySelector('#cboNiveles').value
  }
  //preloader
  document.querySelector('#grdColNiv').innerHTML = (strLoader);
  appFetch(datos,rutaSQL).then(resp => {
    LlenarGridColNiv(resp.colniv); //colniv
  });
}