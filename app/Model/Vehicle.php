<?php
class Vehicle extends AppModel
{
	var $dataOld;
	public function beforeSave($options = array())
	{
		$this->beforeValidate($options);
		
		if($this->id)
		{
			$this->dataOld	=	$this->findById($this->id);
		}
		return true;
	}
	
	public function afterSave($created,$options = array())
	{
		if(!$created)
		{
			$dataNew	=	$this->findById($this->id);
			if($this->dataOld[$this->name]['vehicle_no'] != $dataNew[$this->name]['vehicle_no'])
			{
				$Task	=	ClassRegistry::Init("Task");
				$Task->updateAll(
					array(
						"vehicle_no"		=>	"'".$dataNew[$this->name]['vehicle_no']."'"
					),
					array(
						"Task.vehicle_no"	=>	$this->dataOld[$this->name]['vehicle_no']
					)
				);
			}
		}
	}
	
	public function afterDelete()
	{
		//DELETE IMAGE CONTENT
		App::import('Component','General');
		$General		=	new GeneralComponent();
		$General->DeleteContent($this->id,$this->name);
	}

	public function beforeValidate($options = array())
	{
		if(!empty($this->data[$this->name]['vehicle_no']))
		{
			$this->data[$this->name]['vehicle_no']	=	strtoupper(str_replace(" ","",$this->data[$this->name]['vehicle_no']));
			
		}
		return true;
	}
	
	public function BindDefault($reset	=	true)
	{
		$this->bindModel(array(
			"belongsTo"	=>	array(
				"User"
			)
		),$reset);
	}

	public function BindImageContent($reset	=	true)
	{
		$this->bindModel(array(
			"hasOne"	=>	array(
				"Thumbnail"	=>	array(
					"className"		=>	"Content",
					"foreignKey"	=>	"model_id",
					"conditions"	=>	array(
						"Thumbnail.model"	=>	$this->name,
						"Thumbnail.type"	=>	"square"
					)
				),
				"Default"	=>	array(
					"className"		=>	"Content",
					"foreignKey"	=>	"model_id",
					"conditions"	=>	array(
						"Default.model"		=>	$this->name,
						"Default.type"		=>	"maxWidth"
					)
				)
			)
		),$reset);
	}

	function VirtualFieldActivated()
	{
		$this->virtualFields = array(
			"SStatus"		=> 'IF(('.$this->name.'.status=\'1\'),\'Active\',\'Not Active\')'
		);
	}

	function ValidateData()
	{
		App::uses('CakeNumber', 'Utility');
		
		$this->validate 	= array(
			'vehicle_no' => array(
				'notBlank'	=> array(
					'rule' 		=> "notBlank",
					'message' 	=> __d('validation',"Please insert vehicle number")
				),
				'minLength'	=> array(
					'rule' 		=> array("minLength",3),
					'message' 	=> __d('validation',"Vehicle number is too short")
				),
				'maxLength'	=> array(
					'rule' 		=> array("maxLength",10),
					'message' 	=> __d('validation',"Vehicle number is too long")
				),
				"isUnique"	=> array(
					'rule' 		=> "isUnique",
					'message' 	=> __d('validation',"This vehicle already exists"),
					"on"		=>	"create"
				),
				"CheckVehicleNo"	=> array(
					'rule' 		=> "CheckVehicleNo",
					'message' 	=> __d('validation',"This vehicle already exists"),
					"on"		=>	"update"
				)
			),
			'user_id' => array(
				'notBlank'	=> array(
					'rule' 		=> "notBlank",
					'message' 	=> __d('validation',"Please select driver")
				),
				"isUnique"	=> array(
					'rule' 		=> "isUnique",
					'message' 	=> __d('validation',"This driver already assign to antoher vehicle"),
					"on"		=>	"create"
				),
				"CheckDriver"	=> array(
					'rule' 		=> "CheckDriver",
					'message' 	=> __d('validation',"This driver already assign to antoher vehicle"),
					"on"		=>	"update"
				)
			)
		);
	}
	
	
	function CheckDriver()
	{
		if(isset($this->data[$this->name]['user_id']))
		{
			$check	=	$this->find("first",array(
							"conditions"	=>	array(
								"{$this->name}.user_id"	=>	$this->data[$this->name]['user_id'],
								"NOT"		=>	array(
									"{$this->name}.id"	=>	$this->data[$this->name]['id']
								)
							)	
						));
						
			return empty($check);
		}
		return true;
	}
	
	function CheckVehicleNo()
	{
		if(isset($this->data[$this->name]['vehicle_no']))
		{
			$check	=	$this->find("first",array(
							"conditions"	=>	array(
								"{$this->name}.vehicle_no"	=>	$this->data[$this->name]['vehicle_no'],
								"NOT"		=>	array(
									"{$this->name}.id"	=>	$this->data[$this->name]['id']
								)
							)	
						));
						
			return empty($check);
		}
		return true;
	}
	
	function NotEmptyExternalUrl()
	{
		$addUrl				=	$this->data[$this->name]["add_url"];
		$destinationUrl		=	$this->data[$this->name]["destination_url"];
		$externalUrl		=	$this->data[$this->name]["external_url"];
		
		if($addUrl == "1")
		{
			if($destinationUrl == "0")
			{
				return !empty($externalUrl);
			}
		}
		
		return true;
	}
	
	function IsExists($fields = array())
    {
        foreach ($fields as $key => $value) {
            $data = $this->findById($value);
            if (!empty($data))
                return true;
        }
        return false;
    }


	function UniqueName($fields = array())
	{
		foreach($fields as $key=>$value)
		{
			$data	=	$this->find("first",array(
							"conditions"	=>	array(
								"LOWER(I18n__nameTranslation.content)"	=>	strtolower($value)
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
								"LOWER(I18n__nameTranslation.content)"			=>	strtolower($value),
								"NOT"							=>	array(
									"{$this->name}.id"			=>	$this->data[$this->name]["id"]
								)
							)
						));

			return empty($data);
		}
		return false;
	}
	
	function UniqueCodeEdit($fields = array())
	{
		foreach($fields as $key=>$value)
		{
			$data	=	$this->find("first",array(
							"conditions"	=>	array(
								"LOWER({$this->name}.code)"		=>	strtolower($value),
								"NOT"							=>	array(
									"{$this->name}.id"			=>	$this->data[$this->name]["id"]
								)
							)
						));

			return empty($data);
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
