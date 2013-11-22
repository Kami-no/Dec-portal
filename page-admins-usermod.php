<?php

// Session start check
if(!session_name()) {
    header('Location: index.php');
    exit;
}

// Notification modification
if (isset($_POST['notification'])) {$not = 1;} else {$not = 0;}

// Modify user
if (isset($_POST['mail'])) {
    if ($_POST['password']!=0) {
        $query = 'UPDATE `users` SET `mail`="'.$_POST['mail'].'",  `notification`='.$not.', `pass`='.$_POST['password'].' WHERE `inn`='.$_POST['inn'];
    } else {
        $query = 'UPDATE `users` SET `mail`="'.$_POST['mail'].'",  `notification`="'.$not.'" WHERE `inn`='.$_POST['inn'];
    }
    $msg = 'Информация обновлена.';
    $result = mysqli_query($db,$query) or $msg = $query;
}

// User-data request
$query = 'SELECT * FROM `users` WHERE `admin` = 0 AND `inn` = '.$_GET['inn'];
$result = mysqli_query($db,$query);
$user_list = mysqli_fetch_all($result,MYSQLI_ASSOC);
mysqli_free_result($result);
$not_check = '';
if ($user_list[0]['notification'] == '1') {$not_check='checked';}

// File-list request
$query = 'SELECT * FROM `files` WHERE `inn` = '.$_GET['inn'];
$result = mysqli_query($db,$query);
$file_list = mysqli_fetch_all($result,MYSQLI_ASSOC);
mysqli_free_result($result);


echo '<html><head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Служба деклараций</title>
    </head>
    <body>
    <h1><a href="index.php">Админ</a>: редактирование пользователя '.$_GET['inn'].'</h1>
    <br>'.$msg.'
    <form action="index.php?inn='.$_GET['inn'].'" method="post">
        <table>
            <tr>
                <td>E-Mail:</td>
                <td><input type="text" name="mail" value="'.$user_list[0]['mail'].'" /></td>
            </tr>
            <tr>
                <td>Пароль:</td>
                <td><input type="password" name="password" /></td>
            </tr>
            <tr>
                <td>Высылать оповещения:</td>
                <td><input type="checkbox" name="notification" '.$not_check.' /></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" value="Авторизироваться" /></td>
            </tr>
        </table>
        <input type="hidden" name="inn" value="'.$_GET['inn'].'">
    </form>';

// File list
echo '<h2 align="center">Список файлов пользователя</h2>
    <table align="center" class="atable" border="1"><thead>
    <TR>
    <TH>Дата добавления</TH>
    <TH>Размер</TH>
    <TH>КПП</TH>
    <TH>Период</TH>
    </TR></thead><tbody>';

for($i=0; $i<count($file_list); $i++) {
    echo '<TR>
        <TD>'.$file_list[$i]['date'].' </TD>
        <TD>'.$file_list[$i]['size'].' </TD>
        <TD>'.$file_list[$i]['kpp'].' </TD>
        <TD>'.$file_list[$i]['period'].' </TD>
        </TR>';
}

echo '</table>';

echo $msg;

echo '<br><a href="index.php?logout=1">Выйти</a>
    </body><html>';

?>