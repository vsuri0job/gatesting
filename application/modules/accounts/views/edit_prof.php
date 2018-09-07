<style type="text/css">
  .customTool{
    font-style: normal;
    padding: 0 5px;    
  }
  .tooltip-content {
    font-size: 11px;
    line-height: normal;
    padding: 10px;
}
</style>
<form action="<?= base_url( 'accounts/editProfileUrl/'.$profDet['id'] ); ?>" 
      id="addProfile" name="addProfile" 
      method="POST"
      enctype="multipart/form-data">
    <div class="form-group row">
      <label for="name" class="col-2 col-form-label">
        Close Rate <i class="mytooltip mdi mdi-tooltip-text">
          <span class="tooltip-content customTool text-white">Close rate - Your closing ratio is the number of sales you close compared to the number of leads. Say, for instance, you have 10 last month and closed 2 sales as a result. You closed 2/10ths, or 20 percent, of your potential sales.</span></i>
        </i>
      </label>
      <div class="col-2">
        <input  class="form-control"  
                type="number" value="<?= $profDet['close_rate'] ?>" min="0" value="0" step=".01"
                id="close_rate" required name="close_rate"
                style="width: 90%"><span>%</span>
      </div>
      <div class="col-8 text-left"></div>
    </div>
    <div class="form-group row">
      <label for="name" class="col-2 col-form-label">Avg. Sale Amt.
        <i class="mytooltip mdi mdi-tooltip-text">
          <span class="tooltip-content customTool text-white">Avg sales amount - this is a value in dollars that 1 sale is worth.</span>
          </i>
      </label>
      <div class="col-2">
        <span>$</span>
        <input  class="form-control"  style="width: 90%"
                type="number" value="<?= $profDet['avg_sale_amount'] ?>" min="0" value="0" step=".01"
                id="avg_sale_amount" required name="avg_sale_amount">
      </div>
      <div class="col-8"></div>
    </div>
    <div class="form-group row">
      <label for="name" class="col-2 col-form-label">LTV Amt.
        <i class="mytooltip mdi mdi-tooltip-text">
          <span class="tooltip-content customTool text-white">LTV amt - this is how many times someone who purchases from you come back again and purchases again. For example, a dentist might have a sales value of $200. For that person come every six months for 5 years. So the number we would put here would be 10</span>
          </i>
      </label>      
      <div class="col-2">
        <input  class="form-control"  
                type="number" value="<?= $profDet['ltv_amount'] ?>" min="0" value="0" step=".01"
                id="ltv_amount" required name="ltv_amount">
      </div>
      <div class="col-8"></div>
    </div>
    <div class="form-group row">
      <div class="col-12">
        <h3>Reports Setting</h3>
        <div class="table-responsive" style="overflow: hidden">
        <table class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
          <tbody>
            <tr>
              <th width="30%">Report Name</th>
              <th>Tables</th>
            </tr>
            <tr>
              <td>Analytic (SEO)</td>
              <?php
                $seo_total = $seo_organic =
                $seo_source_medium = $seo_medium = $seo_landing =
                $ppc_month = $wm_month = $wm_queries = $wm_pages = 'checked';

                if( $profDetSetting ){
                  $setting = array();
                  $setting[ 'seo' ] = [ 'total', 'organic', 
                                      'medium', 'source_medium', 'landing' ];
                  $setting[ 'ppc' ] = [ 'month' ];
                  $setting[ 'wm' ] = [ 'month', 'queries', 'pages' ];
                  $seo_total = $seo_organic =
                  $seo_source_medium = $seo_medium = $seo_landing =
                  $ppc_month = $wm_month = $wm_queries = $wm_pages = '';
                  foreach($setting as $skey => $sRef){
                      $profDetSetting[ $skey ] = json_decode( $profDetSetting[ $skey ] );
                      $enableSett = array_intersect($sRef, $profDetSetting[ $skey ]);                      
                      if( $enableSett ){
                        foreach ($enableSett as $enKey) {
                          $sett_set = $skey.'_'.$enKey;
                          ${ $sett_set } = 'checked';
                        }
                      }
                  }                  
                }
              ?>
              <td>
                  <div class="card">
                    <div class="card-body">
                      <div class="m-b-10">
                          <label class="custom-control custom-checkbox">
                              <input  type="checkbox" class="custom-control-input" 
                                      name="seo[]" value="total" <?= $seo_total ?> >
                              <span class="custom-control-label">Total Traffic</span>
                          </label>
                      </div>
                      <div class="m-b-10">
                          <label class="custom-control custom-checkbox">
                              <input type="checkbox" class="custom-control-input" name="seo[]" 
                              value="organic" <?= $seo_organic ?> >
                              <span class="custom-control-label">Organic Traffic</span>
                          </label>
                      </div>
                      <div class="m-b-10">
                          <label class="custom-control custom-checkbox">
                              <input type="checkbox" class="custom-control-input" name="seo[]" 
                              value="medium"  <?= $seo_medium ?> >
                              <span class="custom-control-label">MEDIUM PERFORMANCE</span>
                          </label>
                      </div>
                      <div class="m-b-10">
                          <label class="custom-control custom-checkbox">
                              <input type="checkbox" class="custom-control-input" name="seo[]" 
                              value="source_medium" <?= $seo_source_medium ?> >
                              <span class="custom-control-label">SOURCE / MEDIUM PERFORMANCE</span>
                          </label>
                      </div>
                      <div class="m-b-10">
                          <label class="custom-control custom-checkbox">
                              <input type="checkbox" class="custom-control-input" 
                              name="seo[]" value="landing" <?= $seo_landing ?> >
                              <span class="custom-control-label">LANDING PAGE PERFORMANCE</span>
                          </label>
                      </div>
                    </div>
                  </div>
              </td>
            </tr>
            <tr>
              <td>Adwords (PPC)</td>
              <td>
                  <div class="card">
                    <div class="card-body">
                      <div class="m-b-10">
                          <label class="custom-control custom-checkbox">
                              <input type="checkbox" class="custom-control-input" 
                                name="ppc[]" value="month" <?= $ppc_month; ?>>
                              <span class="custom-control-label">Month Over Month</span>
                          </label>
                      </div>
                    </div>
                  </div>
              </td>
            </tr>
            <tr>
              <td>Google Search Console</td>
              <td>
                  <div class="card">
                    <div class="card-body">
                      <div class="m-b-10">
                          <label class="custom-control custom-checkbox">
                              <input type="checkbox" class="custom-control-input" 
                              name="wm[]" value="month"  <?= $wm_month; ?>>
                              <span class="custom-control-label">Month Over Month</span>
                          </label>
                      </div>
                      <div class="m-b-10">
                          <label class="custom-control custom-checkbox">
                              <input type="checkbox" class="custom-control-input" 
                              name="wm[]" value="queries" <?= $wm_queries; ?>>
                              <span class="custom-control-label">Queries</span>
                          </label>
                      </div>
                      <div class="m-b-10">
                          <label class="custom-control custom-checkbox">
                              <input type="checkbox" class="custom-control-input" 
                              name="wm[]" value="pages"  <?= $wm_pages; ?>>
                              <span class="custom-control-label">Pages</span>
                          </label>
                      </div>
                    </div>
                  </div>
              </td>
            </tr>
          </tbody>
        </table>
        </div>
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