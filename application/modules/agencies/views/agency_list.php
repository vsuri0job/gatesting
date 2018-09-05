<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-10">
                    </div>
                    <div class="col-lg-2">
                        <a href="agencies/add" class="btn btn-info">Add Agency</a>
                    </div>
                </div>
                <div class="table-responsive ">
                    <table id="agencies" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Users</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Users</th>
                            </tr>
                        </tfoot>
                        <tbody>
                        <?php foreach( $agencies as $agency){ ?>
                            <tr>
                                <td><?= $agency[ 'name' ]; ?></td>
                                <td><?= $agency[ 'status' ] ? 'Active' : 'De-active'; ?></td>
                                <td><a href="<?=?>">View</a></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>        
    </div>
</div>