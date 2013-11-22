<?php

// Session start check
if(!session_name()) {
    header('Location: index.php');
    exit;
}

// Выполняем работу с базой данных
$request = 'SELECT `id`,`kpp`,`period`, `date` FROM `files` WHERE `inn` = '.$_SESSION['user_id'];
$result = mysqli_query($db,$request);
$done = mysqli_fetch_all($result,MYSQLI_ASSOC);
mysqli_free_result($result);

echo '
    <html><head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Служба деклараций</title>
    </head>
    <body>
    <h1>Пользоватль</h1>
    <table align="center" class="atable" border="1"><thead>
    <TR>
    <TH>КПП</TH>
    <TH>Период</TH>
    <TH>Добавлено</TH>
    <TH></TH>
    </TR></thead><tbody>';

for($i=0; $i<count($done); $i++) {
    echo '<TR>
        <TD>'.$done[$i]['kpp'].' </TD>
        <TD>'.$done[$i]['period'].' </TD>
        <TD>'.$done[$i]['date'].' </TD>
        <TD><a href="index.php?get_file='.$done[$i]['id'].'">Скачать</a> </TD>
        </TR>';
}

echo '</table>
    <br><a href="index.php?logout=1">Выйти</a>
    </body><html>';

?>