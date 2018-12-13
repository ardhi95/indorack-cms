<?php
$dashBoardMenu	=	"";
if(strtolower($data['controller']) == "dashboards")
{
	$dashBoardMenu	=	'class="active"';
}
?>
<div class="page-sidebar">
    <!-- START X-NAVIGATION -->
    <ul class="x-navigation">
        <li class="xn-logo">
            <a href="#"><?php echo $data['admin_name']?></a>
            <a href="#" class="x-navigation-control"></a>
        </li>
        <li class="xn-profile">
            <a href="#" class="profile-mini">
                <img src="<?php echo $data['avatar']?>" alt="<?php echo $data['admin_name']?>"/>
            </a>
            <div class="profile">
                <div class="profile-image">
                    <img src="<?php echo $data['avatar']?>" alt="<?php echo $data['admin_name']?>"/>
                </div>
                <div class="profile-data">
                    <div class="profile-data-name"><?php echo $data['admin_name']?></div>
                    <div class="profile-data-title"><?php echo $data['admin_email']?></div>
                </div>
            </div>                                                                        
        </li>
        <li class="xn-title">Dashboard</li>
        <li <?php echo $dashBoardMenu?>>
            <a href="<?php echo $data['cms_url']?>">
            	<span class="fa fa-desktop"></span>
                <span class="xn-text">Dashboard</span>
            </a>    
        </li>
        <?php if(!empty($customerDevice)):?>
        <li class="xn-title">Monitoring</li>
        <?php foreach($customerDevice as $deviceData):?>
        <?php $activeClass	=	($deviceData['UserDevice']['id'] == $data['user_device_id']) ? 'class="active"' : '';?>
        <li <?php echo $activeClass?>>
            <a href="<?php echo $data['cms_url']."DeviceLogs/Index/".$deviceData['UserDevice']['id']; ?>">
            	<span class="fa fa-arrow-circle-o-right"></span>
                <span class="xn-text"><?php echo strtoupper($deviceData['UserDevice']['name'])?></span>
            </a>                        
        </li>
        <?php endforeach;?>
        <?php endif;?>
        <li class="xn-title">Settings</li>
        <li>
            <a href="index.html">
            	<span class="fa fa-cog"></span>
                <span class="xn-text">Settings</span>
            </a>    
        </li>
    </ul>
    <!-- END X-NAVIGATION -->
</div>