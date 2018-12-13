<?php
$tab1	=	"";
$tab2	=	"";

if($tab_index == "tab1")
	$tab1	=	" active";
else if($tab_index == "tab2")
	$tab2	=	" active";
	
?>

<?php $this->start("script");?>
<script type="text/javascript" src="<?php echo $settings['cms_url']?>js/jquery-prettyPhoto.js"></script>
<script type="text/javascript" src="<?php echo $settings['cms_url']?>js/plugins/bootstrap/bootstrap-file-input.js"></script>
<script type="text/javascript" src="<?php echo $settings['cms_url']?>js/autoNumeric-1.9.18.js"></script>
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
		$("#latStudio").val(map.getCenter().lat());
		$("#lngStudio").val(map.getCenter().lng());
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


function InfoStatusDriver(status)
{
	var message = "";
	if(status == "2")
	{
		message	=	"Still waiting driver to confirm";
	} 
	else if(status == "3")
	{
		message	=	"Driver has accepted the job";
	}
	else if(status == "4")
	{
		message	=	"Driver has rejected the job";
	}
	else if(status == "5")
	{
		message	=	"Item/Product in delivery process";
	}
	else if(status == "6")
	{
		message	=	"Item/Product has completely delivered";
	}
	else if(status == "7")
	{
		message	=	"Item/Product failed to deliver";
	}
	
	noty({
		text: message,
		layout: 'topCenter',
		timeout:2000,
		buttons: [
				{
					addClass: 'btn btn-success btn-clean', text: '<?php echo __('OK')?>', onClick: function($noty) {
						$noty.close();
					}
				}
			]
	});
}

function InfoStatusTechnician(status)
{
	var message = "";
	if(status == "2")
	{
		message	=	"Still waiting technician to confirm";
	} 
	else if(status == "3")
	{
		message	=	"Technician has accepted the job";
	}
	else if(status == "4")
	{
		message	=	"Technician has rejected the job";
	}
	else if(status == "5")
	{
		message	=	"Item/Product in assembling process";
	}
	else if(status == "6")
	{
		message	=	"Item/Product has completely assembled";
	}
	else if(status == "7")
	{
		message	=	"Item/Product failed to assembled";
	}
	
	noty({
		text: message,
		layout: 'topCenter',
		timeout:2000,
		buttons: [
				{
					addClass: 'btn btn-success btn-clean', text: '<?php echo __('OK')?>', onClick: function($noty) {
						$noty.close();
					}
				}
			]
	});
}

$(document).on("keypress", ":input:not(textarea)", function(event) {
    return event.keyCode != 13;
});

$(document).ready(function(){
	
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
	
	
	$("a[rel^='lightbox']").prettyPhoto({
		social_tools :''
	});
	
	//======== PRODUCT INFORMATION =============/
	$("#OrderProductQty").autoNumeric('init', {  lZero: 'deny', aSep: ',', mDec: 0,vMax:99999999});
	LoadDataProduct()
	//======== PRODUCT INFORMATION =============/
	
	$("input[name='data[Order][is_assembling]']").on('ifChecked', function(event){
	  	if($(this).val() == "1")
		{
			$("#assemblyDiv").show();
		}
		else
		{
			$("#assemblyDiv").hide();
		}
	});
	
	<?php if(isset($this->request->data[$ModelName]['is_assembling']) && $this->request->data[$ModelName]['is_assembling'] == '1'):?>
		$("#assemblyDiv").show();
	<?php else:?>
		$("#assemblyDiv").hide();
		$("#OrderAssemblyDate").val('');
	<?php endif;?>
	
	//========= DELIVERY ============//
	$("input[name='data[Order][delivery_type_id]']").on('ifChecked', function(event)
	{
	  	if($(this).val() == "1")
		{
			$("#deliveryDiv").show();
			$("#pickupDiv").hide();
			//$("#OrderPickupDate").val('');
		}
		else
		{
			$("#deliveryDiv").hide();
			$("#pickupDiv").show();
			/*$("#OrderDeliveryDate").val('');
			$("#OrderReceiverName").val('');
			$("#OrderReceiverPhone").val('');
			$("#OrderAddress").val('');*/
		}
	});
	
	<?php if(isset($this->request->data[$ModelName]['delivery_type_id']) && $this->request->data[$ModelName]['delivery_type_id'] == '1'):?>
		$("#deliveryDiv").show();
		$("#pickupDiv").hide();
		
	<?php elseif(isset($this->request->data[$ModelName]['delivery_type_id']) && $this->request->data[$ModelName]['delivery_type_id'] == '2'):?>
		$("#deliveryDiv").hide();
		$("#pickupDiv").show();
		
	<?php elseif(!isset($this->request->data[$ModelName]['delivery_type_id'])):?>
		$("#deliveryDiv").show();
		$("#pickupDiv").hide();
	<?php endif;?>
	//========= DELIVERY ============//
});

