<?php
class GRuleDFItem2 extends AbstractGruleProcessVertice implements GRule {

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
		//$this->context->addDebugMessage("GRuleDFItem2 process: " . $v);

		$urnStr = $v->urnStr();


		// IF   (V)-ea:artifact-of:->(A) , (B)-ea:artifact-of:->(A)   THEN  (V)-ea:neighbor->(B)
		//ea:neighbor:
		$edges = array();
		$as1  = $v->getVertices(GDirection::OUT,'ea:artifact-of:');
		foreach ($as1 as $a1){
			$a1UrnStr = $a1->urnStr();
			//$this->context->addDebugMessage("#1:  " .$a1->urnStr());
			$as2  = $a1->getVertices(GDirection::IN,'ea:artifact-of:');
			foreach ($as2 as $a2){
				//$this->context->addDebugMessage("##2:  " .$a2->urnStr());
				$a2UrnStr = $a2->urnStr();
				if ($urnStr != $a2UrnStr){
					if (!$v->hasEdge(GDirection::BOTH, 'ea:neighbor:', $a2UrnStr)){
						//$this->context->addDebugMessage("##2:  " . $urnStr . ' --> ' . $a2UrnStr);
						$this->context->addNewEdge($urnStr, $a2UrnStr, 'ea:neighbor:',true);
					}
				}
			}
		}





	}


}
