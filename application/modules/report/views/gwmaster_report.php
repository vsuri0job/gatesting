<div class="row">
    <div class="col-lg-12">
        <div class="card card-outline-info">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6 col-8 align-self-center">
                        <h4 class="m-b-0 text-white"><?= $prodDet[ 'account_url' ]; ?></h4>
                    </div>
                    <div class="col-md-6 col-4 align-self-center">
                        <?php if( $show_public_url ){
                            echo anchor( 'publicReport/'.$prodDet[ 'share_gsc_link' ], 
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
    <style type="text/css">
    .align-bottom small {
        position: absolute;
        right: 0;
        top: 11px;
    }
    .kpiText{
        padding: 0; 
    }
    </style>
<div class="row">    
    <!-- Column -->
    <div class="col-lg-3 col-md-3">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">                         
                    <img src="<?= base_url( 'img/social/search-console-on.png' ) ?>" width="20px" height="20px"> Server Error</h4>
                <div class="text-left m-l-10">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 kpiText">
                            <h2 class="font-light m-b-0"><?= $kpis[ 'server_error' ] ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->    
    <!-- Column -->
    <div class="col-lg-3 col-md-3">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">                         
                    <img src="<?= base_url( 'img/social/search-console-on.png' ) ?>" width="20px" height="20px"> Soft 404</h4>
                <div class="text-left m-l-10">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 kpiText">
                            <h2 class="font-light m-b-0"><?= $kpis[ 'soft_404' ] ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->    
    <!-- Column -->
    <div class="col-lg-3 col-md-3">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">                         
                    <img src="<?= base_url( 'img/social/search-console-on.png' ) ?>" width="20px" height="20px"> Not found  </h4>
                <div class="text-left m-l-10">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 kpiText">
                            <h2 class="font-light m-b-0"><?= $kpis[ 'not_found' ] ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->        
    <!-- Column -->
    <div class="col-lg-3 col-md-3">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">                         
                    <img src="<?= base_url( 'img/social/search-console-on.png' ) ?>" width="20px" height="20px"> Other</h4>
                <div class="text-left m-l-10">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 kpiText">
                            <h2 class="font-light m-b-0"><?= $kpis[ 'other' ] ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->        
</div>
</div>

<div id="lastMonthsData">

<div class="">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Month Over Month</h4>
            <div class="table-responsive">
                <table class="table" id="month-tbl">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Clicks</th>
                            <th>Impressions</th>
                            <th>CTR</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                            <?php
                                $dataHtml = '';
                                foreach( $months as $data ){
                                    $data[ 'month_ref' ] = date( "F Y", strtotime( $data[ 'month_ref' ] ) );                                    
                                    $data[ 'ctr' ] = number_format( $data[ 'ctr' ] * 100, 2 );
                                    $data[ 'clicks' ] = number_format( $data[ 'clicks' ]);
                                    $data[ 'impressions' ] = number_format( $data[ 'impressions' ]);
                                    $dataHtml .= '<tr>
                                                    <td>'.$data[ 'month_ref' ].'</td>
                                                    <td>'.$data[ 'clicks' ].'</td>
                                                    <td>'.$data[ 'impressions' ].'</td>
                                                    <td>'.$data[ 'ctr' ].'%</td>
                                                </tr>';
                                }
                                echo $dataHtml;
                            ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Queries</h4>
            <div class="table-responsive">
                <table class="table" id="queries-tbl">
                    <thead>
                        <tr>
                            <th>Query</th>
                            <th>Clicks</th>
                            <th>Impressions</th>
                            <th>CTR</th>                            
                        </tr>
                    </thead>
                    <tbody>
                        
                            <?php
                                $dataHtml = '';
                                foreach( $queries as $data ){
                                        $data[ 'ctr' ] = number_format( $data[ 'ctr' ] * 100, 2  );
                                        $data[ 'clicks' ] = number_format( $data[ 'clicks' ]);
                                        $data[ 'impressions' ] = number_format( $data[ 'impressions' ]);
                                    $dataHtml .= '<tr>
                                                    <td>'.$data[ 'queries' ].'</td>
                                                    <td>'.$data[ 'clicks' ].'</td>
                                                    <td>'.$data[ 'impressions' ].'</td>
                                                    <td>'.$data[ 'ctr' ].'%</td>
                                                </tr>';
                                }
                                echo $dataHtml;
                            ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Pages</h4>
            <div class="table-responsive">
                <table class="table" id="pages-tbl">
                    <thead>
                        <tr>
                            <th>Page</th>
                            <th>Clicks</th>
                            <th>Impressions</th>
                            <th>CTR</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                            <?php
                                $dataHtml = '';
                                foreach( $pages as $data ){
                                    $data[ 'ctr' ] = number_format( $data[ 'ctr' ] * 100, 2  );
                                    $data[ 'clicks' ] = number_format( $data[ 'clicks' ]);
                                    $data[ 'impressions' ] = number_format( $data[ 'impressions' ]);
                                    $dataHtml .= '<tr>
                                                    <td>'.$data[ 'pages' ].'</td>
                                                    <td>'.$data[ 'clicks' ].'</td>
                                                    <td>'.$data[ 'impressions' ].'</td>
                                                    <td>'.$data[ 'ctr' ].'%</td>
                                                </tr>';
                                }
                                echo $dataHtml;
                            ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div>