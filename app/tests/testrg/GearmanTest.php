<?php

require_once __DIR__ . '/GenericRGTestCase.php';

use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @runTestsInSeparateProcesses
 */
class GearmanTest extends GenericRGTestCase {

	public function setUp() {
		parent::setUp();
	}

	public function testGearman() {
		$strVal = "Hello!";
		Log::info("T: testGearman with strVal: " . $strVal);

		$output = new BufferedOutput();
		$exitCode = Artisan::call('client:sample', ['strVal' => $strVal], $output);
		$fout = trim($output->fetch());

		Log::info("artisan exitCode: " . $exitCode . ", output: " . $fout);

		$this->_assertSame($fout, "Success: " . strrev($strVal));
	}

}