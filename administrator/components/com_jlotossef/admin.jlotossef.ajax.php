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
$task = JCore::getParam($_POST, 'task', '', 'sn');

switch($task){
    case 'check_sef_dup':
        $sef = JCore::getParam($_POST, 'sef', '', 'sn');

        if(!preg_match("#(\.html|\.htm)$#",$sef)){
            $sef = '/' . trim($sef, '/') . '/';
        }
        $_db = JCore::getDB();
        $row = $_db->selectCell("SELECT COUNT(*) FROM `#__sef_link` WHERE `sef` = ?;", $sef);
        if($row){
            echo '<img src="'._JLPATH_SITE . '/administrator/components/com_jlotossef/images/publish_off.png"/>';
        }else{
            echo '<img src="'._JLPATH_SITE . '/administrator/components/com_jlotossef/images/publish_on.png"/>';
        }
        break;
}
// http://jl-com-sef.qqq/administrator/components/com_jlotossef/images/publish_off.png
