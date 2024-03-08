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
        case "selProductos":
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
          $rpta = array("productos"=>$tabla);
          echo json_encode($rpta);
          break;
        case "editProducto":
          //cargar datos de la persona
          $qry = $db->query_all("select * from app_productos where id=".$data->productoID);
          if ($qry) {
            $rs = reset($qry);
            $rpta = array(
              "ID" => $rs["id"],
              "codigo" => $rs["codigo"],
              "abrev" => ($rs["abrevia"]),
              "nombre" => ($rs["nombre"])
            );
          }

          //respuesta
          echo json_encode($rpta);
          break;
        case "insProducto":
          //obteniendo nuevo nro orden
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
          echo json_encode($rpta);
          break;
        case "updProducto":
          $sql = "update app_productos set nombre=:nombre,abrevia=:abrevia,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:id";
          $params = [
            ":id"=>$data->ID,
            ":nombre"=>$data->nombre,
            ":abrevia"=>$data->abrevia,
            ":sysIP"=>$fn->getClientIP(),
            ":userID"=>$_SESSION['usr_ID']
          ];
          $qry = $db->query_all($sql,$params);
          $rs = ($qry) ? (reset($qry)) : (null);

          //respuesta
          $rpta = array("error" => false,"actualizados" => 1, "sql" => $sql);
          echo json_encode($rpta);
          break;
        case "delProductos":
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
          echo json_encode($rpta);
          break;
        case "startProducto":
          //respuesta
          $rpta = array(
            "fecha" => $fn->getFechaActualDB(),
            "colegio" => $web->colegioID);
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
