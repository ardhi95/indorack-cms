<?php
class User extends AppModel
{
	var $aco_id;
	/*var $virtualFields	=	array(
		"fullname"		=> "CONCAT(User.firstname,' ',User.lastname)"
	);*/

	public function beforeSave($options = array())
	{
		App::Import("Components","General");
		$General		=	new GeneralComponent();
		
		if (!empty($this->data[$this->name]['password']))
		{
		    $this->data[$this->name]['password']	=	$General->my_encrypt($this->data[$this->name]['password']);
			$this->data[$this->name]['email']		=	strtolower($this->data[$this->name]['email']);
		}

		foreach($this->data[$this->name] as $key => $name)
		{
			if(!is_array($this->data[$this->name][$key]) && isset($this->data[$this->name][$key]))
				$this->data[$this->name][$key]		=	trim($this->data[$this->name][$key]);
		}
		
		if(isset($this->data[$this->name]['validate_only']) && $this->data[$this->name]['validate_only'] == "1")
			return false;
		
		
		
		
		return true;
	}

	function afterSave($created,$options = array())
	{
	}

	public function afterDelete()
	{
		//DELETE IMAGE CONTENT
		App::import('Component','General');
		$General		=	new GeneralComponent();
		$General->DeleteContent($this->id,$this->name);


		$Aro	=	ClassRegistry::Init("MyAro");
		$add	=	$Aro->updateAll(
						array(
							"total_admin"	=>	"total_admin - 1"
						),
						array(
							"MyAro.id"		=>	$this->aco_id
						)
					);

	}

	public function beforeDelete($cascade = false)
	{
		//GET DETAIL
		$detail				=	$this->find("first",array(
									"conditions"	=>	array(
										"{$this->name}.id"	=>	$this->id
									)
								));
		$this->aco_id		=	$detail[$this->name]["aro_id"];
	}


	public function afterFind($results, $primary = false) {
		/*App::Import("Components","General");
		$General		=	new GeneralComponent();

		foreach ($results as $key => $val)
		{
			if(isset($results[$key][$this->name]['password']))
			{
				$results[$key][$this->name]['password'] 	=	$General->my_decrypt($val[$this->name]['password']);
			}
		}*/
		return $results;
	}

	public function BindThumbnail($reset	=	true)
	{
		$this->bindModel(array(
			"hasOne"	=>	array(
				"Thumbnail"	=>	array(
					"className"	=>	"Content",
					"foreignKey"	=>	"model_id",
					"conditions"	=>	array(
						"Thumbnail.model"	=>	$this->name,
						"Thumbnail.type"	=>	"square"
					)
				)
			)
		),$reset);
	}
	
	public function BindImageContent($reset	=	true)
	{
		$this->bindModel(array(
			"hasOne"	=>	array(
				"Thumbnail"	=>	array(
					"className"	=>	"Content",
					"foreignKey"	=>	"model_id",
					"conditions"	=>	array(
						"Thumbnail.model"	=>	$this->name,
						"Thumbnail.type"	=>	"square"
					)
				),
				"MaxWidth"	=>	array(
					"className"	=>	"Content",
					"foreignKey"	=>	"model_id",
					"conditions"	=>	array(
						"MaxWidth.model"	=>	$this->name,
						"MaxWidth.type"		=>	"maxwidth"
					)
				)
			)
		),$reset);
	}
	
	public function BindDefault($reset	=	true)
	{
		$this->bindModel(array(
			"hasOne"	=>	array(
				"Images"	=>	array(
					"className"	=>	"Content",
					"foreignKey"	=>	"model_id",
					"conditions"	=>	array(
						"Images.model"	=>	"User",
						"Images.type"	=>	"square"
					)
				)
			),
			"belongsTo"	=>	array(
				"MyAro"	=>	array(
					"foreignKey"	=>	"aro_id"
				)
			)
		),$reset);
	}

	function VirtualFieldActivated()
	{
		$this->virtualFields = array(
			"SStatus"		=> 'IF(('.$this->name.'.status=\'1\'),\'Active\',\'Not Active\')',
			"fullname"		=> "CONCAT({$this->name}.firstname,' ',{$this->name}.lastname)",
		);
	}

