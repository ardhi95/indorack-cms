<?php
App::uses('CakeNumber', 'Utility');
App::uses('Validation', 'Utility');
class OrdersController extends AppController
{
	var $ControllerName		=	"Orders";
	var $ModelName 			=	"Order";
	var $helpers 			=	array("Text","General");
	var $aco_id;

	function beforeFilter()
	{
		parent::beforeFilter();
		$this->set("ControllerName", $this->ControllerName);
		$this->set("ModelName", $this->ModelName);
		$this->{$this->ModelName}->locale =  $this->Session->read('Config.language');

		//CHECK PRIVILEGES
		$this->loadModel("MyAco");
		$find         =		$this->MyAco->find("first", array(
								 "conditions" => array(
								 	"LOWER(MyAco.controller)" => $this->ControllerName
								 )
							));

		$this->aco_id = $find["MyAco"]["id"];
		$this->set("aco_id", $this->aco_id);
	}

	function Index($page = 1, $viewpage = 50)
	{
		if ($this->access[$this->aco_id]["_read"] != "1"){
			$this->render("/Errors/no_access");
			return;
		}
		
		$this->Session->delete("Search." . $this->ControllerName);
		$this->Session->delete('Search.' . $this->ControllerName . 'Operand');
		$this->Session->delete('Search.' . $this->ControllerName . 'ViewPage');
		$this->Session->delete('Search.' . $this->ControllerName . 'Sort');
		$this->Session->delete('Search.' . $this->ControllerName . 'Page');
		$this->Session->delete('Search.' . $this->ControllerName . 'Conditions');

		//DEFINE CUSTOMER ID
		$this->loadModel("User");
		$this->User->VirtualFieldActivated();
		$customer_id_list		=	$this->User->find("list",array(
											"conditions"	=>	array(
												"User.aro_id"	=>	7//CUSTOMERS
											),
											"order"	=>	array(
												"User.firstname ASC"
											),
											"fields"	=>	array(
												"User.id",
												"User.fullname"
											)
										));
										
		//DEFINE ORDER STATUS
		$this->loadModel("TaskStatus");
		$task_status_list	=	$this->TaskStatus->find("list");								
										
		$this->set(compact(
			"page",
			"viewpage",
			"customer_id_list",
			"task_status_list"
		));
	}

