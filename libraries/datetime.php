<?php
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
 * @Date      10.07.2013
 * @see       http://wiki.joostina-cms.ru/index.php/JLDateTime
 */

defined('_JLINDEX') or die;

/**
 * Класс для работы с Датой и Временем
 */
class JLDateTime
{
    /**
     * Возвращает разницу дат
     * @param datetime      $date1 - уменьшаемое
     * @param datetime|null $date2 - вычитаемое (если не задана то текущая)
     *
     * @return int - разность (в днях)
     */
    public static function getDateDiff($date1, $date2 = null)
    {
        if (is_null($date2)) {
            $date2 = date("Y-m-d");
        }
        $d2 = new DateTime($date1);
        $d1 = new DateTime($date2);

        $result = intval($d1->diff($d2)->format("%r%a"));
        return $result;
    }

    /**
     * Прибавляет интервал к дате
     * @param string $date     - дата
     * @param string $interval - интервал в формате ISO 8601, например, P5В (5 дней) или P3Y (3 года)
     *                         Y - Количество лет
     *                         M - Количество месяцев
     *                         D - Количество дней
     * @param string $format   - возвращаемый формат (по умолчанию  d.m.Y)
     *
     * @return string - дата в формате $format
     */
    public static function getDateAdd($date, $interval, $format = 'd.m.Y')
    {
        $d1 = new DateTime($date);
        $result = $d1->add(new DateInterval($interval))->format($format);
        return $result;
    }

    /**
     * Отнимает интервал от дате
     * @param string $date     - дата
     * @param string $interval - интервал в формате ISO 8601, например, P5В (5 дней) или P3Y (3 года)
     *                         Y - Количество лет
     *                         M - Количество месяцев
     *                         D - Количество дней
     * @param string $format   - возвращаемый формат (по умолчанию  d.m.Y)
     *
     * @return string - дата в формате $format
     */
    public static function getDateSub($date, $interval, $format = 'd.m.Y')
    {
        $d1 = new DateTime($date);
        $result = $d1->sub(new DateInterval($interval))->format($format);
        return $result;
    }

    /**
     * Преобразует дату в нужный формат
     *
     * @param string $date     - дата
     * @param string $format   - возвращаемый формат (по умолчанию  d.m.Y)
     *
     * @return string
     */
    public static function formatDate($date = '00.00.0000', $format = 'd.m.Y')
    {
        $date_obj = new DateTime($date);
        return $date_obj->format($format);
    }
}
