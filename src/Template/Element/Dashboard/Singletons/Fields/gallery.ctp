<?php
    $randomModalBrowseImageId = rand(100000, 999999);

    if (isset($value)) {
        $selectedImages     = [];
        $selectedImagesPath = [];
        foreach ($value as $fp) {
            $explodeFullPathImage = explode('/', $fp['full_path']);
            $imageName = end($explodeFullPathImage);

            array_push($selectedImages, $imageName);
            array_push($selectedImagesPath, $fp['full_path']);
        }

        $image   = implode(',', $selectedImages);
        $imageFp = implode(',', $selectedImagesPath);
    }
    else {
        $image   = NULL;
        $imageFp = NULL;
    }

    $defaultOptions = [
        'selected'      => $image,
        'widgetTitle'   => $label,
        'inputTarget'   => $uid,
        'browseMedias'  => $browseMedias,
        'multiple'      => true,
        'uniqueId'      => $randomModalBrowseImageId,
        'modalParams'   => [
            'browseContent' => 'singletons::0',
            'browseAction'  => 'send-to-input',
            'browseTarget'  => $uid . '\\[value\\]'
        ],
    ];

    $default  = collection($defaultOptions);
    $newArray = $default->append($options);
?>
<div class="card">
    <div class="card-header">
        <h4 class="card-title uk-margin-remove-bottom"><?= $label ?></h4>
    </div>
    <div class="card-body">
        <div class="uk-alert-primary uk-margin-medium-bottom" uk-alert>
            <a class="uk-alert-close" uk-close></a>
            <p>
                You can upload or select multiple images.
            </p>
        </div>

        <div class="form-group">
            <?= $this->Form->hidden($uid . '.field_type', ['value' => $field_type]) ?>
            <?= $this->Form->hidden($uid . '.value', ['value' => $image, 'data-purple-path' => $imageFp]) ?>
            <button type="button" class="btn btn-gradient-primary btn-sm btn-icon-text btn-block button-browse-images" data-purple-target="#modal-browse-images-<?= $randomModalBrowseImageId ?>" data-purple-browse-content="singletons::0" data-purple-browse-action="send-to-input" data-purple-browse-target="<?= $uid . '\\[value\\]' ?>">
                <i class="mdi mdi-file-find btn-icon-prepend"></i>
                Browse Uploaded Image
            </button>

            <div class="browse-image-preview-<?= $randomModalBrowseImageId ?> uk-margin-top"></div>
        </div>
    </div>
</div>

<?= $this->element('Dashboard/Modal/browse_images_modal', $newArray->toArray()) ?>

<?php
    if (isset($value)):
?>
<script type="text/javascript">
    $(document).ready(function() {
        var newValue    = '<?= $imageFp ?>';
        var split       = newValue.split(",");
        var arrayLength = split.length;
        var init        = '';
        var getPreviewWidth = $('.browse-image-preview-<?= $randomModalBrowseImageId ?>').outerWidth();

        if (getPreviewWidth < 768) {
            for (var i = 0; i < arrayLength; i++) {
                init += '<li><img src="' + split[i] + '" alt="' + split[i] + '" uk-cover></li>';
            }

            var slideshow = '<div class="uk-padding" style="background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAYAAACp8Z5+AAAAIklEQVQYV2NkQAJXrlz5zwjjgzg6OjqMYAEYB8RmROaABAD+tQ+ACU9dmwAAAABJRU5ErkJggg==)"><div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1" uk-slideshow>' +
                        '<ul class="uk-slideshow-items">' + init +
                        '</ul>' +
                        '<a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a>' +
                        '<a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slideshow-item="next"></a>' +
                    '</div></div>';
            $('.browse-image-preview-<?= $randomModalBrowseImageId ?>').html(slideshow);
        }
        else {
            for (var i = 0; i < arrayLength; i++) {
                var newFilePath = split[i].replace("original/", "thumbnails/300x300/");
                init += '<div><a href="' + newFilePath + '"><img src="' + newFilePath + '" alt="' + split[i] + '" ></a></div>';
            }

            var grid = '<div class="uk-padding" style="background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAYAAACp8Z5+AAAAIklEQVQYV2NkQAJXrlz5zwjjgzg6OjqMYAEYB8RmROaABAD+tQ+ACU9dmwAAAABJRU5ErkJggg==)"><div class="uk-child-width-1-1 uk-child-width-1-6@m" uk-grid uk-lightbox>' + init + '</div></div>';
            $('.browse-image-preview-<?= $randomModalBrowseImageId ?>').html(grid);
        }
    })
</script>
<?php
    endif;
?>