<?php
/***
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_JLINDEX') or die();

/**
 * Информация о версии
 * @package Joostina
 */
class joomlaVersion{
	/** @var строка CMS*/
	var $CMS = 'Joostina Lotos';
	/** @var версия*/
	var $CMS_VER = '1.4.4';
	/** @var int Подверсия*/
	var $DEV_LEVEL = '0';
	/** @var int Номер сборки*/
	var $BUILD = 'r147';
    /** @var строка  статус разработки*/
    var $DEV_STATUS = 'beta';
	/** @var string Кодовое имя*/
	var $CODENAME = 'Сaspica';
	/** @var string Дата*/
	var $RELDATE = '20.09.2013';
	/** @var string Время*/
	var $RELTIME = '20:06';
	/** @var string Временная зона*/
	var $RELTZ = '+3 GMT';
	/** @var string Текст авторских прав*/
	var $COPYRIGHT = 'Авторские права &copy; 2011-2013 Joostina Lotos. Все права защищены.';
	/** @var string URL*/
	var $URL = '<a href="http://joostina-cms.ru" target="_blank" title="Система создания и управления сайтами Joostina Lotos CMS">Joostina Lotos!</a> - бесплатное и свободное программное обеспечение для создания сайтов, распространяемое по лицензии GNU/GPL.';
	/** @var string для реального использования сайта установите = 1 для демонстраций = 0: 1 используется по умолчанию*/
	var $SITE = 1;
	/** @var string Whether site has restricted functionality mostly used for demo sites: 0 is default*/
	var $RESTRICT = 0;
	/** @var string Whether site is still in development phase (disables checks for /installation folder) - should be set to 0 for package release: 0 is default*/
	var $SVN = 0;
	/** @var string центр поддержки */
	var $SUPPORT_CENTER = 'http://joostina-cms.ru';
	/** @var string ссылки на сайты поддержки*/
	var $SUPPORT = 'Поддержка: <a href="http://joostina-cms.ru" target="_blank" title="Официальный сайт CMS Joostina">joostina-cms.ru</a> | <a href="http://wiki.joostina-cms.ru" target="_blank" title="Wiki-документация">wiki.joostina-cms.ru</a>';

	/** * @return string Длинный формат версии */
	public static function getLongVersion(){
        $_version = new joomlaVersion();
        $version = $_version->CMS . ' ' . $_version->CODENAME . ' ' . $_version->CMS_VER . '.' . $_version->DEV_LEVEL . ' [' . $_version->DEV_STATUS . ' : ' . $_version->BUILD . '] ';
        return $version;
	}

	/*** @return string Краткий формат версии */
    public function getShortVersion(){
		return $this->CMS_VER . '.' . $this->DEV_LEVEL;
	}

	/*** @return string Version suffix for help files*/
    public function getHelpVersion(){
		return '.' . str_replace('.', '', $this->CMS_VER);
	}

	// получение переменных окружения информации осистеме
	public static function get($name){
		$v = new joomlaVersion();
		return $v->$name;
	}
}