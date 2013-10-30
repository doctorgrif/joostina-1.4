<?php
/**
 * Плагин рейтинга
 * @package   Joostina BOSS
 * @copyright Авторские права (C) 2000-2012 Gold Dragon.
 * @license   Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 *            Joostina BOSS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 *            Joostina BOSS основан на разработках Jdirectory от Thomas Papin
 */
defined('_JLINDEX') or die();

class GDRating
{
    private $units;
    private $rating_unitwidth;
    private $result_only;

    private $content;
    private $directory;
    private $conf;

    public function __construct($directory)
    {
        // количество звёздочек
        $this->units = 10;
        // ширина звёздочки
        $this->rating_unitwidth = 16;
        // включаем показ рейтинга полностью
        $this->result_only = true;
    }

    /**
     * Вызов формы рейтинга
     * @param $content   - запись контента
     * @param $directory - каталог
     * @param $conf      - данные конфигурации
     *
     * @internal param bool $gust - разрешение голосовать гостям (принудительно)
     * @return bool|string
     */
    public function displayVoteForm($content, $directory, $conf)
    {
        // проверяем разрешёл ли рейтинг
        if ($conf->allow_ratings) {
            $mainframe = mosMainFrame::getInstance();
            $this->content = $content;
            $this->directory = $directory;
            $this->conf = $conf;

            // подключение языкового файла
            $path = _JLPATH_ROOT . '/components/com_boss/plugins/ratings/GDRating/lang';
            $lang = $mainframe->getCfg('lang');
            if (file_exists($path . DS . $lang . '.php')) {
                require_once($path . DS . $lang . '.php');
            } else {
                require_once($path . DS . 'russian.php');
            }

            // подключаем стили один раз
            if (!defined('_GDRATING_CSS')) {
                define('_GDRATING_CSS', 1);
                $mainframe->addCSS(_JLPATH_SITE . '/components/com_boss/plugins/ratings/GDRating/style.css');
            }
            // выводим рейтинг
            $result = $this->ratingBar();
            echo $result;

            return true;
        } else {
            return false;
        }
    }

    /**
     * Вызов результата рейтинга
     * @param $content   - запись контента
     * @param $directory - каталог
     * @param $conf      - данные конфигурации
     *
     * @internal param bool $gust - разрешение голосовать гостям (принудительно)
     */
    public function displayVoteResult($content, $directory, $conf)
    {
        $this->result_only = false;
        $this->displayVoteForm($content, $directory, $conf);
    }

