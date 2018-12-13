<?php
class Order extends AppModel
{
	public function __construct( $id = false, $table = NULL, $ds = NULL )
    {
        $this->locale       =   Configure::read('Config.language');
        parent::__construct($id,$table,$ds);
    }

	public function beforeSave($options = array())
	{
		if(!empty($this->data))
		{
			foreach($this->data[$this->name] as $key => $name)
			{
				if(!is_array($this->data[$this->name][$key]))
				{
					$this->data[$this->name][$key]		=	trim($this->data[$this->name][$key]);

					if($key == "delivery_date")
					{
						$this->data[$this->name][$key]		=	date("Y-m-d H:i:s",strtotime($this->data[$this->name][$key].":59"));
						
					}
					
					if($key == "pickup_date")
					{
						if(!empty($this->data[$this->name][$key]))
						{
							$this->data[$this->name][$key]		=	date("Y-m-d H:i:s",strtotime($this->data[$this->name][$key].":59"));
						}
					}
					
					if($key == "assembly_date")
					{
						if($this->data[$this->name]["is_assembling"] == "1")
						{
							if(!empty($this->data[$this->name][$key]))
							{
								$this->data[$this->name][$key]		=	date("Y-m-d H:i:s",strtotime($this->data[$this->name][$key].":59"));
							}	
							else
							{
								$this->data[$this->name]["assembly_date"]	=	NULL;
							}
						}
						else
						{
							$this->data[$this->name]["assembly_date"]	=	NULL;
						}
					}
				}
			}
		}
		
		if(empty($this->id))
		{
			if($this->data[$this->name]["is_assembling"] == "0")
			{
				$this->data[$this->name]["assembly_status"]	=	NULL;
			}
			else if($this->data[$this->name]["is_assembling"] == "1")
			{
				$this->data[$this->name]["assembly_status"]	=	1;
			}
			
			if($this->data[$this->name]["delivery_type_id"] == "2")
			{
				$this->data[$this->name]["receiver_name"]		=	NULL;
				$this->data[$this->name]["receiver_phone"]		=	NULL;
				$this->data[$this->name]["address"]				=	NULL;
				$this->data[$this->name]["latitude"]			=	NULL;
				$this->data[$this->name]["longitude"]			=	NULL;
				$this->data[$this->name]["delivery_date"]		=	NULL;
				$this->data[$this->name]["delivery_status"]		=	NULL;
				$this->data[$this->name]["pickup_status"]		=	"12";
			}
			else if($this->data[$this->name]["delivery_type_id"] == "1")
			{
				$this->data[$this->name]["pickup_date"]			=	NULL;
				$this->data[$this->name]["pickup_status"]		=	NULL;
			}
		}
		else
		{
			$beforeUpdate		=	$this->findById($this->id);
			$assemblyBefore		=	$beforeUpdate[$this->name]['is_assembling'];
			$assemblyAfter		=	$this->data[$this->name]['is_assembling'];
			
			
			if($assemblyBefore == "1" && $assemblyAfter == "0")
			{
				//UPDATE TASK
				$Task			=	ClassRegistry::Init("Task");
				$Task->updateAll(
					array(
						"status"	=>	8//CANCELLED	
					),
					array(
						"Task.order_id"			=>	$this->id,
						"Task.task_type_id"		=>	"2"
					)
				);
				
				//UPDATE TASK ASSIGN
				$detailTask			=	$Task->find("first",array(
											"conditions"	=>	array(
												"Task.order_id"			=>	$this->id,
												"Task.task_type_id"		=>	"2"
											)
										));
										
				$TaskAssign			=	ClassRegistry::Init("TaskAssign");
				$TaskAssign->updateAll(
					array(
						"status"						=>	8,
						"reason"						=>	"'Cancelled by head of inventory'"
					),
					array(
						"TaskAssign.task_id"			=>	$detailTask["Task"]["id"]
					)
				);
				
				//UPDATE TASK HISTORY
				$fTaskAssing				=	$TaskAssign->find("all",array(
													"conditions"	=>	array(
														"TaskAssign.task_id"	=>	$detailTask["Task"]["id"]
													),
													"order"	=>	"TaskAssign.id asc"
												));
												
				$TaskHistory				=	ClassRegistry::Init("TaskHistory");
				
				foreach($fTaskAssing as $fTaskAssing)
				{
					$TaskHistory->create();
					$TaskHistory->saveAll(
						array(
							"task_id"		=>	$detailTask["Task"]["id"],
							"order_id"		=>	$this->id,
							"employee_id"	=>	$fTaskAssing["TaskAssign"]["employee_id"],
							"reason"		=>	"Cancelled by Head of Inventory",
							"status"		=>	"8"
						),
						array(
							"validate"		=>	false
						)
					);
				}
				
				//UPDATE ORDER
				$this->updateAll(
					array(
						"assembly_status"	=>	NULL,
						"assembly_date"		=>	NULL
					),
					array(
						"{$this->name}.id"	=>	$this->id
					)
				);
				
			}
			elseif($assemblyBefore == "0" && $assemblyAfter == "1")
			{
				$Task			=	ClassRegistry::Init("Task");
				$checkFirst		=	$Task->find("first",array(
										"conditions"	=>	array(
											"Task.task_type_id"	=>	"2",
											"Task.order_id"		=>	$this->id,
										)
									));
									
				if(empty($checkFirst))
				{
					//SAVE TASK ASEMBLY
					$Task->create();
					$Task->saveAll(
						array(
							"task_type_id"	=>	"2",
							"order_id"		=>	$this->id
						),
						array(
							"valdate"		=>	false
						)
					);
				}
				else
				{
					//UPDATE TASK STATUS TO NOT ASSIGN
					$Task->updateAll(
						array(
							"status"				=>	1
						),
						array(
							"Task.id"				=>	$checkFirst['Task']['id']
						)
					);
				}
				
				//UPDATE ASSEMBLY STATUS
				$this->updateAll(
					array(
						"assembly_status"	=>	"1"
					),
					array(
						"{$this->name}.id"	=>	$this->id
					)
				);
			}
		
			//============== PICKUP ================//
			$beforeUpdate		=	$this->findById($this->id);
			$pickupBefore		=	$beforeUpdate[$this->name]['delivery_type_id'];
			$pickupAfter		=	$this->data[$this->name]['delivery_type_id'];
			
			//JIKA SEBELUMNYA MAU DIJEMPUT TAPI TIBA TIBA MINTA DI ANTER
			if($pickupBefore == "2" && $pickupAfter == "1")
			{
				$this->data[$this->name]["pickup_date"]			=	NULL;
				$this->data[$this->name]["delivery_status"]		=	"1";
				
				//CHECK DELIVERY TASK
				$Task	=	ClassRegistry::Init("Task");
				$deliveryTask	=	$Task->find("first",array(
					"conditions"	=>	array(
						"Task.order_id"		=>	$this->id,
						"Task.task_type_id"	=>	"1"
					)
				));
				
				if(empty($deliveryTask))
				{
					//SAVE TASK DELIVERY
					$Task->create();
					$Task->saveAll(
						array(
							"task_type_id"	=>	"1",
							"order_id"		=>	$this->id
						),
						array(
							"valdate"		=>	false
						)
					);
				}
				
				
				$is_assembling	=	$beforeUpdate[$this->name]['is_assembling'];
				if($is_assembling)
				{
					//CHECK ASSEMBLING TASK
					$assemblingTask	=	$Task->find("first",array(
						"conditions"	=>	array(
							"Task.order_id"		=>	$this->id,
							"Task.task_type_id"	=>	"2"
						)
					));
					
					if(empty($assemblingTask))
					{
						//SAVE TASK ASEMBLY
						$Task->create();
						$Task->saveAll(
							array(
								"task_type_id"	=>	"2",
								"order_id"		=>	$this->id
							),
							array(
								"valdate"		=>	false
							)
						);
					}
				}
				
				//=========== SAVE NOTIFICATION ================//
				$Notification		=	ClassRegistry::Init("Notification");
				$User				=	ClassRegistry::Init("User");
				$NotificationGroup	=	ClassRegistry::Init("NotificationGroup");
				
				$listKepalGudang	=	$User->find("list",array(
											"conditions"	=>	array(
												"User.aro_id"	=>	4
											),
											"fields"		=>	array(
												"User.id",
												"User.gcm_id"
											)
										));
				
				if(!empty($listKepalGudang))
				{
					$arrGcmId		=	array();
					$title			=	'INDORACK';
					$message    	=	"New order ".$beforeUpdate[$this->name]['delivery_no'];
					$description   	=	"PO No. : ".$beforeUpdate[$this->name]['order_no']."<br/>Delivery No. : ".$beforeUpdate[$this->name]['delivery_no']."<br/>To : ".$beforeUpdate[$this->name]["receiver_name"];
					
					$created		=	date("Y-m-d H:i:s");
					
					//CREATE NOTIFICATION GROUP
					$NotificationGroup->create();
					$NotificationGroup->saveAll(
						array(
							"created"	=>	$created
						),
						array(
							"validate"	=>	false
						)
					);
					$notificationGroupId	=	$NotificationGroup->id;
					
					foreach($listKepalGudang as $idKplGdng =>$gcm_id)
					{
						$Notification->create();
						$Notif["Notification"]["user_id"]					=	$idKplGdng;
						$Notif["Notification"]["gcm_id"]					=	empty($gcm_id) ? NULL : $gcm_id;
						$Notif["Notification"]["notification_group_id"] 	=	$notificationGroupId;
						$Notif["Notification"]["order_id"]					=	$this->id;
						$Notif["Notification"]["title"]						=	$title;
						$Notif["Notification"]["params"]					=	json_encode(array(
																					array(
																						"key"	=>	"id",
																						"val"	=>	"1"
																					),
																					array(
																						"key"	=>	"task",
																						"val"	=>	"2"
																					)
																				));
						$Notif["Notification"]["message"]					=	$message;
						$Notif["Notification"]["description"]				=	$description;
						$Notif["Notification"]["android_class_name"]		=	'DashboardKepalaGudang';
						$Notif["Notification"]["created"]					=	$created;
						
						if(!empty($gcm_id))
							$arrGcmId[]								=	$gcm_id;
						$Notification->save($Notif,array("validate"=>false));
					}
					
					$res 						=	array();
        			$res['data']['title'] 		=	$title;
					$res['data']['message'] 	=	$message;
					$res['data']['class_name'] 	=	'DashboardKepalaGudang';
					$res['data']['params'] 		=	array(
														  array(
															  "key"	=>	"id",
															  "val"	=>	"1"
														  ),
														  array(
															  "key"	=>	"task",
															  "val"	=>	"2"
														  )
													  );
					$res['data']['created'] 				=	$created;
					$res['data']['notification_group_id'] 	=	$notificationGroupId;
					
					
					$fields = array(
						"registration_ids" 		=>	$arrGcmId,
						"data" 					=>	$res,
						"priority"				=>	"high",
						"time_to_live"			=>	2419200
					);
					
					App::import('Component','General');
					$General = new GeneralComponent();
					$push	=	$General->sendPushNotification($fields);
					
					//Configure::write("debug","2");
					//pr($push);
					//pr(json_encode($fields));
				}
				//=========== SAVE NOTIFICATION ================//
			}
			//============== PICKUP ================//
		}
		return true;
	}
	
