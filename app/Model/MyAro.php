<?php
App::uses('Sanitize','Utility');
class MyAro extends AppModel
{
	public $actsAs		=	array('Tree');
	public $useTable	=	"aros";


	public function beforeSave($options = array())
	{
		if(!empty($this->data))
		{
			foreach($this->data[$this->name] as $key => $name)
			{
				$this->data[$this->name][$key]		=	trim($this->data[$this->name][$key]);
			}
			/*if(empty($this->data[$this->name]["parent_id"]) && empty($this->id))
				$this->data[$this->name]["parent_id"] = "1";*/
		}
		return true;
	}

	public function afterFind($results, $primary = false)
	{
		return $results;
	}

	public function afterDelete()
	{
		$AroAcos	=	ClassRegistry::Init("AroAco");
		$AroAcos->deleteAll(array(
			"AroAco.aro_id"	=>	$this->id
		));

		$User		=	ClassRegistry::Init("User");
		$User->updateAll(
			array(
				"aro_id"		=>	NULL
			),
			array(
				"User.aro_id"	=>	$this->id
			)
		);
	}

	function ValidateData()
	{
		App::uses('CakeNumber', 'Utility');
		$this->validate 	= array(
			"id"	=>	array(
				'notBlank' => array(
					'rule' 		=>  "notBlank",
					'message' 	=>  __d('validation','Sorry we cannot find your ID.'),
					"on"		=>	"update"
				),
				'IsExists' => array(
					'rule' 		=> "IsExists",
					'message'	=> __d('validation','Sorry we cannot find your details data.'),
					"on"		=>	"update"
				)
			),
			'alias' => array(
				'notBlank' => array(
					'rule' 		=>	"notBlank",
					'message' 	=>	__d('validation',"Please insert admin group name")
				),
				"UniqueName" => array(
					'rule' 		=>	"UniqueName",
					'message' 	=>	__d('validation',"This group name already exists"),
					"on"		=>	"create"
				),
				"UniqueNameEdit" => array(
					'rule' 		=>	"UniqueNameEdit",
					'message' 	=>	__d("validation","This group name already exists"),
					"on"		=>	"update"
				),
				"NoSpcaeAndOtherCharacter"	=> array(
					'rule'		=>	"NoSpcaeAndOtherCharacter",
					'message'	=>	__d("validation","Not allowed character, please insert A-Z,a-z,0-9,_ and no space")
				),
			),
			'parent_id' => array(
				'notEmpty' => array(
					'rule' 		=> "notEmpty",
					'message' 	=> __d("validation","Please select parent group")
				)
			)
		);
	}

	function BindDefault($reset	=	true)
	{
		$this->bindModel(array(
			"belongsTo"	=>	array(
				"Parent"	=>	array(
					"className"	=>	"MyAro"
				)
			)
		),$reset);
	}

	function VirtualFieldActivated()
	{
		$this->virtualFields = array(
			'SStatus'		=> 'IF(('.$this->name.'.status=\'1\'),\'Active\',\'Hide\')',
		);
	}

	function NoSpcaeAndOtherCharacter($fields = array())
	{
		foreach($fields as $key=>$value)
		{
			$regex	=	"/^[a-zA-Z0-9_ ]{1,}$/";
			$out	=	preg_match($regex,$value);
			return $out;
		}
		return false;
	}

	function UniqueName($fields = array())
	{
		foreach($fields as $key=>$value)
		{
			$value	=	strtolower($value);
			$data	=	$this->find("first",array(
							"conditions"	=>	array(
								"LOWER({$this->name}.alias)"	=>	$value
							)
						));

			return empty($data);
		}
		return false;
	}

	function UniqueNameEdit($fields = array())
	{
		foreach($fields as $key=>$value)
		{
			$value	=	strtolower($value);
			$data	=	$this->find("first",array(
							"conditions"	=>	array(
								"LOWER({$this->name}.alias)"	=>	$value,
								"NOT"							=>	array(
									"{$this->name}.id"			=>	$this->data[$this->name]["id"]
								)
							)
						));
			return empty($data);
		}
		return false;
	}

	function IsExists($fields = array())
	{
		foreach($fields as $key=>$value)
		{
			$data	=	$this->findById($value);
			if(!empty($data)) return true;
		}
		return false;
	}
}
?>
