const rutaSQL = "pages/mtto/pagos/sql.php";
var menu = "";

//=========================funciones para Personas============================
function appPagosGrid(){
  document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="9"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  let txtBuscar = document.querySelector("#txtBuscar").value;
  let datos = { TipoQuery: 'selPagos', buscar:txtBuscar };

  appFetch(datos,rutaSQL).then(resp => {
    // console.log(resp);
    // let disabledDelete = (menu.mtto.submenu.pagos.cmdDelete===1) ? "" : "disabled";
    // document.querySelector("#chk_All").disabled = (menu.mtto.submenu.pagos.cmdDelete===1) ? false : true;
    fnPagosLlenarGrid(resp.pagos);
  });
}

function fnPagosLlenarGrid(data){
  if(data.length>0){
    let fila = "";
    data.forEach((valor,key)=>{
      fila += '<tr style="'+((valor.bloqueo) ? ("color:#aaa;"):(""))+'">'+
              '<td style="text-align:center;"><a href="javascript:appPagosBloquear('+(valor.ID)+');"><i '+((valor.bloqueo)?('class="fa fa-lock" style="color:#aaa;"'):('class="fa fa-unlock" style="color:#555;"'))+' title="Bloqueo"></i></a></td>'+
              '<td>'+((valor.bloqueo)?(""):('<input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'"/>'))+'</td>'+
              '<td style="text-align:center;">'+(valor.codigo)+'</td>'+
              '<td style="text-align:center;">'+((valor.obliga)?('<i class="fa fa-exclamation" style="color:#FF0084;" title="Obligatorio"></i>'):(''))+'</td>'+
              '<td>'+((valor.bloqueo) ? (valor.pago):('<a href="javascript:appPagoView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.pago)+'</a>'))+'</td>'+
              '<td style="text-align:center;">'+(valor.abrevia)+'</td>'+
              '<td style="text-align:right;">'+appFormatMoney(valor.importe,2)+'</td>'+
              '<td style="text-align:center;">'+moment(valor.vencimiento).format("DD/MM/YYYY")+'</td>'+
              '<td></td>'+
              '</tr>';
    });
    document.querySelector('#grdDatos').innerHTML = (fila);
  } else {
    let rpta = (txtBuscar==="") ? ("") : ("para "+txtBuscar);
    document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="9" style="text-align:center;color:red;">Sin Resultados '+rpta+'</td></tr>');
  }
  document.querySelector('#grdCount').innerHTML = (data.length);
}

function appPagosReset(){
  appFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php").then(resp => {
    menu = JSON.parse(resp.menu);
    document.querySelector("#btn_DEL").style.display = (menu.mtto.submenu.pagos.cmdDelete==1)?('inline'):('none');
    document.querySelector("#btn_NEW").style.display = (menu.mtto.submenu.pagos.cmdInsert==1)?('inline'):('none');
    
    document.querySelector("#txtBuscar").value = ("");
    appPagosGrid();
  });
}

function appPagosBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { appPagosGrid(); }
}

function appPagoNuevo(){
  document.querySelector("#btnInsert").style.display = (menu.mtto.submenu.pagos.cmdInsert==1)?('inline'):('none');
  document.querySelector("#btnUpdate").style.display = 'none';
  appFetch({ TipoQuery:'startPago' },rutaSQL).then(resp => {
    try{
      $(".form-group").removeClass("has-error");
      $("#txt_Fecha").datepicker("setDate",moment().format("DD/MM/YYYY"));
      document.querySelector("#txt_Importe").value = ("");
      document.querySelector("#cbo_Obliga").value = 1;
      appLlenarDataEnComboBox(resp.comboTipoProd,"#cbo_Producto",0); //tipos de pago
      document.querySelector("#grid").style.display = 'none';
      document.querySelector("#edit").style.display = 'block';
    } catch (err){
      console.log(err);
    }
  });
}

function appPagoView(pagoID){
  document.querySelector("#btnUpdate").style.display = (menu.mtto.submenu.pagos.cmdUpdate==1)?('inline'):('none');
  document.querySelector("#btnInsert").style.display = 'none';
  $(".form-group").removeClass("has-error");

  let datos = {
    TipoQuery : 'editPago',
    productoID : pagoID
  }

  appFetch(datos,rutaSQL).then(resp => {
    try{
      $(".form-group").removeClass("has-error");
      $("#txt_Fecha").datepicker("setDate",moment(resp.vencimiento).format("DD/MM/YYYY"));
      document.querySelector("#txt_Importe").value = appFormatMoney(resp.importe,2);
      document.querySelector("#cbo_Obliga").value = (resp.obliga);
      appLlenarDataEnComboBox(resp.comboTipoProd,"#cbo_Producto",resp.productoID); //tipo pago
      document.querySelector('#grid').style.display = 'none';
      document.querySelector('#edit').style.display = 'block';
    } catch(err){
      console.log(err);
    }
  });
}

function appPagoInsert(){
  let datos = modGetDataToDataBase();
  if(datos!=""){
    datos.TipoQuery = 'insPago';
    appFetch(datos,rutaSQL).then(resp => {
      appPagoCancel();
    });
  } else {
    alert("!!!Faltan Datos!!!");
  }
}

function appPagoUpdate(){
  let datos = modGetDataToDataBase();
  if(datos!=""){
    datos.TipoQuery = 'updPago';
    appFetch(datos,rutaSQL).then(resp => {
      appPagoCancel();
    });
  } else {
    alert("!!!Faltan Datos!!!");
  }
}

function appPagosBorrar(){
  //let arr = $('[name="chk_Borrar"]:checked').map(function(){return this.value}).get();
  let arr = Array.from(document.querySelectorAll('[name="chk_Borrar"]:checked')).map(function(obj){return obj.attributes[2].nodeValue});
  if(arr.length>0){
    if(confirm("¿Esta seguro de continuar?")) {
      appFetch({ TipoQuery:'delPagos', arr:arr },rutaSQL).then(resp => {
        if (resp.error == false) { //sin errores
          appPagoCancel();
        }
      });
    }
  } else {
    alert("NO eligio borrar ninguno");
  }
}

function appPagosBloquear(ID){
  let txtBuscar = document.querySelector("#txtBuscar").value;
  let datos = {
    TipoQuery : 'pagos_bloquear',
    productoID : ID,
    buscar : txtBuscar
  }
  appFetch(datos,rutaSQL).then(resp => {
    fnPagosLlenarGrid(resp.pagos);
  });
}

function appPagosCambiarImporteBatch(){
  let arr = Array.from(document.querySelectorAll('[name="chk_Borrar"]:checked')).map(function(obj){return obj.attributes[2].nodeValue});
  if(arr.length>0){
    res = appConvertToNumero(prompt("Ingrese cantidad para cambiar en bloque"));
    if(res>0){
      let txtBuscar = document.querySelector("#txtBuscar").value;
      let datos = {
        TipoQuery : "pagos_cambioMontoBloque",
        importe : res,
        buscar : txtBuscar
      }
  
      appFetch(datos,rutaSQL).then(resp => {
        fnPagosLlenarGrid(resp.pagos);
      });
    }
  } else {
    alert("NO eligio ninguno");
  }
}

function appPagosCambiarVcmtoBatch(){
  let arr = Array.from(document.querySelectorAll('[name="chk_Borrar"]:checked')).map(function(obj){return obj.attributes[2].nodeValue});
  if(arr.length>0){
    dato = prompt("Ingrese el año de vencimiento para cambiar en bloque");
    const result = parseInt(dato,10);
    if(!isNaN(result) && result>=2020){
      let txtBuscar = document.querySelector("#txtBuscar").value;
      let datos = {
        TipoQuery : "pagos_cambioVencimientoBloque",
        yyyy : result,
        buscar : txtBuscar
      }
  
      appFetch(datos,rutaSQL).then(resp => {
        fnPagosLlenarGrid(resp.pagos);
      });
    } else {
      alert("El valor ingresado no es un año valido");
    }
  } else {
    alert("NO eligio ninguno");
  }
}

function appPagoCancel(){
  appPagosGrid();
  document.querySelector('#grid').style.display = 'block';
  document.querySelector('#edit').style.display = 'none';
}

function modGetDataToDataBase(){
  let rpta = "";
  let esError = false;

  $(".form-group").removeClass("has-error");
  if(document.querySelector("#txt_Importe").value=="")  { document.querySelector("#div_Importe").className = "form-group has-error"; esError = true; }

  if(!esError){
    rpta = {
      productoID  : document.querySelector("#cbo_Producto").value,
      vencimiento : appConvertToFecha(document.querySelector("#txt_Fecha").value),
      importe : document.querySelector("#txt_Importe").value,
      obliga : document.querySelector("#cbo_Obliga").value,
    }
  }
  return rpta;
}

