<?php
class AccountController extends AppController
{
	public $components		=	array("General","Acl");

	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->layout	=	"login";
	}

	public function Login()
	{
		//CHECK ADMIN COOKIE
		$userlogin				=	$this->Cookie->read("userlogin");

		if(!empty($userlogin))
			$this->redirect($this->settings["cms_url"]);

		//DELETE LOCK SCREEN COOKIE IF EXIST
		$this->Cookie->delete('lockscreen');

		if(!empty($this->request->data))
		{
			
			$this->loadModel("User");
			$this->User->set($this->request->data);
			$this->User->ValidateLoginAdmin();
			if($this->User->validates())
			{
				$data			=	$this->User->find("first",array(
										"conditions"	=>	array(
											"LOWER(User.email)"	=>	strtolower($this->request->data["User"]["email"])
										),
										"order"		=>	array(
											"User.id DESC"
										)
									));

				//UPDATE LAST LOGIN
				$this->User->updateAll(
					array(
						"last_login_cms"	=>	"'".date("Y-m-d H:i:s")."'"
					),
					array(
						"User.id"			=>	$data['User']['id']
					)
				);


				//CREATE COOKIE
				$keep_login		=	$this->request->data["User"]["keep_login"];
				$expired		=	($keep_login=="1") ? "360 days" : 60*60;
				$user_id		=	$data['User']['id'];
				$this->Cookie->write('userlogin',	$this->General->my_encrypt($user_id),false,$expired);
				$this->redirect($this->settings["cms_url"]);
			}
		}
	}

	public function CreateLockScreenCookie()
	{
		$this->autoRender		=	false;
		$this->autoLayout		=	false;

		//CHECK ADMIN COOKIE
		$userlogin				=	$this->Cookie->read("userlogin");

		if(empty($userlogin))
		{
			$this->redirect(array("controller"=>"Account","action"=>"Login"));
		}
		else
		{
			$this->Cookie->write('lockscreen',	$this->General->my_encrypt($this->profile["User"]["id"]),false,"360 days");
			$this->redirect($this->settings['cms_url']);
		}
	}


	public function LockScreen()
	{
		//CHECK ADMIN COOKIE
		$userlogin				=	$this->Cookie->read("userlogin");
		pr($this->profile);
		if(empty($userlogin))
			$this->redirect(array("controller"=>"Account","action"=>"Login"));

		//CHECK LOCK COOKIE
		$lockscreen				=	$this->Cookie->read("lockscreen");

		if(empty($userlogin))
			$this->redirect(array("controller"=>"Account","action"=>"CreateLockScreenCookie"));

		if(!empty($this->request->data))
		{
			$this->loadModel("User");
			$this->request->data["User"]["email"]	=	$this->profile["User"]["email"];
			$this->User->set($this->request->data);
			$this->User->ValidateLoginAdmin();
			if($this->User->validates())
			{
				$this->Cookie->delete('lockscreen');
				$this->redirect($this->settings["cms_url"]);
			}
		}
	}

	public function Logout()
	{
		$this->Cookie->delete('userlogin');
		$this->Cookie->destroy();
		return $this->redirect($this->settings['cms_url']."?time=".time());
	}
}
?>
