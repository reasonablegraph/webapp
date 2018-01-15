<?php
class GRuleDFItem extends AbstractGruleProcessVertice {

	/**
	 *
	 * @param GVertex $v
	 */
	protected function labelForManifestation($v) {
		// $context = $this->context;
		$graph = $v->graph ();
	}

	/**
	 *
	 * @param GVertex $v
	 */
	protected function processVertex($v) {
		$context = $this->context;
		/* @var $graph GGraph */
		$graph = $context->graph();


		//XRISI MONO GIA ADD DIGITAL-ITEM MESA APO MANIFESTATION
		$tmpe=$v->getFirstEdge(GDirection::IN,'ea:manif:digital-item');
		if (!empty($tmpe)){

			$vf=$tmpe->getVertexFrom();
			//($v1UrnStr, $v2UrnStr, $element, $derivative = true ,$deps = null,$label=null);
			$graph->addEdge($v->urnStr(),$vf->urnStr(),'ea:artifact-of:',false);

			$graph->removeEdge($tmpe->vkey(),true);
		}


		//'ea:manif:digital-item'
		//	$this->inferenceVA_THEN_AV('ea:artifact-of:', 'reverse:ea:artifact-of:');

		$r1 = $this->inferenceVAB_THEN_VB ( 'ea:artifact-of:', 'ea:work:', 'ea:artifact-of:' );
		$r2 = $this->inferenceVAB_THEN_VB ( 'ea:artifact-of:', 'inferred:ea:work:', 'ea:artifact-of:' );

// 		$r1 = $this->inferenceVAB_THEN_BV ( 'ea:artifact-of:', 'ea:work:', 'ea:neighbor:' );
// 		$r2 = $this->inferenceVAB_THEN_BV ( 'ea:artifact-of:', 'inferred:ea:work:', 'ea:neighbor:' );

		$ra = $this->inferenceVA_THEN_AV('ea:artifact-of:', 'reverse:ea:artifact-of:');

		//ea:artifact-of:primary
		$edges = $v->getEdges(GDirection::OUT,'ea:artifact-of:');
		foreach ($edges as $e){
			if (!$e->isInferred()){
				$to = $e->getVertexTO();
				$graph->addEdge($v->urnStr(), $to->urnStr() , 'ea:artifact-of:primary');
				break;
			}
		}

		$edges = $v->getEdges(GDirection::BOTH);
		if (count($edges) == 0){
			$v->addFlag('ORPHAN');
		}else{
			$v->removeFlag('ORPHAN');
		}


	}
}
