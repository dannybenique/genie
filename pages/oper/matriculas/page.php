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
            <button type="button" class="btn btn-default btn-sm" onclick="javascript:appMatriculasReset();"><i class="fa fa-refresh"></i></button>
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
                  <th style="width:90px;">Fecha</th>
                  <th style="width:110px;" title="Documento Unico de Identidad = DNI">DNI</th>
                  <th style="">Alumno <i class="fa fa-sort"></i></th>
                  <th style="" title="Matricula">Matricula</th>
                  <th style="width:90px;text-align:right;">Saldo</th>
                  <th style="width:50px;text-align:center;" title="Nro de Cuotas">Cuo</th>
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
      <div class="col-md-3">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title" style="font-family:flexoregular;"><b>Cabecera</b></h3>
          </div>
          <div class="box-body">
            <div class="box-body">
              <input type="hidden" id="hid_crediID" value="">
              Socio: <a id="lbl_crediSocio"></a><br/>
              <span id="lbl_crediTipoDUI"></span>: <a id="lbl_crediNroDUI"></a><br/><br/>
              ID: <a id="lbl_crediID"></a><br/>
              Desembolso: <a id="lbl_crediFecha"></a><br/>
              Producto: <a id="lbl_crediProducto"></a><br/>
              Codigo: <a id="lbl_crediCodigo"></a><br/>
              Tasa Credito: <a id="lbl_crediTasaCred"></a><br/>
              Tasa Mora: <a id="lbl_crediTasaMora"></a><br/>
              Moneda: <a id="lbl_crediMoneda"></a><br/><br/>
              Agencia: <a id="lbl_crediAgencia"></a><br/>
              Promotor: <a id="lbl_crediPromotor"></a><br/>
              Analista: <a id="lbl_crediAnalista"></a><br/><br/>
              Importe: <b><a id="lbl_crediImporte"></a></b><br/>
              Saldo: <b><a id="lbl_crediSaldo"></a></b><br/><br/>
            </div>
            <div class="btn-group pull-left">
              <button id="btnCancel" type="button" class="btn btn-default" onclick="javascript:appMatriculasBotonCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
            </div>
            <div class="btn-group pull-right">
              <button type="button" class="btn btn-info btn-xm" onclick="javascript:appMatriculasRefresh();"><i class="fa fa-refresh"></i></button>
            </div>
          </div>
        </div>
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title" style="font-family:flexoregular;"><b>Opciones</b></h3>
          </div>
          <div class="box-body">
            <div class="btn-group pull-right">
              <a id="lnk_viewTotalPagado" href="javascript:appMatriculasViewTotalPagado();" style="color:#bbb;">Ver Total Pagado&nbsp;&nbsp;<span id="iconTotalPagado"><i class="fa fa-toggle-off"></i></span></a>
            </div><br>
            <div class="btn-group pull-right">
              <a id="lnk_viewTotalPorVencer" href="javascript:appMatriculasViewTotalPorVencer();" style="color:black;">Ver Total por Vencer&nbsp;&nbsp;<span id="iconTotalPorVencer"><i class="fa fa-toggle-off"></i></span></a>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-9">
        <div class="box box-primary">
          <div class="box-body box-profile">
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover" id="grdDetalle" style="font-family:helveticaneue_light;">
                  <thead>
                    <tr>
                      <th style="width:30px;">Nro</th>
                      <th style="width:30px;" title="dias entre cuotas">DS</th>
                      <th style="width:80px;text-align:center;">Fecha</th>
                      <th style="width:95px;text-align:right;">Total</th>
                      <th style="width:95px;text-align:right;">Capital</th>
                      <th style="width:95px;text-align:right;">Interes</th>
                      <th style="width:95px;text-align:right;">Mora</th>
                      <th style="width:80px;text-align:right;">Gastos</th>
                      <th style="width:100px;text-align:right;">Saldo</th>
                      <th style="width:60px;text-align:center;" title="Retraso en dias">Atr.</th>
                      <th style="">Doc Pago</th>
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