<?php 
    $currentUrl = $this->request->getAttribute('params');
    
    if ($currentUrl['_name'] == 'adminPagesBlogsAdd' || $currentUrl['_name'] == 'adminPagesBlogsEdit') {
        $submitRedirect = $this->Url->build([
            '_name' => 'adminPagesDetail',
            'type'  => 'blog',
            'id'    => $this->request->getParam('id'),
            'slug'  => $this->request->getParam('slug')
        ]);
    }
    else {
        $submitRedirect = $this->Url->build([
            '_name' => 'adminBlogs'
        ]);;
    }

    $removeTagUrl = $this->Url->build([
        '_name'  => 'adminBlogsAction',
        'action' => 'ajaxRemoveTag'
    ]);;

    $permalinkUrl = $this->Url->build([
        '_name' => 'specificPost',
        'year'  => date('Y', strtotime($blogData->created)),
        'month' => date('m', strtotime($blogData->created)),
        'date'  => date('d', strtotime($blogData->created)),
        'post'  => $blogData->slug,
    ], true);

    $totalTags = $initialTags->count();
?>

<?php
    echo $this->Form->create($blogEdit, [
        'id'                    => 'form-edit-post',
        'class'                 => '',
        'data-parsley-validate' => '',
        'url'                   => ['action' => 'ajax-update']
    ]);

    echo $this->Form->hidden('id', ['value' => $blogData->id]);
    echo $this->Form->hidden('page_id', ['value' => $this->request->getParam('id')]);
    echo $this->Form->hidden('featured', ['value' => $blogData->featured]);
?>
<div class="row">
    <div class="col-md-12 grid-margin">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Permalink</h4> 
            </div>
            <div class="card-body">
                <div class="uk-inline" style="width: 100%">
                    <a title="Copy Permalink" class="uk-form-icon uk-form-icon-flip icon-copy-permalink" href="#" uk-icon="icon: copy" data-clipboard-target="#purple-permalink" uk-tooltip="title: Copy Permalink; pos: bottom"></a>
                    <input id="purple-permalink" class="uk-input" type="text" value="<?= $permalinkUrl ?>" readonly>
                </div>
                <!-- <a class="button-copy-permalink" data-clipboard-target="#target-to-copy" href="#" uk-tooltip="title: Copy Permalink"><i class="mdi mdi-content-copy"></i></a> <a href="<?= $permalinkUrl ?>" target="_blank"><span id="target-to-copy" class="copy-permalink"><?= $permalinkUrl ?></span></a> -->
            </div>
        </div>
    </div>

    <?php
        if ($totalVisitors > 0):
    ?>
    <div class="col-md-12 grid-margin">
        <div class="card">
            <div class="card-header">
                <a class="purple-toggle-card" href="#" uk-toggle="target: #readers-chart-body; animation: uk-animation-fade"><h4 class="card-title uk-margin-remove-bottom">Readers Statistic <span id="selected-date-range"></span><span class="float-right"><i class="fa fa-chevron-down"></i></span></h4></a>
            </div>
            <div id="readers-chart-body" class="card-body" hidden>
                <div class="row">
                    <div class="col-md-8">
                        <canvas id="readers-chart" class=""></canvas>
                    </div>
                    <div class="col-md-4">
                        <dl class="uk-description-list">
                            <dt><strong>Readers in Last 2 Weeks</strong></dt>
                            <dd><?= $this->Purple->plural(array_sum($totalVisitors2Weeks), 'reader', 's', true) ?></dd>

                            <dt><strong>Total Readers</strong></dt>
                            <dd><?= $this->Purple->plural($totalVisitors, 'reader', 's', true) ?></dd>

                            <dt><strong>Purple Tips</strong> <span uk-icon="info"></span></dt>
                            <dd>Share your post to social media like facebook, instagram, or twitter to get more readers. Enable the Social Sharing Buttons, so reader can share your post to social media. Do not post a duplicate content from another website, make your post unique and interesting to read.
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
        endif;
    ?>
