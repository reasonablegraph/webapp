<?php

class GRuleSolr extends AbstractGruleProcessVertice implements GRule {

 	private $work_contributor_elements = null;
 	private $expression_contributor_elements = null;
	private $manifestation_contributor_elements = null;

	/**
	 *
	 * @var VertexSolrWorkData
	 */
	private $solrData;
	private $solrDataArray = array();

	protected function init(){

		$this->context->addDebugMessage("SOLR RULE INIT");

		$this->work_contributor_elements = array_keys(Setting::get('contributor_work_type_map'));
		$this->expression_contributor_elements =  array_keys(Setting::get('contributor_express_type_map'));
		$this->manifestation_contributor_elements =  array_keys(Setting::get('contributor_manif_type_map'));

		if (Config::get('arc.ENABLE_SOLR',1)>0){
			$cmd = new GRuleUpdateSolrCmd("opac_index", $this->context);
			$this->context->putCommand('V_UPDATE_SOLR',  $cmd);
		}


		$this->skip_readonly = true;

	}

	/**
	 *
	 * @param GVertex $v
	 */
	private function processWork($v) {


		$solrData = $this->solrData;
		// assign record type
		$solrData->record_type = 'work';

		// MAIN VERTEX ATTRIBUTES
		$solrData->id = $v->getPropertyValue('ea:identifier:id');
		$solrData->object_type = $v->getPropertyValue('ea:obj-type:');

		$opacdata = $v->getAttribute('opac1');
		$solrData->opac1 = json_encode($opacdata, JSON_UNESCAPED_UNICODE);

		$solrData->title = $v->getPropertyValue('dc:title:');
		$this->solrData->label = GRuleUtil::getLabel($v); //$v->getAttribute('label');

		// WORK_DESCRIPTIONS
		$work_descriptions = Config::get('arc.SOLR_LIST_FIELDS_DESCRIPTIONS');

		if (!empty($work_descriptions)){
			foreach ($work_descriptions as $description_field){
				$fieldProperties = $v->getProperties($description_field);
				if(!empty($fieldProperties)){
					foreach ($fieldProperties as $fprop){
						$this->solrData->descriptions[] = $fprop->value();
					}
				}
			}
		}

		//SUBJECTS
		$fn =function($i,$link,$chain) use ($solrData){
			$subject_text = GRuleUtil::getLabel($link);
			if (!in_array($subject_text, $solrData->subjects)) {
				$solrData->subjects[] = $subject_text;
			}
		};
		GRuleUtil::travesrseSubjectsLinks($v,$fn);


		// TODO = contributors[]

		// AUTHORS
		$authors = $v->getVertices(GDirection::OUT,'ea:work:authorWork');
		if (!empty($authors)){
			foreach ($authors as $author) {
				$author_name = $author->getPropertyValue('dc:title:');
				$author_name_with_id = $author_name . '‡' . $author->id();
				$this->solrData->authors[] = $author_name;
				$this->solrData->authors_with_ids[] = $author_name_with_id;
			}
		}

		// MANIFESTATIONS
		$manifestationsRaw = $v->getVertices(GDirection::IN, 'ea:work:');
		$manifestationsInferred = $v->getVertices(GDirection::IN, 'inferred:ea:work:');
		$manifestations = array_merge($manifestationsRaw, $manifestationsInferred);
		$countDigitalItems = 0;
		if (!empty($manifestations)){
			foreach ($manifestations as $manif){

				//subjects_manif
				$fn =function($i,$link,$chain) use ($solrData){
					$subject_text = GRuleUtil::getLabel($link);
					if (!in_array($subject_text, $solrData->subjects_manif)) {
						$solrData->subjects_manif[] = $subject_text;
					}
				};
				GRuleUtil::travesrseSubjectsLinks($manif,$fn);


				/* @var $manif GVertex */
				$this->solrData->secondaryTitles[] = $manif->getPropertyValue('dc:title:');
				$work_manif_secondary_title = Config::get('arc.SOLR_LIST_FIELDS_WORK_MANIF_SECONDARY_TITLES');
				if (!empty($work_manif_secondary_title)){
					foreach ($work_manif_secondary_title as $secondary_title){
						$this->solrData->secondaryTitles[] = $manif->getPropertyValue($secondary_title);
					}
				}

// 				$this->solrData->publication_types[] = $manif->getPropertyValue('ea:manif:Type');
				$pub_type = $manif->getProperty('ea:manif:Type');
				if (!empty($pub_type)){
					$tmp =$pub_type->value();
					$this->solrData->publication_types[] = ($tmp != 'undefined') ?  $pub_type->prps('selected_value') : null;
				}

// 				// PUBLICATION PLACE - PUBLISHER NAME
// 				$ps = $manif->getProperties('ea:manif:Publication');
// 				if (!empty($ps)){
// 					foreach ($ps as $id => $p){
// 						$tid = $p->treeId();
// 						if (!empty($tid)){
// 							$tps = $manif->getChildProperties($tid);
// 							foreach ($tps as $tp){
// 								$prps = $tp->prps();
// 								if ($tp->element() == 'ea:manif:Publication_Place'){
// // 									$this->solrData->publication_places[] = $tp->value();
// 									$placeLiteral = $tp->value();
// 									$this->solrData->publication_places[] = $placeLiteral;
// 									$this->solrData->publication_places_ids[] = $tp->refItem();
// 									$pplace_with_id = $placeLiteral . '‡' . $tp->refItem();
// 									$this->solrData->publication_places_with_ids[] = $pplace_with_id;
// 								}else if ($tp->element() == 'ea:manif:Publisher_Name'){
// // 									$this->solrData->publishers[] = $tp->value();
// 									$publisherLiteral = $tp->value();
// 									$this->solrData->publishers[] = $publisherLiteral;
// 									$publisher_with_id = $publisherLiteral . '‡' . $tp->refItem();
// 									$this->solrData->publishers_with_ids[] = $publisher_with_id;
// 								}
// 							}
// 						}
// 					}
// 				}

// // 				ONLY standard Publication Place
// // 				$pplaces = $manif->getVertices(GDirection::OUT,'ea:manif:Publication_Place');
// // 				foreach ($pplaces as $pplace){
// // 					$placeLiteral = GRuleUtil::getLabel($pplace);
// // 					$pplace_with_id = $placeLiteral . '‡' . $pplace->id();
// // 					$this->solrData->publication_places[] = $placeLiteral;
// // 					Log::info("SOLR: adding publication_place_with_id: " . $pplace_with_id);
// // 					$this->solrData->publication_places_with_ids[] = $pplace_with_id;
// // 				}


// // 				ONLY standard publishers
// // 				$publishers = $manif->getVertices(GDirection::OUT,'ea:manif:Publisher_Name');
// // 				foreach ($publishers as $publisher){
// // 					$this->solrData->publishers[] = GRuleUtil::getLabel($publisher);
// // 				}





				$artifacts = $manif->getVertices(GDirection::IN, 'ea:artifact-of:');

				if (!empty($artifacts)) {
					foreach ($artifacts as $artf) {
						$this->solrData->digital_item_types[] = $artf->getPropertyValue('ea:item:type');
					}
				}

				$countDigitalItems += count($artifacts);

				//Lang
				$langs = $manif->getTmpAttribute('Manif_lang');
				if (!empty($langs)){
					foreach ($langs as $lang){
						$this->solrData->languages[] = $lang;
					}
				}

			}
		}

		$this->solrData->num_of_digital_items = $countDigitalItems;
		$this->solrData->num_of_manifestations = count($manifestations);

		// EXPRESSIONS
		$expressions = $v->getVertices(GDirection::IN, 'ea:expressionOf:');
		if (!empty($expressions)){
			foreach ($expressions as $expr){
				$this->solrData->secondaryTitles[] = $expr->getPropertyValue('dc:title:');
				// TODO = get secondary titles of expression. Do these exist? How to get them?
			}
		}

	}