	public function afterSave($created,$options = array())
	{
		if($created)
		{
			
			$Task			=	ClassRegistry::Init("Task");
			$Order			=	$this->findById($this->id);
			
			if($Order[$this->name]["delivery_type_id"] == "2")
				return;
			
			
			//SAVE TASK DELIVERY
			$Task->create();
			$Task->saveAll(
				array(
					"task_type_id"	=>	"1",
					"order_id"		=>	$this->id
				),
				array(
					"valdate"		=>	false
				)
			);
				
			$is_assembling	=	$Order[$this->name]['is_assembling'];
			if($is_assembling)
			{
				//SAVE TASK ASEMBLY
				$Task->create();
				$Task->saveAll(
					array(
						"task_type_id"	=>	"2",
						"order_id"		=>	$this->id
					),
					array(
						"valdate"		=>	false
					)
				);
			}
		}
	}
	
	public function beforeValidate($options = array())
	{
		if(isset($this->data["Order"]["assembly_date"]) && !empty($this->data["Order"]["assembly_date"]))
		{
			$this->data["Order"]["assembly_date"]	=	date("Y-m-d H:i:s",strtotime($this->data["Order"]["assembly_date"].":59"));
		}
		
		if(isset($this->data["Order"]["delivery_date"]) && !empty($this->data["Order"]["delivery_date"]))
		{
			$this->data["Order"]["delivery_date"]	=	date("Y-m-d H:i:s",strtotime($this->data["Order"]["delivery_date"].":59"));
		}
		
		if(isset($this->data["Order"]["pickup_date"]) && !empty($this->data["Order"]["pickup_date"]))
		{
			$this->data["Order"]["pickup_date"]	=	date("Y-m-d H:i:s",strtotime($this->data["Order"]["pickup_date"].":59"));
		}
		return true;
	}
	
