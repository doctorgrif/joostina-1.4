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

$mainframe = mosMainFrame::getInstance();
require_once($mainframe->getPath('toolbar_html'));

$task = JSef::getTask();

switch ($task) {
    case 'savedup':
    case 'editdup':
        ToolbarJLotosSef::editDupMemu();
        break;

    case 'saveref':
    case 'editref':
        ToolbarJLotosSef::editRefMemu();
        break;

    case 'listdup':
        ToolbarJLotosSef::linkDupMemu();
        break;

    case 'listref':
        ToolbarJLotosSef::linkRefMemu();
        break;

    case 'configuration':
        ToolbarJLotosSef::configurationMemu();
        break;

    case 'description':
        ToolbarJLotosSef::descriptionMemu();
        break;

    default:
        ToolbarJLotosSef::defaultMenu();
        break;
}