<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class LogInfoCommand extends Command {

	protected $name = 'logging:log';
	protected $description = 'Log::info $ARG1';

	public function __construct(){
		parent::__construct();
	}


	protected function getOptions() {
		return array (
			array (
				'color', // name
				null, // shortcut
				InputOption::VALUE_OPTIONAL, // mode
				'default, red, green, magenta, blue, yellow', // description
				'default' // defaultValue
			),
		);
	}

	protected function getArguments() {
		return array(
			array('message', InputArgument::REQUIRED, 'message to log')
		);
	}

	public function fire() {
		$msg = $this->argument('message');
		$color = $this->option('color');
		//$this->info($msg);
		//Log::info($color);
		if ($color == 'red'){
			PUtil::logRed($msg);
		} elseif ($color == 'yellow'){
			PUtil::logYellow($msg);
		} elseif ($color == 'magenta'){
			PUtil::logMagenta($msg);
		} elseif ($color == 'blue'){
			PUtil::logBlue($msg);
		} elseif ($color == 'green'){
			PUtil::logGreen($msg);
		} else {
			Log::info($msg);
		}

	}


}