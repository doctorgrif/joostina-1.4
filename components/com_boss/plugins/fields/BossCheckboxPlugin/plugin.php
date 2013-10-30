<?php defined('_JLINDEX') or die;
/**
 * Joostina BOSS Plugin
 *
 * @package   BOSS Plugin
 * @version   1.0
 * @author    Gold Dragon <illusive@bk.ru>
 * @link      http://gd.joostina-cms.ru
 * @copyright 2013 JLotos.
 * @license   GNU GPL: http://www.gnu.org/licenses/gpl-3.0.html
 *            Joostina Lotos CMS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL. (help/copyright.php)
 * @Date      21.07.2013
 * @see       http://wiki.joostina-cms.ru/index.php/BossCheckboxPlugin
 */

class BossCheckboxPlugin{

	//имя типа поля в выпадающем списке в настройках поля
	var $name = 'Check Box (Single)';

	//тип плагина для записи в таблицы
	var $type = 'BossCheckboxPlugin';

	//отображение поля в категории
	function getListDisplay($directory, $content, $field, $field_values, $conf){
		return $this->getDetailsDisplay($directory, $content, $field, $field_values, $conf);
	}

	//отображение поля в контенте
	function getDetailsDisplay($directory, $content, $field, $field_values, $conf){
		$fieldname = $field->name;
		$value = (isset ($content->$fieldname)) ? $content->$fieldname : '';

		$return = '';
		if(!empty($field->text_before))
			$return .= '<span>' . $field->text_before . '</span>';
		if(!empty($field->tags_open))
			$return .= html_entity_decode($field->tags_open);

		$return .= jdGetLangDefinition($field->title);
		if($value == 1)
			$return .= ":&nbsp;" . BOSS_YES;
		else
			$return .= ":&nbsp;" . BOSS_NO;

		if(!empty($field->tags_close))
			$return .= html_entity_decode($field->tags_close);
		if(!empty($field->text_after))
			$return .= '<span>' . $field->text_after . '</span>';

		return $return;
	}

	//функция вставки фрагмента ява-скрипта в скрипт
	//сохранения формы при редактировании контента с фронта.
	function addInWriteScript($field){

	}

	//отображение поля в админке в редактировании контента
	function getFormDisplay($directory, $content, $field, $field_values, $nameform = 'adminForm', $mode = "write"){
		$fieldname = $field->name;
		$value = (isset ($content->$fieldname)) ? $content->$fieldname : '';
		$strtitle = htmlentities(jdGetLangDefinition($field->title), ENT_QUOTES, 'utf-8');

		$mosReq = (($mode == "write") && ($field->required == 1)) ? " mosReq='1' " : '';
		$read_only = (($mode == "write") && ($field->editable == 0)) ? " readonly=true " : '';
		$checked = (($mode == "write") && ($value == 1)) ? " checked='checked' " : '';
		$class = (($mode == "write") && ($field->required == 1)) ? "boss_required" : 'boss';

		$return = "<input class='inputbox $class' type='checkbox' " . $mosReq . $read_only . $checked . " mosLabel='" . $strtitle . "' name='" . $field->name . "' value='1' />\n";

		return $return;
	}

	function onFormSave($directory, $contentid, $field, $isUpdateMode){
		$return = mosGetParam($_POST, $field->name, "");
		return $return;
	}

	function onDelete($directory, $content){
		return;
	}

	//отображение поля в админке в настройках поля
	function getEditFieldOptions($row, $directory, $fieldimages, $fieldvalues){
		$return = "";
		return $return;
	}

	//действия при сохранении настроек поля
	function saveFieldOptions($directory, $field){
		//если плагин не создает собственных таблиц а пользется таблицами босса то возвращаем false
		//иначе true
		return false;
	}

	//расположение иконки плагина начиная со слеша от корня сайта
	function getFieldIcon(){
		return "/components/com_boss/plugins/fields/" . __CLASS__ . "/images/checkbox.png";
	}

	//действия при установке плагина
	function install($directory){
		return;
	}

	//действия при удалении плагина
	function uninstall($directory){
		return;
	}

	//действия при поиске
	function search($directory, $fieldName){
		$search = '';
		$value = mosGetParam($_REQUEST, $fieldName, "");
		if($value != ""){
			$search .= " AND a.$fieldName = '$value'";
		}
		return $search;
	}
}
