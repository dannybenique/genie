<?php
  include_once("../../../libs/pdf.php/mpdf/autoload.php");
  include_once("../../db_database.php");
  $docDNI = $_GET["nroDNI"];
  $ciudad = $_GET["ciudad"];

  //personas
  $qryPers = $db->select("select * from dbo.vw_personas where DNI='".($docDNI)."'");
  $rs = $db->fetch_array($qryPers);
  if($rs["id_doc"]==502) { $persona = utf8_encode($rs["nombres"]); } else { $persona = utf8_encode($rs["persona"]); }
  $tipoDNI = utf8_encode($rs["doc"]);
  $nroDNI = $docDNI;
  $fechanac = $rs["fecha_nac"];
  $domicilio = utf8_encode($rs["direccion"]);
  $distrito = utf8_encode($rs["distrito"]);
  $provincia = utf8_encode($rs["provincia"]);
  $region = utf8_encode($rs["region"]);
  $estadocivil = utf8_encode($rs["ecivil"]);

  //fecha
  $qry = $db->select("select day(getdate()) as dia,nombre as mes,year(getdate()) as anio from sis_meses where ID=MONTH(getdate())");
  $rs = $db->fetch_array($qry);
  $fecha = $rs["dia"]." de ".$rs["mes"]." de ".$rs["anio"];

  //documento html
  $html ='
  <!DOCTYPE html>
  <html lang="en">
    <head>
      <meta charset="utf-8">
      <title>Solicitud de Ingreso de Socio</title>
      <style>
        .clearfix:after {content:"";display:table;clear:both; }
        a {color:#0087C3;text-decoration:none;}
        body {position:relative;width:21cm;height:29.7cm;margin:0;color:#555555;background:white;font-size:14px;font-family:Arial;}
        footer {color: #777777;width: 100%;height: 30px;position:absolute;bottom:0;border-top:1px solid #AAAAAA;padding: 8px 0;text-align: center;}
        #logo {float:left;margin-top:8px;}
        #logo img {height:70px;}
        table {width: 100%;border-collapse: collapse;border-spacing: 0;margin-bottom: 20px;}
        table th {white-space: nowrap;font-weight: normal;}
        table td {text-align: right;}
        table td h3{color: #57B223;font-size: 1.2em;font-weight: normal;margin: 0 0 0.2em 0;}
        table .no {color: #FFFFFF;font-size: 1.6em;background: #57B223;}
        table .desc {text-align: left;}
        table .unit {background: #DDDDDD;}
        table .qty { }
        table .total {background: #57B223;color: #FFFFFF;}
        table td.unit, table td.qty, table td.total {font-size: 1.2em;}
        table tbody tr:last-child td {border: none;}
        table tfoot td {padding: 10px 20px;background: #FFFFFF;border-bottom: none;font-size: 1.2em;white-space: nowrap;border-top: 1px solid #AAAAAA;}
        table tfoot tr:first-child td {border-top: none;}
        table tfoot tr:last-child td {color: #57B223;font-size: 1.4em;border-top: 1px solid #57B223;}
        table tfoot tr td:first-child {border: none;}
      </style>
    </head>
    <body>
      <main>
        <div style="position:relative;">
          <div style="float:left;width:100px;"><img src="img/logo.jpg" style="width:100px;"/></div>
          <div style="width:310px;float:right;">
            <h3 style="width:310px;background:#000;color:white;font-size:16px;border-radius:0.3em;margin:0;padding:0.3em;text-align:center;">SOLICITUD DE INGRESO DE SOCIO(A)</h3>
          </div>
        </div>
        <table border="0" cellspacing="0" cellpadding="0" style="width:270px;magin:10px 0 10px 0;">
          <tbody>
            <tr style="">
              <td style="width:30px;text-align:center;font-size:18px;"><b>Nº</b></td>
              <td style="border:1px solid black;height:30px;"></td>
            </tr>
          </tbody>
        </table>
        <div class="clearfix">
          <div style="font-size:14px;">
            <div><b>Señor:</b></div>
            <div>
              Presidente del consejo de Administración.<br>
              <b>Cooperativa de Ahorro y Crédito GRUPO INVERSION SUDAMERICANO.</b><br>
              Av. Ejercito Nº 212 - Yanahuara | Arequipa<br>
              <b>Presente.-</b>
            </div>
            <br>
            <p style="text-align:justify">Por medio de la presente manifiesto mi voluntad de asociarme a la
            <b style="color:black;">Cooperativa de Ahorro y Crédito GRUPO INVERSION SUDAMERICANO</b> en calidad de Socio, teniendo
            conocimiento tanto de los servicios que presta como de las responsabilidades, obligaciones y
            derechos que asisten al socio, según la información recibida en la charla previa que fue brindada por los funcionarios de la Cooperativa.</p>
          </div>
          <div style="position:absolute;">
            <h3><u>DATOS PERSONALES:</u></h3>
            <div style="height:25px;"><span style="color:#777777;">Apellidos y Nombres / Razon Social:</span> <b style="color:black;">'.$persona.'</b></div>
            <div style="height:25px;"><span style="color:#777777;">Documento</span> <b style="color:black;">'.$tipoDNI.": ".$nroDNI.'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#777777;">Fecha de Nac.</span> <b style="color:black;">'.$fechanac.'</b></div>
            <div style="height:25px;"><span style="color:#777777;">Estado Civil:</span> <b style="color:black;">'.$estadocivil.'</b></div>
            <div style="height:25px;"><span style="color:#777777;">Domicilio:</span> <b style="color:black;">'.$domicilio.'</b></div>
            <div style="height:25px;"><span style="color:#777777;">Distrito:</span> <b style="color:black;">'.$distrito.'</b></div>
            <div style="height:25px;"><span style="color:#777777;">Provincia:</span> <b style="color:black;">'.$provincia.'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#777777;">Departamento:</span> <b style="color:black;">'.$region.'</b></div>
          </div>
          <p></p>
          <div style="font-size:14px;">
            <p style="text-align:justify">
              Así mismo; <b>DECLARO BAJO JURAMENTO:</b><br><br>
              No tener antecedentes penales, judiciales, ni policiales, ni tener procesos en curso por lavado de activos de la UF/LAT, ni estar en ninguna otra causal
              de impedimento de asociarme. Manifiesto conocer que esta declaracion tiene caracter legal y en caso de comprobárseme falsedad habré incurrido en un delito
              contra la fe pública, falsificación de Documentos, (Artículo 427º del Código Pneal, en concordancia con el Artículo IV inciso 1.7) y contra el "Principio
              de Presunción de Veracidad" del Título Preliminar de la Ley de Procedimiento Administrativo General, Ley Nº 27444.</p>
            <p style="text-align:justify">
              Ratifico la veracidad de lo declarado, sometiéndome de no ser asi, a las corrrespondientes acciones administrativas y de Ley.
            </p>
          </div>
          <div style="text-align:right">
            <p>En la ciudad de '.$ciudad.' el '.$fecha.'</p>
          </div>
        </div>
        <div style="text-align:center;width:100%;margin-top:100px;">
          <div style="position:absolute;width:200px;float:left;">
            <div style="width:100%;border-bottom:1px solid #555555;"></div>
            <span style="font-size:12px;color:#555;">APROBADO EN CONSEJO<br> DE ADMINISTRACION</span><br>
          </div>
          <div style="position:absolute;width:200px;float:right;">
            <div style="width:100%;border-bottom:1px solid #555555;"></div>
            <span style="font-size:12px;color:#555;">'.$tipoDNI." Nº ".$nroDNI.'</span><br>
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
