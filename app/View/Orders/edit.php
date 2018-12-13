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
	function DetailCustomer(customerId) {
	    if(customerId != "") {
	      $.getJSON('<?php echo $settings['cms_url'].$ControllerName?>/GetDetailCustomer', 
	      			{ 'customerId':customerId },
	                function(result){
				        if(result.status == "1") {
				        	var fullname	=	result.data.User.fullname;
				        	var address		=	result.data.User.address;
				        	var phone		=	result.data.User.phone1;
				        	var latitude	=	result.data.User.latitude;
				        	var longitude	=	result.data.User.longitude;
				        	$("#OrderAddress").val(address);
				        	$("#OrderReceiverPhone").val(phone);
				        	$("#OrderReceiverName").val(fullname);
				        	$("#latStudio").val(latitude);
				        	$("#lngStudio").val(longitude);
				        }
	      			});
	    }
	  }

	function setEnableDisable() {
		var status = <?=$detail["DeliveryStatus"]["id"]?>;
		if	(status == 3 || status == 5 || status == 6 || status == 11) {
			setDisabled("data[Order][delivery_type_id]");
			setDisabled("data[Order][is_urgent]");
			setDisabled("data[Order][is_ppn]");
			setDisabled("data[Order][is_assembling]");
			setReadOnly("data[Order][receiver_phone]");
			setReadOnly("data[Order][delivery_date]");
			setReadOnly("data[Order][assembly_date]");
			setDisabled("data[Order][customer_id]");
			setReadOnly("data[Order][address]");
			setReadOnly("data[Order][description]");
			setReadOnly("data[Order][receiver_name]");

			$("#btnSubmitEdit").addClass("hide");
			$('#AddNewProductButton').hide();
		}
	}

	function setReadOnly(name){
		$(":input[name='"+name+"']").attr("readonly", true);
	}

	function setDisabled(name){
		$(":input[name='"+name+"']").attr("disabled", true);
	}

	function InfoStatusDriver(status) {
	    var message = "";
	    if(status == "2") {
	      message	=	"Still waiting driver to confirm";
	    } else if(status == "3") {
	      message	=	"Driver has accepted the job";
	    } else if(status == "4") {
	      message	=	"Driver has rejected the job";
	    } else if(status == "5") {
	      message	=	"Item/Product in delivery process";
	    } else if(status == "6") {
	      message	=	"Item/Product has completely delivered";
	    } else if(status == "7") {
	      message	=	"Item/Product failed to deliver";
	    } else if(status == "11"){
			message = "Driver return to office";
			}

	    noty({
	    	text: message,
	    	layout: 'topCenter',
	    	timeout:2000,
	      	buttons: [
		        {
		        	addClass: 'btn btn-success btn-clean', text: '<?php echo __('OK')?>', onClick: function($noty) 
		        	{
		          		$noty.close();
		        	}
		        }
	      	]
	    });
  	}

  	function InfoStatusTechnician(status) {
	    var message = "";
		    if(status == "2") {
		      message	=	"Still waiting technician to confirm";
		    } else if(status == "3") {
		      message	=	"Technician has accepted the job";
		    } else if(status == "4") {
		      message	=	"Technician has rejected the job";
		    } else if(status == "5") {
		      message	=	"Item/Product in assembling process";
		    } else if(status == "6") {
		      message	=	"Item/Product has completely assembled";
		    } else if(status == "7") {
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
		// ===== Set Enable Disable Fields ===== //
		setEnableDisable();
		// ===== Set Enable Disable Fields ===== //

    	var next = 0;
    	$("#add-more").click(function(e){
      		e.preventDefault();
      		var addto = "#field" + next;
      		var addRemove = "#field" + (next);
      		next = next + 1;
      		var newIn = '<div id="field'+ next +'" name="field'+ next +'">'+'</div>';
      		var newInput = $(newIn);
      		var removeBtn = '<button id="remove' + (next - 1) + '" class="btn btn-danger remove-me" >Remove</button></div></div><div id="field">';
      		var removeButton = $(removeBtn);
      		$(addto).after(newInput);
      		$(addRemove).after(removeButton);
      		$("#field" + next).attr('data-source',$(addto).attr('data-source'));
      		$("#count").val(next);
      		$('.remove-me').click(function(e){
        		e.preventDefault();
        		var fieldNum = this.id.charAt(this.id.length-1);
        		var fieldID = "#field" + fieldNum;
        		$(this).remove();
        		$(fieldID).remove();
      		});
    	});

	    $('#sameDayChecked').on('ifChecked', function(event){
	    	var deliveryDate	=	$("#OrderDeliveryDate").val();
	    	if(deliveryDate == "") {
	        	alert("Please select delivery date first");
	        	$('#sameDayChecked').iCheck('uncheck');
	        	('#sameDayChecked').iCheck('update');
	      	} else {
	    		$("#OrderAssemblyDate").val(deliveryDate);
	      	}
	    });

	    $('#sameDayChecked').on('ifUnchecked', function(event){
	    	$("#OrderAssemblyDate").val("");
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
	    	if($(this).hasClass('active')) {
	        	$(href).show();
	        } else {
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
		
	    $("a[rel^='lightbox']").prettyPhoto({
	    	social_tools :''
	    });

	    //======== PRODUCT INFORMATION =============/
	    $("#OrderProductQty").autoNumeric('init', {
	    	lZero: 'deny', 
	    	aSep: ',', 
	    	mDec: 0,
	    	vMax:99999999
	    });

	    LoadDataProduct();
	    //======== PRODUCT INFORMATION =============/

	    $("input[name='data[Order][is_assembling]']").on('ifChecked', function(event){
	    	if($(this).val() == "1") {
	    		$("#assemblyDiv").show();
	    	} else {
	    		$("#assemblyDiv").hide();
	    	}
	    }
                                                    );
	    <?php if(isset($this->request->data[$ModelName]['is_assembling']) && $this->request->data[$ModelName]['is_assembling'] == '1'):?>
	    	$("#assemblyDiv").show();
	    <?php else:?>
	    	$("#assemblyDiv").hide();
	    	$("#OrderAssemblyDate").val('');
	    <?php endif; ?>

	    //========= DELIVERY ============//
	    $("input[name='data[Order][delivery_type_id]']").on('ifChecked', function(event) {
	    	if($(this).val() == "1") {
	    		$("#deliveryDiv").show();
	    		$("#pickupDiv").hide();
	    		//$("#OrderPickupDate").val('');
	    	} else {
	    		$("#deliveryDiv").hide();
	    		$("#pickupDiv").show();
	    	}
	    }
	                                                         );
	    <?php if(isset($this->request->data[$ModelName]['delivery_type_id']) && $this->request->data[$ModelName]['delivery_type_id'] == '1'):?>
	    	$("#deliveryDiv").show();
	    	$("#pickupDiv").hide();
	    <?php elseif(isset($this->request->data[$ModelName]['delivery_type_id']) && $this->request->data[$ModelName]['delivery_type_id'] == '2'):?>
	      	$("#deliveryDiv").hide();
	    	$("#pickupDiv").show();
	    <?php elseif(!isset($this->request->data[$ModelName]['delivery_type_id'])):?>
	      	$("#deliveryDiv").show();
	    	$("#pickupDiv").hide();
	    <?php endif; ?>
	    //========= DELIVERY ============//
  	});

    function AddNewProductButton() {
    	$('#AddNewProductForm').show();
    	$('#AddNewProductButton').hide();
    }

    function CancelAddProductButton() {
    	$('#AddNewProductForm').hide();
    	$('#AddNewProductButton').show();
    }

    function AddNewProductForm() {
    	$("#AddNewProductForm").ajaxSubmit({
        	url:"<?php echo $settings['cms_url'].$ControllerName ?>/AddNewProductForm",
        	type:'POST',
        	dataType: "json",
        	clearForm:false,
        	beforeSend:function() {
          		$("#loaderAddNewProductForm").show();
          		//ShowLoadingVariant();
          	},
        	complete:function(data,html) {
        	},
        	error:function(XMLHttpRequest, textStatus,errorThrown) {
        		$("#loaderAddNewProductForm").hide();
        		noty({
        			text:"<?php echo __('There is problem when add new data!')?>", layout: 'topCenter', type: 'error',timeout:5000}
        			);
        	},
        	success:function(json) {
        		$("#loaderAddNewProductForm").hide();
        		var status		=	json.status;
        		var message		=	json.message;
        		if(status == "1") {
        			LoadDataProduct();
        			if( $('#SaveFlag').val() == "1")
        				location.href   ='<?php echo $settings['cms_url'].$ControllerName."/Index/".$page."/".$viewpage?>';
        			else {
        				noty({
        					text:message, layout: 'topCenter', type: 'success',timeout:5000}
        					);
        				$("#OrderProductProductId").val('');
        				$('.select').selectpicker('refresh');
        				$("#OrderProductProductQty").val('1');
        			}
        		} else {
        			noty({
        				text:message, layout: 'topCenter', type: 'error',timeout:5000}
        				);
        		}
        	}
        });
        return false;
    }

    function ShowLoadingEquipment() {
    	var panel	=	$("#productDiv").parents(".panel");
      	panel.append('<div class="panel-refresh-layer"><img src="<?php echo $this->webroot?>img/loaders/default.gif"/></div>');
      	panel.find(".panel-refresh-layer").width(panel.width()).height(panel.height());
      	panel.addClass("panel-refreshing");
      	onload();
    }

    function LoadDataProduct() {
      	ShowLoadingEquipment();
      	var panel	=	$("#productDiv").parents(".panel");
      	$("#productDiv").load("<?php echo $settings['cms_url'] . $ControllerName?>/ListItemProduct/<?php echo $ID?>",
      		function(){
      			panel.find(".panel-refresh-layer").remove();
      			panel.removeClass("panel-refreshing");
      			$("a[rel^='lightbox']").prettyPhoto({
      				social_tools :''
      			});

      			$(this).find(".icheckbox").iCheck({
      				checkboxClass: 'icheckbox_minimal-grey'
      			});

      			$("input[id^=productChk]").on('ifChecked', function(event){
      				$("#DeleteBtnProduct").show();
      			});

      			$("input[id^=productChk]").on('ifUnchecked', function(event){
      				var checked	=	"";
      				$("input[id^=productChk]").each(function(index){
      					if($(this).prop("checked")) {
      						checked			+=		$(this).val()+",";
      					}
      				});
      				checked		=	checked.substring(0,checked.length-1);
      				if(checked.length == 0) {
      					$("#DeleteBtnProduct").hide();
      				} else {
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

    function DeleteProduct(el,msg,id) {
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
    				$.getJSON("<?php echo $settings["cms_url"].$ControllerName?>/DeleteProduct/"+id,function(result) {
    					LoadDataProduct();
    					if(result.data.status == "1") {
    						noty({
    							text:result.data.message, layout: 'topCenter', type: 'success',timeout:5000}
    							);
    					} else {
    						noty({
    							text:result.data.message, layout: 'topCenter', type: 'error',timeout:5000}
    							);
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

    function DeleteAllProduct(el) {
    	var panel	=	$(el).parents(".panel");
    	var checked	=	"";
      	$("input[id^=productChk]").each(function(index){
      		if($(this).prop("checked")) {
      			checked			+=		$(this).val()+",";
      		}
      	});

    	checked		=	checked.substring(0,checked.length-1);
    	if(checked.length == 0) {
    		noty({
    			text: "<?php echo __('Please check product to be delete!')?>",
    			layout: 'topCenter',
    			timeout:5000,
    			buttons: [{
    				addClass: 'btn btn-success btn-clean', text: 'OK', onClick: function($noty){
    					$noty.close();
    				}
    			}]
    		});
    	} else {
    		noty({
    			text: "<?php echo __('Do you realy want to delete all checked product ?')?>",
    			layout: 'topCenter',
    			timeout:5000,
    			buttons: [{
    				addClass: 'btn btn-success btn-clean', text: '<?php echo __('Yes')?>', onClick: function($noty) {
    					$noty.close();
    					panel.append('<div class="panel-refresh-layer"><img src="<?php echo $this->webroot?>img/loaders/default.gif"/></div>');
    					panel.find(".panel-refresh-layer").width(panel.width()).height(panel.height());
    					panel.addClass("panel-refreshing");
    					$.getJSON("<?php echo $settings["cms_url"].$ControllerName?>/DeleteMultipleProduct/",{
    						"id":checked
    					},
    					function(result) {
    						panel.find(".panel-refresh-layer").remove();
    						panel.removeClass("panel-refreshing");
    						LoadDataProduct();
    						if(result.data.status == "1") {
    							noty({
    								text:result.data.message, layout: 'topCenter', type: 'success', timeout:5000}
    								);
    						} else {
    							noty({
    								text:result.data.message, layout: 'topCenter', type: 'error',timeout:5000}
    								);
    						}
    					});
    				}
    			},
    			{
    				addClass: 'btn btn-danger btn-clean', text: '<?php echo __('Cancel')?>', onClick: function($noty) {
    					$noty.close();
    				}
    			}]
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
	<li class="active">
		<?php echo __('Edit Data')?> : 
		<?php echo $detail[$ModelName]['order_no']?>
	</li>
</ul>
<!-- END BREADCRUMB -->

<!-- START CONTENT FRAME -->
<div class="content-frame">
	<!-- START CONTENT FRAME TOP -->
	<div class="content-frame-top">
		<div class="page-title">
			<h2>
				<span class="fa fa-th-large">
				</span> Edit :
				<?php echo $detail[$ModelName]['order_no']?>
			</h2>
		</div>
		<div class="pull-right">
			<a href="<?php echo $settings['cms_url'].$ControllerName."/Index/".$page."/".$viewpage?>" class="btn btn-danger">
				<i class="fa fa-bars"></i>
				<?php echo __('List Data')?>
			</a>
			<a href="<?php echo $settings['cms_url'].$ControllerName?>/Add" class="btn btn-primary">
				<i class="fa fa-plus"></i>
				<?php echo __('Add New Data')?>
			</a>
		</div>
	</div>
</div>
<!-- END CONTENT FRAME TOP -->

<!-- SALES PO TABLE -->
<?php if (!empty($salesPoData)): ?>
<div class="panel-body">
	<div class="row">
		<div class="col-md-12" style="padding-top: 20px;">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">
						<?php echo Inflector::humanize(Inflector::underscore("Sales PO"))?>
					</h3>
				</div>
				<div class="panel-body panel-body-table">
					<div class="table-responsive">
						<table class="table table-bordered table-striped table-actions">
							<thead>
								<tr>
									<th style="width:5%">No</th>
									<th style="width:10%">Customer Name</th>
									<th style="width:7%">No. Tlp</th>
									<th style="width:3%">PPN</th>
									<th style="width:12%">Alamat</th>
									<th style="width:11%">Description</th>
									<th style="width:8%">Created</th>
								</tr>
							</thead>
							<tbody>
								<?php $count = 0;?>
								<?php $count++;?>
								<?php $no		= $count++;?>
								<tr>
									<td><?= $no ?></td>
									<td><?= $salesPoData['SalesOrder']['custname']?></td>
									<td><?= $salesPoData['SalesOrder']['notlp']?></td>
									<td>
										<?php 
											$isPPN 	=	$salesPoData['SalesOrder']['is_ppn'];
											echo ($isPPN == 0)?"NO":"YES";
										?>
                  					</td>
                  					<td><?= $salesPoData['SalesOrder']['alamat']?></td>
                  					<td><?= $salesPoData['SalesOrder']['description']?></td>
                  					<td><?= date("M d, Y",strtotime($salesPoData['SalesOrder']['modified'])) ?></td>
                  				</tr>
                  			</tbody>
                  		</table>
                  	</div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif ?>
<!-- !END SALES PO TABLE -->

<!-- PAGE CONTENT WRAPPER -->
<div class="page-content-wrap">
	<div class="row">
		<div class="col-md-12">
			<!-- <form class="form-horizontal"> -->
				<div class="panel panel-default tabs">
					<ul class="nav nav-tabs" role="tablist">
						<li>
							<a href="#tab1" role="tab" data-toggle="tab">
								<?php echo __('Order Information')?>
							</a>
						</li>
						<li>
							<a href="#tab2" role="tab" data-toggle="tab">
								<?php echo __('Product Information')?>
							</a>
						</li>
					</ul>
					<div class="panel-body tab-content">
						<?php if(!empty($errMessage)):?>
							<div class="alert alert-danger" id="errorDiv">
								<button type="button" class="close" data-dismiss="alert">
									<span aria-hidden="true">×</span>
									<span class="sr-only">
										<?php echo __('Close')?>
									</span>
								</button>
								<strong>
									<?php echo __('Error')?>
								</strong>
								<ol>
									<?php foreach($errMessage as $message):?>
										<li><?php echo $message?></li>
									<?php endforeach;?>
								</ol>
							</div>
						<?php endif;?>
						<!-- START TAB1 -->
						<div class="tab-pane" id="tab1" >
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
									if(in_array($detail["Order"]["delivery_status"],array(3,5,6)) or
										in_array($detail["Order"]["pickup_status"],array(10)) ){
										$disabled	=	"disabled";
										$disabled2	=	"disabled=\"disabled\"";
									}
								?>

								<?php echo $this->Form->input('id', array(
									'type'			=>	'hidden',
									'readonly'		=>	'readonly'
								)); ?>

								<?php echo $this->Form->hidden("save_flag",array("id"=>"SaveFlag","value"=>"0"))?>

						<div class="panel-body">
		                	<div class="row" style="padding-bottom: 10px;">
		                        <div class="col-md-3">
		                            <?php echo $this->Form->input("order_no",
				                        array(
				                          "div"     =>  array("class"=>"form-group"),
				                          "label"     =>  array("class" =>  "col-md-12","text"=>__("PO No.(*)")),
				                          "between"   =>  '<div class="col-md-12">',
				                          "after"     =>  "</div>",
				                          "autocomplete"  =>  "off",
				                          "type"      =>  "text",
				                          "readonly"    =>  "readonly",
				                          "class"     =>  'form-control',
				                          'error'     =>  array(
				                            'attributes' => array(
				                              'wrap'  => 'label',
				                              'class' => 'error'
				                            )
				                          ),
				                          "format"    =>  array(
				                            'before',
				                            'label',
				                            'between',
				                            'input',
				                            'error',
				                            'after',
				                          ),
				                          "escape"    =>  false
				                        )
				                      )?>
		                        </div>
		                        <div class="col-md-3">
		                            <?php echo $this->Form->input("delivery_no",array(
				                        "div"     =>  array("class"=>"form-group"),
				                        "label"     =>  array("class" =>  "col-md-12","text"=>__("Delivery Order No.(*)")),
				                        "between"   =>  '<div class="col-md-12">',
				                        "after"     =>  "</div>",
				                        "autocomplete"  =>  "off",
				                        "type"      =>  "text",
				                        "readonly"    =>  "readonly",
				                        "class"     =>  'form-control',
				                        'error'     =>  array(
				                          'attributes' => array(
				                            'wrap'  => 'label',
				                            'class' => 'error'
				                          )
				                        ),
				                        "format"    =>  array(
				                          'before',
				                          'label',
				                          'between',
				                          'input',
				                          'error',
				                          'after',
				                        ),
				                        "escape"    =>  false
				                      )
				                    )?>
		                        </div>
		                        <div class="col-md-6">
		                            <?php echo $this->Form->input("customer_id",array(
		                            	"div"			=>	array("class"=>"form-group"),
		                            	"label"			=>	array("class"	=>	"col-md-12","text"=>__("Customers (*)")),
		                            	"between"		=>	'<div class="col-md-12">',
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
										"onchange"				=>	"DetailCustomer(this.value)"
									))?>
								</div>
		                	</div>
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
							<!-- END ROW 1 -->

							<div class="col-md-3">
		                    	<div class="col-md-12">
		                            <?php echo $this->Form->input("delivery_type_id",
		                                array(
		                                    "div"			=>	array("class"=>"form-group"),
		                                    "before"		=>	'<label class="col-md-12"> Delivery Method (*)</label><div class="col-md-12"><label class="check">',
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
		                                    "default"		=>	1
		                                )
		                            )?>

									<?php echo $this->Form->input("is_urgent",
		                                array(
		                                    "div"			=>	array("class"=>"form-group"),
		                                    "before"		=>	'<label class="col-md-12"> Is Urgent</label><div class="col-md-12"><label class="check">',
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

		                        	<?php echo $this->Form->input("is_ppn",
		                                array(
		                                    "div"			=>	array("class"=>"form-group"),
		                                    "before"		=>	'<label class="col-md-12"> With PPN %</label><div class="col-md-12"><label class="check">',
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

									<?php echo $this->Form->input("is_assembling",
		                                array(
		                                    "div"			=>	array("class"=>"form-group","id"=>"WithAssemblingDiv"),
		                                    "before"		=>	'<label class="col-md-12">With assembling ?</label><div class="col-md-12"><label class="check">',
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
							<div class="col-md-3">
		                    	<?php echo $this->Form->input("receiver_name",
									array(
										"div"			=>	array("class"=>"form-group"),
										"label"			=>	array("class"	=>	"col-md-12","text"=>__("Receiver Name.(*)")),
										"between"		=>	'<div class="col-md-12">',
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
										"label"			=>	array("class"	=>	"col-md-12","text"=>__("Phone No. (*)")),
										"between"		=>	'<div class="col-md-12">',
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
		                    	<div id=deliveryDiv>
		                                <?php echo $this->Form->input("delivery_date",
		                                    array(
		                                        "div"			=>	array("class"=>"form-group"),
		                                        "label"			=>	array(
		                                            "class"	=>	"col-md-12",
		                                            "text"	=>	"Must Deliver On (*)"
		                                        ),
		                                        "between"		=>	'<div class="col-md-12">',
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
		                        </div>
		                        <div id="pickupDiv">
		                                <?php
		                                echo $this->Form->input(
		                                    "pickup_date",
		                                    array(
		                                        "div"			=>	array("class"=>"form-group"),
		                                        "label"			=>	array(
		                                                                "class"	=>	"col-md-12",
		                                                                "text"	=>	"Pickup Date (*)"
		                                                            ),
		                                        "between"		=>	'<div class="col-md-12">',
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
		                                )
		                                ?>
		                        </div>
		                        <div id="assemblyDiv">
										<?php echo $this->Form->input(
												"assembly_date",
												array(
													"div"			=>	array("class"=>"form-group"),
													"label"			=>	array(
														"class"	=>	"col-md-12",
														"text"	=>	"Assembly Date (*)"
													),
													"between"		=>	'<div class="col-md-12">',
													"after"			=>	'</div><div class="col-md-12"><label class="check"><input type="checkbox" class="icheckbox" id="sameDayChecked"/>Same day</label></div>',
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
								</div>
		                    </div>
							<!-- END ROW 2 -->
							<div class="col-md-6">
								<?php echo $this->Form->input("address", array(
									"div"			=>	array("class"=>"form-group"),
									"label"			=>	array(
										"class"	=>	"col-md-12",
										"text"	=>	"Address (*)"
									),
									"between"		=>	'<div class="col-md-12">',
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
		                            "rows"			=>	5
		                        ))?>

		                        <?php echo $this->Form->input("description", array(
		                        	"div"			=>	array("class"=>"form-group"),
		                        	"label"			=>	array(
		                        		"class"	=>	"col-md-12",
		                        		"text"	=>	"Additional Notes"
		                        	),
		                        	"between"		=>	'<div class="col-md-12">',
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
		                        	"rows"			=>	5
		                        ))?>

		                        <?php echo $this->Form->hidden("latitude",array("id"=>"latStudio","readonly"=>"readonly"))?>
		                        <?php echo $this->Form->hidden("longitude",array("id"=>"lngStudio","readonly"=>"readonly"))?>
		                    </div>
							<!-- END ROW 3 -->
							<!-- <div class="col-md-5">
								<div class="form-group" id="deliveryDiv">
									<label class="col-md-12">Map Position (*)</label>
									<div class="col-md-12"  style="padding-left:15px; padding-right:15px;">
										<input type="text" id="pac-input" placeholder="Search Your Place" class="form-control"/>
										<?php
											$border	=	"#CCC";
											if ($this->Form->isFieldError('Order.latitude')) {
												$border	=	"#b64645";
											}
										?>
										<div id="map" style="display:block; float:left; height:240px; width:100%; border:1px solid <?php echo $border?>; border-radius:4px;"></div>
										<?php echo $this->Form->error("Order.latitude",null,array("wrap"=>"label","class"=>"error"))?>
									</div>
								</div>
								<?php echo $this->Form->hidden("latitude",array("id"=>"latStudio","readonly"=>"readonly"))?>
								<?php echo $this->Form->hidden("longitude",array("id"=>"lngStudio","readonly"=>"readonly"))?>
							</div> -->
							<!-- END ROW 4 -->
							<!-- END ROW 5 -->
							<?php if(
								(!is_null($detail["Order"]["delivery_status"]) && $detail["Order"]["delivery_status"] > 1) or
								(!is_null($detail["Order"]["assembly_status"]) && $detail["Order"]["assembly_status"] > 1) or
								(!is_null($detail["Order"]["pickup_status"]) && $detail["Order"]["pickup_status"] > 1)
								):
							?>

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
												<?php echo $driver["Driver"]["fullname"]?>
												<a href="javscript:void(0);" onclick="InfoStatusDriver('<?php echo $driver["TaskAssign"]["status"]?>')">[see status]</a>
												<br/>
												<?php if(!empty($driver["TaskAssign"]["reason"])):?>
													<blockquote style="font-size:20px;">
														<i>&ldquo;<?php echo $driver["TaskAssign"]["reason"]?>&rdquo;</i>
													</blockquote>
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
												<?php echo $technician["Driver"]["fullname"]?> 
												<a href="javascript:void(0);" onclick="InfoStatusTechnician('<?php echo $technician["TaskAssign"]["status"]?>')">[see status]</a>
												<br/>
												<?php if(!empty($technician["TaskAssign"]["reason"])):?>
													<blockquote style="font-size:20px;">
														<i>&ldquo;<?php echo $technician["TaskAssign"]["reason"]?>&rdquo;</i>
													</blockquote>
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
							<!-- END ROW 6 -->
							<div class="panel-footer">
								<a href="<?php echo $settings['cms_url'].$ControllerName?>" class="btn btn-danger">
									<span class="fa fa-times fa-left"></span> 
									<?php echo __('Cancel')?>
								</a>
								<!--button type="submit" onclick="OnClickSaveDirect()" class="btn btn-primary pull-right" style="margin-left:10px;"><?php echo __('Save and set product')?><span class="fa fa-floppy-o fa-right"></span></button-->
								<button type="submit" onclick="OnClickSaveStay()" id="btnSubmitEdit" class="btn btn-primary pull-right" >
										<?php echo __('Save and add more')?>
										<span class="fa fa-floppy-o fa-right">
										</span>
								</button>
							</div>
						</form>
						<!-- END PANEL BODY -->
					</div>
				</div>
			</div>
			<!-- END TAB 1 -->

			<!-- START TAB2 -->
			<div class="tab-pane active" id="tab2">
				<?php echo $this->Form->create('OrderProduct', array(
					'url' 			=>	'#',
					'class' 		=>	'form-horizontal',
					'onsubmit'		=>	'return false',
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
							<div class="col-xs-12">
								<div class="col-md-12" >
									<h3> Actions</h3>
									<div id="field">
										<div id="field0">
											<!-- Text input-->
											<div class="form-group">
												<?php echo $this->Form->input("product_id", array(
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
												));?>
											</div>
											<!-- Text input-->
											<?php echo $this->Form->input("qty", array(
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
											))?>

											<?php echo $this->Form->input("description", array(
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
												"rows"	=>	5
											))?>
											<!-- File Button --> 
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="panel-footer">
						<a href="<?php echo $settings['cms_url'].$ControllerName?>" class="btn btn-danger">
							<span class="fa fa-times fa-left"></span> 
							<?php echo __('Cancel')?>
						</a>
						<!--button type="submit" class="btn btn-primary pull-right" style="margin-left:10px;" onclick="$('#SaveFlag').val('1')" <?php echo $disabled2?>><?php echo __('Save')?><span class="fa fa-floppy-o fa-right"></span></button-->
						<button type="submit" class="btn btn-primary pull-right" style="margin-left:10px;" onclick="$('#SaveFlag').val('0');AddNewProductForm();" <?php echo $disabled2?> >
							<?php echo __('Save and add more')?>
							<span class="fa fa-floppy-o fa-right"></span>
						</button>
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
						<button class="btn btn-primary pull-right <?php echo $disabled?> " onclick="DeleteAllProduct(this)" style="margin-left:5px;display:none;" id="DeleteBtnProduct">
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
					No internet access
				</div>
			</div>
		</div>
		<!-- END TAB2 -->
	</div>
</div>
<!-- </form> -->
</div>
</div>
</div>

