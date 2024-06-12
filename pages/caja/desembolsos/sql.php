<?php
  include_once('../../../includes/sess_verifica.php');
  include_once('../../../includes/db_database.php');
  include_once('../../../includes/funciones.php');
  include_once('../../../includes/web_config.php');

  if (!isset($_SESSION["usr_ID"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Caducó la sesión.")); }
  if (!isset($_REQUEST["appSQL"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Ninguna variable en POST")); }

  $data = json_decode($_REQUEST['appSQL']);
  $rpta = 0;

  function getPagos($todos){
    $db = $GLOBALS["db"]; //base de datos
    $web = $GLOBALS["web"]; //web-config
    
    $tabla = array();
    $qry = $db->query_all("select c.*,p.orden,p.nombre as producto,p.abrevia,current_date-c.vencimiento as diferencia from app_colprod c join app_productos p on c.id_producto=p.id where id_colegio=:colegioID ".(($todos) ? (""):("and c.obliga=1"))." order by orden",[":colegioID"=>$web->colegioID]);
    if ($qry) {
      foreach($qry as $rs){
        $tabla[] = array(
          "productoID" => $rs["id_producto"],
          "producto" => $rs["producto"],
          "abrevia" => $rs["abrevia"],
          "importe" => $rs["importe"]*1,
          "bloqueo" => $rs["bloqueo"],
          "orden" => $rs["orden"],
          "vencimiento" => $rs["vencimiento"],
          "disabled" => ($rs["diferencia"]>=0) ? (true):(false),
          "checked" => ($rs["diferencia"]>=0) ? (true):(false)
        );
      }
    }
    return $tabla; 
  }
  switch ($data->TipoQuery) {
    case "desemb_Select":
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
      $db->enviarRespuesta($rpta);
      break;
    
    case "desemb_View":
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

      //respuesta
      $rpta = array(
        'tablaDesembolso'=> $matricula,
        'tablaPagos'=> getPagos($todos = false),
        'tablaPers'=>$fn->getViewPersona($alumnoID)
      );
      $db->enviarRespuesta($rpta);
      break;
    case "desemb_Delete":
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
      $db->enviarRespuesta($rpta);
      break;
    case "desemb_Execute":
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
        if($pago->checked) { $qry = $db->query_all($sql,$params); $rs = reset($qry); }
      }

      //matriculas
      $sql = "update app_matriculas set fecha_matricula=:fecha,importe=:importe,saldo=:saldo,nro_cuotas=:nrocuotas,observac=:observac,estado=:estado,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:id";
      $params = [
        ":id" => $matriculaID,
        ":fecha" => $data->fecha,
        ":importe" => $data->importe,
        ":saldo" => $data->saldo,
        ":nrocuotas" => count($data->pagos),
        ":observac" => $data->observac,
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
      $db->enviarRespuesta($rpta);
      break;
    case "desemb_AddPago":
      //respuesta
      $rpta = array('tablaPagos'=> getPagos($todos = true));
      $db->enviarRespuesta($rpta);
      break;
  }
  $db->close();
?>
