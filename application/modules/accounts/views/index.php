<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Users accounts</h4>                
                <div class="table-responsive m-t-40">
                    <table id="accounts" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Agency</th>
                                <th>Status</th>
                                <th>Services</th>                                
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Name</th>
                                <th>Agency</th>
                                <th>Status</th>
                                <th>Services</th>                                
                            </tr>
                        </tfoot>
                        <tbody>
                        <?php foreach( $accounts as $account){ ?>
                            <tr>
                                <td><?= $account[ 'name' ]; ?></td>
                                <td><?= $account[ 'agency_name' ]; ?></td>
                                <td><?php 
                                        $status =  $account[ 'account_status' ] ? 'Active' : 'Cancelled';
                                        echo $status;
                                    ?>
                                </td>
                                <td><?= $account[ 'services' ]; ?></td>
                                <?php /* ?>
                                <td>
                                    <a href="<?= base_url( 'accounts/addLogo/'.$account[ 'id' ] ) ?>">Add Logo</a>
                                      <?php if( $account[ 'report_logo' ] ){ ?>
                                        <br/>
                                        <small>
                                          <img src="/uploads/report_logo/<?= $account[ 'report_logo' ]; ?>"
                                                width="50" >
                                        </small>
                                      <?php } ?>
                                </td>
                                <?php */ ?>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>        
    </div>
</div>