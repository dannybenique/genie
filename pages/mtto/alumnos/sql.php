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
        $sql = "select a.*,e.nombrecorto as usermod from app_alumnos a left join app_empleados e on e.id=a.sys_user where a.estado=1 and a.id=:alumnoID and a.id_colegio=:colegioID";
        $params = [":alumnoID"=>$personaID,"colegioID"=>$web->colegioID];
        $qry = $db->query_all($sql,$params);
        
        if ($qry) {
            $rs = reset($qry);
            $tabla = array(
              "ID" => ($rs["id"]),
              "colegioID" => ($rs["id_colegio"]),
              "fecha" => $rs["fecha"],
              "codigo" => $rs["codigo"],
              "observac" => ($rs["observac"]),
              "usermod" => ($rs["usermod"]),
              "sysuser" => ($rs["sys_user"]),
              "sysfecha" => ($rs["sys_fecha"])
            );
        }
        return $tabla; 
      }
      switch ($data->TipoQuery) {
        case "selAlumnos":
          $tabla = array();
          $buscar = strtoupper($data->buscar);
          $whr = " and id_colegio=".$web->colegioID." and (alumno LIKE :buscar or nro_dui LIKE :buscar) ";
          $params = [":buscar"=>'%'.$buscar.'%'];
          $sql = "select count(*) as cuenta from vw_alumnos where estado in(0,1) ".$whr;
          $qryCount = $db->query_all($sql,$params);
          $rsCount = ($qryCount) ? reset($qryCount)["cuenta"] : (0);

          $sql = "select * from vw_alumnos where estado in(0,1) ".$whr." order by alumno limit 25 offset 0;";
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
                "direccion" => ($rs["direccion"])
              );
            }
          }

          //respuesta
          $rpta = array("tabla"=>$tabla,"cuenta"=>$rsCount,"rolID" => (int)$_SESSION["usr_data"]["rolID"]);
          echo json_encode($rpta);
          break;
        case "insAlumno":
          //ingresar datos del socio
          $qry = $db->query_all("select codigo from bn_bancos where id=".$data->socAgenciaID.";");
          $codagenc = ($qry) ? (reset($qry)["codigo"]) : (null);
          $qry = $db->query_all("select right('000000'||cast(coalesce(max(right(codigo,6)::integer)+1,1) as text),6) as code from bn_socios where id_agencia=".$data->socAgenciaID.";");
          $codsocio = ($qry) ? (reset($qry)["code"]) : (null);
          $sql = "insert into bn_socios values(:socioID,:colegioID,:agenciaID,null,null,:codsocio,:fecha,:estado,:sysIP,:userID,now(),:observac);";
          $params = [
            ":socioID"=>$data->socioID,
            ":colegioID"=>$web->colegioID,
            ":agenciaID"=>$data->socAgenciaID,
            ":codsocio"=>$codagenc."-".$codsocio,
            ":fecha"=>$data->socFecha,
            ":estado"=>1, 
            ":sysIP"=>$fn->getClientIP(),
            ":userID"=>$_SESSION['usr_ID'], 
            ":observac"=>$data->socObservac
          ];
          $qry = $db->query_all($sql,$params);
          $rs = ($qry) ? (reset($qry)) : (null);

          //verificar e ingresar en SALDOS los productos obligatorios
          $qry = $db->query_all("select * from bn_productos where id_tipo_oper=121 and obliga=1 and id_coopac=".$web->colegioID);
          if ($qry) {
            foreach($qry as $rs){
              $xry = $db->query_all("select coalesce(max(id)+1,1) as code from bn_saldos;");
              $id = reset($xry)["code"];
              $xry = $db->query_all("select concat(to_char(now(),'YYYYMMDD'),'-',right('000000'||cast(coalesce(max(right(cod_prod,4)::integer)+1,1) as text),4)) as code from bn_saldos where left(cod_prod,8)=to_char(now(),'YYYYMMDD') and id_coopac=".$web->colegioID);
              $cod_prod = reset($xry)["code"];
              
              $sql = "insert into bn_saldos values(:id,:colegioID,:socioID,:operID,:productoID,:monedaID,:codprod,:saldo,:estado,:sysIP,:userID,now());";
              $params = [
                ":id"=>$id,
                ":colegioID"=>$web->colegioID,
                ":socioID"=>$data->socioID,
                ":operID"=>$rs["id_tipo_oper"],
                ":productoID"=>$rs["id"],
                ":monedaID"=>111,
                ":codprod"=>$cod_prod,
                ":saldo"=>0,
                ":estado"=>1,
                ":sysIP"=>$fn->getClientIP(),
                ":userID"=>$_SESSION['usr_ID']
              ];
              $xry = $db->query_all($sql,$params);
              $rs = ($xry) ? (reset($xry)) : (null);
            }
          }

          //respuesta
          $rpta = array("error"=>false, "insert"=>1);
          echo json_encode($rpta);
          break;
        case "updAlumno":
          $sql = "update bn_socios set id_agencia=:agenciaID,observac=:observac,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id_socio=:socioID and id_coopac=:colegioID;";
          $params = [
            ":agenciaID"=>$data->socAgenciaID,
            ":observac"=>$data->socObservac,
            ":sysIP"=>$fn->getClientIP(),
            ":userID"=>$_SESSION['usr_ID'],
            "socioID"=>$data->socioID,
            "colegioID"=>$web->colegioID];
          $qry = $db->query_all($sql,$params);
          $rs = ($qry) ? (reset($qry)) : (null);
          
          //respuesta
          $rpta = array("error"=>false, "update"=>1);
          echo json_encode($rpta);
          break;
        case "delAlumnos":
          $params = array();
          for($i=0; $i<count($data->arr); $i++){
            $sql = "update bn_socios set estado=0,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id_socio=:socioID";
            $params = [
              ":socioID"=>$data->arr[$i],
              ":sysIP"=>$fn->getClientIP(),
              "userID"=>$_SESSION['usr_ID']
            ];
            $qry = $db->query_all($sql,$params);
            $rs = ($qry) ? (reset($qry)) : (null);
          }
          //respuesta
          $rpta = array("error"=>false, "delete"=>$data->arr);
          echo json_encode($rpta);
          break;
        case "viewAlumno":
          switch($data->fullQuery){
            case 0: //datos personales
              $rpta = array(
                'tablaSocio'=> getViewSocio($data->personaID),
                'tablaPers'=>$fc->getViewPersona($data->personaID));
              break;
            case 1: //datos personales + laborales
              $rpta = array(
                'tablaSocio'=> getViewSocio($data->personaID),
                'tablaPers'=>$fn->getViewPersona($data->personaID),
                'tablaLabo'=>$fn->getAllLaborales($data->personaID));
              break;
            case 2: //datos personales + laborales + conyuge
              $rpta = array(
                'tablaSocio'=> getViewSocio($data->personaID),
                'tablaPers'=>$fn->getViewPersona($data->personaID),
                'tablaLabo'=>$fn->getAllLaborales($data->personaID),
                'tablaCony'=>$fn->getViewConyuge($data->personaID));
              break;
          }
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
            "mensajeNOadd" => "ya es ALUMNO ACTIVO...");
          echo json_encode($rpta);
          break;
        case "startAlumno":
          //obtener fecha actual de operacion
          $qry = $db->query_all("select cast(now() as date) as fecha");
          if($qry){ $rs = reset($qry); }
          $fechaHoy = $rs["fecha"];
          
          //respuesta
          $rpta = array(
            "fecha" => $fn->getFechaActualDB(),
            "colegio" => $web->colegioID);
          echo json_encode($rpta);
          break;
      }
      $db->close();
    } else{
      $resp = array("error"=>true,"data"=>$tabla,"mensaje"=>"ninguna variable en POST");
      echo json_encode($resp);
    }
  } else {
    $resp = array("error"=>true,"mensaje"=>"Caducó la sesion.");
    echo json_encode($resp);
  }
?>
