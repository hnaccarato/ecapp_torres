
<html>
<head>
<link rel="important stylesheet" href="chrome://messagebody/skin/messageBody.css">
<title><?php echo $this->my_style->get_name()?></title>
</head>
<body>
<div dir="ltr">
<div class="gmail_quote">
<div bgcolor="#FAFAFA" lang="ES" link="blue" vlink="purple">
<div class="m_6300125436136123137WordSection1">

<div align="center">
<table class="table" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100.0%;background:#fafafa">
<tbody>
<tr>
<td valign="top" style="padding:15.0pt 15.0pt 15.0pt 15.0pt">
<div align="center">
<table class="table" border="1" cellspacing="0" cellpadding="0" width="600" style="width:450.0pt;background:white;border:solid #dddddd 1.0pt">
<tbody>
<tr>
<td valign="top" style="border:none;padding:0cm 0cm 0cm 0cm">
<div align="center">
<table class="table" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100.0%">
    <tbody>
        <tr>
            <td style="border:none;border-bottom:solid #dddddd 1.0pt;background-color:<?=$this->my_style->get_color()?>;padding:0cm 0cm 0cm 0cm">
                <p class="MsoNormal">
                    <b>
                        <span style="font-size:13.0pt;font-family:&quot;Helvetica&quot;,&quot;sans-serif&quot;color:#505050">
                            <a href="https://buildingapps.com.ar/app/" target="_blank">
                                <span style="color:#1f5d8c;font-weight:normal;text-decoration:none;">
                                    <h3 style="margin-top:20px;">
                                         <img src="<?php echo $this->my_style->get_logo()?>"  width="192" height="40" class="logo img-responsive" data-pin-nopin="true">
                                    </h3>
                                </span>
                            </a>
                        </span>
                    </b>
                </p>
            </td>
        </tr>

    </tbody>
</table>
</div>
</td>
</tr>
<tr>
<td valign="top" style="border:none;padding:0cm 0cm 0cm 0cm">
<div align="center">
<table class="table" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100.0%;background:white" id="m_6300125436136123137templateBody">
    <tbody>
        <tr>
            <td valign="top" style="padding:15.0pt 15.0pt 15.0pt 15.0pt">
                <p style="line-height:150%">
                    <span style="font-size:18.5pt;line-height:150%;font-family:&quot;Helvetica&quot;,&quot;sans-serif&quot;;color:#505050"> 
                      Nueva Reserva
                    </span>
                    <p style="line-height:150%">

                        <span style="font-size:10.5pt;line-height:150%;font-family:&quot;Helvetica&quot;,&quot;sans-serif&quot;;color:#505050">----------------------------------------------
                        </span>
                    </p>
                    <p style="line-height:150%">
                      <p style="line-height:150%">
                        <span style="font-size:10.5pt;line-height:150%;font-family:&quot;Helvetica&quot;,&quot;
                            ">
                            Gracias por elegir el espacio <?=strtolower($espacio->nombre_espacio)?>.<br> 
                            <?php  if ($this->ion_auth->in_group(ADMINISTRADOR) || $this->ion_auth->in_group(SEGURIDAD)){ ?>
                             Queda notificado de los <a href="<?=base_url('auth/get_tyc/'.$reserva_hash)?>">
                                     <strong>TÉRMINOS Y CONDICIONES</strong>
                                </a> del espacio.<br>   
                            <?php } ?>
                            con fecha del <?=strtolower($menssage)?>
                            <strong><?=ucwords($aprobado)?></strong><br>
                            Codigo de reserva:<small><?=$hash?></small><br>
                            <!-- Tema por covid 19 -->
                          <!--  Recuerde ingresar invitados.<br> -->
                        </span>
                      </p>
                      <p style="line-height:150%">
                        <span style="font-size:10.5pt;line-height:150%;font-family:&quot;Helvetica&quot;,&quot;sans-serif&quot;;color:#505050">
                            Unidad: <small><?=$unidad?></small><br>
                        </span>
                      </p>

                    </p>
                    <p style="line-height:150%">

                        <span style="font-size:10.5pt;line-height:150%;font-family:&quot;Helvetica&quot;,&quot;sans-serif&quot;;color:#505050">----------------------------------------------
                        </span>
                    </p>
                    <span style="font-size:10.5pt;line-height:150%;font-family:&quot;Helvetica&quot;,&quot;sans-serif&quot;;color:#505050">
                    <?php if(!empty($espacio->reglamento) and $espacio->cancel_dia > 0 ){ ?>
                      <p>
                        Usted puede dar de baja la reserva con <?=$espacio->cancel_dia ?> <?=($espacio->cancel_dia == 1 )? "día":"días"?> de antelación; no cumplido el tiempo pertinente de baja, el espacio quedara dado de alta, cumpliendo con ello todos los requisitos de alquiler o simplemente de reserva. Si el espacio remunerado se cancela en esos <?=$espacio->cancel_dia?><?=($espacio->cancel_dia == 1 )? "día":"días"?>, tendrá una multa.
                      </p>
                    <?php } ?>
                        <br>
                        Para ingresar haga click <a href="<?=base_url()?>">Aquí</a>
                    </span>
                    <br>
                    <br>
                    <span style="font-size:10.5pt;line-height:150%;font-family:&quot;Helvetica&quot;,&quot;sans-serif&quot;color:#505050">----------------------------------------------<br>
                    </span>
                    <span style="font-size:10.5pt;line-height:150%;font-family:&quot;Helvetica&quot;,&quot;sans-serif&quot;;color:#505050">
                        Consorcio: <?=@$edificio->nombre?><br>
                        Dirección: <?=@$edificio->direccion?><br>
                        Telefono: <?=@$edificio->telefono?><br>
                        web: <a href="<?=base_url()?>" target="_blank">
                                <?=base_url()?>
                            </a>
                    </span>
                </p>
            </td>
        </tr>
    </tbody>
