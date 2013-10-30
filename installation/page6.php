<?php defined('_JLINDEX') or die(__FILE__);
/**
 * @package   Joostina
 * @copyright Авторские права (C) 2012 Joostina Lotos. Все права защищены.
 * @license   Лицензия http://www.gnu.org/licenses/gpl.html GNU/GPL, или help/license.php
 *            Joostina Lotos - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

/**
 * Шестая страница установки
 * @return mixed
 */
function fun6()
{
    //echo '<pre>';print_r($_POST);echo '</pre>';

    $siteUrl = (isset($_POST['siteUrl'])) ? trim($_POST['siteUrl']) : '';
    $adminLogin = (isset($_POST['adminLogin'])) ? trim($_POST['adminLogin']) : 'admin';
    $adminPassword = (isset($_POST['adminPassword'])) ? trim($_POST['adminPassword']) : '';

    $info['left'] = getLeft(1, 1, 1, 1, 1);
    $info['title'] = 'Завершение установки';

    $info['button'] = '<a id="inst" class="a_but" href="javascript:void(0)">Удалить INSTALLATION</a>';
    $info['button'] .= '<a class="a_but" href="' . $siteUrl . '">Перейти на сайт</a>';
    $info['button'] .= '<a class="a_but" href="' . $siteUrl . '/administrator">Перейти в Панель управления</a>';
    $info['content'] = '
    <h1>Поздравляем Вас!</h1><h1>Вы успешно установили Joostina Lotos CMS!</h1>
    <p>Сайт доступен по адресу: <a href="'.$siteUrl.'" target="_blank">'.$siteUrl.'</a></p>
    <p>Панель управления доступна по адресу: <a href="'.$siteUrl.'/administrator" target="_blank">'.$siteUrl.'/administrator</a></p>
    <p>Данные для входа в <b>Панель управления:</b>
    <ul>
        <li>Логин: <b>'.$adminLogin.'</b></li>
        <li>Пароль: <b>'.$adminPassword.'</b></li>
    </ul>
    </p>
    <br>
    <div class="install-text">В ЦЕЛЯХ БЕЗОПАСНОСТИ ВЫ ДОЛЖНЫ УДАЛИТЬ КАТАЛОГ <b>INSTALLATION</b>!.</div>
<script>
    $(function () {
        $("#inst").click(function () {
            $.get("'.$siteUrl.'/installation/install.ajax.php?task=rminstalldir", function (data) {
                $("#inst").remove();
            });
        });
    });
</script>
    ';

    return $info;
}




































