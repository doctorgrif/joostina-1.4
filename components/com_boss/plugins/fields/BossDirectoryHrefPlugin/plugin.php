<?php defined('_JLINDEX') or die;
/**
 * Joostina BOSS Plugin
 *
 * @package   BOSS Plugin
 * @version   1.0
 * @author    Gold Dragon <illusive@bk.ru>
 * @link      http://gd.joostina-cms.ru
 * @copyright 2013 JLotos.
 * @license   GNU GPL: http://www.gnu.org/licenses/gpl-3.0.html
 *            Joostina Lotos CMS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL. (help/copyright.php)
 * @Date      22.07.2013
 * @see       http://wiki.joostina-cms.ru/index.php/BossDirectoryHrefPlugin
 */

class BossDirectoryHrefPlugin
{

    /** @var string имя типа поля в выпадающем списке в настройках поля */
    public $name = 'Directory Href';

    /** @var string тип плагина для записи в таблицы */
    public $type = 'BossDirectoryHrefPlugin';

    /**
     * Отображение поля в категории
     *
     * @param int    $directory      : не используется
     * @param object $content        : данные контента
     * @param object $field          : данные по полю
     * @param string $field_values   : не используется
     * @param object $conf           : не используется
     *
     * @return string
     */
    public function getListDisplay($directory, $content, $field, $field_values, $conf)
    {
        return BossDirectoryHrefPlugin::getDetailsDisplay($directory, $content, $field, $field_values, $conf);
    }

    /**
     * Отображение поля в контенте
     *
     * @param int    $directory      : не используется
     * @param object $content        : данные контента
     * @param object $field          : данные по полю
     * @param string $field_values   : не используется
     * @param object $conf           : не используется
     *
     * @return string
     */
    public static function getDetailsDisplay($directory, $content, $field, $field_values, $conf)
    {
        $_field_name = $field->name;
        $value = $content->$_field_name;
        $return = "";
        if ($value != "") {

            if (!empty($field->text_before)) {
                $return .= '<span>' . $field->text_before . '</span>';
            }
            if (!empty($field->tags_open)) {
                $return .= html_entity_decode($field->tags_open);
            }
            preg_match("#href=\"(.*)\"#", $value, $tmp);
            $value = str_replace($tmp[1], JSef::getUrlToSef($tmp[1]), $value);
            $return .= $value;

            if (!empty($field->tags_close)) {
                $return .= html_entity_decode($field->tags_close);
            }
            if (!empty($field->text_after)) {
                $return .= '<span>' . $field->text_after . '</span>';
            }
        }
        return $return;
    }

    /**
     * Функция вставки фрагмента ява-скрипта в скрипт
     * сохранения формы при редактировании контента с фронта.
     *
     * @param object $field: данные по полю
     */
    public function addInWriteScript($field)
    {
    }

