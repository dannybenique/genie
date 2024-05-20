<?php
  include_once('sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      $data = json_decode($_REQUEST['appSQL']);

      switch ($data->TipoQuery) {
        case "selDataUser":
          $rpta = $_SESSION['usr_data'];
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
      }
    } else{
      $resp = array("error"=>true,"mensaje"=>"ninguna variable en POST");
      header('Content-Type: application/json');
      echo json_encode($resp);
    }
  } else {
    $resp = array("error"=>true,"mensaje"=>"Caducó la sesion.");
    header('Content-Type: application/json');
    echo json_encode($resp);
  }
?>
