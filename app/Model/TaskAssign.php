<?php
class TaskAssign extends AppModel
{
	function beforeSave($options = array())
	{
		if(!empty($this->data))
		{
			$checkFirst	=	$this->find("first",array(
								"conditions"	=>	array(
									"{$this->name}.task_id"		=>	$this->data[$this->name]['task_id'],
									"{$this->name}.order_id"	=>	$this->data[$this->name]['order_id'],
									"{$this->name}.employee_id"	=>	$this->data[$this->name]['employee_id']
								)
							));

			if(!empty($checkFirst))
				return false;
		}
		return true;
	}
	
	function afterSave($created, $options = array())
	{
		if($created)
		{
			
		}
	}
	
	function ValidateCancelDeliveryOrder()
	{
		App::uses('CakeNumber', 'Utility');
		
		$this->validate 	= array(
			'user_id' => array(
				'notBlank'	=> array(
					'rule' 		=> "notBlank",
					'message' 	=> __d('validation',"Your profile not found")
				),
				'CheckUserPriveleges'	=> array(
					'rule' 		=> "CheckUserPriveleges"
				)
			),
			'order_id' => array(
				'notBlank'	=> array(
					'rule' 		=> "notBlank",
					'message' 	=> __d('validation',"Order ID not found!")
				)
				// 'IsOrderCancelledIsExists'	=> array(
				// 	'rule' 		=> "IsOrderCancelledIsExists",
				// 	'message' 		=> __d('validation',"Order ID not found!")
				// )
			),
			'task_id' => array(
				'notBlank'	=> array(
					'rule' 		=> "notBlank",
					'message' 	=> __d('validation',"Detail task not found!")
				)
			)
		);
	}
	
	
	function ValidateAssignDriver()
	{
		App::uses('CakeNumber', 'Utility');
		
		$this->validate 	= array(
			'user_id' => array(
				'notBlank'	=> array(
					'rule' 		=> "notBlank",
					'message' 	=> __d('validation',"Your profile not found")
				),
				'CheckUserPriveleges'	=> array(
					'rule' 		=> "CheckUserPriveleges"
				)
			),
			'task_id' => array(
				'notBlank'	=> array(
					'rule' 		=> "notBlank",
					'message' 	=> __d('validation',"Order ID not found!")
				),
				'IsOrderIdExists'	=> array(
					'rule' 		=> "IsOrderIdExists"
				)
			),
			'employee_id' => array(
				'notBlank'	=> array(
					'rule' 		=> "notBlank",
					'message' 	=> __d('validation',"Please select driver")
				),
				'IsDriverExists'	=> array(
					'rule' 		=> "IsDriverExists",
					'message' 	=> __d('validation',"Data driver not found")
				)
			),
			'vehicle_no' => array(
				'notBlank'	=> array(
					'rule' 		=> "notBlank",
					'message' 	=> __d('validation',"Please insert vehicle number")
				),
				'minLength'	=> array(
					'rule' 		=> array("minLength",3),
					'message' 	=> __d('validation',"Vehicle number is too short")
				),
				'maxLength'	=> array(
					'rule' 		=> array("maxLength",10),
					'message' 	=> __d('validation',"Vehicle number is too long")
				)
			)
		);
	}
	
	function ValidateAssignTechnisian()
	{
		App::uses('CakeNumber', 'Utility');
		
		$this->validate 	= array(
			'user_id' => array(
				'notBlank'	=> array(
					'rule' 		=> "notBlank",
					'message' 	=> __d('validation',"Your profile not found")
				),
				'CheckUserPriveleges'	=> array(
					'rule' 		=> "CheckUserPriveleges"
				)
			),
			'task_id' => array(
				'notBlank'	=> array(
					'rule' 		=> "notBlank",
					'message' 	=> __d('validation',"Order ID not found!")
				),
				'IsOrderIdExists'	=> array(
					'rule' 		=> "IsOrderIdExists"
				)
			),
			'vehicle_no' => array(
				'noBlankVehicleNo'	=> array(
					'rule' 		=> "noBlankVehicleNo",
					'message' 	=> __d('validation',"Please insert vehicle no.")
				),
				'minLength'	=> array(
					'rule' 			=> array("minLength",3),
					'message' 		=> __d('validation',"Vehicle number is too short"),
					"allowEmpty"	=>	true
				),
				'maxLength'	=> array(
					'rule' 			=> array("maxLength",10),
					'message' 		=> __d('validation',"Vehicle number is too long"),
					"allowEmpty"	=>	true
				)
				
			),
			'technisian_id' => array(
				'notBlankTechnisian'	=> array(
					'rule' 		=> "notBlankTechnisian",
					'message' 	=> __d('validation',"Please select technisian")
				)
			)
		);
	}
	
	
	function noBlankVehicleNo()
	{
		if(isset($this->data[$this->name]['vehicle_no']))
		{
			$task_id		=	$this->data[$this->name]["task_id"];
			$Task			=	ClassRegistry::Init("Task");
			$DetailTask		=	$Task->find("first",array(
									"conditions"	=>	array(
										"Task.id"	=>	$task_id
									)
								));
								
			if($DetailTask["Task"]["task_type_id"] == "1")
			{
				return !empty($this->data[$this->name]['vehicle_no']);
			}
		}
		return true;
	}
	
	
	function notBlankTechnisian()
	{
		if(isset($this->data[$this->name]['technisian_id']))
		{
			return !empty($this->data[$this->name]['technisian_id']);
		}
		return true;
	}
	
	function IsOrderIdExists()
	{
		$order_id		=	$this->data[$this->name]["order_id"];
		$Order			=	ClassRegistry::Init("Order");
		
		$checkOrder		=	$Order->find("first",array(
								"conditions"	=>	array(
									"Order.id"		=>	$order_id
								)
							));
							
		
		if(empty($checkOrder))
		{
			return "Order not found!";
		}
		else if($checkOrder["Order"]["status"] == "2" || $checkOrder["Order"]["status"] == "3")
		{
			return "This order has already assign to another driver!";
		}
		else if($checkOrder["Order"]["status"] == "5")
		{
			return "This order is in progress delivery";
		}
		else if($checkOrder["Order"]["status"] == "6")
		{
			return "This order has completed";
		}
		return true;
	}
	
	function IsOrderCancelledIsExists()
	{
		$order_id		=	$this->data[$this->name]["order_id"];
		$Order			=	ClassRegistry::Init("Order");
		
		$checkOrder		=	$Order->find("first",array(
								"conditions"	=>	array(
									"Order.id"					=>	$order_id,
									"Order.delivery_status"		=>	array(3,5)
								)
							));
		return !empty($checkOrder);
	}
	
	function CheckUserPriveleges()
	{
		$user_id		=	$this->data[$this->name]["user_id"];
		$User			=	ClassRegistry::Init("User");
		
		$checkUser		=	$User->find("first",array(
								"conditions"	=>	array(
									"User.id"		=>	$user_id,
									"User.status"	=>	1
								)
							));
		
		if(empty($checkUser))
		{
			return "Your profile not found!";
		}
		else if($checkUser["User"]["aro_id"] != 4)
		{
			return "You dont have priveleges to assign job";
		}
		return true;
	}
	
	
	function IsDriverExists()
	{
		$driver_id		=	$this->data[$this->name]["employee_id"];
		$User			=	ClassRegistry::Init("User");
		
		$checkUser		=	$User->find("first",array(
								"conditions"	=>	array(
									"User.id"		=>	$driver_id,
									"User.aro_id"	=>	6,
									"User.status"	=>	1
								)
							));
		
		return !empty($checkUser);
	}
}
?>