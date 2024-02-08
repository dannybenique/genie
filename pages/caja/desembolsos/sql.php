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
        $sql = "select s.*,b.nombre as agencia from bn_socios s join bn_bancos b on (s.id_agencia=b.id) where s.estado=1 and s.id_socio=:socioID and s.id_coopac=:coopacID";
        $params = [":socioID"=>$personaID,":coopacID"=>$web->coopacID];
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
        case "selDesembolsos":
          $whr = "";
          $tabla = array();
          $buscar = strtoupper($data->buscar);
          $whr = " and id_colegio=:colegioID and (alumno LIKE :buscar or nro_dui LIKE :buscar) ";
          $params = [":colegioID"=>$web->colegioID,":buscar"=>'%'.$buscar.'%'];
          $qry = $db->query_all("select count(*) as cuenta from vw_matriculas_state2 where estado=2 ".$whr.";",$params);
          $rsCount = reset($qry);

          $qry = $db->query_all("select * from vw_matriculas_state2 where estado=2 ".$whr." order by alumno limit 25 offset 0;",$params);
          if ($qry) {
            foreach($qry as $rs){
              $tabla[] = array(
                "ID" => $rs["id"],
                "fecha_solicita" => $rs["fecha_solicita"],
                "fecha_aprueba" => $rs["fecha_aprueba"],
                "nro_dui"=> str_replace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["nro_dui"]),
                "alumno" => str_ireplace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["alumno"]),
                "codigo" => $rs["codigo"],
                "nivel" => $rs["nivel"],
                "grado" => $rs["grado"],
                "seccion" => $rs["seccion"]
              );
            }
          }

          //respuesta
          $rpta = array("tabla"=>$tabla,"cuenta"=>$rsCount["cuenta"]);
          echo json_encode($rpta);
          break;
        
        case "viewDesembolso":
          $matricula = 0;
          $alumnoID = 0;
          $qry = $db->query_all("select * from vw_matriculas_state2 where id=:id",[":id"=>$data->matriculaID]);
          if ($qry) {
            $rs = reset($qry);
            $alumnoID = $rs["id_alumno"];
            $matricula = array(
              "ID" => $rs["id"],
              "codigo" => $rs["codigo"],
              "alumnoID" => $rs["id_alumno"],
              "alumno" => $rs["alumno"],
              "nro_dui" => $rs["nro_dui"],
              "fecha_solicita" => $rs["fecha_solicita"],
              "user_solicita" => $rs["user_solicita"],
              "fecha_aprueba" => $rs["fecha_aprueba"],
              "user_aprueba" => $rs["user_aprueba"],
              "nivel" => $rs["nivel"],
              "grado" => $rs["grado"],
              "seccion" => $rs["seccion"],
              "observac" => $rs["observac"],
              "estado" => $rs["estado"],
              "rolUser" => $_SESSION['usr_data']['rolID'],
              "rolROOT" => 101
            );
          }

          $pagos = array();
          $qry = $db->query_all("select c.*,p.nombre as producto,p.abrevia,extract(days from age(now(),c.vencimiento)) as diferencia from app_colprod c join app_productos p on c.id_producto=p.id where c.obliga=1 and id_colegio=:colegioID order by abrevia",[":colegioID"=>$web->colegioID]);
          if ($qry) {
            foreach($qry as $rs){
              $pagos[] = array(
                "productoID" => $rs["id_producto"],
                "producto" => $rs["producto"],
                "abrevia" => $rs["abrevia"],
                "importe" => $rs["importe"]*1,
                "vencimiento" => $rs["vencimiento"],
                "diferencia" => $rs["diferencia"]*1
              );
            }
          }
          //respuesta
          $rpta = array(
            'tablaDesembolso'=> $matricula,
            'tablaPagos'=> $pagos,
            'tablaPers'=>$fn->getViewPersona($alumnoID)
          );
          echo json_encode($rpta);
          break;
        case "delDesembolsos":
          $userID = $_SESSION['usr_ID'];
          $clientIP = $fn->getClientIP();

          for($i=0; $i<count($data->arr); $i++){
            $id = $data->arr[$i];
            $params = [":id"=>$id,":sysIP"=>$clientIP,":userID"=>$userID];
            $qry = $db->query_all("update bn_saldos set estado=3,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:id",$params);
            $qry = $db->query_all("update bn_prestamos set id_aprueba=null,fecha_aprueba=null where id_saldo=".$id);
            $rs = reset($qry);
          }
          //respuesta
          $rpta = array("error"=>false, "delete"=>$data->arr);
          echo json_encode($rpta);
          break;
        case "ejecutarDesembolso":
          $userID = $_SESSION['usr_ID'];
          $clientIP = $fn->getClientIP();
          $colegioID = $web->colegioID;
          $matriculaID = $data->matriculaID;

          $sql = "insert into app_saldos ('DESEM',:saldoID,:socioID,:coopacID,:agenciaID,:productoID,:monedaID,:tipopagoID,:importe,:tasa,:desgr,:nrocuotas,:fechaOtor,:tipocredID,:pivot,:sysIP,:userID) as movim_id;";
          $params = [
            ":tipopagoID"=>$data->tipopagoID,
            ":importe"=>$data->importe,
            ":tasa"=>$data->tasa_cred,
            ":desgr"=>$data->tasa_desgr,
            ":nrocuotas"=>$data->nrocuotas,
            ":fechaOtor"=>$data->fecha_otorga,
            ":tipocredID"=>$data->tipocredID,
            ":pivot"=>$data->pivot,
            ":sysIP"=>$clientIP,
            ":userID"=>$userID
          ];
          
          $qry = $db->query_all($sql,$params);
          if($qry){
            $rs = reset($qry);
            $rpta = array("error"=>false, "insert"=>1, "movimID"=>$rs["movim_id"]);
          } else {
            $rpta = array("error"=>true, "insert"=>0);
          }
          
          //respuesta
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
