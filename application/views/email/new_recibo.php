<html>
<head>
<link rel="important stylesheet" href="chrome://messagebody/skin/messageBody.css">
<title><?=$this->my_style->get_name()?></title>
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
                    <span style="font-size:18.5pt;line-height:150%;font-family:&quot;Helvetica&quot;,&quot;sans-serif&quot;;color:#505050"> Recibo de Pago <?=$pago->fecha_pago?>
                    </span>
                    <p style="line-height:150%">
                        <span style="font-size:10.5pt;line-height:150%;font-family:&quot;Helvetica&quot;,&quot;sans-serif&quot;;color:#505050">----------------------------------------------------------
                        </span>
                    </p>
                    <span style="font-size:10.5pt;line-height:150%;font-family:&quot;Helvetica&quot;,&quot;sans-serif&quot;;color:#505050">
                      <table class="recibo" width="60%" style="text-align:center;margin-left: 10%;" border="1">
                        <tr>
                          <td colspan="2">
                            <h4>recibo propiedad horizontal ley 13512</h4>
                          </td>
                        </tr>                                
                        <tr>
                          <td colspan="2">
                            <h4><?=ucfirst($this->my_style->get_name())?></h4> 
                          </td>
                        </tr>
                        <tr>
                          <td>Consorcio:</td>
                          <td><?=$edificio->nombre?></td>
                        </tr>                                
                        <tr>
                          <td>Cuit:</td>
                          <td><?=$edificio->cuit?></td>
                        </tr>                                
                        <tr>
                          <td>Expensa Mes:</td>
                          <td><?=$pago->titulo?></td>
                        </tr>
                        <tr>
                          <td colspan="2">
                            <table width="100%">
                              <tr>
                                <td colspan="6">
                                  Propietario
                                </td>
                              </tr>  
                              <tr>
                                <td>Unidad :</td>
                                <td style="text-align: left;"><?=$pago->unidad?></td>
                                <td>Departamento :</td>
                                <td style="text-align: left;"><?=$pago->departamento?></td>
                                <td>PORC</td>
                                <td></td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="2" style="text-align: right; padding: 0px 12px 0px 0px">
                            <strong>Total :</strong><?=$pago->importe?>    
                          </td>
                        </tr>
                      </table>
                          <br>
                          <br>
                          <br>
                        Para ingresar al sistema haga click <a href="<?=base_url()?>">Aquí</a>
                    </span>
                    <br>
                    <br>
                    <span style="font-size:10.5pt;line-height:150%;font-family:&quot;Helvetica&quot;,&quot;sans-serif&quot;color:#505050">----------------------------------------------------------------------<br>
                    </span>
                    <span style="font-size:10.5pt;line-height:150%;font-family:&quot;Helvetica&quot;,&quot;sans-serif&quot;;color:#505050">
                        Consorcio: <?=$edificio->nombre?><br>
                        Dirección: <?=$edificio->direccion?><br>
                        Telefono: <?=$edificio->telefono?><br>
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