</div>
<div class="row">
    <div class="col-md-8 grid-margin">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Post Detail</h4>
            </div>
            
            <div class="card-body">
                <div class="form-group">
                    <?php
                        echo $this->Form->text('title', [
                            'class'                  => 'form-control',
                            'placeholder'            => 'Post Title',
                            'data-parsley-minlength' => '2',
                            'data-parsley-maxlength' => '255',
                            'uk-tooltip'             => 'title: Required. 2-255 chars.; pos: bottom',
                            'autofocus'              => 'autofocus',
                            'required'               => 'required',
                            'value'                  => $blogData->title
                        ]);
                    ?>
                </div>
                <div class="form-group">
                    <?php
                        echo $this->Form->select(
                            'status',
                            [
                                '0' => 'Draft',
                                '1' => 'Publish',
                            ],
                            [
                                'empty'    => 'Select Status',
                                'class'    => 'form-control',
                                'required' => 'required'
                            ]
                        );
                    ?>
                </div>
                <div class="form-group">
                    <?php
                        echo $this->Form->select(
                            'comment',
                            [
                                'yes' => 'Allow',
                                'no'  => 'Close Comment',
                            ],
                            [
                                'empty'    => 'Allow Comment?',
                                'class'    => 'form-control',
                                'required' => 'required'
                            ]
                        );
                    ?>
                </div>
                <div class="form-group">
                    <?php
                        echo $this->Form->select(
                            'social_share',
                            [
                                'enable'  => 'Enable',
                                'disable' => 'Disable',
                            ],
                            [
                                'empty'    => 'Social Sharing Buttons',
                                'class'    => 'form-control',
                                'required' => 'required'
                            ]
                        );
                    ?>
                </div>
                <div class="form-group">
                    <?php
                        echo $this->Form->textarea('content',[
                            'class'       => 'form-control',
                            'placeholder' => 'Post Content',
                            'required'    => 'required',
                            'value'       => $blogData->content
                        ]);
                    ?>
                </div>   
                <div class="form-group">
                    <?php
                        echo $this->Form->text('tags', [
                            'class'                  => 'form-control',
                            'placeholder'            => 'Post Tags',
                            'uk-tooltip'             => 'title: Optional. Max 5 tags.; pos: bottom'
                        ]);
                    ?>
                </div>        
            </div>
            <div class="card-footer">
                <?php
                    echo $this->Form->button('Save', [
                        'id'    => 'button-edit-post',
                        'class' => 'btn btn-gradient-primary'
                    ]);

                    echo $this->Form->button('Cancel', [
                        'class'   => 'btn btn-outline-primary uk-margin-left',
                        'type'    => 'button',
                        'onclick' => 'location.href = \''.$submitRedirect.'\''
                    ]);
                ?>
            </div>
        </div>
    </div>

    <div class="col-md-4 grid-margin">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <?= $this->element('Dashboard/upload_or_browse_image', [
                        'selected'     => $blogData->featured,
                        'widgetTitle'  => 'Post Cover',
                        'inputTarget'  => 'featured',
                        'browseMedias' => $browseMedias,
                        'multiple'     => true,
                        'modalParams'  => [
                            'browseContent' => 'blogs::0',
                            'browseAction'  => 'send-to-input',
                            'browseTarget'  => 'featured'
                        ]
                ]) ?>
            </div>

            <div class="col-md-12 grid-margin">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title uk-margin-remove-bottom">Category</h4>
                    </div>
                    <div class="card-body">
                        <div id="load-blog-categories">
                            
                        </div>
                    </div>
                    <div class="card-footer">
                        <button id="button-new-post-category" type="button" class="btn btn-gradient-primary btn-sm" data-purple-modal="#modal-add-post-category">New Category</button>
                    </div>
                </div>
            </div>

            <div class="col-md-12 grid-margin">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title uk-margin-remove-bottom">SEO (Search Engine Optimization)</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <!-- <label>Meta Keywords</label> -->
                            <?php
                                echo $this->Form->text('meta_keywords', [
                                    'id'                     => 'form-input-metakeywords',
                                    'class'                  => 'form-control',
                                    'placeholder'            => 'Meta Keywords (Best practice is 10 keyword phrases)',
                                    'data-parsley-maxlength' => '100',
                                    'uk-tooltip'             => 'title: Optional. max 100 chars.; pos: bottom',
                                    'value'                  => $blogData->meta_keywords
                                ]);
                            ?>
                        </div>
                        <div class="form-group">
                            <!-- <label>Meta Description</label> -->
                            <?php
                                echo $this->Form->textarea('meta_description',[
                                    'id'                     => 'form-input-metadescription',
                                    'class'                  => 'form-control',
                                    'placeholder'            => 'Meta Description (Max 150 chars)',
                                    'data-parsley-maxlength' => '150',
                                    'rows'                   => '4',
                                    'uk-tooltip'             => 'title: Optional. max 150 chars.; pos: bottom',
                                    'value'                  => $blogData->meta_description
                                ]);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    
</div>
<?php
    echo $this->Form->end();
?>

