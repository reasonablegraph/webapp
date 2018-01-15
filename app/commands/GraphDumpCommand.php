<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

//use Symfony\Component\Console\Input\InputArgument;

class GraphDumpCommand extends Command {

	protected $name = 'graph:dot';
	protected $description = 'graph dot';

	public function __construct() {
		parent::__construct();
	}
//	protected function getArguments() {
//		return array(
//			array('example', InputArgument::REQUIRED, 'An example argument.'),
//		);
//	}

//--neighbourhood=true /false/item_id
//--inferred=true /false

	protected function getOptions() {
		//array($name, $mode, $description, $defaultValue)
		return array(
			array('neighbourhood', null, InputOption::VALUE_OPTIONAL, 'true /false/item_id', 'false'),
			array('inferred', null, InputOption::VALUE_OPTIONAL, 'true /false/', 'false'),
			array('onlytestnodes', null, InputOption::VALUE_OPTIONAL, 'true /false/', 'false')

		);
	}

	public function fire() {

		Log::info('GraphController:graphviz');
		$graph = null;
		$inferred_flag =  $this->option('inferred','false');
		$neighbourhood = $this->option('neighbourhood','false');
		$only_test_nodes= $this->option('onlytestnodes','false');

		$inferred_flag = ($inferred_flag =='true') ? true : false;
		$only_test_nodes = ($only_test_nodes == 'true') ? true : false;

//		Log::info("1.neighbourhood: " . $neighbourhood  . ' ::: ' . ($neighbourhood ? 'TRUE' : 'FALSE'));
//		Log::info("1.inferred     : " . $inferred_flag . ' ::: ' . ($inferred_flag ? 'TRUE' : 'FALSE'));


		$graph = GGraphIO::loadGraph(null,$inferred_flag,$only_test_nodes);
		GGraphUtil::dumpGraphviz($graph,null,$inferred_flag,$neighbourhood);

	}

}
