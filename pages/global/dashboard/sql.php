<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/web_config.php');

      $data = json_decode($_REQUEST['appSQL']);
      switch ($data->TipoQuery) {
        case "dashboard":
          $colegioID = $web->colegioID;
          // matriculas
          $qry = $db->query_all("select count(*) as cuenta from app_matriculas where estado=1 and id_colegio=:colegioID", [':colegioID'=>$colegioID]);
          $matriculas = reset($qry)['cuenta'];
          // alumnos
          $qry = $db->query_all("select count(*) as cuenta from app_alumnos where estado=1 and id_colegio=:colegioID", [':colegioID'=>$colegioID]);
          $alumnos = reset($qry)['cuenta'];
          // padres
          $qry = $db->query_all("select count(*) as cuenta from app_padres where id_colegio=:colegioID", [':colegioID'=>$colegioID]);
          $padres = reset($qry)['cuenta'];
          //empleados
          $qry = $db->query_all("select count(*) as cuenta from app_empleados where estado=1 and id_colegio=:colegioID", [':colegioID'=>$colegioID]);
          $empleados = reset($qry)['cuenta'];
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
          echo json_encode($rpta);
          break;
      }
      $db->close();
    } else{
      $resp = array("error"=>true,"mensaje"=>"ninguna variable en POST");
      echo json_encode($resp);
    }
  } else {
    $resp = array("error"=>true,"mensaje"=>"Caducó la sesion.");
    echo json_encode($resp);
  }
?>
