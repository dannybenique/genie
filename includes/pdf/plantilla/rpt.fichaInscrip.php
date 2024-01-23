<?php
  include_once("../../../libs/pdf.php/mpdf/autoload.php");
  include_once("../../db_database.php");
  //personas
  $qry = $db->select("select * from dbo.vw_personas where ID=".($_REQUEST["personaID"]));
  $rsPers = $db->fetch_array($qry);
  //laborales de persona
  $qry = $db->select("select * from dbo.vw_personas_labo where id_persona=".($_REQUEST["personaID"]));
  if ($db->has_rows($qry)) {
    $rsLabo = $db->fetch_array($qry);
    $htmlLabo = '
      Nombre de la Empresa: <b style="color:black;"><u>'.utf8_encode($rsLabo["empresa"]).'</u></b><br>
      Actividad: <b style="color:black;"><u>'.utf8_encode($rsLabo["rubro"]).'</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      Telefono: <b style="color:black;"><u>'.utf8_encode($rsLabo["telefono"]).'</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      Cargo: <b style="color:black;"><u>'.utf8_encode($rsLabo["cargo"]).'</u></b><br>
      Región: <b style="color:black;"><u>'.utf8_encode($rsLabo["region"]).'</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      Provincia: <b style="color:black;"><u>'.utf8_encode($rsLabo["provincia"]).'</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      Distrito: <b style="color:black;"><u>'.utf8_encode($rsLabo["distrito"]).'</u></b><br>
      Direccion: <b style="color:black;"><u>'.utf8_encode($rsLabo["direccion"]).'</u></b><br>';
  } else {
    $htmlLabo = 'NO SE REGISTRA DATOS LABORALES';
  }

  //conyuge
  $qry = $db->select("select * from dbo.vw_personas_cony where id_persona=".($_REQUEST["personaID"]));
  if ($db->has_rows($qry)) {
    $rsCony = $db->fetch_array($qry);
    $rsConyPers = $db->fetch_array($db->select("select * from dbo.vw_personas where ID=".($rsCony["id_conyuge"])));
    $htmlConyPers = '
      Apellidos: <b style="color:black;"><u>'.utf8_encode($rsConyPers["ap_paterno"]).' '.utf8_encode($rsConyPers["ap_materno"]).'</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      Nombres: <b style="color:black;"><u>'.utf8_encode($rsConyPers["nombres"]).'</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      '.$rsConyPers["doc"].': <b style="color:black;"><u>'.utf8_encode($rsConyPers["DNI"]).'</u></b><br>
      Fecha de Nac.: <b style="color:black;"><u>'.($rsConyPers["fecha_nac"]).'</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      Lugar de Nac: <b style="color:black;"><u>'.utf8_encode($rsConyPers["lugar_nac"]).'</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      Sexo: <b style="color:black;"><u>'.utf8_encode($rsConyPers["sexo"]).'</u></b><br>
      Estado Civil: <b style="color:black;"><u>'.utf8_encode($rsConyPers["ecivil"]).'</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      Profesion: <b style="color:black;"><u>'.utf8_encode($rsConyPers["profesion"]).'</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      Ocupacion: <b style="color:black;"><u>'.utf8_encode($rsConyPers["ocupacion"]).'</u></b><br>';
    $htmlConyLabo = '
      Nombre de la Empresa: <b style="color:black;"><u>'.utf8_encode($rsCony["empresa"]).'</u></b><br>
      Actividad: <b style="color:black;"><u>'.utf8_encode($rsCony["rubro"]).'</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      Telefono: <b style="color:black;"><u>'.utf8_encode($rsCony["telefono"]).'</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      Cargo: <b style="color:black;"><u>'.utf8_encode($rsCony["cargo"]).'</u></b><br>
      Región: <b style="color:black;"><u>'.utf8_encode($rsCony["region"]).'</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      Provincia: <b style="color:black;"><u>'.utf8_encode($rsCony["provincia"]).'</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      Distrito: <b style="color:black;"><u>'.utf8_encode($rsCony["distrito"]).'</u></b><br>
      Direccion: <b style="color:black;"><u>'.utf8_encode($rsCony["direccion"]).'</u></b><br>';
  } else {
    $htmlConyPers = 'NO SE REGISTRA DATOS DE CONYUGE';
    $htmlConyLabo = 'NO SE REGISTRA DATOS LABORALES DE CONYUGE';
  }


  //documento html
  $html ='
  <!DOCTYPE html>
  <html lang="es">
    <head>
      <meta charset="utf-8">
      <title>Ficha de Inscripcion</title>
      <style>
        .clearfix:after {content:"";display:table;clear:both; }
        a {color:#0087C3;text-decoration:none;}
        body {position:relative;width:21cm;height:29.7cm;margin:0;color:#555555;background:white;font-size:13px;font-family:Arial;}
        table {border-collapse:collapse;border-spacing:0;}
        footer {color: #777777;width: 100%;height: 30px;position:absolute;bottom:0;border-top:1px solid #AAAAAA;padding: 8px 0;text-align: center;}
      </style>
    </head>
    <body>
      <main>
        <div style="position:relative;">
          <div style="float:left;width:100px;"><img src="img/logo.jpg" style="width:100px;"/></div>
          <div style="width:230px;float:right;">
            <h3 style="width:230px;background:#000;color:white;font-size:18px;border-radius:0.3em;margin:0;padding:0.3em;text-align:center;">FICHA DE INSCRIPCION</h3>
          </div>
        </div>
        <table border="0" cellspacing="0" cellpadding="0" style="margin:0px 0 10px 0;">
          <tbody>
            <tr style="">
              <td style="width:120px;color:#555555;text-align:right;">Codigo&nbsp;</td>
              <td style="border-bottom:1px solid black;width:100px;"></td>
            </tr>
            <tr style="">
              <td style="color:#555555;text-align:right;">Fecha de Ingreso&nbsp;</td>
              <td style="height:40px;">
                <table border="0" cellspacing="0" cellpadding="0" style="width:100px;height:40px;">
                  <tr>
                    <td style="border:1px solid black;color:white;">.</td>
                    <td style="border:1px solid black;color:white;">.</td>
                    <td style="border:1px solid black;color:white;">.</td>
                  </tr>
                </table>
              </td>
            </tr>
          </tbody>
        </table>
        <div class="clearfix">
          <div style="font-size:13px;">
            <div style="background:#555;color:white;width:150px;padding:3px;margin-bottom:5px;"><b>&nbsp;DATOS PERSONALES</b></div>
            <div>
              Apellidos: <b style="color:black;"><u>'.utf8_encode($rsPers["ap_paterno"]).' '.utf8_encode($rsPers["ap_materno"]).'</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              Nombres: <b style="color:black;"><u>'.utf8_encode($rsPers["nombres"]).'</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              '.utf8_encode($rsPers["doc"]).': <b style="color:black;"><u>'.utf8_encode($rsPers["DNI"]).'</u></b><br>
              Fecha de Nac.: <b style="color:black;"><u>'.utf8_encode($rsPers["fecha_nac"]).'</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              Lugar de Nac: <b style="color:black;"><u>'.utf8_encode($rsPers["lugar_nac"]).'</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              Sexo: <b style="color:black;"><u>'.utf8_encode($rsPers["sexo"]).'</u></b><br>
              Estado Civil: <b style="color:black;"><u>'.utf8_encode($rsPers["ecivil"]).'</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              Profesion: <b style="color:black;"><u>'.utf8_encode($rsPers["profesion"]).'</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              Ocupacion: <b style="color:black;"><u>'.utf8_encode($rsPers["ocupacion"]).'</u></b><br>
              Telefono: <b style="color:black;"><u>'.utf8_encode($rsPers["telefijo"]).'</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              Celular: <b style="color:black;"><u>'.utf8_encode($rsPers["celular"]).'</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              Email: <b style="color:black;"><u>'.utf8_encode($rsPers["email"]).'</u></b><br>

              Casa: <b style="color:black;"><u>'.utf8_encode($rsPers["tipovivienda"]).'</u></b><br>
              Departamento: <b style="color:black;"><u>'.utf8_encode($rsPers["region"]).'</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              Provincia: <b style="color:black;"><u>'.utf8_encode($rsPers["provincia"]).'</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              Distrito: <b style="color:black;"><u>'.utf8_encode($rsPers["distrito"]).'</u></b><br>
              Direccion: <b style="color:black;"><u>'.utf8_encode($rsPers["direccion"]).'</u></b><br>
            </div>

            <div style="background:#555;color:white;width:240px;padding:3px;margin:10px 0 5px 0;"><b>&nbsp;CENTRO DE TRABAJO O ESTUDIOS</b></div>
            <div>'.$htmlLabo.'</div>

            <div style="background:#555;color:white;width:220px;padding:3px;margin:35px 0 5px 0;"><b>&nbsp;DATOS DEL CONYUGE O TUTOR</b></div>
            <div>'.$htmlConyPers.'</div>
            <div style="background:#555;color:white;width:240px;padding:3px;margin:10px 0 5px 0;"><b>&nbsp;CENTRO DE TRABAJO O ESTUDIOS</b></div>
            <div>'.$htmlConyLabo.'</div>
          </div>
        </div>
        <div style="margin-top:20px;">
          <h3 style="background:#000;color:white;font-size:18px;border-radius:0.3em;padding:0.1em;width:350px;text-align:center;margin-left:23%;">DECLARACION JURADA DEL TITULAR</h3>
          <div style="text-align:justify;font-size:13px;">
            Certifico que las respuestas y declaraciones contenidas en esta Ficha de Inscripción son verídicas y se ajustan a la realidad y de no serlo,
            cualquier declaración falsa hecha por escrito, voluntaria o involuntaria, invalida la presente solicitud y libera de toda responsabilidad y
            compromiso a la <b>Cooperativa de Ahorro y Crédito GRUPO INVERSION SUDAMERICANO</b>, quedando la inscripcion nula y sin efecto.
          </div>
          <div style="text-align:right;">
            <div style="text-align:center;width:90px;margin-top:20px;float:right;">
              <div style="border:1px solid black;width:90px;height:100px;">&nbsp;
              </div>
              <i style="font-size:12px;">huella Digital</i>
            </div>
          </div>
          <div style="text-align:center;width:100%;">
            <div style="position:absolute;width:300px;margin-left:28%;">
              <div style="width:100%;border-bottom:1px solid #555555;"></div>
              <b style="font-size:12px;color:black;">'.utf8_encode($rsPers["ap_paterno"]." ".$rsPers["ap_materno"]." ".$rsPers["nombres"]).'</b><br>
              <b style="color:black;">'.utf8_encode($rsPers["doc"]).' Nº '.utf8_encode($rsPers["DNI"]).'</b>
            </div>
          </div>
        </div>
        <div style="page-break-before:always;">
          <h3 style="background:#000;color:white;font-size:18px;border-radius:0.3em;padding:0.1em;width:350px;text-align:center;margin-left:23%;">COMISION DE ADMINISTRACION</h3>
          <div style="text-align:center;width:100%;margin-top:150px;">
            <div style="position:absolute;width:250px;float:left;">
              <div style="width:100%;border-bottom:1px solid #555555;"></div>
              <i style="font-size:12px;color:#999999;">Presidente del Consejo de Administración</i><br>
            </div>
            <div style="position:absolute;width:250px;float:right;">
              <div style="width:100%;border-bottom:1px solid #555555;"></div>
              <i style="font-size:12px;color:#999999;">Secretario del Consejo de Administración</i><br>
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
