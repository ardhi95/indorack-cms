<?php $this->start("css");?>
<link rel="stylesheet" type="text/css" id="theme" href="<?php echo $this->webroot?>css/prettyPhoto.css"/>
<?php $this->end();?>

<?php $this->start("script");?>
<script type="text/javascript" src="<?php echo $settings['cms_url']?>js/jquery-prettyPhoto.js"></script>
<script>
function Before(el)
{
	var panel	=	$(el).parents(".panel");
	panel.append('<div class="panel-refresh-layer"><img src="<?php echo $this->webroot?>img/loaders/default.gif"/></div>');
	panel.find(".panel-refresh-layer").width(panel.width()).height(panel.height());
	panel.addClass("panel-refreshing");
	onload();
	var fullScreenMode		=	0;
	if(panel.hasClass("panel-fullscreened")){
		fullScreenMode		=	1;
	}
	else
	{
		fullScreenMode		=	0;
	}
	$(el).parents(".dropdown").removeClass("open");
	return fullScreenMode;
}

function CallBack(fullScreenMode)
{
	$(".panel-fullscreen").on("click",function(){
		panel_fullscreen($(this).parents(".panel"));
		return false;
	});

	if($(".icheckbox").length > 0){
		 $(".icheckbox,.iradio").iCheck({checkboxClass: 'icheckbox_minimal-grey',radioClass: 'iradio_minimal-grey'});

		$("#CheckAll").on('ifChecked', function(event){
			$('input[id^=chck_]').each(function(){
				if($(this).attr("disabled") != "disabled")
				{
					$(this).iCheck('check');
				}
			});
		});

		$("#CheckAll").on('ifUnchecked', function(event){
			$('input[id^=chck_]').iCheck('uncheck');
		});
	}

	var panel	= $(".panel-fullscreened");
	var head    = panel.find(".panel-heading");
	var body    = panel.find(".panel-body");
	var footer  = panel.find(".panel-footer");
	var hplus   = 30;

	if(body.hasClass("panel-body-table") || body.hasClass("padding-0")){
		hplus = 0;
	}
	if(head.length > 0){
		hplus += head.height()+21;
	}
	if(footer.length > 0){
		hplus += footer.height()+21;
	}

	panel.find(".panel-body,.chart-holder").height($(window).height() - hplus);

	if(fullScreenMode)
	{
		$("#pagination-center").show();
	}
	else
	{
		$("#pagination-center").hide();
	}

	onload();
	$("a[rel^='lightbox']").prettyPhoto({
		social_tools :''
	});
	
	$("input[name='data[Search][delivery_type_id]']").on('ifChecked', function(event){
		if($(this).val() == "1")
		{
			$("#DeliveryDiv").show(50);
			$("#PickupDiv").hide(50);
			
			$("#SearchPickupStartDate").val('');
			$("#SearchPickupEndDate").val('');

			$("#SearchPickupStatus").val('');
			$("#SearchPickupStatus").selectpicker('refresh');
			
		}
		else
		{
			$("#DeliveryDiv").hide(50);
			$("#PickupDiv").show(50);
			
			$("#SearchDeliveryStartDate").val('');
			$("#SearchDeliveryEndDate").val('');
			
			$("#SearchDeliveryStatus").val('');
			$("#SearchDeliveryStatus").selectpicker('refresh');
		}
	});
}


function Refresh(el)
{
	var fullScreenMode	=	Before(el);
	$("#contents_area").load("<?php echo $settings['cms_url'] . $ControllerName?>/ListItem/page:<?php echo $page?>/limit:<?php echo $viewpage?>/fullScreenMode:"+fullScreenMode,function(){
		CallBack(fullScreenMode);

	});
}

function onClickPage(el,divName,url)
{
	var fullScreenMode	=	Before(el);
	$(divName).load(url + "/fullScreenMode:"+fullScreenMode,function(){
		CallBack(fullScreenMode);
	});
	return false;
}

function SearchAdvance()
{
	var fullScreenMode;
	$("#SearchAdvance").ajaxSubmit({
		url:"<?php echo $settings['cms_url'].$ControllerName ?>/ListItem",
		type:'POST',
		dataType: "html",
		clearForm:false,
		beforeSend:function()
		{
			$("#reset").val("0");
			fullScreenMode	=	Before($(".dataTables_wrapper"));
		},
		complete:function(data,html)
		{

		},
		error:function(XMLHttpRequest, textStatus,errorThrown)
		{
			alert(textStatus);
		},
		success:function(data)
		{
			$("#contents_area").html(data);
			CallBack(fullScreenMode);
		}
	});

	return false;
}

function ClearSearchAdvance()
{
	$(':input','#SearchAdvance')
	.not(':button, :submit, :reset, :hidden, :radio')
	.val('')
	.removeAttr('checked')
	.removeAttr('selected')
	
	$(':radio','#SearchAdvance').iCheck('uncheck');

	$("#SearchCustomerId > option").removeAttr('selected');
	$("#SearchStatus > option").removeAttr('selected');

	$(".select").find('option').each(function(){
        $(this).removeAttr('selected');
    });
	$('.select').selectpicker('refresh');
	$('#reset').val('1');
	$("#DeliveryDiv").hide();
	$("#PickupDiv").hide();
	SearchAdvance();
}

