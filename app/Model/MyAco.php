<?php
App::uses('Sanitize','Utility');
class MyAco extends AppModel
{
	public $actsAs		=	array('Tree');
	public $useTable	=	"acos";


	public function beforeSave($options = array())
	{
		if(!empty($this->data))
		{
			foreach($this->data[$this->name] as $key => $name)
			{
				$this->data[$this->name][$key]		=	trim($this->data[$this->name][$key]);
				if($key == "alias")
				{

					if(!empty($this->id))
						$this->data[$this->name][$key]	=	Inflector::singularize($this->data[$this->name][$key]);
					//var_dump($this->data[$this->name][$key]."<br/>");

					$this->data[$this->name][$key]	=	Inflector::slug($this->data[$this->name][$key],"_");
					//var_dump($this->data[$this->name][$key]."<br/>");
					$this->data[$this->name][$key]	=	Inflector::camelize($this->data[$this->name][$key]);
					//var_dump($this->data[$this->name][$key]."<br/>");
					$this->data[$this->name][$key]	=	Inflector::pluralize($this->data[$this->name][$key]);
					//var_dump($this->data[$this->name][$key]."<br/>");
				}
			}
		}
		return true;
	}

	public function afterFind($results, $primary = false)
	{
		return $results;
	}

	public function afterSave($created, $options = array())
	{

		if($created)
		{

			$MyAro				=	ClassRegistry::Init("MyAro");
			$AroAco				=	ClassRegistry::Init("AroAco");
			$superAdminAro		=	$MyAro->find("first",array(
										"conditions"	=>	array(
											"MyAro.parent_id"	=>	NULL
										)
									));

			$premiumAdminAro	=	$MyAro->find("first",array(
										"conditions"	=>	array(
											"MyAro.parent_id"	=>	$superAdminAro["MyAro"]["id"]
										)
									));


			$AroAco->create();
			$save	=	$AroAco->saveAll(array(
				"aro_id"		=>	$superAdminAro["MyAro"]["id"],
				"aco_id"		=>	$this->id,
				"_create"	=>	"1",
				"_update"	=>	"1",
				"_delete"	=>	"1",
				"_read"		=>	"1"
			),array("validate"=>false));



			if(!empty($premiumAdminAro))
			{
				$AroAco->create();
				$AroAco->saveAll(array(
					"aro_id"		=>	$premiumAdminAro["MyAro"]["id"],
					"aco_id"		=>	$this->id,
					"_create"	=>	"1",
					"_update"	=>	"1",
					"_delete"	=>	"1",
					"_read"		=>	"1"
				),array("validate"=>false));
			}
		}
	}

	public function afterDelete()
	{
		$AroAcos	=	ClassRegistry::Init("AroAco");
		$AroAcos->deleteAll(array(
			"AroAco.aco_id"	=>	$this->id
		));

		$CmsMenu	=	ClassRegistry::Init("CmsMenu");

		$CmsMenu->deleteAll(array("aco_id"	=>	$this->id));
	}


	function beforeValidate($options = array())
	{
		if(empty($this->data[$this->name]["parent_id"]))
		{
			$CheckParent	=	$this->find("first",array(
									"conditions"	=>	array(
										"{$this->name}.parent_id"	=>	NULL
									)
								));
			$this->data[$this->name]["parent_id"]	=	$CheckParent[$this->name]["id"];
		}
		return true;
	}

	function ValidateData()
	{
		App::uses('CakeNumber', 'Utility');
		$this->validate 	= array(
			"id"	=>	array(
				'notBlank' => array(
					'rule' 		=>    "notBlank",
					'message' 	=>  __d('validation','Sorry we cannot find your ID.'),
					"on"		=>    "update"
				),
				'IsExists' => array(
					'rule' 		=>    "IsExists",
					'message'	=>   __d('validation','Sorry we cannot find your details data.'),
					"on"		=>    "update"
				)
			),
			'alias' => array(
				'notBlank' => array(
					'rule' 		=>    "notBlank",
					'message' 	=>    __d('validation',"Please enter modul name")
				),
				"UniqueName" => array(
					'rule' 		=>    "UniqueName",
					'message' 	=>    __d('validation',"Modul name already exists"),
					"on"		=>    "create"
				),
				"UniqueNameEdit" => array(
					'rule' 		=>    "UniqueNameEdit",
					'message' 	=>    __d('validation',"Modul name already exists"),
					"on"		=>    "update"
				),
				"NoSpcaeAndOtherCharacter"	=> array(
					'rule'		=>    "NoSpcaeAndOtherCharacter",
					'message'	=>    __d('validation',"Not allowed character, please insert A-Z,a-z,0-9,_ and no space")
				),
			),
			'controller' => array(
				'notBlank' => array(
					'rule' 		=>    "notBlank",
					'message' 	=>    __d('validation',"Please select controller name")
				)
			)
		);
	}

	function BindDefault($reset	=	true)
	{
		$this->bindModel(array(
			"belongsTo"	=>	array(
				"Parent"	=>	array(
					"className"	=>	"MyAco"
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
			$value	=	Inflector::singularize($value);
			$value	=	Inflector::slug($value,"_");
			$value	=	Inflector::camelize($value);
			$value	=	Inflector::pluralize($value);
			$data	=	$this->find("first",array(
							"conditions"	=>	array(
								"{$this->name}.alias"	=>	$value
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
			$value	=	Inflector::singularize($value);
			$value	=	Inflector::slug($value,"_");
			$value	=	Inflector::camelize($value);
			$value	=	Inflector::pluralize($value);
			$data	=	$this->find("first",array(
							"conditions"	=>	array(
								"{$this->name}.alias"			=>	$value,
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
