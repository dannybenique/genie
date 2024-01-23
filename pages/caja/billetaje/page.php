<?php if(isset($menu->caja->submenu->desemb)){?>
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-btc"></i> <b>Billetaje</b></h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">billetaje</li>
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
              <button type="button" id="btn_DEL" class="btn btn-default btn-sm" onclick="javascript:appBillBotonBorrar();"><i class="fa fa-trash-o"></i></button>
              <button type="button" id="btn_NEW" class="btn btn-default btn-sm" onclick="javascript:appBillBotonNuevo();"><i class="fa fa-plus"></i></button>
            </div>
            <button type="button" class="btn btn-default btn-sm" onclick="javascript:appBillReset();"><i class="fa fa-refresh"></i></button>
            <div class="btn-group">
              <div class="input-group" style="width:110px;">
                <span class="input-group-addon" style="background:#F4F4F4;"><b>Usuario</b></span>  
                <span class="input-group-btn">
                  <select id="cboUsuarios" class="btn btn-default btn-sm" style="height:30px;text-align:left;" onchange="javascript:appBillGrid();" disabled></select>
                </span>
              </div>
            </div>
            <div class="btn-group">
              <div class="input-group" style="width:120px;">
                <span class="input-group-addon" style="background:#F4F4F4;"><b>Moneda</b></span>  
                <span class="input-group-btn">
                  <select id="cboMonedas" class="btn btn-default btn-sm" style="height:30px;text-align:left;" onchange="javascript:appBillGrid();"></select>
                </span>
              </div>
            </div>
            <span id="grdCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover" id="grd">
              <thead>
                <tr>
                  <th style="width:25px;"><input type="checkbox" id="chk_All" onclick="toggleAll(this,'chk_Borrar');" /></th>
                  <th style="width:85px;text-align:center;" title="Fecha de Solicitud">Fecha</th>
                  <th style="">Agencia</th>
                  <th style="">Empleado</th>
                  <th style="width:90px;">Moneda</th>
                  <th style="width:90px;text-align:right;">Importe</th>
                </tr>
              </thead>
              <tbody id="grdDatos"></tbody>
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
                <input type="hidden" id="hid_billID" value="">
                <input type="hidden" id="hid_usuarioID" value="">
                <div id="pn_Usuario" class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" style="background:#eeeeee;"><b>Usuario</b></span>
                    <input id="txt_UsuarioEdit" type="text" maxlength="50" class="form-control" disabled/>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-6">
                    <div id="pn_Codigo" class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="background:#eeeeee;"><b>Moneda</b></span>
                        <select id="cbo_MonedasEdit" class="form-control selectpicker" disabled></select>
                      </div>
                    </div>
                  </div>
                  <div class="col-xs-6">
                    <div id="pn_Fecha" class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="background:#eeeeee;"><b>Fecha</b></span>
                        <input id="txt_Fecha" type="text" maxlength="5" class="form-control" placeholder="DD/MM/AAAA" disabled/>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:15px;">
                  <div class="input-group">
                    <span class="input-group-addon" title="Region" style="background:#eeeeee;"><b>Agencia</b></span>
                    <select id="cbo_AgenciasEdit" class="form-control selectpicker"></select>
                  </div>
                </div>
                <strong><i class="fa fa-money"></i> Billetes y Monedas</strong>
                <div class="row" style="margin-top:-5px;">
                  <div class="col-md-6">
                    <div class="box-body">
                      <div id="pn_Mx200" class="form-group" style="margin-bottom:3px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="background:#eeeeee;width:40%;"><b><span class="billetaje_mon" style="font-size:10px;"></span> 200</b></span>
                          <input id="txt_Mx200" type="numeric" class="form-control text-center" placeholder="0" oninput="javascript:appBillCalcular();"/>
                        </div>
                      </div>
                      <div id="pn_Mx100" class="form-group" style="margin-bottom:3px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="background:#eeeeee;width:40%;"><b><span class="billetaje_mon" style="font-size:10px;"></span> 100</b></span>
                          <input id="txt_Mx100" type="numeric" class="form-control text-center" placeholder="0" oninput="javascript:appBillCalcular();"/>
                        </div>
                      </div>
                      <div id="pn_Mx50" class="form-group" style="margin-bottom:3px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="background:#eeeeee;width:40%;"><b><span class="billetaje_mon" style="font-size:10px;"></span> 50</b></span>
                          <input id="txt_Mx50" type="numeric" class="form-control text-center" placeholder="0" oninput="javascript:appBillCalcular();"/>
                        </div>
                      </div>
                      <div id="pn_Mx20" class="form-group" style="margin-bottom:3px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="background:#eeeeee;width:40%;"><b><span class="billetaje_mon" style="font-size:10px;"></span> 20</b></span>
                          <input id="txt_Mx20" type="numeric" class="form-control text-center" placeholder="0" oninput="javascript:appBillCalcular();"/>
                        </div>
                      </div>
                      <div id="pn_Mx10" class="form-group" style="margin-bottom:3px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="background:#eeeeee;width:40%;"><b><span class="billetaje_mon" style="font-size:10px;"></span> 10</b></span>
                          <input id="txt_Mx10" type="numeric" class="form-control text-center" placeholder="0" oninput="javascript:appBillCalcular();"/>
                        </div>
                      </div>
                      <div id="pn_Mx5" class="form-group" style="margin-bottom:3px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="background:#eeeeee;width:40%;"><b><span class="billetaje_mon" style="font-size:10px;"></span> 5</b></span>
                          <input id="txt_Mx5" type="numeric" class="form-control text-center" placeholder="0" oninput="javascript:appBillCalcular();"/>
                        </div>
                      </div>
                      <div id="pn_Mx2" class="form-group" style="margin-bottom:3px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="background:#eeeeee;width:40%;"><b><span class="billetaje_mon" style="font-size:10px;"></span> 2</b></span>
                          <input id="txt_Mx2" type="numeric" class="form-control text-center" placeholder="0" oninput="javascript:appBillCalcular();"/>
                        </div>
                      </div>
                      <div id="pn_Mx1" class="form-group" style="margin-bottom:3px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="background:#eeeeee;width:40%;"><b><span class="billetaje_mon" style="font-size:10px;"></span> 1</b></span>
                          <input id="txt_Mx1" type="numeric" class="form-control text-center" placeholder="0" oninput="javascript:appBillCalcular();"/>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="box-body">
                      <div id="pn_Mx05" class="form-group" style="margin-bottom:3px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="background:#eeeeee;width:40%;"><b><span class="billetaje_mon" style="font-size:10px;"></span> 0.5</b></span>
                          <input id="txt_Mx05" type="numeric" class="form-control text-center" placeholder="0" oninput="javascript:appBillCalcular();"/>
                        </div>
                      </div>
                      <div id="pn_Mx02" class="form-group" style="margin-bottom:3px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="background:#eeeeee;width:40%;"><b><span class="billetaje_mon" style="font-size:10px;"></span> 0.2</b></span>
                          <input id="txt_Mx02" type="numeric" class="form-control text-center" placeholder="0" oninput="javascript:appBillCalcular();"/>
                        </div>
                      </div>
                      <div id="pn_Mx01" class="form-group" style="margin-bottom:3px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="background:#eeeeee;width:40%;"><b><span class="billetaje_mon" style="font-size:10px;"></span> 0.1</b></span>
                          <input id="txt_Mx01" type="numeric" class="form-control text-center" placeholder="0" oninput="javascript:appBillCalcular();"/>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div>
                  <strong style="font-size:18px;">TOTAL: <span class="billetaje_mon" style="font-size:10px;"></span>&nbsp;<span id="txt_MxTotal" style="font-size:18px;"></span></strong>
                </div>
              </div>
            </div>
          </div>
          <div class="box-body">
            <button type="button" class="btn btn-default" onclick="javascript:appBillBotonCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
            <button type="button" id="btnInsert" class="btn btn-primary pull-right" onclick="javascript:appBillInsert();"><i class="fa fa-save"></i> Guardar</button>
            <button type="button" id="btnUpdate" class="btn btn-info pull-right" onclick="javascript:appBillUpdate();"><i class="fa fa-save"></i> Actualizar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>

<script src="pages/caja/billetaje/script.js"></script>
<script>
  $(document).ready(function(){
    appBillReset();
  });
</script>
<?php } ?>