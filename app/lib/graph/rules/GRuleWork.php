<?php
class GRuleWork  extends AbstractGruleProcessVertice implements GRule {

	//private $work_contributor_elements = null;
	//private $expression_contributor_elements = null;
	//private $work_expression_contributor_elements_arr = null;
	private $contributor_work_type_map = null;

	protected function init(){
		$this->contributor_work_type_map = Setting::get('contributor_work_type_map');
		//$this->work_contributor_elements  =  GRuleUtil::createInferredMap(array_keys(Setting::get('contributor_work_type_map')));
		//$this->expression_contributor_elements =   $createInferredMap(array_keys(Setting::get('contributor_express_type_map')));
		//$this->work_expression_contributor_elements_arr = array_values($this->work_contributor_elements);

	}



	//  $(1) --|ea:expressionOf:|--> $v   ==>  $(1) <--|reverse:ea:ea:expressionOf|--   $v
	private function reverseExpressionOf( $v){
		//@@@ $this->inferenceAV_THEN_VA('ea:expressionOf:', 'reverse:ea:expressionOf:');
	}


	/**
	 * @param GVertex $v
	 */
	protected function processVertex( $v){

		//Log::info("GRULEWORK proc " . $v->urnStr());
		$context = $this->context;
		//$v->addFlag('OT:WORK');
		$v->addFlag('HAS:auth-work');
		$v->addFlag('IS:auth-work');

		$this->inferenceVA_VB_THEN_AB('ea:lemma:manifestation', 'ea:lemma:work', 'inferred:ea:work:');

		$this->reverseExpressionOf( $v);
		//INFERED CONTRIBUTOR
		//$elements = $this->work_contributor_elements;
		$elements = $this->contributor_work_type_map;
		foreach($elements as $k => $vv){

			$nk = $k;
			//$context->addDebugMessage('CHK2: ' . $k . ' > ' . $nk);
			//  $(1)--|ea:work:|-->$v --|$k|-->$(2)  ==> $(1) --|inferred:$k|--> $(2);

			//DIRECT MANIFESTATIONS OF WORK HAS infered-contributor the contributor of work
			$edges = $this->inferenceAVB_THEN_AB('ea:work:', $k, $nk);
			foreach ($edges as $e){
				//$context->addDebugMessage("NV2::: " . $e->getVertexFrom()->urnStr()." # " . $e->getVertexTo()->urnStr() . " #  " .  $e->element());
				//$context->addDebugMessage("NV2::: " . $e->getVertexTO()->urnStr()." # " . $e->getVertexFrom()->urnStr() . " #  " .  $nk);
				$to = $e->getVertexTO();
				$label = GRuleUtil::getLabel($to);
				$e->setLabel($label);
				//R//$context->addNewEdge($to->urnStr(), $e->getVertexFrom()->urnStr(), 'reverse:' . $e->element(),true,null,$label);
			}

			//INDIRECT (expression) MANIFESTATIONS OF WORK HAS infered-contributor the contributor of work
			$edges = $this->inferenceAVB_THEN_AB('inferred:ea:work:', $k, $nk);
			//$edges = $this->inferenceAVB_THEN_AB('ea:neighbor:', $nk, $k);
			foreach ($edges as $e){
				//$context->addDebugMessage("NV2::: " . $e->getVertexFrom()->urnStr()." # " . $e->getVertexTo()->urnStr() . " #  " .  $e->element());
				//$context->addDebugMessage("NV2.1::: " . $e->getVertexTO()->urnStr()." # " . $e->getVertexFrom()->urnStr() . " #  " .  $nk);

				$to = $e->getVertexTO();
				$label = GRuleUtil::getLabel($to);
				$e->setLabel($label);
				//R//$context->addNewEdge($to->urnStr(), $e->getVertexFrom()->urnStr(), 'reverse:' . $e->element(),true,null,$label);
			}

		}


		//DIRECT EXPRESSION OF WORK HAS infered-contributor the contributor of work
		foreach($elements as $k=>$vv){
			$nk = $k;
			//$context->addDebugMessage('CHK3: ' . $k . ' > ' . $nk);
			//  $(1)--|ea:expresionOf:|-->$v --|$k|-->$(2)  ==> $(1) --|inferred:$k|--> $(2);
			$edges = $this->inferenceAVB_THEN_AB('ea:expressionOf:', $k, $nk);
			foreach ($edges as $e){
				//$context->addDebugMessage("NV3::: " . $e->getVertexFrom()->urnStr()." # " . $e->getVertexTo()->urnStr() . " #  " .  $e->element());
				//$context->addDebugMessage("NV3::: " . $e->getVertexTO()->urnStr()." # " . $e->getVertexFrom()->urnStr() . " #  " .  $nk);
				$to = $e->getVertexTO();
				$label = GRuleUtil::getLabel($to);
				$e->setLabel($label);
				//R//$context->addNewEdge($to->urnStr(), $e->getVertexFrom()->urnStr(), 'reverse:' . $e->element(),true,null,$label);
			}

		}

// 		//DIRECT MANIFS
// 		$manifEdges = $v->getEdges(GDirection::OUT,array('ea:workOf:'));
// 		foreach ($manifEdges as $e){
// 			$context->addNewEdge($e->getVertexFrom()->urnStr(), $e->getVertexTO()->urnStr(),'ea:manifestation:primary');
// 		}



		$work_type = $v->getPropertyValue('ea:work:Type');
		if ($work_type == 'workSubclassTable_Individual'){
			$v->addFlag('INDIVIDUAL_WORK');
		}
		if ($work_type == 'workSubclassTable_Independent'){
			$v->addFlag('INDEPENDENT_WORK');
		}

//Independent-InIndividual
		$this->inferenceAVB_THEN_AB('ea:work:','ea:relation:containerOfIndependent' , 'ea:work:independent');
		$this->inferenceAV_BV_THEN_AB('ea:work:','ea:relation:containedInIndividual' , 'ea:work:independent');
		$this->inferenceAVB_THEN_AB('inferred:ea:work:','ea:relation:containerOfIndependent','ea:work:independent');
		// 		$this->inferenceVA_VB_THEN_AB('ea:workOf:','ea:relation:containerOfIndependent' , 'ea:work:independent');
		// 		$this->inferenceAVB_THEN_BA('ea:workOf:','ea:relation:containedInIndividual' , 'ea:work:independent');
//Contributions
		$this->inferenceAVB_THEN_AB('ea:work:','ea:relation:containerOfContributions' , 'ea:work:contribution');
		$this->inferenceAV_BV_THEN_AB('ea:work:','ea:relation:containedInContributions', 'ea:work:contribution');
		$this->inferenceAVB_THEN_AB('inferred:ea:work:','ea:relation:containerOfContributions','ea:work:contribution');
//Documents
		$this->inferenceAVB_THEN_AB('ea:work:','ea:relation:containerOfDocuments' , 'ea:work:documents');
		$this->inferenceAV_BV_THEN_AB('ea:work:','ea:relation:containedInDocuments', 'ea:work:documents');
		$this->inferenceAVB_THEN_AB('inferred:ea:work:','ea:relation:containerOfDocuments','ea:work:documents');


	}


}

