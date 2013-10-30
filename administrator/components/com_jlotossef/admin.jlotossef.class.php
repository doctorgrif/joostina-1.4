<?php defined('_JLINDEX') or die();

/**
 * JLotos SEF - Компонент для управления SEF (ЧПУ)
 *
 * @package   JLotosSEF
 * @version   1.0
 * @author    Gold Dragon <illusive@bk.ru>
 * @link      http://gd.joostina-cms.ru
 * @copyright 2000-2013 Gold Dragon
 * @license   GNU GPL: http://www.gnu.org/licenses/gpl-3.0.html
 * @date      01.07.2013
 * @see       http://wiki.joostina-cms.ru/index.php/JLotosSEF
 */

class JLotosSefClass
{
    /** @var array настройки */
    private static $config = array();

    /** @var array Языковые константы */
    private static $lang = array();

    /**
     * Запись дубликата
     */
    public static function saveDup()
    {
        $_db = JCore::getDB();
        $row_dup = array();
        $row_ref = array();
        $_error = array();
        $_lang = self::getLang();

        $row_ref['url'] = str_replace(_JLPATH_SITE, '', JCore::getParam($_POST, 'ref_url', '', 'sn'));
        $row_ref['sef'] = str_replace(_JLPATH_SITE, '', JCore::getParam($_POST, 'ref_sef', '', 'sn'));

        $row_dup['url'] = str_replace(_JLPATH_SITE, '', JCore::getParam($_POST, 'dup_url', '', 'sn'));
        $row_dup['sef'] = str_replace(_JLPATH_SITE, '', JCore::getParam($_POST, 'dup_sef', '', 'sn'));
        if (empty($row_dup['sef']) or $row_dup['sef'] == '/') {
            $_error[] = $_lang['JLSEF_CFG_LINK_NO_SEF'];
        }else{
            if ($row_dup['sef'][0] != '/') {
                $row_dup['sef'] = '/' . $row_dup['sef'];
            }

            $row_dup['id'] = JCore::getParam($_POST, 'id', 0, 'i');

            $url = $_db->selectCell("SELECT `url` FROM `#__sef_link` WHERE `sef`=?", $row_dup['sef']);
            if (!empty($url)) {
                $_error[] = $_lang['JLSEF_CFG_LINK_NO_URL'];
            }
        }

        if (sizeof($_error)) {
            JLotosSefHtml::editDup($row_dup, $row_ref, self::getLang(), implode('<br />', $_error));
        } else {
           // нормализуем ссылку
            $row_dup['url'] = str_replace('&amp;', '&', $row_dup['url']);
            $row_dup['url'] = str_replace('&', '&amp;', $row_dup['url']);

            // Добавляем ссылку на таблицу ссылок
            $sql = "INSERT INTO `#__sef_link` (`url`, `sef`) VALUES (?, ?);";
            $_db->insert($sql, $row_dup['url'], $row_dup['sef']);

            // Удаляем ссылку из дубликатов
            $sql = "DELETE FROM `#__sef_duplicate` WHERE  `id`= ?;";
            $_db->delete($sql, $row_dup['id']);

            mosRedirect('index2.php?option=com_jlotossef&amp;task=listdup', $_lang['JLSEF_CFG_LINK_SAVE']);
        }
    }
    /**
     * Редактирование дубликата
     *
     * @param $id - идентификатор ссылки
     */
    public static function editDup($id)
    {
        $_db = JCore::getDB();
        $row_dup = $_db->selectRow("SELECT `id`, `url`, `sef` FROM `#__sef_duplicate` WHERE `id` = ?;", $id);
        $row_ref = $_db->selectRow("SELECT `id`, `url`, `sef` FROM `#__sef_link` WHERE `sef` = ?;", $row_dup['sef']);
        JLotosSefHtml::editDup($row_dup, $row_ref, self::getLang());
    }

