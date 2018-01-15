<?php

//use Log;
class GRuleUtil {


	/**
	 *
	 * @param string[] $keys
	 * @return array[string][string]
	 */
	public static final function createInferredMap($keys){
		$map = array();
		foreach($keys as $k){
			$map[$k] = 'inferred:' . $k;
		}
		return $map;
	}


// 	/**
// 	 *
// 	 * @param GVertex $v
// 	 */
// 	public static final function vertexIsReadOnly($v){
// 		return (! $v->hasAttribute('_READONLY'));
// 	}
// 	/**
// 	 *
// 	 * @param GNode $v
// 	 */
// 	public static final function nodeIsReadOnly($v){
// 		return (! $v->hasAttribute('_READONLY'));
// 	}



	/**
	 *
	 * @param GVertex $v
	 */
	public static final function getLabel($v){

		$label = $v->getTmpAttribute('label');
		if (!empty($label)){ return $label; }

		$label = $v->getPropertyValue("Title_punc");//TODO:CHECK THIS
		if (!empty($label)){ return $label; }

		$label = $v->getPropertyValue("dc:title:");
		if (!empty($label)){ return $label; }

		$label = $v->urn();
		return $label;
	}





	//$(A) --|elementAV|--> $(V)   ==>  $(A) <--|newElement|--   $(V)
	public static function inferenceAV_THEN_VA($context, $v, $elementAV, $newElement){
		$edges = new PArray();
		$vUrnStr = $v->urnStr();
		$as  = $v->getVertices(GDirection::IN,$elementAV);
		foreach ($as as $a){
			$edges->appendIfNotEmpty($context->addNewEdge($vUrnStr, $a->urnStr(), $newElement,true));
		}
		return $edges;
	}


	//$(V) --|elementVA|--> $A   ==>  $(V) <--|newElement|--   $(VA)
	public static function inferenceVA_THEN_AV($context, $v, $elementVA, $newElement){
		$edges = new PArray();
		$vUrnStr = $v->urnStr();
		$as  = $v->getVertices(GDirection::OUT,$elementVA);
		foreach ($as as $a){
			$edges->appendIfNotEmpty($context->addNewEdge($a->urnStr(), $vUrnStr, $newElement,true));
		}
		return $edges;
	}

	/**
	 *
	 * @param GRuleContextR $context
	 * @param GVertex $v
	 * @param unknown $elementAV
	 * @param unknown $elementVB
	 * @param unknown $newElement
	 */
	public static function inferenceAVB_THEN_AB($context, $v, $elementAV,$elementVB,$newElement){
		//$(A) --|elementAV|--> $(V) --|elementVB|--> $(B) ==> $(A) --|newElement|--> $(B)
		$edges = new PArray();
		$as  = $v->getVertices(GDirection::IN,$elementAV);
		$bs  = $v->getVertices(GDirection::OUT,$elementVB);
		foreach ($as as $a){
			$aUrnStr = $a->urnStr();
			foreach ($bs as $b){
				$edges->appendIfNotEmpty($context->addNewEdge($aUrnStr, $b->urnStr(), $newElement,true));
			}
		}
		return $edges;
	}

	/**
	 *
	 * @param GRuleContextR $context
	 * @param GVertex $v
	 * @param unknown $elementAV
	 * @param unknown $elementVB
	 * @param unknown $newElement
	 */
	public static function inferenceAVB_THEN_BA($context, $v, $elementAV,$elementVB,$newElement){
		//$(A) --|elementAV|--> $(V) --|elementVB|--> $(B) ==> $(A) --|newElement|--> $(B)
		$edges = new PArray();
		$as  = $v->getVertices(GDirection::IN,$elementAV);
		$bs  = $v->getVertices(GDirection::OUT,$elementVB);
		foreach ($as as $a){
			$aUrnStr = $a->urnStr();
			foreach ($bs as $b){
				$edges->appendIfNotEmpty($context->addNewEdge($b->urnStr(),$aUrnStr, $newElement,true));
			}
		}
		return $edges;
	}

