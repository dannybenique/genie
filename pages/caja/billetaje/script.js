const rutaSQL = "pages/caja/billetaje/sql.php";
var menu = "";

//=========================funciones para Personas============================
async function appBillGrid(){
  //loader
  document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="9"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  const disabledDelete = (menu.caja.submenu.billetaje.cmdDelete===1) ? "" : "disabled";
  try{
    const resp = await appAsynFetch({
      TipoQuery: 'selBilletaje',
      usuarioID: document.querySelector("#cboUsuarios").value,
      monedaID: document.querySelector("#cboMonedas").value
    }, rutaSQL);
  
    //respuesta
    document.querySelector("#chk_All").disabled = (menu.caja.submenu.billetaje.cmdDelete===1) ? false : true;
    if(resp.tabla.length>0){
      let fila = "";
      resp.tabla.forEach((valor,key)=>{
        fila += '<tr>';
        fila += '<td><input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'" '+disabledDelete+'/></td>';
        fila += '<td><a href="javascript:appBillView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(moment(valor.fecha).format("DD/MM/YYYY"))+'</a></td>';
        fila += '<td>'+(valor.agencia)+'</td>';
        fila += '<td>'+(valor.empleado)+'</td>';
        fila += '<td>'+(valor.moneda)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.total,2)+'</td>';
        fila += '</tr>';
      });
      $('#grdDatos').html(fila);
    }else{
      $('#grdDatos').html('<tr><td colspan="5" style="text-align:center;color:red;">Sin Resultados</td></tr>');
    }
    $('#grdCount').html(resp.tabla.length+"/"+resp.cuenta);
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appBillReset(){
  try{
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    menu = JSON.parse(resp.menu);
    document.querySelector("#btn_DEL").style.display = (menu.caja.submenu.billetaje.cmdDelete==1)?('inline'):('none');
    document.querySelector("#btn_NEW").style.display = (menu.caja.submenu.billetaje.cmdInsert==1)?('inline'):('none');
    
    const rpta = await appAsynFetch({ TipoQuery:'StartBilletaje' },rutaSQL);
    appLlenarDataEnComboBox(rpta.comboMonedas,"#cboMonedas",0);
    appLlenarDataEnComboBox(rpta.comboUsuarios,"#cboUsuarios",((rpta.rolID=rpta.root)?(0):(rpta.userID)));
    document.querySelector("#cboUsuarios").disabled = ((rpta.rolID==rpta.root)?(false):(true));
    appBillGrid();
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appBillBotonCancel(){
  appBillGrid();
  $('#grid').show();
  $('#edit').hide();
}

async function appBillBotonBorrar(){
  let arr = Array.from(document.querySelectorAll('[name="chk_Borrar"]:checked')).map(function(obj){return obj.attributes[2].nodeValue});
  if(arr.length>0){
    if(confirm("¿Esta seguro de continuar?")) {
      try{
        const resp = await appAsynFetch({ TipoQuery:'delBilletaje', arr:arr },rutaSQL);
        if(!resp.error) { appBillGrid(); }
      } catch(err){
        console.error('Error al cargar datos:', err);
      }
    }
  } else {
    alert("NO eligio borrar ninguno");
  }
}

async function appBillBotonNuevo(){
  document.querySelector("#btnInsert").style.display = (menu.caja.submenu.billetaje.cmdInsert==1)?('inline'):('none');
  document.querySelector("#btnUpdate").style.display = 'none';
  
  try{
    const resp = await appAsynFetch({
      TipoQuery:'newBilletaje',
      monedaID : document.querySelector("#cboMonedas").value,
      usuarioID : document.querySelector("#cboUsuarios").value 
    },rutaSQL);
      
    //respuesta  
    $(".form-group").removeClass("has-error");
    $(".billetaje_mon").html(resp.mon_abrevia);
    $('#txt_Fecha').datepicker("setDate",moment(resp.fecha).format("DD/MM/YYYY"));
    appLlenarDataEnComboBox(resp.comboMonedas,"#cbo_MonedasEdit",document.querySelector("#cboMonedas").value);
    appLlenarDataEnComboBox(resp.comboAgencias,"#cbo_AgenciasEdit",0);
    document.querySelector("#hid_billID").value = 0;
    document.querySelector("#hid_usuarioID").value = (document.querySelector("#cboUsuarios").value);
    document.querySelector("#txt_UsuarioEdit").value = resp.usuario;
    document.querySelector("#txt_Mx200").value = "";
    document.querySelector("#txt_Mx100").value = "";
    document.querySelector("#txt_Mx50").value = "";
    document.querySelector("#txt_Mx20").value = "";
    document.querySelector("#txt_Mx10").value = "";
    document.querySelector("#txt_Mx5").value = "";
    document.querySelector("#txt_Mx2").value = "";
    document.querySelector("#txt_Mx1").value = "";
    document.querySelector("#txt_Mx05").value = "";
    document.querySelector("#txt_Mx02").value = "";
    document.querySelector("#txt_Mx01").value = "";
    document.querySelector("#txt_MxTotal").innerHTML = "0.00";
    
    document.querySelector("#grid").style.display = 'none';
    document.querySelector("#edit").style.display = 'block';
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appBillView(billID){
  try{
    const resp = await appAsynFetch({
      TipoQuery : 'viewBilletaje',
      billID : billID
    },rutaSQL);

    document.querySelector("#btnUpdate").style.display = (resp.rolUSR===resp.rolROOT)?('inline'):((resp.fecha==resp.tabla.fecha && menu.caja.submenu.billetaje.cmdUpdate==1)?('inline'):('none'));
    document.querySelector("#btnInsert").style.display = 'none';
    document.querySelector('#grid').style.display = 'none';
    document.querySelector('#edit').style.display = 'block';
  
    $(".form-group").removeClass("has-error");
    $(".billetaje_mon").html(resp.tabla.mon_abrevia);
    $('#txt_Fecha').datepicker("setDate",moment(resp.tabla.fecha).format("DD/MM/YYYY"));
    appLlenarDataEnComboBox(resp.comboMonedas,"#cbo_MonedasEdit",resp.tabla.monedaID);
    appLlenarDataEnComboBox(resp.comboAgencias,"#cbo_AgenciasEdit",resp.tabla.agenciaID);
    
    document.querySelector("#hid_billID").value = resp.tabla.ID;
    document.querySelector("#hid_usuarioID").value = resp.tabla.usuarioID;
    document.querySelector("#txt_UsuarioEdit").value = resp.tabla.usuario;
    document.querySelector("#txt_Mx200").value = resp.tabla.mx_200;
    document.querySelector("#txt_Mx100").value = resp.tabla.mx_100;
    document.querySelector("#txt_Mx50").value = resp.tabla.mx_50;
    document.querySelector("#txt_Mx20").value = resp.tabla.mx_20;
    document.querySelector("#txt_Mx10").value = resp.tabla.mx_10;
    document.querySelector("#txt_Mx5").value = resp.tabla.mx_5;
    document.querySelector("#txt_Mx2").value = resp.tabla.mx_2;
    document.querySelector("#txt_Mx1").value = resp.tabla.mx_1;
    document.querySelector("#txt_Mx05").value = resp.tabla.mx_05;
    document.querySelector("#txt_Mx02").value = resp.tabla.mx_02;
    document.querySelector("#txt_Mx01").value = resp.tabla.mx_01;
    document.querySelector("#txt_MxTotal").innerHTML = appFormatMoney(resp.tabla.mx_total,2);
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appBillCalcular(){
  let mx200 = $.isNumeric(document.querySelector("#txt_Mx200").value) ? (document.querySelector("#txt_Mx200").value*200) : (0);
  let mx100 = $.isNumeric(document.querySelector("#txt_Mx100").value) ? (document.querySelector("#txt_Mx100").value*100) : (0);
  let mx50 = $.isNumeric(document.querySelector("#txt_Mx50").value) ? (document.querySelector("#txt_Mx50").value*50) : (0);
  let mx20 = $.isNumeric(document.querySelector("#txt_Mx20").value) ? (document.querySelector("#txt_Mx20").value*20) : (0);
  let mx10 = $.isNumeric(document.querySelector("#txt_Mx10").value) ? (document.querySelector("#txt_Mx10").value*10) : (0);
  let mx5 = $.isNumeric(document.querySelector("#txt_Mx5").value) ? (document.querySelector("#txt_Mx5").value*5) : (0);
  let mx2 = $.isNumeric(document.querySelector("#txt_Mx2").value) ? (document.querySelector("#txt_Mx2").value*2) : (0);
  let mx1 = $.isNumeric(document.querySelector("#txt_Mx1").value) ? (document.querySelector("#txt_Mx1").value*1) : (0);
  let mx05 = $.isNumeric(document.querySelector("#txt_Mx05").value) ? (document.querySelector("#txt_Mx05").value*0.5) : (0);
  let mx02 = $.isNumeric(document.querySelector("#txt_Mx02").value) ? (document.querySelector("#txt_Mx02").value*0.2) : (0);
  let mx01 = $.isNumeric(document.querySelector("#txt_Mx01").value) ? (document.querySelector("#txt_Mx01").value*0.1) : (0);
  let total = mx200 + mx100 + mx50 + mx20 + mx10 + mx5 + mx2 + mx1 + mx05 + mx02 + mx01;

  document.querySelector("#txt_MxTotal").innerHTML = appFormatMoney(total,2);
}

async function appBillInsert(){
  const datos = appGetDataToDataBase();
  if(datos!=null){
    datos.TipoQuery = 'insBilletaje';
    try{
      const resp = await appAsynFetch(datos,rutaSQL);
      if(!resp.error){
        appBillGrid();
        appBillBotonCancel();
      }
    } catch(err){
      console.error('Error al cargar datos:', err);
    }
  } else {
    alert("!!!Faltan Datos!!!");
  }
}

async function appBillUpdate(){
  const datos = appGetDataToDataBase();
  if(datos!=null){
    datos.TipoQuery = 'updBilletaje';
    try{
      const resp = await appAsynFetch(datos,rutaSQL);
      if(!resp.error){
        appBillGrid();
        appBillBotonCancel();
      }
    } catch(err){
      console.error('Error al cargar datos:', err);
    }
  } else {
    alert("!!!Faltan Datos!!!");
  }
}

function appGetDataToDataBase(){
  let rpta = null;
  let esError = false;

  $(".form-group").removeClass("has-error");
  if(document.querySelector("#txt_Fecha").value=="") { document.querySelector("#pn_Fecha").className = "form-group has-error"; esError = true; }
  
  if(!esError){
    rpta = {
      ID : document.querySelector("#hid_billID").value,
      usuarioID: document.querySelector("#hid_usuarioID").value,
      monedaID : document.querySelector("#cbo_MonedasEdit").value,
      agenciaID : document.querySelector("#cbo_AgenciasEdit").value,
      fecha : appConvertToFecha(document.querySelector("#txt_Fecha").value),
      mx200 : ($.isNumeric(document.querySelector("#txt_Mx200").value)?(document.querySelector("#txt_Mx200").value):(0)),
      mx100 : ($.isNumeric(document.querySelector("#txt_Mx100").value)?(document.querySelector("#txt_Mx100").value):(0)),
      mx50 : ($.isNumeric(document.querySelector("#txt_Mx50").value)?(document.querySelector("#txt_Mx50").value):(0)),
      mx20 : ($.isNumeric(document.querySelector("#txt_Mx20").value)?(document.querySelector("#txt_Mx20").value):(0)),
      mx10 : ($.isNumeric(document.querySelector("#txt_Mx10").value)?(document.querySelector("#txt_Mx10").value):(0)),
      mx5 : ($.isNumeric(document.querySelector("#txt_Mx5").value)?(document.querySelector("#txt_Mx5").value):(0)),
      mx2 : ($.isNumeric(document.querySelector("#txt_Mx2").value)?(document.querySelector("#txt_Mx2").value):(0)),
      mx1 : ($.isNumeric(document.querySelector("#txt_Mx1").value)?(document.querySelector("#txt_Mx1").value):(0)),
      mx05 : ($.isNumeric(document.querySelector("#txt_Mx05").value)?(document.querySelector("#txt_Mx05").value):(0)),
      mx02 : ($.isNumeric(document.querySelector("#txt_Mx02").value)?(document.querySelector("#txt_Mx02").value):(0)),
      mx01 : ($.isNumeric(document.querySelector("#txt_Mx01").value)?(document.querySelector("#txt_Mx01").value):(0)),
      mxtotal : appConvertToNumero(document.querySelector("#txt_MxTotal").innerHTML)
    }
  }
  return rpta;
}