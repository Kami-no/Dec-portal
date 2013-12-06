<?php

// Session start check
if(!session_name()) {
    header('Location: index.php');
    exit;
}

$target = "upload/";
$target = $target . basename( $_FILES['uploaded_org']['name']) ;
$ok=1;
if(move_uploaded_file($_FILES['uploaded_org']['tmp_name'], $target))
{
    echo "The file ". basename( $_FILES['uploaded_org']['name']). " has been uploaded";
}
else {
    echo "Sorry, there was a problem uploading your file.";
}

function import_csv(
    $table, 		// Имя таблицы для импорта
    $afields, 		// Массив строк - имен полей таблицы
    $filename, 	 	// Имя CSV файла, откуда берется информация
                    // (путь от корня web-сервера)
    $delim=',',  		// Разделитель полей в CSV файле
    $escaped='\\', 	 	// Ставится перед специальными символами
    $lineend='\\r\\n',   	// Чем заканчивается строка в файле CSV
    $hasheader=FALSE){  	// Пропускать ли заголовок CSV

    if($hasheader) $ignore = 'IGNORE 1 LINES ';
    else $ignore = '';
    $q_import =
        'LOAD DATA INFILE "'.
        $_SERVER["DOCUMENT_ROOT"].$filename.'" INTO TABLE '.$table.' '.
        'FIELDS TERMINATED BY "'.$delim.
        '"    ESCAPED BY "'.$escaped.'" '.
        'LINES TERMINATED BY "'.$lineend.'" '.
        $ignore.
        '('.implode(',', $afields).')';
    return mysql_query($q_import);
}
