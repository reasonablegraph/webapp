<?php

use Illuminate\Support\Facades\Log;


class TestCase extends Illuminate\Foundation\Testing\TestCase {

//	public function run(PHPUnit_Framework_TestResult $result = null) {
//		$this->setPreserveGlobalState(false);
//		parent::run($result);
//	}
	/**
	 * Creates the application.
	 *
	 * @return \Symfony\Component\HttpKernel\HttpKernelInterface
	 */
	public function createApplication()
	{

		$unitTesting = true;

		$testEnvironment = 'testing';

		return require __DIR__.'/../../bootstrap/start.php';
	}

}
