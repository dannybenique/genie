<?php
  include_once('../../../includes/sess_verifica.php');
  include_once('../../../includes/db_database.php');
  include_once('../../../includes/funciones.php');
  include_once('../../../includes/web_config.php');

  if (!isset($_SESSION["usr_ID"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Caducó la sesión.")); }
  if (!isset($_REQUEST["appSQL"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Ninguna variable en POST")); }
  
  $data = json_decode($_REQUEST['appSQL']);
  $rpta = 0;

  switch ($data->TipoQuery) {
    case "selBilletaje":
      $tabla = array();
      $params = [":coopacID"=>$web->colegioID,":usuarioID"=>$data->usuarioID,":monedaID"=>$data->monedaID];
      $whr = " and bi.id_coopac=:coopacID and bi.id_empleado=:usuarioID and bi.id_moneda=:monedaID";
      $qry = $db->query_all("select count(bi.*) as cuenta from bn_billetaje bi where bi.estado=1 ".$whr.";",$params);
      $rsCount = reset($qry);

      $sql = "select em.nombrecorto as empleado,bn.nombre as agencia,mo.nombre as moneda,bi.* from bn_billetaje bi join app_empleados em on (bi.id_empleado=em.id_empleado) join bn_bancos bn on (bi.id_agencia=bn.id) join sis_tipos mo on(bi.id_moneda=mo.id) where bi.estado=1 ".$whr." order by bi.fecha desc limit 25 offset 0;";
      $qry = $db->query_all($sql,$params);
      if($qry) {
        foreach($qry as $rs){
          $tabla[] = array(
            "ID" => $rs["id"],
            "empleado" => $rs["empleado"],
            "agencia" => $rs["agencia"],
            "fecha" => $rs["fecha"],
            "moneda" => $rs["moneda"],
            "total" => $rs["mx_total"]*1
          );
        }
      }

      //respuesta
      $rpta = array("tabla"=>$tabla,"cuenta"=>$rsCount["cuenta"]);
      $db->enviarRespuesta($rpta);
      break;
    case "viewBilletaje":
      $tabla = 0;
      $qry = $db->query_all("select e.nombrecorto as usuario,s.abrevia as mon_abrevia,b.* from bn_billetaje b join app_empleados e on (b.id_empleado=e.id_empleado) join sis_tipos s on(b.id_moneda=s.id) where b.id=".$data->billID);
      if ($qry) {
        $rs = reset($qry);
        $tabla = array(
          "ID" => $rs["id"],
          "usuarioID" => $rs["id_empleado"],
          "coopacID" => $rs["id_coopac"],
          "agenciaID" => $rs["id_agencia"],
          "monedaID" => $rs["id_moneda"],
          "mon_abrevia" => $rs["mon_abrevia"],
          "usuario" => $rs["usuario"],
          "fecha" => $rs["fecha"],
          "mx_200" => $rs["mx_200"]*1,
          "mx_100" => $rs["mx_100"]*1,
          "mx_50" => $rs["mx_50"]*1,
          "mx_20" => $rs["mx_20"]*1,
          "mx_10" => $rs["mx_10"]*1,
          "mx_5" => $rs["mx_5"]*1,
          "mx_2" => $rs["mx_2"]*1,
          "mx_1" => $rs["mx_1"]*1,
          "mx_05" => $rs["mx_05"]*1,
          "mx_02" => $rs["mx_02"]*1,
          "mx_01" => $rs["mx_01"]*1,
          "mx_total" => $rs["mx_total"]*1,
          "estado" => ($rs["estado"]*1)
        );
      }
      
      //respuesta
      $rpta = array(
        "comboMonedas" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=1"),
        "comboAgencias" => $fn->getComboBox("select id,nombre from bn_bancos where estado=1 and id_padre=".$web->colegioID),
        "fecha" => $fn->getFechaActualDB(),
        "rolUSR" => $_SESSION['usr_data']['rolID'],
        "rolROOT" => 101, //rol de ROOT
        "tabla" => $tabla
      );
      $db->enviarRespuesta($rpta);
      break;
    case "newBilletaje":
      $monAbrevia = $fn->getValorCampo("select abrevia from sis_tipos where id=".$data->monedaID, "abrevia");
      $nombreCorto = $fn->getValorCampo("select nombrecorto from app_empleados where id_empleado=".$data->usuarioID, "nombrecorto");

      //respuesta
      $rpta = array(
        "comboMonedas" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=1"),
        "comboAgencias" => $fn->getComboBox("select id,nombre from bn_bancos where estado=1 and id_padre=".$web->colegioID),
        "fecha" => $fn->getFechaActualDB(),
        "mon_abrevia" => $monAbrevia,
        "usuario" => $nombreCorto
      );
      $db->enviarRespuesta($rpta);
      break;
    case "delBilletaje":
      $params = array();
      for($i=0; $i<count($data->arr); $i++){
        $sql = "update bn_billetaje set estado=0,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:id";
        $params = [
          ":id"=>$data->arr[$i],
          ":sysIP"=>$fn->getClientIP(),
          ":userID"=>$_SESSION['usr_ID']
        ];
        $qry = $db->query_all($sql,$params);
        $rs = reset($qry);
      }

      //respuesta
      $rpta = array("error"=>false, "delete"=>$data->arr);
      $db->enviarRespuesta($rpta);
      break;
    case "insBilletaje":
      $estado = 1;
      $clientIP = $fn->getClientIP();

      /******agregamos bn_billetaje*******/
      $billID = $fn->getValorCampo("select coalesce(max(id)+1,1) as code from bn_billetaje;", "code");
      $sql = "insert into bn_billetaje values(:id,:usuarioID,:coopacID,:agenciaID,:monedaID,:fecha,:mx200,:mx100,:mx50,:mx20,:mx10,:mx5,:mx2,:mx1,:mx05,:mx02,:mx01,:mxtotal,:estado,:sysIP,:userID,now())";
      $params = [
        ":id"=>$billID,
        ":usuarioID"=>$data->usuarioID,
        ":coopacID"=>$web->colegioID,
        ":agenciaID"=>$data->agenciaID,
        ":monedaID"=>$data->monedaID,
        ":fecha"=>$data->fecha,
        ":mx200"=>$data->mx200,
        ":mx100"=>$data->mx100,
        ":mx50"=>$data->mx50,
        ":mx20"=>$data->mx20,
        ":mx10"=>$data->mx10,
        ":mx5"=>$data->mx5,
        ":mx2"=>$data->mx2,
        ":mx1"=>$data->mx1,
        ":mx05"=>$data->mx05,
        ":mx02"=>$data->mx02,
        ":mx01"=>$data->mx01,
        ":mxtotal"=>$data->mxtotal,
        ":estado"=>$estado,
        ":sysIP"=>$clientIP,
        ":userID"=>$_SESSION['usr_ID']
      ];
      $qry = $db->query_all($sql,$params);
      $rs = reset($qry);
      
      //respuesta
      $rpta = array("error"=>false, "billetajeID"=>$billID);
      $db->enviarRespuesta($rpta);
      break;
    case "updBilletaje":
      /******actualizamos bn_billetaje*******/
      $billID = $fn->getValorCampo("select coalesce(max(id)+1,1) as code from bn_billetaje;", "code");
      $sql = "update bn_billetaje set id_agencia=:agenciaID,fecha=:fecha,mx_200=:mx200,mx_100=:mx100,mx_50=:mx50,mx_20=:mx20,mx_10=:mx10,mx_5=:mx5,mx_2=:mx2,mx_1=:mx1,mx_05=:mx05,mx_02=:mx02,mx_01=:mx01,mx_total=:mxtotal,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:id;";
      $params = [
        ":id"=>$data->ID,
        ":agenciaID"=>$data->agenciaID,
        ":fecha"=>$data->fecha,
        ":mx200"=>$data->mx200,
        ":mx100"=>$data->mx100,
        ":mx50"=>$data->mx50,
        ":mx20"=>$data->mx20,
        ":mx10"=>$data->mx10,
        ":mx5"=>$data->mx5,
        ":mx2"=>$data->mx2,
        ":mx1"=>$data->mx1,
        ":mx05"=>$data->mx05,
        ":mx02"=>$data->mx02,
        ":mx01"=>$data->mx01,
        ":mxtotal"=>$data->mxtotal,
        ":sysIP"=>$fn->getClientIP(),
        ":userID"=>$_SESSION['usr_ID']
      ];
      $qry = $db->query_all($sql,$params);
      $rs = reset($qry);
      
      //respuesta
      $rpta = array("error"=>false, "billetajeID"=>$billID);
      $db->enviarRespuesta($rpta);
      break;
    case "StartBilletaje":
      $params = [":coopacID"=>$web->colegioID,":id"=>$_SESSION['usr_ID']];
      $qry = $db->query_all("select id_rol from bn_usuarios where id=:id and id_coopac=:coopacID and estado=1;",$params);
      if ($qry) { //usuario de una coopac
        $rolID = reset($qry)["id_rol"]; 
      } else {//root
        $qrx = $db->query_all("select id_rol from bn_usuarios where estado=1 and id=".$_SESSION['usr_ID']);
        $rolID = reset($qrx)["id_rol"];
      }
      
      //respuesta
      $rpta = array(
        "root" => "101",
        "rolID" => $rolID,
        "userID" => $_SESSION['usr_ID'],
        "comboUsuarios" => $fn->getComboBox("select id_empleado as id,nombrecorto as nombre from app_empleados where estado=1 and id_coopac=".$web->colegioID.(($rolID>102)?(" and id_empleado=".$_SESSION['usr_ID']):(""))),
        "comboAgencias" => $fn->getComboBox("select id,nombre from bn_bancos where estado=1 and id_padre=".$web->colegioID),
        "comboMonedas" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=1")
      );
      $db->enviarRespuesta($rpta);
      break;
  }
  $db->close();
?>
