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
      <title>Ahorros - Anexo 01</title>
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
        <div style="font-size:12px;">
          <div style="text-align:center;text-decoration:underline;font-weight:bold;">
            ANEXO 01
          </div>
          <p style="text-align:justify">
            <b>LA HOJA DE RESUMEN,</b> forma parte integrante del contrato de depósito suscrito por las partes y tiene por finalidad establecer la tasa de interés que se retribuirá a EL (LA) SOCIO (A) por el depósito efectuado, establecer  las comisiones, gastos y obligaciones  sociales inherentes a su calidad de socio cooperativista, que serán de cuenta de EL (LA) SOCIO (A), asimismo, contiene un resumen de las obligaciones o facultades contractuales relevantes para las partes, de acuerdo al tipo de depósito contratado, conforme al siguiente detalle:
          </p>
          <table border="0" cellspacing="0" cellpadding="0" class="gridBordes" style="width:100%;font-size:12px;border-top:1px solid #555555;border-right:1px solid #555555;">
            <tbody>
              <tr style="">
                <th style="width:5%;font-weight:bold;">Nº</th>
                <th style="font-weight:bold;">Detalle</th>
                <th style="width:15%;font-weight:bold;">Soles</th>
                <th style="width:15%;font-weight:bold;">Dolares</th>
              </tr>
              <tr>
                <td style="text-align:left;">1</td>
                <td style="text-align:left;">Número de la cuenta</td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td style="text-align:left;">2</td>
                <td style="text-align:left;">Monto mínimo de apertura<br>
                  a) Ahorro Movible
                  b) Ahorro a Plazo Fijo
                </td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td style="text-align:left;">3</td>
                <td style="text-align:left;">Moneda y Monto del depósito efectuado </td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td style="text-align:left;">4</td>
                <td style="text-align:left;">Tasa de Interés compensatoria efectiva anual (360 días) (indicar si es Fija  o Variable)</td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td style="text-align:left;">5</td>
                <td style="text-align:left;">Monto Total de Intereses a Pagar al vencimiento en la Moneda del Depósito (Sólo para depósitos a plazo fijo)</td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td style="text-align:left;">6</td>
                <td style="text-align:left;">Fecha de Corte para el abono de Intereses</td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td style="text-align:left;">7</td>
                <td style="text-align:left;">Fecha de vencimiento del Depósito</td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td style="text-align:left;">8</td>
                <td style="text-align:left;">Consulta de Saldos en Oficina por el Jefe de Operaciones y/o Jefes Agencias</td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td style="text-align:left;">9</td>
                <td style="text-align:left;">
                  Tasa de Rendimiento Efectivo  Anual (TREA) a un año (360) días, asumiendo que no existen transacciones adicionales a la apertura de la cuenta. Ejemplo:<br>
                  Ahorros :     por       S/.   1,000.00………….…….%TEA;<br>
                  Ahorros:      por   US$.    1,000.00………………..%TEA;<br>
                  Plazo Fijo:   por      S/.    1,000.00………………..%TEA;<br>
                  Ahorros:      por    US$.   1,000.00………………..%TEA;<br>
                  El saldo que se requiere mantener en la cuenta de ahorros para no perder ni ganar al final del mes es: cero (S/.0.00) El saldo mínimo para obtener rendimiento (saldo mínimo de equilibrio) es: a partir de cualquier monto que mantenga EL (LA) SOCIO (A) en su cuenta.
                </td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td style="text-align:left;">10</td>
                <td style="text-align:left;">Exceptuado de pagar Impuesto a las Transacciones financieras ITF</td>
                <td>S/. 0.00</td>
                <td>US$ 0.00</td>
              </tr>
              <tr>
                <td style="text-align:left;">11</td>
                <td style="text-align:left;">Comisión por Transferencias  de cuentas a terceros (Bancos, Micro financieras no bancarias y/o instituciones financieras).</td>
                <td style="text-align:left;" colspan="2">Únicamente se trasladará a EL (LA) SOCIO (A) el importe que cobre la entidad bancaria y/o financiera de destino de la transferencia.</td>
              </tr>
              <tr>
                <td style="text-align:left;">12</td>
                <td style="text-align:left;">
                  <b>Disposiciones aplicables a cuentas de Depósito a Plazo Fijo.</b><br>
                  De producirse la cancelación del Depósito de Ahorro a Plazo Fijo establecido, la tasa efectiva anual a pagarse se reajustará aplicándose la ofertada para Ahorro Movible conforme al tarifario respectivo vigente por el periodo de permanencia efectiva del depósito.<br>En el Caso que la cancelación del Depósito a Plazo Fijo se efectúe antes del plazo establecido:<br>
                  a) Cancelación dentro de los 30 días calendario desde su apertura no se reconoce rendimiento (interés) alguno.<br>
                  b) cancelación dentro de un periodo mayor a 30 días calendario desde su apertura se reconoce la tasa de interés anual para cuenta de ahorro movible vigente (según tarifario).
                </td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td style="text-align:left;">13</td>
                <td style="text-align:left;">Comisiones, Gastos y Obligaciones sociales inherentes a la calidad de socio- cooperativista aplicables.</td>
                <td style="text-align:left;" colspan="2">Según tarifario de Comisiones, Gastos y Obligaciones Sociales para Operaciones Pasivas.</td>
              </tr>
            </tbody>
          </table>
          <p style="font-weight:bold;">
            RESUMEN DE ALGUNAS CONDICIONES CONTRACTUALES RELEVANTES PARA LAS PARTES:
          </p>
          <p style="text-align:justify;">
            <b>1. LA COOPERATIVA,</b> tiene la facultad de modificar unilateralmente las condiciones aplicables a los contratos de depósito, tasas de interés, comisiones, gastos, obligaciones sociales inherentes a la calidad de socio cooperativista o establecer  nuevas condiciones, las cuales serán puestas en conocimiento previo de EL (LA) SOCIO (A); en caso no resulte posible el uso de dicho medio de comunicación al correo electrónico de EL (LA) SOCIO (A); en caso no resulte posible el uso de dicho medio de comunicación directo, la cooperativa Informará de las modificaciones o establecimiento de nuevas condiciones a través de los diarios de mayor circulación a nivel local o nacional, según corresponda, así como en su página web y en los tarifarios de las agencias.
          </p>
          <p style="text-align:justify;">
            <b>2. LA COOPERATIVA</b> está expresamente facultada para que pueda proceder respecto a cualquiera de las cuentas de EL (LA) SOCIO (A) a cargar las comisiones, gastos y obligaciones sociales inherentes a la calidad de socio cooperativista ( de ser el caso) a EL (LA) SOCIO (A), establecidos en la presente cartilla de Información, así como cargar cualquier obligación directa o indirecta que adeude a LA COOPERATIVA, aún aquellas cedidas o  endosadas LA COOPERATIVA por terceros deudores de EL (LA) SOCIO (A) y/o las que éste haya garantizado; sea por capital, intereses, primas de seguro, fondo de equipamiento, gastos, tributos, comisiones y aportaciones sociales (ésta última aplicables de ser el caso, y únicamente referidas a aquellas establecidas y que resulten aplicable al caso específico de EL (LA) SOCIO (A).
          </p>
          <p style="text-align:justify;">
            <b>3.</b> En caso de incumplimiento de contrato por retiro anticipado de los ahorros, EL SOCIO  deberá presentar una solicitud firmada por él, en la cual requiera retirar anticipadamente sus ahorros, luego de la presentación de dicha solicitud LA COOPERATIVA tendrá el plazo de 5 (cinco) días hábiles para proceder con la devolución del dinero al SOCIO.<br>
            Yo: <b>'.$nombres.' '.$apellidos.'</b>,Declaro haber leído la presente cartilla de Información y que he sido instruido(s) acerca de los alcances y significado de los términos y condiciones establecidos en dichos documentos, habiendo sido absueltas y aclaradas a mi (nuestra) satisfacción todas las consultas efectuadas y/o deudas por lo que suscribo (suscribimos)  el presente documento en duplicado y con pleno y exacto conocimiento de las condiciones establecidas en dichos documentos, así me (nos) fueron entregados el contrato y la cartilla respectiva.<br> En señal de conformidad, las partes suscriben este documento en la ciudad de Arequipa a los <b>'.$dia.'</b> días del mes de <b>'.$mes.'</b> de <b>'.$anio.'</b>.
          </p>
          <p style="font-weight:bold;text-decoration:underline;text-align:center;margin-top:30px;">
            CONSTANCIA DE RECEPCIÓN DE CARTILLA DE INFORMACIÓN
          </p>
          <p style="text-align:justify;margin-bottom:400px;">
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
  $footer = '';

  $mpdf = new \Mpdf\Mpdf([]);
  $mpdf->WriteHTML($html);
  $mpdf->SetHTMLFooter($footer);
  $mpdf->Output();
  exit;
?>
