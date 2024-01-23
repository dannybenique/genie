const rutaSQL = "pages/oper/simula/sql.php";

//=========================funciones para Simulacion Ahorros============================
function appAhorrosFechaFin(){
  let tiempo = document.querySelector("#txt_TiempoMeses").value;
  let fechaIni = appConvertToFecha(document.querySelector("#date_FechaIni").value,"-");
  let fechaFin = moment(fechaIni).add(tiempo,'months');

  document.querySelector("#date_FechaFin").innerHTML = (fechaFin.format("DD/MM/YYYY"));
  document.querySelector("#dias_FechaFin").innerHTML = (fechaFin.diff(fechaIni,'days'));
}

function appAhorrosReset(){
  $('#date_FechaIni').datepicker("setDate",moment().format("DD/MM/YYYY"));
  appAhorrosFechaFin();
  appFetch({ TipoQuery:"selProductos" },rutaSQL).then(resp => {
    appLlenarDataEnComboBox(resp,"#cbo_Productos",0); 
  });
}

function appAhorrosGenerarIntereses(){
  let tiempo = document.querySelector("#txt_TiempoMeses").value;
  let productoID = document.querySelector("#cbo_Productos").value;
  let fecha = appConvertToFecha(document.querySelector("#date_FechaIni").value,"-");
  let capital = appConvertToNumero(document.querySelector("#txt_Importe").value);
  let datos = {
    TipoQuery : 'simulaAhorro',
    fechaIni : moment(fecha).format("YYYYMMDD"),
    fechaFin : moment(fecha).add(tiempo,'months').format("YYYYMMDD"),
    productoID : productoID,
    importe : capital,
    segDesgr : 0.1,
    tasa : appConvertToNumero($("#txt_Tasa").val())
  }
  appFetch(datos,rutaSQL).then(resp => {
    let fila = "";
    let total = 0;
    let interes = appConvertToNumero(resp.interes);

    switch(productoID){
      case "106": //ahorrosuperpension
        interes = interes/tiempo;
        total = capital+interes;
        for(x=1; x<=tiempo; x++){
          fila += '<tr>';
          fila += '<td>'+(x)+'</td>';
          fila += '<td>'+(moment(fecha).add(x,'months').format("DD/MM/YYYY"))+'</td>';
          fila += '<td style="text-align:right;">'+appFormatMoney((x==tiempo)?capital:0,2)+'</td>';
          fila += '<td style="text-align:right;">'+appFormatMoney(interes,2)+'</td>';
          fila += '<td style="text-align:right;">'+appFormatMoney((x==tiempo)?total:interes,2)+'</td>';
          fila += '<td></td>';
          fila += '</tr>';
        }
        fila += '<tr style="color:blue;">';
        fila += '<td colspan="2" style="text-align:center;">TOTAL</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(capital,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(appConvertToNumero(resp.interes),2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(capital+appConvertToNumero(resp.interes),2)+'</td>';
        fila += '<td></td>';
        fila += '</tr>';
        break;
      default:
        fila += '<tr>';
        fila += '<td>'+(1)+'</td>';
        fila += '<td>'+(moment(fecha).add(tiempo,'months').format("DD/MM/YYYY"))+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(capital,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(interes,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(capital+interes,2)+'</td>';
        fila += '<td></td>';
        fila += '</tr>';
        break;
    }

    document.querySelector('#grdDatos').innerHTML = (fila);
  });
}


//=========================funciones para Simulacion Creditos============================
function appCreditosReset(){
  $('#txt_FechaSimula').datepicker("setDate",moment().format("DD/MM/YYYY"));
  $('#txt_FechaPriCuota').datepicker("setDate",moment().add(1,'M').format("DD/MM/YYYY"));
  document.querySelector('#txt_TEA').value = (30);
  document.querySelector('#txt_NroCuotas').value = (12);
  document.querySelector('#txt_Importe').value = (1000);
  document.querySelector('#txt_SegDesgr').value = (0.1);
  document.querySelector('#txt_Frecuencia').value = (14);
  document.querySelector('#grdDatos').innerHTML = ("");
  document.querySelector('#lbl_TEA').innerHTML = ("0.00 %");
  document.querySelector('#lbl_TEM').innerHTML = ("0.00 %");
  document.querySelector('#lbl_TED').innerHTML = ("0.00 %");
}

function appCreditosCambiarTipoCredito(){
  switch(document.querySelector("#cbo_TipoCredito").value){
    case "1":
      document.querySelector("#div_FechaPriCuota").style.display = 'block';
      document.querySelector("#div_Frecuencia").style.display = 'none';
      break;
    case "2":
      document.querySelector("#div_Frecuencia").style.display = 'block';
      document.querySelector("#div_FechaPriCuota").style.display = 'none';
      break;
  }
}

function appCreditosGenerarPlanPagos(){
  document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="9"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  let datos = {
    TipoQuery : 'simulaCredito',
    TipoCredito : document.querySelector("#cbo_TipoCredito").value,
    importe : appConvertToNumero(document.querySelector("#txt_Importe").value),
    TEA : document.querySelector("#txt_TEA").value,
    segDesgr : document.querySelector("#txt_SegDesgr").value,
    nroCuotas: document.querySelector("#txt_NroCuotas").value,
    fecha : appConvertToFecha(document.querySelector("#txt_FechaSimula").value,""),
    pricuota : appConvertToFecha(document.querySelector("#txt_FechaPriCuota").value,""),
    frecuencia : document.querySelector("#txt_Frecuencia").value
  }

  appFetch(datos,rutaSQL).then(resp => {
    if(resp.tabla.length>0){
      let fila = "";
      let tot_Cuota = 0;
      let tot_Capital = 0;
      let tot_Interes = 0;
      let tot_Desgrav = 0;

      resp.tabla.forEach((valor,key)=>{
        tot_Cuota += Number(valor.cuota);
        tot_Capital += Number(valor.capital);
        tot_Interes += Number(valor.interes);
        tot_Desgrav += Number(valor.desgr);

        fila += '<tr>';
        fila += '<td>'+(valor.nro)+'</td>';
        fila += '<td style="color:#aaa;">'+(valor.dias)+'</td>';
        fila += '<td>'+(moment(valor.fecha).format("DD/MM/YYYY"))+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.cuota,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.capital,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.interes,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.desgr,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.saldo,2)+'</td>';
        fila += '<td></td>';
        fila += '</tr>';
      });
      //totales
      fila += '<tr>';
      fila += '<td style="text-align:center;" colspan="3"><b>Totales</b></td>';
      fila += '<td style="text-align:right;"><b>'+appFormatMoney(tot_Cuota,2)+'</b></td>';
      fila += '<td style="text-align:right;"><b>'+appFormatMoney(tot_Capital,2)+'</b></td>';
      fila += '<td style="text-align:right;"><b>'+appFormatMoney(tot_Interes,2)+'</b></td>';
      fila += '<td style="text-align:right;"><b>'+appFormatMoney(tot_Desgrav,2)+'</b></td>';
      fila += '<td style="" colspan="2"></td>';
      fila += '</tr>';

      document.querySelector('#grdDatos').innerHTML = (fila);
      document.querySelector('#lbl_TEA').innerHTML = (appFormatMoney(resp.tea,2)+" %");
      document.querySelector('#lbl_TEM').innerHTML = (resp.tem+" %");
      document.querySelector('#lbl_TED').innerHTML = (resp.ted+" %");
    }else{
      document.querySelector('#grdDatos').innerHTML = ("");
    }
  });
}