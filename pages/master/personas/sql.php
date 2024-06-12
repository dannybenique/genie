<?php
  include_once('../../../includes/sess_verifica.php');
  include_once('../../../includes/db_database.php');
  include_once('../../../includes/funciones.php');

  if (!isset($_SESSION["usr_ID"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Caducó la sesión.")); }
  if (!isset($_REQUEST["appSQL"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Ninguna variable en POST")); }
  
  $data = json_decode($_REQUEST['appSQL']);
  $rpta = 0;

  switch ($data->TipoQuery) {
    case "persona_sel":
      $rpta = $fn->getAllPersonas($data->buscar,0);
      $db->enviarRespuesta($rpta);
      break;
    case "persona_view":
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
      $db->enviarRespuesta($rpta);
      break;
    case "persona_del":
      for($i=0; $i<count($data->arr); $i++){
        $sql = "delete from personas where id=".$data->arr[$i].";";
        $qry = $db->query_all($sql);
        $rs = ($qry) ? (reset($qry)) : (null);
      }
      //respuesta
      $rpta = array("error"=>false, "delete"=>$data->arr);
      $db->enviarRespuesta($rpta);
      break;
    case "persona_audit": //auditoria de personas
      $tablaPers = $fc->getViewPersona($data->personaID);
      $tablaLog = array();
      $qry = $db->query_all("select * from sislog where tabla like'tb_persona%' and ID=".$data->personaID." order by sysfecha desc,syshora desc;");

      if ($qry) {
        foreach($qry as $rs){
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
      $db->enviarRespuesta($rpta);
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
      $db->enviarRespuesta($rpta);
      break;
  }
  $db->close();
?>
