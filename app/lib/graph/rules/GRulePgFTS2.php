<?php




class GRulePgFTS2 extends AbstractGruleProcessVertice implements GRule {

 	private $work_contributor_elements = null;
 	private $expression_contributor_elements = null;
	private $manifestation_contributor_elements = null;

	/**
	 *
	 * @var VertextFtsData
	 */
	private $ftsData;
	private $ftsDataArray = array();



	protected function init(){

		$createInferredArray = function($keys){
					$rep = array();
					foreach($keys as $k){
						$rep[] = 'inferred:' . $k;
					}
					return $rep;
		};

		$this->work_contributor_elements = $createInferredArray(array_keys(Setting::get('contributor_work_type_map')));
		$this->expression_contributor_elements =  $createInferredArray(array_keys(Setting::get('contributor_express_type_map')));
		$this->manifestation_contributor_elements =  $createInferredArray(array_keys(Setting::get('contributor_manif_type_map')));
		//$this->context->put('work_expression_contributor_elements', $work_expression_contributor_elements);

		//$this->ftsData = new VertextFtsData();

		$cmd = new GRuleUpdatePgFTSCmd('dsd.item2','item_id');
		$this->context->putCommand('V_UPDATE_FTS',  $cmd);


	}



	private function processWork($v){
		//$this->context->addDebugMessage("FTS WORK " . $v);
		$this->ftsContributor($v);
// 		$ftsDataStuff = $this->ftsDataStuff;
// 		$ftsDataOpac = $this->ftsDataOpac;

		$this->ftsData->stuff->addA($v->getPropertyValue('dc:title:'));
		$this->ftsData->opac->addB($v->getPropertyValue('dc:title:'));

		//EXPRESSIONS
		$expressions = $v->getVertices(GDirection::OUT,'reverse:ea:expressionOf:');
		foreach ($expressions as $expr){
		//	$this->context->addDebugMessage("FTS WORK: expresion: " . $expr);
			$expr_title = $expr->getPropertyValue('dc:title:');
			$this->ftsData->stuff->addB($expr_title);

			$this->ftsData->opac->addB($expr_title);

			//EXPRESSION MANIFESTATIONS
			$manifs = $expr->getVertices(GDirection::IN,'ea:work:');
			$emtree = ARRAY();
			foreach ($manifs as $manif){
				//$this->context->addDebugMessage("FTS WORK: expresion manif: " . $manif);
				$manif_title = $manif->getPropertyValue('dc:title:');
				$manif_title_remainder = $manif->getPropertyValue('ea:manif:Title_Remainder');

				$this->ftsData->stuff->addB($manif_title);
				$this->ftsData->stuff->addC($manif_title_remainder);

				$this->ftsData->opac->addA($manif_title);
				$this->ftsData->opac->addB($manif_title_remainder);
			}
		}

		//DIRECT MANIFESTATIONS
		$manifs = $v->getVertices(GDirection::IN,'ea:work:');
		$mtree = ARRAY();
		foreach ($manifs as $manif){
			//$this->context->addDebugMessage("FTS WORK: direct manif: " . $manif);
			$manif_title = $manif->getPropertyValue('dc:title:');
			$manif_title_remainder = $manif->getPropertyValue('ea:manif:Title_Remainder');


			$this->ftsData->stuff->addB($manif_title);
			$this->ftsData->stuff->addC($manif_title_remainder);

			$this->ftsData->opac->addA($manif_title);
			$this->ftsData->opac->addB($manif_title_remainder);
		}





	}

	private function processExpression( $v){
		$this->ftsData->stuff->addA($v->getPropertyValue('dc:title:'));
		$this->ftsContributor( $v);
	}

	private function processManifestation( $v){
		$this->ftsData->stuff->addA($v->getPropertyValue('dc:title:'));
		$this->ftsContributor( $v);
		$this->ftsISBN($v);
	}

