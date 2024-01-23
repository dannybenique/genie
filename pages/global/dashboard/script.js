const rutaSQL = "pages/global/dashboard/sql.php";

//=========================funciones para Dashboard============================
function appDashBoard(){
  appFetch({ TipoQuery : 'dashboard' },rutaSQL).then(resp => {
    // console.log(resp);
    $("#appTotalColegios").html(resp.colegios);
    $("#appTotalAlumnos").html(resp.alumnos);
    $("#appTotalPadres").html(resp.padres);
  });
}