	public function beforeDelete($cascade = true)
	{
		$data	=	$this->findById($this->id);
		if(
				in_array($data[$this->name]["delivery_status"],array(3,5,6))
			or
				in_array($data[$this->name]["pickup_status"],array(10))
		)
		{
			return false;
		}
		return true;
	}
	
	public function afterDelete()
	{
		//DELETE IMAGE CONTENT
		App::import('Component','General');
		$General		=	new GeneralComponent();
		$General->DeleteContent($this->id,$this->name);
		
		//DELETE ORDER PRODUCT
		$OrderProduct	=	ClassRegistry::Init("OrderProduct");
		$OrderProduct->deleteAll(array("OrderProduct.order_id"	=>	$this->id),true,true);
		
		//DELETE TASK
		$Task	=	ClassRegistry::Init("Task");
		$Task->deleteAll(array("Task.order_id"	=>	$this->id),true,true);
	}

	public function BindDefault($reset	=	true)
	{
		$this->bindModel(array(
			"belongsTo"	=>	array(
				"Customer"	=>	array(
					"foreignKey"	=>	"customer_id",
					"className"		=>	"User"
				),
				"OrderStatus"	=>	array(
					"foreignKey"	=>	"status"
				)
			)
		),$reset);
	}

	public function BindImageContent($reset	=	true)
	{
		$this->bindModel(array(
			"hasOne"	=>	array(
				"Thumbnail"	=>	array(
					"className"		=>	"Content",
					"foreignKey"	=>	"model_id",
					"conditions"	=>	array(
						"Thumbnail.model"	=>	$this->name,
						"Thumbnail.type"	=>	"square"
					)
				),
				"Default"	=>	array(
					"className"		=>	"Content",
					"foreignKey"	=>	"model_id",
					"conditions"	=>	array(
						"Default.model"	=>	$this->name,
						"Default.type"	=>	"default"
					)
				)
			)
		),$reset);
	}

