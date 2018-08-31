<div class="row">
    <div class="col-lg-12">
        <div class="card card-outline-info">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-10 col-10 align-self-center">
                        <h4 class="m-b-0 text-white"><?= $prodDet[ 'account_url' ] ?> Locations:                     
                        <?php
                            echo form_dropdown( 'locations', $gmb_locs, "", 
                                ' id="locations" class="form-control" style="width:250px"' );
                        ?>                    
                        </h4>
                    </div>
                    <div class="col-md-2 col-2 align-self-center">
                        <?php if( $show_public_url ){
                            echo anchor( 'publicReport/'.$prodDet[ 'share_gmb_link' ], 
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
    <div class="table-responsive">
        <table id="locations-data" class="table">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Clicks to Site</th>
                    <th>Driving Direction Requests</th>
                    <th>Calls</th>
                </tr>
            </thead>
            <tbody id="locationsData">
                <?php
                    if( $gmb_data ){
                        $loc_data = $gmb_data[ $gmb_loc_id ];                        
                        foreach ($loc_data as $lData) {
                            $lData[ 1 ] = number_format( $lData[ 1 ]);
                            $lData[ 2 ] = number_format( $lData[ 2 ]);
                            $lData[ 3 ] = number_format( $lData[ 3 ]);
                ?>
                        <tr>
                            <td class="ucfirst"><?= $lData[ 0 ]; ?></td>
                            <td ><?= $lData[ 1 ]; ?></td>
                            <td ><?= $lData[ 2 ]; ?></td>
                            <td ><?= $lData[ 3 ]; ?></td>
                        </tr>
                <?php   }
                    } 
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>Month</th>
                    <th>Clicks to Site</th>
                    <th>Driving Direction Requests</th>
                    <th>Calls</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>