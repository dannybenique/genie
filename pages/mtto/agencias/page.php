<?php if(isset($menu->mtto->submenu->agencias)){?>
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-gear"></i> <b>Agencias</b></h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">agencias</li>
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
              <button type="button" id="btn_DEL" class="btn btn-default btn-sm" onclick="javascript:appAgenciasDelete();"><i class="fa fa-trash"></i></button>
              <button type="button" id="btn_NEW" class="btn btn-default btn-sm" onclick="javascript:appAgenciaNuevo();"><i class="fa fa-plus"></i></button>
            </div>
            <button type="button" id="btn_RST" class="btn btn-default btn-sm" onclick="javascript:appAgenciasReset();"><i class="fa fa-refresh"></i></button>
            <div class="btn-group">
              <input type="text" id="txtBuscar" class="form-control input-sm pull-right" placeholder="agencia..." onkeypress="javascript:appAgenciasBuscar(event);" style="text-transform:uppercase;" autocomplete="off">
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover" id="grd">
              <thead>
                <tr>
                  <th style="width:25px;"><input type="checkbox" id="chk_All" onclick="javascript:toggleAll(this,'chk_Borrar');" /></th>
                  <th style="width:60px;">Codigo</th>
                  <th style="width:150px;">Agencia</th>
                  <th style="">Direccion</th>
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
                <input type="hidden" id="hid_agenciaID" value="">
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
                <div class="form-group" style="margin-bottom:15px;">
                  <div class="input-group">
                    <span class="input-group-addon" style="background:#eeeeee;"><b>Telefonos</b></span>
                    <input id="txt_Telefonos" type="text" maxlength="30" class="form-control" placeholder="telefonos..."/>
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" title="Region" style="background:#eeeeee;"><b>Region</b></span>
                    <select id="cbo_Region" class="form-control selectpicker" onchange="javascript:comboProvincias();"></select>
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" title="Provincia" style="background:#eeeeee;"><b>Provincia</b></span>
                    <select id="cbo_Provincia" class="form-control selectpicker" onchange="javascript:comboDistritos();"></select>
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" title="Distrito" style="background:#eeeeee;"><b>Distrito</b></span>
                    <select id="cbo_Distrito" class="form-control selectpicker"></select>
                  </div>
                </div>
                <div id="pn_Ciudad" class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" style="background:#eeeeee;"><b>Ciudad</b></span>
                    <input id="txt_Ciudad" type="text" maxlength="50" class="form-control" placeholder="ciudad..."/>
                  </div>
                </div>
                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon" style="background:#eeeeee;"><b>Direccion</b></span>
                    <input id="txt_Direccion" type="text" maxlength="150" class="form-control" placeholder="direccion..."/>
                  </div>
                </div>
                <div class="form-group">
                  <div class="input-group">
                    <textarea id="txt_Observac" type="text" placeholder="Observaciones..." cols="100" rows="6" style="width:100%;"></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="box-body">
            <button type="button" class="btn btn-default" onclick="javascript:appAgenciaCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
            <button type="button" id="btnInsert" class="btn btn-primary pull-right" onclick="javascript:appAgenciaInsert();"><i class="fa fa-save"></i> Guardar</button>
            <button type="button" id="btnUpdate" class="btn btn-info pull-right" onclick="javascript:appAgenciaUpdate();"><i class="fa fa-save"></i> Actualizar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>

<script src="pages/mtto/agencias/script.js"></script>
<script>
  $(document).ready(function(){
    appAgenciasReset();
  });
</script>
<?php } ?>