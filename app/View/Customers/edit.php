<?php $this->start("script");?>
<script type="text/javascript" src="<?php echo $settings['cms_url']?>js/plugins/bootstrap/bootstrap-file-input.js"></script>
<script type="text/javascript" src="<?php echo $settings['cms_url']?>js/jquery-prettyPhoto.js"></script>
<script type="text/javascript" src="<?php echo $settings['cms_url']?>js/bootstrap-datetimepicker.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $settings['map_browser_api_key']?>&language=id&libraries=places"></script>
<script>

//============ MAP ==============/
var map;
var marker;

var latitude	=	'<?php echo $settings['default_lat']?>';
var longitude	=	'<?php echo $settings['default_lng']?>';
<?php if(!empty($this->request->data['User']['latitude']) && !empty($this->request->data['User']['longitude'])):?>
latitude		=	'<?php echo $this->request->data['User']['latitude']?>';
longitude		=	'<?php echo $this->request->data['User']['longitude']?>';
<?php endif;?>

var infowindow 	=	new google.maps.InfoWindow();

var mapLatlng 	=	new google.maps.LatLng(latitude, longitude);
var mapOptions 	= {
  zoom: 15,
  center: mapLatlng,
  mapTypeId: 'roadmap'
};


function initMap()
{
	map 		= 	new google.maps.Map(document.getElementById('map'), mapOptions);
	marker		=	new google.maps.Marker({
						position	:	mapLatlng,
						map			:	map,
						draggable	:	true
					});
					
	<?php if(empty($this->request->data['User']['latitude']) && empty($this->request->data['User']['longitude'])):?>
		infowindow.setContent('Drag me to change location');
		infowindow.open(map, marker);
		//$("#latStudio").val(map.getCenter().lat());
		//$("#lngStudio").val(map.getCenter().lng());
	<?php elseif(!empty($this->request->data['User']['latitude']) && !empty($this->request->data['User']['longitude'])):?>
		infowindow.setContent("Latitude : <?php echo $this->request->data['User']['latitude']?><br/>Longitude : <?php echo $this->request->data['User']['longitude']?>");
		infowindow.open(map, marker);
		
	<?php endif;?>
	
	marker.addListener('dragend', function(event){
		$("#latStudio").val(event.latLng.lat());
		$("#lngStudio").val(event.latLng.lng());
		infowindow.setContent("Latitude : "+event.latLng.lat()+"<br/>Longitude : "+event.latLng.lng());
		infowindow.open(map, marker);
	});
	
	marker.addListener('drag', function(event){
		infowindow.close();
	});
	
	var input 		=	document.getElementById('pac-input');
	$("#pac-input").focusin(function(){
		$(this).val('');
	});
	
	var searchBox 	=	new google.maps.places.SearchBox(input);
	map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
	map.addListener('bounds_changed', function() {
	  searchBox.setBounds(map.getBounds());
	});
	
	searchBox.addListener('places_changed', function() {
		var places = searchBox.getPlaces();
		if (places.length == 0) {
			return;
		}
		
		var bounds = new google.maps.LatLngBounds();
		places.forEach(function(place) {
			if (!place.geometry) {
				return;
			}
			
			if (place.geometry.viewport) {
              bounds.union(place.geometry.viewport);
            } else {
              bounds.extend(place.geometry.location);
            }
			
			marker.setPosition(place.geometry.location);
			$("#latStudio").val(bounds.getCenter().lat());
			$("#lngStudio").val(bounds.getCenter().lng());
		});
		map.fitBounds(bounds);
		infowindow.setContent("Latitude : "+bounds.getCenter().lat()+"<br/>Longitude : "+bounds.getCenter().lng());
		infowindow.open(map, marker);
	});
}
//============ MAP ==============/



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


$(document).on("keypress", ":input:not(textarea)", function(event) {
    return event.keyCode != 13;
});

$(document).ready(function(){
	$('#sameDayChecked').on('ifChecked', function(event){
		
		var deliveryDate	=	$("#UserDeliveryDate").val();
		if(deliveryDate == "")
		{
			alert("Please select delivery date first");
			$('#sameDayChecked').iCheck('uncheck');
			('#sameDayChecked').iCheck('update');
		}
		else
		{
			$("#UserAssemblyDate").val(deliveryDate);
		}
	});
	
	$('#sameDayChecked').on('ifUnchecked', function(event){
		$("#UserAssemblyDate").val("");
	});
	
	//======= START TAB =========/
	$(".list-group-item").bind("click",function(){
		$(".list-group-item").each(function(index, element) {
            $(this).removeClass("active");
			var href	=	$(this).attr("href");
			$(href).hide();
        });
		$(this).addClass("active");
		var href	=	$(this).attr("href");
		$(href).show();
        onload();
		$("#flashMessage,#errorDiv").hide();
	});

	$(".list-group-item").each(function(){
		var href	=	$(this).attr("href");

		if($(this).hasClass('active'))
		{
			$(href).show();
		}
		else
		{
			$(href).hide();
		}
	});
	//======= END TAB =========/
	
	//======= DATETIMEPICKER =======/
    $(".datetimepicker").datetimepicker({
        format: "dd MM yyyy hh:ii",
        autoclose: true,
        todayBtn: true
    });
    //======= DATETIMEPICKER =======/
	
	//======== INIT MAP =============/
	initMap();
	//======== INIT MAP =============/
	
	<?php if($this->request->data['User']['is_new_customer'] == "1"):?>
		OpenFormAddNewCustomer();
	<?php else:?>
		CloseFormAddNewCustomer();
	<?php endif;?>
	
	$("a[rel^='lightbox']").prettyPhoto({
		social_tools :''
	});
	
	$("input[name='data[User][is_assembling]']").on('ifChecked', function(event){
	  	if($(this).val() == "1")
		{
			$("#assemblyDiv").show();
		}
		else
		{
			$("#assemblyDiv").hide();
			$("#UserAssemblyDate").val('');
		}
	});
	

	//========= DELIVERY ============//
});

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

