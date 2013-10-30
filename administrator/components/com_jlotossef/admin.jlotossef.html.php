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

class JLotosSefHtml
{
    /**
     * Редактирование дубликата
     *
     * @param array  $row_dup : данные дубликата
     * @param array  $row_ref : данные ссылки
     * @param array  $lang    : языковые константы
     * @param string $error   : сообщение об ошибке
     */
    public static function editDup($row_dup, $row_ref, $lang, $error = '')
    {
        $error = (empty($error)) ? '' : '<div class="jwarning">' . $error . '</div>';
        ?>
        <?php echo $error; ?>
        <form action="index2.php?option=com_jlotossef" method="post" name="adminForm">
            <table class="adminheading">
                <tr>
                    <th class="categories"><?php echo $lang['JLSEF_CFG_DUP_EDIT']; ?></th>
                </tr>
            </table>

            <h3><?php echo $lang['JLSEF_CFG_LINK_REF']; ?></h3>
            <table class="adminform">
                <tr class="row0">
                    <td width="100px"><?php echo $lang['JLSEF_CFG_LINK_URL']; ?>:</td>
                    <td><div style="padding: 5px 0;"><?php echo $row_ref['url']; ?></div></td>
                </tr>
                <tr class="row1">
                    <td><?php echo $lang['JLSEF_CFG_LINK_SEF']; ?>:</td>
                    <td><div style="padding: 5px 0;"><?php echo $row_ref['sef']; ?></div></td>
                </tr>
            </table>

            <h3><?php echo $lang['JLSEF_CFG_LINK_DUP']; ?></h3>
            <table class="adminform">
                <tr class="row0">
                    <td width="100px"><?php echo $lang['JLSEF_CFG_LINK_URL']; ?>:</td>
                    <td><div style="padding: 5px 0;"><?php echo $row_dup['url']; ?></div></td>
                </tr>
                <tr class="row1">
                    <td><?php echo $lang['JLSEF_CFG_LINK_SEF']; ?>:</td>
                    <td>
                        <input class="inputbox" type="text" name="dup_sef" value="<?php echo $row_dup['sef']; ?>" size="100" maxlength="250"/>
                        <span id="sef_img_err"></span>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="ref_url" value="<?php echo $row_ref['url']; ?>"/>
            <input type="hidden" name="ref_sef" value="<?php echo $row_ref['sef']; ?>"/>
            <input type="hidden" name="dup_url" value="<?php echo $row_dup['url']; ?>"/>
            <input type="hidden" name="id" value="<?php echo $row_dup['id']; ?>"/>
            <input type="hidden" name="task" value=""/>
        </form>
        <script>
            $(function () {
                $("input[name=dup_sef]").keyup(function () {
                    $a = $(this).val();

                    $.post('ajax.index.php?option=com_jlotossef',
                        {
                            task: "check_sef_dup",
                            sef: $a
                        },
                        function (data) {
                            $("#sef_img_err").html(data);
                        }
                    );
                    return false;
                });
            });
        </script>
    <?php
    }

    /**
     * Вывод списка Дубликатов
     *
     * @param array  $rows    : данные по ссылкам (id, url, sef)
     * @param object $pageNav : навигация
     */
    public static function pageDup($rows, $pageNav)
    {

        $lang = JLotosSefClass::getLang();
        ?>
        <form action="index2.php?option=com_jlotossef" method="POST" name="adminForm">
            <table class="adminheading">
                <tr>
                    <th class="categories"><?php echo $lang['JLSEF_REF']; ?></th>
                </tr>
            </table>
            <table class="adminlist">
                <tr>
                    <th>ID</th>
                    <th></th>
                    <th>URL</th>
                    <th>SEF</th>
                </tr>
                <?php
                $k = 0;
                foreach ($rows as $row) {
                    ?>
                    <tr class="<?php echo "row" . $k; ?>">
                        <td><?php echo $row['id']; ?></td>
                        <td>
                            <?php
                            echo '<input type="checkbox" id="cb' . $row['id'] . '" name="cid[]" value="' . $row['id'] . '" onClick="isChecked(this.checked);" />';
                            ?>
                        </td>
                        <td><a href="" onClick="return listItemTask('cb<?php echo $row['id']; ?>','editdup')"><?php echo $row['url']; ?></a></td>
                        <td><?php echo $row['sef']; ?></td>
                    </tr>
                    <?php
                    $k = 1 - $k;
                }
                ?>
            </table>
            <?php echo $pageNav->getListFooter(); ?>
            <input type="hidden" name="task" value="listdup"/>
            <input type="hidden" name="boxchecked" value="0"/>
        </form>
    <?php
    }

