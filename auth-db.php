<?php

// Session start check
if(!session_name()) {
    header('Location: index.php');
    exit;
};

// Import LDAP config
include_once ('config-db.php');

// Logout
if (isset($_GET['logout'])) {
      if (isset($_SESSION['user_id'])) {
            unset($_SESSION['user_id']);
            unset($_SESSION['admin']);
            header('Location: index.php');
            exit;
      }
}

// Connect to DB
$db = new mysqli($db_host, $db_user, $db_pass, $db_base);
if(mysqli_connect_errno()) {
            $msg = "MySQL connection failed: ".mysqli_connect_error();
            setcookie('err',$msg);
            header('Location: index.php');
            exit;
}

// File-requests go to download.php
if(isset($_GET['get_file']) && isset($_SESSION['user_id'])) {
      include('download.php');
      mysqli_close($db);
      exit;
}

// User-list requests go to page-admins-list.php
if(isset($_GET['user_list']) && isset($_SESSION['admin'])) {
      include('page-admins-list.php');
      mysqli_close($db);
      exit;
}

// Edit user
if(isset($_GET['inn']) && isset($_SESSION['admin'])) {
      include('page-admins-usermod.php');
      mysqli_close($db);
      exit;
}

// Authorized admins go to page-admins.php
if (isset($_SESSION['admin'])) {
      include('page-admins.php');
      mysqli_close($db);
      exit;
}

// Authorized users go to page-users.php
if (isset($_SESSION['user_id'])) {
      include('page-users.php');
      mysqli_close($db);
      exit;
}

// Authorize user
if (isset($_POST['login']) && isset($_POST['password'])) {
      $login = $_POST['login'];
      $password = $_POST['password'];
      
      $request = 'SELECT `inn` FROM `users` WHERE `inn` = '.$login.' AND `pass` = '.$password;
      $result = mysqli_query($db,$request);
      $users = mysqli_num_rows($result);
      mysqli_free_result($result);

      // User authorization
      if ($users == 1) {
            $_SESSION['user_id'] = $login;
      } else {
            mysqli_close($db);
            setcookie('err','Неправильный логин или пароль.');
            header('Location: index.php');
            exit;
      }

      $request = 'SELECT `inn` FROM `users` WHERE `inn` = '.$login.' AND `admin` = 1';
      $result = mysqli_query($db,$request);
      $admins = mysqli_num_rows($result);
      mysqli_free_result($result);
      
      // Authorized admins go to admins's page and others go to user's page
      if ($admins == 1) {
            $_SESSION['admin'] = 1;
            include('page-admins.php');
            mysqli_close($db);
            exit;
      } else {
            include('page-users.php');
            mysqli_close($db);
            exit;
      }
}
?>