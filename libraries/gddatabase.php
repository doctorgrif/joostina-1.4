<?php
/**
 * GDDatabase - Класс для управления базой данных
 *
 * @package     GDDatabase
 * @version     1.3
 * @author      Gold Dragon <illusive@bk.ru>
 * @link        http://gdlotos.ru
 * @copyright   2000-2013 Gold Dragon
 * @license     GNU GPL: http://www.gnu.org/licenses/gpl-3.0.html
 * @date        19.01.2013
 * @see         https://code.google.com/p/gddatabase/
 * @see         https://code.google.com/p/gddatabase/w/list
 *
 * @description Класс для управления базой данных. MySQLi::stmt - подготовленные выражения
 */

class GDDatabase
{
    /** @var string - хост */
    private $_db_host;

    /** @var string - пользователь */
    private $_db_user;

    /** @var string - пароль */
    private $_db_password;

    /** @var string - имя БД */
    private $_db_name;

    /** @var string - порт */
    private $_db_port;

    /** @var string - сокет */
    private $_db_socket;

    /** @var int - флаг отладки */
    private $_db_debug;

    /** @var int - флаг кэширования */
    private $_db_caching;

    /** @var string - префикс для таблиц */
    private $_db_prefix;

    /** @var string - путь до файлов кэширования */
    private $_db_cache_dir;

    /** @var int - время кэширования */
    private $_db_cache_time;

    /** @var null|object - интерфейс */
    protected static $_resource = null;

    /** @var \mysqli - MySQLi object */
    protected $db_resource;

    /** @var \mysqli_stmt - MySQLi_STMT object */
    protected $stmp;

    /** @var string - имя метода для подготовленного запроса */
    protected $returnMethod;

    /** @var array - ассоциациативный массив
     *      [affected_rows] - Количество строк, затронутых последним запросом
     *      [insert_id]     - ID, сгенерированный предыдущей операцией INSERT,
     *      [num_rows]      - Число строк в результате запроса
     *      [field_count]   - Число полей в заданном выражении
     *      [sqlstate]      - SQLSTATE ошибка последнего запроса
     */
    protected $queryInfo;

    /***************************************************************************
     * Magic methods
    /***************************************************************************/

    /**
     * Конструктор
     * Защищаем от создания через new
     */
    private function __construct()
    {
        // Сохранение основных настроек
        $this->_db_host = JCore::getCfg('host');

        $this->_db_user = JCore::getCfg('user');

        $this->_db_password = JCore::getCfg('password');

        $this->_db_name = JCore::getCfg('db');

        $this->_db_port = JCore::getCfg('port');

        $this->_db_socket = JCore::getCfg('socket');

        $this->_db_debug = JCore::getCfg('debug');

        $this->_db_caching = JCore::getCfg('caching');

        $this->_db_prefix = JCore::getCfg('dbprefix');

        $this->_db_cache_dir = JCore::getCfg('cachepath');

        $this->_db_cache_time = JCore::getCfg('cachetime');

        // Проверка существует ли вообще функция подключения
        if (!function_exists('mysqli_connect')) {
            throw new Exception(_EXCEP_MYSQLI_MODULE_NOT);
        }

        // Временно отключаем вывод ошибок
        $_error = error_reporting();
        error_reporting(0);

        // Создаём объект базы
        $this->db_resource = new mysqli(
            $this->_db_host,
            $this->_db_user,
            $this->_db_password,
            $this->_db_name,
            $this->_db_port,
            $this->_db_socket);

        if ($this->db_resource->connect_error) {
            throw new Exception(_EXCEP_ERROR_CONNECT_DB);
        }

        // Возвращаем состояние вывода ошибок
        error_reporting($_error);

        // Устанавливаем кодировку
        if (!$this->db_resource->set_charset('utf8')) {
            throw new Exception(sprintf(_EXCEP_ERROR_LOADING_CHARACTER_SET, $this->db_resource->character_set_name()));
        }

        // записываем логи запросов
        if ($this->_db_debug) {
            $this->db_resource->query('set profiling=1');
            $this->db_resource->query('set profiling_history_size=100');
        }
    }

