<?php
/**
 * @package Joostina BOSS
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina BOSS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Joostina BOSS основан на разработках Jdirectory от Thomas Papin
 */
defined('_JLINDEX') or die();

class defaultRating extends mosDBTable{
	var $id = null;
	var $contentid = null;
	var $userid = null;
	var $value = null;
	var $ip = null;
	var $date = null;

	public function __construct($directory){
		$database = database::getInstance();
		$this->mosDBTable('#__boss_' . $directory . '_rating', 'id', $database);
	}

	function save_vote($directory){
		$mainframe = mosMainFrame::getInstance();
		$my = JCore::getUser();
		$database = database::getInstance();

		//get configuration
		$conf = getConfig($directory);
		$row = new defaultRating($directory);
		$catid = (int)mosGetParam($_POST, 'category', 0);

		if($conf->allow_ratings == 1){

			// bind it to the table
			if(!$row->bind($_POST)){
				echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
				exit();
			}

			if(($my->id == "0" && $conf->allow_unregisered_comment == 0)){
				mosRedirect(JSef::getUrlToSef("index.php?option=com_boss&amp;task=show_content&amp;&contentid=" . $row->contentid . "&amp;directory=$directory"), "");
				return;
			}

			$query = " SELECT count(*) FROM #__boss_" . $directory . "_rating " .
				" WHERE contentid = " . $row->contentid . " AND userid = " . $my->id;
			$database->setQuery($query);
			$nb = $database->loadResult();

			if(($nb > 0)){
				mosRedirect(JSef::getUrlToSef("index.php?option=com_boss&amp;task=show_content&amp;&contentid=" . $row->contentid . "&amp;directory=$directory"), BOSS_ALREADY_VOTE);
				return;
			}

			$row->userid = $my->id;
			$row->date = time();
			$row->ip = ip2long(getIp());

			// store it in the db
			if(!$row->store()){
				echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
				exit();
			}

			mosRedirect(JSef::getUrlToSef("index.php?option=com_boss&amp;task=show_content&amp;&contentid=" . $row->contentid . "&amp;directory=$directory"), BOSS_THANKS_FOR_YOUR_VOTE);
		} else{
			mosRedirect(JSef::getUrlToSef("index.php?option=com_boss&amp;task=show_content&amp;&contentid=" . $row->contentid . "&amp;directory=$directory"), "");
		}
	}

	/**
	 * действия при установке плагина
	 * @param $directory
	 * @return void
	 */
	public function install($directory){
		$sql = "CREATE TABLE IF NOT EXISTS `#__boss_" . $directory . "_rating` (
  					`id` int(10) NOT NULL AUTO_INCREMENT,
  					`contentid` int(10) DEFAULT '0',
  					`userid` int(10) DEFAULT '0',
  					`value` tinyint(1) DEFAULT '5',
  					`ip` int(11) DEFAULT '0',
  					`date` int(10) DEFAULT '0',
  				PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ";
		$this->database->setQuery($sql)->query();
	}

	public function uninstall($directory){
		$query = "DROP TABLE IF EXISTS `#__boss_" . $directory . "_rating`";
		$this->_db->setQuery($query)->query();
	}

	//функция для вставки таблиц и полей рейтинга в запрос категории и контента
	public function queryString($directory, $conf){
		$query = array();
		if($conf->allow_ratings == 1){
			$query['tables'] = " LEFT JOIN #__boss_" . $directory . "_rating as rat ON a.id = rat.contentid \n";
			$query['fields'] = " count(DISTINCT rat.id) as num_votes, AVG(rat.value) as sum_votes, rat.id as not_empty, \n";
			$query['wheres'] = '';
		} else{
			$query['tables'] = '';
			$query['fields'] = '';
			$query['wheres'] = '';
		}
		return $query;
	}

	function displayVoteForm($content, $directory, $conf){
		if($conf->allow_ratings){
			$mainframe = mosMainFrame::getInstance();
			$my = JCore::getUser();

			if($my->id == 0 && $conf->allow_unregisered_comment == 0){
				$link = JSef::getUrlToSef("index.php?option=com_boss&amp;task=login&amp;directory=$directory");
				echo sprintf(BOSS_VOTE_LOGIN_REQUIRED, $link);
			} else{
				$this->displayVoteResult($content, $directory, $conf);
				$target = JSef::getUrlToSef("index.php?option=com_boss&amp;task=save_vote&amp;directory=$directory");
				?>
			<form action="<?php echo $target;?>" method="post" name="reviewForm">
				<select name="value">
					<option value="0">0</option>
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
				</select>
				<input type="hidden" name="contentid" value="<?php echo $content->id; ?>"/>
				<input type="hidden" name="catid" value="<?php echo intval(mosGetParam($_REQUEST, 'catid', 0)); ?>"/>
				<input type="button" value=<?php echo BOSS_SUBMIT_VOTE; ?> onclick="submit()" />
			</form>
			<?php
			}
			$this->displayNumVotes($content);
			return true;
		} else{
			return false;
		}
	}

	function displayNumVotes($content){
		if(isset($content->not_empty))
			$nb = $content->num_votes;
		else
			$nb = 0;
		echo sprintf(BOSS_NUM_VOTES, $nb);
	}


	function displayVoteResult($content, $directory, $conf){
		if($conf->allow_ratings){
			if(($content->num_votes > 0) && (isset($content->not_empty)))
				$result = $content->sum_votes;
			else
				$result = 0;
			for($i = 1; $i <= 5; $i++){
				if($result >= $i){
					echo '<img src="' . _JLPATH_SITE . '/components/com_boss/plugins/ratings/defaultRating/images/star_10.png" alt="star_10" align="middle" />';
				} else if($result >= $i - 0.5){
					echo '<img src="' . _JLPATH_SITE . '/components/com_boss/plugins/ratings/defaultRating/images/star_05.png" alt="star_05" align="middle"/>';
				} else{
					echo '<img src="' . _JLPATH_SITE . '/components/com_boss/plugins/ratings/defaultRating/images/star_00.png" alt="star_00" align="middle" />';
				}
			}
			$this->displayNumVotes($content);
			return true;
		} else{
			return false;
		}
	}
}














