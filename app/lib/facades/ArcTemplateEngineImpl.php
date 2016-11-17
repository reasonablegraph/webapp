<?php

use Handlebars\Handlebars;

class ArcTemplateEngineImpl {

	private $engine;
	//private $mustache;
	private $handlebars;

	function __construct() {
		$this->handlebars = new Handlebars();
		$this->engine =  new Mustache_Engine();
	}


	function render($template, $context = array()){
		return $this->engine->render($template, $context);
	}

	function renderLine($template, $context = array()){
		//return str_replace("\n",' ', trim($this->engine->render($template, $context)));
		return preg_replace('/\s+/',' ',str_replace("\n",' ', trim($this->engine->render($template, $context))));
	}


	function renderHandlebars($template, $context = array()){
		return $this->handlebars->render($template, $context);
	}


	function renderLineHandlebars($template, $context = array()){
		return preg_replace('/\s+/',' ',str_replace("\n",' ', trim($this->handlebars->render($template, $context))));
	}


}
