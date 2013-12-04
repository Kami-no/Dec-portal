<?php

session_start();

$msg='';
// Go to auth-db.php
include_once ('auth-db.php');

// Go to login.php if no result in auth-db.php
include_once ('login.php');
