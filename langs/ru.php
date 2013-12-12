<?php

// Session start check
if(!$welcome) {
    header('Location: index.php');
    exit;
}

$cn = 'ФИО';
$telephonenumber = 'Внутренний номер';
$mobile = 'Сотовый телефон';
