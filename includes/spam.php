<?php

// Session start check
if (!$welcome) {
    header('Location: index.php');
    exit;
}

$error = FALSE;

//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set($timezone);

require 'PHPMailer/PHPMailerAutoload.php';

// Get user mail
$query = 'SELECT `mail` FROM `users` WHERE `id` = "'.$_GET['mid'].'"';
$result = mysqli_query($db,$query);
$spam = mysqli_fetch_all($result,MYSQLI_ASSOC);
mysqli_free_result($result);

// If no users then add one
$pass = mt_rand(1000000, 9999999);

//Create a new PHPMailer instance
$mail = new PHPMailer();
//Tell PHPMailer to use SMTP
$mail->isSMTP();
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug = 0;
//Ask for HTML-friendly debug output
$mail->Debugoutput = 'html';
//Set the hostname of the mail server
$mail->Host = $m_server;
//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
$mail->Port = 25;
//Whether to use SMTP authentication
$mail->SMTPAuth = true;
//Username to use for SMTP authentication - use full email address for gmail
$mail->Username = $m_outbox;
//Password to use for SMTP authentication
$mail->Password = $m_pass;

//Set who the message is to be sent from
$mail->setFrom($m_outbox, 'VDK');
//Set an alternative reply-to address
$mail->addReplyTo($m_outbox, 'VDK');
//Set the subject line
$mail->Subject = 'New password';
$mail->addAddress($spam[0]['mail'], 'User');
//Set the subject line
$mail->Subject = 'New password';
$mail->Body = 'Добрый день!
    <br>
    <br>Для вас создана учётная запись на сайте <a href="http://decl.vdk.vl.ru/">Деклараций</a> компании ' . $m_company . '.
    <br>Для подключения используйте следующие данные:
    <br> - пользователь: ' . $spam[0]['mail'] . '
    <br> - пароль: ' . $pass . '
    <br>
    <br>С Уважением,
    <br>' . $m_company;
//Replace the plain text body with one created manually
$mail->AltBody = 'Добрый день!

    Для вас создана учётная запись на сайте <a href="' . $m_site . '">Деклараций</a> компании ' . $m_company . '.
    Для подключения используйте следующие данные:
     - пользователь: ' . $spam[0]['mail'] . '
        - пароль: ' . $pass . '

    С Уважением,
    ' . $m_company;

// Send the message and check for errors
if (!$mail->send()) {
    $msg = 'Mailer Error: ' . $mail->ErrorInfo;
    $error = TRUE;
}

if (!$error) {
    // Change pass user
    $query = 'UPDATE `users` SET `pass` = "'.$pass.'" WHERE `id` = "'.$_GET['mid'].'";';
    $result = mysqli_query($db, $query);
    $msg = 'Пользователю отправлено оповещение на e-mail: '.$spam[0]['mail'];
}

echo '<!DOCTYPE html>
    <meta charset="utf-8">
    <title>Служба деклараций</title>
    <h1><a href="index.php">Админ</a>: изменение пароля пользователя '.$_GET['mid'].'</h1>
    <br>'.$msg;

echo '<br><a href="index.php?logout=1">Выйти</a>';
