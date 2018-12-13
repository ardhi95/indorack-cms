<?php
class TaskHistory extends AppModel
{
	function beforeSave($options = array())
	{
		if(!empty($this->data))
		{
			$checkFirst	=	$this->find("first",array(
								"conditions"	=>	array(
									"{$this->name}.task_id"		=>	$this->data[$this->name]['task_id'],
									"{$this->name}.order_id"	=>	$this->data[$this->name]['order_id'],
									"{$this->name}.employee_id"	=>	$this->data[$this->name]['employee_id']
								),
								"order"							=>	"{$this->name}.created desc"
							));

			if(!empty($checkFirst))
			{
				if(
					(in_array($checkFirst[$this->name]['status'],array("2","3")) and 
					$this->data[$this->name]['status'] == "2") or
					(
						in_array($checkFirst[$this->name]['status'],array("5","6","7")) &&
						$checkFirst[$this->name]['status'] == $this->data[$this->name]['status']
					)
				)
					return false;
			}
		}
		return true;
	}
}
?>