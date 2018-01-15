<?php


class  GRuleRemoveInference extends AbstractBaseRule implements  GRule {

	public function execute(){
//		error_log("REMOVe INFERENCE");
		Log::info("REMOVe INFERENCE");
		$graph = $this->context->graph();
		$graph->removeInferredEdges();
	}

}


