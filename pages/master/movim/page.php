<?php if(isset($menu->master->submenu->tipos)){?>
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-lock"></i> <b>Movimientos</b></h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Movimientos</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row" id="grid">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <button type="button" id="btn_RST" class="btn btn-default btn-sm" onclick="javascript:appMovimReset();"><i class="fa fa-refresh"></i></button>
            <div class="btn-group">
              <input type="text" id="txtBuscar" class="form-control input-sm pull-right" placeholder="nombre..." onkeypress="javascript:appMovimBuscar(event);" style="text-transform:uppercase;" autocomplete="off">
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover" id="grd">
              <thead>
                <tr>
                  <th style="width:50px;">ID</th>
                  <th style="">Nombre</th>
                  <th style="width:100px;">Codigo</th>
                  <th style="width:60px;" title="Abreviatura">Ab</th>
                  <th style="width:60px;">Tipo</th>
                  <th style="width:80px;">I/O</th>
                  <th style="width:80px;" title="indica si afecta al tipo">afecta</th>
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
                <input type="hidden" id="hid_tipoID" value="">
                <div class="row">
                  <div class="col-xs-6">
                    <div id="pn_Codigo" class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="background:#eeeeee;"><b>Codigo</b></span>
                        <input id="txt_Codigo" type="text" maxlength="4" class="form-control" placeholder="codigo..."/>
                      </div>
                    </div>
                  </div>
                  <div class="col-xs-6">
                    <div id="pn_Abrev" class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="background:#eeeeee;"><b>Abrev.</b></span>
                        <input id="txt_Abrev" type="text" maxlength="5" class="form-control" placeholder="abrev..."/>
                      </div>
                    </div>
                  </div>
                </div>
                <div id="pn_Nombre" class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" style="background:#eeeeee;"><b>Nombre</b></span>
                    <input id="txt_Nombre" type="text" maxlength="50" class="form-control" placeholder="nombre..."/>
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" style="background:#eeeeee;"><b>Tipo</b></span>
                    <input id="txt_Tipo" type="number" class="form-control" placeholder="..."/>
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" title="Region" style="background:#eeeeee;"><b>Padre</b></span>
                    <select id="cbo_Padre" class="form-control selectpicker"></select>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="box-body">
            <button type="button" class="btn btn-default" onclick="javascript:appTiposCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
            <button type="button" id="btnInsert" class="btn btn-primary pull-right" onclick="javascript:appAgenciaInsert();"><i class="fa fa-save"></i> Guardar</button>
            <button type="button" id="btnUpdate" class="btn btn-info pull-right" onclick="javascript:appAgenciaUpdate();"><i class="fa fa-save"></i> Actualizar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>

<script src="pages/master/movim/script.js"></script>
<script>
  $(document).ready(function(){
    appMovimReset();
  });
</script>
<?php } ?>