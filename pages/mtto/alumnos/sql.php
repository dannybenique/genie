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
        $sql = "select a.*,fn_get_persona(pd.tipo_persona::numeric, pd.ap_paterno::text, pd.ap_materno::text, pd.nombres::text) AS pd_nombre,pd.id as pd_id,pd.nro_dui as pd_nrodni,pd.direccion as pd_direccion,fn_get_persona(md.tipo_persona::numeric, md.ap_paterno::text, md.ap_materno::text, md.nombres::text) AS md_nombre,md.id as md_id,md.nro_dui as md_nrodni,md.direccion as md_direccion,fn_get_persona(ap.tipo_persona::numeric, ap.ap_paterno::text, ap.ap_materno::text, ap.nombres::text) AS ap_nombre,ap.id as ap_id,ap.nro_dui as ap_nrodni,ap.direccion as ap_direccion,e.nombrecorto as usermod from app_alumnos a left join app_empleados e on e.id=a.sys_user left join personas pd on a.id_padre=pd.id left join personas md on a.id_madre=md.id left join personas ap on a.id_apoderado=ap.id where a.id=:alumnoID and a.id_colegio=:colegioID";
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
              "estado" => ($rs["estado"]),
              "usermod" => ($rs["usermod"]),
              "sysuser" => ($rs["sys_user"]),
              "sysfecha" => ($rs["sys_fecha"])
            );
        }
        return $tabla; 
      }
      function setPadreID($personaID){
        $db = $GLOBALS["db"]; //base de datos
        $fn = $GLOBALS["fn"]; //funciones
        $web = $GLOBALS["web"]; //web-config
        $rs = null;
        if($personaID!=""){
          $qry = $db->query_all("select id from app_padres where id=".$personaID);
          if(!$qry){
            $params = [
              ":padreID" => $personaID,
              ":colegioID" => $web->colegioID,
              ":estado"=>1,
              ":sysIP"=>$fn->getClientIP(),
              ":userID"=>$_SESSION['usr_ID']
            ];
            $sql = "insert into app_padres values(:padreID,:colegioID,now(),:estado,:sysIP,:userID,now());";
            $ins = $db->query_all($sql,$params);
            $rs = ($ins) ? (reset($ins)) : (null);
          }
        }
        return $rs;
      }
      switch ($data->TipoQuery) {
        case "alumno_sel":
          $tabla = array();
          $buscar = strtoupper($data->buscar);
          $whr = (($data->verTodos==1) ? "" : "and estado=1")." and id_colegio=".$web->colegioID." and (alumno LIKE :buscar or nro_dui LIKE :buscar) ";
          $params = [":buscar"=>'%'.$buscar.'%'];
          $sql = "select count(*) as cuenta from vw_alumnos where 1=1 ".$whr;
          $qryCount = $db->query_all($sql,$params);
          $rsCount = ($qryCount) ? reset($qryCount)["cuenta"] : (0);

          $sql = "select * from vw_alumnos where 1=1 ".$whr." order by alumno limit 25 offset 0;";
          $qry = $db->query_all($sql,$params);
          if($qry) {
            foreach($qry as $rs){
              $tabla[] = array(
                "ID" => $rs["id"],
                "codigo" => $rs["codigo"],
                "fecha" => $rs["fecha"],
                "nro_dui"=> str_replace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["nro_dui"]),
                "alumno" => str_ireplace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["alumno"]),
                "url" => $rs["urlfoto"],
                "estado" => $rs["estado"],
                "direccion" => ($rs["direccion"])
              );
            }
          }

          //respuesta
          $rpta = array("tabla"=>$tabla,"cuenta"=>$rsCount,"rolID" => (int)$_SESSION["usr_data"]["rolID"]);
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "alumno_ins":
          //verificar padres
          $padreID = setPadreID($data->alumnoPadreID);
          $madreID = setPadreID($data->alumnoMadreID);
          $apoderaID = setPadreID($data->alumnoApoderaID);
          
          //ingresar datos del alumno
          $qry = $db->query_all("select right('000000'||cast(coalesce(max(right(codigo,6)::integer)+1,1) as text),6) as code from app_alumnos where id_colegio=".$web->colegioID.";");
          $codigo = ($qry) ? (reset($qry)["code"]) : (null);
          $sql = "insert into app_alumnos values(:alumnoID,:codigo,:padreID,:madreID,:apoderaID,:colegioID,:fecha,:estado,:sysIP,:userID,now());";
          $params = [
            ":alumnoID"=>$data->alumnoID,
            ":codigo"=>$codigo,
            ":padreID"=>($data->alumnoPadreID!="") ? ($data->alumnoPadreID) : (null),
            ":madreID"=>($data->alumnoMadreID!="") ? ($data->alumnoMadreID) : (null),
            ":apoderaID"=>($data->alumnoApoderaID!="") ? ($data->alumnoApoderaID) : (null),
            ":colegioID"=>$web->colegioID,
            ":fecha"=>$data->alumnoFecha,
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
        case "alumno_upd":
          //verificar padres
          $padreID = setPadreID($data->alumnoPadreID);
          $madreID = setPadreID($data->alumnoMadreID);
          $apoderaID = setPadreID($data->alumnoApoderaID);

          //actualizar datos del alumno
          $sql = "update app_alumnos set id_padre=:padreID,id_madre=:madreID,id_apoderado=:apoderaID,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:alumnoID and id_colegio=:colegioID;";
          $params = [
            ":alumnoID"=>$data->alumnoID,
            ":colegioID"=>$web->colegioID,
            ":padreID"=>($data->alumnoPadreID!="") ? ($data->alumnoPadreID) : (null),
            ":madreID"=>($data->alumnoMadreID!="") ? ($data->alumnoMadreID) : (null),
            ":apoderaID"=>($data->alumnoApoderaID!="") ? ($data->alumnoApoderaID) : (null),
            ":sysIP"=>$fn->getClientIP(),
            ":userID"=>$_SESSION['usr_ID']];
          $qry = $db->query_all($sql,$params);
          $rs = ($qry) ? (reset($qry)) : (null);
          
          //respuesta
          $rpta = array("error"=>false, "update"=>1);
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "alumno_del": //borrar alumnos
          $params = array();
          for($i=0; $i<count($data->arr); $i++){
            $sql = "update app_alumnos set estado=0,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:alumnoID";
            $params = [
              ":alumnoID"=>$data->arr[$i],
              ":sysIP"=>$fn->getClientIP(),
              ":userID"=>$_SESSION['usr_ID']
            ];
            $qry = $db->query_all($sql,$params);
            $rs = ($qry) ? (reset($qry)) : (null);
          }
          //respuesta
          $rpta = array("error"=>false, "delete"=>$data->arr);
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "alumno_add": //quitar el soft delete (estado)
          $sql = "update app_alumnos set estado=1,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:alumnoID";
          $params = [
            ":alumnoID"=>$data->alumnoID,
            ":sysIP"=>$fn->getClientIP(),
            ":userID"=>$_SESSION['usr_ID']
          ];
          $qry = $db->query_all($sql,$params);
          $rs = ($qry) ? (reset($qry)) : (null);

          //respuesta
          $rpta = array("error"=>(($rs==null)?true:false));
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "alumno_view":
          switch($data->fullQuery){
            case 0: //datos personales
              $rpta = array(
                'tablaAlumno' => getViewAlumno($data->personaID),
                'tablaPers' => $fc->getViewPersona($data->personaID));
              break;
            case 1: //datos personales + laborales
              $rpta = array(
                'tablaAlumno' => getViewAlumno($data->personaID),
                'tablaPers' => $fn->getViewPersona($data->personaID),
                'tablaLabo' => $fn->getAllLaborales($data->personaID));
              break;
            case 2: //datos personales + laborales + conyuge
              $rpta = array(
                'tablaAlumno' => getViewAlumno($data->personaID),
                'tablaPers' => $fn->getViewPersona($data->personaID),
                'tablaLabo' => $fn->getAllLaborales($data->personaID),
                'tablaCony' => $fn->getViewConyuge($data->personaID));
              break;
          }
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "alumno_start":
          //respuesta
          $rpta = array(
            "fecha" => $fn->getFechaActualDB(),
            "colegio" => $web->colegioID);
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "familiar_view":
          $tipoFami = "";
          switch($data->tipo){
            case 1: $tipoFami = "Padre"; break;
            case 2: $tipoFami = "Madre"; break;
            case 3: $tipoFami = "Apoderado"; break;
          }
          
          //respuesta
          $rpta = array(
            "tipoFami" => $tipoFami,
            "tablaPers" => $fn->getViewPersona($data->familiarID));
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "VerifyAlumno":
          $tablaPers = ""; //almacena los datos de la persona
          $persona = false; //indica que existe en personas
          $activo = false; //indica que encontro en tabla de alumnos
          
          //verificar en Personas
          $params = [":nrodni"=>$data->nroDNI];
          $qry = $db->query_all("select id from personas where (nro_dui=:nrodni);",$params);
          if($qry){
            $rs = reset($qry);
            $tablaPers = $fn->getViewPersona($rs["id"]);
            $persona = true;
            
            //verificar en Alumnos
            $sql = "select id from app_alumnos where id_colegio=:colegioID and id=:alumnoID;";
            $paramAlumno = [":colegioID"=>$web->colegioID,":alumnoID"=>$rs["id"]];
            $qryAlumno = $db->query_all($sql,$paramAlumno);
            $activo = ($qryAlumno) ? true : false;
          }

          //respuesta
          $rpta = array(
            "tablaPers" => $tablaPers,
            "persona" => $persona,
            "activo" => $activo,
            "mensajeNOadd" => "ya es ALUMNO ACTIVO...");
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
            $persona = true;
            
            //verificar en Alumnos
            $sql = "select id_padre from app_alumnos where id_colegio=:colegioID and id_padre=:padreID;";
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
        case "VerifyMadre":
          $tablaPers = ""; //almacena los datos de la persona
          $persona = false; //indica que existe en personas
          $activo = false; //indica que encontro en tabla de alumnos
          
          //verificar en Personas
          $params = [":nrodni"=>$data->nroDNI];
          $qry = $db->query_all("select id from personas where (nro_dui=:nrodni);",$params);
          if($qry){
            $rs = reset($qry);
            $tablaPers = $fn->getViewPersona($rs["id"]);
            $persona = true;
            
            //verificar en Alumnos
            $sql = "select id_madre from app_alumnos where id_colegio=:colegioID and id_madre=:madreID;";
            $paramAlumno = [":colegioID"=>$web->colegioID,":madreID"=>$rs["id"]];
            $qryAlumno = $db->query_all($sql,$paramAlumno);
            $activo = ($qryAlumno) ? true : false;
          }

          //respuesta
          $rpta = array(
            "tablaPers" => $tablaPers,
            "persona" => $persona,
            "activo" => $activo,
            "mensajeNOadd" => "ya es MADRE ACTIVO...");
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "VerifyApodera":
          $tablaPers = ""; //almacena los datos de la persona
          $persona = false; //indica que existe en personas
          $activo = false; //indica que encontro en tabla de alumnos
          
          //verificar en Personas
          $params = [":nrodni"=>$data->nroDNI];
          $qry = $db->query_all("select id from personas where (nro_dui=:nrodni);",$params);
          if($qry){
            $rs = reset($qry);
            $tablaPers = $fn->getViewPersona($rs["id"]);
            $persona = true;
            
            //verificar en Alumnos
            $sql = "select id_apoderado from app_alumnos where id_colegio=:colegioID and id_apoderado=:apoderaID;";
            $paramAlumno = [":colegioID"=>$web->colegioID,":apoderaID"=>$rs["id"]];
            $qryAlumno = $db->query_all($sql,$paramAlumno);
            $activo = ($qryAlumno) ? true : false;
          }

          //respuesta
          $rpta = array(
            "tablaPers" => $tablaPers,
            "persona" => $persona,
            "activo" => $activo,
            "mensajeNOadd" => "ya es APODERADO ACTIVO...");
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
