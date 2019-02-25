<?php
define("PBKDF2_HASH_ALGORITHM", "sha256");
define("PBKDF2_ITERATIONS", 901);
define("PBKDF2_SALT_BYTE_SIZE", 12);
define("PBKDF2_HASH_BYTE_SIZE", 24);
define("SEPARATOR", "$");
define("TAG", "PBKDF2");
class GeneralComponent extends Component
{
	var $settings;

	public function GeneralComponent()
	{
	}

	function ResizeImageContent($source,$host,$model_id,$model_name,$type,$mime_type,$width,$height,$resizeType = 'cropResize')
	{
		//SETTING
		$this->settings = Cache::read('settings', 'long');
		if(!$this->settings || (isset($_GET['debug']))) {

			$this->loadModel('Setting');
			$settings			=	$this->Setting->find('first');
			$this->settings		=	$settings['Setting'];
			Cache::write('settings', $this->settings, 'long');
		}

		$ext									=	pathinfo($source,PATHINFO_EXTENSION);
		$Content								=	ClassRegistry::Init("Content");

		$data									=	$Content->find("first",array(
														"conditions"	=>	array(
															"Content.model_id"		=>	$model_id,
															"Content.model"			=>	$model_name,
															"LOWER(Content.type)"	=>	strtolower($type),
														)
													));

		if(!empty($data))
		$Contents["Content"]["id"]				=	$data["Content"]["id"];
		$Contents["Content"]["model"]			=	$model_name;
		$Contents["Content"]["model_id"]		=	$model_id;
		$Contents["Content"]["host"]			=	$host;
		$Contents["Content"]["mime_type"]		=	$mime_type;
		$Contents["Content"]["cloud"]			=	"0";

		$path_content	=	$this->settings['path_content'];
			if(!is_dir($path_content))mkdir($path_content,0777);

		$path_model		=	$path_content. $model_name . "/";
			if(!is_dir($path_model)) mkdir($path_model,0777);

		$path_model_id	=	$path_model . $model_id . "/";
			if(!is_dir($path_model_id))
				mkdir($path_model_id,0777);

		//RESIZE
		App::import('Vendor','upload' ,array('file'=>'class.upload.php'));
		$path_content							=	$path_model_id.$model_id.'_'.$type.".".$ext;
		@unlink($path_content);
		
		$img 									=	new upload($source);
		$img->file_new_name_body   				=	$model_id.'_'.$type;
		if($resizeType == 'cropResize') {

			$img->image_resize          		=	true;
			$img->image_ratio_crop      		=	true;
			$img->image_y               		=	$height;
			$img->image_x               		=	$width;

		} else if($resizeType == 'resizeMaxWidth') {

			$img->image_resize          		=	true;
			$img->image_ratio_y        			= 	true;
			$img->image_x               		=	$width;
		}
		else if($resizeType == 'cropRatio')
		{
			$img->image_resize          		=	true;
			$img->image_ratio		      		=	true;
			$img->image_y               		=	$height;
			$img->image_x               		=	$width;
		}
		else if($resizeType == 'cropFill')
		{
			$img->image_resize          		=	true;
			$img->image_ratio_fill	      		=	true;
			$img->image_y               		=	$height;
			$img->image_x               		=	$width;
		}

		$img->process($path_model_id);
		$Contents["Content"]["type"]			=	$type;
		$Contents["Content"]["url"]				=	"contents/{$model_name}/{$model_id}/{$model_id}_{$type}.{$ext}";
		$Contents["Content"]["path"]			=	$path_content;

		if ($img->processed)
		{
			$size									=	getimagesize($path_content);
			$Contents["Content"]["width"]			=	$size[0];
			$Contents["Content"]["height"]			=	$size[1];
			$Content->create();
			$Content->save($Contents);
			return true;
		}
		return false;
	}
	