	function VirtualFieldActivated()
	{
		/*$this->virtualFields = array(
			"SStatus"		=> 'IF(('.$this->name.'.status=\'1\'),\'Active\',\'Not Active\')'
		);*/
	}
	
	function ValidatePickup()
	{
		App::uses('CakeNumber', 'Utility');
		
		$this->validate 	= array(
			'id' => array(
				'ValidateOrderIdPickup'	=> array(
					'rule' 		=> "ValidateOrderIdPickup"
				)
			),
			'receiver_name' => array(
				'notBlank'	=> array(
					'rule' 		=> "notBlank",
					"message"	=>	__d('validation',"Pickup name cannot empty, please insert pickup name")
				)
			),
			'receiver_phone' => array(
				'notBlank'	=> array(
					'rule' 		=> "notBlank",
					"message"	=>	__d('validation',"Pickup phone cannot empty, please insert pickup phone")
				)
			),
			'images' => array(
				'notEmptyImage' => array(
					'rule' 			=>	"notEmptyImage",
					'message' 		=>	__d('validation','Please capture pickup person')
				),
				'extension' => array(
					'rule' 			=>	array('validateName', array('gif','jpeg','jpg','png')),
					'message' 		=>	__d('validation','Only (*.gif,*.jpeg,*.jpg,*.png) are allowed.')
				)
			)
		);
	}
	
