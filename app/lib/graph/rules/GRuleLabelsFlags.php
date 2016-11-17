<?php


class GRuleLabelsFlags  extends AbstractGruleProcessVertice implements GRule {





	/**
	 * @param GVertex $v
	 */
	protected function processVertex( $v){
		$context = $this->context;


		//$context->addDebugMessage(" process: " . $v);


		//$id = $v->persistenceId();
		//$graph = $v->graph();

		$title = $v->getPropertyValue('dc:title:');
		$obj_type = $v->getPropertyValue('ea:obj-type:');

		if ($obj_type == 'auth-general'){

			$v->addFlag('HAS:auth-general');
			$v->addFlag('IS:auth-general');
			$v->setTmpAttribute('label',$title);

		} elseif($obj_type == 'auth-place'){

			$v->addFlag('HAS:auth-place');
			$v->addFlag('IS:auth-place');
			$v->setTmpAttribute('label',$title);
			
		} elseif($obj_type == 'auth-concept'){
			
			$v->addFlag('HAS:auth-concept');
			$v->addFlag('IS:auth-concept');
			$v->setTmpAttribute('label',$title);
		
		} elseif($obj_type == 'auth-event'){
			
			$v->addFlag('HAS:auth-event');
			$v->addFlag('IS:auth-event');
			$v->setTmpAttribute('label',$title);
				
		} elseif($obj_type == 'auth-object'){
			
			$v->addFlag('HAS:auth-object');
			$v->addFlag('IS:auth-object');
			$v->setTmpAttribute('label',$title);
					
		} elseif($obj_type == 'auth-genre'){
			
			$v->addFlag('HAS:auth-genre');
			$v->addFlag('IS:auth-genre');
			$v->setTmpAttribute('label',$title);

		} else {

			$v->setTmpAttribute('label',$title);
		}


	}




}

?>