    /**
     * Вывод рейтинга
     * @return string
     */
    private function ratingBar()
    {
        $_db = JCore::getDB();
        $mainframe = mosMainFrame::getInstance();

        $sql
            = "SELECT COUNT(*) AS count, SUM(value) AS sum
				FROM #__boss_" . $this->directory . "_rating
				WHERE `contentid` = ?";
        $row = $_db->selectRow($sql, $this->content->id);

        $mainframe->addLib('text');

        $rating_width = (!empty($row['count'])) ? number_format($row['sum'] / $row['count'], 2) * $this->rating_unitwidth : 0;

        $rating1 = (!empty($row['count'])) ? number_format($row['sum'] / $row['count'], 1) : "0.0";
        $rating2 = (!empty($row['count'])) ? number_format($row['sum'] / $row['count'], 2) : "0.00";

        $tense = Text::declension($row['count'], array(_GDRATING_MES01, _GDRATING_MES02, _GDRATING_MES03));
        if (!$this->result_only) {
            $result = '<span>' . _GDRATING_MES05 . ': <strong> ' . $rating1 . '</strong>/' . $this->units . ' (' . $row['count'] . ' ' . $tense . ')' . '</span>';
            return $result;
        } else {
            $my = JCore::getUser();
            if ($my->id == 0 AND $this->conf->allow_unregisered_comment == 0) {
                $result = array();
                $result[] .= '<div class="ratingblock" id="ratingblock_' . $this->content->id . '">';
                $result[] .= '<div id="unit_long' . $this->content->id . '">';
                $result[] .= '<ul id="unit_ul' . $this->content->id . '" class="unit-rating" style="width:' . $this->rating_unitwidth * $this->units . 'px;">';
                $result[] .= '<li class="current-rating" style="width:' . $rating_width . 'px;">' . _GDRATING_MES04 . ' ' . $rating2 . '/' . $this->units . '</li>';
                $result[] .= '</ul>';
                $result[] .= '<p class="static">';
                $result[] .= '<span>' . _GDRATING_MES05 . ': <strong> ' . $rating1 . '</strong>/' . $this->units . ' (' . $row['count'] . ' ' . $tense . ')' . '</span>';
                $result[] .= '<br /><span style="font-size:90%">' . _GDRATING_MES07 . '.</span></p>';
                $result[] .= '</div>';
                $result[] .= '</div>';
                return join("", $result);
            } else {
                // получаем IP
                $ip = getIp();

                // проверяем голосовавших
                if ($my->id) {
                    $sql = "SELECT COUNT(*) FROM #__boss_" . $this->directory . "_rating WHERE contentid = ? AND userid = ?";
                    $voted = $_db->selectCell($sql, $this->content->id, $my->id);
                } else {
                    $sql = "SELECT * FROM #__boss_" . $this->directory . "_rating WHERE contentid = ? AND userid = ? AND ip = ?";
                    $voted = $_db->selectCell($sql, $this->content->id, 0, ip2long($ip));
                }

                $result = '<div class="ratingblock" id="ratingblock_' . $this->content->id . '">';
                $result .= '<div id="unit_long' . $this->content->id . '">';
                $result .= '<ul id="unit_ul' . $this->content->id . '" class="unit-rating" style="width:' . $this->rating_unitwidth * $this->units . 'px;">';
                $result .= '<li class="current-rating" style="width:' . $rating_width . 'px;">' . _GDRATING_MES04 . ' ' . $rating2 . '/' . $this->units . '</li>';
                for ($i = 1; $i <= $this->units; $i++) {
                    if (empty($voted)) {
                        $result
                            .= '<li><a
						 onclick="gd_rating_plugin('
                            . $i . ','
                            . $this->content->id . ','
                            . ip2long($ip) . ','
                            . $this->units . ','
                            . $this->rating_unitwidth . ','
                            . $this->directory . ','
                            . $my->id . ', \''
                            . _JLPATH_SITE . '/components/com_boss/plugins/ratings/GDRating/db.php\')"
						href="javascript:void(0)"
						title="' . $i . ' out of ' . $this->units . '"
						class="r' . $i . '-unit rater"
						rel="nofollow">' . $i . '</a></li>';
                    }
                }
                $result .= '</ul>';
                $result .= '<p>' . _GDRATING_MES05 . ': <strong> ' . $rating1 . '</strong>/' . $this->units . ' (' . $row['count'] . ' ' . $tense . ')' . '</span></p>';
                $result .= '</div>';
                $result .= '</div>';
                if (!defined('_GDRATING_JS')) {
                    define('_GDRATING_JS', 1);
                    $mainframe->addJS(_JLPATH_SITE . '/components/com_boss/plugins/ratings/GDRating/script.js');
                }
                return $result;
            }
        }
    }

    /**
     * функция для вставки таблиц и полей рейтинга в запрос категории и контента
     * @param $directory
     * @param $conf
     *
     * @return array
     */
    public function queryString($directory, $conf)
    {
        $query = array();
        if ($conf->allow_ratings == 1) {
            $query['tables'] = " LEFT JOIN #__boss_" . $directory . "_rating as rat ON a.id = rat.contentid \n";
            $query['fields'] = " count(DISTINCT rat.id) as num_votes, AVG(rat.value) as sum_votes, rat.id as not_empty, \n";
            $query['wheres'] = '';
        } else {
            $query['tables'] = '';
            $query['fields'] = '';
            $query['wheres'] = '';
        }
        return $query;
    }

    /**
     * действия при установке плагина
     * @param $directory
     *
     * @return void
     */
    public function install($directory)
    {
        $_db = JCore::getDB();
        $sql = "CREATE TABLE IF NOT EXISTS `#__boss_" . $directory . "_rating` (
  					`id` int(10) NOT NULL AUTO_INCREMENT,
  					`contentid` int(10) DEFAULT '0',
  					`userid` int(10) DEFAULT '0',
  					`value` tinyint(1) DEFAULT '5',
  					`ip` int(11) DEFAULT '0',
  					`date` int(10) DEFAULT '0',
  				PRIMARY KEY (`id`)
				)";
        $_db->select($sql);
    }

    /**
     * действия при удалении плагина
     * @param $directory
     *
     * @return void
     */
    public function uninstall($directory)
    {
        $_db = JCore::getDB();
        $sql = "DROP TABLE IF EXISTS `#__boss_" . $directory . "_rating`";
        $_db->select($sql);
    }
}