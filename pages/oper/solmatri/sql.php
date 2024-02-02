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
      function getViewSocio($personaID){
        $db = $GLOBALS["db"]; //base de datos
        $fn = $GLOBALS["fn"]; //funciones
        $web = $GLOBALS["web"]; //web-config
        
        //obtener datos personales
        $sql = "select s.*,b.nombre as agencia from bn_socios s,bn_bancos b where s.estado=1 and s.id_agencia=b.id and s.id_socio=:socioID and s.id_coopac=:coopacID";
        $params = [":socioID"=>$personaID,"coopacID"=>$web->coopacID];
        $qry = $db->query_all($sql,$params);
        
        if ($qry) {
            $rs = reset($qry);
            $tabla = array(
              "ID" => ($rs["id_socio"]),
              "coopacID" => ($rs["id_coopac"]),
              "agenciaID" => ($rs["id_agencia"]),
              "comboAgencias" => ($fn->getComboBox("select id,nombre from bn_bancos where estado=1 and id_padre=".$web->coopacID." order by codigo;")),
              "agencia" => $rs["agencia"],
              "fecha" => $rs["fecha"],
              "codigo" => $rs["codigo"],
              "observac" => ($rs["observac"]),
              "sysuser" => ($rs["sys_user"]),
              "sysfecha" => ($rs["sys_fecha"])
            );
        }
        return $tabla; 
      }
      switch ($data->TipoQuery) {
        case "selSolMatri":
          $tabla = array();
          $buscar = strtoupper($data->buscar);
          $whr = " and id_colegio=:colegioID and (alumno LIKE :buscar or nro_dui LIKE :buscar) ";
          $params = [":colegioID"=>$web->colegioID,":buscar"=>'%'.$buscar.'%'];
          $qry = $db->query_all("select count(*) as cuenta from vw_matriculas where estado=3 ".$whr.";",$params);
          $rsCount = reset($qry);

          $sql = "select * from vw_matriculas where estado=3 ".$whr." order by alumno limit 25 offset 0;";
          $qry = $db->query_all($sql,$params);
          if ($qry) {
            foreach($qry as $rs){
              $tabla[] = array(
                "ID" => $rs["id"],
                "fecha_solmatri" => $rs["fecha_solicita"],
                "nro_dui"=> str_replace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["nro_dui"]),
                "alumno" => str_ireplace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["alumno"]),
                "codigo" => $rs["codigo"],
                "nivel" => $rs["nivel"],
                "grado" => $rs["grado"],
                "seccion" => $rs["seccion"]
              );
            }
          }
          $rpta = array("tabla"=>$tabla,"cuenta"=>$rsCount["cuenta"]);
          echo json_encode($rpta);
          break;
        case "execSolMatri":
          //inicialmente el estado debe ser 3 en la matricula
          $sql = "select sp_matriculas (:TipoExec,:id,:colegioID,:alumnoID,:seccionID,:userSolicitaID,:userApruebaID,:fechaSolicita,:fechaAprueba,:fechaMatricula,:estado,:sysIP,:userID,:observac) as nro;";
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
          echo json_encode($rpta);
          break;
        case "newSolMatri":
          //respuesta
          $rpta = array(
            "comboNiveles" => ($fn->getComboBox("select id,nombre from app_niveles where id_padre is null order by nombre;")),
            "comboGrados" => ($fn->getComboBox("select id,nombre from app_niveles where id_padre=2 order by abrevia;")),
            "comboSecciones" => ($fn->getComboBox("select id,nombre from app_niveles where id_padre=6 order by abrevia;")),
            "fecha" => $fn->getFechaActualDB(),
            "rolUser" => $_SESSION['usr_data']['rolID'],
            "rolROOT" => 101
          );
          echo json_encode($rpta);
          break;
        case "viewSolMatri":
          $tabla = 0;
          $alumnoID = 0;
          $qry = $db->query_all("select m.*,n.id_grado,n.id_nivel from app_matriculas m join vw_niveles n on m.id_seccion=n.id_seccion where m.id=".$data->matriculaID);
          if ($qry) {
            $rs = reset($qry);
            $alumnoID = $rs["id_alumno"];
            $tabla = array(
              "ID" => $rs["id"],
              "codigo" => $rs["codigo"],
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
              "comboNiveles" => ($fn->getComboBox("select id,nombre from app_niveles where id_padre is null order by nombre;")),
              "comboGrados" => ($fn->getComboBox("select id,nombre from app_niveles where id_padre=".$rs["id_nivel"]." order by abrevia;")),
              "comboSecciones" => ($fn->getComboBox("select id,nombre from app_niveles where id_padre=".$rs["id_grado"]." order by abrevia;"))
            );
          }
          
          //respuesta
          $rpta = array('tablaSolMatri'=> $tabla,'tablaPers'=>$fn->getViewPersona($alumnoID));
          echo json_encode($rpta);
          break;
        case "viewApruebaSoliCred":
          $qry = $db->query_all("select * from vw_prestamos_ext where id=:id",[":id"=>$data->SoliCredID]);
          if ($qry) {
            $rs = reset($qry);
            $date = new DateTime($rs["fecha_pricuota"]);
            $pivot = ($rs["id_tipocred"]==1)?($date->format('Ymd')):($rs["frecuencia"]);
            $cuota = $fn->getSimulacionCredito($rs["id_tipocred"],$rs["importe"],$rs["tasa_cred"],$rs["tasa_desgr"],$rs["nro_cuotas"],$rs["fecha_otorga"],$pivot);
            $tabla = array(
              "ID" => $rs["id"],
              "codigo" => $rs["codigo"],
              "socio" => $rs["socio"],
              "coopacID" => $rs["id_coopac"],
              "agencia" => $rs["agencia"],
              "promotor" => $rs["promotor"],
              "analista" => $rs["analista"],
              "producto" => $rs["producto"],
              "tiposbs" => $rs["tiposbs"],
              "destsbs" => $rs["destsbs"],
              "clasifica" => $rs["clasifica"],
              "condicion" => $rs["condicion"],
              "moneda" => $rs["moneda"],
              "mon_abrevia" => $rs["mon_abrevia"],
              "importe" => $rs["importe"],
              "saldo" => $rs["saldo"],
              "tasa" => $rs["tasa_cred"],
              "mora" => $rs["tasa_mora"],
              "desgr" => $rs["tasa_desgr"],
              "nrocuotas" => $rs["nro_cuotas"],
              "fecha_solicred" => $rs["fecha_solicred"],
              "fecha_otorga" => $rs["fecha_otorga"],
              "fecha_pricuota" => $rs["fecha_pricuota"],
              "tipocred" => $rs["tipocred"],
              "tipocredID" => $rs["id_tipocred"],
              "fecha_solicred" => $rs["fecha_solicred"],
              "fecha_aprueba" => $fn->getFechaActualDB(),
              "frecuencia" => $rs["frecuencia"],
              "cuota" => $cuota[1]["cuota"],
              "observac" => $rs["observac"],
              "estado" => ($rs["estado"]*1),
              "rolUser" => $_SESSION['usr_data']['rolID'],
              "rolROOT" => 101
            );
          }

          //respuesta
          $rpta = $tabla;
          echo json_encode($rpta);
          break;
        case "aprobarSoliCred":
          //inicialmente el estado debe ser 3 en bn_saldos
          $sql = "select sp_prestamos (:TipoExec,:id,:socioID,:coopacID,:agenciaID,:promotorID,:analistaID,:apruebaID,:productoID,:tiposbsID,:destsbsID,:clasificaID,:condicionID,:monedaID,:importe,:saldo,:tasa,:mora,:desgr,:nrocuotas,:fechaSoli,:fechaApru,:fechaOtor,:fechaPriC,:tipocredID,:frecuencia,:estado,:sysIP,:userID,:observac) as nro;";
          $params = [
            ":TipoExec"=>$data->TipoExec,
            ":id"=>$data->ID,
            ":socioID"=>null,
            ":coopacID"=>null,
            ":agenciaID"=>null,
            ":promotorID"=>null,
            ":analistaID"=>null,
            ":apruebaID"=>$_SESSION['usr_ID'],
            ":productoID"=>null,
            ":tiposbsID"=>null,
            ":destsbsID"=>null,
            ":clasificaID"=>null,
            ":condicionID"=>null,
            ":monedaID"=>null,
            ":importe"=>null,
            ":saldo"=>null,
            ":tasa"=>null,
            ":mora"=>null,
            ":desgr"=>null,
            ":nrocuotas"=>null,
            ":fechaSoli"=>null,
            ":fechaApru"=>$data->FechaAprueba,
            ":fechaOtor"=>null,
            ":fechaPriC"=>null,
            ":tipocredID"=>null,
            ":frecuencia"=>null,
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
          echo json_encode($rpta);
          break;
        case "VerifySolMatri":
          $tablaPers = ""; //almacena los datos de la persona
          $persona = false; //indica que existe en personas
          $activo = false; //indica que encontro en tabla de prestamos
          
          //verificar en Personas
          $sql = "select p.id from personas p join app_alumnos a on  p.id=a.id where (a.estado=1) and (a.id_colegio=:colegioID) and (p.nro_dui=:nrodni);";
          $params = [":nrodni"=>$data->nroDNI,":colegioID"=>$web->colegioID];
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
            "persona"=>$persona,
            "activo"=>$activo,
            "mensajeNOadd" => "");
          echo json_encode($rpta);
          break;
        case "comboNivel":
          switch($data->tipoID){
            case 3: //actualiza grados
              $grados = $fn->getComboBox("select id,nombre from app_niveles where id_padre=".$data->padreID." order by abrevia;");
              $secciones = $fn->getComboBox("select id,nombre from app_niveles where id_padre=".$grados[0]["ID"]." order by abrevia;");
              $rpta = array( "grados" => $grados, "secciones" => $secciones );
              break;
            case 4: //actualiza secciones
              $secciones = $fn->getComboBox("select id,nombre from app_niveles where id_padre=".$data->padreID." order by abrevia;");
              $rpta = array( "secciones" => $secciones );
              break;
          }
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