	function ValidateLoginAdmin()
	{
		$this->validate 	= array(
			'email' => array(
				'notBlank' => array(
					'rule' 		=> "notBlank",
					'message' 	=> __d('validation','Please insert your email.')
				),
				'email' => array(
					'rule' 		=> "email",
					'message' 	=> __d('validation','Your email is not valid.')
				),
				'IsEmailAdminExists' => array(
					'rule' 		=> "IsEmailAdminExists",
					'message' 	=> __d('validation','Your email is not registered as Admin.')
				)
			),
			'password' => array(
				'notBlank' => array(
					'rule' 		=> "notBlank",
					'message' 	=> __d('validation','Please insert your password.')
				),
				'CheckPasswordAdmin' => array(
					'rule' 		=> "CheckPasswordAdmin",
					'message' 	=> __d('validation','Your password is wrong!')
				)
			)
		);
	}

	function ValidateData()
	{
		App::uses('CakeNumber', 'Utility');
		$this->validate 	= array(
			'firstname' => array(
				'notBlank' => array(
					'rule' => "notBlank",
					'message' => __d('validation',"Please enter first name")
				),
				'minLength' => array(
					'rule' 		=> array("minLength","3"),
					'message'	=> __d('validation',"First name too sort")
				),
				'maxLength' => array(
					'rule' 		=> array("maxLength","20"),
					'message'	=> __d('validation',"First name too long")
				)
			),
			'lastname' => array(
				'maxLength' => array(
					'rule' 			=> array("maxLength","20"),
					'message'		=> __d('validation',"First name too long"),
            		'allowEmpty' 	=> true
				)
			),
			'email' => array(
				'notBlank' => array(
					'rule' => "notBlank",
					'message' => __d('validation',"Please insert email.")
				),
				'email' => array(
					'rule' => "email",
					'message' => __d('validation',"Email format is wrong")
				),
				'isUnique' => array(
					'rule' 		=>	"isUnique",
					'message' 	=>	__d('validation',"This email already exists"),
					"required"	=>	"create"
				),
				'UniqueEmailEdit' => array(
					'rule' 		=>	"UniqueEmailEdit",
					'message' 	=>	__d('validation',"This email already exists"),
					"required"	=>	"update"
				)
			),
			'password' => array(
				'notBlank' => array(
					'rule' => "notBlank",
					'message' => __d('validation',"Please enter password")
				),
				'minLength' => array(
					'rule' 		=> array("minLength","4"),
					'message'	=> __d('validation',"Please insert less than 4 characters")
				),
				'maxLength' => array(
					'rule' 		=> array("maxLength","20"),
					'message'	=> __d('validation',"Please insert greater or equal than 20 characters")
				)
			),
			'aro_id' => array(
				'notBlank' => array(
					'rule' => "notBlank",
					'message' => __d('validation',"Please select admin group")
				)
			)
		);
	}

function ValidateDataCustomer()
	{
		App::uses('CakeNumber', 'Utility');
		$this->validate 	= array(
			'firstname' => array(
				'notBlank' => array(
					'rule' => "notBlank",
					'message' => __d('validation',"Please enter first name")
				),
				'minLength' => array(
					'rule' 		=> array("minLength","3"),
					'message'	=> __d('validation',"First name too sort")
				),
				'maxLength' => array(
					'rule' 		=> array("maxLength","20"),
					'message'	=> __d('validation',"First name too long")
				)
			),
			'lastname' => array(
				'maxLength' => array(
					'rule' 			=> array("maxLength","20"),
					'message'		=> __d('validation',"First name too long"),
            		'allowEmpty' 	=> true
				)
			),
			'email' => array(
				'notBlank' => array(
					'rule' => "notBlank",
					'message' => __d('validation',"Please insert email.")
				),
				'email' => array(
					'rule' => "email",
					'message' => __d('validation',"Email format is wrong")
				),
				'isUnique' => array(
					'rule' 		=>	"isUnique",
					'message' 	=>	__d('validation',"This email already exists"),
					"required"	=>	"create"
				),
				'UniqueEmailEdit' => array(
					'rule' 		=>	"UniqueEmailEdit",
					'message' 	=>	__d('validation',"This email already exists"),
					"required"	=>	"update"
				)
			),
			'password' => array(
				'notBlank' => array(
					'rule' => "notBlank",
					'message' => __d('validation',"Please enter password")
				),
				'minLength' => array(
					'rule' 		=> array("minLength","4"),
					'message'	=> __d('validation',"Please insert less than 4 characters")
				),
				'maxLength' => array(
					'rule' 		=> array("maxLength","20"),
					'message'	=> __d('validation',"Please insert greater or equal than 20 characters")
				)
			),
			'phone1' => array(
				'notBlank' => array(
					'rule' => "notBlank",
					'message' => __d('validation',"Please enter phone number")
				),
				'minLength' => array(
					'rule' 		=> array("minLength","6"),
					'message'	=> __d('validation',"Phone number too sort")
				),
				'maxLength' => array(
					'rule' 		=> array("maxLength","20"),
					'message'	=> __d('validation',"Phone number too long")
				)
			),
			'address' => array(
				'notBlank' => array(
					'rule' => "notBlank",
					'message' => __d('validation',"Please enter address")
				),
				'minLength' => array(
					'rule' 		=> array("minLength","5"),
					'message'	=> __d('validation',"address too sort")
				),
				'maxLength' => array(
					'rule' 		=> array("maxLength","500"),
					'message'	=> __d('validation',"address too long")
				)
			),
			'aro_id' => array(
				'notBlank' => array(
					'rule' => "notBlank",
					'message' => __d('validation',"Please select admin group")
				)
			)
		);
	}

