<div>
    Profile: <?= $analytic_profile[ 'profile_name' ]; ?><br/>
    Property: <?= $analytic_profile[ 'property_name' ]; ?><br/>
    View: <?= $analytic_profile[ 'view_name' ]; ?>
</div>
<div class="table-responsive">
    <input type="hidden" name="analytic_id" value="<?= $analytic_profile[ 'id' ]; ?>">
    <table id="accountsTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Select</th>
                <th>Name</th>
                <th>Agency Name</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $accountHtml = '';
                foreach( $accounts as $account ){
                    $accountHtml .= '<tr>
                                        <td><input type="radio" name="account" 
                                                id="accountId'.$account[ 'id' ].'"
                                                value="'.$account[ 'id' ].'"></td>
                                        <td><label for="accountId'.$account[ 'id' ].'">'.$account[ 'name' ].'</label></td>
                                        <td><label for="accountId'.$account[ 'id' ].'">'.$account[ 'agency_name' ].'</label></td>
                                    </tr>';
                }
                echo $accountHtml;
            ?>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    $( document ).ready( function(){
        $('#accountsTable').DataTable( {} );
    });
</script>