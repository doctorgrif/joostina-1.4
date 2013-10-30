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

$my = JCore::getUser();

// Проверка доступа к компоненту
if (!($acl->acl_check('administration', 'config', 'users', $my->usertype)) || $acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'com_gdfeedback')) {
    mosRedirect('index2.php', _NOT_AUTH);
}

require_once(_JLPATH_ADMINISTRATOR . '/components/com_jlotossef/admin.jlotossef.class.php');
require_once(_JLPATH_ADMINISTRATOR . '/components/com_jlotossef/admin.jlotossef.html.php');

$task = JSef::getTask();

switch ($task) {

    // Сохранение le,kbrfnf
    case 'savedup':
        JLotosSefClass::saveDup();
        break;

    // Редактирование дубликата
    case 'editdup':
        $cid = josGetArrayInts('cid');
        JLotosSefClass::editDup(intval($cid[0]));
        break;

    // Удаление дубликатов
    case 'removedup':
        $cid = josGetArrayInts('cid');
        JLotosSefClass::pageRemoveDup($cid);
        break;

    // Вывод списка дубликатов
    case 'listdup':
        JLotosSefClass::pageDup();
        break;

    // Сохранение ссылки
    case 'saveref':
        JLotosSefClass::saveRef();
        break;

    // Редактирование ссылки
    case 'editref':
        $cid = josGetArrayInts('cid');
        JLotosSefClass::editRef(intval($cid[0]));
        break;

    // Удаление ссылки
    case 'removeref':
        $cid = josGetArrayInts('cid');
        JLotosSefClass::pageRemoveRef($cid);
        break;

    // Вывод списка ссылкок
    case 'listref':
        JLotosSefClass::pageRef();
        break;

    // Запись ("Применить") настроек
    case 'applycfg':
        JLotosSefClass::saveConfiguration(true);
        break;

    // Запись настроек
    case 'savecfg':
        JLotosSefClass::saveConfiguration();
        break;

    // Настройки
    case 'configuration':
        JLotosSefClass::pageConfiguration();
        break;

    // Экспарт таблицы дубликатов
    case 'expd':
        JLotosSefClass::exportLink(1);
        break;

    // Экспорт таблицы ссылок
    case 'expr':
        JLotosSefClass::exportLink(0);
        break;

    // Очистка таблицы дубликатов
    case 'clrd':
        JLotosSefClass::clearLink(1);
        break;

    // Очистка таблицы ссылок
    case 'clrr':
        JLotosSefClass::clearLink(0);
        break;

    // Описание
    case 'description':
        JLotosSefClass::pageDescription();
        break;

    // Главная страница
    default:
        JLotosSefClass::pageDefault();
        break;
}

// /index.php?option=com_boss&amp;task=show_content&amp;catid=1&amp;contentid=13&amp;directory=5
// /index.php?option=com_boss&task=show_content&catid=1&contentid=13&directory=5