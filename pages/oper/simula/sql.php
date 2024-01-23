<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');
      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        //****************simulacion****************
        case "simulaCredito":
          //obtenemos la simulacion
          $pivot = ($data->TipoCredito=="1")?($data->pricuota):($data->frecuencia);
          $tabla = $fn->getSimulacionCredito(
            $data->TipoCredito,
            $data->importe,
            $data->TEA,
            $data->segDesgr,
            $data->nroCuotas,
            $data->fecha,
            $pivot
          );

          //tasas
          $qry = $db->query_all("select fn_get_tem(".$data->TEA.") as tem,fn_get_ted(".$data->TEA.") as  ted;");
          $rs = reset($qry);
          $TEM = $rs["tem"];
          $TED = $rs["ted"];
          //respuesta
          $rpta = array("tabla"=>$tabla, "tea"=>$data->TEA, "tem"=>$TEM, "ted"=>$TED);
          echo json_encode($rpta);
          break;
        case "simulaAhorro":
          $sql = "select dbo.fn_GetAhorrosTotalInteresImporte('".$data->fechaIni."','".$data->fechaFin."',".$data->importe.",".$data->tasa.") as interes";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
              $rs = $db->fetch_array($qry);
              $tabla = array(
                "interes" => $rs["interes"]
              );
          }
          echo json_encode($tabla);
          break;
        case "selProductos":
          $tabla = array();

          $qry = $db->query("select * from bn_productos where id_padre=2 and id_tipo_prod=201 order by nombre;");
          if ($db->has_rows($qry)) {
            for($xx=0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => $rs["ID"],
                "nombre"=> ($rs["nombre"])
              );
            }
          }

          //respuesta
          $rpta = $tabla;
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
