<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/web_config.php');
      include_once('../../../includes/funciones.php');

      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      //****************personas****************
      switch ($data->TipoQuery) {
        case "selPagos":
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
                "importe" => $rs["importe"],
                "vencimiento" => $rs["vencimiento"]
              );
            }
          }
          $rpta = array("pagos"=>$tabla);
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
          echo json_encode($rpta);
          break;
        case "insPago":
          //agregando a la tabla
          $sql = "insert into app_colprod values (:colegioID,:productoID,:obliga,:importe,:vencimiento,:estado,:sysIP,:userID,now())";
          $params = [
            ":productoID"=>$data->productoID,
            ":colegioID"=>$web->colegioID,
            ":obliga"=>$data->obliga,
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
          echo json_encode($rpta);
          break;
        case "delPagos":
          for($i=0; $i<count($data->arr); $i++){
            $sql = "update bn_productos set estado=0,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:id";
            $params = [
              ":id"=>$data->arr[$i],
              ":sysIP"=>$fn->getClientIP(),
              ":userID"=>$_SESSION['usr_ID']
            ];
            $qry = $db->query_all($sql,$params);
            $rs = ($qry) ? (reset($qry)) : (null);
          }

          //respuesta
          $rpta = array("error" => false,"borrados" => count($data->arr));
          echo json_encode($rpta);
          break;
        case "startPago":
          //respuesta
          $rpta = array(
            "comboTipoProd" => $fn->getComboBox("select id,nombre from app_productos where estado=1 and id not in(select id_producto from app_colprod);")
          );
          echo json_encode($rpta);
          break;
      }
      $db->close();
    } else{
      $resp = array("error"=>true,"mensaje"=>"ninguna variable en POST");
      echo json_encode($resp);
    }
  } else {
    $resp = array("error"=>true,"mensaje"=>"Caducó la sesion.");
    echo json_encode($resp);
  }
?>
