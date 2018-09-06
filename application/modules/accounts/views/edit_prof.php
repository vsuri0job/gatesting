<style type="text/css">
  .customTool{
    font-style: normal;
    padding: 0 5px;    
  }
</style>
<form action="<?= base_url( 'accounts/editProfileUrl/'.$profDet['id'] ); ?>" 
      id="addProfile" name="addProfile" 
      method="POST"
      enctype="multipart/form-data" >    
    <div class="form-group row">
      <label for="name" class="col-4 col-form-label">
        Close Rate <i class="mytooltip mdi mdi-tooltip-text">
          <span class="tooltip-content customTool text-white">Close rate - Your closing ratio is the number of sales you close compared to the number of leads. Say, for instance, you have 10 last month and closed 2 sales as a result. You closed 2/10ths, or 20 percent, of your potential sales.</span></i>
        </i>
      </label>
      <div class="col-8">
        <input  class="form-control"  
                type="number" value="<?= $profDet['close_rate'] ?>" min="0" value="0" step=".01"
                id="close_rate" required name="close_rate">
      </div>
    </div>
    <div class="form-group row">
      <label for="name" class="col-4 col-form-label">Avg. Sale Amt.
        <i class="mytooltip mdi mdi-tooltip-text">
          <span class="tooltip-content customTool text-white">Avg sales amount - this is a value in dollars that 1 sale is worth.</span>
          </i>
      </label>
      <div class="col-8">
        <input  class="form-control"  
                type="number" value="<?= $profDet['avg_sale_amount'] ?>" min="0" value="0" step=".01"
                id="avg_sale_amount" required name="avg_sale_amount">
      </div>
    </div>
    <div class="form-group row">
      <label for="name" class="col-4 col-form-label">LTV Amt.
        <i class="mytooltip mdi mdi-tooltip-text">
          <span class="tooltip-content customTool text-white">LTV amt - this is how many times someone who purchases from you come back again and purchases again. For example, a dentist might have a sales value of $200. For that person come every six months for 5 years. So the number we would put here would be 10</span>
          </i>
      </label>      
      <div class="col-8">
        <input  class="form-control"  
                type="number" value="<?= $profDet['ltv_amount'] ?>" min="0" value="0" step=".01"
                id="ltv_amount" required name="ltv_amount">
      </div>
    </div>
    <div class="form-group row">
        <div class="col-6">
          <a href="<?= base_url( 'accounts/deleteProfileUrl/'.$profDet['id'] ) ?>"
            onclick="return confirm('Are you sure, you want to delete it?')"
            class="btn btn-danger waves-effect waves-light float-left" >Delete</a>
        </div>  
        <div class="col-6">          
          <button type="submit" class="btn btn-info waves-effect waves-light float-right">Submit</button>
        </div>
    </div>
</form>