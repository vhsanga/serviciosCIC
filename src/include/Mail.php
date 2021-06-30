<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\POP3;
use PHPMailer\PHPMailer\Exception;


require './../vendor/phpmailer/phpmailer/src/Exception.php';
require './../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require './../vendor/phpmailer/phpmailer/src/SMTP.php';


function mail_enviarNotificacionRobo($destinatario, $reporte_robo){
    $body = file_get_contents('./email.html');
    $body = str_replace('$_placa', $reporte_robo["placa"],  $body); 
    $body = str_replace('$_id', $reporte_robo["id"],  $body); 
    $body = str_replace('$_marca', $reporte_robo["marca"],  $body); 
    $body = str_replace('$_modelo', $reporte_robo["modelo"],  $body); 
    $body = str_replace('$_anio', $reporte_robo["anio"],  $body); 
    $body = str_replace('$_matricula', $reporte_robo["matricula"],  $body); 
    $body = str_replace('$_tipo', $reporte_robo["tipo"],  $body); 
    $body = str_replace('$_propietario', $reporte_robo["propietario"],  $body); 
    $body = str_replace('$_ci', $reporte_robo["ci"],  $body); 
    $body = str_replace('$_direccion', $reporte_robo["direccion"],  $body); 
    $body = str_replace('$_telefono', $reporte_robo["telefono"],  $body); 
    $body = str_replace('$_correo', $reporte_robo["correo"],  $body); 
    $body = str_replace('$_fecha_robo', $reporte_robo["fecha_robo"],  $body); 
    $body = str_replace('$_observacion', $reporte_robo["observacion"],  $body); 
    $body = str_replace('$_direccion_robo', $reporte_robo["direccion_robo"],  $body); 
    $asunto="Asundo del correo";
    enviarMail($destinatario, $asunto, $body );
}


function enviarMail($receptor, $asunto, $mensaje ){
    $mail = new PHPMailer(true);
    try {
        /**************************************************************/
        /*Configuracion del servdor de correo SMTP */
        $smtp_server='mail.nodoclic.com';
        $smtp_usuario='transitoseguro@nodoclic.com';
        $smtp_pass='@fd!ve@5Vw}b';
        $smtp_puerto=587;
        $smtp_encriptacion='tls';
        $smtp_from='transitoseguro@nodoclic.com';
        $smtp_app='Transito Seguro';
        /****************************************************************/
        /* Agregar destinatarios */
        $destinos= explode(",",$receptor);
        foreach ($destinos as $key => $destino) {
            $mail->addAddress($destino, 'usuario '.$key);
        }

        /****************************************************************/
        $mail->setFrom($smtp_from, $smtp_app);        
        $mail->Subject = $asunto;
        $mail->Body = $mensaje;
        $mail->IsHTML(true); 

        /* SMTP parameters. */

        /* Tells PHPMailer to use SMTP. */
        $mail->isSMTP();

        /* SMTP server address. */
        $mail->Host = $smtp_server;

        /* Use SMTP authentication. */
        $mail->SMTPAuth = TRUE;

        /* Set the encryption system. */
        $mail->SMTPSecure = $smtp_encriptacion;

        /* SMTP authentication username. */
        $mail->Username = $smtp_usuario;

        /* SMTP authentication password. */
        $mail->Password = $smtp_pass;

        /* Set the SMTP port. */
        $mail->Port =  $smtp_puerto;


        /* Disable some SSL checks. */
        $mail->SMTPOptions = array(
          'ssl' => array(
              'verify_peer' => false,
              'verify_peer_name' => false,
              'allow_self_signed' => true
          )
      );

        /* Finally send the mail. */
        $mail->send();
        //echo 'Message has been sent';
    } catch (Exception $e) {
       //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

?>