    /**
     * Защищаем от создания через клонирование
     */
    private function __clone()
    {
    }

    /**
     * Защищаем от создание через unserialize
     */
    private function __wakeup()
    {
    }

    /**
     * @static Подключение класса
     *
     * @return object
     */
    public static function Init()
    {
        if (!is_object(self::$_resource)) {
            $class_name = __CLASS__;
            self::$_resource = new $class_name;
        }

        return self::$_resource;
    }


    /***************************************************************************
     * Private methods
    /***************************************************************************/

    /**
     * Передаёт параметры в подготовленный запрос
     *
     * @param  array  $bindVars - значения для заметы
     * @param  array  $params   - параметры
     *
     * @return mixed
     */
    private function _bindParams($bindVars, &$params)
    {
        $params[] = $this->_getParamTypes($bindVars);
        foreach ($bindVars as $key => $param) {
            $params[] = & $bindVars[$key];
        }
        return call_user_func_array(array($this->stmp, 'bind_param'), $params);
    }

    /**
     * Связывает результат с запросом
     *
     * @param  $data
     *
     * @return mixed
     */
    private function _bindResult(&$data)
    {
        $this->stmp->store_result();
        $variables = array();

        $meta = $this->stmp->result_metadata();

        while ($field = $meta->fetch_field()) {
            $variables[] = & $data[$field->name];
        }

        return call_user_func_array(array($this->stmp, 'bind_result'), $variables);
    }

    /**
     * Возвращает полный результат запроса
     *
     * @param  string $data - строка запроса
     *
     * @return array|bool - двухмерный массив с результатом запроса
     */
    private function _mysqliFetchAssoc(&$data)
    {
        $i = 0;
        $array = array();
        while ($this->stmp->fetch()) {
            $array[$i] = array();
            foreach ($data as $k => $v) {
                $array[$i][$k] = $v;
            }
            $i++;
        }

        return $array;
    }

    /**
     * Возвращает первый столбец запроса
     *
     * @param string $data - строка запроса
     *
     * @return array|bool - возвращаемые значения
     */
    private function _mysqliFetchCol(&$data)
    {
        $i = 0;
        $array = array();
        while ($this->stmp->fetch()) {
            $array[$i] = array();
            foreach ($data as $v) {
                $array[$i] = $v;
                break;
            }
            $i++;
        }

        return $array;
    }

    /**
     * Возвращает первую строку запроса
     *
     * @param string $data - строка запроса
     *
     * @return array - возвращаемые значения
     */
    private function _mysqliFetchRow(&$data)
    {
        $this->stmp->fetch();

        return $data;
    }

    /**
     * Возвращает первую ячейку первой строки запроса
     *
     * @param string $data - строка запроса
     *
     * @return string - возвращаемые значения
     */
    private function _mysqliFetchCell(&$data)
    {
        $this->stmp->fetch();

        return $data[key($data)];
    }

    /**
     * Возвращает откомпелированную строку для связи с параметрами
     *
     * @param  $arguments
     *
     * @return string
     */
    private function _getParamTypes($arguments)
    {
        unset($arguments[0]);
        $retval = '';
        foreach ($arguments as $arg) {
            $retval .= $this->getTypeByVal($arg);
        }

        return $retval;
    }

    /**
     * Замена префикса в SQL-запросе
     *
     * @param $sql - SQL-запрос
     *
     * @return mixed
     */
    private function _replacePrefix(&$sql)
    {
        $sql = str_replace('#__', $this->_db_prefix, $sql);
    }

    /**
     * Проверяем существует ли имя таблицы в базе
     *
     * @param null|string $table - имя таблицы
     *
     * @return bool
     */
    private function _checkTable($table = null)
    {
        // Проверяем, а задано ли имя таблицы
        if (!is_null($table)) {
            // меняем префикс
            $this->_replacePrefix($table);

            // Получаем все таблицы в базе
            $row = $this->select("SHOW TABLES FROM `" . $this->_db_name . "`");
            $b = preg_match('#"' . $table . '"#', serialize($row));

            // Eсли такая таблица есть
            if ($b) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }

    }

    /***************************************************************************
     * Protected methods
    /***************************************************************************/

