<?php
  include_once('../../../includes/sess_verifica.php');
  include_once('../../../includes/db_database.php');
  include_once('../../../includes/web_config.php');
  include_once('../../../includes/funciones.php');
  
  if (!isset($_SESSION["usr_ID"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Caducó la sesión.")); }
  if (!isset($_REQUEST["appSQL"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Ninguna variable en POST")); }
  
  $data = json_decode($_REQUEST['appSQL']);
  $rpta = 0;

  switch ($data->TipoQuery) {
    case "config_start":
      //obtener configuracion del colegio actual
      $config = json_decode($fn->getConfigColegio($web->colegioID));
      
      //respuesta
      $rpta = array(
        "comboYEAR" => $fn->getComboBox("select generate_series(extract(year from current_date)-1,extract(year from current_date)+1) as id,1 as nombre;"),
        "config" => $config, //configuracion de colegio
        "colegioID" => $web->colegioID
      );
      $db->enviarRespuesta($rpta);
      break;
    case "config_update":
      $sql = "update app_colegios set config=:config where id=:colegioID";
      $params = [
        ":config"=>$data->config,
        ":colegioID"=>$web->colegioID
      ];
      $qry = $db->query_all($sql,$params);
      $rs = ($qry) ? (reset($qry)) : (null);

      //respuesta
      $rpta = array("error" => false);
      $db->enviarRespuesta($rpta);
      break;
  }
  $db->close();
?>
