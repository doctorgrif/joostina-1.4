<?php
/**
 * @package   Joostina Lotos
 * @copyright Авторские права (C) 2011-2012 Joostina Lotos. Все права защищены.
 * @license   Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 *            Joostina Lotos! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 * @autor     Gold Dragon (http://gd.joostina-cms.ru)
 */

// запрет прямого доступа
defined('_JLINDEX') or die();

/**
 * Основной класс для обработки SEF
 */
class JSef
{

    /** разделитель параметр-значение */
    const separator = '-';

    /** @var object библиотека JLIni */
    private static $_lib_ini;

    /** @var object библиотека JLUrl */
    private static $_lib_url;

    /** @var object библиотека JLText */
    private static $_lib_text;

    /** @var array Хранит запросы к базе */
    private static $_sef_url = array();

    /** @var int разрешён ли SEF */
    public static $cfg_sef;

    /** @var int очищать ссылку на главную */
    public static $cfg_frontpage;

    /** @var array массив sef-файлов компонентов */
    public static $sef_files;

    /** @var string - ссылка на страницу (REQUEST_URI) */
    public static $url;

    /**
     * @static Подключение класса
     *
     * @param int $cfg_sef
     * @param int $cfg_frontpage
     *
     * @return object JSef
     */
    public static function getInstance($cfg_sef = 0, $cfg_frontpage = 0)
    {
        // Подключаем необходимые библиотеки
        JCore::getLib('url');
        self::$_lib_url = JLUrl::getInstance();

        // запоминаем адрес страницы
        if (!defined('IS_ADMIN')) {
            self::$url = self::getLinkNormalization(_JLPATH_SITE . $_SERVER['REQUEST_URI']);
        }

        //используется ли SEF
        if ($cfg_sef and !defined('IS_ADMIN') and !substr_count($_SERVER['REQUEST_URI'], 'index2.php') and !isset($_REQUEST['tp'])) {
            // запоминаем настройки
            self::$cfg_sef = (int)$cfg_sef;
            self::$cfg_frontpage = (int)$cfg_frontpage;

            JCore::getLib('ini');
            self::$_lib_ini = JLIni::getInstance();

            JCore::getLib('text');
            self::$_lib_text = JLText::getInstance();

            // список sef-файлов
            $sef_com = scandir(_JLPATH_SEF . DS);
            foreach ($sef_com as $value) {
                if (preg_match('#\.sef\.ini$#i', $value, $tmp)) {
                    self::$sef_files[] = $value;
                }
            }

            // Существует ли сторонний обработчик, то подключаем его, если нет, то подключаем стандартный обработчик
            if (file_exists(_JLPATH_ROOT . DS . 'components' . DS . 'com_sef' . DS . 'sef.php')) {
                require_once(_JLPATH_ROOT . DS . 'components' . DS . 'com_sef' . DS . 'sef.php');
            } else {
                // перебрасываем на корректный адрес
                if (ltrim(strpos($_SERVER['REQUEST_URI'], 'index.php'), '/') == 1 AND $_SERVER['REQUEST_METHOD'] == 'GET') {
                    //Преобразование URL`а
                    $link = self::getUrlToSef('index.php?' . $_SERVER['QUERY_STRING']);
                    // Переадресация на SEF-адрес
                    header("Location: " . $link, TRUE, 301);
                    exit(301);
                } else {
                    self::getSefToUrl();
                }
            }
        }
    }

    private static function checkINI($option_ini)
    {
        $option_ini = $option_ini . '.sef.ini';
        // проверяем есть ли отработчик для компонента и включен ли он
        if (in_array($option_ini, self::$sef_files)) {
            // подключаем библиотеку для обработки INI-файлов
            self::$_lib_ini->parse(_JLPATH_SEF . DS . $option_ini);
            $result = (int)self::$_lib_ini->checkKey('sef');
        } else { // Стандартный обработчик
            $result = null;
        }
        return $result;
    }

    /**
     * @static Получение имени компонета
     *
     * @param string $link - нормальная ссылка
     *
     * @return string - имя компонента
     */
    public static function getOption($link = null)
    {
        if (is_null($link)) {
            $option = (isset($_REQUEST['option'])) ? trim(strval($_REQUEST['option'])) : '';
            if ($option == '') {
                $link = $_SERVER['REQUEST_URI'];
                $link = explode("/", preg_replace('#(^\/)|(\/$)#', '', $link));
                $option = (isset($link[0])) ? $link[0] : '';
            }
        } else {
            self::$_lib_url->parse($link);
            $option = self::$_lib_url->getVar('option', '');
        }
        return $option;
    }

