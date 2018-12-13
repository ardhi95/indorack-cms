<?php $this->start("script");?>
<script type="text/javascript" src="<?php echo $settings['cms_url']?>js/jquery-prettyPhoto.js"></script>
<script type="text/javascript" src="<?php echo $settings['cms_url']?>js/plugins/bootstrap/bootstrap-file-input.js"></script>
<script type="text/javascript" src="<?php echo $settings['cms_url']?>js/plugins/tagsinput/jquery.tagsinput.min.js"></script>

<script type="text/javascript" src="<?php echo $settings['cms_url']?>js/bootstrap-datetimepicker.min.js"></script>

<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $settings['map_browser_api_key']?>&language=id&libraries=places"></script>

<script>

//============ MAP ==============/
var map;
var marker;

var latitude	=	'<?php echo $settings['default_lat']?>';
var longitude	=	'<?php echo $settings['default_lng']?>';
<?php if(!empty($this->request->data['Order']['latitude']) && !empty($this->request->data['Order']['longitude'])):?>
latitude	=	'<?php echo $this->request->data['Order']['latitude']?>';
longitude	=	'<?php echo $this->request->data['Order']['longitude']?>';
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
					
	<?php if(empty($this->request->data['Order']['latitude']) && empty($this->request->data['Order']['longitude'])):?>
		infowindow.setContent('Drag me to change location');
		infowindow.open(map, marker);
		//$("#latStudio").val(map.getCenter().lat());
		//$("#lngStudio").val(map.getCenter().lng());
	<?php elseif(!empty($this->request->data['Order']['latitude']) && !empty($this->request->data['Order']['longitude'])):?>
		infowindow.setContent("Latitude : <?php echo $this->request->data['Order']['latitude']?><br/>Longitude : <?php echo $this->request->data['Order']['longitude']?>");
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


function OpenFormAddNewCustomer()
{
	$("#isNewCustomer").val("1");
	$("#newCustomerDiv").show();
	google.maps.event.trigger(map, 'resize');
	map.setCenter(new google.maps.LatLng(marker.getPosition().lat(),marker.getPosition().lng()));
}

function CloseFormAddNewCustomer()
{
	$("#isNewCustomer").val("0");
	$("#newCustomerDiv").hide();
	
	$("#OrderFirstname").val('');
	$("#OrderLastname").val('');
	$("#OrderEmail").val('');
	$("#OrderPassword").val('');
}

function OpenFormAddNewProduct()
{
	$("#isNewProduct").val("1");
	$("#newProductDiv").show();
}

function CloseFormAddNewProduct()
{
	$("#isNewProduct").val("0");
	$("#newProductDiv").hide();
	
	$("#OrderProductCode").val('');
	$("#OrderProductName").val('');
	$("#OrderProductDescription").val('');
}


function DetailCustomer(customerId)
{
	if(customerId != "")
	{
		$.getJSON('<?php echo $settings['cms_url'].$ControllerName?>/GetDetailCustomer',
			{
				'customerId':customerId
			},
			function(result){
				if(result.status == "1")
				{
					var fullname	=	result.data.User.fullname;
					var address		=	result.data.User.address;
					var phone		=	result.data.User.phone1;
					var latitude	=	result.data.User.latitude;
					var longitude	=	result.data.User.longitude;
					
					
					$("#OrderAddress").val(address);
					$("#OrderReceiverPhone").val(phone);
					$("#OrderReceiverName").val(fullname);
					
					if(latitude != null && longitude != null)
					{
						var mapLatlng 	=	new google.maps.LatLng(latitude, longitude);
						marker.setPosition(mapLatlng);
						infowindow.setContent("Latitude : "+latitude+"<br/>Longitude : "+longitude);
						infowindow.open(map, marker);
						$("#latStudio").val(latitude);
						$("#lngStudio").val(longitude);
					}
				}
		});
	}
}


$(document).on("keypress", ":input:not(textarea)", function(event) {
    return event.keyCode != 13;
});

