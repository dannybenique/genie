<?php
  include_once('includes/sess_verifica.php');
  if(isset($_SESSION['usr_ID'])) {
    $user = $_SESSION['usr_data'];
    $menu = json_decode($user["menu"]);
    $link = (isset($_GET["page"])?($_GET["page"]):(""));
    
    function crearMenu($_menu,$_obj,$_link){
      $submenu = "";
      foreach($_menu->submenu as $valor){
        $submenu .= '
        <li '.(($valor->mnuLink==$_link)?('class="active"'):('')).'>
          <a href="interfaz.php?page='.$valor->mnuLink.'">
            <i class="fa '.$valor->mnuIcon.'"></i> <span>'.$valor->mnuText.'</span>
          </a>
        </li>';
      }

      echo '
        <li class="treeview '.$_obj.'">
          <a href="#">
            <i class="fa '.$_menu->mnuIcon.'"></i>
            <span>'.$_menu->mnuText.'</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">'
            .$submenu.
          '</ul>
        </li>';
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="Expires" content="0">
  <meta http-equiv="Last-Modified" content="0">
  <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <title>GENIE [administracion educativa]</title>
  <link rel="shortcut icon" href="favicon.png" />
  <link rel="icon" type="image/vnd.microsoft.icon" href="favicon.png" />
  <!-- Bootstrap -->
  <link rel="stylesheet" href="libs/bootstrap/3.3.7/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="libs/font-awesome/4.7.0/css/all.min.css">
    <!-- fonts para el sistema -->
  <link rel="stylesheet" href="app/css/fonts.css" />
  <link rel="stylesheet" href="app/css/interfaz.css" />

  <!-- jQuery 3 -->
  <script src="libs/jquery/jquery-3.6.0.min.js"></script>
  <!-- Bootstrap -->
  <script src="libs/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <!-- app interfaz-->
  <script src="app/js/interfaz/adminLTE.min.js"></script>
  <script src="app/js/interfaz/funciones.js"></script>
  <script src="app/js/interfaz/interfaz.js"></script>
  <script>
    inicioAPP();
  </script>
</head>
<body class="hold-transition skin-blue sidebar-mini" <?php if(!($user['rolID']==101)) echo('ondragstart="return false" onselectstart="return false" oncontextmenu="return false"');?>>
<div class="wrapper">
  <header class="main-header">
    <!-- Logo -->
    <a href="interfaz.php" class="logo" style="background:#1A2226;">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><img src="app/images/interfaz_logo.png" style="width:34px;"/></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg">
        <img src="app/images/interfaz_logo.png" style="width:34px;position:relative;top:-5px;"/>
        <b>GENIE</b></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
      </a>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <?php if($user['rolID']==101){ //solo superadmin?>
          <!-- Notificaciones: style can be found in dropdown.less -->
          <li class="dropdown notifications-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-bell-o"></i>
              <span id="lblNotifiCount1" class="label label-warning NotifiCount"></span>
            </a>
            <ul class="dropdown-menu">
              <li class="header">Tienes <span id="lblNotifiCount2" class="NotifiCount"></span> notificaciones</li>
              <li>
                <ul class="menu" id="appInterfazNotificaciones">
                </ul>
              </li>
              <li class="footer"><a href="javascript:appSubmitButton('notificaciones');">Ver todas...</a></li>
            </ul>
          </li>
          <?php }?>
          <!-- Menu Usuario: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img id="ifaz_barra_imagen" src="" class="user-image" alt="Usuario">
              <span id="ifaz_barra_nombrecorto" class="hidden-xs"></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img id="ifaz_perfil_imagen" src="" class="img-circle" alt="User Image">
                <p>
                  <span id="ifaz_perfil_nombrecorto"></span>
                  <small id="ifaz_perfil_cargo"></small>
                </p>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="javascript:appSubmitButton('profile');" class="btn btn-default btn-flat">Perfil</a>
                </div>
                <div class="pull-right">
                  <a href="javascript:appSubmitButton('logout');" class="btn btn-default btn-flat">Salir</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <!-- =========================== MENU ============================== -->
  <?php
  $menuDashboard = ""; //menu dashboard
  $menuCaja = ""; //menu caja
  $menuOper = ""; //menu operaciones (creditos)
  $menuFondos = ""; //menu fondos (aportes,DPF,ahorros)
  $menuMtto = ""; //menu mantenimiento
  $menuMaster = ""; //menu maestro del sistema
  $menuRepo = ""; //menu reportes

  $appPage = "pages/global/dashboard/page.php";
  if(isset($_GET["page"])){
    switch ($_GET["page"]) {
      case "profile": $appPage = "pages/global/profile/page.php"; break;
      case "cajaDesembolsos" : $menuCaja = 'active menu-open'; $appPage = "pages/caja/desembolsos/page.php"; break;
      case "cajaPagos"       : $menuCaja = 'active menu-open'; $appPage = "pages/caja/pagos/page.php"; break;
      case "cajaAportes"     : $menuCaja = 'active menu-open'; $appPage = "pages/caja/aportes/page.php"; break;
      case "cajaAhorros"     : $menuCaja = 'active menu-open'; $appPage = "pages/caja/ahorros/page.php"; break;
      case "cajaExtornos"    : $menuCaja = 'active menu-open'; $appPage = "pages/caja/extornos/page.php"; break;
      case "cajaBilletaje"   : $menuCaja = 'active menu-open'; $appPage = "pages/caja/billetaje/page.php"; break;
      case "operCreditos"        : $menuOper = 'active menu-open'; $appPage = "pages/oper/creditos/page.php"; break;
      case "operSimulaCreditos"  : $menuOper = 'active menu-open'; $appPage = "pages/oper/simula/creditos.php"; break;
      case "operSolicitaCredito" : $menuOper = 'active menu-open'; $appPage = "pages/oper/solicred/page.php"; break;
      case "mttoAlumnos"   : $menuMtto = 'active menu-open'; $appPage = "pages/mtto/alumnos/page.php"; break;
      case "mttoEmpleados" : $menuMtto = 'active menu-open'; $appPage = "pages/mtto/empleados/page.php"; break;
      case "masterPersonas"  : $menuMaster = 'active menu-open'; $appPage = "pages/master/personas/page.php"; break;
      case "masterProductos" : $menuMaster = 'active menu-open'; $appPage = "pages/master/productos/page.php"; break;
      case "masterTipos"     : $menuMaster = 'active menu-open'; $appPage = "pages/master/tipos/page.php"; break;
      case "masterMovim"     : $menuMaster = 'active menu-open'; $appPage = "pages/master/movim/page.php"; break;
      case "repoMovim"         : $menuRepo = 'active menu-open'; $appPage = "pages/repo/movim/page.php"; break;
      case "repoExtractoBanca" : $menuRepo = 'active menu-open'; $appPage = "pages/repo/extractobanca/page.php"; break;
    }
  } else{
    $menuDashboard = 'class="active"';
  }
?>

<aside class="main-sidebar">
      <!-- MENU PRINCIPAL -->
      <section class="sidebar">
        <div class="user-panel" style="background:#1A2226;display:none;">
          <div class="pull-left image">
            <img id="ifaz_menu_imagen" src="" class="img-circle" alt="foto de usuario">
          </div>
          <div class="pull-left info">
            <p id="ifaz_menu_nombrecorto"></p>
            <small id="ifaz_menu_login" style="color:#859E9E;position:relative;top:-5px;"></small>
          </div>
        </div>
        <ul class="sidebar-menu" data-widget="tree">
          <li <?php echo($menuDashboard);?>>
            <a href="interfaz.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a>
          </li>
          <?php 
            if(isset($menu->caja)){ crearMenu($menu->caja,$menuCaja,$link); }
            if(isset($menu->oper)){ crearMenu($menu->oper,$menuOper,$link); }
            if(isset($menu->fondos)){ crearMenu($menu->fondos,$menuFondos,$link); }
            if(isset($menu->mtto)){ crearMenu($menu->mtto,$menuMtto,$link); }
            if(isset($menu->master)){ crearMenu($menu->master,$menuMaster,$link); }
            if(isset($menu->repo)){ crearMenu($menu->repo,$menuRepo,$link); }
          ?>
        </ul>
      </section>
    </aside>
  <!-- ========================= CONTENIDO  ====================== -->
  <div class="content-wrapper">
    <?php include_once($appPage); ?>
  </div>
</div>
</body>
</html>
<?php
  } else {
    header('location:index.php');
  }
?>
