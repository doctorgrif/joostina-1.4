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

mosAdminMenus::menuItem($type);

$directory = getDirectory($menu);
$task = JSef::getTask();
switch($task){

	case 'edit':
		boss_search_menu::editCategory($cid[0], $menutype, $option, $menu, $directory);
		break;

	case 'save':
	case 'apply':
	case 'save_and_new':
		saveMenu($option, $task);
		break;
}