    /**
     * Удаление дубликатов
     */
    public static function pageRemoveDup($cid)
    {

        $_lang = self::getLang();

        if (sizeof($cid)) {
            $cids = implode(',', $cid);
            $cids_q = preg_replace("#[\d]+#", '?', $cids);

            $_db = JCore::getDB();
            $_db->delete('DELETE FROM `#__sef_duplicate` WHERE `id` IN (' . $cids_q . ')', $cid);
        }
        mosRedirect('index2.php?option=com_jlotossef&amp;task=listdup', $_lang['JLSEF_CFG_LINK_DEL']);
    }

    /**
     * Получение списка дупликатов
     */
    public static function pageDup()
    {

        $_db = JCore::getDB();
        $mainframe = mosMainFrame::getInstance();
        $limit = intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', JCore::getCfg('list_limit')));
        $limitstart = intval($mainframe->getUserStateFromRequest("viewjlotosseflimitstart", 'limitstart', 0));

        // получить общее количество записей
        $total = $_db->selectCell("SELECT COUNT(*) FROM `#__sef_duplicate`");

        // подключение навигации
        mosMainFrame::addLib('pagenavigation');
        $pageNav = new mosPageNav($total, $limitstart, $limit);

        $sql = "SELECT `id`, `url`, `sef` FROM `#__sef_duplicate` ORDER BY `url` LIMIT ?, ?";
        $rows = $_db->select($sql, $pageNav->limitstart, $pageNav->limit);

        JLotosSefHtml::pageDup($rows, $pageNav);
    }

    /**
     * Запись ссылки
     */
    public static function saveRef()
    {
        $_db = JCore::getDB();
        $row = array();
        $_error = array();
        $_lang = self::getLang();

        $row['url'] = str_replace(_JLPATH_SITE, '', JCore::getParam($_POST, 'url', '', 'sn'));
        if ($row['url'][0] != '/') {
            $row['url'] = '/' . $row['url'];
        }
        // нормализуем ссылку
        $row['url'] = str_replace('&amp;','&' ,$row['url']);
        $row['url'] = str_replace('&','&amp;' ,$row['url']);

        $row['sef'] = str_replace(_JLPATH_SITE, '', JCore::getParam($_POST, 'sef', '', 'sn'));
        if (empty($row['sef']) or $row['sef'] == '/') {
            $_error[] = $_lang['JLSEF_CFG_LINK_NO_SEF'];
        }else{
            if ($row['sef'][0] != '/') {
                $row['sef'] = '/' . $row['sef'];
            }

            $row['id'] = JCore::getParam($_POST, 'id', 0, 'i');

            $url = $_db->selectCell("SELECT `url` FROM `#__sef_link` WHERE `sef`=? AND `url` != ?", $row['sef'], $row['url']);
            if (!empty($url)) {
                $_error[] = $_lang['JLSEF_CFG_LINK_NO_URL'];
            }
        }

        if (sizeof($_error)) {
            JLotosSefHtml::editRef($row, self::getLang(), implode('<br />', $_error));
        } else {
            $_db->update("UPDATE `#__sef_link` SET `sef`=? WHERE  `id`=?;", $row['sef'], $row['id']);
            mosRedirect('index2.php?option=com_jlotossef&amp;task=listref', $_lang['JLSEF_CFG_LINK_SAVE']);
        }
    }

    /**
     * Редактирование ссылки
     *
     * @param $id - идентификатор ссылки
     */
    public static function editRef($id)
    {
        $_db = JCore::getDB();
        $row = $_db->selectRow("SELECT `id`, `url`, `sef` FROM `#__sef_link` WHERE `id` = ?;", $id);
        JLotosSefHtml::editRef($row, self::getLang());
    }

    /**
     * Удаление ссылки
     */
    public static function pageRemoveRef($cid)
    {

        $_lang = self::getLang();

        if (sizeof($cid)) {
            $cids = implode(',', $cid);
            $cids_q = preg_replace("#[\d]+#", '?', $cids);

            $_db = JCore::getDB();
            $_db->delete('DELETE FROM `#__sef_link` WHERE `id` IN (' . $cids_q . ')', $cid);
        }
        mosRedirect('index2.php?option=com_jlotossef&amp;task=listref', $_lang['JLSEF_CFG_LINK_DEL']);
    }

