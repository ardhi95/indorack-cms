<?php $this->start("script");?>
<script type="text/javascript" src="<?php echo $this->webroot?>js/plugins/bootstrap/bootstrap-file-input.js"></script>
<script type="text/javascript" src="<?php echo $this->webroot?>js/plugins/cropper/cropper.min.js"></script>
<script type="text/javascript" src="<?php echo $this->webroot?>js/lang/<?php echo Configure::read('Config.language')?>/demo_edit_profile.js"></script>
<script type="text/javascript" src="<?php echo $settings['cms_url']?>js/jquery-prettyPhoto.js"></script>
<script>
$(document).ready(function(){
  $("a[rel^='lightbox']").prettyPhoto({
	  social_tools :''
  });
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

$('#ButtonSaveDirect').click(function(){
    OnClickSaveDirect();
    var status			=	$("input[name='data[User][status]']:checked").val();
	if(status == "0")
	{
		noty({
			text: "<?php echo __('Deactivate admin will make this admin no longer can login to mobile apps or website any more and will destroy all his/her privileges. Do you really want to continue ?')?>",
			layout: 'topCenter',
			buttons: [
					{
						addClass: 'btn btn-success btn-clean', text: '<?php echo __('Yes')?>', onClick: function($noty) {
							$noty.close();
                            $("#UserEditProfileForm").submit();
					}
					},
					{
						addClass: 'btn btn-danger btn-clean', text: '<?php echo __('Cancel')?>', onClick: function($noty) {
							$noty.close();
						}
					}
				]
		});
	}
    else {
        $("#UserEditProfileForm").submit();
    }
});

$('#ButtonSaveStay').click(function(){
    OnClickSaveStay();
    var status			=	$("input[name='data[User][status]']:checked").val();
	if(status == "0")
	{
		noty({
			text: "<?php echo __('Deactivate admin will make this admin no longer can login to mobile apps or website any more and will destroy all his/her privileges. Do you really want to continue ?')?>",
			layout: 'topCenter',
			buttons: [
					{
						addClass: 'btn btn-success btn-clean', text: '<?php echo __('Yes')?>', onClick: function($noty) {
							$noty.close();
                            $("#UserEditProfileForm").submit();
					}
					},
					{
						addClass: 'btn btn-danger btn-clean', text: '<?php echo __('Cancel')?>', onClick: function($noty) {
							$noty.close();
						}
					}
				]
		});
	}
    else {
        $("#UserEditProfileForm").submit();
    }
});


function OpenDialogUpload()
{
	$('#modal_change_photo').modal('show');
	$("#cp_target").html('');
}

</script>
<?php $this->end()?>

<?php $this->start("css");?>
<link rel="stylesheet" type="text/css" id="theme" href="<?php echo $this->webroot?>css/prettyPhoto.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot?>css/cropper/cropper.min.css"/>
<?php $this->end()?>

<!-- MODALS -->
<div class="modal animated fadeIn" id="modal_change_photo" tabindex="-1" role="dialog" aria-labelledby="smallModalHead" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                	<span aria-hidden="true">&times;</span>
                    <span class="sr-only"><?php echo __('Close')?></span>
                </button>
                <h4 class="modal-title" id="smallModalHead"><?php echo __('Change photo')?></h4>
            </div>
            <form id="cp_crop" method="post" action="<?php echo $settings['cms_url'].$ControllerName?>/CropImage/<?php echo $ID?>">
            <div class="modal-body">
                <div class="text-center" id="cp_target">
                	<?php echo __('Only (*.gif,*.jpeg,*.jpg,*.png) are allowed.')?>
                </div>
                <input type="hidden" name="cp_img_path" id="cp_img_path"/>
                <input type="hidden" name="ic_x" id="ic_x"/>
                <input type="hidden" name="ic_y" id="ic_y"/>
                <input type="hidden" name="ic_w" id="ic_w"/>
                <input type="hidden" name="ic_h" id="ic_h"/>
            </div>
            </form>
            <form id="cp_upload" method="post" enctype="multipart/form-data" action="<?php echo $settings['cms_url'].$ControllerName?>/UploadProfileImage/<?php echo $ID?>" >
            <div class="modal-body form-horizontal form-group-separated">
                <div class="form-group">
                    <label class="col-md-4 control-label"><?php echo __('New Photo')?></label>
                    <div class="col-md-4">
                        <input type="file" class="fileinput btn-info" name="data[<?php echo $ModelName?>][images]" id="cp_photo" data-filename-placement="inside" title="<?php echo __('Select file')?>" accept="image/*"/>
                    </div>
                </div>
            </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-success disabled" id="cp_accept"><?php echo __('Accept')?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close')?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal animated fadeIn" id="modal_change_password" tabindex="-1" role="dialog" aria-labelledby="smallModalHead" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="smallModalHead">Change Password</h4>
            </div>
            <div class="modal-body">
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer faucibus, est quis molestie tincidunt</p>
            </div>
            <div class="modal-body form-horizontal form-group-separated">
                <div class="form-group">
                    <label class="col-md-3 control-label">Old Password</label>
                    <div class="col-md-9">
                        <input type="password" class="form-control" name="old_password"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">New Password</label>
                    <div class="col-md-9">
                        <input type="password" class="form-control" name="new_password"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">Repeat New</label>
                    <div class="col-md-9">
                        <input type="password" class="form-control" name="re_password"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Proccess</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- EOF MODALS -->


<!-- START BREADCRUMB -->
<ul class="breadcrumb push-down-0">
    <li>
    	<a href="<?php echo $settings["cms_url"].$ControllerName?>">
		<?php echo Inflector::humanize(Inflector::underscore($ControllerName))?></a>
    </li>
    <li class="active"><?php echo __('Edit Data')?></li>
</ul>
<!-- END BREADCRUMB -->

<!-- PAGE TITLE -->
<div class="content-frame">
    <div class="content-frame-top">
        <div class="page-title">
            <h2>
            	<span class="fa fa-th-large"></span>
                <?php echo __('Edit Data')?> : <?php echo $detail[$ModelName]['fullname']?>
            </h2>
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

<!-- PAGE CONTENT WRAPPER -->
<div class="page-content-wrap">

    <div class="row">
    	<div class="col-md-12">
    	<?php
			echo $this->Session->flash();
		?>
        </div>
        <div class="col-md-3 col-sm-4 col-xs-5">

            <form action="#" class="form-horizontal">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h3><span class="fa fa-user"></span> <?php echo $detail["User"]["fullname"];?></h3>
                    <p><?php echo $detail["MyAro"]["alias"];?></p>
                    <div class="text-center" id="user_image">
                    	<?php if(!empty($detail["Thumbnail"]["id"])):?>
                        <a rel="lightbox" title="<?php echo $detail[$ModelName]['fullname'] ?>" href="<?php echo $detail["MaxWidth"]["host"].$detail["MaxWidth"]["url"]?>?time=<?php echo time()?>" style="border:0px;">
                            <img src="<?php echo $detail["Thumbnail"]["host"].$detail["Thumbnail"]["url"]?>?time=<?php echo time()?>" width="200" height="200" class="img-thumbnail"/>
                        </a>
                        <?php else:?>
                            <img src="<?php echo $this->webroot?>img/default_avatar.jpg" width="200" height="200" class="img-thumbnail"/>
                        <?php endif;?>
                    </div>
                </div>
                <div class="panel-body form-group-separated">
                    <div class="form-group">
                        <div class="col-md-12 col-xs-12">
                            <a href="javascript:void(0);" class="btn btn-primary btn-block " onclick="OpenDialogUpload()"><?php echo __('Change Photo')?></a>
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </div>

        <div class="col-md-6 col-sm-8 col-xs-7">
			<?php echo $this->Form->create($ModelName, array('url' => array("controller"=>$ControllerName,"action"=>"EditProfile",$ID,$page,$viewpage),'class' => 'form-horizontal',"type"=>"file","novalidate")); ?>

            <?php
				echo $this->Form->input('id', array(
					'type'			=>	'hidden',
					'readonly'		=>	'readonly'
				));
			?>

			<?php echo $this->Form->hidden("save_flag",array("id"=>"SaveFlag","value"=>"0"))?>

            <div class="panel panel-default">
                <div class="panel-body">
                    <h3><span class="fa fa-pencil"></span> <?php echo __('Profile')?></h3>
                </div>
                <div class="panel-body form-group-separated">
                <?php
					$disabled	=	"";
					if($detail["User"]["id"] == "45")
					{
						$disabled	=	"disabled";
					}
				?>
                    <?php echo $this->Form->input("firstname",
						array(
							"div"			=>	array("class"=>"form-group"),
							"label"			=>	array("class"	=>	"col-md-3 col-xs-5 control-label","text"=>__("First Name")),
							"between"		=>	'<div class="col-md-9 col-xs-7">',
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
							"disabled"	=>	$disabled
						)
					)?>
                    <?php echo $this->Form->input("lastname",
						array(
							"div"			=>	array("class"=>"form-group"),
							"label"			=>	array("class"	=>	"col-md-3 col-xs-5 control-label","text"=>__("Last Name")),
							"between"		=>	'<div class="col-md-9 col-xs-7">',
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
							"disabled"	=>	$disabled
						)
					)?>

					<?php echo $this->Form->input("email",
						array(
							"div"			=>	array("class"=>"form-group"),
							"label"			=>	array("class"	=>	"col-md-3 col-xs-5 control-label","text"=>__('Email')),
							"between"		=>	'<div class="col-md-9 col-xs-7">',
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
							"label"			=>	array("class"	=>	"col-md-3 col-xs-5 control-label","text"=>__("Password")),
							"between"		=>	'<div class="col-md-9 col-xs-7">',
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
										"label"			=>	array("class"	=>	"col-md-3 col-xs-5 control-label","text"=>__("Admin Group")),
										"between"		=>	'<div class="col-md-9 col-xs-7">',
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
										"empty"					=>	__("Select Admin Group"),
										"data-live-search"		=>	"true",
										"disabled"				=>	$disabledParent
									)
								);

								echo $this->Form->input("status",
									array(
										"div"			=>	array("class"=>"form-group"),
										"before"		=>	'<label class="col-md-3 col-xs-5 control-label">Status</label><div class="col-md-9 col-xs-7"><label class="check">',
										"after"			=>	'</label></div>',
										"separator"		=>	'</label><label class="check">',
										"label"			=>	false,
										"options"		=>	array("1"=>__("Active"),"0"=>__("Not Active")),
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
                    <div class="form-group">
                        <div class="col-md-12 col-xs-5">
                        	<a href="<?php echo $settings['cms_url'].$ControllerName?>" class="btn btn-danger"><span class="fa fa-times fa-left"></span> <?php echo __('Cancel')?></a>
                            <button type="button" id="ButtonSaveDirect" class="btn btn-primary  pull-right" style="margin-left:10px;"><?php echo __('Save')?><span class="fa fa-floppy-o fa-right"></span></button>
                            <button type="button" id="ButtonSaveStay" class="btn btn-primary  pull-right" ><?php echo __('Save and stay')?><span class="fa fa-floppy-o fa-right"></span></button>

                        </div>
                    </div>
                </div>
            </div>
            </form>
        </div>

        <div class="col-md-3">
            <div class="panel panel-default form-horizontal">
                <div class="panel-body">
                    <h3><span class="fa fa-info-circle"></span> <?php echo __('Quick Info')?></h3>
                </div>
                <div class="panel-body form-group-separated">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-5 control-label"><?php echo __('Last visit')?></label>
                        <div class="col-md-8 col-xs-7 line-height-30">
                        	<?php
                            if(!is_null($detail['User']['last_login_cms'])){
								echo date("M d, Y H:i",strtotime($detail['User']['last_login_cms']));
							}
							else
							{
								echo __("Have not login yet!");
							}
							?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 col-xs-5 control-label"><?php echo __('Registration')?></label>
                        <div class="col-md-8 col-xs-7 line-height-30">
                        	<?php
                            	echo date("M d, Y H:i",strtotime($detail['User']['created']));
							?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 col-xs-5 control-label"><?php echo __('Groups')?></label>
                        <div class="col-md-8 col-xs-7 line-height-30">
                        	<?php echo $this->General->IsEmptyVal($detail["MyAro"]["alias"],"-");?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- END PAGE CONTENT WRAPPER -->
