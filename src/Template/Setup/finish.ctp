<?php
    $loginUrl = $this->Url->build(["_name" => 'adminLogin']) 
?>
<dl class="row" style="margin-top: 20px; margin-left: -35px">
  <dt class="col-sm-3">Username</dt>
  <dd class="col-sm-9"><span class="uk-visible@m uk-visible@l">: </span><?= $admin['username'] ?></dd>
  <dt class="col-sm-3">Password</dt>
  <dd class="col-sm-9"><span class="uk-visible@m uk-visible@l">: </span>Your chosen password</dd>
</dl>
<?php
    if ($this->request->host() != 'localhost'):
?>
<p>Your login information has been sent to your email.</p>
<?php endif; ?>
<div class="login-buttons">
	<?= $this->Form->button('Start Purple', ['type' => 'button', 'onClick' => "location.href='".$loginUrl."'", 'class' => 'btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn']); ?>
</div>