    /**
     * Выполнение запроса
     *
     * @param array $arguments - данные запроса
     *
     * @return bool
     * @throws Exception
     */
    protected function query($arguments)
    {
        $this->prepareQuery($arguments);
        $query = $arguments[0];

        if ($this->stmp) {
            $this->stmp->close();
        }

        $bindVars = $arguments;
        unset($bindVars[0]);

        if (substr_count($query, '?') == count($bindVars)) {

            $this->stmp = $this->db_resource->prepare($query);
            if (!$this->stmp) {
                return false;
            }

            if (count($bindVars)) {
                $params = array();
                $binding = $this->_bindParams($bindVars, $params);
                if (!$binding) {
                    return false;
                }
            }
        } else {
            throw new Exception(sprintf(_EXCEP_SQL_ISNOT_DATA, substr_count($query, '?'), count($bindVars)));
        }
        return true;
    }

    /**
     * Метод для запросов SELECT-подобных
     *
     * @return array|string|bool
     *      array: select, selectCol, selectRow
     *      string: selectCell
     *      bool: false при ошибке
     */
    protected function s_query()
    {
        $data = null;
        $arguments = func_get_args();
        $this->query($arguments);
        if (!$this->stmp) {
            return false;
        }

        $execute = $this->stmp->execute();
        if (!$execute) {
            return false;
        }

        $result = $this->_bindResult($data);
        if (!$result) {
            return false;
        }

        $returnPrepareMethod = $this->returnMethod;
        $rows = $this->$returnPrepareMethod($data);
        $this->setQueryInfo();

        return $rows;
    }

    /**
     * Метод для запросов INSERT/DELETE-подобных
     *
     * @return bool|MySQLi_STMT
     */
    protected function i_query()
    {
        $arguments = func_get_args();
        $this->query($arguments);
        if (!$this->stmp) {
            return false;
        }

        $execute = $this->stmp->execute();
        if (!$execute) {
            return false;
        }
        $this->setQueryInfo();

        return true;
    }

    /**
     * Сохранение дополнительной информации
     *
     * @return void
     */
    protected function setQueryInfo()
    {
        $info = array('affected_rows' => $this->stmp->affected_rows, 'insert_id' => $this->stmp->insert_id,
                      'num_rows'      => $this->stmp->num_rows, 'field_count' => $this->stmp->field_count,
                      'sqlstate'      => $this->stmp->sqlstate,);
        $this->queryInfo = $info;
    }

    /**
     * Нормализует переданные аргументы
     *
     * @param  $arguments
     *
     * @return void
     */
    protected function prepareQuery(&$arguments)
    {
        $sprintfArg = array();

        // замена префикса
        $this->_replacePrefix($arguments[0]);

        // Строка запроса
        $sprintfArg[] = $arguments[0];
        foreach ($arguments as $pos => $var) {
            if (is_array($var)) {
                $insertAfterPosition = $pos;
                $replaceWith = array();
                unset($arguments[$pos]);
                foreach ($var as $arrayVar) {
                    array_splice($arguments, $insertAfterPosition, 0, $arrayVar);
                    $insertAfterPosition++;
                    $replaceWith[] = '?';
                }
                $sprintfArg[] = implode(',', $replaceWith);
            }
        }
        $arguments[0] = call_user_func_array('sprintf', $sprintfArg);
    }

    /**
     * Определяет тип данных (int,float or string)
     *
     * @param  string $variable - значение
     *
     * @return string - тип
     */
    protected function getTypeByVal($variable)
    {
        switch (gettype($variable)) {
            case 'integer':
                $type = 'i';
                break;
            case 'double':
                $type = 'd';
                break;
            default:
                $type = 's';
        }
        return $type;
    }

    /***************************************************************************
     * Public methods
    /***************************************************************************/

    /**
     * Возвращает полный результат запроса
     *
     * @param  string $query - строка запроса
     *
     * @return array|bool - двухмерный массив с результатом запроса
     */
    public function select($query)
    {
        $this->returnMethod = '_mysqliFetchAssoc';
        $arguments = func_get_args();

        return call_user_func_array(array($this, 's_query'), $arguments);
    }

