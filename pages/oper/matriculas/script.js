const rutaSQL = "pages/oper/matriculas/sql.php";
var viewTotalPagado = false;
var viewTotalPorVencer = false;
var matriculaID = 0;
var menu = "";

//=========================funciones para Personas============================
async function appMatriculasGrid(){
  document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="10"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  const txtBuscar = document.querySelector("#txtBuscar").value;
  try{
    const resp = await appAsynFetch({
      TipoQuery: 'matricula_Select',
      buscar: txtBuscar
    },rutaSQL);
  
    //respuesta
    if(resp.tabla.length>0){
      let fila = "";
      resp.tabla.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td>'+(moment(valor.fecha).format("DD/MM/YYYY"))+'</td>'+
                '<td>'+(valor.nro_dui)+'</td>'+
                '<td>'+(valor.alumno)+'</td>'+
                '<td style="text-align:center;">'+(valor.yyyy)+'</td>'+
                '<td><a href="javascript:appMatriculasView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.codigo)+' &raquo; '+(valor.nivel)+' &raquo; '+(valor.grado)+' &raquo; '+(valor.seccion)+'</a></td>'+
                '<td style="text-align:right;">'+appFormatMoney(valor.importe,2)+'</td>'+
                '<td style="text-align:right;">'+appFormatMoney(valor.saldo,2)+'</td>'+
                '<td style="text-align:center;">'+(valor.nro_cuotas)+'</td>'+
                '</tr>';
      });
      $('#grdDatos').html(fila);
    }else{
      let res = (txtBuscar==="") ? (""):("para "+txtBuscar);
      $('#grdDatos').html('<tr><td colspan="10" style="text-align:center;color:red;">Sin Resultados '+(res)+'</td></tr>');
    }
    $('#grdCount').html(resp.tabla.length+"/"+resp.cuenta);
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appMatriculasReset(){
  try{
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    
    document.querySelector("#txtBuscar").value = ("");
    matriculaID = 0;
    menu = JSON.parse(resp.menu);
    appMatriculasGrid();
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appMatriculasBuscar(e){
  if(e.keyCode === 13) { load_flag = 0; $('#grdDatosBody').html(""); appMatriculasGrid(); }
}

function appMatriculasRefreshTabla(){
  document.querySelector("#txtBuscar").value = ("");
  appMatriculasGrid();
}

function appMatriculasBotonCancel(){
  appMatriculasGrid();
  $('#grid').show();
  $('#edit').hide();
}

function appMatriculasRefreshCuotas(){
  appMatriculasView(matriculaID);
}

async function appMatriculasView(matriculaID){
  $('#grdDetalleDatos').html('<tr><td colspan="7"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  try{
    const resp = await appAsynFetch({
      TipoQuery : 'matricula_View',
      matriculaID : matriculaID
    },rutaSQL);
    
    //respuesta
    appCabeceraSetData(resp.cabecera);
    appDetalleSetData(resp.detalle);
    document.querySelector('#grid').style.display = 'none';
    document.querySelector('#edit').style.display = 'block';
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appCabeceraSetData(data){
  matriculaID = (data.ID);
  document.querySelector('#lbl_matriAlumno').innerHTML = (data.alumno);
  document.querySelector('#lbl_matriNroDUI').innerHTML = (data.nro_dui);
  document.querySelector('#lbl_matriCodigo').innerHTML = ('<i class="fa fa-info-circle" style="font-size:11px;"></i> ') + (data.codigo);
  document.querySelector('#lbl_matriCodigo').title = ("ID: "+data.ID);
  document.querySelector('#lbl_matriFechaMatricula').innerHTML = (moment(data.fechaMatricula).format("DD/MM/YYYY"));
  document.querySelector('#lbl_matriFechaAprueba').innerHTML = (moment(data.fechaAprueba).format("DD/MM/YYYY"));
  document.querySelector('#lbl_matriFechaSolicitud').innerHTML = (moment(data.fechaSolicita).format("DD/MM/YYYY"));
  document.querySelector('#lbl_matriYYYY').innerHTML = (data.yyyy);
  document.querySelector('#lbl_matriNivel').innerHTML = (data.nivel);
  document.querySelector('#lbl_matriGrado').innerHTML = (data.grado);
  document.querySelector('#lbl_matriSeccion').innerHTML = (data.seccion);
  document.querySelector('#lbl_matriImporte').innerHTML = (appFormatMoney(data.importe,2));
  document.querySelector('#lbl_matriSaldo').innerHTML = (appFormatMoney(data.saldo,2));
}

function appDetalleSetData(data){
  let totalImporte = 0;
  let totalSaldo = 0;
  let fila = "";

  data.forEach((valor,key)=>{
    totalImporte += valor.importe;
    totalSaldo += valor.saldo;
    fila += '<tr style="'+((valor.saldo<=0)?('color:#bbb;'):((valor.atraso>0)?("color:#f00;"):("color:#000;")))+'">'+
            '<td>'+(valor.item)+'</td>'+
            '<td>'+(valor.producto)+'</td>'+
            '<td>'+(moment(valor.vencimiento).format("DD/MM/YYYY"))+'</td>'+
            '<td style="text-align:right;">'+appFormatMoney(valor.importe,2)+'</td>'+
            '<td style="text-align:right;">'+appFormatMoney(valor.saldo,2)+'</td>'+
            '<td style="text-align:center;">'+((valor.saldo<=0)?(0):((valor.atraso>0)?(valor.atraso):("")))+'</td>'+
            '<td></td></tr>';
  });
  //totales
  fila += '<tr>'+
          '<td colspan="3" style="text-align:center;"><b>Total por Vencer</b></td>'+
          '<td style="text-align:right;"><b>'+appFormatMoney(totalImporte,2)+'</b></td>'+
          '<td style="text-align:right;"><b>'+appFormatMoney(totalSaldo,2)+'</b></td>'+
          '<td></td>'+
          '</tr>';
  
  $('#grdDetalleDatos').html(fila);
}
