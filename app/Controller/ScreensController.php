<?php
App::uses('CakeNumber', 'Utility');
App::uses('Validation', 'Utility');
class ScreensController extends AppController
{
	var $ControllerName		=	"Screens";
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
												"CONCAT(Customer.firstname,' ',Customer.lastname) as fullname",	
												"DeliveryStatus.name",
												"AssemblyStatus.name",
												"Product.name",
												"DeliveryType.name",
												"PickupStatus.name"
										   )
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

	function Excel()
	{
		if($this->access[$this->aco_id]["_read"] != "1")
		{
			$this->render("/Errors/no_access");
			return;
		}

		$this->layout		=	"ajax";
		$this->{$this->ModelName}->BindDefault(false);
		$this->{$this->ModelName}->User->VirtualFieldActivated();

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
		$title			=	$this->ModelName;
		$filename			=	$this->ControllerName."-".date("dMY").".xlsx";
		$this->set(compact("data","title","page","viewpage","filename"));
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
}
?>