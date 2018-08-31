<!-- Start Page Content -->
<!-- ============================================================== -->
<style type="text/css">
    .ucfirst{
        text-transform: capitalize;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="card card-outline-info">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-10 col-10 align-self-center">
                        <h4 class="m-b-0 text-white"><?= $rankProfile[ 'account_url' ] ?>                        
                        </h4>
                    </div>
                    <div class="col-md-2 col-2 align-self-center">
                        <?php if( $show_public_url ){
                            echo anchor( 'publicReport/'.$rankProfile[ 'share_rankinity_link' ], 
                                'Public Link', ' class="btn pull-right btn-outline-primary" target="_blank" ' );
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>    
</div>
<!-- Row -->
<div class="row">
    <div class="col-12">
        <div class="card">

          <div class="card-body">                
                <div class="row m-t-10">
                    <!-- Column -->
                    <div class="col-md-6 col-lg-4 col-xlg-4">
                        <div class="card card-inverse card-info">
                            <img src="<?= $rankProfile[ 'rankinity_project_screenshot' ] ?>" />
                            <div class="box bg-info text-center" style="bottom:0; width: 100%; margin: 0 auto; position: absolute;" >
                                <h1 class="font-light text-white"><?= $rankProfile[ 'rankinity_project_name' ] ?></h1>
                                <h6 class="text-white"><?= $rankProfile[ 'rankinity_project_url' ] ?></h6>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <div class="col-md-6 col-lg-8 col-xlg-8">
                        <div class="card card-info card-inverse">
                            <div class="box text-center">
                                <h1 class="font-light text-white">Serch Engines</h1>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12 col-xlg-12">
                                        <select name="engines" id="engines" class="form-control">
                                            <?php
                                                $engineId = '';
                                                $engineHtml = '';
                                                foreach ($profileEngines as $eKey => $engine) {
                                                    if( !$eKey ){
                                                        $engineId = $engine[ 'engine_id' ];
                                                    }
                                                    $engineHtml .= '<option value="'.$engine[ 'engine_id' ].'">'.$engine[ 'engine_title' ].'</option>';
                                                }
                                                echo $engineHtml;
                                                $selectedEngine = $profileEnginesVisibility[ $engineId ];
                                                $selectedEngineRank = $profileEnginesRanks[ $engineId ];
                                            ?>
                                        </select>  
                                    </div>
                                </div>
                                <div class="row text-white font-light m-t-30">
                                    <div class="col-md-6 col-lg-6 col-xlg-6 m-t-20">
                                        Visibility 
                                        <hr style="color: white;background: white;margin: 3px 39%;">
                                        <span id="profVisibility"><?= com_arrIndex($selectedEngine, 'position', 0); ?></span> 
                                        <i class="mdi mdi-percent" aria-hidden="true"></i>
                                    </div>
                                    <div class="col-md-6 col-lg-6 col-xlg-6 m-t-20">
                                        Unchanged
                                        <hr style="color: white;background: white;margin: 3px 39%;">
                                        <span id="pos_unchanged"><?= com_arrIndex($selectedEngine, 'position_unchanged') ?></span> 
                                        <i class="mdi mdi-radiobox-blank" aria-hidden="true"></i>
                                    </div>
                                    <div class="col-md-6 col-lg-6 col-xlg-6 m-t-20">
                                        Up
                                        <hr style="color: white;background: white;margin: 3px 40%;">
                                        <span id="pos_up"><?=  com_arrIndex($selectedEngine, 'position_up' ); ?></span> 
                                        <i class="mdi mdi-trending-up" aria-hidden="true"></i>
                                    </div>
                                    <div class="col-md-6 col-lg-6 col-xlg-6 m-t-20">
                                        Down
                                        <hr style="color: white;background: white;margin: 3px 40%;">
                                        <span id="pos_down"><?=  com_arrIndex($selectedEngine, 'position_down' ); ?></span> 
                                        <i class="mdi mdi-trending-down" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title text-center">Visibility history</h4>
                            <div id="visibility-chart"></div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="keyword-ranks" class="table m-t-30 table-hover no-wrap contact-list" data-page-size="10">
                        <thead>
                            <tr>
                                <th>Keyword</th>
                                <th>Position</th>
                            </tr>
                        </thead>
                        <tbody id="keywordData">
                            <?php
                                if( $selectedEngineRank ){
                                foreach ($selectedEngineRank as $rKey => $rankData) {
                                    $icon = 'mdi-arrow-down-bold text-danger';
                                    if( $rankData[ 'position_boost' ] >= 0 ){
                                        $icon = 'mdi-arrow-up-bold text-success';
                                    }
                            ?>
                            <tr>
                                <td class="ucfirst"><?= $rankData[ 'keyword_name' ]; ?></td>
                                <td>
                                    <?= $rankData[ 'position' ] ?>
                                    <i class="mdi <?= $icon; ?>" aria-hidden="true"></i>
                                    <small><?= $rankData[ 'position_boost' ] ?></small>
                                </td>
                            </tr>
                            <?php
                                }
                                }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Keyword</th>                                
                                <th>Position</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ============================================================== -->
<!-- End PAge Content -->