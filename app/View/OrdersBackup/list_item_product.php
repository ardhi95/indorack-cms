<?php if(!empty($data)): ?>
<?php
	  $disabled	=	"";
	  $disabled2	=	"";
	  if(in_array($detail["Order"]["delivery_status"],array(3,5,6)))
	  {
		  $disabled	=	"disabled";
		  $disabled2	=	"disabled=\"disabled\"";
	  }
 ?>
<div class="table-responsive" id="table_product_variant">
    <table class="table table-bordered table-striped table-actions">
        <thead>
            <tr>
                <th style="width:5%;vertical-align:top;">
                	No
                </th>
                <th style="width:5%;vertical-align:top;" class="text-center">
                	<input type="checkbox" class="icheckbox" id="CheckAllProduct"/>
                </th>
                <th style="width:15%;vertical-align:top;" class="text-center">
                	Image
                </th>
                <th style="width:30%;vertical-align:top;" class="text-center">
                    <?php echo __('Product')?>
                </th>
                <th style="width:30%;vertical-align:top;" class="text-center">
                    <?php echo __('Notes')?>
                </th>
                <th style="width:5%;vertical-align:top;" class="text-center">
                    <?php echo __('Quantity')?>
                </th>
                <th style="width:10%;vertical-align:top;" class="text-center">
					<?php echo __('Actions')?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php $count = 0;?>
            <?php foreach($data as $data): ?>
            <?php $count++;?>
            <?php $no		=	$count;?>
            <tr>
                <td><?php echo $no ?></td>
                <td class="text-center">
                    <input type="checkbox" value="<?php echo $data["OrderProduct"]['id']?>" class="icheckbox" id="productChk<?php echo $data["OrderProduct"]['id']?>"/>
                </td>
                <td style="text-align:center;">
                	<?php if(!empty($data["Thumbnail"]["id"])):?>
                    <a rel="lightbox" title="<?php echo $data["Product"]['name'] ?>" href="<?php echo $data["MaxWidth"]["host"].$data["MaxWidth"]["url"]?>?time=<?php echo time()?>" style="border:0px;">
                        <img src="<?php echo $data["Thumbnail"]["host"].$data["Thumbnail"]["url"]?>?time=<?php echo time()?>" width="60" height="60"/>
                    </a>
                    <?php else:?>
                        <img src="<?php echo $this->webroot?>img/default_content.png" width="60" height="60"/>
                    <?php endif;?>
                </td>
                <td>
					<?php echo $data["Product"]['name']?>
                </td>
                <td>
					<?php echo $this->General->IsEmptyVal($data["OrderProduct"]['description'])?>
                </td>
                <td style="text-align:right;">
                    <?php echo number_format($data['OrderProduct']['qty']); ?>
                </td>
                <td class="text-center">
                    <?php
                        $disabledDelete		=	"disabled=disabled";
                        if($access[$aco_id]["_delete"] == 1)
                        {
                            $disabledDelete	=	"";
                        }
						
						if(
								in_array($data["Order"]["delivery_status"],array(3,5,6))
							or
								in_array($data["Order"]["pickup_status"],array(10))
						)
						{
							$disabledDelete	=	"disabled=disabled";
						}
                    ?>
                    <a href="javascript:void(0);" class="btn btn-danger btn-condensed btn-sm <?php echo $disabled?>" data-toggle="tooltip" data-placement="top" title="<?php echo __('Delete')?>" <?php echo $disabledDelete?> onclick="DeleteProduct(this,'<?php echo __('Do you realy want to delete this item ?')?>','<?php echo $data["OrderProduct"]['id']?>')">
                        <i class="fa fa-times"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else:?>
<div class="alert alert-danger" role="alert">
    <?php echo __('Data is not available!')?>
</div>
<?php endif;?>