	/**
	 *
	 * @param GVertex $v
	 */
	private function processPerson($v) {

		// assign record type
		$this->solrData->record_type = 'person';

		// MAIN VERTEX ATTRIBUTES
		$this->solrData->id = $v->getPropertyValue('ea:identifier:id');
		$this->solrData->object_type = $v->getPropertyValue('ea:obj-type:');

		$opacdata = $v->getAttribute('opac1');
		$this->solrData->opac1 = json_encode($opacdata, JSON_UNESCAPED_UNICODE);

		$this->solrData->title = $v->getPropertyValue('dc:title:');
		$this->solrData->label = GRuleUtil::getLabel($v);//$v->getAttribute('label');

		$person_secondary_title = Config::get('arc.SOLR_LIST_FIELDS_PERSON_SECONDARY_TITLES');

		if (!empty($person_secondary_title)){
			foreach ($person_secondary_title as $secondary_title){
				$this->solrData->secondaryTitles[] =$v->getPropertyValue($secondary_title);
			}
		}

		$person_descriptions = Config::get('arc.SOLR_LIST_FIELDS_DESCRIPTIONS');
		if (!empty($person_descriptions)){
			foreach ($person_descriptions as $description_field){
				$fieldProperties = $v->getProperties($description_field);
				if(!empty($fieldProperties)){
					foreach ($fieldProperties as $fprop){
						$this->solrData->descriptions[] = $fprop->value();
					}
				}
			}
		}

	}