    function ValidateLoginCustomer()
	{
		$this->validate 	= array(
			'email' => array(
				'notBlank' => array(
					'rule' 		=> "notBlank",
					'message' 	=> __d('validation','Please insert your email.')
				),
				'email' => array(
					'rule' 		=> "email",
					'message' 	=> __d('validation','Your email is not valid.')
				),
				'IsEmailExists' => array(
					'rule' 		=> "IsEmailExists",
					'message' 	=> __d('validation','Your email is not registered.')
				)
			),
			'password' => array(
				'notBlank' => array(
					'rule' 		=> "notBlank",
					'message' 	=> __d('validation','Please insert your password.')
				),
				'CheckPassword' => array(
					'rule' 		=> "CheckPassword",
					'message' 	=> __d('validation','Your password is wrong!')
				)
			)
		);
	}

	function ValidateUploadImage()
	{
		App::uses('CakeNumber', 'Utility');
		$this->validate 	= array(
			'images' => array(
				'extension' => array(
					'rule' => array('validateName', array('gif','jpeg','jpg','png')),
					'message' => __d('validation','Only (*.gif,*.jpeg,*.jpg,*.png) are allowed.')
				)
			)
		);
	}

	function IsAllowed($fields	=	array())
	{
		$this->BindDefault();
		$this->AdminGroup->BindDefault();

		App::Import("Components","General");
		$General		=	new GeneralComponent();

		$Aco			=	ClassRegistry::Init("Aco");
		$aco			=	$Aco->find("first",array(
								"conditions"	=>	array(
									"LOWER(Aco.alias)"	=>	"login"
								)
							));
		$aco_id			=	$aco["Aco"]["id"];

		$ArosAco		=	ClassRegistry::Init("ArosAco");


		foreach($fields as $k => $v)
		{
			$General	=	new GeneralComponent();
			$email		=	$this->data[$this->name]['email'];
			$password	=	$General->my_encrypt(trim($this->data[$this->name]['password']));

			$find		=	$this->find('first',array(
								'conditions'	=>	array(
									"LOWER({$this->name}.email)"	=>	strtolower($email),
									"{$this->name}.password"		=>	$password
								),
								'order'								=>	array("{$this->name}.id DESC"),
								"recursive"							=>	2
							));

			if(!empty($find))
			{
				if($find[$this->name]["status"]=="1")
				{
					$aro_id			=	$find["AdminGroup"]["Aro"]["id"];
					$prev			=	$ArosAco->find("first",array(
											"conditions"	=>	array(
												"aro_id"	=>	$aro_id,
												"aco_id"	=>	$aco_id
											)
										));
					return !empty($prev);
				}
			}
		}
		return false;
	}


	function CheckPassword()
	{
		App::Import("Components","General");
		$General	=	new GeneralComponent();
		$email		=	$this->data[$this->name]['email'];
		$password	=	$this->data[$this->name]['password'];

		if(!empty($email) && !empty($password))
		{
			if($this->IsEmailExists())
			{
				$encryptPass	=	$General->my_encrypt($this->data[$this->name]['password']);
				$data			=	$this->find('first',array(
										'conditions'	=>	array(
											"LOWER({$this->name}.email)"	=>	strtolower($email),
											"{$this->name}.password"		=>	$encryptPass
										),
										"order"	=>	array("{$this->name}.id ASC")
									));
				return !empty($data);
			}
		}
		return true;
	}
	
