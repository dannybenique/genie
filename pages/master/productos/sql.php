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
    case "producto_sel":
      $tabla = array();
      $buscar = strtoupper($data->buscar);
      $sql = "select * from app_productos where estado=1 and nombre LIKE :buscar order by orden;";
      $params = [":buscar"=>'%'.$buscar.'%'];
      $qry = $db->query_all($sql,$params);
      if ($qry) {
        foreach($qry as $rs){
          $tabla[] = array(
            "ID" => $rs["id"],
            "codigo" => $rs["codigo"],
            "producto" => str_ireplace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["nombre"]),
            "abrevia" => $rs["abrevia"],
            "estado" => $rs["estado"],
            "orden" => $rs["orden"]
          );
        }
      }

      //respuesta
      $rpta = array("productos"=>$tabla);
      $db->enviarRespuesta($rpta);
      break;
    case "producto_edit":
      //cargar datos de la persona
      $qry = $db->query_all("select * from app_productos where id=".$data->productoID);
      if ($qry) {
        $rs = reset($qry);
        $rpta = array(
          "ID" => $rs["id"],
          "codigo" => $rs["codigo"],
          "abrev" => ($rs["abrevia"]),
          "nombre" => ($rs["nombre"]),
          "orden" => ($rs["orden"]),
          "totalregs" => ($fn->getValorCampo("select count(*) as cuenta from app_productos","cuenta"))
        );
      }

      //respuesta
      $db->enviarRespuesta($rpta);
      break;
    case "producto_ins":
      //obteniendo nuevo nro orden siempre al final
      $qry = $db->query_all("select COALESCE(max(orden)+1,1) as maxi from app_productos;");
      $orden = reset($qry)["maxi"];

      //agregando a la tabla
      $sql = "insert into app_productos (codigo,nombre,abrevia,orden,estado,sys_ip,sys_user,sys_fecha) values (:codigo,:nombre,:abrevia,:orden,:estado,:sysIP,:userID,now()) returning id;";
      $params = [
        ":codigo"=>$data->codigo,
        ":nombre"=>$data->nombre,
        ":abrevia"=>$data->abrevia,
        ":orden"=>$orden,
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
    case "producto_upd":
      $params = [
        ":id"=>$data->ID,
        ":nombre"=>$data->nombre,
        ":abrevia"=>$data->abrevia,
        ":sysIP"=>$fn->getClientIP(),
        ":userID"=>$_SESSION['usr_ID']
      ];
      $sql = "update app_productos set nombre=:nombre,abrevia=:abrevia,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:id";
      $qry = $db->query_all($sql,$params);
      $rs = ($qry) ? (reset($qry)) : (null);
      
      if($data->new_orden!=$data->old_orden){
        $fn->setExecSQL("update app_productos set orden = tb_sort.ord from ( select id, ROW_NUMBER() OVER (order by orden) AS ord from app_productos) AS tb_sort where app_productos.id = tb_sort.id;");
        $fn->setExecSQL("update app_productos set orden = -1 where orden=".$data->old_orden);
        if($data->new_orden < $data->old_orden){
          $fn->setExecSQL("update app_productos set orden = orden + 1 where orden between ".($data->new_orden)." and ".($data->old_orden-1));
        }else{
          $fn->setExecSQL("update app_productos set orden = orden - 1 where orden between ".($data->old_orden+1)." and ".($data->new_orden));
        }
        $fn->setExecSQL("update app_productos set orden = ".($data->new_orden)." where orden=-1");
      }

      //respuesta
      $rpta = array("error" => false,"actualizados" => 1, "sql" => $sql);
      $db->enviarRespuesta($rpta);
      break;
    case "producto_del":
      for($i=0; $i<count($data->arr); $i++){
        $sql = "update app_productos set estado=0,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:id";
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
      $db->enviarRespuesta($rpta);
      break;
    case "producto_start":
      //respuesta
      $rpta = array(
        "fecha" => $fn->getFechaActualDB(),
        "colegio" => $web->colegioID);
      $db->enviarRespuesta($rpta);
      break;
  }
  $db->close();
?>