	private function processActor( $v){
		$this->ftsData->stuff->addA($v->getPropertyValue('dc:title:'));
		$this->ftsData->opac->addA($v->getPropertyValue('dc:title:'));

	}




	/**
	 *
	 * @param GVertex $v
	 */
	private function ftsISBN($v){
		/* @var $props  GPValue[] */
		$props = $v->getProperties('ea:manif:ISBN_Number');
		if (!empty($props)){
			foreach($props as $prop){
				$value = $prop->value();

				$value = str_replace('-','',$value);
				$value = str_replace(' ','',$value);

				$this->ftsData->isbn->addA($value);
				$this->ftsData->opac->addA($value);
			}
		}
	}


	/**
	 *
	 * @param GVertex $v
	 */
	private function ftsContributor($v){


		$this->context->addDebugMessage("ftsContributor: " . $v);
		//$tmp = GGraphUtil::dumpVertex($v,false);
		//$this->context->addDebugMessage($tmp);

		$edges = $v->getEdges(GDirection::OUT,'dc:contributor:');
		foreach ($edges as $e){
			$label = $e->label();
			if (!empty($label)){
				//$this->context->addDebugMessage("ftsContributorAdv 0 2: dc:contributor: : " . $label);
				$this->ftsData->contributor->addA($label);
				$this->ftsData->opac->addD($label);
				$this->ftsData->stuff->addD($label);
			}
		}

		foreach ($this->work_contributor_elements as $el){
			//$this->context->addDebugMessage("ftsContributorAdv 1: " . $v . " : " . $el);
			$edges = $v->getEdges(GDirection::OUT, $el);
			foreach ($edges as $e){
				$label = $e->label();
				if (!empty($label)){
					//$this->context->addDebugMessage("ftsContributorAdv 1 2: " . $el . " : " . $label);
					$this->ftsData->contributor->addA($label);
					//$this->ftsDataOpac->addD($label);
				}
			}
		}

		foreach ($this->expression_contributor_elements as $el){
			//$this->context->addDebugMessage("ftsContributorAdv 2: " . $el);
			$edges = $v->getEdges(GDirection::OUT, $el);
			foreach ($edges as $e){
				$label = $e->label();
				if (!empty($label)){
					//$this->context->addDebugMessage("ftsContributorAdv 2 2: " . $el . " : " . $label);
					$this->ftsData->contributor->addA($label);
					//$this->ftsDataOpac->addD($label);
				}
			}
		}




	}


	/**
	 * @param GVertex $v
	 */
	protected function processVertex( $v){
		$vid = $v->id();

		$this->ftsData = isset($this->ftsDataArray[$vid]) ? $this->ftsDataArray[$vid] :  new VertextFtsData();
		//$this->context->addDebugMessage("FTS PROC VERTEX " . $v);
		//FTSControl('dsd.item2','item_id', 'fts');
		//$graph = $v->graph();
		//$context->addDebugMessage("PgFTS process: " . $v);
		//$dumpV  = GGraphUtil::dumpVertex($v,false);
		//$context->addDebugMessage($dumpV);

		$id = $v->persistenceId();
		$obj_type = $v->getObjectType();

		if ($obj_type == 'auth-person'
				|| $obj_type == 'auth-family'
				|| $obj_type == 'auth-organization'
		){
			$this->processActor( $v);
		}


		if ($obj_type == 'auth-manifestation'){
			$this->processManifestation( $v);
		}elseif ($obj_type == 'auth-expression'){
			$this->processExpression( $v);
		}elseif ($obj_type == 'auth-work'){
			$this->processWork($v);
		} else {
			$this->ftsData->stuff->addA($v->getPropertyValue('dc:title:'));
		}


		$this->ftsDataArray[$vid] = $this->ftsData;


	}



	public function postExecute() {
		$this->context->addDebugMessage("POST EXeXCUTE FTS");
		$this->context->put('FTS_VERTEX_DATA', $this->ftsDataArray);
	}




}

