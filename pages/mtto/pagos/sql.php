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
                "fecha" => $rs["fecha"]
              );
            }
          }
          $rpta = array("pagos"=>$tabla);
          echo json_encode($rpta);
          break;
        case "editPago":
          //cargar datos de la persona
          $qry = $db->query_all("select * from bn_productos where id=".$data->productoID);
          if ($qry) {
            $rs = reset($qry);
            $rpta = array(
              "ID" => $rs["id"],
              "codigo" => $rs["codigo"],
              "abrev" => ($rs["abrevia"]),
              "nombre" => ($rs["nombre"]),
              "id_padre" => ($rs["id_padre"]),
              "obliga" => ($rs["obliga"]*1),
              "comboTipoProd" => $fn->getComboBox("select id,nombre from bn_productos where id_padre is null;"),
            );
          }

          //respuesta
          echo json_encode($rpta);
          break;
        case "insPago":
          //obteniendo nuevo ID y otros
          $qry = $db->query_all("select COALESCE(max(id)+1,1) as maxi from bn_productos;");
          $id = reset($qry)["maxi"];
          $qry = $db->query_all("select right('0000'||cast(max(codigo::integer+1) as text),4) as code from bn_productos where id_coopac=".$web->coopacID);
          $codigo = reset($qry)["code"];
          $qry = $db->query_all("select id_tipo_oper from bn_productos where id=".$data->tipoID);
          $id_tipo_oper = reset($qry)["id_tipo_oper"];

          //agregando a la tabla
          $sql = "insert into bn_productos values (:id,:codigo,:nombre,:abrevia,:operID,:coopacID,:tipoID,:obliga,:estado,:sysIP,:userID,now())";
          $params = [
            ":id"=>$id,
            ":codigo"=>$codigo,
            ":nombre"=>$data->nombre,
            ":abrevia"=>$data->abrevia,
            ":operID"=>$id_tipo_oper,
            ":coopacID"=>$web->coopacID,
            ":tipoID"=>$data->tipoID,
            ":obliga"=>$data->obliga,
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
          $qry = $db->query_all("select id_tipo_oper from bn_productos where id=".$data->tipoID);
          $id_tipo_oper = reset($qry)["id_tipo_oper"];
          $sql = "update bn_productos set nombre=:nombre,abrevia=:abrevia,id_tipo_oper=:operID,id_padre=:tipoID,obliga=:obliga,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:id";
          $params = [
            ":id"=>$data->ID,
            ":nombre"=>$data->nombre,
            ":abrevia"=>$data->abrevia,
            ":operID"=>$id_tipo_oper,
            ":tipoID"=>$data->tipoID,
            ":obliga"=>$data->obliga,
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
            "comboTipoProd" => $fn->getComboBox("select id,nombre from bn_productos where id_padre is null;"),
            "fecha" => $fn->getFechaActualDB(),
            "coopac" => $web->coopacID);
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