    /**
     * Редактирование ссылки
     *
     * @param array  $row   : данные ссылки
     * @param array  $lang  : языковые константы
     * @param string $error : сообщение об ошибке
     */
    public static function editRef($row, $lang, $error = '')
    {
        $error = (empty($error)) ? '' : '<div class="jwarning">' . $error . '</div>';
        ?>
        <form action="index2.php?option=com_jlotossef" method="post" name="adminForm">
            <table class="adminheading">
                <tr>
                    <th class="categories"><?php echo $lang['JLSEF_CFG_LINK_EDIT']; ?></th>
                </tr>
            </table>
            <?php echo $error; ?>
            <table class="adminform">
                <tr class="row0">
                    <td><?php echo $lang['JLSEF_CFG_LINK_URL']; ?>:</td>
                    <td>
                        <div style="padding: 5px 0;"><?php echo $row['url']; ?></div>
                    </td>
                </tr>
                <tr class="row1">
                    <td><?php echo $lang['JLSEF_CFG_LINK_SEF']; ?>:</td>
                    <td><input class="inputbox" type="text" name="sef" value="<?php echo $row['sef']; ?>" size="100" maxlength="250"/></td>
                </tr>
                <input type="hidden" name="url" value="<?php echo $row['url']; ?>"/>
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>"/>
                <input type="hidden" name="task" value=""/>
            </table>
        </form>
    <?php
    }

    /**
     * Вывод списка ссылок
     *
     * @param array  $rows    : данные по ссылкам (id, url, sef)
     * @param object $pageNav : навигация
     */
    public static function pageRef($rows, $pageNav)
    {

        $lang = JLotosSefClass::getLang();
        ?>
        <form action="index2.php?option=com_jlotossef" method="POST" name="adminForm">
            <table class="adminheading">
                <tr>
                    <th class="categories"><?php echo $lang['JLSEF_REF']; ?></th>
                </tr>
            </table>
            <table class="adminlist">
                <tr>
                    <th>ID</th>
                    <th></th>
                    <th>URL</th>
                    <th>SEF</th>
                </tr>
                <?php
                $k = 0;
                foreach ($rows as $row) {
                    ?>
                    <tr class="<?php echo "row" . $k; ?>">
                        <td><?php echo $row['id']; ?></td>
                        <td>
                            <?php
                            echo '<input type="checkbox" id="cb' . $row['id'] . '" name="cid[]" value="' . $row['id'] . '" onClick="isChecked(this.checked);" />';
                            ?>
                        </td>
                        <td><a href="" onClick="return listItemTask('cb<?php echo $row['id']; ?>','editref')"><?php echo $row['url']; ?></a></td>
                        <td><?php echo $row['sef']; ?></td>
                    </tr>
                    <?php
                    $k = 1 - $k;
                }
                ?>
            </table>
            <?php echo $pageNav->getListFooter(); ?>
            <input type="hidden" name="task" value="listref"/>
            <input type="hidden" name="boxchecked" value="0"/>
        </form>
    <?php
    }

    /**
     * Вывод страницы настроек
     *
     * @param array $data : данные настроек
     */
    public static function pageConfiguration($data)
    {
        $lang = JLotosSefClass::getLang();
        mosCommonHTML::loadOverlib();
        ?>
        <table class="adminheading" border="0">
            <tr>
                <th class="config"><?php echo $lang['JLSEF_CFG']; ?></th>
            </tr>
        </table>

        <form action="index2.php?option=com_jlotossef" method="post" name="adminForm">
            <table class="adminform">
                <tr>
                    <th style="width: 50%"><?php echo $lang['JLSEF_CFG_TBL1']; ?></th>
                    <th><?php echo $lang['JLSEF_CFG_TBL2']; ?></th>
                </tr>
                <tr class="row0">
                    <td><?php echo $lang['JLSEF_CFG_PACK_REF'];
                        echo mosToolTip($lang['JLSEF_CFG_PACK_DES']); ?></td>
                    <td><?php echo $data['pack_ref']; ?></td>
                </tr>
                <tr class="row1">
                    <td><?php echo $lang['JLSEF_CFG_PACK_DUP'];
                        echo mosToolTip($lang['JLSEF_CFG_PACK_DES']); ?></td>
                    <td><?php echo $data['pack_dup']; ?></td>
                </tr>
                <tr class="row0">
                    <td><?php echo $lang['JLSEF_CFG_PACK_PATH']; ?></td>
                    <td><?php echo $data['pack_path']; ?></td>
                </tr>
                <tr class="row1">
                    <td><?php echo $lang['JLSEF_CFG_PACK_PREF']; ?></td>
                    <td><?php echo $data['pack_pref']; ?></td>
                </tr>
                <tr class="row0">
                    <td><?php echo $lang['JLSEF_CFG_PACK_FORMAT']; ?></td>
                    <td><?php echo $data['pack_format']; ?></td>
                </tr>
                <input type="hidden" name="task" value="save"/>
            </table>
        </form>

    <?php
    }

