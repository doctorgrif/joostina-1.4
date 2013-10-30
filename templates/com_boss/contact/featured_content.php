<article class="boss_content_featured">
	<h3>
		<?php $this->displayContentTitle($content); ?>
		<span class="boss_cat">/<?php $this->displayCategoryTitle($content, 2); ?></span>
	</h3>
	<table>
		<tr>
			<td><div class="tpl_image"><?php $this->loadFieldsInGroup($content, "CatImage", ""); ?></div></td>
			<td><div class="tpl_description"><?php $this->loadFieldsInGroup($content, "CatSubtitle", "<br />"); ?></div></td>
		</tr>
	</table>

	<div class="comments">
		<?php echo $this->displayListTags($content); ?>
	</div>
	<div class="comments">
		<?php $this->displayContentHits($content); ?>
		<?php if ($this->isReviewAllowed()) {
			echo '&nbsp;&nbsp;';
			$this->comments->displayNumReviews($content, $this->reviews, $this->conf);
		} ?>
	</div>

	<div class="edit"><?php $this->displayContentEditDelete($content); ?></div>
</article>
