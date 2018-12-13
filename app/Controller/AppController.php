<?php
App::uses('Controller', 'Controller');

class AppController extends Controller
{

	public $settings;
	public $super_admin_id;
	public $login_id;
	public $profile;
	public $access;

	public $helpers		=	array("Form","Js","Session");
	public $ext 		=	'.php';
	public $components	=	array(
								"General",
								"Session",
								"Cookie",
								"Acl"
							);
	public $langList;

	public function beforeFilter()
	{
		$this->layout	=	"main";
		//SETTING
		$this->settings = Cache::read('settings', 'long');
		if(!$this->settings || (isset($_GET['debug']))) {

			$this->loadModel('Setting');
			$settings			=	$this->Setting->find('first');
			$this->settings		=	$settings['Setting'];
			Cache::write('settings', $this->settings, 'long');
		}
		$this->set('settings',$this->settings);

		//LOCALIZATION
		$this->langList = Cache::read('langList', 'long');
		if(!$this->langList || (isset($_GET['debug']))) {

			$this->loadModel('Lang');
			$this->langList			=	$this->Lang->find("list",array(
											"fields"	=>	array(
												"code"
											)
										));
			Cache::write('langList', $this->langList, 'long');
		}

		//LOCALIZATION
		if(isset($_REQUEST['lang']) && in_array($_REQUEST['lang'],$this->langList))
			$this->Session->write('Config.language',$_REQUEST['lang']);


		if ($this->Session->check('Config.language')) {
            Configure::write('Config.language', $this->Session->read('Config.language'));
        }
		else
		{
			$this->Session->write('Config.language',Configure::read('Config.language'));
		}


		//GET CONTROLLER AND ACTION NAME
		$controller			=	strtolower($this->params["controller"]);
		$action				=	strtolower($this->params["action"]);
		$c_allowed			=	array("account","template","screenorder");//LIST OF CONTROlLER THAT SHOULD NOT REDIRECT
		$a_allowed			=	array("login","register","lockscreen");//LIST OF ACTION THAT SHOULD NOT REDIRECT

		//CHECK ADMIN COOKIE
		$userlogin			=	$this->Cookie->read("userlogin");
		$lockscreen			=	$this->Cookie->read("lockscreen");

		
		if(empty($userlogin))
		{
			if(!in_array($controller,$c_allowed) and !in_array($action,$a_allowed))
			{
				$this->redirect(array("controller"=>"Account","action"=>"Login"));
			}
		}
		else
		{
			if(!empty($lockscreen) && $controller!="account" && $action!= "lockscreen")
			{
				$this->redirect(array("controller"=>"Account","action"=>"LockScreen"));
			}
			else
			{
				$this->profile		=	$this->CheckProfile();
				$this->login_id		=	$this->profile['User']['id'];

				if(empty($this->profile))
				{
					$this->Cookie->delete("userlogin");
					$this->redirect($this->settings["cms_url"]);
				}
			}
		}
		
		$this->set('login_id',$this->login_id);
		$this->set('super_admin_id',$this->super_admin_id);
		$this->set('profile',$this->profile);
		$this->set('access',$this->access);
		$this->set('current_active_menu',array("3"));
	}

	function CheckProfile()
	{
		$id		=	$this->General->my_decrypt($this->Cookie->read("userlogin"));
		$this->loadModel('User');
		$this->User->BindDefault();
		$this->User->BindImageContent(false);
		$this->User->VirtualFieldActivated();
		
		$find	=	$this->User->find('first',array(
						'conditions'	=>	array(
							'User.id'			=>	$id,
							'User.status'		=>	1
						),
						"recursive"		=>	2
					));

		//CHECK PRIVILEGES
		$this->loadModel("AroAco");
		$aro_id			=	$find["User"]["aro_id"];
		$fPrevilidges	=	$this->AroAco->find("all",array(
									"conditions"	=>	array(
										"AroAco.aro_id"	=>	$aro_id
									)
								));
		if(!empty($fPrevilidges))
		{
			foreach($fPrevilidges as $fPrevilidges)
			{
				$access[$fPrevilidges["AroAco"]["aco_id"]]["_read"]		=	$fPrevilidges["AroAco"]["_read"];
				$access[$fPrevilidges["AroAco"]["aco_id"]]["_create"]	=	$fPrevilidges["AroAco"]["_create"];
				$access[$fPrevilidges["AroAco"]["aco_id"]]["_update"]	=	$fPrevilidges["AroAco"]["_update"];
				$access[$fPrevilidges["AroAco"]["aco_id"]]["_delete"]	=	$fPrevilidges["AroAco"]["_delete"];
			}
			$this->access	=	$access;
		}

		//CHECK SUPERADMIN ID
		$this->loadModel("MyAro");
		$aro		=	$this->MyAro->find("first",array(
							"conditions"	=>	array(
								"MyAro.parent_id"	=>	NULL
							)
						));
		$superadmin	=	$this->User->find('first',array(
							'conditions'	=>	array(
								'User.aro_id'		=>	$aro["MyAro"]["id"]
							)
						));
		$this->super_admin_id	=	$superadmin["User"]["id"];
		return $find;
	}

	public function __GetSettings()
	{
		//SETTING
		$settings = Cache::read('settings', 'long');
		if(!$settings || (isset($_GET['debug']))) {

			$this->loadModel('Setting');
			$setting			=	$this->Setting->find('first');
			$settings			=	$setting['Setting'];
			Cache::write('settings', $settings, 'long');
		}
		return $settings;
	}

	public function __GetAccess()
	{
		$id		=	$this->General->my_decrypt($this->Cookie->read("userlogin"));
		$this->loadModel('User');
		$this->User->BindDefault();
		$this->User->BindImageContent(false);
		$find	=	$this->User->find('first',array(
						'conditions'	=>	array(
							'User.id'			=>	$id,
							'User.status'		=>	1
						),
						"recursive"		=>	2
					));

		//CHECK PRIVILEGES
		$this->loadModel("AroAco");
		$aro_id			=	$find["User"]["aro_id"];
		$fPrevilidges	=	$this->AroAco->find("all",array(
									"conditions"	=>	array(
										"AroAco.aro_id"	=>	$aro_id
									)
								));
		$access			=	array();

		if(!empty($fPrevilidges))
		{
			foreach($fPrevilidges as $fPrevilidges)
			{
				$access[$fPrevilidges["AroAco"]["aco_id"]]["_read"]		=	$fPrevilidges["AroAco"]["_read"];
				$access[$fPrevilidges["AroAco"]["aco_id"]]["_create"]	=	$fPrevilidges["AroAco"]["_create"];
				$access[$fPrevilidges["AroAco"]["aco_id"]]["_update"]	=	$fPrevilidges["AroAco"]["_update"];
				$access[$fPrevilidges["AroAco"]["aco_id"]]["_delete"]	=	$fPrevilidges["AroAco"]["_delete"];
			}
		}
		
		return $access;
	}
}
