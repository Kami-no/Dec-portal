<?php

// Session start check
if(!$welcome) {
    header('Location: index.php');
    exit;
}

// Error check flag
$error = FALSE;

//Remove UTF8 Bom
function removeBOM($str=""){
    if(substr($str, 0,3) == pack("CCC",0xef,0xbb,0xbf)) {
        $str=substr($str, 3);
    }
    return $str;
}

//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set($timezone);
// PHPMailer
require 'PHPMailer/PHPMailerAutoload.php';

/*
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
//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
$mail->Port = 25;
//Set the encryption system to use - ssl (deprecated) or tls
//$mail->SMTPSecure = 'tls';
//Whether to use SMTP authentication
$mail->SMTPAuth = true;
//Username to use for SMTP authentication - use full email address for gmail
$mail->Username = $m_outbox;
//Password to use for SMTP authentication
$mail->Password = $m_pass;
*/
echo '<!DOCTYPE html>
    <meta charset="utf-8">
    <title>Служба деклараций</title>
    <h1><a href="index.php">Админ</a>: импорт</h1>';

// Upload file to HDD
$target = 'upload/organizations.csv';


if(move_uploaded_file($_FILES['upload_org']['tmp_name'], $target))
{
    chmod($target, 0666);
    $msg = 'The file '. basename( $_FILES['upload_org']['name']).' has been uploaded';
}
else {
    $msg = 'Sorry, there was a problem uploading your file.';
}

echo '<br>'.$msg.'<br>';
$msg = '';

// Input
$query = 'LOAD DATA LOCAL INFILE "'.dirname(__FILE__).'/'.$target.'" INTO TABLE import FIELDS TERMINATED BY "," (mail, inn, kpp)';
$result = mysqli_query($db,$query);

// Delete file
unlink($target);

// Output check
$query = 'SELECT * FROM `import`';
$result = mysqli_query($db,$query);
$import_list = mysqli_fetch_all($result,MYSQLI_ASSOC);
$import_n = mysqli_num_rows($result);
mysqli_free_result($result);

if($import_n!=0) {
    echo '<table align="center" border="1"><thead>
            <tr>
                <th>Почта</th>
                <th>ИНН</th>
                <th>КПП</th>
                <th>Результат</th>
            </tr>
        </thead><tbody>';

    // Import data
    for($i=0; $i<count($import_list); $i++) {
        $import_list[$i]['mail'] = removeBOM($import_list[$i]['mail']);
        $import_list[$i]['inn'] = removeBOM($import_list[$i]['inn']);
        $import_list[$i]['kpp'] = removeBOM($import_list[$i]['kpp']);
        $msg = '';
        // Check if organization exists
        $query = 'SELECT `org_id` FROM `organizations` WHERE `inn` = "'.$import_list[$i]['inn'].'" AND `kpp` = "'.$import_list[$i]['kpp'].'"';
        $result = mysqli_query($db,$query);
        $org_n = mysqli_num_rows($result);
        mysqli_free_result($result);
        if($org_n == 0) {
            // If no organizations check if user exists
            $query = 'SELECT `id` FROM `users` WHERE `mail` = "'.$import_list[$i]['mail'].'"';
            $result = mysqli_query($db,$query);
            $mail_list = mysqli_fetch_all($result,MYSQLI_ASSOC);
            $mail_n = mysqli_num_rows($result);
            mysqli_free_result($result);
            if($mail_n == 0) {
                // If no users then add one
                $pass = mt_rand(1000000,9999999);

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
                //Set the encryption system to use - ssl (deprecated) or tls
                //$mail->SMTPSecure = 'tls';
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
                    $error = TRUE;
                } else {
                    $msg = 'mail';
                }

                if (!$error) {
                    // Add user
                    $query = 'INSERT INTO `users` (`mail`, `pass`) VALUES ("'.$import_list[$i]['mail'].'", "'.$pass.'")';
                    $result = mysqli_query($db,$query);
                    // Get user ID
                    $query = 'SELECT `id` FROM `users` WHERE `mail` = "'.$import_list[$i]['mail'].'"';
                    $result = mysqli_query($db,$query);
                    $mail_list = mysqli_fetch_all($result,MYSQLI_ASSOC);
                    mysqli_free_result($result);
                    $msg .= '+user';
                }
            }
            // Add new organization
            if (!$error) {
                $query = 'INSERT INTO `organizations` (`id`, `inn`, `kpp`) VALUES ("'.$mail_list[0]['id'].'", "'.$import_list[$i]['inn'].'", "'.$import_list[$i]['kpp'].'")';
                $result = mysqli_query($db,$query);
                $msg .= '+org';
            }
        } else {
            $msg = 'exist';
        }
        echo '<tr>
            <td>'.$import_list[$i]['mail'].' </td>
            <td>'.$import_list[$i]['inn'].' </td>
            <td>'.$import_list[$i]['kpp'].' </td>
            <td>'.$msg.' </td>
        </tr>';
        // Remove used line if no error
        if (!$error) {
            $query = 'DELETE FROM `import` WHERE `imp_id` = '.$import_list[$i]['imp_id'];
            $result = mysqli_query($db,$query);
        }
	$error = FALSE;
    }
    echo '</table>';
} else {
    echo 'Ничего не импортировано';
}
