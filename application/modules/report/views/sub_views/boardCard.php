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