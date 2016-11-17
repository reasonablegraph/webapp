<?php


class GRuleActor  extends AbstractGruleProcessVertice implements GRule {


// 	protected function processVertex( $v){
// 		$context = $this->context;

// 		$id = $v->persistenceId();
// 		$g = $v->graph();


// 	}

	/**
	 * @param GVertex $v
	 */
	protected function processVertex( $v){

		$context = $this->context;
		//$id = $v->persistenceId();

		$graph = $v->graph();
		//$context->addDebugMessage(" process: " . $v);


		$title = $v->getPropertyValue('dc:title:');
		$obj_type = $v->getPropertyValue('ea:obj-type:');

		$title_punct = null;

		if ($obj_type == 'auth-person'){
			// 			$this->opac1['test-key-1'] = 'test-value-1';

			$v->addFlag('HAS:auth-person');
			$v->addFlag('HAS:actor');
			$v->addFlag('IS:actor');

			$p_numeration = $v->getPropertyValue('ea:auth:Person_Numeration');
			$p_fuller_name = $v->getPropertyValue('ea:auth:Person_FullerName');
			$p_titles_associated  = $v->getProperties('ea:auth:Person_TitlesAssociated');
			$p_dates_associated  = $v->getPropertyValue('ea:auth:Person_DatesAssociated');

			// 			Log::info(print_r($p_titles_associated,true));
			// 			echo '<pre>'; print_r($p_dates_associated); echo '</pre>';

			$value_array = array();
			$list_array = array();

			if(!empty($p_titles_associated)){
				$i=0;
				foreach ($p_titles_associated as $vi) {
					$val1 = $vi-> value();
					$i++;
					if($i>1){
						$value_array['delimiter'] ='true';
					}

					if(!empty($val1)){
						$value_array['value'] = $val1;
						$list_array['list'][] = $value_array;
					}
				}
			}

			$var_templ = array(
					'title' => $title,
					'person_dates_associated' => $p_dates_associated,
					'person_numeration' => $p_numeration,
					'person_fuller_name' => $p_fuller_name,
					'titles_associated' => $list_array,
			);

			$template = Config::get('arc_display_template.person_title');
			//$title_punct = ArcTemplateEngine::renderLineHandlebars($template,$var_templ);
			$title_punct = ArcTemplateEngine::renderLine($template,$var_templ);

		}elseif ($obj_type == 'auth-organization'){


			$v->addFlag('HAS:auth-organization');
			$v->addFlag('HAS:actor');
			$v->addFlag('IS:actor');

			$o_subdivision = $v->getProperties('ea:auth:Organization_Subdivision');
			$o_addition = $v->getProperties('ea:auth:Organization_Addition');
			$o_number  = $v->getPropertyValue('ea:auth:Organization_Number');
			$o_date  = $v->getPropertyValue('ea:auth:Organization_Date');
			$o_location  = $v->getPropertyValue('ea:auth:Organization_Location');
			$o_type = $v->getProperty('ea:auth:Organization_Attributes_Type');

			if (!empty($o_type)){
				$tmp =$o_type->value();
				$o_type = ($tmp != 'undefined') ?  $o_type->prps('selected_value') : null;
			}

			$value_array = array();
			$list_array = array();
			$value_array2 = array();
			$list_array2 = array();

			if(!empty($o_subdivision)){
				$i=0;
				foreach ($o_subdivision as $vi) {
					$val1 = $vi-> value();
					$i++;
					if($i>1){
						$value_array['delimiter'] ='true';
					}

					if(!empty($val1)){
						$value_array['value'] = $val1;
						$list_array['list'][] = $value_array;
					}
				}
			}

			if(!empty($o_addition)){
				$i=0;
				foreach ($o_addition  as $vi) {
					$val1 = $vi-> value();
					$i++;
					if($i>1){
						$value_array2['delimiter'] ='true';
					}

					if(!empty($val1)){
						$value_array2['value'] = $val1;
						$list_array2['list'][] = $value_array2;
					}
				}
			}
			// 			echo '<pre>'; print_r($list_array); echo '</pre>';
			$var_templ = array(
					'title' => $title,
					'subdivision' => $list_array,
					'addition' =>  $list_array2,
					'number' => $o_number,
					'date' => $o_date,
					'location' => $o_location,
					'type' => $o_type,
			);

			$template = Config::get('arc_display_template.organization_title');
			//$m = new Mustache_Engine;
			//$m =  App::make('template-engine');
			//$this->opac2['Title_punc'] =  trim($m->render($template,$var_templ));
			//$title_punct = trim($m->render($template,$var_templ));
			$title_punct = ArcTemplateEngine::renderLine($template,$var_templ);

		}elseif($obj_type == 'auth-family'){

			$v->addFlag('HAS:auth-family');
			$v->addFlag('HAS:actor');
			$v->addFlag('IS:actor');

			$f_type = $v->getPropertyValue('ea:auth:Family_Type');
			$f_titles_place = $v->getProperties('ea:auth:Family_Titles_Place');
			$f_datesAssociated  = $v->getPropertyValue('ea:auth:Person_DatesAssociated');

			$value_array = array();
			$list_array = array();

			if(!empty($f_titles_place)){
				$i=0;
				foreach ($f_titles_place  as $vi) {
					$val1 = $vi-> value();
					$i++;
					if($i>1){
						$value_array['delimiter'] ='true';
					}

					if(!empty($val1)){
						$value_array['value'] = $val1;
						$list_array['list'][] = $value_array;
					}
				}
			}

			$var_templ = array(
					'title' => $title,
					'type' => $f_type,
					'titles_place' =>  $list_array,
					'dates_associated' => $f_datesAssociated,
			);

			$template = Config::get('arc_display_template.family_title');
			//$m =  App::make('template-engine');
			//$this->opac2['Title_punc'] =  trim($m->render($template,$var_templ));
			//$title_punct = trim($m->render($template,$var_templ));
			$title_punct = ArcTemplateEngine::renderLine($template,$var_templ);
		}

		if (empty($title_punct) || trim($title_punct) == ''){
			$title_punct = GRuleUtil::getLabel($v);
		}

		$v->setTmpAttribute('Title_punc',$title_punct);
		$v->setTmpAttribute('label',$title_punct);


	}


}

?>