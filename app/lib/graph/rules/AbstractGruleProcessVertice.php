<?php

class AbstractGruleProcessVertice extends AbstractBaseRule implements GRule {

	protected $my_types = array();
	protected $vertex = null;
	protected $skip_readonly  = false;


	protected function init(){
	}

	/**
	 *
	 * @param GRuleContextR $context
	 */
	public function __construct($context,$args) {
		if (!empty($args)){
			if (!isset($args['obj_types'])){
				throw new Exception("AbstractGruleProcessVertice obj_type expected");
			}
			$this->my_types = $args['obj_types'];
		}
		if (isset($args['skip_readonly'])){
			$this->skip_readonly = $args['skip_readonly'];
		}
		parent::__construct($context);
		$this->init($args);
	}



	public function execute() {

		$context = $this->context;
		/*@var $g GGRaph */
		$g = $this->context->graph();

		$vertices = &$g->getRefernceToVertices();
		#$vertices = $g->getVertices();
		foreach ( $vertices as $v ) {
			/*@var $v GVertex */
			if ($this->skip_readonly && $v->isReadOnly()){
				//Putil::logRed("@@: SKIP READONLY VERTEX: " . $v->urnStr());
				//$context->addDebugMessage("SKIP READONLY VERTEX: " . $v->urnStr());
				continue;
			}
			$ot = $v->getObjectType();
// 			Log::info($v->persistenceId().'- ot: '.$ot);
			foreach ( $this->my_types as $type ) {
// 				Log::info('###: '.$v->persistenceId().'-'.$type);
				if ($ot == $type) {
					//$id = $v->persistenceId();
					$this->vertex = $v;
					$this->processVertex(  $v);
				}
			}
		}

		$this->postExecute();
	}


	public function postExecute() {

	}



	//$(A) --|elementAV|--> $(V)   ==>  $(A) <--|newElement|--   $(V)
	protected function inferenceAV_THEN_VA($elementAV, $newElement){
		return GRuleUtil::inferenceAV_THEN_VA($this->context, $this->vertex, $elementAV, $newElement);
	}

	//$(V) --|elementVA|--> $A   ==>  $(V) <--|newElement|--   $(VA)
	protected function inferenceVA_THEN_AV($elementVA, $newElement){
		return GRuleUtil::inferenceVA_THEN_AV($this->context, $this->vertex, $elementVA, $newElement);
	}



	//$(A) --|elementAV|--> $(V) --|elementVB|--> $(B) ==> $(A) --|newElement|--> $(B)
	protected function inferenceAVB_THEN_AB($elementAV,$elementVB,$newElement){
		return GRuleUtil::inferenceAVB_THEN_AB($this->context, $this->vertex,$elementAV,$elementVB,$newElement);
	}

	//$(A) --|elementAV|--> $(V) --|elementVB|--> $(B) ==> $(B) --|newElement|--> $(A)
	protected function inferenceAVB_THEN_BA($elementAV,$elementVB,$newElement){
		return GRuleUtil::inferenceAVB_THEN_BA($this->context, $this->vertex,$elementAV,$elementVB,$newElement);
	}


	//$(A) --|elementAV|--> $(V) <--|elementBV|-- $(B) ==> $(A) --|newElement|--> $(B)
	protected function inferenceAV_BV_THEN_AB($elementAV,$elementBV,$newElement){
		return GRuleUtil::inferenceAV_BV_THEN_AB($this->context, $this->vertex,$elementAV,$elementBV,$newElement);
	}

	//$(V) --|elementVA|--> $(A),  $(V) --|elementVB|--> $(B) ==> $(A) --|newElement|--> $(B)
	protected function inferenceVA_VB_THEN_AB($elementVA,$elementVB,$newElement){
		return GRuleUtil::inferenceVA_VB_THEN_AB($this->context, $this->vertex,$elementVA,$elementVB,$newElement);
	}

	//$(V) --|elementVA|--> $(A) --|elementAB|--> $(B) ==> $(V) --|newElement|--> $(B)
	protected function inferenceVAB_THEN_VB($elementVA,$elementAB,$newElement){
		return GRuleUtil::inferenceVAB_THEN_VB($this->context, $this->vertex, $elementVA, $elementAB, $newElement);
	}



	//$(V) --|elementVA|--> $(A) --|elementAB|--> $(B) ==> $(B) --|newElement|--> $(V)
	protected function inferenceVAB_THEN_BV($elementVA,$elementAB,$newElement){
		return GRuleUtil::inferenceVAB_THEN_BV($this->context, $this->vertex, $elementVA, $elementAB, $newElement);
	}









	// 	//  $(1) --|ea:work:|--> $v   ==>  $(1) <--|reverse:ea:work|--   $v
	// 	//
	// 	private function reverseWork($context, $v){
	// 		$vUrnStr = $v->urnStr();
		// 		$wmanifs = $v->getVertices(GDirection::IN,'ea:work:');
		// 		foreach ($wmanifs as $wm){
		// 			$context->addNewEdge($vUrnStr, $wm->urnStr(), 'reverse:ea:work:',true);
		// 		}

		// 	}

		// 	//  $(1) --|ea:expressionOf:|--> $v   ==>  $(1) <--|reverse:ea:ea:expressionOf|--   $v
		// 	//
		// 	private function reverseExpressionOf($context, $v){
		// 		$vUrnStr = $v->urnStr();
		// 		$wexprs = $v->getVertices(GDirection::IN,'ea:expressionOf:');
		// 		foreach ($wexprs as $we){
		// 			$context->addNewEdge($vUrnStr, $we->urnStr(), 'reverse:ea:expressionOf:',true);
		// 		}

		// 	}




		// 	public static function relation_A_V_B($directionAV, $directionVB, $newElement){

		// 	}































	/**
	 *
	 * @param GVertex $v
	 */
	protected function processVertex( $v){
	}












}
