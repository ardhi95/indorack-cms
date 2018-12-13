<div class="page-sidebar">
    <!-- START X-NAVIGATION -->
    <?php 
        echo $this->Tree->generate($menu,array(
            'model' 		=> 'CmsMenu',
            "cms_url"		=>	$param["cms_url"],
            "avatar"		=>	$param["avatar"],
            "admin_name"	=>	$param["admin_name"],
            "admin_group"	=>	$param["admin_group"],
			"admin_id"		=>	$param["admin_id"]
        ),$param["aco_id"]);
    ?>
    <!-- END X-NAVIGATION -->
</div>