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
 * @Date      26.07.2013
 * @see       http://wiki.joostina-cms.ru/index.php/BossEmailPlugin
 */

class BossEmailPlugin
{
    /** @var string имя типа поля в выпадающем списке в настройках поля */
    public $name = 'Email Address';

    /** @var string тип плагина для записи в таблицы */
    public $type = 'BossEmailPlugin';

    /**
     * Отображение поля в категории
     *
     * @param int    $directory   : идентификатор каталога
     * @param object $content     : данные контента
     * @param object $field       : данные по полю
     * @param string $field_values: не используется
     * @param object $conf        : не используется
     *
     * @return string
     */
    public function getListDisplay($directory, $content, $field, $field_values, $conf)
    {
        return $this->getDetailsDisplay($directory, $content, $field, $field_values, $conf);
    }

    /**
     * Отображение поля в контенте
     *
     * @param int    $directory   : идентификатор каталога
     * @param object $content     : данные контента
     * @param object $field       : данные по полю
     * @param string $field_values: не используется
     * @param object $conf        : не используется
     *
     * @return string
     */
    public function getDetailsDisplay($directory, $content, $field, $field_values, $conf)
    {
        $fieldname = $field->name;
        $fieldid = $field->fieldid;
        $value = (isset ($content->$fieldname)) ? $content->$fieldname : '';

        $return = '';
        if (!empty($field->text_before)) {
            $return .= '<span>' . $field->text_before . '</span>';
        }
        if (!empty($field->tags_open)) {
            $return .= html_entity_decode($field->tags_open);
        }

        $_db = JCore::getDB();
        $sql = "SELECT `fieldvalue` FROM #__boss_" . $directory . "_field_values WHERE fieldid = ?";
        $config = $_db->selectCell($sql, $fieldid);
        if ($value != "") {
            switch ($config) {
                case 2:
                    $emailForm = JSef::getUrlToSef("index.php?option=com_boss&amp;task=show_message_form&amp;mode=0&amp;contentid=" . $content->id . "&amp;directory=" . $directory);
                    $return .= '<a href="' . $emailForm . '">' . BOSS_EMAIL_FORM . '</a>';
                    break;
                case 1:
                    $return .= Txt2Png($value, $directory);
                    break;
                default:
                    $return .= "<a href='mailto:" . $value . "'>" . cutLongWord($value, $field->maxlength) . "</a>";
                    break;
            }
        }

        if (!empty($field->tags_close)) {
            $return .= html_entity_decode($field->tags_close);
        }
        if (!empty($field->text_after)) {
            $return .= '<span>' . $field->text_after . '</span>';
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
     * @param int    $directory      : не используется
     * @param object $content        : данные контента
     * @param object $field          : данные по полю
     * @param string $field_values   : не используется
     * @param string $nameform       : не используется
     * @param string $mode           : права
     *
     * @return null|string
     */
    public function getFormDisplay($directory, $content, $field, $field_values, $nameform = 'adminForm', $mode = "write")
    {
        $fieldname = $field->name;
        $value = (isset ($content->$fieldname)) ? $content->$fieldname : '';
        $strtitle = htmlentities(jdGetLangDefinition($field->title), ENT_QUOTES, 'utf-8');

        $mosReq = (($mode == "write") && ($field->required == 1)) ? " mosReq='1' " : '';
        $read_only = (($mode == "write") && ($field->editable == 0)) ? " readonly=true " : '';
        $class = (($mode == "write") && ($field->required == 1)) ? "boss_required" : 'boss';

        $return = "<input class='$class' id='" . $field->name . "' type='text' test='emailaddress' mosLabel='" . $strtitle . "' name='" . $field->name . "' size='$field->size' maxlength='$field->maxlength' $read_only $mosReq value='$value' />\n";

        return $return;
    }

    /**
     * действия при сохранении контента
     *
     * @param int    $directory   : не используется
     * @param int    $contentid   : не используется
     * @param object $field       : данные поля
     * @param string $isUpdateMode: не используется
     *
     * @return null|string
     */
    public function onFormSave($directory, $contentid, $field, $isUpdateMode)
    {
        $return = JCore::getParam($_POST, $field->name, "", 'sn');
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
     * @param object $field      : данные плагина
     * @param int    $directory  : идентификатор категории
     * @param        $fieldimages: не используется
     * @param        $fieldvalues: не используется
     *
     * @return string
     */
    public function getEditFieldOptions($field, $directory, $fieldimages, $fieldvalues)
    {
        $fieldid = $field->fieldid;

        $_db = JCore::getDB();
        $sql = "SELECT `fieldvalue` FROM `#__boss_" . $directory . "_field_values` WHERE `fieldid` = ?";
        $fieldvalue = $_db->selectCell($sql, $fieldid);

        $return = "<div id='divEmailOptions'>";
        $return .= "<table class='adminform'>";
        $return .= "<tr>";
        $return .= "<td width='20%'>" . BOSS_EMAIL_DISPLAY . "</td>";
        $return .= "<td width='20%' align=left>";
        $return .= "<select id='email_display' name='email_display' mosReq='1' mosLabel='" . BOSS_EMAIL_DISPLAY . "'>";
        $selected = ($fieldvalue == 2) ? 'selected="selected"' : '';
        $return .= "<option value='2' " . $selected . ">" . BOSS_EMAIL_DISPLAY_FORM . "</option>";
        $selected = ($fieldvalue == 1) ? 'selected="selected"' : '';
        $return .= "<option value='1' " . $selected . ">" . BOSS_EMAIL_DISPLAY_IMAGE . "</option>";
        $selected = ($fieldvalue == 0) ? 'selected="selected"' : '';
        $return .= "<option value='0' " . $selected . ">" . BOSS_EMAIL_DISPLAY_LINK . "</option>";
        $return .= "</select>";
        $return .= "<td>" . BOSS_EMAIL_DISPLAY_LONG . "</td>";
        $return .= "</tr>";
        $return .= "</table>";
        $return .= "</div>";
        return $return;
    }

    /**
     * действия при сохранении настроек поля
     *
     * @param int    $directory: идентификатор каталога
     * @param object $field    : данные по полю
     *
     * @return bool
     */
    public function saveFieldOptions($directory, $field)
    {
        $fieldId = $field->fieldid;
        $email_display = JCore::getParam($_POST, "email_display", 0, 'i');

        $_db = JCore::getDB();
        $_db->delete("DELETE FROM `#__boss_" . $directory . "_field_values` WHERE `fieldid` = ?", $fieldId);

        $_db->insert("INSERT INTO `#__boss_" . $directory . "_field_values`
                    (`fieldid`, `fieldtitle`, `fieldvalue`, `ordering`, `sys`)
    		        VALUES (?, ?, ?, ?, ?);",
            $fieldId, 'email_display' , $email_display, 1,0);

        //если плагин не создает собственных таблиц а пользется таблицами босса то возвращаем false иначе true
        return false;
    }

    /**
     * расположение иконки плагина начиная со слеша от корня сайта
     */
    function getFieldIcon()
    {
        return "/components/com_boss/plugins/fields/" . __CLASS__ . "/images/email.png";
    }

    /**
     * действия при установке плагина
     *
     * @param int $directory: не используется
     */
    function install($directory)
    {
        return;
    }

    /**
     * действия при удалении плагина
     *
     * @param int $directory: не используется
     */
    function uninstall($directory)
    {
        return;
    }

    /**
     * действия при поиске
     *
     * @param int    $directory: не используется
     * @param string $fieldName: имя поля
     *
     * @return string
     */
    function search($directory, $fieldName)
    {
        $search = '';
        $value = JCore::getParam($_REQUEST, $fieldName, "", 'sn');
        if (!empty($value)) {
            $search .= " AND a.$fieldName LIKE '%$value%'";
        }
        return $search;
    }
}
