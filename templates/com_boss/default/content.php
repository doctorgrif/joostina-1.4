<article>

    <h1>
        <?php $this->displayContentTitle($content, false); ?>
        <span>
		&nbsp;&nbsp;&nbsp;&nbsp;<?php $this->PrintIcon($content, '<img src="/images/M_images/printButton.png" alt="Print" align="top" />');?>
            &nbsp;<?php $this->EmailIcon($content, '<img src="/images/M_images/emailButton.png" alt="Email" align="top" />');?>
		</span>
    </h1>

    <div class="boss_pathway"><?php $this->displayPathway($content); ?></div>

    <div class="boss_vote">
        <?php $this->rating->displayVoteForm($content, $this->directory, $this->conf); ?>
    </div>

    <div class="boss_date">
        <?php $this->displayContentDate($content); ?>
    </div>

    <div class="boss_content">
        <?php $this->loadFieldsInGroup($content, "ConShort", "<br/>"); ?>
        <?php $this->loadFieldsInGroup($content, "ConFull", "<br/>"); ?>
    </div>

    <?php if ($this->displayTags()) { ?>
    <div class="comments">
        <?php echo $this->displayTags(); ?>
    </div>
    <?php } ?>

    <div class="comments">
        <?php $this->displayContentHits($content); ?>
        <?php if ($this->isReviewAllowed()) {
        echo '&nbsp;&nbsp;';
        $this->comments->displayNumReviews($content, $this->reviews, $this->conf);
    } ?>
    </div>
    <?php if ($this->isReviewAllowed()) { ?>
    <hr>
    <div class="boss_comments">
        <h2 class="componentheading2">
            <?php echo BOSS_REVIEWS; ?>
        </h2>

        <div class="boss_reviews">
            <?php $this->comments->displayReviews($content, $this->directory, $this->conf, $this->reviews); ?>
        </div>

        <br/>

        <h2 class="componentheading2">
            <?php echo BOSS_ADD_REVIEWS; ?>
        </h2>

        <div>
            <?php $this->comments->displayAddReview($this->directory, $content, $this->conf); ?>
        </div>
    </div>
    <?php } ?>

    <div class="edit"><?php $this->displayContentEditDelete($content); ?></div>
</article>
