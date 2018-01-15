<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class ClientSample extends Command {

	protected $name = 'client:sample';
	protected $description = 'Run sample client job (reverse string).';

	public function __construct(){
		parent::__construct();
	}

	protected function getArguments() {
		return array(
			array('strVal', InputArgument::REQUIRED, 'The string value to be reversed.'),
		);
	}

	public function fire() {

		$gmclient = new GearmanClient();
		$gmclient->addServer();

		do {
			$result = $gmclient->doNormal("reverse", $this->argument('strVal'));

			// Check for various return packets and errors.
			switch($gmclient->returnCode()) {
				case GEARMAN_WORK_DATA:
					$this->info("Data: $result");
					break;
				case GEARMAN_WORK_STATUS:
					list($numerator, $denominator)= $gmclient->doStatus();
					$this->info("Status: $numerator/$denominator complete");
					break;
				case GEARMAN_WORK_FAIL:
					$this->info("Failed");
					exit;
				case GEARMAN_SUCCESS:
					$this->info("Success: $result");
					break;
				default:
					$this->info("RET: " . $gmclient->returnCode());
					exit;
			}
		}
		while($gmclient->returnCode() != GEARMAN_SUCCESS);

	}

}