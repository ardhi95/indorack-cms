<?php if(!empty($data)): ?>

<?php
	$order		=	array_keys($this->params['paging'][$ModelName]['order']);
	$direction	=	$this->params['paging'][$ModelName]["order"][$order[0]];
	$ordered	=	($order[0]!==0) ? "/sort:".$order[0]."/direction:".$direction: "";
?>
<?php $this->Paginator->options(array(
				'url'	=> array(
					'controller'	=> $ControllerName,
					'action'		=> 'ListItem/limit:'.$viewpage,
				),
				'onclick'=>"return onClickPage(this,'#contents_area',$(this).attr('href'));")
			);
?>

<script>


</script>

<!-- START DEFAULT DATATABLE -->
<?php
$fullscreened	=	($fullScreenMode == 1) ? 'panel-fullscreened' : '';
$faClass		=	($fullScreenMode == 1) ? 'fa-compress' : 'fa-expand';
?>
<?php if($fullScreenMode==1):?>
<div class="panel-fullscreen-wrap">
<?php endif;?>
<div class="panel panel-info <?php echo $fullscreened?>">

    <div class="dataTables_wrapper no-footer panel-heading">
    <div class="panel-body panel-body-table">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-actions">
                <thead>
                    <tr>
                    	<?php
						if(
							$access[$aco_id]["_update"] == 1 or
							$access[$aco_id]["_delete"] == 1
						):
						?>
                    	<!-- <th class="text-center">
                        	<input type="checkbox" class="icheckbox" id="CheckAll"/>
                        </th> -->
                        <?php endif;?>
                    	
                    	<th>
                        	<?php echo $this->Paginator->sort("$ModelName.order_no",__('PO No.'));?>
                        </th>
                        
                        <th>
                        	<?php echo $this->Paginator->sort("Customer.fullname",__('Customer'));?>
                        </th>
                        
                        <th>
                        	<?php echo $this->Paginator->sort("DeliveryType.name",__('Delivery Type'));?>
                        </th>
                        <th>
                        	<?php echo $this->Paginator->sort("Order.is_assembling",__('Assembling'));?>
                        </th>
                        
                        <th>
                        	<?php echo $this->Paginator->sort("DeliveryStatus.name",__('Delivery Status'));?>
                        </th>
                        
                        <th class="text-center">
                        	<?php echo $this->Paginator->sort("AssemblyStatus.name",__('Assembling Status'));?>
                        </th>
                        
                        <th class="text-center">
                        	<?php echo $this->Paginator->sort("PickupStatus.name",__('Pickup  Status'));?>
                        </th>
                        
                    </tr>
                </thead>
                <tbody>
                	<?php $count = 0;?>
					<?php foreach($data as $data): ?>
                    <?php $count++;?>
                    <?php $no		=	(($page-1)*$viewpage) + $count;?>
                   
                    <tr style="color: black;">
                        <td>
							<?php echo $data[$ModelName]['order_no']?>
                        </td>
                        <td>
                        	<?php echo $this->General->IsEmptyVal($data[0]['fullname'])?>
                        </td>
                        <td>
                        	<?php echo $this->General->IsEmptyVal($data["DeliveryType"]['name'])?>
                        </td>
                        <td style="text-align:center;">
							<?php
								if($data[$ModelName]['is_assembling'] == "1")
									echo "Yes";
								else
									echo "No";
							?>
                        </td>
                        
                        <td class="text-center">
							<?php echo $this->General->IsEmptyVal($data["DeliveryStatus"]['name'])?>
                        </td>
                        <td class="text-center">
							<?php echo $this->General->IsEmptyVal($data["AssemblyStatus"]['name'])?>
                        </td>
                        
                        <td class="text-center">
							<?php echo $this->General->IsEmptyVal($data["PickupStatus"]['name'])?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="panel-footer">
                <div class="dataTables_info">
                	<?php echo $this->Paginator->counter(array('format' => __('Showing %start% to %end% of %count% entries')));?>
                </div>
                <?php if($this->Paginator->hasPrev() or $this->Paginator->hasNext()):?>
                <ul class="pagination pagination-sm pull-right">
                <?php
					echo $this->Paginator->prev("&laquo;",
						array(
							"escape"	=>	false,
							'tag'		=>	"li"
						),
						"<a href='javascript:void(0)'>&laquo;</a>",
						array(
							'tag'		=>	"li",
							"escape"	=>	false,
							"class"		=>	"disabled"
						)
					);
					echo $this->Paginator->numbers(array(
						'separator'		=>	null,
						'tag'			=>	"li",
						'currentTag'	=>	'span',
						'currentClass'	=>	'active',
						'modulus'		=>	4
					));
					echo $this->Paginator->next("&raquo;",
						array(
							"escape"	=>	false,
							'tag'		=>	"li"
						),
						"<a href='javascript:void(0)'>&raquo;</a>",
						array(
							'tag'		=>	"li",
							"escape"	=>	false,
							"class"		=>	"disabled"
						)
					);
				?>
                </ul>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>
<!-- END SIMPLE DATATABLE -->
<?php if($fullScreenMode==1):?>
</div>
<?php endif;?>
<?php else:?>
<div class="alert alert-danger" role="alert">
    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
    <?php echo __('Data is not available!')?>
</div>
<?php endif;?>
