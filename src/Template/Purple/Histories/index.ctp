<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Activity Log</h4>
            </div>
            <div class="card-toolbar">
                <button type="button" class="btn btn-gradient-primary btn-toolbar-card btn-sm btn-icon-text active button-dashboard-date-range" data-purple-modal="#modal-select-date-range">
                <i class="mdi mdi-calendar-range btn-icon-prepend"></i>
                    Filter
                </button>
            </div>
            <div class="card-body">
            	<?php
            		if ($histories->count() > 0):
            	?>
            	<ul class="uk-comment-list purple-notification-list">
                    <?php
                        foreach ($histories as $history):
                    ?>
                    <li>
                        <article class="uk-comment uk-visible-toggle post-comment-list">
                            <header class="uk-comment-header uk-position-relative">
                                <div class="uk-grid-medium uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto">
                                        <?php if ($history->admin->photo === NULL): ?>
                                        <img class="uk-comment-avatar uk-border-circle initial-photo" src="" alt="<?= $history->admin->display_name ?>" data-name="<?= $history->admin->display_name ?>" data-height="50" data-width="50" data-char-count="2" data-font-size="20">
                                        <?php else: ?>
                                        <img class="uk-border-circle" src="<?= $this->request->getAttribute("webroot") . 'uploads/images/original/' . $history->admin->photo ?>" alt="<?= $history->admin->display_name ?>" width="50" height="50" style="max-width: none;">
                                        <?php endif; ?>    
                                    </div>
                                    <div class="uk-width-expand">
                                        <h5 class="uk-comment-title uk-margin-remove">
                                            <a class="uk-link-reset" href="#">
                                                <?= $history->title ?><br><small class="text-muted">by : <?= $history->admin->display_name ?></small>
                                            </a> </h5>
                                        <p class="uk-comment-meta uk-margin-remove-top">
                                            <i class="fa fa-clock-o"></i> <a class="uk-link-reset" href="#" data-livestamp="<?= $history->created ?>"></a>
                                        </p>
                                    </div>
                                </div>
                            </header>
                            <div class="uk-comment-body">
                                <p>
                                    <?php
                                        if ($history->admin->id == $history->admin_id) {
                                            echo 'You ';
                                        } 
                                        else {
                                            echo $history->admin->display_name.' ';
                                        }

                                        echo $history->detail; 
                                    ?>   
                                </p>
                            </div>
                        </article>
                    </li>
                    <?php 
                        endforeach; 
                    ?>
            	</ul>
            	<?php
					else:
				?>
				<div class="uk-alert-danger" uk-alert>
				    <p>Can't find activity log for now.</p>
				</div>
				<?php
					endif;
				?>
            </div>
            <?php
                if ($historiesTotal > $historiesLimit):
            ?>
            <div class="card-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="uk-position-center-left">Page <?= $this->Paginator->counter() ?></div>
                        </div>
                        <div class="col-md-6">
                            <ul class="uk-pagination purple-pagination uk-margin-remove-bottom uk-flex-right">
                                <?php
                                    if ($this->Paginator->current() - 1 <= 0) {
                                        $previousUrl = [
                                            '_name'  => 'adminHistoriesPagination',
                                            'id'     => $this->Paginator->current() - 0
                                        ];
                                    }
                                    else {
                                        $previousUrl = [
                                            '_name'  => 'adminHistoriesPagination',
                                            'id'     => $this->Paginator->current() - 1
                                        ];
                                    }

                                    if ($this->Paginator->current() + 1 > $this->Paginator->total()) {
                                        $nextUrl = [
                                            '_name'  => 'adminHistoriesPagination',
                                            'id'     => $this->Paginator->current() + 0
                                        ];
                                    }
                                    else {
                                        $nextUrl = [
                                            '_name'  => 'adminHistoriesPagination',
                                            'id'     => $this->Paginator->current() + 1
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
            </div>  
            <?php endif ?>
        </div>
    </div>
</div>

<div id="modal-select-date-range" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($historyFilter, [
                'id'                    => 'form-history-filter', 
                'class'                 => 'pt-3', 
                'data-parsley-validate' => '',
                'url'                   => ['action' => 'ajax-filter']
            ]);
        ?>
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">Select Activity Log Month</h3>
        </div>
        <div class="uk-modal-body">
            <div class="form-group">
                <?php
                    echo $this->Form->select(
                        'month',
                        $monthLatinArray,
                        [
                            'empty'    => 'Select Month', 
                            'class'    => 'form-control',
                            'required' => 'required'
                        ]
                    );
                ?>
            </div>
            <div class="form-group">
                <?php
                    echo $this->Form->select(
                        'year',
                        $yearArray,
                        [
                            'empty'    => 'Select Year', 
                            'class'    => 'form-control',
                            'required' => 'required'
                        ]
                    );
                ?>
            </div>
        </div>
        <div class="uk-modal-footer uk-text-right">
            <?php
                echo $this->Form->button('Filter', [
                    'id'    => 'button-history-filter',
                    'class' => 'btn btn-gradient-primary'
                ]);

                echo $this->Form->button('Cancel', [
                    'id'           => 'button-close-modal',
                    'class'        => 'btn btn-outline-primary uk-margin-left uk-modal-close',
                    'type'         => 'button',
                    'data-target'  => '.purple-modal'
                ]);
            ?>
        </div>
        <?php
            echo $this->Form->end();
        ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        <?php
            if ($historiesTotal > $historiesLimit):
        ?>
        $('.purple-pagination .prev a').attr('href', '<?= $this->Url->build($previousUrl) ?>')
        $('.purple-pagination .next a').attr('href', '<?= $this->Url->build($nextUrl) ?>')
        <?php
            endif;
        ?>

        var filterHistory = {
            form            : 'form-history-filter',
            button          : 'button-history-filter',
            action          : 'edit',
            redirectType    : 'redirect',
            redirect        : '',
            btnNormal       : false,
            btnLoading      : '<i class="fa fa-circle-o-notch fa-spin"></i> Filtering...'
        };

        var targetButton = $("#"+filterHistory.button);
        targetButton.one('click',function() {
            var month       = $('#form-history-filter').find('select[name=month]').val(),
                year        = $('#form-history-filter').find('select[name=year]').val(),
                redirectUrl = '<?= $this->Url->build(['_name' => 'adminHistories']); ?>' + '/filter/' + year + '/' + month;

            ajaxSubmit(filterHistory.form, filterHistory.action, filterHistory.redirectType, redirectUrl, filterHistory.btnNormal, filterHistory.btnLoading);
        })
    })
</script>