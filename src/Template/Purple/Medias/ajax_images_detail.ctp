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
            $thumbSquare = $this->cell('Medias::mediaPath', [$media->name, 'image', 'thumbnail::300']);
            $fullImage   = $this->cell('Medias::mediaPath', [$media->name, 'image', 'original']);
            $previousId  = $this->cell('Medias::previousId', [$media->id]);
            $nextId      = $this->cell('Medias::nextId', [$media->id]);

            if ($previousId == '0') {
                $previousUrl = '#';
            }
            else {
                $previousUrl = $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => $this->request->getParam('action')]) . '?id=' . $previousId;
            }

            if ($nextId == '0') {
                $nextUrl = '#';
            }
            else {
                $nextUrl = $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => $this->request->getParam('action')]) . '?id=' . $nextId;
            }

            $colorsUrl = $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => 'ajaxGetImageColors']);
    ?>
    <div class="col-6 col-md-2 grid-margin" data-date="<?= date('Y-m-d H:i', strtotime($media->created)) ?>">
        <div>
            <div class="uk-card uk-card-default">
                <div class="uk-card-media-top">
                    <a class="media-link-to-image" href="#modal-full-content" data-purple-id="<?= $media->id ?>" data-purple-by="<?= ucwords($media->admin->display_name) ?>" data-purple-host="<?= $this->request->host() ?>" data-purple-image="<?= $fullImage ?>" data-purple-created="<?= date('F d, Y H:i', strtotime($media->created)) ?>"  data-purple-next-url="<?= $nextUrl ?>" data-purple-previous-url="<?= $previousUrl ?>" data-purple-colors-url="<?= $colorsUrl ?>" title="<?= $media->title ?>" data-purple-description="<?= $media->description ?>"><?= $this->Html->image($thumbSquare, ['alt' => $media->title, 'width' => '100%']) ?></a>
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

        function lightOrDark(color) {
            // Variables for red, green, blue values
            var r, g, b, hsp;
            // Check the format of the color, HEX or RGB?
            if (color.match(/^rgb/)) {
                // If HEX --> store the red, green, blue values in separate variables
                color = color.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+(?:\.\d+)?))?\)$/);
                
                r = color[1];
                g = color[2];
                b = color[3];
            } 
            else {
                // If RGB --> Convert it to HEX: http://gist.github.com/983661
                color = +("0x" + color.slice(1).replace( 
                color.length < 5 && /./g, '$&$&'));
        
                r = color >> 16;
                g = color >> 8 & 255;
                b = color & 255;
            }
            
            // HSP (Highly Sensitive Poo) equation from http://alienryderflex.com/hsp.html
            hsp = Math.sqrt(
            0.299 * (r * r) +
            0.587 * (g * g) +
            0.114 * (b * b)
            );
        
            // Using the HSP value, determine whether the color is light or dark
            if (hsp>127.5) {
                return 'light';
            } 
            else {
                return 'dark';
            }
        }

        $(".media-link-to-image").click(function () {
            var id       = $(this).data('purple-id'),
                image    = $(this).data('purple-image'),
                host     = $(this).data('purple-host'),
                by       = $(this).data('purple-by'),
                created  = $(this).data('purple-created'),
                colors   = $(this).data('purple-colors'),
                desc     = $(this).data('purple-description'),
                title    = $(this).attr('title'),
                colorUrl = $(this).attr('data-purple-colors-url'),
                token    = $('#csrf-ajax-token').val(),
                modal    = $("#modal-full-content");

            modal.find(".bind-background").css('background-color', '#ffffff');
            modal.find('.bind-colors').html('<i class="fa fa-circle-o-notch fa-spin"></i> Getting image colors...');

            $.ajax({
                type: "POST",
                url:  colorUrl,
                headers : {
                    'X-CSRF-Token': token
                },
                data: { image:image },
                cache: false,
                success: function(data){
                    // Image colors
                    var colorsArray = data.split(","),
                        i, setColors = '';
                    for (i = 0; i < colorsArray.length; i++) {
                        setColors += '<a href="#" class="uk-margin-small-right" style="color: ' + colorsArray[i] + '" title="' + colorsArray[i] + '"><i class="fa fa-square"></i></a>';
                    }

                    setTimeout(() => {
                        modal.find(".bind-colors").html(setColors);
                        if (colorsArray.length <= 3) {
                            if (lightOrDark(colorsArray[0]) == 'light') {
                                modal.find(".bind-background").css('background-color', '#0e0e0e');
                            }
                            else {
                                modal.find(".bind-background").css('background-color', '#ffffff');
                            }
                        }
                        else {
                            modal.find(".bind-background").css('background-color', colorsArray[0]);
                        }
                    }, 1000);
                }
            })
            
            modal.find(".uk-background-contain").css('background-image', 'url(' + image + ')');
            modal.find("form input[name=id]").val(id);
            modal.find("form input[name=title]").val(title);
            modal.find("form textarea[name=description]").val(desc);
            modal.find("form input[name=path]").val(image);
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

            $('#modal-full-content').find('.bind-background').on({
            mouseenter: function () {
                var modal   = $('#modal-full-content');
                var prevUrl = modal.find('#media-image-previous-url').attr('href');
                var nextUrl = modal.find('#media-image-next-url').attr('href');
                if (prevUrl != '#') {
                    modal.find('#media-image-previous-url').show(500);
                }
                if (nextUrl != '#') {
                    modal.find('#media-image-next-url').show(500);
                }
            },
            mouseleave: function () {
                var modal = $('#modal-full-content');
                modal.find('#media-image-previous-url').hide(500);
                modal.find('#media-image-next-url').hide(500);
            }
        });
            
            return false;
        });
    })
</script>
<?php endif; ?>