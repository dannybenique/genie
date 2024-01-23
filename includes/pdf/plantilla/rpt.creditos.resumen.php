<?php
  include_once("../../../libs/pdf.php/mpdf/autoload.php");
  include_once("../../db_database.php");
  $docDNI = $_GET["nroDNI"];

  //personas
  $qryPers = $db->select("select * from dbo.vw_personas where DNI='".($docDNI)."'");
  $rs = $db->fetch_array($qryPers);
  $apellidos = utf8_encode($rs["ap_paterno"])." ".utf8_encode($rs["ap_materno"]);
  $nombres = utf8_encode($rs["nombres"]);
  $tipoDNI = utf8_encode($rs["doc"]);
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
      <title>HOJA DE RESUMEN DE CREDITO</title>
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
        <div style="font-size:10px;">
          <div style="text-align:center;font-weight:bold;font-size:18px;">
            HOJA DE RESUMEN DE CREDITO (SOCIO)
          </div>
          <p style="text-align:justify">
            El presente documento forma parte integrante del contrato de crédito como acto cooperativo, suscrito por las partes, y tiene por finalidad establecer las condiciones del crédito (socio), y los aspectos más relevantes, tasa de interés compensatorio, tasa de interés moratorio, comisiones, gastos y el resumen de las condiciones contractuales para las partes.
          </p>
          <table border="0" cellspacing="0" cellpadding="0" class="gridBordes" style="width:100%;font-size:10px;border-top:1px solid #555555;border-right:1px solid #555555;">
            <tr style="">
              <th style="font-weight:bold;background:orange;" colspan="2">DATOS DEL CREDITO</th>
            </tr>
            <tr><td style="text-align:left;width:40%">1. Nombre del Socio</td>
                <td style="text-align:left;"></td></tr>
            <tr><td style="text-align:left;">2. Codigo del Socio</td>
                <td style="text-align:left;"></td></tr>
            <tr><td style="text-align:left;">3. Documento de Identidad</td>
                <td style="text-align:left;"></td></tr>
            <tr><td style="text-align:left;">4. Tipo de Credito</td>
                <td style="text-align:left;"></td></tr>
            <tr><td style="text-align:left;">5. Nº de Pagare</td>
                <td style="text-align:left;"></td></tr>
            <tr><td style="text-align:left;">6. Moneda y Monto del crédito aprobado</td>
                <td style="text-align:left;"></td></tr>
            <tr><td style="text-align:left;">7. Monto Desembolsado</td>
                <td style="text-align:left;"></td></tr>
            <tr><td style="text-align:left;">8. Destino del Crédito</td>
                <td style="text-align:left;"></td></tr>
            <tr><td style="text-align:left;">9. Plazo del Crédito</td>
                <td style="text-align:left;"></td></tr>
            <tr><td style="text-align:left;">10. Fecha de Vencimiento</td>
                <td style="text-align:left;"></td></tr>
            <tr><td style="text-align:left;">11. Frecuencia de pago</td>
                <td style="text-align:left;"></td></tr>
            <tr><td style="text-align:left;">12. Tasa de interés compensatoria efectiva anual(TEA 360 días)</td>
                <td style="text-align:left;"></td></tr>
            <tr><td style="text-align:left;">13. Monto total de interés compensatorio</td>
                <td style="text-align:left;"></td></tr>
            <tr><td style="text-align:left;">14. Saldo capital</td>
                <td style="text-align:left;"></td></tr>
            <tr><td style="text-align:left;">15. Tasa Efectiva Anual Moratoria (TEA 360 días)</td>
                <td style="text-align:left;"></td></tr>
            <tr><td style="text-align:left;">16. Tasa de costo efectiva anual (TCEA)</td>
                <td style="text-align:left;"></td></tr>
          </table>
          <p style="font-weight:bold;">
            RESUMEN DE ALGUNAS CONDICIONES CONTRACTUALES RELEVANTES PARA LAS PARTES:
          </p>
          <p style="text-align:justify;">
            18.	En caso de incumplimiento en el pago de la fecha prevista (frecuencia de pago), EL SOCIO(a) será reportado a las Centrales de Riesgo de la SBS y a aquellas constituidas de acuerdo a ley, con la que COOPAC GRUPO INVERSION SUDAMERICANO, tenga suscrito un convenio o contrato con dicho objeto, con calificación que corresponda, de conformidad con el reglamento para la evaluación y clasificación del deudor y la exigencia de provisiones vigente.
          </p>
          <p style="text-align:justify;">
            19.	LOS SOCIO (a) podrá solicitar una constancia de cancelación del crédito detallado en el presente documento solo por única vez, a partir de la segunda constancia se pagará una comisión, cuyo importe aparece en el tarifario de comisiones.
          </p>
          <p style="text-align:justify;">
            20.	En caso EL SOCIO(a) que efectúe un pago en exceso que no sea una amortización o un pago anticipado del crédito, dicho exceso les será devuelto por la COOPAC GRUPO INVERESION SUDAMERICANO.
          </p>

          <p style="text-align:justify;margin-bottom:300px;">
            Los abajo firmantes, declaramos haber recibido una copia de la presente cartilla de Información y del Tarifario de Comisiones, Gastos y Obligaciones Sociales para Operaciones Pasivas, así como haber sido instruido sobre los contenidos y las cláusulas estipuladas en los mismos.
          </p>
          <table border="0" cellspacing="0" cellpadding="0" style="width:100%;">
            <tbody>
              <tr style="">
                <td style="width:30%;text-align:center;font-size:10px;">
                  <hr/>
                  SOCIO(A)(S) TITULAR DE LA CUENTA
                </td>
                <td style="width:40%;"></td>
                <td style="width:30%;text-align:center;font-size:10px;">
                  <hr/>
                  TESTIGO A RUEGO/<br>APODERADO(A) DEL SOCIO(A)<br><br>
                </td>
              </tr>
              <tr style="">
                <td style="text-align:left;vertical-align:top;font-size:10px;">
                  Apellidos: <b>'.$apellidos.'</b><br>
                  Nombres: <b>'.$nombres.'</b><br>
                  '.$tipoDNI.': <b>'.$nroDNI.'</b><br>
                  Direccion:<b>'.$domicilio.'</b>
                </td>
                <td style=""></td>
                <td style="text-align:left;vertical-align:top;font-size:10px;">
                  Apellidos:.........................................................<br>
                  Nombres:.........................................................<br>
                  DNI:...................................................................<br>
                  Direccion:.........................................................<br>
                  .........................................................................
                </td>
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
