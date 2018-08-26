<div class="row page-titles">
    <div class="col-md-6 col-8 align-self-center">
        <h3 class="text-themecolor m-b-0 m-t-0"><?= isset( $page_title ) ? $page_title : 'Page Title'  ?></h3>
        <ol class="breadcrumb">
            <?php
                $blist = '<li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>';
                $breadcrumbs = $this->breadcrumb->getBreadcrumbArray();
                if($breadcrumbs){                    
                    $blist = '<li class="breadcrumb-item"><a href="'.base_url().'">Home</a></li>';
                    $ttl = count($breadcrumbs);
                    $cnt = 1;
                    foreach( $breadcrumbs as $key => $bcrumb ){
                        if( $cnt ==  $ttl ){
                            $blist .= '<li class="breadcrumb-item active">'.$bcrumb[ 'name' ].'</li>';
                        } else {
                            $blist .= '<li class="breadcrumb-item"><a href="'.$bcrumb[ 'url' ].'">'.$bcrumb[ 'name' ].'</a></li>';
                        }
                        $cnt++;
                    }
                    echo $blist;
                }
            ?>
        </ol>
    </div>
    <div class="col-md-6 col-4 align-self-center">
        <?php

        // <button class="right-side-toggle waves-effect waves-light btn-info btn-circle btn-sm pull-right m-l-10"><i class="ti-settings text-white"></i></button>
        // <button class="btn pull-right hidden-sm-down btn-success"><i class="mdi mdi-plus-circle"></i> Create</button>
        // <div class="dropdown pull-right m-r-10 hidden-sm-down">
        //     <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        //         January 2017
        //     </button>
        //     <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        //         <a class="dropdown-item" href="#">February 2017</a>
        //         <a class="dropdown-item" href="#">March 2017</a>
        //         <a class="dropdown-item" href="#">April 2017</a>
        //     </div>
        // </div>
        
        ?>
    </div>
</div>