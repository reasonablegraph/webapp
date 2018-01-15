<?php

require_once 'GenericRGTestCase.php';

/**
 * @runTestsInSeparateProcesses
 */
class C3CountsTest extends GenericRGTestCase {

	public function setUp() {
		parent::setUp();
	}

	public function testCounts() {
		Log::info("T: testCounts");

		$okVerticesCount = 15;
		$okEdgesCount = 28;

		$this->test->checkCounts($okVerticesCount, $okEdgesCount);
	}

}