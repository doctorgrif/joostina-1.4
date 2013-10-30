<table class="addcomment">
    <tr>
        <td>
            <?php echo BOSS_FNAME; ?>:
            <br/>
            <input id='title' type='text' name='title' maxlength='50' value='<?php echo $name; ?>'/>
            <?php if($this->isReviewCaptchaActivated($conf) && $my->id == 0){?>
            <br/><br/>
            <?php $this->displayCaptchaImage(); ?>
            <br/>
            <?php echo BOSS_FORM_SECURITY_CODE_VERIFY; ?>:
            <br/>
            <?php $this->displayCaptchaInput(); ?>
            <br/>
            <?php } ?>

        </td>
        <td>
            <textarea id='description' name='description' cols='60' rows='10' wrap='VIRTUAL'></textarea><br/>
        </td>
    </tr>
    <tr>
        <td></td>
        <td style="text-align:left;">
            <span class="button">
                <input type="button" value=<?php echo BOSS_SUBMIT; ?> onclick="submit()" />
            </span>
        </td>
    </tr>
</table>


