<!DOCTYPE html>
<html lang="id" class="body-full-height">
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
    	<!-- CONTENT -->
		<?php echo $this->fetch('content'); ?>
		<!-- CONTENT -->
        
   <!-- START SCRIPTS -->
        <!-- START PLUGINS -->
        <script type="text/javascript" src="<?php echo $this->webroot?>js/plugins/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo $this->webroot?>js/plugins/jquery/jquery-ui.min.js"></script>
        <script type="text/javascript" src="<?php echo $this->webroot?>js/plugins/bootstrap/bootstrap.min.js"></script>        
        <script type='text/javascript' src='<?php echo $this->webroot?>js/plugins/icheck/icheck.min.js'></script>
        <!-- END PLUGINS -->

        <!-- START TEMPLATE -->                
        
		<script type="text/javascript" src="<?php echo $this->webroot?>js/plugins.js"></script>
        <script type="text/javascript" src="<?php echo $this->webroot?>js/actions.js"></script>
        
        <!-- END TEMPLATE -->
    <!-- END SCRIPTS -->
        
        <?php echo $this->element('sql_dump'); ?>
        <?php echo $this->fetch('script'); ?>
    <!-- END SCRIPTS -->
    </body>
</html>