<?php
class TemplateController extends AppController
{
	public $components	=	array("Paginator");
	var $helpers		=	array("Tree");
	var $access;
	var $settings;

	public function beforeFilter()
	{
		//parent::beforeFilter();
		$this->layout	=	"ajax";
	}

	public function CustomerMenu()
	{
		$data	=	$this->params['param'];
		//Configure::write('debug',2);
		//pr($data);
		
		//GET DEVICE LIST
		$this->loadModel("UserDevice");
		
		$joins			=	array(
								array(
									"table"			=>	"devices",
									"type"			=>	"LEFT",
									"alias"			=>	"Device",
									"conditions"	=>	array(
										"
											UserDevice.device_id	=	Device.id
										"
									)
								)
							);
		$customerDevice	=	$this->UserDevice->find("all",array(
								"conditions"	=>	array(
									"UserDevice.user_id"	=>	$data['admin_id']
								)
							));
		
		$this->set(compact(
			"data",
			"customerDevice"
		));
	}
	
	public function CmsMenu()
	{
		$this->loadModel("CmsMenu");
        $this->CmsMenu->locale =  $this->Session->read('Config.language');

		$this->CmsMenu->BindDefault(false);
		$this->access	=	$this->__GetAccess();

		//DEFINE CMS MENU
		$conditions			=	array(
									"CmsMenu.status"		=>	1,
									"CmsMenu.parent_id IS NOT NULL"
								);

		//FILTER CMS MENU BY ACOS
		$listAcoId			=	array();
		foreach($this->access as $aco_id=>$access)
		{
			if($access["_read"]=="1")
				$listAcoId[]	=	$aco_id;
		}

		
		$conditions["OR"]	=	array(
									"CmsMenu.aco_id"				=>	$listAcoId,
									"CmsMenu.is_group_separator"	=>	1
								);


		$menu				=	$this->CmsMenu->find("all",array(
									'conditions'	=>	$conditions,
									'order' 		=> 'CmsMenu.lft ASC'
								 ));
		

		$idFinalResult		=	array();
		foreach($menu as $k	=>	$menuData)
		{
			$add	=	true;
			/*if($menuData["CmsMenu"]["is_group_separator"] == "1")
			{
				if(!isset($menu[$k+1]["CmsMenu"]) or $menu[$k+1]["CmsMenu"]["is_group_separator"]=="1")
				{
					$add	=	false;
				}
			}
			*/
			if($add)
			{
				$idFinalResult[]	=	$menuData["CmsMenu"]["id"];
			}
		}
		$menu	=	$this->CmsMenu->find("all",array(
						'conditions'	=>	array(
							"CmsMenu.id"	=>	$idFinalResult
						),
						'order' 		=> 'CmsMenu.lft ASC'
					 ));
		$this->set(compact("menu"));
		$this->set("param",$this->params['param']);
	}

	public function MainHeader()
	{
		$this->set("settings",$this->__GetSettings());
		$this->set("param",$this->params['param']);
	}
}