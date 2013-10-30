<?php defined('_JLINDEX') or die(__FILE__);
/**
 * @package   Joostina
 * @copyright Авторские права (C) 2012 Joostina Lotos. Все права защищены.
 * @license   Лицензия http://www.gnu.org/licenses/gpl.html GNU/GPL, или help/license.php
 *            Joostina Lotos - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

/**
 * Четвёртая страница установки
 * @return mixed
 */
function fun4()
{
    $DBhostname = (isset($_POST['DBhostname'])) ? trim($_POST['DBhostname']) : '';
    $DBuserName = (isset($_POST['DBuserName'])) ? trim($_POST['DBuserName']) : '';
    $DBpassword = (isset($_POST['DBpassword'])) ? trim($_POST['DBpassword']) : '';
    $DBname = (isset($_POST['DBname'])) ? trim($_POST['DBname']) : '';
    $DBPrefix = (isset($_POST['DBPrefix'])) ? trim($_POST['DBPrefix']) : 'jos_';

    $sitename = (isset($_POST['sitename'])) ? trim($_POST['sitename']) : '';
    $siteUrl = (isset($_POST['siteUrl'])) ? trim($_POST['siteUrl']) : _JLPATH_SITE;
    $absolutePath = (isset($_POST['absolutePath'])) ? trim($_POST['absolutePath']) : _JLPATH_ROOT;
    $absolutePath = str_replace('\\', '/', $absolutePath);
    $adminLogin = (isset($_POST['adminLogin'])) ? trim($_POST['adminLogin']) : 'admin';
    $adminPassword = (isset($_POST['adminPassword'])) ? trim($_POST['adminPassword']) : '';
    $adminEmail = (isset($_POST['adminEmail'])) ? trim($_POST['adminEmail']) : '';

    $info['left'] = getLeft(1, 1, 1, 0, 0);
    $info['title'] = 'Настройка Сайта';

    $info['button'] = getButton(5, 'Далее', 'form');
    $info['content']
        = '
    <form action="index.php?page=5" method="post" name="form" id="form">
	    <input type="hidden" name="DBhostname" value="' . $DBhostname . '" />
	    <input type="hidden" name="DBuserName" value="' . $DBuserName . '"/>
	    <input type="hidden" name="DBpassword" value="' . $DBpassword . '"/>
	    <input type="hidden" name="DBname" value="' . $DBname . '"/>
	    <input type="hidden" name="DBPrefix" value="' . $DBPrefix . '"/>
    <table class="content" width="100%">
        <tr>
			<td>Название сайта</td>
			<td><input class="inputbox" type="text" name="sitename" size="40" value="'.$sitename.'"/></td>
			<td>Например: Мой новый сайт!</td>
		</tr>
        <tr>
            <td>URL сайта</td>
			<td><input class="inputbox" type="text" name="siteUrl" value="' . $siteUrl . '" size="40"/></td>
			<td><img src="img/info.png" alt="Внимание" style="float: left; padding-right: 5px" />Это значение как правило не требует вмешательства пользователя</td>
		</tr>
        <tr>
			<td>Абсолютный путь</td>
			<td><input class="inputbox" type="text" name="absolutePath" value="' . $absolutePath . '" size="40"/></td>
			<td><img src="img/info.png" alt="Внимание" style="float: left; padding-right: 5px" />Это значение как правило не требует вмешательства пользователя</td>
		</tr>
		<tr>
			<td>Ваш логин</td>
			<td><input class="inputbox" type="text" name="adminLogin" value="' . $adminLogin . '" size="40"/></td>
			<td>Используется как логин для авторизации главного Администратора сайта. Длина логина должна быть больше 2 и не больше 30 символов</td>
		</tr>
		<tr>
			<td>Пароль Администратора</td>
			<td><input class="inputbox" type="text" name="adminPassword" value="' . $adminPassword . '" size="40"/></td>
			<td>Пароль должен содержать минимум 8 символов и должен содержать минимум два символа отличных от цифр</td>
		</tr>
		<tr>
		    <td>Ваш E-mail</td>
			<td><input class="inputbox" type="text" name="adminEmail" value="' . $adminEmail . '" size="40"/></td>
			<td>Используется как адрес главного Администратора сайта</td>
		</tr>
	</table>
    </form>
    ';
    return $info;
}




