$(document).ready(function(){
	
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
	
	$("a[rel^='lightbox']").prettyPhoto({
		social_tools :''
	});
	
	<?php if($this->request->data['Order']['is_new_customer'] == "1"):?>
		OpenFormAddNewCustomer();
	<?php else:?>
		CloseFormAddNewCustomer();
	<?php endif;?>
	
	<?php if($this->request->data['Order']['is_new_product'] == "1"):?>
		OpenFormAddNewProduct();
	<?php else:?>
		CloseFormAddNewProduct();
	<?php endif;?>
	
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
<style>
#pac-input {
	margin-top:10px;
	width:80%;
	height:35px;
	margin-left:30px;
	background-color:#fff;
	border:1px solid grey;
	border-corner:50%;
}
</style>
<link rel="stylesheet" type="text/css" id="theme" href="<?php echo $this->webroot?>css/prettyPhoto.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot?>css/bootstrap-datetimepicker.min.css" media="all" />
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
        	<?php
				echo $this->Session->flash();
			?>
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
                    	<?php echo $this->Form->hidden("is_new_customer",array("id"=>"isNewCustomer","value"=>"0"))?>
                    	<?php echo $this->Form->input("order_no",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("PO No.(*)")),
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
								"escape"		=>	false
                            )
                        )?>
                        
                        <?php echo $this->Form->input("delivery_no",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("Delivery Order No.(*)")),
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
								"escape"		=>	false
                            )
                        )?>
                        <?php echo $this->Form->input("delivery_date",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array(
														"class"	=>	"col-md-3 control-label",
														"text"	=>	"Must Deliver On (*)"
													),
                                "between"		=>	'<div class="col-md-5">',
                                "after"			=>	'</div>',
                                "autocomplete"	=>	"off",
                                "type"			=>	"text",
                                "class"			=>	'form-control datetimepicker',
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
                        
                        <?php echo $this->Form->input("customer_id",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("Customers (*)")),
                                "between"		=>	'<div class="col-md-5">',
                                "after"			=>	"<span class='help-block' style='color:#000000;'>Customers not exists in list ?, <a href='javascript:void(0);' onclick='OpenFormAddNewCustomer();'>click here</a> to add new customer</span></div>",
                                "autocomplete"	=>	"off",
                                "options"		=>	$customer_id_list,
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
								"empty"					=>	__("Select Customer"),
								"data-live-search"		=>	"true",
								"onchange"				=>	"DetailCustomer(this.value)"
                            )
                        )?>
                        
                        <div id="newCustomerDiv" style="display:none; background-color:#eaeaea;">
                        	<hr/>
                            <div class="col-12" style="text-align:right; margin-top:-18px;">
                            	<a href='javascript:void(0);' class='btn btn-default btn-condensed' onclick="javascript:CloseFormAddNewCustomer();"><i class='fa fa-times'></i></a>
                            </div>
                            <br/>
                            <?php echo $this->Form->input("firstname",
								array(
									"div"			=>	array("class"=>"form-group"),
									"label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("First Name (*)")),
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
									"escape"		=>	false
								)
							)?>
                            
                            <?php echo $this->Form->input("lastname",
								array(
									"div"			=>	array("class"=>"form-group"),
									"label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("Last Name")),
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
									"escape"		=>	false
								)
							)?>
                            
                            <?php echo $this->Form->input("email",
								array(
									"div"			=>	array("class"=>"form-group"),
									"label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("Email (*)")),
									"between"		=>	'<div class="col-md-5">',
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
                            
                            <?php echo $this->Form->input("password",
								array(
									"div"			=>	array("class"=>"form-group"),
									"label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("Password (*)")),
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
									"escape"		=>	false
								)
							)?>
                            
                            <hr style="margin-top:50px;"/>
                        </div>
                        
                        <?php echo $this->Form->input("receiver_name",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("Receiver Name.(*)")),
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
								"escape"		=>	false
                            )
                        )?>
                        
                        
                        
                        <?php echo $this->Form->input("receiver_phone",
							array(
								"div"			=>	array("class"=>"form-group"),
								"label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("Phone No. (*)")),
								"between"		=>	'<div class="col-md-5">',
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
									"between"		=>	'<div class="col-md-5">',
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
								)
							)?>
                           	
                            
                            <div class="form-group">
                            	<label class="col-md-3 control-label">Map Position (*)</label>
                                <div class="col-md-9"  style="padding-left:10px; padding-right:10px;">
                                	<input type="text" id="pac-input" placeholder="Search Your Place" class="form-control"/>
                                    <?php
									$border	=	"#CCC";
                                    if ($this->Form->isFieldError('Order.latitude'))
									{
										$border	=	"#b64645";
									}
									?>
                                    <div id="map" style="display:block; float:left; height:400px; width:100%; border:1px solid <?php echo $border?>; border-radius:4px;">
                                    </div>
                                    
                                	<?php echo $this->Form->error("Order.latitude",null,array("wrap"=>"label","class"=>"error"))?>
                                </div>
                            </div>
                            
                            <?php echo $this->Form->hidden("latitude",array("id"=>"latStudio","readonly"=>"readonly"))?>
                        	<?php echo $this->Form->hidden("longitude",array("id"=>"lngStudio","readonly"=>"readonly"))?>
                        
                        <?php echo $this->Form->input("description",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array(
														"class"	=>	"col-md-3 control-label",
														"text"	=>	"Additional Notes"
													),
                                "between"		=>	'<div class="col-md-5">',
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
                            )
                        )?>
                        
                        <?php echo $this->Form->input("is_assembling",
                            array(
                                "div"			=>	array("class"=>"form-group"),
								"before"		=>	'<label class="col-md-3 control-label">With assembling ?</label><div class="col-md-5"><label class="check">',
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
								"default"		=>	"0"
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
