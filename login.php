<?php

// Session start check
if(!$welcome) {
    header('Location: index.php');
    exit;
}

if (isset($_COOKIE['err'])) {
    $msg = $_COOKIE['err'];
    setcookie('err','');

}

// reCAPTCHA pub key
$publickey = '6LdTOOwSAAAAANgdFVrj2Z15wEfzsCDqJKBZan07';

// Login form
echo '<!DOCTYPE html>
    <meta charset="utf-8">
    <title>'.$m_company.'</title>
    <div align="center"><img src="img/title.jpg" /></div>
    <h1 align="center">Служба деклараций '.$m_company.'</h1>
    <form action="index.php" method="post">
        <table align="center">
            <tr><td>
                <table align="center">
                    <tr>
                        <td>E-Mail:</td>
                        <td><input type="text" name="login" /></td>
                    </tr>
                    <tr>
                        <td>Пароль:</td>
                        <td><input type="password" name="password" /></td>
                    </tr>
                </table>
            </td></tr>
            <tr><td>Введите текст с обоих картинок, разделив их пробелом.</td></tr>
            <tr><td align="center">'.recaptcha_get_html($publickey).'</td></tr>
            <tr><td align="center"><input type="submit" value="Войти" /></td></tr>
            <tr><td align="center">'.$msg.'</td></tr>
        </table>
    </form>';

echo '<h2>ВАЖНО!!! Вниманию декларантов!!!</h2>
    <ul>
    <li>Корректность сведений об объёмах отгруженной алкогольной продукции, предоставленной на этом ресурсе, мы гарантируем в 18-ых числах месяца, следующего за истекшим отчётным периодом. Мы регулярно вносим изменения, по зависящим и независящим от нас причинам. Точную актуальную дату формирования данных мы опубликуем.</li>
    <li>Отгрузка осуществляется только со следующего склада: Россия, 690034, Приморский край, Владивосток г, Фадеева ул, д.32, здание-овощехранилище, лит.А, этаж 2, помещение 1, номер по плану 2 (S=390,3 кв.м), 9 (S=783,7 кв.м), 15 (S=67,4 кв.м), 16 (S=455,3 кв.м), 18 (S=310,4 кв.м), 22 (S=514,4 кв.м), 23 (S=272,9 кв.м)</li>
    <li>В случае расхождения данных в файле xlsx и результата импорта в программу «Декларант-Алко», обратите внимание на соответствие производителей наших и своих в справочнике «Контрагенты» и отгрузки по белорусским производителям.</li>
    <li>Наш юридический КПП 253601001 НЕ ИСПОЛЬЗУЕТСЯ в декларировании. Работал только один склад с КПП обособленного подразделения 253645001, его и используйте и никакой другой.</li>
    <li>Лицензия серия РА № 000097 рег. № 25ЗАП0001510 от "17" января 2013г. по "01" декабря 2017г.</li>
    <li>Файлы с данными находятся в архиве, для их открытия можно использовать бесплатный архиватор <a href="http://downloads.sourceforge.net/sevenzip/7z920.exe">7-zip</a>.</li>
    <li>Акт сверки в формате Microsoft Office 2007 - xlsx. Для того, чтобы открыть его в Microsoft Office 2003 необходимо установить <a href="http://www.microsoft.com/ru-ru/download/details.aspx?id=3">File Format Converter</a>.</li>
    </ul>
    <b>Будьте внимательны.</b>';
