<?php

require_once 'GenericRGTestCase.php';

/**
 * @runTestsInSeparateProcesses
 */
class C1CountsTest extends GenericRGTestCase {

	public function setUp() {
		parent::setUp();
	}

	public function testCounts() {
		Log::info("T: testCounts");

		$okVerticesCount = 10;
		$okEdgesCount = 16;

		$this->test->checkCounts($okVerticesCount, $okEdgesCount);
	}

}