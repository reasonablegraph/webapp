<?php

class GruleDumpGraphCmd implements GCommand {

	private $title;
	private $graph;
	private $c;

	public function __construct($c, $graph ,$title) {
		$this->graph = $graph;
		$this->title = $title;
		$this->c = $c;
	}


	public function execute($context){

		$graph = $this->graph;
		$c = $this->c;
		Log::info("##: GruleDumpGraphCmd: " . $c);
		$title = null;
		if (!empty($this->title)){
			$title = $this->title;
		} else {
			$title = sprintf('g%s',$c);
		}

		$out = "\n";
		$out .="=================================================================================================\n";
		$out .="DUMP GRAPH $c: \n";
		$out .=$this->title;
		$out .="\n";
		$out .="=================================================================================================\n";
		$out .= GGraphUtil::dump1($graph,false);
		$out .="=================================================================================================\n";

		//Log::info($out);
		$file1 = sprintf('/tmp/g%s.dot',$c);
		$file2 = sprintf('/tmp/g%s.txt',$c);
		GGraphUtil::dumpDOT($graph,array('file'=>$file1,'label'=>$title,'neighbourhoodFlag'=>false,'inferredFlag'=>true,'graph_dump_file'=>$file2));

	}


}


class GRuleDumpGraph  extends AbstractBaseRule implements  GRule {

	private $graph_index = null;
	private $title = null;
	private $commandFlag = false;

	/**
	 * @param GRuleContextR $context
	 */
	public function __construct($context,$args) {
		if (!empty($args)){
			if (isset($args['graph_index'])){
				$this->graph_index = $args['graph_index'];
			}
			if (isset($args['title'])){
				$this->title = $args['title'];
			}

			if (isset($args['commandFlag'])){
				$this->commandFlag = $args['commandFlag'];
			}
		}
		parent::__construct($context);
	}

	public function execute(){

		$context = $this->context;
		//Log::info('GRuleDumpGraph');
		$c = $context->get('dump_graph_counter',0);
		$c+=1;
		$context->put('dump_graph_counter', $c);

		if ($this->graph_index != null){
			$graph = $context->getGraph($this->graph_index);
		} else {
			$graph = $context->graph();
		}

		$cmd = new GruleDumpGraphCmd($c, $graph,$this->title);
		if ($this->commandFlag){
			$context->putCommand("DUMP-GRAPH", $cmd);
		} else {
			$cmd->execute($context);
		}

// 		$out = "\n";
// 		$out .="=================================================================================================\n";
// 		$out .="DUMP GRAPH $c: \n";
// 		if (!empty($this->title)){
// 			$out .=$this->title;
// 			$out .="\n";
// 		}
// 		$out .="=================================================================================================\n";
// 		$out .= GGraphUtil::dump1($graph,false);
// 		$out .="=================================================================================================\n";

// 		Log::info($out);
// 		$file = sprintf('/tmp/g%s.dot',$c);
// 		GGraphUtil::dumpGraphviz($graph,$file);

	}

}

