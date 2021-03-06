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

/**
 * @package Joostina
 * @subpackage Search
 */
class search_html{

	public static function openhtml($params){
		if($params->get('page_title')){
			?>
		<div class="componentheading<?php echo $params->get('pageclass_sfx'); ?>"><h1><?php echo $params->get('header'); ?></h1></div><?php
		}
	}

	public static function searchbox($searchword, $lists, $params){
		?>
	<br/>

	<form action="<?php echo JSef::getUrlToSef('index.php?option=com_search')?>" method="get" name="searchForm" id="searchForm">
		<div class="contentpaneopen<?php echo $params->get('pageclass_sfx'); ?>">
			<label for="search_searchword"><?php echo _PROMPT_KEYWORD; ?>:</label>
			<br/>
			<input type="text" name="searchword" id="search_searchword" size="30" maxlength="20" value="<?php echo stripslashes($searchword); ?>" class="inputbox"/>
			<span class="button"><input type="submit" name="submit2" value="<?php echo _SEARCH; ?>" class="button"/></span>
			<br/>
			<?php echo $lists['searchphrase']; ?>
			<br/>
			<br/>

			<h3><?php echo _SEARCH_RESULTS ?></h3>
			<label for="search_ordering"><?php echo _ORDERING; ?>:</label>
			<?php echo $lists['ordering']; ?>
		</div>
	</form>
	<?php
	}

	public static function searchintro($searchword, $params){
		?>
		<div class="searchintro<?php echo $params->get('pageclass_sfx'); ?>">
			<h4><?php echo _PROMPT_KEYWORD, ' <span>', stripslashes($searchword), '</span>'; ?></h4>
		<?php
	}

	public static function message($message){
		echo $message;
	}

	public static function displaynoresult(){
		search_html::message(_NOKEYWORD);
		echo '</div>';
	}

	public static function display($rows, $params, $pageNav, $limitstart, $limit, $total, $totalRows, $searchword){
		$option = JSef::getOption();
		$searchword = urldecode($searchword);
		$searchword = htmlspecialchars($searchword, ENT_QUOTES, 'UTF-8');
		?>
		</div>
		<br/>
	<?php
		echo $pageNav->writePagesCounter();
		$ordering = strtolower(strval(mosGetParam($_REQUEST, 'ordering', 'newest')));
		$ordering_exist = array('newest', 'oldest', 'popular', 'alpha', 'category');
		$ordering = isset($ordering_exist[$ordering]) ? $ordering : 'newest';
		$searchphrase = strtolower(strval(mosGetParam($_REQUEST, 'searchphrase', 'any')));
		$searchphrase = htmlspecialchars($searchphrase, ENT_QUOTES, 'UTF-8');
		$cleanWord = htmlspecialchars($searchword, ENT_QUOTES, 'UTF-8');
		$link = _JLPATH_SITE . "/index.php?option=$option&amp;searchword=$cleanWord&amp;searchphrase=$searchphrase&amp;ordering=$ordering";
		//if($total>0){
		echo $pageNav->getLimitBox($link);
		//}
		?>
	<br/><br/>
	<table class="contentpaneopen<?php echo $params->get('pageclass_sfx'); ?>">
		<tr class="<?php echo $params->get('pageclass_sfx'); ?>">
			<td><h4><?php eval('echo "' . _CONCLUSION . '";'); ?></h4>
				<?php
				$z = $limitstart + 1;
				$end = $limit + $z;
				if($end > $total){
					$end = $total + 1;
				}
				for($i = $z; $i < $end; $i++){
					$row = $rows[$i - 1];
					if($row->created){
						$created = mosFormatDate($row->created, _DATE_FORMAT_LC);
					} else{
						$created = '';
					}
					?>
					<fieldset>
						<div>
							<span class="small<?php echo $params->get('pageclass_sfx'); ?>"><?php echo $i . '. '; ?></span>
							<?php
							if($row->href){
								$row->href = ampReplace($row->href);
								if($row->browsernav == 1){
									?>
										<a href="<?php echo JSef::getUrlToSef($row->href); ?>" target="_blank">
											<?php
								} else{
									?>
											<a href="<?php echo JSef::getUrlToSef($row->href); ?>">
												<?php
								}
							}
							echo $row->title;
							if($row->href){
								?>
										</a>
										<?php
							}
							if($row->section){
								?>
								<br/>
								<span class="small<?php echo $params->get('pageclass_sfx'); ?>">(<?php echo $row->section; ?>)</span>
								<?php
							}
							?>
						</div>
						<div><?php echo ampReplace($row->text); ?></div>
						<?php
						if(JCore::getCfg('showCreateDate')){
							?>
							<div class="small<?php echo $params->get('pageclass_sfx'); ?>"><?php echo $created; ?></div>
							<?php
						}
						?>
					</fieldset>
					<?php
				}
				?>
			</td>
		</tr>
	</table>
	<br/>
	<div class="nofollow">
        В других поисковиках:
        <a href="http://www.yandex.ru/yandsearch?text=<?php echo $searchword; ?>" target="_blank">Яндекс</a>&nbsp;|
        <a href="http://www.google.ru/search?hl=ru&amp;q=<?php echo $searchword; ?>&amp;aq=f" target="_blank">Google</a>&nbsp;|
        <a href="http://www.nova.rambler.ru/srch?&amp;words=<?php echo $searchword; ?>" target="_blank">Rambler</a>&nbsp;|
        <a href="http://ru.wikipedia.org/wiki/<?php echo $searchword; ?>" target="_blank">Википедия</a>&nbsp;|
        <a href="http://ru.search.yahoo.com/search?p=<?php echo $searchword; ?>" target="_blank">Yahoo</a>&nbsp;|
        <a href="http://bing.com/search?q=<?php echo $searchword; ?>" target="_blank">Bing</a>&nbsp;|
        <a href="http://go.mail.ru/search?rch=l&amp;q=<?php echo $searchword; ?>" target="_blank">Поиск@Mail.ru</a>
	</div>
	<br/>
	<?php
	}

