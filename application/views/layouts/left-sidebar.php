<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- User profile -->
        <div class="user-profile">
            <!-- User profile image -->
            <div class="profile-img"> <img src="<?= com_user_img() ?>" alt="user" /> </div>
            <!-- User profile text-->
            <div class="profile-text"> <a href="#" style="text-transform: capitalize;" class="dropdown-toggle link u-dropdown" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true"><?= com_user_data( 'username' ); ?><span class="caret"></span></a>
                <div class="dropdown-menu animated flipInY">
                    <a href="<?= base_url( 'dashboard/profile' ); ?>" class="dropdown-item"><i class="ti-user"></i> My Profile</a>
                    <a href="#" class="dropdown-item"><i class="ti-wallet"></i> My Balance</a>
                    <a href="#" class="dropdown-item"><i class="ti-email"></i> Inbox</a>
                    <div class="dropdown-divider"></div> <a href="#" class="dropdown-item"><i class="ti-settings"></i> Account Setting</a>
                    <div class="dropdown-divider"></div> <a href="<?= base_url( 'welcome/logout' ); ?>" class="dropdown-item"><i class="fa fa-power-off"></i> Logout</a>
                </div>
            </div>
        </div>
        <!-- End User profile text-->
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <li class="nav-devider"></li>
                <li class="">
                    <a class="" href="<?=base_url( 'accounts/list' )?>" aria-expanded="false">
                        <i class="mdi mdi-account-network"></i>
                        <span class="hide-menu">Accounts</span>
                    </a>
                </li>
                <li>
                    <a class="has-arrow " href="#" aria-expanded="false"><i class="mdi mdi-bullseye"></i><span class="hide-menu">SEO</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="<?=base_url( 'report/citation_and_content' ); ?>">Citation & Content</a></li>                        
                    </ul>
                </li>
                <li class="">
                    <a class="" href="<?=base_url()?>" aria-expanded="false"><i class="mdi mdi-bullseye"></i><span class="hide-menu">PPC</span></a>
                </li>
                <li class="">
                    <a class="has-arrow " href="#" aria-expanded="false"><i class="mdi mdi-bullseye"></i><span class="hide-menu">REPORT</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li>
                            <a  class="" href="<?=base_url('report/ganalyticreport')?>" 
                                aria-expanded="false"><i class="mdi mdi-bullseye"></i>
                                <span class="hide-menu">Google Analytic</span>
                            </a>
                        </li>
                        <li>
                            <a  class="" href="<?=base_url('report/gadwordreport')?>" 
                                aria-expanded="false"><i class="mdi mdi-bullseye"></i>
                                <span class="hide-menu">Google Adword</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="">
                    <a class="has-arrow " href="#" aria-expanded="false"><i class="mdi mdi-bullseye"></i><span class="hide-menu">TRELLO REPORT</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li>
                            <a  class="" href="<?=base_url('report/tboardreport')?>" 
                                aria-expanded="false"><i class="mdi mdi-bullseye"></i>
                                <span class="hide-menu">Boards</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="">
                    <a class="" href="<?=base_url()?>" aria-expanded="false"><i class="mdi mdi-bullseye"></i><span class="hide-menu">PPC Report</span></a>
                </li>
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
    <!-- Bottom points-->
    <div class="sidebar-footer">
        <!-- item-->
        <a href="" class="link" data-toggle="tooltip" title="Settings"><i class="ti-settings"></i></a>
        <!-- item-->
        <a href="" class="link" data-toggle="tooltip" title="Email"><i class="mdi mdi-gmail"></i></a>
        <!-- item-->
        <a href="<?= base_url( 'welcome/logout' ); ?>" class="link" data-toggle="tooltip" title="Logout"><i class="mdi mdi-power"></i></a>
    </div>
    <!-- End Bottom points-->
</aside>