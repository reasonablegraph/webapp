<?php
class GRuleWEMCreate  extends AbstractGruleProcessVertice implements GRule {

// 	protected function init(){
// 	}

	/**
	 * @param GVertex $v
	 */
	protected function processVertex( $v){
		/* @var $context GRuleContextR */
		$context = $this->context;
		/* @var $graph GGraph */
		$graph = $this->context->graph();
		$expresions = $v->getEdges(GDirection::OUT,array('ea:expression:tmp'));
		$exprEdge = count($expresions) > 0 ? current($expresions) : null;
		$exprEdge_id = !empty($expr) ? $expr->persistenceId() : null;

		$manifs =  $v->getEdges(GDirection::OUT,array('ea:manifestation:tmp'));
		$manifEdge = count($manifs) > 0 ? current($manifs) : null;
		$manifEdge_id = !empty($manif) ? $manif->persistenceId() : null;

		if (!empty($exprEdge) && ! empty($manifEdge)){
			$expr = $exprEdge->getVertexTO();
			$manif = $manifEdge->getVertexTO();
			$context->addNewEdge($manif->urnStr(), $expr->urnStr(), 'ea:work:',false);
			$graph->removeEdge($manifEdge->vkey(),true);
			$manifEdge = null;
		}

		if (!empty($exprEdge)){
			$graph->renameEdge($exprEdge->vkey(),'ea:expression:' );
		}
		if (!empty($manifEdge)){
			$graph->renameEdge($manifEdge->vkey(),'ea:workOf:' );
		}

	}


}


