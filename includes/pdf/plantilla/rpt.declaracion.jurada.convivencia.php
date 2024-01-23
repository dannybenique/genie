<?php
  include_once("../../../libs/pdf.php/mpdf/autoload.php");
  include_once("../../db_database.php");
  $personaID = $_GET["personaID"];

  //personas
  $qryPers = $db->select("select * from dbo.vw_personas where ID=".($personaID));
  $qryCony = $db->select("select * from dbo.tb_personas_cony where id_persona=".($personaID));
  if ($db->has_rows($qry)) { $rsCony = $db->fetch_array($qryCony); }
  $rs = $db->fetch_array($qryPers);
  $apellidos = utf8_encode($rs["ap_paterno"])." ".utf8_encode($rs["ap_materno"]);
  $nombres = utf8_encode($rs["nombres"]);
  $tipoDNI = utf8_encode($rs["doc"]);
  $nroDNI = ($rs["DNI"]);
  $domicilio = utf8_encode($rs["direccion"]);
  $distrito = utf8_encode($rs["distrito"]);
  $provincia = utf8_encode($rs["provincia"]);
  $region = utf8_encode($rs["region"]);
  $tiempoRelacion = ($rsCony["tiempoRelacion"]);

  //fecha
  $qry = $db->select("select day(getdate()) as dia,nombre as mes,year(getdate()) as anio from sis_meses where ID=MONTH(getdate())");
  $rs = $db->fetch_array($qry);
  $dia = $rs["dia"];
  $mes = $rs["mes"];
  $anio = $rs["anio"];

  //documento html
  $html ='
  <!DOCTYPE html>
  <html lang="en">
    <head>
      <meta charset="utf-8">
      <title>Carta de Autorizacion - Garantia Liquida</title>
      <style>
        .clearfix:after { content: ""; display: table; clear: both; }

        body { position: relative; width: 21cm; height: 29.7cm; margin: 0; color: #111; background:#fff; font-size: 14px; font-family: Arial; }
        a { color: #0087C3; text-decoration: none; }
        header { padding: 10px 0; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; border-spacing: 0; margin-bottom: 20px; }
        table th { white-space: nowrap; font-weight: normal; }
        table td { text-align: right; }
        table td h3{ color: #57B223; font-size: 1.2em; font-weight: normal; margin: 0 0 0.2em 0; }
        table tbody tr:last-child td { border: none; }
        table tfoot td { padding: 10px 20px; background: #FFFFFF; border-bottom: none; font-size: 1.2em; white-space: nowrap; border-top: 1px solid #AAAAAA; }
        table tfoot tr:first-child td { border-top: none; }
        table tfoot tr:last-child td { color: #57B223; font-size: 1.4em; border-top: 1px solid #57B223; }
        table tfoot tr td:first-child { border: none; }

        #logo { float: left; margin-top: 8px; }
        #logo img { height: 70px; }
      </style>
    </head>
    <body>
    <main>
      <div style="position:relative;">
        <div style="float:left;width:100px;"><img src="img/logo.jpg" style="width:100px;"/></div>
        <div style="width:400px;float:right;">
            <h3 style="width:400px;background:#000;color:white;font-size:18px;border-radius:0.3em;margin:0;padding:0.3em;text-align:center;">DECLARACION JURADA DE CONVIVENCIA</h3>
        </div>
      </div>
      <div class="clearfix">
        <div style="font-size:15px;">
          <p style="text-align:justify;margin-top:50px;">
            Por la presente YO: '.$nombres.' '.$apellidos.', identificado con Documento Nacional de Identidad '.$tipoDNI.' Nº '.$nroDNI.',
            domiciliado en '.$domicilio.', distrito de '.$distrito.', provincia de '.$provincia.' región de '.$region.'.
          </p>
          <p style="text-align:center;text-decoration:underline;font-weight:bold;font-size:18;margin-top:35px;">
            DECLARO BAJO JURAMENTO:
          </p>
          <p style="text-align:justify;margin-top:20px;">
            Que el(la) suscrito(a), soy conviviente y vengo haciendo vida en común con el señor(a)....................<br>
            ............................................................................................................ con DNI Nº............................., desde
            hace '.$tiempoRelacion.' años aproximadamente y mantenemos una relacion de concubinaria en forma estable y voluntaria habiendo entre ambos realizado finalidades y cumpliendo
            deberes semejantes al matrimonio, habiendo procreado a nuestros menores hijos llamados:<br>
            <br>
              - ............................................................................................................. de .......(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) años de edad<br>
              - ............................................................................................................. de .......(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) años de edad<br>
              - ............................................................................................................. de .......(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) años de edad<br>
              - ............................................................................................................. de .......(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) años de edad<br>
            <br>
            Formulo la presente declaración en honor a la verdad y al amparo de lo dispuesto por la ley Nº 27444, ley de simplificación administrativa, sus modificatorias
            y su reglamento. Asumiendo expresa responsabilidad civil y penal por la veracidad de la presente declaración relevando a terceros de ulteriores responsabilidades
            y en caso de falsedad nos sometemos a las sanciones que establece la ley.
          </p>
          <p style="text-align:justify;">
            Para tal efecto y fe de lo que declaro, rubrico la misma dando fe de lo declarado a los '.$dia.' dias del mes de '.$mes.' del año '.$anio.'.
          </p>
          <div style="margin-top:80px;">
            <div style="text-align:right;">
              <div style="text-align:center;width:90px;margin-top:20px;float:right;">
                <div style="border:1px solid black;width:90px;height:100px;">&nbsp;
                </div>
                <i style="font-size:12px;">huella Digital</i>
              </div>
            </div>
            <div style="text-align:center;width:100%;">
              <div style="position:absolute;width:300px;margin-left:25%;">
                <div style="width:100%;border-bottom:1px solid #555555;"></div>
                <b style="font-size:14px;color:black;">'.$apellidos.' '.$nombres.'</b><br>
                <b style="color:black;">'.$tipoDNI.' Nº '.$nroDNI.'</b>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
    </body>
  </html>';

  $mpdf = new \Mpdf\Mpdf([]);
  $mpdf->WriteHTML($html);
  $mpdf->Output();
  exit;
?>
