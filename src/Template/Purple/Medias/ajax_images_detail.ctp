<?php 
    if ($medias->count() > 0):
?>
<!-- <div class="row" style="margin-left: 0; margin-right: 0; width: 100%">
    <div class="col-md-12 grid-margin">
       <ul class="" uk-tab>
            <li class="uk-active" uk-filter-control="sort: data-date"><a href="#"><span uk-icon="arrow-down"></span></a></li>
            <li uk-filter-control="sort: data-date; order: desc"><a href="#"><span uk-icon="arrow-up"></span></a></li>
        </ul>
    </div>
</div> -->
<div id="bind-media-load" class="js-filter row">
    <?php foreach ($medias as $media): ?>
    <?php
            $thumbSquare = '/uploads/images/thumbnails/300x300/' . $media->name;
            $fullImage   = $this->request->getAttribute("webroot") . 'uploads/images/original/' . $media->name;
    ?>
    <div class="col-6 col-md-2 grid-margin" data-date="<?= date('Y-m-d H:i', strtotime($media->created)) ?>">
        <div>
            <div class="uk-card uk-card-default">
                <div class="uk-card-media-top">
                    <a class="media-link-to-image" href="#modal-full-content" data-purple-id="<?= $media->id ?>" data-purple-by="<?= ucwords($media->admin->display_name) ?>" data-purple-host="<?= $this->request->host() ?>" data-purple-image="<?= $fullImage ?>" data-purple-created="<?= date('F d, Y H:i', strtotime($media->created)) ?>" title="<?= $media->title ?>" data-purple-description="<?= $media->description ?>"><?= $this->Html->image($thumbSquare, ['alt' => $media->title, 'width' => '100%']) ?></a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <?php
        if ($mediaImageTotal > $mediaImageLimit):
    ?>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-6">
                <div class="">Page <?= $this->Paginator->counter() ?></div>
            </div>
            <div class="col-md-6">
                <ul class="uk-pagination purple-pagination uk-flex-right">
                    <?php
                        if ($this->Paginator->current() - 1 <= 0) {
                            $previousUrl = [
                                '_name' => 'adminMediasImagesPagination',
                                'id'    => $this->Paginator->current() - 0
                            ];
                        }
                        else {
                            $previousUrl = [
                                '_name' => 'adminMediasImagesPagination',
                                'id'    => $this->Paginator->current() - 1
                            ];
                        }

                        if ($this->Paginator->current() + 1 > $this->Paginator->total()) {
                            $nextUrl = [
                                '_name' => 'adminMediasImagesPagination',
                                'id'    => $this->Paginator->current() + 0
                            ];
                        }
                        else {
                            $nextUrl = [
                                '_name' => 'adminMediasImagesPagination',
                                'id'    => $this->Paginator->current() + 1
                            ];
                        }

                        echo $this->Paginator->prev('<span uk-pagination-previous class="uk-margin-small-right"></span> Previous', [
                            'escape' => false,
                        ]);
                        // echo $this->Paginator->numbers();
                        echo $this->Paginator->next('Next <span uk-pagination-next class="uk-margin-small-left"></span>', [
                            'escape' => false,
                        ]);
                    ?>
                </ul>
            </div>
        </div>
    </div>
    <?php
        endif;
    ?>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // UIkit.filter("#uk-filtering");

        <?php
            if ($mediaImageTotal > $mediaImageLimit):
        ?>
        $('.purple-pagination .prev a').attr('href', '<?= $this->Url->build($previousUrl) ?>')
        $('.purple-pagination .next a').attr('href', '<?= $this->Url->build($nextUrl) ?>')
        <?php
            endif;
        ?>

        $(".media-link-to-image").click(function () {
            var id      = $(this).data('purple-id'),
                image   = $(this).data('purple-image'),
                host    = $(this).data('purple-host'),
                by      = $(this).data('purple-by'),
                created = $(this).data('purple-created'),
                desc    = $(this).data('purple-description'),
                title   = $(this).attr('title'),
                modal   = $("#modal-full-content");
            modal.find(".uk-background-contain").css('background-image', 'url(' + image + ')');
            modal.find("form input[name=id]").val(id);
            modal.find("form input[name=title]").val(title);
            modal.find("form textarea[name=description]").val(desc);
            modal.find("form input[name=path]").val(host + image);
            modal.find(".bind-created").html('Uploaded at ' + created);
            modal.find(".bind-by").html('Uploaded by ' + by);
            modal.find("form .button-delete-media-image").attr('data-id', id);
            UIkit.modal('#modal-full-content').show();
            
            var clipboard   = new ClipboardJS('#button-clipboard-js'),
                targetLabel = modal.find("form label[for=path]").html();

            clipboard.on('success', function(e) {
                console.info('Action:', e.action);
                console.info('Text:', e.text);
                console.info('Trigger:', e.trigger);
                modal.find("form label[for=path]").html(targetLabel + ' <span class="text-primary">Copied</span>');
                setTimeout(function() {
                    modal.find("form label[for=path]").html('URL');
                }, 2500);
                e.clearSelection();
            });

            clipboard.on('error', function(e) {
                console.error('Action:', e.action);
                console.error('Trigger:', e.trigger);
                modal.find("form label[for=path]").html(targetLabel + ' <span class="text-danger">Error. Text is not copied</span>');
                setTimeout(function() {
                    modal.find("form label[for=path]").html('URL');
                }, 2500);
            });
            
            modal.find("form .button-delete-media-image").on("click", function() {
                UIkit.modal('#modal-full-content').hide();
                setTimeout(function() {
                    var deleteModal = $("#modal-delete-media"),
                        deleteForm  = deleteModal.find("form"),
                        deleteID    = deleteForm.find("input[name=id]"),
                        deleteTitle = deleteForm.find(".bind-title");
                    
                    deleteID.val(id);
                    deleteTitle.html(title);
                    
                    UIkit.modal('#modal-delete-media').show();
                }, 500);
                return false;
            });
            
            return false;
        });
    })
</script>
<?php endif; ?>