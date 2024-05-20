<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');
      include_once('../../../includes/web_config.php');

      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      //****************personas****************
      function getViewAlumno($personaID){
        $db = $GLOBALS["db"]; //base de datos
        $fn = $GLOBALS["fn"]; //funciones
        $web = $GLOBALS["web"]; //web-config
        
        //obtener datos personales
        $sql = "select a.*,fn_get_persona(pd.tipo_persona::numeric, pd.ap_paterno::text, pd.ap_materno::text, pd.nombres::text) AS pd_nombre,pd.id as pd_id,pd.nro_dui as pd_nrodni,pd.direccion as pd_direccion,fn_get_persona(md.tipo_persona::numeric, md.ap_paterno::text, md.ap_materno::text, md.nombres::text) AS md_nombre,md.id as md_id,md.nro_dui as md_nrodni,md.direccion as md_direccion,fn_get_persona(ap.tipo_persona::numeric, ap.ap_paterno::text, ap.ap_materno::text, ap.nombres::text) AS ap_nombre,ap.id as ap_id,ap.nro_dui as ap_nrodni,ap.direccion as ap_direccion,e.nombrecorto as usermod from app_alumnos a left join app_empleados e on e.id=a.sys_user left join personas pd on a.id_padre=pd.id left join personas md on a.id_madre=md.id left join personas ap on a.id_apoderado=ap.id where a.estado=1 and a.id=:alumnoID and a.id_colegio=:colegioID";
        $params = [":alumnoID"=>$personaID,"colegioID"=>$web->colegioID];
        $qry = $db->query_all($sql,$params);
        
        if ($qry) {
            $rs = reset($qry);
            $tabla = array(
              "ID" => ($rs["id"]),
              "colegioID" => ($rs["id_colegio"]),
              "fecha" => $rs["fecha"],
              "codigo" => $rs["codigo"],
              "pdID" => $rs["pd_id"],
              "pd_nombre" => $rs["pd_nombre"],
              "pd_nrodni" => $rs["pd_nrodni"],
              "pd_direccion" => $rs["pd_direccion"],
              "mdID" => $rs["md_id"],
              "md_nombre" => $rs["md_nombre"],
              "md_nrodni" => $rs["md_nrodni"],
              "md_direccion" => $rs["md_direccion"],
              "apID" => $rs["ap_id"],
              "ap_nombre" => $rs["ap_nombre"],
              "ap_nrodni" => $rs["ap_nrodni"],
              "ap_direccion" => $rs["ap_direccion"],
              "usermod" => ($rs["usermod"]),
              "sysuser" => ($rs["sys_user"]),
              "sysfecha" => ($rs["sys_fecha"])
            );
        }
        return $tabla; 
      }
      switch ($data->TipoQuery) {
        case "selPadres":
          $tabla = array();
          $buscar = strtoupper($data->buscar);
          $whr = "and id_colegio=".$web->colegioID." and (persona LIKE :buscar or nro_dui LIKE :buscar) ";
          $params = [":buscar"=>'%'.$buscar.'%'];
          $sql = "select count(*) as cuenta from vw_padres where estado=1 ".$whr;
          $qryCount = $db->query_all($sql,$params);
          $rsCount = ($qryCount) ? reset($qryCount)["cuenta"] : (0);

          $sql = "select * from vw_padres where estado=1 ".$whr." order by persona limit 25 offset 0;";
          $qry = $db->query_all($sql,$params);
          if($qry) {
            foreach($qry as $rs){
              $tabla[] = array(
                "ID" => $rs["id"],
                "nro_dui"=> str_replace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["nro_dui"]),
                "padre" => str_ireplace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["persona"]),
                "url" => $rs["urlfoto"],
                "direccion" => ($rs["direccion"])
              );
            }
          }

          //respuesta
          $rpta = array("tabla"=>$tabla,"cuenta"=>$rsCount,"rolID" => (int)$_SESSION["usr_data"]["rolID"]);
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "insPadre":
          //ingresar datos del padre
          $sql = "insert into app_padres values(:padreID,:colegioID,now(),:estado,:sysIP,:userID,now());";
          $params = [
            ":padreID"=>$data->padreID,
            ":colegioID"=>$web->colegioID,
            ":estado"=>1,
            ":sysIP"=>$fn->getClientIP(),
            ":userID"=>$_SESSION['usr_ID']
          ];
          $qry = $db->query_all($sql,$params);
          $rs = ($qry) ? (reset($qry)) : (null);

          //respuesta
          $rpta = array("error"=>false, "insert"=>1);
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "updPadre":
          $sql = "update app_padres set sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:padreID and id_colegio=:colegioID;";
          $params = [
            ":padreID"=>$data->padreID,
            ":colegioID"=>$web->colegioID,
            ":sysIP"=>$fn->getClientIP(),
            ":userID"=>$_SESSION['usr_ID']];
          $qry = $db->query_all($sql,$params);
          $rs = ($qry) ? (reset($qry)) : (null);
          
          //respuesta
          $rpta = array("error"=>false, "update"=>1);
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "delPadres":
          $params = array();
          for($i=0; $i<count($data->arr); $i++){
            $sql = "update app_padres set estado=0,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:padreID";
            $params = [
              ":padreID"=>$data->arr[$i],
              ":sysIP"=>$fn->getClientIP(),
              "userID"=>$_SESSION['usr_ID']
            ];
            $qry = $db->query_all($sql,$params);
            $rs = ($qry) ? (reset($qry)) : (null);
          }
          //respuesta
          $rpta = array("error"=>false, "delete"=>$data->arr);
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "viewPadre":
          switch($data->fullQuery){
            case 0: //datos personales
              $rpta = array(
                'tablaPers'=>$fc->getViewPersona($data->personaID));
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
        case "VerifyPadre":
          $tablaPers = ""; //almacena los datos de la persona
          $persona = false; //indica que existe en personas
          $activo = false; //indica que encontro en tabla de alumnos
          
          //verificar en Personas
          $params = [":nrodni"=>$data->nroDNI];
          $qry = $db->query_all("select id from personas where (nro_dui=:nrodni);",$params);
          if($qry){
            $rs = reset($qry);
            $tablaPers = $fn->getViewPersona($rs["id"]);
            $tablaPers["tablaLabo"] = $fn->getAllLaborales($rs["id"]);
            $tablaPers["tablaCony"] = $fn->getViewConyuge($rs["id"]);
            $persona = true;
            
            //verificar en padres
            $sql = "select id from app_padres where id_colegio=:colegioID and id=:padreID;";
            $paramAlumno = [":colegioID"=>$web->colegioID,":padreID"=>$rs["id"]];
            $qryAlumno = $db->query_all($sql,$paramAlumno);
            $activo = ($qryAlumno) ? true : false;
          }

          //respuesta
          $rpta = array(
            "tablaPers" => $tablaPers,
            "persona" => $persona,
            "activo" => $activo,
            "mensajeNOadd" => "ya es PADRE ACTIVO...");
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
