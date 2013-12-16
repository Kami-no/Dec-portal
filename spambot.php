<?php

// Session start check
if(!$welcome) {
    header('Location: index.php');
    exit;
}

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
$mail->Host = 'smtp.gmail.com';
//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
$mail->Port = 587;
//Set the encryption system to use - ssl (deprecated) or tls
$mail->SMTPSecure = 'tls';
//Whether to use SMTP authentication
$mail->SMTPAuth = true;
//Username to use for SMTP authentication - use full email address for gmail
$mail->Username = "vdk@kami-no.ru";
//Password to use for SMTP authentication
$mail->Password = "2E7X2XNhLSlf";
//Set who the message is to be sent from
$mail->setFrom('vdk@kami-no.ru', 'VDK');
//Set an alternative reply-to address
$mail->addReplyTo('vdk@kami-no.ru', 'VDK');
//Set the subject line
$mail->Subject = 'New password';

/*
// Запрос на список пользователей
$query = 'SELECT `mail`, `pass` FROM `users` WHERE `pass` = 12345 LIMIT 0 , '.$_GET['spam'];
$result = mysqli_query($db,$query);
$spam_list = mysqli_fetch_all($result,MYSQLI_ASSOC);
mysqli_free_result($result);

for($i=0; $i<count($spam_list); $i++) {
    //Set who the message is to be sent to
    $mail->addAddress($spam_list[$i]['mail'], 'Клиент');
    $mail->Body = 'Добрый день!
        <br>
        <br>Для вас создана учётная запись на сайте <a href="http://home.bit/alpha/decl/">Деклараций</a> компании ООО "ВДК".
        <br>Для подключения используйте следующие данные:
        <br> - пользователь: '.$spam_list[$i]['mail'].'
        <br> - пароль: '.mt_rand(1000000,9999999).'
        <br>
        <br>С Уважением,
        <br>ООО "ВДК"';
    //Replace the plain text body with one created manually
    $mail->AltBody = 'This is a plain-text message body';

    //send the message, check for errors
    if (!$mail->send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
        echo "Message sent!";
    }
}
*/
