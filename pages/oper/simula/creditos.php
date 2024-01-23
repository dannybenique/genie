<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-wrench"></i> <b>Simulador de Creditos</b></h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Simulador de Creditos</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row" id="edit">
    <div class="col-md-3">
      <form class="form-horizontal" autocomplete="off">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title" style="font-family:flexoregular;"><b>Datos para Simulacion</b></h3>
          </div>
          <div class="box-body">
            <div class="box-body">
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon" style="background:#eeeeee;width:95px;"><b>Tipo</b></span>
                  <select id="cbo_TipoCredito" class="form-control selectpicker" style="width:130px;" onchange="javascript:appCreditosCambiarTipoCredito();">
                    <option value="1">Fecha Fija</option>
                    <option value="2">Plazo Fijo</option>
                  </select>
                </div>
              </div>
              <div class="form-group" style="margin-bottom:5px;">
                <div class="input-group">
                  <span class="input-group-addon" style="background:#eeeeee;width:95px;"><b>Importe S/.</b></span>
                  <input id="txt_Importe" type="text" class="form-control" style="width:100px;"/>
                </div>
              </div>
              <div class="form-group" style="margin-bottom:5px;">
                <div class="input-group">
                  <span class="input-group-addon" style="background:#eeeeee;width:95px;"><b>Nº Cuotas</b></span>
                  <input id="txt_NroCuotas" type="number" class="form-control" style="width:100px;"/>
                </div>
              </div>
              <div class="form-group" style="margin-bottom:5px;">
                <div class="input-group">
                  <span class="input-group-addon" style="background:#eeeeee;width:95px;" title="Tasa Efectiva Anual"><b>TEA %</b></span>
                  <input id="txt_TEA" type="text" class="form-control" style="width:100px;"/>
                </div>
              </div>
              <div class="form-group" style="margin-bottom:5px;">
                <div class="input-group">
                  <span class="input-group-addon" style="background:#eeeeee;width:95px;" title="Seguro de Desgravamen"><b>S. Desgr. %</b></span>
                  <input id="txt_SegDesgr" type="number" class="form-control" style="width:100px;" value="0.10"/>
                </div>
              </div>
              <div class="form-group" style="margin-bottom:5px;">
                <div class="input-group">
                  <span class="input-group-addon" style="background:#eeeeee;width:95px;" title="Fecha de Inicio"><b>Fecha Ini</b></span>
                  <input id="txt_FechaSimula" type="text" class="form-control" style="width:105px;"/>
                </div>
              </div>
              <div id="div_FechaPriCuota" class="form-group">
                <div class="input-group">
                  <span class="input-group-addon" style="background:#eeeeee;width:95px;" title="Fecha para la primera cuota"><b>1° Cuota</b></span>
                  <input id="txt_FechaPriCuota" type="text" class="form-control" style="width:105px;"/>
                </div>
              </div>
              <div id="div_Frecuencia" class="form-group" style="display:none;">
                <div class="input-group">
                  <span class="input-group-addon" style="background:#eeeeee;width:95px;" title="Frecuencia de Pago en dias"><b>Frecuencia</b></span>
                  <input id="txt_Frecuencia" type="number" class="form-control" style="width:105px;"/>
                </div>
              </div>
            </div>
            <div class="btn-group pull-left">
              <button type="button" class="btn btn-default" id="btn_print" onclick="javascript:appCreditosReset();"><i class="fa fa-refresh"></i></button>
            </div>
            <div class="btn-group pull-right">
              <button type="button" class="btn btn-info" id="btn_print" onclick="javascript:appCreditosGenerarPlanPagos();"><i class="fa fa-flash"></i> Generar Simulacion</button>
            </div>
          </div>
        </div>
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title" style="font-family:flexoregular;"><b>Tasas</b></h3>
          </div>
          <div class="box-body">
            <div class="box-body">
              <div class="form-group" style="margin-bottom:10px;">
                <div class="input-group">
                  <span class="label label-success" style="font-size:14px;font-weight:normal;"><i class="fa fa-pencil margin-r-5"></i> TEA: <span id="lbl_TEA">0.00 %</span></span>
                </div>
              </div>
              <div class="form-group" style="margin-bottom:10px;">
                <div class="input-group">
                  <span class="label label-info" style="font-size:14px;font-weight:normal;"><i class="fa fa-pencil margin-r-5"></i> TEM: <span id="lbl_TEM">0.00 %</span></span>
                </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <span class="label label-warning" style="font-size:14px;font-weight:normal;"><i class="fa fa-pencil margin-r-5"></i> TED: <span id="lbl_TED">0.00 %</span></span>
                </div>
              </div>
            </div>
          </div>    
        </div>
      </form>
    </div>
    <div class="col-md-9">
      <div class="box box-primary">
        <div class="box-body box-profile">
          <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grd" style="font-family:helveticaneue_light;">
                <thead>
                  <tr>
                    <th style="width:30px;">Nro</th>
                    <th style="width:40px;" title="dias">Dias</th>
                    <th style="width:80px;text-align:center;">Fecha</th>
                    <th style="width:95px;text-align:right;">Total</th>
                    <th style="width:95px;text-align:right;">Capital</th>
                    <th style="width:95px;text-align:right;">Interes</th>
                    <th style="width:80px;text-align:right;">Desgr</th>
                    <th style="width:100px;text-align:right;">Saldo</th>
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
</section>

<script src="pages/oper/simula/simula.js"></script>
<script>
  $(document).ready(function(){
    appCreditosReset();
  });
</script>
