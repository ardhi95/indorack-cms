<?php
class ProductSubCategory extends AppModel
{
	public function beforeSave($options = array())
	{
		if($this->id)
		{
		}
		return true;
	}
	
	public function afterSave($created,$options = array())
	{
	}
	
	public function afterDelete()
	{
		//DELETE IMAGE CONTENT
		App::import('Component','General');
		$General		=	new GeneralComponent();
		$General->DeleteContent($this->id,$this->name);
	}

	public function BindDefault($reset	=	true)
	{
		/*$this->bindModel(array(
			"hasOne"	=>	array(
				"ProductImage"	=>	array(
					"foreignKey"	=>	"product_id",
					"conditions"	=>	"ProductImage.pos = 0"
				)
			)
		),$reset);*/
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
			'name' => array(
				'notBlank'	=> array(
					'rule' 		=> "notBlank",
					'message' 	=> __d('validation',"Please insert category name")
				),
				'minLength'	=> array(
					'rule' 		=> array("minLength",2),
					'message' 	=> __d('validation',"Category name is too short")
				),
				'maxLength'	=> array(
					'rule' 		=> array("maxLength",200),
					'message' 	=> __d('validation',"Category name too long")
				)
			),
			'category_id' => array(
				'notBlank'	=> array(
					'rule' 		=> "notBlank",
					'message' 	=> __d('validation',"Please select product category")
				)
			),
			'images' => array(
				'imagewidth'	=> array(
					'rule' 		=> array('imagewidth',300),
					'message' 	=> __d('validation','Please upload image with minimum width is %s px',array(300))
				),
				'extension' => array(
					'rule' => array('validateName', array('gif','jpeg','jpg','png')),
					'message' => __d('validation','Only (*.gif,*.jpeg,*.jpg,*.png) are allowed.')
				)
			)
		);
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
