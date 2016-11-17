<?php
class GRuleExpression  extends AbstractGruleProcessVertice implements GRule {



//	private $expression_contributor_elements = null;
	private $contributor_express_type_map  =null;


	protected function init(){
		//$this->expression_contributor_elements =   GRuleUtil::createInferredMap(array_keys(Setting::get('contributor_express_type_map')));
		$this->contributor_express_type_map =  Setting::get('contributor_express_type_map');

	}


	/**
	 * @param GVertex $v
	 */
	protected function processVertex( $v){
		 		$context = $this->context;
		// 				$id = $v->persistenceId();
		// 				$g = $v->graph();

	//	$v->addFlag('OT:WORK');
	//	$v->addFlag('OT:EXPRESSION');

		//$context->addDebugMessage("ADD POST RULE: " . $v);
		//$context->addPostRule(new GRulePostFRBRExpression($context, $v, $this->work_expression_contributor_elements_arr));

		//$edges  = $this->inferenceAVB_THEN_AB('ea:work:','ea:expressionOf:' , 'inferred:ea:work:');
		$edges  = $this->inferenceAVB_THEN_AB('ea:work:','ea:expressionOf:' , 'inferred:ea:work:');
	//@@@	$edges  = $this->inferenceAVB_THEN_BA('ea:work:','ea:expressionOf:' , 'ea:neighbor:');
		// 			foreach ($edges as $e){
		// 				$context->addDebugMessage('@manif-expr-work: ' . $e);
		// 			}

		$elements = $this->contributor_express_type_map;
		foreach($elements as $k => $vv){
			$nk = $k;
			//$context->addDebugMessage('CHK4: ' . $k . ' > ' . $nk);
			//  $(1)--|ea:work:|-->$v --|$k|-->$(2)  ==> $(1) --|inferred:$k|--> $(2);
			$edges =  $this->inferenceAVB_THEN_AB('ea:work:', $k, $nk);
			foreach ($edges as $e){
				$context->addDebugMessage("NV4::: " . $e->getVertexFrom()->urnStr()." # " . $e->getVertexTo()->urnStr() . " #  " .  $nk);
				//$context->addDebugMessage("NV4::: " . $e->getVertexTO()->urnStr()." # " . $e->getVertexFrom()->urnStr() . " #  " .  'reverse:' . $e->element());
				//$context->addNewEdge($e->getVertexTO()->urnStr(), $e->getVertexFrom()->urnStr(), 'reverse:' . $e->element());
				$to = $e->getVertexTO();
				$label = GRuleUtil::getLabel($to);
				$e->setLabel($label);
				//R//$context->addNewEdge($to->urnStr(), $e->getVertexFrom()->urnStr(), 'reverse:' . $e->element(),true,null,$label);

			}
		}



		$title = $v->getPropertyValue('dc:title:');
		$translator = null;
		$translatorVertex = $v->getFirstVertex(GDirection::OUT,'ea:expres:translator');
		if ($translatorVertex){
			$translator = $translatorVertex->getPropertyValue('dc:title:');
		}
		$label = $title ;
		if (!empty($translator)){
			$label = $label . ' / ' . $translator . ' ['. tr('Translator').']' ;
		}

		//$title_punct = ArcTemplateEngine::renderLine($template,$var_templ);
		$v->setTmpAttribute('Title_punc', $label);
		$v->setTmpAttribute('label',$label);


		//$this->labelForActor($v);


	}



}

?>

