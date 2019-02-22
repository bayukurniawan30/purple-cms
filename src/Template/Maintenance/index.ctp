<div class="auth-form-light text-left">
	<?php
		echo $this->Form->create($notifyEmail, [
			'id'                    => 'form-get-email',
			'class'                 => 'pt-3',
			'data-parsley-validate' => '',
			'url' 					=> ['_name' => 'ajaxGetNotifyEmail']
		]);
	?>
		<div class="form-group">
			<div class="input-group">
				<?php
					echo $this->Form->text('email', [
						'type'             => 'email',
						'class'            => 'form-control input-lg',
						'placeholder'      => 'Enter your email',
						'aria-label'       => 'Enter your email',
						'aria-describedby' => 'button-get-email',
						'data-parsley-errors-container' => '#error-email',
						'required'         => 'required'
					]);
				?>
			  	<div class="input-group-append">
			  		<?php
						echo $this->Form->button('NOTIFY', [
							'id'    => 'button-get-email',
							'class' => 'btn btn-gradient-primary btn-block btn-lg font-weight-medium'
						]);
					?>
			  	</div>
			</div>
			<div id="error-email"></div>
			<p id="error-result" class="text-muted">Provide your email to get early notification of our break!</p>
		</div>
	<?php
		echo $this->Form->end();
	?>

	<?php
		if ($socials->count() > 0):
	?>
	<ul class="uk-iconnav">
	    <li class="stay-in-touch">Stay in touch : </li>
	    <?php
	        foreach ($socials as $social):
	    ?>
	    <li><a href="<?= $social->link ?>" target="_blank"><i class="fa fa-<?= $social->name ?>"></i></a></li>
		<?php endforeach; ?>
	</ul>
	<?php
		endif;
	?>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		var sendComment = {
		    form            : 'form-get-email',
		    button          : 'button-get-email',
		    action          : 'notify',
		    redirectType    : 'ajax',
		    redirect        : '#error-result',
		    btnNormal       : false,
		    btnLoading      : false
		};

		var targetButton = $("#"+sendComment.button);
		targetButton.one('click',function() {
		    ajaxSubmit(sendComment.form, sendComment.action, sendComment.redirectType, sendComment.redirect, sendComment.btnNormal, sendComment.btnLoading);
		})
	})
</script>