	function CheckPasswordAdmin()
	{
		App::Import("Components","General");
		$General	=	new GeneralComponent();
		$email		=	$this->data[$this->name]['email'];
		$password	=	$this->data[$this->name]['password'];
			
		if(!empty($email) && !empty($password))
		{
			if($this->IsEmailAdminExists())
			{
				
				$encryptPass	=	$General->my_encrypt($this->data[$this->name]['password']);
				$data			=	$this->find('first',array(
										'conditions'	=>	array(
											"LOWER({$this->name}.email)"	=>	strtolower($email),
											"{$this->name}.password"		=>	$encryptPass
										),
										"order"	=>	array("{$this->name}.id ASC")
									));
				return !empty($data);
			}
		}
		return true;
	}

	function IsEmailExists()
	{
		$email		=	$this->data[$this->name]['email'];
		
		if(!empty($email))
		{

			$data		=	$this->find('first',array(
								'conditions'	=>	array(
									"LOWER(email)"			=>	strtolower($email),
									"status"				=>	"1"
								),
								"order"	=>	array("{$this->name}.id ASC")
							));
			return !empty($data);
		}
		return false;
	}
	
	function IsEmailAdminExists()
	{
		$email		=	$this->data[$this->name]['email'];
		if(!empty($email))
		{

			$data		=	$this->find('first',array(
								'conditions'	=>	array(
									"LOWER(email)"			=>	strtolower($email),
									"status"				=>	"1",
									"{$this->name}.aro_id"	=>	array("1","2","3")
								),
								"order"	=>	array("{$this->name}.id ASC")
							));
			return !empty($data);
		}
		return false;
	}

	function NoSpcaeAndOtherCharacter($fields = array())
	{
		foreach($fields as $key=>$value)
		{
			$regex	=	"/^[a-zA-Z0-9_]{1,}$/";
			$out	=	preg_match($regex,$value);
			return $out;
		}
		return false;
	}

	function UniqueName($fields = array())
	{
		foreach($fields as $key=>$value)
		{
			$data	=	$this->find("first",array(
							"conditions"	=>	array(
								"LOWER({$this->name}.email)"	=>	strtolower($value)
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
			$data	=	$this->find("first",array(
							"conditions"	=>	array(
								"LOWER({$this->name}.username)"	=>	strtolower($value),
								"NOT"							=>	array(
									"{$this->name}.id"			=>	$this->data[$this->name]["id"]
								)
							)
						));

			return empty($data);
		}
		return false;
	}

	function UniqueEmailEdit($fields = array())
	{
		if(
			isset($this->data[$this->name]["validate_only"]) &&
			$this->data[$this->name]["validate_only"] == "1"
		)
		{
			return true;
		}
			
		foreach($fields as $key=>$value)
		{
			$data	=	$this->find("first",array(
							"conditions"	=>	array(
								"LOWER({$this->name}.email)"	=>	strtolower($value),
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

	function size( $field=array(), $aloowedsize)
    {
		foreach( $field as $key => $value ){
            $size = intval($value['size']);
            if($size > $aloowedsize) {
                return FALSE;
            } else {
                continue;
            }
        }
        return TRUE;
    }


	function notEmptyImage($fields = array())
	{
		foreach($fields as $key=>$value)
		{
			if(empty($value['name']))
			{
				return false;
			}
		}

		return true;
	}

	function validateName($file=array(),$ext=array())
	{
		$err	=	array();
		$i=0;

		foreach($file as $file)
		{
			$i++;

			if(!empty($file['name']))
			{
				if(!Validation::extension($file['name'], $ext))
				{
					return false;
				}
			}
		}
		return true;
	}

	function imagewidth($field=array(), $allowwidth=0)
	{

		foreach( $field as $key => $value ){
			if(!empty($value['name']))
			{
				$imgInfo	= getimagesize($value['tmp_name']);
				$width		= $imgInfo[0];

				if($width < $allowwidth)
				{
					return false;
				}
			}
        }
        return TRUE;
	}

	function imageheight($field=array(), $allowheight=0)
	{
		foreach( $field as $key => $value ){
			if(!empty($value['name']))
			{
				$imgInfo	= getimagesize($value['tmp_name']);
				$height		= $imgInfo[1];

				if($height < $allowheight)
				{
					return false;
				}
			}
        }
        return TRUE;
	}
}
?>
