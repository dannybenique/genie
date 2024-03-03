<?php if(isset($menu->mtto->submenu->pagos)){?>
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-gear"></i> <b>Pagos</b></h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">pagos</li>
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
              <button type="button" id="btn_EDTMONTO" class="btn btn-default btn-sm" onclick="javascript:appPagosCambiarImporteBatch();" title="cambiar en bloque el importe de pago de los registros"><i class="fa fa-beer"></i> Cambiar Importe</button>
              <button type="button" id="btn_EDTVCMTO" class="btn btn-default btn-sm" onclick="javascript:appPagosCambiarVcmtoBatch();" title="cambiar en bloque el vencimiento de los  registros"><i class="fa fa-calendar"></i> Cambiar Año Vcmto</button>
            </div>
            <div class="btn-group">
              <button type="button" id="btn_DEL" class="btn btn-default btn-sm" onclick="javascript:appPagosBorrar();" title="Borrar Pagos"><i class="fa fa-trash"></i></button>
              <button type="button" id="btn_NEW" class="btn btn-default btn-sm" onclick="javascript:appPagoNuevo();" title="Agregar Pagos"><i class="fa fa-plus"></i></button>
            </div>
            <button type="button" id="btn_RST" class="btn btn-default btn-sm" onclick="javascript:appPagosReset();" title="Actualizar la Grilla de Pagos"><i class="fa fa-refresh"></i></button>
            <div class="btn-group">
              <input type="text" id="txtBuscar" class="form-control input-sm pull-right" placeholder="pago..." onkeypress="javascript:appPagosBuscar(event);" style="text-transform:uppercase;" autocomplete="off">
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover" id="grd">
              <thead>
                <tr>
                  <th style="width:30px;text-align:center;"><i class="fa fa-lock" title="Bloqueo"></i></th>
                  <th style="width:25px;"><input type="checkbox" id="chk_All" onclick="javascript:toggleAll(this,'chk_Borrar');" /></th>
                  <th style="width:70px;text-align:center;">Codigo</th>
                  <th style="width:30px;text-align:center;"><i class="fa fa-exclamation" title="Obligatorio"></i></th>
                  <th style="width:250px;">Pago</th>
                  <th style="width:70px;text-align:center;" title="Abreviatura">Abrev</th>
                  <th style="width:100px;text-align:right;" title="Importe">Importe</th>
                  <th style="width:105px;text-align:center;" title="Fecha de Vencimiento">Vencimiento</th>
                  <th></th>
                </tr>
              </thead>
              <tbody id="grdDatos">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row" id="edit" style="display:none;">
    <div class="col-md-6">
      <form class="form-horizontal" autocomplete="off">
        <div class="box box-primary">
          <div class="box-body">
            <div class="col-md-12">
              <div class="box-body">
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" title="Tipo de Producto" style="background:#eeeeee;"><b>Tipo Producto</b></span>
                    <select id="cbo_Producto" class="form-control selectpicker"></select>
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" title="Obligatorio" style="background:#eeeeee;"><b>Obligatorio</b></span>
                    <select id="cbo_Obliga" class="form-control selectpicker" style="width:105px;">
                      <option value=1>SI</option>
                      <option value=0>NO</option>
                    </select>
                  </div>
                </div>
                <div id="div_Importe" class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" style="background:#eeeeee;"><b>Importe</b></span>
                    <input id="txt_Importe" type="number" class="form-control" placeholder="0.00" style="width:120px;text-align:right;"/>
                  </div>
                </div>
                <div id="div_Fecha" class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" style="background:#eeeeee;"><b>Fecha</b></span>
                    <input id="txt_Fecha" type="text" class="form-control" style="width:105px;"/>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="box-body">
            <button type="button" class="btn btn-default" onclick="javascript:appPagoCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
            <button type="button" id="btnInsert" class="btn btn-primary pull-right" onclick="javascript:appPagoInsert();"><i class="fa fa-save"></i> Guardar</button>
            <button type="button" id="btnUpdate" class="btn btn-info pull-right" onclick="javascript:appPagoUpdate();"><i class="fa fa-save"></i> Actualizar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>

<script src="pages/mtto/pagos/script.js"></script>
<script>
  $(document).ready(function(){
    appPagosReset();
  });
</script>
<?php } ?>