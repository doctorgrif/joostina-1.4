<?php
defined('_JLINDEX') or die();
global $my, $mainframe;
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<?php
	if($my->id && $mainframe->allow_wysiwyg){
		initEditor();
	}
	echo $mainframe->addJS(_JLPATH_SITE . '/templates/' . JTEMPLATE . '/js/html5.js');
	echo $mainframe->addCSS(_JLPATH_SITE . '/templates/' . JTEMPLATE . '/css/template_css.css');
	mosShowHead(array('js'=> 1, 'css'=> 1, 'jquery'=> 1));
	?>
</head>
<body>

<div id="tpl_body">
    <div id="tpl_left">
        <div id="tpl_left_1"></div>
        <div id="tpl_left_2"></div>
        <div id="tpl_left_3">
            <div>
                <?php mosLoadModules('user1'); ?>
                <?php mosLoadModules('left'); ?>
                <?php mosLoadModules('user2'); ?>

            </div>
        </div>
    </div>
    <div id="tpl_right">
        <div id="tpl_right_1">
            <div id="tpl_right_1_1"><?php mosLoadModules('top'); ?></div>
        </div>
        <div id="tpl_right_2">
            <div>
                <?php mosLoadModules('menu1'); ?>
                <?php mosLoadModules('banner1'); ?>
                <?php mosLoadModules('user3'); ?>
                <div><?php mosMainbody(); ?></div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
    <div id="tpl_down">
        <div id="tpl_down_1">
            <div>
                <?php
                $_version = new joomlaVersion();
                echo joomlaVersion::get('CMS'). '<br>';
                echo $_version->CODENAME . ' ' . $_version->CMS_VER . '.' . $_version->DEV_LEVEL . ' [' . $_version->DEV_STATUS . ' : ' . $_version->BUILD . '] ';
                ?>
            </div>
        </div>
        <div id="tpl_down_2">
            <div>
                <a href="http://joostina-cms.ru">Joostina Lotos</a> - свободное программное обеспечение (<a href="http://www.gnu.org/licenses/gpl.html">GNU General Public License version 3</a>)
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>

<?php
mosShowFooter(array('js'=> 1));
mosShowFooter(array('custom'=> 1));
?>
</body>
</html>