<?php

class GRuleOpacPrepareManifestation extends AbstractGruleProcessVertice implements GRule {
	
	protected function init(){
	}
	
	
	/**
	 * @param GVertex $v
	 */
	protected function processVertex( $v){
		//$context = $this->context;
		
		$graph = $v->graph();
		//$context->addDebugMessage("GRuleOpacPrepareManifestation process: " . $v);
		
		$title = $v->getPropertyValue('dc:title:');
		
		$m_title_medium = $v->getPropertyValue('ea:manif:Title_Medium');
		$m_title_remainder = $v->getPropertyValue('ea:manif:Title_Remainder');
// 		$m_title_responsibility = $v->getPropertyValue('ea:manif:Title_Responsibility');
		$m_title_partNumber = $v->getPropertyValue('ea:manif:Title_PartNumber');
		$m_title_partName = $v->getPropertyValue('ea:manif:Title_PartName');
		$m_publication = $v->getPropertyValue('ea:manif:Publication');
		$m_distribution = $v->getPropertyValue('ea:manif:Distribution');
		$m_production = $v->getPropertyValue('ea:manif:Production');
		$m_manufactur = $v->getPropertyValue('ea:manif:Manufactur');
		$m_edition = $v->getProperty('ea:manif:Edition_Statement');
			
			
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
		
	}
	
	
}



?>