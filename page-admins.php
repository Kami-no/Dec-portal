<?php

// Session start check
if(!$welcome) {
    header('Location: index.php');
    exit;
}

// Check if a file has been uploaded
if(isset($_FILES['uploaded_files'])) {

    foreach($_FILES['uploaded_files']['tmp_name'] as $key => $tmp_name ){
        // Make sure the file was sent without errors
        //if($_FILES['uploaded_files']['error'] == 0) {
            // Gather all required data
            $name_u = $db->real_escape_string($_FILES['uploaded_files']['name'][$key]);
            $type = $db->real_escape_string($_FILES['uploaded_files']['type'][$key]);
            $content = $db->real_escape_string(file_get_contents($_FILES  ['uploaded_files']['tmp_name'][$key]));
            $size = intval($_FILES['uploaded_files']['size'][$key]);
            $name_pre = explode('.',$name_u);
            $name = explode('-',$name_pre[0]);

            // Create the SQL query
            $query = "
                INSERT INTO `files` (
                    `inn`, `kpp`, `period`, `size`, `type`, `content`, `date`
                )
                VALUES (
                    '$name[0]', '$name[1]', '$name[2]', '$size', '$type', '$content', NOW()
                )";

            // Execute the query
            $result = $db->query($query);

            // Check if it was successful
            if($result) {
                $msg = 'Success! Your file was successfully added!';
            } else {
                $msg = 'Error! Failed to insert the file'
                   . '<pre>{$db->error}</pre>';
            }
        //} else {
        //    $msg = 'An error accured while the file was being uploaded. '
        //       . 'Error code: '. intval($_FILES['uploaded_files']['error']);
        //}
    }

} else {
    $msg = '';
}

echo '<!DOCTYPE html>
    <meta charset="utf-8">
    <title>Служба деклараций</title>
    <h1>Админ</h1>';

// Admins list
$query = 'SELECT `mail` FROM `users` WHERE `admin` = 1';
$result = mysqli_query($db,$query);
$user_list = mysqli_fetch_all($result,MYSQLI_ASSOC);
$user_n = mysqli_num_rows($result);
mysqli_free_result($result);

if($user_n!=1) {
    echo '<h2>Список админов</h2>
        <table align="center" border="1"><thead>
            <tr><th>Mail</th></tr>
        </thead><tbody>';

    for($i=0; $i<count($user_list); $i++) {
        echo '<tr>
            <td>'.$user_list[$i]['mail'].' </td>
            </tr>';
    }

    echo '</table>';
}

// List of users without pass and e-mail
$query = 'SELECT * FROM `users` WHERE `pass` = NULL OR `mail` = NULL';
$result = mysqli_query($db,$query);
$user_list = mysqli_fetch_all($result,MYSQLI_ASSOC);
$user_n = mysqli_num_rows($result);
mysqli_free_result($result);

if($user_n!=0) {
    echo '<h2>Список пользователей без пароля или почты</h2>
        <table align="center" border="1"><thead>
        <tr>
        <th>INN</th>
        <th>PASS</th>
        <th>Mail</th>
        </tr></thead><tbody>';

    for($i=0; $i<count($user_list); $i++) {
        echo '<tr>
            <td>'.$user_list[$i]['inn'].' </td>
            <td>'.$user_list[$i]['pass'].' </td>
            <td>'.$user_list[$i]['mail'].' </td>
            </tr>';
    }

    echo '</table>';
}

// Declarations upload
echo '<h2>Загрузка файлов деклараций (можно несколько сразу)</h2>
    <form method="post" enctype="multipart/form-data">
    <table width="350" border="0" cellpadding="1" cellspacing="1" class="box">
        <tr><td>Выбирете файлы</td></tr>
        <tr>
            <td>
                <input type="hidden" name="MAX_FILE_SIZE" value="16000000">
                <input name="uploaded_files[]" type="file" multiple>
            </td>
            <td width="80"><input name="upload" type="submit" class="box" id="upload" value=" Upload "></td>
        </tr>
    </table>
    </form>';

// Organizations upload
echo '<h2>Загрузка файла с организациями</h2>
    <form method="post" enctype="multipart/form-data">
    <table width="350" border="0" cellpadding="1" cellspacing="1" class="box">
        <tr><td>Выбирете файл</td></tr>
        <tr>
            <td>
                <input type="hidden" name="MAX_FILE_SIZE" value="16000000">
                <input name="upload_org" type="file">
            </td>
            <td width="80"><input name="upload" type="submit" class="box" id="upload" value=" Upload "></td>
        </tr>
    </table>
    </form>';


echo $msg;

echo '<br><a href="index.php?user_list=1">Список пользователей</a>
    <br><a href="index.php?logout=1">Выйти</a>';
