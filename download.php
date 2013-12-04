<?php

// Session start check
if(!session_name()) {
    header('Location: index.php');
    exit;
}

$file_id = intval($_GET['get_file']);

// Make sure the ID is in fact a valid ID
if($file_id <= 0) {
    die('The ID is invalid!');
} else {
    // Fetch the file information
    $request = 'SELECT `file_id`, `inn`, `kpp`, `period`, `size`, `type`, `content` FROM `files` WHERE `file_id` = '.$file_id;
    $result = mysqli_query($db,$request);
    $file = mysqli_fetch_assoc($result);
    $file_n = mysqli_num_rows($result);
    mysqli_free_result ($result);

    // Verify if user is authorized for this file
    if(isset($_SESSION['admin'])) {
        $owner = 1;
    } else {
        $request = 'SELECT `org_id` FROM `organizations` WHERE `id` = '.$_SESSION['user_id'].' AND `inn` = '.$file['inn'].' AND `kpp` = '.$file['kpp'];
        $result = mysqli_query($db,$request);
        $owner = mysqli_num_rows($result);
        mysqli_free_result($result);
    }

    // Make sure the result is valid
    if($file_n == 1 && $owner == 1) {
        $name = $file['inn'].'-'.$file['kpp'].'-'.$file['period'].'.zip';

        // Print headers
        header('Content-Type: '. $file['type']);
        header('Content-Length: '. $file['size']);
        header('Content-Disposition: attachment; filename='. $name);

        // Print data
        echo $file['content'];
    } else {
        echo 'Error! No file exists with that ID.';
    }
}
