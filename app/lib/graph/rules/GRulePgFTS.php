
<?php



class GRulePgFTS extends AbstractGruleProcessVertice implements GRule {

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
		Log::info("@:: PgFTS INIT");

// 		$createInferredArray = function($keys){
// 					$rep = array();
// 					foreach($keys as $k){
// 						$rep[] = 'inferred:' . $k;
// 					}
// 					return $rep;
// 		};

// 		$this->work_contributor_elements = $createInferredArray(array_keys(Setting::get('contributor_work_type_map')));
// 		$this->expression_contributor_elements =  $createInferredArray(array_keys(Setting::get('contributor_express_type_map')));
// 		$this->manifestation_contributor_elements =  $createInferredArray(array_keys(Setting::get('contributor_manif_type_map')));

		$this->work_contributor_elements = array_keys(Setting::get('contributor_work_type_map'));
		$this->expression_contributor_elements =  array_keys(Setting::get('contributor_express_type_map'));
		$this->manifestation_contributor_elements =  array_keys(Setting::get('contributor_manif_type_map'));


		//$this->context->put('work_expression_contributor_elements', $work_expression_contributor_elements);

		//$this->ftsData = new VertextFtsData();

		//TODO:SOLR
		$cmd = new GRuleUpdatePgFTSCmd('dsd.item2','item_id');
		//$cmd = new GRuleUpdateSolrFTSCmd();
		$this->context->putCommand('V_UPDATE_FTS',  $cmd);

