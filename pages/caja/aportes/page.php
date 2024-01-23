<?php if(isset($menu->caja->submenu->aportes)){?>
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-trophy"></i> <b>Operaciones Aportes</b></h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">aportes</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row" id="edit">
    <div class="col-md-4">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title" style="font-family:flexoregular;"><b>Opciones</b></h3>
        </div>
        <div class="box-body">
          <button type="button" id="btn_NEW" class="btn btn-info btn-sm" onclick="javascript:appAportesBotonIngreso();"><i class="fa fa-plus"></i> Ingreso</button>
          <button type="button" id="btn_RET" class="btn btn-danger btn-sm" onclick="javascript:appAportesBotonRetiro();"><i class="fa fa-minus"></i> Retiro</button>
          <button type="button" class="btn btn-primary btn-sm pull-right" onclick="javascript:appPagosReset();"><i class="fa fa-refresh"></i></button>
        </div>
        <br/><br/>
        <div class="box-body">
          <div class="box-body">
            Socio: <a id="lbl_aporteSocio"></a><br/>
            <span id="lbl_aporteTipoDUI">DUI</span>: <a id="lbl_aporteNroDUI"></a><br/><br/>
            <b>SALDO</b>: <a id="lbl_aporteSaldo"></a><br/>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title" style="font-family:flexoregular;"><b>Operacion</b></h3>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="box-body">
              <div class="box-body">
                <div class="form-group" style="margin-bottom:10px;">
                  <div class="input-group">
                    <span class="input-group-addon" style="background:#eeeeee;"><b>FECHA PAGO</b></span>
                    <input id="txt_aporteFecha" type="text" class="form-control" disabled="disabled" style="width:120px;"/>
                  </div>
                </div>
              </div>
              <div class="box-body">
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" style="background:#eeeeee;"><b>MONEDA</b></span>
                    <select id="cbo_aporteMonedas" class="form-control selectpicker" disabled="disabled"></select>
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" style="background:#eeeeee;"><b>MEDIO DE PAGO</b></span>
                    <select id="cbo_aporteMedioPago" class="form-control selectpicker"></select>
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" style="background:#eeeeee;"><b>IMPORTE</b></span>
                    <input id="txt_aporteImporte" type="text" class="form-control" autocomplete="off"/>
                  </div>
                </div>
              </div>
              <div class="box-body">
                <button type="button" id="btn_EXEC" class="btn btn-success btn-sm pull-right" onclick="javascript:appAportesBotonExec();"><i class="fa fa-flash"></i> Operacion</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalAporte" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 id="modalAporte_Titulo" class="modal-title" style="font-family:flexoregular;font-weight:bold;">Datos Credito</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon no-border">Nro Documento</span>
                  <input type="number" id="modalAporte_TxtBuscar" class="form-control" placeholder="DNI, RUC..." onkeypress="javascript:modalAporte_keyBuscar(event);">
                  <div class="input-group-btn" style="height:30px;">
                    <button type="button" class="btn btn-primary" onclick="javacript:modalAporteBuscar();"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
              <div class="box-body table-responsive no-padding">
                <span id="modalAporte_Wait"></span>
                <div id="modalAporte_Grid">
                  <table class="table table-hover">
                    <tr>
                      <th style="width:110px;">DUI</th>
                      <th style="">Socio</th>
                      <th style="">Producto</th>
                      <th style="width:100px;text-align:right;">Saldo</th>
                    </tr>
                    <tbody id="modalAporte_GridBody"></tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalPrint" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" style="font-family:flexoregular;"><b>Imprimir Voucher</b></h4>
          </div>
          <div class="modal-body" style="border-right:1px solid white;">
            <div class="table-responsive no-padding" id="contenedorFrame">
            </div>
          </div>
          <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
          </div>
      </div>
    </div>
  </div>
</section>

<script src="pages/caja/aportes/script.js"></script>
<script>
  $(document).ready(function(){
    appPagosReset();
  });
</script>
<?php } ?>