    /**
     * Получение списка ссылок
     */
    public static function pageRef()
    {

        $_db = JCore::getDB();
        $mainframe = mosMainFrame::getInstance();
        $limit = intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', JCore::getCfg('list_limit')));
        $limitstart = intval($mainframe->getUserStateFromRequest("viewjlotosseflimitstart", 'limitstart', 0));

        // получить общее количество записей
        $total = $_db->selectCell("SELECT COUNT(*) FROM `#__sef_link`");

        // подключение навигации
        mosMainFrame::addLib('pagenavigation');
        $pageNav = new mosPageNav($total, $limitstart, $limit);

        $sql = "SELECT `id`, `url`, `sef` FROM `#__sef_link` ORDER BY `url` LIMIT ?, ?";
        $rows = $_db->select($sql, $pageNav->limitstart, $pageNav->limit);

        JLotosSefHtml::pageRef($rows, $pageNav);
    }

    /**
     * Получаем языковые константы
     *
     * @return array
     */
    public static function getLang()
    {
        if (empty(self::$lang)) {
            self::$lang = JLLang::getLang('com.jlotossef');
        }
        return self::$lang;
    }

    /**
     * Получить значение настройки
     *
     * @param null|string $key - имя настройки. Если NULL, возвращает все настройки
     *                         pack_ref - Делать резервную копию таблицы ссылок перед очисткой
     *               0 - Нет
     *               1 - Да. Перезаписывать файл
     *               2 - Да. Создавать новый файл
     *                         pack_dup - Делать резервную копию таблицы дубликатов перед очисткой
     *               0 - Нет
     *               1 - Да. Перезаписывать файл
     *               2 - Да. Создавать новый файл
     *                         pack_path - путь до архивных файлов
     *                         pack_pref - префикс архивных фалов
     *
     * @return string|array - значение настройки
     */
    public static function getCfg($key = null)
    {
        if (empty(self::$config)) {
            $_db = JCore::getDB();
            $rows = $_db->select("SELECT `name`, `value` FROM `#__sef_config`;");

            // Нормализуем настройки
            foreach ($rows as $row) {
                self::$config[$row['name']] = $row['value'];
            }
        }

        if (empty($key)) {
            return self::$config;
        } else {
            return self::$config[$key];
        }
    }

    /**
     * Страница Настройки
     */
    public static function pageConfiguration()
    {
        $_config = JLotosSefClass::getCfg();
        $lang = self::getLang();
        $data = array();

        // Создавать резервную копию таблицы ссылок перед очисткой
        $_tmp_array = array();
        $_tmp_array[] = mosHTML::makeOption('0', $lang['JLSEF_CFG_PACK_REF_VAL0']);
        $_tmp_array[] = mosHTML::makeOption('1', $lang['JLSEF_CFG_PACK_REF_VAL1']);
        $_tmp_array[] = mosHTML::makeOption('2', $lang['JLSEF_CFG_PACK_REF_VAL2']);
        $data['pack_ref'] = mosHTML::selectList($_tmp_array, 'pack_ref', 'class="inputbox" size="1"', 'value', 'text', $_config['pack_ref']);

        // Создавать резервную копию таблицы дубликатов перед очисткой
        $_tmp_array = array();
        $_tmp_array[] = mosHTML::makeOption('0', $lang['JLSEF_CFG_PACK_DUP_VAL0']);
        $_tmp_array[] = mosHTML::makeOption('1', $lang['JLSEF_CFG_PACK_DUP_VAL1']);
        $_tmp_array[] = mosHTML::makeOption('2', $lang['JLSEF_CFG_PACK_DUP_VAL2']);
        $data['pack_dup'] = mosHTML::selectList($_tmp_array, 'pack_dup', 'class="inputbox" size="1"', 'value', 'text', $_config['pack_dup']);

        // Путь до архивных файлов
        $data['pack_path'] = (trim($_config['pack_path'], '/\\') == '') ? '/' : '/' . trim($_config['pack_path'], '/\\') . '/';
        $data['pack_path'] = '<input class="text_area" type="text" name="pack_path" size="40" value="' . $data['pack_path'] . '"/>';

        // Префикс архивных файлов
        $data['pack_pref'] = '<input class="text_area" type="text" name="pack_pref" size="10" value="' . trim($_config['pack_pref']) . '"/>';

        // Формат архивного файла
        $_tmp_array = array();
        $_tmp_array[] = mosHTML::makeOption('0', $lang['JLSEF_CFG_PACK_FORMAT_VAL0']);
        $_tmp_array[] = mosHTML::makeOption('1', $lang['JLSEF_CFG_PACK_FORMAT_VAL1']);
        $_tmp_array[] = mosHTML::makeOption('2', $lang['JLSEF_CFG_PACK_FORMAT_VAL2']);
        $_tmp_array[] = mosHTML::makeOption('3', $lang['JLSEF_CFG_PACK_FORMAT_VAL3']);
        $_tmp_array[] = mosHTML::makeOption('4', $lang['JLSEF_CFG_PACK_FORMAT_VAL4']);
        $data['pack_format'] = mosHTML::selectList($_tmp_array, 'pack_format', 'class="inputbox" size="1"', 'value', 'text', $_config['pack_format']);

        JLotosSefHtml::pageConfiguration($data);
    }