	function ListItem($excel = "false")
	{
		$this->layout		=	"ajax";
		$fullScreenMode		=	$this->params['named']['fullScreenMode'];

		if ($this->access[$this->aco_id]["_read"] != "1") {
			   $data = array();
			   $this->set(compact("data"));
			   return;
		}

		$this->loadModel($this->ModelName);
		$this->{$this->ModelName}->bindModel(array(
			"hasMany"	=>	array(
				"OrderHistory"
			)
		),false);
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
		//DEFINE LAYOUT, LIMIT AND OPERAND
		$operand  	=	"AND";
		if($excel == "true")
		{
			$order		=	$this->Session->read("Search.".$this->ControllerName."Sort");
			$viewpage	=	$this->Session->read("Search.".$this->ControllerName."Viewpage");
		}
		else
		{
			$viewpage	=	empty($this->params['named']['limit']) ? 50 : $this->params['named']['limit'];
			$order    	= 	array(
							   "{$this->ModelName}.id" => "DESC"
							);
			$this->Session->write('Search.' . $this->ControllerName . 'Viewpage', $viewpage);
			$this->Session->write('Search.' . $this->ControllerName . 'Sort', (empty($this->params['named']['sort']) or !isset($this->params['named']['sort'])) ? $order : $this->params['named']['sort'] . " " . $this->params['named']['direction']);
		}

		//DEFINE SEARCH DATA
		if (!empty($this->request->data)) {
			$cond_search = array();
			$operand     = $this->request->data[$this->ModelName]['operator'];
			$this->Session->delete('Search.' . $this->ControllerName);

			if (!empty($this->request->data['Search']['order_no'])) {
				   $cond_search["{$this->ModelName}.order_no LIKE "] = "%".$this->data['Search']['order_no']."%";
			}

			if (!empty($this->request->data['Search']['item_name'])) {
				   $cond_search["{$this->ModelName}.item_name LIKE "] = "%".$this->data['Search']['item_name']."%";
			}
			
			if (!empty($this->request->data['Search']['customer_id'])) {
				   $cond_search["{$this->ModelName}.customer_id"] =	$this->data['Search']['customer_id'];
			}
			
			if (!empty($this->request->data['Search']['delivery_type_id']))
			{
				$cond_search["{$this->ModelName}.delivery_type_id"] =	$this->data['Search']['delivery_type_id'];
				
				if($this->request->data['Search']['delivery_type_id'] == "1")
				{
					if (!empty($this->request->data['Search']['delivery_status'])) {
						   $cond_search["{$this->ModelName}.delivery_status"] =	$this->data['Search']['delivery_status'];
					}
			
					if (!empty($this->data['Search']['delivery_start_date']) && empty($this->data['Search']['delivery_end_date'])) 
					{
						$cond_search["{$this->ModelName}.delivery_date >= "] = $this->data['Search']['delivery_start_date'] . " 00:00:00";
					}
					
					if (empty($this->data['Search']['delivery_start_date']) && !empty($this->data['Search']['delivery_end_date'])) {
						$cond_search["{$this->ModelName}.delivery_date <= "] = $this->data['Search']['delivery_end_date'] . " 23:59:59";
					}
					
					if (!empty($this->data['Search']['delivery_start_date']) && !empty($this->data['Search']['delivery_end_date'])) {
						$tmp		=	$this->data['Search']['delivery_start_date'];
						
						$START  	=	(strtotime($this->data['Search']['delivery_end_date']) < strtotime($this->data['Search']['delivery_start_date'])) ? $this->data['Search']['delivery_end_date'] : $this->data['Search']['delivery_start_date'];
						
						$END        =	($this->data['Search']['delivery_end_date'] < $tmp) ? $tmp : $this->data['Search']['delivery_end_date'];
						$cond_search["{$this->ModelName}.delivery_date BETWEEN ? AND ? "] = array(
							 $START . " 00:00:00",
							 $END . " 23:59:59"
						);
					}
				}
				else if($this->request->data['Search']['delivery_type_id'] == "2")
				{
					if (!empty($this->request->data['Search']['pickup_status'])) {
						   $cond_search["{$this->ModelName}.pickup_status"] =	$this->data['Search']['pickup_status'];
					}
					
					if (!empty($this->data['Search']['pickup_start_date']) && empty($this->data['Search']['pickup_end_date'])) 
					{
						$cond_search["{$this->ModelName}.pickup_date >= "] = $this->data['Search']['pickup_start_date'] . " 00:00:00";
					}
					
					if (empty($this->data['Search']['pickup_start_date']) && !empty($this->data['Search']['pickup_end_date'])) {
						$cond_search["{$this->ModelName}.pickup_date <= "] = $this->data['Search']['pickup_start_date'] . " 23:59:59";
					}
					
					if (!empty($this->data['Search']['pickup_start_date']) && !empty($this->data['Search']['pickup_end_date'])) {
						$tmp		=	$this->data['Search']['pickup_start_date'];
						
						$START  	=	(strtotime($this->data['Search']['pickup_end_date']) < strtotime($this->data['Search']['pickup_start_date'])) ? $this->data['Search']['pickup_end_date'] : $this->data['Search']['pickup_start_date'];
						
						$END        =	($this->data['Search']['pickup_end_date'] < $tmp) ? $tmp : $this->data['Search']['pickup_end_date'];
						$cond_search["{$this->ModelName}.pickup_date BETWEEN ? AND ? "] = array(
							 $START . " 00:00:00",
							 $END . " 23:59:59"
						);
					}
				}
			}
			
			

			if ($this->request->data["Search"]['reset'] == "0") {
				   $this->Session->write("Search." . $this->ControllerName, $cond_search);
				   $this->Session->write('Search.' . $this->ControllerName . 'Operand', $operand);
			}
		}

		$this->Session->write('Search.' . $this->ControllerName . 'Viewpage', $viewpage);
		$this->Session->write('Search.' . $this->ControllerName . 'Sort', (empty($this->params['named']['sort']) or !isset($this->params['named']['sort'])) ? $order : $this->params['named']['sort'] . " " . $this->params['named']['direction']);

		$cond_search     	=	array();
		$filter_paginate 	=	array();

		//DEFINE CURRENT PAGE
		if (
			isset($this->params['named']['page']) &&
			$this->params['named']['page'] >
			$this->params['paging'][$this->ModelName]['pageCount']
		)
		{
			   $this->params['named']['page'] = $this->params['paging'][$this->ModelName]['pageCount'];
		}

		if($excel == "true")
		{
			$page			=	$this->Session->read("Search.".$this->ControllerName."Page");
		}
		else
		{
			$page 	= empty($this->params['named']['page']) ? 1 : $this->params['named']['page'];
			$this->Session->write('Search.' . $this->ControllerName . 'Page', $page);
		}

		$this->paginate  	=	array(
									"{$this->ModelName}" => array(
										   "order" 			=>	$order,
										   'limit' 			=>	$viewpage,
                                           "maxLimit"       =>  1000,
										   "joins"			=>	$joins,
										   "fields"			=>	array(
										   		"Order.id",
										   		"Order.order_no",
												"Order.is_assembling",
												"Order.assembly_date",
												"Order.delivery_date",
												"Order.delivery_status",
												"Order.pickup_status",
												"Order.pickup_date",
												"Order.is_assembling",
												"CONCAT(Customer.firstname,' ',Customer.lastname) as fullname",	
												"DeliveryStatus.name",
												"AssemblyStatus.name",
												"Product.name",
												"DeliveryType.name",
												"PickupStatus.name",
												"CONCAT(Driver.firstname,'',Driver.lastname) as Driver",
												"GROUP_CONCAT(CONCAT(Technician.firstname,'',Technician.lastname) SEPARATOR ', ') as Technitions"
										   ),
										   "group"	=>	"Order.id"
									)
								);

		$ses_cond    		=	$this->Session->read("Search." . $this->ControllerName);
		$cond_search 		=	isset($ses_cond) ? $ses_cond : array();
		$ses_operand 		=	$this->Session->read("Search." . $this->ControllerName . "Operand");
		$operand     		=	isset($ses_operand) ? $ses_operand : "AND";
		$merge_cond  		=	empty($cond_search) ? $filter_paginate : array_merge($filter_paginate, array(
			   $operand => $cond_search
		));
		
		$data				=	array();

		try {
			$data        		= $this->paginate("{$this->ModelName}", $merge_cond);
		}
		catch (NotFoundException $e) {
			$count 				= $this->{$this->ModelName}->find('count',array("conditions"=>$merge_cond));
			$pageCount 			= intval(ceil($count / $viewpage));
			$this->request->params['named']['page'] = $pageCount;
			$this->Session->write('Search.' . $this->ControllerName . 'Page', $pageCount);
			$this->{$this->ModelName}->BindDefault(false);
			$data        		= $this->paginate("{$this->ModelName}", $merge_cond);
    	}
		$this->Session->write('Search.' . $this->ControllerName . 'Conditions', $merge_cond);
		pr($data);
		$this->set(compact(
			'data',
			'page',
			'viewpage',
			'fullScreenMode'
		));

		$filename		=	$this->ControllerName."-".date("dMY").".xlsx";
		if($excel == "true") {
			$this->set('filename',$filename);
			$this->render('excel');
		} else {
			$this->render('list_item');
		}
	}

	// function Excel()
	// {
	// 	if($this->access[$this->aco_id]["_read"] != "1")
	// 	{
	// 		$this->render("/Errors/no_access");
	// 		return;
	// 	}

	// 	$this->layout		=	"ajax";
	// 	$this->{$this->ModelName}->BindDefault(false);
	// 	$this->{$this->ModelName}->bindModel(array(
	// 		"hasMany"	=>	array(
	// 			"OrderHistory"
	// 		)
	// 	),false);
	// 	$this->{$this->ModelName}->User->VirtualFieldActivated();

	// 	$order				=	$this->Session->read("Search.".$this->ControllerName."Sort");
	// 	$viewpage			=	$this->Session->read("Search.".$this->ControllerName."Viewpage");
	// 	$page				=	$this->Session->read("Search.".$this->ControllerName."Page");
	// 	$conditions			=	$this->Session->read("Search.".$this->ControllerName."Conditions");

	// 	$this->paginate		=	array(
	// 								"{$this->ModelName}"	=>	array(
	// 									"order"				=>	$order,
	// 									"limit"				=>	$viewpage,
	// 									"conditions"		=>	$conditions,
	// 									"page"				=>	$page
	// 								)
	// 							);

	// 	$data				=	$this->paginate("{$this->ModelName}",$conditions);
	// 	$title			=	$this->ModelName;
	// 	$filename			=	$this->ControllerName."-".date("dMY").".xlsx";
	// 	$this->set(compact("data","title","page","viewpage","filename"));
	// }

