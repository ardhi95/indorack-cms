<?php $this->start("script");?>
<script type="text/javascript" src="<?php echo $settings['cms_url']?>js/jquery-prettyPhoto.js"></script>
<script type="text/javascript" src="<?php echo $settings['cms_url']?>js/plugins/bootstrap/bootstrap-file-input.js"></script>
<script type="text/javascript" src="<?php echo $settings['cms_url']?>js/plugins/tagsinput/jquery.tagsinput.min.js"></script>

<script>
$(document).ready(function(){
	$("a[rel^='lightbox']").prettyPhoto({
		social_tools :''
	});
	$("#<?php echo $ModelName?>MetaKeywords").tagsInput({width: '100%',height:'auto',defaultText: ""});
});

function PreviewImage(fileId,imageId,lighbox) {
	if ( window.FileReader && window.File && window.FileList && window.Blob )
	{
		var oFReader = new FileReader();
		oFReader.readAsDataURL(document.getElementById(fileId).files[0]);
		oFReader.onload = function (oFREvent) {
			document.getElementById(imageId).src = oFREvent.target.result;
			$(lighbox).attr("href",oFREvent.target.result);
			$("#imagePrev").hide(300);
			$("#imagePrev").show(300);
		};
	}
};

</script>
<?php $this->end();?>

<?php $this->start("css");?>
<link rel="stylesheet" type="text/css" id="theme" href="<?php echo $this->webroot?>css/prettyPhoto.css"/>
<?php $this->end();?>

<!-- START BREADCRUMB -->
<ul class="breadcrumb push-down-0">
    <li>
    	<a href="<?php echo $settings['cms_url'].$ControllerName?>">
			<?php echo Inflector::humanize(Inflector::underscore($ControllerName))?>
        </a>
    </li>
    <li class="active"><?php echo __("Add New Data")?></li>
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
        	<?php echo $this->Session->flash();?>
        	<?php echo $this->Form->create($ModelName, array('url' => array("controller"=>$ControllerName,"action"=>"Add"),'class' => 'form-horizontal',"type"=>"file","novalidate")); ?>

            <?php echo $this->Form->hidden("save_flag",array("id"=>"SaveFlag","value"=>"0"))?>

            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <?php echo __("Add New Data")?>
                    </h3>
                </div>
                <div class="panel-body">
                	<div class="col-md-12">
                    	<?php echo $this->Form->input("code",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("Product Code/ID (*)")),
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
								'maxLength'		=>	'100',
								"escape"		=>	false
                            )
                        )?>
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
								'maxLength'		=>	'100',
								"escape"		=>	false
                            )
                        )?>
                        
                        <?php echo $this->Form->input("category_id",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("Category (*)")),
                                "between"		=>	'<div class="col-md-5">',
                                "after"			=>	"</div>",
                                "autocomplete"	=>	"off",
                                "options"		=>	$category_id_list,
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
								"empty"					=>	__("Select Product Category"),
								"data-live-search"		=>	"false"
                            )
                        )?>
                        
                        <?php echo $this->Form->input("description",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array(
														"class"	=>	"col-md-3 control-label",
														"text"	=>	"Description"
													),
                                "between"		=>	'<div class="col-md-5">',
                                "after"			=>	'<span class="help-block" id="CountTextArea"></span></div>',
                                "autocomplete"	=>	"off",
                                "type"			=>	"textarea",
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
                        
                        <?php echo $this->Form->input("images",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("Photo")),
                                "between"		=>	'<div class="col-md-7">
								<div class="col-md-4" style="display:none;padding:0px 10px 0px 0px;" id="imagePrev">
									<div class="gallery">
										<a class="gallery-item" href="javascript:void(0);" style="padding:0px;width:100%;height:150px;overflow:hidden;" id="previewLink" rel="lightbox">
											<div class="image">
												<img src="" id="previewImg"/>
											</div>
										</a>
									</div>
								</div>
								<div class="col-md-6" style="padding:0px; 10px; 0px; 0px;">
								',
                                "after"			=>	'<span class="help-block">'.__('Will be scaled to %s X %s',array($settings['product_width'].'px',$settings['product_height'].'px')).'</span></div></div>',
                                "autocomplete"	=>	"off",
                                "type"			=>	"file",
                                "class"			=>	"fileinput",
								'error' 		=>	array(
									'attributes' => array(
										'wrap' 	=> 'label',
										'class' => 'error'
									)
								),
								"onchange"		=>	"PreviewImage('".$ModelName."Images','previewImg','#previewLink')",
								"accept"		=>	"image/*",
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
						echo $this->Form->input("pdf",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("PDF")),
                                "between"		=>	'<div class="col-md-7">
								',
                                "after"			=>	'</div>',
                                "autocomplete"	=>	"off",
                                "type"			=>	"file",
                                "class"			=>	"fileinput",
								'error' 		=>	array(
									'attributes' => array(
										'wrap' 	=> 'label',
										'class' => 'error'
									)
								),
								"accept"		=>	"application/pdf",
								"format"		=>	array(
									'before',
									'label',
									'between',
									'input',
									'error',
									'after',
								),
                            )
                        );
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
                	<a href="<?php echo $settings['cms_url'].$ControllerName?>" class="btn btn-danger"><span class="fa fa-arrow-left fa-left"></span> <?php echo __('Back')?></a>
                    <button type="submit" onclick="OnClickSaveStay()" class="btn btn-primary pull-right" style="margin-left:10px;"><?php echo __('Save and add more')?><span class="fa fa-floppy-o fa-right"></span></button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
