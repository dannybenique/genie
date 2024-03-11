const rutaSQL = "pages/global/dashboard/sql.php";

//=========================funciones para Dashboard============================
async function appDashBoard(){
  $("#appTotalMatriculas").html('<span class="wrap-loading" style="left:-8px;"><span class="loading loading-1"></span></span>');
  $("#appTotalAlumnos").html('<span class="wrap-loading" style="left:-8px;"><span class="loading loading-1"></span></span>');
  $("#appTotalPadres").html('<span class="wrap-loading" style="left:-8px;"><span class="loading loading-1"></span></span>');
  $("#appConfigYearMatricula").html('<span class="wrap-loading" style="left:-8px;"><span class="loading loading-1"></span></span>');
  
  const resp = await appAsynFetch({ TipoQuery : 'dashboard' },rutaSQL);
  $("#appTotalAlumnos").html(resp.alumnos);
  $("#appTotalPadres").html(resp.padres);
  $("#appConfigYearMatricula").html(resp.config.YearCurrentMatricula);

  //matriculas
  const porcTotalMatric = resp.CantMatricActual * 100 / resp.TotalMatricCole;
  $("#appTotalMatriculas").html('<span class="info-box-number">'+(resp.CantMatricActual)+'</span>'+
    '<div class="progress"><div class="progress-bar" style="width: '+appFormatMoney(porcTotalMatric,2)+'%"></div></div>'+
    '<span class="progress-description">'+appFormatMoney(porcTotalMatric,2)+'% del total de Matriculas</span>');
}