	public static function conclusion($searchword, $pageNav){
		$option = JSef::getOption();
		$ordering = strtolower(strval(mosGetParam($_REQUEST, 'ordering', 'newest')));
		$ordering_exist = array('newest', 'oldest', 'popular', 'alpha', 'category');
		$ordering = isset($ordering_exist[$ordering]) ? $ordering : 'newest';
		$searchphrase = strtolower(strval(mosGetParam($_REQUEST, 'searchphrase', 'any')));
		$searchphrase = htmlspecialchars($searchphrase);
		$link = _JLPATH_SITE . "/index.php?option=$option&amp;searchword=$searchword&amp;searchphrase=$searchphrase&amp;ordering=$ordering";
		echo $pageNav->writePagesLinks($link);
	}

}

class search_by_tag_HTML{

	public static function tag_page($items, $params, $groups){
		?>
	<div class="tag_page">
		<div class="contentpagetitle">
			<h1><?php echo $params->title; ?> "<?php echo $items->tag; ?>"</h1>

			<div class="search_result"><?php echo self::view_group($items, $params, $groups); ?></div>
		</div>
	</div>
	<?php
	}

	public static function view_group($items, $params, $groups){
		if(count($items->items['com_boss']) > 0){
			foreach($groups as $key => $group){
				foreach($items->items[$key] as $item){
					$item->link = searchByTag::construct_url($item, $group);
					$item->text = Text::word_limiter(mosHTML::cleanText($item->text), 25);
					?>
				<div class="search_item">
					<h2><a class="contentpagetitle" href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a></h2>
					<span class="date"><?php echo $item->date; ?></span> <br/>

					<p><?php echo $item->text; ?></p>
				</div>
				<?php
				}
			}
		} else{
			?>
		<div><?php echo _SEARCH_NONE_W_TAG ?></div>
		<?php }
		; ?>
	<?php
	}

}

?>