    /**
     * Экспорт таблицы ссылок
     *
     * @param int  $indx     : 0 - #__sef_link
     *                       : 1 - #__sef_duplicate
     * @param bool $redirect : Делать ли переадресацию
     */
    public static function exportLink($indx, $redirect = true)
    {
        $_lang = self::getLang();
        if (($indx == 0 and self::getCfg('pack_ref')) OR ($indx == 1 and self::getCfg('pack_dup'))) {
            $table = ($indx) ? JCore::getCfg('dbprefix') . 'sef_duplicate' : JCore::getCfg('dbprefix') . 'sef_link';

            $_path = _JLPATH_ROOT . self::getCfg('pack_path');
            $_db = JCore::getDB();

            // Проверяем, существует ли каталог
            if (!is_dir($_path)) {
                mosRedirect('index2.php?option=com_jlotossef', $_lang['JLSEF_CFG_DIR_NO']);
            }

            // Получаем ссылки
            $rows = $_db->select("SELECT `url`, `sef` FROM `" . $table . "`;");

            $result_tmp = array();

            // Листаем
            foreach ($rows as $row) {
                switch (self::getCfg('pack_format')) {
                    case '4': // JSON-формат
                        $result_tmp[] = '{"url": "' . $row['url'] . '","sef": "' . $row['sef'] . '"}';
                        break;
                    case '3': // XML-формат
                        $result_tmp[] = "\t<row>";
                        $result_tmp[] = "\t\t<url>" . $row['url'] . "</url>";
                        $result_tmp[] = "\t\t<sef>" . $row['sef'] . "</sef>";
                        $result_tmp[] = "\t</row>";
                        break;
                    case '2': // SQL REPLACEs
                        $result_tmp[] = "REPLACE INTO `" . $table . "` VALUES ('" . $row['url'] . "', '" . $row['sef'] . "');";
                        break;
                    case '0': // текстовый файл
                        $result_tmp[] = $row['url'] . "\t" . $row['sef'];
                        break;
                    default: // SQL INSERTs
                        $result_tmp[] = "INSERT INTO `" . $table . "` VALUES ('" . $row['url'] . "', '" . $row['sef'] . "');";
                }
            }
            // завершаем формат
            switch (self::getCfg('pack_format')) {
                case '4': // JSON-формат
                    $result_file = "[" . implode(",\n", $result_tmp) . "]";
                    $exp = '.json';
                    break;
                case '3': // XML-формат
                    $result_file = "<?xml version=\"1.0\" encoding=\"utf8\"?>\n<table name=\"" . $table . "\">\n"
                        . implode("\n", $result_tmp)
                        . "\n</table>";
                    $exp = '.xml';
                    break;
                case '2':
                case '1':
                    $result_file = implode("\n", $result_tmp);
                    $exp = '.sql';
                    break;
                default: // SQL, TEXT
                    $result_file = implode("\n", $result_tmp);
                    $exp = '.txt';
            }

            if ($indx) {
                if (self::getCfg('pack_dup') == 1) { // перезапись
                    $filename = $table . $exp;
                } else { // создать новый
                    $filename = self::getCfg('pack_pref') . $table . '_' . time() . $exp;
                }
            } else {
                if (self::getCfg('pack_ref') == 1) { // перезапись
                    $filename = $table . $exp;
                } else { // создать новый
                    $filename = self::getCfg('pack_pref') . $table . '_' . time() . $exp;
                }
            }
            $fp = fopen(_JLPATH_ROOT . self::getCfg('pack_path') . $filename, 'w');
            fwrite($fp, $result_file);
            fclose($fp);

            if ($redirect) {
                mosRedirect('index2.php?option=com_jlotossef', sprintf($_lang['JLSEF_CFG_FILE_SAVE'], $table, $filename));
            }
        } elseif($redirect) {
            mosRedirect('index2.php?option=com_jlotossef', $_lang['JLSEF_CFG_PACK_CFG']);
        }
    }

