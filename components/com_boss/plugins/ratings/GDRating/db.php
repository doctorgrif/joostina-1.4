<?php

if (isset($_SERVER['HTTP_REFERER'])) {
    // Установка флага родительского файла
    define('_JLINDEX', 1);

    // корень файлов
    define('_JLPATH_ROOT', $_SERVER['DOCUMENT_ROOT']);

    // подключение основных глобальных переменных
    require_once _JLPATH_ROOT . '/core/defines.php';

    // подключение конфигурации
    require_once(_JLPATH_ROOT . '/configuration.php');

    // подключение языкового файла
    $path = __DIR__ . '/lang';

    // подключение главного файла - ядра системы
    require_once(_JLPATH_ROOT . '/core/core.php');

    // подключение главного файла - ядра системы
    // TODO GoDr: заменить со временем на core.php
    require_once(_JLPATH_ROOT . '/includes/joostina.php');

    if (file_exists($path . '/' . JCore::getCfg('lang') . '.php')) {
        require_once($path . '/' . JCore::getCfg('lang') . '.php');
    } else {
        require_once($path . '/' . 'russian.php');
    }

    // балл
    $value = isset($_REQUEST['value']) ? intval($_REQUEST['value']) : 0;
    // ID контента
    $content_id = isset($_REQUEST['content_id']) ? intval($_REQUEST['content_id']) : 0;
    // IP
    $ip = isset($_REQUEST['ip']) ? intval($_REQUEST['ip']) : 0;
    // ID пользователя
    $user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
    // Каталог
    $directory = isset($_REQUEST['directory']) ? intval($_REQUEST['directory']) : 0;

    if ($value == 0 OR $content_id == 0 OR ($ip == 0 AND $user_id == 0)) {
        echo _GDRATING_MES08;
    } else {
        // количество звёздочек
        $units = isset($_REQUEST['units']) ? intval($_REQUEST['units']) : 10;
        // ширина звёздочки
        $width = isset($_REQUEST['width']) ? intval($_REQUEST['width']) : 30;

        $_db = JCore::getDB();

        $sql = "INSERT INTO #__boss_" . $directory . "_rating (id, contentid, userid, value, ip, date)
                VALUES (?, ?, ?, ?, ?, ?);";
        $_db->insert($sql, '', $content_id, $user_id, $value, $ip, time());

        $row = $_db->selectRow("SELECT COUNT(*) AS count, SUM(value) AS sum FROM #__boss_" . $directory . "_rating WHERE `contentid` = ?", $content_id);

        require_once(_JLPATH_ROOT . '/includes/libraries/text/text.php');

        $rating_width = (!empty($row['count'])) ? number_format($row['sum'] / $row['count'], 2) * $width : 0;
        $rating1 = (!empty($row['count'])) ? number_format($row['sum'] / $row['count'], 1) : "0.0";
        $rating2 = (!empty($row['count'])) ? number_format($row['sum'] / $row['count'], 2) : "0.00";

        $tense = Text::declension($row['count'], array(_GDRATING_MES01, _GDRATING_MES02, _GDRATING_MES03));

        $static_rater = array();
        $static_rater[] .= '<div id="unit_long' . $content_id . '">';
        $static_rater[] .= '<ul id="unit_ul' . $content_id . '" class="unit-rating" style="width:' . $width * $units . 'px;">';
        $static_rater[] .= '<li class="current-rating" style="width:' . $rating_width . 'px;">' . _GDRATING_MES04 . ' ' . $rating2 . '/' . $units . '</li>';
        $static_rater[] .= '</ul>';
        $static_rater[] .= '<p class="static">' . _GDRATING_MES05 . ': <strong> ' . $rating1 . '</strong>/' . $units . ' (' . $row['count'] . ' ' . $tense . ')';
        $static_rater[] .= '<br /><span class="thanks">' . _GDRATING_MES09 . '.</span></p>';
        $static_rater[] .= '</div>';
        echo join("", $static_rater);

    }
} else {
    echo 'Нарушение безопасности: попытка прямого доступа';
}















