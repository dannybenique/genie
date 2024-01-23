<?php
  // No almacenar en el cache del navegador esta pagina.
  header("Expires: Tue, 01 Jul 2001 06:00:00 GMT");                 // Expira en fecha pasada
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");    // Siempre pagina modificada
  header("Cache-Control: no-store, no-cache, must-revalidate");   // HTTP/1.1
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");                                       // HTTP/1.0

  // -------- Chequear sesion existe -------
  session_name("GENIE"); // usamos la sesion de nombre definido.
  session_start(); // Iniciamos el uso de sesiones

  // Chequeamos si estan creadas las variables de sesion de identificacion del usuario,
  // El caso mas comun es el de una vez "matado" la sesion se intenta volver hacia atras con el navegador.
  if (!isset($_SESSION['usr_ID'])) { // Borramos la sesion creada por el inicio de session anterior
    session_destroy();
    session_start();
    session_regenerate_id(true);
  }
?>