		$this->skip_readonly = true;


	}





	/**
	 *
	 * @param GVertex $v
	 */
	private function processWork($v){
		$this->context->addDebugMessage("@:: FTS WORK: " . $v->urnStr() );

		//TODO:SOLR
//		$this->processGenericFields($v,$this->ftsData->stuff);
//		$this->processGenericFields($v,$this->ftsData->label);
//		$this->processGenericFields($v,$this->ftsData->subject);
//		$this->processGenericFields($v,$this->ftsData->contributor);

		foreach ($this->work_contributor_elements as $el){
			//$this->context->addDebugMessage('#1 ' . $el);
			$edges = $v->getEdges(GDirection::OUT,$el);
			foreach ($edges as $e){
				//$this->context->addDebugMessage("WORK CONTRIBUTOR: " . $el );
				$label = $e->label();
				$this->ftsData->contributor->addA($label);
				$this->ftsData->opac->addB($label);
			}
		}


		$title = $v->getPropertyValue('dc:title:');
		$this->ftsData->stuff->addA($title);
		$this->ftsData->opac->addA($title);
		$this->ftsData->label->addA($title);
		$this->ftsData->subject->addA($title);


		//EXPRESSIONS
		//$expressions = $v->getVertices(GDirection::OUT,'reverse:ea:expressionOf:');
		$expressions = $v->getVertices(GDirection::OUT,'ea:expression:');
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


	private function processPeriodic( $v){
		$id = $v->persistenceId();
		$title  = GRuleUtil::getLabel($v);
		$this->ftsData->stuff->addA($title);
		$this->ftsData->opac->addA($title);
		$this->ftsData->subject->addA($title);

	}


	private function processExpression( $v){
		$title = $v->getPropertyValue('dc:title:');
		$this->ftsData->stuff->addA($title);
		$this->ftsData->subject->addA($title);
	}

	private function processCollection( $v){
		$title = $v->getPropertyValue('dc:title:');
		$this->ftsData->opac->addA($title);
		$this->ftsData->stuff->addA($title);
		$this->ftsData->subject->addA($title);
	}


	private function processManifestation( $v){
		//$this->context->addDebugMessage("PGFTS# MANIF: " . $v->urnStr());

		$title = $v->getPropertyValue('dc:title:');
		$title_remainder = $v->getPropertyValue('ea:manif:Title_Remainder');

		$this->ftsData->stuff->addA($title);
		$this->ftsData->stuff->addB($title_remainder);

		$this->ftsData->label->addA($title);
		$this->ftsData->label->addB($title_remainder);

		$this->ftsData->subject->addA($title);

		$this->ftsISBN($v);


		foreach ($this->manifestation_contributor_elements as $el){
			//$this->context->addDebugMessage("manif contributor element " . $el);
			$edges = $v->getEdges(GDirection::OUT,$el);
			foreach ($edges as $e){
				$label = $e->label();
				$this->ftsData->contributor->addA($label);
				$this->ftsData->opac->addB($label);
				//$this->context->addDebugMessage("manif contributor element: " . $el . " ## " . $label);

			}
		}


		$expressions = $v->getVertices(GDirection::OUT,'ea:work:');
		foreach ($expressions as $expr){
			//$this->context->addDebugMessage("###1 " .  $expr->urnStr());
			foreach ($this->expression_contributor_elements as $el){
				$edges = $expr->getEdges(GDirection::OUT,$el);
				foreach ($edges as $e){
					$label = $e->label();
					$this->ftsData->contributor->addA($label);
					$this->ftsData->opac->addB($label);
				}
			}
		}


	}


	private function processActor( $v){
		$id = $v->persistenceId();
// 		$title =$v->getPropertyValue('dc:title:');
		$title  = GRuleUtil::getLabel($v);
		$this->ftsData->stuff->addA($title);
		$this->ftsData->opac->addA($title);
		$this->ftsData->subject->addA($title);

	}


	private function processConcept( $v){

		$title =$v->getPropertyValue('dc:title:');
		$this->ftsData->stuff->addA($title);
		$this->ftsData->opac->addA($title);
		$this->ftsData->subject->addA($title);

	}


	private function processGeneral( $v){

		$title =$v->getPropertyValue('dc:title:');
		$this->ftsData->stuff->addA($title);
		$this->ftsData->opac->addA($title);
		$this->ftsData->subject->addA($title);

	}

	private function processPlace( $v){

// 		$title =$v->getPropertyValue('dc:title:');
		$title  = GRuleUtil::getLabel($v);
		$this->ftsData->stuff->addA($title);
		$this->ftsData->opac->addA($title);
		$this->ftsData->subject->addA($title);

	}

	private function processEvent( $v){
		$title =$v->getPropertyValue('dc:title:');
		$this->ftsData->stuff->addA($title);
		$this->ftsData->opac->addA($title);
		$this->ftsData->subject->addA($title);
	}

	private function processObject( $v){
		$title =$v->getPropertyValue('dc:title:');
		$this->ftsData->stuff->addA($title);
		$this->ftsData->opac->addA($title);
		$this->ftsData->subject->addA($title);
	}

	private function processLemmaCategory( $v){
		$title = GRuleUtil::getLabel($v);
// 		$title =$v->getPropertyValue('dc:title:');
		$this->ftsData->stuff->addA($title);
		$this->ftsData->opac->addA($title);
// 		$this->ftsData->subject->addA($title);
		$this->ftsData->label->addA($title);
	}

	private function processLemma( $v){
		$title =$v->getPropertyValue('dc:title:');
		$title_alternative = $v->getPropertyValue('ea:lemma:alternative_title');
		$this->ftsData->stuff->addA($title);
		$this->ftsData->opac->addA($title);
		$this->ftsData->opac->addB($title_alternative);
	}

	private function processWebSiteInstance( $v){
		$title =$v->getPropertyValue('dc:title:');
		$title_remainder = $v->getPropertyValue('ea:manif:Title_Remainder');
		$this->ftsData->stuff->addA($title);
		$this->ftsData->opac->addA($title);
		$this->ftsData->opac->addB($title_remainder);
		$this->ftsData->subject->addA($title);
	}

	private function processPeriodicPublication( $v){
		$title =$v->getPropertyValue('dc:title:');
		$title_remainder = $v->getPropertyValue('ea:manif:Title_Remainder');
		$this->ftsData->stuff->addA($title);
		$this->ftsData->opac->addA($title);
		$this->ftsData->opac->addB($title_remainder);
		$this->ftsData->subject->addA($title);
	}


	private function processMedia( $v){
		$title =$v->getPropertyValue('dc:title:');
		$title_remainder = $v->getPropertyValue('ea:manif:Title_Remainder');
		$this->ftsData->stuff->addA($title);
		$this->ftsData->opac->addA($title);
		$this->ftsData->opac->addB($title_remainder);
		$this->ftsData->subject->addA($title);
	}


	private function processGenre( $v){

		$title =$v->getPropertyValue('dc:title:');
		$this->ftsData->stuff->addA($title);
		$this->ftsData->opac->addA($title);
		$this->ftsData->subject->addA($title);

	}
	//FIXME: traverse chain for fts data
	private function processSubjectChain( $v){
		$title =$v->getPropertyValue('dc:title:');
		$this->ftsData->stuff->addA($title);
		$this->ftsData->opac->addA($title);
		$this->ftsData->subject->addA($title);
	}


	private function processItem( $v){
		$title  = GRuleUtil::getLabel($v);
		if(empty($title)){
			$title =$v->getPropertyValue('ea:identifier:id');
		}
		$this->ftsData->subject->addA($title);
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
				$this->ftsData->stuff->addA($value);
			}
		}
	}


	/**
	 * @param GVertex $v
	 * @param FTSData $ftsData,
	 * @param string $title
	 */
	private function processGenericFields( $v, $ftsData,  $title = null){
		$title = empty($title)? $v->getPropertyValue('dc:title:'): $title;
		$obj_type = $v->getObjectType();

		$opac1 = $v->getAttribute('opac1');
		$opac2 = $v->getAttribute('opac2');
		$opac = array('opac1'=>$opac1,'opac2'=>$opac2);


		$ftsData->object_type = $obj_type;
		$ftsData->title = $title;
		$ftsData->id = $v->id();
		$ftsData->flags = $v->getFlags();
		$ftsData->create_date = '2001-05-20T17:33:18Z';
		$ftsData->status = $v->getPropertyValue('ea:status:');
		$ftsData->opac = $opac;
	}


	/**
	 * @param GVertex $v
	 */
	protected function processVertex( $v){


		$vid = $v->id();
		//$this->context->addDebugMessage("PGFTS: " . $vid);

		//Log::info("@:: PGFTS: " . $vid . ' :: ' . ($v->isReadOnly() ? 'TRUE' : 'FALSE'));

		$this->ftsData = isset($this->ftsDataArray[$vid]) ? $this->ftsDataArray[$vid] :  new VertextFtsData();

// 		$graph = $v->graph();
// 		$this->context->addDebugMessage("PgFTS process: " . $v);
// 		$dumpV  = GGraphUtil::dumpVertex($v,false);
// 		$this->context->addDebugMessage($dumpV);

		$id = $v->persistenceId();
		$obj_type = $v->getObjectType();


		if ($obj_type == 'auth-person'
				|| $obj_type == 'auth-family'
				|| $obj_type == 'auth-organization'
		){
			$this->processActor( $v);
		}elseif  ($obj_type == 'auth-concept'){
			$this->processConcept( $v);
		}elseif ($obj_type == 'auth-general'){
			$this->processGeneral( $v);
		}elseif  ($obj_type == 'auth-place'){
			$this->processPlace( $v);
		}elseif  ($obj_type == 'auth-event'){
			$this->processEvent( $v);
		}elseif  ($obj_type == 'auth-object'){
			$this->processObject( $v);
		}elseif  ($obj_type == 'lemma-category'){
			$this->processLemmaCategory( $v);
		}elseif  ($obj_type == 'lemma'){
			$this->processLemma( $v);
		}elseif  ($obj_type == 'web-site-instance'){
			$this->processWebSiteInstance( $v);
		}elseif  ($obj_type == 'periodic-publication'){
			$this->processPeriodicPublication( $v);
		}elseif  ($obj_type == 'media'){
			$this->processMedia( $v);
		}elseif  ($obj_type == 'auth-genre'){
			$this->processGenre( $v);
		}elseif  ($obj_type == 'digital-item' || $obj_type == 'physical-item'){
			$this->processItem( $v);
		}elseif  ($obj_type == 'auth-manifestation'){
			$this->processManifestation( $v);
		}elseif ($obj_type == 'auth-expression'){
			$this->processExpression( $v);
		}elseif ($obj_type == 'auth-work'){
			$this->processWork($v);
		}elseif ($obj_type == 'periodic'){
			$this->processPeriodic($v);
		}elseif ($obj_type == 'subject-chain'){
			$this->processSubjectChain($v);
		}elseif ($obj_type == 'collection'){
			$this->processCollection($v);
		}else {
			$this->ftsData->stuff->addA(GRuleUtil::getLabel($v));
		}

		$this->ftsDataArray[$vid] = $this->ftsData;


	}



	public function postExecute() {
		//Log::info('FTS POST EXECUTE');
		$this->context->put('FTS_VERTEX_DATA', $this->ftsDataArray);
	}




}

