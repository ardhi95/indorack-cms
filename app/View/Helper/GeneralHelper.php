<?php
class GeneralHelper extends AppHelper
{
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

	function IsEmptyVal($string,$default_val="-") {
		if(strlen($string)>0 && trim($string) != "" && trim($string) != "<br>")
		{
			return $string;
		}
		return $default_val;
	}

	function StatusContent($status,$options=array()) {

		$statusLabel			=	'';
		$option					=	array("hide"	=> __("Not Active"), "show" =>	__("Active"));
		$option					=	array_merge($option,$options);
		switch($status)
		{
			case "1":
				$statusLabel	=	'<span class="label label-success label-form">'.$option["show"].'</span>';
				break;
			case "0";
				$statusLabel	=	'<span class="label label-danger label-form">'.$option["hide"].'</span>';
				break;
			default:
				$statusLabel	=	'<span class="label label-warning label-form">'.__('Unknown').'</span>';
				break;
		}

		return $statusLabel;
	}
}
?>
