<!-- Вывод контента на главной -->
<div class="boss_tpl_flist_item">

	<div class="boss_tpl_image">
		<?php $this->loadFieldsInGroup($content, "CatImage", ""); ?>
	</div>
	<div class="boss_tpl_txt">
		<h3><?php $this->displayContentTitle($content); ?></h3>
		<h5><?php $this->displayCategoryTitle($content, 3); ?></h5>

		<div class="boss_tpl_description"><?php $this->loadFieldsInGroup($content, "CatInfo", "<br />"); ?></div>
	</div>
	<br style="clear: both"/>

	<div class="boss_tpl_description"><?php $this->loadFieldsInGroup($content, "CatDescription", "<br />"); ?></div>
	<?php if($this->displayContentEditDelete($content)){ ?>
	<div class="boss_tpl_edit">
		<?php $this->displayContentEditDelete($content); ?>
	</div>
	<?php } ?>
</div>