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

$mainframe->addCSS(_JLPATH_SITE . '/templates/css/print.css');
$mainframe->addJS(_JLPATH_SITE . '/includes/js/print/print.js');

$pg_link = str_replace(array('&pop=1', '&page=0'), '', $_SERVER['REQUEST_URI']);
$pg_link = str_replace('index2.php', 'index.php', $pg_link);

?>
<!-- Change doctipe clean html output
@doctorgrif (30.10.13 09:18 -->
<div class="logo"><?php echo JCore::getCfg('sitename'); ?></div>
<div id="main"><?php echo $_MOS_OPTION['buffer'];?> </div>
<div id="ju_foo">
	<p><?php echo _PRINT_PAGE_LINK; ?>: 
	<em><?php echo JSef::getUrlToSef($pg_link); ?></em>
	</p>
	<p>&copy;<?php echo JCore::getCfg('sitename'); ?>,&nbsp;'<?php echo date('Y'); ?></p>
</div>