<?= $this->element('Dashboard/Modal/add_blog_category_modal', [
        'blogCategoryAdd' => $blogCategoryAdd,
        'pageId'          => $this->request->getParam('id'),
        'afterSubmit'     => 'load',
        'loadTarget'      => '#load-blog-categories',
        'loadUrl'         => $this->Url->build(["controller" => 'BlogCategories', "action" => "ajaxLoadSelectbox"])
]) ?>

<!-- Include PDF export JS lib. -->
<?= $this->Html->script('/master-assets/plugins/html2pdf/html2pdf.bundle.js'); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $('input[name="tags"]').tagEditor({ 
            <?php
                if ($totalTags > 0):
            ?>
            initialTags: [<?php
                    $i = 1;
                    foreach ($initialTags as $tag):
                        if ($i < $totalTags) {
                            echo '"' . $tag->title . '",';
                        }
                        else {
                            echo '"' . $tag->title . '"';
                        }

                        $i++;
                    endforeach;
                ?>],
            <?php endif; ?>
            autocomplete: {
                delay: 0, 
                position: { collision: 'flip' },
                source: [<?php  
                        foreach ($tagsArray as $tags):
                            if (end($tagsArray) === $tags) {
                                echo '"' . $tags . '"';
                            }
                            else {
                                echo '"' . $tags . '", ';
                            }
                        endforeach;
                    ?>]
            },
            maxTags: 5,
            placeholder: "Tags (Max 5 tags)",
            beforeTagDelete: function(field, editor, tags, val) {
                var id    = <?= $blogData->id ?>,
                    slug  = val,
                    data  = {id:id, slug:slug},
                    url   = '<?= $removeTagUrl ?>';
                    token = $("input[name=_csrfToken]").val();

                $.ajax({
                    type: "POST",
                    url:  url,
                    headers : {
                        'X-CSRF-Token': token
                    },
                    data: data,
                    cache: false,
                    beforeSend: function() {
                        $('button').attr('disabled', 'disabled');
                    },
                    success: function(data) {
                        var json    = $.parseJSON(data),
                            status  = (json.status);
                        if (status == 'ok') {
                            console.log(slug + ' removed');
                        } 
                        else {
                            console.log("Can't remove " + slug);
                        }

                        $('button').removeAttr('disabled');
                    }
                })
            }
            
        });

        $('#form-edit-post').find('select[name=status] option[value="<?= $blogData->status ?>"]').attr("selected","selected");
        $('#form-edit-post').find('select[name=comment] option[value="<?= $blogData->comment ?>"]').attr("selected","selected");
        $('#form-edit-post').find('select[name=social_share] option[value="<?= $blogData->social_share ?>"]').attr("selected","selected");

        var token                  = $("input[name=_csrfToken]").val(),
            froalaManagerLoadUrl   = $("#froala-load-url").val(),
            froalaImageUploadUrl   = $("#froala-image-upload-url").val(),
            froalaFileUploadUrl    = $("#froala-file-upload-url").val(),
            froalaVideoUploadUrl   = $("#froala-video-upload-url").val();

        $('textarea[name=content]').froalaEditor({
            theme: 'royal',
            height: 400,
            toolbarStickyOffset: 70,
            charCounterCount: false,
            placeholderText: 'Post content here...',
            enter: $.FroalaEditor.ENTER_DIV,
            imageManagerLoadURL: froalaManagerLoadUrl,
            imageUploadURL: froalaImageUploadUrl,
            fileUploadURL: froalaFileUploadUrl,
            videoUploadURL: froalaVideoUploadUrl,
            imageMaxSize: 3 * 1024 * 1024,
            imageAllowedTypes: ['jpeg', 'jpg', 'png'],
            fileMaxSize: 5 * 1024 * 1024,
            fileAllowedTypes: ['*'],
            videoMaxSize: 20 * 1024 * 1024,
            videoAllowedTypes: ['mp4', 'm4v', 'ogg', 'webm'],
            requestHeaders: {
                'X-CSRF-Token': token
            }
        })

        var url = '<?= $this->Url->build(["controller" => 'BlogCategories', "action" => "ajaxLoadSelectbox"]); ?>';

        $.ajax({
            type: "POST",
            url:  url,
            headers : {
                'X-CSRF-Token': token
            },
            data: {page: '<?php if ($this->request->getParam('id') == NULL) echo 'NULL'; else echo $this->request->getParam('id') ?>'},
            cache: false,
            beforeSend: function() {
            },
            success: function(data) {
                $('#load-blog-categories').html(data);
                $('#form-edit-post').find('select[name=blog_category_id] option[value="<?= $blogData->blog_category_id ?>"]').attr("selected","selected");
            }
        })

        <?php
            if ($totalVisitors > 0):
        ?>
        if ($("#readers-chart").length) {
            <?php
                $firstDay = ucwords(strtolower($visitorsDays[0]));
                $lastDay  = ucwords(strtolower($visitorsDays[13]));
            ?>
            var firstDay = "<?= $firstDay ?>";
            var lastDay  = "<?= $lastDay ?>";

            $("#selected-date-range").html(firstDay + ' - ' + lastDay);

            Chart.defaults.global.legend.labels.usePointStyle = true;
            var ctx = document.getElementById('readers-chart').getContext("2d");

            var gradientStrokeViolet = ctx.createLinearGradient(0, 0, 0, 181);
            gradientStrokeViolet.addColorStop(0, 'rgba(218, 140, 255, 1)');
            gradientStrokeViolet.addColorStop(1, 'rgba(154, 85, 255, 1)');
            var gradientLegendViolet = 'linear-gradient(to right, rgba(218, 140, 255, 1), rgba(154, 85, 255, 1))';
          
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [
                        <?php 
                            foreach($visitorsDays as $last2Weeks) { 
                                echo "'".$last2Weeks."',";
                            } 
                        ?>
                    ],
                    datasets: [
                    {
                        label: "Readers",
                        borderColor: gradientStrokeViolet,
                        backgroundColor: gradientStrokeViolet,
                        hoverBackgroundColor: gradientStrokeViolet,
                        legendColor: gradientLegendViolet,
                        pointRadius: 0,
                        fill: false,
                        borderWidth: 1,
                        fill: 'origin',
                        data: [
                            <?php 
                                foreach($totalVisitors2Weeks as $totalVisitors) { 
                                    echo $totalVisitors.",";
                                } 
                            ?>
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    legend: false,
                    legendCallback: function(chart) {
                        var text = []; 
                        text.push('<ul>'); 
                        for (var i = 0; i < chart.data.datasets.length; i++) { 
                            text.push('<li><span class="legend-dots" style="background:' + 
                                       chart.data.datasets[i].legendColor + 
                                       '"></span>'); 
                            if (chart.data.datasets[i].label) { 
                                text.push(chart.data.datasets[i].label); 
                            } 
                            text.push('</li>'); 
                        } 
                        text.push('</ul>'); 
                        return text.join('');
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                display: false,
                                min: 0,
                                stepSize: <?php if (max($totalVisitors2Weeks) == 0) echo '10'; else { $maxAxis = round((max($totalVisitors2Weeks) + 200) / 10); echo $maxAxis; }  ?>,
                                max: <?php if (max($totalVisitors2Weeks) == 0) echo '50'; else { $maxAxis = max($totalVisitors2Weeks) + 200; echo $maxAxis; }  ?>
                        },
                        gridLines: {
                            drawBorder: false,
                            color: 'rgba(235,237,242,1)',
                            zeroLineColor: 'rgba(235,237,242,1)'
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            display:false,
                            drawBorder: false,
                            color: 'rgba(0,0,0,1)',
                            zeroLineColor: 'rgba(235,237,242,1)'
                        },
                        ticks: {
                            padding: 10,
                            fontColor: "#9c9fa6",
                            autoSkip: true,
                        },
                        categoryPercentage: 0.5,
                        barPercentage: 0.5
                    }]
                }
            },
                elements: {
                    point: {
                        radius: 0
                    }
                }
            })
             
            $("#readers-chart").html(myChart.generateLegend());
        }
        <?php
            endif;
        ?>

        var categoryAdd = {
            form            : 'form-add-post-category',
            button          : 'button-add-post-category',
            action          : 'ajax-load-post-categories',
            redirectType    : 'ajax',
            redirect        : '#load-blog-categories',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+categoryAdd.button);
        targetButton.one('click',function() {
            ajaxSubmit(categoryAdd.form, categoryAdd.action, categoryAdd.redirectType, categoryAdd.redirect, categoryAdd.btnNormal, categoryAdd.btnLoading);
        })

        var blogEdit = {
            form            : 'form-edit-post',
            button          : 'button-edit-post',
            action          : 'edit',
            redirectType    : 'redirect',
            redirect        : '<?= $submitRedirect; ?>',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+blogEdit.button);
        targetButton.one('click',function() {
            ajaxSubmit(blogEdit.form, blogEdit.action, blogEdit.redirectType, blogEdit.redirect, blogEdit.btnNormal, blogEdit.btnLoading, true, true);
        })
    })
</script>