	function ResizeMultiImageContent($source,$host,$model_id,$fileArr,$model_name,$type,$mime_type,$width,$height,$resizeType = 'cropResize')
	{
		//SETTING
		$this->settings = Cache::read('settings', 'long');
		if(!$this->settings || (isset($_GET['debug']))) {

			$this->loadModel('Setting');
			$settings			=	$this->Setting->find('first');
			$this->settings		=	$settings['Setting'];
			Cache::write('settings', $this->settings, 'long');
		}

		$ext									=	pathinfo($source,PATHINFO_EXTENSION);
		$Content								=	ClassRegistry::Init("Content");

		$data									=	$Content->find("first",array(
														"conditions"	=>	array(
															"Content.model_id"		=>	$model_id,
															"Content.model"			=>	$model_name,
															"LOWER(Content.type)"	=>	strtolower($type),
														)
													));

		if(!empty($data))
		$Contents["Content"]["id"]				=	$data["Content"]["id"];
		$Contents["Content"]["model"]			=	$model_name;
		$Contents["Content"]["model_id"]		=	$model_id;
		$Contents["Content"]["host"]			=	$host;
		$Contents["Content"]["mime_type"]		=	$mime_type;
		$Contents["Content"]["cloud"]			=	"0";

		$path_content	=	$this->settings['path_content'];
			if(!is_dir($path_content))mkdir($path_content,0777);

		$path_model		=	$path_content. $model_name . "/";
			if(!is_dir($path_model)) mkdir($path_model,0777);

		$path_model_id	=	$path_model . $model_id . "/";
			if(!is_dir($path_model_id))
				mkdir($path_model_id,0777);

		//RESIZE
		App::import('Vendor','upload' ,array('file'=>'class.upload.php'));
		$path_content							=	$path_model_id.$model_id.'_'.$type.".".$ext;
		@unlink($path_content);
		
		$img 									=	new upload($source);
		$img->file_new_name_body   				=	$model_id.'_'.$fileArr.'_'.$type;
		if($resizeType == 'cropResize') {

			$img->image_resize          		=	true;
			$img->image_ratio_crop      		=	true;
			$img->image_y               		=	$height;
			$img->image_x               		=	$width;

		} else if($resizeType == 'resizeMaxWidth') {

			$img->image_resize          		=	true;
			$img->image_ratio_y        			= 	true;
			$img->image_x               		=	$width;
		}
		else if($resizeType == 'cropRatio')
		{
			$img->image_resize          		=	true;
			$img->image_ratio		      		=	true;
			$img->image_y               		=	$height;
			$img->image_x               		=	$width;
		}
		else if($resizeType == 'cropFill')
		{
			$img->image_resize          		=	true;
			$img->image_ratio_fill	      		=	true;
			$img->image_y               		=	$height;
			$img->image_x               		=	$width;
		}

		$img->process($path_model_id);
		$Contents["Content"]["type"]			=	$type;
		$Contents["Content"]["url"]				=	"contents/{$model_name}/{$model_id}/{$model_id}_{$fileArr}_{$type}.{$ext}";
		$Contents["Content"]["path"]			=	$path_content;

		if ($img->processed)
		{
			$size									=	getimagesize($path_content);
			$Contents["Content"]["width"]			=	$size[0];
			$Contents["Content"]["height"]			=	$size[1];
			$Content->create();
			$Content->save($Contents);
			return true;
		}
		return false;
	}
	