    /**
     * Возвращает первый столбец запроса
     *
     * @param string $query - строка запроса
     *
     * @return array|bool - возвращаемые значения
     */
    public function selectCol($query)
    {
        $this->returnMethod = '_mysqliFetchCol';
        $arguments = func_get_args();

        return call_user_func_array(array($this, 's_query'), $arguments);
    }

    /**
     * Возвращает первую ячейку первой строки запроса
     *
     * @param string $query - строка запроса
     *
     * @return string - возвращаемые значения
     */
    public function selectCell($query)
    {
        $this->returnMethod = '_mysqliFetchCell';
        $arguments = func_get_args();

        return call_user_func_array(array($this, 's_query'), $arguments);
    }

    /**
     * Возвращает первую строку запроса
     *
     * @param string $query - строка запроса
     *
     * @return array|bool   - возвращаемые значения
     */
    public function selectRow($query)
    {
        $this->returnMethod = '_mysqliFetchRow';
        $arguments = func_get_args();

        return call_user_func_array(array($this, 's_query'), $arguments);
    }

    /**
     * Возвращает все значения из таблицы
     *
     * @param null|string $table - имя таблицы
     * @param null|string $field - поле для фильтра
     * @param null|mixed  $value - значение фильтра
     *
     * @return array|bool - двухмерный массив значений, false - при ошибке
     */
    public function selectAll($table = null, $field = null, $value = null)
    {
        if ($this->_checkTable($table)) {
            if (!is_null($field)) {
                return $this->select("SELECT * FROM `" . $table . "` WHERE `" . $field . "` = ?", $value);
            } else {
                return $this->select("SELECT * FROM `" . $table . "`");
            }
        } else {
            return false;
        }
    }

    /**
     * Выполнение UPDATE
     *
     * @param  string $query - строка запроса
     *
     * @return bool
     */
    public function update($query)
    {
        $arguments = func_get_args();

        return call_user_func_array(array($this, 'i_query'), $arguments);
    }

    /**
     * Выполнение INSERT
     *
     * @param  string $query - строка запроса
     *
     * @return bool
     */
    public function insert($query)
    {
        $arguments = func_get_args();

        return call_user_func_array(array($this, 'i_query'), $arguments);
    }

    /**
     * Выполнение REPLACE
     *
     * @param  string $query - строка запроса
     *
     * @return bool
     */
    public function replace($query)
    {
        $arguments = func_get_args();

        return call_user_func_array(array($this, 'i_query'), $arguments);
    }

    /**
     * Выполнение DELETE
     *
     * @param  string $query - строка запроса
     *
     * @return bool
     */
    public function delete($query)
    {
        $arguments = func_get_args();

        return call_user_func_array(array($this, 'i_query'), $arguments);
    }

    /**
     * Получить результат кэширования SQL-запроса
     *
     * @return mixed - данные
     * @throws Exception
     *
     * @example
     *      string $params[0]       - метод класса БД
     *      string $params[1]   - строка SQL-запроса
     *      string|array  $params[2]..[n]  - данные
     */
    public function getCacheSql()
    {
        // Получаем аргументы переданные в функцию
        $params = func_get_args();

        // метод класса БД
        $method = array_shift($params);

        // строка SQL-запроса
        $sql = array_shift($params);

        // путь до кэша
        $path = $this->_db_cache_dir . '/sql_cache/' . md5($method . $sql . serialize($params)) . '.cache';

        // Создаём каталог если его ещё нет
        if (!is_dir($this->_db_cache_dir . '/sql_cache')) {
            mkdir($this->_db_cache_dir . '/sql_cache', 0777);
        }

        if (method_exists(__CLASS__, $method)) {
            if (is_readable($path) and $this->_db_caching) {
                $stat = stat($path);
                if (($stat['mtime'] + $this->_db_cache_time) > time()) {

                    // Если время ещё не истекло, получаем из файла
                    return unserialize(file_get_contents($path));
                } else {

                    // Если время истекло, делаем запрос в БД
                    $rows = $this->$method($sql, $params);
                    file_put_contents($path, serialize($rows), LOCK_EX);
                    return $rows;
                }
            } else {

                // Если файла кэша не существует, делаем запрос в БД
                $rows = $this->$method($sql, $params);
                file_put_contents($path, serialize($rows), LOCK_EX);
                return $rows;
            }
        } else {
            // Если нет метода
            throw new Exception(sprintf(_EXCEP_UNDEFINED_PROPERTY_CLASS, $method, 'GDLDatabase'));
        }
    }

