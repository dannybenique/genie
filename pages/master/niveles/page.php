<?php if(isset($menu->master->submenu->niveles)){?>
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-gear"></i> <b>Niveles</b></h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">niveles</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row" id="grid">
    <div class="col-md-6">
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <div class="btn-group">
              <button type="button" id="btn_NEW" class="btn btn-default btn-sm" onclick="javascript:appNivelNuevo();"><i class="fa fa-plus"></i></button>
              <div class="input-group" style="width:110px;">
                <span class="input-group-addon" style="background:#ddd;"><b>Niveles</b></span>  
                <span class="input-group-btn">
                  <select id="cboNiveles" class="btn btn-default btn-sm" style="height:30px;text-align:left;" onchange="javascript:appChangeNivel();"></select>
                </span>
              </div>
            </div>
            <button type="button" id="btn_SND" class="btn btn-default btn-sm" onclick="javascript:appNivelSend();"><i class="fa fa-send"></i></button>
            <button type="button" id="btn_RST" class="btn btn-default btn-sm" onclick="javascript:appNivelesReset();"><i class="fa fa-refresh"></i></button>
            <span id="grdCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover" id="grd">
              <thead>
                <tr>
                  <th style="">Nivel &raquo; Grado</th>
                  <th style="width:25px;"><input type="checkbox" id="chk_All" onclick="javascript:toggleAll(this,'chk_Borrar');" /></th>
                  <th style="text-align:center;width:80px;">Seccion</th>
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
    <div class="col-md-6">
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <div class="btn-group">
              <button type="button" id="btn_DEL" class="btn btn-default btn-sm" onclick="javascript:appColNivBorrar();"><i class="fa fa-trash"></i></button>
            </div>
            <button type="button" id="btn_RST" class="btn btn-default btn-sm" onclick="javascript:appColNivReset();"><i class="fa fa-refresh"></i></button>
            <span id="grdColNivCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover" id="grd">
              <thead>
                <tr>
                  <th style="">Nivel &raquo; Grado</th>
                  <th style="width:25px;"><input type="checkbox" id="chk_All" onclick="javascript:toggleAll(this,'chk_Borrar');" /></th>
                  <th style="text-align:center;width:80px;">Seccion</th>
                  <th title="Nombre con el que se reconocera">Alias</th>
                  <th style="width:50px;" title="Capacidad">Cap</th>
                  <th></th>
                </tr>
              </thead>
              <tbody id="grdColNiv">
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
            
          </div>
          <div class="box-body">
            <button type="button" class="btn btn-default" onclick="javascript:appNivelCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
            <button type="button" id="btnInsert" class="btn btn-primary pull-right" onclick="javascript:appNivelInsert();"><i class="fa fa-save"></i> Guardar</button>
            <button type="button" id="btnUpdate" class="btn btn-info pull-right" onclick="javascript:appNivelUpdate();"><i class="fa fa-save"></i> Actualizar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="modal fade" id="modalColNiv" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <form class="form-horizontal" id="frmColNiv" autocomplete="off">
          <div class="modal-header" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" style="font-family:flexoregular;font-weight:bold;">Edicion de Niveles por Colegio</h4>
          </div>
          <div class="modal-body" style="border-right:1px solid white;">
            <div class="box-body row">
              <div class="col-md-12">
                <div class="box-body">
                  <input type="hidden" id="hid_colnivnivelID" value="">
                  <div id="div_Nombre" class="form-group" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span class="input-group-addon" style="background:#eeeeee;"><b>Nivel</b></span>
                      <input id="txt_modColNivNombre" type="text" class="form-control" disabled/>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-xs-6">
                      <div id="div_Codigo" class="form-group" style="margin-bottom:5px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="background:#eeeeee;"><b>Alias</b></span>
                          <input id="txt_modColNivAlias" type="text" class="form-control"/>
                        </div>
                      </div>
                    </div>
                    <div class="col-xs-6">
                      <div id="div_Abrev" class="form-group" style="margin-bottom:5px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="background:#eeeeee;"><b>Capacidad</b></span>
                          <input id="txt_modColNivCapacidad" type="number" class="form-control" placeholder="0..."/>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
            <button type="button" class="btn btn-primary" onclick="javascript:appColNivUpdate();"><i class="fa fa-flash"></i> Actualizar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<script src="pages/master/niveles/script.js"></script>
<script>
  $(document).ready(function(){
    appNivelesReset();
  });
</script>
<?php } ?>