<form action="<?= base_url( 'accounts/addLogo/'.$account[ 'id' ] ); ?>" 
      id="addLogo" 
      method="POST"
      enctype="multipart/form-data" >
  <div class="form-group row">
    <label for="name" class="col-2 col-form-label">Name</label>
    <div class="col-10">
      <input class="form-control" type="text" value="<?= $account[ 'name' ]; ?>" id="name" readonly>
    </div>
  </div>
  <div class="form-group row">
    <label for="agency_name" class="col-2 col-form-label">Agency Name</label>
    <div class="col-10">
      <input class="form-control" type="text" value="<?= $account[ 'agency_name' ]; ?>" id="agency_name" readonly>
    </div>
  </div>
  <div class="form-group row">
    <label for="email" class="col-2 col-form-label">Email</label>
    <div class="col-10">
      <input class="form-control" type="email" value="<?= $account[ 'email' ]; ?>" id="email" readonly>
    </div>
  </div>
  <div class="form-group row">
    <label for="status" class="col-2 col-form-label">Account Status</label>
    <div class="col-10">
      <input class="form-control" type="url" value="<?= $account[ 'account_status' ] ? 'Active' : 'Cancelled' ; ?>" id="status" readonly>
    </div>
  </div>
  <div class="form-group row">
    <label for="report_logo" class="col-2 col-form-label">
      Report Logo
      <?php if( $account[ 'report_logo' ] ){ ?>
        <small>
          <img src="/uploads/report_logo/<?= $account[ 'report_logo' ]; ?>"
                width="100" >
        </small>
      <?php } ?>
    </label>
    <div class="col-10">
      <input  class="form-control" 
              type="file" 
              accept="image/*" 
              name="report_logo" id="report_logo">
    </div>
  </div>
  <button type="submit" class="btn btn-success waves-effect waves-light">Submit</button>
</form>