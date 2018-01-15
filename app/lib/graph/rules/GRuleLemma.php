<?php


class GRuleLemma  extends AbstractGruleProcessVertice implements GRule {


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

		if (!$v->isReadOnly()){

			$obj_type = $v->getPropertyValue('ea:obj-type:');

			if ($obj_type == 'lemma'){
				$entities = $v->getVertices(GDirection::OUT,'ea:lemma:manifestation');
				if (!empty($entities)){
					foreach ($entities as $entity) {
						if ($entity->getObjectType() == 'auth-manifestation') {
							$v->addFlag('IS:lemma-book');
							$v->removeFlag('IS:lemma-other');
							break;
						}else{
							$v->addFlag('IS:lemma-other');
							$v->removeFlag('IS:lemma-book');
						}
					}
				}else{
					$v->addFlag('IS:lemma-other');
					$v->removeFlag('IS:lemma-book');
				}
			}


			if ($obj_type == 'auth-person'){
				$biographical_data = $v->getPropertyValue('ea:authBiographical:Data_Text');
				if(!empty($biographical_data)){
					$v->addFlag('IS:lemma-person');
				}else{
					$v->removeFlag('IS:lemma-person');
				}
			}

		}

	}


}