    /**
     * @static Получение задачи компонета
     *
     * @param null|string $link - ссылка
     *
     * @return string
     */
    public static function getTask($link = null)
    {
        if (is_null($link)) {
            $task = (isset($_REQUEST['task'])) ? trim(strval($_REQUEST['task'])) : '';
            if ($task == '') {
                $link = $_SERVER['REQUEST_URI'];
                $link = explode("/", preg_replace('#(^\/)|(\/$)#', '', $link));
                $task = (isset($link[0])) ? $link[0] : '';
            }
        } else {
            self::$_lib_url->parse($link);
            $task = self::$_lib_url->getVar('task', '');
        }
        return $task;
    }

    /**
     * @static преобразует нормальную ссылку в Sef-ссылку
     *
     * @param $link - нормальная ссылка
     *
     * @return mixed - sef-ссылка
     */
    public static function getUrlToSef($link)
    {
        // Нормализуем ссылку
        $link = self::getLinkNormalization($link);

        if (self::$cfg_sef and !defined('IS_ADMIN') and strstr($link, 'index.php')) {

            // Парсим URL
            self::$_lib_url->parse($link);

            // Проверяем ссылку на внешний ресурс
            $site_url = parse_url(_JLPATH_SITE);
            $host = (is_null(self::$_lib_url->getHost()) or $site_url['host'] == self::$_lib_url->getHost()) ? true : false;

            // Игнорируем некоторые ссылки

            if (self::$_lib_url->_path != 'ajax.index.php' and self::$_lib_url->_path != '/index2.php' and $host) {

                // хэш ссылки
                $link_hash = md5($link);

                // Проверяем существует ли уже sef-ссылка
                if (array_key_exists($link_hash, self::$_sef_url)) {

                    $link_sef = self::$_sef_url[$link_hash];

                } else {

                    // Подключаем класс БД
                    $_db = JCore::getDB();

                    // Проверяем есть ли такой URL в базе, если есть возвращаем SEF
                    $link_sef = $_db->selectCell('SELECT sef FROM `#__sef_link` WHERE url = ?', trim(str_replace(_JLPATH_SITE, '', $link)));

                    // Если в базе нет
                    if (!$link_sef) {

                        if (self::$_lib_url->_path == 'index.php') {
                            self::$_lib_url->_path = '';
                        }

                        // Проверяем, существует ли sef-файл и включен ли он
                        if (self::checkINI(self::getOption($link))) {

                            // получаем массив ключ-значение из URL
                            $url_vars = self::$_lib_url->_vars;

                            // получаем имя компонента
                            //-------------------------------------------------------------
                            $option_show = (int)self::$_lib_ini->getValue('option_show');
                            $option_name = self::$_lib_ini->getValue('option_name');
                            $option_sql = self::$_lib_ini->getValue('option_sql');

                            if ($option_show == 1 and $option_name != '') {

                                //Получаем значение из SEF-файла
                                $option = $option_name;

                            } elseif ($option_show == 2 and $option_sql != '') {

                                // получаем значение из SQL-запроса
                                $option = self::getOptionSql($option_sql, self::getOption($link));

                            } else {

                                // Получаем значение из URL
                                $option = self::getOption($link);
                            }
                            // Транслитерация
                            $option = '/' . self::$_lib_text->text_to_url($option);

                            // Удаляем из параметров option
                            if (isset($url_vars['option'])) {
                                unset($url_vars['option']);
                            }

                            // Получаем task
                            //------------------------------------------------------
                            $task_prm_arr = self::$_lib_ini->getValue('task_prm');
                            $task_val_arr = self::$_lib_ini->getValue('task_val');
                            $task_sql_arr = self::$_lib_ini->getValue('task_sql');

                            $tmp_key = array_search(self::$_lib_url->getVar('task'), $task_prm_arr);
                            if ($tmp_key !== false) {
                                if (isset($task_val_arr[$tmp_key]) and $task_val_arr[$tmp_key] != '') {

                                    // Получаем значение из task_val
                                    $task = $task_val_arr[$tmp_key];

                                } elseif (isset($task_sql_arr[$tmp_key]) and $task_sql_arr[$tmp_key] != '') {

                                    // Получаем значение их task_sql
                                    $task = self::getOptionSql($task_sql_arr[$tmp_key], $task_prm_arr[$tmp_key]);

                                } else {

                                    // Получаем значение из task_prm
                                    $task = $task_prm_arr[$tmp_key];
                                }
                            }

                            // Транслитерация
                            $task = (isset($task)) ? '/' . self::$_lib_text->text_to_url($task) : '';

                            // Удаляем из зараметров task
                            if (isset($url_vars['task'])) {
                                unset($url_vars['task']);
                            }

                            // Получаем params
                            //-------------------------------------------------------------
                            $params = self::$_lib_ini->getValue('param');

                            // Сортировка порядка отображения параметров
                            $orders = self::$_lib_ini->getValue('order');
                            $tmp = array();

                            foreach ($orders as $order) {
                                if (array_key_exists($order, $url_vars)) {
                                    $tmp[$order] = $url_vars[$order];
                                    unset($url_vars[$order]);
                                }
                            }
                            $url_vars = array_merge($tmp, $url_vars);

                            // Добавляем параметры
                            $param = '';
                            foreach ($url_vars as $key => $var) {
                                if (array_search($key, $params) === false) {
                                    $param .= '/' . self::$_lib_text->text_to_url($var);
                                }
                            }

                            // Добавляем окончание
                            $tmp_key = array_search(self::$_lib_url->getVar('task'), self::$_lib_ini->getValue('task_html'));
                            $param .= ($tmp_key === false) ? '/' : '.html';

                            // Добавляем "Якорь"
                            $fragment = (self::$_lib_url->_fragment) ? '#' . self::$_lib_text->text_to_url(self::$_lib_url->_fragment) : '';

                            // Формируем sef-ссылку
                            $link_sef = $option . $task . $param . $fragment;
                        } else {

                            // если ссылка идёт на компонент главной страницы - очистим её
                            if ((JSef::$cfg_frontpage AND stripos($link, 'option=com_frontpage') > 0 AND !(stripos($link, 'limit'))) OR $link == 'index.php' OR $link == 'index.php?') {

                                $link_sef = '/';

                            } else {

                                // получаем часть fragment (после знака диеза #)
                                $fragment = self::$_lib_url->getFragment();

                                // Транслитерация Fragment
                                $fragment = self::$_lib_text->text_to_url($fragment);

                                $link_sef = self::$_lib_url->_path;

                                // Удаляем index.php из пути
                                $link_sef = str_replace('index.php', '', $link_sef);

                                // проверяем часть query после знака вопроса ?
                                if (self::$_lib_url->_query) {

                                    // специальная обработка для javascript
                                    self::$_lib_url->_query = stripslashes(str_replace('+', '%2b', self::$_lib_url->_query));

                                    // очистить возможные атаки XSS
                                    self::$_lib_url->_query = preg_replace("'%3Cscript[^%3E]*%3E.*?%3C/script%3E'si", '', self::$_lib_url->_query);

                                    $_query = array();
                                    foreach (self::$_lib_url->_vars as $key => $value) {
                                        $_query[] = $key . self::separator . self::$_lib_text->text_to_url($value);
                                    }
                                    $link_sef .= '/' . implode('/', $_query);
                                }

                                // Формируем ссылку
                                $link_sef = ($fragment) ? $link_sef . '#' . $fragment : $link_sef . '/';
                            }
                        }

                        // Удаляем возможные повторы слеша
                        $link_sef = str_replace('//', '/', $link_sef);

                        // записываем sef-ссылку во внутреннее хранилище
                        self::$_sef_url[$link_hash] = $link_sef;

                        // Если нет дубликата, то добавляем. Если есть, то записываем в таблицу дубликатов
                        self::saveSef($link, $link_sef);
                    }
                }
                // формируем окончательно ссылку
                $link = _JLPATH_SITE . $link_sef;
            }
        }

        return $link;
    }


