<?php

class GRuleOpacPrepareExpresion extends AbstractGruleProcessVertice implements GRule {
	
	protected function init(){
	}
	
	
	/**
	 * @param GVertex $v
	 */
	protected function processVertex( $v){
		//$context = $this->context;
		
		$graph = $v->graph();
		$this->context->addDebugMessage("GRuleOpacPrepareManifestation process: " . $v->urnStr());
		
		$title = $v->getPropertyValue('dc:title:');
		$translator = null;
		$translatorVertex = $v->getFirstVertex(GDirection::OUT,'ea:expres:translator');
		if ($translatorVertex){
			$translator = $translatorVertex->getPropertyValue('dc:title:');
		}		
		$label = $title ;
		if (!empty($translator)){
			$label = $label . ' / ' . $translator . ' ['. tr('Translator').']' ;
		}
		
		//$title_punct = ArcTemplateEngine::renderLine($template,$var_templ);
		$v->setTmpAttribute('Title_punc', $label);
		
	}
	
	
}



?>