<?php

// Session start check
if(!session_name()) {
    header('Location: index.php');
    exit;
}

$db_host = 'localhost';
$db_user = 'decl';
$db_pass = 'Qwerty123';
$db_base = 'decl';

?>
