<?php

// Session start check
if(!$welcome) {
    header('Location: index.php');
    exit;
}

//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set($timezone);

require 'PHPMailer/PHPMailerAutoload.php';

//Create a new PHPMailer instance
$mail = new PHPMailer();
//Tell PHPMailer to use SMTP
$mail->isSMTP();
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug = 2;
//Ask for HTML-friendly debug output
$mail->Debugoutput = 'html';
//Set the hostname of the mail server
$mail->Host = $m_server;
//Set the SMTP port number - likely to be 25, 465 or 587
$mail->Port = 25;
//Whether to use SMTP authentication
$mail->SMTPAuth = true;
//Username to use for SMTP authentication
$mail->Username = $m_outbox;
//Password to use for SMTP authentication
$mail->Password = $m_pass;
//Set who the message is to be sent from
$mail->setFrom($m_outbox, $m_sender);
//Set an alternative reply-to address
$mail->addReplyTo($m_outbox, $m_sender);
//Set who the message is to be sent to
$mail->addAddress($import_list[$i]['mail'], 'User');
//Set the subject line
$mail->Subject = 'New password';
$mail->Body = 'Добрый день!
    <br>
    <br>Для вас создана учётная запись на сайте <a href="http://decl.vdk.vl.ru/">Деклараций</a> компании '.$m_company.'.
    <br>Для подключения используйте следующие данные:
    <br> - пользователь: '.$import_list[$i]['mail'].'
    <br> - пароль: '.$pass.'
    <br>
    <br>Письмо создано автоматической службой оповещения.
    <br>
    <br>С Уважением,
    <br>'.$m_company;//Replace the plain text body with one created manually
$mail->AltBody = 'Добрый день!

    Для вас создана учётная запись на сайте <a href="'.$m_site.'">Деклараций</a> компании '.$m_company.'.
    Для подключения используйте следующие данные:
     - пользователь: '.$import_list[$i]['mail'].'
     - пароль: '.$pass.'

    С Уважением,
    '.$m_company;

//send the message, check for errors
if (!$mail->send()) {
    $msg = 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    $msg = 'mail';
}
