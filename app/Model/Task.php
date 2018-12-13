<?php
class Task extends AppModel
{
	public function afterDelete()
	{
		//DELETE IMAGE CONTENT
		App::import('Component','General');
		$General		=	new GeneralComponent();
		$General->DeleteContent($this->id,$this->name);
		
		//DELETE TASK USER
		$TaskAssign	=	ClassRegistry::Init("TaskAssign");
		$TaskAssign->deleteAll(array("TaskAssign.task_id"	=>	$this->id),true,true);
		
		//DELETE TASK HISTORY
		$TaskHistory	=	ClassRegistry::Init("TaskHistory");
		$TaskHistory->deleteAll(array("TaskHistory.task_id"	=>	$this->id),true,true);
	}
	
	function ValidateResponse()
	{
		App::uses('CakeNumber', 'Utility');
		
		$this->validate 	= array(
			'id' => array(
				'notBlank'	=> array(
					'rule' 		=> "notBlank",
					'message' 	=> __d('validation',"Job not found!")
				),
				"ValidateTaskId"	=>	array(
					"rule"	=>	"ValidateTaskId"
				)
			),
			'employee_id' => array(
				'notBlank'	=> array(
					'rule' 		=> "notBlank",
					'message' 	=> __d('validation',"Your account profile not found!")
				),
				"ValidateEmployeeId"	=>	array(
					"rule"	=>	"ValidateEmployeeId"
				)
			),
			'status' => array(
				'notBlank'	=> array(
					'rule' 		=> "notBlank",
					'message' 	=> __d('validation',"Your account profile not found!")
				)
			)
		);
	}
	
	function ValidateStatus()
	{
		if(isset($this->data[$this->name]["status"]))
		{
			$status		=	$this->data[$this->name]["status"];
			return in_array($status,array(1,2,3,4,5,6,7,8));
		}
		return false;
	}
	
	
	function ValidateTaskId()
	{
		if(isset($this->data[$this->name]["id"]))
		{
			$id		=	$this->data[$this->name]["id"];
			
			$check	=	$this->find("first",array(
							"conditions"	=>	array(
								"{$this->name}.id"	=>	$id
							)
						));
						
			if(empty($check))
			{
				return "Job not found!";
			}
			else
			{
				return true;
			}
		}
		return false;
	}
	
	function ValidateEmployeeId()
	{
		if(isset($this->data[$this->name]["employee_id"]))
		{
			$employee_id		=	$this->data[$this->name]["employee_id"];
			$task_id			=	$this->data[$this->name]["id"];
			
			$TaskAssign			=	ClassRegistry::Init("TaskAssign");
			$check				=	$TaskAssign->find("first",array(
										"conditions"	=>	array(
											"TaskAssign.task_id"		=>	$task_id,
											"TaskAssign.employee_id"	=>	$employee_id
										)
									));
									
			if(empty($check))
			{
				return "You don't assign for this job!";
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