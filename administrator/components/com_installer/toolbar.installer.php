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

$mainframe = mosMainFrame::getInstance();
require_once ($mainframe->getPath('toolbar_html'));
$task = JSef::getTask();
switch($task){
	case 'new':
		TOOLBAR_installer::_NEW();
		break;

	default:
		$element = strval(mosGetParam($_REQUEST, 'element', ''));
		if($element != 'installer'){
			TOOLBAR_installer::_DEFAULT2();
		} else{
			TOOLBAR_installer::_DEFAULT();
		}
		break;
}