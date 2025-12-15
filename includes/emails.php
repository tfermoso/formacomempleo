<?php
require_once __DIR__ . "/enviar_email.php";

/**
 * EMAIL: Registro de empresa
 */
function enviarEmailRegistroEmpresa($email, $nombreEmpresa)
{

    $mensaje = '
    <div style="font-family: Arial; background:#f5f5f5; padding:20px;">
        <div style="max-width:600px; margin:auto; background:white; padding:20px; border-radius:10px;">
            <h2 style="color:#2c3e50;">Bienvenido a Formacom Empleo</h2>

            <p style="font-size:16px;">
                Hola <strong>' . $nombreEmpresa . '</strong>,
            </p>

            <p style="font-size:16px;">
                Tu empresa ha sido registrada correctamente en la plataforma.
                Ya puedes acceder a tu panel y comenzar a gestionar ofertas.
            </p>

            <a href="http://localhost/formacomempleo/login.php"
               style="display:inline-block; padding:10px 20px; background:#3498db; color:white; text-decoration:none; border-radius:5px; margin-top:15px;">
               Acceder al panel
            </a>

            <hr style="margin-top:30px;">
            <p style="font-size:12px; color:#7f8c8d;">
                Este es un mensaje automático, por favor no respondas.
            </p>
        </div>
    </div>
    ';

    return enviarEmail($email, "Registro completado", $mensaje);
}



/**
 * EMAIL: Recuperación de contraseña
 */
function enviarEmailRecuperarPassword($email, $token)
{

    $enlace = "http://localhost/formacomempleo/empresa/restablecer.php?token=$token";

    $mensaje = '
    <div style="font-family: Arial; background:#f5f5f5; padding:20px;">
        <div style="max-width:600px; margin:auto; background:white; padding:20px; border-radius:10px;">
            <h2 style="color:#c0392b;">Recuperación de contraseña</h2>

            <p style="font-size:16px;">
                Hemos recibido una solicitud para restablecer tu contraseña.
            </p>

            <p style="font-size:16px;">
                Haz clic en el siguiente botón para continuar:
            </p>

            <a href="' . $enlace . '"
               style="display:inline-block; padding:10px 20px; background:#e74c3c; color:white; text-decoration:none; border-radius:5px; margin-top:15px;">
               Restablecer contraseña
            </a>

            <p style="margin-top:20px; font-size:14px;">
                Si no solicitaste este cambio, puedes ignorar este mensaje.
            </p>

            <hr style="margin-top:30px;">
            <p style="font-size:12px; color:#7f8c8d;">
                Este enlace expira en 1 hora.
            </p>
        </div>
    </div>
    ';

    return enviarEmail($email, "Recuperar contraseña", $mensaje);
}



/**
 * EMAIL: Verificación de cuenta
 */
function enviarEmailVerificacionCuenta($email, $token)
{

    $enlace = "http://localhost/formacomempleo/verificar.php?token=$token";

    $mensaje = '
    <div style="font-family: Arial; background:#f5f5f5; padding:20px;">
        <div style="max-width:600px; margin:auto; background:white; padding:20px; border-radius:10px;">
            <h2 style="color:#27ae60;">Verifica tu cuenta</h2>

            <p style="font-size:16px;">
                Gracias por registrarte. Para activar tu cuenta, haz clic en el siguiente botón:
            </p>

            <a href="' . $enlace . '"
               style="display:inline-block; padding:10px 20px; background:#2ecc71; color:white; text-decoration:none; border-radius:5px; margin-top:15px;">
               Verificar cuenta
            </a>

            <hr style="margin-top:30px;">
            <p style="font-size:12px; color:#7f8c8d;">
                Si no has creado esta cuenta, ignora este mensaje.
            </p>
        </div>
    </div>
    ';

    return enviarEmail($email, "Verifica tu cuenta", $mensaje);
}



/**
 * EMAIL: Nueva oferta publicada
 */
function enviarEmailNuevaOferta($email, $tituloOferta)
{

    $mensaje = '
    <div style="font-family: Arial; background:#f5f5f5; padding:20px;">
        <div style="max-width:600px; margin:auto; background:white; padding:20px; border-radius:10px;">
            <h2 style="color:#2980b9;">Nueva oferta publicada</h2>

            <p style="font-size:16px;">
                Se ha publicado una nueva oferta: <strong>' . $tituloOferta . '</strong>
            </p>

            <p style="font-size:16px;">
                Puedes verla en tu panel de empresa.
            </p>

            <a href="http://localhost/formacomempleo/empresa/panel.php"
               style="display:inline-block; padding:10px 20px; background:#3498db; color:white; text-decoration:none; border-radius:5px; margin-top:15px;">
               Ir al panel
            </a>

            <hr style="margin-top:30px;">
            <p style="font-size:12px; color:#7f8c8d;">
                Notificación automática.
            </p>
        </div>
    </div>
    ';

    return enviarEmail($email, "Nueva oferta publicada", $mensaje);
}
