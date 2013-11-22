<?php

// Session start check
if(!session_name()) {
    header('Location: index.php');
    exit;
}

// Запрос на список пользователей
$query = 'SELECT * FROM `users` WHERE `admin` = 0';
$result = mysqli_query($db,$query);
$user_list = mysqli_fetch_all($result,MYSQLI_ASSOC);
mysqli_free_result($result);

echo '
    <html><head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Служба деклараций</title>
    </head>
    <body>
    <h1><a href="index.php">Админ</a>: список пользователей</h1>
    <table align="center" class="atable" border="1"><thead>
    <TR>
    <TH>INN</TH>
    <TH>PASS</TH>
    <TH>Mail</TH>
    </TR></thead><tbody>';

for($i=0; $i<count($user_list); $i++) {
    echo '<TR>
        <TD><a href="index.php?inn='.$user_list[$i]['inn'].'">'.$user_list[$i]['inn'].' </TD>
        <TD>'.$user_list[$i]['pass'].' </TD>
        <TD>'.$user_list[$i]['mail'].' </TD>
        </TR>';
}

echo '</table>';

echo $msg;

echo '<br><a href="index.php?logout=1">Выйти</a>
    </body><html>';

?>