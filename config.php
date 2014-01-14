<?php

// Session start check
if(!$welcome) {
    header('Location: index.php');
    exit;
}

// Set language
$lang = 'ru';

// Mail settings
$m_outbox = 'spam@bot.com';
$m_pass = 'Qwerty123';
$m_server = 'mail.google.com';
$m_site = 'http://spam.bot.ru/';
$m_company = 'Spam Inc';
$m_sender = 'Spam';
$timezone = 'Asia/Vladivostok';