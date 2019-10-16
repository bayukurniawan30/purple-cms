<div class="form-group">
	<?php
        echo $this->Form->label('blog_category_id', 'Category');
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