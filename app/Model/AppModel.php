<?php
App::uses('Model', 'Model');
class AppModel extends Model
{
	var $settings;
	var $langList;
	public function __construct( $id = false, $table = NULL, $ds = NULL )
    {
        parent::__construct($id,$table,$ds);
		
		//SETTING
		$this->settings = Cache::read('settings', 'long');
		if(!$this->settings || (isset($_GET['debug']))) {

			$Setting			=	ClassRegistry::Init('Setting');
			$settings			=	$Setting->find('first');
			$this->settings		=	$settings['Setting'];
			Cache::write('settings', $this->settings, 'long');
		}
		
		//LOCALIZATION
		$this->langList = Cache::read('langList', 'long');
		if(!$this->langList || (isset($_GET['debug'])))
		{
			$Lang					=	ClassRegistry::Init('Lang');
			$this->langList			=	$Lang->find("list",array(
											"fields"	=>	array(
												"code"
											)
										));
			Cache::write('langList', $this->langList, 'long');
		}
    }
	
	public function BindDefault($reset	=	true)
	{
	}
}