/*$("#UserEditForm").bind("submit",function(){
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
});*/
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
                	<div class="col-md-6">
                    	<?php echo $this->Form->input("firstname",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("First Name")),
                                "between"		=>	'<div class="col-md-9">',
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
                        <?php echo $this->Form->input("lastname",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("Last Name")),
                                "between"		=>	'<div class="col-md-9">',
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
                                "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("Email")),
                                "between"		=>	'<div class="col-md-9">',
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
                                "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__('Password')),
                                "between"		=>	'<div class="col-md-9">',
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

                        <?php echo $this->Form->input("aro_id",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("Admin Group")),
                                "between"		=>	'<div class="col-md-9">',
                                "after"			=>	"</div>",
                                "autocomplete"	=>	"off",
                                "type"			=>	"hidden",
                                "value"			=>	"7",
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
									'value',
									'error',
									'after',
								),
                            )
                        )?>

                        <?php echo $this->Form->input("phone1",
								array(
									"div"			=>	array("class"=>"form-group"),
									"label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("Phone No. (*)")),
									"between"		=>	'<div class="col-md-9">',
									"after"			=>	"</div>",
									"autocomplete"	=>	"new-password",
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
									"escape"		=>	false
								)
							)?>
								
							<?php echo $this->Form->input("address",
								array(
									"div"			=>	array("class"=>"form-group"),
									"label"			=>	array(
															"class"	=>	"col-md-3 control-label",
															"text"	=>	"Address (*)"
														),
									"between"		=>	'<div class="col-md-9">',
									"after"			=>	'</div>',
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
									"rows"	=>	10
								)
							)?>

                        
                    </div>

                    <div class="col-md-6">
						<div class="form-group">
                                <label class="col-md-3 control-label">Map Position (*)</label>
                                <div class="col-md-9"  style="padding-left:15px; padding-right:15px;">
                                    <input type="text" id="pac-input" placeholder="Search Your Place" class="form-control"/>
                                    <?php
                                    $border	=	"#CCC";
                                    if ($this->Form->isFieldError('User.latitude'))
                                    {
                                        $border	=	"#b64645";
                                    }
                                    ?>
                                    <div id="map" style="display:block; float:left; height:270px; width:100%; border:1px solid <?php echo $border?>; border-radius:4px;">
                                    </div>
                                    
                                    <?php echo $this->Form->error("User.latitude",null,array("wrap"=>"label","class"=>"error"))?>
                                </div>
                            </div>
                            
                            <?php echo $this->Form->hidden("latitude",array("id"=>"latStudio","readonly"=>"readonly"))?>
                            <?php echo $this->Form->hidden("longitude",array("id"=>"lngStudio","readonly"=>"readonly"))?>
						
						<?php
                        $imgPreview	=	(!empty($detail["Thumbnail"]["id"])) ? $detail["Thumbnail"]["host"].$detail["Thumbnail"]["url"]."?time=".time() : $this->webroot ."img/default_content.png";

						 $imgPreviewBig	=	(!empty($detail["MaxWidth"]["id"])) ? $detail["MaxWidth"]["host"].$detail["MaxWidth"]["url"]."?time=".time() : $this->webroot ."img/default_content.png";

						echo $this->Form->input("images",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("Images")),
                                "between"		=>	'<div class="col-md-9">
								<div class="col-md-4" style="padding:0px 10px 0px 0px;" id="imagePrev">
									<div class="gallery">
										<a class="gallery-item" href="'.$imgPreviewBig.'" style="padding:0px;width:100%;height:150px;overflow:hidden;" id="previewLink" rel="lightbox">
											<div class="image">
												<img src="'.$imgPreview.'" id="previewImg"/>
											</div>
										</a>
									</div>
								</div>
								<div class="col-md-4" style="padding:0px; 10px; 0px; 0px;">
								',
                                "after"			=>	'<span class="help-block">'.__('Will be scaled to %s X %s',array('300px','300px')).'</span></div></div>',
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
                        );
						?>
						<?php
								echo $this->Form->input("status",
									array(
										"div"			=>	array("class"=>"form-group"),
										"before"		=>	'<label class="col-md-3 control-label">Status</label><div class="col-md-9"><label class="check">',
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
										"legend"		=>	false
									)
								);
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