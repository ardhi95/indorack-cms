<?php
class CmsMenu extends AppModel
{
	public $actsAs	=	array(
                            'Tree',
                            'Translate' =>  array(
                                "name"				=>	"menuNameTranslations"
                            )
                        );

    public $displayField	        =	'name';
	public $translateModel	        =	'CmsMenuTranslation';
    public $translateTable	        =	'cms_menu_translations';
    public $validationDomain        =   'validation';

	public function beforeSave($options = array())
	{
		if(!empty($this->data))
		{
			if($this->data[$this->name]['is_group_separator'] == "1")
			{
				$this->data[$this->name]['aco_id']	=	NULL;
			}
		}
		return true;
	}

	public function BindDefault($reset	=	true)
	{
		$this->bindModel(array(
			"belongsTo"	=>	array(
				"Parent"	=>	array(
					"className"		=>	"CmsMenu",
					"foreignKey"	=>	"parent_id"
				),
				"MyAco"	=>	array(
					"className"		=>	"MyAco",
					"foreignKey"	=>	"aco_id"
				)
			)
		),$reset);
	}

	function VirtualFieldActivated()
	{
		$this->virtualFields = array(
			"SStatus"		=> 'IF(('.$this->name.'.status=\'1\'),\'Show\',\'Hide\')',
			"clickable"		=> 'IF(('.$this->name.'.is_group_separator=\'0\'),\''.__('Yes').'\',\''.__('No').'\')'
		);
	}

	function ValidateData()
	{
		App::uses('CakeNumber', 'Utility');
		$this->validate 	= array(
			'id' => array(
				'notBlank'		=>	array(
					'rule' 		=>	"notBlank",
					'message' 	=>	__d("validation","ID Not found!"),
					"on"		=>	"update"
				),
                'IsExists' => array(
                    'rule'    =>  "IsExists",
                    'message' =>  __d("validation",'Sorry we cannot find your details data.'),
                    'on'      =>  'update'
                )
			),
			'name' => array(
				'notBlank'		=>	array(
					'rule' 		=>	"notBlank",
					'message' 	=>	__d("validation","Please insert menu name")
				),
				'minLength' 	=>	array(
					'rule' 		=>	array("minLength","2"),
					'message'	=>	__d("validation","Menu name is too short")
				),
				'maxLength' 	=>	array(
					'rule' 		=>	array("maxLength","30"),
					'message'	=>	__d("validation","Menu name is too long")
				)
			),
			'parent_id' => array(
				'notBlank'		=>	array(
					'rule' 		=>	"notBlank",
					'message' 	=>	__d("validation","Please select parent menu")
				)
			),
		);
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

}
?>
