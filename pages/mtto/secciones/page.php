<?php if(isset($menu->mtto->submenu->secciones)){?>
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-gear"></i> <b>Secciones</b></h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">secciones</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row" id="grid">
    <div class="col-md-8">
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <div class="btn-group">
              <div class="input-group" style="width:110px;">
                <span class="input-group-addon" style="background:#ddd;"><b>Niveles</b></span>  
                <span class="input-group-btn">
                  <select id="cboNiveles" class="btn btn-default btn-sm" style="height:30px;text-align:left;" onchange="javascript:appNivelesGrid();"></select>
                </span>
              </div>
            </div>
            <div class="btn-group">
              <button type="button" id="btn_DEL" class="btn btn-default btn-sm" onclick="javascript:appColNivBorrar();"><i class="fa fa-trash"></i></button>
              <button type="button" id="btn_NEW" class="btn btn-default btn-sm" onclick="javascript:appNivelNuevo();"><i class="fa fa-plus"></i></button>
            </div>
            <button type="button" id="btn_RST" class="btn btn-default btn-sm" onclick="javascript:appColNivRefresh();"><i class="fa fa-refresh"></i></button>
            <span id="grdColNivCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover" id="grd">
              <thead>
                <tr>
                  <th style="width:25px;"><input type="checkbox" id="chk_All" onclick="javascript:toggleAll(this,'chk_Borrar');" /></th>
                  <th style="width:180px;">Nivel &raquo; Grado &raquo; Seccion</th>
                  <th style="width:50px;text-align:right;" title="Capacidad">Cap</th>
                  <th title="Nombre con el que se reconocera">Alias</th>
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

<script src="pages/mtto/secciones/script.js"></script>
<script>
  $(document).ready(function(){
    appNivelesReset();
  });
</script>
<?php } ?>