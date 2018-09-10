<div class="row">
    <div class="col-lg-12">
        <div class="card card-outline-info">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6 col-8 align-self-center">
                        <h4 class="m-b-0 text-white">Search Rankinity</h4>
                    </div>
                    <div class="col-md-6 col-4 align-self-center">
                        <a href="<?= base_url( 'social/updateRankinityProjects/'.$profile[ 'id' ] ) ?>" class="btn pull-right btn-outline-primary">Update Rankinity Projects Pending</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="<?= base_url( 'social/updateAccountRankinity/'.$profile[ 'id' ] ) ?>" class="form-horizontal" 
                    id="getGoogleData" method="POST">
                    <input type="hidden" name="fetched_profile" value="<?= $profile[ 'id' ]; ?>">
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="control-label text-right col-md-2">Rankinity Projects</label>
                                    <div class="col-md-10">                                        
                                            <?php
                                                echo form_dropdown( 'rankProjects', $projects, array(), 
                                                    ' id="rankProjects" class="form-control" required' );
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