function AddNewProductButton()
{
	$('#AddNewProductForm').show();
	$('#AddNewProductButton').hide();
}

function CancelAddProductButton()
{	
	$('#AddNewProductForm').hide();
	$('#AddNewProductButton').show();
}

function AddNewProductForm()
{
	$("#AddNewProductForm").ajaxSubmit({
		url:"<?php echo $settings['cms_url'].$ControllerName ?>/AddNewProductForm",
		type:'POST',
		dataType: "json",
		clearForm:false,
		beforeSend:function()
		{
			$("#loaderAddNewProductForm").show();
			//ShowLoadingVariant();
		},
		complete:function(data,html)
		{
		},
		error:function(XMLHttpRequest, textStatus,errorThrown)
		{
			$("#loaderAddNewProductForm").hide();
			noty({text:"<?php echo __('There is problem when add new data!')?>", layout: 'topCenter', type: 'error',timeout:5000});
		},
		success:function(json)
		{
			$("#loaderAddNewProductForm").hide();
			
			var status		=	json.status;
			var message		=	json.message;

			if(status == "1")
            {
				LoadDataProduct();
				
                if( $('#SaveFlag').val() == "1")
                    location.href   ='<?php echo $settings['cms_url'].$ControllerName."/Index/".$page."/".$viewpage?>';
                else
				{
                    noty({text:message, layout: 'topCenter', type: 'success',timeout:5000});
					
					$("#OrderProductProductId").val('');
					$('.select').selectpicker('refresh');
					$("#OrderProductProductQty").val('1');
				}
            }
			else
				noty({text:message, layout: 'topCenter', type: 'error',timeout:5000});
		}
	});
	return false;
}

function ShowLoadingEquipment()
{
	var panel	=	$("#productDiv").parents(".panel");
	panel.append('<div class="panel-refresh-layer"><img src="<?php echo $this->webroot?>img/loaders/default.gif"/></div>');
	panel.find(".panel-refresh-layer").width(panel.width()).height(panel.height());
	panel.addClass("panel-refreshing");
	onload();
}

function LoadDataProduct()
{
	ShowLoadingEquipment();
	
	var panel	=	$("#productDiv").parents(".panel");
    
	$("#productDiv").load("<?php echo $settings['cms_url'] . $ControllerName?>/ListItemProduct/<?php echo $ID?>",
	function(){
		
		panel.find(".panel-refresh-layer").remove();
    	panel.removeClass("panel-refreshing");
	
	
		$("a[rel^='lightbox']").prettyPhoto({
			social_tools :''
		});
		
		$(this).find(".icheckbox").iCheck({checkboxClass: 'icheckbox_minimal-grey'});
		
		$("input[id^=productChk]").on('ifChecked', function(event){
			$("#DeleteBtnProduct").show();
		});

		$("input[id^=productChk]").on('ifUnchecked', function(event){
			var checked	=	"";
			$("input[id^=productChk]").each(function(index){
				if($(this).prop("checked"))
				{
					checked			+=		$(this).val()+",";
				}

			});
			checked		=	checked.substring(0,checked.length-1);
			if(checked.length == 0)
			{
				$("#DeleteBtnProduct").hide();
			}
			else
			{
				$("#DeleteBtnProduct").show();
			}
		});
		
		$("#CheckAllProduct").on('ifChecked', function(event){
			$('input[id^=productChk]').iCheck('check');
		});

		$("#CheckAllProduct").on('ifUnchecked', function(event){
			$('input[id^=productChk]').iCheck('uncheck');
		});
	});
}

