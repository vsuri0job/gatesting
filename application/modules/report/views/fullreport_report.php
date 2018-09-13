<div class="row">
    <div class="col-lg-12">
        <div class="card card-outline-info">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8 col-8 align-self-center">
                        <h4 class="m-b-0 text-white"><?=$profDet['account_url']?></h4>
                    </div>
                    <div class="col-md-4 col-4 align-self-center">
<?php if ($show_public_url) {
	echo anchor('publicReport/' . $profDet['share_full_link'],
		'Public Link', ' class="btn pull-right btn-outline-primary" target="_blank" ');
}?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Row -->
    <div class="m-b-10">
        <?php 
        if( $profDet[ 'view_id' ] 
            || $profDet[ 'linked_adwords_acc_id' ] 
            || $profDet[ 'linked_google_page_location' ] ){
            echo $overview_report; 
        }
        ?>
    </div>


    <div class="m-b-10">
        <?php 
        if( $profDet[ 'view_id' ]){
            echo '<h4 class="m-b-10">SEO Report</h4>';
            echo $ganalytic_report;
        }
        ?>
    </div>


    <div class="m-b-10">
        <?php 
        if( $profDet[ 'linked_adwords_acc_id' ]){
            echo '<h4 class="m-b-10">Adwords Report</h4>';
            echo $gad_report;
        }
        ?>
    </div>

    <div class="m-b-10">
        <?php 
        if( $profDet[ 'linked_google_page_location' ]){
            echo '<h4 class="m-b-10">Google My Business Report</h4>';
            echo $gmb_report;
        }
        ?>
    </div>

    <div class="m-b-10">
        <?php 
        if( $profDet[ 'linked_rankinity_id' ]){
            echo '<h4 class="m-b-10">Rankinity Report</h4>';
            echo $rankinity_report;
        }
        ?>
    </div>

    <div class="m-b-10">
        <?php 
        if( $profDet[ 'linked_account_id' ]){
            echo '<h4 class="m-b-10">Citation Report</h4>';
            echo $citation_content;
        }
        ?>
    </div>

    <div class="m-b-10">
        <?php 
        if( $profDet[ 'linked_webmaster_site' ]){
            echo '<h4 class="m-b-10">Google Search Console Report</h4>';
            echo $gwmaster_report;
        }
        ?>
    </div>

    <div class="m-b-10">
        <?php 
        if( $profDet[ 'linked_trello_board_id' ] && $tboard_cards){
            echo '<h4 class="m-b-10">Trello Report</h4>';
            echo $tboard_report;
        }
        ?>
    </div>