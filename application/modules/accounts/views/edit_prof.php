<form action="<?= base_url( 'accounts/editProfileUrl/'.$profDet['id'] ); ?>" 
      id="addProfile" name="addProfile" 
      method="POST"
      enctype="multipart/form-data" >    
    <div class="form-group row">
      <label for="name" class="col-2 col-form-label">Close Rate</label>
      <div class="col-10">
        <input  class="form-control"  
                type="number" value="<?= $profDet['close_rate'] ?>" min="0" value="0" step=".01"
                id="close_rate" required name="close_rate">
      </div>
    </div>
    <div class="form-group row">
      <label for="name" class="col-2 col-form-label">Avg. Sale Amt.</label>
      <div class="col-10">
        <input  class="form-control"  
                type="number" value="<?= $profDet['avg_sale_amount'] ?>" min="0" value="0" step=".01"
                id="avg_sale_amount" required name="avg_sale_amount">
      </div>
    </div>
    <div class="form-group row">
      <label for="name" class="col-2 col-form-label">LTV Amt.</label>
      <div class="col-10">
        <input  class="form-control"  
                type="number" value="<?= $profDet['ltv_amount'] ?>" min="0" value="0" step=".01"
                id="ltv_amount" required name="ltv_amount">
      </div>
    </div>
  <button type="submit" class="btn btn-success waves-effect waves-light float-right">Submit</button>
</form>