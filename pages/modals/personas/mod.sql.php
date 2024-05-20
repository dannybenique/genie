<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      function cadSQL($objDatos){
        $fn = $GLOBALS["fn"];
        return "select sp_personas ('".$objDatos->commandSQL."',".
          ($objDatos->ID).",".
          ($objDatos->persTipoPersona).",'".
          ($objDatos->persNombres)."','".
          ($objDatos->persApePaterno)."','".
          ($objDatos->persApeMaterno)."','".
          ($objDatos->persNroDUI)."',".
          ($objDatos->persId_DUI).",".
          ($objDatos->persId_sexo).",".
          ($objDatos->persId_Ginstruc).",".
          ($objDatos->persId_Ecivil).",".
          ($objDatos->persId_Ubigeo).",".
          ($objDatos->persId_TipoVivienda).",".
          ($objDatos->persId_Paisnac).",'".
          ($objDatos->persFechaNac)."','".
          ($objDatos->persLugarNac)."','".
          ($objDatos->persTelefijo)."','".
          ($objDatos->persCelular)."','".
          ($objDatos->persEmail)."','".
          ($objDatos->persProfesion)."','".
          ($objDatos->persOcupacion)."','".
          ($objDatos->persDireccion)."','".
          ($objDatos->persReferencia)."','".
          ($objDatos->persMedidorluz)."','".
          ($objDatos->persMedidorAgua)."','".
          ($objDatos->persUrlFoto)."','".
          ($objDatos->persObservac)."','".
          $fn->getClientIP()."',".
          $_SESSION['usr_ID'].") as nro";
      }
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');
      $data = json_decode($_REQUEST['appSQL']);

      switch ($data->TipoQuery) {
        case "selPersona":
          header('Content-Type: application/json');
          echo json_encode($fn->getEditPersona($data->personaID));
          break;
        case "newPersona":
          //obtener fecha actual de operacion
          $qry = $db->query_all("select cast(now() as date) as fecha");
          $fechaHoy = reset($qry)["fecha"];

          //comboBox inicial
          $rpta = array(
            "fecha" => $fechaHoy,
            "comboPais" => ($fn->getComboBox("select id,nombre from sis_ubigeo where tipo=1 order by nombre;")),
            "comboDUI" => ($fn->getComboBox("select id,nombre from personas_aux where id_padre=5 order by orden;")),
            "comboSexo" => ($fn->getComboBox("select id,nombre from personas_aux where id_padre=1 order by orden;")),
            "comboECivil" => ($fn->getComboBox("select id,nombre from personas_aux where id_padre=2 order by orden;")),
            "comboGInstruc" => ($fn->getComboBox("select id,nombre from personas_aux where id_padre=3 order by orden;")),
            "comboTipoViv" => ($fn->getComboBox("select id,nombre from personas_aux where id_padre=4 order by orden;")),
            "comboRegiones" => ($fn->getComboBox("select id,nombre from sis_ubigeo where id_padre=101 order by nombre;")),
            "comboProvincias" => ($fn->getComboBox("select id,nombre from sis_ubigeo where id_padre=1014 order by nombre;")),
            "comboDistritos" => ($fn->getComboBox("select id,nombre from sis_ubigeo where id_padre=1401 order by nombre;"))
          );
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "insPersona":
          try {
            $sql = cadSQL($data);
            $qry = $db->query_all($sql);
            $rs = ($qry) ? (reset($qry)) : (null); 
            
            //respuesta
            $rpta = array("error"=>false, $data->commandSQL=>1, "sql"=>$sql, "tablaPers"=>$fn->getViewPersona($rs["nro"])); 
            header('Content-Type: application/json');
            echo json_encode($rpta);
          } catch(Exception $e) {
            header('Content-Type: application/json');
            echo json_encode($e->getMessage());
          }
          break;
        case "updPersona":
          try {
            //en caso de haber fotos
            if(isset($_FILES["imgFoto"])){
              $foto = $_FILES["imgFoto"];
              if(is_uploaded_file($foto['tmp_name'])){
                if($foto["type"]=="image/jpg" or $foto["type"]=="image/jpeg"){
                  $data->persUrlFoto = "data/personas/".$data->ID.".jpg";
                  $ruta = "../../../".$data->persUrlFoto;
                  move_uploaded_file($foto["tmp_name"],$ruta);
                }
              }
            }

            //datos para DB
            $sql = cadSQL($data);
            $qry = $db->query_all($sql);
            $rs = ($qry) ? (reset($qry)) : (null); 
            
            //respuesta
            $rpta = array("error"=>false, $data->commandSQL=>1, "sql"=>$sql, "tablaPers"=>$fn->getViewPersona($rs["nro"])); 
            header('Content-Type: application/json');
            echo json_encode($rpta);
          } catch(Exception $e) {
            header('Content-Type: application/json');
            echo json_encode($e->getMessage());
          }
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
        
        case "VerifyBlacklist":
          $activo = 0; //indica que encontro en blacklist
          $persona = 0; //indica que encontro en personas
          //verificar en Personas
          $qryPers = $db->select(utf8_decode("select ID from dbo.tb_personas where (DNI='".$data->nroDNI."');"));
          if($db->has_rows($qryPers)){
            $rsPers = $db->fetch_array($qryPers);
            $persona = getOnePersona($rsPers["ID"]);
            //verificar en blacklist
            $qryBlacklist = $db->select(utf8_decode("select id_persona from dbo.tb_blacklist where (id_persona=".$rsPers["ID"].");"));
            if($db->has_rows($qryBlacklist)){
              $rsBlacklist = $db->fetch_array($qryBlacklist);
              $activo = 1;
            }
          }

          //respuesta
          $rpta = array("persona"=>$persona,"activo"=>$activo);
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "VerifyAhorros":
          $activo = 0; //indica que encontro en ahorros
          $persona = 0; //indica que encontro en personas
          //verificar en Personas
          $qryPers = $db->select(utf8_decode("select id_persona from dbo.vw_socios where (DNI='".$data->nroDNI."');"));
          if($db->has_rows($qryPers)){
            $rsPers = $db->fetch_array($qryPers);
            $persona = getOnePersona($rsPers["id_persona"]);
            //verificar en ahorros
            $qry = $db->select(utf8_decode("select id_socio from dbo.tb_oper_ahorros where (id_socio=".$rsPers["id_persona"].");"));
            if($db->has_rows($qry)){ $activo = 1; }
            //verificar en Aportes
            $qry = $db->select(utf8_decode("select id_socio from dbo.tb_oper_aportes where id_socio=".$rsPers["id_persona"]." and id_producto=1 and saldo<0;"));
            if($db->has_rows($qry)){ $activo = 2; }
          }

          //respuesta
          $rpta = array("persona"=>$persona,"activo"=>$activo);
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "VerifySuplentes":
          $activo = 0; //indica que encontro en ahorros
          $persona = 0; //indica que encontro en personas
          //verificar en Personas
          $qryPers = $db->select(utf8_decode("select ID from dbo.vw_personas where (DNI='".$data->nroDNI."');"));
          if($db->has_rows($qryPers)){
            $rsPers = $db->fetch_array($qryPers);
            $persona = getOnePersona($rsPers["ID"]);
            //verificar en suplentes
            $qryAhorros = $db->select(utf8_decode("select id_suplente from dbo.tb_oper_ahorros_suplentes where id_suplente=".$rsPers["ID"]." and id_ahorro=".$data->foreignKey.";"));
            if($db->has_rows($qryAhorros)){ $activo = 1; }
          }

          //respuesta
          $rpta = array("persona"=>$persona,"activo"=>$activo);
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "VerifyCajaProveedor":
          $activo = 0; //indica que encontro en ahorros
          $persona = 0; //indica que encontro en personas
          //verificar en Personas
          $qryPers = $db->select(utf8_decode("select ID from dbo.tb_personas where (DNI='".$data->nroDNI."');"));
          if($db->has_rows($qryPers)){
            $rsPers = $db->fetch_array($qryPers);
            $persona = getOnePersona($rsPers["ID"]);
            //verificar en caja
            //$qryAhorros = $db->select(utf8_decode("select id_suplente from dbo.tb_oper_ahorros_suplentes where id_suplente=".$rsPers["ID"]." and id_ahorro=".$data->foreignKey.";"));
            //if($db->has_rows($qryAhorros)){ $activo = 1; }
          }

          //respuesta
          $rpta = array("persona"=>$persona,"activo"=>$activo);
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "VerifyWorker":
          $activo = 0; //indica que encontro en workers
          $persona = 0; //indica que encontro en personas

          //verificar en Personas
          $qryPers = $db->select(utf8_decode("select ID from dbo.tb_personas where (DNI='".$data->nroDNI."');"));
          if($db->has_rows($qryPers)){
            $rsPers = $db->fetch_array($qryPers);
            $persona = getOnePersona($rsPers["ID"]);
            //verificar en workers
            $qryWorker = $db->select(utf8_decode("select id_persona,estado from dbo.tb_workers where (id_persona=".$rsPers["ID"].");"));
            if($db->has_rows($qryWorker)){
              $rsWorker = $db->fetch_array($qryWorker);
              //verificar estado
              //if($rsWorker["estado"]==1) { $estado = 1; }
              $activo = 1;
            }
          }

          //respuesta
          $rpta = array("persona"=>$persona,"activo"=>$activo);
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "VerifyPreventa":
          $activo = 0;
          $persona = 0; //indica que encontro en personas
          //verificar en Personas
          $qryPers = $db->select(utf8_decode("select ID from dbo.tb_personas where (DNI='".$data->nroDNI."');"));
          if($db->has_rows($qryPers)){
            $rsPers = $db->fetch_array($qryPers);
            $persona = getOnePersona($rsPers["ID"]);
            //verificar en Socios
            $qry = $db->select(utf8_decode("select id_persona,estado from dbo.tb_oper_captaciones where (id_persona=".$rsPers["ID"].");"));
            if($db->has_rows($qry)){ $activo = 1; }
          }

          //respuesta
          $rpta = array("persona"=>$persona,"activo"=>$activo);
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        /*case "apiPeru"://consulta el ruc o DNI
          $url = (strlen($data->nroDNI)==8)?("dni/".$data->nroDNI):((strlen($data->nroDNI)==11)?("ruc/".$data->nroDNI):(""));
          $veri = @file_get_contents("https://dniruc.apisperu.com/api/v1/".$url."?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImRhbm55YmVuaXF1ZUBtc24uY29tIn0.ts3qFRsLtLxqnoOMvwYEeOu470tyTUGWQbsuH4ZTC7I")
                  or exit(12);

          if($veri==false) { $retu = array("error"=>true,"message"=>$veri); }
          else { $retu = array("error"=>false,"api"=>$veri); }

          //respuesta
          $rpta = $retu;
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;*/
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
