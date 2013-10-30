<?php defined('_JLINDEX') or die();

/**
 * @package     Menu Easy
 * @copyright   Авторские права (C) 2000-2013 Gold Dragon.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @dascription Menu Easy -  модуль простого одноуровневого меню для Joostina 1.4.3+
 * @see         http://wiki.joostina-cms.ru/index.php/MenuEasy
 */

class mod_menu_easy_Helper
{
    private $_moduleclass_sfx;
    private $_menutype;
    private $_link_active;
    private $_link_frame;
    private $_link_type;
    private $_image_template;
    private $_image_prefix;
    private $_image_roller;
    private $_image_active;
    private $_link_null;
    private $_hide_first;

    private $_db;
    private $_links;
    private $_module;

    function getParams($params, $module)
    {

        $this->_db = JCore::getDB();
        $this->_module = $module;
        $this->_moduleclass_sfx = trim($params->get('moduleclass_sfx', ''));
        $this->_menutype = trim($params->get('menutype', ''));
        $this->_link_active = intval($params->get('link_active', 1));
        $this->_link_frame = intval($params->get('link_frame', 0));
        $this->_link_type = intval($params->get('link_type', 1));
        $this->_image_template = intval($params->get('image_template', 0));
        $this->_image_prefix = trim($params->get('image_prefix', ''));
        $this->_image_roller = intval($params->get('image_roller', 0));
        $this->_image_active = intval($params->get('image_active', 1));
        $this->_link_null = intval($params->get('link_null', 1));
        $this->_hide_first = intval($params->get('hide_first', 0));

        $access = '';
        $sql_array = array();

        // Получаем ссылки из базы
        if (!JCore::getCfg('disable_access_control')) {
            $access = " AND access <= ?";
        }

        $sql
            = "SELECT b.*
                FROM #__menu AS b
                WHERE b.menutype = ?
                    AND b.published = ?"
            . $access
            . " ORDER BY b.parent, b.ordering";
        $sql_array[] = $this->_menutype;
        $sql_array[] = 1;
        if (!empty($access)) {
            $_my = JCore::getUser();
            $sql_array[] = intval($_my->gid);
        }
        $this->_links = $this->_db->select($sql, $sql_array);
    }

    public function getHTML($params, $moduleid)
    {
        $this->getParams($params, $moduleid);

        $link_array = array();

        foreach ($this->_links as $link) {

            // Скрываем 1-ю ссылку, если нужно
            if($this->_hide_first){
                $this->_hide_first = 0;
                continue;
            }

            $actived = '';

            // получаем ссылку
            $link_norm = JSef::getUrlToSef($link['link']);

            // Формируем стиль
            if ($this->_link_active == 1 AND JSef::$url == $link_norm) {
                $actived = ' id="mod_menu_easy_a' . $this->_module->id . '"';
            } elseif ($this->_link_active == 2 AND JSef::$url == $link_norm) {
                $actived = ' class="mod_menu_easy_a' . $this->_module->id . '"';
            }

            // Формируем ссылку
            if ($this->_link_type) {

                // Если ссылка активная, то только текст
                if (JSef::$url == $link_norm and $this->_link_null) {
                    $result = '<span ' . $actived . '>' . $link['name'] . '</span>';
                } else {
                    $result = '<a href="' . $link_norm . '" ' . $actived . '>' . $link['name'] . '</a>';
                }

            } else {

                // Папка с картинками
                if ($this->_image_template) {
                    $_mainframe = mosMainFrame::getInstance();
                    $path_root = _JLPATH_TEMPLATES . '/' . $_mainframe->getTemplate() . '/images/modules/mod_menu_easy';
                    $path_site = _JLPATH_SITE . '/templates/' . $_mainframe->getTemplate() . '/images/modules/mod_menu_easy';
                } else {
                    $path_root = _JLPATH_ROOT . '/images/menuimages';
                    $path_site = _JLPATH_SITE . '/images/menuimages';
                }

                // Поиск изображения
                if (is_file($path_root . '/' . $this->_image_prefix . $link['id'] . '.png')) {

                    $name_link = $this->imgeLink('png', $path_root, $path_site, $link, $link_norm);

                } elseif (is_file($path_root . '/' . $this->_image_prefix . $link['id'] . '.gif')) {

                    $name_link = $this->imgeLink('gif', $path_root, $path_site, $link, $link_norm);

                } elseif (is_file($path_root . '/' . $this->_image_prefix . $link['id'] . '.jpg')) {

                    $name_link = $this->imgeLink('jpg', $path_root, $path_site, $link, $link_norm);

                } else {
                    $name_link = $link['name'];
                }

                // Если ссылка активная то только картинка
                if (JSef::$url == $link_norm and $this->_link_null) {
                    $result = $name_link;
                } else {
                    $result = $result = '<a href="' . $link_norm . '" ' . $actived . '>' . $name_link . '</a>';
                }

            }

            // Формируем обрамление
            if ($this->_link_frame == 1) {
                $result = '<li>' . $result . '</li>';
            } elseif ($this->_link_frame == 2) {
                $result = '<div>' . $result . '</div>';
            }
            $link_array[] = $result;
        }

        if (sizeof($link_array)) {

            // Приводим в соответсвие со спецификацией HTML
            if ($this->_link_frame == 1) {
                $result = '<div><ul>' . implode($link_array) . '</ul></div>';
            } else {
                $result = '<div>' . implode($link_array) . '</div>';
            }

            echo $result;

            // Если вкючен роллер
            if ($this->_image_roller) {
                // Выводим скрипт один раз
                if (!defined('_MOD_MENU_EASY_JS')) {
                    echo '
                    <script>
                    function modmenueasy_image(id, name){
                        $("#"+id).attr("src", name);
                    }
                    </script>';
                    define('_MOD_MENU_EASY_JS', 1);
                }
            }
        }
    }

    /**
     * Получаем карткинку ссылки
     *
     * @param string $ext       - расширение файла
     * @param        $path_root - абсолютный путь до картинки
     * @param        $path_site - HTML - путь до картинки
     * @param        $link      - массив с данными по ссылкам из БД
     * @param        $link_norm - нормализованная ссылка
     *
     * @return string - HTML картика
     */
    private function imgeLink($ext = 'png', $path_root, $path_site, $link, $link_norm)
    {

        // Идентификатор картинки
        $id_image = 'mod_mei_' . $this->_module->id . '_' . $link['id'];

        $image_off = $path_site . '/' . $this->_image_prefix . $link['id'] . '.' . $ext;

        $name_link = '<img id="' . $id_image . '"';

        // Если включен роллер
        if ($this->_image_roller) {
            if (is_file($path_root . '/' . $this->_image_prefix . $link['id'] . '_on.' . $ext)) {
                $image_on = $path_site . '/' . $this->_image_prefix . $link['id'] . '_on.' . $ext;
            } else {
                $image_on = $image_off;
            }

            // проверяем оставлять ли активную картинку при активном пункте
            if (JSef::$url == $link_norm) {
                $image_off = $image_on;
            }

            $name_link .= 'onmouseout="modmenueasy_image(\'' . $id_image . '\', \'' . $image_off . '\')"';
            $name_link .= 'onmouseover="modmenueasy_image(\'' . $id_image . '\', \'' . $image_on . '\')"';
        }
        $name_link .= 'src="' . $image_off . '" alt="' . $link['name'] . '" />';

        return $name_link;
    }
}
