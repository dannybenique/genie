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
  $fecha = $rs["dia"]." de ".$rs["mes"]." de ".$rs["anio"];

  //documento html
  $html ='
  <!DOCTYPE html>
  <html lang="en">
    <head>
      <meta charset="utf-8">
      <title>Contrato de Ahorros</title>
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
        <div style="font-size:11px;">
          <div style="text-align:center;text-decoration:underline;font-weight:bold;">
            CONTRATO DE OPERACIONES Y SERVICIOS FINANCIEROS DE CUENTAS DE AHORRO, DEPÓSITO A PLAZO FIJO
          </div>
          <p style="text-align:justify">
            Conste por el presente documento las Condiciones Generales y especiales Aplicables a los contratos de Operaciones y Servicios Financieros de cuentas de Ahorro Movible, Depósito a plazo fijo, que celebran de una parte la <b>COOPERATIVA DE AHORRO Y CRÉDITO GRUPO INVERSIÓN SUDAMERICANO con RUC N° 20601390419,</b> inscrita en la partida N° 11341715 del Registro de Personas Jurídicas de los Registros Públicos de Arequipa, a quien en adelante se le denominará LA COOPERATIVA, debidamente representada por los funcionarios que suscriben al final del presente documento; y de la otra parte la(s) persona(s) que suscriben el presente documento, en adelante “EL(LA) SOCIO(A)”, cuyos datos de identificación y domicilio se consignan al final del presente documento; conforme a las cláusulas siguientes:
          </p>
          <p style="text-align:justify">
            El presente contiene las condiciones generales y especiales aplicables al o los depósitos y servicios complementarios que EL (LA) SOCIO (A) contrate en la fecha de suscripción del presente y los que contrate en el futuro en forma individual o conjuntamente y/o otras personas, y que LA COOPERATIVA de conformidad con las normas regulatorias aplicables, sus políticas y reglamentos acuerde otorgarle.
          </p>
          <p style="text-align:justify">
            Cada uno de los depósitos y/o servicios complementarios contratados por EL (LA) SOCIO (A) y/o que contrate en el futuro se rigen/regirán además por las condiciones particulares contenidas en el anexo 01, y el registro de cuenta o solicitud de apertura de cuenta, que se emitirán al momento de la apertura de cuenta.
          </p>
          <p style="text-align:justify">
            LA COOPERATIVA conforme a las disposiciones del T.U.O de la Ley General de Cooperativas- D.S. N° 074-90-TR-, el Reglamento de las Cooperativas no autorizadas a operar con recursos del público- Resolución SBS N° 540-1999- y Ley General del Sistema Financiero y del Sistema de Seguros y Orgánica de la Superintendencia de banca, Seguros y AFP (en adelante “ Ley de Bancos”)- Ley No. 26702-, ofrece a sus socios (personas naturales y jurídicas) los servicios de Depósito de Ahorros y Depósitos a Plazo Fijo, los cuales se regirán de acuerdo a los mandatos de las referidas normas legales y sus modificatorias, el Código de Protección y Defensa del Consumidor- Ley N° 29571, la Ley Complementaria a la Ley de Protección al Consumidor en Materia de Servicios Financieros- Ley N°28587, la Resolución SBS N° 1765-2005 Reglamento de Transparencia de Información y otros dispositivos legales aplicables a la contratación específica con los Usuarios del Sistema Financiero; y a las estipulaciones del presente documento, que se encuentran homologadas a las normas legales mencionadas.
          </p>
          <p style="font-weight:bold;">
            I.CONDICIONES GENERALES APLICABLES A LAS CUENTAS DE AHORROS Y  DEPOSITO A PLAZO FIJO:
          </p>
          <p style="font-weight:bold;">
            DE LA APERTURA DE LA CUENTA:
          </p>
          <p style="text-align:justify;">
            <b>Cláusula Primera: </b> La cuenta de Depósito se abrirá a solicitud expresa de EL (LA) SOCIO (A), mediante un depósito mínimo que será fijado por la COOPERATIVA. Al abrir una Cuenta de Depósitos, LA COOPERATIVA asignará a la misma un número de cuenta e inscribirá al titular de la misma en el “Registro de Cuenta” correspondiente, el cual forma parte del presente contrato, consignando toda la información necesaria para su identificación y manejo.
          </p>
          <p style="text-align:justify;">
            <b>Cláusula Segunda: </b> En el caso de personas naturales, la cuenta se abrirá previa identificación y presentación del Documento Nacional de Identidad, cuando se trate de Personas Jurídicas previa presentación del documento de su constitución y la acreditación de sus representantes legales o apoderados con facultades suficientes para operar cuentas de depósitos, y además requisitos exigidos por LA COOPERATIVA.
          </p>
          <p style="text-align:justify;">
            <b>Cláusula Tercera: </b> La cuentas de depósitos podrán ser: i) Individuales; ii) mancomunadas y iii) Solidarias: para los ii) y iii), requieren la intervención de todos los titulares que registren sus firmas para disponer de sus fondos, para el i) es suficiente la intervención del titular que tenga registrada su firma para disponer de los fondos, para todas las cuentas se tendrá que Identificar con el documento Nacional de Identidad.
          </p>
          <p style="text-align:justify;">
            <b>Cláusula Cuarta: </b> LA COOPERATIVA, se reserva el derecho de aceptar o denegar la solicitud de apertura de una Cuenta de Depósitos, así como verificar la veracidad de los datos proporcionados por EL (LA) SOCIO (A).
          </p>
          <p style="font-weight:bold;">
            DE LOS MOVIMIENTOS EN LA CUENTA:
          </p>
          <p style="text-align:justify;">
            <b>Cláusula Quinta: </b> En caso de personas naturales. EL (LA) SOCIO (A) podrá efectuar sus operaciones en forma personal o por intermedio de terceros. Para dicho efecto, podrá autorizar a un tercero, para realizar operaciones de cobros, retiros o cancelaciones de cuenta a su nombre, mediante poder y bajo los siguientes criterios:
            <ul style="list-style-type:lower-latin;">
              <li>Para operaciones hasta media (1/2) U.I.T vigente a la fecha de la operación: Se exigirá por operaciones la presentación de carta poder con firma legalizada notarialmente.</li>
              <li>Para operaciones desde más media  (1/2) hasta (02) U.I.T vigente a la fecha de la operación: Se exigirá por operación la representación de Poder Notarial fuera de Registro.</li>
              <li>Para operaciones  mayores a dos (02) U.I.T vigente a la fecha de la operación: se exigirá la presentación de Poder por escritura Pública, inscrito en Registros Públicos y vigencia de poder de una antigüedad no mayor de 30 días de la fecha en la que se realiza la operación.</li>
            </ul>
          </p>
          <p style="text-align:justify;">
            EL (LA) SOCIO (A) se obliga a comunicar por escrito y bajo responsabilidad toda modificación o revocatoria relativa a los poderes y facultades otorgadas a sus representantes o terceros no teniendo LA COOPERATIVA responsabilidad alguna por las disposiciones de dinero de la cuenta de depósitos que efectuaren personas cuyo poder haya sido revocado o invalidado y que no haya sido oportunamente comunicado a LA COOPERATIVA.
          </p>
          <p style="text-align:justify;">
            <b>Cláusula Sexta: </b> En caso de Personas Jurídicas, EL (LA) SOCIO (A) efectuará sus operaciones a través de sus representantes legales o apoderados debidamente acreditados y con facultades suficientes para operar cuentas de depósitos. EL (LA) SOCIO (A) se obliga a comunicar por escrito y bajo responsabilidad toda modificación o revocatoria relativa a los poderes y facultades otorgadas  a sus presentantes o apoderados, las que para surtir efecto ante LA COOPERATIVA requerirán de la presentación de los instrumentos pertinentes debidamente inscritos en Registros Públicos o formalidad legal respectiva. Toda comunicación sin estos requisitos se considerará inválida para LA COOPERATIVA. Mientras LA COOPERATIVA concluya con la revisión de los documentos presentados para acreditar a los representantes o proceder a su cambio, la cuenta  será bloqueada temporalmente.  En caso de duda o conflicto sobre  legitimidad de la representación con que se opere las cuentas de EL (LA) SOCIO (A),  LA COOPERATIVA podrá sin responsabilidad  alguna, suspender  la ejecución de toda orden o instrucción, hasta que estos se resuelvan, aceptando EL (LA) SOCIO (A) en estos casos, que LA COOPERATIVA proceda, si lo estima conveniente a bloquear la cuenta de depósitos. El control de los poderes y facultades especiales como límites de disposición, y otras facultades especiales conferidas corresponden a EL (LA) SOCIO (A), limitándose LA COOPERATIVA a verificar únicamente su calidad de tales y si actúan a sola firma o en forma conjunta.
          </p>
          <p style="text-align:justify;">
            <b>Clausula sétima</b> Todas las operaciones que se efectúen en la cuenta de depósitos serán registradas en forma electrónica y constarán en vouchers y/o hojas móviles expedidas por medios mecánicos o electrónicos, estos documentos se emitirán al momento de realizarse la operación y se entregarán a la persona que  la realice y sólo servirán para acreditar la existencia de la operación. Para cualquier reclamo vinculado con una operación realizada en cualquiera de las cuentas de ahorros de LA COOPERATIVA será necesario presentar el registro emitido por LA COOPERATIVA.
          </p>
          <p style="text-align:justify;">
            <b>Cláusula  octava</b> Salvo en los casos de cancelación, el monto de cada operación para depósitos o retiros no podrá ser inferior al que periódicamente fije LA COOPERATIVA, y que aparecerá publicado en avisos que se colocarán en cada una de sus oficinas y agencias que atienden ahorros, así como en su página web.
          </p>
          <p style="text-align:justify;">
            <b>Cláusula novena</b> EL (LA) SOCIO (A) tiene la obligación de guardar reserva del número de cuenta asignado a su depósito. En ese sentido, LA COOPERATIVA podrá recibir y depositar en la cuenta de EL (LA) SOCIO (A) cualquier suma de dinero a través de terceros que presenten el número de identificación de la cuenta, presumiéndose que dicha operación ha sido autorizada por EL (LA) SOCIO (A).
          </p>
          <p style="text-align:justify;">
            <b>Cláusula Décima</b> Los depósitos pueden efectuarse únicamente en dinero en efectivo. Asimismo, EL (LA) SOCIO (A) constituye  garantía mobiliaria a favor de LA COOPERATIVA sobre sus fondos, depósitos, bienes y valores existentes en sus cuentas o en poder de LA COOPERATIVA, con la única excepción de los fondos intangibles conforme a ley, en garantía de cualquier obligación de su cargo y con preferencia frente a cualquier otro acreedor, dentro de los alcances de los art. 132.9, 171 y 172 de la Ley N° 26702 y arts. 3 y 4 de la Ley N° 28677; estando facultada LA COOPERATIVA a ejercer las acciones previstas en la Ley N° 28677, de ser el caso.
          </p>
          <p style="text-align:justify;">
            <b>Cláusula decima primera</b> En el caso de las personas naturales, el retiro de los depósitos se efectuará en cualquiera de las ventanillas de atención ubicados en las oficinas, agencias o puntos de atención de LA COOPERATIVA, siendo  facultad de esta última solicitar la presentación del Documento Oficial  de identidad. En caso de cuentas mancomunadas el retiro procederá únicamente a través de ventanilla previa  identificación a través del documento oficial de Identidad de todos los titulares.
          </p>
          <p style="text-align:justify;">
            <b>Clausula décimo segunda</b> En el caso de las personas jurídicas estas efectuarán sus retiros a través de sus representantes o apoderados debidamente registrados, y se podrán efectuar en las ventanillas de cualquiera de las oficinas o agencias de LA COOPERATIVA mediante la presentación del documento oficial de identidad del o los representantes o apoderados con los poderes vigentes.
          </p>
          <p style="text-align:justify;">
            <b>Clausula  décima  tercera</b> Las cuentas de depósito podrán ser usadas por EL (LA) SOCIO (A) para recaudar fondos o efectuar pagos diversos, en cuyo caso EL (LA) SOCIO (A) deberá comunicar previamente a LA COOPERATIVA tal finalidad, la cual de acceder a lo solicitado suscribirá con el EL (LA) SOCIO (A) el contrato de servicio correspondiente. En ese sentido, LA COOPERATIVA se reserva el derecho de suspender o terminar toda relación contractual cuando las cuentas de depósitos se utilicen para dicha finalidad sin contar con el contrato de servicio respectivo.
          </p>
          <p style="text-align:justify;">
            <b>Clausula décimo cuarta</b> EL (LA) SOCIO (A) podrá realizar operaciones e impartir instrucciones, empleando medios electrónicos, facsímil o similares, así como solicitar se le remitan sus estados de cuenta a través de estos medios.  El empleo de los mismos está sujeto a la solicitud previa de EL (LA) SOCIO (A) y a la autorización y condiciones que establezca LA COOPERATIVA, los que serán comunicados a EL (LA) SOCIO (A) a través de folletos, carteles en Agencias u oficinas de LA COOPERATIVA y /o en su página web.
          </p>
          <p style="font-weight:bold;">
            DEL CIERRE O CANCELACIÓN Y BLOQUEO DE LA CUENTA DE DEPÓSITOS:
          </p>
          <p style="text-align:justify;">
            <b>Cláusula décima quinta:</b> LA COOPERATIVA podrá cerrar o cancelar las cuentas de EL (LA) SOCIO (A) cuando se presenten cualquiera de los siguientes supuestos: a) Cuando lo solicité EL (LA) SOCIO (A), en el caso de cuentas solidarias bastará la declaración de voluntad de uno de los titulares, en el caso de cuentas mancomunadas se requerirá la manifestación de voluntad de todos los titulares de la cuenta; b) Cuando LA COOPERATIVA así lo determine sin expresión de causa, dando aviso en el domicilio señalado con un plazo  de anticipación de 15 días calendario antes de proceder a la cancelación o cierre de la cuenta; c) En caso de fallecimiento de EL (LA) SOCIO (A) cuando LA COOPERATIVA tome conocimiento  de ello en forma fehaciente o a solicitud de los herederos, debidamente acreditados, salvo que se trate de cuentas solidarias, las que se pondrán mantener vigentes a nombre de los demás titulares, pudiendo los sucesores debidamente acreditados subrogarse en el lugar del fallecido, sin que esto modifique la responsabilidad de los sucesores por las deudas de su causante según Ley; d) por tratarse de una cuenta inactiva sin calendario antes de proceder a la cancelación o cierre de la cuenta;  e) Por mandato judicial o de autoridad competente, f) cierre previsto por ley, dando aviso en el domicilio señalado con un plazo de anticipación de 15 días calendario antes de proceder a la cancelación o cierre de la cuenta, g) cuando se comuniquen telefónicamente o escrita remitida al domicilio señalado por EL (LA) SOCIO (A) con un plazo de anticipación de 15 días calendario  antes de proceder a la cancelación o cierre de la cuenta, h) cuando sin autorización de LA COOPERATIVA, la cuenta se utilice para colectas, recaudaciones o depósitos hechos a favor de terceras personas, i) se realicen operaciones que puedan perjudicar a LA COOPERATIVA o EL (LA) SOCIO (A) dé información falsa a LA COOPERATIVA o incumpla con cualquiera de sus obligaciones conforme a este contrato, dando aviso en el domicilio señalado con un plazo de anticipación de 15 días calendario antes de proceder a la cancelación o cierre de la cuenta.<br> Asimismo LA COOPERATIVA informará a las autoridades competentes sobre operaciones comprendidas en la Sección Quinta de la Ley general del Sistema Financiero- Ley N° 26702- o las normas sobre legitimación de Activos, debiendo EL (LA) SOCIO (A) explicar  y documentar a LA COOPERATIVA, la suficiencia económica y legal de sus operaciones. LA COOPERATIVA podrá cerrar las cuentas de EL (LA) SOCIO (A),  en cualquier momento por decisión de negocios  o pérdida de confianza y por el sólo mérito de una comunicación previa dirigida al domicilio registrado de EL (LA) SOCIO (A). Las cuentas se cerrarán transcurridos 15 días después de la comunicación.<br>
            EL (LA) SOCIO (A) faculta a LA COOPERATIVA para que, sin necesidad de previo aviso, pueda proceder con el bloqueo de la cuenta en los siguientes casos:
            <ul style="list-style-type:lower-latin;">
              <li>Por apertura incompleta( bloqueo de retiros).</li>
              <li>Por mandato judicial ( bloqueo total o por el importe señalando en la carta y/o emitido por la autoridad jurisdiccional competente).</li>
              <li>Por otro motivo justificado con autorización de la gerencia, jefatura de operaciones y/o Jefe de Agencia/apoderado.</li>
              <li>Por requerimiento de EL (LA) SOCIO (A) titular de la cuenta.</li>
              <li>Cuando advierta indicios de operaciones inusuales, irregulares o sospechosos de acuerdo a las normas de la materia o a fin de resguardar los intereses económicos del propio EL (LA) SOCIO (A) y la COOPERATIVA.</li>
            </ul>
            En los supuestos de bloqueo de cuenta antes señalados- con excepción del previsto en el inciso d) – LA COOPERATIVA informará dicha situación a EL (LA) SOCIO (A) en forma inmediata y posterior al bloqueo de la cuenta, mediante comunicación telefónica o escrita dirigida a su domicilio señalado en el presente  contrato.
          </p>
          <p style="text-align:justify;">
            <b>Clausula décima sexta:</b> La cancelación o cierre y bloqueo de las cuentas a solicitud de EL (LA) SOCIO (A) procede en cualquiera de las agencias u oficinas de LA COOPERATIVA.<br> Clausula decima sétima En todos  los supuestos  de cancelación o cierre y bloqueo, LA COOPERATIVA previamente podrá hacer uso de las facultades establecidas en la cláusula  25 de las condiciones generales. Como consecuencia de la cancelación o cierre y/o  de la cuenta de depósitos, EL (LA) SOCIO (A) asume total responsabilidad de dicha acción, liberando a LA COOPERATIVA de cualquier responsabilidad por el incumplimiento de las obligaciones de EL (LA) SOCIO (A) que pueda generarse como consecuencia del cierre y/o bloqueo de la cuenta o de la comunicación de cierre y/o bloqueo que emita LA COOPERATIVA.
          </p>
          <p style="font-weight:bold;">
            DE LAS CUENTAS INACTIVAS:
          </p>
          <p style="text-align:justify;">
            <b>Cláusula décimo octava:</b> LA COOPERATIVA declarará como cuentas inactivas las que se encuentren dentro de los siguientes supuestos, no obstante lo cual continuarán  generando intereses con las tasas vigentes a favor de EL (LA) SOCIO (A), comisiones y gastos a favor de LA COOPERATIVA: a) las cuentas que no registren movimientos durante 12 meses; b) las cuentas que durante  seis meses hayan tenido saldo inferior al mínimo establecido por LA COOPERATIVA. En la oportunidad en que se presente el titular o su representante en el caso de personas jurídicas a efectuar una operación, estas cuentas serán activadas, no se considera como operación la solicitud de información o de extracto de las cuentas.
          </p>
          <p style="text-align:justify;">
            <b>Cláusula décima novena:</b> LAS PARTES acuerdan que LA COOPERATIVA pagará EL (LA) SOCIO (A) una tasa de interés compensatorio por el tiempo efectivo de permanencia de su depósito, según el periodo de capitalización establecido de acuerdo al tipo de cuenta contratada; asimismo, las PARTES, acuerdan que LA COOPERATIVA cobrará a EL (LA) SOCIO (A) las comisiones respectivas por los servicios que le proporcione, los gastos que LA COOPERATIVA  incurra con terceros derivados de las operaciones pasivas, los gastos que LA COOPERATIVA incurra con terceros derivados de las operaciones pasivas, los que serán trasladados a EL (LA) SOCIO (A) y las obligaciones sociales inherentes a la calidad de socio cooperativista. La tasa de interés, el periodo de capitalización, las comisiones y los gastos referidos aparecen detallados en el anexo adjunto al presente denominado “ANEXO 01” el cual forma parte integrante del presente contrato, y ha sido  puesto en conocimiento de EL (LA) SOCIO(A) en forma previa y es suscrito en forma simultánea a este contrato, En ningún caso la terminación anticipada de los servicios o resolución del contrato dará lugar a la devolución de los conceptos por comisión o gastos ya incurridos o cobrados, Así mismo, LA COOPERATIVA podrá cobrar primas de seguro a EL (LA) SOCIO(A) siempre que este las hubiera solicitado o consentido en forma previa, expresa y escrita.
          </p>
          <p style="font-weight:bold;">
            DE LA FACULTAD DE LA COOPERATIVA DE MODIFICAR UNILATERALMENTE LAS CONDICIONES DEL PRESENTE CONTRATO, TASA DE INTERES, COMISIONES, GASTOS Y OBLIGACIONES SOCIALES INHERENTES A LA CALIDAD DE SOCIO COOPERATIVISTA O ESTABLECER NUEVAS CONDICIONES:
          </p>
          <p style="text-align:justify;">
            <b>Clausula vigésima:</b> LAS PARTES acuerdan expresamente que las condiciones establecidas en el presente contrato, así como las tasas de interés, comisiones, gastos y obligaciones sociales inherentes a la calidad de socio cooperativista detalladas en la “Anexo 01” podrán ser variadas o modificados por LA COOPERATIVA en forma unilateral, cuando las condiciones del mercado y/o LA COOPERATIVA  así lo determine de acuerdo a las políticas de depósitos.
          </p>
          <p style="text-align:justify;">
            <b>Clausula vigésima primera:</b> Asimismo LAS PARTES acuerdan que LA COOOPERATIVA podrá unilateralmente  durante la ejecución del presente contrato establecer nuevos conceptos por comisiones, gastos, obligaciones sociales inherentes a la calidad de socio cooperativista o en general nuevas condiciones contractuales.
          </p>
          <p style="text-align:justify;">
            <b>Clausula vigésima segunda:</b> en cualquiera de los casos referidos en las clausulas 20 y 21 de estas condiciones generales, para surtir efecto y ser oponibles deberán ser previamente comunicadas a EL(LA) SOCIO(A), con el plazo de anticipación previsto en las normas de protección al consumidor y las que establezca la Superintendencia de Banca y Seguros. Luego de transcurrido el plazo señalado en la comunicación,  en la fecha que se señale de manera expresa en la comunicación, dichas modificaciones o nuevas condiciones entraran en vigencia automática por lo que la permanencia o continuación de EL (LA) SOCIO(A) en el uso de las cuentas o servicios significa su total aceptación.
          </p>
          <p style="text-align:justify;">
            <b>Clausula vigésima tercera:</b> en el caso de modificaciones a la tasa de interés, comisiones, gastos u obligaciones sociales inherentes a la calidad de socio cooperativista que impliquen condiciones más favorables para EL (LA) SOCIO (A), estas se aplicaran de manera inmediata, por lo que no es aplicable lo señalado en la cláusula anterior.
          </p>
          <p style="font-weight:bold;">
            DE LAS FORMAS DE COMUNICACION PREVIA DE LAS MODIFICACIONES DE LAS CONDICIONES DEL CONTRATO O ESTABLECIMIENTO DE NUEVAS CONDICIONES:
          </p>
          <p style="text-align:justify;">
            <b>Clausula vigésimo cuarta:</b> Para la comunicación a EL(LA) SOCIO(A) de toda variación o modificación de los conceptos y condiciones establecidas en el (anexo 1) o el establecimiento de nuevas condiciones vinculadas con dichos aspectos, deberá contener clara y expresa las modificaciones o adiciones respectivas y se efectuara, siempre que sea posible, mediante comunicación al correo electrónico de EL(LA) SOCIO(A); en caso no resulte posible el uso de dicho medio de comunicación directo LA COOPERATIVA informara de las modificaciones  o establecimiento de nuevas condiciones a través de los diarios de mayor circulación a nivel local o nacional, según corresponda, así como en su página web y en los tarifarios de las oficinas y/o agencia de LA COOPERATIVA.<br> La comunicación de la modificación de otro tipo de condiciones contractuales o el establecimiento de  nuevas condiciones diferentes a las establecidas en el primer párrafo de la presente clausula, se efectuara a través de los medios de comunicación masiva señalados en el párrafo anterior.
          </p>
          <p style="font-weight:bold;">
            FACULTADES DE LA COOPERATIVA
          </p>
          <p style="text-align:justify;">
            <b>Clausula vigésima quinta:</b> LA COOPERATIVA queda expresamente facultada por EL (LA) SOCIO(A) para que, sin necesidad de previo aviso, pueda proceder respecto a cualquiera de sus cuentas o valores a:
            <ul style="list-style-type:lower-latin;">
              <li>Cargar los conceptos establecidos en el Anexo 01 o los que posteriormente hayan modificados o incorporados conforme a las cláusulas 22 y 24.</li>
              <li>Cargar cualquier obligación directa o indirecta que adeude a LA COOPERATIVA, aun aquellos cedidas o endosadas a LA COOPERATIVA por terceros deudores de EL(LA) SOCIO(A) Y/O las que este haya garantizado en calidad de fiador solidario y/o bajo cualquier responsabilidad otro título, sea por capital, interés compensatorios, primas de seguro, fondo de equipamiento, gastos, tributos, comisiones y aportaciones sociales (estas últimas aplicables de ser el caso y únicamente referidas a aquellas establecidas y que resulten aplicable al caso específico de EL(LA) SOCIO(A), Asimismo podrá retener y aplicar a sus adeudos cualquier suma o valor que tenga  en su poder o reciba a favor de EL(LA) SOCIO(A) por cualquier concepto y en cualquiera de sus oficinas y agencias. Estos cargos y/o compensaciones podrá hacerlos LA COOPERATIVA, aun en casos de encontrarse EL (LA) SOCIO(A) concursado, en liquidación o fallecimiento, conforme el artículo 132, inciso 11 de la ley 26702, sin perjuicio del procedimiento legal preestablecido.</li>
              <li>abonar o cargar las sumas que resulten de errores de cualquier tipo y/o que resulten necesarios para regularizar las mismas. Los abonos o cargos registrados indebidamente, serán objeto de corrección mediante extorno, o por medio de notas de abono o débito, sin que se requiera para ello de notificación previa a EL (LA) SOCIO(A) o de instrucciones o aceptación expresa del mismo, no generándole ninguna otra responsabilidad para LA COOPERATIVA. Todo esto será de conocimiento de EL (LA) SOCIO(A) mediante su estado de cuenta o comunicación  directa que se emita por escrito.</li>
              <li>Abrir a nombre de EL (LA) SOCIO(A) cuentas de depósito en moneda nacional o extranjera, para evidenciar los abonos correspondientes a créditos a su favor. Para el efecto, LA COOPERATIVA le remitirá el estado de cuenta, el presente Contrato y Anexo 01 respectivo, para su firma y regularización.</li>
            </ul>
          </p>
          <p style="text-align:justify;">
            <b>Clausula vigésima sexta:</b> EL (LA) SOCIO(A) autoriza a LA COOOPERATIVA a informar a las autoridades respectivas de la realización de cualquier operación que a su solo criterio y calificación constituya una transacción sospechosa o tenga las características a que se refiere la sección quinta de la ley Nª 26702, conforme a la ley Nª 27693 referida al sistema de prevención del lavado de activos. En tales casos. EL (LA) SOCIO(A) libera a LA COOPERATIVA de toda responsabilidad o reclamo de orden penal, civil o administrativo; liberándola asimismo de toda reserva que le imponen las normas del secreto bancario.
          </p>
          <p style="text-align:justify;">
            <b>Clausula vigésima séptima:</b> en situaciones de casos fortuito o fuerza mayor y en situaciones que no resulten imputables a LA COOPERATIVA por encontrarse fuera de su ámbito de control EL (LA) SOCIO(A) libera de responsabilidad a esta por daños o perjuicios que eventualmente pudiera sufrir por falta, suspensión o interrupción de uno o todos sus servicios en forma temporal, en el caso que dichas circunstancias ocasionen el erróneo o indebido registro de los abonos o cargos en sus cuentas y sean advertidas por EL (LA) SOCIO(A), este deberá ser comunicado de inmediato y por escrito; y una vez demostrada la veracidad del reclamo, se procederá a la corrección del error mediante extornos, notas de cargo o abono, según corresponda.<br> Se considerarán como causas de fuerza mayor o caso fortuito, sin que la enumeración sea limitativa, las siguientes:
            <ul style="list-style-type:lower-latin;">
              <li>La interrupción del sistema de cómputo, red de teleproceso local de telecomunicaciones.</li>
              <li>Falta de fluido eléctrico.</li>
              <li>Terremotos, incendios, inundaciones y otros similares.</li>
              <li>Actos y consecuencias de vandalismo, terrorismo y conmoción civil.</li>
              <li>Huelgas y paros</li>
              <li>Actos y consecuencias imprevisibles debidamente justificadas por LA COOPERATIVA.</li>
              <li>Suministro y abastecimientos a sistemas y canales de distribución de productos y servicios.</li>
            </ul>
            Para la aplicación  de los incisos “a” y “g” la ausencia de responsabilidad de LA COOPERATIVA  sólo operará en caso fortuito o de fuerza mayor.
          </p>
          <p style="font-weight:bold;">
            SOLICITUD DE INFORMACIÓN EL (LA) SOCIO(A):
          </p>
          <p style="text-align:justify;">
            <b>Cláusula vigésima Octava:</b> EL (LA) SOCIO(A) de la cuenta tiene derecho a solicitar los extractos de sus cuentas ya sean totales o parciales, para lo cual firmará un cargo de recepción. En caso de cuentas mancomunadas o solidarias, bastará que uno de los titulares de la cuenta lo solicite para ejercer el derecho establecido en la presente cláusula.
          </p>
          <p style="text-align:justify;">
            <b>Cláusula Vigésimo Novena:</b> EL (LA) SOCIO(A) autoriza en forma previa, expresa e indefinida a LA COOPERATIVA a efecto de que ésta pueda remitir al correo electrónico de EL (LA) SOCIO(A) publicidad respecto de los servicios o contratos que LA COOPERATIVA ofrece al público en general, aunque dicha publicidad no tenga relación con el servicio contratado por medio del presente documento.
          </p>
          <p style="text-align:justify;">
            <b>Cláusula Trigésima:</b> EL (LA) SOCIO(A) señala como domicilio el indicado en este contrato, donde se le cursarán las comunicaciones que fueren necesarias, salvo las comunicaciones que se efectúen a través de medios  masivos de comunicación conforme a lo establecido en la cláusula 24 del presente. EL (LA) SOCIO(A) comunicará toda variación de domicilia con un plazo de anticipación de 15 días calendario, plazo dentro del cual  surtirán efecto todas las comunicaciones que se remitan al anterior domicilio. Asimismo EL (LA) SOCIO(A), se somete a la competencia de los jueces de la ciudad donde se suscribe el presente instrumento.
          </p>
          <p style="font-weight:bold;">
            II. CONDICIONES ESPECIALES APLICABLES A LAS CUENTAS DE AHORROS, DEPÓSITO A PLAZO FIJO:
          </p>
          <p style="font-weight:bold;">
            a. CONDICIONES DE LA CUENTA DE AHORRO:
          </p>
          <p style="text-align:justify;">
            <b>Cláusula Primera:</b> La cuenta de ahorros representa las obligaciones contraídas por LA COOPERATIVA provenientes de las imposiciones de dinero por un periodo indeterminado de tiempo. En esta modalidad de cuenta de depósitos EL (LA) SOCIO(A) podrá disponer de sus depósitos cuando lo solicite. LA COOPERATIVA atenderá los retiros, previa solicitud, salvo casos especiales en los que reserva la facultad de fijar un plazo para que proceda, o se acuerde un número limitado de operaciones, dentro de un plazo determinado. La identificación de los retiros se realizará conforme a las normas generales sobre capacidad y representación.
          </p>
          <p style="text-align:justify;">
            <b>Cláusula Segunda:</b> La duración del presente  contrato es a plazo indeterminado, bastando para su resolución la cancelación o cierre de la cuenta por cualquiera de las causales establecidas en la cláusula décimo quinta de las condiciones generales.
          </p>
          <p style="text-align:justify;">
            <b>Cláusula Tercera:</b> Si el titular del depósito de ahorro fuera un menor de edad o un analfabeto, los retiros podrán ser efectuados por sus representantes legalmente autorizados, y bajo responsabilidad plena de éstos, según lo establecido  en los siguientes incisos: a) Si el titular fuera menor de edad, los retiros podrán ser hechos a través de sus representantes legales autorizados y sólo procederá en la medida que exista una Autorización Judicial para disponer, total o parcialmente, de los fondos del menor de edad, quienes serán iidentificados  con su respectiva Partida de nacimiento o Documento nacional de identidad. Para el caso de analfabetos o incapacitados para firmar, EL (LA) SOCIO (A) deberá traer  su firmante a ruego el cual firmará en el comprobante  de la transacción y colocará al lado izquierdo su huella digital; EL (LA) SOCIO (A) pondrá su huella al lado derecho de la firma del firmante a ruego.
          </p>
          <p style="text-align:justify;">
            <b>Cláusula Cuarta:</b> LA COOPERATIVA podrá establecer, a su solo criterio, un número ilimitado de retiros dentro de un plazo determinado, sin costo para EL (LA) SOCIO (A). Los retiros adicionales podrán tener un costo expresamente señalado en el tarifario de LA COOPERATIVA. El número  de operaciones  libres así como el costo de operaciones adicionales  de ser el caso, se encontrarán establecidos en el Anexo 01 que se anexa al presente Contrato.
          </p>
          <p style="text-align:justify;">
            <b>Clausula Quinta:</b> Aplíquese en forma complementaria las condiciones generales antes señaladas, las normas establecidas en las disposiciones legales vigentes y los reglamentos y procedimientos de la COOPERATIVA.
          </p>
          <p style="font-weight:bold;">
            b. DEPÓSITOS A PLAZO FIJO
          </p>
          <p style="text-align:justify;">
            <b>Clausula sexta:</b> El depósito  de ahorro  a plazo fijo, generará una constancia  denominada “CERTIFICADO DE DEPÓSITO A PLAZO FIJO- CDP”. Este documento constituye un valor materializado no negociable razón por la cual no puede ser transferido por su titular bajo ninguna modalidad.
          </p>
          <p style="text-align:justify;">
            <b>Cláusula  séptima:</b> Los depósitos a plazo Fijo, son aquellas sumas de dinero entregadas por EL (LA) SOCIO (A) a LA COOPERATIVA con el fin expreso que esta última las conserve en su poder por un periodo de tiempo determinado, depósito que podrá ser renovado y que será remunerado a la tasa de interés que LA COOPERATIVA tenga establecida para las operaciones de este tipo en el momento de la apertura del depósito, así como en el momento de cada renovación. Los intereses serán capitalizados o pagados según  las indicaciones del EL (LA) SOCIO (A) con la frecuencia y periodicidad que LA COOPERATIVA tenga establecida al efecto. Los intereses de los depósitos a plazo fijo se pagarán  teniendo en cuenta el plazo establecido para ello.
          </p>
          <p style="text-align:justify;">
            <b>Cláusula octava:</b> El monto mínimo de apertura no podrá ser inferior al que fije LA COOPERATIVA, según el tarifario vigente, contenida en el Anexo 01 adjunta, salvo en los casos de remanentes por cancelaciones de créditos con garantía de cuentas de ahorro a plazo fijo u otros.
          </p>
          <p style="text-align:justify;">
            <b>Cláusula novena:</b> LA COOPERATIVA procederá a renovar de manera automática el depósito, en las mismas condiciones originalmente pactadas respecto al plazo, y con la tasa de interés efectiva anual de acuerdo a tarifario vigente, en caso el plazo pactado haya vencido sin que EL (LA) SOCIO (A) se haya apersonado a retirar el dinero depositado más los respectivos intereses; si la tasa de interés hubiera variado será puesta en conocimiento de EL (LA) SOCIO (A) por los mecanismos previstos por la ley.
          </p>
          <p style="text-align:justify;">
            <b>Cláusula décima:</b> EL (LA) SOCIO (A) se compromete a no realizar ni abonos durante el plazo pactado para el depósito. El retiro parcial o total de un depósito antes del vencimiento del plazo pactado facultará a LA COOPERATIVA a pagar tan sólo la tasa de interés que tenga para los depósitos de ahorros, por el periodo que pudiera haber transcurrido desde la última capitalización de los mismos.
          </p>
          <p style="text-align:justify;">
            <b>Cláusula décimo primera:</b> La tasa de interés será fijada por LA COOPERATIVA según el plazo efectivo del depósito, conforme consta en el anexo 01 que se adjunta.
          </p>
          <p style="text-align:justify;">
            <b>Cláusula décimo segunda:</b> Si la cuenta de ahorro de plazo fijo es cancelada antes del vencimiento del plazo pactado con EL (LA) SOCIO (A), la tasa de interés será la que corresponda a las cuentas de ahorro movible vigente.
          </p>
          <p style="text-align:justify;">
            <b>Cláusula décimo tercera:</b> Queda expresamente establecido que si EL (LA) SOCIO (A) de la cuenta mantuviera algún crédito (s) pendiente(s) con LA COOPERATIVA o si tuviera la condición de Fiador Solidario de algún socio de LA cooperativa que mantenga crédito(s) pendiente(s) de pago y/o situación de morosidad, y no fuera pagado en la oportunidad y en plazo pactado, LA COOPERATIVA podrá compensar aquella obligación pendiente de pago directamente de la cuenta  de plazo fijo o de cualquier otra cuenta que mantenga el titular como saldo, para la cual este otorga autorización a LA COOPERATIVA mediante la presente cláusula.
          </p>
          <p style="text-align:justify;">
            <b>Cláusula Décima Cuarta:</b> Estas condiciones pueden ser modificadas por LA COOPERATIVA conforme  a Ley, lo que será puesto en conocimiento en EL (LA) SOCIO (A) mediante aviso en alguna de las formas y dentro del plazo que señala la Ley; y las establecidas en el presente contrato.
          </p>
          <p style="text-align:justify;">
            <b>Cláusula Décimo Quinta:</b> En caso de incumplimiento de contrato por retiro anticipado de los ahorros, EL SOCIO deberá presentar una solicitud firmada por él, en la cual requiera retirar anticipadamente sus ahorros, luego de la presentación de dicha solicitud LA COOPERATIVA tendrá el plazo de 5 (cinco) días hábiles para proceder con la devolución del dinero al SOCIO.
          </p>
          <p style="text-align:justify;">
            <b>Cláusula Décimo Sexta:</b> EL (LA) SOCIO(A), declara  expresamente que previamente  a la celebración del presente contrato ha recibido toda la información necesaria acerca de las condiciones generales y específicas aplicables al tipo de cuenta contratada, tasas de interés, comisiones y gastos y toda la información  necesaria acerca del uso y operatividad de la misma. Asimismo, declara haber recibido y conocer el formulario contractual conteniendo todas las condiciones establecidas en el presente contrato.
          </p>
          <p style="font-weight:bold;">
            DECLARACIÓN DE EL (LA) SOCIO(A) DE HABER RECIBIDO TODA LA INFORMACIÓN NECESARIA PREVIA A LA CELEBRACIÓN DEL PRESENTE CONTRATO:
          </p>
          <p style="text-align:justify;margin-bottom:50px;">
            <b>Cláusula Décimo Séptima:</b> EL (LA) SOCIO(A) declara expresamente que previamente a la celebración del presente contrato ha recibido toda la información necesaria acerca de las condiciones generales y específicas aplicables a cada tipo de cuenta, tasas de interés, comisiones y gastos y toda la información necesaria acerca del uso y operatividad de la misma. Asimismo, declara haber recibido y conocer el formulario contractual  conteniendo todas  las condiciones establecidas en el presente contrato.<br> EL (LA) SOCIO(A) además declara haber leído, previamente a su suscripción, el presente contrato y que ha sido instruido acerca de los alcances y significado de los términos y condiciones establecido en el mismo, habiendo sido absueltas y aclaradas a satisfacción sus consultas y/o dudas, por lo que declara tener pleno y exacto conocimiento de las condiciones establecidas en dicho documento.
          </p>
          <p style="text-align:right;margin-bottom:330px;">
            Arequipa, '.$fecha.'
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
