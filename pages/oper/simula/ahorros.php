<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css"/>
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-flag"></i> Simulador de Ahorros</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Simulador de Ahorros</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row" id="edit">
    <div class="col-md-5">
      <form class="form-horizontal" autocomplete="off">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title" style="font-family:flexoregular;"><b>Datos para Simulacion</b></h3>
          </div>
          <div class="box-body">
            <div class="box-body">
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon"><b>Producto</b></span>
                  <select id="cbo_Productos" name="cbo_Productos" class="form-control selectpicker"></select>
                </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon" style="width:85px;"><b>Fecha Ini</b></span>
                  <input id="date_FechaIni" name="date_FechaIni" type="text" class="form-control" style="width:105px;"/>
                </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon" style="width:85px;"><b>Meses</b></span>
                  <input id="txt_TiempoMeses" name="txt_TiempoMeses" type="number" class="form-control" value="3" placeholder="..." style="width:70px;" onblur="javascrit:appAhorrosFechaFin();"/>
                  <span id="date_FechaFin" class="input-group-addon" style="width:105px;font-weight:bold;color:#888;"></span>
                  <span id="dias_FechaFin" class="input-group-addon" style="width:60px;font-weight:bold;color:#888;">0</span>
                </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon" style="width:85px;"><b>Tasa %</b></span>
                  <input id="txt_Tasa" name="txt_Tasa" type="text" class="form-control" style="width:130px;"/>
                </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon" style="width:85px;"><b>Importe</b></span>
                  <input id="txt_Importe" name="txt_Importe" type="text" class="form-control" style="width:130px;"/>
                </div>
              </div>
            </div>
            <div class="btn-group">
              <button type="button" class="btn btn-info" id="btn_print" onclick="javascript:appAhorrosGenerarIntereses();"><i class="fa fa-flash"></i> Generar Simulacion</button>
            </div>
          </div>
        </div>
      </form>
    </div>
    <div class="col-md-7">
      <div class="box box-primary">
        <div class="box-body box-profile">
          <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grdDatos">
                <thead>
                  <tr>
                    <th style="width:30px;">Nro</th>
                    <th style="width:90px;">Fecha</th>
                    <th style="width:100px;text-align:right;">Capital</th>
                    <th style="width:100px;text-align:right;">Interes</th>
                    <th style="width:100px;text-align:right;">Total</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody id="grdDatosBody">
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
    appAhorrosReset();
  });
</script>
