<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_JLINDEX') or die();

// ensure user has access to this function
if(!($acl->acl_check('administration', 'edit', 'users', $my->usertype, 'modules', 'all') | $acl->acl_check('administration', 'install', 'users', $my->usertype, 'modules', 'all'))){
	mosRedirect('index2.php', _NOT_AUTH);
}

$mainframe = mosMainFrame::getInstance();

require_once ($mainframe->getPath('admin_html'));

$client = strval(mosGetParam($_REQUEST, 'client', ''));
$moduleid = mosGetParam($_REQUEST, 'moduleid', null);

$cid = josGetArrayInts('cid');

if($cid[0] == 0 && isset($moduleid)){
	$cid[0] = $moduleid;
}

mosCache::cleanCache('init_modules');
$task = JSef::getTask();
switch($task){
	case 'copy':
		copyModule($option, intval($cid[0]), $client);
		break;

	case 'new':
		editModule($option, 0, $client);
		break;

	case 'edit':
		editModule($option, intval($cid[0]), $client);
		break;

	case 'editA':
		editModule($option, $id, $client);
		break;

	case 'save':
	case 'apply':
		saveModule($option, $client, $task);
		break;

	case 'remove':
		removeModule($cid, $option, $client);
		break;

	case 'cancel':
		cancelModule($option, $client);
		break;

	case 'publish':
	case 'unpublish':
		publishModule($cid, ($task == 'publish'), $option, $client);
		break;

	case 'orderup':
	case 'orderdown':
		orderModule(intval($cid[0]), ($task == 'orderup' ? -1 : 1), $option);
		break;

	case 'accesspublic':
	case 'accessregistered':
	case 'accessspecial':
		accessMenu(intval($cid[0]), $task, $option, $client);
		break;

	case 'saveorder':
		saveOrder($cid, $client);
		break;

	default:
		viewModules($option, $client);
		break;
}

/**
 * Compiles a list of installed or defined modules
 */
function viewModules($option, $client){
	$mainframe = mosMainFrame::getInstance();
    $my = JCore::getUser();
	$database = database::getInstance();

	$filter_position = $mainframe->getUserStateFromRequest("filter_position{$option}{$client}", 'filter_position', 0);
	$filter_type = $mainframe->getUserStateFromRequest("filter_type{$option}{$client}", 'filter_type', 0);
	$limit = intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', JCore::getCfg('list_limit')));
	$limitstart = intval($mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0));
	$search = $mainframe->getUserStateFromRequest("search{$option}{$client}", 'search', '');

	if($client == 'admin'){
		$where[] = "m.client_id = 1";
		$client_id = 1;
	} else{
		$where[] = "m.client_id = 0";
		$client_id = 0;
		$client = '';
	}

	// used by filter
	if($filter_position){
		$where[] = "m.position = " . $database->Quote($filter_position);
	}
	if($filter_type){
		if($filter_type == 'user_create')
			$where[] = "m.module = ''";
		else
			$where[] = "m.module = " . $database->Quote($filter_type);
	}
	if($search){
		$where[] = "LOWER( m.title ) LIKE '%" . $database->getEscaped(Jstring::trim(Jstring::strtolower($search))) . "%'";
	}

	// get the total number of records
	$query = "SELECT COUNT(*) FROM #__modules AS m" . (count($where) ? "\n WHERE " . implode(' AND ', $where) : '');
	$database->setQuery($query);
	$total = $database->loadResult();

	mosMainFrame::addLib('pagenavigation');
	$pageNav = new mosPageNav($total, $limitstart, $limit);

	$sql = "SELECT m.*, u.name AS editor, g.name AS groupname, mm.option AS pages
			FROM #__modules AS m
			LEFT JOIN #__users AS u ON u.id = m.checked_out
			LEFT JOIN #__groups AS g ON g.id = m.access
			LEFT JOIN #__modules_com AS mm ON mm.moduleid = m.id" . (count($where) ? "\n WHERE " . implode(' AND ', $where) : ''). "
			GROUP BY m.id
			ORDER BY position ASC, ordering ASC";
	$database->setQuery($sql, $pageNav->limitstart, $pageNav->limit);
	$rows = $database->loadObjectList();
	if($database->getErrorNum()){
		echo $database->stderr();
		return false;
	}

	// get list of Positions for dropdown filter
	$query = "SELECT t.position AS value, t.position AS text"
		. "\n FROM #__template_positions as t"
		. "\n LEFT JOIN #__modules AS m ON m.position = t.position"
		. "\n WHERE m.client_id = " . (int)$client_id . "\n GROUP BY t.position"
		. "\n ORDER BY t.position";
	$positions = array();
	$positions[] = mosHTML::makeOption('0', _SEL_POSITION);
	$database->setQuery($query);
	$positions = array_merge($positions, $database->loadObjectList());
	$lists['position'] = mosHTML::selectList($positions, 'filter_position', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $filter_position);

	// get list of Positions for dropdown filter
	$query = "SELECT module AS value, module AS text"
		. "\n FROM #__modules"
		. "\n WHERE module!='' AND client_id = " . (int)$client_id . "\n GROUP BY module"
		. "\n ORDER BY module";
	$types[] = mosHTML::makeOption('0', _SEL_TYPE);
	$types[] = mosHTML::makeOption('user_create', _USER_MODULES);
	$database->setQuery($query);
	$types = array_merge($types, $database->loadObjectList());
	$lists['type'] = mosHTML::selectList($types, 'filter_type', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', "$filter_type");

	HTML_modules::showModules($rows, $my->id, $client, $pageNav, $option, $lists, $search);
}

