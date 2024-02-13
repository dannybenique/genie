<!-- datepicker -->
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- ChartJS -->
<script src="libs/chart.js/chart.min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><b>Dashboard</b></h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Dashboard</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div id="dashboard1" class="row">
    <div class="col-md-3 col-sm-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon bg-blue"><i class="fa fa-graduation-cap"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Alumnos</span>
          <span id="appTotalAlumnos" class="info-box-number" style="font-size:22px;"></span>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon bg-aqua"><i class="fa fa-users"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Padres</span>
          <span id="appTotalPadres" class="info-box-number" style="font-size:22px;"></span>
        </div>
      </div>
    </div>
  </div>
  <div id="dashboard2" class="row">
    <div class="col-md-3 col-sm-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon bg-green"><i class="fa fa-gg-circle"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Matriculas</span>
          <span id="appTotalMatriculas" class="info-box-number" style="font-size:22px;"></span>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-3 col-xs-6">
      <div class="small-box bg-yellow">
        <div class="inner">
          <h3 ><sup style="font-size:20px">&nbsp;</sup></h3>
          <p>Colegios</p>
        </div>
        <div class="icon">
          <i class="fa fa-building"></i>
        </div>
        <a href="javascript:appSubmitButton('mttoAgencias');" class="small-box-footer">Mas info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
  </div>
</section>

<!-- dashboard.js -->
<script src="pages/global/dashboard/script.js"></script>

<script>
  $(document).ready(function(){ appDashBoard(); });
</script>
