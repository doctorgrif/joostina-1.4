<?php
/**
 * Joostina Lotos CMS 1.4.1
 *
 * @package   LANGUAGE
 * @version   1.4.1
 * @author    Gold Dragon <illusive@bk.ru>
 * @link      http://gd.joostina-cms.ru
 * @copyright 2000-2012 Gold Dragon
 * @license   GNU GPL: http://www.gnu.org/licenses/gpl-3.0.html
 *            Joostina Lotos CMS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL. (help/copyright.php)
 * @Date      30.08.2012
 */

// запрет прямого доступа
defined('_JLINDEX') or die();

/**
 * Класс для работы с языковыми файлами
 * @see http://wiki.joostina-cms.ru/index.php/JLLang
 *
 * @example
 *     типы языковых файлов
 *        'front' - системные клиентской части
 *        'admin' - системные администранивной части
 *        'com' - все компоненты
 *        'mod' - все модули
 *        'plg' - все плагины
 *        'tpl' - все шаблоны
 *     структура языкового файла соответвует требованиям для INI файлов
 *     комментарии начинаются с "точка с запятой"
 *     пример названия файла: com.mycomponent.lang.ini, mod.mymodules.lang.ini
 */
class JLLang
{

    /** @var array Массив Ключ-Значение */
    private static $language = array();

    private static $lang_codes
        = array(
            'bel' => 'belarusian', 'deu' => 'german', 'eng' => 'english', 'spa' => 'spanish', 'fra' => 'french', 'ita' => 'italian', 'rus' => 'russian', 'ukr' => 'ukrainian'
        );

    /**
     * Подключение языкового файла
     *
     * @param null $expansion - расширение
     * @param null $lang      - язык
     *
     * @return array - массив Ключ-Значение
     */
    public static function getLang($expansion = null, $lang = null)
    {
        $result = array();
        if (!is_null($expansion)) {

            // если язык не указан или не корректен, подключаем по умолчнию
            if (is_null($lang) and !preg_match('#([a-z]{3})#', $lang)) {
                $lang = array_search(JCore::getCfg('lang'), self::$lang_codes);
            }

            // на всякий случай корректируем расширение если вдруг ошибка
            $type_a = array('sys_', 'front_', 'admin_', 'com_', 'mod_', 'plg_', 'tpl_');
            $type_b = array('sys.', 'front.', 'admin.', 'com.', 'mod.', 'plg.', 'tpl.');
            $expansion = str_replace($type_a, $type_b, $expansion);

            // Проверяем существует ли уже языковой файл
            if (!isset(self::$language[$lang . '.' . $expansion])) {

                // подключаем языковой файл
                $pathFile = _JLPATH_LANG . '/' . $lang . '/' . $expansion . '.lang.ini';
                if (is_readable($pathFile)) {
                    $lang_ini = parse_ini_file($pathFile);
                    self::$language[$lang . '.' . $expansion] = $lang_ini;
                    $result = $lang_ini;
                }
            } else {
                $result = self::$language[$lang . '.' . $expansion];
            }
        }
        return $result;
    }

    /**
     * @static Подключение языковых файлов по категориям
     *
     * @param array $ind - тип языковых фалов
     *     'front' - системные клиентской части
     *     'admin' - системные администранивной части
     *     'com' - все компоненты
     *     'mod' - все модули
     *     'plg' - все плагины
     *     'tpl' - все шаблоны
     */
    public function loadAll($ind = array())
    {
//        if (!is_array($ind)) {
//            $ind = array($ind);
//        }
//        $fileNames = scandir(_JLPATH_LANG . DS . self::$lang);
//
//        $expansion = array();
//
//        foreach ($fileNames as $fileName) {
//            $tmp1 = preg_match('#^(front|admin|com|mod|plg|tpl)\.([a-z0-9_]+)(\.lang\.ini)$#', $fileName, $tmp2);
//            if ($tmp1) {
//                foreach ($ind as $value) {
//                    if ($tmp2[1] == $value) {
//                        self::$expansion = $tmp2[1] . '.' . $tmp2[2];
//                        $this->load();
//                        $expansion = array_merge($expansion, self::$language);
//                    }
//                }
//            }
//        }
//        self::$language = $expansion;
    }
}