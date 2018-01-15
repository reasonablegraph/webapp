<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

//use Symfony\Component\Console\Input\InputArgument;

class GraphResetCommand extends Command {

	protected $name = 'graph:reset';
	protected $description = 'graph reset';

	public function __construct() {
		parent::__construct();
	}
//	protected function getArguments() {
//		return array(
//			array('example', InputArgument::REQUIRED, 'An example argument.'),
//		);
//	}

	protected function getOptions() {
		return array(
				array('reset-verbose-level', null, InputOption::VALUE_OPTIONAL, 'verbose: ', '1')
		);
	}

	public function fire() {

		$rlock = new GRuleEngineLock();
		$rlock->lock(null);

		$context = GGraphUtil::graphResetFull();

		$rlock->release();

		$verbose_level = $this->option('reset-verbose-level');

		if ($verbose_level != '1'){
			return;
		}

		$graph=  $context->graph();
		$this->info("------------------------------------------");
		$this->info ("DEBUG MESSAGES:");
		$this->info ("------------------------------------------");
		$dms = $context->getDebugMessages();
		foreach ( $dms as $dm ) {
			$this->info ("  $dm");
		}
		$this->info ("");
		$this->info ("");

		$this->info ("------------------------------------------");
		$this->info ("INFO MESSAGES:");
		$this->info ("------------------------------------------");
		$ims = $context->getMessages();
		foreach ( $ims as $im ) {
			$this->info ("  $im");
		}
		$this->info ("");
		$this->info ("");

		$this->info ("------------------------------------------");
		$this->info ("INFERED EDGES:");
		$this->info ("------------------------------------------");

		$des2 = $graph->getInferredEdges();
//		foreach ( $des2 as $e ) {
//			if ($verbose) {
//				$this->info ($e);
//				$this->info ("");
//			}
//		}
		$this->info ("------------------------------------------");


		$this->info ("------------------------------------------");
		$this->info ("EDITED PROPERTIES:");
		$this->info ("------------------------------------------");

		$this->info ("------------------------------------------");
		$eps = $context->getEditPropUrns();
		foreach ( $eps as $urnStr ) {
			$this->info (" $urnStr :");
			$elements = $context->getEditProps($urnStr);
			foreach ( $elements as $el ) {
				$this->info (" >> $el ");
			}
		}
		$this->info ("------------------------------------------");


	}

}