/**
 * Compiles information to add or edit a module
 * @param string The current GET/POST option
 * @param integer The unique id of the record to edit
 */
function copyModule($option, $uid, $client){
	$database = database::getInstance();
	josSpoofCheck();
	$row = new mosModule($database);
	// load the row from the db table
	$row->load((int)$uid);
	$row->title = _MODULES_COPY . $row->title;
	$row->id = 0;
	$row->iscore = 0;
	$row->published = 0;

	if(!$row->check()){
		echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}
	if(!$row->store()){
		echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->checkin();
	if($client == 'admin'){
		$where = "client_id='1'";
	} else{
		$where = "client_id='0'";
	}
	$row->updateOrder('position=' . $database->Quote($row->position) . " AND ($where)");

	$sql = "SELECT * FROM #__modules_com WHERE moduleid = " . (int)$uid;
	$database->setQuery($sql);
	$rows = $database->loadObjectList();
	foreach($rows as $opt){
		$sql = "INSERT INTO #__modules_com (`id`, `moduleid`, `option`, `directory`, `category`, `task`)
		 		VALUES (
		 			NULL,
		 			'" . $row->id . "',
		 			'" . $opt->option . "',
		 			'" . $opt->directory . "',
		 			'" . $opt->category . "',
		 			'" . $opt->task . "')
		 		;";
		$database->setQuery($sql);
		$database->query();
	}

	mosCache::cleanCache('com_boss');

	$msg = _MODULE_COPIED . ' [' . $row->title . ']';
	mosRedirect('index2.php?option=' . $option . '&client=' . $client, $msg);
}

/**
 * Сохранение модуля после редактирования
 * @param $option - компонент
 * @param $client
 * @param $task - страница
 *
 * @modification 12.08.2013
 */
function saveModule($option, $client, $task){
	$database = database::getInstance();

	josSpoofCheck();
	$params = mosGetParam($_POST, 'params', '');

	if(is_array($params)){
		$txt = array();
		foreach($params as $k => $v){
			$txt[] = "$k=$v";
		}
		$_POST['params'] = mosParameters::textareaHandling($txt);
	}
	$row = new mosModule($database);

	if(!$row->bind($_POST, 'selections')){
		echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}
	if(!$row->check()){
		echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}
	if(!$row->store()){
		echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$row->checkin();
	if($client == 'admin'){
		$where = "client_id=1";
	} else{
		$where = "client_id=0";
	}

	$row->updateOrder('position=' . $database->Quote($row->position) . " AND ($where)");

	// delete old module to menu item associations
	$sql = "DELETE FROM #__modules_com WHERE moduleid = " . (int)$row->id;
	$database->setQuery($sql);
	$database->query();

	$menus = isset($_POST['selections']) ? $_POST['selections'] : array();
	if(!count($menus) or in_array('0-0-0-', $menus)){
		$sql = "INSERT INTO `#__modules_com`  (`id`, `moduleid`, `option`, `directory`, `category`, `task`)
		 		VALUES (NULL, '" . $row->id . "', '0',  '0',  '0',  '');";
	} elseif(in_array('-0-0-', $menus)){
		$sql = "INSERT INTO `#__modules_com` (`id`, `moduleid`, `option`, `directory`, `category`, `task`)
		 		VALUES (NULL, '" . $row->id . "', '',  '0',  '0',  '');";
	} else{
		$sql = '';
		foreach($menus as $menu){
			if($menu != '-999'){
				$tmp = preg_match("#^([a-z0-9_-]*)-(\d+)-(\d+)-([a-z0-9_-]*)$#i", $menu, $arr_sel);
				if($tmp){
					$sel['option'] = isset($arr_sel[1]) ? $arr_sel[1] : '';
					$sel['directory'] = isset($arr_sel[2]) ? $arr_sel[2] : 0;
					$sel['category'] = isset($arr_sel[3]) ? $arr_sel[3] : 0;
					$sel['task'] = isset($arr_sel[4]) ? $arr_sel[4] : '';
					$sql .= "INSERT INTO #__modules_com (`id`, `moduleid`, `option`, `directory`, `category`, `task`)
		 					VALUES (
		 						NULL,
		 						'" . $row->id . "',
		 						'" . $sel['option'] . "',
		 						'" . $sel['directory'] . "',
		 						'" . $sel['category'] . "',
		 						'" . $sel['task'] . "'
		 					);";
				}
			}
		}
	}
	$database->setQuery($sql);
	$database->multiQuery();

	mosCache::cleanCache('com_boss');

	switch($task){
		case 'apply':
			$msg = $row->title . ' - ' . _E_ITEM_SAVED;
			mosRedirect('index2.php?option=' . $option . '&amp;client=' . $client . '&amp;task=editA&amp;hidemainmenu=1&amp;id=' . $row->id, $msg);
			break;

		case 'save':
		default:
			$msg = $row->title . ' - ' . _E_ITEM_SAVED;
			mosRedirect('index2.php?option=' . $option . '&amp;client=' . $client, $msg);
			break;
	}
}

/**
 * Compiles information to add or edit a module
 * @param string The current GET/POST option
 * @param integer The unique id of the record to edit
 */
function editModule($option, $uid, $client){
	$mainframe = mosMainFrame::getInstance();
    $my = JCore::getUser();
	$database = database::getInstance();

	$lists = array();
	$row = new mosModule($database);
	// load the row from the db table
	$row->load((int)$uid);
	// fail if checked out not by 'me'
	if($row->isCheckedOut($my->id)){
		mosErrorAlert(_MODULE . " " . $row->title . " " . _NOW_EDITING_BY_OTHER);
	}

	$row->content = htmlspecialchars($row->content);

	if($uid){
		$row->checkout($my->id);
	}
	// if a new record we must still prime the mosModule object with a default
	// position and the order; also add an extra item to the order list to
	// place the 'new' record in last position if desired
	if($uid == 0){
		$row->position = 'left';
		$row->showtitle = true;
		$row->published = 1;
	}


	if($client == 'admin'){
		$where = "client_id = 1";
		$lists['client_id'] = 1;
		$path = 'mod1_xml';
	} else{
		$where = "client_id = 0";
		$lists['client_id'] = 0;
		$path = 'mod0_xml';
	}
	$sql = "SELECT position, ordering, showtitle, title
			FROM #__modules
			WHERE " . $where .  "
			ORDER BY ordering";
	$database->setQuery($sql);
	if(!($orders = $database->loadObjectList())){
		echo $database->stderr();
		return false;
	}

	$query = "SELECT position, description"
		. "\n FROM #__template_positions"
		. "\n WHERE position != ''"
		. "\n ORDER BY position";
	$database->setQuery($query);
	// hard code options for now
	$positions = $database->loadObjectList();

	$orders2 = array();
	$pos = array();
	foreach($positions as $position){
		$orders2[$position->position] = array();
		$pos[] = mosHTML::makeOption($position->position, $position->description);
	}

	for($i = 0, $n = count($orders); $i < $n; $i++){
		$ord = 0;
		if(array_key_exists($orders[$i]->position, $orders2)){
			$ord = count(array_keys($orders2[$orders[$i]->position])) + 1;
		}

		$orders2[$orders[$i]->position][] = mosHTML::makeOption($ord, $ord . '::' .
			addslashes($orders[$i]->title));
	}

	// build the html select list
	$pos_select = 'onchange="changeDynaList(\'ordering\',orders,document.adminForm.position.options[document.adminForm.position.selectedIndex].value, originalPos, originalOrder)"';
	$active = ($row->position ? $row->position : 'left');
	$lists['position'] = mosHTML::selectList($pos, 'position', 'class="inputbox" size="1" ' . $pos_select, 'value', 'text', $active);

	// Получить привязку к компонентам для  $lists['components']
	if($uid){
		$sql = "SELECT *
				FROM #__modules_com
				WHERE moduleid = " . (int)$row->id;
		$database->setQuery($sql);
		$lookup = $database->loadObjectList();
	} else{
		$lookup = array();
	}

	if($row->access == 99 || $row->client_id == 1 || $lists['client_id']){
		$lists['access'] = 'Administrator<input type="hidden" name="access" value="99" />';
		$lists['showtitle'] = 'N/A <input type="hidden" name="showtitle" value="1" />';
		$lists['components'] = 'N/A';
	} else{
		if($client == 'admin'){
			$lists['access'] = 'N/A';
			$lists['components'] = 'N/A';
		} else{
			$lists['access'] = mosAdminMenus::Access($row, true);
			$lists['components'] = mosAdminMenus::MenuLinks($lookup, 1, 1);
		}
		$lists['showtitle'] = mosHTML::yesnoRadioList('showtitle', 'class="inputbox"', $row->showtitle);
	}

	// build the html select list for published
	$lists['published'] = mosAdminMenus::Published($row);

	$row->description = '';
	// XML library
	require_once (_JLPATH_ROOT . '/includes/domit/xml_domit_lite_include.php');
	// xml file for module
	$xmlfile = $mainframe->getPath($path, $row->module);
	$xmlDoc = new DOMIT_Lite_Document();
	$xmlDoc->resolveErrors(true);
	if($xmlDoc->loadXML($xmlfile, false, true)){
		$root = $xmlDoc->documentElement;

		if($root->getTagName() == 'mosinstall' && $root->getAttribute('type') == 'module'){
			$element = $root->getElementsByPath('description', 1);
			$row->description = $element ? trim($element->getText()) : '';
		}
	}

	// get params definitions
	$params = new mosParameters($row->params, $xmlfile, 'module');
	HTML_modules::editModule($row, $orders2, $lists, $params, $option);
}

/**
 * Deletes one or more modules
 * Also deletes associated entries in the #__module_menu table.
 * @param array An array of unique category id numbers
 */
function removeModule($cid, $option, $client){
	$database = database::getInstance();
	josSpoofCheck();
	if(count($cid) < 1){
		echo "<script> alert('Select a module to delete'); window.history.go(-1);</script>\n";
		exit;
	}

	mosArrayToInts($cid);
	$cids = 'id=' . implode(' OR id=', $cid);

	$query = "SELECT id, module, title, iscore, params FROM #__modules WHERE ( $cids )";
	$database->setQuery($query);
	if(!($rows = $database->loadObjectList())){
		echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit;
	}

	$err = array();
	$cid = array();
	foreach($rows as $row){
		if($row->module == '' || $row->iscore == 0){
			$cid[] = $row->id;
		} else{
			$err[] = $row->title;
		}
		// mod_mainmenu modules only deletable via Menu Manager
		if($row->module == 'mod_mainmenu'){
			if(strstr($row->params, 'mainmenu')){
				echo "<script> alert('" . _CANNOT_DELETE_MOD_MAINMENU . "'); window.history.go(-1); </script>\n";
				exit;
			}
		}
	}

	if(count($cid)){
		mosArrayToInts($cid);
		$cids = 'id=' . implode(' OR id=', $cid);
		$query = "DELETE FROM #__modules WHERE ( $cids )";
		$database->setQuery($query);
		if(!$database->query()){
			echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			exit;
		}
		// mosArrayToInts( $cid ); // just done a few lines earlier
		$cids = 'moduleid=' . implode(' OR moduleid=', $cid);
		$query = "DELETE FROM #__modules_com WHERE ( $cids )";
		$database->setQuery($query);
		if(!$database->query()){
			echo "<script> alert('" . $database->getErrorMsg() . "');</script>\n";
			exit;
		}
		$mod = new mosModule($database);
		$mod->ordering = 0;
		$mod->updateOrder("position='left'");
		$mod->updateOrder("position='right'");
	}

	if(count($err)){
		$cids = addslashes(implode("', '", $err));
		echo "<script>alert('$cids " . _CANNOT_DELETE_MODULES . "');</script>\n";
	}

	mosCache::cleanCache('com_boss');

	mosRedirect('index2.php?option=' . $option . '&client=' . $client);
}

/**
 * Publishes or Unpublishes one or more modules
 * @param array An array of unique record id numbers
 * @param integer 0 if unpublishing, 1 if publishing
 */
function publishModule($cid = null, $publish = 1, $option, $client){
    $my = JCore::getUser();
	$database = database::getInstance();
	josSpoofCheck();
	if(count($cid) < 1){
		$action = $publish ? 'publish' : 'unpublish';
		echo "<script> alert('" . _CHOOSE_OBJECT_FOR . " $action'); window.history.go(-1);</script>\n";
		exit;
	}

	mosArrayToInts($cid);
	$cids = 'id=' . implode(' OR id=', $cid);

	$query = "UPDATE #__modules" . "\n SET published = " . (int)$publish . "\n WHERE ( $cids ) AND ( checked_out = 0 OR ( checked_out = " . (int)$my->id . " ) )";
	$database->setQuery($query);
	if(!$database->query()){
		echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	if(count($cid) == 1){
		$row = new mosModule($database);
		$row->checkin($cid[0]);
	}

	mosCache::cleanCache('com_boss');

	mosRedirect('index2.php?option=' . $option . '&client=' . $client);
}

/**
 * Cancels an edit operation
 */
function cancelModule($option, $client){
	$database = database::getInstance();
	josSpoofCheck();
	$row = new mosModule($database);
	// ignore array elements
	$row->bind($_POST, 'selections params');
	$row->checkin();

	mosRedirect('index2.php?option=' . $option . '&client=' . $client);
}

/**
 * Moves the order of a record
 * @param integer The unique id of record
 * @param integer The increment to reorder by
 */
function orderModule($uid, $inc, $option){
	$database = database::getInstance();
	josSpoofCheck();
	$client = strval(mosGetParam($_POST, 'client', ''));

	$row = new mosModule($database);
	$row->load((int)$uid);
	if($client == 'admin'){
		$where = "client_id = 1";
	} else{
		$where = "client_id = 0";
	}

	$row->move($inc, "position = " . $database->Quote($row->position) . " AND ( $where )");
	if($client){
		$client = '&client=admin';
	} else{
		$client = '';
	}

	mosCache::cleanCache('com_boss');

	mosRedirect('index2.php?option=' . $option . '&client=' . $client);
}

/**
 * changes the access level of a record
 * @param integer The increment to reorder by
 */
function accessMenu($uid, $access, $option, $client){
	$database = database::getInstance();
	josSpoofCheck();
	switch($access){
		case 'accesspublic':
			$access = 0;
			break;

		case 'accessregistered':
			$access = 1;
			break;

		case 'accessspecial':
			$access = 2;
			break;
	}

	$row = new mosModule($database);
	$row->load((int)$uid);
	$row->access = $access;

	if(!$row->check()){
		return $row->getError();
	}
	if(!$row->store()){
		return $row->getError();
	}

	mosCache::cleanCache('com_boss');

	mosRedirect('index2.php?option=' . $option . '&client=' . $client);
}

function saveOrder($cid, $client){
	$database = database::getInstance();
	josSpoofCheck();
	$total = count($cid);
	$order = josGetArrayInts('order');

	$row = new mosModule($database);
	$conditions = array();

	// update ordering values
	for($i = 0; $i < $total; $i++){
		$row->load((int)$cid[$i]);
		if($row->ordering != $order[$i]){
			$row->ordering = $order[$i];
			if(!$row->store()){
				echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
				exit();
			} // if
			// remember to updateOrder this group
			$condition = "position = " . $database->Quote($row->position) . " AND client_id = " . (int)
			$row->client_id;
			$found = false;
			foreach($conditions as $cond)
				if($cond[1] == $condition){
					$found = true;
					break;
				} // if
			if(!$found) $conditions[] = array($row->id, $condition);
		} // if
	} // for

	// execute updateOrder for each group
	foreach($conditions as $cond){
		$row->load($cond[0]);
		$row->updateOrder($cond[1]);
	} // foreach

	mosCache::cleanCache('com_boss');

	$msg = _NEW_ORDER_SAVED;
	mosRedirect('index2.php?option=com_modules&client=' . $client, $msg);
} // saveOrder