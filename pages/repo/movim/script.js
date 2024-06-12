const rutaSQL = "pages/repo/movim/sql.php";
var menu = "";

//=========================funciones para Personas============================
async function appMovimGrid(){
  document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="7"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  try{
    const resp = await appAsynFetch({ 
      TipoQuery: 'selMovim',
      agenciaID: document.querySelector('#cboAgencias').value,
      usuarioID: document.querySelector('#cboUsuarios').value,
      monedaID: document.querySelector('#cboMonedas').value,
      fecha: appConvertToFecha(document.querySelector('#txtFecha').value,'')
    },rutaSQL);

    //respuesta
    if(resp.movim.length>0){
      let totIngresos = 0;
      let totSalidas = 0;
      let fila = "";
      let foot = "";
      resp.movim.forEach((valor,key)=>{
        totIngresos += valor.ingreso;
        totSalidas += valor.salida;
        fila += '<tr>'+
                '<td>'+(valor.hora)+'</td>'+
                '<td>'+(valor.voucher)+'</td>'+
                '<td>'+(valor.codsocio+' '+valor.socio)+'</td>'+
                '<td>'+(valor.codprod+' '+valor.producto)+'</td>'+
                '<td>'+(valor.codmov+' '+valor.movim)+'</td>'+
                '<td style="text-align:right;">'+((valor.ingreso>0)?(appFormatMoney(valor.ingreso,2)):('-'))+'</td>'+
                '<td style="text-align:right;">'+((valor.salida>0)?(appFormatMoney(valor.salida,2)):('-'))+'</td>'+
                '</tr>';
      });
      foot = '<tr>'+
              '<td colspan="5" style="text-align:right;"><b>TOTAL GENERAL</b></td>'+
              '<td style="text-align:right;border-bottom-style:double;"><b>'+(appFormatMoney(totIngresos,2))+'</b></td>'+
              '<td style="text-align:right;border-bottom-style:double;"><b>'+(appFormatMoney(totSalidas,2))+'</b></td>'+
              '</tr>'+
              '<tr><td colspan="7"></td></tr>';
      document.querySelector('#grdDatos').innerHTML = (fila);
      
    }else{
      document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="7" style="text-align:center;color:red;">Sin Resultados</td></tr>');
    }
    document.querySelector('#grdCount').innerHTML = (resp.movim.length);
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appMovimReset(){
  $('#txtFecha').datepicker("setDate",moment().format("DD/MM/YYYY"));
  try{
    const resp = await appAsynFetch({TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    menu = JSON.parse(resp.menu);
      
    const rpta = await appAsynFetch({ TipoQuery:'StartMovim' },rutaSQL);
    appLlenarDataEnComboBox(rpta.comboAgencias,"#cboAgencias",0);
    appLlenarDataEnComboBox(rpta.comboMonedas,"#cboMonedas",0);
    appLlenarDataEnComboBox(rpta.comboUsuarios,"#cboUsuarios",0);
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appMovimBuscar(e){
  if(e.keyCode === 13) { appMovimGrid(); }
}

async function appMovimView(voucherID){
  $(".form-group").removeClass("has-error");
  document.querySelector('#grid').style.display = 'none';
  document.querySelector('#edit').style.display = 'block';
  try{
    const resp = await appAsynFetch({
      TipoQuery : 'viewMovim',
      voucherID : voucherID
    },rutaSQL);

    //cabecera
    document.querySelector("#hid_movimID").value = resp.cab.ID;
    document.querySelector("#lbl_pagoAgencia").innerHTML = (resp.cab.agencia);
    document.querySelector("#lbl_pagoTipoOper").innerHTML = (resp.cab.tipo_oper+" / "+resp.cab.moneda);
    document.querySelector("#lbl_pagoCodigo").innerHTML = (resp.cab.codigo);
    document.querySelector("#lbl_pagoFecha").innerHTML = (resp.cab.fecha+" <small style='font-size:10px;'>"+resp.cab.hora+"</small>");
    document.querySelector("#lbl_pagoSocio").innerHTML = (resp.cab.socio);
    document.querySelector("#lbl_tipodui").innerHTML = (resp.cab.tipodui+":");
    document.querySelector("#lbl_pagoNroDUI").innerHTML = (resp.cab.nrodui);
    document.querySelector("#lbl_pagoCajera").innerHTML = (resp.cab.cajera);
    document.querySelector("#lbl_pagoImporte").innerHTML = "<small style='font-size:10px;'>"+resp.cab.mon_abrevia+"</small> "+appFormatMoney(resp.cab.importe,2);

    //detalle
    if(resp.deta.length>0){
      console.log(resp.deta);
      let fila = "";
      resp.deta.forEach((valor,key)=>{
        fila += '<tr>';
        fila += '<td style="text-align:center;">'+(valor.item)+'</td>';
        fila += '<td>'+(valor.tipo_mov)+'</td>';
        fila += '<td>'+(valor.producto)+'</td>';
        fila += '<td style="text-align:right;">'+(appFormatMoney(valor.importe,2))+'</td>';
        fila += '</tr>';
      });
      document.querySelector('#grdDetalleDatos').innerHTML = (fila);
    }else{
      document.querySelector('#grdDetalleDatos').innerHTML = ('<tr><td colspan="4" style="text-align:center;color:red;">Sin DETALLE</td></tr>');
    }

    document.querySelector('#grid').style.display = 'none';
    document.querySelector('#edit').style.display = 'block';
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appMovimRefresh(){
  let codigo = document.querySelector("#hid_movimID").value;
  appMovimView(codigo);
}

function appMovimCancel(){
  appMovimGrid();
  document.querySelector('#grid').style.display = 'block';
  document.querySelector('#edit').style.display = 'none';
}
