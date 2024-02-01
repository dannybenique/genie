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
                "nivel" => $rs["nivel"],
                "grado" => $rs["grado"],
                "seccion" => $rs["seccion"]
              );
            }
          }
          $rpta = array("tabla"=>$tabla,"cuenta"=>$rsCount["cuenta"]);
          echo json_encode($rpta);
          break;
        case "execSoliCred":
          //inicialmente el estado debe ser 3 en bn_saldos
          $sql = "select sp_prestamos (:TipoExec,:id,:socioID,:coopacID,:agenciaID,:promotorID,:analistaID,:apruebaID,:productoID,:tiposbsID,:destsbsID,:clasificaID,:condicionID,:monedaID,:importe,:saldo,:tasa,:mora,:desgr,:nrocuotas,:fechaSoli,:fechaApru,:fechaOtor,:fechaPriC,:tipocredID,:frecuencia,:estado,:sysIP,:userID,:observac) as nro;";
          $params = [
            ":TipoExec"=>$data->TipoExec,
            ":id"=>$data->ID,
            ":socioID"=>$data->socioID,
            ":coopacID"=>$web->coopacID,
            ":agenciaID"=>$data->agenciaID,
            ":promotorID"=>$data->promotorID,
            ":analistaID"=>$data->analistaID,
            ":apruebaID"=>null,
            ":productoID"=>$data->productoID,
            ":tiposbsID"=>$data->tiposbsID,
            ":destsbsID"=>$data->destsbsID,
            ":clasificaID"=>$data->clasificaID,
            ":condicionID"=>$data->condicionID,
            ":monedaID"=>$data->monedaID,
            ":importe"=>$data->importe,
            ":saldo"=>$data->saldo,
            ":tasa"=>$data->tasa,
            ":mora"=>$data->mora,
            ":desgr"=>$data->desgr,
            ":nrocuotas"=>$data->nrocuotas,
            ":fechaSoli"=>$data->fecha_solicred,
            ":fechaApru"=>null,
            ":fechaOtor"=>$data->fecha_otorga,
            ":fechaPriC"=>$data->fecha_pricuota,
            ":tipocredID"=>$data->tipocredID,
            ":frecuencia"=>($data->tipocredID==2)?($data->frecuencia):(null),
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
        case "delSoliCred":
          $params = array();
          for($i=0; $i<count($data->arr); $i++){
            $sql = "update bn_saldos set estado=0,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:id";
            $params = [
              ":id"=>$data->arr[$i],
              ":sysIP"=>$fn->getClientIP(),
              ":userID"=>$_SESSION['usr_ID']
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
        case "viewSoliCred":
          $tabla = 0;
          $socioID = 0;
          $qry = $db->query_all("select s.*,p.* from bn_saldos s inner join bn_prestamos p on p.id_saldo=s.id and s.id=".$data->SoliCredID);
          if ($qry) {
            $rs = reset($qry);
            $socioID = $rs["id_socio"];
            $date = new DateTime($rs["fecha_pricuota"]);
            $pivot = ($rs["id_tipocred"]==1)?($date->format('Ymd')):($rs["frecuencia"]);
            $cuota = $fn->getSimulacionCredito($rs["id_tipocred"],$rs["importe"],$rs["tasa_cred"],$rs["tasa_desgr"],$rs["nro_cuotas"],$rs["fecha_otorga"],$pivot);

            $tabla = array(
              "ID" => $rs["id"],
              "codigo" => $rs["codigo"],
              "socioID" => $rs["id_socio"],
              "coopacID" => $rs["id_coopac"],
              "agenciaID" => $rs["id_agencia"],
              "promotorID" => $rs["id_promotor"],
              "analistaID" => $rs["id_analista"],
              "productoID" => $rs["id_producto"],
              "tiposbsID" => $rs["id_tiposbs"],
              "destsbsID" => $rs["id_destsbs"],
              "clasificaID" => $rs["id_clasifica"],
              "condicionID" => $rs["id_condicion"],
              "monedaID" => $rs["id_moneda"],
              "importe" => $rs["importe"],
              "saldo" => $rs["saldo"],
              "tasa" => $rs["tasa_cred"],
              "mora" => $rs["tasa_mora"],
              "desgr" => $rs["tasa_desgr"],
              "nrocuotas" => $rs["nro_cuotas"],
              "fecha_solicred" => $rs["fecha_solicred"],
              "fecha_otorga" => $rs["fecha_otorga"],
              "fecha_pricuota" => $rs["fecha_pricuota"],
              "tipocredID" => $rs["id_tipocred"],
              "frecuencia" => $rs["frecuencia"],
              "cuota" => $cuota[1]["cuota"],
              "observac" => $rs["observac"],
              "estado" => ($rs["estado"]*1),
              "comboAgencias" => $fn->getComboBox("select id,nombre from bn_bancos where estado=1 and id_padre=".$web->coopacID), //agencias
              "comboEmpleados" => $fn->getComboBox("select id_empleado as id,upper(nombrecorto) as nombre from vw_empleados where estado=1 and id_coopac=".$web->coopacID), //empleados
              "comboProductos" => $fn->getComboBox("select id,nombre from bn_productos where estado=1 and id_padre=4 and id_coopac=".$web->coopacID), //productos
              "comboTipoSBS" => $fn->getComboBox("select s.id,s.nombre from sis_tipos s join bn_tipos b on(b.id_tipo=s.id) where s.id_padre=5 and b.id_coopac=".$web->coopacID), //tipos credito SBS
              "comboDestSBS" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=6 order by id;"), //destino credito
              "comboClasifica" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=3 order by id;"), //clasificacion crediticia
              "comboCondicion" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=4 order by id;"), //condicion credito
              "comboMoneda" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=1 order by id;"), //tipos moneda
            );
          }
          
          //respuesta
          $rpta = array('tablaSoliCred'=> $tabla,'tablaPers'=>$fn->getViewPersona($socioID));
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
          $sql = "select p.id from personas p join app_alumnos a on  p.id=a.id where (p.nro_dui=:nrodni) and (a.id_colegio=:colegioID);";
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
