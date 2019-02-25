<?php

App::uses('CakeNumber', 'Utility');
App::uses('Validation', 'Utility');

class ProductsController extends AppController
{
	var $ControllerName		=	"Products";
	var $ModelName 			=	"Product";
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
		$this->{$this->ModelName}->ProductImage->BindImageContent(false);
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

			if (!empty($this->request->data['Search']['code'])) {
				   $cond_search["{$this->ModelName}.code"] = $this->data['Search']['code'];
			}

			if (!empty($this->request->data['Search']['name'])) {
				   $cond_search["{$this->ModelName}.name LIKE "] = "%".$this->data['Search']['name']."%";
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
										   "order" 			=>	$order,
										   'limit' 			=>	$viewpage,
                                           "maxLimit"       =>  1000,
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
			$this->{$this->ModelName}->BindDefault(false);
			
			if($this->{$this->ModelName}->validates())
			{
				
				$save	=	$this->{$this->ModelName}->saveAll($this->request->data,array("validate"=>false));
				$ID		=	$this->{$this->ModelName}->getLastInsertId();
				
				//==================== START SAVE PDF ====================//
				if(!empty($this->request->data[$this->ModelName]["pdf"]["name"]))
				{
					$uploadPdf	=	$this->General->UploadContent(
						$ID,
						"Product",
						"pdf",
						$this->request->data[$this->ModelName]["pdf"]
					);
					
					if($uploadPdf)
					{
						$this->{$this->ModelName}->updateAll(
							array(
								"pdf_download"			=>	"1"
							),
							array(
								"{$this->ModelName}.id"	=>	$ID
							)
						);
					}
				}
				//==================== START SAVE PDF ====================//
				
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
						$mime_type						=	mime_content_type($tmp_images1_img);
						$width							=	$width > 800 ? 800 : $width;
						
						$this->loadModel("ProductImage");
						$this->ProductImage->create();
						$this->ProductImage->saveAll(
							array(
								"product_id"	=>	$ID,
								"pos"			=>	0,
								"created"		=>	date("Y-m-d H:i:s")
							),
							array(
								"validate"		=>	false
							)
						);
						
						$ProductImageId					=	$this->ProductImage->id;
						$resize							=	$this->General->ResizeImageContent(
																$tmp_images1_img,
																$this->settings["cms_url"],
																$ProductImageId,
																"ProductImage",
																"square",
																$mime_type,
																$this->settings["product_width"],
																$this->settings["product_height"],
																"cropResize"
															);

						$resize							=	$this->General->ResizeImageContent(
																$tmp_images1_img,
																$this->settings["cms_url"],
																$ProductImageId,
																"ProductImage",
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
		
		//LIST CATEGORY
		$this->loadModel("ProductSubCategory");
		$category_id_list		=	$this->ProductSubCategory->find("list",array(
										"order"	=>	"ProductSubCategory.name"
									));
									
									
		$this->set(compact(
			"category_id_list"
		));
	}

	function Edit($ID = NULL, $page = 1, $viewpage = 50)
	{
		if ($this->access[$this->aco_id]["_update"] != "1")
		{
			$this->render("/Errors/no_access");
			return;
		}

		$this->{$this->ModelName}->set($this->request->data);
		$this->{$this->ModelName}->BindDefault(false);
		$this->{$this->ModelName}->ProductImage->BindImageContent();
		
		$detail		=	$this->{$this->ModelName}->find('first', array(
							'conditions' => array(
								"{$this->ModelName}.id" => $ID
							),
							"recursive"	=>	2
						));
		
		if(empty($detail))
		{
		   $this->render("/Errors/error404");
		   return;
		}

		if(empty($this->data))
		{
			$this->request->data										=	$detail;
		}
		else
		{
			$this->{$this->ModelName}->set($this->request->data);
			$this->{$this->ModelName}->ValidateData();
			if($this->{$this->ModelName}->validates())
			{
				$save	=	$this->{$this->ModelName}->saveAll($this->request->data,array("validate"=>false));
				
				//==================== START SAVE PDF ====================//
				if(!empty($this->request->data[$this->ModelName]["pdf"]["name"]))
				{
					$uploadPdf	=	$this->General->UploadContent(
						$ID,
						"Product",
						"pdf",
						$this->request->data[$this->ModelName]["pdf"]
					);
					
					if($uploadPdf)
					{
						$this->{$this->ModelName}->updateAll(
							array(
								"pdf_download"			=>	"1"
							),
							array(
								"{$this->ModelName}.id"	=>	$ID
							)
						);
					}
				}
				//==================== START SAVE PDF ====================//
				
				
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
						$mime_type						=	mime_content_type($tmp_images1_img);
						$width							=	$width > 800 ? 800 : $width;
						
						$this->loadModel("ProductImage");
						$ProductImageId					=	$this->ProductImage->find("first",array(
																"conditions"	=>	array(
																	"ProductImage.product_id"	=>	$ID,
																	"ProductImage.pos"	=>	0
																)
															));
						if(empty($ProductImageId))
						{
							$this->ProductImage->saveAll(
								array(
									"product_id"	=>	$ID,
									"pos"			=>	0
								)
							);
							$ProductImageId		=	$this->ProductImage->id;
						}
						else
						{
							$ProductImageId	=	$ProductImageId["ProductImage"]["id"];
						}
						
									
						$resize							=	$this->General->ResizeImageContent(
																$tmp_images1_img,
																$this->settings["cms_url"],
																$ProductImageId,
																"ProductImage",
																"square",
																$mime_type,
																$this->settings["product_width"],
																$this->settings["product_height"],
																"cropResize"
															);

						$resize							=	$this->General->ResizeImageContent(
																$tmp_images1_img,
																$this->settings["cms_url"],
																$ProductImageId,
																"ProductImage",
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
		
		
		//LIST CATEGORY
		$this->loadModel("ProductSubCategory");
		$category_id_list		=	$this->ProductSubCategory->find("list",array(
										"order"	=>	"ProductSubCategory.name"
									));
		
		$this->set(compact(
			"category_id_list",
			"detail",
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
}