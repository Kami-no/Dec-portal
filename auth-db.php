<?php

// Session start check
if(!session_name()) {
    header('Location: index.php');
    exit;
};

// Logout
if (isset($_GET['logout'])) {
    unset($_SESSION['user_id']);
    unset($_SESSION['admin']);
    header('Location: index.php');
    exit;
}

// Import DB config
include_once ('config-db.php');

// Connect to DB
$db = new mysqli($db_host, $db_user, $db_pass, $db_base);
if(mysqli_connect_errno()) {
    $msg = "MySQL connection failed: ".mysqli_connect_error();
    setcookie('err',$msg);
    header('Location: index.php');
    exit;
}

// File-requests
if(isset($_GET['get_file']) && isset($_SESSION['user_id'])) {
    include('download.php');
    mysqli_close($db);
    exit;
}

// User-list requests
if(isset($_GET['user_list']) && isset($_SESSION['admin'])) {
    include('page-admins-list.php');
    mysqli_close($db);
    exit;
}

// Edit user
if(isset($_GET['id']) && isset($_SESSION['admin'])) {
    include('page-admins-usermod.php');
    mysqli_close($db);
    exit;
}

// Authorized admins
if (isset($_SESSION['admin'])) {
    include('page-admins.php');
    mysqli_close($db);
    exit;
}

// Authorized users
if (isset($_SESSION['user_id'])) {
    include('page-users.php');
    mysqli_close($db);
    exit;
}

// Authorize user
if (isset($_POST['login']) && isset($_POST['password'])) {
    $request = 'SELECT `id`, `admin` FROM `users` WHERE `mail` = "'.$_POST['login'].'" AND `pass` = '.$_POST['password'];
    $result = mysqli_query($db,$request);
    $done = mysqli_fetch_all($result,MYSQLI_ASSOC);
    $users = mysqli_num_rows($result);
    //mysqli_free_result($result);

    // User authorization
    if ($users == 1) {
        $_SESSION['user_id'] = $done['0']['id'];
    } else {
        mysqli_close($db);
        setcookie('err','Неправильный логин или пароль.');
        header('Location: index.php');
        exit;
    }

    // Authorized admins go to admins' page and others go to user's page
    // TODO: verify setting SESSION equal $done
    if ($done['0']['admin'] == 1) {
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
