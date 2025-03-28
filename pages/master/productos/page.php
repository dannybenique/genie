<?php if(isset($menu->master->submenu->productos)){?>
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-gear"></i> <b>Productos</b></h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">productos</li>
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
              <button type="button" id="btn_DEL" class="btn btn-default btn-sm" onclick="javascript:appProductosBorrar();"><i class="fa fa-trash"></i></button>
              <button type="button" id="btn_NEW" class="btn btn-default btn-sm" onclick="javascript:appProductoNuevo();"><i class="fa fa-plus"></i></button>
            </div>
            <button type="button" id="btn_RST" class="btn btn-default btn-sm" onclick="javascript:appProductosReset();"><i class="fa fa-refresh"></i></button>
            <div class="btn-group">
              <input type="text" id="txtBuscar" class="form-control input-sm pull-right" placeholder="producto..." onkeypress="javascript:appProductosBuscar(event);" style="text-transform:uppercase;" autocomplete="off">
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover" id="grd">
              <thead>
                <tr>
                  <th style="width:25px;"><input type="checkbox" id="chk_All" onclick="javascript:toggleAll(this,'chk_Borrar');" /></th>
                  <th style="width:70px;text-align:center;">Codigo</th>
                  <th style="width:30px;text-align:center;"><i class="fa fa-info-circle" title="Obligatorio"></i></th>
                  <th style="width:250px;">Producto</th>
                  <th style="width:70px;" title="Abreviatura">Abrev</th>
                  <th style="width:30px;text-align:center;" title="Orden de Prioridad">Ord</th>
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
                <input type="hidden" id="hid_productoID" value="">
                <div class="row">
                  <div class="col-xs-6">
                    <div id="div_Codigo" class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="background:#eeeeee;"><b>Codigo</b></span>
                        <input id="txt_Codigo" type="text" maxlength="7" class="form-control" placeholder="0000"/>
                      </div>
                    </div>
                  </div>
                  <div class="col-xs-6">
                    <div id="div_Abrev" class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="background:#eeeeee;"><b>Abrev.</b></span>
                        <input id="txt_Abrev" type="text" maxlength="5" class="form-control" placeholder="abrev..."/>
                      </div>
                    </div>
                  </div>
                </div>
                <div id="div_Nombre" class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" style="background:#eeeeee;"><b>Nombre</b></span>
                    <input id="txt_Nombre" type="text" maxlength="50" class="form-control" placeholder="nombre..."/>
                  </div>
                </div>
                <div id="div_Orden" class="form-group" style="margin-bottom:5px;width:130px;">
                  <div class="input-group">
                    <span class="input-group-addon" style="background:#eeeeee;"><b>Orden</b></span>
                    <select id="cbo_Orden" class="form-control selectpicker" style="height:30px;text-align:left;"></select>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="box-body">
            <button type="button" class="btn btn-default" onclick="javascript:appProductoCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
            <button type="button" id="btnInsert" class="btn btn-primary pull-right" onclick="javascript:appProductoInsert();"><i class="fa fa-save"></i> Guardar</button>
            <button type="button" id="btnUpdate" class="btn btn-info pull-right" onclick="javascript:appProductoUpdate();"><i class="fa fa-save"></i> Actualizar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>

<script src="pages/master/productos/script.js"></script>
<script>
  $(document).ready(function(){
    appProductosReset();
  });
</script>
<?php } ?>