</table>
</div>
</td>
</tr>
<tr>
<td valign="top" style="border:none;padding:0cm 0cm 0cm 0cm">
<div align="center">
<table class="table" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100.0%" id="m_6300125436136123137templateFooter">
    <tbody>
        <tr>
            <td valign="top" style="padding:15.0pt 15.0pt 15.0pt 15.0pt">
                <div class="MsoNormal" align="center" style="text-align:center;line-height:150%">
                    <span style="font-size:9.0pt;line-height:150%;font-family:&quot;Helvetica&quot;,&quot;sans-serif&quot;">
                        <hr size="2" width="100%" align="center">
                    </span>
                </div>

                <div>
                    <p class="MsoNormal" align="center" style="text-align:center;line-height:150%">
                        <span style="font-size:9.0pt;line-height:150%;font-family:&quot;Helvetica&quot;,&quot;sans-serif&quot;">
                            <br>PROXIMAMENTE EN SU SMARTPHONE<br>
                            <a href="[APP_IOS]" title="App Store" target="_blank">
                                <span style="color:#606060;text-decoration:none">
                                    <img border="0" id="m_6300125436136123137_x0000_i1027" src="<?=base_url('access/images/appstore.png')?>" width="130px">
                                </span>
                            </a>
                            <a href="[APP_ANDROID]" title="Google Play" target="_blank">
                                <span style="color:#606060;text-decoration:none">
                                    <img border="0" id="m_6300125436136123137_x0000_i1028" src="<?=base_url('access/images/google-play.png')?>" width="130px">
                                </span>
                            </a>
                        </span>
                    </p>
                </div>
                <p class="MsoNormal" align="center" style="text-align:center;line-height:150%">
                    <span style="font-size:9.0pt;line-height:150%;font-family:&quot;Helvetica&quot;,&quot;sans-serif&quot;">
                       Este e-mail se ha generado por un sistema automático. Por favor, "no responder". Si desea recibir respuesta contactar directamente con administración.
                        <b>
                            <a href="https://buildingapps.com.ar/" target="_blank">buildingapps.com.ar</a>
                        </b> 
                    </span>
                </p>
            </td>
        </tr>
    </tbody>
</table>
</div>
</td>
</tr>
</tbody>
</table>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<p class="MsoNormal"></p>
</div>
</div>
</div>
</div>
</body>
</html>
