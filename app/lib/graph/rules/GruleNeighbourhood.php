<?php

// select item_id,obj_type,label,jdata->>'nMethod' as method, jdata->'neighbourhood' as neigh from dsd.item2 order by 2,1;
// select item_id,obj_type,label,jdata->'neighbourhood'->'def' as neigh from dsd.item2 order by 2,1;
class GRuleNeighbourhood extends AbstractBaseRule implements GRule {

	//gitonia 1: 	APOSTASI 1 MAZI ME TA INFERED
	//GITONIA 2:  INE oi gitones 1 ton gitonon 1 tou komvoumas ***READ ONLY***


	/**
	 *
	 * @param GRuleContextR $context
	 */
	public function __construct($context){
		parent::__construct($context);
	}


		private $neighbourhood = array();
		private $neighbourhood_vertex_for = null;
		private function clearNeighbourhood($id=null){
			$this->neighbourhood_vertex_for = $id;
			$this->neighbourhood = array();
		}


		/**
		 *
		 * @param GVertex $v
		 * @param String $atrributeNeighbourhoodName
		 * @param String $name
		 */
		private function addVertexsAtributeToNeighbourhood($v, $atrributeNeighbourhoodName ,$name='def' ){
			$n = $v->getAttribute('neighbourhood');
			if (!empty($n)){
				if (isset($n[$atrributeNeighbourhoodName])){
					$nn = $n[$atrributeNeighbourhoodName];
					foreach ($nn as $id){
						$this->addIdToNeighbourhood($id,$name);
					}
				}
			}
		}

		private function initNeighbourhood($name = 'def' ){
			if (!isset($this->neighbourhood[$name])){
				$this->neighbourhood[$name]= array();
			}
		}

		private function addIdToNeighbourhood($id, $name = 'def' ){
			if ($id == $this->neighbourhood_vertex_for){
				return;
			}
			if (!isset($this->neighbourhood[$name])){
				$this->neighbourhood[$name]= array();
			}

			if (isset($this->neighbourhood[$name][$id])){
				return false;
			}

			$this->neighbourhood[$name][$id] = null;
			return true;
		}

		private function addVertexToNeighbourhood($vertex, $name = 'def' ){
			$id = $vertex->persistenceId();
// 			if (empty($id)) {
// 				Log::info("??? GRuleNeighbourhood: NEW NODE");
// 				$id = PDao::nextval('dsd.item2_id_seq');
// 			}
			return $this->addIdToNeighbourhood($id,$name);
		}


// 	private function procHelper1($v, $returnInferredFlag = true , $minTrueDistance=1, $maxTraverseDistance = 1, $objTypes =null,$name='def'){
// 		$self = $this;
// 		$g = $v->graph();

// 		$vertexFilter = function($c, $s, $e, $distance) use($returnInferredFlag,$objTypes,$minTrueDistance){
// 			/* @var $e GEdge */
// 			/* @var $s GVertex */
// 			if ( !$returnInferredFlag && $e->isInferred()){ return false; }
// 			if ($distance <= $minTrueDistance){ return true; }
// 			if (!empty($objTypes)) {
// 				return in_array($s->getObjectType(),$objTypes);
// 			}
// 			return true;
// 		};

// 		$handler = function($c, $s, $e, $distance)use($self,$name){
// 			$self->addVertexToNeighbourhood($s,$name);
// 		};

// 		$g->traverseBF($v, $maxTraverseDistance ,$handler,null,GDirection::BOTH, $vertexFilter );
// 	}


//$this->procHelper($v,true,0,5,array(//'subject-chain'=>1, //gia traversal

