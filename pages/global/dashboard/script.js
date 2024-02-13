const rutaSQL = "pages/global/dashboard/sql.php";

//=========================funciones para Dashboard============================
function appDashBoard(){
  $("#appTotalMatriculas").html('<span class="wrap-loading"><span class="loading loading-1"></span></span>');
  $("#appTotalAlumnos").html('<span class="wrap-loading"><span class="loading loading-1"></span></span>');
  $("#appTotalPadres").html('<span class="wrap-loading"><span class="loading loading-1"></span></span>');
  appFetch({ TipoQuery : 'dashboard' },rutaSQL).then(resp => {
    // console.log(resp);
    $("#appTotalMatriculas").html(resp.matriculas);
    $("#appTotalAlumnos").html(resp.alumnos);
    $("#appTotalPadres").html(resp.padres);
  });
}
