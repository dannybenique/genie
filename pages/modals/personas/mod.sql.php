<?php
  include_once('../../../includes/sess_verifica.php');
  include_once('../../../includes/db_database.php');
  include_once('../../../includes/funciones.php');
  
  if (!isset($_SESSION["usr_ID"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Caducó la sesión.")); }
  if (!isset($_REQUEST["appSQL"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Ninguna variable en POST")); }
  
  $data = json_decode($_REQUEST['appSQL']);
  $rpta = 0;

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

  switch ($data->TipoQuery) {
    case "selPersona":
      $db->enviarRespuesta($fn->getEditPersona($data->personaID));
      break;
    case "newPersona":
      //comboBox inicial
      $rpta = array(
        "fecha" => $fn->getFechaActualDB(),
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
      
      //respuesta
      $db->enviarRespuesta($rpta);
      break;
    case "insPersona":
      try {
        $sql = cadSQL($data);
        $qry = $db->query_all($sql);
        $rs = ($qry) ? (reset($qry)) : (null); 
        
        //respuesta
        $rpta = array("error"=>false, $data->commandSQL=>1, "tablaPers"=>$fn->getViewPersona($rs["nro"])); 
        $db->enviarRespuesta($rpta);
      } catch(Exception $e) {
        $db->enviarRespuesta($e->getMessage());
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
        $rpta = array("error"=>false, $data->commandSQL=>1, "tablaPers"=>$fn->getViewPersona($rs["nro"])); 
        $db->enviarRespuesta($rpta);
      } catch(Exception $e) {
        $db->enviarRespuesta($e->getMessage());
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

      //respuesta
      $db->enviarRespuesta($rpta);
      break;
    /*case "apiPeru"://consulta el ruc o DNI
      $url = (strlen($data->nroDNI)==8)?("dni/".$data->nroDNI):((strlen($data->nroDNI)==11)?("ruc/".$data->nroDNI):(""));
      $veri = @file_get_contents("https://dniruc.apisperu.com/api/v1/".$url."?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImRhbm55YmVuaXF1ZUBtc24uY29tIn0.ts3qFRsLtLxqnoOMvwYEeOu470tyTUGWQbsuH4ZTC7I")
              or exit(12);

      if($veri==false) { $retu = array("error"=>true,"message"=>$veri); }
      else { $retu = array("error"=>false,"api"=>$veri); }

      //respuesta
      $rpta = $retu;
      $db->enviarRespuesta($rpta);
      break;*/
  }
  $db->close();
?>