	/**
	 *
	 * @param GVertex $v
	 * @param string $returnInferredFlag
	 * @param number $minTrueDistance
	 * @param number $maxTraverseDistance
	 * @param unknown $objTypes
	 * @param string $name
	 */
	private function procHelper($v, $returnInferredFlag = true , $minTrueDistance=1, $maxTraverseDistance = 1, $objTypes =null,$name='def'){

		$debugFlag =false;
		//$debugFlag =($v->persistenceId() == 102);
		//return false;
		$self = $this;
		$g = $v->graph();

		$objTypeKeys = array_keys($objTypes);
		$vertexFilter = function($c, $s, $e, $distance) use($returnInferredFlag,$objTypes,$minTrueDistance,$objTypeKeys,$debugFlag){
			if ($debugFlag && $distance > 1){Log::info('#TEST: ' . $distance .  ' : ' . $e->urnStr()); };
			//return ($distance <= 0);
			/* @var $e GEdge */
			/* @var $s GVertex */
			if ( !$returnInferredFlag && $e->isInferred()){ return false; }
			if ($distance <= $minTrueDistance){ return true; }
			if (!empty($objTypeKeys)) {
				$ot = $s->getObjectType();
				if (in_array($ot,$objTypeKeys)){
					$d2 = $objTypes[$ot];
					return ($distance <= $d2);
				}
				return false;
			}
			return true;
		};

		$handler = function($c, $s, $e, $distance)use($self,$name,$debugFlag){
			if ($debugFlag){Log::info('#ADD: ' . $s->urnStr() . ' : ' . $s->getObjectType()); };
			$self->addVertexToNeighbourhood($s,$name);
		};

		$g->traverseBF($v, $maxTraverseDistance ,$handler,null,GDirection::BOTH, $vertexFilter );
	}





	////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////


	private function procGeneric($v){
		Log::info("PROC GENERIC");
// 		return $this->procHelper($v,true,1,1,array());
		$self = $this;
		$this->nh1($v,null,GDirection::BOTH,null,null,function($v1) use ($self){
			return true;
		});
	}


	//'lemma','lemma-category','periodic-publication','web-site-instance','media'
	private function procLemmaCategory($v){
// 		return $this->procHelper($v,true,1,1,array());
		$self = $this;
		$this->nh1($v,null,GDirection::BOTH,null,null,function($v1) use ($self){
			return true;
		});

	}


	private function procPeriodic($v){
		// 		return $this->procHelper($v,true,1,1,array());
		$self = $this;
		$this->nh1($v,null,GDirection::BOTH,null,null,function($v1) use ($self){
			return true;
		});
	}



	private function procLemma($v){
// 		$this->procHelper($v,true,1,10,array(
// 			'auth-work'=>1,
// 			'auth-manifestation'=>1,
// 			'lemma-category'=>1,
// 			'periodic-publication'=>1,
// 			'web-site-instance'=>1,
// 			'media'=>1,
// 			'digital-item'=>2,
// 			'physical-item'=>2,
// 		));

			$self = $this;
			$this->nh1($v,null,GDirection::BOTH,null,null,function($v1) use ($self){ //TODO check digital-item
				return true;
			});

	}

	private function procManifs($v){
// 		return $this->procHelper($v,true,1,1,array());
		$self = $this;
		$this->nh1($v,null,GDirection::BOTH,null,null,function($v1) use ($self){
			return true;
		});
	}


	//CHAIN  ISOS NA TRE3I KETO procSubjectChainLink
	private function procSubjectChain($v){
//		//Log::info("### procSubjectChain " . $v->urnStr());
//		$this->procHelper($v,true,1,1,array());
//		$this->_procSubjectChainLink($v);


		$self = $this;
		$this->nh1($v,null,GDirection::BOTH,null,null,function($v1) use ($self){
			$self->nh1($v1,'auth-work',GDirection::IN,['ea:work:','inferred:ea:work:'],'auth-manifestation');
			return true;
		});


	}


	//PREPEI NA TRE3EI KE TO CHAIN-LINK
	private function procItem($v){
//		$this->procHelper($v,true,1,10,array(
//				'auth-manifestation'=>1,
//				'auth-expression'=>2,
//				'auth-work'=>3,
//		));

		$self = $this;
		$this->nh1($v,null,GDirection::BOTH,null,null,function($v1) use ($self){
				$self->nh1($v1,'auth-manifestation',GDirection::OUT,['reverse:ea:artifact-of:'],'digital-item');
				return true;
		});


	}

	//PREPEI NA TRE3EI KE TO CHAIN-LINK
	private function procManif($v){
		//return $this->procHelper1($v,true,1,10,array('auth-work','digital-item','auth-expression'));
//		$this->procHelper($v,true,1,10,array(
//				'auth-expression'=>1,
//				'auth-work'=>2,
//				'digital-item'=>1,
//				'physical-item'=>1,
//		));

		$this->_procChainLink($v);
		$self = $this;
		$this->nh1($v,null,GDirection::BOTH,null,null,function($v1) use ($self){
			$self->nh1($v1,'auth-work',GDirection::OUT,['ea:subj:'],null);
			return true;
		});

	}