	function UploadContent($model_id,$model_name,$type,$source)
	{
		//SETTING
		$settings 	= Cache::read('settings', 'long');
		if(!$settings || (isset($_GET['debug']))) {

			$this->loadModel('Setting');
			$settings			=	$this->Setting->find('first');
			$settings			=	$settings['Setting'];
			Cache::write('settings', $settings, 'long');
		}
		
		$tmp_name							=	$source["name"];
		$tmp								=	$source["tmp_name"];
		$mime_type							=	$source["type"];
		$ext								=	pathinfo($tmp_name,PATHINFO_EXTENSION);
		$Content							=	ClassRegistry::Init("Content");
		$data								=	$Content->find("first",array(
													"conditions"	=>	array(
														"Content.model_id"		=>	$model_id,
														"Content.model"			=>	$model_name,
														"LOWER(Content.type)"	=>	strtolower($type),
													)
												));
		
		$path_tmp							=	ROOT.DS.'app'.DS.'tmp'.DS.'upload'.DS;
			if(!is_dir($path_tmp)) mkdir($path_tmp,0777);
			
		$ext								=	pathinfo($tmp_name,PATHINFO_EXTENSION);
		$tmp_file_name						=	md5(time());
		$tmp_images1_img					=	$path_tmp.$tmp_file_name.".".$ext;
												
		if(!empty($data))
			$Contents["Content"]["id"]			=	$data["Content"]["id"];	
							
		$Contents["Content"]["model"]		=	$model_name;
		$Contents["Content"]["model_id"]	=	$model_id;
		$Contents["Content"]["host"]		=	$settings["cms_url"];
		$Contents["Content"]["mime_type"]	=	$mime_type;
		$Contents["Content"]["cloud"]		=	"0";
		$Contents["Content"]["type"]		=	$type;
		$Contents["Content"]["url"]			=	"contents/{$model_name}/{$model_id}/{$model_id}_{$type}.{$ext}";
		$Contents["Content"]["path"]		=	"contents/{$model_name}/{$model_id}/{$model_id}_{$type}.{$ext}";
		
		$path_content	=	$settings['path_content'];
			if(!is_dir($path_content))mkdir($path_content,0777);
		
		$path_model		=	$path_content. $model_name . "/";
			if(!is_dir($path_model)) mkdir($path_model,0777);
			
		$path_model_id	=	$path_model . $model_id . "/";
			if(!is_dir($path_model_id)) mkdir($path_model_id,0777);
			
		$path_content						=	$path_model_id.$model_id.'_'.$type.".".$ext;
		$upload 							=	move_uploaded_file($tmp,$path_content);
		
		
		
		if($upload)
		{
			$Content->create();
			$Content->save($Contents);
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	function ClearCache($apiUrl)
	{
		$ch     =   curl_init();
		curl_setopt($ch, CURLOPT_URL,$apiUrl);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$out = curl_exec($ch);
		curl_close($ch);
		return $out;
	}
	
	function DeleteContent($model_id,$model_name)
	{
		//SETTING
		$this->settings = Cache::read('settings', 'long');
		if(!$this->settings || (isset($_GET['debug']))) {

			$Setting			=	ClassRegistry::Init("Setting");
			$settings			=	$Setting->find('first');
			$this->settings		=	$settings['Setting'];
			Cache::write('settings', $this->settings, 'long');
		}

		$Content							=	ClassRegistry::Init("Content");
		$data								=	$Content->find("first",array(
													"conditions"	=>	array(
														"LOWER(Content.model_id)"		=>	$model_id,
														"LOWER(Content.model)"			=>	$model_name
													)
												));
		if(!empty($data))
		{
			$path_model_id					=	$this->settings["path_content"].ucfirst($model_name)."/".$model_id."/";
			$this->RmDir($path_model_id);
			$Content->deleteAll(array(
				"LOWER(Content.model_id)"		=>	$model_id,
				"LOWER(Content.model)"			=>	$model_name
			));
		}
	}

	function PostCurl($url,$post,$header)
	{
		//TRY TO SEND PARAMETER
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		$out = curl_exec($ch);
		curl_close($ch);
		return $out;
	}

	function copy_directory( $source, $destination ) {
		if ( is_dir( $source ) ) {
			@mkdir( $destination );
			$directory = dir( $source );
			while ( FALSE !== ( $readdirectory = $directory->read() ) ) {
				if ( $readdirectory == '.' || $readdirectory == '..' ) {
					continue;
				}
				$PathDir = $source . '/' . $readdirectory;
				if ( is_dir( $PathDir ) ) {
					$this->copy_directory( $PathDir, $destination . '/' . $readdirectory );
					continue;
				}
				copy( $PathDir, $destination . '/' . $readdirectory );
			}

			$directory->close();
		}else {
			copy( $source, $destination );
		}
	}


	function RmDir($filepath){

		if (is_dir($filepath) && !is_link($filepath))
		{
			if ($dh = opendir($filepath))
			{
				while (($sf = readdir($dh)) !== false)
				{
					if ($sf == '.' || $sf == '..')
					{
						continue;
					}
					$filepath = (substr($filepath, -1) != "/")? $filepath."/":$filepath;
					if (!$this->RmDir($filepath.$sf))
					{
						echo ($filepath.$sf.' could not be deleted.');
					}
				}
				closedir($dh);
			}
			return rmdir($filepath);
		}
		return unlink($filepath);
	}

	function getContent($destination, $source){
		$filename 	= $destination;
		$handle 	= fopen("$source", "rb");
		if($handle)
		{
	  		$somecontent = stream_get_contents($handle);
	  		fclose($handle);
	  		$handle = fopen($filename, 'wb');

	  		if($handle)
			{
				if (fwrite($handle, $somecontent) === FALSE)
				{
		   			$confirm = false;
		   			exit;
				}
				$confirm = true;
				fclose($handle);
	  		}
			else
			{
		 		$confirm = false;
		 		exit;
	  		}
		}
		return $confirm;
	}

	function GetImageFromUrl($destination, $source)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch,CURLOPT_URL,$source);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		
		$result	=	curl_exec($ch);
		curl_close($ch);

		$handle = fopen($destination, 'wb');
		if($handle)
		{
			if (fwrite($handle, $result) === FALSE)
			{
				return false;
			}
			fclose($handle);
			return true;
		}
		else
		{
			return false;
		}
	}

