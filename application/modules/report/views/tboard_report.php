<div class="row">
    <div class="col-lg-12">
        <div class="card card-outline-info">
            <div class="card-header">
                <h4 class="m-b-0 text-white">Board <small><?= $board[ 'board_name' ] ?></small></h4>
            </div>
            <div class="card-body">
                <div class="col-lg-12" id="cardResult">
                    <div class="row">
                        <!-- Column -->
                        <?php        
                            if( isset( $cards ) && $cards  ){
                                foreach( $cards as $cIndex => $card ){
                        ?>
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body" style="background-color: <? $cardsColor[ $cIndex ]; ?>">
                                    <h3 class="font-normal"><?= $card->name ?></h3>
                                    <p class="m-b-0 m-t-10"><?= $card->url ?></p>
                                    <p class="m-b-0 m-t-10">Closed <?= ($card->closed ? 'Yes' : 'No'); ?></p>
                                    <p class="m-b-0 m-t-10"><?= $card->desc ?></p>
                                </div>
                            </div>
                        </div>
                        <?php
                                }
                            } else {
                        ?>
                        <div class="col-lg-12">
                            No record found!     
                        </div>
                        <?php
                            }
                        ?>
                    </div>     
                </div>
            </div>
        </div>
    </div>
</div>