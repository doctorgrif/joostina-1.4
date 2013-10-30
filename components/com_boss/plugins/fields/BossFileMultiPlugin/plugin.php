<?php defined('_JLINDEX') or die;
/**
 * Joostina BOSS Plugin
 *
 * @package   BOSS Plugin
 * @version   1.3
 * @author    Gold Dragon <illusive@bk.ru>
 * @link      http://gd.joostina-cms.ru
 * @copyright 2013 JLotos.
 * @license   GNU GPL: http://www.gnu.org/licenses/gpl-3.0.html
 *            Joostina Lotos CMS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL. (help/copyright.php)
 * @Date      28.07.2013
 * @see       http://wiki.joostina-cms.ru/index.php/BossFileMultiPlugin
 */

//подгружаем языковой файл плагина
if (!defined('BOSS_PLG_NB_FILES')) {
    boss_helpers::loadBossPluginLang('fields', 'BossFileMultiPlugin');
}

class BossFileMultiPlugin
{

    /** @var string имя типа поля в выпадающем списке в настройках поля */
    public $name = 'File (Muliple)';

    /** @var string тип плагина для записи в таблицы */
    public $type = 'BossFileMultiPlugin';

    /**
     * скрипты и стили в голову, которые не кешируются
     *
     * @param $field       : не используется
     * @param $field_values: не используется
     *
     * @return array
     */
    public function addInHead($field, $field_values)
    {
        $params = array();
        $params['css'] = _JLPATH_SITE . '/components/com_boss/plugins/fields/BossFileMultiPlugin/css/plugin.css';
        return $params;
    }

