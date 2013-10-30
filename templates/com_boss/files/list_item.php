<article>
	<table>
		<tr>
			<td>
				<div class="tpl_image"><?php $this->loadFieldsInGroup($content, "CatImage", ""); ?></div>
			</td>
			<td>
				<h3><?php $this->displayContentTitle($content); ?></h3>
				<div class="boss_cat"><?php $this->displayCategoryTitle($content, 2); ?></div>
				<div class="boss_vote">
					<?php $this->rating->displayVoteResult($content, $this->directory, $this->conf); ?>
				</div>

				<div class="tpl_txt">
					<div class="tpl_description"><?php $this->loadFieldsInGroup($content, "CatInfo", "<br />"); ?></div>
				</div>
			</td>
		</tr>
	</table>

	<div class="tpl_description"><?php $this->loadFieldsInGroup($content, "CatDescription", "<br />"); ?></div>

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
