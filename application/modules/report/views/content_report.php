<div class="row">
    <!-- column -->
    <div class="col-12">
        <div class="card">
            <div class="card-body">                
                <div class="table-responsive">
                    <h4 class="card-title">Content Report For Month ( <?= $month ?> )</h4>
                    <h6 class="card-subtitle">Export data to Copy, CSV, Excel, PDF & Print</h6>
                    <table id="citation-tbl" class="display nowrap table table-hover table-striped table-bordered" 
                    cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Acc. Name</th>
                                <th>Date</th>
                                <th>Topic/Title</th>
                                <th>Keyword Focus</th>
                                <th>Blog Url</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Acc. Name</th>
                                <th>Date</th>
                                <th>Topic/Title</th>
                                <th>Keyword Focus</th>
                                <th>Blog Url</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php
                                $ccHtml = '';
                                foreach ($contents as $cc) {
                                    $ccHtml .= '<tr>
                                                    <td>'.$cc[ 'account_name' ].'</td>
                                                    <td>'.$cc[ 'content_date' ].'</td>
                                                    <td>'.$cc[ 'content_topic_title' ].'</td>
                                                    <td>'.$cc[ 'keyword_focus' ].'</td>
                                                    <td>'.$cc[ 'blog_url' ].'</td>
                                                </tr>';
                                }
                                echo $ccHtml;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>