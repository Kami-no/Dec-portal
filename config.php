<?php

// Session start check
if(!$welcome) {
    header('Location: index.php');
    exit;
}

// Set language
$lang = 'ru';