    /**
     * Отображение поля в админке в редактировании контента
     *
     * @param int    $directory      : идентификатор каталога
     * @param object $content        : данные контента
     * @param object $field          : данные по полю
     * @param string $field_values   : не используется
     * @param string $nameform       : не используется
     * @param string $mode           : не используется
     *
     * @return null|string
     */
    public function getFormDisplay($directory, $content, $field, $field_values, $nameform = 'adminForm', $mode = "write")
    {
        $_field_name = $field->name;

        $mainframe = mosMainFrame::getInstance();
        if ($mainframe->isAdmin() != 1) {
            return null;
        }
        ?>
        <script type="text/javascript">
            function loadFunc<?php echo $field->fieldid; ?>(func) {
                var url = 'http://' + location.hostname;
                url = url + '/administrator/ajax.index.php?option=com_boss&act=plugins&task=run_plugins_func&directory=<?php echo $directory;?>&class=BossDirectoryHrefPlugin&function=' + func;

                if (func == 'loadCategory') {
                    $('#<?php echo $_field_name;?>_content<?php echo $field->fieldid; ?>').html('');
                    $('#<?php echo $_field_name;?>_category<?php echo $field->fieldid; ?>').html('');
                }

                if (func == 'loadContent') {
                    $('#<?php echo $_field_name;?>_content<?php echo $field->fieldid; ?>').html('');
                }

                if ($("select").is("#<?php echo $_field_name;?>_directory<?php echo $field->fieldid; ?>")) {
                    var sel_dir = $('#<?php echo $_field_name;?>_directory<?php echo $field->fieldid; ?>').val()
                    url = url + '&sel_dir=' + sel_dir;
                    url = url + '&id_cat=<?php echo $_field_name;?>_category_sel<?php echo $field->fieldid; ?>';
                }

                if ($("select").is("#<?php echo $_field_name;?>_category_sel<?php echo $field->fieldid; ?>")) {
                    var sel_cat = $('#<?php echo $_field_name;?>_category_sel<?php echo $field->fieldid; ?>').val()
                    url = url + '&sel_cat=' + sel_cat;
                    url = url + '&id_cont=<?php echo $_field_name;?>_content_sel<?php echo $field->fieldid; ?>';
                }

                if ($("select").is("#<?php echo $_field_name;?>_content_sel<?php echo $field->fieldid; ?>")) {
                    var sel_cont = $('#<?php echo $_field_name;?>_content_sel<?php echo $field->fieldid; ?>').val()
                    var alt_name = $('#<?php echo $_field_name;?>_name<?php echo $field->fieldid; ?>').val()
                    url = url + '&sel_cont=' + sel_cont;
                    url = url + '&id_href=<?php echo $_field_name;?>_href<?php echo $field->fieldid; ?>';
                }

                $.ajax({
                    type: "POST",
                    url: url,
                    data: 'fieldid=<?php echo $field->fieldid; ?>',
                    dataType: 'HTML',
                    success: function (data) {
                        if ($("select").is("#<?php echo $_field_name;?>_content_sel<?php echo $field->fieldid; ?>")) {
                            $('#<?php echo $_field_name;?>_hreff<?php echo $field->fieldid; ?>').html(data);
                            $('#<?php echo $_field_name;?>_href<?php echo $field->fieldid; ?>').val(data);
                        }
                        else if ($("select").is("#<?php echo $_field_name;?>_category_sel<?php echo $field->fieldid; ?>")) {
                            $('#<?php echo $_field_name;?>_content<?php echo $field->fieldid; ?>').html(data);
                        }
                        else if ($("select").is("#<?php echo $_field_name;?>_directory<?php echo $field->fieldid; ?>")) {
                            $('#<?php echo $_field_name;?>_category<?php echo $field->fieldid; ?>').html(data);
                        }
                    }
                });
            }
            $(function () {
                $("#<?php echo $_field_name; ?>_href<?php echo $field->fieldid; ?>").val($("#<?php echo $_field_name; ?>_hreff<?php echo $field->fieldid; ?>").html());
            })
        </script>
        <?php

        $value = (isset ($content->$_field_name)) ? $content->$_field_name : '';

        $return = '<table><tr>';
        $return .= '<td>' . BOSS_EMAIL_DISPLAY_LINK . ': </td>';
        $return .= '<td><div id="' . $_field_name . '_hreff' . $field->fieldid . '">' . $value . '</div>';
        $return .= '<input id="' . $_field_name . '_href' . $field->fieldid . '" name="' . $_field_name . '_href' . $field->fieldid . '" type="hidden" value=""></td>';
        $return .= '</tr></table>';

        $return .= "<table><tr><td>";
        $return .= "<select class='boss' style='width: 200px;' name='" . $_field_name . "_directory" . $field->fieldid . "' id='" . $_field_name . "_directory" . $field->fieldid . "' onchange='loadFunc"
            . $field->fieldid . "(\"loadCategory\")' />\n";
        $return .= $this->loadDirectories();
        $return .= "</select>";
        $return .= "</td><td>";
        $return .= "<div id='" . $_field_name . "_category" . $field->fieldid . "'></div>";
        $return .= "</td><td>";
        $return .= "<div id='" . $_field_name . "_content" . $field->fieldid . "'></div>";
        $return .= "</td></tr></table>";

        return $return;
    }

    /**
     * действия при сохранении контента
     *
     * @param int    $directory   : не используется
     * @param int $contentid   : не используется
     * @param object $field       : данные поля
     * @param string $isUpdateMode: не используется
     *
     * @return null|string
     */
    public function onFormSave($directory, $contentid, $field, $isUpdateMode)
    {
        $return = JCore::getParam($_POST, $field->name . "_href" . $field->fieldid, '', 'sn');
        return $return;
    }

    /**
     * действия при удалении контента
     *
     * @param int    $directory: не используется
     * @param object $content  : не используется
     */
    public function onDelete($directory, $content)
    {
        return;
    }

    /**
     * отображение поля в админке в настройках поля
     *
     * @param $row        : не используется
     * @param $directory  : не используется
     * @param $fieldimages: не используется
     * @param $fieldvalues: не используется
     *
     * @return string
     */
    public function getEditFieldOptions($row, $directory, $fieldimages, $fieldvalues)
    {
        $return = "";
        return $return;
    }

    /**
     * действия при сохранении настроек поля
     * если плагин не создает собственных таблиц а пользется таблицами босса то возвращаем false
     * иначе true
     *
     * @param $directory: не используется
     * @param $field: не используется
     *
     * @return bool
     */
    public function saveFieldOptions($directory, $field)
    {
        return false;
    }

    /**
     * расположение иконки плагина начиная со слеша от корня сайта
     */
    public function getFieldIcon()
    {
        return "/components/com_boss/plugins/fields/" . __CLASS__ . "/images/folder.png";
    }

