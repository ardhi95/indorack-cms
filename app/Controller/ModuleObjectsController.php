<?php

App::uses('CakeNumber', 'Utility');
App::uses('Validation', 'Utility');

class ModuleObjectsController extends AppController
{
	 var $ControllerName	=	"ModuleObjects";
	 var $ModelName 		=	"MyAco";
	 var $helpers 			=	array("Text","General");

	function beforeFilter()
	{
		parent::beforeFilter();
		$this->set("ControllerName", $this->ControllerName);
		$this->set("ModelName", $this->ModelName);
		$this->loadModel($this->ModelName);

		//CHECK PRIVILEGES
		$this->loadModel("MyAco");
		$find         =		$this->MyAco->find("first", array(
								 "conditions" => array(
										 "LOWER(MyAco.controller)" => $this->ControllerName
								 )
							));

		$this->aco_id = $find["MyAco"]["id"];
		$this->set("aco_id", $this->aco_id);

		//REFRESH CONTROLLER CACHE LIST
		$deleteCache	=	Cache::delete('object_map', '_cake_core_');
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

		//DEFINE PARENT MENU
		$parent_id_list	=	$this->{$this->ModelName}->generateTreeList(
			array("{$this->ModelName}.parent_id IS NOT NULL"),
			"{n}.{$this->ModelName}.id",
			"{n}.{$this->ModelName}.alias",
			"-- "
		);

		$this->set(compact(
			"page",
			"viewpage",
			"parent_id_list"
		));
	}

