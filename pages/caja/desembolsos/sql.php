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
                "yyyy" => $rs["yyyy"],
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
              "yyyy" => $rs["yyyy"],
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
          $qry = $db->query_all("select c.*,p.nombre as producto,p.abrevia,current_date-c.vencimiento as diferencia from app_colprod c join app_productos p on c.id_producto=p.id where c.obliga=1 and id_colegio=:colegioID order by abrevia",[":colegioID"=>$web->colegioID]);
          if ($qry) {
            foreach($qry as $rs){
              $pagos[] = array(
                "productoID" => $rs["id_producto"],
                "producto" => $rs["producto"],
                "abrevia" => $rs["abrevia"],
                "importe" => $rs["importe"]*1,
                "vencimiento" => $rs["vencimiento"],
                "disabled" => ($rs["diferencia"]>=0) ? (true):(false),
                "checked" => ($rs["diferencia"]>=0) ? (true):(false)
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
            $qry = $db->query_all("update app_matriculas set estado=3,id_useraprueba=null,fecha_aprueba=null,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:id",$params);
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
          
          //pagos
          foreach($data->pagos as $index=>$pago){
            $sql = "insert into app_matriculas_det (id_matricula,item,id_producto,importe,saldo,vencimiento,estado,sys_ip,sys_user,sys_fecha) values(:matriculaID,:item,:productoID,:importe,:saldo,:vencimiento,:estado,:sysIP,:userID,now());";
            $params = [
              ":matriculaID" => $matriculaID,
              ":productoID"  => $pago->productoID,
              ":item"  => $index+1,
              ":importe"  => $pago->importe,
              ":saldo"  => ($pago->checked==1) ? (0):($pago->importe),
              ":vencimiento" => $pago->vencimiento,
              ":estado" => 1,
              ":sysIP"  => $clientIP,
              ":userID" => $userID
            ];
            $qry = $db->query_all($sql,$params);
            $rs = reset($qry);
          }
          
          //movim
          $qry = $db->query_all("select concat(".$userID.",'-',right('0000000'||cast(coalesce(max(right(codigo,7)::integer)+1,1) as text),7)) as code from app_movim where id_cajera=".$userID.";");
          $voucher = ($qry) ? (reset($qry)["code"]) : (null);
          $sql = "insert into app_movim(id_colegio,id_matricula,id_tipo_oper,id_tipo_pago,id_tipo_mov,id_cajera,fecha,codigo,total,estado,sys_ip,sys_user,sys_fecha,observac) values(:colegioID,:matriculaID,:tipooperID,:tipopagoID,:tipomovID,:cajeraID,now(),:voucher,:total,:estado,:sysIP,:userID,now(),:observac) returning id;";
          $params = [
            ":colegioID"   => $colegioID,
            ":matriculaID" => $matriculaID,
            ":tipooperID"  => 124, //entrega de credito
            ":tipopagoID"  => 164, //en efectivo
            ":tipomovID"  => 9, //entrega de credito
            ":cajeraID"  => $userID,
            ":voucher"  => $voucher, //codigo del movimiento = voucher
            ":total"  => $data->total,
            ":estado" => 1,
            ":sysIP"  => $clientIP,
            ":userID" => $userID,
            ":observac" => ""
          ];
          $qry = $db->query_all($sql,$params);
          $rs = reset($qry);
          $movimID = $rs["id"];

          foreach($data->pagos as $indice=>$pago){
            $sql = "insert into app_movim_det(id_movim,item,id_producto,importe) values(:movimID,:item,:productoID,:importe);";
            $params = [
              ":movimID" => $movimID,
              ":item" => $indice+1,
              ":productoID" => $pago->productoID,
              ":importe" => $pago->importe
            ];
            if($pago->obliga==1) { $qry = $db->query_all($sql,$params); $rs = reset($qry); }
          }

          //matriculas
          $sql = "update app_matriculas set fecha_matricula=:fecha,importe=:importe,saldo=:saldo,nro_cuotas=:nrocuotas,estado=:estado,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:id";
          $params = [
            ":id" => $matriculaID,
            ":fecha" => $data->fecha,
            ":importe" => $data->importe,
            ":saldo" => $data->saldo,
            ":nrocuotas" => count($data->pagos),
            ":estado" => 1,
            ":sysIP"=>$clientIP,
            ":userID"=>$userID
          ];
          $qry = $db->query_all($sql,$params);
          if($qry){
            $rs = reset($qry);
            $rpta = array("error"=>false, "insert"=>1, "movimID"=>$movimID);
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
