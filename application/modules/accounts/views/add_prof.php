<form action="<?= base_url( 'accounts/addProfileUrl' ); ?>" 
      id="addProfile" name="addProfile" 
      method="POST"
      enctype="multipart/form-data" >
  <div class="form-group row">
    <label for="name" class="col-2 col-form-label">Account Url</label>
    <div class="col-10">
      <input class="form-control" type="text" value="" id="account_url" required name="account_url">
    </div>
  </div>
  <button type="submit" class="btn btn-success waves-effect waves-light float-right">Submit</button>
</form>