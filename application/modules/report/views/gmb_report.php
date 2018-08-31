<div class="row">
    <div class="col-lg-12">
        <div class="card card-outline-info">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-4 col-4 align-self-center">
                        <h4 class="m-b-0 text-white">View Google My Business</h4>
                    </div>
                    <div class="col-md-6 col-6 align-self-center">
                        <?php
                            echo form_dropdown( 'locations', $gmb_locs, "", ' id="locations" class="form-control" ' );
                        ?>
                    </div>
                    <div class="col-md-2 col-2 align-self-center">
                        <?php if( $show_public_url ){
                            echo anchor( 'publicReport/'.$prodDet[ 'share_adwords_link' ], 
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
        <table id="locations-data" class="table m-t-30 table-hover no-wrap contact-list" data-page-size="10">
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