	function ListItem()
	{
		$this->layout		=	"ajax";
		$fullScreenMode		=	$this->params['named']['fullScreenMode'];

		if ($this->access[$this->aco_id]["_read"] != "1") {
			   $data = array();
			   $this->set(compact("data"));
			   return;
		}

		$this->loadModel($this->ModelName);
		$this->{$this->ModelName}->BindDefault(false);
		$this->{$this->ModelName}->VirtualFieldActivated();

		//DEFINE LAYOUT, LIMIT AND OPERAND
		$viewpage = empty($this->params['named']['limit']) ? 50 : $this->params['named']['limit'];
		$order    = array(
			   "{$this->ModelName}.lft" => "ASC"
		);
		$operand  = "AND";

		//DEFINE SEARCH DATA
		if (!empty($this->request->data)) {
			$cond_search = array();
			$operand     = $this->request->data[$this->ModelName]['operator'];
			$this->Session->delete('Search.' . $this->ControllerName);

			if (!empty($this->request->data['Search']['id'])) {
				   $cond_search["{$this->ModelName}.id"] = $this->data['Search']['id'];
			}

			if (!empty($this->request->data['Search']['name'])) {
				   $cond_search["{$this->ModelName}.alias LIKE "] = "%".$this->data['Search']['name']."%";
			}

			if (!empty($this->request->data['Search']['parent_id'])) {
				   $cond_search["{$this->ModelName}.parent_id"] = $this->data['Search']['parent_id'];
			}

			if (!empty($this->data['Search']['start_date']) && empty($this->data['Search']['end_date'])) {
				   $cond_search["{$this->ModelName}.created >= "] = $this->data['Search']['start_date'] . " 00:00:00";
			}

			if (empty($this->data['Search']['start_date']) && !empty($this->data['Search']['end_date'])) {
				   $cond_search["{$this->ModelName}.created <= "] = $this->data['Search']['end_date'] . " 23:59:59";
			}

			if (!empty($this->data['Search']['start_date']) && !empty($this->data['Search']['end_date'])) {
				   $tmp                                                            = $this->data['Search']['start_date'];
				   $START                                                          = (strtotime($this->data['Search']['end_date']) < strtotime($this->data['Search']['start_date'])) ? $this->data['Search']['end_date'] : $this->data['Search']['start_date'];
				   $END                                                            = ($this->data['Search']['end_date'] < $tmp) ? $tmp : $this->data['Search']['end_date'];
				   $cond_search["{$this->ModelName}.created BETWEEN ? AND ? "] = array(
						   $START . " 00:00:00",
						   $END . " 23:59:59"
				   );
			}

			if ($this->request->data["Search"]['reset'] == "0") {
				   $this->Session->write("Search." . $this->ControllerName, $cond_search);
				   $this->Session->write('Search.' . $this->ControllerName . 'Operand', $operand);
			}
		}

		$this->Session->write('Search.' . $this->ControllerName . 'Viewpage', $viewpage);
		$this->Session->write('Search.' . $this->ControllerName . 'Sort', (empty($this->params['named']['sort']) or !isset($this->params['named']['sort'])) ? $order : $this->params['named']['sort'] . " " . $this->params['named']['direction']);

		$cond_search     	=	array();
		$filter_paginate 	=	array(
									"{$this->ModelName}.parent_id IS NOT NULL"
								);

		$this->paginate  	=	array(
									"{$this->ModelName}" => array(
										   "order" 			=>	$order,
										   'limit' 			=>	$viewpage,
										   "recursive"		=>	2,
                                           "maxLimit"       =>  1000
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
			pr($data);
		}
		catch (NotFoundException $e) {
			$count 				= $this->{$this->ModelName}->find('count',array("conditions"=>$merge_cond));
			$pageCount 			= intval(ceil($count / $viewpage));
			$this->request->params['named']['page'] = $pageCount;
			$this->{$this->ModelName}->BindDefault(false);
			$data        		= $this->paginate("{$this->ModelName}", $merge_cond);
    	}
		$this->Session->write('Search.' . $this->ControllerName . 'Conditions', $merge_cond);

		if (
			isset($this->params['named']['page']) &&
			$this->params['named']['page'] >
			$this->params['paging'][$this->ModelName]['pageCount']
		)
		{
			   $this->params['named']['page'] = $this->params['paging'][$this->ModelName]['pageCount'];
		}
		$page = empty($this->params['named']['page']) ? 1 : $this->params['named']['page'];

		$this->Session->write('Search.' . $this->ControllerName . 'Page', $page);
		$this->set(compact('data', 'page', 'viewpage','fullScreenMode'));
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
		 $this->{$this->ModelName}->VirtualFieldActivated();

		$order			=	$this->Session->read("Search.".$this->ControllerName."Sort");
		$viewpage			=	$this->Session->read("Search.".$this->ControllerName."Viewpage");
		$page				=	$this->Session->read("Search.".$this->ControllerName."Page");
		$conditions		=	$this->Session->read("Search.".$this->ControllerName."Conditions");

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

	function Add()
	{
		if($this->access[$this->aco_id]["_create"] != "1")
		{
			$this->render("/Errors/no_access");
			return;
		}

		if(!empty($this->request->data))
		{
			$this->{$this->ModelName}->set($this->request->data);
			$this->{$this->ModelName}->ValidateData();
			if($this->{$this->ModelName}->validates())
			{
				$save	=	$this->{$this->ModelName}->save($this->request->data,array("validate"=>false));
				$ID		=	$this->{$this->ModelName}->getLastInsertId();

				$this->Session->setFlash(
					'<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button><p>Congratulation data successfully saved!</p>',
					'default',
					array(
						'class' => 'alert alert-success',
					),
					"success"
				);
				$this->redirect(array("action"=>"Add"));
			}//END IF VALIDATE
		}//END IF NOT EMPTY


		//DEFINE CONTROLLER NAMES
		$this->loadModel("RestrictedController");
		$restrictedCosntroller	=	$this->RestrictedController->find("list");
		$alreadyUsedController	=	$this->{$this->ModelName}->find("list",array(
										"fields"	=>	array(
											"controller"
										),
										"conditions"	=>	array(
											"{$this->ModelName}.parent_id IS NOT NULL"
										)
									));

		$usedControllerList	=	array();
		foreach($alreadyUsedController as $alreadyUsedController)
		{
			$usedControllerList[]	=	$alreadyUsedController."Controller";
		}


		$ListControllers 	=	App::objects('controller');
		$controllerList		=	array();

		foreach($ListControllers as $ListControllers)
		{
			if(!in_array($ListControllers,array_merge($restrictedCosntroller,$usedControllerList)))
				$controllerList[str_replace("Controller","",$ListControllers)]	=	$ListControllers;
		}
		$this->set(compact("controllerList"));
	}

	function Edit($ID = NULL, $page = 1, $viewpage = 50)
	{
		if ($this->access[$this->aco_id]["_update"] != "1")
		{
			$this->render("/Errors/no_access");
			return;
		}

		$detail		=	$this->{$this->ModelName}->find('first', array(
							'conditions' => array(
									 "{$this->ModelName}.id" => $ID
							),
							"recursive"	=>	3
						));


		if (empty($detail))
		{
		   $this->render("/Errors/error404");
		   return;
		}

		if (empty($this->data))
		{
			$this->data = $detail;
		}
		else
		{
			$this->{$this->ModelName}->set($this->data);
			$this->{$this->ModelName}->ValidateData();
			if($this->{$this->ModelName}->validates())
			{
				$save	=	$this->{$this->ModelName}->save($this->request->data,array("validate"=>false));
				$this->Session->setFlash(
					'<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button><p>Congratulation data successfully updated!</p>',
					'default',
					array(
						'class' => 'alert alert-success',
					)
				);
				$this->redirect(array("action"=>"Edit",$ID,$page,$viewpage));
			}//END IF VALIDATE
		}

		$this->set(compact(
			"ID",
			"page",
			"viewpage"
		));
	}

	function ChangeStatus($ID = NULL, $status)
	{
		if ($this->access[$this->aco_id]["_update"] != "1") {
			echo json_encode(array(
				"data" => array(
					"status" => "0",
					"message" => "No privileges"
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
			$message = "Item not found.";
		} else {
			$data[$this->ModelName]["id"]     	=	$ID;
			$data[$this->ModelName]["status"] 	=	$status;
			$this->{$this->ModelName}->save($data);
			$message      						=	"Data has updated.";
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
					"message" => "No privileges"
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
							   "message" => "No privileges"
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
			   $message      = "Item not found.";
			   $resultStatus = "0";
		} else {
			   $this->{$this->ModelName}->delete($ID, false);
			   $message      = "Data has deleted.";
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

	function DeleteMultiple()
	{
		if ($this->access[$this->aco_id]["_delete"] != "1") {
			echo json_encode(array(
				   "data" => array(
						   "status" => "0",
						   "message" => "No privileges"
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
		$message = "Data has deleted.";

		echo json_encode(array(
			   "data" => array(
					   "status" => "1",
					   "message" => $message
			   )
		));
		$this->autoRender = false;
	}


}
?>
