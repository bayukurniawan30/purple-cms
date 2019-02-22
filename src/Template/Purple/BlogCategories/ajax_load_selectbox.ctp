<div class="form-group">
	<?php
        echo $this->Form->select(
            'blog_category_id',
            $blogCategoriesArray,
            [
                'empty'    => 'Select Category',
                'class'    => 'form-control',
                'required' => 'required'
            ]
        );
    ?>
</div>