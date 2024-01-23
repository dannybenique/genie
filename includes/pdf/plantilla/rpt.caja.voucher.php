<?php
  include_once("../../../libs/pdf.php/mpdf/autoload.php");
  include_once("../../db_database.php");
  $voucherID = $_REQUEST["voucherID"];

  //datos Cooperativa
  $qryCoop = $db->select("select * from dbo.fn_CoopAC(1)");
  $rs = $db->fetch_array($qryCoop);
  $coopRUC = $rs["RUC"];

  //datos voucher
  $Moneda = "SOLES";
  $num_trans = "01115";
  $agencia = "Ag. Mariscal";
  $usuarioID = "2";
  $fecha = "07/07/2020";
  $hora = "17:07";
  $servicio = "SUD Consumo Rapido";
  $num_pres = "97831";
  $socio = "03-0129 - LINARES RIOS, CARLOS JESUS";
  $socioDNI = "29727655";

  //===============================================================================================================================================
  //documento html
  $html ='
  <!DOCTYPE html>
  <html lang="en">
    <head>
      <meta charset="utf-8">
      <title>voucher</title>
      <style>
        .clearfix:after {content:"";display:table;clear:both; }
        body {position:relative;width:21cm;height:29.7cm;margin:0;color:black;background:white;font-size:11px;font-family:Arial;}
        table {width:100%;border-collapse:collapse;border-spacing:0;}
      </style>
    </head>
    <body>
      <main>
        <table border="0" cellspacing="0" cellpadding="0">
          <tr style="">
            <td style="vertical-align:top;">
              <table border="0" cellspacing="0" cellpadding="0">
                <tr><td style="width:180px;"><b>R.U.C.: '.$coopRUC.'</b></td>
                    <td style="width:80px;font-weight:bold;">'.$Moneda.'</td>
                    <td style="width:80px;text-align:right;">Nro. '.$num_trans.'</td></tr>
              </table>
              <table border="0" cellspacing="0" cellpadding="0">
                <tr><td style="width:30px;">Ag.:</td>
                    <td style="width:180px;">'.$agencia.' - '.$usuarioID.'</td>
                    <td style="width:80px;text-align:right;">'.$fecha.' '.$hora.'</td></tr>
                <tr><td style="">Serv.:</td>
                    <td style="" colspan="2">'.$servicio.'</td></tr>
                <tr><td style="vertical-align:top;">Socio:</td>
                    <td style="" colspan="2">'.$socio.'<br>DNI:'.$socioDNI.'</td></tr>
              </table>
              <table border="0" cellspacing="0" cellpadding="0" style="margin-top:10px;margin-bottom:10px;">
                <tr style="border:1px solid black;">
                  <td style="font-weight:bold;padding-left:2px;" colspan="2">DETALLE</td>
                  <td style="width:70px;font-weight:bold;text-align:right;">Pagos</td>
                  <td style="width:70px;font-weight:bold;text-align:right;padding-right:2px;">Saldos</td>
                </tr>
                <tr>
                  <td>Amortizacion Prestamo</td>
                  <td style="width:20px;">0</td>
                  <td style="text-align:right;">795.15</td>
                  <td style="text-align:right;">231.13</td>
                </tr>
                <tr>
                  <td>Int Simple Cobr Prestamos</td>
                  <td style="width:20px;">0</td>
                  <td style="text-align:right;">38.01</td>
                  <td style="text-align:right;">0.00</td>
                </tr>
                <tr>
                  <td colspan="2" style="text-align:right;">TOTAL</td>
                  <td style="text-align:right;">840.00</td>
                  <td></td>
                </tr>
              </table>
              Son: OCHOCIENTOS CUARENTA con 00/100 SOLES<br>
              Cuota Pagada: 10/12<br>
              Prox. Venc.: 19/04/2018 12:00:00
            </td>
            <td style="width:30px;"></td>
            <td style="vertical-align:top;">
            <table border="0" cellspacing="0" cellpadding="0">
              <tr><td style="width:180px;"><b>R.U.C.: '.$coopRUC.'</b></td>
                  <td style="width:80px;font-weight:bold;">'.$Moneda.'</td>
                  <td style="width:80px;text-align:right;">Nro. '.$num_trans.'</td></tr>
            </table>
            <table border="0" cellspacing="0" cellpadding="0">
              <tr><td style="width:30px;">Ag.:</td>
                  <td style="width:180px;">'.$agencia.' - '.$usuarioID.'</td>
                  <td style="width:80px;text-align:right;">'.$fecha.' '.$hora.'</td></tr>
              <tr><td style="">Serv.:</td>
                  <td style="" colspan="2">'.$servicio.'</td></tr>
              <tr><td style="vertical-align:top;">Socio:</td>
                  <td style="" colspan="2">'.$socio.'<br>DNI:'.$socioDNI.'</td></tr>
            </table>
            <table border="0" cellspacing="0" cellpadding="0" style="margin-top:10px;margin-bottom:10px;">
              <tr style="border:1px solid black;">
                <td style="font-weight:bold;padding-left:2px;" colspan="2">DETALLE</td>
                <td style="width:70px;font-weight:bold;text-align:right;">Pagos</td>
                <td style="width:70px;font-weight:bold;text-align:right;padding-right:2px;">Saldos</td>
              </tr>
              <tr>
                <td>Amortizacion Prestamo</td>
                <td style="width:20px;">0</td>
                <td style="text-align:right;">795.15</td>
                <td style="text-align:right;">231.13</td>
              </tr>
              <tr>
                <td>Int Simple Cobr Prestamos</td>
                <td style="width:20px;">0</td>
                <td style="text-align:right;">38.01</td>
                <td style="text-align:right;">0.00</td>
              </tr>
              <tr>
                <td colspan="2" style="text-align:right;">TOTAL</td>
                <td style="text-align:right;">840.00</td>
                <td></td>
              </tr>
            </table>
            Son: OCHOCIENTOS CUARENTA con 00/100 SOLES<br>
            Cuota Pagada: 10/12<br>
            Prox. Venc.: 19/04/2018 12:00:00
            </td>
          </tr>
        </table>
      </main>
    </body>
  </html>';

  $mpdf = new \Mpdf\Mpdf([]);
  $mpdf->AddPageByArray([
    'margin-left' => '7mm',
    'margin-right' => '5mm',
    'margin-top' => '20mm',
    'margin-bottom' => 0,
]);
  $mpdf->WriteHTML($html);
  $mpdf->Output();
  exit;
?>
