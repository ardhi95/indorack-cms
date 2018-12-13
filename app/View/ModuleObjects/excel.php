<?php
$this->PhpExcel->createWorksheet()->setDefaultFont('Calibri', 12);

// define table cells
$table = array(
    array('label' => __('No'), 'filter' => false),
	array('label' => __('ID'), 'filter' => false),
    array('label' => __('Name'), 'filter' => true),
    array('label' => __('Parent'), 'filter' => true),
	array('label' => __('Last Changes'),'filter' => true)
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
		$parent	=	($data["Parent"]['parent_id'] == null) ? "-" : $data["Parent"]['alias'];
		$this->PhpExcel->addTableRow(array(
			$no,
			$data[$ModelName]['id'],
			$data[$ModelName]['alias'],
			$parent,
			date("M d, Y",strtotime($data[$ModelName]['modified']))
		));
	}
}
// close table and output
$this->PhpExcel->addTableFooter()->output($filename);
?>