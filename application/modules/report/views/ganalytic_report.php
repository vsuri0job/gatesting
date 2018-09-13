<?php if (!isset($full_report_show) || !$full_report_show) {
	?>
            <div class="row" style="margin-top:-50px">
                <div class="col-lg-6"></div>
                <div class="col-lg-6 text-right">
                    <?php if( isset( $report_setting[ 'report_logo' ] ) 
                    && $report_setting[ 'report_logo' ]){ 
                        $iattr = array();
                        $iattr[ 'style' ] = ' width="100px" height="100px" ';
                        $iattr[ 'accept' ] = ' image/png,image/gif,image/jpg ';
                        echo img("/uploads/report_logo/".$report_setting[ 'report_logo' ], "", $iattr);
                    } ?>            
                </div>
            </div>
<div class="row">    
    <div class="col-lg-12">
        <div class="card card-outline-info">
            <div class="card-header">                
                <div class="row">
                    <div class="col-md-8 col-8 align-self-center">
                        <h4 class="m-b-0 text-white"><?=$prodDet['account_url']?></h4>
                    </div>
                    <div class="col-md-4 col-4 align-self-center">
                        <?php if ($show_public_url) {
		echo anchor('publicReport/' . $prodDet['share_analytic_link'],
			'Public Link', ' class="btn pull-right btn-outline-primary" target="_blank" ');
	}?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php }?>
<!-- Row -->
<div id="currentMonthDataAnalytic">
	<?=$currMonthHtml;?>
</div>

<div id="lastMonthsDataAnalytic">
	<?=$lastMonthHtml;?>
</div>