    /**
     * действия при установке плагина
     */
    public function install()
    {
        return;
    }

    /**
     * действия при удалении плагина
     */
    public function uninstall()
    {
        return;
    }

    /**
     * действия при поиске
     *
     * @param $directory: не используется
     * @param $fieldName: не используется
     */
    public function search($directory, $fieldName)
    {
        return;
    }

    /**************************************************************************************/
    /** AJAX функции
    /**************************************************************************************/

    /**
     * Получаем выпадающий список каталогов
     *
     * @return string
     */
    public function loadDirectories()
    {
        $_db = JCore::getDB();
        $directories = $_db->select("SELECT `id`, `name` FROM `#__boss_config`");
        $return = "<option value=''>" . BOSS_DIRECTORY_SEL . "</option>";
        foreach ($directories as $d) {
            $return .= "<option value='" . $d['id'] . "'>" . $d['name'] . "&nbsp;(" . $d['id'] . ")</option>";
        }
        return $return;
    }

    /**
     * Получаем выпадающий список категорий
     */
    public function loadCategory()
    {
        $directory = JCore::getParam($_REQUEST, 'sel_dir', 0, 'i');
        $fieldid = JCore::getParam($_REQUEST, 'fieldid', 0, 'sn');

        if ($directory == 0) {
            return;
        }
        $id_cat = JCore::getParam($_REQUEST, 'id_cat', 'directory_htef_category' . $fieldid, 'sn');

        require_once(_JLPATH_ROOT . '/administrator/components/com_boss/admin.boss.html.php');

        $_db = JCore::getDB();
        $rows = $_db->select("SELECT * FROM `#__boss_" . $directory . "_categories` ORDER BY `parent`, `ordering`");

        // установить иерархию меню
        $children = array();
        JCore::getLib('array');
        foreach ($rows as $row) {
            $pt = $row['parent'];
            $list = isset($children[$pt]) ? $children[$pt] : array();
            array_push($list, JLArray::ArrayToObject($row));
            $children[$pt] = $list;
        }

        //выводим селект выбора категорий
        echo '<select name="' . $id_cat . '" id="' . $id_cat . '" class="boss" style="width: 200px;" onchange=\'loadFunc' . $fieldid . '("loadContent")\'>';
        echo "<option value=''>" . BOSS_SELECT_CATEGORY . "</option>";
        HTML_boss::selectCategories(0, "Корень >> ", $children);
        echo '</select>';
    }

    /**
     * Получаем выпадающий список контента
     */
    public function loadContent()
    {
        $sel_dir = mosGetParam($_REQUEST, 'sel_dir', 0);
        $fieldid = JCore::getParam($_REQUEST, 'fieldid', 0, 'sn');
        if ($sel_dir == 0) {
            return;
        }
        $sel_cat = mosGetParam($_REQUEST, 'sel_cat', 0);
        if ($sel_cat == 0) {
            return;
        }
        $id_cont = mosGetParam($_REQUEST, 'id_cont', 'directory_htef_content' . $fieldid);

        $_db = JCore::getDB();
        $sql = "SELECT c.id, c.name FROM"
            . " `#__boss_" . $sel_dir . "_contents` AS c,"
            . " `#__boss_" . $sel_dir . "_content_category_href` AS cch"
            . " WHERE c.id = cch.content_id AND cch.category_id = $sel_cat"
            . " ORDER BY c.name";

        $rows = $_db->select($sql);

        //выводим селект выбора категорий
        echo '<select name="' . $id_cont . '" id="' . $id_cont . '" class="boss" style="width: 200px;" onchange=\'loadFunc' . $fieldid . '("loadHref")\'>';
        echo "<option value=''>" . BOSS_SELECT_CONTENT . "</option>";
        foreach ($rows as $row) {
            echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
        }
        echo '</select>';
    }

    /**
     * Получаем ссылку на материал
     */
    function loadHref()
    {
        $sel_dir = mosGetParam($_REQUEST, 'sel_dir', 0);
        if ($sel_dir == 0) {
            return;
        }
        $sel_cat = mosGetParam($_REQUEST, 'sel_cat', 0);
        if ($sel_cat == 0) {
            return;
        }
        $sel_cont = mosGetParam($_REQUEST, 'sel_cont', 0);
        if ($sel_cont == 0) {
            return;
        }

        $_db = JCore::getDB();

        $sql = "SELECT `name` FROM `#__boss_" . $sel_dir . "_contents` WHERE `id` = ?";
        $name = $_db->selectCell($sql, $sel_cont);

        echo '<a href="' . _JLPATH_SITE . '/index.php?option=com_boss&task=show_content&contentid=' . $sel_cont . '&catid=' . $sel_cat . '&directory=' . $sel_dir . '">' . $name . '</a>';
    }
}
