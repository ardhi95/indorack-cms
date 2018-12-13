<?php $this->start("script");?>
<script type="text/javascript" src="<?php echo $settings['cms_url']?>js/demo_icons.js"></script>
<script type="text/javascript" src="<?php echo $settings['cms_url']?>js/list.min.js"></script>
<script>
function SelectIcon()
{
	$('#iconPreview').modal('toggle');
	$("#CmsMenuIconClass").val($("#classIcon").html());
	$("#listIcon").hide();
}

function CheckIsGroupSeparator()
{
	var radioValue	=	$("input:radio[name='data[CmsMenu][is_group_separator]']:checked").val();
	if(radioValue == "1")
	{
		$("#CmsMenuIconClass").val("");
		$("#CmsMenuIconClass").attr("disabled","disabled");

		$("#CmsMenuAcoId").val("");
		$("#CmsMenuAcoId").attr("disabled","disabled");
		$('#CmsMenuAcoId').selectpicker('refresh');
	}
	else
	{
		$("#CmsMenuIconClass").removeAttr("disabled");
		$('#CmsMenuAcoId').removeAttr('disabled');
		$('#CmsMenuAcoId').selectpicker('refresh');
	}
}

$(document).ready(function(){
	$("#CmsMenuIconClass").focus(function(){
		$("#listIcon").show();
	});

	$("input:radio[name='data[CmsMenu][is_group_separator]']").on('ifChecked', function(event){
		CheckIsGroupSeparator();
	});
	<?php if(!empty($this->data)):?>
	CheckIsGroupSeparator();
	<?php endif;?>

	var options = {
	  valueNames: [ 'name'],
	  listClass : "icons-list",
	  searchClass: "searchIcon"
	};

	var userList = new List('listIcon', options);

});
</script>
<?php $this->end();?>


<!-- START MODAL ICON PREVIEW -->
<div class="modal fade" id="iconPreview" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title"><?php echo __('Icon Preview')?></h4>
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
                <a href="javascript:void(0);" class="btn btn-primary" onClick="SelectIcon();"><?php echo __('select')?></a>
            </div>
        </div>
    </div>
</div>
<!-- END MODAL ICON PREVIEW -->
<!-- START BREADCRUMB -->
<ul class="breadcrumb push-down-0">
    <li>
        <a href="<?php echo $settings['cms_url'].$ControllerName ?>">
            CMS Menu
        </a>
    </li>
    <li class="active"><?php echo __('Add New Data')?></li>
</ul>
<!-- END BREADCRUMB -->

<!-- PAGE TITLE -->
<div class="content-frame">
    <div class="content-frame-top">
        <div class="page-title">
            <h2><span class="fa fa-th-large"></span>
                CMS Menu
            </h2>
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
    	<div class="col-md-12" style="display:none;" id="listIcon">
        	<!-- START FONT AWESOME ICONS -->
            <div class="panel-fullscreen-wrap">
                <div class="panel panel-default panel-fullscreened">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php echo __('Select Icon')?></h3>

                        <ul class="panel-controls">
                            <li><a href="javascript:void(0);" onClick="$('#listIcon').hide();"><span class="fa fa-times"></span></a></li>
                        </ul>

                        <div class="input-group col-md-2 pull-right" style="margin-right:20px;">
                            <span class="input-group-addon"><span class="fa fa-search"></span></span>
                            <input class="form-control searchIcon" placeholder="<?php echo __('Search...')?>" type="text" id="searchIconInput" autofocus="autofocus">
                         </div>
                    </div>
                    <div class="panel-body" style="height:512px;">
                        <?php echo $this->element("icon_list")?>
                    </div>
                    <div class="panel-footer">&nbsp;
                    </div>
               </div>
        	</div>
            <!-- END FONT AWESOME ICONS -->
        </div>
    	<div class="col-md-12">
        	<?php
				echo $this->Session->flash();
			?>
        	<?php echo $this->Form->create($ModelName, array('url' => array("controller"=>$ControllerName,"action"=>"Add"),'class' => 'form-horizontal',"type"=>"file","novalidate")); ?>

			<?php echo $this->Form->hidden("save_flag",array("id"=>"SaveFlag","value"=>"0"))?>

            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <?php echo __('Add New Data')?>
                    </h3>
                </div>
                <div class="panel-body">
                	<div class="col-md-12">
                    	<?php echo $this->Form->input("name",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("Name (*)")),
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
                        <?php echo $this->Form->input("parent_id",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("Parent (*)")),
                                "between"		=>	'<div class="col-md-5">',
                                "after"			=>	"</div>",
                                "autocomplete"	=>	"off",
                                "options"		=>	$parent_id_list,
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
								"empty"					=>	__("Select Parent Menu"),
								"data-live-search"		=>	"true",
                            )
                        )?>
                        <?php
						echo $this->Form->input("is_group_separator",
                            array(
                                "div"			=>	array("class"=>"form-group"),
								"before"		=>	'<label class="col-md-3 control-label">'.__('As Grouping Menu ?').'</label><div class="col-md-5"><label class="check">',
								"after"			=>	'</label></div>',
								"separator"		=>	'</label><label class="check">',
								"label"			=>	false,
                                "options"		=>	array("1"=>__("Yes"),"0"=>__("No")),
                                "class"			=>	'iradio',
								'error' 		=>	array(
									'attributes' => array(
										'wrap' 	=> 'label',
										'class' => 'error'
									)
								),
								"type"			=>	"radio",
								"legend"		=>	false,
								"default"		=>	"0",
								"onclick"		=>	"ChangeGroupOption()"
                            )
                        );
						?>
                        <?php
                        echo $this->Form->input("aco_id",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("Select Module")),
                                "between"		=>	'<div class="col-md-5">',
                                "after"			=>	"</div>",
                                "autocomplete"	=>	"off",
                                "options"		=>	$aco_id_list,
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
								"empty"					=>	__("Select Module"),
								"data-live-search"		=>	"true",
                            )
                        )?>

                        <?php echo $this->Form->input("icon_class",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("Icon Class")),
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

                        <?php
						/*echo $this->Form->input("url",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array("class"	=>	"col-md-3 control-label"),
                                "between"		=>	'<div class="col-md-5">
													 	<div class="input-group">
															<input type="text" disabled="disabled" value="'.substr($settings['cms_url'],0,-1).'" class="form-control"/>
															<span class="input-group-addon add-on">/</span>',
                                "after"			=>	"</div></div>",
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
                        )*/
						?>

                        <?php echo $this->Form->input("status",
                            array(
                                "div"			=>	array("class"=>"form-group"),
								"before"		=>	'<label class="col-md-3 control-label">Status</label><div class="col-md-5"><label class="check">',
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
                        )?>


                    </div>
                </div>
                <div class="panel-footer">
                	<a href="<?php echo $settings['cms_url'].$ControllerName?>" class="btn btn-danger"><span class="fa fa-times fa-left"></span> <?php echo __('Cancel')?></a>
                    <button type="submit" onclick="OnClickSaveDirect()" class="btn btn-primary pull-right" style="margin-left:10px;"><?php echo __('Save')?><span class="fa fa-floppy-o fa-right"></span></button>
                    <button type="submit" onclick="OnClickSaveStay()" class="btn btn-primary pull-right" ><?php echo __('Save and add more')?><span class="fa fa-floppy-o fa-right"></span></button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
