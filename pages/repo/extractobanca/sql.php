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
        case "selSocios":
          $socios = array();
          $buscar = strtoupper($data->buscar);
          $params = [ ":coopacID" => $web->coopacID, ":buscar"=>'%'.$buscar.'%' ];
          $sql = "select s.id_socio,s.persona,s.dui,s.nro_dui,count(x.*) as productos from vw_socios s left join bn_saldos x on (s.id_socio=x.id_socio and x.estado=1) where s.estado=1 and s.id_coopac=:coopacID and (nro_dui LIKE :buscar) group by s.id_socio,s.persona,s.dui,s.nro_dui";
          $qry = $db->query_all($sql,$params);
          if ($qry) {
            foreach($qry as $rs){
              $socios[] = array(
                "ID" => $rs["id_socio"],
                "socio" => $rs["persona"],
                "DUI" => $rs["dui"],
                "nro_DUI" => str_ireplace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["nro_dui"]),
                "prods" => $rs["productos"]*1
              );
            }
          }
          $rpta = array("socios"=>$socios);
          echo json_encode($rpta);
          break;
        case "viewSocio":
          //socio
          $params = ["coopacID"=>$web->coopacID,":socioID"=>$data->socioID];
          $sql = "select * from vw_socios where id_coopac=:coopacID and id_socio=:socioID";
          $qry = $db->query_all($sql,$params);
          if ($qry) {
            $rs = reset($qry);
            $socio = array(
              "tipoPersona" => $rs["tipo_persona"],
              "socioID" => $rs["id_socio"],
              "codigo" => $rs["codigo"],
              "persona" => $rs["persona"],
              "tipoDUI" => $rs["dui"],
              "nroDUI" => $rs["nro_dui"],
              "direccion" => $rs["direccion"]
            );
          }
          
          //productos
          $prods = array();
          $sql = "select * from vw_saldos where estado=1 and id_coopac=:coopacID and id_socio=:socioID order by id_tipo_oper;";
          $qry = $db->query_all($sql,$params);
          if ($qry) {
            foreach($qry as $rs){
              $prods[] = array(
                "saldoID" => $rs["id"],
                "operID" => $rs["id_tipo_oper"],
                "productoID" => $rs["id_producto"],
                "producto" => $rs["producto"],
                "cod_prod" => $rs["cod_prod"],
                "saldo" => $rs["saldo"]
              );
            }
          }

          //respuesta
          $rpta = array('socio'=> $socio, 'prods'=> $prods);
          echo json_encode($rpta);
          break;
        case "viewMovimAportes":
          //producto
          $qry = $db->query_all("select p.nombre as producto,s.* from bn_saldos s join bn_productos p on (p.id=s.id_producto) where s.id=".$data->saldoID);
          $producto = reset($qry)["producto"];

          //movimientos
          $movim = array();
          $params =[":coopacID"=>$web->coopacID,":saldoID"=>$data->saldoID];
          $qry = $db->query_all("select * from vw_movim where id_coopac=:coopacID and id_saldo=:saldoID order by fecha;",$params);
          if ($qry) {
            foreach($qry as $rs){
              $movim[] = array(
                "codagenc" => $rs["codagenc"],
                "coduser" => $rs["coduser"],
                "fecha" => $rs["fecha"],
                "codigo" => $rs["codigo"],
                "codmov" => $rs["codmov"],
                "movim" => $rs["movim"],
                "ingresos" => ($rs["in_out"]==1 && $rs["afec_prod"]==1)?($rs["importe_det"]*1):(0),
                "salidas" => ($rs["in_out"]==0 && $rs["afec_prod"]==1)?($rs["importe_det"]*1):(0),
                "otros" => ($rs["afec_prod"]==0)?($rs["importe_det"]*1):(0)
              );
            }
          }

          //respuesta
          $rpta = array('producto'=>$producto,'movim'=> $movim);
          echo json_encode($rpta);
          break;
        case "viewMovimCreditos":
          //producto
          $qry = $db->query_all("select p.nombre as producto,s.* from bn_saldos s join bn_productos p on (p.id=s.id_producto) where s.id=".$data->saldoID);
          $rs = reset($qry);
          $producto = array(
            "producto" => $rs["producto"],
            "codigo" => $rs["codigo"],
            "saldo" => $rs["saldo"]
          );

          //movimientos
          $movim = array();
          $params =[":coopacID"=>$web->coopacID,":saldoID"=>$data->saldoID];
          $sql = "select codagenc,coduser,fecha,codigo,".
                 "sum(CASE WHEN codmov='09' THEN importe_det*(-1) ELSE 0 END) as desembolso,".
                 "sum(CASE WHEN codmov='10' THEN importe_det ELSE 0 END) as capital,".
                 "sum(CASE WHEN codmov='11' THEN importe_det ELSE 0 END) as interes,".
                 "sum(CASE WHEN codmov='12' THEN importe_det ELSE 0 END) as mora,".
                 "sum(CASE WHEN codmov='13' THEN importe_det ELSE 0 END) as otros ".
                 "from vw_movim where id_coopac=:coopacID and id_saldo=:saldoID ".
                 "group by codagenc,coduser,fecha,codigo ".
                 "order by fecha,codigo";
          $qry = $db->query_all($sql,$params);
          if ($qry) {
            foreach($qry as $rs){
              $movim[] = array(
                "codagenc" => $rs["codagenc"],
                "coduser" => $rs["coduser"],
                "fecha" => $rs["fecha"],
                "codigo" => $rs["codigo"],
                "capital" => $rs["capital"]+$rs["desembolso"],
                "interes" => $rs["interes"]*1,
                "mora" => $rs["mora"]*1,
                "otros" => $rs["otros"]*1
              );
            }
          }

          //respuesta
          $rpta = array('producto'=>$producto,'movim'=> $movim);
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
