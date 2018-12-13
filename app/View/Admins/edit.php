<?php $this->start("script");?>
<script>
$(document).ready(function(){
	$("input:radio[name='data[User][status]']").on('ifChecked', function(){
		if($(this).val() == "0")
		{
			$("#UserAroId > option").removeAttr('selected');
			$("#UserAroId").attr("disabled","disabled").selectpicker('refresh');
		}
		else
		{
			$("#UserAroId").removeAttr("disabled").selectpicker('refresh');
		}
	});
});

$("#UserEditForm").bind("submit",function(){
	var status			=	$("input[name='data[User][status]']:checked").val();
	if(status == "0")
	{
		noty({
			text: "Deactivate admin will make this admin no longer can login to mobile apps or website any more and will destroy all his/her privileges. Do you really want to continue?",
			layout: 'topCenter',
			buttons: [
					{
						addClass: 'btn btn-success btn-clean', text: 'Yes', onClick: function($noty) {
							$noty.close();
							return true;
					}
					},
					{
						addClass: 'btn btn-danger btn-clean', text: 'Cancel', onClick: function($noty) {
							$noty.close();
						}
					}
				]
		});
		return false;
	}
	return true;
});
</script>
<?php $this->end();?>



<!-- START BREADCRUMB -->
<ul class="breadcrumb push-down-0">
    <li><a href="<?php echo $settings["cms_url"].$ControllerName?>"><?php echo Inflector::humanize(Inflector::underscore($ControllerName))?></a></li>                    
    <li class="active">Edit Data</li>
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
                <i class="fa fa-bars"></i> List Data
            </a>
            <a href="<?php echo $settings['cms_url'].$ControllerName?>/Add" class="btn btn-primary">
                <i class="fa fa-plus"></i> Add New Data
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
            
			<?php echo $this->Form->hidden("save_flag",array("id"=>"SaveFlag","value"=>"0"))?>
            <?php
            	$disabled	=	"";
				if($detail["User"]["id"] == "45")
				{
					$disabled	=	"disabled";
				}
			?>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Edit Data
                    </h3>
                </div>
                <div class="panel-body">
                	<div class="col-md-12">
                    	<?php echo $this->Form->input("firstname",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>"First Name"),
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
								"disabled"		=>	true
                            )
                        )?>
                        <?php echo $this->Form->input("lastname",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>"Last Name"),
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
                        
                        <?php echo $this->Form->input("email",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array("class"	=>	"col-md-3 control-label"),
                                "between"		=>	'<div class="col-md-5">',
                                "after"			=>	"</div>",
                                "autocomplete"	=>	"off",
                                "type"			=>	"email",
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
                        
                        <?php echo $this->Form->input("password",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array("class"	=>	"col-md-3 control-label"),
                                "between"		=>	'<div class="col-md-5">',
                                "after"			=>	"</div>",
                                "autocomplete"	=>	"new-password",
                                "type"			=>	"password",
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
                        
                        <?php 
						
						if($detail[$ModelName]["aro_id"] != $superAdminAro["MyAro"]["id"])
						{
							
							if(
									$profile[$ModelName]["aro_id"] == $superAdminAro["MyAro"]["id"] 
								or
									(
											$profile["User"]["aro_id"] == $premiumAdminAro["MyAro"]["id"]
										and
											$detail[$ModelName]["aro_id"] != $premiumAdminAro["MyAro"]["id"]	
									)
							)
							{
								echo $this->Form->input("aro_id",
									array(
										"div"			=>	array("class"=>"form-group"),
										"label"			=>	array("class"	=>	"col-md-3 control-label","text"=>"Admin Group"),
										"between"		=>	'<div class="col-md-5">',
										"after"			=>	"</div>",
										"autocomplete"	=>	"off",
										"options"		=>	$aro_id_list,
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
										"empty"					=>	"Select Admin Group",
										"data-live-search"		=>	"true",
										"disabled"				=>	$disabledParent
									)
								);
								
								echo $this->Form->input("status",
									array(
										"div"			=>	array("class"=>"form-group"),
										"before"		=>	'<label class="col-md-3 control-label">Status</label><div class="col-md-5"><label class="check">',
										"after"			=>	'</label></div>',
										"separator"		=>	'</label><label class="check">',
										"label"			=>	false,
										"options"		=>	array("1"=>"Active","0"=>"Not Active"),
										"class"			=>	'iradio',
										'error' 		=>	array(
											'attributes' => array(
												'wrap' 	=> 'label', 
												'class' => 'error'
											)
										),
										"type"			=>	"radio",
										"legend"		=>	false,
										"default"		=>	"1"
									)
								);
							}
						}
						?>
                    </div>
                </div>
                <div class="panel-footer">
                	<a href="<?php echo $settings['cms_url'].$ControllerName?>" class="btn btn-danger"><span class="fa fa-times fa-left"></span> Cancel</a>
                    <button type="submit" onclick="OnClickSaveDirect()" class="btn btn-primary pull-right" style="margin-left:10px;">Save<span class="fa fa-floppy-o fa-right"></span></button>
                    <button type="submit" onclick="OnClickSaveStay()" class="btn btn-primary pull-right" >Save and stay<span class="fa fa-floppy-o fa-right"></span></button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>