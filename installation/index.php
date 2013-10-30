<?php
/**
 * @package   Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license   Лицензия http://www.gnu.org/licenses/gpl.html GNU/GPL, или help/license.php
 *            Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */
define('_JLINDEX', 1);


// корень файлов
define('_JLPATH_ROOT', dirname(dirname(__FILE__)));

require_once(_JLPATH_ROOT . '/core/defines.php');

$page = (isset($_GET['page'])) ? intval($_GET['page']) : 0;

// Проверка на существование файла конфигурации
if (file_exists(_JLPATH_ROOT . '/configuration.php') and filesize(_JLPATH_ROOT . '/configuration.php') > 10 and $page != 6) {
    header("Location: " . _JLPATH_ROOT . "/index.php");
    exit();
}

// подключаем дополнительные функции
require_once(_JLPATH_ROOT . '/installation/function.php');

require_once(_JLPATH_ROOT . '/includes/version.php');

switch ($page) {
    case 6:
        require_once(_JLPATH_ROOT . '/installation/page6.php');
        $info = fun6();
        break;
    case 5:
        require_once(_JLPATH_ROOT . '/installation/page5.php');
        $info = fun5();
        break;
    case 4:
        require_once(_JLPATH_ROOT . '/installation/page4.php');
        $info = fun4();
        break;
    case 3:
        require_once(_JLPATH_ROOT . '/installation/page3.php');
        $info = fun3();
        break;
    case 2:
        require_once(_JLPATH_ROOT . '/installation/page2.php');
        $info = fun2();
        break;
    case 1:
        require_once(_JLPATH_ROOT . '/installation/page1.php');
        $info = fun1();
        break;
    default:
        require_once(_JLPATH_ROOT . '/installation/page0.php');
        $page = 0;
        $info = fun0();
}

$info['content_title'] = getTitle($page, $info['title']);

$info['version'] = joomlaVersion::get('CMS') . ' '
    . joomlaVersion::get('CMS_VER') . '.'
    . joomlaVersion::get('DEV_LEVEL') . '<br>'
    . joomlaVersion::get('CODENAME') . ' ['
    . joomlaVersion::get('DEV_STATUS') . ':'
    . joomlaVersion::get('BUILD') . ']';


/******************************************************************
 *                  Шаблон
 ******************************************************************/
?>
<!DOCTYPE html>
<html>
<head>
    <title>Joostina Lotos. <?php echo $info['title']; ?></title>
    <meta charset="utf8"/>
    <meta name="generator" content="Joostina Lotos CMS"/>
    <link rel="shortcut icon" href="favicon.ico"/>
    <link rel="stylesheet" href="install.css" type="text/css"/>
    <script src="../includes/js/jquery/jquery.js"></script>
</head>
<body>

<div id="tpl_body">
    <div id="tpl_left">
        <div id="tpl_left_1"></div>
        <div id="tpl_left_2"></div>
        <div id="tpl_left_3">
            <div><?php echo $info['left']; ?></div>
        </div>
    </div>
    <div id="tpl_right">
        <div id="tpl_right_1"></div>
        <div id="tpl_right_2">
            <div>
                <div class="div_but"><?php echo $info['button']; ?></div>
                <?php echo $info['content_title']; ?>
                <?php echo $info['content']; ?>
            </div>
        </div>
    </div>
    <div class="clear"></div>
    <div id="tpl_down">
        <div id="tpl_down_1">
            <div><?php echo $info['version']; ?></div>
        </div>
        <div id="tpl_down_2">
            <div>
                <a href="http://joostina-cms.ru">Joostina Lotos</a> - свободное программное обеспечение (<a href="http://www.gnu.org/licenses/gpl.html">GNU General Public License version 3</a>)
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>
</body>
</html>
