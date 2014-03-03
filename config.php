<?php

// Session start check
if(!$welcome) {
    header('Location: index.php');
    exit;
}

// Set language
$lang = 'ru';

// Mail settings
$m_outbox = 'decl@vdk.vl.ru';
$m_pass = '2E7X2XNhLSlf';
$m_server = 'mail.vdk.vl.ru';
$m_site = 'http://decl.vdk.vl.ru/';
$m_company = 'ООО "ВДК"';
$m_sender = 'VDK';
$timezone = 'Asia/Vladivostok';

$debug = FALSE;