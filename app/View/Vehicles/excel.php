<?php
$this->PhpExcel->createWorksheet()->setDefaultFont('Calibri', 12);

// define table cells
$table = array(
    array('label' => __('No'), 'filter' => false),
	array('label' => __('Vehicle No'), 'filter' => false),
    array('label' => __('Driver'), 'filter' => true),
	array('label' => __('Last Changes'), 'filter' => true),
	array('label' => __('Status'),'filter' => true)
);

// add heading with different font and bold text
$this->PhpExcel->addTableHeader($table, array('name' => 'Cambria', 'bold' => true));

// add data
if(!empty($data))
{
	$count = 0;
	foreach ($data as $data)
	{
		$count++;
		$no		=	(($page-1)*$viewpage) + $count;
		$parent	=	($data["Parent"]['parent_id'] == null) ? "-" : $data["Parent"]['name'];
		$this->PhpExcel->addTableRow(array(
			$no,
			$data[$ModelName]['vehicle_no'],
			$data["User"]['fullname'],
			date("M d, Y",strtotime($data[$ModelName]['modified'])),
			$data[$ModelName]['SStatus']
		));
	}
}
// close table and output
$this->PhpExcel->addTableFooter()->output($filename);
?>