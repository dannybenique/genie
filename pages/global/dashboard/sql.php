<?php
  include_once('../../../includes/sess_verifica.php');
  include_once('../../../includes/db_database.php');
  include_once('../../../includes/web_config.php');
  include_once('../../../includes/funciones.php');

  if (!isset($_SESSION["usr_ID"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Caducó la sesión.")); }
  if (!isset($_REQUEST["appSQL"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Ninguna variable en POST")); }

  $data = json_decode($_REQUEST['appSQL']);

  switch ($data->TipoQuery) {
    case "dashboard":
      $colegioID = $web->colegioID;
      
      $matriculas = $fn->getValorCampo("select count(*) as cuenta from app_matriculas where estado=1 and id_colegio=".$colegioID, 'cuenta'); // matriculas
      $alumnos = $fn->getValorCampo("select count(*) as cuenta from app_alumnos where estado=1 and id_colegio=".$colegioID, 'cuenta'); // alumnos
      $padres = $fn->getValorCampo("select count(*) as cuenta from app_padres where estado=1 and id_colegio=".$colegioID, 'cuenta'); // padres
      $empleados = $fn->getValorCampo("select count(*) as cuenta from app_empleados where estado=1 and id_colegio=".$colegioID, 'cuenta'); //empleados
      
      // config
      $qry = $db->query_all("select c.config,sum(x.capacidad) as captotal from app_colniv x join app_colegios c on c.id=x.id_colegio where c.id=:colegioID group by c.config;", [':colegioID'=>$colegioID]);
      $rs = reset($qry);
      $config = json_decode($rs['config']);
      $capTotal = $rs['captotal'];
      
      //respuesta
      $rpta = array(
        "alumnos" => $alumnos,
        "padres" => $padres,
        "empleados" => $empleados,
        "config" => $config,
        "CantMatricActual" => $matriculas, //cantidad actual de matriculados
        "TotalMatricCole" => $capTotal //capacidad Total de matricular
      );
      $db->enviarRespuesta($rpta);
      break;
  }
  $db->close();
?>
