<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');
      include_once('../../../includes/web_config.php');

      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      //****************personas****************
      function getViewWorker($personaID){
        $db = $GLOBALS["db"]; //base de datos
        $fn = $GLOBALS["fn"]; //funciones
        $web = $GLOBALS["web"]; //web-config
        
        //obtener datos personales
        $sql = "select s.*,c.nombre as colegio,e.nombrecorto as usermod from app_empleados s join app_colegios c on s.id_colegio=c.id left join app_empleados e on e.id=s.sys_user where s.estado=1 and s.id=:personaID and s.id_colegio=:colegioID;";
        $colegioID = ($fn->getValorCampo("select id_rol from app_usuarios where id=".$personaID,"id_rol")==101) ? 1 : $web->colegioID;
        $params = [":personaID"=>$personaID,":colegioID"=>$colegioID];
        $qry = $db->query_all($sql,$params);
        
        if ($qry) {
            $rs = reset($qry);
            $tabla = array(
              "ID" => ($rs["id"]),
              "colegioID" => ($rs["id_colegio"]),
              "cargoID" => ($rs["id_cargo"]),
              "comboCargos" => ($fn->getComboBox("select id,nombre from sis_tipos where id_padre=7 order by id;")),
              "codigo" => $rs["codigo"],
              "estado" => $rs["estado"],
              "nombrecorto" => $rs["nombrecorto"],
              "correo" => $rs["correo"],
              "fecha_ing" => $rs["fecha_ing"],
              "fecha_vacac" => $rs["fecha_vacac"],
              "fecha_renov" => $rs["fecha_renov"],
              "fecha_baja" => $rs["fecha_baja"],
              "observac" => ($rs["observac"]),
              "usermod" => ($rs["usermod"]),
              "sysuser" => ($rs["sys_user"]),
              "sysfecha" => ($rs["sys_fecha"])
            );
        }
        return $tabla; 
      }
      function getViewUser($personaID){
        $db = $GLOBALS["db"]; //base de datos
        $fn = $GLOBALS["fn"]; //funciones
        $web = $GLOBALS["web"]; //web-config

        //obtener datos usuario
        $sql = "select * from app_usuarios where id=:id and id_colegio=:colegioID;";
        $colegioID = ($fn->getValorCampo("select id_rol from app_usuarios where id=".$personaID,"id_rol")==101) ? 1 : $web->colegioID;
        $params = [":id"=>$personaID,":colegioID"=>$colegioID];
        $qry = $db->query_all($sql,$params);
        if ($qry) {
          $rs = reset($qry);
          $tabla = array(
            "ID" => ($rs["id"]*1),
            "colegioID" => ($rs["id_colegio"]*1),
            "rolID" => ($rs["id_rol"]*1),
            "login" => ($rs["login"]),
            "comboRoles" => ($fn->getComboBox("select id,nombre from sis_tipos where id_padre=2 order by id;")),
            "menu" => ($rs["menu"]),
            "sysuser" => ($rs["sys_user"]),
            "sysfecha" => ($rs["sys_fecha"])
          );
        } else {
          $tabla = array(
            "ID" => null,
            "comboRoles" => ($fn->getComboBox("select id,nombre from sis_tipos where id_padre=2 order by id;"))
          );
        }
        return $tabla; 
      }
      switch ($data->TipoQuery) {
        case "selWorkers":
          //obteniendo la consulta
          $tabla = array();
          $buscar = strtoupper($data->buscar);
          $whr = " and id_colegio in(:colegioID".(($_SESSION["usr_data"]["rolID"]==101)?",1":"").") and (empleado LIKE :buscar or nro_dui LIKE :buscar) ";
          $params = [":colegioID"=>$web->colegioID,":buscar"=>'%'.$buscar.'%'];
          $qryCount = $db->query_all("select count(*) as cuenta from vw_empleados where estado=1 ".$whr.";",$params);
          $rsCount = ($qryCount) ? reset($qryCount)["cuenta"] : (0);

          $qry = $db->query_all("select * from vw_empleados where estado=1 ".$whr." order by empleado limit 25 offset 0;",$params);
          if ($qry) {
            foreach($qry as $rs){
              $tabla[] = array(
                "ID" => $rs["id"],
                "codigo" => $rs["codigo"],
                "nro_dui"=> str_replace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["nro_dui"]),
                "empleado" => str_ireplace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["empleado"]),
                "nombrecorto" => $rs["nombrecorto"],
                "cargo" => ($rs["cargo"]),
                "login" => ($rs["login"])
              );
            }
          }

          //respuesta
          $rpta = array("tabla"=>$tabla,"cuenta"=>$rsCount,"rolID" => (int)$_SESSION["usr_data"]["rolID"]);
          echo json_encode($rpta);
          break;
        case "insWorker":
          //ingresar empleado
          $qry = $db->query_all("select right('000000'||cast(coalesce(max(right(codigo,4)::integer)+1,1) as text),4) as code from app_empleados where id_colegio=".$web->colegioID.";");
          $codworker = reset($qry)["code"];
          $sql = "insert into app_empleados values(:id,:colegioID,:cargoID,:codworker,:nombrecorto,:correo,:fecha,null,null,null,:estado,:sysIP,:userID,now(),:observac);";
          $params = [
            ":id"=>$data->workerID,
            ":colegioID"=>$web->colegioID, 
            ":cargoID"=>$data->cargoID, 
            ":codworker"=>$codworker, 
            ":nombrecorto"=>$data->nombrecorto,
            ":correo"=>$data->correo,
            ":fecha"=>$data->fecha,
            ":estado"=>1,
            ":sysIP"=>$fn->getClientIP(), 
            ":userID"=>$_SESSION['usr_ID'],
            ":observac"=>$data->observac
          ];
          $qry = $db->query_all($sql,$params);
          $rs = reset($qry);

          //ingresar usuario
          if($data->usuario!=null){
            $sql = "insert into app_usuarios values(:id,:colegioID,:rolID,:login,:passw,:estado,:menu,:sysIP,:userID,now());";
            $params = [
              ":id"=>$data->workerID,
              ":colegioID"=>$web->colegioID,
              ":rolID"=>$data->usuario->rolID,
              ":login"=>$data->usuario->login,
              ":passw"=>$data->usuario->passw,
              ":estado"=>1,
              ":menu"=>$data->usuario->menu,
              ":sysIP"=>$fn->getClientIP(), 
              ":userID"=>$_SESSION['usr_ID']
            ];
            $qry = $db->query_all($sql,$params);
            $rs = reset($qry);
          }

          //respuesta
          $rpta = array("error"=>false, "insert"=>1);
          echo json_encode($rpta);
          break;
        case "updWorker":
          //actualiza empleado
          $sql = "update app_empleados set id_cargo=:cargoID,nombrecorto=:nombrecorto,correo=:correo,observac=:observac,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:id and id_colegio=:colegioID;";
          $params = [
            ":id" => $data->workerID,
            ":colegioID" => $web->colegioID,
            ":cargoID" => $data->cargoID,
            ":nombrecorto" => $data->nombrecorto,
            ":correo" => $data->correo,
            ":observac" => $data->observac,
            ":sysIP" => $fn->getClientIP(), 
            ":userID" => $_SESSION['usr_ID']
          ];
          $qry = $db->query_all($sql,$params);
          $rs = reset($qry);

          //actualiza usuario
          if($data->usuario!=null){
            $usuario = $data->usuario;
            
            //verificamos que el usuario este agregado en usuarios
            $qry = $db->query_all("select * from app_usuarios where id=".$data->workerID);
            if($qry) { //actualizamos datos
              $paramx = [
                ":id" => $data->workerID,
                ":rolID" => $usuario->rolID,
                ":login" => $usuario->login,
                ":menu" => $usuario->menu,
                ":sysIP" => $fn->getClientIP(), 
                ":userID" => $_SESSION['usr_ID']
              ];
              $xql = "update app_usuarios set id_rol=:rolID,login=:login,menu=:menu,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:id;";
            } else { //insertamos nuevo usuario
              $paramx = [
                ":id" => $data->workerID,
                ":colegioID" => $web->colegioID,
                ":rolID" => $usuario->rolID,
                ":login" => $usuario->login,
                ":passw" => $usuario->passw,
                ":menu" => $usuario->menu,
                ":sysIP" => $fn->getClientIP(), 
                ":userID" => $_SESSION['usr_ID']
              ];
              $xql = "insert into bn_usuarios values(:id,:coopacID,:rolID,:login,:passw,1,:menu,:sysIP,:userID,now())";
            }
            $qrx = $db->query_all($xql,$paramx);
            $rs = reset($qrx);
          } else {
            $qry = $db->query_all("delete from bn_usuarios where id=".$data->workerID);
            $rs = reset($qry);
          }

          //respuesta
          $rpta = array("error"=>false, "update"=>1);
          echo json_encode($rpta);
          break;
        case "delWorkers":
          $params = array();
          for($i=0; $i<count($data->arr); $i++){
            $sql = "update app_empleados set estado=0,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:id";
            $params = [
              ":id"=>$data->arr[$i],
              ":sysIP"=>$fn->getClientIP(),
              ":userID"=>$_SESSION['usr_ID']
            ];
            $qry = $db->query_all($sql,$params);
            $rs = ($qry) ? (reset($qry)) : (null);
          }

          //respuesta
          $rpta = array("error"=>false, "delete"=>$data->arr);
          echo json_encode($rpta);
          break;
        case "viewWorker":
          //respuesta
          $rpta = array(
            'tablaPers' => $fn->getViewPersona($data->personaID),
            'tablaWorker' => getViewWorker($data->personaID),
            'tablaUser' => getViewUser($data->personaID)
          );
          echo json_encode($rpta);
          break;
        case "VerifyWorker":
          $tablaPers = ""; //almacena los datos de la persona
          $persona = false; //indica que existe en personas
          $activo = false; //indica que encontro en tabla de empleados

          //verificar en Personas
          $sql = "select id from personas where (nro_dui=:nrodni);";
          $params = [":nrodni"=>$data->nroDNI];
          $qry = $db->query_all($sql,$params);
          if($qry){
            $rs = reset($qry);
            $tablaPers = $fn->getViewPersona($rs["id"]);
            $persona = true;
            //verificar en Empleados
            $sql = "select id from app_empleados where id_colegio=:colegioID and id=:id;";
            $params = [":colegioID"=>$web->colegioID,":id"=>$rs["id"]];
            $qryEmpleado = $db->query_all($sql,$params);
            $activo = ($qryEmpleado) ? true : false;
          }

          //respuesta
          $rpta = array(
            "tablaPers" => $tablaPers,
            "persona" => $persona,
            "activo" => $activo,
            "mensajeNOadd" => "ya es EMPLEADO ACTIVO...",
            "comboCargos" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=7 order by id;"),
            "comboRoles" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=2 order by id;"),
            "fecha" => $fn->getFechaActualDB(),
            "colegioID" => $web->colegioID);
          echo json_encode($rpta);
          break;
        case "startWorker":
          //respuesta
          $rpta = array(
            "comboCargos" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=7 order by id;"),
            "comboRoles" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=2 order by id;"),
            "fecha" => $fn->getFechaActualDB(),
            "colegio" => $web->colegioID);
          echo json_encode($rpta);
          break;
        case "selSisMenu":
          //obtener menu de los perfiles => tabla sis_menu
          $menu = "";
          $qry = $db->query_all("select * from sis_menu where id=".$data->perfilID);
          if ($qry) {
            $rs = reset($qry);
            $menu = $rs["json"];
          }

          //respuesta
          $rpta = array("menu"=>$menu);
          echo json_encode($rpta);
          break;
        case "selUserPass":
          $qry = $db->query_all("select u.id,u.id_colegio,u.login,e.nombrecorto from app_usuarios u join app_empleados e on u.id=e.id where u.id=".$data->userID);
          if ($qry) {
            $rs = reset($qry);
            $tabla = array(
              "ID" => $rs["id"],
              "login" => $rs["login"],
              "nombrecorto" => $rs["nombrecorto"],
              "colegioID" => $rs["id_colegio"]
            );
          }

          //respuesta
          $rpta = $tabla;
          echo json_encode($rpta);
          break;
        case "changeUserPass":
          $sql = "update app_usuarios set passw=:passw,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:id and id_colegio=:colegioID;";
          $params = [
            ":id"=>$data->userID, 
            ":colegioID"=>$data->colegioID, 
            ":passw"=>$data->passw, 
            ":sysIP"=>$fn->getClientIP(), 
            ":userID"=>$_SESSION['usr_ID']
          ];
          $qry = $db->query_all($sql,$params);
          $rs = ($qry) ? (reset($qry)) : (null);

          //respuesta
          $rpta = array("error"=>false, "update"=>1);
          echo json_encode($rpta);
      }
      $db->close();
    } else{
      $resp = array("error"=>true,"data"=>$tabla,"mensaje"=>"ninguna variable en POST");
      echo json_encode($resp);
    }
  } else {
    $resp = array("error"=>true,"mensaje"=>"Caducó la sesion.");
    echo json_encode($resp);
  }
?>
