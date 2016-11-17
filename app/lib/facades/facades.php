<?php

use Illuminate\Support\Facades\Facade;

class ArcTemplateEngine extends Facade {

	/**
	 * @return string
	 */
	protected static function getFacadeAccessor() {  return 'arc-template-engine'; }

	
}






class ArcApp extends Facade {
	
	/**
	 * @return string
	 */
	protected static function getFacadeAccessor() {  return 'arc-app'; }
	
	
}

