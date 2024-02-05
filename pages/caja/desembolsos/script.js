const rutaSQL = "pages/caja/desembolsos/sql.php";
var menu = "";
var objDesemb = null;

//=========================funciones para Personas============================
function appDesembGrid(){
  document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="9"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  let txtBuscar = document.querySelector("#txtBuscar").value;
  let datos = {
    TipoQuery: 'selDesembolsos',
    buscar: txtBuscar
  };

  appFetch(datos,rutaSQL).then(resp => {
    let disabledDelete = (menu.caja.submenu.desembolsos.cmdDelete===1) ? "" : "disabled";
    document.querySelector("#chk_All").disabled = (menu.caja.submenu.desembolsos.cmdDelete===1) ? false : true;
    if(resp.tabla.length>0){
      let fila = "";
      resp.tabla.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td><input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'" '+disabledDelete+'/></td>'+
                '<td style="text-align:center;"><a href="javascript:appDesembView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.codigo)+'</a></td>'+
                '<td>'+(moment(valor.fecha_solicita).format("DD/MM/YYYY"))+'</td>'+
                '<td>'+(moment(valor.fecha_aprueba).format("DD/MM/YYYY"))+'</td>'+
                '<td>'+(valor.nro_dui)+'</td>'+
                '<td>'+(valor.alumno)+'</td>'+
                '<td>'+(valor.nivel)+' &raquo; '+(valor.grado)+' &raquo; '+(valor.seccion)+'</td>'+
                '</tr>';
      });
      $('#grdDatos').html(fila);
    }else{
      $('#grdDatos').html('<tr><td colspan="9" style="text-align:center;color:red;">Sin Resultados '+((txtBuscar==="")?(""):("para "+txtBuscar))+'</td></tr>');
    }
    $('#grdCount').html(resp.tabla.length+"/"+resp.cuenta);
  });
}

function appDesembReset(){
  appFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php").then(resp => {
    objDesemb = null;
    menu = JSON.parse(resp.menu);
    document.querySelector("#btn_DEL").style.display = (menu.caja.submenu.desembolsos.cmdDelete==1)?('inline'):('none');

    document.querySelector("#txtBuscar").value = ("");
    document.querySelector("#grdDatos").innerHTML = ("");
    document.querySelector("#div_PersAuditoria").style.display = ((resp.rolID==101)?('block'):('none'));
    appDesembGrid();
  });
}

function appDesembBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { load_flag = 0; $('#grdDatos').html(""); appDesembGrid(); }
}

function appDesembBotonCancel(){
  appDesembGrid();
  $('#grid').show();
  $('#edit').hide();
}

function appDesembBotonDesembolsar(){
  // //obliga a desembolsar en la fecha actual
  // let objDesemb = appConvertToFecha(document.querySelector("#txt_DesembFecha").value,"-");
  // let inicio = appConvertToFecha(document.querySelector("#lbl_DesembFechaIniCred").innerHTML,"-");
  // console.log("desemb: "+desemb);
  // console.log("inicio: "+inicio);
  // console.log("resta: "+(moment(desemb).diff(moment(inicio),"days")));

  if(confirm("¿Esta seguro de continuar?")) {
    let datos = {
      TipoQuery : 'ejecutarDesembolso',
      ID : desemb.id,
      socioID : desemb.socioID,
      monedaID : desemb.monedaID,
      agenciaID : desemb.agenciaID,
      tipopagoID : desemb.tipopagoID,
      tipocredID : desemb.tipocredID,
      productoID : desemb.productoID,
      cod_prod : document.querySelector("#lbl_DesembCodigo").innerHTML,
      fecha_desemb : appConvertToFecha(document.querySelector("#txt_DesembFecha").value,""),
      fecha_otorga : appConvertToFecha(document.querySelector("#lbl_DesembFechaOtorga").innerHTML),
      importe : appConvertToNumero(document.querySelector("#lbl_DesembImporte").innerHTML),
      tasa_cred : appConvertToNumero(document.querySelector("#lbl_DesembTasaCred").innerHTML),
      tasa_desgr : appConvertToNumero(document.querySelector("#lbl_DesembTasaDesgr").innerHTML),
      nrocuotas : document.querySelector("#lbl_DesembNrocuotas").innerHTML,
      pivot : (desemb.tipocredID==1)?(appConvertToFecha(document.querySelector("#lbl_DesembFechaPriCuota").innerHTML)):(document.querySelector("#lbl_DesembFrecuencia").innerHTML),
      observac: document.querySelector("#lbl_DesembObservac").innerHTML
    }
    // console.log(datos);
    appFetch(datos,rutaSQL).then(resp => {
      console.log(resp);
      if (!resp.error) { 
        if(confirm("¿Desea Imprimir el desembolso?")){
          $("#modalPrint").modal("show");
          let urlServer = appUrlServer()+"pages/caja/desembolsos/rpt.voucher.php?movimID="+resp.movimID;
          $("#contenedorFrame").html('<object id="objPDF" type="text/html" data="'+urlServer+'" width="100%" height="500px"></object>');
        }
        appDesembBotonCancel(); 
      }
    });
  }
}