    /**
     * Нормализует ссылку, т.е. все параметры упорядочиваются чтобы исключить дублирование
     *
     * @param $link - ссылка
     *
     * @return string
     *
     * @modification 07.08.2013
     */
    private static function getLinkNormalization($link)
    {
        $result = array();

        // проверяем внешнюю ссылку
        self::$_lib_url->parse(_JLPATH_SITE);
        $_host_site = self::$_lib_url->_host;
        self::$_lib_url->parse($link);

        if (is_null(self::$_lib_url->_host) or $_host_site == self::$_lib_url->_host) {
            if (self::$_lib_url->_scheme) {
                $result[] = self::$_lib_url->_scheme . '://';
            }

            if (self::$_lib_url->_host) {
                $result[] = self::$_lib_url->_host;
            }

            // Формируем абсолютную ссылку
            if (!sizeof($result)) {
                $result[] = _JLPATH_SITE . '/';
            }

            if (self::$_lib_url->_path) {
                $result[] = self::$_lib_url->_path;
            }

            $fragment = self::$_lib_url->getFragment();

            if (count(self::$_lib_url->_vars)) {

                $vars_a = array();

                if (self::$_lib_url->checkVar('option')) {
                    $vars_a[] = 'option=' . self::$_lib_url->getVar('option');
                    self::$_lib_url->delVar('option');
                }

                if (self::$_lib_url->checkVar('task')) {
                    $vars_a[] = 'task=' . self::$_lib_url->getVar('task');
                    self::$_lib_url->delVar('task');
                }

                if (count(self::$_lib_url->_vars)) {
                    $array = self::$_lib_url->_vars;
                    ksort($array);
                    foreach ($array as $key => $value) {
                        $vars_a[] = $key . '=' . $value;
                    }
                }
                $query = implode('&amp;', $vars_a);

                $result[] = '?' . $query;
            }

            $link = implode($result);
            if ($fragment) {
                $link = '#' . $fragment;
            }

            // Очищаем ссылку главной страницы
            if (JCore::getCfg('com_frontpage_clear')
                and ($link == _JLPATH_SITE . '/index.php?option=com_frontpage'
                    or (!sizeof(self::$_lib_url->_vars)
                        and empty(self::$_lib_url->_path)))
            ) {
                $link = _JLPATH_SITE . '/';
            }
        }
        return $link;
    }

