<?php
  include_once("../../../libs/pdf.php/mpdf/autoload.php");
  include_once("../../db_database.php");
  $docDNI = $_GET["nroDNI"];

  //personas
  $qryPers = $db->select("select p.*,s.codigo from dbo.vw_personas p,tb_socios s where p.ID=s.id_persona and p.DNI='".($docDNI)."'");
  $rs = $db->fetch_array($qryPers);
  $apellidos = utf8_encode($rs["ap_paterno"])." ".utf8_encode($rs["ap_materno"]);
  $nombres = utf8_encode($rs["nombres"]);
  $tipoDNI = utf8_encode($rs["doc"]);
  $codigo = utf8_encode($rs["codigo"]);
  $nroDNI = $docDNI;
  $domicilio = utf8_encode($rs["direccion"]);

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
        .gridBordes th,.gridBordes td{border-bottom:1px solid #555555;border-left:1px solid #555555;}
        .clearfix:after { content: ""; display: table; clear: both; }

        body { position: relative; width: 21cm; height: 29.7cm; margin: 0; color: #111; background: #FFFFFF; font-size: 14px; font-family: Arial; }
        a { color: #0087C3; text-decoration: none; }
        header { padding: 10px 0; margin-bottom: 20px; }
        h2.name { font-size: 1.4em; font-weight: normal; margin: 0; }
        footer { color: #777777; width: 100%; height: 30px; position: absolute; bottom: 0; border-top: 1px solid #AAAAAA; padding: 8px 0; text-align: center; }
        table { width: 100%; border-collapse: collapse; border-spacing: 0; margin-bottom: 20px; }
        table th { white-space: nowrap; font-weight: normal; }
        table td { text-align: right; }
        table td h3{ color: #57B223; font-size: 1.2em; font-weight: normal; margin: 0 0 0.2em 0; }
        table .no { color: #FFFFFF; font-size: 1.6em; background: #57B223; }
        table .desc { text-align: left; }
        table .unit { background: #DDDDDD; }
        table .qty { }
        table .total { background: #57B223; color: #FFFFFF; }
        table td.unit, table td.qty, table td.total { font-size: 1.2em; }
        table tbody tr:last-child td { border: none; }
        table tfoot td { padding: 10px 20px; background: #FFFFFF; border-bottom: none; font-size: 1.2em; white-space: nowrap; border-top: 1px solid #AAAAAA; }
        table tfoot tr:first-child td { border-top: none; }
        table tfoot tr:last-child td { color: #57B223; font-size: 1.4em; border-top: 1px solid #57B223; }
        table tfoot tr td:first-child { border: none; }

        #logo { float: left; margin-top: 8px; }
        #logo img { height: 70px; }
        #company { float: right; text-align: right; }
        #details { margin-bottom: 50px; }
        #client { padding-left: 6px; border-left: 6px solid #0087C3; float: left; }
        #client .to { color: #777777; }

        #invoice { float: right; text-align: right; }
        #invoice h1 { color: #0087C3; font-size: 2.4em; line-height: 1em; font-weight: normal; margin: 0  0 10px 0; }
        #invoice .date { font-size: 1.1em; color: #777777; }

        #thanks{ font-size: 2em; margin-bottom: 50px; }
        #notices{ padding-left: 6px; border-left: 6px solid #0087C3; }
        #notices .notice { font-size: 1.2em; }
      </style>
    </head>
    <body>
    <main>
      <div class="clearfix">
        <div style="font-size:16px;">
          <div style="text-align:center;text-decoration:underline;">
            <h2>CARTA DE AUTORIZACION</h2>
          </div>
          <p style="text-align:justify">
            <b>Señor Presidente del Consejo de Administración<br>Cooperativa de Ahorro y Crédito Grupo Inversión Sudamericano</b>
          </p>
          <p style="font-weight:bold;">
            Presente.-
          </p>
          <p style="font-weight:bold;">
            Asunto: Autorización de disposición.
          </p>
          <p style="text-align:justify;margin-top:35px;">
            Por medio de la presente, me dirijo a su representada el suscrito '.$nombres.' '.$apellidos.' identificado con '.$tipoDNI.' Nº '.$nroDNI.'; es socio con código '.$codigo.', de su entidad, por lo que <b><u>AUTORIZO</u></b>, a la Cooperativa de Ahorro y Crédito Grupo Inversión Sudamericano, disponer de mis aportes voluntarios y ahorros, para el pago de mis obligaciones de crédito y otros que mantenga como socio en dicha cooperativa, ello con el fin de garantizar el pago de las deudas pendientes contraídas, hasta la cancelación en su totalidad de mis obligaciones.
          </p>
          <p style="text-align:justify;margin-top:35px;">
            Agradeciéndole se sirva disponer a quien corresponda, sin otro particular aprovecho la oportunidad para reiterarle las consideraciones de mi estima personal. Dicha carta de autorización tendrá efecto de declaración jurada según lo establecido en la Ley Nº 27444.
          </p>
          <p style="text-align:center;margin-top:50px;">
            Atentamente,
          </p>
          <table border="0" cellspacing="0" cellpadding="0" style="width:100%;margin-top:250px;">
            <tbody>
              <tr style="">
                <td style="width:35%;text-align:center;font-size:10px;"></td>
                <td style="width:30%;text-align:center;font-size:10px;">
                  <hr/>
                  SOCIO(A)(S) TITULAR DE LA CUENTA
                </td>
                <td style="width:35%;text-align:center;font-size:10px;"></td>
              </tr>
              <tr style="">
                <td></td>
                <td style="text-align:left;vertical-align:top;font-size:10px;">
                  Apellidos: <b>'.$apellidos.'</b><br>
                  Nombres: <b>'.$nombres.'</b><br>
                  '.$tipoDNI.': <b>'.$nroDNI.'</b><br>
                  Direccion:<b>'.$domicilio.'</b>
                </td>
                <td></td>
              </tr>
            </tbody>
          </table>
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