	//$(A) --|elementAV|--> $(V) <--|elementBV|-- $(B) ==> $(A) --|newElement|--> $(B)
	public static function inferenceAV_BV_THEN_AB($context, $v,  $elementAV,$elementBV,$newElement){
		$edges = new PArray();
		$as  = $v->getVertices(GDirection::IN,$elementAV);
		$bs  = $v->getVertices(GDirection::IN,$elementBV);
		foreach ($as as $a){
			$aUrnStr = $a->urnStr();
			foreach ($bs as $b){
				$edges->appendIfNotEmpty( $context->addNewEdge($aUrnStr, $b->urnStr(), $newElement,true));
			}
		}
		return $edges;
	}

	//$(V) --|elementVA|--> $(A),  $(V) --|elementVB|--> $(B) ==> $(A) --|newElement|--> $(B)
	public static function inferenceVA_VB_THEN_AB($context, $v, $elementVA,$elementVB,$newElement){
		$edges = new PArray();
		$as  = $v->getVertices(GDirection::OUT,$elementVA);
		$bs  = $v->getVertices(GDirection::OUT,$elementVB);
		foreach ($as as $a){
			$aUrnStr = $a->urnStr();
			foreach ($bs as $b){
				$edges->appendIfNotEmpty( $context->addNewEdge($aUrnStr, $b->urnStr(), $newElement,true));
			}
		}
		return $edges;
	}






	/**
	 *
	 * @param GRuleContextR $context
	 * @param GVertex $v
	 * @param unknown $elementVA
	 * @param unknown $elementAB
	 * @param unknown $newElement
	 */
	public static function inferenceVAB_THEN_VB($context, $v, $elementVA,$elementAB,$newElement){
		//$(V) --|elementVA|--> $(A) --|elementAB|--> $(B) ==> $(V) --|newElement|--> $(B)
		$vUrnStr = $v->urnStr();
		$edges = new PArray();
		$as  = $v->getVertices(GDirection::OUT,$elementVA);
		foreach ($as as $a){
			$bs  = $a->getVertices(GDirection::OUT,$elementAB);
			foreach ($bs as $b){
				$edges->appendIfNotEmpty( $context->addNewEdge($vUrnStr, $b->urnStr(), $newElement,true));
			}
		}

		return $edges;
	}



	/**
	 *
	 * @param GRuleContextR $context
	 * @param GVertex $v
	 * @param unknown $elementVA
	 * @param unknown $elementAB
	 * @param unknown $newElement
	 */
	public static function inferenceVAB_THEN_BV($context, $v, $elementVA,$elementAB,$newElement){
		//$(V) --|elementVA|--> $(A) --|elementAB|--> $(B) ==> $(V) --|newElement|--> $(B)
		$vUrnStr = $v->urnStr();
		$edges = new PArray();
		$as  = $v->getVertices(GDirection::OUT,$elementVA);
		foreach ($as as $a){
			$bs  = $a->getVertices(GDirection::OUT,$elementAB);
			foreach ($bs as $b){
				$edges->appendIfNotEmpty( $context->addNewEdge($b->urnStr(),$vUrnStr, $newElement,true));
			}
		}

		return $edges;
	}


	/**
	 * @param GVertex $v
	 * @param callable $closure
	 */
	public static function travesrseSubjectsLinks($v,$closure){
		$i=0;
		// SUBJECTS
		$subjects = $v->getVertices(GDirection::OUT,'ea:subj:');
		if (!empty($subjects)){
			foreach ($subjects as $subject) {
				if ($subject->getObjectType() == 'subject-chain') {
					$subject_links = $subject->getVertices(GDirection::OUT,'ea:inferred-chain-link:');
					foreach ($subject_links as $sl){
						$closure($i,$sl,$subject);
						$i+=1;
					}
				} else {
					$closure($i,$subject,null);
					$i+=1;
				}
			}
		}




	}





}