	/**
	 *
	 * @param GVertex $v
	 */
	private function processOrganization($v) {

		// assign record type
		$this->solrData->record_type = 'organization';

		// MAIN VERTEX ATTRIBUTES
		$this->solrData->id = $v->getPropertyValue('ea:identifier:id');
		$this->solrData->object_type = $v->getPropertyValue('ea:obj-type:');

		$opacdata = $v->getAttribute('opac1');
		$this->solrData->opac1 = json_encode($opacdata, JSON_UNESCAPED_UNICODE);

		$this->solrData->title = $v->getPropertyValue('dc:title:');
		$this->solrData->label = GRuleUtil::getLabel($v);//$v->getAttribute('label');

		$organization_secondary_title = Config::get('arc.SOLR_LIST_FIELDS_ORGANIZATION_SECONDARY_TITLES');
		if (!empty($organization_secondary_title)){
			foreach ($organization_secondary_title as $secondary_title_field){
				$fieldProperties = $v->getProperties($secondary_title_field);
				if(!empty($fieldProperties)){
					foreach ($fieldProperties as $fprop){
						$this->solrData->secondaryTitles[] =$fprop->value();
					}
				}
			}
		}

		$organization_descriptions = Config::get('arc.SOLR_LIST_FIELDS_DESCRIPTIONS');
		if (!empty($organization_descriptions)){
			foreach ($organization_descriptions as $description_field){
				$fieldProperties = $v->getProperties($description_field);
				if(!empty($fieldProperties)){
					foreach ($fieldProperties as $fprop){
						$this->solrData->descriptions[] = $fprop->value();
					}
				}
			}
		}

	}

	/**
	 *
	 * @param GVertex $v
	 */
	private function processFamily($v) {

		// assign record type
		$this->solrData->record_type = 'family';

		// MAIN VERTEX ATTRIBUTES
		$this->solrData->id = $v->getPropertyValue('ea:identifier:id');
		$this->solrData->object_type = $v->getPropertyValue('ea:obj-type:');

		$opacdata = $v->getAttribute('opac1');
		$this->solrData->opac1 = json_encode($opacdata, JSON_UNESCAPED_UNICODE);

		$this->solrData->title = $v->getPropertyValue('dc:title:');
		$this->solrData->label = GRuleUtil::getLabel($v);//$v->getAttribute('label');

		$family_descriptions = Config::get('arc.SOLR_LIST_FIELDS_DESCRIPTIONS');
		if (!empty($family_descriptions)){
			foreach ($family_descriptions as $description_field){
				$fieldProperties = $v->getProperties($description_field);
				if(!empty($fieldProperties)){
					foreach ($fieldProperties as $fprop){
						$this->solrData->descriptions[] = $fprop->value();
					}
				}
			}
		}

	}

	/**
	 *
	 * @param GVertex $v
	 */
	private function processPlace($v) {

		// assign record type
		$this->solrData->record_type = 'place';

		// MAIN VERTEX ATTRIBUTES
		$this->solrData->id = $v->getPropertyValue('ea:identifier:id');
		$this->solrData->object_type = $v->getPropertyValue('ea:obj-type:');

		$opacdata = $v->getAttribute('opac1');
		$this->solrData->opac1 = json_encode($opacdata, JSON_UNESCAPED_UNICODE);

		$this->solrData->title = $v->getPropertyValue('dc:title:');
		$this->solrData->label = GRuleUtil::getLabel($v); //$v->getAttribute('label');

	}

	/**
	 *
	 * @param GVertex $v
	 */
	private function processConcept($v) {

		// assign record type
		$this->solrData->record_type = 'concept';

		// MAIN VERTEX ATTRIBUTES
		$this->solrData->id = $v->getPropertyValue('ea:identifier:id');
		$this->solrData->object_type = $v->getPropertyValue('ea:obj-type:');

		$opacdata = $v->getAttribute('opac1');
		$this->solrData->opac1 = json_encode($opacdata, JSON_UNESCAPED_UNICODE);

		$this->solrData->title = $v->getPropertyValue('dc:title:');
		$this->solrData->label = GRuleUtil::getLabel($v); //$v->getAttribute('label');

	}

