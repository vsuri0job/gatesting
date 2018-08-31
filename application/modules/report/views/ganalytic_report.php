<div class="row">
    <div class="col-lg-12">
        <div class="card card-outline-info">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8 col-8 align-self-center">
                        <h4 class="m-b-0 text-white">View ( <small><?= $prodDet[ 'account_url' ] ?></small> )</h4>
                    </div>
                    <div class="col-md-4 col-4 align-self-center">
                        <?php if( $show_public_url ){
                            echo anchor( 'publicReport/'.$prodDet[ 'share_analytic_link' ], 
                                'Public Link', ' class="btn pull-right btn-outline-primary" target="_blank" ' );
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Row -->
<div id="currentMonthData">
	<?= $currMonthHtml; ?>
</div>

<div id="lastMonthsData">
	<?= $lastMonthHtml; ?>
</div>