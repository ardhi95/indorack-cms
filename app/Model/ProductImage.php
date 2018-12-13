<?php 
class ProductImage extends AppModel
{
	var $dataOld;
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
				"MaxWidth"	=>	array(
					"className"		=>	"Content",
					"foreignKey"	=>	"model_id",
					"conditions"	=>	array(
						"MaxWidth.model"	=>	$this->name,
						"MaxWidth.type"	=>	"maxwidth"
					)
				)
			)
		),$reset);
	}
	
	public function beforeDelete($cascade = false)
	{
		$this->dataOld	=	$this->findById($this->id);
	}
	
	public function afterDelete()
	{
		//DELETE IMAGE CONTENT
		App::import('Component','General');
		$General		=	new GeneralComponent();
		$General->DeleteContent($this->id,$this->name);
		
		//DELETE PRODUCT VARIANT IMAGE
		$ProductImageVariant	=	ClassRegistry::Init("ProductImageVariant");
		$ProductImageVariant->query("DELETE FROM product_image_variants WHERE product_image_id = '".$this->id."'");
		
		//DETAIL DATA
		$data			=	$this->findById($this->id);
		$product_id		=	$this->dataOld[$this->name]['product_id'];
		$data			=	$this->find("all",array(
								"conditions"	=>	array(
									"{$this->name}.product_id"	=>	$product_id
								),
								"order"			=>	array(
									"{$this->name}.id ASC"
								)
							));
		
		if(!empty($data))
		{
			$counter		=	1;
			foreach($data as $data)
			{
				$this->updateAll(
					array(
						"pos"	=>	"'".$counter."'"
					),
					array(
						"{$this->name}.id"	=>	$data[$this->name]["id"]
					)
				);
				$counter++;
			}
		}
	}
}
?>