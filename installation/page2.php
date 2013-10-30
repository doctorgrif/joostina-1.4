<?php defined('_JLINDEX') or die(__FILE__);
/**
 * @package   Joostina
 * @copyright Авторские права (C) 2012 Joostina Lotos. Все права защищены.
 * @license   Лицензия http://www.gnu.org/licenses/gpl.html GNU/GPL, или help/license.php
 *            Joostina Lotos - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

/**
 * Третья страница установки
 * @return mixed
 */
function fun2()
{
    $DBhostname = (isset($_POST['DBhostname'])) ? trim($_POST['DBhostname']) : '';
    $DBuserName = (isset($_POST['DBuserName'])) ? trim($_POST['DBuserName']) : '';
    $DBpassword = (isset($_POST['DBpassword'])) ? trim($_POST['DBpassword']) : '';
    $DBname = (isset($_POST['DBname'])) ? trim($_POST['DBname']) : '';
    $DBPrefix = (isset($_POST['DBPrefix'])) ? trim($_POST['DBPrefix']) : 'jos_';
    $DBcreated = (isset($_POST['DBcreated'])) ? trim($_POST['DBcreated']) : 0;
    $DBcreated_checked = ($DBcreated) ? 'checked' : '';
    $DBDel = (isset($_POST['DBDel'])) ? intval($_POST['DBDel']) : 0;
    $DBDel_checked = ($DBDel) ? 'checked' : '';
    $DBBackup = (isset($_POST['DBBackup'])) ? intval($_POST['DBBackup']) : 0;
    $DBBackup_checked = ($DBBackup) ? 'checked' : '';
    $DBSample = (isset($_POST['DBSample'])) ? intval($_POST['DBSample']) : 1;
    $DBSample_checked = ($DBSample) ? 'checked' : '';

    $info['left'] = getLeft(1, 1, 0, 0, 0);
    $info['title'] = 'Настройка базы данных';

    $info['button'] = getButton(1, 'Лицензионное соглашение');
    $info['button'] .= getButton(3, 'Далее', 'form');
    ob_start();
    ?>
    <form action="index.php?page=3" method="post" name="form" id="form">
        <table class="content">
            <tr>
                <td>Имя хоста MySQL</td>
                <td><input class="inputbox" type="text" name="DBhostname" value="<?php echo $DBhostname; ?>"/></td>
                <td>Обычно это &nbsp;<b>localhost</b></td>
            </tr>
            <tr>
                <td>Имя пользователя MySQL</td>
                <td><input class="inputbox" type="text" name="DBuserName" value="<?php echo $DBuserName; ?>"/></td>
                <td>Для установки на домашнем компьютере чаще всего используется имя <b>root</b> <i>(для Denwer)</i> или <b>mysql</b> <i>(для OpenServer)</i>, а для установки в Интернете, введите данные, полученные у Хостера.</td>
            </tr>
            <tr>
                <td>Пароль доступа к БД MySQL</td>
                <td><input class="inputbox" type="text" name="DBpassword" value="<?php echo $DBpassword; ?>"/></td>
                <td>Оставьте поле пустым для домашней установки или введите пароль доступа к Вашей БД, полученный у хостера.</td>
            </tr>
            <tr>
                <td>Имя БД MySQL</td>
                <td><input class="inputbox" type="text" name="DBname" value="<?php echo $DBname; ?>"/></td>
                <td>Имя существующей или новой БД, которая будет использоваться для сайта</td>
            </tr>
            <tr>
                <td>Префикс таблиц БД MySQL</td>
                <td><input class="inputbox" type="text" name="DBPrefix" value="<?php echo $DBPrefix; ?>"/></td>
                <td>Используйте префикс таблиц для установки в одну БД. Не используйте <b>old_</b> - это зарезервированное значение.</td>
            </tr>
            <tr>
                <td>Создать базу данных, если её нет</td>
                <td><input type="hidden" value="0" name="DBcreated"><input id="DBcreated" type="checkbox" name="DBcreated" <?php echo $DBcreated_checked; ?> /></td>
                <td>Внимание! Не на всех хостингах создание БД таким способом будет возможно. В случае возникновения ошибок - создайте пустую БД стандартным для вашего хостинга способом и выберите её</td>
            </tr>
            <tr>
                <td>Удалить существующие таблицы</td>
                <td><input type="hidden" value="0" name="DBDel"><input id="DBDel" type="checkbox" name="DBDel" <?php echo $DBDel_checked; ?> /></td>
                <td>Все существующие таблицы от предыдущих установок Joostina будут удалены.
                </td>
            </tr>
            <tr>
                <td>Создать резервные копии существующих таблиц</td>
                <td><input type="hidden" value="0" name="DBBackup"><input id="DBBackup" type="checkbox" name="DBBackup" <?php echo $DBBackup_checked; ?> /></td>
                <td>Все существующие резервные копии таблиц от предыдущих установок Joostina будут заменены.</td>
            </tr>
            <tr>
                <td>Установить демонстрационные данные</td>
                <td><input type="hidden" value="0" name="DBSample"><input type="checkbox" name="DBSample" <?php echo $DBSample_checked; ?> /></td>
                <td><span style="color: #ff0000">Не выключайте это, если Вы ещё не знакомы с Joostina Lotos! Вы всегда можете легко удалить примеры!</span></td>
            </tr>
        </table>
    </form>
    <script>
        $(function () {
            $("input[name=DBBackup]").prop({
                "disabled": true,
                "checked": false
            });

            $("input[name=DBcreated]").change(function () {
                if ($(this).prop("checked")) {
                    $("input[name=DBDel]").prop("disabled", false);
                } else {
                    $("input[name=DBDel]").prop({
                        "disabled": true,
                        "checked": false
                    });
                    $("input[name=DBBackup]").prop({
                        "disabled": true,
                        "checked": false
                    }   );
                }
            });

            $("input[name=DBDel]").prop({
                "disabled": true,
                "checked": false
            }).change(function () {
                    if ($(this).prop("checked")) {
                        $("input[name=DBBackup]").prop("disabled", false);
                    } else {
                        $("input[name=DBBackup]").prop({
                            "disabled": true,
                            "checked": false
                        });
                    }
                });
        });
    </script>
    <?php
    $info['content'] = ob_get_contents();
    ob_end_clean();
    return $info;
}
