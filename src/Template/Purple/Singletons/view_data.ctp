<?php
    $submitRedirect = $this->Url->build([
        '_name' => 'adminSingletonsViewData',
        'data'  => $singleton->slug
    ]);

    $addDataUrl = $this->Url->build([
        '_name' => 'adminSingletonsData',
        'data'  => $singleton->slug
    ]);

    $apiEndpointUrl = $this->Url->build([
        '_name' => 'apiv1ViewSingletonnDatas',
        'slug'  => $singleton->slug
    ], true);
?>

<?= $this->Flash->render('flash', [
    'element' => 'Flash/Purple/success'
]); ?>

<div class="row">
    <div class="col-md-12 uk-margin-bottom">
        <?php
            if ($singletonDatas->count() == 0):
        ?>
        <button type="button" class="btn btn-gradient-primary btn-toolbar-card btn-sm btn-icon-text uk-margin-right" onclick="location.href='<?= $newSingletonUrl ?>'">
        <i class="mdi mdi-pencil btn-icon-prepend"></i>
            Add Data
        </button>
        <?php
            endif;
        ?>
        <button type="button" class="btn btn-gradient-success btn-toolbar-card btn-sm btn-icon-text" uk-toggle="target: #modal-api-endpoint">
        <i class="mdi mdi-link-variant btn-icon-prepend"></i>
            API Endpoint
        </button>
    </div>
    <div class="col-md-12 grid-margin">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Data in <?= $singleton->name ?></h4> 
            </div>
            <div class="card-body <?php if ($singletonDatas->count() == 0) echo 'uk-padding-remove'; ?>">
                <?php
	            	if ($singletonDatas->count() > 0):
                        $defaultTableHeaders = [
                            ['No' => ['width' => '30']]
                        ];

                        $decodeFields = json_decode($singleton->fields, true);
                        if (count($decodeFields) > 0):
                            $n = 1;
                            foreach ($decodeFields as $field):
                                $decodeField = json_decode($field, true);
                                array_push($defaultTableHeaders, $decodeField['label']);

                                if ($n == 4) break;

                                $n++;
                            endforeach;

                            array_push($defaultTableHeaders, ['Action' => ['class' => 'uk-width-small text-center']]);
                        endif;

	            ?>
                <div class="uk-overflow-auto">
    	            <table class="uk-table uk-table-justify uk-table-divider uk-table-middle purple-datatable">
    				    <thead>
    				        <?php
    				            echo $this->Html->tableHeaders($defaultTableHeaders);
    				        ?>
    				    </thead>
    				    <tbody> 
                            <?php
                                $i = 1;
                                foreach ($singletonDatas as $data):
                                    $editDataUrl = $editPostUrl = $this->Url->build([
                                        '_name' => 'adminSingletonsEditData',
                                        'data'  => $singleton->slug,
                                        'id'    => $data->id,
    				                ]);

                                    $decodeContent = json_decode($data->content, true);
                                    echo '<tr>';
                                    echo '<td>' . $i . '</td>';

                                    $j = 1;
                                    $decodeFields = json_decode($singleton->fields, true);
                                    foreach ($decodeFields as $field):
                                        $decodeField = json_decode($field, true);
                                        if ($decodeContent[$decodeField['uid']]['field_type'] == 'text' ||
                                            $decodeContent[$decodeField['uid']]['field_type'] == 'textarea' ||
                                            $decodeContent[$decodeField['uid']]['field_type'] == 'time' ||
                                            $decodeContent[$decodeField['uid']]['field_type'] == 'date'
                                        ) {
                                            echo '<td>' . $decodeContent[$decodeField['uid']]['value'] . '</td>';
                                        }
                                        elseif ($decodeContent[$decodeField['uid']]['field_type'] == 'html') {
                                            echo '<td>' . $this->Text->truncate(
                                                html_entity_decode($decodeContent[$decodeField['uid']]['value']),
                                                50,
                                                [
                                                    'ellipsis' => '...',
                                                    'exact' => false,
                                                    'html' => true
                                                ]
                                        ) . '</td>';
                                        }
                                        elseif ($decodeContent[$decodeField['uid']]['field_type'] == 'image') {
                                            echo '<td><img src="' . $decodeContent[$decodeField['uid']]['value']['thumbnail']['300x300'] . '" alt="' . $decodeContent[$decodeField['uid']]['value']['thumbnail']['300x300'] . '" width="50" ></td>';
                                        }
                                        elseif ($decodeContent[$decodeField['uid']]['field_type'] == 'gallery') {
                                            echo '<td><div class="uk-child-width-expand@s uk-text-center uk-grid-small" uk-grid>';
                                            $totalImage   = 1;
                                            $totalGallery = count($decodeContent[$decodeField['uid']]['value']);
                                            $moreImages   = $totalGallery - 2;
                                            foreach ($decodeContent[$decodeField['uid']]['value'] as $gallery) {
                                                if ($totalImage < 3) {
                                                    echo '<div><img src="' . $gallery['thumbnail']['300x300'] . '" alt="' . $gallery['thumbnail']['300x300'] . '" width="100%"></div>';
                                                }
                                                else {
                                                    echo '<div><div class="uk-inline"><img src="' . $gallery['thumbnail']['300x300'] . '" alt="' . $gallery['thumbnail']['300x300'] . '" width="100%"><div class="uk-position-cover uk-overlay uk-overlay-primary uk-text-large uk-flex uk-flex-center uk-flex-middle">' . $moreImages . '</div></div></div>';
                                                }
                                                
                                                $totalImage++;

                                                if ($totalImage == 4) break;
                                            }
                                            echo '</div></td>';
                                        }
                                        elseif ($decodeContent[$decodeField['uid']]['field_type'] == 'boolean') {
                                            if ($decodeContent[$decodeField['uid']]['value'] == '1') {
                                                echo '<td>True</td>';
                                            }
                                            else {
                                                echo '<td>False</td>';
                                            }
                                        }
                                        elseif ($decodeContent[$decodeField['uid']]['field_type'] == 'link') {
                                            if ($decodeContent[$decodeField['uid']]['value']['target'] == '_self') {
                                                $openLinkIn = '(Open in current tab)';
                                            }
                                            elseif ($decodeContent[$decodeField['uid']]['value']['target'] == '_blank') {
                                                $openLinkIn = '(Open in new tab)';
                                            }

                                            echo '<td>' . $decodeContent[$decodeField['uid']]['value']['url'] . ' ' . $openLinkIn .  '</td>';
                                        }
                                        elseif ($decodeContent[$decodeField['uid']]['field_type'] == 'tags') {
                                            echo '<td>';
                                            if (strpos($decodeContent[$decodeField['uid']]['value'], ',') !== false) {
                                                $explodeTags = explode(',', $decodeContent[$decodeField['uid']]['value']);
                                            }
                                            else {
                                                $explodeTags = [$decodeContent[$decodeField['uid']]['value']];
                                            }

                                            foreach ($explodeTags as $tag) {
                                                $marginTag = '';
                                                if (end($explodeTags) !== $tag) {
                                                    $marginTag = ' uk-margin-small-right';
                                                }
                                                echo '<span class="uk-label label-purple ' . $marginTag . '">' . $tag . '</span>';
                                            }
                                            echo '</td>';
                                        }
                                        else {
                                            echo '<td>' . $decodeContent[$decodeField['uid']]['value'] . '</td>';
                                        }

                                        if ($j == 4) break;

                                        $j++;
                                    endforeach;

                                    echo '<td class="text-center">
                                        <button type="button" class="btn btn-gradient-primary btn-rounded btn-icon" uk-tooltip="Edit Data" onclick="location.href=\'' . $editDataUrl . '\'">
                                            <i class="mdi mdi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-gradient-danger btn-rounded btn-icon button-delete-purple" uk-tooltip="Delete Data" data-purple-id="' . $data->id . '" data-purple-name="selected data in '. $singleton->name . ' Singleton" data-purple-modal="#modal-delete-singleton-data">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </td>';
                                    echo '</tr>';

                                    $i++;
                                endforeach;
    				        ?>
                        </tbody>
    				</table>
                </div>
	            <?php
				    else:
				?>  
				<div class="uk-alert-danger <?php if ($singletonDatas->count() == 0) echo 'uk-margin-remove-bottom'; ?>" uk-alert>
				    <p>Can't find data for this singleton. You can add a new data <a href="<?= $addDataUrl ?>" class="">here</a>.</p>
				</div>
				<?php
				    endif;
				?>
            </div>
        </div>
    </div>
</div>

<?php
    if ($singletonDatas->count() > 0):
        echo $this->element('Dashboard/Modal/delete_modal', [
            'action'     => 'singleton-data',
            'form'       => $singletonDataDelete,
            'formAction' => 'ajax-delete-data'
        ]);
    endif;

    echo $this->element('Dashboard/Modal/api_endpoint_modal', [
        'url'         => $apiEndpointUrl,
        'apiResponse' => $apiResult
    ]);
?>

<script type="text/javascript">
    $(document).ready(function() {
    	<?php
            if ($singletonDatas->count() > 0):
        ?>
        var dataTable = $('.purple-datatable').DataTable({
            "columnDefs": [{
                "targets": -1,
                "orderable": false
            }]
        });

        var dataDelete = {
            form            : 'form-delete-singleton-data',
            button          : 'button-delete-singleton-data',
            action          : 'delete',
            redirectType    : 'redirect',
            redirect        : '<?= $submitRedirect ?>',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+dataDelete.button);
        targetButton.one('click',function() {
            ajaxSubmit(dataDelete.form, dataDelete.action, dataDelete.redirectType, dataDelete.redirect, dataDelete.btnNormal, dataDelete.btnLoading);
        })
        <?php
            endif;
        ?>
    })
</script>