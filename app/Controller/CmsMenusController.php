<?php

App::uses('CakeNumber', 'Utility');
App::uses('Validation', 'Utility');

class CmsMenusController extends AppController
{
	var $ControllerName		=	"CmsMenus";
	var $ModelName 			=	"CmsMenu";
	var $helpers 			=	array("Text","General");
	var $aco_id;

	function beforeFilter()
	{
		parent::beforeFilter();
		$this->set("ControllerName", $this->ControllerName);
		$this->set("ModelName", $this->ModelName);
		$this->loadModel($this->ModelName);
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

    /*function Test()
    {
        $this->loadModel("Lang");
        $find           =   $this->{$this->ModelName}->find("all",array(
                                "order"    =>  array(
                                    "{$this->ModelName}.id ASC"
                                )
                            ));
        $listLang       =   $this->Lang->find("all");
        pr($listLang);
        foreach($find as $find2)
        {
            foreach($listLang as $listLang2)
            {
                $query  =   "
                    INSERT INTO cms_menu_translations(locale,model,foreign_key,field,content) VALUES('".$listLang2['Lang']['code']."','CmsMenu','".$find2[$this->ModelName]['id']."','name','".$find2[$this->ModelName]['name']."');
                ";
                echo $query."<br/>";
            }
        }
    }*/

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
		$this->{$this->ModelName}->reorder(array(
			'id' => 1,
			'field' => "lft",
			'order' => "ASC",
			'verify' => true
		));

		//DEFINE PARENT MENU
		$parent_id_list	=	$this->{$this->ModelName}->generateTreeList(
			array("{$this->ModelName}.parent_id IS NOT NULL"),
			"{n}.{$this->ModelName}.id",
			"{n}.{$this->ModelName}.name",
			"-- "
		);

	  	//DEFINE ACO LIST
		$listAcoId				=	array();
		foreach($this->access as $aco_id=>$access)
		{
			if($access["_read"]=="1")
				$listAcoId[]	=	$aco_id;
		}
		$aco_id_list	=	$this->MyAco->find("list",array(
								"conditions"	=>	array(
									"MyAco.id"	=>	$listAcoId
								),
								"order"			=>	"MyAco.lft ASC",
								"fields"		=>	array(
									"alias"
								)
							));


		$this->set(compact(
			"page",
			"viewpage",
			"parent_id_list",
			"aco_id_list"
		));
		//$this->set('current_active_menu',array(9,11));
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
				   $cond_search["{$this->ModelName}.name LIKE "] = "%".$this->data['Search']['name']."%";
			}

			if (!empty($this->request->data['Search']['aco_id'])) {
				   $cond_search["{$this->ModelName}.aco_id"] 	= $this->data['Search']['aco_id'];
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

		//FILTER CMS MENU BY ACOS
		$listAcoId				=	array();
		foreach($this->access as $aco_id=>$access)
		{
			if($access["_read"]=="1")
				$listAcoId[]	=	$aco_id;
		}
		$filter_paginate["OR"]	=	array(
										"CmsMenu.aco_id"				=>	$listAcoId,
										"CmsMenu.is_group_separator"	=>	1
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


		$this->set(compact(
			'data',
			'page',
			'viewpage',
			'fullScreenMode'
		));
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
                foreach(array_keys($this->request->data[$this->ModelName]) as $keyName)
				{
					if(in_array($keyName,array("name")))
					{
						foreach($this->langList as $lang)
						{
							$request[$this->ModelName][$keyName][$lang]	=	$this->request->data[$this->ModelName][$keyName];
						}
					}
					else
					{
						$request[$this->ModelName][$keyName]			=	$this->request->data[$this->ModelName][$keyName];
					}
				}

                $save	=	$this->{$this->ModelName}->save($request,array("validate"=>false));
				$ID		=	$this->{$this->ModelName}->getLastInsertId();

				$this->Session->setFlash(
					'<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button><p>Congratulation data successfully saved!</p>',
					'default',
					array(
						'class' => 'alert alert-success',
					)
				);

				if($this->request->data[$this->ModelName]['save_flag'] == "1")
					$this->redirect(array("action"=>"Index"));
				else
					$this->redirect(array("action"=>"Add"));

			}//END IF VALIDATE
		}//END IF NOT EMPTY

		//DEFINE PARENT MENU
		$parent_id_list	=	$this->{$this->ModelName}->generateTreeList(
			null,
			"{n}.{$this->ModelName}.id",
			"{n}.{$this->ModelName}.name",
			"-- "
		);

		//DEFINE ACO LIST
		$this->loadModel("MyAro");
		$superAdminAro		=	$this->MyAro->find("first",array(
									"conditions"	=>	array(
										"MyAro.parent_id"	=>	NULL
									)
								));

		$conditions			=	array(
									"NOT"	=>	array(
										"MyAco.parent_id"	=>	NULL
									)
								);

		if($this->profile["User"]["aro_id"] != $superAdminAro["MyAro"]["id"])
		{
			$conditions["MyAco.acos_type_id"]	=	2;
		}

		$this->loadModel("MyAco");
		$aco_id_list	=	$this->MyAco->find("list",array(
								"conditions"	=>	$conditions,
								"fields"	=>	array(
									"alias"
								)
							));
	  	$this->set(compact(
			"parent_id_list",
			"aco_id_list"
		));
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

				if($this->request->data[$this->ModelName]['save_flag'] == "1")
					$this->redirect(array("action"=>"Index"));
				else
					$this->redirect(array("action"=>"Edit",$ID,$page,$viewpage));

			}//END IF VALIDATE
		}


		//DEFINE PARENT MENU
		$parent_id_list	=	$this->{$this->ModelName}->generateTreeList(
								array(
									"NOT"	=>	array(
										"{$this->ModelName}.id"	=>	$ID
									)
								),
								"{n}.{$this->ModelName}.id",
								"{n}.{$this->ModelName}.name",
								"-- "
							);

		//DEFINE PARENT MENU
		$this->loadModel("MyAco");
		$aco_id_list	=	$this->MyAco->find("list",array(
								"conditions"	=>	array(
									"NOT"	=>	array(
										"MyAco.parent_id"	=>	NULL
									)
								),
								"fields"	=>	array(
									"alias"
								)
							));
		$this->set(compact(
			"aco_id_list",
			"parent_id_list",
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
			$message = "Item not found.";
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
		if (empty($detail))
		{
			$message      = __("Item not found.");
			$resultStatus = "0";
		}
		else
		{
			//$this->{$this->ModelName}->reorder();
			$moveUp			=	$this->{$this->ModelName}->moveUp($ID,1);
			$message		=	__("Item has successfully move up.");
			$resultStatus 	=	"1";
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


	function ChangePosition()
	{
		//$this->{$this->ModelName}->moveUp(3,1);
	}
}
