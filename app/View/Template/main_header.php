
<!-- START X-NAVIGATION VERTICAL -->
<ul class="x-navigation x-navigation-horizontal x-navigation-panel">
    <!-- TOGGLE NAVIGATION -->
    <li class="xn-icon-button">
        <a href="#" class="x-navigation-minimize">
        <span class="fa fa-dedent"></span></a>
    </li>
    <!-- END TOGGLE NAVIGATION -->
    <!-- SEARCH -->
    <!--li class="xn-search">
        <form role="form">
            <input type="text" name="search" placeholder="Search..."/>
        </form>
    </li-->
    <!-- END SEARCH -->
    <!-- POWER OFF -->
    <li class="xn-icon-button pull-right last">
        <a href="#"><span class="fa fa-power-off"></span></a>
        <ul class="xn-drop-left animated zoomIn">
            <li>
                <a href="<?php echo $settings["cms_url"]?>Account/CreateLockScreenCookie">
                    <span class="fa fa-lock"></span>Lock Screen
                </a>
            </li>
            <li>
                <a href="<?php echo $settings["cms_url"]?>Account/LogOut">
                    <span class="fa fa-sign-out"></span> Sign Out
                </a>
            </li>
        </ul>
    </li>
    <!-- END POWER OFF -->
    <!-- MESSAGES -->
    <li class="xn-icon-button pull-right">
        <a href="#"><span class="fa fa-bell"></span></a>
        <!--div class="informer informer-danger">4</div-->
        <div class="panel panel-primary animated zoomIn xn-drop-left xn-panel-dragging">
            <div class="panel-heading">
                <h3 class="panel-title"><span class="fa fa-bell"></span> Activities</h3>
                <!--div class="pull-right">
                    <span class="label label-danger">4 new</span>
                </div-->
            </div>
            <div class="panel-body list-group list-group-contacts scroll" style="height: 200px;">
                <a href="#" class="list-group-item">
                    <div class="list-group-status status-online"></div>
                    <span class="contacts-title">New Order - NPO399200992</span>
                    <p>13 Feb 17 12:00</p>
                </a>
                <a href="#" class="list-group-item">
                    <div class="list-group-status status-online"></div>
                    <span class="contacts-title">ORDER COMPLETE - NPO399200992</span>
                    <p>13 Feb 17 14:12</p>
                </a>
                <a href="#" class="list-group-item">
                    <div class="list-group-status status-offline"></div>
                    <span class="contacts-title">ORDER FAILED - NPO399200992</span>
                    <p>13 Feb 17 14:19</p>
                </a>
                <a href="#" class="list-group-item">
                    <div class="list-group-status status-offline"></div>
                    <span class="contacts-title">ON PROGRESS - NPO399200992</span>
                    <p>13 Feb 17 13:05</p>
                </a>
            </div>
            <div class="panel-footer text-center">
                <a href="javascript:void(0);">Show all activities</a>
            </div>
        </div>
    </li>
    <!-- END MESSAGES -->
    
    <!-- LANG BAR -->
    <!--li class="xn-icon-button pull-right">
    	<?php
			$flagClass	=	"flag-".Configure::read('Config.language');
		?>
        <a href="#"><span class="flag <?php echo $flagClass?>"></span></a>
        <ul class="xn-drop-left xn-drop-white animated zoomIn">
            <li><a href="<?php echo $param['currentUrl'] ?>?lang=eng"><span class="flag flag-eng"></span> English</a></li>
            <li><a href="<?php echo $param['currentUrl'] ?>?lang=idn"><span class="flag flag-idn"></span> Indonesia</a></li>
        </ul>
    </li>
    <!-- END LANG BAR -->
</ul>
<!-- END X-NAVIGATION VERTICAL -->