    /**
     * Страница описания sef-файлов
     */
    public static function pageDescription()
    {
        // подключаем языковой файл
        $_lang = JLotosSefClass::getLang();

        ?>
        <table class="adminheading" border="0">
            <tr>
                <th class="info"><?php echo $_lang['JLSEF_DES']; ?></th>
            </tr>
        </table>

        <br><br>

        <h3>Название и расположение SEF-файла</h3>
        <ul>
            <li><p>SEF-файлы физически располагаются по адресу:
                    <br><code>корень_сайта/settings/sef/</code></p></li>
            <li><p>Название SEF-файла имеет следующую структуру:
                    <br><code><b>XX.sef.ini</b>, где <b>XX</b> - имя компонента, например <b>com_boss.sef.ini</b></code>
        </ul>

        <h3>Разделы SEF-файла</h3>
        <ul>
            <li><p><b>[main]</b> - Основные настройки</p></li>
            <li><p><b>[option_cfg]</b> - Настройка option</p></li>
            <li><p><b>[task_cfg]</b> - Настройка task</p></li>
            <li><p><b>[param_cfg]</b> - Настройка параметров адресной строки</p></li>
            <li><p><b>[order_cfg]</b> - Порядок отображения</p></li>
        </ul>

        <h3>[main]</h3>
        <ul>
            <li><p><b>sef</b> - Включен ли обработчик:
                    <br>0 - нет,
                    <br>1 - да</p></li>
        </ul>

        <h3>[option_cfg]</h3>
        <ul>
            <li><p><b>option_show</b> - Показывать ли название компонента:
                    <br>0 - нет,
                    <br>1 - из option_cfg_name,
                    <br>2 - из option_cfg_sql</p></li>
            <li><p><b>option_name</b> - Название компонента [a-z0-9-_]. Если пустой, то используется значение option</p></li>
            <li><p><b>option_sql</b> - Имя параметра - SQL-запрос. Если пустой, то используется значение option.
                    <br>Переменные в запросе беруться из адресной строки и указываются:
                    <br> - если имя поля, таблицы или их часть, то в одинарных квадратных скобках [...]
                    <br> - если это значение, то в двойных квадратных скобках [[...]]
                    <br><br>Пример:
                    <br><code>option_sql = "SELECT `id` AS value FROM `#__boss[qqq]_config` WHERE `id`=[[directory]]"</code>
                </p></li>
        </ul>

        <h3>[task_cfg]</h3>
        <ul>
            <li><p><b>task_prm</b> - Название параметра</p></li>
            <li><p><b>task_val</b> - Имя параметра. Если пустой, то используется task_sql</p></li>
            <li><p><b>task_sql</b> - Имя параметра - SQL-запрос. Если пустой, то используется task_prm
                    <br>Переменные в запросе беруться из адресной строки и указываются:
                    <br> - если имя поля, таблицы или их часть, то в одинарных квадратных скобках [...]
                    <br> - если это значение, то в двойных квадратных скобках [[...]]
                    <br><br>Пример:
                    <code>
                        <br>task_prm[] = 'search'
                        <br>task_val[] =
                        <br>task_sql[] =
                        <br>
                        <br>task_prm[] = 'show_user'
                        <br>task_val[] = 'user'
                        <br>task_sql[] =
                        <br>
                        <br>task_prm[] = 'show_category'
                        <br>task_val[] =
                        <br>task_sql[] = "SELECT `slug` AS `value` FROM `#__boss_[directory]_categories` WHERE `id`=[[catid]]"
                    </code>
                </p></li>
            <li><p><b>task_html</b> - Добавлять окончание ".html" при перечисленных task
                    <br><br>Пример:
                    <code>
                        <br>task_html[] = 'show_content'
                        <br>task_html[] = 'write_content'
                        <br>task_html[] = 'delete_content'
                    </code>
                </p></li>
        </ul>

        <h3>[param_cfg]</h3>
        <ul>
            <li><p><b>param</b> - Перечень параметров, которые нужно скрыть в адресной строке.
                    <br><br>Пример:
                    <code>
                        <br>param[] = 'contentid'
                        <br>param[] = 'catid'
                        <br>param[] = 'directory'
                        <br>Если скрывать нечего, оставьте значение пустым.
                    </code>
                </p></li>
        </ul>

        <h3>[order_cfg]</h3>
        <ul>
            <li><p><b>order</b> - Перечень параметров в порядке, в котором будут отображаться в адресной строке.
                    <br><br>Пример:
                    <code>
                        <br>order[] = option
                        <br>order[] = task
                        <br>order[] = directory
                        <br>order[] = year
                        <br>order[] = month
                        <br>order[] = day
                        <br>order[] = catid
                        <br>order[] = content_types
                        <br>order[] = contentid
                        <br>Если скрывать нечего, оставьте значение пустым.
                    </code>
                </p></li>
        </ul>
    <?php
    }

