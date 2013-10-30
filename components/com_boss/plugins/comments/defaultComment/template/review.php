<div class="dotteddiv">
    <h3><?php $this->displayReviewTitle($review); ?></h3>

    <div class="text">
        <?php $this->displayReviewContent($review); ?>
        <div class="user"><?php echo BOSS_BY; ?> <?php $this->displayReviewDate($review); ?></div>
        <?php $this->displayButDelete($content, $directory, $review); ?>
    </div>

</div>