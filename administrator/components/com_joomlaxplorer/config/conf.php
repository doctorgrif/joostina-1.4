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

//------------------------------------------------------------------------------
// login to use joomlaXplorer: (true/false)
$GLOBALS["require_login"] = false;

$GLOBALS["language"] = JCore::getCfg('lang');

// the filename of the QuiXplorer script: (you rarely need to change this)
if($_SERVER['SERVER_PORT'] == 443){
	$GLOBALS["script_name"] = "https://" . $GLOBALS['__SERVER']['HTTP_HOST'] . $GLOBALS['__SERVER']["PHP_SELF"];
} else{
	$GLOBALS["script_name"] = "http://" . $GLOBALS['__SERVER']['HTTP_HOST'] . $GLOBALS['__SERVER']["PHP_SELF"];
}

// allow Zip, Tar, TGz -> Only (experimental) Zip-support
if(function_exists("gzcompress")){
	$GLOBALS["zip"] = $GLOBALS["tgz"] = true;
} else{
	$GLOBALS["zip"] = $GLOBALS["tgz"] = false;
}

if(strstr(_JLPATH_ROOT, "/")){
	$GLOBALS["separator"] = "/";
} else{
	$GLOBALS["separator"] = "\\";
}

$GLOBALS["home_dir"] = JCore::getCfg('joomlaxplorer_dir');
$GLOBALS["home_url"] = _JLPATH_SITE;

// show hidden files in QuiXplorer: (hide files starting with '.', as in Linux/UNIX)
$GLOBALS["show_hidden"] = true;

// filenames not allowed to access: (uses PCRE regex syntax)
$GLOBALS["no_access"] = "^\.ht";

// user permissions bitfield: (1=modify, 2=password, 4=admin, add the numbers)
$GLOBALS["permissions"] = 7;