<div class="row">
    <div class="col-lg-12">
        <div class="card card-outline-info">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6 col-8 align-self-center">
                        <h4 class="m-b-0 text-white">Link <?= $profDet[ 'account_url' ]; ?></h4>
                    </div>
                    <div class="col-md-6 col-4 align-self-center">
                        <a href="<?= base_url( 'social/updateGoogle/webmaster/'.$profDet[ 'id' ] ) ?>" 
                                class="btn pull-right btn-outline-primary">Update Adwords Projects</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="<?= base_url( 'social/link_webmaster/'.$profDet[ 'id' ] ) ?>" class="form-horizontal" 
                    id="getGoogleData" method="POST">
                    <input type="hidden" name="fetched_profile" value="<?= $profDet[ 'id' ]; ?>">
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="control-label text-right col-md-2">Site Url</label>
                                    <div class="col-md-10">                                        
                                            <?php
                                                echo form_dropdown( 'webmaster_sites', $webmaster_sites, array(), 
                                                    ' id="webmaster_sites" class="form-control" 
                                                        required data-placeholder="Choose" ' );
                                            ?>
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
                                        <button type="submit" 
                                            id="update-webmaster"
                                            class="btn btn-success">Update Account</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6"> </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="hide" id="progressBar">
            <div class="progress-bar bg-success" role="progressbar" id="progress-bar" 
                style="width: 15%;height:15px;" 
                role="progressbar"> 15% </div>
            <div class="font-weight-bold">
                Gathering Google Search Console data for the last 13 months, this will take a bit.  Please do not close this screen while magic happens
            </div>
        </div>
    </div>
</div>
<!-- Row -->
<div id="currentMonthData">
</div>

<div id="lastMonthsData">
</div>