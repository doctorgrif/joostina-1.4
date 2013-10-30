<?php defined('_JLINDEX') or die(__FILE__);
/**
 * @package   Joostina
 * @copyright Авторские права (C) 2012 Joostina Lotos. Все права защищены.
 * @license   Лицензия http://www.gnu.org/licenses/gpl.html GNU/GPL, или help/license.php
 *            Joostina Lotos - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

/**
 * Проверка, создание, запись данных в Базу Данных
 * @return mixed
 */

function fun3()
{
    /*echo '<pre>';print_r($_POST);echo '</pre>';*/

    $info['title'] = 'Состояние настроек Базы данных';
    $info['content'] = '<div><br><br>';
// Получаем данные POST
    $error = array();
    $DBhostname = (isset($_POST['DBhostname'])) ? trim($_POST['DBhostname']) : '';
    $DBuserName = (isset($_POST['DBuserName'])) ? trim($_POST['DBuserName']) : '';
    $DBpassword = (isset($_POST['DBpassword'])) ? trim($_POST['DBpassword']) : '';
    $DBname = (isset($_POST['DBname'])) ? trim($_POST['DBname']) : '';
    $DBPrefix = (isset($_POST['DBPrefix'])) ? trim($_POST['DBPrefix']) : 'jos_';
    $DBcreated = (isset($_POST['DBcreated'])) ? trim($_POST['DBcreated']) : 1;
    $DBDel = (isset($_POST['DBDel'])) ? trim($_POST['DBDel']) : 0;
    $DBBackup = (isset($_POST['DBBackup'])) ? trim($_POST['DBBackup']) : 1;
    $DBSample = (isset($_POST['DBSample'])) ? trim($_POST['DBSample']) : 1;

    $DBcreated = ($DBcreated === 0 or $DBcreated === '0') ? 0 : 1;
    $DBDel = ($DBDel === 0 or $DBDel === '0') ? 0 : 1;
    $DBBackup = ($DBBackup === 0 or $DBBackup === '0') ? 0 : 1;
    $DBSample = ($DBSample === 0 or $DBSample === '0') ? 0 : 1;

    if (!$DBhostname) {
        $error[] = 'Неверное имя хоста или поле не запонено.';
    }

    if (!$DBuserName) {
        $error[] = 'Неверное имя пользователя или поле не запонено.';
    }

    if (!$DBname) {
        $error[] = 'Неверное имя базы данных или поле не запонено.';
    }

    if ($DBPrefix == '') {
        $error[] = 'Префикс таблиц не верный или поле не заполнено.';
    }

    if (!count($error)) {
       error_reporting(0);
        // Подключаем библиотеку DB
        require_once (_JLPATH_ROOT . '/includes/libraries/database/database.php');

        $database = new mysqli($DBhostname, $DBuserName, $DBpassword);
		$database->set_charset('utf8');

        if (is_null($database->connect_error)) {
		    error_reporting(E_ALL);
            $info['content'] .= '<p>&nbsp;&nbsp;&nbsp;&nbsp;&#149;&nbsp;Новое соединение с <b>MySQLi</b> - <b><span style="color: #090">OK</span></b></p>';

            //Если не выбрано создание базы, пробуем соединиться с указанной
            if ($DBcreated) {
                $sql
                    = "CREATE DATABASE IF NOT EXISTS `$DBname` CHARACTER SET utf8 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT COLLATE utf8_general_ci";
                if ($database->query($sql)) {
                    $info['content'] .= '<p>&nbsp;&nbsp;&nbsp;&nbsp;&#149;&nbsp;Создание базы данных <b>' . $DBname
                        . '</b> - <b><span style="color: #090">OK</span></b></p>';
                } else {
                    $error[] = 'Ошибка создания базы данных <b>' . $DBname . '</b>: ' . $database->error;
                }
            } elseif ($database->select_db($DBname)) {
                $sql = "USE `$DBname` ";
                $database->query($sql);
                $info['content']
                    .= '<p>&nbsp;&nbsp;&nbsp;&nbsp;&#149;&nbsp;Установка соединения с базой данных <b>' . $DBname
                    . '</b> - <b><span style="color: #090">OK</span></b></p>';
            } else {
                $error[] = 'Подключение к базе данных ' . $DBname . ' невозможно.';
            }

            unset($database);

            // создание новых параметров БД и замена существующих
            $database = new mysqli($DBhostname, $DBuserName, $DBpassword, $DBname);
			$database->set_charset('utf8');

            // удаление существующих таблиц (если задано)
            if ($DBDel) {
                $sql = "SHOW TABLES FROM `$DBname`";
                $result = $database->query($sql);
				
                // Временно заблокирована из-за не поддержки сервером
				// $rows = $result->fetch_all(MYSQLI_NUM);
				// вместо этого применяется следующий код
				$rows = array();
				while($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
					$rows[] = $row;
				}
                if (!is_null($rows)) {
                    foreach ($rows as $row) {
                        $table = $row[0];

                        if (substr($table, 0, strlen($DBPrefix)) == $DBPrefix) {
                            $old_table = substr_replace($table, 'old_', 0, strlen($DBPrefix));
                            if ($DBBackup) {
                                $sql = 'DROP TABLE IF EXISTS `' . $old_table . '`;';
                                $database->query($sql);
                                $sql = 'CREATE TABLE IF NOT EXISTS `' . $old_table . '` LIKE `' . $table . '`;';
                                if ($database->query($sql)) {
                                    $info['content']
                                        .= '<p>&nbsp;&nbsp;&nbsp;&nbsp;&#149;&nbsp;Создание резервной копии таблицы <b>'
                                        . $table . '</b> - <b><span style="color: #090">OK</span></b></p>';
                                } else {
                                    $error[]
                                        = 'Ошибка создания резервной копии таблицы <b>' . $table . '</b> [' . $sql
                                        . ']<br>'
                                        . $database->error;
                                }
                            }
                            if ($DBDel) {
                                $sql = "DROP TABLE IF EXISTS `" . $table . "`";
                                if ($database->query($sql)) {
                                    $info['content']
                                        .= '<p>&nbsp;&nbsp;&nbsp;&nbsp;&#149;&nbsp;Удаление таблицы <b>' . $table
                                        . '</b> - <b><span style="color: #090">OK</span></b></p>';
                                } else {
                                    $error[] = 'Ошибка удаления таблицы <b>' . $table . '</b> [' . $sql . ']<br>'
                                        . $database->error;
                                }
                            }
                        }
                    }
                }
            }

            // Создание основных таблиц
            if (is_readable(_JLPATH_ROOT . '/installation/sql/joostina.sql')) {
                $info['content'] .= '<p>&nbsp;&nbsp;&nbsp;&nbsp;&#149;&nbsp;Файл с дампом базы данных - <b><span style="color: #090">Загружен</span></b></p>';

                $sql_array = file(_JLPATH_ROOT . '/installation/sql/joostina.sql', FILE_SKIP_EMPTY_LINES|FILE_IGNORE_NEW_LINES);

                $sql_tmp = '';
                $sql_count = 0;
                foreach($sql_array as $value){
                    $b = preg_match_all('#(^[\s]*$|^[\s]*--|^[\s]*\/\*!|^[\s]*drop|^[\s]*\#)#i', $value, $tmp);
                    if(!$b){
                        $sql_tmp = $sql_tmp . $value;
                        if(preg_match_all('#;\s*$#', $value, $tmp)){
                            $sql_count++;
                            $sql_tmp = str_replace('#__', $DBPrefix, $sql_tmp);
                            if($database->query($sql_tmp)){
                                $info['content'] .= '<p>&nbsp;&nbsp;&nbsp;&nbsp;&#149;&nbsp;SQL-запрос #'.$sql_count.' - <b><span style="color: #090">ОК</span></b></p>';
                            }else{
                                $error[] = 'Ошибка загрузки SQL-запроса #'.$sql_count.'<br>'
                                    . $database->error.'<div style="border: 1px solid #ccc;padding:3px;">'.$sql_tmp.'</div>';
                            }
                            $sql_tmp = '';
                        }
                    }
                }
            } else {
                $error[] = 'Файл с дампом базы данных не существует или не доступен для чтения.';
            }

            // Загрузка примеров
            if($DBSample){
                if (is_readable(_JLPATH_ROOT . '/installation/sql/sample_data.sql')) {
                    $info['content'] .= '<p>&nbsp;&nbsp;&nbsp;&nbsp;&#149;&nbsp;Файл с демонстрационными данными - <b><span style="color: #090">Загружен</span></b></p>';

                    $sql_array = file(_JLPATH_ROOT . '/installation/sql/sample_data.sql', FILE_SKIP_EMPTY_LINES|FILE_IGNORE_NEW_LINES);

                    $sql_tmp = '';
                    $sql_count = 0;

                    foreach($sql_array as $value){
                        $b = preg_match_all('#(^[\s]*$|^[\s]*--|^[\s]*\/\*!|^[\s]*drop|^[\s]*\#)#i', $value, $tmp);
                        if(!$b){
                            $sql_tmp = $sql_tmp . $value;
                            if(preg_match_all('#;\s*$#m', $value, $tmp)){
                                $sql_count++;
                                $sql_tmp = str_replace('#__', $DBPrefix, $sql_tmp);
                                if($database->query($sql_tmp)){
                                    $info['content'] .= '<p>&nbsp;&nbsp;&nbsp;&nbsp;&#149;&nbsp;SQL-запрос примеров #'.$sql_count.' - <b><span style="color: #090">ОК</span></b></p>';
                                }else{
                                    $error[] = 'Ошибка загрузки SQL-запроса примера #'.$sql_count.'<br>'
                                        . $database->error.'<div style="border: 1px solid #ccc;padding:3px;">'.$sql_tmp.'</div>';
                                }
                                $sql_tmp = '';
                            }
                        }
                    }
                } else {
                    $error[] = 'Файл с дампом базы данных не существует или не доступен для чтения.';
                }
            }

        } else {
            $error[]
                = '<span style="color: #f00">Ошибка создания соединения с <b>MySQLi</b></span>:
                        <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- хост: <b>' . $DBhostname . '</b>'
                . '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- пользователь: <b>' . $DBuserName . '</b>'
                . '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- пароль: <b>' . $DBpassword . '</b>';
        }
    }

    if (count($error)) {
        $info['left'] = getLeft(1, 1, 0, 0, 0);
        $info['button'] = getButton(2, 'Вернуться назад', 'form');

        array_unshift($error, '<b>Ошибка!</b>');
        $error = '<br><br><div class="install-text" style="color:red">' . implode(
            '<br>&nbsp;&nbsp;&nbsp;&nbsp;&#149;&nbsp;', $error
        ) . '</div>';

        $info['content'] .= $error . '
        <form action="index.php?page=2" method="post" name="form" id="form">
            <input type="hidden" name="DBhostname" value="' . $DBhostname . '"/>
            <input type="hidden" name="DBuserName" value="' . $DBuserName . '"/>
            <input type="hidden" name="DBpassword" value="' . $DBpassword . '"/>
            <input type="hidden" name="DBname" value="' . $DBname . '"/>
            <input type="hidden" name="DBPrefix" value="' . $DBPrefix . '"/>
            <input type="hidden" name="DBcreated" value="' . $DBcreated . '" />
            <input type="hidden" name="DBDel" value="' . $DBDel . '" />
            <input type="hidden" name="DBBackup" value="' . $DBBackup . '" />
            <input type="hidden" name="DBSample" value="' . $DBSample . '" />
        </form>
    ';
    } else {
        $info['left'] = getLeft(1, 1, 1, 0, 0);
        $info['button'] = getButton(4, 'Далее', 'form');
    }
    $info['content'] .= '
            <form action="index.php?page=4" method="post" name="form" id="form">
            <input type="hidden" name="DBhostname" value="' . $DBhostname . '"/>
            <input type="hidden" name="DBuserName" value="' . $DBuserName . '"/>
            <input type="hidden" name="DBpassword" value="' . $DBpassword . '"/>
            <input type="hidden" name="DBname" value="' . $DBname . '"/>
            <input type="hidden" name="DBPrefix" value="' . $DBPrefix . '"/>
        </form>
    </div>';

    return $info;
}

























