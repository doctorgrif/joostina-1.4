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

// load language file
include_once ('language/' . JCore::getCfg('lang') . '/system.php');

?>
<!-- Change doctipe to html5, clean html output, change charset
@doctorgrif (30.10.13 09:04 -->
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title><?php echo _404; ?> - <?php echo JCore::getCfg('sitename'); ?></title>
	<style type="text/css">
		body {
			font-family: Arial, Helvetica, Sans Serif;
			font-size: 11px;
			color: #333333;
			background: #ffffff;
			text-align: center;
		}
	</style>
</head>
<body>
<h2><?php echo JCore::getCfg('sitename'); ?></h2>

<h2><?php echo _404; ?></h2>

<h3>
	<a href="<?php echo _JLPATH_SITE; ?>"><?php echo _404_RTS; ?></a>
</h3>
</body>
</html>
