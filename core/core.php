<?php
/**
 * Joostina Lotos CMS 1.4.1
 *
 * @package   CORE
 * @version   1.4.1
 * @author    Gold Dragon <illusive@bk.ru>
 * @link      http://gd.joostina-cms.ru
 * @copyright 2000-2012 Gold Dragon
 * @license   GNU GPL: http://www.gnu.org/licenses/gpl-3.0.html
 *            Joostina Lotos CMS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL. (help/copyright.php)
 * @Date      02.07.2012
 */

// запрет прямого доступа
defined('_JLINDEX') or die();

/**
 * Основной класс - Ядро
 * @see http://wiki.joostina-cms.ru/index.php/JCore
 */
class JCore
{
    /** @var object Интерфейс класса ядра */
    private static $_instance;

    /** @var object Интерфейс класса конфигурации */
    private static $_config;

    /** @var object Интерфейс класса БД */
    private static $_db;

    /** @var object Интерфейс класса mosUser */
    private static $_user;

    /** @var object Интерфейс класса Language */
    private static $_lang;

    /**
     * Конструктор
     */
    private function __construct()
    {
    }

    /**
     * Подключение редактора
     */
    public static function connectionEditor()
    {
        global $_MAMBOTS;
        require_once _JLPATH_ROOT . '/includes/editor.php';
    }

    /**
     * @static Подключение класса
     * @return object
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            $class_name = __CLASS__;
            self::$_instance = new $class_name;
        }
        return self::$_instance;
    }

    /**
     * Возвращает интерфейс для работы с базой данных
     *
     * @return object GDDatabase
     */
    public static function getDB()
    {
        if (!isset(self::$_db)) {
            self::getLib('gddatabase');
            self::$_db = GDDatabase::Init();
        }
        return self::$_db;
    }

    /**
     * Возвращает интерфейс для работы с пользователем
     *
     * @return object
     * @see
     */
    public static function getUser()
    {
        if (!isset(self::$_user)) {
            $_mainframe = mosMainFrame::getInstance();
            self::$_user = $_mainframe->getUser();
        }
        return self::$_user;
    }

    /**
     * Подключение библиотек (/libraries/...)
     *
     * @param $str - имя библиотеки, оно же имя файла $str.php
     *
     * @return bool - false - нет файла, true - файл подключен
     */
    public static function getLib($str)
    {
        $file_lib = _JLPATH_LIBRARIES . '/' . $str . '.php';
        if (is_file($file_lib)) {
            require_once($file_lib);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Получение значения конфигурации
     *
     * @param $varname - параметр конфигурации
     *
     * @return JConfig|null|object - значение параметра
     */
    public static function getCfg($varname = null)
    {
        if (!isset(self::$_config)) {
            self::$_config = JConfig::getInstance();
        }
        if (is_null($varname)) {
            return self::$_config;
        } else {

            $varname = 'config_' . $varname;
            $varname = (isset(self::$_config->$varname)) ? self::$_config->$varname : null;
            return $varname;
        }
    }

    /**
     * Получает значение из глобальной переменной или массива
     *
     * @param array       $arr        - глобальный массив
     * @param string      $name       - параметр
     * @param string|null $def        - значение по умолчанию
     * @param string|null $is         - тип переменной
     *                                s  - строка
     *                                i  - число
     *                                n  - удалить пробельные символы в начале и конце
     *                                u  - декодирует URL-кодированную строку
     *                                sn - строка без пробельных символов в начале и конце
     *
     * @return null|string - значение из глобальной переменной
     */
    public static function getParam($arr, $name, $def = null, $is = null)
    {
        $result = null;
        if (isset($arr) and isset($arr[$name])) {
            $result = $arr[$name];
            if (!is_null($is)) {
                switch ($is) {
                    case 'sn':
                        $result = trim(strval($result));
                        break;
                    case 's':
                        $result = strval($result);
                        break;
                    case 'i':
                        $result = intval($result);
                        break;
                    case 'n':
                        $result = trim($result);
                        break;
                    case 'u':
                        $result = urldecode($result);
                        break;
                }
            }
            return $result;
        } else {
            return $def;
        }
    }

}

