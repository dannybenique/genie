<?php if(isset($menu->repo->submenu->movim)){?>
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-ticket"></i> <b>Movimiento Diario</b></h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">movimientos</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row" id="grid">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <div class="btn-group">
              <div class="input-group" style="width:110px;">
                <span class="input-group-addon" style="background:#F4F4F4;"><b>Usuario</b></span>  
                <span class="input-group-btn">
                  <select id="cboUsuarios" class="btn btn-default btn-sm" style="height:30px;text-align:left;"></select>
                </span>
              </div>
            </div>
            <div class="btn-group">
              <div class="input-group" style="width:110px;">
                <span class="input-group-addon" style="background:#F4F4F4;"><b>Agencia</b></span>  
                <span class="input-group-btn">
                  <select id="cboAgencias" class="btn btn-default btn-sm" style="height:30px;text-align:left;"></select>
                </span>
              </div>
            </div>
            <div class="btn-group">
              <div class="input-group" style="width:120px;">
                <span class="input-group-addon" style="background:#F4F4F4;"><b>Moneda</b></span>  
                <span class="input-group-btn">
                  <select id="cboMonedas" class="btn btn-default btn-sm" style="height:30px;text-align:left;"></select>
                </span>
              </div>
            </div>
            <div class="btn-group">
            <div class="input-group" style="width:130px;">
                <span class="input-group-addon" style="background:#F4F4F4;"><b>Fecha</b></span>  
                <span class="input-group-btn">
                  <input id="txtFecha" type="text" class="form-control" style="width:105px;height:30px;">
                </span>
              </div>
            </div>
            <button type="button" id="btn_RST" class="btn btn-default btn-sm" onclick="javascript:appMovimGrid();"><i class="fa fa-flash"></i> Ejecutar</button>
            <span id="grdCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <br>
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover" id="grd">
              <thead>
                <tr>
                  <th style="width:70px;">Hora</th>
                  <th style="width:120px;">Voucher</th>
                  <th style="">Socio</th>
                  <th style="">Producto</th>
                  <th style="">Movimiento</th>
                  <th style="width:110px;text-align:right;">Ingresos</th>
                  <th style="width:110px;text-align:right;">Salidas</th>
                </tr>
              </thead>
              <tbody id="grdDatos">
              </tbody>
              <tfoot id="grdFoot">
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row" id="edit" style="display:none;">
    <div class="col-md-4">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title" style="font-family:flexoregular;">Cabecera</h3>
        </div>
        <div class="box-body">
          <div class="box-body">
            <input type="hidden" id="hid_movimID" value="">
            
            <span>Agencia:</span> <a id="lbl_pagoAgencia"></a><br/>
            <span title="Tipo de operacion">Tipo:</span> <a id="lbl_pagoTipoOper"></a><br/>
            <span>Codigo:</span> <a id="lbl_pagoCodigo"></a><br/>
            <span>Fecha:</span> <a id="lbl_pagoFecha"></a><br/>
            <span>Socio:</span> <a id="lbl_pagoSocio"></a><br/>
            <span id="lbl_tipodui"></span> <a id="lbl_pagoNroDUI"></a><br/><br/>
            <span>Cajera:</span> <a id="lbl_pagoCajera"></a><br/>
            <span>Importe:</span> <a id="lbl_pagoImporte"></a><br/>
          </div>
          <div class="btn-group pull-left">
            <button id="btnCancel" type="button" class="btn btn-default" onclick="javascript:appMovimCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
          </div>
          <div class="btn-group pull-right">
            <button type="button" class="btn btn-info" id="btn_print" onclick="javascript:appMovimRefresh();"><i class="fa fa-refresh"></i></button>
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
                    <th style="">Tipo de Movimiento</th>
                    <th style="">Producto</th>
                    <th style="width:100px;text-align:right;">Importe</th>
                  </tr>
                </thead>
                <tbody id="grdDetalleDatos">
                </tbody>
              </table>
            </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="pages/repo/movim/script.js"></script>
<script>
  $(document).ready(function(){
    appMovimReset();
  });
</script>
<?php } ?>