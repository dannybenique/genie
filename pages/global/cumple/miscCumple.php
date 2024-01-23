<!-- fullCalendar -->
<script src="libs/moment/min/moment.min.js"></script>
<link rel="stylesheet" href="libs/fullcalendar/dist/fullcalendar.min.css">
<script src="libs/fullcalendar/dist/fullcalendar.min.js"></script>
<script src="libs/fullcalendar/dist/locale/es.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-pie-chart"></i> Reporte de Cumpleaños</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Cumpleaños</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-9">
      <div class="box box-primary">
        <div class="box-body no-padding">
          <!-- THE CALENDAR -->
          <div id="calendar"></div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- /.content -->

<!-- SlimScroll -->
<script src="libs/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="libs/fastclick/lib/fastclick.js"></script>

<script>
  $(function () {
    /* ==================initialize the calendar================== */
    var date = new Date();
    var d    = date.getDate(),
        m    = date.getMonth(),
        y    = date.getFullYear();

    //Fechas de cumple de este mes
    $('#calendar').fullCalendar({
      firstDay: 0,
      header    : { left : 'prev,next', center : 'title', right : '' },
      buttonText: { today: 'hoy', month: 'mes' },
      //defaultDate:'2018-12-12',
      businessHours: false,
      editable  : false,
      droppable : false,
      events:
        { url:'pages/global/cumple/sql.php',
          color:'#605CA8',
          textColor:'white',
          error:function(resp){ console.log(resp);}
        }
    })
  })
</script>
