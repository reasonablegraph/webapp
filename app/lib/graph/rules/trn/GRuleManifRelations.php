<?php 

class GRuleManifRelations extends AbstractGruleProcessVertice implements GRule {
	
	
	protected function init(){
	}
	
	
	/**
	 * @param GVertex $v
	 */
	protected function processVertex($v){
	
		$graph = $v->graph();
		//$context->addDebugMessage(" process: " . $v);
		//$dumpV  = GGraphUtil::dumpVertex($v,false);
		$v->getEdges(GDirection::OUT,'ea:work:');
		
		
	}
	
	
}