	function ValidateOrderIdPickup()
	{
		if(
			isset($this->data[$this->name]["id"]) &&
				isset($this->data[$this->name]["employee_id"])
			)
		{
			$User		=	ClassRegistry::Init("User");
			$orderId	=	$this->data[$this->name]["id"];
			$employeeId	=	$this->data[$this->name]["employee_id"];
			
			$checkData	=	$this->find("first",array(
								"conditions"	=>	array(
									"{$this->name}.id"	=>	$this->data[$this->name]["id"]
								)
							));
							
			$checkUser	=	$User->find("first",array(
								"conditions"	=>	array(
									"User.id"	=>		$employeeId
								)
							));
							
			if(empty($checkData))
				return "Order ID not found";
				
			if($checkData[$this->name]["delivery_type_id"] != "2")
				return "This order is not pickup order";
				
			if(empty($checkUser) or $checkUser["User"]["aro_id"] != "4")
				return "You don't have authorize to update this order";
			
		}
		return true;
	}

	function ValidateData()
	{
		App::uses('CakeNumber', 'Utility');
		
		$this->validate 	= array(
			'order_no' => array(
				'notBlank'	=> array(
					'rule' 		=> "notBlank",
					'message' 	=> __d('validation',"Please insert PO number")
				),
				'minLength'	=> array(
					'rule' 		=> array("minLength","2"),
					'message' 	=> __d('validation',"Purchase PO number is too short")
				),
				'isUnique'	=> array(
					'rule' 		=> 'isUnique',
					'message' 	=> __d('validation',"Purchase PO number is already used"),
					"on"		=>	"create"
				),
				'OrderNoUniqueEdit'	=> array(
					'rule' 		=> 'OrderNoUniqueEdit',
					'message' 	=> __d('validation',"Purchase PO number is already used"),
					"on"		=>	"update"
				)
			),
			'delivery_no' => array(
				'notBlank'	=> array(
					'rule' 		=> "notBlank",
					'message' 	=> __d('validation',"Please insert delivery number")
				),
				'minLength'	=> array(
					'rule' 		=> array("minLength","2"),
					'message' 	=> __d('validation',"Delivery number is too short")
				),
				'isUnique'	=> array(
					'rule' 		=> 'isUnique',
					'message' 	=> __d('validation',"Delivery number is already used"),
					"on"		=>	"create"
				),
				'DeliveryNoUniqueEdit'	=> array(
					'rule' 		=> 'DeliveryNoUniqueEdit',
					'message' 	=> __d('validation',"Delivery number is already used"),
					"on"		=>	"update"
				)
			),
			'delivery_date' => array(
				'notBlankDelivery'	=> array(
					'rule' 		=> "notBlankDelivery",
					'message' 	=> __d('validation',"Please insert delivery date")
				),
				/*'HigherThanNow'	=> array(
					'rule' 		=> "HigherThanNow",
					'message' 	=> __d('validation',"Delivery date must be higher than now"),
					"on"		=>	"create"
				)*/
			),
			'pickup_date' => array(
				'ValidatePickupDate'	=> array(
					'rule' 		=> "ValidatePickupDate"
				)
			),
			'assembly_date' => array(
				'notEmptyAssemblyDate'	=> array(
					'rule' 		=> "notEmptyAssemblyDate",
					'message' 	=> __d('validation',"Please insert assembly date")
				),
				/*'HigherThanNow'	=> array(
					'rule' 		=> "HigherThanNow",
					'message' 	=> __d('validation',"Assembly date must be higher than now"),
					"on"		=>	"create"
				),*/
				'HigherThanDeliveryDate'	=> array(
					'rule' 		=> "HigherThanDeliveryDate",
					'message' 	=> __d('validation',"Assembly date must be higher than delivery date"),
					"on"		=>	"create"
				)
			),
			'customer_id' => array(
				'CustomerIdValidation'	=> array(
					'rule' 		=> "CustomerIdValidation",
					'message' 	=> __d('validation',"Please select customer")
				)
			),
			'firstname' => array(
				'NotEmptyFirstname'	=> array(
					'rule' 		=> "NotEmptyFirstname",
					'message' 	=> __d('validation',"First name cann't be empty")
				),
				'minLength'	=> array(
					'rule' 		=> array("minLength","2"),
					'message' 	=> __d('validation',"First name is too short"),
					'allowEmpty'	=>	true
				)
			),
			'email' => array(
				'NotEmptyEmail'	=> array(
					'rule' 		=> "NotEmptyEmail",
					'message' 	=> __d('validation',"Email cann't be empty")
				),
				'email'	=> array(
					'rule' 			=> "email",
					'message' 		=> __d('validation',"Email format is wrong"),
					'allowEmpty'	=>	true
				),
				'UniqueEmail' => array(
					'rule' 			=>	"UniqueEmail",
					'message' 		=>	__d('validation',"Email already exists, please insert another email"),
					"required"		=>	"create",
					'allowEmpty'	=>	true
				)
			),
			'password' => array(
				'NotEmptyPassword'	=> array(
					'rule' 		=> "NotEmptyPassword",
					'message' 	=> __d('validation',"Password cann't be empty")
				),
				'minLength' => array(
					'rule' 			=> array("minLength","8"),
					'message'		=> __d('validation',"Password is too short"),
					"allowEmpty"	=>	true
				)
			),
			'product_id' => array(
				'ProductIdValidation'	=> array(
					'rule' 			=> "ProductIdValidation",
					'message' 		=> __d('validation',"Please select product")
				)
			),
			'product_code' => array(
				'ProductCodeValidation'	=> array(
					'rule' 			=> "ProductCodeValidation"
				)
			),
			'product_name' => array(
				'ProductNameValidation'	=> array(
					'rule' 			=> "ProductNameValidation",
					'message' 		=> __d('validation',"Please insert product name")
				)
			),
			'product_description' => array(
				'minLength'	=> array(
					'rule' 			=>	array("minLength",3),
					'message' 		=>	__d('validation',"Product description is too short"),
					"allowEmpty"	=>	true
				)
			),
			'images' => array(
				'imagewidth'		=>	array(
					'rule' 			=>	array('imagewidth',$this->settings['product_width']),
					'message' 		=>	__d('validation','Please upload image with minimum width is %s px',array($this->settings['product_width']))
				),
				'imageheight'		=>	array(
					'rule' 			=>	array('imageheight',$this->settings['product_height']),
					'message' 		=>	__d('validation','Please upload image with minimum width is %s spx',array($this->settings['product_height']))
				),
				'extension' => array(
					'rule' 			=>	array('validateName', array('gif','jpeg','jpg','png')),
					'message' 		=>	__d('validation','Only (*.gif,*.jpeg,*.jpg,*.png) are allowed.')
				)
			),
			'receiver_name' => array(
				'notBlankDelivery'	=>	array(
					'rule' 			=>	"notBlankDelivery",
					'message' 		=>	__d('validation',"Please insert receiver name")
				),
				'minLength'	=> array(
					'rule' 			=>	array("minLength","2"),
					'message' 		=>	__d('validation',"Receiver name is too short"),
					"allowEmpty"	=>	true
				)
			),
			'receiver_phone' => array(
				'notBlankDelivery'	=>	array(
					'rule' 			=> 	"notBlankDelivery",
					'message' 		=>	__d('validation',"Please insert phone number")
				)
			),
			'address' => array(
				'notBlankDelivery'	=>	array(
					'rule' 			=>	"notBlankDelivery",
					'message' 		=>	__d('validation',"Please insert address")
				),
				'minLength'	=> array(
					'rule' 			=>	array("minLength","2"),
					'message' 		=>	__d('validation',"Receiver name is too short"),
					"allowEmpty"	=>	true
				)
			),
			'latitude' => array(
				'notBlankDelivery'	=>	array(
					'rule' 			=>	"notBlankDelivery",
					'message' 		=>	__d('validation',"Select delivery position by dragging marker")
				)
			)
		);
	}

