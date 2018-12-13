<?php
class Rating extends AppModel
{
	
	public function beforeSave($options = array())
	{
		return true;	
	}
	
	public function beforeValidate($options = array())
	{
		if(!isset($this->data[$this->name]["id"]))
		{
			$check	=	$this->find("first",array(
							"conditions"	=>	array(
								"{$this->name}.employee_id"		=>	$this->data[$this->name]["employee_id"],
								"{$this->name}.task_id"			=>	$this->data[$this->name]["task_id"]
							)
						));
			if(!empty($check))
			{
				$this->id	=	$check[$this->name]["id"];
			}
		}
		return true;
	}
	
	function ValidateData()
	{
		App::uses('CakeNumber', 'Utility');
		$this->validate 	= array(
			'user_id' => array(
				'notBlank'	=> array(
					'rule' 		=> "notBlank",
					'message' 	=> __d('validation',"Your profile not found!")
				),
				'ValidateUserId'	=> array(
					'rule' 		=> "ValidateUserId",
					'message' 	=> __d('validation',"Your profile not found!")
				)
			),
			'task_id' => array(
				'notBlank'	=> array(
					'rule' 		=> "notBlank",
					'message' 	=> __d('validation',"Detail order not found!")
				),
				'ValidateTaskId'	=> array(
					'rule' 		=> "ValidateTaskId",
					'message' 	=> __d('validation',"Detail order not found!")
				)
			),
			'star' => array(
				'notBlank'	=> array(
					'rule' 		=> "notBlank",
					'message' 	=> __d('validation',"Please put your star rating")
				)
			)
		);
	}
	
	function ValidateUserId()
	{
		if(isset($this->data[$this->name]["user_id"]))
		{
			$User		=	ClassRegistry::Init("User");
			$user_id	=	$this->data[$this->name]["user_id"];
			$checkUser	=	$User->find("first",array(
								"conditions"	=>	array(
									"User.id"		=>	$user_id,
									"User.status"	=>	"1"
								)
							));
			return !empty($checkUser);
		}
		return false;
	}
	
	function ValidateTaskId()
	{
		if(isset($this->data[$this->name]["task_id"]))
		{
			$customer_id	=	$this->data[$this->name]["user_id"];
			$Task			=	ClassRegistry::Init("Task");
			$Task->bindModel(array(
				"belongsTo"	=>	array(
					"Order"
				)
			));
			
			$task_id		=	$this->data[$this->name]["task_id"];
			$check			=	$Task->find("first",array(
									"conditions"	=>	array(
										"Task.id"				=>	$task_id,
										"Order.customer_id"		=>	$customer_id
									)
								));
			return !empty($check);
		}
		return false;
	}
	
	function ValidateStar()
	{
		if(isset($this->data[$this->name]["star"]))
		{
			$star			=	$this->data[$this->name]["star"];
			$description	=	$this->data[$this->name]["description"];
			
			if(empty($star) && empty($description))
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		return false;
	}
}
?>