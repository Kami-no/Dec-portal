<?php

// Session start check
if(!session_name()) {
    header('Location: index.php');
    exit;
}

// Check if a file has been uploaded
if(isset($_FILES['uploaded_file'])) {
    // Make sure the file was sent without errors
    if($_FILES['uploaded_file']['error'] == 0) {
        // Gather all required data
        $name_u = $db->real_escape_string($_FILES['uploaded_file']['name']);
        $type = $db->real_escape_string($_FILES['uploaded_file']['type']);
        $content = $db->real_escape_string(file_get_contents($_FILES  ['uploaded_file']['tmp_name']));
        $size = intval($_FILES['uploaded_file']['size']);
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

        // Check if it was successfull
        if($result) {
            $msg = 'Success! Your file was successfully added!';
        }
        else {
            $msg = 'Error! Failed to insert the file'
               . "<pre>{$db->error}</pre>";
        }
    }
    else {
        $msg = 'An error accured while the file was being uploaded. '
           . 'Error code: '. intval($_FILES['uploaded_file']['error']);
    }

}
else {
    $msg = '';
}

// Admins list
$query = 'SELECT * FROM `users` WHERE `admin` = 1';
$result = mysqli_query($db,$query);
$user_list = mysqli_fetch_all($result,MYSQLI_ASSOC);
mysqli_free_result($result);

echo '
    <html><head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Служба деклараций</title>
    </head>
    <body>
    <h1>Админ</h1>
    <h2>Список админов</h2>
    <table align="center" class="atable" border="1"><thead>
    <TR>
    <TH>INN</TH>
    <TH>PASS</TH>
    <TH>Mail</TH>
    </TR></thead><tbody>';

for($i=0; $i<count($user_list); $i++) {
    echo '<TR>
        <TD>'.$user_list[$i]['inn'].' </TD>
        <TD>'.$user_list[$i]['pass'].' </TD>
        <TD>'.$user_list[$i]['mail'].' </TD>
        </TR>';
}

echo '</table>';

// File upload
echo '<h2>Загрузка файлов на сервер (пока по одному)</h2>
    <form method="post" enctype="multipart/form-data">
    <table width="350" border="0" cellpadding="1" cellspacing="1" class="box">
        <tr><td>please select a file</td></tr>
        <tr>
            <td>
                <input type="hidden" name="MAX_FILE_SIZE" value="16000000">
                <input name="uploaded_file" type="file"> 
            </td>
            <td width="80"><input name="upload" type="submit" class="box" id="upload" value=" Upload "></td>
        </tr>
    </table>
    </form>';

echo $msg;

echo '<br><a href="index.php?user_list=1">Список пользователей</a>
    <br><a href="index.php?logout=1">Выйти</a>
    </body><html>';

?>