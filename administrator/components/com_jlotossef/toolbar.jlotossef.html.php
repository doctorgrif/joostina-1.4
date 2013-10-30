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

class ToolbarJLotosSef{

    public static function linkDupMemu(){
        $_lang = JLotosSefClass::getLang();
        mosMenuBar::startTable();
        mosMenuBar::deleteList('','removedup');
        mosMenuBar::editList('editdup');
        mosMenuBar::back($_lang['JLSEF_DES_1'], 'index2.php?option=com_jlotossef');
        mosMenuBar::endTable();
    }

    /**
     * Редактирование дубликатов
     */
    public static function editDupMemu(){
        $_lang = JLotosSefClass::getLang();
        mosMenuBar::startTable();
        mosMenuBar::save('savedup');
        mosMenuBar::cancel('listdup');
        mosMenuBar::back($_lang['JLSEF_DES_1'], 'index2.php?option=com_jlotossef');
        mosMenuBar::endTable();
    }
    /**
     * Меню по уполчанию: список форм
     */
    public static function defaultMenu(){
	}

    /**
     * Описание sef-файла
     */
    public static function descriptionMemu(){
        $_lang = JLotosSefClass::getLang();
        mosMenuBar::startTable();
        mosMenuBar::back($_lang['JLSEF_DES_1'], 'index2.php?option=com_jlotossef');
        mosMenuBar::endTable();
    }

    public static function configurationMemu(){
        $_lang = JLotosSefClass::getLang();
        mosMenuBar::startTable();
        mosMenuBar::cancel();
        mosMenuBar::save('savecfg');
        mosMenuBar::apply('applycfg');
        mosMenuBar::back($_lang['JLSEF_DES_1'], 'index2.php?option=com_jlotossef');
        mosMenuBar::endTable();
    }

    public static function linkRefMemu(){
        $_lang = JLotosSefClass::getLang();
        mosMenuBar::startTable();
        mosMenuBar::deleteList('','removeref');
        mosMenuBar::editList('editref');
        mosMenuBar::back($_lang['JLSEF_DES_1'], 'index2.php?option=com_jlotossef');
        mosMenuBar::endTable();
    }

    public static function editRefMemu(){
        $_lang = JLotosSefClass::getLang();
        mosMenuBar::startTable();
        mosMenuBar::save('saveref');
        mosMenuBar::cancel('listref');
        mosMenuBar::back($_lang['JLSEF_DES_1'], 'index2.php?option=com_jlotossef');
        mosMenuBar::endTable();
    }
}





















