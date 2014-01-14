<?php

// Session start check
if(!$welcome) {
    header('Location: index.php');
    exit;
}

if (isset($_COOKIE['err'])) {
    $msg = $_COOKIE['err'];
    setcookie('err','');

}

// reCAPTCHA pub key
$publickey = '6LdTOOwSAAAAANgdFVrj2Z15wEfzsCDqJKBZan07';

// Login form
echo '<!DOCTYPE html>
    <meta charset="utf-8">
    <title>'.$m_company.'</title>
    <div align="center"><img src="img/title.jpg" /></div>
    <h1 align="center">Служба деклараций '.$m_company.'</h1>
    <form action="index.php" method="post">
        <table align="center">
            <tr><td>
                <table align="center">
                    <tr>
                        <td>E-Mail:</td>
                        <td><input type="text" name="login" /></td>
                    </tr>
                    <tr>
                        <td>Пароль:</td>
                        <td><input type="password" name="password" /></td>
                    </tr>
                </table>
            </td></tr>
            <tr><td align="center">'.recaptcha_get_html($publickey).'</td></tr>
            <tr><td align="center"><input type="submit" value="Войти" /></td></tr>
            <tr><td align="center">'.$msg.'</td></tr>
        </table>
    </form>';
