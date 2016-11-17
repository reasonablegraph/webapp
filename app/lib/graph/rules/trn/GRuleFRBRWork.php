<?php






class GRulePostFRBRExpression   implements GRule {

	private $context;
	private $v;
	private $elements;
	public function __construct($context, $v, $elements) {
		$this->context =$context;
		$this->v = $v;
		$this->elements = $elements;
	}


	public function execute(){

		//$this->context->addDebugMessage("POST RULE EXEC " . $this->v);

		$arr = $this->elements;
		$v = $this->v;
		$context=$this->context;
		//$(A:manifestation) --|ea:work:|--> $(V:expresion)--|elementBV|-->  $(B:actor)  ==>  $(A) --|elementBV|-->   $B
		foreach ($arr as $k){
			//$context->addDebugMessage('CHK1: ' . $k);
			$edges = GRuleUtil::inferenceAVB_THEN_AB($context, $v, 'ea:work:', $k, $k);
			//$edges = GRuleUtil::inferenceAV_BV_THEN_AB($context, $v, 'ea:work:', $k, $k);
			foreach ($edges as $e){
			//	$context->addDebugMessage("NV1::: " . $e->getVertexFrom()->urnStr()." # " . $e->getVertexTo()->urnStr() . " #  " .  $e->element());
				//$context->addDebugMessage("NV1::: " . $e->getVertexTO()->urnStr()." # " . $e->getVertexFrom()->urnStr() . " #  " .  'reverse:' . $e->element());
				//$context->addNewEdge($e->getVertexTO()->urnStr(), $e->getVertexFrom()->urnStr(), 'reverse:' . $e->element());
				$to = $e->getVertexTO();
				$label = GRuleUtil::getLabel($to);
				$e->setLabel($label);
				//R//$context->addNewEdge($to->urnStr(), $e->getVertexFrom()->urnStr(), 'reverse:' . $e->element(),true,null,$label);
			}

		}


	}
};


// class GRulePostFRBRReverse   implements GRule {

// 	private $context;
// 	private $v;
// 	private $map;
// 	public function __construct($context) {
// 		$this->context =$context;
// 	}


// 	public function execute(){
// 		/* @var $context GRuleContextR */
// 		$context=$this->context;
// 		/* @var $graph GGraph */
// 		$graph = $context->graph();
// 		$vs = $graph->getVertices();
// 		foreach ($vs as )

// 		foreach ($map as $k=>$nk){
// 			GRuleUtil::inferenceVA_THEN_AV($context, $v,$nk,$k );
// 		}

// 	}


// };




class GRuleFRBRWork  extends AbstractGruleProcessVertice implements GRule {




	private $work_contributor_elements = null;
	private $expression_contributor_elements = null;
	private $work_expression_contributor_elements_arr = null;


	protected function init(){

		$createInferredMap = function($keys){
			$map = array();
			foreach($keys as $k){
				$map[$k] = 'inferred:' . $k;
			}
			return $map;
		};

// 		$this->context->addDebugMessage('contributor_work_type_map');
// 		$this->context->addDebugMessage(print_r(Setting::get('contributor_work_type_map'),true));
// 		$this->context->addDebugMessage('contributor_express_type_map');
// 		$this->context->addDebugMessage(print_r(Setting::get('contributor_express_type_map'),true));

// 		contributor_work_type_map   Array (  [ea:work:authorWork] => Author ..
//		contributor_express_type_map  Array ( [ea:expres:translator] => Translator ..



		$this->work_contributor_elements  =  $createInferredMap(array_keys(Setting::get('contributor_work_type_map')));
		$this->expression_contributor_elements =   $createInferredMap(array_keys(Setting::get('contributor_express_type_map')));
		$this->work_expression_contributor_elements_arr = array_values($this->work_contributor_elements);

// 		foreach ($keys as $k){
// 			$this->work_expression_contributor_elements[$k] = $k;
// 		}
		//$this->context->put('work_expression_contributor_elements', $work_expression_contributor_elements);

	}





	private function digitalItemType(){
		//$this->context->addDebugMessage("digitalItemType " . $this->vertex);
		$v = $this->vertex;
		$type = $v->getPropertyValue('ea:item:type');
		if (!empty($type)){
			$v1 = $v->getFirstVertex(GDirection::OUT, 'ea:artifact-of:');
			if (!empty($v1)){
				//$this->context->addDebugMessage('digitalItemType ADD FLAG: DIGITAL_ITEM_TYPE:' . $type);
				$v1->addFlag('DIGITAL_ITEM:TYPE:' . $type);
			}
		}
	}

	private function reverseArtifactOf(){
		//$this->context->addDebugMessage("reverseArtifactOf " . $this->vertex);
		$this->inferenceVA_THEN_AV('ea:artifact-of:', 'reverse:ea:artifact-of:');
	}


	//  $(1) --|ea:work:|--> $v   ==>  $(1) <--|reverse:ea:work|--   $v
	private function reverseWork($v){
		//@@@@ $this->inferenceAV_THEN_VA('ea:work:', 'reverse:ea:work:');
	}

