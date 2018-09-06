<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-10">
                    </div>
                    <div class="col-lg-2">
                        <a href="/agencies/add_user/<?=$agency[ 'id' ] ?>" class="btn btn-info">Add Agency User</a>
                    </div>
                </div>
                <div class="table-responsive ">
                    <table id="agencies" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Company Name</th>
                                <th>Email</th>
                                <th>Logo</th>
                                <th>Users Count</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Company Name</th>
                                <th>Email</th>
                                <th>Logo</th>
                                <th>Users Count</th>
                            </tr>
                        </tfoot>
                        <tbody>
                        <?php foreach( $agency_users as $ag_user){ ?>
                            <tr>
                                <td><?= $ag_user[ 'company_name' ]; ?></td>
                                <td><?= $ag_user[ 'email' ]; ?></td>
                                <td></td>
                                <td><?= 0; ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>        
    </div>
</div>