	/**
	 *
	 * @param GVertex $v
	 */
	private function processObject($v) {

		// assign record type
		$this->solrData->record_type = 'object';

		// MAIN VERTEX ATTRIBUTES
		$this->solrData->id = $v->getPropertyValue('ea:identifier:id');
		$this->solrData->object_type = $v->getPropertyValue('ea:obj-type:');

		$opacdata = $v->getAttribute('opac1');
		$this->solrData->opac1 = json_encode($opacdata, JSON_UNESCAPED_UNICODE);

		$this->solrData->title = $v->getPropertyValue('dc:title:');
		$this->solrData->label = GRuleUtil::getLabel($v); //$v->getAttribute('label');

	}


	/**
	 *
	 * @param GVertex $v
	 */
	private function processGeneral($v) {

		// assign record type
		$this->solrData->record_type = 'general';

		// MAIN VERTEX ATTRIBUTES
		$this->solrData->id = $v->getPropertyValue('ea:identifier:id');
		$this->solrData->object_type = $v->getPropertyValue('ea:obj-type:');

		$opacdata = $v->getAttribute('opac1');
		$this->solrData->opac1 = json_encode($opacdata, JSON_UNESCAPED_UNICODE);

		$this->solrData->title = $v->getPropertyValue('dc:title:');
		$this->solrData->label = GRuleUtil::getLabel($v); //$v->getAttribute('label');

	}



	/**
	 *
	 * @param GVertex $v
	 */
	private function processEvent($v) {

		// assign record type
		$this->solrData->record_type = 'event';

		// MAIN VERTEX ATTRIBUTES
		$this->solrData->id = $v->getPropertyValue('ea:identifier:id');
		$this->solrData->object_type = $v->getPropertyValue('ea:obj-type:');

		$opacdata = $v->getAttribute('opac1');
		$this->solrData->opac1 = json_encode($opacdata, JSON_UNESCAPED_UNICODE);

		$this->solrData->title = $v->getPropertyValue('dc:title:');
		$this->solrData->label = GRuleUtil::getLabel($v); //$v->getAttribute('label');

	}

	/**
	 *
	 * @param GVertex $v
	 */
	private function processGenre($v) {

		// assign record type
		$this->solrData->record_type = 'genre';

		// MAIN VERTEX ATTRIBUTES
		$this->solrData->id = $v->getPropertyValue('ea:identifier:id');
		$this->solrData->object_type = $v->getPropertyValue('ea:obj-type:');

		$opacdata = $v->getAttribute('opac1');
		$this->solrData->opac1 = json_encode($opacdata, JSON_UNESCAPED_UNICODE);

		$this->solrData->title = $v->getPropertyValue('dc:title:');
		$this->solrData->label = GRuleUtil::getLabel($v); //$v->getAttribute('label');

	}

	/**
	 *
	 * @param GVertex $v
	 */
	private function processLemma($v) {

		// assign record type
		$this->solrData->record_type = 'lemma';

		// MAIN VERTEX ATTRIBUTES
		$this->solrData->id = $v->getPropertyValue('ea:identifier:id');
		$this->solrData->object_type = $v->getPropertyValue('ea:obj-type:');

		$opacdata = $v->getAttribute('opac1');
		$this->solrData->opac1 = json_encode($opacdata, JSON_UNESCAPED_UNICODE);

		$this->solrData->title = $v->getPropertyValue('dc:title:');
		$this->solrData->label = GRuleUtil::getLabel($v); //$v->getAttribute('label');

	}


	/**
	 *
	 * @param GVertex $v
	 */
	private function processMedia($v) {

		// assign record type
		$this->solrData->record_type = 'media';

		// MAIN VERTEX ATTRIBUTES
		$this->solrData->id = $v->getPropertyValue('ea:identifier:id');
		$this->solrData->object_type = $v->getPropertyValue('ea:obj-type:');

		$opacdata = $v->getAttribute('opac1');
		$this->solrData->opac1 = json_encode($opacdata, JSON_UNESCAPED_UNICODE);

		$this->solrData->title = $v->getPropertyValue('dc:title:');
		$this->solrData->label = GRuleUtil::getLabel($v); //$v->getAttribute('label');

	}