	function HigherThanNow($fields = array())
	{
		foreach ($fields as $key => $value)
		{
			if(!empty($this->data[$this->name][$key]))
			{
				$delivery_date	=	strtotime($value);
				$now			=	mktime(0,0,0,date("n"),date("j"),date("Y"));
				return $delivery_date > $now;
			}
		}
		return true;
	}
	
	
	function ValidatePickupDate()
	{
		$deliveryTypeId	=	$this->data[$this->name]['delivery_type_id'];
		if($deliveryTypeId == "2")
		{
			$pickupDate	=	$this->data[$this->name]['pickup_date'];
			if(empty($pickupDate))
				return "Please insert pickup date";
				
			$pickupDateTimestamp	=	strtotime($pickupDate);
			$now					=	mktime(0,0,0,date("n"),date("j"),date("Y"));
			if($pickupDateTimestamp < $now)
				return "Pickup date must be higher than now";
		}
		return true;
	}
	
	function notBlankDelivery($fields=array())
	{
		$deliveryTypeId	=	$this->data[$this->name]['delivery_type_id'];
		
		foreach($fields as $k => $v)
		{
			$field		=	$this->data[$this->name][$k];
			
			if($deliveryTypeId == "1")
				return !empty($field);
		}
		return true;
	}
	
	function HigherThanDeliveryDate()
	{
		$is_assembling			=	$this->data[$this->name]['is_assembling'];
		$delivery_type_id		=	$this->data[$this->name]['delivery_type_id'];
		
		if($is_assembling == "1" && $is_assembling=="1")
		{
			if(!empty($this->data[$this->name]['assembly_date']) && !empty($this->data[$this->name]['delivery_date']))
			{
				$AssemblyDate	=	strtotime($this->data[$this->name]['assembly_date']);
				$DeliveryDate	=	strtotime($this->data[$this->name]['delivery_date']);
				return $AssemblyDate  >= $DeliveryDate;
			}
		}
		return true;
	}
	
