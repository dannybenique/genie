<?php if(isset($menu->oper->submenu->matriculas)){?>
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-gg-circle"></i> <b>Matriculas</b></h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Matriculas</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row" id="grid">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <button type="button" class="btn btn-default btn-sm" onclick="javascript:appMatriculasRefreshTabla();"><i class="fa fa-refresh"></i></button>
            <div class="btn-group">
              <input type="text" id="txtBuscar" name="txtBuscar" class="form-control input-sm pull-right" placeholder="DNI, persona..." onkeypress="javascript:appMatriculasBuscar(event);" style="text-transform:uppercase;" autocomplete="off">
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover" id="grd">
              <thead>
                <tr>
                  <th style="width:90px;" title="Fecha de Matricula">Fecha <i class="fa fa-info-circle" style="color:#666;font-size:11px;"></i></th>
                  <th style="width:110px;" title="Documento Unico de Identidad = DNI">DNI <i class="fa fa-info-circle" style="color:#666;font-size:11px;"></i></th>
                  <th>Alumno <i class="fa fa-sort"></i></th>
                  <th style="width:70px;text-align:center;">Matr.</th>
                  <th title="Codigo Matricula &raquo; Nivel &raquo; Grado &raquo; Seccion">Matricula <i class="fa fa-info-circle" style="color:#666;font-size:11px;"></i></th>
                  <th style="width:90px;text-align:right;">Importe</th>
                  <th style="width:90px;text-align:right;">Saldo</th>
                  <th style="width:60px;text-align:center;" title="Nro Total de Cuotas">Cuo <i class="fa fa-info-circle" style="color:#666;font-size:11px;"></i></th>
                </tr>
              </thead>
              <tbody id="grdDatos"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row" id="edit" style="display:none;">
    <form class="form-horizontal" id="frmPersona" autocomplete="off">
      <div class="col-md-4">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title" style="font-family:flexoregular;"><b>Matricula</b></h3>
          </div>
          <div class="box-body">
            <div class="box-body">
              <input type="hidden" id="hid_matriID" value="">
              Alumno: <a id="lbl_matriAlumno"></a><br/>
              DNI: <a id="lbl_matriNroDUI"></a><br/><br/>
              Codigo: <a id="lbl_matriCodigo"></a><br/>
              Ejecucion: <a id="lbl_matriFechaMatricula"></a><br/>
              Aprobacion: <a id="lbl_matriFechaAprueba"></a><br/>
              Solicitud: <a id="lbl_matriFechaSolicitud"></a><br/>
              Año Matr.: <a id="lbl_matriYYYY"></a><br/><br/>
              Nivel: <a id="lbl_matriNivel"></a><br/>
              Grado: <a id="lbl_matriGrado"></a><br/>
              Seccion: <a id="lbl_matriSeccion"></a><br/><br/>
              Importe: <b><a id="lbl_matriImporte"></a></b><br/>
              Saldo: <b><a id="lbl_matriSaldo"></a></b><br/><br/>
            </div>
            <div class="btn-group pull-left">
              <button id="btnCancel" type="button" class="btn btn-default" onclick="javascript:appMatriculasBotonCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
            </div>
            <div class="btn-group pull-right">
              <button type="button" class="btn btn-info btn-xm" onclick="javascript:appMatriculasRefreshCuotas();"><i class="fa fa-refresh"></i></button>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-8">
        <div class="box box-primary">
          <div class="box-body box-profile">
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover" id="grdDetalle" style="font-family:helveticaneue_light;">
                  <thead>
                    <tr>
                      <th style="width:30px;">Item</th>
                      <th>Concepto</th>
                      <th style="width:80px;text-align:center;">Vencimiento</th>
                      <th style="width:95px;text-align:right;">Total</th>
                      <th style="width:100px;text-align:right;">Saldo</th>
                      <th style="width:60px;text-align:center;" title="Retraso en dias">Atr. <i class="fa fa-info-circle" style="color:#666;font-size:11px;"></i></th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody id="grdDetalleDatos">
                  </tbody>
                </table>
              </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</section>

<script src="pages/oper/matriculas/script.js"></script>
<script>
  $(document).ready(function(){
    appMatriculasReset();
  });
</script>
<?php } ?>