<?php

// Session start check
if(!$welcome) {
    header('Location: index.php');
    exit;
}

$cn = 'Full name';
$telephonenumber = 'Telephone Number';
$mobile = 'Mobile';