    /**
     * Отображение поля в категории
     *
     * @param int    $directory   : идентификатор каталога
     * @param object $content     : данные контента
     * @param object $field       : данные по полю
     * @param string $field_values: значение поля
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
     * @param array  $field_values: массив объектов данных поля
     * @param object $conf        : не используется
     *
     * @return string
     */
    public function getDetailsDisplay($directory, $content, $field, $field_values, $conf)
    {
        $fieldname = $field->name;

        $field_conf = new stdClass();
        foreach ($field_values as $field_value) {
            $ft = $field_value->fieldtitle;
            $field_conf->$ft = $field_value->fieldvalue;
        }

        $value = (isset ($content->$fieldname)) ? $content->$fieldname : '';
        $dataArray = array();
        $return = '';
        if ($value != "") {
            $value = json_decode($value, 1);
            if (!empty($field->text_before)) {
                $return .= '<span>' . $field->text_before . '</span>';
            }
            if (!empty($field->tags_open)) {
                $return .= html_entity_decode($field->tags_open);
            }
            $return .= '<div class="boss_files">';

            if (is_array($value) && count($value) > 0) {
                foreach ($value as $row) {
                    $row['counter'] = (!empty($row['counter'])) ? $row['counter'] : 0;
                    $html = '<div class="boss_file">';
                    $html .= $this->displayFileLink($directory, $content, $field, $field_values, $row, 'joostfree', 'front', $field_conf);
                    $html .= '</div>';
                    $dataArray[] = $html;
                }
            }
            $return .= implode(html_entity_decode($field->tags_separator), $dataArray);
            $return .= '</div>';
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
     * отображение ссылки на скачивание
     *
     * @param int    $directory   : идентификатор каталога
     * @param object $content     : данные контента
     * @param object $field       : данные по полю
     * @param array  $field_values: массив объектов данных значений полей
     * @param array  $row         : данные значения поля
     * @param string $template    : имя шаблона админки
     * @param string $type        : тип фронт/админка
     * @param object $field_conf  : настройка поля
     *
     * @return string
     */
    private function displayFileLink($directory, $content, $field, $field_values, $row, $template, $type = "admin", $field_conf)
    {
        $mainframe = mosMainFrame::getInstance();
        if ($mainframe->isAdmin()) {
            $fv = $field_values[$field->fieldid];
        } else {
            $fv = $field_values;
        }

        $filename = $row['file'];
        $downloads = $row['counter'];

        $fieldname = $field->name;
        $value = (isset ($content->$fieldname)) ? $content->$fieldname : '';
        //настройки
        $counter = (!empty($field_conf->counter)) ? $field_conf->counter : 0;
        $show_img = (!empty($field_conf->show_img)) ? $field_conf->show_img : 0;
        $show_file = (!empty($field_conf->show_file)) ? $field_conf->show_file : 0;
        $show_button = (!empty($field_conf->show_button)) ? $field_conf->show_button : 0;
        $show_size = (!empty($field_conf->show_size)) ? $field_conf->show_size : 0;
        $show_desc = (!empty($field_conf->show_desc)) ? $field_conf->show_desc : 0;
        $show_date = (!empty($field_conf->show_date)) ? $field_conf->show_date : 0;
        // картинка как ссылка
        $show_img_link = (!empty($field_conf->show_img_link)) ? $field_conf->show_img_link : 0;
        // размер картинки
        $show_img_size = (!empty($field_conf->show_img_size)) ? $field_conf->show_img_size : 24;


        $date_created = '';
        if ($show_date) {
            $date_created = (isset ($content->date_created)) ? ' <span class="boss_file boss_file_date">' . BOSS_DATE . ':&nbsp;<span>' . mosFormatDate($content->date_created) . '</span></span>' : '';
        }

        $size = '';
        if ($show_size) {
            $size = round(@filesize(_JLPATH_ROOT . "/images/boss/" . $directory . "/files/" . $filename) / 1024, 2);
            $size = $size ? $size . ' Кб' : '';
            $size = "<span class='boss_file boss_file_size'>" . BOSS_FIELD_SIZE . " <span>" . $size . '</span>.</span>';
        }
        $image = '';
        if ($show_img) {
            // получаем расширение файла
            $matches = null;
            preg_match('#\.([\d\w]+)$#usi', $filename, $matches);
            $ext = mb_strtolower($matches[1]);

            // получаем картинку расширения
            switch ($ext) {
                case 'zip':
                case 'rar':
                case '7z':
                case 'gz':
                case 'tgz':
                case 'cab':
                case 'bz':
                case 'gzip':
                case 'bzip2':
                    $image = 'archive.png';
                    break;

                case 'xls':
                case 'xlt':
                case 'xlsx':
                case 'sql':
                case 'db':
                case 'mdb':
                case 'dbf':
                case 'acs':
                    $image = 'table.png';
                    break;

                case 'doc':
                case 'docx':
                case 'odt':
                case 'txt':
                case 'rtf':
                case 'pdf':
                    $image = 'docum.png';
                    break;

                case 'jpg':
                case 'png':
                case 'gif':
                case 'ico':
                case 'jpeg':
                    $image = 'image.png';
                    break;

                case 'php':
                case 'bin':
                case 'cfg':
                case 'sys':
                case 'dll':
                    $image = 'pc.png';
                    break;

                case 'ttf':
                case 'otf':
                case 'eot':
                case 'fon':
                case 'fnt':
                    $image = 'fonts.png';
                    break;

                case 'avi':
                case 'mp4':
                case 'mpg':
                case 'mpeg':
                case 'mov':
                case 'mkv':
                case '3gp':
                case 'divx':
                case 'flv':
                case 'ogv':
                case 'ogg':
                    $image = 'video.png';
                    break;

                case 'aif':
                case 'mp3':
                case 'mpa':
                case 'wma':
                    $image = 'music.png';
                    break;

                default:
                    $image = 'file.png';
                    break;
            }
            $image = '<img src=' . _JLPATH_SITE . '/components/com_boss/plugins/fields/BossFileMultiPlugin/images/' . $show_img_size . '/' . $image . " alt=" . $ext . ' align="middle" border="0" />';
        }

        $return = '';
        if ($filename) {
            if ($type == "front") { //отображение ссылки на фронте

                if ($counter) {
                    $url = _JLPATH_SITE . '/ajax.index.php?option=com_boss&act=plugins&task=run_plugins_func&directory=' . $directory . '&class=BossFileMultiPlugin&function=download&file=' . $filename . '&cid='
                        . $content->id . '&fname=' . $fieldname;
                    $counterPrint = '<span class="boss_file boss_file_counter">' . BOSS_PLG_COUNTER . ' <span>' . $downloads . '</span>.</span>';
                } else {
                    $url = _JLPATH_SITE . "/images/boss/" . $directory . "/files/" . $filename;
                    $counterPrint = '';
                }

                $desc = '';
                if ($show_desc) {
                    if (!$show_button && !$show_file) {
                        $desc = '<span class="boss_file boss_file_desc"><a href="' . $url . '" target="_blank">' . $row['signature'] . '</a></span>';
                    } else {
                        $desc = '<span class="boss_file boss_file_desc">' . $row['signature'] . '</span>';
                    }
                }

                $filenamePrint = '';
                if ($show_file) {
                    if ($show_button) {
                        $filenamePrint = '<span class="boss_file boss_file_name">' . $filename . '</span>';
                    } else {
                        $filenamePrint = '<span class="boss_file boss_file_name"><a href="' . $url . '" target="_blank">' . $filename . '</a></span>';
                    }
                }

                // картика как ссылка
                if ($show_img_link) {
                    $image = '<a class="boss_file boss_file_img" href="' . $url . '" target="_blank">' . $image . '</a>';
                }

                $button = '';
                if ($show_button) {
                    $button = "[&nbsp;<a href=\"" . $url . "\" target=\"_blank\">" . BOSS_DOWNLOAD_FILE . "</a>&nbsp;]";
                }

                $return .= $image . $desc . $filenamePrint . $button . '<br>' . $counterPrint . $size . $date_created;
                if ($return == '') {
                    $return = $filename;
                }
            } else { //отображение ссылки в админке
                $size = round(@filesize(_JLPATH_ROOT . "/images/boss/" . $directory . "/files/" . $filename) / 1024, 2);
                $return .= "<img src=\"" . _JLPATH_SITE . "/administrator/templates/" . $template . "/images/file_ico/" . $image . "\" alt=\"" . $ext . "\" align=\"middle\" border=\"0\" />&nbsp;"
                    . "<a title='" . BOSS_DOWNLOAD_FILE . "' href=\"" . _JLPATH_SITE . "/images/boss/" . $directory . "/files/" . $filename . "\" target=\"_blank\">"
                    . $filename . "</a> <span class='boss_file filesize'>&mdash;&nbsp;<span>" . $size . '</span></span>';
            }
        }
        return $return;
    }

    //отображение поля в админке в редактировании контента
    public function getFormDisplay($directory, $content, $field, $field_values, $nameform = 'adminForm', $mode = "write")
    {
        mosCommonHTML::loadJquery();
        $mainframe = mosMainFrame::getInstance();
        $mainframe->addJS(_JLPATH_SITE . '/administrator/components/com_boss/js/upload.js');
        $mainframe->addJS(_JLPATH_SITE . '/components/com_boss/plugins/fields/BossFileMultiPlugin/js/script.js');
        $mainframe->addCSS(_JLPATH_SITE . '/components/com_boss/plugins/fields/BossFileMultiPlugin/css/plugin.css');

        $fieldname = $field->name;

        $isAdmin = ($mainframe->isAdmin() == 1) ? 1 : 0;

        $fValuers = array();
        foreach ($field_values[$field->fieldid] as $field_value) {
            $fValuers[$field_value->fieldtitle] = $field_value->fieldvalue;
        }

        $value = (isset ($content->$fieldname)) ? $content->$fieldname : '';
        $value = (!empty($value)) ? json_decode($value, 1) : '';

        $nb_files = (!empty($fValuers['nb_files'])) ? $fValuers['nb_files'] : 0;
        $counter = (!empty($fValuers['counter'])) ? $fValuers['counter'] : 0;
        $enable_files = (!empty($fValuers['enable_files'])) ? implode("', '", explode(',', $fValuers['enable_files'])) : 'all';
        $return = '';
        $return
            .= "
                <script type=\"text/javascript\">
		            var boss_nb_files = " . (int)$nb_files . ";
		            var boss_enable_files = new Array('" . $enable_files . "');
		            var boss_isadmin = " . $isAdmin . ";
                </script>

                <div id='boss_plugin_file'>
                    <input id='upload' type='button' value='" . BOSS_PLG_FM_UPLOAD . "' style='float: left;'/>
			        <div id='status'></div>
			        <br style='clear: both;' />
			        <div id='files'>
                    ";

        if (!empty($value)) {
            foreach ($value as $i => $row) {
                $return
                    .= "
                        <div id='file_" . $i . "'>
                        <label>" . BOSS_PLG_DESC . " </label>
                        <input type='text' size='40'
                            name='boss_file[" . $i . "][signature]' class='inputbox boss_file' value='" . $row['signature'] . "' />";

                if ($counter) {
                    $row['counter'] = (!empty($row['counter'])) ? $row['counter'] : 0;
                    $return
                        .= "
                        <label>" . BOSS_PLG_COUNTER . " </label>
                        <input type='text' size='3' readonly='true'
                            name='boss_file[" . $i . "][counter]' class='inputbox boss_file' value='" . $row['counter'] . "' />";
                }
                $return
                    .= "
                        <input type='hidden' name='boss_file[" . $i . "][file]' value='" . $row['file'] . "' />
                            &nbsp;&nbsp;&nbsp;"
                    . self::displayFileLink($directory, $content, $field, $field_values, $row, JTEMPLATE, 0, 'admin')
                    . "&nbsp;&nbsp;<input type='button' value='X' class='button' onclick='bossDeleteFile(\"" . $row['file'] . "\", \"file_" . $i . "\")' />
                    </div>";
            }

        }
        $return .= "</div>";
        return $return;
    }

    //функция вставки фрагмента ява-скрипта в скрипт
    //сохранения формы при редактировании контента с фронта.
    public function addInWriteScript($field)
    {

    }

    //действия при сохранении контента
    public function onFormSave($directory, $contentid, $field, $isUpdateMode)
    {
        $boss_file = mosGetParam($_REQUEST, 'boss_file', '');
        $boss_file = boss_helpers::json_encode_cyr($boss_file);
        return $boss_file;
    }

    //функция транслитерации и замены пробелов в названии файла
    private function tranform($str)
    {

        $str = russian_transliterate($str);

        $maxchars = 70; //макс. кол-во символов

        if (Jstring::strlen($str) > $maxchars) { //если длина названия превышает макс. кол-во символов

            //вычленяем из название расширение файла
            $ext = explode('.', $str);
            $ext = $ext[(count($ext) - 1)];

            $length = strripos(Jstring::substr($str, 0, $maxchars), '_'); //ищем позицию последнего подчеркивания в названии
            $length = $length ? $length : $maxchars; //если нет подчеркиваний обрезаем по макс. кол-ву символов ($maxchars)
            $str = Jstring::substr($str, 0, $length) . '.' . $ext; //обрезаем по позиции найденного подчеркивания или по макс. кол-ву символов
        }
        return $str;
    }

    /**
     * Удаление файлов при удалении контента
     *
     * @param int    $directory: идентификатор каталога
     * @param object $content  : данные контента
     *
     * @modification 29.07.2013
     */
    public function onDelete($directory, $content)
    {
        $_db = JCore::getDB();
        $contents = $_db->selectRow("SELECT * FROM `#__boss_" . $directory . "_contents` WHERE `id` = ?", $content->id);
        $file_fields = $_db->select("SELECT `name` FROM `#__boss_" . $directory . "_fields` WHERE `type` = ?", $this->type);

        if (is_array($file_fields) && count($file_fields) > 0) {
            foreach ($file_fields as $file_field) {
                $fileFieldName = $file_field['name'];
                $files = json_decode($contents[$fileFieldName]);
                if (is_array($files) && count($files) > 0) {
                    foreach ($files as $file) {
                        @unlink(_JLPATH_ROOT . "/images/boss/" . $directory . "/files/" . $file->file);
                    }
                }
            }
        }
    }

    /**
     * отображение поля в админке в настройках поля
     *
     * @param       $row        : не используется
     * @param       $directory  : не используется
     * @param       $fieldimages: не используется
     * @param mixed $fieldvalues: значение поля
     *
     * @return string
     */
    public function getEditFieldOptions($row, $directory, $fieldimages, $fieldvalues)
    {
        $_nb_files = (isset($fieldvalues['nb_files']->fieldvalue)) ? $fieldvalues['nb_files']->fieldvalue : 5;
        $_enable_files = (isset($fieldvalues['enable_files']->fieldvalue)) ? $fieldvalues['enable_files']->fieldvalue : 'zip,7z,rar,rtf,pdf';
        $_counter = (isset($fieldvalues['counter']->fieldvalue)) ? $fieldvalues['counter']->fieldvalue : 1;
        $_show_img = (isset($fieldvalues['show_img']->fieldvalue)) ? $fieldvalues['show_img']->fieldvalue : 1;
        $_show_img_link = (isset($fieldvalues['show_img_link']->fieldvalue)) ? $fieldvalues['show_img_link']->fieldvalue : 1;

        $_show_img_size = (isset($fieldvalues['show_img_size']->fieldvalue)) ? $fieldvalues['show_img_size']->fieldvalue : 24;
        $_show_img_size_op = array();
        $_show_img_size_op[] = mosHTML::makeOption(16, '16x16');
        $_show_img_size_op[] = mosHTML::makeOption(24, '24x24');
        $_show_img_size_op[] = mosHTML::makeOption(32, '32x32');
        $_show_img_size_op[] = mosHTML::makeOption(48, '48x48');
        $_show_img_size_op[] = mosHTML::makeOption(64, '64x64');
        $_show_img_size = mosHTML::selectList($_show_img_size_op, 'show_img_size', 'class="inputbox"', 'value', 'text', $_show_img_size);

        $_show_desc = (isset($fieldvalues['show_desc']->fieldvalue)) ? $fieldvalues['show_desc']->fieldvalue : 1;
        $_show_file = (isset($fieldvalues['show_file']->fieldvalue)) ? $fieldvalues['show_file']->fieldvalue : 0;
        $_show_button = (isset($fieldvalues['show_button']->fieldvalue)) ? $fieldvalues['show_button']->fieldvalue : 1;
        $_show_size = (isset($fieldvalues['show_size']->fieldvalue)) ? $fieldvalues['show_size']->fieldvalue : 1;
        $_show_date = (isset($fieldvalues['show_date']->fieldvalue)) ? $fieldvalues['show_date']->fieldvalue : 1;

        $return
            = '
            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
                <tr>
                    <td>' . BOSS_PLG_NB_FILES . '</td>
                    <td><input type="text" name="nb_files" id="nb_files" value="' . $_nb_files . '"/></td>
                    <td>' . boss_helpers::bossToolTip(BOSS_PLG_NB_FILES_LONG) . '</td>
                </tr>
                <tr>
                    <td>' . BOSS_PLG_ENABLE_EXT . '</td>
                    <td><input type="text" name="enable_files" id="enable_files" value="' . $_enable_files . '"/></td>
                    <td>' . boss_helpers::bossToolTip(BOSS_PLG_ENABLE_EXT_LONG) . '</td>
                </tr>
                <tr>
                    <td>' . BOSS_PLG_ENABLE_COUNTER . '</td>
                    <td>' . mosHTML::yesnoRadioList('counter', 'class="inputbox"', $_counter) . '</td>
                    <td>' . boss_helpers::bossToolTip(BOSS_PLG_ENABLE_COUNTER_LONG) . '</td>
                </tr>

                <tr>
                    <td>' . BOSS_PLG_SHOW_IMG . '</td>
                    <td>' . mosHTML::yesnoRadioList('show_img', 'class="inputbox"', $_show_img) . '</td>
                    <td>' . boss_helpers::bossToolTip(BOSS_PLG_SHOW_IMG_LONG) . '</td>
                </tr>

                <tr>
                    <td>' . BOSS_PLG_SHOW_IMG_LINK . '</td>
                    <td>' . mosHTML::yesnoRadioList('show_img_link', 'class="inputbox"', $_show_img_link) . '</td>
                    <td>' . boss_helpers::bossToolTip(BOSS_PLG_SHOW_IMG_LINK_LONG) . '</td>
                </tr>

                <tr>
                    <td>' . BOSS_PLG_SHOW_IMG_SIZE . '</td>
                    <td>' . $_show_img_size . '</td>
                    <td>' . boss_helpers::bossToolTip(BOSS_PLG_SHOW_IMG_SIZE_LONG) . '</td>
                </tr>

                <tr>
                    <td>' . BOSS_PLG_SHOW_DESC . '</td>
                    <td>' . mosHTML::yesnoRadioList('show_desc', 'class="inputbox"', $_show_desc) . '</td>
                    <td>' . boss_helpers::bossToolTip(BOSS_PLG_SHOW_DESC_LONG) . '</td>
                </tr>
                <tr>
                    <td>' . BOSS_PLG_SHOW_FILE . '</td>
                    <td>' . mosHTML::yesnoRadioList('show_file', 'class="inputbox"', $_show_file) . '</td>
                    <td>' . boss_helpers::bossToolTip(BOSS_PLG_SHOW_FILE_LONG) . '</td>
                </tr>
                <tr>
                    <td>' . BOSS_PLG_SHOW_BUTTON . '</td>
                    <td>' . mosHTML::yesnoRadioList('show_button', 'class="inputbox"', $_show_button) . '</td>
                    <td>' . boss_helpers::bossToolTip(BOSS_PLG_SHOW_BUTTON_LONG) . '</td>
                </tr>
                <tr>
                    <td>' . BOSS_PLG_SHOW_SIZE . '</td>
                    <td>' . mosHTML::yesnoRadioList('show_size', 'class="inputbox"', $_show_size) . '</td>
                    <td>' . boss_helpers::bossToolTip(BOSS_PLG_SHOW_SIZE_LONG) . '</td>
                </tr>
                <tr>
                    <td>' . BOSS_PLG_SHOW_DATE . '</td>
                    <td>' . mosHTML::yesnoRadioList('show_date', 'class="inputbox"', $_show_date) . '</td>
                    <td>' . boss_helpers::bossToolTip(BOSS_PLG_SHOW_DATE_LONG) . '</td>
                </tr>
            </table>';
        $return .= BOSS_PLG_NB_FILES_DESC . '
                <a href="#" id="filesize" onClick="setFileSizeFocus();">' . BOSS_PLG_NB_FILES_DESC_1 . '</a>
                ' . BOSS_PLG_NB_FILES_DESC_2;
        $return
            .= "
			<script language=\"javascript\" type=\"text/javascript\">
				function setFileSizeFocus () {					
					jQuery('input[name=size]').focus().css('borderColor','red').css('color','red');
				}
			</script>
			";
        return $return;
    }

    //действия при сохранении настроек поля
    public function saveFieldOptions($directory, $field)
    {
        $fieldId = $field->fieldid;
        $_db = JCore::getDB();
        $nb_files = mosGetParam($_POST, "nb_files", 0);
        $enable_files = str_replace(' ', '', mosGetParam($_POST, "enable_files", ''));
        $counter = mosGetParam($_POST, "counter", 0);

        $show_file = mosGetParam($_POST, "show_file", 0);
        $show_button = mosGetParam($_POST, "show_button", 0);
        $show_size = mosGetParam($_POST, "show_size", 0);
        $show_date = mosGetParam($_POST, "show_date", 0);
        $show_desc = mosGetParam($_POST, "show_desc", 0);
        $show_img = mosGetParam($_POST, "show_img", 0);
        $show_img_link = mosGetParam($_POST, "show_img_link", 1);
        $show_img_size = mosGetParam($_POST, "show_img_size", 24);

        $_db->delete("DELETE FROM `#__boss_" . $directory . "_field_values` WHERE `fieldid` = ?", $fieldId);

        $sql = "INSERT INTO `#__boss_" . $directory . "_field_values` (`fieldid`, `fieldtitle`, `fieldvalue`, `ordering`, `sys`)
    		    VALUES (?, ?, ?, ?, ?), (?, ?, ?, ?, ?), (?, ?, ?, ?, ?), (?, ?, ?, ?, ?), (?, ?, ?, ?, ?),
    		        (?, ?, ?, ?, ?), (?, ?, ?, ?, ?), (?, ?, ?, ?, ?), (?, ?, ?, ?, ?), (?, ?, ?, ?, ?), (?, ?, ?, ?, ?)";
        $_db->insert(
            $sql, $fieldId, 'nb_files', $nb_files, 1, 0,
            $fieldId, 'enable_files', $enable_files, 2, 0,
            $fieldId, 'counter', $counter, 3, 0,
            $fieldId, 'show_file', $show_file, 4, 0,
            $fieldId, 'show_button', $show_button, 5, 0,
            $fieldId, 'show_size', $show_size, 6, 0,
            $fieldId, 'show_date', $show_date, 7, 0,
            $fieldId, 'show_desc', $show_desc, 8, 0,
            $fieldId, 'show_img', $show_img, 9, 0,
            $fieldId, 'show_img_link', $show_img_link, 10, 1,
            $fieldId, 'show_img_size', $show_img_size, 11, 24
        );

        //если плагин не создает собственных таблиц а пользется таблицами босса то возвращаем false
        //иначе true
        return false;
    }

    //расположение иконки плагина начиная со слеша от корня сайта
    public function getFieldIcon()
    {
        return "/components/com_boss/plugins/fields/" . __CLASS__ . "/images/image_add.png";
    }

    //действия при установке плагина
    public function install($directory)
    {
        return;
    }

    //действия при удалении плагина
    public function uninstall($directory)
    {
        return;
    }

    //действия при поиске
    public function search($directory, $fieldName)
    {
        $search = '';
        $value = mosGetParam($_REQUEST, $fieldName, "");
        if ($value != "") {
            $search .= " AND a.$fieldName LIKE '%$value%'";
        }
        return $search;
    }

    /**
     * Обновление счётчика скачивания и выдача файла
     */
    public function download()
    {

        $directory = mosGetParam($_REQUEST, 'directory', 0);
        $file = mosGetParam($_REQUEST, 'file', '');
        $fname = mosGetParam($_REQUEST, 'fname', '');
        $cid = mosGetParam($_REQUEST, 'cid', 0);

        $_db = JCore::getDB();
        $field = $_db->selectCell("SELECT `" . $fname . "` FROM `#__boss_" . $directory . "_contents` WHERE `id` = ?", $cid);

        if (!empty($field)) {
            $field = json_decode($field, 1);

            $newVal = array();
            foreach ($field as $f) {
                if ($f['file'] == $file) {
                    $f['counter'] = (!empty($f['counter'])) ? $f['counter'] + 1 : 1;
                }
                $newVal[] = $f;
            }
            $newVal = boss_helpers::json_encode_cyr($newVal);

            $_db->update("UPDATE `#__boss_" . $directory . "_contents` SET `" . $fname . "` = ? WHERE `id` = ?", $newVal, $cid);
        }
        mosRedirect(_JLPATH_SITE . "/images/boss/" . $directory . "/files/" . $file);
    }
}