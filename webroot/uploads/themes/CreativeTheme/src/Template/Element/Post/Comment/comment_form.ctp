<h4>Leave a Reply</h4>

<?php
    echo $this->Form->create($postComment, [
        'id'                    => 'form-send-comment',
        'class'                 => 'contact-form form form-send-comment',
        'data-parsley-validate' => '',
        'url'                   => ''
    ]);

    echo $this->Form->hidden('blog_id', ['value' => $blog_id]);
    echo $this->Form->hidden('ds', ['value' => $this->Url->build(['_name' => 'adminComments', 'blogid' => $blog_id])]);

    if ($formSecurity == 'on') {
        echo $this->Form->hidden('status', ['value' => '']);
        echo $this->Form->hidden('score', ['value' => '']);
    }
?>
    <div class="controls">
        <div class="form-group">
            <?php
                echo $this->Form->label('name', 'Your name');
                echo $this->Form->text('name', [
                    'class'                  => 'form-control',
                    'placeholder'            => '',
                    'data-parsley-minlength' => '2',
                    'data-parsley-maxlength' => '50',
                    'required'               => 'required'
                ]);
            ?>
        </div>
        <div class="form-group">
            <?php
                echo $this->Form->label('email', 'Your email');
                echo $this->Form->text('email', [
                    'type'                   => 'email',
                    'class'                  => 'form-control',
                    'placeholder'            => '',
                    'required'               => 'required'
                ]);
            ?>
        </div>
        <div class="form-group">
            <?php
                echo $this->Form->label('content', 'Comment');
                echo $this->Form->textarea('content', [
                    'class'                  => 'form-control', 
                    'placeholder'            => '',
                    'data-parsley-maxlength' => '1000',
                    'rows'                   => '5',
                    'required'               => 'required'
                ]);
            ?>
        </div> 
        <div>
            <?php
                echo $this->Form->button('Submit Comment', [
                    'id'    => 'button-send-comment',
                    'class' => 'btn btn-outline-primary'
                ]);
            ?>
        </div>

        <div class="form-group uk-margin-top">
            <div id="error-result" class=""></div>
        </div>
    </div>
<?php
    echo $this->Form->end();

    if ($formSecurity == 'on'):
?>
<script type="text/javascript">
    grecaptcha.ready(function() {
        grecaptcha.execute('<?= $recaptchaSitekey ?>', {action: 'ajaxVerifyForm'}).then(function(token) {
            fetch('<?= $this->Url->build(['_name' => 'ajaxVerifyForm', 'action' => 'ajaxVerifyForm', 'token' => '']); ?>' + token).then(function(response) {
                response.json().then(function(data) {
                    console.log(data);
                    var json    = $.parseJSON(data),
                        success = (json.success),
                        score   = (json.score);

                    if (success == true) {
                       $('#form-send-comment').find('input[name=status]').val('success'); 
                       $('#form-send-comment').find('input[name=score]').val(score); 
                    }
                    else {
                       $('#form-send-comment').find('input[name=status]').val('failed'); 
                       $('#form-send-comment').find('input[name=score]').val('0'); 
                    }
                });
            });
        });
    });
</script>
<?php endif; ?>
<script type="text/javascript">
	$(document).ready(function(){
		var sendComment = {
		    form            : 'form-send-comment',
		    button          : 'button-send-comment',
		    action          : 'comment',
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