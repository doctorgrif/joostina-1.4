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

define('_JLPATH_ROOT', __DIR__);

// подключение основных глобальных переменных
require_once __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'defines.php';

// проверка конфигурационного файла, если не обнаружен, то загружается страница установки
if(!file_exists('configuration.php') || filesize('configuration.php') < 10){
	$self = rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/';
	header('Location: http://' . $_SERVER['HTTP_HOST'] . $self . 'installation/index.php');
	exit();
}

// подключение файла конфигурации
require_once (_JLPATH_ROOT . DS . 'configuration.php');

// подключение главного файла - ядра системы
require_once (_JLPATH_ROOT . DS . 'core' . DS . 'core.php');

// подключение главного файла - ядра системы
// TODO GoDr: заменить со временем на core.php
require_once (_JLPATH_ROOT . DS . 'includes' . DS . 'joostina.php');

// отображение состояния выключенного сайта
if (JCore::getCfg('offline') == 1) {
	echo 'syte-offline';
	exit();
}

// автоматическая перекодировка в юникод, по умолчанию актвино
$utf_conv = intval(mosGetParam($_REQUEST, 'utf', 1));
$option = strval(strtolower(mosGetParam($_REQUEST, 'option', '')));
$task = strval(mosGetParam($_REQUEST, 'task', ''));

$commponent = str_replace('com_', '', $option);

if (JCore::getCfg('mmb_ajax_starts_off') == 0) {
	$_MAMBOTS->loadBotGroup('system');
	$_MAMBOTS->trigger('onAjaxStart');
}

// mainframe - основная рабочая среда API, осуществляет взаимодействие с 'ядром'
$mainframe = mosMainFrame::getInstance();

$mainframe->initSession();

$mainframe->set('lang', JCore::getCfg('lang'));
include_once($mainframe->getLangFile());

$my = JCore::getUser();

$gid = intval($my->gid);

if (JCore::getCfg('mmb_ajax_starts_off') == 0) {
	$_MAMBOTS->trigger('onAfterAjaxStart');
}

header("Content-type: text/html; charset=utf-8");
header("Cache-Control: no-cache, must-revalidate ");

// проверяем, какой файл необходимо подключить, данные берутся из пришедшего GET запроса
if (file_exists(_JLPATH_ROOT . "/components/$option/$commponent.ajax.php")) {
	include_once (_JLPATH_ROOT . "/components/$option/$commponent.ajax.php");
} else {
	die('error-1');
}