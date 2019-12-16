<?php

include_once 'PHPMailer/src/PHPMailer.php';
include_once 'PHPMailer/src/Exception.php';
include_once 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include_once 'config.inc.php';

/**
 * Description of ClaseMail
 *
 * @author jpsanchez
 */
class ClaseMail {

    private $mail;
    private $destinatarios;
    private $asunto;
    private $mensaje;
    private $adjuntos;

    public function __construct() {
        $this->mail = new PHPMailer(true);
        //Server settings
        $this->mail->SMTPDebug = 0;            // Enable verbose debug output
        $this->mail->isSMTP();                 // Set mailer to use SMTP
        $this->mail->Host = _SMTP;             // Specify main and backup SMTP servers
        $this->mail->SMTPAuth = true;          // Enable SMTP authentication
        $this->mail->Username = _USERNAME;     // SMTP username
        $this->mail->Password = _PASSWORDMAIL; // SMTP password
        $this->mail->SMTPSecure = _SMTPSECURE; // Enable TLS encryption, `ssl` also accepted
        $this->mail->Port = _PORT;             // TCP port to connect to
        $this->mail->isHTML(true);             // Set email format to HTML
        //Recipients
        $this->mail->setFrom(_FROM, _FROMNAME);
    }

    public function send($destinatarios, $asunto, $mensaje, $adjuntos, $rutaImagen = '', $nombreImagen = '') {
        $this->mail->clearAddresses();
        $this->mail->clearAllRecipients();
        $this->mail->clearAttachments();
        $this->mail->clearBCCs();
        $this->mail->clearCCs();

        if ($rutaImagen != '') {
            $this->mail->addEmbeddedImage($rutaImagen, $nombreImagen);
        }

        $this->mail->Subject = $asunto;
        $this->mail->Body = $mensaje;
        $this->mail->AltBody = $mensaje;
        $this->adjuntos = $adjuntos;

        try {
            foreach ($destinatarios as $key => $value) {
                if ($value['a']) {
                    $this->mail->AddAddress($value['mail'], $value['nombre']);
                }

                if ($value['cc']) {
                    $this->mail->AddCC($value['mail'], $value['nombre']);
                }

                if ($value['bcc']) {
                    $this->mail->AddBCC($value['mail'], $value['nombre']);
                }
            }

            foreach ($this->adjuntos as $key => $value) {
                $this->mail->AddAttachment($value['ruta']);
            }

            $this->mail->send();
            //echo 'Message has been sent';
            $result = array(error => 'N', enviado => 'S', mensaje => 'Mensaje enviado');
            return $result;
        } catch (phpmailerException $e) {
            //echo $e->errorMessage(); //Pretty error messages from PHPMailer
            $result = array(error => 'S', mensaje => 'phpmailerException ' . $e->errorMessage());
            return $result;
        } catch (Exception $e) {
            //echo $e->getMessage(); //Boring error messages from anything else!
            $result = array(error => 'S', enviado => 'N', mensaje => 'Exception ' . $e->errorMessage());
            return $result;
        }
    }

}