    /**
     * Старт транзакции
     * @return void
     * @example:
     *      $db=GDLDatabase::Init();
     *      $db->transactionStart();
     *      $db->insert('INSERT INTO `test` set testval=?',20);
     *      $db->insert('INSERT INTO `test` set testval=?',10);
     *      $db->insert('INSERT INTO `test` set testval=?',123);
     *      $db->transactionCommit();
     */
    public function transactionStart()
    {
        $this->db_resource->autocommit(false);
    }

    /**
     * Остановка транзацкии
     *
     * @return void
     */
    public function transactionCommit()
    {
        // фиксирует текущую транзакцию
        $this->db_resource->commit();

        // Включает автоматическую фиксацию изменений базы данных
        $this->db_resource->autocommit(true);
    }

    /**
     * Откат текущей транзакции
     *
     * @return void
     * @example:
     *      $db=GDLDatabase::Init();
     *      $db->transactionStart();
     *      $db->insert('INSERT INTO `test` set testval=?',20);
     *      $db->insert('INSERT INTO `test` set testval=?',10);
     *      $db->insert('INSERT INTO `test` set testval=?',123);
     *      $db->transactionRollBack();
     *      $db->transactionCommit();
     */
    public function transactionRollBack()
    {
        $this->db_resource->rollback();
    }

    /**
     * @param string $query - Простое выполнение запроса
     *
     * @return bool|mysqli_result
     */
    public function simpleQuery($query = '')
    {
        $this->_replacePrefix($query);
        return $this->db_resource->query($query);
    }

    /**
     * Очистка таблицы (TRUNCATE)
     *
     * @param null $table - имя таблицы
     *
     * @return bool|mysqli_result - результат
     */
    public function clearTable($table = null)
    {
        if ($this->_checkTable($table)) {
            $sql = 'TRUNCATE ' . $table;
            $this->_replacePrefix($sql);
            return $this->db_resource->query($sql);
        } else {
            return false;
        }
    }

    /**
     * Получение Дополнительной информации
     *
     * @param null $key - eckb указан ключ, то возвращается это значение
     *
     * @return array - ассоциациативный массив
     *              [affected_rows] - Количество строк, затронутых последним запросом
     *              [insert_id]     - ID, сгенерированный предыдущей операцией INSERT,
     *              [num_rows]      - Число строк в результате запроса
     *              [field_count]   - Число полей в заданном выражении
     *              [sqlstate]      - SQLSTATE ошибка последнего запроса
     *         string - возвращает конкретное значение если задан $key
     */
    public function getQueryInfo($key = null)
    {
        switch ($key) {
            case 'affected_rows':
                return $this->queryInfo['affected_rows'];
                break;
            case 'insert_id':
                return $this->queryInfo['insert_id'];
                break;
            case 'num_rows':
                return $this->queryInfo['num_rows'];
                break;
            case 'field_count':
                return $this->queryInfo['field_count'];
                break;
            case 'sqlstate':
                return $this->queryInfo['sqlstate'];
                break;
            default:
                return $this->queryInfo;
        }
    }

    /**
     * Показать профилирование запросов
     * @return mixed
     */
    public function showProfiles()
    {
        $result = $this->db_resource->query("show profiles");
        return $result->fetch_all();
    }

    /**
     * Возвращает ошибку
     *
     * @return string
     */
    public function getError()
    {
        return $this->db_resource->error;
    }

    /**
     * Возвращает номер ошибки запроса
     *
     * @return int
     */
    public function getErrno()
    {
        return $this->db_resource->errno;
    }

    /**
     * Возвращает обьект класса mysqli для нереализованных операций
     *
     * @return mysqli
     */
    public function getObject()
    {
        return $this->db_resource;
    }

    public function __destruct()
    {
        $this->db_resource->close();
    }
}