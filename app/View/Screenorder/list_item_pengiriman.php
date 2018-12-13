<table class="table">
	<thead>
		<tr>
			<th class="col-md-1">No</th>
			<th class="col-md-1">No. Order</th>
			<th class="col-md-6">Tujuan</th>
			<th class="col-md-2">Waktu</th>
			<th class="col-md-1">Driver</th>
			<th class="col-md-1">Status</th>
		</tr>
	</thead>
	<tbody>
		<?php $no = 0; ?>
		<?php foreach($data as $data):?>
			<?php 
				$string = $data['DeliveryStatus']['name'];
				$st = str_replace(' ', '', $string);
				$final = strtolower($st);
				
				if (!empty($data['Order']['pickup_date'])) {
					$originalDatePic = $data['Order']['pickup_date'];;
					$newDate = date("d-m-Y", strtotime($originalDatePic));
				}else{
					$originalDate = $data['Order']['delivery_date'];;
					$newDate = date("d-m-Y", strtotime($originalDate));
				}
				$no++;
			 ?>
			<?php 
				if (($data['Order']['is_urgent'] != 1) /*&& ($data['DeliveryStatus']['name'] != "Accepted")*/) {
					$print	= $final;
				} elseif ($data['DeliveryStatus']['name'] == "Completed"){
					$print	= $final;
				} else{
					$print	= "invalid";
				}
			 ?> 
		<tr class="<?= $print; ?>">
			 <td><?php echo $no; ?></td>
			<td><?php echo $data["Order"]["delivery_no"]?></td>
			<td>
				<?php if (!empty($data['PickupStatus']['name'])): ?>
					Pickup on indorack office
				<?php else: ?>
					<?php echo substr(($data['Order']['address']),0, 40); ?>
				<?php endif ?>
			</td>
			<td><?php echo $newDate ?></td>
			<td><?php echo $data['Task']['TaskAssign']['Driver']['firstname'].$data['Task']['TaskAssign']['Driver']['lastname']?></td>
			<td>
				<?php if (!empty($data['PickupStatus']['name'])): ?>
					<?php if ($data['Order']['is_urgent'] == 1): ?>
						Urgent
					<?php else: ?>
						<?php echo $data['PickupStatus']['name']; ?>
					<?php endif ?>
				<?php else: ?>
					<?php if ($data['Order']['is_urgent'] == 1): ?>
						Urgent <?php echo $data['DeliveryStatus']['name']; ?>
					<?php else: ?>
						<?php echo $data['DeliveryStatus']['name']; ?>
					<?php endif ?>
				<?php endif ?>
			</td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>