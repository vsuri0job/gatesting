<div class="row">
    <!-- Column -->
    <div class="col-lg-4 col-xlg-3 col-md-5">
        <div class="card">
            <div class="card-body">
                <center class="m-t-30"> <img src="<?=com_user_img('username');?>" class="img-circle" width="150" />
                    <h4 class="card-title m-t-10"><?=com_user_data('username');?></h4>
                    <div class="row text-center justify-content-md-center">
                        <div class="col-4"><a href="javascript:void(0)" class="link"><i class="icon-people"></i> <font class="font-medium">254</font></a></div>
                        <div class="col-4"><a href="javascript:void(0)" class="link"><i class="icon-picture"></i> <font class="font-medium">54</font></a></div>
                    </div>
                </center>
            </div>
            <div>
                <hr> </div>
            <div class="card-body">
                <small class="text-muted">Email address </small>
                <h6><?=com_user_data('email');?></h6>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-8 col-xlg-9 col-md-7">
        <div class="card">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs profile-tab" role="tablist">
                <li class="nav-item"> <a class="nav-link <?=$socialTabActive ? "" : "active";?>" data-toggle="tab" href="#profile" role="tab">Profile</a> </li>
                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#settings" role="tab">Settings</a> </li>                
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <!--second tab-->
                <div class="tab-pane <?=$socialTabActive ? "" : "active";?>" id="profile" role="tabpanel">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 col-xs-6 b-r"> <strong>Full Name</strong>
                                <br>
                                <p class="text-muted"><?=com_user_data('username');?></p>
                            </div>
                            <div class="col-md-6 col-xs-6"> <strong>Email</strong>
                                <br>
                                <p class="text-muted"><?=com_user_data('email');?></p>
                            </div>
                        </div>
                        <hr>
                        <h4 class="font-medium m-t-30">Agencies</h4>
                        <hr>
                        <?php
$agencies = com_user_data('agencies');
if ($agencies) {
	$agencies = explode(',', $agencies);
	$agencies = $this->agency->getAgenciesByArray($agencies);
	foreach ($agencies as $agency) {
		$accountCount = $this->agency->getAgencyActiveAccountCount($agency['id']);
		?>
                            <h5 class="m-t-30">
                                <?=$agency['name']?>
                                <span class="pull-right">Active accounts: <?=$accountCount;?></span>
                            </h5>
                        <?php
}
}
?>
                    </div>
                </div>
                <div class="tab-pane" id="settings" role="tabpanel">
                    <div class="card-body">
                        <form   class="form-horizontal form-material"
                                action="<?=base_url('dashboard/profile');?>"
                                method="POST"
                                enctype="multipart/form-data">
                            <div class="form-group">
                                <label class="col-md-12">Full Name</label>
                                <div class="col-md-12">
                                    <input  type="text"
                                            id="username"
                                            name="username"
                                            required
                                            placeholder="<?=com_user_data('username');?>"
                                            class="form-control form-control-line">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="example-email" class="col-md-12">Email</label>
                                <div class="col-md-12">
                                    <input  type="email"
                                            required
                                            name="email"
                                            placeholder="<?=com_user_data('email');?>"
                                            class="form-control form-control-line"
                                            id="email">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Password</label>
                                <div class="col-md-12">
                                    <input  type="password"
                                            id="password"
                                            name="password"
                                            value=""
                                            class="form-control form-control-line">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Report Logo</label>
                                <div class="col-md-12">
                                    <input  type="file"
                                            accept="image/*"
                                            name="report_logo"
                                            class="form-control form-control-line" >
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <input type="hidden" name="profile_submit" value="1">
                                    <input  type="submit"
                                            value="Update Profile"
                                            class="btn btn-success" />
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
</div>