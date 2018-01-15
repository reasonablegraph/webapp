<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class WorkerSample extends Command {

	protected $name = 'worker:sample';
	protected $description = 'Run sample worker (reverse string).';
	protected $reverse_fn_fast;
	protected $edit_step3;

	public function __construct() {
		parent::__construct();

		$this->reverse_fn_fast = function ($job) {
			$this->info("Received job: " . $job->handle() . ", for function: reverse_fn_fast, and workload size: " . $job->workloadSize());
			return strrev($job->workload());
		};

	}

	public function fire() {
		$this->info('Starting sample worker ...');
		$this->info('Worker locale: ' . App::getLocale());

		$gmworker= new GearmanWorker();
		$gmworker->addServer();
		$gmworker->addFunction("reverse", $this->reverse_fn_fast);

		$this->info("Waiting for job...");
		while($gmworker->work()) {
			if ($gmworker->returnCode() != GEARMAN_SUCCESS) {
				$this->info("return_code: " . $gmworker->returnCode());
				break;
			}
		}
	}

}