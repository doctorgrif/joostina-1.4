<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_JLINDEX') or die();

require_once (_JLPATH_ROOT . '/configuration.php');

$basePath = dirname(__file__);

// подключение главного файла - ядра системы
require_once (_JLPATH_ROOT . '/core/core.php');

require_once(_JLPATH_ROOT . '/includes/joostina.php');

$my = JCore::getUser();

session_name(md5(_JLPATH_SITE));
session_start();

header('Content-type: text/html; charset=UTF-8');

$database = database::getInstance();

// restore some session variables
if(!isset($my)){
	$my = new mosUser($database);
}

$my->id = intval(mosGetParam($_SESSION, 'session_user_id', ''));
$my->username = strval(mosGetParam($_SESSION, 'session_USER', ''));
$my->usertype = strval(mosGetParam($_SESSION, 'session_usertype', ''));
$my->gid = intval(mosGetParam($_SESSION, 'session_gid', ''));
$session_id = strval(mosGetParam($_SESSION, 'session_id', ''));
$logintime = strval(mosGetParam($_SESSION, 'session_logintime', ''));

if($session_id != md5($my->id . $my->username . $my->usertype . $logintime)){
	mosRedirect('index.php');
	die;
}