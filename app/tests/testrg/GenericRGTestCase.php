<?php

require_once 'app/tests/lib/TestRGHelper.php';

//use Monolog\Logger;
//use Monolog\Handler\StreamHandler;

class GenericRGTestCase extends TestCase {

	/* @var $graph GGraphO */
	protected $graph;
	protected $stack;
	/* @var $test TestRGHelper */
	protected $test;

	public function setUp() {
		parent::setUp();
		//Log::info("T: setUp");

		// log manipulation
		Log::useFiles('/tmp/laravel-test.log'); // notice: http://stackoverflow.com/questions/32256642/laravel-log-usefiles-method-is-making-log-write-in-multiple-files
		//$monolog = Log::getMonolog();
		//$monolog->pushHandler(new StreamHandler(storage_path() . '/logs/laravel-test.log', Logger::WARNING));

		Log::info("CURENT USER: <<" . getmyuid() . ">>");

		// initializations
		$this->graph = GGraphIO::loadGraph();
		$this->stack = [];
		$this->test = new TestRGHelper($this->graph, $this);
	}

	public function tearDown() {
		$this->test->writeContext();
		$hasFailures = $this->contextGet('PHPUNIT_HAS_FAILURES',false);
		if ($hasFailures) {TestCase::fail('test has failures');}
		parent::tearDown();
	}


	//// CONTEXT DELEGATIONS ////
	public function getContext(){
		return $this->test->getContext();
	}

	public function contextSet($key, $value) {
		return $this->test->contextSet($key, $value);
	}

	public function contextGet($key) {
		return $this->test->contextGet($key);
	}

	public function contextHas($key) {
		$this->test->contextHas($key);
	}

	public function contextDump() {
		$this->test->contextDump();
	}


	//// ASSERT DELEGATIONS ////

	public function _assertSame($expected, $actual, $message = '') {
		return $this->test->assertSame($expected, $actual, $message);
	}

	public function _assertNotSame($expected, $actual, $message = '') {
		return $this->test->assertNotSame($expected, $actual, $message);
	}

	public function _assertNull($actual, $message = '') {
		return $this->test->assertNull($actual, $message);
	}

	public function _assertNotNull($actual, $message = '') {
		return $this->test->assertNotNull($actual, $message);
	}

	public function _assertCount($expectedCount, $haystack, $message = '') {
		return $this->test->assertCount($expectedCount, $haystack, $message);
	}

	public function _assertEmpty($actual, $message = '') {
		return $this->test->assertEmpty($actual, $message);
	}

	public function _assertNotEmpty($actual, $message = '') {
		return $this->test->assertNotEmpty($actual, $message);
	}

	public function _assertTrue($condition, $message = '') {
		return $this->test->assertTrue($condition, $message);
	}

	public function _assertFalse($condition, $message = '') {
		return $this->test->assertFalse($condition, $message);
	}

	public function  _assertLessThan($expected, $actual, $message = ''){
		return $this->test->assertLessThan($expected, $actual,$message);
	}
	public function  _assertLessThanOrEqual($expected, $actual, $message = ''){
		return $this->test->assertLessThanOrEqual($expected, $actual, $message);
	}


}