<?php
class OrderProduct extends AppModel
{
	public function beforeSave($options = array())
	{
		if(isset($this->data[$this->name]['description']))
		{
			if(empty($this->data[$this->name]['description']))
			{
				$this->data[$this->name]['description']	=	NULL;
			}
		}
		
		if(isset($this->data[$this->name]['qty']))
		{
			if(empty($this->data[$this->name]['qty']))
			{
				$this->data[$this->name]['qty']	=	str_replace(",","",$this->data[$this->name]['qty']);
			}
		}
		
		if(!$this->ProductIsUnique())
			return false;
		return true;
	}
	
	public function BindDefault($reset	=	true)
	{
		$this->bindModel(array(
			"belongsTo"	=>	array(
				"Product"	=>	array(
					"foreignKey"	=>	"product_id"
				)
			)
		),$reset);
	}
	
	function ValidateData()
	{
		App::uses('CakeNumber', 'Utility');
		
		$this->validate 	= array(
			'order_id' => array(
				'notBlank'	=> array(
					'rule' 		=> "notBlank",
					'message' 	=> __d('validation',"Order ID not found!")
				)
			),
			'product_id' => array(
				'notBlank'	=> array(
					'rule' 		=> "notBlank",
					'message' 	=> __d('validation',"Please select product")
				)
			),
			'qty' => array(
				'notBlank'	=> array(
					'rule' 		=> "notBlank",
					'message' 	=> __d('validation',"Please insert quantity product")
				),
				'GreaterThanZero'	=> array(
					'rule' 		=> "GreaterThanZero",
					'message' 	=> __d('validation',"Please insert quantity product")
				)
			)
		);
	}
	
	function ProductIsUnique()
	{
		if(isset($this->data[$this->name]["order_id"]) && isset($this->data[$this->name]["product_id"]))
		{
			$product_id	=	$this->data[$this->name]['product_id'];
			$order_id	=	$this->data[$this->name]['order_id'];
			
			$checkFirst	=	$this->find("first",array(
									"conditions"	=>	array(
										"{$this->name}.order_id"	=>	$order_id,
										"{$this->name}.product_id"	=>	$product_id
									)
								));
			return empty($checkFirst);
		}
		return true;
	}
	
	function GreaterThanZero()
	{
		if(isset($this->data[$this->name]["qty"]))
		{
			$qty	=	intval($this->data[$this->name]["qty"]);
			return $qty > 0;
		}
		return true;
	}
}
?>