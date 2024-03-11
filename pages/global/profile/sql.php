<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');

      $data = json_decode($_REQUEST['appSQL']);

      //****************personas****************
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
          echo json_encode($rpta);
          break;
        case "updPassword": //cambiar password de usuario
          $sql = "update app_usuarios set passw=:passw where id=:id;";
          $params = [":passw"=>$data->pass,":id"=>$data->userID];
          $qry = $db->query_all($sql, $params);
          $user = ($qry) ? (array("error" => false,"resp" => "Se actualizo el passw")) : (array("error" => true,"resp" => "Fallo actualizacion"));
          
          //respuesta
          $rpta  = $user;
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
