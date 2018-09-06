<?php if( !isset($skip_det_link) ){ ?>
<div class="row">
    <div class="col-lg-12">
        <div class="card card-outline-info">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8 col-8 align-self-center">
                        <h4 class="m-b-0 text-white">Board <small><?= $board[ 'board_name' ] ?></small></h4>
                    </div>
                    <div class="col-md-4 col-4 align-self-center">
                        <?php if ($show_public_url && 1 == 0) {
        echo anchor('publicReport/' . $prodDet['share_analytic_link'],
            'Public Link', ' class="btn pull-right btn-outline-primary" target="_blank" ');
    }?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
}
    foreach($cardLists as $listDet){
?>
    <h2>List : <?= $listDet->name ?></h2>
    <div class="row">
        <?php        
            if( isset( $cards ) && $cards  ){
                foreach( $cards as $cIndex => $card ){
                    if( $card->idList == $listDet->id && !$card->closed ){
        ?>
        <div class="col-lg-3 col-md-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title"><?= $card->name; ?></h4>
                    <div class="text-left m-l-10">
                        <div class="row">
                            <div class="col-lg-12 col-md-12">
                                <p class="m-b-0 m-t-10"><?= $card->desc ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
                    }
                }
            }
        ?>
        </div>
    <!-- Column -->
<?php } ?>