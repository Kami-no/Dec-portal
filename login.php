<?php

// Session start check
if(!session_name()) {
    header('Location: index.php');
    exit;
}

if (isset($_COOKIE['err'])) {
    $msg = $_COOKIE['err'];
    setcookie('err','');

}

// Login form
echo '<!DOCTYPE html>
    <meta charset="utf-8">
    <table>
    <tr><td align="center"><h3>Вход</h3></td></tr>
    <tr><td>
    <form action="index.php" method="post">
    <table>
        <tr>
            <td>E-Mail:</td>
            <td><input type="text" name="login" /></td>
        </tr>
        <tr>
            <td>Пароль:</td>
            <td><input type="password" name="password" /></td>
        </tr>
        <tr>
            <td></td>
            <td><input type="submit" value="Войти" /></td>
        </tr>
    </table>
    </form>
    </td></tr>
    <tr><td width="240" align="center">'.$msg.'</td></tr>
    </table>';

?>
