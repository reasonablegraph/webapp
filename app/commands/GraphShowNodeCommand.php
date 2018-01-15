<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GraphShowNodeCommand extends Command {

	protected $name = 'graph:show-node';
	protected $description = 'graph show node';

	public function __construct() {
		parent::__construct();
	}

	protected function getArguments() {
		return array(
			array('id', InputArgument::REQUIRED, 'The node item id.'),
			array('depth', InputArgument::OPTIONAL, 'The required depth', 2),
		);
	}

	protected function getOptions() {
		return array(
			array('neighbourhood', null, InputOption::VALUE_OPTIONAL, 'true/false/item_id', 'false'),
			array('inferred', null, InputOption::VALUE_OPTIONAL, 'true/false', 'true'),
			array('onlytestnodes', null, InputOption::VALUE_OPTIONAL, 'true/false', 'false'),
      array('direction', null, InputOption::VALUE_OPTIONAL, 'in/out/both', 'both')

		);
	}

	public function fire() {

		// options
		$inferred_flag = $this->option('inferred');
		$neighbourhood = $this->option('neighbourhood');
		$only_test_nodes = $this->option('onlytestnodes');
    $arg_direction =  strtolower($this->option('direction'));


    $direction = GDirection::BOTH;
    if ($arg_direction == 'in'){
      $direction = GDirection::IN;
    } elseif ($arg_direction == 'out'){
      $direction = GDirection::OUT;
    }

    //echo "DIRECTION " . $direction  . "\n";


		$inferred_flag = ($inferred_flag == 'true') ? true : false;
		$neighbourhood = ($neighbourhood == 'true') ? true : false;
		$only_test_nodes = ($only_test_nodes == 'true') ? true : false;

		// arguments
		$id_arg = intval($this->argument('id'));
		$depth_arg = intval($this->argument('depth'));


		/* @var $graph GGraph */
		$graph = GGraphIO::loadGraph(null, $inferred_flag, $only_test_nodes);
		$root = $graph->getVertex('oi:' . $id_arg);
		if (empty($root)){
		  trigger_error("NODE WITH ID: $id_arg NOT EXISTS");
    }
		$root->setAttribute(GGraphUtil::DUMPGRAPHVIZ, true);
    $root->setAttribute('GRAPHVIZ_ROOT', true);

    $nc = 1;
    //traverseBF($root, $maxDistance, $handler, $elements = null, $direction=GDirection::OUT, $vertexFilter = null, $traverseInferred = true) {
		$graph->traverseBF($root, $depth_arg, function ($c, $vertex, $edge, $distance) use (&$nc) {
      $nc+=1;
			/* @var $vertex GVertex */
			/* @var $edge GEdge */
			//echo ('a/a: ' . $c . " distance: " . $distance . " vertex: " . $vertex->id() . " parent: " . $edge->getVertexFrom()->id() . "\n");
			$vertex->setAttribute(GGraphUtil::DUMPGRAPHVIZ, $distance);
      $vertex->setAttribute('GRAPHVIZ_TO', $c);
			return true;
		},null, $direction, null,false );


		$glabel="root: "  . $id_arg .  " nodes count: " . $nc;
		GGraphUtil::dumpGraphviz($graph,null, $inferred_flag, $neighbourhood, true, $glabel, null, true);
	}

}

