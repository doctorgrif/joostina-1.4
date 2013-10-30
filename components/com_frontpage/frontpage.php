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

require_once ($mainframe->getPath('class', 'com_frontpage'));

$frontpageConf = (object)null;
$configObject = new frontpageConfig();

$database = JCore::getDB();

$sql = "SELECT `value` FROM #__config WHERE `name` = ? AND `group` = ? AND `subgroup` = ?";
$frontpageConf->directory = $configObject->_parseValue($database->selectCell($sql, 'directory', 'com_frontpage', 'default'));

$sql = "SELECT `value` FROM #__config WHERE `name` = ? AND `group` = ? AND `subgroup` = ?";
$frontpageConf->task = $configObject->_parseValue($database->selectCell($sql, 'page', 'com_frontpage', 'default'));

// code handling has been shifted into content.php
require_once (_JLPATH_ROOT . '/components/com_boss/boss.php');