    /**
     * Если нет дубликата, то добавляем в базу SEF_LINK. Если есть, то записываем в таблицу дубликатов
     *
     * @param $link     - url-ссылка
     * @param $link_sef - sef-ссылка
     */
    private static function saveSef($link, $link_sef)
    {
        // Облегчаем базу, удалив домен из URL
        $link = trim(str_replace(_JLPATH_SITE, '', $link));
        $link_sef = trim(str_replace(_JLPATH_SITE, '', $link_sef));

        if ($link != '' and $link != '/' and $link_sef and $link_sef != '/') {
            $_db = JCore::getDB();

            // проверяем существует ли дубликат
            $url = $_db->selectCell("SELECT url FROM `#__sef_link` WHERE sef = ?", $link_sef);

            if (is_null($url)) {
                $sql = 'INSERT INTO `#__sef_link` (url, sef) VALUES (?, ?);';
                $_db->insert($sql, $link, $link_sef);
            } else {
                $url = $_db->selectCell("SELECT url FROM `#__sef_duplicate` WHERE url = ? AND sef = ?", $link, $link_sef);
                if (is_null($url)) {
                    $sql = 'INSERT INTO `#__sef_duplicate` (url, sef) VALUES (?, ?)';
                    $_db->insert($sql, $link, $link_sef);
                }
            }
        }
    }

    /**
     * Возвращает значение из базы
     *
     * @param $sql     - запрос из sef-файла
     *
     * @param $default - значение по умолчанию
     *
     * @return string
     */
    private static function getOptionSql($sql, $default)
    {
        $_db = JCore::getDB();

        // массив для значений
        $sql_value = array();

        // Поизводим замену значений
        $b = preg_match_all("#\[\[([a-z0-9-_]*)\]\]#i", $sql, $sql_arr);
        if ($b) {
            // Получаем значения
            foreach ($sql_arr[1] as $value) {
                $sql_value[] = self::$_lib_url->getVar($value);
            }
            // производим замену
            $sql = str_replace($sql_arr[0], '?', $sql);
        }

        // Поизводим замену имён полей
        $b = preg_match_all("#\[([a-z0-9-_]*)\]#i", $sql, $sql_arr);
        if ($b) {
            // массив для имён полей
            $sql_name = array();
            // Получаем значения
            foreach ($sql_arr[1] as $value) {
                $sql_name[] = self::$_lib_url->getVar($value);
            }
            // производим замену
            $sql = str_replace($sql_arr[0], $sql_name, $sql);
        }
        // получаем значение через кэш
        $option = $_db->getCacheSql('selectCell', $sql, $sql_value);
        if (!$option) {
            $option = $default;
        }
        return $option;
    }

    /**
     * @static Загрузка параметров из SEF-url в глобальные
     * @return mixed
     */
    public static function getSefToUrl()
    {
        $_db = JCore::getDB();

        // получаем URL
        $link = $_SERVER['REQUEST_URI'];

        // Проверяем существует ли обратная ссылка в базе
        $url = $_db->selectCell('SELECT url FROM `#__sef_link` WHERE sef = ?', $link);

        // Если адрес найден, то готовим глобальные. Иначе пытаемся сформировать
        if (!is_null($url)) {
            self::$_lib_url->parse($url);
            $url_vars = self::$_lib_url->_vars;
            foreach ($url_vars as $key => $value) {
                $_GET[$key] = $value;
                $_REQUEST[$key] = $value;
            }
        } else {
            // получаем массив с параметрами
            $url = explode("/", $link);

            // присваиваем значения глобальным переменным
            $option = false;

            foreach ($url as $value) {
                $value = explode(self::separator, $value, "2");
                $val1 = (isset($value[0])) ? $value[0] : false;

                // присваиваем если есть ключ
                if ($val1) {
                    $val2 = (isset($value[1])) ? $value[1] : '';
                    $_GET[$val1] = $val2;
                    $_REQUEST[$val1] = $val2;
                }
            }
        }
    }
}