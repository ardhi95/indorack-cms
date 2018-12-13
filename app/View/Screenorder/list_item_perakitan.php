<table class="table">
	<thead>
		<tr>
			<th class="col-md-1">No</th>
			<th class="col-md-1">No. Order</th>
			<th class="col-md-6">Tujuan</th>
			<th class="col-md-2">Waktu</th>
			<th class="col-md-1">Technician</th>
			<th class="col-md-1">Status</th>
		</tr>
	</thead>
	<tbody>
		<?php $no = 0; ?>
		<?php foreach($data as $data):?>
			<?php 
			$string = $data['AssemblyStatus']['name'];
			$st = str_replace(' ', '', $string);
			$final = strtolower($st);
			if (!empty($data['Order']['pickup_date'])) {
				$originalDatePic = $data['Order']['pickup_date'];;
				$newDate = date("d-m-Y", strtotime($originalDatePic));
			}else{
				$originalDate = $data['Order']['assembly_date'];;
				$newDate = date("d-m-Y", strtotime($originalDate));
			}
			$no++;
			?>
			<?php 
			if (($data['Order']['is_urgent'] != 1) && ($data['DeliveryStatus']['name'] != "Accepted")) {
				$print	= $final;
			} elseif ($data['DeliveryStatus']['name'] == "Completed"){
				$print	= $final;
			} else{
				$print	= "invalid";
			}
			?> 
			<tr class="<?= $print; ?>">
				<td><?= $no; ?></td>
				<td><?= $data["Order"]["order_no"]?></td>
				<td><?= substr(($data['Order']['address']),0, 40); ?></td>
				<td><?= $newDate ?></td>
				<td><?= $data[0]['Technician'];?></td>
				<td>
					<?php if ($data['Order']['is_urgent'] == 1): ?>
						Urgent <?= $data['AssemblyStatus']['name']; ?>
					<?php else: ?>
						<?= $data['AssemblyStatus']['name']; ?>
					<?php endif ?>
				</td>
			</tr>
		<?php endforeach;?>
	</tbody>
</table>