$(document).ready(function(){
	Refresh();
	
	
});
</script>
<?php $this->end()?>

                        
<!-- START BREADCRUMB -->
<ul class="breadcrumb push-down-0">
    <li>
    	<a href="javascript:void(0);">
			<?php echo Inflector::humanize(Inflector::underscore($ControllerName))?>
       	</a>
    </li>
    <li class="active"><?php echo __('List Data')?></li>
</ul>
<!-- END BREADCRUMB -->

<!-- PAGE TITLE -->
<div class="content-frame">
    <div class="content-frame-top">
        <div class="page-title">
            <h2><span class="fa fa-th-large"></span> <?php echo Inflector::humanize(Inflector::underscore($ControllerName))?></h2>
        </div>
        <div class="pull-right">
            <a href="<?php echo $settings['cms_url'].$ControllerName?>/ListItem/true" class="btn btn-danger" target="_top">
                <i class="fa fa-bars"></i> <?php echo __('Export Data')?>
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

            <div class="panel panel-success panel-toggled">
    			<div class="panel-heading">
                    <h3 class="panel-title">
                    	<?php echo __('Advance Search')?>
                    </h3>
                    <ul class="panel-controls">
                        <li>
                        	<a href="#" class="panel-collapse">
                            	<span class="fa fa-angle-down"></span>
                            </a>
                        </li>
                    </ul>
                </div>
                <?php echo $this->Form->create("Search",array("onsubmit"=>"return SearchAdvance()","url"=>"","id"=>"SearchAdvance","class"=>"form-horizontal"))?>
                <div class="panel-body">
                    <input name="data[Search][reset]" type="hidden" value="0" id="reset">
                    <div class="col-md-6">
                        <?php echo $this->Form->input("Search.order_no",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("PO No.")),
                                "between"		=>	'<div class="col-md-6">
                                                        <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <span class="fa fa-search"></span>
                                                            </span>',
                                "after"			=>	"</div></div>",
                                "autocomplete"	=>	"off",
                                "type"			=>	"text",
                                "class"			=>	'form-control'
                            )
                        )?>
                        
                        <?php echo $this->Form->input("Search.customer_id",
                            array(
                                "div"			=>	array("class"=>"form-group"),
                                "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("Customers")),
                                "between"		=>	'<div class="col-md-6">',
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
								"data-live-search"		=>	"true"
                            )
                        )?>
                    </div>
                    <div class="col-md-6">
                    	<?php echo $this->Form->input("Search.delivery_type_id",
                            array(
                                "div"			=>	array("class"=>"form-group"),
								"before"		=>	'<label class="col-md-3 control-label">Delivery Type</label><div class="col-md-9"><label class="check">',
								"after"			=>	'</label></div>',
								"separator"		=>	'</label><label class="check">',
								"label"			=>	false,
                                "options"		=>	array("1"=>__("Delivery by driver"),"2"=>__("Pickup by customer")),
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
                        )?>
                        
                        <div style="width:100%;display:none;" id="PickupDiv">
                        	<div class="form-group">
                                <label class="col-md-3 control-label"><?php echo __('Pickup Schedule')?></label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" class="form-control datepicker" id="SearchPickupStartDate" name="data[Search][pickup_start_date]"/>
                                        <span class="input-group-addon add-on"> - </span>
                                        <input type="text" class="form-control datepicker" id="SearchPickupEndDate" name="data[Search][pickup_end_date]"/>
                                    </div>
                                </div>
                            </div>
                            
                            <?php echo $this->Form->input("Search.pickup_status",
                                array(
                                    "div"			=>	array("class"=>"form-group"),
                                    "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("Pickup Status")),
                                    "between"		=>	'<div class="col-md-6">',
                                    "after"			=>	"</div>",
                                    "autocomplete"	=>	"off",
                                    "options"		=>	$task_status_list,
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
                                    "empty"					=>	__("Select status"),
                                    "data-live-search"		=>	"true"
                                )
                            )?>
                        </div>
                        
                        
                        <div style="width:100%;display:none;" id="DeliveryDiv">
                        	<div class="form-group">
                                <label class="col-md-3 control-label"><?php echo __('Del. Schedule')?></label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" class="form-control datepicker" id="SearchDeliveryStartDate" name="data[Search][delivery_start_date]"/>
                                        <span class="input-group-addon add-on"> - </span>
                                        <input type="text" class="form-control datepicker" id="SearchDeliveryEndDate" name="data[Search][delivery_end_date]"/>
                                    </div>
                                </div>
                            </div>
                            
                            <?php echo $this->Form->input("Search.delivery_status",
                                array(
                                    "div"			=>	array("class"=>"form-group"),
                                    "label"			=>	array("class"	=>	"col-md-3 control-label","text"=>__("Del. Status")),
                                    "between"		=>	'<div class="col-md-6">',
                                    "after"			=>	"</div>",
                                    "autocomplete"	=>	"off",
                                    "options"		=>	$task_status_list,
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
                                    "empty"					=>	__("Select status"),
                                    "data-live-search"		=>	"true"
                                )
                            )?>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <button class="btn btn-primary"><?php echo __('Search')?></button>
                    <a href="javascript:void(0);" onclick="ClearSearchAdvance();" class="btn btn-default pull-right"><?php echo __('Clear Form')?></a>
                </div>
                </form>
             </div>

        	<div id="contents_area">
            </div>
        </div>
    </div>
</div>
<!-- END PAGE CONTENT WRAPPER -->