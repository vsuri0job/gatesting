<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-8 align-self-center">
                        <h4 class="card-title">Accounts Url</h4>
                    </div>
                    <div class="col-md-6 col-4 align-self-center">
                        <a href="<?= base_url( 'accounts/addProfileUrl' ) ?>" class="btn pull-right btn-info">Add Account</a>
                    </div>
                </div>
                <div class="table-responsive m-t-40">
                    <table id="accounts" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Account</th>                                
                                <th>SEO</th>
                                <th>PPC</th>
                                <th>Local</th>
                                <th>Trello</th>
                                <th>Rankinity</th>
                                <th>Webmaster</th>
                                <th>Content/Citations</th>
                                <th>Overview Report</th>
                                <th>Full Report</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Account</th>
                                <th>SEO</th>
                                <th>PPC</th>
                                <th>Local</th>
                                <th>Trello</th>
                                <th>Rankinity</th>
                                <th>Webmaster</th>
                                <th>Content/Citations</th>
                                <th>Overview Report</th>
                                <th>Full Report</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php foreach( $accounts as $account){
                                $subInner = [];
                                $subInner[ 'account' ] = $account;
                                echo $this->view( 'sub_views/prof_list_row', $subInner, true );
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>        
    </div>
</div>
<div    id="link-account-modal" class="modal fade" 
        tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form name="link-account" id="link-analytic-profile">
            <div class="modal-header">                
                <h4 class="modal-title">Link Account</h4>
            </div>
            <div class="modal-body" id="link-account-modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-danger waves-effect waves-light">Link Account</button>
            </div>
            </form>
        </div>
    </div>
</div>
<div    id="link-account-adword" class="modal fade" 
        tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form name="link-account" id="adword-cus-id" action="" method="POST">
            <div class="modal-header">                
                <h4 class="modal-title">Provide Master Customer Id</h4>
            </div>
            <div class="modal-body" id="link-account-modal-body">
                <input type="text" name="customer_id" value="" class="form-control" required
                    placeholder="Please Provide 10 Digit Customer Id" id="customer_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-danger waves-effect waves-light">Proceed</button>
            </div>
            </form>
        </div>
    </div>
</div>
<div    id="link-account-rankinity" class="modal fade" 
        tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form name="link-account" id="rank-cus-id" action="" method="POST">
            <div class="modal-header" style="flex-wrap: wrap;text-align: center;">
                <h4 class="modal-title">Provide Rankinity Account API Token</h4>       
                <p><small>You can find it under Setting > Account > API Token</small></p>
            </div>            
            <div class="modal-body" id="link-account-modal-body">
                <input type="text" name="rankinity_token" value="" class="form-control" required
                    placeholder="Please Provide Rankinity API Token">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-danger waves-effect waves-light">Proceed</button>
            </div>
            </form>
        </div>
    </div>
</div>