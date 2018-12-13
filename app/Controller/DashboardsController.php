<?php
class DashboardsController extends AppController
{
	var $helpers			=	array("Tree");
	var $ControllerName		=	"Dashboards";
	var $ModelName 			=	"CmsMenu";

	public function beforeFilter()
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

	function Index()
	{
		if ($this->access[$this->aco_id]["_read"] != "1"){
			$this->render("/Errors/no_access");
			return;
		}
	}

	function GetData()
	{
		$this->autoRender	=	false;
		$this->autoLayout	=	false;

		$this->loadModel("Matrixs");
		$data		=	$this->Matrixs->find("all",array(
							"order"	=>	"Matrixs.id ASC"
						));
		$json		=	json_encode(array("data"=>$data));
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($data);
		}
	}
}
