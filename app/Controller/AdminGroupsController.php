<?php

App::uses('CakeNumber', 'Utility');
App::uses('Validation', 'Utility');

class AdminGroupsController extends AppController
{
	 var $ControllerName	=	"AdminGroups";
	 var $ModelName 		=	"MyAro";
	 var $helpers 			=	array("Text","General");

	function beforeFilter()
	{
		parent::beforeFilter();
		$this->set("ControllerName", $this->ControllerName);
		$this->set("ModelName", $this->ModelName);

		//CHECK PRIVILEGES
		$this->loadModel("MyAco");
		$find         =		$this->MyAco->find("first", array(
								 "conditions" => array(
										 "LOWER(MyAco.controller)" => $this->ControllerName
								 )
							));

		$this->aco_id = $find["MyAco"]["id"];
		$this->set("aco_id", $this->aco_id);
		$this->loadModel($this->ModelName);
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

	function ListItem($excel = "false")
	{
		$this->layout		=	"ajax";
		$fullScreenMode		=	$this->params['named']['fullScreenMode'];

		if ($this->access[$this->aco_id]["_read"] != "1") {
			   $data = array();
			   $this->set(compact("data"));
			   return;
		}

		//GET CHILDREN
		$this->loadModel("MyAro");
		$children	=	$this->MyAro->children($this->profile['MyAro']['id'],false);
		$child		=	array();
		foreach($children as $children)
		{
			$child[]		=	$children['MyAro']["id"];
		}


		$this->loadModel($this->ModelName);
		$this->{$this->ModelName}->BindDefault(false);
		$this->{$this->ModelName}->VirtualFieldActivated();

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
							   "{$this->ModelName}.lft" => "ASC"
							);
			$this->Session->write('Search.' . $this->ControllerName . 'Viewpage', $viewpage);
			$this->Session->write('Search.' . $this->ControllerName . 'Sort', (empty($this->params['named']['sort']) or !isset($this->params['named']['sort'])) ? $order : $this->params['named']['sort'] . " " . $this->params['named']['direction']);
		}

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
		$filter_paginate 	=	array();

		if($this->login_id != $this->super_admin_id)
		{
			$filter_paginate 	=	array("{$this->ModelName}.parent_id IS NOT NULL");
		}

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
										   "recursive"		=>	2,
										   "page"			=>	$page,
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
			'fullScreenMode',
			'child'
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

		$groupName					=	"";
		$superAdminAro				=	$this->{$this->ModelName}->find("first",array(
											"conditions"	=>	array(
												"MyAro.parent_id"	=>	NULL
											)
										));

		$premiumAdminAro			=	$this->{$this->ModelName}->find("first",array(
											"conditions"	=>	array(
												"{$this->ModelName}.parent_id"	=>	$superAdminAro[$this->ModelName]["id"]
											)
										));

		if(empty($premiumAdminAro))
		{
			$groupName				=	$superAdminAro[$this->ModelName]["alias"];
		}
		else
		{
			$groupName				=	$premiumAdminAro[$this->ModelName]["alias"];
		}

		if(!empty($this->request->data))
		{
			$this->{$this->ModelName}->set($this->request->data);
			$this->{$this->ModelName}->ValidateData();
			if($this->{$this->ModelName}->validates())
			{
				/*
				* Jika Aro ID nya adalah premium admin
				* Maka parent ID nya di set menjadi premium admin
				* semua aro yang di tambahkan oleh premium admin
				* harus di menjadi child dari premium admin
				*/

				if(empty($premiumAdminAro))
				{
					$this->request->data[$this->ModelName]["parent_id"]		=	$superAdminAro[$this->ModelName]["id"];
				}
				else
				{
					$this->request->data[$this->ModelName]["parent_id"]		=	$premiumAdminAro[$this->ModelName]["id"];
				}

				$save	=	$this->{$this->ModelName}->save($this->request->data,array("validate"=>false));
				$ID		=	$this->{$this->ModelName}->getLastInsertId();

				$this->Session->setFlash(
					'<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button><p>Congratulation data successfully saved, please set privileges for this group!</p>',
					'default',
					array(
						'class' => 'alert alert-success',
					)
				);
				$this->redirect(array("action"=>"SettingPrivileges",$ID,1,50));

			}//END IF VALIDATE
		}//END IF NOT EMPTY

