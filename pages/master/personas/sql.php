<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');

      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      //****************personas****************
      switch ($data->TipoQuery) {
        case "selPersonas":
          $rpta = $fn->getAllPersonas($data->buscar,0);
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "viewPersona":
          switch($data->fullQuery){
            case 0: //datos personales
              $rpta = array('tablaPers'=>$fn->getViewPersona($data->personaID));
              break;
            case 1: //datos personales + laborales
              $rpta = array(
                'tablaPers'=>$fn->getViewPersona($data->personaID),
                'tablaLabo'=>$fn->getAllLaborales($data->personaID));
              break;
            case 2: //datos personales + laborales + conyuge
              $rpta = array(
                'tablaPers'=>$fn->getViewPersona($data->personaID),
                'tablaLabo'=>$fn->getAllLaborales($data->personaID),
                'tablaCony'=>$fn->getViewConyuge($data->personaID));
              break;
          }
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "audiPersona": //auditoria de personas
          $tablaPers = $fc->getViewPersona($data->personaID);
          $tablaLog = array();
          $sql = "select * from dbo.vw_sislog where tabla like'tb_persona%' and ID=".$data->personaID." order by sysfecha desc,syshora desc;";
          $qry = $db->query($sql);
          if ($db->num_rows($qry)>0) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tablaLog[] = array(
                "codigo" => $rs["codigo"],
                "tabla" => ($rs["tabla"]),
                "accion" => ($rs["accion"]),
                "campo" => ($rs["campo"]),
                "observac" => ($rs["observ"]),
                "usuario" => ($rs["usuario"]),
                "sysIP" => ($rs["sysIP"]),
                "sysagencia" => ($rs["sysagencia"]),
                "sysfecha" => ($rs["sysfecha1"]),
                "syshora" => ($rs["syshora1"])
              );
            }
          }
          $rpta = array("tablaPers"=>$tablaPers,"tablaLog"=>$tablaLog);
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "VerifyPersona":
          $tablaPers = ""; //almacena los datos de la persona
          $persona = false; //indica que existe en personas
          $activo = false; //indica que encontro en tabla personas
          
          $sql = "select id from personas where (nro_dui=:dui);";
          $params = [":dui"=>$data->nroDNI];
          $qry = $db->query_all($sql,$params);
          if($qry){
            $rs = reset($qry);
            $tablaPers = $fn->getViewPersona($rs["id"]);
            $persona = true;
            $activo = true;
          }

          //respuesta
          $rpta = array(
            "tablaPers" => $tablaPers,
            "persona" => $persona,
            "activo" => $activo,
            "mensajeNOadd" => "ya fue ingresada...");
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
      }
      $db->close();
    } else{
      $resp = array("error"=>true,"data"=>$tabla,"mensaje"=>"ninguna variable en POST");
      header('Content-Type: application/json');
      echo json_encode($resp);
    }
  } else {
    $resp = array("error"=>true,"mensaje"=>"Caducó la sesion.");
    header('Content-Type: application/json');
    echo json_encode($resp);
  }
?>
