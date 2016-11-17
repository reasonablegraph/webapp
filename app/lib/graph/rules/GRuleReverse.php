<?php 



class GRuleReverse   extends AbstractGruleProcessVertice implements  GRule {
	
	
	/**
	 *
	 * @param GVertex $v
	 */
	protected function processVertex( $v){
// 		$context = $this->context;
		
// 		$id = $v->persistenceId();
// 		//GGraphUtil::dumpVertex($v);
// 		$g = $v->graph();
	
// 		$title = $v->getPropertyValue('dc:title:');
// 		$urnStr = $v->urnStr();
		
// 		$edges = $v->getEdges(GDirection::OUT);
// 		foreach ($edges as $e){
// 			$el = $e->element();
// 			if (!PUtil::strBeginsWith($el, 'inferred:')
// 					&& !PUtil::strBeginsWith($el, 'reverse:')
// 					&& !PUtil::strBeginsWith($el, 'dc:contributor:')
// 					){
// 				$element = 'reverse:' . $el;
// 				$to = $e->getVertexTO()->urnStr();
// 				$g->addEdge($to, $urnStr, $element,true,null,$title);
// 				$context->addDebugMessage("ADD: " . $to . ' -> ' . $urnStr . ' : ' . $element );
// 			}
		
// 		}
		
	}
	
	
	
	
}




?>