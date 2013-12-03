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
        $query = 'UPDATE `users` SET `mail`="'.$_POST['mail'].'",  `notification`='.$not.', `pass`='.$_POST['password'].' WHERE `id`='.$_POST['id'];
    } else {
        $query = 'UPDATE `users` SET `mail`="'.$_POST['mail'].'",  `notification`="'.$not.'" WHERE `id`='.$_POST['id'];
    }
    $msg = 'Информация обновлена.';
    $result = mysqli_query($db,$query) or $msg = $query;
}

// User-data request
$query = 'SELECT * FROM `users` WHERE `admin` = 0 AND `id` = '.$_GET['id'];
$result = mysqli_query($db,$query);
$user_list = mysqli_fetch_all($result,MYSQLI_ASSOC);
mysqli_free_result($result);
$not_check = '';
if ($user_list[0]['notification'] == '1') {$not_check='checked';}

// Get INN & KPP combo
$request = 'SELECT `org_id`, `inn`, `kpp` FROM `organizations` WHERE `id` = '.$_GET['id'];
$result = mysqli_query($db,$request);
$organizations = mysqli_fetch_all($result,MYSQLI_ASSOC);
mysqli_free_result($result);

echo '<!DOCTYPE html>
    <meta charset="utf-8">
    <title>Служба деклараций</title>
    <h1><a href="index.php">Админ</a>: редактирование пользователя '.$_GET['id'].'</h1>
    <br>'.$msg.'
    <form action="index.php?id='.$_GET['id'].'" method="post">
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
                <td><input type="submit" value="Изменить" /></td>
            </tr>
        </table>
        <input type="hidden" name="id" value="'.$_GET['id'].'">
    </form>';

// File list
echo '<h2 align="center">Список файлов пользователя</h2>
    <table align="center" border="1"><thead>
        <tr>
            <th>ИНН</th>
            <th>КПП</th>
            <th>Период</th>
            <th>Добавлено</th>
            <th></th>
        </tr>
    </thead><tbody>';

// Get the files
for($i=0; $i<count($organizations); $i++) {
    $request = 'SELECT `file_id`,`inn`,`kpp`,`period`, `date` FROM `files` WHERE `inn` = '.$organizations[$i]['inn'].' AND `kpp` = '.$organizations[$i]['kpp'];
    $result = mysqli_query($db,$request);
    $done = mysqli_fetch_all($result,MYSQLI_ASSOC);
    mysqli_free_result($result);

    for($j=0; $j<count($done); $j++) {
        echo '<tr>
            <td>'.$done[$j]['inn'].' </td>
            <td>'.$done[$j]['kpp'].' </td>
            <td>'.$done[$j]['period'].' </td>
            <td>'.$done[$j]['date'].' </td>
            <td><a href="index.php?get_file='.$done[$j]['file_id'].'">Скачать</a> </td>
            </tr>';
    }
}
echo '</table>';

echo $msg;

echo '<br><a href="index.php?logout=1">Выйти</a>';
