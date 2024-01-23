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
        case "selTipos":
          $tabla = array();
          $buscar = strtoupper($data->buscar);
          $sql = "select s.*,x.nombre as nivel from sis_tipos s join sis_tipos x on (s.id_padre=x.id) where s.id_padre=:tipoID and s.nombre LIKE :buscar order by s.id;";
          $params = [":tipoID"=>$data->tipo,":buscar"=>'%'.$buscar.'%'];
          $qry = $db->query_all($sql,$params);
          if ($qry) {
            foreach($qry as $rs){
              $tabla[] = array(
                "ID" => $rs["id"],
                "nombre" => str_ireplace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $rs["nombre"]),
                "codigo" => $rs["codigo"],
                "abrevia" => $rs["abrevia"],
                "tipo" => $rs["tipo"],
                "padreID" => $rs["id_padre"],
                "nivel" => $rs["nivel"]
              );
            }
          }
          $rpta = array("tipos"=>$tabla,"sql"=>$sql);
          echo json_encode($rpta);
          break;
        case "viewTipo":
          //cabecera
          $qry = $db->query_all("select * from sis_tipos where id=".$data->ID);
          if ($qry) {
            $rs = reset($qry);
            $tipo = array(
              "ID" => $rs["id"],
              "codigo" => $rs["codigo"],
              "nombre" => $rs["nombre"],
              "abrevia" => $rs["abrevia"],
              "tipo" => $rs["tipo"],
              "padreID" => $rs["id_padre"]
            );
          }
          
          //respuesta
          $rpta = array(
            'comboTipos' => $fn->getComboBox("select id,nombre from sis_tipos where id_padre is null order by id;"),
            'tipo'=> $tipo );
          echo json_encode($rpta);
          break;
        case "startTipos":
          //respuesta
          $rpta = array(
            'comboTipos' => $fn->getComboBox("select id,nombre from sis_tipos where id_padre is null order by id;"));
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