    /**
     * Очистка Таблицы ссылок
     *
     * @param int $indx : 0 - #__sef_link
     *                  : 1 - #__sef_duplicate
     */
    public static function clearLink($indx)
    {
        JLotosSefClass::exportLink($indx, false);
        $table = ($indx) ? JCore::getCfg('dbprefix') . 'sef_duplicate' : JCore::getCfg('dbprefix') . 'sef_link';
        $_lang = self::getLang();
        $_db = JCore::getDB();
        $result = $_db->clearTable($table);
        if ($result) {
            mosRedirect('index2.php?option=com_jlotossef', sprintf($_lang['JLSEF_CLR_OK'], $table));
        } else {
            mosRedirect('index2.php?option=com_jlotossef', sprintf($_lang['JLSEF_CLR_NO'], $table));
        }

    }

    /**
     * Запись настроек
     *
     * @param bool $indx : false - делать переадресацию на панель
     *                   : true - делать переадресацию в настройки
     */
    public static function saveConfiguration($indx = false)
    {
        $rows = array();
        $_config = self::getCfg();
        $_db = JCore::getDB();
        $_lang = self::getLang();

        $rows['pack_ref'] = JCore::getParam($_POST, 'pack_ref', 0, 'i');
        $rows['pack_dup'] = JCore::getParam($_POST, 'pack_dup', 0, 'i');
        $rows['pack_path'] = JCore::getParam($_POST, 'pack_path', '/administrator/backups/', 's');
        $rows['pack_pref'] = JCore::getParam($_POST, 'pack_pref', 'sef_', 's');
        $rows['pack_format'] = JCore::getParam($_POST, 'pack_format', 1, 'i');

        foreach ($rows as $key => $value) {
            if ($_config[$key] != $value) {
                $sql = "UPDATE `#__sef_config` SET `value` = ? WHERE `name` = ?;";
                $_db->update($sql, $value, $key);
            }
        }
        if ($indx) {
            mosRedirect('index2.php?option=com_jlotossef&task=configuration', $_lang['JLSEF_CFG_SAVE']);
        } else {
            mosRedirect('index2.php?option=com_jlotossef', $_lang['JLSEF_CFG_SAVE']);
        }
    }

    /**
     * вывод описания SEF-файла
     */
    public static function pageDescription()
    {
        JLotosSefHtml::pageDescription();
    }

    /**
     * Вывод панели управления
     */
    public static function pageDefault()
    {
        $_db = JCore::getDB();
        // подсчитываем количество sef-файлов
        $sef_files = sizeof(glob(_JLPATH_SEF . '/*sef.ini'));

        // подсчитываем количество ссылок
        $sef_link = $_db->selectCell("SELECT COUNT(`url`) FROM `#__sef_link`;");

        // подсчитываем количество дубликтов
        $sef_dubl = $_db->selectCell("SELECT COUNT(`id`) FROM `#__sef_duplicate`;");

        JLotosSefHtml::pageDefault($sef_files, $sef_link, $sef_dubl);
    }
}
