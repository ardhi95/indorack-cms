<!DOCTYPE html>
<html lang="id" >
	<head>
        <!-- META SECTION -->
        <title><?php echo $settings["cms_title"]?></title>

        <meta name="title" content="<?php echo $settings["cms_title"]?>" />
        <meta name="description" content="<?php echo $settings["cms_description"]?>" />
        <meta name="keywords" content="<?php echo $settings["cms_keywords"]?>" />
        <meta name="author" content="<?php echo $settings["cms_author"]?>" />

        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta http-equiv="x-ua-compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <meta http-equiv="cache-control" content="public" />
    	<meta http-equiv="expires" content="Mon, 22 Jul 2025 11:12:01 GMT" />

        <meta name="msapplication-TileColor" content="#ffffff">
    	<meta name="msapplication-TileImage" content="<?php echo $this->webroot?>favicon/ms-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="57x57" href="<?php echo $this->webroot?>favicon/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="<?php echo $this->webroot?>favicon/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="<?php echo $this->webroot?>favicon/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="<?php echo $this->webroot?>favicon/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="<?php echo $this->webroot?>favicon/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="<?php echo $this->webroot?>favicon/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="<?php echo $this->webroot?>favicon/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="<?php echo $this->webroot?>favicon/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $this->webroot?>favicon/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="<?php echo $this->webroot?>favicon/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $this->webroot?>favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="<?php echo $this->webroot?>favicon/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $this->webroot?>favicon/favicon-16x16.png">
        <link rel="manifest" href="<?php echo $this->webroot?>favicon/manifest.json">

        <!-- CSS INCLUDE -->
        <link rel="stylesheet" type="text/css" id="theme" href="<?php echo $this->webroot?>css/theme-default.css"/>
        <!-- EOF CSS INCLUDE -->

        <?php echo $this->fetch('css'); ?>
    </head>
    <body>
        <!-- ======== START PAGE CONTAINER ============ -->
        <div class="page-container">
            <!-- CMS MENU -->

            <?php
			$src_avatar	=	(!empty($profile["Thumbnail"]["url"])) ? $profile["Thumbnail"]["host"].$profile["Thumbnail"]["url"]."?t=".time() : $this->webroot."img/default_avatar.jpg";
			
			echo $this->requestAction(
			array(
				"controller"	=>	"Template",
				"action"		=>	"CmsMenu"
			),
			array(
				"param"			=>	array(
					"cms_url"				=>	$settings["cms_url"],
					"avatar"				=>	$src_avatar,
					"admin_name"			=>	$profile["User"]["fullname"],
					"admin_id"				=>	$profile["User"]["id"],
					"admin_group"			=>	$profile["MyAro"]["alias"],
					"aco_id"				=>	$aco_id,
					"time"					=>	time()
				),
				"return"
			));
            ?>
            <!-- CMS MENU -->

            <!-- PAGE CONTENT -->
            <div class="page-content">

                <!-- HEADER -->
                <?php
				echo $this->requestAction(
					"/Template/MainHeader",
					array(
						"param"		=>	array(
							"currentUrl"	=>	$this->Html->url(null, true)
						),
						"return"
					)
				);?>
                <!-- HEADER -->

                <!-- CONTENT -->
				<?php echo $this->fetch('content'); ?>
                <!-- CONTENT -->

                <?php if(Configure::read("debug") == "2"):?>
                <div class="page-body">
                	<?php echo $this->element('sql_dump'); ?>
                </div>
                <?php endif;?>

            </div>
            <!-- PAGE CONTENT -->
        </div>

        <!-- ======== END PAGE CONTAINER ============ -->



		<!-- =============== START SCRIPTS ============== -->
        <!-- START PLUGINS -->
        <script>
        var cms_url	=	'<?php echo $settings['cms_url']?>';
		var webroot	=	'<?php echo $this->webroot?>';
        </script>
        <script type="text/javascript" src="<?php echo $this->webroot?>js/plugins/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo $this->webroot?>js/plugins/jquery/jquery-migrate.min.js"></script>

        <script type="text/javascript" src="<?php echo $this->webroot?>js/plugins/jquery/jquery-ui.min.js"></script>
        <script type="text/javascript" src="<?php echo $this->webroot?>js/plugins/bootstrap/bootstrap.min.js"></script>
        <!-- END PLUGINS -->

        <!-- START THIS PAGE PLUGINS-->
        <script type='text/javascript' src='<?php echo $this->webroot?>js/plugins/icheck/icheck.min.js'></script>
        <script type="text/javascript" src="<?php echo $this->webroot?>js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>
        <script type="text/javascript" src="<?php echo $this->webroot?>js/plugins/rickshaw/d3.v3.js"></script>
        <script type="text/javascript" src="<?php echo $this->webroot?>js/plugins/rickshaw/rickshaw.min.js"></script>
        <script type="text/javascript" src="<?php echo $this->webroot?>js/plugins/bootstrap/bootstrap-datepicker.js"></script>
        <script type="text/javascript" src="<?php echo $this->webroot?>js/plugins/owl/owl.carousel.min.js"></script>
        <script type="text/javascript" src="<?php echo $this->webroot?>js/jquery.form.min.js"></script>
        <script type="text/javascript" src="<?php echo $this->webroot?>js/plugins/bootstrap/bootstrap-select.js"></script>
        <script type='text/javascript' src='<?php echo $this->webroot?>js/plugins/noty/jquery.noty.js'></script>
        <script type='text/javascript' src='<?php echo $this->webroot?>js/plugins/noty/layouts/topCenter.js'></script>
        <script type='text/javascript' src='<?php echo $this->webroot?>js/plugins/noty/layouts/topLeft.js'></script>
        <script type='text/javascript' src='<?php echo $this->webroot?>js/plugins/noty/layouts/topRight.js'></script>
        <script type='text/javascript' src='<?php echo $this->webroot?>js/plugins/noty/themes/default.js'></script>
        <script type="text/javascript" src="<?php echo $this->webroot?>js/plugins/nestable/jquery.nestable.js"></script>
        <script type="text/javascript" src="<?php echo $this->webroot?>js/plugins/knob/jquery.knob.min.js"></script>
        <!-- END THIS PAGE PLUGINS-->

        <!-- START TEMPLATE -->
        <script type="text/javascript" src="<?php echo $this->webroot?>js/settings.js"></script>
        <script type="text/javascript" src="<?php echo $this->webroot?>js/plugins.js"></script>
        <script type="text/javascript" src="<?php echo $this->webroot?>js/actions.js"></script>
        <!-- END TEMPLATE -->
    	<!-- =============== END SCRIPTS ============== -->

        <?php echo $this->fetch('script'); ?>
    <!-- END SCRIPTS -->
    </body>
</html>
