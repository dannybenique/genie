<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/web_config.php');
      include_once('../../../includes/funciones.php');

      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      //****************respuesta JSON****************
      switch ($data->TipoQuery) {
        case "selPagos":
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
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "editPago":
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
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "insPago":
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
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "updPago":
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
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "delPagos":
          for($i=0; $i<count($data->arr); $i++){
            $sql = "delete from app_colprod where id_producto=:id and id_colegio=:colegioID";
            $params = [
              ":id"=>$data->arr[$i],
              ":colegioID"=>$web->colegioID
            ];
            $qry = $db->query_all($sql,$params);
            $rs = ($qry) ? (reset($qry)) : (null);
          }

          //respuesta
          $rpta = array("error" => false,"borrados" => count($data->arr));
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "pagos_cambioMontoBloque"://cambia el importe de los registros en bloque
          //actualizamos todos los registros
          $sql = "update app_colprod set importe=:importe where bloqueo=0 and id_colegio=:colegioID;";
          $params = [
            "importe" => $data->importe,
            ":colegioID"=>$web->colegioID
          ];
          $qry = $db->query_all($sql,$params);
          $rs = ($qry) ? (reset($qry)) : (null);

          //cargamos nuevamente toda la data
          $tabla = array();
          $buscar = strtoupper($data->buscar);
          $sql = "select * from vw_pagos where estado=1 and nombre LIKE :buscar order by nombre;";
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
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "pagos_cambioVencimientoBloque"://cambia el año de vencimiento de los registros seleccionados
          //actualizamos todos los registros
          $sql = "update app_colprod set vencimiento = TO_DATE(:yyyy || '-' || extract(month from vencimiento) || '-' || extract(day from vencimiento),'YYYY-MM-DD') where bloqueo=0 and id_colegio=:colegioID;";
          $params = [
            ":yyyy" => $data->yyyy,
            ":colegioID"=>$web->colegioID
          ];
          $qry = $db->query_all($sql,$params);
          $rs = ($qry) ? (reset($qry)) : (null);

          //cargamos nuevamente toda la data
          $tabla = array();
          $buscar = strtoupper($data->buscar);
          $sql = "select * from vw_pagos where estado=1 and nombre LIKE :buscar order by nombre;";
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
          header('Content-Type: application/json');
          echo json_encode($rpta);
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
          $rs = ($qry) ? (reset($qry)) : (null);

          //cargamos nuevamente toda la data
          $tabla = array();
          $buscar = strtoupper($data->buscar);
          $sql = "select * from vw_pagos where estado=1 and nombre LIKE :buscar order by nombre;";
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
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "startPago":
          //respuesta
          $rpta = array(
            "comboTipoProd" => $fn->getComboBox("select id,nombre from app_productos where estado=1 and id not in(select id_producto from app_colprod);")
          );
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
      }
      $db->close();
    } else{
      $resp = array("error"=>true,"mensaje"=>"ninguna variable en POST");
      header('Content-Type: application/json');
      echo json_encode($resp);
    }
  } else {
    $resp = array("error"=>true,"mensaje"=>"Caducó la sesion.");
    header('Content-Type: application/json');
    echo json_encode($resp);
  }
?>