	function notEmptyAssemblyDate()
	{
		$is_assembling		=	$this->data[$this->name]['is_assembling'];
		$delivery_type_id	=	$this->data[$this->name]['delivery_type_id'];
		
		if($is_assembling == "1" && $delivery_type_id=="1")
		{
			$AssemblyDate	=	$this->data[$this->name]['assembly_date'];
			return !empty($AssemblyDate);
		}
		return true;
	}
	
	function CustomerIdValidation()
	{
		if(isset($this->data['Order']['is_new_customer']))
		{
			$is_new_customer	=	$this->data['Order']['is_new_customer'];
			if($is_new_customer == "0")
			{
				$customer_id	=	$this->data['Order']['customer_id'];
				return !empty($customer_id);
			}
		}
		return true;
	}
	
	function ProductIdValidation()
	{
		$is_new_product	=	$this->data['Order']['is_new_product'];
		if($is_new_product == "0")
		{
			$product_id	=	$this->data['Order']['product_id'];
			return !empty($product_id);
		}
		return true;
	}
	
	function ProductCodeValidation()
	{
		$is_new_product	=	$this->data['Order']['is_new_product'];
		if($is_new_product == "1")
		{
			$product_code	=	$this->data['Order']['product_code'];
			if(empty($product_code))
			{
				return "Please insert product code/id";
			}
			else
			{
				$Product	=	ClassRegistry::Init("Product");
				$checkCode	=	$Product->find("first",array(
									"conditions"	=>	array(
										"Product.code"	=>	$product_code
									)
								));
								
				if(!empty($checkCode))
				{
					return "Product code/id already exists";
				}
				else
				{
					return true;
				}
			}
		}
		return true;
	}
	
	function ProductNameValidation()
	{
		$is_new_product	=	$this->data['Order']['is_new_product'];
		if($is_new_product == "1")
		{
			$product_name	=	$this->data['Order']['product_name'];
			return !empty($product_name);
		}
		return true;
	}
	
	function OrderNoUniqueEdit()
	{
		$order_no	=	$this->data['Order']['order_no'];
		$orderId	=	$this->data['Order']['id'];
		
		$check		=	$this->find("first",array(
							"conditions"	=>	array(
								"{$this->name}.order_no"	=>	$order_no,
								"NOT"	=>	array(
									"{$this->name}.id"	=>	$orderId,
								)
							)
						));
		return empty($check);
	}
	