	//PREPEI NA TRE3EI KE TO CHAIN-LINK
	private function procExpres($v){

		$self = $this;
		$this->nh1($v,null,GDirection::BOTH,null,null,function($v1) use ($self){
			$self->nh1($v1,'auth-work',GDirection::OUT,['ea:subj:'],null);
			return true;
		});

		$this->_procChainLink($v);

//		//return $this->procHelper1($v,true,1,10,array('auth-work','auth-manifestation'));
//		$this->procHelper($v,true,1,10,array(
//				'auth-work'=>1,
//				'auth-manifestation'=>1,
//		//	'digital-item'=>2,
//		//  'physical-item'=>2,
//		));



	}

	//PREPEI NA TRE3EI KE TO CHAIN-LINK
	private function procWork($v){
//		//return $this->procHelper1($v,true,1,10,array('auth-work','auth-manifestation','auth-expression'));
//		//gitonia 1: 	APOSTASI 1 MAZI ME TA INFERED
//		//GITONIA 2:  INE oi gitones 1 ton gitonon 1 tou komvoumas ***READ ONLY***
//		$this->procHelper($v,true,1,10,array(
//				'auth-expression'=>1,
//				'auth-manifestation'=>2,
//				'digital-item'=>3,
//				'physical-item'=>3,
//		));
//		//$this->_procSubjectChainLink($v);
//
		$this->_procChainLink($v);

		$self = $this;
		$this->nh1($v,null,GDirection::BOTH,null,null,function($v1) use ($self){
			$self->nh1($v1,'subject-chain',GDirection::IN,'ea:subj:',null);//XRIAZETE OTAN O KOMVOS INE CHAIN LINK OR SUBJECT
			$self->nh1($v1,'subject-chain',GDirection::OUT,'ea:inferred-chain-link:',null);//XRIAZETE GIA TOUS KOMVOUS POU EINAI KRIKOI ALISIDAS NA ENIMERWNONTAI GIA ALLAGES TOU WORK
			//$v,$obj_type1=null, $direction=null,$element=null,$obj_type2=null, $handler = null
			$self->nh1($v1,'auth-expression',GDirection::OUT,null,['auth-person','auth-organization','auth-family'],null);//sintelestes expression
			$self->nh1($v1,'auth-manifestation',GDirection::OUT,null,['auth-person','auth-organization','auth-family'],null);//sintelestes manif
			//$self->nh1($v1,'auth-manifestation',GDirection::IN,'ea:artifact-of:',null,null);//digital-item iparxi velaki artifact-of opote den xriazete

			return true;
		});





	}

	//otan enas komvos mporei na ine krikos alisidas
	private function _procChainLink($v){
		$self = $this;
		//($v,$obj_type1=null, $direction=null,$element=null,$obj_type2=null, $handler = null){
		$this->nh1($v,null,GDirection::IN,'ea:inferred-chain-link:',null,function($v1) use ($self){
			$self->nh1($v1,'subject-chain',GDirection::OUT,'ea:inferred-chain-link:',null);
		});

	}

	//PREPEI NA TRE3EI KE TO CHAIN-LINK
	/**
	 * @param $v GVertex
	 */
	private function procActor($v){
//		$this->procHelper($v,true,1,10,array(
//				'auth-work'=>1, //apostasi 1 gia contributor
//				'auth-expression'=>2,
//				'auth-manifestation'=>3,
//				'subject-chain'=>2,//MESO TOU WORK ME INFERRED giati o actor simetexei sto onoma tou work
//		));
//		$this->_procSubjectChainLink($v);

//private function  nh1($v,$obj_type1=null, $direction=null,$element=null,$obj_type2=null, $handler = null){

		$this->_procChainLink($v);

		$self = $this;
		$this->nh1($v,null,GDirection::BOTH,null,null,function($v1) use ($self){
			//$self->nh1($v1,['auth-work'],'GDirection::IN');
				//GDirection::IN,null,['auth-work']);
			//$self->nh1($v1,'auth-manifestation',GDirection::OUT,['ea:work:','inferred:ea:work:'],null);

			$self->nh1($v1,'subject-chain',GDirection::IN,'ea:subj:',null);//XRIAZETE OTAN O KOMVOS INE CHAIN LINK OR SUBJECT
			$self->nh1($v1,'auth-expression',GDirection::IN,'ea:expression:','auth-work',null);//GIA PERSON TRANSLATOR XRIAZONTAI UPDATE TA JDATA TOY WORK

			//$self->nh1($v1,'auth-manifestation',GDirection::OUT,['ea:work:','inferred:ea:work:'],null);


			return true;
		});

	}

