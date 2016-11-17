<?php

class LUtil {

	public static function log_var_dump($value){
		ob_start();
		var_dump($value);
		Log::info(ob_get_clean());
	}

}


class PArray extends ArrayObject {

	// 	public function __construct($array = array()){
	// 			parent::__construct($array);
	// 	}

	public function appendIfNotEmpty($value){
		if (!empty($value)){
			$this->append($value);
		}
	}

}
