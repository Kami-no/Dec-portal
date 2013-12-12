<?php

// Session start check
if(!$welcome) {
    header('Location: index.php');
    exit;
}

echo '<!DOCTYPE html>
    <meta charset="utf-8">
    <title>Служба деклараций</title>
    <h1><a href="index.php">Админ</a>: импорт</h1>';

// Upload file to HDD
$target = 'upload/organizations.csv';

$ok=1;
if(move_uploaded_file($_FILES['upload_org']['tmp_name'], $target))
{
    chmod($target, 0666);
    $msg = 'The file '. basename( $_FILES['upload_org']['name']).' has been uploaded';
}
else {
    $msg = 'Sorry, there was a problem uploading your file.';
}

// Input
$query = 'LOAD DATA LOCAL INFILE "'.$_SERVER['DOCUMENT_ROOT'].'/alpha/decl/'.$target.'" INTO TABLE import FIELDS TERMINATED BY "," (mail, inn, kpp)';
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
                $pass = '123';
                $query = 'INSERT INTO `users` (`mail`, `pass`) VALUES ("'.$import_list[$i]['mail'].'", "'.$pass.'")';
                $result = mysqli_query($db,$query);

                // Get user ID
                $query = 'SELECT `id` FROM `users` WHERE `mail` = "'.$import_list[$i]['mail'].'"';
                $result = mysqli_query($db,$query);
                $mail_list = mysqli_fetch_all($result,MYSQLI_ASSOC);
                mysqli_free_result($result);
                $msg = '+user';
            } else {$msg = '+org';}
            // Add new organization
            $query = 'INSERT INTO `organizations` (`id`, `inn`, `kpp`) VALUES ("'.$mail_list[0]['id'].'", "'.$import_list[$i]['inn'].'", "'.$import_list[$i]['kpp'].'")';
            $result = mysqli_query($db,$query);
            } else {$msg = 'exist';}
        echo '<tr>
            <td>'.$import_list[$i]['mail'].' </td>
            <td>'.$import_list[$i]['inn'].' </td>
            <td>'.$import_list[$i]['kpp'].' </td>
            <td>'.$msg.' </td>
        </tr>';
        // Remove used line
        $query = 'DELETE FROM `import` WHERE `imp_id` = '.$import_list[$i]['imp_id'];
        $result = mysqli_query($db,$query);
    }
    echo '</table>';
} else {
    echo 'Ничего не импортировано';
}
