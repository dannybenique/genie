<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/web_config.php');
      include_once('../../../includes/funciones.php');

      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      function pago_Item($item,$importe){
        $valor = (($item>0)
                  ?(($importe>=$item)?($item):($importe))
                  :(0));
        return round($valor,2);
      }
      function reg_movim_det($tipo_movID,$prestamoID,$movimID,$productoID,$importe){
        $db = $GLOBALS["db"];
        $detalleID = $db->fetch_array($db->query("select coalesce(max(id)+1,1) as code from bn_movim_det;"))["code"];
        $item = $db->fetch_array($db->query("select coalesce(max(item)+1,1) as item from bn_movim_det where id_movim=".$movimID))["item"];
        $sql = "insert into bn_movim_det values($1,$2,$3,$4,$5,$6,$7);";
        $params = array($detalleID,$movimID,$prestamoID,$productoID,$tipo_movID,$item,$importe);
        $rs = $db->fetch_array($db->query_params($sql,$params));
      }
      //****************sql****************
      switch ($data->TipoQuery) {
        case "selCreditos":
          $tabla = array();
          $data->buscar = pg_escape_string($data->buscar);
          $sql = "select * from vw_prestamos_min where estado=1 and saldo>0 and id_coopac=$1 and nro_dui ilike'%".$data->buscar."%'";
          $qry = $db->query_params($sql,array($web->coopacID));
          if ($db->num_rows($qry)>0) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => $rs["id"],
                "nro_DUI" => str_ireplace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $rs["nro_dui"]),
                "socio" => $rs["socio"],
                "codigo" => $rs["codigo"],
                "producto" => $rs["producto"],
                "mon_abrevia" => $rs["mon_abrevia"],
                "tasa" => $rs["tasa"],
                "importe" => $rs["importe"]*1,
                "saldo" => $rs["saldo"]*1
              );
            }
          }
          $rpta = array("prestamos"=>$tabla);
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "viewCredito":
          //cabecera
          $cabecera = 0;
          $qry = $db->query_params("select p.*,extract(days from (now()-d.fecha)) as atraso from vw_prestamos_ext p join bn_prestamos_det d on p.id=d.id_prestamo where p.id=$1 and d.numero=(select min(numero) from bn_prestamos_det where id_prestamo=$1 and numero>0 and capital>pg_capital)",array($data->prestamoID));
          if ($db->num_rows($qry)) {
            $rs = $db->fetch_array($qry);
            
            $cabecera = array(
              "ID" => $rs["id"],
              "codigo" => $rs["codigo"],
              "socioID" => $rs["id_socio"],
              "socio" => $rs["socio"],
              "dui" => $rs["dui"],
              "nro_dui" => $rs["nro_dui"],
              "agencia" => $rs["agencia"],
              "promotor" => $rs["promotor"],
              "analista" => $rs["analista"],
              "producto" => $rs["producto"],
              "productoID" => $rs["id_producto"],
              "tiposbs" => $rs["tiposbs"],
              "destsbs" => $rs["destsbs"],
              "clasifica" => $rs["clasifica"],
              "condicion" => $rs["condicion"],
              "moneda" => $rs["moneda"],
              "mon_abrevia" => $rs["mon_abrevia"],
              "importe" => $rs["importe"],
              "saldo" => $rs["saldo"],
              "tasa" => $rs["tasa_cred"],
              "mora" => $rs["tasa_mora"],
              "desgr" => $rs["tasa_desgr"],
              "nrocuotas" => $rs["nro_cuotas"],
              "fecha_solicred" => $rs["fecha_solicred"],
              "fecha_otorga" => $rs["fecha_otorga"],
              "fecha_pricuota" => $rs["fecha_pricuota"],
              "tipocred" => $rs["tipocred"],
              "frecuencia" => $rs["frecuencia"],
              "observac" => ($rs["observac"]),
              "estado" => ($rs["estado"]*1),
              "atraso" => (($rs["atraso"]<0)?(0):($rs["atraso"] ))
            );
          }
          
          //detalle
          $detalle = 0;
          $qry = $db->query_params("select sum(capital-pg_capital) as capital,sum(interes-pg_interes) as interes,sum(extract(days from now()-fecha)::float*(".$cabecera["mora"]."*0.01/360)*(capital-pg_capital)) as mora,sum(otros-pg_otros) as otros from bn_prestamos_det where extract(days from now()-fecha)>=0 and numero>0 and id_prestamo=$1;",array($data->prestamoID));
          if ($db->num_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $detalle = array(
                "capital" => $rs["capital"]*1,
                "interes" => $rs["interes"]*1,
                "mora" => $rs["mora"]*1,
                "otros" => $rs["otros"]*1,
              );
            }
          }

          //respuesta
          $rpta = array(
            'cabecera'=> $cabecera,
            'detalle'=> $detalle,
            "comboTipoPago" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=13 order by id;"),
            "comboMonedas" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=1 order by id;"),
            "fecha" => $db->fetch_array($db->query("select now() as fecha;"))["fecha"]);
          header('Content-Type: application/json');
          echo json_encode($rpta);
          break;
        case "insPago":
          $estado = 1;
          $tipo_operID = 124;
          $coopacID = $web->coopacID;
          $clientIP = $fn->getClientIP();
          $userID = $_SESSION['usr_ID'];
          $importe = $data->importe;

          /******agregamos bn_movim*******/
          /*******************************/
          $movimID = $db->fetch_array($db->query("select coalesce(max(id)+1,1) as code from bn_movim;"))["code"];
          $codigo = $userID."-".$db->fetch_array($db->query("select right('0000000'||cast(coalesce(max(right(codigo,7)::integer)+1,1) as text),7) as code from bn_movim where id_cajera=".$userID))["code"];
          $sql = "insert into bn_movim values($1,$2,$3,$4,$5,$6,$7,$8,now(),$9,$10,$11,$12,$13,now(),$14)";
          $params = array($movimID,$coopacID,$data->agenciaID,$tipo_operID,$data->medioPagoID,$data->monedaID,$data->socioID,$userID,$codigo,$importe,$estado,$clientIP,$userID,'');
          $rs = $db->fetch_array($db->query_params($sql,$params));
          
          //actualizamos cantidades en bn_prestamos_det
          $pg_otros = 0;
          $pg_mora = 0;
          $pg_interes = 0;
          $pg_capital = 0;
          $pg_tot_otros = 0;
          $pg_tot_mora = 0;
          $pg_tot_interes = 0;
          $pg_tot_capital = 0;
          $sql = "select id_prestamo,numero,capital-pg_capital as capital,interes-pg_interes as interes,extract(days from now()-fecha)::float*($1*0.01/360)*(capital-pg_capital) as mora,otros-pg_otros as otros from bn_prestamos_det where extract(days from now()-fecha)>=0 and numero>0 and id_prestamo=$2 order by numero;";
          $qry = $db->query_params($sql,array($data->tasaMora,$data->prestamoID));
          if ($db->num_rows($qry)>0) {
            $temp = "";
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              if($importe>0){
                $pg_otros = pago_Item(($rs["otros"]),$importe); $importe -= $pg_otros;
                $pg_mora = pago_Item(($rs["mora"]),$importe); $importe -= $pg_mora;
                $pg_interes = pago_Item(($rs["interes"]),$importe); $importe -= $pg_interes;
                $pg_capital = pago_Item(($rs["capital"]),$importe); $importe -= $pg_capital;
                $pg_tot_otros += $pg_otros;
                $pg_tot_mora += $pg_mora;
                $pg_tot_interes += $pg_interes;
                $pg_tot_capital += $pg_capital;
                $whr_otros = ($pg_otros>0)?(",pg_otros=pg_otros+".$pg_otros):("");
                $whr_mora = ($pg_mora>0)?(",pg_mora=pg_mora+".$pg_mora):("");
                $whr_interes = ($pg_interes>0)?(",pg_interes=pg_interes+".$pg_interes):("");
                $whr_capital = ($pg_capital>0)?(",pg_capital=pg_capital+".$pg_capital):("");
                $aa = $db->fetch_array($db->query("update bn_prestamos_det set numero=".$rs["numero"].$whr_otros.$whr_mora.$whr_interes.$whr_capital." where id_prestamo=".$data->prestamoID." and numero=".$rs["numero"].";"));
              }
            }
          }
          //actualizamos saldo de bn_prestamos
          if($pg_tot_capital>0) { $qry = $db->query_params("update bn_prestamos set saldo=saldo-$2 where id=$1;",array($data->prestamoID,$pg_tot_capital)); }

          //agregamos bn_movim_det
          if($pg_tot_otros>0) {reg_movim_det(13,$data->prestamoID,$movimID,$data->productoID,$pg_tot_otros); }
          if($pg_tot_mora>0) {reg_movim_det(12,$data->prestamoID,$movimID,$data->productoID,$pg_tot_mora); }
          if($pg_tot_interes>0) {reg_movim_det(11,$data->prestamoID,$movimID,$data->productoID,$pg_tot_interes); }
          if($pg_tot_capital>0) {reg_movim_det(10,$data->prestamoID,$movimID,$data->productoID,$pg_tot_capital); }


          /******agregamos bn_saldos*******/
          /********************************/
          if($pg_tot_capital>0) {
            $sql = "update bn_saldos set saldo=(saldo + $5),sys_ip=$6,sys_user=$7,sys_fecha=now() where id_coopac=$1 and id_socio=$2 and id_tipo_oper=$3 and id_producto=$4;";
            $params = array(
              $coopacID,
              $data->socioID,
              $tipo_operID,
              $data->productoID,
              $pg_tot_capital,
              $clientIP,
              $userID );
            $rs = $db->fetch_array($db->query_params($sql,$params));
          }

          $rpta = array("error" => false,"movimID"=>$movimID,"ingresados" => 1);
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
