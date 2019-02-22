<div class="p-3">
    <h4 class="font-italic">Search</h4>
    
    <?php
	    echo $this->Form->create($sidebarSearch, [
	        'id'                    => 'form-search',
	        'class'                 => 'uk-grid-small form-search',
	        'data-parsley-validate' => '',
	        'url'                   => ['controller' => 'Search', 'action' => 'index'],
	        'uk-grid'               => ''
	    ]);
	?>
	<div class="uk-width-1-1">
		<div class="uk-form-controls">
			<?php
	            echo $this->Form->text('search', [
	                'class'                  => 'uk-input',
	                'placeholder'            => 'Search',
	                'data-parsley-minlength' => '2',
	                'data-parsley-maxlength' => '100',
	                'value'					 => '',
	                'required'               => 'required'
	            ]);
	        ?>
		</div>
	</div>
	<?php
	    echo $this->Form->end();
    ?>
</div>