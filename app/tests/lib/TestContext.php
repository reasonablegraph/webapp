<?php

class TestContext {

	private $context;

	public function __construct($init_context) {
		$this->context = $init_context;
	}

	public function get($key,$defaultValue = null) {
		return (isset($this->context[$key])) ? $this->context[$key] : $defaultValue;
	}

	public function has($key) {
		return isset($this->context[$key]);
	}

	public function set($key, $val) {
		$this->context[$key] = $val;
	}

	public function dump() {
		Log::info($this->context);
	}

	public function asArray() {
		return $this->context;
	}

}
