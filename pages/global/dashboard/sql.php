<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/web_config.php');

      $data = json_decode($_REQUEST['appSQL']);
      switch ($data->TipoQuery) {
        case "dashboard":
          $qry = $db->query_all("select count(*) as cuenta from app_colegios;");
          $colegios = reset($qry)['cuenta'];
          $qry = $db->query_all("select count(*) as cuenta from app_alumnos where id_colegio=:colegioID", [':colegioID'=>$web->colegioID]);
          $alumnos = reset($qry)['cuenta'];
          $qry = $db->query_all("select count(*) as cuenta from app_padres where id_colegio=:colegioID", [':colegioID'=>$web->colegioID]);
          $padres = reset($qry)['cuenta'];

          //respuesta
          $rpta = array(
            "colegios" => $colegios,
            "alumnos" => $alumnos,
            "padres" => $padres
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