function DeleteProduct(el,msg,id)
{
	var panel	=	$(el).parents(".panel");
	noty({
		text: msg,
		layout: 'topCenter',
		timeout:5000,
		buttons: [
				{
					addClass: 'btn btn-success btn-clean', text: '<?php echo __('Yes')?>', onClick: function($noty) {
						$noty.close();
						
						panel.append('<div class="panel-refresh-layer"><img src="<?php echo $this->webroot?>img/loaders/default.gif"/></div>');
						panel.find(".panel-refresh-layer").width(panel.width()).height(panel.height());
						panel.addClass("panel-refreshing");
							
						$.getJSON("<?php echo $settings["cms_url"].$ControllerName?>/DeleteProduct/"+id,function(result)
						{
							LoadDataProduct();
							if(result.data.status == "1")
							{
								noty({text:result.data.message, layout: 'topCenter', type: 'success',timeout:5000});
							}
							else
							{
								noty({text:result.data.message, layout: 'topCenter', type: 'error',timeout:5000});
							}
						});
				}
				},
				{
					addClass: 'btn btn-danger btn-clean', text: '<?php echo __('Cancel')?>', onClick: function($noty) {
						$noty.close();
					}
				}
			]
	});
	return false;
}

function DeleteAllProduct(el)
{
    var panel	=	$(el).parents(".panel");
    var checked	=	"";
	$("input[id^=productChk]").each(function(index){
		if($(this).prop("checked"))
		{
			checked			+=		$(this).val()+",";
		}

	});
	checked		=	checked.substring(0,checked.length-1);

	if(checked.length == 0)
	{
		noty({
			text: "<?php echo __('Please check product to be delete!')?>",
			layout: 'topCenter',
			timeout:5000,
			buttons: [{addClass: 'btn btn-success btn-clean', text: 'OK', onClick: function($noty){
				$noty.close();
			}}]
		});
	}
	else
	{
		noty({
			text: "<?php echo __('Do you realy want to delete all checked product ?')?>",
			layout: 'topCenter',
			timeout:5000,
			buttons: [
					{
						addClass: 'btn btn-success btn-clean', text: '<?php echo __('Yes')?>', onClick: function($noty) {
							$noty.close();
                            panel.append('<div class="panel-refresh-layer"><img src="<?php echo $this->webroot?>img/loaders/default.gif"/></div>');
                        	panel.find(".panel-refresh-layer").width(panel.width()).height(panel.height());
                        	panel.addClass("panel-refreshing");

							$.getJSON("<?php echo $settings["cms_url"].$ControllerName?>/DeleteMultipleProduct/",{
									"id":checked
								},function(result)
							{

                                panel.find(".panel-refresh-layer").remove();
                        		panel.removeClass("panel-refreshing");

                                LoadDataProduct();

								if(result.data.status == "1")
								{
									noty({text:result.data.message, layout: 'topCenter', type: 'success', timeout:5000});
								}
								else
								{
									noty({text:result.data.message, layout: 'topCenter', type: 'error',timeout:5000});
								}
							});
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
}

</script>
<?php $this->end()?>

<?php $this->start("css");?>
<style>
#pac-input {
	margin-top:10px;
	width:70%;
	height:30px;
	margin-left:0px;
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
    <li class="active"><?php echo __('Edit Data')?> : <?php echo $detail[$ModelName]['order_no']?></li>
</ul>
<!-- END BREADCRUMB -->

<!-- START CONTENT FRAME -->
<div class="content-frame">

    <!-- START CONTENT FRAME TOP -->
    <div class="content-frame-top">
        <div class="page-title">
            <h2><span class="fa fa-th-large"></span> Edit : <?php echo $detail[$ModelName]['order_no']?></h2>
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
    <!-- END CONTENT FRAME TOP -->
    <!-- START CONTENT FRAME LEFT -->
    <div class="content-frame-left" style="display:block;">
        <div class="panel panel-default">
            <div class="panel-body list-group border-bottom">
            	<a href="#tab1" class="list-group-item<?php echo $tab1?>" data-toggle="tab">
					<?php echo __('Order Information')?>
                </a>
                <a href="#tab2" class="list-group-item<?php echo $tab2?>" data-toggle="tab">
					<?php echo __('Product Information')?>
                </a>
			</div>
		</div>
	</div>
    <!-- END CONTENT FRAME LEFT -->
    
    <!-- START CONTENT FRAME BODY -->
    <div class="content-frame-body">
    
    	<?php if(!empty($errMessage)):?>
    	<div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only"><?php echo __('Close')?></span></button>
            <strong><?php echo __('Error')?></strong>
            <ol>
            	<?php foreach($errMessage as $message):?>
            	<li><?php echo $message?></li>
                <?php endforeach;?>
            </ol>
        </div>
        <?php endif;?>
        <?php echo $this->Session->flash();?>
        
        <!-- START TAB1 -->
        <div class="tab-pane active" id="tab1" >
            <div class="panel panel-primary">
            
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <?php echo __('Order Information')?>
                    </h3>
                </div>
                
               <?php echo $this->Form->create($ModelName, array('url' => array("controller"=>$ControllerName,"action"=>"Edit",$ID,$page,$viewpage),'class' => 'form-horizontal',"type"=>"file","novalidate")); ?>
               
               <?php
               		$disabled	=	"";
					$disabled2	=	"";
					if(
							in_array($detail["Order"]["delivery_status"],array(3,5,6))
						or
							in_array($detail["Order"]["pickup_status"],array(10))
					)
					{
						$disabled	=	"disabled";
						$disabled2	=	"disabled=\"disabled\"";
					}
			   ?>
               <?php
					echo $this->Form->input('id', array(
						'type'			=>	'hidden',
						'readonly'		=>	'readonly'
					));
				?>
            	<?php echo $this->Form->hidden("save_flag",array("id"=>"SaveFlag","value"=>"0"))?>
               
                <div class="panel-body">
                    <div class="col-md-12">
                    	<?php echo $this->Form->input("order_no",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("PO No.(*)")),
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
								"escape"		=>	false,
								"disabled"		=>	$disabled
                            )
                        )?>
                        <?php echo $this->Form->input("delivery_no",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("Delivery Order No.(*)")),
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
								"escape"		=>	false,
								"disabled"		=>	$disabled
                            )
                        )?>
                        
                        <?php echo $this->Form->input("customer_id",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("Customers (*)")),
                                "between"		=>	'<div class="col-md-9">',
                                "after"			=>	"</div>",
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
								"onchange"				=>	"DetailCustomer(this.value)",
								"disabled"				=>	$disabled
                            )
                        )?>
                        
                        <?php echo $this->Form->input("delivery_type_id",
                            array(
                                "div"			=>	array("class"=>"form-group"),
								"before"		=>	'<label class="col-md-3 control-label"> Delivery Method (*)</label><div class="col-md-9"><label class="check">',
								"after"			=>	'</label></div>',
								"separator"		=>	'</label><label class="check">',
								"label"			=>	false,
                                "options"		=>	array("1"=>__("Delivery by driver"),"2"=>__("Pickup on indorack office")),
                                "class"			=>	'iradio',
								'error' 		=>	array(
									'attributes' => array(
										'wrap' 	=> 'label',
										'class' => 'error'
									)
								),
								"type"			=>	"radio",
								"legend"		=>	false,
								"default"		=>	1,
								"disabled"		=>	$disabled
                            )
                        )?>
                        
                        <div id="pickupDiv" style="display:none;float:left; width:100%; margin-bottom:10px;">
                        	<?php echo $this->Form->input("pickup_date",
								array(
									"div"			=>	array("class"=>"form-group"),
									"label"			=>	array(
															"class"	=>	"col-md-3 control-label",
															"text"	=>	"Pickup Date (*)"
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
									"disabled"		=>	$disabled
								)
							)?>
                        </div>
                        <div id="deliveryDiv" style="float:left; width:100%;">
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
									"disabled"		=>	$disabled
                                )
                            )?>
                            <?php echo $this->Form->input("receiver_name",
                                array(
                                    "div"			=>	array("class"=>"form-group"),
                                    "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("Receiver Name.(*)")),
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
                                    "escape"		=>	false,
									"disabled"		=>	$disabled
                                )
                            )?>
                            
                            <?php echo $this->Form->input("receiver_phone",
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
                                    "escape"		=>	false,
									"disabled"		=>	$disabled
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
                                    "rows"	=>	10,
									"disabled"		=>	$disabled
                                )
                            )?>
                            
                            <div class="form-group">
                                <label class="col-md-3 control-label">Map Position (*)</label>
                                <div class="col-md-9"  style="padding-left:15px; padding-right:15px;">
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
                        </div>
                        
                        <?php echo $this->Form->input("description",
							array(
								"div"			=>	array("class"=>"form-group"),
								"label"			=>	array(
														"class"	=>	"col-md-3 control-label",
														"text"	=>	"Additional Notes"
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
								"rows"	=>	10,
								"disabled"				=>	$disabled
							)
						)?>
                        
                        <?php echo $this->Form->input("is_urgent",
                            array(
                                "div"			=>	array("class"=>"form-group"),
								"before"		=>	'<label class="col-md-3 control-label"> Is Urgent </label><div class="col-md-9"><label class="check">',
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
								"disabled"				=>	$disabled
                            )
                        )?>
                        
                        <?php echo $this->Form->input("is_assembling",
                            array(
                                "div"			=>	array("class"=>"form-group"),
								"before"		=>	'<label class="col-md-3 control-label">With assembling ?</label><div class="col-md-9"><label class="check">',
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
								"disabled"				=>	$disabled
                            )
                        )?>
                        <div id="assemblyDiv" style="display:none; float:left; width:100%;">
                        <?php echo $this->Form->input("assembly_date",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array(
														"class"	=>	"col-md-3 control-label",
														"text"	=>	"Assembly Date (*)"
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
								"disabled"				=>	$disabled
                            )
                        )?>
                        </div>
                        <?php if(
							(!is_null($detail["Order"]["delivery_status"]) &&
							$detail["Order"]["delivery_status"] > 1) or
							(!is_null($detail["Order"]["assembly_status"]) &&
							$detail["Order"]["assembly_status"] > 1) or
							(!is_null($detail["Order"]["pickup_status"]) &&
							$detail["Order"]["pickup_status"] > 1)
						):?>
                        <div class="form-group">&nbsp;</div>
                        <hr/>
                        
                        <?php if($detail["Order"]["delivery_type_id"] == "1"):?>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Delivery Status</label>
                                <div class="col-md-9 line-height-30">
                                    <?php echo $detail["DeliveryStatus"]["name"]?>
                                </div>
                            </div>
                            
                            <?php if(!empty($driver)):?>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Assign Driver</label>
                                <div class="col-md-9 line-height-30">
                                    <?php foreach($driver as $driver):?>
                                    <?php echo $driver["Driver"]["fullname"]?> <a href="javscript:void(0);" onclick="InfoStatusDriver('<?php echo $driver["TaskAssign"]["status"]?>')">[see status]</a><br/>
                                    <?php if(!empty($driver["TaskAssign"]["reason"])):?>
                                    <blockquote style="font-size:20px;"><i>&ldquo;<?php echo $driver["TaskAssign"]["reason"]?>&rdquo;</i></blockquote>
                                    <?php endif;?>
                                    <?php endforeach;?>
                                </div>
                            </div>
                            <?php endif;?>
                            
                            <?php if(!empty($taskDriver["Image"])):?>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Photos</label>
                                <div class="col-md-9">
                                    <div class="gallery" style="width:200px;">
                                        <a class="gallery-item" href="<?php echo $taskDriver["Image"]["host"].$taskDriver["Image"]["url"]."?t=".$taskDriver["Image"]["modified"]?>" style="padding:10px;width:100%;height:150px;overflow:hidden;" id="previewLink" rel="lightbox">
                                            <div class="image" style="width:200px;">
                                                <img src="<?php echo $taskDriver["Image"]["host"].$taskDriver["Image"]["url"]."?t=".$taskDriver["Image"]["modified"]?>" style="width:200px;"/>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endif;?>
                            
                            <?php if($detail["Order"]["is_assembling"] == "1"):?>
                            <hr/>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Assembling Status</label>
                                <div class="col-md-9 line-height-30">
                                    <?php echo $detail["AssemblyStatus"]["name"]?>
                                </div>
                            </div>
                            <?php if(!empty($technician)):?>
                            	<label class="col-md-3 control-label">Assign Technician</label>
                                <div class="col-md-9 line-height-30">
                                    <?php foreach($technician as $technician):?>
                                    <?php echo $technician["Driver"]["fullname"]?> <a href="javascript:void(0);" onclick="InfoStatusTechnician('<?php echo $technician["TaskAssign"]["status"]?>')">[see status]</a><br/>
                                    <?php if(!empty($technician["TaskAssign"]["reason"])):?>
                                    <blockquote style="font-size:20px;"><i>&ldquo;<?php echo $technician["TaskAssign"]["reason"]?>&rdquo;</i></blockquote>
                                    <?php endif;?>
                                    <?php endforeach;?>
                                </div>
                            <?php endif;?>
                            <?php if(!empty($taskTechnician["Image"])):?>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Photos</label>
                                <div class="col-md-9">
                                    <div class="gallery" style="width:200px;">
                                        <a class="gallery-item" href="<?php echo $taskTechnician["Image"]["host"].$taskTechnician["Image"]["url"]."?t=".$taskTechnician["Image"]["modified"]?>" style="padding:10px;width:100%;height:150px;overflow:hidden;" id="previewLink" rel="lightbox">
                                            <div class="image" style="width:200px;">
                                                <img src="<?php echo $taskTechnician["Image"]["host"].$taskTechnician["Image"]["url"]."?t=".$taskTechnician["Image"]["modified"]?>" style="width:200px;"/>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endif;?>
                            <?php endif;?>
                        <?php elseif($detail["Order"]["delivery_type_id"] == "2"):?>
                        	<div class="form-group">
                                <label class="col-md-3 control-label">Pickup Status</label>
                                <div class="col-md-9 line-height-30">
                                    <?php echo $detail["PickupStatus"]["name"]?>
                                </div>
                            </div>
                            <?php if($detail["Order"]["pickup_status"] == "10"):?>
                            	<div class="form-group">
                                    <label class="col-md-3 control-label">Receiver Name</label>
                                    <div class="col-md-9 line-height-30">
                                        <?php echo $detail["Order"]["receiver_name"]?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Receiver Phone</label>
                                    <div class="col-md-9 line-height-30">
                                        <?php echo $detail["Order"]["receiver_phone"]?>
                                    </div>
                                </div>
                                <?php if(!empty($detail["Image"]["id"])):?>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Photos</label>
                                    <div class="col-md-9">
                                        <div class="gallery" style="width:200px;">
                                        	<a class="gallery-item" href="<?php echo $detail["Image"]["host"].$detail["Image"]["url"]."?t=".$detail["Image"]["modified"]?>" style="padding:10px;width:100%;height:150px;overflow:hidden;" id="previewLink" rel="lightbox">
                                            	<div class="image" style="width:200px;">
                                                    <img src="<?php echo $detail["Image"]["host"].$detail["Image"]["url"]."?t=".$detail["Image"]["modified"]?>" style="width:200px;"/>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php endif;?>
                            <?php endif;?>
                        <?php endif;?>
                        <?php endif;?>
                    </div>
            	</div>
                <div class="panel-footer">
                    <a href="<?php echo $settings['cms_url'].$ControllerName?>" class="btn btn-danger"><span class="fa fa-times fa-left"></span> <?php echo __('Cancel')?></a>
                    <button type="submit" onclick="OnClickSaveDirect()" class="btn btn-primary pull-right" style="margin-left:10px;" <?php echo $disabled2?>><?php echo __('Save and set product')?><span class="fa fa-floppy-o fa-right"></span></button>
                    <button type="submit" onclick="OnClickSaveStay()" class="btn btn-primary pull-right" <?php echo $disabled2?>><?php echo __('Save and stay')?><span class="fa fa-floppy-o fa-right"></span></button>
                </div>
                </form>
    		</div>
    	</div>
        <!-- END TAB1 -->
        <!-- START TAB2 -->
        <div class="tab-pane" id="tab2">
			<?php echo $this->Form->create('OrderProduct', array(
				'url' 			=>	'#',
				'class' 		=>	'form-horizontal',
				'onsubmit'		=>	'return AddNewProductForm()',
				"id"			=>	'AddNewProductForm',
				"style"     	=>  "display:none;",
				"novalidate")); 
            ?>
            <?php echo $this->Form->hidden("order_id",array("value"=>$detail['Order']['id']));?>
            <div class="panel panel-primary">
            	<div class="panel-heading">
                    <h3 class="panel-title" id="panel_title_equipment">
                        <?php echo __('Add New Product')?>
                    </h3>
                    <ul class="panel-controls">
                        <li>
                            <a href="javascript:void(0);" onclick="CancelAddProductButton()">
                            	<span class="fa fa-times"></span>
                            </a>
                        </li>
                    </ul>
            	</div>
                <div class="panel-body">
                	<div class="col-md-12">
                    	<?php
                             echo $this->Form->input("product_id",
                                array(
                                    "div"			=>	array("class"=>"form-group"),
                                    "label"			=>	array(
															"class"	=>	"col-md-3 control-label",
															"text"	=>__("Product (*)")
														),
                                    "between"		=>	'<div class="col-md-5">',
                                    "after"			=>	"<span class='help-block' style='color:#000000;'>Products not exists in list ?, <a href='".$settings["cms_url"]."Products/Add' target='_blank'>click here</a> to add new product</span></div>",
                                    "autocomplete"	=>	"off",
                                    "options"		=>	$product_id_list,
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
                                    "data-live-search"	=>	"true",
                                    "empty"				=>	__("Select Product")
                                )
                            );
                        ?>
                        <?php echo $this->Form->input("qty",
                            array(
                                "div"				=>	array("class"=>"form-group"),
                                "label"				=>	array(
                                                            "class"				=>	"col-md-3 control-label",
                                                            "text"				=>	__("Quantity (*)")
                                                        ),
                                "between"			=>	'<div class="col-md-5"><div class="input-group">',
                                "after"				=>	'<span class="input-group-addon">unit</span></div></div>',
                                "autocomplete"		=>	"off",
                                "type"				=>	"text",
                                "class"				=>	'form-control',
                                'error' 			=>	array(
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
								"value"	=>	"1"
                            )
                        )?>
                        
                        <?php echo $this->Form->input("description",
                            array(
                                "div"				=>	array("class"=>"form-group"),
                                "label"				=>	array(
                                                            "class"				=>	"col-md-3 control-label",
                                                            "text"				=>	__("Notes")
                                                        ),
                                "between"			=>	'<div class="col-md-9">',
                                "after"				=>	'</div>',
                                "autocomplete"		=>	"off",
                                "type"				=>	"textarea",
                                "class"				=>	'form-control',
                                'error' 			=>	array(
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
                                )
                            )
                        )?>
                        <hr/>
                    </div>
                </div>
                <div class="panel-footer">
                    <a href="<?php echo $settings['cms_url'].$ControllerName?>" class="btn btn-danger"><span class="fa fa-times fa-left"></span> <?php echo __('Cancel')?></a>
                    
                    <button type="submit" class="btn btn-primary pull-right" style="margin-left:10px;" onclick="$('#SaveFlag').val('1')" <?php echo $disabled2?>><?php echo __('Save')?><span class="fa fa-floppy-o fa-right"></span></button>
                    
                    <button type="submit" class="btn btn-primary pull-right" style="margin-left:10px;" onclick="$('#SaveFlag').val('0')" <?php echo $disabled2?>><?php echo __('Save and stay')?><span class="fa fa-floppy-o fa-right"></span></button>
                    
                    <img src="<?php echo $this->webroot?>img/loaders/loader9.gif" class="pull-right" id="loaderAddNewProductForm" style="display:none;"/>
                </div>
            </div>
            </form>
            
        	<div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <?php echo __('Product Information')?>
                    </h3>
                    <div class="pull-right" >
                        <button class="btn btn-primary pull-right <?php echo $disabled?> " 
                            onclick="DeleteAllProduct(this)"
                            style="margin-left:5px;display:none;"
                            id="DeleteBtnProduct">
                            <span class="fa fa-trash-o"></span>
                            <?php echo __('Delete')?>
                        </button>
                        
                        <button class="btn btn-primary pull-right" onClick="AddNewProductButton();" id="AddNewProductButton" <?php echo $disabled2 ?>>
                            <span class="fa fa-plus"></span>
                            <?php echo __('Add New Product')?>
                        </button>
                        
                    </div>
                </div>
                <div class="panel-body" id="productDiv">
            	</div>
    		</div>
        </div>
        <!-- END TAB2 -->
	</div>
    <!-- END CONTENT FRAME BODY -->
    
</div>
<!-- END CONTENT FRAME -->