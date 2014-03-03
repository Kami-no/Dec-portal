<?php

// Session start check
if(!$welcome) {
    header('Location: index.php');
    exit;
}

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
$db = new mysqli($db_host, $db_user, $db_pass, $db_base) or die('boom');
if (mysqli_connect_errno()) {
    $msg = "MySQL connection failed: ".mysqli_connect_error();
    setcookie('err',$msg);
    include_once ('login.php');
    exit;
}

if (isset($_GET['spam'])) {
    include('spam.php');
    mysqli_close($db);
    exit;
}

// Authorized user
if (isset($_SESSION['user_id'])) {
    // Get file for authorized user
    if (isset($_GET['get_file'])) {
        include('download.php');
        mysqli_close($db);
        exit;
    }
    // For admins
    if ($_SESSION['admin'] == 1) {
        // List users
        if (isset($_GET['user_list'])) {
            include('page-admins-list.php');
            mysqli_close($db);
            exit;
        }
        // Edit user
        if (isset($_GET['id'])) {
            include('page-admins-usermod.php');
            mysqli_close($db);
            exit;
        }
        // Import organizations
        if (isset($_FILES['upload_org'])) {
            include('org-import.php');
            mysqli_close($db);
            exit;
        }
        // Send new password to user
        if (isset($_GET['mid'])) {
            include('includes/spam.php');
            mysqli_close($db);
            exit;
        }
        // Go to admin's home
        include('page-admins.php');
        mysqli_close($db);
        exit;
    } else {
        // Go to user's home
        include('page-users.php');
        mysqli_close($db);
        exit;
    }
}

// Activate reCAPTCHA
require_once('recaptcha/recaptchalib.php');

// Authorize user
if (isset($_POST['login']) && isset($_POST['password'])) {
    // CAPTCHA check
    $privatekey ='6LdTOOwSAAAAAN-PGbIBEi-Xvodf_yOIm72u0ciu';
    $resp = recaptcha_check_answer ($privatekey,$_SERVER['REMOTE_ADDR'],$_POST['recaptcha_challenge_field'],$_POST['recaptcha_response_field']);
    if (!$resp->is_valid) {
        // wrong CAPTCHA
        setcookie('err','Неправильно введены данные с картинки: '.$resp->error);
    } else {
        // right CAPTCHA
        $request = 'SELECT `id`, `admin` FROM `users` WHERE `mail` = "'.$_POST['login'].'" AND `pass` = '.$_POST['password'];
        $result = mysqli_query($db,$request);
        $done = mysqli_fetch_all($result,MYSQLI_ASSOC);
        $users = mysqli_num_rows($result);
        mysqli_free_result($result);

        // User authorization
        if ($users == 1) {
            $_SESSION['user_id'] = $done['0']['id'];
            $_SESSION['admin'] = $done['0']['admin'];
        } else {
            setcookie('err','Неправильный логин или пароль.');
        }
    }

    mysqli_close($db);
    header('Location: index.php');
    exit;
}
