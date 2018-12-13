<?php $this->start("script");?>
<script type="text/javascript" src="<?php echo $settings['cms_url']?>js/demo_icons.js"></script>
<script type="text/javascript" src="<?php echo $settings['cms_url']?>js/list.min.js"></script>
<script>

$(document).ready(function(){
});
</script>
<?php $this->end();?>



<!-- START BREADCRUMB -->
<ul class="breadcrumb push-down-0">
    <li><a href="<?php echo $settings['cms_url'].$ControllerName ?>"><?php echo Inflector::humanize(Inflector::underscore($ControllerName))?></a></li>
    <li class="active"><?php echo __('Edit Data')?></li>
</ul>
<!-- END BREADCRUMB -->

<!-- PAGE TITLE -->
<div class="content-frame">
    <div class="content-frame-top">
        <div class="page-title">
            <h2><span class="fa fa-th-large"></span> <?php echo Inflector::humanize(Inflector::underscore($ControllerName))?></h2>
        </div>
        <div class="pull-right">
            <a href="<?php echo $settings['cms_url'].$ControllerName."/Index/".$page."/".$viewpage?>" class="btn btn-danger">
                <i class="fa fa-bars"></i> <?php echo __('List Data')?>
            </a>
            <a href="<?php echo $settings['cms_url'].$ControllerName?>/Add" class="btn btn-primary">
                <i class="fa fa-plus"></i> <?php echo __('Add New Data')?>
            </a>
        </div>
    </div>
</div>
<!-- END PAGE TITLE -->
<!-- START PAGE CONTENT WRAPPER -->
<div class="page-content-wrap">
    <div class="row">
    	<div class="col-md-12">
        	<?php
				echo $this->Session->flash();
			?>
        	<?php echo $this->Form->create($ModelName, array('url' => array("controller"=>$ControllerName,"action"=>"Edit",$ID,$page,$viewpage),'class' => 'form-horizontal',"type"=>"file","novalidate")); ?>
            <?php
				echo $this->Form->input('id', array(
					'type'			=>	'hidden',
					'readonly'		=>	'readonly'
				));
			?>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Edit Data
                    </h3>
                </div>
                <div class="panel-body">
                	<div class="col-md-12">
                    	<?php echo $this->Form->input("alias",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("Module Name")),
                                "between"		=>	'<div class="col-md-5">',
                                "after"			=>	"</div>",
                                "autocomplete"	=>	"off",
                                "type"			=>	"text",
                                "class"			=>	'form-control',
								'error' 		=>	array(
									'attributes' => array(
										'wrap' 	=> 'label',
										'class' => 'error'
									)
								),
								"format"		=>	array(
									'before',
									'label',
									'between',
									'input',
									'error',
									'after',
								),
                            )
                        )?>

                    </div>
                </div>
                <div class="panel-footer">
                	<a href="<?php echo $settings["cms_url"].$ControllerName?>/Index/<?php echo $page?>/<?php echo $viewpage?>" class="btn btn-default"><?php echo __('Cancel')?></a>
                	<input type="submit" value="<?php echo __('Submit')?>" class="btn btn-primary pull-right" />
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
