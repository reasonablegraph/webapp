<?php

class GRuleOpacPrepareActor extends AbstractGruleProcessVertice implements GRule {

	protected function init(){
	}




	private  function labelForPerson($v){
		$title = $v->getPropertyValue('dc:title:');
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
		$template = str_replace($template,' ',"\t");
		//$m = new Mustache_Engine;

		//$m =  App::make('template-engine');
		//$title_punct = str_replace("\n",' ', trim($m->render($template,$var_templ)));

		//$title_punct = str_replace("\n",' ', trim(ArcTemplateEngine::render($template,$var_templ)));
		$title_punct = ArcTemplateEngine::renderLine($template,$var_templ);
		$v->setTmpAttribute('Title_punc',$title_punct);
		$v->setTmpAttribute('label',$title_punct);


	}


	private function labelForFamily($v){
		$title = $v->getPropertyValue('dc:title:');
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
		$v->setTmpAttribute('Title_punc',$title_punct);
		$v->setTmpAttribute('label',$title_punct);
	}

	private  function labelForOrganization($v){
		$title = $v->getPropertyValue('dc:title:');
		$o_subdivision = $v->getProperties('ea:auth:Organization_Subdivision');
		$o_addition = $v->getProperties('ea:auth:Organization_Addition');
		$o_number  = $v->getPropertyValue('ea:auth:Organization_Number');
		$o_date  = $v->getPropertyValue('ea:auth:Organization_Date');
		$o_location  = $v->getPropertyValue('ea:auth:Organization_Location');

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
		);

		$template = Config::get('arc_display_template.organization_title');
		//$m = new Mustache_Engine;
		//$m =  App::make('template-engine');
		//$this->opac2['Title_punc'] =  trim($m->render($template,$var_templ));
		//$title_punct = trim($m->render($template,$var_templ));
		$title_punct = ArcTemplateEngine::renderLine($template,$var_templ);
		$v->setTmpAttribute('Title_punc',$title_punct);
		$v->setTmpAttribute('label',$title_punct);


	}



	/**
	 * @param GVertex $v
	 */
	protected function processVertex( $v){

		$graph = $v->graph();
		//$context->addDebugMessage(" process: " . $v);
		//$title = $v->getPropertyValue('dc:title:');
		$obj_type = $v->getPropertyValue('ea:obj-type:');

		if ($obj_type == 'auth-person'){
			$this->labelForPerson($v);
		} elseif ($obj_type == 'auth-organization'){
			$this->labelForOrganization($v);
		} elseif ($obj_type == 'auth-family'){
			$this->labelForFamily($v);
		}

	}


}



?>