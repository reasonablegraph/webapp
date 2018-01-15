<?php

require_once 'GenericRGTestCase.php';

/**
 * @runTestsInSeparateProcesses
 */
class ContextTest extends GenericRGTestCase {

	public function setUp() {
		parent::setUp();
	}

	public function test1() {
		Log::info("T: test1: dump context");
		$this->test->getContext()->dump();
	}

	/**
	 * @depends test1
	 */
	public function test2() {
		Log::info("T: test2: write to context");
		$context = $this->test->getContext();
		$context->set('FROM_PHP_TEST','OK');

		$this->test->assertSame(1,1," kook lala minima");
		$this->_assertSame(1,1," kook lala minima");
	}

	/**
	 * @depends test2
	 */
	public function test3() {
		Log::info("T: test3: dump context");
		$this->test->getContext()->dump();
	}

}