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

$database = database::getInstance();

include_once (_JLPATH_ROOT . DS . 'language' . DS . JCore::getCfg('lang') . DS . 'system.php');


$adminOffline = false;

if(!defined('_INSTALL_CHECK')){
	session_name(md5(_JLPATH_SITE));
	session_start();

	require_once(_JLPATH_ROOT . '/components/com_users/users.class.php');
	if(class_exists('mosUser') && $database != null){
		// восстановление некоторых переменных сессии
		$admin = new mosUser($database);
		$admin->id = intval(mosGetParam($_SESSION, 'session_user_id', ''));
		$admin->username = strval(mosGetParam($_SESSION, 'session_USER', ''));
		$admin->usertype = strval(mosGetParam($_SESSION, 'session_usertype', ''));
		$session_id = mosGetParam($_SESSION, 'session_id', '');
		$logintime = mosGetParam($_SESSION, 'session_logintime', '');

		// проверка наличия строки сессии в базе данных
		if($session_id == md5($admin->id . $admin->username . $admin->usertype . $logintime)){
			$query = "SELECT* FROM #__session WHERE session_id = " . $database->Quote($session_id) . " AND username = " . $database->Quote($admin->username) . "\n AND userid = " . intval($admin->id);
			$database->setQuery($query);
			if(!$result = $database->query()){
				echo $database->stderr();
			}

			if($database->getNumRows($result) == 1){
				define('_ADMIN_OFFLINE', 1);
			}
		}
	}
}

$config = Jconfig::getInstance();

if(!defined('_ADMIN_OFFLINE') || defined('_INSTALL_CHECK')){
	include_once (_JLPATH_ROOT . DS . 'language' . DS . JCore::getCfg('lang') . DS . 'system.php');
	require_once (_JLPATH_ROOT . DS . 'includes' . DS . 'version.php');

	$_VERSION = new joomlaVersion();
	$version = $_VERSION->CMS . ' ' . $_VERSION->CMS_VER . ' ' . $_VERSION->DEV_STATUS . ' [ ' . $_VERSION->CODENAME . ' ] ' . $_VERSION->RELDATE . ' ' . $_VERSION->RELTIME . ' ' . $_VERSION->RELTZ;

	if($database != null){
		// получение названия шаблона сайта по умолчанию
		$query = "SELECT template FROM #__templates_menu WHERE client_id = 0 AND menuid = 0";
		$database->setQuery($query);
		$cur_template = $database->loadResult();
		$path = "_JLPATH_ROOT/templates/$cur_template/index.php";
		if(!file_exists($path)){
			$cur_template = 'newline2';
		}
	} else{
		$cur_template = 'newline2';
	}

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
        <title><?php echo $config->config_sitename; ?> - <?php echo _SITE_OFFLINE; ?></title>
	<style type="text/css">
		@import url(<?php echo _JLPATH_SITE; ?>/administrator/templates/joostfree/css/admin_login.css);
	</style>
	<link rel="stylesheet" href="<?php echo _JLPATH_SITE; ?>/templates/css/offline.css" type="text/css"/>
	<?php
	// значок избранного (favicon)
	$config->config_favicon = $config->config_favicon ? $config->config_favicon : 'favicon.ico';
	$icon = _JLPATH_ROOT . '/images/' . $config->config_favicon;
	// checks to see if file exists
	$icon = (!file_exists($icon)) ? _JLPATH_SITE . '/images/favicon.ico' : _JLPATH_SITE . '/images/' . $config->config_favicon;
	?>
	<link rel="shortcut icon" href="<?php echo $icon; ?>"/>
</head>
<body>
<div id="joo">
	<img src="<?php echo _JLPATH_SITE;?>/administrator/templates/joostfree/images/logo_130.png" alt="Joostina!"/>
</div>
<div id="maindiv" align="center">
	<p>&nbsp;</p>
	<table align="center" class="outline">
		<tr>
			<td align="center">
				<img src="<?php echo _JLPATH_SITE; ?>/images/system/lotos.jpg" alt="<?php echo _SITE_OFFLINE?>" align="middle"/>
			</td>
		</tr>
		<tr>
			<td align="center">
				<h1><?php echo $config->config_sitename; ?></h1>
			</td>
		</tr>
		<?php
		if($config->config_offline == 1){
			?>
			<tr>
				<td width="39%" align="center">
					<strong><?php echo $config->config_offline_message; ?></strong>
				</td>
			</tr>
			<?php
		} elseif(isset($mosSystemError)){
			?>
			<tr>
				<td width="39%" align="center">
					<strong><?php echo $config->config_error_message; ?></strong>
					<br/>
					<span class="err"><?php echo defined('_SYSERR' . $mosSystemError) ? constant('_SYSERR' . $mosSystemError) : $mosSystemError; ?></span>
				</td>
			</tr>
			<?php
		} else{
			?>
			<tr>
				<td width="39%" align="center"><b><?php echo _INSTALL_WARN; ?></b></td>
			</tr>
			<?php
		}
		?>
	</table>
</div>
<div id="break"></div>
<div id="footer_off" align="center">
	<div align="center"><?php echo $version; ?></div>
</div>
</body>
</html>
<?php
	exit(0);
}