	public function Excel()
	{
		if($this->access[$this->aco_id]["_read"] != "1")
		{
			$this->render("/Errors/no_access");
			return;
		}

		$this->layout		=	"ajax";

		$this->loadModel($this->ModelName);
		$this->{$this->ModelName}->BindDefault(false);
		$this->{$this->ModelName}->bindModel(array(
			"hasMany"	=>	array(
				"OrderHistory"
			)
		),false);

		$order				=	$this->Session->read("Search.".$this->ControllerName."Sort");
		$viewpage			=	$this->Session->read("Search.".$this->ControllerName."Viewpage");
		$page				=	$this->Session->read("Search.".$this->ControllerName."Page");
		$conditions			=	$this->Session->read("Search.".$this->ControllerName."Conditions");

		$joins			=	array(
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
		$this->paginate  	=	array(
									"{$this->ModelName}" => array(
										   	"order"				=>	$order,
											"limit"				=>	$viewpage,
											"conditions"		=>	$conditions,
											"page"				=>	$page,
										   	"joins"				=>	$joins,
										   	"fields"			=>	array(
										   		"Order.id",
										   		"Order.order_no",
												"Order.is_assembling",
												"Order.assembly_date",
												"Order.delivery_date",
												"Order.pickup_date",
												"Order.delivery_status",
												"Order.assembly_status",
												"Order.pickup_status",
												"Order.is_assembling",
												"CONCAT(Customer.firstname,' ',Customer.lastname) as Customer",
												"CONCAT(Driver.firstname,'',Driver.lastname) as Driver",
												"GROUP_CONCAT(CONCAT(Technician.firstname,'',Technician.lastname) SEPARATOR ', ') as Technician"
										   	),
										   	"group"	=>	"Order.id"
									)
								);
		
		$data				=	$this->paginate("{$this->ModelName}",$conditions);
		$title				=	$this->ModelName;
		$filename			=	$this->ControllerName."-".date("dMY").".xlsx";
		// pr($data);
		$this->set(compact("data","title","page","viewpage","filename"));	

	}

	function Add($salesPoId)
	{
		if($this->access[$this->aco_id]["_create"] != "1")
		{
			$this->render("/Errors/no_access");
			return;
		}

		if (!empty($salesPoId)) {
			$this->loadModel('SalesOrder');
			$salesPoData	=	$this->SalesOrder->find('first',array(
										'conditions'	=>	array(
											'SalesOrder.id'	=>	$salesPoId
										)
									)
								);
		}

		pr("Woooooooooooooo");
		//========== Genenrate PO & DO id ==========//
		$dateTimeZone 	= 	date_default_timezone_set("Asia/Jakarta");
		$date         	= 	date('ymdHis', time());
		$POID 			=	"PO_".$date;
		$DOID			=	"DO_".$date;
		//========== Genenrate PO & DO id ==========//
		//pr($salesPoData);
		
		$errorProductForm	=	false;
		$errorProductId		=	array();
		$errorProductQty	=	array();
		$errorProductNotes	=	array();
		
		var_dump($this->request->data);

		if(!empty($this->request->data))
		{
			//Configure::write("debug","2");
			//pr($this->request->data);
			
			$this->loadModel("User");
			
			$this->{$this->ModelName}->set($this->request->data);
			$this->{$this->ModelName}->ValidateData();
			$this->{$this->ModelName}->BindDefault(false);
			$this->{$this->ModelName}->Customer->virtualFields = array(
				"fullname"		=> "CONCAT(Customer.firstname,' ',Customer.lastname)",
			);
			
			$error							=	$this->{$this->ModelName}->invalidFields();
			
			if(!empty($this->request->data["OrderProduct"]))
			{
				foreach($this->request->data["OrderProduct"] as $k => $v)
				{
					$errorProductId[$k]		=	"";
					$errorProductQty[$k]	=	"";
					$errorProductNotes[$k]	=	"";
					
					if(empty($v["product_id"]))
					{
						$errorProductId[$k]		=	"Please select product";
						$errorProductForm		=	true;
					}
						
					if(intval($v["qty"])<=0)
					{
						$errorProductQty[$k]	=	"Please insert quantity product";
						$errorProductForm		=	true;
					}
				}
			}
			
			//Configure::write("debug","2");
			//pr($this->request->data);
			
			if(empty($error) && empty($errorProductForm))
			{
				$this->{$this->ModelName}->bindModel(array(
					"hasMany"	=>	array(
						"OrderProduct"
					)
				),false);
				
				$this->{$this->ModelName}->create();
				$save	=	$this->{$this->ModelName}->saveAll($this->request->data,array("validate"=>false));
				$ID		=	$this->{$this->ModelName}->getLastInsertId();
				$status =	"";
				$detail	=	$this->{$this->ModelName}->find("first",array(
								"conditions"	=>	array(
									"{$this->ModelName}.id"	=>	$ID
								)
							));

				$this->loadModel("OrderHistory");
				$request["OrderHistory"]["user_id"]			=	"0";
				$request["OrderHistory"]["order_id"]		=	$ID;
				// $request["OrderHistory"]["sales_po_id"]		=	$salesPoId;
				$request["OrderHistory"]["description"]		=	"Invoice telah dibuat";
				$request["OrderHistory"]["taskType"]		=	"0";
				
				
				if ($this->request->data[$this->ModelName]["delivery_type_id"] == "1") {
					if ($this->request->data[$this->ModelName]["is_assembling"] == "1") {
						$dataAss["OrderHistory"]["user_id"]			=	"0";
						$dataAss["OrderHistory"]["order_id"]		=	$ID;
						$dataAss["OrderHistory"]["description"]		=	"PO Telah dibuat";
						$dataAss["OrderHistory"]["taskType"]		=	"0";
						$dataAss["OrderHistory"]["status"]			=	"2";
						$this->OrderHistory->saveAll($dataAss,array("validate"=>false));

						$request["OrderHistory"]["status"]			=	"2";
						$status 									=	"2";
						$saveHistory	=	$this->OrderHistory->saveAll($request,array("validate"=>false));
						if ($saveHistory){
							$request["OrderHistory"]["status"]			=	"1";
							$status 									=	"1";
							$this->OrderHistory->saveAll($request,array("validate"=>false));
						}
					} else {
						$request["OrderHistory"]["status"]			=	"1";
						$status 									=	"1";
						$this->OrderHistory->saveAll($request,array("validate"=>false));
					}
				} else {
					$request["OrderHistory"]["status"]			=	"3";
					$status 									=	"3";
					$this->OrderHistory->saveAll($request,array("validate"=>false));
				}
				
				
				//========== UPDATE SALES PO =========//
				if (!empty($salesPoId)) {
					$this->loadModel('SalesOrder');
					$this->SalesOrder->updateAll(
						array(
							'SalesOrder.order_id'	=>	$ID,
							'SalesOrder.status'		=>	$status
						),
						array(
							'SalesOrder.id'			=>	$salesPoId
						)
					);
					
					$this->loadModel("OrderHistory");
					$this->OrderHistory->updateAll(
						array(
							'OrderHistory.order_id'			=>	$ID,
							'OrderHistory.status'			=>	$status
						),
						array(
							'OrderHistory.sales_po_id'		=>	$salesPoId
						)
					);
				}
				//========== END UPDATE SALES PO =========//
				
							
				//===========SAVE NEW USER===========//
				$isNewUser	=	$this->request->data[$this->ModelName]['is_new_customer'];
				if($isNewUser == "1")
				{
					$data['User']['firstname']	=	$this->request->data[$this->ModelName]['firstname'];
					$data['User']['lastname']	=	$this->request->data[$this->ModelName]['lastname'];
					$data['User']['email']		=	$this->request->data[$this->ModelName]['email'];
					$data['User']['password']	=	$this->request->data[$this->ModelName]['password'];
					$data['User']['aro_id']		=	"7";
					$data['User']['is_admin']	=	"0";
					$data['User']['status']		=	"1";
					
					$data['User']['address']	=	$this->request->data[$this->ModelName]['address'];
					$data['User']['latitude']	=	$this->request->data[$this->ModelName]['latitude'];
					$data['User']['longitude']	=	$this->request->data[$this->ModelName]['longitude'];
					$data['User']['phone1']		=	$this->request->data[$this->ModelName]['receiver_phone'];
					
					$this->User->create();
					$this->User->save($data,array("validate"=>false));
					$userId	=	$this->User->id;
					$this->{$this->ModelName}->updateAll(
						array(
							"customer_id"	=>	$userId
						),
						array(
							"{$this->ModelName}.id"	=>	$ID
						)
					);
				}
				//===========SAVE NEW USER===========//
				
				//========== GET SALES ID ==========//
				$salesID 	=	$this->SalesOrder->find("first", array(
							"conditions"	=>	array(
								"SalesOrder.id"	=>	6
							),
							"fields"		=>	array(
								"SalesOrder.user_id"
							)
						));
				//========== GET SALES ID ==========//

				//=========== SAVE NOTIFICATION ================//
				$delivery_type_id		=	$this->request->data[$this->ModelName]['delivery_type_id'];
				$tab_index				=	0;
				
				$this->loadModel("Notification");
					$listKepalGudang	=	$this->User->find("list",array(
												"conditions"	=>	array(
													//"User.aro_id"	=>	4
													"OR"	=>	array(
														array("User.aro_id"		=>	4),
														array("User.id"			=>	$salesID['SalesOrder']['user_id'])
													)
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
					
					if($delivery_type_id == "1")
					{
						$message    	=	"NEW DELIVERY ORDER ".$this->request->data[$this->ModelName]['delivery_no'];
						$description   	=	"PO No. : ".$this->request->data[$this->ModelName]['order_no']."<br/>Delivery No. : ".$this->request->data[$this->ModelName]['delivery_no']."<br/>To : ".$detail[$this->ModelName]["receiver_name"];
						$tab_index		=	0;
					}
					else if($delivery_type_id == "2")
					{
						$message    	=	"NEW PICKUP ORDER ".$this->request->data[$this->ModelName]['delivery_no'];
						$description   	=	"Delivery No. : ".$this->request->data[$this->ModelName]['delivery_no']."<br/>Customer : ".$detail['Customer']['fullname'];
						$tab_index		=	2;
					}
					
					$created		=	date("Y-m-d H:i:s");
					
					//CREATE NOTIFICATION GROUP
					$this->loadModel("NotificationGroup");
					$this->NotificationGroup->create();
					$this->NotificationGroup->saveAll(
						array(
							"created"	=>	$created
						),
						array(
							"validate"	=>	false
						)
					);
					$notificationGroupId	=	$this->NotificationGroup->id;
					
					foreach($listKepalGudang as $idKplGdng =>$gcm_id)
					{
						$this->Notification->create();
						$Notif["Notification"]["user_id"]					=	$idKplGdng;
						$Notif["Notification"]["gcm_id"]					=	empty($gcm_id) ? NULL : $gcm_id;
						$Notif["Notification"]["notification_group_id"] 	=	$notificationGroupId;
						$Notif["Notification"]["order_id"]					=	$ID;
						$Notif["Notification"]["title"]						=	$title;
						$Notif["Notification"]["params"]					=	json_encode(array(
																					array(
																						"key"	=>	"id",
																						"val"	=>	"1"
																					),
																					array(
																						"key"	=>	"task",
																						"val"	=>	"2"
																					),
																					array(
																						"key"	=>	"currentTabIndex",
																						"val"	=>	$tab_index
																					)
																				));
						$Notif["Notification"]["message"]					=	$message;
						$Notif["Notification"]["description"]				=	$description;
						$Notif["Notification"]["android_class_name"]		=	'DashboardKepalaGudang';
						$Notif["Notification"]["created"]					=	$created;
						
						if(!empty($gcm_id))
							$arrGcmId[]								=	$gcm_id;
						$this->Notification->save($Notif,array("validate"=>false));
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
														  ),
														  array(
															  "key"	=>	"currentTabIndex",
															  "val"	=>	$tab_index
														  )
													  );
					$res['data']['created'] 	=	$created;
					$res['data']['notification_group_id'] 	=	$notificationGroupId;
					
					
					$fields = array(
						"registration_ids" 		=>	$arrGcmId,
						"data" 					=>	$res,
						"priority"				=>	"high",
						"time_to_live"			=>	2419200
					);
					$push	=	$this->General->sendPushNotification($fields);
					
					//Configure::write("debug","2");
					//pr($push);
					//pr(json_encode($fields));
				}
				//=========== SAVE NOTIFICATION ================//
				
				/*$this->Session->setFlash(
					'<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button><p>Congratulation data successfully saved!</p>',
					'default',
					array(
						'class' => 'alert alert-success',
					)
				);*/

				if($this->request->data[$this->ModelName]['save_flag'] == "1")
					$this->redirect(array("action"=>"Edit",$ID,1,50,"tab2",$salesPoId));
				else
				{
					$this->Session->setFlash(
						'<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button><p>Congratulation data successfully saved!</p>',
						'default',
						array(
							'class' => 'alert alert-success',
						)
					);
					$this->redirect(array("action"=>"Edit",$ID,1,50,"tab2",$salesPoId));
				}

				/*if($this->request->data[$this->ModelName]['save_flag'] == "1")
					$this->redirect(array("action"=>"Index"));
				else
					$this->redirect(array("action"=>"Add"));*/
					
			}//END IF VALIDATE
			else
			{
				foreach($error as $k => $message)
				{
					$errMessage[]	=	reset($message)."<br/>";
				}
				
				foreach($errorProductId as $k => $v)
				{
					if(!empty($v))
						$errMessage[]	=	$v." (form-".($k+1).") <br/>";
				}
				
				foreach($errorProductQty as $k => $v)
				{
					if(!empty($v))
						$errMessage[]	=	$v." (form-".($k+1).") <br/>";
				}
				
				
				foreach($errorProductNotes as $k => $v)
				{
					if(!empty($v))
						$errMessage[]	=	$v." (form-".($k+1).") <br/>";
				}
				
			}
		}//END IF NOT EMPTY
		
		//DEFINE CUSTOMER ID
		$this->loadModel("User");
		$this->User->VirtualFieldActivated();
		$customer_id_list		=	$this->User->find("list",array(
											"conditions"	=>	array(
												"User.aro_id"	=>	7//CUSTOMERS
											),
											"order"	=>	array(
												"User.firstname ASC"
											),
											"fields"	=>	array(
												"User.id",
												"User.fullname"
											)
										));
										
		//DEFINE PRODUCT ID
		$this->loadModel("Product");
		$product_id_list		=	$this->Product->find("list",array(
											"conditions"	=>	array(
												"Product.status"	=>	1
											),
											"order"	=>	array(
												"Product.name ASC"
											),
											"fields"	=>	array(
												"Product.id",
												"Product.name"
											)
										));
								
		$this->set(compact(
			"errMessage",
			"customer_id_list",
			"product_id_list",
			"errorProductId",
			"errorProductQty",
			"errorProductNotes",
			"salesPoData",
			"salesPoId",
			"POID",
			"DOID"
		));
		
		//$this->render("add_bak");
	}

	function Edit($ID = NULL, $page = 1, $viewpage = 50, $tab_index="tab1", $salesPoId)
	{
		
		$this->loadModel('SalesOrder');


		if ($this->access[$this->aco_id]["_update"] != "1")
		{
			$this->render("/Errors/no_access");
			return;
		}

		if (!empty($salesPoId)) {
			$salesPoData	=	$this->SalesOrder->find('first',array(
										'conditions'	=>	array(
											'SalesOrder.id'	=>	$salesPoId
										)
									)
								);
		} else {
			$salesPoData	=	$this->SalesOrder->find('first',array(
										'conditions'	=>	array(
											'SalesOrder.order_id'	=>	$ID
										)
									)
								);
		}

		$this->{$this->ModelName}->set($this->request->data);
		$this->{$this->ModelName}->BindDefault(false);
		$this->{$this->ModelName}->bindModel(array(
			"belongsTo"	=>	array(
				"DeliveryStatus"	=>	array(
					"className"		=>	"TaskStatus",
					"foreignKey"	=>	"delivery_status"
				),
				"AssemblyStatus"	=>	array(
					"className"		=>	"TaskStatus",
					"foreignKey"	=>	"assembly_status"
				),
				"PickupStatus"	=>	array(
					"className"		=>	"TaskStatus",
					"foreignKey"	=>	"pickup_status"
				)
			),
			"hasOne"	=>	array(
				"Image"			=>	array(
					"className"		=>	"Content",
					"foreignKey"	=>	"model_id",
					"conditions"	=>	array(
						"model"	=>	"Order",
						"type"	=>	"maxwidth"
					)
				)
			),
			"hasMany"	=>	array(
				"OrderProduct"
			)
		),false);
		
		
		$detail		=	$this->{$this->ModelName}->find('first', array(
							'conditions' => array(
								"{$this->ModelName}.id" => $ID
							)
						));
						
						
		if(empty($detail))
		{
		   $this->render("/Errors/error404");
		   return;
		}
		
		//FIND DRIVER
		$this->loadModel("Task");
		$this->Task->bindModel(array(
			"hasOne"	=>	array(
				"Image"			=>	array(
					"className"		=>	"Content",
					"foreignKey"	=>	"model_id",
					"conditions"	=>	array(
						"model"	=>	"Task",
						"type"	=>	"maxwidth"
					)
				)
			)
		),false);
		
		$this->loadModel("TaskAssign");
		$this->TaskAssign->bindModel(array(
			"belongsTo"	=>	array(
				"Driver"	=>	array(
					"className"		=>	"User",
					"foreignKey"	=>	"employee_id"
				)
			)
		),false);
		
		$this->TaskAssign->Driver->virtualFields = array(
			"fullname"		=> "CONCAT(Driver.firstname,' ',Driver.lastname)",
		);
		
		$taskDriver			=	$this->Task->find("first",array(
									"conditions"	=>	array(
										"Task.order_id"			=>	$ID,
										"Task.task_type_id"		=>	"1"
									)
								));
		
		$taskTechnician		=	$this->Task->find("first",array(
									"conditions"	=>	array(
										"Task.order_id"			=>	$ID,
										"Task.task_type_id"		=>	"2"
									)
								));
		
		$driver	=	array();
		if(!empty($taskDriver))
		{
			$driver		=	$this->TaskAssign->find("all",array(
								"conditions"	=>	array(
									"TaskAssign.task_id"	=>	$taskDriver["Task"]["id"]
								),
								"recursive"		=>	"3"
							));
		}
		
		$technician	=	array();
		if(!empty($taskTechnician))
		{			
			$technician		=	$this->TaskAssign->find("all",array(
									"conditions"	=>	array(
										"TaskAssign.task_id"	=>	$taskTechnician["Task"]["id"]
									),
									"recursive"		=>	"3"
								));
		}
		
		$errorProductForm	=	false;
		$errorProductId		=	array();
		$errorProductQty	=	array();
		$errorProductNotes	=	array();
		
		
		if(empty($this->data))
		{
			if(!is_null($detail[$this->ModelName]['delivery_date']))
				$detail[$this->ModelName]['delivery_date']	=	date("d M Y H:i",strtotime($detail[$this->ModelName]['delivery_date']));
			
			if(!is_null($detail[$this->ModelName]['assembly_date']))
				$detail[$this->ModelName]['assembly_date']	=	date("d M Y H:i",strtotime($detail[$this->ModelName]['assembly_date']));
				
			if(!is_null($detail[$this->ModelName]['pickup_date']))
				$detail[$this->ModelName]['pickup_date']	=	date("d M Y H:i",strtotime($detail[$this->ModelName]['pickup_date']));
				
			$this->request->data	=	$detail;
		}
		else
		{
			$this->{$this->ModelName}->set($this->request->data);
			$this->{$this->ModelName}->ValidateData();
			$error	=	$this->{$this->ModelName}->invalidFields();
			
			if(!empty($this->request->data["OrderProduct"]))
			{
				foreach($this->request->data["OrderProduct"] as $k => $v)
				{
					$errorProductId[$k]		=	"";
					$errorProductQty[$k]	=	"";
					$errorProductNotes[$k]	=	"";
					
					if(empty($v["product_id"]))
					{
						$errorProductId[$k]		=	"Please select product";
						$errorProductForm		=	true;
					}
						
					if(intval($v["qty"])<=0)
					{
						$errorProductQty[$k]	=	"Please insert quantity product";
						$errorProductForm		=	true;
					}
				}
			}
			
			
			if(empty($error) && empty($errorProductForm))
			{
				$this->loadModel("OrderProduct");
				
				$this->OrderProduct->deleteAll(array("OrderProduct.order_id" => $ID));
				
				
				$save	=	$this->{$this->ModelName}->saveAll($this->request->data,array("validate"=>false));
				
				
				$this->Session->setFlash(
					'<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button><p>Congratulation data successfully updated!</p>',
					'default',
					array(
						'class' => 'alert alert-success',
					)
				);
				
				if($this->request->data[$this->ModelName]['save_flag'] == "1")
					$this->redirect(array("action"=>"Index"));
				else
					$this->redirect(array("action"=>"Edit",$ID,$page,$viewpage));

			}//END IF VALIDATE
			else
			{
				foreach($error as $k => $message)
				{
					$errMessage[]	= reset($message)."<br/>";
				}
				
				foreach($errorProductId as $k => $v)
				{
					if(!empty($v))
						$errMessage[]	=	$v." (form-".($k+1).") <br/>";
				}
				
				foreach($errorProductQty as $k => $v)
				{
					if(!empty($v))
						$errMessage[]	=	$v." (form-".($k+1).") <br/>";
				}
				
				
				foreach($errorProductNotes as $k => $v)
				{
					if(!empty($v))
						$errMessage[]	=	$v." (form-".($k+1).") <br/>";
				}
			}
		}
		
		//DEFINE CUSTOMER ID
		$this->loadModel("User");
		$this->User->VirtualFieldActivated();
		$customer_id_list		=	$this->User->find("list",array(
											"conditions"	=>	array(
												"User.aro_id"	=>	7//CUSTOMERS
											),
											"order"	=>	array(
												"User.firstname ASC"
											),
											"fields"	=>	array(
												"User.id",
												"User.fullname"
											)
										));
										
		//DEFINE CUSTOMER ID
		$this->loadModel("Product");
		$this->Product->virtualFields = array(
    		'product_label' => 'CONCAT(Product.code, " - ", Product.name)'
		);

		$product_id_list		=	$this->Product->find("list",array(
											"conditions"	=>	array(
												"Product.status"	=>	1
											),
											"order"	=>	array(
												"Product.name ASC"
											),
											"fields"	=>	array(
												"Product.id",
												"Product.code"
											)
										));
							
		$this->set(compact(
			"detail",
			"driver",
			"technician",
			"ID",
			"page",
			"viewpage",
			"tab_index",
			"customer_id_list",
			"product_id_list",
			"taskDriver",
			"taskTechnician",
			"errMessage",
			"errorProductId",
			"errorProductQty",
			"errorProductNotes",
			"salesPoData"
		));
	}

	function ChangeStatus($ID = NULL, $status)
	{
		if ($this->access[$this->aco_id]["_update"] != "1") {
			echo json_encode(array(
				"data" => array(
					"status" => "0",
					"message" => __("No privileges")
				)
			));
			$this->autoRender = false;
			$this->autoLayout = false;
			return;
		}

		$detail = $this->{$this->ModelName}->find('first', array(
			'conditions' => array(
				"{$this->ModelName}.id" => $ID
			)
		));

		$resultStatus = "0";
		if (empty($detail)){
			$message = __("Item not found.");
		} else {
			$data[$this->ModelName]["id"]     	=	$ID;
			$data[$this->ModelName]["status"] 	=	$status;
			$this->{$this->ModelName}->save($data);
			$message      						=	__("Data has updated.");
			$resultStatus 						=	"1";
		}

		echo json_encode(array(
			"data" => array(
				"status"	=>	$resultStatus,
				"message"	=>	$message
			)
		));
		$this->autoRender = false;
	}

	function ChangeStatusMultiple()
	{
		if ($this->access[$this->aco_id]["_update"] != "1") {
			echo json_encode(array(
				"data" => array(
					"status" => "0",
					"message" => __("No privileges")
				)
			));
			$this->autoRender = false;
			$this->autoLayout = false;
			return;
		}

		$ID     =	explode(",", $_REQUEST["id"]);
		$status =	$_REQUEST["status"];

		$this->{$this->ModelName}->updateAll(array(
			"status" => "'" . $status . "'"
		), array(
			"{$this->ModelName}.id" => $ID
		));

		$message = "Data has updated.";
		echo json_encode(array(
			"data" => array(
				"status" => "1",
				"message" => $message
			)
		));
		$this->autoRender = false;
	}

	function Delete($ID = NULL)
	{
		if ($this->access[$this->aco_id]["_delete"] != "1") {
			   echo json_encode(array(
					   "data" => array(
							   "status" => "0",
							   "message" => __("No privileges")
					   )
			   ));
			   $this->autoRender = false;
			   $this->autoLayout = false;
			   return;
		}

		$detail       = $this->{$this->ModelName}->find('first', array(
			   'conditions' => array(
					   "{$this->ModelName}.id" => $ID
			   )
		));
		$resultStatus = "0";

		if (empty($detail)) {
			   $message      = __("Item not found.");
			   $resultStatus = "0";
		} else {
			   $this->{$this->ModelName}->delete($ID, false);
			   $message      = __("Data has deleted.");
			   $resultStatus = "1";
		}

		if ($resultStatus == "1") {
			$this->loadModel("SalesOrder");
			$this->SalesOrder->updateAll(
						array(
							'SalesOrder.order_id'	=>	0,
							'SalesOrder.status'		=>	0
						),
						array(
							'SalesOrder.order_id'	=>	$ID
						)
					);
		}

		echo json_encode(array(
			   "data" => array(
					   "status" => $resultStatus,
					   "message" => $message
			   )
		));
		$this->autoRender = false;
	}

	function DeleteMultiple()
	{
		if ($this->access[$this->aco_id]["_delete"] != "1") {
			echo json_encode(array(
				   "data" => array(
						   "status"  => "0",
						   "message" => __("No privileges")
				   )
			));
			$this->autoRender	=	false;
			$this->autoLayout	=	false;
			return;
		}

		$id = explode(",", $_REQUEST["id"]);
		$this->{$this->ModelName}->deleteAll(array(
			   "id" => $id
		), false, true);
		$message = __("Data has deleted.");

		echo json_encode(array(
			   "data" => array(
					   "status" => "1",
					   "message" => $message
			   )
		));
		$this->autoRender = false;
	}


	function MoveUp($ID = NULL)
	{
		if ($this->access[$this->aco_id]["_update"] != "1") {
			   echo json_encode(array(
					   "data" => array(
							   "status" => "0",
							   "message" => __("No privileges")
					   )
			   ));
			   $this->autoRender = false;
			   $this->autoLayout	=	false;
			   return;
		}

		$detail       = $this->{$this->ModelName}->find('first', array(
			   'conditions' => array(
					   "{$this->ModelName}.id" => $ID
			   )
		));

		$resultStatus = "0";
		if (empty($detail)) {
			   $message      = __("Item not found.");
			   $resultStatus = "0";
		} else {
			   $this->{$this->ModelName}->moveUp($ID,1);
			   $message      = __("Item has successfully move up.");
			   $resultStatus = "1";
		}

		echo json_encode(array(
			   "data" => array(
					   "status" 	=> $resultStatus,
					   "message" 	=> $message
			   )
		));
		$this->autoRender = false;
	}

	function MoveDown($ID = NULL)
	{
		if ($this->access[$this->aco_id]["_update"] != "1") {
			echo json_encode(array(
				   "data" => array(
						   "status"  => "0",
						   "message" => __("No privileges")
				   )
			));
			$this->autoRender = false;
			$this->autoLayout	=	false;
			return;
		}

		$detail       = $this->{$this->ModelName}->find('first', array(
			   'conditions' => array(
					   "{$this->ModelName}.id" => $ID
			   )
		));

		$resultStatus = "0";
		if (empty($detail)) {
			   $message      = __("Item not found.");
			   $resultStatus = "0";
		} else {
			   $this->{$this->ModelName}->moveDown($ID,1);
			   $message      = __("Item has successfully move down.");
			   $resultStatus = "1";
		}

		echo json_encode(array(
			   "data" => array(
					   "status" 	=> $resultStatus,
					   "message" 	=> $message
			   )
		));
		$this->autoRender = false;
	}
	
	function GetDetailCustomer()
	{
		$customerId	=	$_REQUEST['customerId'];
		$this->loadModel("User");
		$this->User->VirtualFieldActivated();
		$detailCustomer	=	$this->User->find("first",array(
								"conditions"	=>	array(
									"User.id"		=>	$customerId,
									"User.aro_id"	=>	7
								),
								"fields"		=>	array(
									"User.*"
								)
							));
		
		$status		=	"0";
		$data		=	array();
		if(!empty($detailCustomer))
		{
			$status	=	"1";
			$data	=	$detailCustomer;
		}
		
		$out	=	array("status"	=>	$status,"data"	=>	$data);
		
		$this->autoRender = false;
		$this->autoLayout = false;
		
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}
	
	function DeleteProduct($order_product_id = NULL)
	{
		if ($this->access[$this->aco_id]["_update"] != "1") {
			   echo json_encode(array(
					   "data" => array(
							   "status" => "0",
							   "message" => __("No privileges")
					   )
			   ));
			   $this->autoRender = false;
			   $this->autoLayout = false;
			   return;
		}

		$this->loadModel('OrderProduct');
		$detail		=	$this->OrderProduct->find('first', array(
						   'conditions' => array(
								"OrderProduct.id" => $order_product_id
						   )
						));

		$resultStatus = "0";

		if (empty($detail)) {
			$message      = __("Item not found.");
			$resultStatus = "0";
		} else {
			$this->OrderProduct->delete($order_product_id, false, true);
			$message      = __("Data has deleted.");
			$resultStatus = "1";
		}

		echo json_encode(array(
			   "data" => array(
					   "status" => $resultStatus,
					   "message" => $message
			   )
		));
		$this->autoRender = false;
	}
	
	function DeleteMultipleProduct()
	{
		if ($this->access[$this->aco_id]["_delete"] != "1") {
			echo json_encode(array(
				   "data" => array(
						   "status" => "0",
						   "message" => __("No privileges")
				   )
			));
			$this->autoRender	=	false;
			$this->autoLayout	=	false;
			return;
		}

		$this->loadModel('OrderProduct');

		$id = explode(",", $_REQUEST["id"]);
		$this->OrderProduct->deleteAll(array(
			   "OrderProduct.id" => $id
		), false, true);
		$message = __("Data has deleted.");

		echo json_encode(array(
			   "data" => array(
					   "status" => "1",
					   "message" => $message
			   )
		));
		$this->autoRender = false;
	}
	
	function ListItemProduct($order_id)
	{
		$this->layout	=	"ajax";
		$this->loadModel("OrderProduct");
		
		$joins			=	array(
								array(
									"table"			=>	"orders",
									"alias"			=>	"Order",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"OrderProduct.order_id	=	Order.id"
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
									"table"			=>	"product_images",
									"alias"			=>	"ProductImage",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"
												ProductImage.product_id	=	Product.id
											AND
												ProductImage.pos	=	0
										"
									)
								),
								array(
									"table"			=>	"contents",
									"alias"			=>	"Thumbnail",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"
												Thumbnail.model_id	=	ProductImage.id
											AND
												Thumbnail.model		=	'ProductImage'
											AND
												Thumbnail.type		=	'square'
										"
									)
								),
								array(
									"table"			=>	"contents",
									"alias"			=>	"MaxWidth",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"
												MaxWidth.model_id	=	ProductImage.id
											AND
												MaxWidth.model		=	'ProductImage'
											AND
												MaxWidth.type		=	'maxwidth'
										"
									)
								)
							);
						
						
		/*$this->OrderProduct->bindModel(array(
			"belongsTo"	=>	array(
				"Product"
			)	
		),false);
		$this->OrderProduct->Product->BindDefault(false);
		$this->OrderProduct->Product->ProductImage->BindImageContent(false);*/
		
		
		$data	=	$this->OrderProduct->find("all",array(
						"conditions"	=>	array(
							"OrderProduct.order_id"	=>	$order_id
						),
						"order"			=>	array(
							"OrderProduct.id"	=>	"desc"
						),
						"recursive"		=>	3,
						"joins"			=>	$joins,
						"fields"		=>	array(
							"Order.id",
							"Order.delivery_status",
							"Order.pickup_status",
							"OrderProduct.id",
							"OrderProduct.qty",
							"OrderProduct.description",
							"Product.*",
							"ProductImage.id",
							"Thumbnail.*",
							"MaxWidth.*"
						)
					));
		pr($data);
		//DETAIL ORDER
		$this->loadModel("Order");
		$detail	=	$this->Order->find("first",array(
						"conditions"	=>	array(
							"Order.id"	=>	$order_id
						)
					));
		$this->set(compact(
			"data",
			"detail"
		));
	}
	
	function AddNewProductForm()
	{
		//prepare for logging
		CakeLog::config('apiLog', array(
			'engine' => 'File'
		));
		$requestLog = "\n===========START===========\n";
		$requestLog .=	time();
		$requestLog .= "===========END===========\n";
		CakeLog::write('apiLog', $requestLog);
		
		$this->autoLayout	=	false;
		$this->autoRender	=	false;
		$status				=	"0";
		$message			=	__("Failed add new product variant!");
		$data				=	array();

		$this->loadModel('OrderProduct');
		
		if(!empty($this->request->data))
		{
			$this->OrderProduct->set($this->request->data);
			$this->OrderProduct->ValidateData();
			
			$error									=	$this->OrderProduct->InvalidFields();
			if(empty($error))
			{
				$status		=	true;
				$message	=	"Data has saved!";
				$this->OrderProduct->create();
				$this->OrderProduct->save($this->request->data,array("validate"=>false));
			}
			else
			{
				$status		=	false;
				foreach($error as $k => $v)
				{
					$message	=	$v[0];
					break;
				}
				$data		=	null;
			}
		}

		$out		=	array(
							"status"	=>	$status,
							"message"	=>	$message,
							"data"		=>	$data
						);

		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
	}

	function Test(){
		$this->autoLayout	=	false;
		$this->autoRender	=	false;
		$this->loadModel($this->ModelName);
		
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
		$mboh = $this->{$this->ModelName}->find('all',
			array(
					"joins"		=>	$joins,
					"group"			=> "Order.id",	
					"fields"			=>	array(
										   		"Order.id",
										   		"Order.order_no",
												"Order.is_assembling",
												"Order.assembly_date",
												"Order.delivery_date",
												"Order.delivery_status",
												"Order.pickup_status",
												"CONCAT(Customer.firstname,' ',Customer.lastname) as fullname",	
												"DeliveryStatus.name",
												"AssemblyStatus.name",
												"Product.name",
												"DeliveryType.name",
												"PickupStatus.name",
												"CONCAT(Technician.firstname,'',Technician.lastname) as Technitions",
												"CONCAT(Driver.firstname,'',Driver.lastname) as Driver"
										   )
			)
		);
		pr($mboh);
	}

	function UpdateQty($ID = null ,$id = null, $qty = null){
		$this->autoRender = false;
		$this->autoLayout = false;

		$this->loadModel('OrderProduct');
		$this->OrderProduct->updateAll(
			array('qty'	=>	$qty),
			array('OrderProduct.id' => $id)
		);

		$this->redirect(array("action"=>"Edit",$ID,1,50));
	}

	public function TestUpdateSales($salesPoId, $order_id)
	{
		$this->autoRender = false;
		$this->autoLayout = false;

		$this->loadModel('SalesOrder');
		$update 	=	$this->SalesOrder->updateAll(
							array(
								'SalesOrder.order_id'	=>	$order_id,
								'SalesOrder.status'		=>	1
							),
							array(
								'SalesOrder.id'			=>	$salesPoId
							)
						);

		if ($update) {
			$data 	=	$this->SalesOrder->find('first',array(
								'conditions' 	=>	array(
									'SalesOrder.id'	=>	$salesPoId
								)
							)
						);
			pr($data);
		}

	}

	public function testExcel()
	{
		if($this->access[$this->aco_id]["_read"] != "1")
		{
			$this->render("/Errors/no_access");
			return;
		}

		$this->autoRender = false;
		$this->autoLayout = false;

		$this->{$this->ModelName}->BindDefault(false);
		// $this->{$this->ModelName}->User->VirtualFieldActivated();
		$this->{$this->ModelName}->bindModel(array(
			"hasMany"	=>	array(
				"OrderHistory"
			)
		),false);

		$order				=	$this->Session->read("Search.".$this->ControllerName."Sort");
		$viewpage			=	$this->Session->read("Search.".$this->ControllerName."Viewpage");
		$page				=	$this->Session->read("Search.".$this->ControllerName."Page");
		$conditions			=	$this->Session->read("Search.".$this->ControllerName."Conditions");

		$this->paginate		=	array(
									"{$this->ModelName}"	=>	array(
										"order"				=>	$order,
										"limit"				=>	$viewpage,
										"conditions"		=>	$conditions,
										"page"				=>	$page
									)
								);
		$data				=	$this->paginate("{$this->ModelName}",$conditions);
		pr($data);
	}

	public function TestAja()
	{
		if($this->access[$this->aco_id]["_read"] != "1")
		{
			$this->render("/Errors/no_access");
			return;
		}

		$this->autoRender = false;
		$this->autoLayout = false;

		$this->loadModel($this->ModelName);
		$this->{$this->ModelName}->BindDefault(false);
		$this->{$this->ModelName}->bindModel(array(
			"hasMany"	=>	array(
				"OrderHistory"
			)
		),false);

		$order		=	$this->Session->read("Search.".$this->ControllerName."Sort");
		$viewpage	=	$this->Session->read("Search.".$this->ControllerName."Viewpage");

		$joins			=	array(
								// array(
								// 	"table"			=>	"delivery_types",
								// 	"alias"			=>	"DeliveryType",
								// 	'type'			 => 'LEFT',
								// 	"conditions"	=>	array(
								// 		"Order.delivery_type_id	=	DeliveryType.id"
								// 	)
								// ),
								// array(
								// 	"table"			=>	"task_statuses",
								// 	"alias"			=>	"DeliveryStatus",
								// 	'type'			 => 'LEFT',
								// 	"conditions"	=>	array(
								// 		"Order.delivery_status	=	DeliveryStatus.id"
								// 	)
								// ),
								// array(
								// 	"table"			=>	"task_statuses",
								// 	"alias"			=>	"AssemblyStatus",
								// 	'type'			 => 'LEFT',
								// 	"conditions"	=>	array(
								// 		"Order.assembly_status	=	AssemblyStatus.id"
								// 	)
								// ),
								// array(
								// 	"table"			=>	"task_statuses",
								// 	"alias"			=>	"PickupStatus",
								// 	'type'			 => 'LEFT',
								// 	"conditions"	=>	array(
								// 		"Order.pickup_status	=	PickupStatus.id"
								// 	)
								// ),
								// array(
								// 	"table"			=>	"order_products",
								// 	"alias"			=>	"OrderProduct",
								// 	'type'			 => 'LEFT',
								// 	"conditions"	=>	array(
								// 		"
								// 				OrderProduct.order_id	=	Order.id
								// 			AND
								// 				OrderProduct.id	=	(SELECT MAX(id) FROM order_products WHERE order_id = Order.id)
								// 		"
								// 	)
								// ),
								// array(
								// 	"table"			=>	"products",
								// 	"alias"			=>	"Product",
								// 	'type'			 => 'LEFT',
								// 	"conditions"	=>	array(
								// 		"OrderProduct.product_id	=	Product.id"
								// 	)
								// ),
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
		$this->paginate  	=	array(
									"{$this->ModelName}" => array(
										   "order" 			=>	$order,
										   'limit' 			=>	$viewpage,
                                           "maxLimit"       =>  1000,
										   "joins"			=>	$joins,
										   "fields"			=>	array(
										   		"Order.id",
										   		"Order.order_no",
												"Order.is_assembling",
												"Order.assembly_date",
												"Order.delivery_date",
												"Order.pickup_date",
												"Order.delivery_status",
												"Order.assembly_status",
												"Order.pickup_status",
												"Order.is_assembling",
												"CONCAT(Customer.firstname,' ',Customer.lastname) as Customer",	
												// "DeliveryStatus.name",
												// "AssemblyStatus.name",
												// "Product.name",
												// "DeliveryType.name",
												// "PickupStatus.name",
												"CONCAT(Driver.firstname,'',Driver.lastname) as Driver",
												"GROUP_CONCAT(CONCAT(Technician.firstname,'',Technician.lastname) SEPARATOR ', ') as Technician"
										   ),
										   "group"	=>	"Order.id"
									)
								);
		$data				=	$this->paginate("{$this->ModelName}");
		pr($data);
	}

}