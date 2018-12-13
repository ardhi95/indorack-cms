<?php echo $this->start("css");?>
<style>
.circleBase {
	-moz-border-radius: 50%;
	-webkit-border-radius: 50%;
	border-radius: 50%;
	width: 85px;
	height: 85px;
	border: 3px solid #FFF;
	background: #e7e7e7;
	margin:15px auto 7px auto;
	text-align:center;
	line-height:70px;
	/*background-image:linear-gradient(#ececec, #c8c8c8);
	box-shadow: 0 0 5px rgba(0, 0, 0, .5);
	-moz-box-shadow: 0 0 5px rgba(0,0,0,0.5); 
	-webkit-box-shadow: 0 0 5px rgba(0,0,0,0.5); 
	-o-box-shadow: 0 0 5px rgba(0,0,0,0.5);*/
}

.circleBase img{
	width:50px;
	height:50px;
}

.widget .widget-int{
  font-size: 10px;
  line-height: 30px;
}

.widget-int span{
	font-size:30px;
}

.widget-bottom{
	width:100%;
	position:absolute;
	bottom:0;
	left:0;
	height:55px;
	padding-left:80px;
	padding-top:16px;
}
.widget-bottom-data
{
	width:100%;
	float:left;
	height:50px;
}
.widget-bottom-left
{
	width:49%;
	float:left;
	padding-left:10px;
	font-size:10px;
	line-height:15px;
}

.widget-bottom-left span
{
	width:100%;
	float:left;
	font-size:18px;
}

</style>
<?php echo $this->end();?>

<?php echo $this->start("script");?>
<script type="text/javascript" src="<?php echo $settings['cms_url']?>js/date.format.js"></script>
<?php echo $this->end();?>


<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="javascript:void(0);">Home</a></li>
    <li class="active">Dashboard</li>
</ul>
<!-- END BREADCRUMB -->

<!-- PAGE TITLE -->
<div class="page-title">
    <h2><span class="fa fa-desktop"></span> <?php echo Inflector::humanize(Inflector::underscore($ControllerName))?></h2>
</div>
<!-- END PAGE TITLE -->

<!-- START PAGE CONTENT WRAPPER -->
<div class="page-content-wrap">
    <!-- START ROW -->
    <div class="row">
    </div>
    <!-- START ROW -->
    
</div>
<!-- END PAGE CONTENT WRAPPER -->