	/**
	 * @param GVertex $v
	 * @param $obj_type || array()
	 * @param $direction
	 * @param $element
	 * @param $obj_type || array()
	 * @param callable $handler  (vertex)
	 */
	private function  nh1($v,$obj_type1=null, $direction=null,$element=null,$obj_type2=null, $handler = null){
		$direction = (empty($direction)?GDirection::BOTH : $direction);


		$checkOT = function($v,$ot){
			$tot = $v->getObjectType();
			if (is_array($ot)){
				foreach ($ot as $ok){
					if ($ok === $tot){
						return true;
					}
				}
				return false;
			} else{
				return ($tot === $ot);
			}
		};

		if (empty($obj_type1) ||  $checkOT($v,$obj_type1)){
			$n2= $v->getVertices($direction,$element);
			foreach ($n2 as $v2){
				if (empty($obj_type2) || $checkOT($v2,$obj_type2)) {
					$addFlag = true;
					if (!empty($handler)) {
						$addFlag = $handler($v2);
					}
					if ($addFlag){$this->addVertexToNeighbourhood($v2);};
				}
			}
		}
	}

	/**
	 * @param GVertex $v
	 *
	 */
	private function procPlace($v){
		$self = $this;
		$this->nh1($v,null,GDirection::BOTH,null,null,function($v1) use ($self){
			$self->nh1($v1,'subject-chain',GDirection::IN,'ea:subj:',null);//XRIAZETE OTAN O KOMVOS INE CHAIN LINK OR SUBJECT
			$self->nh1($v1,'auth-manifestation',GDirection::OUT,['ea:work:','inferred:ea:work:'],null);
			return true;
		});
	}


	private function procSubjectChainLink($v){
//		$this->procHelper($v,true,1,1,array());
//		$this->_procSubjectChainLink($v);
		$self = $this;
		$this->nh1($v,null,GDirection::BOTH,null,null,function($v1) use ($self){
			$self->nh1($v1,'subject-chain',GDirection::IN,'ea:subj:',null);//XRIAZETE OTAN O KOMVOS INE CHAIN LINK OR SUBJECT
			return true;
		});
		$this->_procChainLink($v);

	}



	private function _procSubjectChainLink($v){
		//return $this->procHelper1($v,true,1,10,array('subject-chain','auth-concept', 'auth-event','auth-general','auth-genre','auth-object','auth-place','auth-work','auth-organization','auth-person','auth-family'));
		$this->procHelper($v,true,0,5,array(
				'subject-chain'=>1, //gia traversal

				//PATERADES
				'auth-work'=>2,//MESO CHAIN ME INFERRED //SUBJECT KRIKOI
				'auth-general'=>2,//MESO CHAIN ME INFERRED
				'auth-manifestation'=>2,//MESO CHAIN ME INFERRED
				'auth-experssion'=>2,//MESO CHAIN ME INFERRED
				'digital-item'=>2,//MESO CHAIN ME INFERRED
				'physical-item'=>2,//MESO CHAIN ME INFERRED
			//PATERADES ACTORS:
				'auth-organization'=>2,//MESO CHAIN ME INFERRED //SUBJECT KRIKOI
				'auth-person'=>2,//MESO CHAIN ME INFERRED //SUBJECT KRIKOI
				'auth-family'=>2,//MESO CHAIN ME INFERRED //SUBJECT KRIKOI

			//SUBJECT KRIKOI
				'auth-object'=>2,
				'auth-concept'=>2,
				'auth-event'=>2,
				'auth-family'=>2,
				'auth-general'=>2,
				'auth-genre'=>2,
				'auth-object_collection'=>2,
				'auth-place'=>2,

				// 				'auth-manifestation'=>1,
				// 				'auth-expression'=>2,
				// 				'auth-work'=>3,
		));
	}



