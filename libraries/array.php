<?php defined('_JLINDEX') or die;
/**
 * Joostina Lotos CMS 1.4.3
 *
 * @package   LIBRARIES
 * @version   1.4.4
 * @author    Gold Dragon <illusive@bk.ru>
 * @link      http://gd.joostina-cms.ru
 * @copyright 2000-2013 Gold Dragon
 * @license   GNU GPL: http://www.gnu.org/licenses/gpl-3.0.html
 *            Joostina Lotos CMS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL. (help/copyright.php)
 * @Date      11.07.2013
 * @see       http://wiki.joostina-cms.ru/index.php/JLArray
 */

/**
 * Class JLArray - Класс для работы с массвами
 */
class JLArray{

    /**
     * Преобразование одномерного массива в объект
     *
     * @param array $array - массив с данными
     *
     * @return stdClass - объект с данными
     */
    public static function ArrayToObject($array = array()){
        $object = new stdClass();
        if(is_array($array) and sizeof($array)){
            foreach($array as $key => $value){
                $object->$key = $value;
            }
        }
        return $object;
    }
}
