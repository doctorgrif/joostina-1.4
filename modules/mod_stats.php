<?php
/**
 * @package   Joostina Lotos
 * @copyright Авторские права (C) 2012 Joostina Lotos. Все права защищены.
 * @license   Лицензия http://www.gnu.org/licenses/gpl.html GNU/GPL, или help/license.php
 *            Joostina Lotos - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_JLINDEX') or die();

$serverinfo = $params->get('serverinfo');
$siteinfo = $params->get('siteinfo');

$content = '';

if ($serverinfo) {
    echo "<strong>OS:</strong> " . substr(php_uname(), 0, 7) . "<br />\n";
    echo "<strong>PHP:</strong> " . phpversion() . "<br />\n";
    echo "<strong>MySQL:</strong> " . $database->getVersion() . "<br />\n";
    echo "<strong>" . _TIME_STAT . ": </strong> " . date("H:i", time() + (JCore::getCfg('offset') * 60 * 60)) . "<br />\n";
    $c = JCore::getCfg('caching') ? _YES : _NO;
    echo '<strong>' . _CACHE . ':</strong> ' . $c . '<br />';
    $z = JCore::getCfg('gzip') ? _YES : _NO;
    echo '<strong>GZIP:</strong> ' . $z . '<br />';
}

if ($siteinfo) {
    $query = "SELECT COUNT( id ) AS count_users FROM #__users";
    $database->setQuery($query);
    echo "<strong>" . _MEMBERS_STAT . ":</strong> " . $database->loadResult() . "<br />\n";

    $query = "SELECT COUNT( id ) AS count_links FROM #__weblinks WHERE published = 1";
    $database->setQuery($query);
    echo "<strong>" . _LINKS_COUNT . ":</strong> " . $database->loadResult() . "<br />\n";
}

if (JCore::getCfg('enable_stats')) {
    $counter = $params->get('counter');
    $increase = $params->get('increase');
    if ($counter) {
        $query = "SELECT SUM( hits ) AS count FROM #__stats_agents WHERE type = 1";
        $database->setQuery($query);
        $hits = $database->loadResult();

        $hits = $hits + $increase;

        if ($hits == NULL) {
            $content .= "<strong>" . _VISITORS . ":</strong> 0\n";
        } else {
            $content .= "<strong>" . _VISITORS . ":</strong> " . $hits . "\n";
        }
    }
}