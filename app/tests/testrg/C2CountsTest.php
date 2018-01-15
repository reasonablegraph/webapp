<?php

require_once 'GenericRGTestCase.php';

/**
 * @runTestsInSeparateProcesses
 */
class C2CountsTest extends GenericRGTestCase {

	public function setUp() {
		parent::setUp();
	}

	public function testCounts() {
		Log::info("T: testCounts");

		$okVerticesCount = 13;
		$okEdgesCount = 21;

		$this->test->checkCounts($okVerticesCount, $okEdgesCount);
	}

}