	//  $(1) --|ea:expressionOf:|--> $v   ==>  $(1) <--|reverse:ea:ea:expressionOf|--   $v
	private function reverseExpressionOf( $v){
		//@@@ $this->inferenceAV_THEN_VA('ea:expressionOf:', 'reverse:ea:expressionOf:');
	}


	/**
	 * @param GVertex $v
	 */
	protected function processVertex( $v){
		$context = $this->context;

		$id = $v->persistenceId();
		$g = $v->graph();


		$this->reverseWork( $v);

		if ($v->getObjectType() == 'digital-item') {
			$this->reverseArtifactOf();
			$this->digitalItemType();
			//GGraphUtil::dumpVertex($v);
		}



		if ($v->getObjectType() == 'auth-work') {

			$v->addFlag('OT:WORK');

			$this->reverseExpressionOf( $v);
			//INFERED CONTRIBUTOR
			$elements = $this->work_contributor_elements;
			foreach($elements as $k => $nk){
				$context->addDebugMessage('CHK2: ' . $k . ' > ' . $nk);
				//  $(1)--|ea:work:|-->$v --|$k|-->$(2)  ==> $(1) --|inferred:$k|--> $(2);
				$edges = $this->inferenceAVB_THEN_AB('ea:work:', $k, $nk);
				foreach ($edges as $e){
					//$context->addDebugMessage("NV2::: " . $e->getVertexFrom()->urnStr()." # " . $e->getVertexTo()->urnStr() . " #  " .  $e->element());
					//$context->addDebugMessage("NV2::: " . $e->getVertexTO()->urnStr()." # " . $e->getVertexFrom()->urnStr() . " #  " .  'reverse:' . $e->element());
					$to = $e->getVertexTO();
					$label = GRuleUtil::getLabel($to);
					$e->setLabel($label);
					//R//$context->addNewEdge($to->urnStr(), $e->getVertexFrom()->urnStr(), 'reverse:' . $e->element(),true,null,$label);
				}

			}

			foreach($elements as $k => $nk){
				//$context->addDebugMessage('CHK3: ' . $k . ' > ' . $nk);
				//  $(1)--|ea:expresionOf:|-->$v --|$k|-->$(2)  ==> $(1) --|inferred:$k|--> $(2);
				$edges = $this->inferenceAVB_THEN_AB('ea:expressionOf:', $k, $nk);
				foreach ($edges as $e){
					//$context->addDebugMessage("NV3::: " . $e->getVertexFrom()->urnStr()." # " . $e->getVertexTo()->urnStr() . " #  " .  $e->element());
					//$context->addDebugMessage("NV3::: " . $e->getVertexTO()->urnStr()." # " . $e->getVertexFrom()->urnStr() . " #  " .  'reverse:' . $e->element());
					$to = $e->getVertexTO();
					$label = GRuleUtil::getLabel($to);
					$e->setLabel($label);
					//R//$context->addNewEdge($to->urnStr(), $e->getVertexFrom()->urnStr(), 'reverse:' . $e->element(),true,null,$label);
				}

			}





		} else if ($v->getObjectType() == 'auth-expression'){
			$v->addFlag('OT:WORK');
			$v->addFlag('OT:EXPRESSION');

			$context->addDebugMessage("ADD POST RULE: " . $v);
			$context->addPostRule(new GRulePostFRBRExpression($context, $v, $this->work_expression_contributor_elements_arr));

			$edges  = $this->inferenceAVB_THEN_AB('ea:work:','ea:expressionOf:' , 'inferred:ea:work:');
// 			foreach ($edges as $e){
// 				$context->addDebugMessage('@manif-expr-work: ' . $e);
// 			}

			$elements = $this->expression_contributor_elements;
			foreach($elements as $k => $nk){
				//$context->addDebugMessage('CHK4: ' . $k . ' > ' . $nk);
					//  $(1)--|ea:work:|-->$v --|$k|-->$(2)  ==> $(1) --|inferred:$k|--> $(2);
				$edges =  $this->inferenceAVB_THEN_AB('ea:work:', $k, $nk);
				foreach ($edges as $e){
					//$context->addDebugMessage("NV4::: " . $e->getVertexFrom()->urnStr()." # " . $e->getVertexTo()->urnStr() . " #  " .  $e->element());
					//$context->addDebugMessage("NV4::: " . $e->getVertexTO()->urnStr()." # " . $e->getVertexFrom()->urnStr() . " #  " .  'reverse:' . $e->element());
					//$context->addNewEdge($e->getVertexTO()->urnStr(), $e->getVertexFrom()->urnStr(), 'reverse:' . $e->element());
					$to = $e->getVertexTO();
					$label = GRuleUtil::getLabel($to);
					$e->setLabel($label);
					//R//$context->addNewEdge($to->urnStr(), $e->getVertexFrom()->urnStr(), 'reverse:' . $e->element(),true,null,$label);

				}
			}




		}




	}


}

?>