	function checkExtContent($type,$code,$prefix="")
	{
		$dest	=	Configure::read('PATH_CONTENT')."images/".$type."/".$code."/";
		$prefix	=	empty($prefix) ? $code : $code.$prefix;

		if(!is_dir($dest))
		{
			return false;
		}
		else
		{
			if ($dh = opendir($dest))
			{
				while (($sf = readdir($dh)) !== false)
				{
					if ($sf == '.' || $sf == '..')
					{
						continue;
					}
					$sep	=	explode(".",$sf);
					if($sep[0]==$prefix)
					{
						return $sep[1] ;
					}
				}
				closedir($dh);
			}
			else
			{
				return false;
			}
		}
	}

	function seoUrl($string)
	{
		//Unwanted:  {UPPERCASE} ; / ? : @ & = + $ , . ! ~ * ' ( )
		$string = strtolower($string);
		//Strip any unwanted characters
		$string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
		//Clean multiple dashes or whitespaces
		$string = preg_replace("/[\s-]+/", " ", $string);
		//Convert whitespaces and underscore to dash
		$string = preg_replace("/[\s_]/", "-", $string);

		return $string;
	}

	function my_encrypt($string, $key="aby") {
		$result = '';
		for($i=0; $i<strlen($string); $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key))-1, 1);
			$char = chr(ord($char)+ord($keychar));
			$result.=$char;
		}

		return base64_encode($result);
	}

	function my_decrypt($string, $key="aby") {
		$result = '';
		$string = base64_decode($string);

		for($i=0; $i<strlen($string); $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key))-1, 1);
			$char = chr(ord($char)-ord($keychar));
			$result.=$char;
		}
		return $result;
	}
	
	function sendPushNotification($fields)
	{
		//SETTING
		$this->settings = Cache::read('settings', 'long');
		if(!$this->settings || (isset($_GET['debug']))) {

			$Setting			=	ClassRegistry::Init('Setting');
			$settings			=	$Setting->find('first');
			$this->settings		=	$settings['Setting'];
			Cache::write('settings', $this->settings, 'long');
		}
		
        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';

        $headers = array(
            'Authorization: key=' . $this->settings['firebase_api_key'],
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }

        // Close connection
        curl_close($ch);

        return $result;
    }
}