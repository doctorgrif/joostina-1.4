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
$cur_file_icons_path = _JLPATH_SITE . '/' . JADMIN_BASE . '/templates/' . JTEMPLATE . '/images/ico';

$session_id = stripslashes(mosGetParam($_SESSION, 'session_id', ''));

// Get no. of users online not including current session
$database = JCore::getDB();
$sql = "SELECT COUNT( session_id )
		FROM #__session
		WHERE session_id != ?";
$online_num = intval($database->selectCell($sql, $session_id));

?>
<span class="mod_online">
	<img src="<?php echo $cur_file_icons_path;?>/users.png" alt="<?php echo _MOD_ONLINE_USERS;?>"/>
	<strong><?php echo $online_num; ?></strong>
</span>