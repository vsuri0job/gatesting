<div class="row">
    <div class="col-lg-12">
        <div class="card card-outline-info">
            <div class="card-header">
                <h4 class="m-b-0 text-white"><?= $profDet[ 'account_url' ]; ?></h4>
            </div>
            <div class="card-body">
                <form name="searchBoard" id="searchBoard" action="<?= base_url( 'social/link_trello/' ).$profDet[ 'id' ] ?>" method="POST">
                    <input type="hidden" name="profId" value="<?= $profDet[ 'id' ]; ?>">
                    <div class="form-body">
                        <div class="row p-t-20">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Boards</label>
                                    <select name="board" id="board" class="form-control">
                                        <?php
                                            $liHtml = '';
                                            foreach( $boards as $board_id => $board_name ){
                                                $liHtml .= '<option value="'.$board_id.'">'.$board_name.'</option>';
                                            }
                                            echo $liHtml;
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!--/row-->
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-info"> <i class="fa fa-check"></i>Link Board</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12" id="cardResult">
    </div>
</div>