<?php

// Session start check
if(!session_name()) {
    header('Location: index.php');
    exit;
}

$id = intval($_GET['get_file']);

// Make sure the ID is in fact a valid ID
if($id <= 0) {
    die('The ID is invalid!');
} else {
    // Fetch the file information
    $query = "
        SELECT `type`, `inn`, `kpp`, `period`, `size`, `content`
        FROM `files`
        WHERE `id` = {$id} AND `inn` = {$_SESSION['user_id']}";
    $result = $db->query($query);

    if($result) {
        // Make sure the result is valid
        if($result->num_rows == 1) {
            // Get the row
            $row = mysqli_fetch_assoc($result);
            $name = $row['inn'].'-'.$row['kpp'].'-'.$row['period'].'.zip';

            // Print headers
            header("Content-Type: ". $row['type']);
            header("Content-Length: ". $row['size']);
            header("Content-Disposition: attachment; filename=". $name);

            // Print data
            echo $row['content'];
        } else {
            echo 'Error! No image exists with that ID.';
        }

        // Free the mysqli resources
        @mysqli_free_result($result);
    } else {
        echo "Error! Query failed: <pre>{$db->error}</pre>";
    }
}

?>