	function DeliveryNoUniqueEdit()
	{
		$order_no	=	$this->data['Order']['delivery_no'];
		$orderId	=	$this->data['Order']['id'];
		
		$check		=	$this->find("first",array(
							"conditions"	=>	array(
								"{$this->name}.delivery_no"	=>	$order_no,
								"NOT"	=>	array(
									"{$this->name}.id"	=>	$orderId,
								)
							)
						));
		return empty($check);
	}
	
	function NotEmptyFirstname()
	{
		$is_new_customer	=	$this->data['Order']['is_new_customer'];
		if($is_new_customer == "1")
		{
			$firstName	=	$this->data['Order']['firstname'];
			return !empty($firstName);
		}
		return true;
	}
	
	function NotEmptyEmail()
	{
		$is_new_customer	=	$this->data['Order']['is_new_customer'];
		if($is_new_customer == "1")
		{
			$email	=	$this->data['Order']['email'];
			return !empty($email);
		}
		return true;
	}
	
	function NotEmptyPassword()
	{
		$is_new_customer	=	$this->data['Order']['is_new_customer'];
		if($is_new_customer == "1")
		{
			$email	=	$this->data['Order']['password'];
			return !empty($email);
		}
		return true;
	}
	
	function UniqueEmail()
	{
		$email	=	$this->data["Order"]["email"];
		$User	=	ClassRegistry::Init("User");
		$data	=	$User->find("first",array(
						"conditions"	=>	array(
							"LOWER(User.email)"	=>	strtolower($email)
						)
					));
  
		return empty($data);
	}
	
	function IsExists($fields = array())
    {
        foreach ($fields as $key => $value) {
            $data = $this->findById($value);
            if (!empty($data))
                return true;
        }
        return false;
    }


	function UniqueName($fields = array())
	{
		foreach($fields as $key=>$value)
		{
			$data	=	$this->find("first",array(
							"conditions"	=>	array(
								"LOWER(I18n__nameTranslation.content)"	=>	strtolower($value)
							)
						));

			return empty($data);
		}
		return false;
	}

	function UniqueNameEdit($fields = array())
	{
		foreach($fields as $key=>$value)
		{
			$data	=	$this->find("first",array(
							"conditions"	=>	array(
								"LOWER(I18n__nameTranslation.content)"			=>	strtolower($value),
								"NOT"							=>	array(
									"{$this->name}.id"			=>	$this->data[$this->name]["id"]
								)
							)
						));

			return empty($data);
		}
		return false;
	}

	function size( $field=array(), $aloowedsize)
    {
		foreach( $field as $key => $value ){
            $size = intval($value['size']);
            if($size > $aloowedsize) {
                return FALSE;
            } else {
                continue;
            }
        }
        return TRUE;
    }

	function notEmptyImage($fields = array())
	{
		foreach($fields as $key=>$value)
		{
			if(empty($value['name']))
			{
				return false;
			}
		}

		return true;
	}

	function validateName($file=array(),$ext=array())
	{
		$err	=	array();
		$i=0;

		foreach($file as $file)
		{
			$i++;

			if(!empty($file['name']))
			{
				if(!Validation::extension($file['name'], $ext))
				{
					return false;
				}
			}
		}
		return true;
	}

	function imagewidth($field=array(), $allowwidth=0)
	{
		
		foreach( $field as $key => $value ){
			if(!empty($value['name']))
			{
				$imgInfo	= getimagesize($value['tmp_name']);
				$width		= $imgInfo[0];
				if($width < $allowwidth)
				{
					return false;
				}
			}
        }
        return TRUE;
	}

	function imageheight($field=array(), $allowheight=0)
	{
		foreach( $field as $key => $value ){
			if(!empty($value['name']))
			{
				$imgInfo	= getimagesize($value['tmp_name']);
				$height		= $imgInfo[1];

				if($height < $allowheight)
				{
					return false;
				}
			}
        }
        return TRUE;
	}
}