	/**
	 *
	 * @param GVertex $v
	 */
	private function processWebSiteInstance($v) {

		// assign record type
		$this->solrData->record_type = 'web-site-instance';

		// MAIN VERTEX ATTRIBUTES
		$this->solrData->id = $v->getPropertyValue('ea:identifier:id');
		$this->solrData->object_type = $v->getPropertyValue('ea:obj-type:');

		$opacdata = $v->getAttribute('opac1');
		$this->solrData->opac1 = json_encode($opacdata, JSON_UNESCAPED_UNICODE);

		$this->solrData->title = $v->getPropertyValue('dc:title:');
		$this->solrData->label = GRuleUtil::getLabel($v); //$v->getAttribute('label');

	}


	/**
	 *
	 * @param GVertex $v
	 */
	private function processPeriodicPublication($v) {

		// assign record type
		$this->solrData->record_type = 'periodic-publication';

		// MAIN VERTEX ATTRIBUTES
		$this->solrData->id = $v->getPropertyValue('ea:identifier:id');
		$this->solrData->object_type = $v->getPropertyValue('ea:obj-type:');

		$opacdata = $v->getAttribute('opac1');
		$this->solrData->opac1 = json_encode($opacdata, JSON_UNESCAPED_UNICODE);

		$this->solrData->title = $v->getPropertyValue('dc:title:');
		$this->solrData->label = GRuleUtil::getLabel($v); //$v->getAttribute('label');

	}



	/**
	 *
	 * @param GVertex $v
	 */
	private function processExpressionAsWork($v) {

		// assign record type
		if ($v->getObjectType() == 'auth-expression') {
			$this->solrData->record_type = 'work';
		}

		$this->solrData->label = GRuleUtil::getLabel($v); //$v->getAttribute('label');

		// MAIN VERTEX ATTRIBUTES
		$this->solrData->id = $v->getPropertyValue('ea:identifier:id');
		$this->solrData->object_type = $v->getPropertyValue('ea:obj-type:');

		$opacdata = $v->getAttribute('opac1');
		$this->solrData->opac1 = json_encode($opacdata, JSON_UNESCAPED_UNICODE);

		$this->solrData->title = $v->getPropertyValue('dc:title:');

		// WORK_DESCRIPTIONS
		$work_descriptions = Config::get('arc.SOLR_LIST_FIELDS_DESCRIPTIONS');
		if (!empty($work_descriptions)){
			foreach ($work_descriptions as $description_field){
				$fieldProperties = $v->getProperties($description_field);
				if(!empty($fieldProperties)){
					foreach ($fieldProperties as $fprop){
						$this->solrData->descriptions[] = $fprop->value();
					}
				}
			}
		}

		// SUBJECTS
		$subjects = $v->getVertices(GDirection::OUT,'ea:subj:');
		if (!empty($subjects)){
			foreach ($subjects as $subject) {
				$this->solrData->subjects[] = $subject->getPropertyValue('dc:title:');
			}
		}

		// TODO = contributors[]

		// AUTHORS
		$authors = $v->getVertices(GDirection::OUT,'ea:work:authorWork');
		if (!empty($authors)){
		foreach ($authors as $author) {
				$author_name = $author->getPropertyValue('dc:title:');
				$author_name_with_id = $author_name . '‡' . $author->id();
				$this->solrData->authors[] = $author_name;
				$this->solrData->authors_with_ids[] = $author_name_with_id;
			}
		}


		// MANIFESTATIONS
		$manifestationsRaw = $v->getVertices(GDirection::IN, 'ea:work:');
		$manifestationsInferred = $v->getVertices(GDirection::IN, 'inferred:ea:work:');
		$manifestations = array_merge($manifestationsRaw, $manifestationsInferred);
		if (!empty($manifestations)){
			foreach ($manifestations as $manif){
				$this->solrData->secondaryTitles[] = $manif->getPropertyValue('dc:title:');
				$work_manif_secondary_title = Config::get('arc.SOLR_LIST_FIELDS_WORK_MANIF_SECONDARY_TITLES');
				if (!empty($work_manif_secondary_title)){
					foreach ($work_manif_secondary_title as $secondary_title){
						$this->solrData->secondaryTitles[] = $manif->getPropertyValue($secondary_title);
					}
				}

				$artifacts = $manif->getVertices(GDirection::IN, 'ea:artifact-of:');

				if (!empty($artifacts)) {
					foreach ($artifacts as $artf) {
						$this->solrData->digital_item_types[] = $artf->getPropertyValue('ea:item:type');
					}
				}

				$this->solrData->num_of_digital_items = count($artifacts);

			}
		}

		$this->solrData->num_of_manifestations = count($manifestations);

	}

