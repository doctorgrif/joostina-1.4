<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// Установка флага родительского файла
define('_JLINDEX', 1);

// корень файлов
define('_JLPATH_ROOT',dirname(dirname(__FILE__)));

// подключение основных глобальных переменных
require_once _JLPATH_ROOT . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'defines.php';

if(!defined('IS_ADMIN')) define('IS_ADMIN', 1);

require_once (_JLPATH_ROOT . DS . 'configuration.php');

// подключение главного файла - ядра системы
require_once (_JLPATH_ROOT . DS . 'core' . DS . 'core.php');


// подключаем ядро
require_once (_JLPATH_ROOT . DS . 'includes' . DS . 'joostina.php');

// создаём сессии
session_name(md5(_JLPATH_SITE));
session_start();

header("Content-type: text/html; charset=utf-8");
header("Cache-Control: no-cache, must-revalidate ");

$option = strval(strtolower(mosGetParam($_REQUEST, 'option', '')));
$task = strval(mosGetParam($_REQUEST, 'task', ''));

// mainframe - основная рабочая среда API, осуществляет взаимодействие с 'ядром'
$mainframe = mosMainFrame::getInstance(true);
$mainframe->set('lang', JCore::getCfg('lang'));
require_once($mainframe->getLangFile());

// получение шаблона страницы
$cur_template = $mainframe->getTemplate();
define('JTEMPLATE', $cur_template);

require_once (_JLPATH_ADMINISTRATOR . DS . 'includes' . DS . 'admin.php');

$my = $mainframe->initSessionAdmin($option, $task);

if(!$my->id){
	die('error-my');
}

// запускаем мамботты событий onAfterAdminAjaxStart
if(JCore::getCfg('mmb_ajax_starts_off') == 0){
	$_MAMBOTS->loadBotGroup('admin');
	$_MAMBOTS->trigger('onAfterAdminAjaxStart');
}

$commponent = str_replace('com_', '', $option);

initGzip();
// файл обработки Ajax запрсоов конкртеного компонента
$file_com = _JLPATH_ADMINISTRATOR . DS . 'components' . DS . $option . DS . 'admin.' . $commponent . '.ajax.php';
// проверяем, какой файл необходимо подключить, данные берутся из пришедшего GET запроса
if(file_exists($file_com)){
	//Подключаем язык компонента
	if($mainframe->getLangFile($option)){
		include($mainframe->getLangFile($option));
	}
	include_once ($file_com);
} else{
	die('error-inc-component');
}

doGzip();