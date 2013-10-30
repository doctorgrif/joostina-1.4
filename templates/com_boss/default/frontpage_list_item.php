<article>
    <h3>
        <?php $this->displayContentTitle($content); ?>
        <span class="boss_cat">/<?php $this->displayCategoryTitle($content, 3); ?></span>
    </h3>

    <div class="boss_vote">
        <?php $this->rating->displayVoteResult($content, $this->directory, $this->conf); ?>
    </div>

    <div class="boss_date">
        <?php $this->displayContentDate($content); ?>
    </div>

    <div class="boss_content">
        <?php if ($this->countFieldsInGroup("CatShort")) {
        $this->loadFieldsInGroup($content, "CatShort", "&nbsp;");
    } ?>
    </div>

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

