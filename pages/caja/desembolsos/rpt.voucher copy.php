<?php
  include_once("../../../includes/db_database.php");
  include_once("../../../includes/web_config.php");
  $movimID = $_REQUEST["movimID"];
  //bancos
  $qry = $db->query_all("select * from app_colegios where id=".$web->colegioID);
  $rs = reset($qry);
  $colegio_nombre = strtoupper($rs["nombre"]);
  $colegio_ruc = $rs["ruc"];

  //cabecera
  $params = [":movimID"=>$movimID];
  $sql = "select m.*,mv.nombre as tipomov,pg.nombre as tipopago,to_char(fecha,'DD/MM/YYYY HH24:MI:SS') as fechamov,fn_get_persona(p.tipo_persona, p.ap_paterno, p.ap_materno, p.nombres) AS alumno,p.nro_dui,upper(fn_get_letras(total)) as importe_letras1,upper(' con '||round(total-trunc(total),2)) as importe_letras2 from app_movim m join app_matriculas mt on m.id_matricula=mt.id join sis_mov mv on m.id_tipo_mov=mv.id join sis_tipos pg on m.id_tipo_pago=pg.id join personas p on mt.id_alumno=p.id where m.id=:movimID;";
  $qry = $db->query_all($sql,$params);
  if($qry) {
    $rs = reset($qry); 
    $mov_tipomov = strtoupper($rs["tipomov"]);
    $mov_tipopago = strtoupper($rs["tipopago"]);
    $mov_voucher = $rs["codigo"];
    $mov_fecha = $rs["fechamov"];
    $mov_alumno = $rs["alumno"];
    $mov_DNI = $rs["nro_dui"];
    $mov_total = $rs["total"];
    $mov_importeletras = trim($rs["importe_letras1"]).$rs["importe_letras2"];
  }

  //detalle
  $detalle = "";
  $importeLetras = "";
  $sql = "select d.*,p.nombre as producto from app_movim_det d join app_productos p on d.id_producto=p.id where id_movim=:movimID order by item";
  $qry = $db->query_all($sql,$params);
  if($qry) {
    foreach($qry as $rs){
      $detalle .= '<tr><td style="text-align:left;">'.$rs["producto"].'</td><td style="text-align:right;">'.number_format($rs["importe"],2,".",",").'</td></tr>';
    }
    $detalle .= '<tr><td style="text-align:right;vertical-align:bottom;">TOTAL&nbsp;&nbsp;</td><td style="text-align:right;vertical-align:bottom;height:20px;border-bottom:double;">'.number_format($mov_total,2,".",",").'</td></tr>';
  }
  
  //documento html
  $html ='
  <!DOCTYPE html>
  <html lang="en">
    <head>
      <meta charset="utf-8">
      <title>Voucher</title>
      <style>
        body { position: relative; margin: 0; padding: 0; color: #111; background: #FFFFFF; font-size: 10px; font-family: Arial; }
        .gridBordes th,.gridBordes td{border-bottom:1px solid #555555;border-left:1px solid #555555;}
        .clearfix:after { content: ""; display: table; clear: both; }
      </style>
    </head>
    <body>
    <main>
      <div class="clearfix">
        <div>
          <div style="font-size:10px;text-align:center;">
            <b><u>'.$colegio_nombre.'</u></b><br>
            <span style="font-size:9px;">RUC: '.$colegio_ruc.'</span>
          </div>
          <br><br>
          <table border="0" cellspacing="0" cellpadding="0" style="width:100%;font-size:7px;">
            <tbody>
              <tr>
                <td style="text-align:left;">Operacion</td>
                <td style="text-align:left;">'.$mov_tipomov.'</td>
              </tr>
              <tr>
                <td style="text-align:left;">Codigo</td>
                <td style="text-align:left;">'.$mov_voucher.'</td>
              </tr>
              <tr>
                <td style="text-align:left;">Fecha</td>
                <td style="text-align:left;">'.$mov_fecha.'</td>
              </tr>
              <tr>
                <td style="text-align:left;vertical-align:top;">Alumno</td>
                <td style="text-align:left;">'.$mov_alumno.'</td>
              </tr>
              <tr>
                <td style="text-align:left;vertical-align:top;">DNI</td>
                <td style="text-align:left;">'.$mov_DNI.'</td>
              </tr>
            </tbody>
          </table>
          <br>
          <table border="0" cellspacing="0" cellpadding="0" style="width:100%;font-size:8px;">
            <tbody>
              <tr style="">
                <th style="border-bottom:1px dotted black;font-weight:bold;text-align:left;">Detalle</th>
                <th style="border-bottom:1px dotted black;width:45px;font-weight:bold;text-align:right;">Importe</th>
              </tr>
              '.$detalle.'
            </tbody>
          </table>
          <span style="font-size:7px;">SON:... '.$mov_importeletras.' SOLES</span>
        </div>
      </div>
    </main>
    </body>
  </html>';
  $footer = '<p style="text-align:justify;font-size:6px;">
              <b>NO OLVIDE,</b> pague a tiempo y evite cargos
            </p>';

  include_once("../../../libs/pdf.php/vendor/autoload.php");
  $mpdf = new \Mpdf\Mpdf([
    'tempDir' => sys_get_temp_dir(),
    'format' => [55,100],
    'margin_left' => 1,
    'margin_right' => 1,
    'margin_top' => 2,
    'margin_bottom' => 1
  ]);
  $mpdf->WriteHTML($html);
  $mpdf->SetHTMLFooter($footer);
  $mpdf->Output('voucher.pdf','I');
  exit;
?>
