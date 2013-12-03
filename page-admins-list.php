<?php

// Session start check
if(!session_name()) {
    header('Location: index.php');
    exit;
}

// Запрос на список пользователей
$query = 'SELECT * FROM `users` WHERE `admin` = 0';
$result = mysqli_query($db,$query);
$user_list = mysqli_fetch_all($result,MYSQLI_ASSOC);
mysqli_free_result($result);

echo '<!DOCTYPE html>
    <meta charset="utf-8">
    <title>Служба деклараций</title>
    <h1><a href="index.php">Админ</a>: список пользователей</h1>
    <table align="center" class="atable" border="1"><thead>
    <tr>
        <th>Mail</th>
        <th>Pass</th>
    </tr></thead><tbody>';

for($i=0; $i<count($user_list); $i++) {
    echo '<tr>
        <td><a href="index.php?id='.$user_list[$i]['id'].'">'.$user_list[$i]['mail'].' </td>
        <td>'.$user_list[$i]['pass'].' </td>
        </tr>';
}

echo '</table>';

echo $msg;

echo '<br><a href="index.php?logout=1">Выйти</a>';

?>