    /**
     * Панель управления (страница по умолчанию)
     *
     * @param $sef_files - количество sef-файлов
     * @param $sef_link  - количество ссылок
     * @param $sef_dubl  - количество дубликатов
     */
    public static function pageDefault($sef_files, $sef_link, $sef_dubl)
    {
        // подключаем языковой файл
        $_lang = JLotosSefClass::getLang();

        $sef_dubl = (intval($sef_dubl) > 0) ? '<span style="color:#f00">' . $sef_dubl . '</span>' : $sef_dubl;

        ?>
        <table class="adminheading" border="0">
            <tr>
                <th class="cpanel"><?php echo $_lang['JLSEF_NAME']; ?></th>
            </tr>
        </table>
        <table>
            <tr>
                <td width="50%" valign="top">
                    <div class="cpicons">
                        <?php

                        $link = 'index2.php?option=com_jlotossef&task=configuration';
                        self::quickIconButton($link, 'configuration_b.png', $_lang['JLSEF_CFG']);

                        $link = 'index2.php?option=com_jlotossef&task=listref';
                        self::quickIconButton($link, 'references_b.png', $_lang['JLSEF_REF']);

                        $link = 'index2.php?option=com_jlotossef&task=listdup';
                        self::quickIconButton($link, 'duplicates_b.png', $_lang['JLSEF_DUP']);

                        $link = 'index2.php?option=com_jlotossef&task=description';
                        self::quickIconButton($link, 'description_b.png', $_lang['JLSEF_DES']);

                        $link = 'index2.php?option=com_jlotossef&task=clrr';
                        self::quickIconButton($link, 'clear_b.png', $_lang['JLSEF_CLR_R']);

                        $link = 'index2.php?option=com_jlotossef&task=clrd';
                        self::quickIconButton($link, 'clear_b.png', $_lang['JLSEF_CLR_D']);

                        $link = 'index2.php?option=com_jlotossef&task=expr';
                        self::quickIconButton($link, 'exp_b.png', $_lang['JLSEF_EXP_R']);

                        $link = 'index2.php?option=com_jlotossef&task=expd';
                        self::quickIconButton($link, 'exp_b.png', $_lang['JLSEF_EXP_D']);

                        ?>
                    </div>
                    <div style="clear:both;">&nbsp;</div>
                </td>
                <td width="50%" valign="top">
                    <table class="adminlist">
                        <tr>
                            <th align="center"><?php echo $_lang['JLSEF_TBL_1']; ?></th>
                            <th align="center"><?php echo $_lang['JLSEF_TBL_2']; ?></th>
                            <th align="center"><?php echo $_lang['JLSEF_TBL_3']; ?></th>
                        </tr>
                        <tr>
                            <td align="center"><?php echo $sef_files; ?></td>
                            <td align="center"><?php echo $sef_link; ?></td>
                            <td align="center"><?php echo $sef_dubl; ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    <?php
    }

    /**
     * Прорисовка кнопок управления
     *
     * @param $link  - ссылка
     * @param $image - иконка
     * @param $text  - подпись
     */
    public static function quickIconButton($link, $image, $text)
    {
        ?>
        <span>
	        <a href="<?php echo $link; ?>" title="<?php echo $text; ?>">
                <?php
                echo mosAdminMenus::imageCheckAdmin($image, '/administrator/components/com_jlotossef/images/', null, null, $text);
                echo $text;
                ?>
            </a>
        </span>
    <?php
    }
}
