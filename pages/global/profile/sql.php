<?php
  include_once('../../../includes/sess_verifica.php');
  include_once('../../../includes/db_database.php');
  include_once('../../../includes/funciones.php');

  if (!isset($_SESSION["usr_ID"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Caducó la sesión.")); }
  if (!isset($_REQUEST["appSQL"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Ninguna variable en POST")); }

  $data = json_decode($_REQUEST['appSQL']);

  switch ($data->TipoQuery) {
    case "viewPerfil":
      //user
      $user = $_SESSION['usr_data'];
      $user["menu"] = "";
      $user["rolROOT"] = 101;
      //respuesta
      $rpta = array(
        "tablaPers" => $fn->getViewPersona($data->userID),
        "user" => $user
      );
      $db->enviarRespuesta($rpta);
      break;
    case "updPassword": //cambiar password de usuario
      $sql = "update app_usuarios set passw=:passw where id=:id;";
      $params = [":passw"=>$data->pass,":id"=>$data->userID];
      $qry = $db->query_all($sql, $params);
      $user = ($qry) ? (array("error" => false,"resp" => "Se actualizo el passw")) : (array("error" => true,"resp" => "Fallo actualizacion"));
      
      //respuesta
      $rpta  = $user;
      $db->enviarRespuesta($rpta);
      break;
  }
  $db->close();
?>