	/**
	 *
	 * @param GVertex $v
	 */
	private function processManifestation($v, $asWork) {

		$solrData = $this->solrData;
		// assign record type
		if ($asWork) {
			$this->solrData->record_type = 'work';
		} else {
			$this->solrData->record_type = 'manifestation';
		}

		// MAIN VERTEX ATTRIBUTES
		if ($asWork) {
			$this->solrData->id = $v->getPropertyValue('ea:identifier:id');
		} else {
			$this->solrData->id = 'MANIF:' . $v->getPropertyValue('ea:identifier:id');
		}

		$this->solrData->object_type = $v->getPropertyValue('ea:obj-type:');

		$opacdata = $v->getAttribute('opac1');
		$this->solrData->opac1 = json_encode($opacdata, JSON_UNESCAPED_UNICODE);

		$this->solrData->title = $v->getPropertyValue('dc:title:');

		$label = GRuleUtil::getLabel($v); //$v->getAttribute('label');
		$this->solrData->label = $label;

		// WORK_DESCRIPTIONS
		$work_descriptions = Config::get('arc.SOLR_LIST_FIELDS_DESCRIPTIONS');
		if (!empty($work_descriptions)){
			foreach ($work_descriptions as $description_field){
				$fieldProperties = $v->getProperties($description_field);
				if(!empty($fieldProperties)){
					foreach ($fieldProperties as $fprop){
						$this->solrData->descriptions[] = $fprop->value();
					}
				}
			}
		}



		//SUBJECTS FROM MANIF
		$fn =function($i,$link,$chain) use ($solrData){
			$subject_text = GRuleUtil::getLabel($link);
			if (!in_array($subject_text, $solrData->subjects_manif)) {
				$solrData->subjects_manif[] = $subject_text;
				$solrData->subjects_ids[] = $link->id();
			}
		};
		GRuleUtil::travesrseSubjectsLinks($v,$fn);

		//SUBJECTS FROM WORK
		$direct = array();
		$indirect = array();
		if (!empty($v->getVertices(GDirection::OUT,'ea:work:'))){
			$direct = $v->getVertices(GDirection::OUT,'ea:work:');
		}
		if(!empty($v->getVertices(GDirection::OUT,'inferred:ea:work:'))){
			$indirect = $v->getVertices(GDirection::OUT,'inferred:ea:work:');
		}
		$works =  array_merge($direct,$indirect);
		if (!empty($works)){
			$fn =function($i,$link,$chain) use ($solrData){
				$subject_text = GRuleUtil::getLabel($link);
				if (!in_array($subject_text, $solrData->subjects_manif)) {
					$solrData->subjects[] = $subject_text;
					$solrData->subjects_ids[] = $link->id();
				}
			};
			foreach ($works as $work) {
				GRuleUtil::travesrseSubjectsLinks($work,$fn);
			}
		}

		// TODO = contributors[]

		// AUTHORS
		$authors = $v->getVertices(GDirection::OUT,'ea:work:authorWork');
		if (!empty($authors)){
			foreach ($authors as $author) {

// 				mallon ksemeine apo paliotera, to kano comment out (V.S.)
// 				$this->solrData->workAuthors[] = $author->getPropertyValue('dc:title:');

				$author_name = $author->getPropertyValue('dc:title:');
				$author_name_with_id = $author_name . '‡' . $author->id();
				$solrData->authors[] = $author_name;
				$solrData->authors_with_ids[] = $author_name_with_id;
				//Log::info(print_r($solrData->authors_with_ids,true));
			}
		}

		// PUBLICATION PLACE - PUBLISHER NAME
		$ps = $v->getProperties('ea:manif:Publication');
		if (!empty($ps)){
			foreach ($ps as $id => $p){
				$tid = $p->treeId();
				if (!empty($tid)){
					$tps = $v->getChildProperties($tid);
					foreach ($tps as $tp){
						//$prps = $tp->prps();
						if ($tp->element() == 'ea:manif:Publication_Place'){
							$tp_ref = $tp->refItem();
							$tp_value =  ( empty($tp_ref) ? '[' . $tp->value()  .']': $tp->value());
							$this->solrData->publication_places[] = $tp_value;
							$this->solrData->publication_places_ids[] = $tp_ref;
							$pplace_with_id = $tp_value . '‡' . $tp_ref;
							$this->solrData->publication_places_with_ids[] = $pplace_with_id;
						}else if ($tp->element() == 'ea:manif:Publisher_Name'){
							$tp_ref = $tp->refItem();
							$tp_value =  ( empty($tp_ref) ? '[' . $tp->value()  .']': $tp->value());
							$this->solrData->publishers[] = $tp_value;
							$this->solrData->publishers_ids[] = $tp_ref;
							$publisher_with_id = $tp_value . '‡' . $tp_ref;
							$this->solrData->publishers_with_ids[] = $publisher_with_id;
						}
					}
				}
			}
		}


		//MANIF TYPE
		$manif_type = $v->getProperty('ea:manif:Type');
		if (!empty($manif_type)){
			$tmp = $manif_type->value();
			$this->solrData->publication_types[] = ($tmp != 'undefined') ?  $manif_type->prps('selected_value') : null;
		}


		//MANIF LANG
		$langs = $v->getTmpAttribute('Manif_lang');
		if (!empty($langs)){
			foreach ($langs as $lang){
				$this->solrData->languages[] = $lang;
			}
		}

		$artifacts = $v->getVertices(GDirection::IN, 'ea:artifact-of:');

		if (!empty($artifacts)) {
			foreach ($artifacts as $artf) {
				$this->solrData->digital_item_types[] = $artf->getPropertyValue('ea:item:type');
			}
		}

		if ($asWork) {
			$this->solrData->num_of_manifestations = 1;
			$this->solrData->num_of_digital_items = count($artifacts);
		} else {
			$this->solrData->num_of_manifestations = 0;
			$this->solrData->num_of_digital_items = 0;
		}

	}

