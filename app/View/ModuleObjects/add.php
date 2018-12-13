<?php $this->start("script");?>
<script type="text/javascript" src="<?php echo $settings['cms_url']?>js/demo_icons.js"></script>
<script type="text/javascript" src="<?php echo $settings['cms_url']?>js/list.min.js"></script>
<script>
$(document).ready(function(){
});

function ChangeControllerName(elVal)
{
	$("input[name='data[<?php echo $ModelName?>][alias]']").val(elVal);
}
</script>
<?php $this->end();?>


<!-- START MODAL ICON PREVIEW -->
<div class="modal fade" id="iconPreview" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Icon preview</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="icon-preview"></div>
                    </div>
                    <div class="col-md-8">
                        <ul class="list-group border-bottom">
                            <li class="list-group-item icon-preview-span"></li>
                            <li class="list-group-item icon-preview-i"></li>
                            <li class="list-group-item icon-preview-class" id="classIcon"></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0);" class="btn btn-primary" onClick="SelectIcon();">select</a>
            </div>
        </div>
    </div>
</div>
<!-- END MODAL ICON PREVIEW -->
<!-- START BREADCRUMB -->
<ul class="breadcrumb push-down-0">
    <li>
        <a href="<?php echo $settings['cms_url'].$ControllerName ?>">
            <?php echo Inflector::humanize(Inflector::underscore($ControllerName))?>
        </a>
    </li>
    <li class="active"><?php echo __('Add New Data')?></li>
</ul>
<!-- END BREADCRUMB -->

<!-- PAGE TITLE -->
<div class="content-frame">
    <div class="content-frame-top">
        <div class="page-title">
            <h2><span class="fa fa-th-large"></span> <?php echo Inflector::humanize(Inflector::underscore($ControllerName))?></h2>
        </div>
        <div class="pull-right">
            <a href="<?php echo $settings['cms_url'].$ControllerName?>" class="btn btn-primary">
                <i class="fa fa-bars"></i> <?php echo __('List Data')?>
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
				echo $this->Session->flash("success");
			?>
        	<?php if(!empty($controllerList)):?>
        	<?php echo $this->Form->create($ModelName, array('url' => array("controller"=>$ControllerName,"action"=>"Add"),'class' => 'form-horizontal',"type"=>"file","novalidate")); ?>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <?php echo __('Add New Data')?>
                    </h3>
                </div>
                <div class="panel-body">
                	<div class="col-md-12">

                        <?php echo $this->Form->input("controller",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("Controller Name")),
                                "between"		=>	'<div class="col-md-5">',
                                "after"			=>	"</div>",
                                "autocomplete"	=>	"off",
                                "options"		=>	$controllerList,
                                "class"			=>	'form-control select',
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
								"empty"					=>	__("Select Controller"),
								"data-live-search"		=>	"true",
								"onchange"				=>	"ChangeControllerName(this.value)"
                            )
                        )?>

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
                	<a href="<?php echo $settings['cms_url'].$ControllerName?>" class="btn btn-default"><?php echo __('Cancel')?></a>
                	<input type="submit" value="<?php echo __('Submit')?>" class="btn btn-primary pull-right" />
                </div>
            </div>
            </form>
            <?php elseif(empty($this->Session->check("success"))):?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo __d('validation','All controllers already used, you must create more controllers to add more module!')?>
                        </div>
            <?php endif;?>
        </div>
    </div>
</div>
