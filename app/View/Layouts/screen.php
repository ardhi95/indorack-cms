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
        <style type="text/css">
            .x-navigation div h3, p{
                color: white;
                padding-right: 10px;
                margin-bottom: 5px;
                margin-top: 5px; 
                text-align: center;
            }
            .cust-logo h2{
                color: white;
                padding-top: 5px;
                margin-top: 2px;

            }
            .cust-logo h2 img{
                margin-left: 10px;
                height: 30px;
                width: 30px;
            }
            body{
                background-color: #8f9191;
            }
            .panel-body{
                background-color: #262828;   
            }
            .row{
                background-color: #8f9191;
            }
            tr{
                color: white;
            }

            /*Status*/
            .notassign{
                /*background-color: #B71C1C;*/
                /*background-color: #5d5f63;*/
            }
            .assigned{
               /* background-color: #0085b6;*/
               /*background-color: #5d5f63;*/
            }
            .accepted{
                /*background-color: #23bb0f;*/
                /*background-color: #5d5f63;*/
            }
            .onprogress{
                /*background-color: #6a6e00;*/
                /*background-color: #5d5f63;*/
            }
            .completed{
                /*background-color: #5d5f63;*/
            }
            .rejected{
                /*background-color: #7f7fff;*/
                background-color: red;
            }
            .cancelled{
                /*background-color: #ab0065;*/
                /*background-color: #5d5f63;*/  
            }

            @-webkit-keyframes invalid {
              from { background-color: red; }
              to { background-color: inherit; }
          }
          @-moz-keyframes invalid {
              from { background-color: red; }
              to { background-color: inherit; }
          }
          @-o-keyframes invalid {
              from { background-color: red; }
              to { background-color: inherit; }
          }
          @keyframes invalid {
              from { background-color: red; }
              to { background-color: inherit; }
          }
          .invalid {
              -webkit-animation: invalid 1s infinite; /* Safari 4+ */
              -moz-animation:    invalid 1s infinite; /* Fx 5+ */
              -o-animation:      invalid 1s infinite; /* Opera 12+ */
              animation:         invalid 1s infinite; /* IE 10+ */
          }

        </style>

        <script type="text/javascript"></script>
        <script type="text/javascript" src="<?php echo $this->webroot?>js/plugins/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo $this->webroot?>js/moment.js"></script>
    </head>
    <body>

        <!-- PAGE CONTENT -->
            <div class="page-content">
                <!-- START X-NAVIGATION VERTICAL -->
                <ul class="x-navigation x-navigation-horizontal x-navigation-panel">
                    
                    <div class="pull-right">
                        <h3 id="divtime"></h3>
                        <p id="divUTC"></p>
                    </div> 
                    <div class="cust-logo">
                        <h2>
                            <img src="<?php echo $this->webroot?>img/logo-small.png">
                            INDORACK
                        </h2>
                    </div>                    
            </div>            
        <!-- END PAGE CONTENT -->
        <!-- PAGE CONTENT WRAPPER -->
        <script>
            $(function(){
              setInterval(function(){
                var divUtc = $('#divUTC');
                var divtime = $('#divtime');  
                //put UTC time into divUTC  
                divUtc.text(moment().format('dddd, MMMM DD, YYYY'));
                divtime.text(moment().format('HH:mm:ss'));
              },1000);

              /*Tabel Pengiriman*/
              $('#reloadtable').load('<?php echo $settings['cms_url'] ?>Screenorder/ListItemPengiriman');
              /*Tabel Perakitan*/
              $('#reloadtablerakit').load('<?php echo $settings['cms_url'] ?>Screenorder/ListItemPerakitan');
              
              setInterval(function(){
                $('#reloadtable').load('<?php echo $settings['cms_url'] ?>Screenorder/ListItemPengiriman');
                $('#reloadtablerakit').load('<?php echo $settings['cms_url'] ?>Screenorder/ListItemPerakitan');
              },10000);

              

            });
        </script>

<div class="page-content-wrap">
    <!-- START ROW -->
    <div class="row">
        <div class="col-md-12">
            <!-- <form class="form-horizontal"> -->
                <!-- START PANEL -->
                <div class="black-panel">
                    
                    <div class="panel-body" style="background-color: #8f9191;">                                                                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading" style="background-color: #fffb11;">
                                        
                                        <h1 class="panel-title">
                                            <i class="fa fa-truck"></i> &nbsp;Pengiriman</h1>
                                    </div>
                                    <div class="back-tables">
                                    <div class="panel-body">
                                        <div id="reloadtable" class="table-responsive" >
                                            
                                        </div>
                                    </div>
                                </div>

                                </div>                                            
                            </div>
                            <div class="col-md-6">
                                <!-- START STRIPED TABLE SAMPLE -->
                                <div class="panel panel-default">
                                    <div class="panel-heading" style="background-color: #fffb11;">
                                        <h1 class="panel-title">
                                            <i class="fa fa-wrench"></i> &nbsp;Perakitan</h1>
                                    </div>
                                    <div class="panel-body">
                                        <div id="reloadtablerakit" class="table-responsive">
                                            
                                        </div>
                                    </div>
                                </div>
                                <!-- END STRIPED TABLE SAMPLE -->
                            </div>

                        </div>

                    </div>
                </div>
                <!-- END PANEL -->
            <!-- </form> -->
        </div>
    </div>
    <!-- END ROW -->
</div>

<!-- END PAGE CONTENT WRAPPER -->
<script type="text/javascript" src="<?php echo $this->webroot?>js/plugins/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo $this->webroot?>js/moment.js"></script>
    </body>
    </html>