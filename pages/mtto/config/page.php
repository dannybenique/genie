<?php if(isset($menu->mtto->submenu->config)){?>
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-gear"></i> <b>Configuracion</b></h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">configuracion</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row" id="grid">
    <div class="col-md-6">
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <button type="button" id="btn_RST" class="btn btn-default btn-sm" onclick="javascript:appConfigReset();"><i class="fa fa-refresh"></i></button>
            <button type="button" id="btn_SAVE" class="btn btn-info btn-sm" onclick="javascript:appCoonfigUpdate();" disabled>Guardar Configuracion <i class="fa fa-save"></i></button>
            <span id="grdCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover" id="grd">
              <thead>
                <tr>
                  <th style="">Parametro</th>
                  <th style="width:120px;" title="Valor">Valor</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td style="line-height:1.8"><span>Año para la matricula</span></td>
                  <td><select id="cbo_currYEAR" class="btn btn-default btn-sm selectpicker" onchange="javascript:appHabilitarGuardar();"></select></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalConfig" role="dialog">
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

<script src="pages/mtto/config/script.js"></script>
<script>
  $(document).ready(function(){
    appConfigReset();
  });
</script>
<?php } ?>