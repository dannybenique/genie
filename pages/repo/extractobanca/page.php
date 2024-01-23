<?php if(isset($menu->repo->submenu->extractobanca)){?>
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-gears"></i> <b>Extracto Bancario</b></h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">extrac.banca</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row" id="edit">
    <div class="col-md-4">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title" style="font-family:flexoregular;"><b>Buscar</b></h3>
          <button type="button" class="btn btn-primary btn-xs pull-right" onclick="javascript:appBotonBuscar();"><i class="fa fa-search"></i></button>
        </div>
        <div id="div_InfoCorta" class="box-body">
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="box box-primary">
        <div class="box-header with-border">
          <table style="width:100%;">
            <tr><td id="div_title_movim"></td></tr>
          </table>
        </div>
        <div class="box-body box-profile">
          <div id="div_TablaMovim" class="box-body table-responsive no-padding">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalSocio" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 id="modalSocio_Titulo" class="modal-title" style="font-family:flexoregular;font-weight:bold;">Datos Credito</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon no-border">Nro Documento</span>
                  <input type="number" id="modalSocio_TxtBuscar" class="form-control" placeholder="DNI, RUC..." onkeypress="javascript:modalSocio_keyBuscar(event);">
                  <div class="input-group-btn" style="height:30px;">
                    <button type="button" class="btn btn-primary" onclick="javacript:modalSocioBuscar();"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
              <div class="box-body table-responsive no-padding">
                <span id="modalSocio_Wait"></span>
                <div id="modalSocio_Grid">
                  <table class="table table-hover">
                    <tr>
                      <th style="width:150px;">DUI</th>
                      <th style="">Socio</th>
                      <th style="width:100px;text-align:right;">Prods</th>
                    </tr>
                    <tbody id="modalSocio_GridBody"></tbody>
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

<script src="pages/repo/extractobanca/script.js"></script>
<script>
  $(document).ready(function(){
    appReset();
  });
</script>
<?php } ?>