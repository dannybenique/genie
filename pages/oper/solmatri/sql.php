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
      switch ($data->TipoQuery) {
        case "selSolMatri":
          $tabla = array();
          $buscar = strtoupper($data->buscar);
          $whr = " and id_colegio=:colegioID and (alumno LIKE :buscar or nro_dui LIKE :buscar) ";
          $params = [":colegioID"=>$web->colegioID,":buscar"=>'%'.$buscar.'%'];
          $qry = $db->query_all("select count(*) as cuenta from vw_matriculas_state3 where estado=3 ".$whr.";",$params);
          $rsCount = reset($qry);

          $sql = "select * from vw_matriculas_state3 where estado=3 ".$whr." order by alumno limit 25 offset 0;";
          $qry = $db->query_all($sql,$params);
          if ($qry) {
            foreach($qry as $rs){
              $tabla[] = array(
                "ID" => $rs["id"],
                "fecha_solicita" => $rs["fecha_solicita"],
                "nro_dui"=> str_replace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["nro_dui"]),
                "alumno" => str_ireplace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["alumno"]),
                "codigo" => $rs["codigo"],
                "yyyy" => $rs["yyyy"],
                "nivel" => $rs["nivel"],
                "grado" => $rs["grado"],
                "seccion" => $rs["seccion"]
              );
            }
          }
          $rpta = array("tabla"=>$tabla,"cuenta"=>$rsCount["cuenta"]);
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "execSolMatri":
          //inicialmente el estado debe ser 3 en la matricula
          $sql = "select sp_matriculas (:TipoExec,:id,:colegioID,:alumnoID,:seccionID,:userSolicitaID,:userApruebaID,:fechaSolicita,:fechaAprueba,:fechaMatricula,:yyyy,:estado,:sysIP,:userID,:observac) as nro;";
          $params = [
            ":TipoExec"=>$data->TipoExec,
            ":id"=>$data->ID,
            ":colegioID"=>$web->colegioID,
            ":alumnoID"=>$data->alumnoID,
            ":seccionID"=>$data->seccionID,
            ":userSolicitaID"=>$_SESSION['usr_ID'],
            ":userApruebaID"=>null,
            ":fechaSolicita"=>$data->fecha_solicita,
            ":fechaAprueba"=>null,
            ":fechaMatricula"=>null,
            ":yyyy"=>$data->yyyy,
            ":estado"=>3,
            ":sysIP"=>$fn->getClientIP(),
            ":userID"=>$_SESSION['usr_ID'],
            ":observac"=>$data->observac
          ];
          
          $qry = $db->query_all($sql,$params);
          if($qry){
            $rs = reset($qry);
            $rpta = array("error"=>false, "insert"=>$rs["nro"]);
          } else {
            $rpta = array("error"=>true, "insert"=>0);
          }
          
          //respuesta
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "delSolMatri":
          $params = array();
          $sysIP = $fn->getClientIP();
          $userID = $_SESSION['usr_ID'];
          for($i=0; $i<count($data->arr); $i++){
            $sql = "update app_matriculas set estado=0,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:id";
            $params = [
              ":id"=>$data->arr[$i],
              ":sysIP"=>$sysIP,
              ":userID"=>$userID
            ];
            $qry = $db->query_all($sql,$params);
            $rs = ($qry) ? (reset($qry)) : (null);
          }

          //respuesta
          $rpta = array("error"=>false, "delete"=>$data->arr);
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "newSolMatri":
          //obtenemos configuracion del colegio
          $config = json_decode($fn->getConfigColegio($web->colegioID));
          
          //otros datos combobox
          $niveles = $fn->getComboBox("select distinct id_nivel as id,nivel as nombre from vw_colniv order by id_nivel;");
          $nivelID = $niveles[0]["ID"];
          $grados = $fn->getComboBox("select distinct id_grado as id,grado as nombre from vw_colniv where id_nivel=".$nivelID." order by id_grado;");
          $gradoID = $grados[0]["ID"];
          $secciones = $fn->getComboBox("select distinct id_seccion as id,concat(seccion,case when trim(alias)='' then '' else concat(' - ',trim(alias)) end,' - ',capacidad) as nombre from vw_colniv where id_grado=".$gradoID." order by nombre;");
          
          //respuesta
          $rpta = array(
            "comboNiveles" => $niveles,
            "comboGrados" => $grados,
            "comboSecciones" => $secciones,
            "yyyy" => $config->YearCurrentMatricula,
            "fecha" => $fn->getFechaActualDB(),
            "rolUser" => $_SESSION['usr_data']['rolID'],
            "rolROOT" => 101
          );
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "viewSolMatri":
          $tabla = 0;
          $alumnoID = 0;
          $qry = $db->query_all("select m.*,n.id_grado,n.id_nivel from app_matriculas m join vw_colniv n on m.id_seccion=n.id_seccion where m.id=".$data->matriculaID);
          if ($qry) {
            $rs = reset($qry);
            $alumnoID = $rs["id_alumno"];
            $tabla = array(
              "ID" => $rs["id"],
              "codigo" => $rs["codigo"],
              "yyyy" => $rs["yyyy"],
              "alumnoID" => $alumnoID,
              "colegioID" => $rs["id_colegio"],
              "nivelID" => $rs["id_nivel"],
              "gradoID" => $rs["id_grado"],
              "seccionID" => $rs["id_seccion"],
              "fecha_solicita" => $rs["fecha_solicita"],
              "observac" => $rs["observac"],
              "estado" => $rs["estado"],
              "rolUser" => $_SESSION['usr_data']['rolID'],
              "rolROOT" => 101,
              "persona" => "",
              "comboNiveles" => ($fn->getComboBox("select distinct id_nivel as id,nivel as nombre from vw_colniv order by id_nivel;")),
              "comboGrados" => ($fn->getComboBox("select distinct id_grado as id,grado as nombre from vw_colniv where id_nivel=".$rs["id_nivel"]." order by id_grado;")),
              "comboSecciones" => ($fn->getComboBox("select distinct id_seccion as id,concat(seccion,case when trim(alias)='' then '' else concat(' - ',trim(alias)) end) as nombre from vw_colniv where id_grado=".$rs["id_grado"]." order by nombre;"))
            );
          }
          
          //respuesta
          $rpta = array('tablaSolMatri'=> $tabla,'tablaPers'=>$fn->getViewPersona($alumnoID));
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "viewApruebaSolMatri":
          $qry = $db->query_all("select * from vw_matriculas_state3 where id=:id",[":id"=>$data->matriculaID]);
          if ($qry) {
            $rs = reset($qry);
            $tabla = array(
              "ID" => $rs["id"],
              "codigo" => $rs["codigo"],
              "yyyy" => $rs["yyyy"],
              "alumno" => $rs["alumno"],
              "nro_dui" => $rs["nro_dui"],
              "fecha_solicita" => $rs["fecha_solicita"],
              "nivel" => $rs["nivel"],
              "grado" => $rs["grado"],
              "seccion" => $rs["seccion"],
              "observac" => $rs["observac"],
              "estado" => $rs["estado"],
              "rolUser" => $_SESSION['usr_data']['rolID'],
              "rolROOT" => 101
            );
          }

          //respuesta
          $rpta = $tabla;
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "aprobarSolMatri":
          //inicialmente el estado debe ser 3 en bn_saldos
          $sql = "select sp_matriculas (:TipoExec,:id,:colegioID,:alumnoID,:seccionID,:userSolicitaID,:userApruebaID,:fechaSolicita,:fechaAprueba,:fechaMatricula,:yyyy,:estado,:sysIP,:userID,:observac) as nro;";
          $params = [
            ":TipoExec"=>$data->TipoExec,
            ":id"=>$data->ID,
            ":colegioID"=>null,
            ":alumnoID"=>null,
            ":seccionID"=>null,
            ":userSolicitaID"=>null,
            ":userApruebaID"=>$_SESSION['usr_ID'],
            ":fechaSolicita"=>null,
            ":fechaAprueba"=>$data->fecha_aprueba,
            ":fechaMatricula"=>null,
            ":yyyy"=>null,
            ":estado"=>2,
            ":sysIP"=>$fn->getClientIP(),
            ":userID"=>$_SESSION['usr_ID'],
            ":observac"=>null
          ];
          
          $qry = $db->query_all($sql,$params);
          if($qry){
            $rs = reset($qry);
            $rpta = array("error"=>false, "insert"=>$rs["nro"]);
          } else {
            $rpta = array("error"=>true, "insert"=>0);
          }
          
          //respuesta
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "VerifySolMatri":
          $tablaPers = ""; //almacena los datos de la persona
          $persona = false; //indica que existe en personas
          $activo = false; //indica que encontro en tabla de matriculas
          
          //verificar en Personas
          $sql = "select p.id from personas p join app_alumnos a on p.id=a.id where (a.estado=1) and (a.id_colegio=:colegioID) and (p.nro_dui=:nrodni);";
          $params = [":nrodni"=>$data->nroDNI,":colegioID"=>$web->colegioID];
          $qry = $db->query_all($sql,$params);
          if($qry){
            $rs = reset($qry);
            $tablaPers = $fn->getViewPersona($rs["id"]);
            $persona = true;

            //obtenemos configuracion del colegio
            $config = json_decode($fn->getConfigColegio($web->colegioID));
            //verificar en matriculas
            $sql = "select id_alumno from app_matriculas where yyyy=".$config->YearCurrentMatricula." and id_colegio=".$web->colegioID." and id_alumno=".$rs["id"].";";
            $qryAlumno = $db->query_all($sql);
            $activo = ($qryAlumno) ? true : false;
          }

          //respuesta
          $rpta = array(
            "tablaPers" => $tablaPers,
            "persona"=>$persona,
            "activo"=>$activo,
            "mensajeNOadd" => "");
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "comboNivel":
          switch($data->tipoID){
            case 3: //actualiza grados
              $grados = $fn->getComboBox("select distinct id_grado as id,grado as nombre from vw_colniv where id_nivel=".$data->padreID." order by id;");
              $gradoID = $grados[0]["ID"];
              $secciones = $fn->getComboBox("select distinct id_seccion as id,concat(seccion,case when trim(alias)='' then '' else concat(' - ',trim(alias)) end) as nombre from vw_colniv where id_grado=".$gradoID." order by nombre;");
              $rpta = array( "grados" => $grados, "secciones" => $secciones );
              break;
            case 4: //actualiza secciones
              $secciones = $fn->getComboBox("select distinct id_seccion as id,concat(seccion,case when trim(alias)='' then '' else concat(' - ',trim(alias)) end) as nombre from vw_colniv where id_grado=".$data->padreID." order by nombre;");
              $rpta = array( "secciones" => $secciones );
              break;
          }
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
      }
      $db->close();
    } else {
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
