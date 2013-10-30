<?php defined('_JLINDEX') or die();

/**
 * @package     Insert PHP
 * @copyright   Авторские права (C) 2000-2013 Gold Dragon.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @description Insert PHP -  - модуль для вставки PHP-кода в контент для Joostina 1.4.3+
 * @see         http://wiki.joostina-cms.ru/index.php/InsertPHP
 */

if (!function_exists('modInsertPhp')) {
    function modInsertPhp($params, $module)
    {
        $_error_code = null;

        // получаем код
        $code = $params->get('code', '');

        // На всякий случай удаляем лишние теги

        $code = trim($code);
        $code = preg_replace('#^<\?php#ius', '', $code);
        $code = preg_replace('#(<br \/>)*#ius', "", $code);
        $code = preg_replace('#\?>[\s]*$#ius', '', $code);
        $code = trim($code);

        // Временно отключаем вывод ошибок
        $_error = error_reporting();
        error_reporting(0);

        eval($code . ';');

        $_error_code = error_get_last();

        // Возвращаем состояние вывода ошибок
        error_reporting($_error);

        if (!is_null($_error_code) and $params->get('error', 0)) {
            $_lang = JLLang::getLang('mod.insert_php');
            echo sprintf($_lang['EXCEP_ERROR_PHP_CODE'], $module->title, $module->id);

            if ($params->get('error_type', 0)) {
                echo sprintf($_lang['EXCEP_ERROR_PHP_TYPE'], $_error_code['type']);
            }
            if ($params->get('error_message', 0)) {
                echo sprintf($_lang['EXCEP_ERROR_PHP_MES'], $_error_code['message']);
            }
            if ($params->get('error_line', 0)) {
                echo sprintf($_lang['EXCEP_ERROR_PHP_LINE'], $_error_code['line']);
            }
        }
    }
}

modInsertPhp($params, $module);











