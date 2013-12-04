<?php

// Session start check
if(!session_name()) {
    header('Location: index.php');
    exit;
}

// Get INN & KPP combo
$request = 'SELECT `org_id`, `inn`, `kpp` FROM `organizations` WHERE `id` = '.$_SESSION['user_id'];
$result = mysqli_query($db,$request);
$organizations = mysqli_fetch_all($result,MYSQLI_ASSOC);
mysqli_free_result($result);

echo '<!DOCTYPE html>
    <meta charset="utf-8">
    <title>Служба деклараций</title>
    <h1>Пользоватль</h1>
    <table align="center" border="1"><thead>
    <tr>
    <th>ИНН</th>
    <th>КПП</th>
    <th>Период</th>
    <th>Добавлено</th>
    <th></th>
    </tr></thead><tbody>';

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
echo '</table><br><a href="index.php?logout=1">Выйти</a>';
