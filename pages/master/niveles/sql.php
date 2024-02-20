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
        case "selNiveles":
          switch ($data->tipo){
            case "ALL":
              $niveles = array();
              $sql = "select * from vw_niveles where id_nivel=".$data->nivelID." order by id_nivel,id_grado,seccion;";
              $qry = $db->query_all($sql);
              if ($qry) {
                foreach($qry as $rs){
                  $niveles[] = array(
                    "nivelID" => $rs["id_nivel"],
                    "nivel" => $rs["nivel"],
                    "gradoID" => $rs["id_grado"],
                    "grado" => $rs["grado"],
                    "seccionID" => $rs["id_seccion"],
                    "seccion" => $rs["seccion"]
                  );
                }
              }
              
              $colniv = array();
              $sql = "select n.*,cn.alias,cn.capacidad from vw_niveles n join app_colniv cn on (cn.id_nivel=n.id_seccion) where id_colegio=:colegioID and n.id_nivel=:nivelID order by id_nivel,id_grado,seccion;";
              $params = [
                ":colegioID" => $web->colegioID,
                ":nivelID" => $data->nivelID
              ];
              $qry = $db->query_all($sql,$params);
              if ($qry) {
                foreach($qry as $rs){
                  $colniv[] = array(
                    "nivelID" => $rs["id_nivel"],
                    "nivel" => $rs["nivel"],
                    "gradoID" => $rs["id_grado"],
                    "grado" => $rs["grado"],
                    "seccionID" => $rs["id_seccion"],
                    "seccion" => $rs["seccion"],
                    "alias" => $rs["alias"],
                    "capacidad" => $rs["capacidad"]
                  );
                }
              }

              $rpta = array("niveles"=>$niveles,"colniv"=>$colniv);
              break;
          }
          echo json_encode($rpta);
          break;
        case "editNivel":
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
        case "insNivel":
          //obteniendo nuevo ID
          $qry = $db->query_all("select COALESCE(max(id)+1,1) as maxi from app_productos;");
          $id = reset($qry)["maxi"];

          //agregando a la tabla
          $sql = "insert into app_productos values (:id,:codigo,:nombre,:abrevia,:estado,:sysIP,:userID,now())";
          $params = [
            ":id"=>$id,
            ":codigo"=>$data->codigo,
            ":nombre"=>$data->nombre,
            ":abrevia"=>$data->abrevia,
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
        case "updNivel":
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
        case "delNiveles":
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
        case "startNivel":
          //respuesta
          $rpta = array(
            "comboNiveles" => $fn->getComboBox("select id,nombre from app_niveles where id_padre is null order by id;"),
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
