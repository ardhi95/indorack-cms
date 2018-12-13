<?php if(!empty($data)): ?>
<div class="table-responsive" id="table_product_variant">
    <table class="table table-bordered table-striped table-actions">
        <thead>
            <tr>
                <th style="width:5%;vertical-align:top;">
                	No
                </th>
                <th style="width:5%;vertical-align:top;" class="text-center">
                	<input type="checkbox" class="icheckbox" id="CheckAllTarget"/>
                </th>
                <th style="width:30%;vertical-align:top;" class="text-center">
                    <?php echo __('Target Start')?>
                </th>
                <th style="width:30%;vertical-align:top;" class="text-center">
                    <?php echo __('Target End')?>
                </th>
                <th style="width:5%;vertical-align:top;" class="text-center">
                    <?php echo __('Total')?>
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
                    <input type="checkbox" value="<?php echo $data["SalesTarget"]['id']?>" class="icheckbox" id="targetChk<?php echo $data["SalesTarget"]['id']?>"/>
                </td>
                <td>
					<?php echo date("d M Y",strtotime($data["SalesTarget"]['start_date']))?>
                </td>
                <td>
					<?php echo date("d M Y",strtotime($data["SalesTarget"]['end_date']))?>
                </td>
                <td style="text-align:right;">
                    <?php echo number_format($data['SalesTarget']['total']); ?>
                </td>
                <td class="text-center">
                    <a href="javascript:void(0);" class="btn btn-danger btn-condensed btn-sm" data-toggle="tooltip" data-placement="top" title="<?php echo __('Delete')?>" onclick="DeleteTarget(this,'<?php echo __('Do you realy want to delete this target?')?>','<?php echo $data["SalesTarget"]['id']?>')">
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
    <?php echo __('No target are defined!')?>
</div>
<?php endif;?>