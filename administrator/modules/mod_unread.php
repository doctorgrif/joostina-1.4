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

$my = JCore::getUser();

$cur_file_icons_path = _JLPATH_SITE . '/' . JADMIN_BASE . '/templates/' . JTEMPLATE . '/images/ico';

$sql = "SELECT COUNT(*)
		FROM #__messages
		WHERE state = ?
			AND user_id_to = ?";

$database = JCore::getDB();
$unread = $database->selectCell($sql, 0, $my->id);

if($unread){
	echo "<a class=\"adminmail\" href=\"index2.php?option=com_messages\" style=\"color: red; text-decoration: none;  font-weight: bold\"><img  src=\"" . $cur_file_icons_path . "/mail.png\" align=\"top\" border=\"0\" alt=\"Почта\" /> $unread </a>";
} else{
	echo "<a class=\"adminmail\" href=\"index2.php?option=com_messages\" style=\"color: black; text-decoration: none;\"><img src=\"" . $cur_file_icons_path . "/nomail.png\" align=\"top\" border=\"0\" alt=\"Почта\" /> $unread </a>";
}