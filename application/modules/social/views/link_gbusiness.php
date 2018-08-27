<div class="row">
    <div class="col-lg-12">
        <div class="card card-outline-info">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6 col-8 align-self-center">
                        <h4 class="m-b-0 text-white">Link Admin Account</h4>
                    </div>
                    <div class="col-md-6 col-4 align-self-center">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="<?= base_url( 'social/updateGbusinessAccount/'.$profile[ 'id' ] ) ?>" class="form-horizontal" 
                    id="getGoogleData" method="POST">
                    <input type="hidden" name="fetched_profile" value="<?= $profile[ 'id' ]; ?>">
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="control-label text-right col-md-2">Business List</label>
                                    <div class="col-md-10">                                        
                                            <?php
                                                echo form_dropdown( 'gbuissAccounts[]', $gList, [], 
                                                    ' id="gbuissAccounts" class="form-control select2-multiple" 
                                                        required multiple data-placeholder="Choose" ' );
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
                                        <button type="submit" class="btn btn-success">Update Account</button>
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