function appDesembBotonBorrar(){
  let arr = Array.from(document.querySelectorAll('[name="chk_Borrar"]:checked')).map(function(obj){return obj.attributes[2].nodeValue});
  if(arr.length>0){
    if(confirm("¿Esta seguro de continuar?")) {
      appFetch({ TipoQuery:'delDesembolsos', arr:arr },rutaSQL).then(resp => {
        if (resp.error == false) { //sin errores
          //console.log(resp);
          appDesembGrid();
        }
      });
    }
  } else {
    alert("NO eligio borrar ninguno");
  }
}

function appDesembView(matriculaID){
  let datos = {
    TipoQuery : 'viewDesembolso',
    matriculaID : matriculaID
  };
  
  appFetch(datos,rutaSQL).then(resp => {
    console.log(menu);
    //tabs default en primer tab
    $('.nav-tabs li').removeClass('active');
    $('.tab-content .tab-pane').removeClass('active');
    $('a[href="#datosSoliCred"]').closest('li').addClass('active');
    $('#datosSoliCred').addClass('active');
    document.querySelector("#btnInsert").style.display = (menu.caja.submenu.desembolsos.cmdUpdate==1)?('inline'):('none');
    document.querySelector('#grid').style.display = 'none';
    document.querySelector('#edit').style.display = 'block';

    appDesembSetData(resp.tablaDesembolso);  //pestaña Solicitud de credito
    appPersonaSetData(resp.tablaPers);
  });
}

function appDesembSetData(data){
  //pestaña de desembolso
  objDesemb = {
    id : data.ID,
    tipopagoID : 164,
    tipocredID : data.tipocredID,
    productoID : data.productoID,
    monedaID : data.monedaID,
    socioID : data.socioID,
    agenciaID : data.agenciaID
  }
  document.querySelector("#lbl_FormAprueba").style.color = (data.aprueba=="")?("#D00"):("#777");
  document.querySelector("#txt_DesembFecha").disabled = (data.rolUser==data.rolROOT) ? (false):(true);
  $('#txt_DesembFecha').datepicker("setDate",moment(data.fecha_desemb).format("DD/MM/YYYY"));

  document.querySelector('#lbl_DesembSocio').innerHTML = (data.socio);
  document.querySelector('#lbl_DesembFechaSoliCred').innerHTML = (moment(data.fecha_solicred).format("DD/MM/YYYY"));
  document.querySelector("#lbl_DesembCodigo").innerHTML = (data.codigo);
  document.querySelector("#lbl_DesembMoneda").innerHTML = (data.moneda);
  document.querySelector("#lbl_DesembClasifi").innerHTML = (data.clasifica);
  document.querySelector("#lbl_DesembCondicion").innerHTML = (data.condicion);
  document.querySelector("#lbl_DesembAgencia").innerHTML = (data.agencia);
  document.querySelector("#lbl_DesembPromotor").innerHTML = (data.promotor);
  document.querySelector("#lbl_DesembAnalista").innerHTML = (data.analista);
  document.querySelector("#lbl_DesembAprueba").innerHTML = (data.aprueba);
  document.querySelector("#lbl_DesembTipoSBS").innerHTML = (data.tiposbs);
  document.querySelector("#lbl_DesembDestSBS").innerHTML = (data.destsbs);
  document.querySelector("#lbl_DesembPrestamoID").innerHTML = (data.ID);
  document.querySelector("#lbl_DesembTipoCred").innerHTML = (data.tipocred);
  document.querySelector("#lbl_DesembProducto").innerHTML = (data.producto);
  document.querySelector("#lbl_DesembImporte").innerHTML = (appFormatMoney(data.importe,2));
  document.querySelector("#lbl_DesembNrocuotas").innerHTML = (data.nrocuotas);
  document.querySelector("#lbl_DesembTasaCred").innerHTML = (appFormatMoney(data.tasa_cred,2));
  document.querySelector("#lbl_DesembTasaMora").innerHTML = (appFormatMoney(data.tasa_mora,2));
  document.querySelector("#lbl_DesembTasaDesgr").innerHTML = (appFormatMoney(data.tasa_desgr,2));
  document.querySelector("#lbl_DesembFechaOtorga").innerHTML = (moment(data.fecha_otorga).format("DD/MM/YYYY"));
  document.querySelector("#lbl_DesembFechaPriCuota").innerHTML = (moment(data.fecha_pricuota).format("DD/MM/YYYY"));
  document.querySelector("#lbl_DesembEtqFrecuencia").style.display = (data.tipocredID=="1")?("none"):("inherit");
  document.querySelector("#lbl_DesembFrecuencia").innerHTML = (data.frecuencia);
  document.querySelector("#lbl_DesembCuota").innerHTML = ("&nbsp;<small style='font-size:10px;'>"+data.mon_abrevia+"</small>&nbsp;&nbsp;"+data.cuota+"&nbsp;");
  document.querySelector("#lbl_DesembObservac").innerHTML = (data.observac);
}

function appPersonaSetData(data){
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
  document.querySelector("#hid_PersID").value = (data.ID);
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