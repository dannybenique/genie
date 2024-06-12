<?php
  include_once('../../../includes/sess_verifica.php');
  include_once('../../../includes/db_database.php');
  include_once('../../../includes/funciones.php');

  if (!isset($_SESSION["usr_ID"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Caducó la sesión.")); }
  if (!isset($_REQUEST["appSQL"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Ninguna variable en POST")); }
  
  $data = json_decode($_REQUEST['appSQL']);
  $rpta = 0;

  switch ($data->TipoQuery) {
    case "selLaboral":
      $db->enviarRespuesta($fn->getEditLaboral($data->ID));
      break;
    case "newLaboral":
      //respuesta
      $rpta = array(
        "fecha" => $fn->getFechaActualDB(),
        "comboRegiones" => ($fn->getComboBox("select id,nombre from sis_ubigeo where id_padre=101 order by nombre;")),
        "comboProvincias" => ($fn->getComboBox("select id,nombre from sis_ubigeo where id_padre=1014 order by nombre;")),
        "comboDistritos" => ($fn->getComboBox("select id,nombre from sis_ubigeo where id_padre=1401 order by nombre;"))
      );
      $db->enviarRespuesta($rpta);
      break;
    case "execLaboral":
      $params = array();
      //if($data->commandSQL=="INS"){ $data->permisoID = 1; }
      //else { if($_SESSION['usr_usernivelID']==701){ $data->permisoID = 1; } }//darle permiso al superusuario

      $sql = "select sp_personas_labo ('".($data->commandSQL)."',".
        ($data->ID).",".
        ($data->personaID).",".
        ($data->condicion).",'".
        ($data->empresa)."','".
        ($data->ruc)."','".
        ($data->telefono)."','".
        ($data->rubro)."',".
        ($data->distritoID).",'".
        ($data->direccion)."','".
        ($data->cargo)."',".
        ($data->ingreso).",'".
        ($data->fechaini)."','".
        ($data->observac)."',".
        ($data->estado).",'".
        $fn->getClientIP()."',".
        $_SESSION['usr_ID'].") as nro;";
      $qry = $db->query_all($sql);
      $rs = ($qry) ? (reset($qry)) : (null);

      //respuesta
      $rpta = array("error"=>false, $data->commandSQL=>1, "sql"=>$sql, "tablaLabo"=>$fn->getAllLaborales($data->personaID));
      $db->enviarRespuesta($rpta);
      break;
    case "ersLaboral": //borrar un registro de forma logica
      $sql = "select sp_personas_labo ('".$data->commandSQL."',".$data->laboralID.",".$data->personaID.",0,'','','','',0,'','',0,now()::date,'',0,'".$fn->getClientIP()."',".$_SESSION['usr_ID'].") as nro;";
      $qry = $db->query_all($sql);
      $rs = ($qry) ? (reset($qry)) : (null);
      
      //respuesta
      $rpta = array("error"=>false, $data->commandSQL=>1, "sql"=>$sql, "tablaLabo"=>$fn->getAllLaborales($data->personaID));
      $db->enviarRespuesta($rpta);
      break;
    case "comboUbigeo":
      switch($data->tipoID){
        case 3: //actualiza provincia
          $provincias = $fn->getComboBox("select id,nombre from sis_ubigeo where id_padre=".$data->padreID." order by nombre;");
          $distritos = $fn->getComboBox("select id,nombre from sis_ubigeo where id_padre=".$provincias[0]["ID"]." order by nombre;");
          $rpta = array( "provincias" => $provincias, "distritos" => $distritos );
          break;
        case 4: //actualiza distrito
          $distritos = $fn->getComboBox("select id,nombre from sis_ubigeo where id_padre=".$data->padreID." order by nombre;");
          $rpta = array( "distritos" => $distritos );
          break;
      }
      //respuesta
      $db->enviarRespuesta($rpta);
      break;
  }
  $db->close();
?>
