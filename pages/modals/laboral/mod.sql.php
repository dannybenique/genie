<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');
      $data = json_decode($_REQUEST['appSQL']);
      
      switch ($data->TipoQuery) {
        case "selLaboral":
          header('Content-Type: application/json');
          echo json_encode($fn->getEditLaboral($data->ID));
          break;
        case "newLaboral":
          //obtener fecha actual de operacion
          $qry = $db->query_all("select cast(now() as date) as fecha");
          if($qry){ $rs = reset($qry); }
          $fechaHoy = $rs["fecha"];

          $rpta = array(
            "fecha" => $fechaHoy,
            "comboRegiones" => ($fn->getComboBox("select id,nombre from sis_ubigeo where id_padre=101 order by nombre;")),
            "comboProvincias" => ($fn->getComboBox("select id,nombre from sis_ubigeo where id_padre=1014 order by nombre;")),
            "comboDistritos" => ($fn->getComboBox("select id,nombre from sis_ubigeo where id_padre=1401 order by nombre;"))
          );
          header('Content-Type: application/json');
          echo json_encode($rpta);
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
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "ersLaboral": //borrar un registro de forma logica
          $sql = "select sp_personas_labo ('".$data->commandSQL."',".$data->laboralID.",".$data->personaID.",0,'','','','',0,'','',0,now()::date,'',0,'".$fn->getClientIP()."',".$_SESSION['usr_ID'].") as nro;";
          $qry = $db->query_all($sql);
          $rs = ($qry) ? (reset($qry)) : (null);
          
          //respuesta
          $rpta = array("error"=>false, $data->commandSQL=>1, "sql"=>$sql, "tablaLabo"=>$fn->getAllLaborales($data->personaID));
          header('Content-Type: application/json');
          echo json_encode($rpta);
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
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
      }
      $db->close();
    } else {
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
