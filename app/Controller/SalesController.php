<?php
App::uses('CakeNumber', 'Utility');
App::uses('Validation', 'Utility');
class SalesController extends AppController
{
	var $ControllerName		=	"Sales";
	var $ModelName 			=	"SalesOrder";
	var $helpers 			=	array("Text","General");
	var $aco_id;

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


		$this->set(compact(
			"page",
			"viewpage"
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
		$this->{$this->ModelName}->BindDefault(false);
		$this->{$this->ModelName}->VirtualFieldActivated();
		//$this->{$this->ModelName}->User->VirtualFieldActivated();

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

			if (!empty($this->request->data['Search']['vehicle_no'])) {
				   $cond_search["{$this->ModelName}.vehicle_no  LIKE "] = "%".$this->data['Search']['vehicle_no']."%";
			}

			if (!empty($this->request->data['Search']['name'])) {
				   $cond_search["CONCAT(User.firstname,' ',User.lastname) LIKE "] = "%".$this->data['Search']['name']."%";
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
										"conditions"	=>	array("SalesOrder.status" 	=>	0),
										"order" 		=>	$order,
										'limit' 		=>	$viewpage,
                                        "maxLimit"      =>  1000,
										"recursive"		=>	"2"
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
		 $this->{$this->ModelName}->VirtualFieldActivated();

		$order			=	$this->Session->read("Search.".$this->ControllerName."Sort");
		$viewpage		=	$this->Session->read("Search.".$this->ControllerName."Viewpage");
		$page			=	$this->Session->read("Search.".$this->ControllerName."Page");
		$conditions		=	$this->Session->read("Search.".$this->ControllerName."Conditions");

		$this->paginate		=	array(
									"{$this->ModelName}"	=>	array(
										"order"				=>	$order,
										"limit"				=>	$viewpage,
										"conditions"		=>	$conditions,
										"page"				=>	$page
									)
								);

		$data			=	$this->paginate("{$this->ModelName}",$conditions);
		$title			=	$this->ModelName;
		$filename		=	$this->ControllerName."-".date("dMY").".xlsx";
		$this->set(compact("data","title","page","viewpage","filename"));
	}

	function Add()
	{
		if($this->access[$this->aco_id]["_create"] != "1")
		{
			$this->render("/Errors/no_access");
			return;
		}
		
		if(isset($_GET['url']))
		{
			$this->Session->write('back_url',urldecode($_GET['url']));
			$this->redirect(array("action"=>"Add"));
		}
		
		
		$errMessage	=	array();
		if(!empty($this->request->data))
		{
			$this->request->data[$this->ModelName]['aro_id']	=	"7";
			$this->request->data[$this->ModelName]['is_admin']	=	"1";
			
			$this->{$this->ModelName}->set($this->request->data);
			$this->{$this->ModelName}->ValidateDataCustomer();
			$error							=	$this->{$this->ModelName}->invalidFields();
			if(empty($error))
			{
				$save	=	$this->{$this->ModelName}->save($this->request->data,array("validate"=>false));
				$ID		=	$this->{$this->ModelName}->getLastInsertId();
				
				//==================== START SAVE FOTO ====================//
				if(!empty($this->request->data[$this->ModelName]["images"]["name"]))
				{
					$tmp_name							=	$this->request->data[$this->ModelName]["images"]["name"];
					$tmp								=	$this->request->data[$this->ModelName]["images"]["tmp_name"];
					$mime_type							=	$this->request->data[$this->ModelName]["images"]["type"];

					$path_tmp							=	ROOT.DS.'app'.DS.'tmp'.DS.'upload'.DS;
						if(!is_dir($path_tmp)) mkdir($path_tmp,0777);

					$ext								=	pathinfo($tmp_name,PATHINFO_EXTENSION);
					$tmp_file_name						=	md5(time());
					$tmp_images1_img					=	$path_tmp.$tmp_file_name.".".$ext;
					$upload 							=	move_uploaded_file($tmp,$tmp_images1_img);

					if($upload)
					{
						list($width, $height)			=	getimagesize($tmp_images1_img);
						$mime_type						=	"image/jpeg";
						$width							=	$width > 800 ? 800 : $width;
									
						$resize							=	$this->General->ResizeImageContent(
																$tmp_images1_img,
																$this->settings["cms_url"],
																$ID,
																$this->ModelName,
																"square",
																$mime_type,
																300,
																300,
																"cropResize"
															);

						$resize							=	$this->General->ResizeImageContent(
																$tmp_images1_img,
																$this->settings["cms_url"],
																$ID,
																$this->ModelName,
																"maxwidth",
																$mime_type,
																$width,
																$width,
																"resizeMaxWidth"
															);

					}
					@unlink($tmp_images1_img);
				}
				//==================== START SAVE FOTO ====================//
				
				
				if($this->request->data[$this->ModelName]['save_flag'] == "1")
					$this->redirect(array("action"=>"Edit",$ID,1,50,"tab2"));
				else
				{
					$this->Session->setFlash(
						'<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button><p>Congratulation data successfully saved!</p>',
						'default',
						array(
							'class' => 'alert alert-success',
						)
					);
					$this->redirect(array("action"=>"Edit",$ID,1,50));
				}
			}//END IF VALIDATE
			else
			{
				foreach($error as $k => $message)
				{
					$errMessage[]	= reset($message)."<br/>";
				}
			}
		}//END IF NOT EMPTY
		
	  	$this->set(compact(
			"errMessage"
		));
	}

	function Edit($ID = NULL, $page = 1, $viewpage = 50, $tab_index="tab1")
	{
		if ($this->access[$this->aco_id]["_read"] != "1"){
			$this->render("/Errors/no_access");
			return;
		}
		
		$errMessage	=	array();
		$this->{$this->ModelName}->BindDefault(false);
		$this->{$this->ModelName}->BindImageContent(false);
		$detail		=	$this->{$this->ModelName}->find('first', array(
							'conditions' => array(
								"{$this->ModelName}.id" 		=> $ID,
								"{$this->ModelName}.aro_id" 	=> "7"
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
			$detail[$this->ModelName]["password"]	=	$this->General->my_decrypt($detail[$this->ModelName]["password"]);
			$this->data = $detail;
		}
		else
		{
			$this->{$this->ModelName}->set($this->data);
			$this->{$this->ModelName}->ValidateDataCustomer();
			$error							=	$this->{$this->ModelName}->invalidFields();
			
			if(empty($error))
			{
				/*if($this->request->data[$this->ModelName]["status"] == "0")
				{
					$this->request->data[$this->ModelName]["aro_id"]	=	NULL;
				}*/

				$save	=	$this->{$this->ModelName}->save($this->request->data,array("validate"=>false));
				
				//==================== START SAVE FOTO ====================//
				if(!empty($this->request->data[$this->ModelName]["images"]["name"]))
				{
					$tmp_name							=	$this->request->data[$this->ModelName]["images"]["name"];
					$tmp								=	$this->request->data[$this->ModelName]["images"]["tmp_name"];
					$mime_type							=	$this->request->data[$this->ModelName]["images"]["type"];

					$path_tmp							=	ROOT.DS.'app'.DS.'tmp'.DS.'upload'.DS;
						if(!is_dir($path_tmp)) mkdir($path_tmp,0777);

					$ext								=	pathinfo($tmp_name,PATHINFO_EXTENSION);
					$tmp_file_name						=	md5(time());
					$tmp_images1_img					=	$path_tmp.$tmp_file_name.".".$ext;
					$upload 							=	move_uploaded_file($tmp,$tmp_images1_img);

					if($upload)
					{
						list($width, $height)			=	getimagesize($tmp_images1_img);
						$mime_type						=	"image/jpeg";
						$width							=	$width > 800 ? 800 : $width;
									
						$resize							=	$this->General->ResizeImageContent(
																$tmp_images1_img,
																$this->settings["cms_url"],
																$ID,
																$this->ModelName,
																"square",
																$mime_type,
																300,
																300,
																"cropResize"
															);

						$resize							=	$this->General->ResizeImageContent(
																$tmp_images1_img,
																$this->settings["cms_url"],
																$ID,
																$this->ModelName,
																"maxwidth",
																$mime_type,
																$width,
																$width,
																"resizeMaxWidth"
															);

					}
					@unlink($tmp_images1_img);
				}
				//==================== START SAVE FOTO ====================//
				
				
				if($this->request->data[$this->ModelName]['save_flag'] == "1")
					$this->redirect(array("action"=>"Index",1,50));
				else
				{
					$this->Session->setFlash(
						'<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button><p>Congratulation data successfully saved!</p>',
						'default',
						array(
							'class' => 'alert alert-success',
						)
					);
					$this->redirect(array("action"=>"Edit",$ID,1,50));
				}
				
				/*$this->Session->setFlash(
					'<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button><p>Congratulation data successfully updated!</p>',
					'default',
					array(
						'class' => 'alert alert-success',
					)
				);

				if($this->request->data[$this->ModelName]['save_flag'] == "1")
					$this->redirect(array("action"=>"Index"));
				else
					$this->redirect(array("action"=>"Edit",$ID,$page,$viewpage));*/

			}//END IF VALIDATE
			else
			{
				foreach($error as $k => $message)
				{
					$errMessage[]	= reset($message)."<br/>";
				}
			}
		}
		
		$this->set(compact(
			"ID",
			"page",
			"viewpage",
			"detail",
			"errMessage",
			"tab_index"
		));
	}

	

	function ChangeStatus($ID, $status)
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
		$this->loadModel("SalesOrder");
		$detail = $this->SalesOrder->find('first', array(
			'conditions' => array(
				"SalesOrder.id" => $ID
			)
		));

		pr($detail);

		$resultStatus = "0";
		if (empty($detail)){
			$message = __("Item not found.");
		} else {
			$data["SalesOrder"]["id"]     	=	$ID;
			$data["SalesOrder"]["status"] 	=	$status;
			$this->SalesOrder->save($data);
			
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

		$ID     		=	explode(",", $_REQUEST["id"]);
		$status 		=	$_REQUEST["status"];
		$arrUpdate		=	array(
								"status" => "'" . $status . "'"
							);
		
		$this->{$this->ModelName}->updateAll($arrUpdate, array(
			"{$this->ModelName}.id" => $ID
		));

		$message = __("Data has updated.");
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
			   "{$this->ModelName}.id" => $id
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
	
	public function AddNewTargetForm()
	{
		$this->autoLayout	=	false;
		$this->autoRender	=	false;
		$status				=	"0";
		$message			=	__("Failed add new target!");
		$data				=	array();
		
		$this->loadModel('SalesTarget');
		if(!empty($this->request->data))
		{
			$this->SalesTarget->set($this->request->data);
			$this->SalesTarget->ValidateData();
			
			$error									=	$this->SalesTarget->InvalidFields();
			if(empty($error))
			{
				$status		=	true;
				$message	=	"Data has saved!";
				$this->SalesTarget->create();
				$this->SalesTarget->save($this->request->data,array("validate"=>false));
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
	
	function ListItemTarget($sales_id)
	{
		$this->layout	=	"ajax";
		$this->loadModel("SalesTarget");
		$data	=	$this->SalesTarget->find("all",array(
						"conditions"	=>	array(
							"SalesTarget.sales_id"	=>	$sales_id
						),
						"order"			=>	array(
							"SalesTarget.id"	=>	"desc"
						)
					));
		$this->set(compact(
			"data"
		));
	}
	
	function DeleteTarget($sales_target_id = NULL)
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

		$this->loadModel('SalesTarget');
		$detail		=	$this->SalesTarget->find('first', array(
						   'conditions' => array(
								"SalesTarget.id" => $sales_target_id
						   )
						));

		$resultStatus = "0";

		if (empty($detail)) {
			$message      = __("Item not found.");
			$resultStatus = "0";
		} else {
			$this->SalesTarget->delete($sales_target_id, false, true);
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
	
	function DeleteMultipleTarget()
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

		$this->loadModel('SalesTarget');
		$id = explode(",", $_REQUEST["id"]);
		$this->SalesTarget->deleteAll(array(
			   "SalesTarget.id" => $id
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
}
