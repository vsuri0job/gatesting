<div class="row">
    <div class="col-lg-12">
        <div class="card card-outline-info">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6 col-8 align-self-center">
                        <h4 class="m-b-0 text-white">Fetch Account <small><i><?= $profDet[ 'account_url' ]; ?></i></small></h4>
                    </div>
                    <div class="col-md-6 col-4 align-self-center">
                        <a href="<?= base_url( 'social/updateGoogleProfiles' ) ?>" 
                                class="btn pull-right btn-outline-primary">Update Google Profile Pending</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="#" class="form-horizontal" id="getGoogleData">
                    <input type="hidden" name="prof_id" value="<?= $profDet[ 'id' ]; ?>">
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="control-label text-right col-md-2">Profiles</label>
                                    <div class="col-md-10">
                                        <select id="profile" name="profile" class="form-control custom-select" required>
                                            <?php
                                                if( $profiles ){
                                                    $optHtml = '<option value="">Select</option>';
                                                    foreach( $profiles as $profile){                                            
                                                        $optHtml .= '<option value="'.$profile[ 'profile_id' ].'"
                                                                >'.$profile[ 'profile_name' ].'</option>';
                                                    }
                                                    echo $optHtml;
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/row-->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="control-label text-right col-md-2">Properties</label>
                                    <div class="col-md-10">
                                        <select id="prop" name="prop" class="form-control custom-select" required> 
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/row-->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="control-label text-right col-md-2">Views</label>
                                    <div class="col-md-10">
                                        <select id="view" name="view" class="form-control custom-select" required>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" class="btn btn-success">Fetch Data</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6"> </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Row -->
<div id="currentMonthData">
</div>

<div id="lastMonthsData">
</div>