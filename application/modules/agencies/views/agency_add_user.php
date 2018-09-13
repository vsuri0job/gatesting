<div class="row">
    <div class="col-lg-12">
        <div class="card card-outline-info">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6 col-8 align-self-center">
                        <h4 class="m-b-0 text-white">Add new agency user</h4>
                    </div>
                    <div class="col-md-6 col-4 align-self-center">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php
                    $errors = validation_errors();
                    if( $errors ){
                        echo ul($errors, array( 'class' => 'form-contrl text-danger' ));
                    }
                ?>
                <form action="<?= base_url( 'agencies/add_user/'.$agency[ 'id' ] ) ?>" class="form-horizontal" 
                    id="add_agency" method="POST">
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="control-label text-right col-md-2">Company Name</label>
                                    <div class="col-md-10">                                        
                                        <input  type="text" name="company_name" 
                                                id="company_name" 
                                                class="form-control" 
                                                required
                                                value="<?= set_value( 'company_name' ); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="control-label text-right col-md-2">Email</label>
                                    <div class="col-md-10">                                        
                                        <input  type="text" name="email" 
                                                value="<?= set_value( 'email' ); ?>"
                                                id="email" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="control-label text-right col-md-2">Password</label>
                                    <div class="col-md-10">                                        
                                        <input  type="password" name="password" 
                                                id="password" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="control-label text-right col-md-2">Logo</label>
                                    <div class="col-md-10">                                        
                                        <input type="file" name="logo"
                                        accept="image/*" 
                                        >
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
                                        <button type="submit" class="btn btn-success">Submit</button>
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