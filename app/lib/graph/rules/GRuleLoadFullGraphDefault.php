<?php

/**
 *
 * @author kostas
 *
 */
class  GRuleLoadFullGraphDefault  extends AbstractBaseRule implements  GRule {


	public function execute(){
		$context = $this->context;

		$graph = $this->context->graph();
		GGraphIO::loadGraph($graph);

		if ($context->getDebugFlag()){
			$count = $graph->countVertices();
			//Log::info("LOAD $count vertices");
			$context->addDebugMessage("LOAD $count vertices");
		}
	}



}