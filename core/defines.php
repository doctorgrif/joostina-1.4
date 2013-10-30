<?php
/**
 * Joostina Lotos CMS 1.4.1
 * @package   DEFINITIONS
 * @version   1.0
 * @author    Gold Dragon <illusive@bk.ru>
 * @link      http://gd.joostina-cms.ru
 * @copyright 2000-2012 Gold Dragon
 * @license   GNU GPL: http://www.gnu.org/licenses/gpl-3.0.html
 *            Joostina Lotos CMS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL. (help/copyright.php)
 *            Date: 18.06.2012
 */

// запрет прямого доступа
defined('_JLINDEX') or die();

// Глобальные определения.
define('DS', DIRECTORY_SEPARATOR);

// абсолютный путь до библиотек
define('_JLPATH_LIBRARIES', _JLPATH_ROOT . DS . 'libraries');

// абсолютный путь до sef-файлов
define('_JLPATH_SEF', _JLPATH_ROOT . DS . 'settings' . DS . 'sef');

// абсолютный путь до каталога панели управления
define('_JLPATH_ADMINISTRATOR', _JLPATH_ROOT . DS . 'administrator');

// абсолютный путь до каталога с языковыми файлами
define('_JLPATH_LANG', _JLPATH_ROOT . DS . 'language');

// абсолютный путь до каталога с языковыми файлами
define('_JLPATH_TEMPLATES', _JLPATH_ROOT . DS . 'templates');

// Адрес сайта
define('_JLPATH_SITE', "http://" . $_SERVER['SERVER_NAME']);

// функции отладки
function _x($var, $text = '<pre>'){
	echo $text;
	var_export($var);
	echo "\n";
}

function _v($var){
	echo '<pre style="border:1px solid #ff0000;color:#ff0000;padding:5px;background-color:#ffffff;">';
	var_dump($var);
	echo "</pre>";
}

function _p($var){
	echo '<pre style="border:1px solid #ff0000;color:#ff0000;padding:5px;background-color:#ffffff;">';
	print_r($var);
	echo "</pre>";
}
function _a($var = null)
{
	echo '<span style="border:1px solid #ff0000;color:#ff0000;padding:1px;background-color:#ffffff;">';
	if (is_null($var)) {
		echo '+++++++++';
	} else {
		echo $var;
	}
	echo '</span>';
}

function _d()
{
	echo '<span style="border:1px solid #ff0000;color:#ff0000;padding:1px;background-color:#ffffff;">';
	echo '+++++++++';
	echo '</span>';
	die(__LINE__ . ':' . __FILE__);
}






























