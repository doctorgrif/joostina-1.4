<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

/** проверка включения этого файла файлом-источником*/
defined('_JLINDEX') or die();

require_once (_JLPATH_ROOT . '/includes/joostina.php');
include_once (_JLPATH_ROOT . DS . 'language' . DS . JCore::getCfg('lang') . DS . 'system.php');

$option = JSef::getOption();
$database = database::getInstance();

// получение шаблона страницы
$cur_template = @JTEMPLATE;
if(!$cur_template){
	$cur_template = 'newline2';
}

// Вывод HTML

// требуется для разделения номера ISO из константы языкового файла _ISO
$iso = explode('=', _ISO);
// xml prolog
echo '<?xml version="1.0" encoding="' . $iso[1] . '"?' . '>';
?>
<!-- Change doctipe to html5, clean html output, change charset
@doctorgrif (30.10.13 09:04 -->
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title><?php echo JCore::getCfg('sitename'); ?> - <?php echo _SITE_OFFLINE?></title>
	<link rel="stylesheet" href="<?php echo _JLPATH_SITE; ?>/templates/<?php echo $cur_template; ?>/css/template_css.css" type="text/css"/>
        <style>
		table.moswarning {
			font-size: 200%;
			background-color: #c00;
			color: #fff;
			border-bottom: 2px solid #600
		}

		table.moswarning h2 {
			padding: 0;
			margin: 0;
			text-align: center;
			font-family: Arial, Helvetica, sans-serif;
		}

	</style>
</head>
<body style="margin: 0px; padding: 0px;">

<table width="100%" align="center" class="moswarning">
	<?php
	if(JCore::getCfg('offline') == 1){
		?>
		<tr>
			<td>
				<h2>
					<?php
					echo JCore::getCfg('sitename');
					echo ' - ';
					echo JCore::getCfg('offline_message');
					?>
				</h2>
			</td>
		</tr>
		<?php
	} elseif(@$mosSystemError){

		?>
		<tr>
			<td>
				<h2>
					<?php echo JCore::getCfg('error_message'); ?>
				</h2>
				<?php echo $mosSystemError; ?>
			</td>
		</tr>
		<?php
	} else{
		?>
		<tr>
			<td>
				<h2>
					<?php echo INSTALL_WARN; ?>
				</h2>
			</td>
		</tr>
		<?php
	}
	?>
</table>

</body>
</html>