		/*//DEFINE PARENT MENU
		$parent_id_list	=	$this->{$this->ModelName}->generateTreeList(
			null,
			"{n}.{$this->ModelName}.id",
			"{n}.{$this->ModelName}.alias",
			"-- "
		);

		//DISABLED
		$disabledParent				=	array();

		$superAdminAro				=	$this->{$this->ModelName}->find("first",array(
											"conditions"	=>	array(
												"MyAro.parent_id"	=>	NULL
											)
										));

		$premiumAdminAro			=	$this->{$this->ModelName}->find("first",array(
											"conditions"	=>	array(
												"MyAro.parent_id"	=>	$superAdminAro[$this->ModelName]["id"]
											)
										));

		if(!empty($premiumAdminAro))
		{
			$disabledParent[]			=	$superAdminAro[$this->ModelName]["id"];
			$disabledParent				=	array(
											$superAdminAro[$this->ModelName]["id"],
											$premiumAdminAro["MyAro"]["id"]
										);
		}*/




	  	$this->set(compact(
			"parent_id_list",
			"disabledParent",
			"groupName",
			"superAdminAro",
			"premiumAdminAro"
		));
	}

	function Edit($ID = NULL, $page = 1, $viewpage = 50)
	{
		if ($this->access[$this->aco_id]["_update"] != "1")
		{
			$this->render("/Errors/no_access");
			return;
		}

		$this->{$this->ModelName}->BindDefault();
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
					'<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button><p>Congratulation, data successfully updated!</p>',
					'default',
					array(
						'class' => 'alert alert-success',
					)
				);
				$this->redirect(array("action"=>"Edit",$ID,$page,$viewpage));
			}//END IF VALIDATE
		}



		$superAdminAro				=	$this->{$this->ModelName}->find("first",array(
											"conditions"	=>	array(
												"MyAro.parent_id"	=>	NULL
											)
										));

		$premiumAdminAro			=	$this->{$this->ModelName}->find("first",array(
											"conditions"	=>	array(
												"{$this->ModelName}.parent_id"	=>	$superAdminAro[$this->ModelName]["id"]
											)
										));

		//DISABLED
		/*$disabledParent				=	array($ID);
		$hideFromId					=	array();
		$superAdminAro				=	$this->{$this->ModelName}->find("first",array(
											"conditions"	=>	array(
												"MyAro.parent_id"	=>	NULL
											)
										));
		$hideFromId[]				=	$superAdminAro[$this->ModelName]["id"];
		$premiumAdminAro			=	$this->{$this->ModelName}->find("first",array(
											"conditions"	=>	array(
												"{$this->ModelName}.parent_id"	=>	$superAdminAro[$this->ModelName]["id"]
											)
										));

		if(!empty($premiumAdminAro))
		{
			$hideFromId[]		=	$premiumAdminAro["MyAro"]["id"];
			if($detail[$this->ModelName]["parent_id"] == $premiumAdminAro["MyAro"]["id"])
			{
				$disabledParent[]	=	$superAdminAro["MyAro"]["id"];
			}
		}

		//DEFINE PARENT MENU
		$parent_id_list	=	$this->{$this->ModelName}->generateTreeList(
								null,
								"{n}.{$this->ModelName}.id",
								"{n}.{$this->ModelName}.alias",
								"-- "
							);*/
		$this->set(compact(
			"ID",
			"page",
			"viewpage",
			"detail",
			"superAdminAro",
			"premiumAdminAro"
		));
	}

	public function SettingPrivileges($ID=NULL,$page=1,$viewpage=50)
	{
		if ($this->access[$this->aco_id]["_update"] != "1")
		{
			$this->render("/Errors/no_access");
			return;
		}

		$this->loadModel("MyAco");
		$this->loadModel("MyAro");
		$this->loadModel("AroAco");

		//GET DETAIL ARO
		$detailAro	=	$this->MyAro->find("first",array(
							"conditions"	=>	array(
								"MyAro.id"	=>	$ID
							)
						));
		if(empty($detailAro))
		{
			$this->render("/Errors/error404");
			return;
		}

		//GET DETAIL PARENT
		$top				=	$this->MyAco->find("first",array(
										"conditions"	=>	array(
											"MyAco.parent_id"	=>	NULL
										)
									));
		$lft				=	$top["MyAco"]["lft"];
		$rght				=	$top["MyAco"]["rght"];

		//DEFINE NEWS ACO
		$this->loadModel("MyAro");
		$superAdminAro		=	$this->MyAro->find("first",array(
									"conditions"	=>	array(
										"MyAro.parent_id"	=>	NULL
									)
								));

		$conditions			=	array("MyAco.lft > "=>"$lft","MyAco.rght < "=>"$rght");
		if($this->profile["User"]["aro_id"]!=$superAdminAro["MyAro"]["id"])
		{
			$conditions["MyAco.acos_type_id"]	=	2;
		}
		$data				=	$this->MyAco->generateTreeList2($conditions,"{n}.MyAco.id","{n}.MyAco.alias","&raquo; ");

		if(!empty($this->request->data))
		{
			$post	=	$this->request->data["AroAco"];
			$this->AroAco->deleteAll(array(
				"AroAco.aro_id"	=>	$ID
			));

			foreach($post as $k => $v)
			{
				$AroAco["AroAco"]["aro_id"]		=	$ID;
				$AroAco["AroAco"]["aco_id"]		=	$k;
				$AroAco["AroAco"]["_create"]	=	$v["_create"];
				$AroAco["AroAco"]["_read"]		=	$v["_read"];
				$AroAco["AroAco"]["_update"]	=	$v["_update"];
				$AroAco["AroAco"]["_delete"]	=	$v["_delete"];
				$this->AroAco->create();
				$this->AroAco->save($AroAco);
			}

			$this->Session->setFlash(
				'<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button><p>Congratulation, data successfully saved!</p>',
				'default',
				array(
					'class' => 'alert alert-success',
				)
			);
			$this->redirect(array("action"=>"SettingPrivileges",$ID,$page,$viewpage));
		}
		else
		{
			$find	=	$this->AroAco->find("all",array(
							"conditions"	=>	array(
								"AroAco.aro_id"	=>	$ID
							)
						));
			foreach($find as $find)
			{
				$this->request->data["AroAco"][$find["AroAco"]["aco_id"]]["_read"]		=	$find["AroAco"]["_read"];
				$this->request->data["AroAco"][$find["AroAco"]["aco_id"]]["_create"]	=	$find["AroAco"]["_create"];
				$this->request->data["AroAco"][$find["AroAco"]["aco_id"]]["_update"]	=	$find["AroAco"]["_update"];
				$this->request->data["AroAco"][$find["AroAco"]["aco_id"]]["_delete"]	=	$find["AroAco"]["_delete"];
			}
		}

		$this->set(compact("ID","data","top","page","viewpage","detailAro"));
	}

	/*function ChangeStatus($ID = NULL, $status)
	{
		if ($this->access[$this->aco_id]["_update"] != "1") {
			echo json_encode(array(
				"data" => array(
					"status" => "0",
					"message" => "No privileges"
				)
			));
			$this->autoRender = false;
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
	}*/

	/*function ChangeStatusMultiple()
	{
		if ($this->access[$this->aco_id]["_update"] != "1") {
			echo json_encode(array(
				"data" => array(
					"status" => "0",
					"message" => "No privileges"
				)
			));
			$this->autoRender = false;
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
	}*/


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

		echo json_encode(array(
			   "data" => array(
					   "status" => $resultStatus,
					   "message" => $message
			   )
		));
		$this->autoRender = false;
	}

	/*function DeleteMultiple()
	{
		if ($this->access[$this->aco_id]["_delete"] != "1") {
			echo json_encode(array(
				   "data" => array(
						   "status" => "0",
						   "message" => "No privileges"
				   )
			));
			$this->autoRender = false;
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
	}*/
}
?>
