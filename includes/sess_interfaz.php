<?php
  include_once('sess_verifica.php');
  function enviarRespuesta($resp){
    header('Content-Type: application/json');
    echo json_encode($resp);
    exit;
  }

  if (!isset($_SESSION["usr_ID"])) { enviarRespuesta(array("error" => true, "mensaje" => "Caducó la sesión.")); }
  if (!isset($_REQUEST["appSQL"])) { enviarRespuesta(array("error" => true, "mensaje" => "Ninguna variable en POST")); }

  $data = json_decode($_REQUEST['appSQL']);

  switch ($data->TipoQuery) {
    case "selDataUser":
      enviarRespuesta($_SESSION['usr_data']);
      break;
    default:
      enviarRespuesta(array("error" => true, "mensaje" => "TipoQuery no válido"));
      break;
  }
?>
