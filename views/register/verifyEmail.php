<div class="sub extended"> 
  
  <h2><?php t('verifyEmail') ?></h2>
  <br />
  <?php if($data->alreadyVerified): ?>
  <div>
    Your E-Mail has already been verified.
  </div>
  <?php elseif($data->verified): ?>
  <div id="registerSuccess">
    Your E-Mail has now been verified, you may now login!
  </div>
  <?php else: ?>
  <div id="registerFail">
    An Error has occured.
  </div>
  <?php endif ?>
</div>