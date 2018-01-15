<?php

class GRuleManifestation  extends AbstractGruleProcessVertice implements GRule {


	/**
	 * @param GVertex $v
	 */
	protected function labelForManifestation( $v){
		//$context = $this->context;
		$graph = $v->graph();
		//$context->addDebugMessage("GRuleOpacPrepareManifestation process: " . $v);

		$title = $v->getPropertyValue('dc:title:');

		$issue = $v->getPropertyValue('ea:issue:');
		$m_title_medium = $v->getPropertyValue('ea:manif:Title_Medium');
		$m_title_remainder = $v->getPropertyValue('ea:manif:Title_Remainder');
		// 		$m_title_responsibility = $v->getPropertyValue('ea:manif:Title_Responsibility');
		$m_title_partNumber = $v->getPropertyValue('ea:manif:Title_PartNumber');
		$m_title_partName = $v->getPropertyValue('ea:manif:Title_PartName');
		$m_distribution = $v->getPropertyValue('ea:manif:Distribution');
		$m_production = $v->getPropertyValue('ea:manif:Production');
		$m_manufactur = $v->getPropertyValue('ea:manif:Manufactur');
		$m_edition = $v->getProperty('ea:manif:Edition_Statement');
// 		$m_publication = $v->getPropertyValue('ea:manif:Publication');


		//Publication
		$m_publication = null;
		$ps = $v->getProperties('ea:manif:Publication');
		if (!empty($ps)){
			foreach ($ps as $psid => $p){
				$tid = $p->treeId();

				$publication_date = null;
				$publication_place = array();
				$places_arr = array();
				$publication_name = array();
				$names_arr = array();
				$i=0;
				$j=0;

				if (!empty($tid)){
					$tps = $v->getChildProperties($tid);
					foreach ($tps as $tp){
						/* @var GPValueTree $tp */
						if ($tp->element() == 'ea:manif:Publication_Place'){
							$place_title=$tp->value();
							$refItem = $tp->refItem();
							$tmp_v = $graph->getVertexByPersisteceId($refItem);
							if (!empty($tmp_v)) {
								$place_title = $tmp_v->getPropertyValue('dc:title:');
							}
							$i++;
							$places_arr['value'] = $place_title;
							if($i>1){
								$places_arr['delimiter'] = 'true';
							}
							$publication_place['place'][] = $places_arr;
						}elseif ($tp->element() == 'ea:manif:Publisher_Name'){
							$name_title=$tp->value();
							$refItem = $tp->refItem();
							$tmp_v = $graph->getVertexByPersisteceId($refItem);
							if (!empty($tmp_v)) {
// 								$name_title = $tmp_v->getPropertyValue('dc:title:');
// 								$org_subdivision = $tmp_v->getPropertyValue('ea:auth:Organization_Subdivision');
								$name_title = $tmp_v->getPropertyValue('ea:label:');
								if (empty($name_title)){
									$name_title = $tmp_v->getPropertyValue('dc:title:');
								}
							}
							$j++;
							$names_arr['value'] = $name_title;
							if($j>1){
								$names_arr['delimiter'] = 'true';
							}
							$publication_name['name'][] = $names_arr;
						}elseif ($tp->element() == 'ea:manif:Publication_Date'){
							$publication_date =  $tp->valueJson();
							if(!empty($publication_date['t'])){
								$publication_date = $publication_date['t'];
							}else{
								$publication_date = $publication_date['y'];
							}
						}
					}
				}
			}

		$var_publication_templ = array(
				'publication_place' => $publication_place,
				'publication_name' => $publication_name,
				'publication_date' => $publication_date,
		);

		$template = Config::get('arc_display_template.manifestation_publication');
		$m_publication = ArcTemplateEngine::renderLine($template,$var_publication_templ);

		$v->setTmpAttribute('publication', $m_publication);

		}

		$issue_publication_year = null;
		$issue_publication_month = null;
		$issue_publication_day = null;
		$manif_type = $v->getPropertyValue('ea:form-type:');
		if (!empty($manif_type)){
			if($manif_type == 'issue'){
				$publication_date = $v->getProperty('ea:manif:Publication_Date');
				if(!empty($publication_date)){
					$publication_date = $publication_date->valueJson();
					$issue_publication_year = $publication_date['y'];
					$issue_publication_months = $publication_date['m'];
					$issue_publication_days = $publication_date['d'];
					if (preg_match("/^[\d]+\/[\d]+$/", $issue_publication_months)) {
						$months = explode("/", $issue_publication_months);
						for ($i = 0; $i < count($months); $i++) {
							$m = intval($months[$i]);
							if ($m <= 12 && $m >= 1){
								$issue_publication_month .= tr('acc_'.$m);
								if(end($months) != $m){
									$issue_publication_month .='/';
								}
							}
						}
					}else{
						if(!empty($issue_publication_months)){
						$issue_publication_month = tr('acc_'.$issue_publication_months);
						}
					}

					if (preg_match("/^[\d]+\/[\d]+$/", $issue_publication_days)) {
						$issue_publication_day = $publication_date['d'];
					}



				}
			}
		}


		if (!empty($m_edition)){
			$m_edition = $m_edition->pnctn();
		}

		// 			$authors_names = '';
		// 			$authors = $v->getVertices(GDirection::OUT,'ea:inferred-work:authorWork');
		// 			$sep = '';
		// 			foreach ($authors as $author){
		// 				//Log::info(print_r(GGraphUtil::dumpVertex($author)));
			// 				$authors_names .= $sep . $author->getPropertyValue('dc:title:');
			// 				$sep = '; ';
			// 			}

			// 			$this->opac1['AUTHORS_NAMES'] = $authors_names;
			// 			$author_edge = $v->getFirstEdge(GDirection::OUT,'ea:inferred-work:authorWork');
			// 			if (!empty($author_edge)){
			// 				$this->opac1['FIRST_AUTHOR_NAME']  = $author_edge->getVertexTO()->getPropertyValue('dc:title:');
			// 			}



			$var_templ = array(
					'title' => $title,
					'issue' => $issue,
					'issue_publication_year' => $issue_publication_year,
					'issue_publication_month' => $issue_publication_month,
					'issue_publication_day' => $issue_publication_day,
					'title_medium' => $m_title_medium,
					'title_remainder' => $m_title_remainder,
					// 		'title_responsibility' => $m_title_responsibility,
					'title_partNumber' => $m_title_partNumber,
					'title_partName' => $m_title_partName,
					'edition' => $m_edition,
					'publication' => $m_publication,
					'distribution' => $m_distribution,
					'production' => $m_production,
					'manufactur' => $m_manufactur,
			);


			$template = Config::get('arc_display_template.manifestation_title_details');
			//$m = new Mustache_Engine;
			//$m =  App::make('template-engine');
			//$title_punct = str_replace("\n",' ', trim($m->render($template,$var_templ)));
			$title_punct = ArcTemplateEngine::renderLine($template,$var_templ);
			$v->setTmpAttribute('Title_punc', $title_punct);
			$v->setTmpAttribute('label',$title_punct);

		}


		//  $v --|ea:work:|--> $(A)   ==>  $v <--|reverse:ea:work|--   $(A)
		private function reverseWork($v){
			//@@@ $this->inferenceVA_THEN_AV('ea:work:','reverse:ea:work:');
		}


	/**
	 * @param GVertex $v
	 */
	protected function processVertex( $v){

		$this->context->addDebugMessage("GRuleManifestation process: " . $v);
		// 		$context = $this->context;
// 				$id = $v->persistenceId();
// 				$g = $v->graph();

		$this->reverseWork($v);
		$this->labelForManifestation($v);


	}

}

