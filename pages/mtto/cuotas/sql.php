<?php
  include_once('../../../includes/sess_verifica.php');
  include_once('../../../includes/db_database.php');
  include_once('../../../includes/web_config.php');
  include_once('../../../includes/funciones.php');

  if (!isset($_SESSION["usr_ID"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Caducó la sesión.")); }
  if (!isset($_REQUEST["appSQL"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Ninguna variable en POST")); }
  
  $data = json_decode($_REQUEST['appSQL']);
  $rpta = 0;

  switch ($data->TipoQuery) {
    case "pago_sel":
      $tabla = array();
      $buscar = strtoupper($data->buscar);
      $sql = "select * from vw_pagos where estado=1 and nombre LIKE :buscar order by orden;";
      $params = [":buscar"=>'%'.$buscar.'%'];
      $qry = $db->query_all($sql,$params);
      if ($qry) {
        foreach($qry as $rs){
          $tabla[] = array(
            "ID" => $rs["id"],
            "codigo" => $rs["codigo"],
            "pago" => str_ireplace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["nombre"]),
            "abrevia" => $rs["abrevia"],
            "estado" => $rs["estado"],
            "obliga" => $rs["obliga"],
            "bloqueo" => $rs["bloqueo"],
            "importe" => $rs["importe"],
            "vencimiento" => $rs["vencimiento"]
          );
        }
      }
      $rpta = array("pagos"=>$tabla);
      $db->enviarRespuesta($rpta);
      break;
    case "pago_edit":
      //cargar datos de producto por colegio
      $sql = "select p.nombre,p.abrevia,p.codigo,cp.* from app_productos p join app_colprod cp on p.id=cp.id_producto where id_producto=:productoID and id_colegio=:colegioID;";
      $params = [
        ":productoID"=>$data->productoID,
        ":colegioID"=>$web->colegioID
      ];
      $qry = $db->query_all($sql,$params);
      
      if ($qry) {
        $rs = reset($qry);
        $rpta = array(
          "productoID" => $rs["id_producto"],
          "codigo" => $rs["codigo"],
          "abrev" => ($rs["abrevia"]),
          "nombre" => ($rs["nombre"]),
          "obliga" => ($rs["obliga"]),
          "importe" => ($rs["importe"]*1),
          "vencimiento" => ($rs["vencimiento"]),
          "estado" => ($rs["estado"]),
          "comboTipoProd" => $fn->getComboBox("select id,nombre from app_productos where estado=1;"),
        );
      }

      //respuesta
      $db->enviarRespuesta($rpta);
      break;
    case "pago_ins":
      //agregando a la tabla
      $sql = "insert into app_colprod values (:colegioID,:productoID,:obliga,:bloqueo,:importe,:vencimiento,:estado,:sysIP,:userID,now())";
      $params = [
        ":productoID"=>$data->productoID,
        ":colegioID"=>$web->colegioID,
        ":obliga"=>$data->obliga,
        ":bloqueo"=>0,
        ":importe"=>$data->importe,
        ":vencimiento"=>$data->vencimiento,
        ":estado"=>1,
        ":sysIP"=>$fn->getClientIP(),
        ":userID"=>$_SESSION['usr_ID']
      ];
      $qry = $db->query_all($sql,$params);
      $rs = ($qry) ? (reset($qry)) : (null);

      //respuesta
      $rpta = array("error" => false,"ingresados" => 1);
      $db->enviarRespuesta($rpta);
      break;
    case "pago_upd":
      $sql = "update app_colprod set obliga=:obliga,importe=:importe,vencimiento=:vencimiento,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id_producto=:productoID and id_colegio=:colegioID";
      $params = [
        ":productoID"=>$data->productoID,
        ":colegioID"=>$web->colegioID,
        ":obliga"=>$data->obliga,
        ":importe"=>$data->importe,
        ":vencimiento"=>$data->vencimiento,
        ":sysIP"=>$fn->getClientIP(),
        ":userID"=>$_SESSION['usr_ID']
      ];
      $qry = $db->query_all($sql,$params);
      $rs = ($qry) ? (reset($qry)) : (null);

      //respuesta
      $rpta = array("error" => false,"actualizados" => 1, "sql" => $sql);
      $db->enviarRespuesta($rpta);
      break;
    case "pago_del":
      foreach($data->pagos as $pago){
        $qry = $db->query_all("delete from app_colprod where id_producto=".$pago." and id_colegio=".$web->colegioID);
        $rs = reset($qry);
      }

      //respuesta
      $rpta = array("error" => false,"borrados" => count($data->arr));
      $db->enviarRespuesta($rpta);
      break;
    case "pagos_bloquear":
      //bloqueamos el registro
      $sql = "update app_colprod set bloqueo=(select case bloqueo when 1 then 0 when 0 then 1 end as ego from app_colprod where id_producto=:productoID),sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id_colegio=:colegioID and id_producto=:productoID;";
      $params = [
        ":productoID"=>$data->productoID,
        ":colegioID"=>$web->colegioID,
        ":sysIP"=>$fn->getClientIP(),
        ":userID"=>$_SESSION['usr_ID']
      ];
      $qry = $db->query_all($sql,$params);
      $rs = reset($qry);

      //cargamos nuevamente toda la data
      $tabla = array();
      $buscar = strtoupper($data->buscar);
      $sql = "select * from vw_pagos where estado=1 and nombre LIKE :buscar order by orden;";
      $params = [":buscar"=>'%'.$buscar.'%'];
      $qry = $db->query_all($sql,$params);
      if ($qry) {
        foreach($qry as $rs){
          $tabla[] = array(
            "ID" => $rs["id"],
            "codigo" => $rs["codigo"],
            "pago" => str_ireplace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["nombre"]),
            "abrevia" => $rs["abrevia"],
            "estado" => $rs["estado"],
            "obliga" => $rs["obliga"],
            "bloqueo" => $rs["bloqueo"],
            "importe" => $rs["importe"],
            "vencimiento" => $rs["vencimiento"]
          );
        }
      }
      //respuesta
      $rpta = array("error" => false,"ingresados" => 1,"pagos"=>$tabla);
      $db->enviarRespuesta($rpta);
      break;
    case "pago_start":
      //respuesta
      $rpta = array( "comboTipoProd" => $fn->getComboBox("select id,nombre from app_productos where estado=1 and id not in(select id_producto from app_colprod);") );
      $db->enviarRespuesta($rpta);
      break;
    case "cambio_MontoEnBloque"://cambia el importe de los registros en bloque
      //actualizamos todos los registros
      $params = [
        "importe" => $data->importe,
        ":colegioID"=>$web->colegioID,
        ":sysIP"=>$fn->getClientIP(),
        ":userID"=>$_SESSION['usr_ID']
      ];

      foreach($data->pagos as $pago){
        $sql = "update app_colprod set importe=:importe,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id_producto=".$pago." and id_colegio=:colegioID;";
        $qry = $db->query_all($sql,$params);
        $rs = reset($qry);
      }

      //cargamos nuevamente toda la data
      $tabla = array();
      $buscar = strtoupper($data->buscar);
      $sql = "select * from vw_pagos where estado=1 and nombre LIKE :buscar order by orden;";
      $params = [":buscar"=>'%'.$buscar.'%'];
      $qry = $db->query_all($sql,$params);
      if ($qry) {
        foreach($qry as $rs){
          $tabla[] = array(
            "ID" => $rs["id"],
            "codigo" => $rs["codigo"],
            "pago" => str_ireplace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["nombre"]),
            "abrevia" => $rs["abrevia"],
            "estado" => $rs["estado"],
            "obliga" => $rs["obliga"],
            "bloqueo" => $rs["bloqueo"],
            "importe" => $rs["importe"],
            "vencimiento" => $rs["vencimiento"]
          );
        }
      }
      //respuesta
      $rpta = array("error" => false,"pagos"=>$tabla);
      $db->enviarRespuesta($rpta);
      break;
    case "cambio_VcmtoBloque"://cambia el año de vencimiento de los registros seleccionados
      //actualizamos todos los registros
      $params = [
        ":yyyy" => $data->yyyy,
        ":colegioID"=>$web->colegioID,
        ":sysIP"=>$fn->getClientIP(),
        ":userID"=>$_SESSION['usr_ID']
      ];
      foreach($data->pagos as $pago){
        $sql = "update app_colprod set vencimiento = TO_DATE(:yyyy || '-' || extract(month from vencimiento) || '-' || extract(day from vencimiento),'YYYY-MM-DD'),sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id_producto=".$pago." and id_colegio=:colegioID;";
        $qry = $db->query_all($sql,$params);
        $rs = reset($qry);
      }

      //cargamos nuevamente toda la data
      $tabla = array();
      $buscar = strtoupper($data->buscar);
      $sql = "select * from vw_pagos where estado=1 and nombre LIKE :buscar order by orden;";
      $params = [":buscar"=>'%'.$buscar.'%'];
      $qry = $db->query_all($sql,$params);
      if($qry) {
        foreach($qry as $rs){
          $tabla[] = array(
            "ID" => $rs["id"],
            "codigo" => $rs["codigo"],
            "pago" => str_ireplace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["nombre"]),
            "abrevia" => $rs["abrevia"],
            "estado" => $rs["estado"],
            "obliga" => $rs["obliga"],
            "bloqueo" => $rs["bloqueo"],
            "importe" => $rs["importe"],
            "vencimiento" => $rs["vencimiento"]
          );
        }
      }
      //respuesta
      $rpta = array("error" => false,"pagos"=>$tabla);
      $db->enviarRespuesta($rpta);
      break;
  }
  $db->close();
?>