	/**
	 * @param GVertex $v
	 */
	private function procWEMI($v){
		$self = $this;
		$g = $v->graph();

		$vertexFilter = function($c, $s, $e, $distance){
			if ($distance < 1){ return true; }
			/* @var $e GEdge */
			if ($e->isInferred()){ return false; }
			/* @var $s GVertex */
			$ot = $s->getObjectType();
			return in_array($ot,array('auth-work','auth-expression','auth-manifestation', 'digital-item','physical-item','auth-organization','auth-person','auth-family'));
		};

		$handler = function($c, $s, $e, $distance)use($self){
			$self->addVertexToNeighbourhood($s);
		};

		$g->traverseBF($v, 10,$handler,null,GDirection::BOTH, $vertexFilter );
	}



	public function execute(){
		//Log::info("Neighbourhood RULE");
		$this->context->addDebugMessage("GRuleNeighbourhood");
		/* @var $context GRuleContextR */
		$context = $this->context;
		/* @var $g GGRaph */
		$g = $context->graph();

		$self = $this;

		$vertices = $g->getVertices();
		foreach ($vertices as $v){
			//$old_neighbourhood = $v->getAttribute('neighbourhood');
			//Log::info(print_r($old_neighbourhood,true));
			$v->setAttribute('neighbourhood',array());
		}


		/**
		 * @param GVertex $v1
		 */
		$procVertice = function($v1,$method) use ($g,$self){
				//$this->context->addDebugMessage("PROC: " . $v1->urnStr());
				$self->clearNeighbourhood($v1->id());
				$self->$method($v1);
				//Log::info('neighbourhood: ' . $v1->urnStr() . ' : ' . $v1->getObjectType() .  ' : ' . implode(', ',array_keys($self->neighbourhood)));

				$old_neighbourhood = $v1->getAttribute('neighbourhood');
				if (empty($old_neighbourhood)) { $old_neighbourhood = array();};


				$final_neighbourhood = array();
				foreach ($this->neighbourhood as $k=>$nes){
					if (!isset($old_neighbourhood[$k])){
						$final_neighbourhood[$k]=array_keys($nes);
					} else {
						$final_neighbourhood[$k]= array_values(array_unique( array_merge($old_neighbourhood[$k],array_keys($nes))));
					}
				}

				if (isset($final_neighbourhood['def']) && ! isset($final_neighbourhood['item'])){
					//Log::info("@:: SET DEFAULT ITEM neighbourhood " . $v1->urnStr());
					$final_neighbourhood['item'] = $final_neighbourhood['def'];
				}
				$v1->setTmpAttribute('neighbourhood', true);
				$v1->setAttribute('neighbourhood', $final_neighbourhood);
				$v1->setAttribute('nMethod', $method);
		};

		$proc = function($ot,$method) use ($g,$self,$procVertice){
			//Log::info("#1 " . $ot .  ' : ' . $method);
			$vs = $g->getRefernceToVerticesByOT($ot);
			//Log::info("#1 " . $ot .  ' : ' . $method . ' : ' . count($vs));
			$context =$this->context;
			$context->addDebugMessage("ND PROC:: " .$ot . ' : ' . count($vs));
			foreach ( $vs as $v1 ) {
// 				if ($v1->isReadOnly()){
// 					Log::info("############## GRuleNeighbourhood READONLY: " . $v1->urnStr());
// 					continue; }
				$procVertice($v1,$method);
			}
		};

		//'auth-concept', 'auth-event','auth-general','auth-genre','auth-object','auth-place'

		$proc('subject-chain','procSubjectChain');
		$proc('auth-concept','procSubjectChainLink');
		$proc('auth-event','procSubjectChainLink');
		$proc('auth-general','procSubjectChainLink');
		$proc('auth-genre','procSubjectChainLink');
		$proc('auth-object','procSubjectChainLink');
		$proc('auth-place','procPlace');

		$proc('auth-person','procActor');
		$proc('auth-organization','procActor');
		$proc('auth-family','procActor');

		$proc('periodic','procPeriodic');

		$proc('auth-manifestation','procManif');
		$proc('auth-work','procWork');
		$proc('auth-expression','procExpres');
		$proc('digital-item','procItem');
		$proc('physical-item','procItem');

		$proc('lemma','procLemma');
		$proc('lemma-category','procLemmaCategory');
		$proc('periodic-publication','procManifs');
		$proc('web-site-instance','procManifs');
		$proc('media','procManifs');

// 		foreach ($vertices as $v){
// 			if ( !$v->hasTmpAttribute('neighbourhood')){
// 				$procVertice($v,'procGeneric');
// 			}
// 		}

	}


}

