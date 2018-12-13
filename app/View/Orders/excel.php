<?php 
	
	$this->PhpExcel->createWorksheet()->setDefaultFont('Calibri', 12);

	// define table cells
	$table = array(
	    array('label' => __('No'), 'filter' => false),
		array('label' => __('No . PO'), 'filter' => false),
	    array('label' => __('Pelanggan'), 'filter' => false),
		array('label' => __('PO'), 'filter' => false),
		array('label' => __('Invoice'), 'filter' => false),
		array('label' => __('Gudang'),'filter' => false),
		array('label' => __('Driver'),'filter' => false),
		array('label' => __('Sampai Tujuan'),'filter' => false),
		array('label' => __('Kembali ke Gudang'),'filter' => false),
		array('label' => __('Perakitan'), 'filter' => false),
		array('label' => __('Kurir'), 'filter' => false),
		array('label' => __('Teknisi'), 'filter' => false),
		array('label' => __('Pickup'), 'filter' => false)
	);

	// add heading with different font and bold text
	$this->PhpExcel->addTableHeader($table, array('name' => 'Cambria', 'bold' => true));

	if (!empty($data)) {
		$count = 0;
		foreach ($data as $key) {
			$count++;
			$no					=	(($page-1)*$viewpage) + $count;
			$po_no				=	$key["Order"]["order_no"];
			$custName 			=	$key[0]["fullname"];
			$driverName			=	$key[0]["Driver"];
			$teknisiName		=	$key[0]["Technitions"];
			$po_date			= 	"";
			$invoice 			= 	"";
			$gudang 			= 	"";
			$driver 			= 	"";
			$sampai 			= 	"";
			$finish 			= 	"";
			$rakit				= 	"";
			$pick 				= 	"";
			foreach ($key["OrderHistory"] as $k) {
				// PO date
				if ($k["description"] == "PO Telah dibuat") {
					$po_date	=	$k["created"];
				}

				// Invoice
				if ($k["description"] == "Invoice telah dibuat") {
					$invoice 	=	$k["created"];
				}

				// Gudang 
				if ($k["description"] == "Barang telah disiapkan") {
					$gudang 	=	$k["created"];
				}

				// Driver
				if ($k["description"] == "Driver mengantar pesanan") {
					$driver 	=	$k["created"];
				}

				// Sampai Tujuan
				if ($k["description"] == "Pesanan telah sampai") {
					$sampai 	=	$k["created"];
				}

				// Kembali Gudang
				if ($k["description"] == "Driver kembali ke Gudang") {
					$finish 	=	$k["created"];
				}

				// Teknisi
				if ($k["description"] == "Teknisi merakit pesanan") {
					$rakit		=	$k["created"];
				}

				// Pick Up
				if ($k["description"] == "Barang sudah diambil") {
					$pick 		=	$k["description"];
				}
			}
			// echo "No : ".$no;
			// echo "<br>";
			// echo "PO NO : ".$po_no;
			// echo "<br>";
			// echo "Customer : ".$custName;
			// echo "<br>";
			// echo "Driver : ".((!empty($driverName)) ? $driverName : "-");
			// echo "<br>";
			// echo "Technician : ".((!empty($teknisiName)) ? $teknisiName : "-");
			// echo "<br>";
			// echo "po_date : ".((!empty($po_date)) ? $po_date : "-");
			// echo "<br>";
			// echo "invoice : ".((!empty($invoice)) ? $invoice : "-");
			// echo "<br>";
			// echo "gudang : ".((!empty($gudang)) ? $gudang : "-");
			// echo "<br>";
			// echo "driver : ".((!empty($driver)) ? $driver : "-");
			// echo "<br>";
			// echo "sampai : ".((!empty($sampai)) ? $sampai : "-");
			// echo "<br>";
			// echo "finish : ".((!empty($finish)) ? $finish : "-");
			// echo "<br>";
			// echo "rakit : ".((!empty($rakit)) ? $rakit : "-");
			// echo "<br>";
			// echo "pick : ".((!empty($pick)) ? $pick : "-");
			// echo "<br>";
			// echo "========================================================";
			// echo "<br>";

			$this->PhpExcel->addTableRow(array(
				$no,
				$po_no,
				$custName,
				((!empty($po_date)) ? $po_date : "-"),
				((!empty($invoice)) ? $invoice : "-"),
				((!empty($gudang)) ? $gudang : "-"),
				((!empty($driver)) ? $driver : "-"),
				((!empty($sampai)) ? $sampai : "-"),
				((!empty($finish)) ? $finish : "-"),
				((!empty($rakit)) ? $rakit : "-"),
				((!empty($driverName)) ? $driverName : "-"),
				((!empty($teknisiName)) ? $teknisiName : "-"),
				((!empty($pick)) ? $pick : "-")
			));

		}
	}

	// close table and output
	$this->PhpExcel->addTableFooter()->output($filename);
 ?>