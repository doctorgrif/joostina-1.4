<?php
/**
 * @package   JLImage - Замена изображений в контенте "всплывающими" изображениями, увеличивающимися при нажатии
 * @copyright Авторские права (C) 2000-2013 Gold Dragon.
 * @license   http://www.gnu.org/licenses/gpl.htm GNU/GPL
 *            GDNLotos - Главные новости - модуль позволяет выводить основные материалы по определённым критериям для Joostina 1.4.1.x
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл view/copyright.php.
 */

// запрет прямого доступа
defined('_JLINDEX') or die();

$_MAMBOTS->registerFunction('onCustomEditorButton', 'botJLImageButton');

/**
 * кнопка изображения Joostina
 * @return array - возвращает массив из двух элементов: ( imageName, textToInsert )
 */
function botJLImageButton(){
	$button = array('jlimage.png', '{{jlimage}}');
	return $button;
}