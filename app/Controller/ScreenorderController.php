<?php

class ScreenorderController extends AppController
{
	public $components		=	array("General","Acl");

	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->layout	=	"screen";
	}

	public function index()
	{
		# code...
	}

	public function ListItemPengiriman()
	{
		# code...
		$this->layout	= "ajax";

		$this->loadModel('Order');
		$this->Order->bindModel(array(
			"belongsTo" =>	array(
				"Customer"	=>	array(
					"className"  =>	"User",
					"foreignKey" =>	"customer_id"
				),
				"DeliveryStatus"	=>	array(
					"className"  =>	"TaskStatus",
					"foreignKey" =>	"delivery_status"
				),
				"PickupStatus"	=>	array(
					"className"  =>	"TaskStatus",
					"foreignKey" =>	"pickup_status"
				),
			),
			"hasOne"	=>	array(
				"Task"	=>	array(
					"foreignKey"	=>	"order_id",
					"conditions"	=>	array(
						"Task.task_type_id" =>	"1"
					)
				)
			)
		));

		$this->Order->Task->bindModel(array(
			"hasOne"	=>	array(
				"TaskAssign"
			)
		));

		$this->Order->Task->TaskAssign->bindModel(array(
			"belongsTo"	=>	array(
				"Driver"	=>	array(
					"className"		=>	"User",
					"foreignKey"	=>	"employee_id"
				)
			)
		));

			$data 	=	$this->Order->find('all',array(
				'conditions' => array(
					"OR"	=>	array(
						"DATE_FORMAT(Order.delivery_date,'%Y-%m')"	=> date('Y-m'),
						"DATE_FORMAT(Order.pickup_date,'%Y-%m')"	=> date('Y-m')
					),
					/*"Order.delivery_status !="	=>	'6'*/
				),
				"order"		=>	array(
						"Order.id DESC"
						/*"Order.pickup_date asc",
						"Order.delivery_date asc"*/

					),
				"limit"		=>	"50",
				"recursive"	=>	"3"
			));

			pr($data);

		$this->set(compact(
			"data"
		));
	}

	public function ListItemPerakitan()
	{
		# code...
		$this->layout	= "ajax";
		$this->loadModel('Order');
		$joins			=	array(
			array(
				"table"			=>	"users",
				"alias"			=>	"Customer",
				'type'			 => 'LEFT',
				"conditions"	=>	array(
					"Customer.id	=	Order.customer_id"
				)
			),
			array(
				"table"			=>	"delivery_types",
				"alias"			=>	"DeliveryType",
				'type'			 => 'LEFT',
				"conditions"	=>	array(
					"Order.delivery_type_id	=	DeliveryType.id"
				)
			),
			array(
				"table"			=>	"task_statuses",
				"alias"			=>	"DeliveryStatus",
				'type'			 => 'LEFT',
				"conditions"	=>	array(
					"Order.delivery_status	=	DeliveryStatus.id"
				)
			),
			array(
				"table"			=>	"task_statuses",
				"alias"			=>	"AssemblyStatus",
				'type'			 => 'LEFT',
				"conditions"	=>	array(
					"Order.assembly_status	=	AssemblyStatus.id"
				)
			),
			array(
				"table"			=>	"task_statuses",
				"alias"			=>	"PickupStatus",
				'type'			 => 'LEFT',
				"conditions"	=>	array(
					"Order.pickup_status	=	PickupStatus.id"
				)
			),
			array(
				"table"			=>	"order_products",
				"alias"			=>	"OrderProduct",
				'type'			 => 'LEFT',
				"conditions"	=>	array(
					"
					OrderProduct.order_id	=	Order.id
					AND
					OrderProduct.id	=	(SELECT MAX(id) FROM order_products WHERE order_id = Order.id)
					"
				)
			),
			array(
				"table"			=>	"products",
				"alias"			=>	"Product",
				'type'			 => 'LEFT',
				"conditions"	=>	array(
					"OrderProduct.product_id	=	Product.id"
				)
			),
			array(
				"table"		 =>	"tasks",
				"alias"		 =>	"Task",
				'type'		 =>	"LEFT",
				"conditions" =>	array(
					"Task.order_id		=	Order.id",
					"Task.task_type_id 	= 	2"
				)
			),
			array(
				"table"		 =>	"task_assigns",
				"alias"		 =>	"TaskAssign",
				'type'		 =>	"LEFT",
				"conditions" =>	array(
					"TaskAssign.task_id	=	Task.id",
				)
			),
			array(
				"table"		 =>	"tasks",
				"alias"		 =>	"TaskDriver",
				'type'		 =>	"LEFT",
				"conditions" =>	array(
					"TaskDriver.order_id		=	Order.id",
					"TaskDriver.task_type_id 	= 	1"
				)
			),
			array(
				"table"		 =>	"task_assigns",
				"alias"		 =>	"TaskAssignDriver",
				'type'		 =>	"LEFT",
				"conditions" =>	array(
					"TaskAssignDriver.task_id	=	TaskDriver.id",
				)
			),
			array(
				"table"		 =>	"users",
				"alias"		 =>	"Technician",
				'type'		 =>	"LEFT",
				"conditions" =>	array(
					"TaskAssign.employee_id = Technician.id"
				)
			),
			array(
				"table"		 =>	"users",
				"alias"		 =>	"Driver",
				'type'		 =>	"LEFT",
				"conditions" =>	array(
					"TaskAssignDriver.employee_id = Driver.id"
				)
			)
		);
		$data 	=	$this->Order->find('all',array(
			"joins"			=>	$joins,
			"fields"			=>	array(
				"Order.id",
				"Order.order_no",
				"Order.is_assembling",
				"Order.assembly_date",
				"Order.delivery_date",
				"Order.delivery_status",
				"Order.pickup_status",
				"Order.address",
				"Order.pickup_date",
				"Order.is_assembling",
				"Order.is_urgent",
				"CONCAT(Customer.firstname,' ',Customer.lastname) as fullname",	
				"DeliveryStatus.name",
				"AssemblyStatus.name",
				"Product.name",
				"DeliveryType.name",
				"PickupStatus.name",
				"CONCAT(Driver.firstname,'',Driver.lastname) as Driver",
				"substring_index(GROUP_CONCAT(CONCAT(Technician.firstname,'',Technician.lastname) SEPARATOR ', '), ',',2) as Technician"
			),
			'conditions' => array(
				"Order.is_assembling"						=>	"1",
				"OR"	=>	array(
					"DATE_FORMAT(Order.delivery_date,'%Y-%m')"	=> date('Y-m'),
					"DATE_FORMAT(Order.pickup_date,'%Y-%m')"	=> date('Y-m')
				)
			),
			"order"		=>	array(
				"Order.assembly_date asc"
			),
			"limit"		=>	"20",
			"group"	=>	"Order.id"
		));

		pr($data);

		$this->set(compact(
			"data"
		));
	}

	public function FunctionName()
	{
		/*$this->autoRender	=	false;
		$this->autoLayout	=	false;*/


	}
}

?>