	/**
	 * @param GVertex $v
	 */
	protected function processVertex($v){

		 /** Solr record types in use:
		 * 	- dns (do not show)
		 * 	- work
		 * 	- person
		 * 	- org
		 * 	- family
		 * 	- place
		 * 	- concept
		 * 	- object
		 * 	- event
		 * 	- genre
		 * 	- subject
		 */

		$vid = $v->id();
		$object_status = $v->getPropertyValue("ea:status:");
		//Log::info("SOLR: PROCESSING VERTEX " . $vid . " (" . $v->getObjectType() . ") WITH STATUS: " . $object_status);

		if ($object_status != 'finish') {
			//Log::info("SOLR: SKIPPED VERTEX " . $vid . " (NOT IN FINISH STATUS)");
			return;
		}


		$obj_type = $v->getObjectType();

		if ($obj_type == 'auth-work') {
			if ( !empty($v->getVertices(GDirection::IN,'inferred:ea:work:')) || !empty($v->getVertices(GDirection::IN,'ea:work:')) ) {//?
				$this->solrData = isset($this->solrDataArray[$vid]) ? $this->solrDataArray[$vid] :  new VertexSolrWorkData();
				$this->processWork($v);
				$this->solrDataArray[$vid] = $this->solrData;
			}
		}

		else if ($obj_type == 'auth-person') {
			$this->solrData = isset($this->solrDataArray[$vid]) ? $this->solrDataArray[$vid] :  new VertexSolrWorkData();
			$this->processPerson($v);
			$this->solrDataArray[$vid] = $this->solrData;
		}

		else if ($obj_type == 'auth-organization') {
			$this->solrData = isset($this->solrDataArray[$vid]) ? $this->solrDataArray[$vid] :  new VertexSolrWorkData();
			$this->processOrganization($v);
			$this->solrDataArray[$vid] = $this->solrData;
		}

		else if ($obj_type == 'auth-family') {
			$this->solrData = isset($this->solrDataArray[$vid]) ? $this->solrDataArray[$vid] :  new VertexSolrWorkData();
			$this->processFamily($v);
			$this->solrDataArray[$vid] = $this->solrData;
		}

		else if ($obj_type == 'auth-place' ) {
			$this->solrData = isset($this->solrDataArray[$vid]) ? $this->solrDataArray[$vid] :  new VertexSolrWorkData();
			$this->processPlace($v);
			$this->solrDataArray[$vid] = $this->solrData;
		}

		else if ($obj_type == 'auth-concept' ) {
			$this->solrData = isset($this->solrDataArray[$vid]) ? $this->solrDataArray[$vid] :  new VertexSolrWorkData();
			$this->processConcept($v);
			$this->solrDataArray[$vid] = $this->solrData;
		}

		else if ($obj_type == 'auth-object' ) {
			$this->solrData = isset($this->solrDataArray[$vid]) ? $this->solrDataArray[$vid] :  new VertexSolrWorkData();
			$this->processObject($v);
			$this->solrDataArray[$vid] = $this->solrData;
		}

		else if ($obj_type == 'auth-general' ) {
			$this->solrData = isset($this->solrDataArray[$vid]) ? $this->solrDataArray[$vid] :  new VertexSolrWorkData();
			$this->processGeneral($v);
			$this->solrDataArray[$vid] = $this->solrData;
		}

		else if ($obj_type == 'auth-event' ) {
			$this->solrData = isset($this->solrDataArray[$vid]) ? $this->solrDataArray[$vid] :  new VertexSolrWorkData();
			$this->processEvent($v);
			$this->solrDataArray[$vid] = $this->solrData;
		}

		else if ($obj_type == 'auth-genre' ) {
			$this->solrData = isset($this->solrDataArray[$vid]) ? $this->solrDataArray[$vid] :  new VertexSolrWorkData();
			$this->processGenre($v);
			$this->solrDataArray[$vid] = $this->solrData;
		}

		else if ($obj_type == 'auth-expression') {
			if (empty($v->getVertices(GDirection::OUT, 'ea:expressionOf:'))) {
				$this->solrData = isset($this->solrDataArray[$vid]) ? $this->solrDataArray[$vid] :  new VertexSolrWorkData();
				$this->processExpressionAsWork($v);
				$this->solrDataArray[$vid] = $this->solrData;
			}
		}

		else if ($obj_type == 'auth-manifestation') {

			// index as manifestation anyway
			$vidManif = 'MANIF:' . $vid;
			$this->solrData = isset($this->solrDataArray[$vidManif]) ? $this->solrDataArray[$vidManif] :  new VertexSolrWorkData();
			$this->processManifestation($v, false);
			$this->solrDataArray[$vidManif] = $this->solrData;

			// if orphan index also as work //TODO orphan delete
// 			if (empty($v->getVertices(GDirection::OUT, 'ea:work:'))) {
// 				$this->solrData = isset($this->solrDataArray[$vid]) ? $this->solrDataArray[$vid] :  new VertexSolrWorkData();
// 				$this->processManifestation($v, true);
// 				$this->solrDataArray[$vid] = $this->solrData;
// 			}

		}

		else if ($obj_type == 'lemma' ) {
			$this->solrData = isset($this->solrDataArray[$vid]) ? $this->solrDataArray[$vid] :  new VertexSolrWorkData();
			$this->processLemma($v);
			$this->solrDataArray[$vid] = $this->solrData;
		}

		else if ($obj_type == 'media' ) {
			$this->solrData = isset($this->solrDataArray[$vid]) ? $this->solrDataArray[$vid] :  new VertexSolrWorkData();
			$this->processMedia($v);
			$this->solrDataArray[$vid] = $this->solrData;
		}

		else if ($obj_type == 'web-site-instance' ) {
			$this->solrData = isset($this->solrDataArray[$vid]) ? $this->solrDataArray[$vid] :  new VertexSolrWorkData();
			$this->processWebSiteInstance($v);
			$this->solrDataArray[$vid] = $this->solrData;
		}

		else if ($obj_type == 'periodic-publication' ) {
			$this->solrData = isset($this->solrDataArray[$vid]) ? $this->solrDataArray[$vid] :  new VertexSolrWorkData();
			$this->processPeriodicPublication($v);
			$this->solrDataArray[$vid] = $this->solrData;
		}

		if(empty($this->solrData)){
			Log::info("SOLR UNKNOWN NODE: " . $v->getObjectType() .  ' :: ' .  $v->urnStr());
			return;
		}

		$date_avail = $v->getPropertyValue('dc:date:available');

		//$phpdate = strtotime( $date_issued );
		if(!empty($date_avail)){
			$this->solrData->create_dt = new DateTime($date_avail);
		}

		//$v->setAttribute('solr_data',json_encode($this->solrData->arrayValue(),JSON_UNESCAPED_UNICODE));
		$v->setAttribute('solr_data',$this->solrData->arrayValue());
		//Log::info(print_r($this->solrData,true));

		// an einai flaged os subject kane to is_subject TRUE
		if ($v->hasFlag('IS:subject')) {
			// 		if (!empty($v->getVertices(GDirection::IN, 'ea:subj:'))) {
			//	Log::info("SOLR: Found subject flagged record");
			$this->solrData->is_subject = true;
		}

		// remove duplicates from $languages array
		if (!empty($this->solrData->languages)) {
			$tmp = array_unique($this->solrData->languages);
			$this->solrData->languages = $tmp;
		}

	}

	public function postExecute() {
		$this->context->put('SOLR_VERTEX_DATA', $this->solrDataArray);
	}

}

?>

