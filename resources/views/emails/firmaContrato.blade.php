<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Firma de Contrato</title>
</head>
<body style="margin:0; padding:0; font-family: Arial, Helvetica, sans-serif; background-color:#f4f6f8; color:#333;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f6f8; padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" 
                       style="background:#ffffff; border-radius:8px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,0.05);">
                    
                    <!-- Logo -->
                    <tr>
                        <td align="center" style="padding-bottom:20px;">
                            <img src="{{ $message->embed(public_path('img/logo.png')) }}" 
                                 alt="Logo {{ env('RAZON') }}" 
                                 style="max-width:180px; height:auto; display:block;"/>
                        </td>
                    </tr>

                    <!-- Encabezado -->
                    <tr>
                        <td align="center" style="padding-bottom:15px;">
                            <h1 style="margin:0; font-size:22px; font-weight:bold; color:#242E68;">
                                Firma de Contrato
                            </h1>
                        </td>
                    </tr>

                    <!-- Contenido -->
                    <tr>
                        <td style="font-size:15px; line-height:24px; text-align:left; color:#555;">
                            <p style="margin:0 0 15px;">Estimado(a) <strong>{{ $nombre }}</strong>,</p>

                            <p style="margin:0 0 20px;">
                                Tiene un contrato pendiente de firma con 
                                <strong>{{ env('RAZON') }}</strong>.  
                                Para proceder, haga clic en el siguiente botón:
                            </p>

                            <!-- Botón CTA -->
                            <p style="text-align:center; margin:30px 0;">
                                <a href="{{ $linkContrato }}" 
                                   style="background-color:#242E68; color:#ffffff; text-decoration:none; 
                                          padding:14px 32px; border-radius:6px; font-weight:bold; 
                                          display:inline-block; font-size:16px;">
                                    Firmar Contrato
                                </a>
                            </p>

                            <p style="margin:20px 0 0;">Atentamente,<br>
                                <strong>{{ env('RAZON') }}</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer corporativo -->
                    <tr>
                        <td align="center" style="padding-top:25px; border-top:1px solid #eee; font-size:12px; color:#777;">
                            <p style="margin:4px 0;"><strong>{{ env('RAZON') }}</strong></p>
                            <p style="margin:4px 0;">NIT: {{ env('NIT') }}</p>
                            <p style="margin:4px 0;">Tel: {{ env('TEL') }} | {{ env('DIREC') }}</p>
                            <p style="margin:4px 0;">{{ env('CIU_DPT_EMPRE') }}</p>
                            <p style="margin:10px 0 0; color:#999;">No responda a este correo. Si tiene dudas, comuníquese con nuestro equipo de soporte.</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
