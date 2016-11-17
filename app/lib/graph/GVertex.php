<?php


//DOT1:  "GVertex" [ label = "Gvertex\n (interface)"];
//DOT2:  "GVertex" -> "GNode"  [ label = "extends"];
interface  GVertex extends GNode {

	const OBJECT_TYPE = 'vertex';

	/**
	 * @param GDirection $direction
	 * @return GEdge[]
	 */
	public function getEdges($direction,$elements=null);


	public function hasEdge($direction,$element, $vertexURNStr);

	/**
	 *
	 * @param GDirection:: $direction
	 * @param String $element
	 * @param GURN $vertexURN
	 * @return GEdge || false;
	 */
	public function getFirstEdge($direction,$element,$vertexURNStr = null);
	/**
	 *
	 * @param GDirection:: $direction
	 * @param String $element
	 * @param GURN $vertexURN
	 * @return GVertex||null
	 */
	public function getFirstVertex($direction,$element,$vertexURNStr = null);

	/**
	 * @param GDirection $direction
	 * @param String[] $elements
	 * @return GVertex[]
	 */
	public function getVertices($direction,$elements=null);


	public function isOrphan();
	public function isRoot();
	public function isLeaf();


	/**
	 * @return string
	 */
	public function getObjectType();


	   public function reloadProperties();
	   public function loadProperties();


}

//DOT1:  "GVertexO" [ label = "GVertexO\n (class)"];
//DOT2:  "GVertexO" -> "GNodeO"  [ label = "extends"];
//DOT2:  "GVertexO" -> "GVertex"  [ label = "implements"];
class GVertexO extends GNodeO implements GVertex {

	//public $_graph;
// 	public $_label;
// 	public $_objType;
// 	public $_objClass;

// 	private $_verticesIN = array();
// 	private $_verticesOUT = array();

	/**
	 * EDGES IN MAP
	 * MAP KEY: edge->id();
	 *
	 * @var GEdgeO[]
	 *
	 */
	private $_edgesIN = array();
	/**
	 * EDGES OUT MAP
	 * MAP KEY: edge->id();
	 * @var GEdgeO[]
	 */
	private $_edgesOUT = array();

	/**
	 *
	 * @param unknown $graph
	 * @param GURN $id
	 */
	public function __construct($graph,$urn) {
		$persistenceId = null;
		if ($urn->temporalType() == GURN::TEMPORAL_TYPE_OLD){
			$persistenceId = $urn->nss();
		}
		parent::__construct($graph,$urn,$persistenceId );
	}
// 	public function removeEdge($element, $vertexID ){
// 	}
// 	public function addEdge($element, $vertex ){
// 	}

// 	public function getVertices($direction){
// 		if ($direction == GDirection::IN){
// 			return $this->_verticesIN;
// 		}elseif ($direction == GDirection::OUT){
// 			return $this->_verticesOUT;
// 		} else if ($direction == GDirection::BOTH){
// 			return array_merge($this->_verticesIN, $this->_verticesOUT);
// 		} else {
// 			throw new Exception("UNKNOWN DIRECTION");
// 		}
// 	}


	public function getVertices($direction,$elements=null){
		$rep = array();
		if ($direction == GDirection::OUT || $direction == GDirection::BOTH){
			$edges = $this->getEdges(GDirection::OUT,$elements);
			foreach ($edges as $e){
				$rep[] = $e->getVertexTO();
			}
		}
		if ($direction == GDirection::IN || $direction == GDirection::BOTH){
			$edges = $this->getEdges(GDirection::IN,$elements);
			foreach ($edges as $e){
				$rep[] = $e->getVertexFrom();
			}
		}
		return $rep;
	}


	/**
	 * (non-PHPdoc)
	 * @see GVertex::getObjectType()
	 */
	public function getObjectType(){
		return $this->getPropertyValue('ea:obj-type:');
	}


	private function getEdgesIN($filter_e  = null){
		if ($filter_e ==  null){
			return $this->_edgesIN;
		}
		return array_filter($this->_edgesIN,$filter_e);
	}

	private function getEdgesOUT($filter_e  = null){
		if ($filter_e ==  null){
			return $this->_edgesOUT;
		}
		return array_filter($this->_edgesOUT,$filter_e);
	}


	/**
	 * (non-PHPdoc)
	 * @see GVertex::getEdges()
	 */
	public function getEdges($direction,$elements=null){

		$filter_e = null;
		if ($elements != null){
			if (is_array($elements)){
				$filter_e = function($e) use($elements){
					return (in_array($e->element(),$elements));
				};
			} else {
				$filter_e = function($e) use($elements){
					return ($e->element() == $elements);
				};
			}
		}



		if ($direction == GDirection::IN){
			return $this->getEdgesIN($filter_e);
		}

		if ($direction == GDirection::OUT){
			return $this->getEdgesOUT($filter_e);
		}

		if ($direction == GDirection::BOTH){
			$rep = array();
			foreach ($this->_edgesIN as $e){
				if (empty($filter_e) || $filter_e($e)){
					$e->setTmpAttribute('DIRECTION', GDirection::IN);
					$rep[] = $e;
				}
			}
			foreach ($this->_edgesOUT as $e){
				if (empty($filter_e) || $filter_e($e)){
					$e->setTmpAttribute('DIRECTION', GDirection::OUT);
					$rep[] = $e;
				}
			}
			return $rep;
		}

		return null;


// 		if ($direction == GDirection::IN){
// 			return $this->getEdgesIN($filter_e);
// 		}

// 		if ($direction == GDirection::OUT){
// 			return $this->getEdgesOUT($filter_e);
// 		}

// 		return array_merge($this->getEdgesIN($filter_e),$this->getEdgesOUT($filter_e));



	}

// 	public function getEdges_OLD($direction,$elements=null, $vertexURNStr = null){
// 		if ( !empty($vertexURNStr) && is_object($vertexURNStr)){
// 			$vertexURNStr = $vertexURNStr->toString();
// 		}

// 		$filter_e = null;
// 		if ($elements != null){
// 			if (is_array($elements)){
// 				$filter_e = function($e) use($elements){
// 					return (in_array($e->element(),$elements));
// 				};
// 			} else {
// 				$filter_e = function($e) use($elements){
// 					return ($e->element() == $elements);
// 				};
// 			}
// 		}

// 		$createFilterUrnFROM = function($vertexURNStr){
// 			return function($e) use ($vertexURNStr){ return ($e->getVertexFROM().urnStr() == $vertexURNStr);};
// 		};

// 		$createFilterUrnTO = function($vertexURNStr){
// 			return function($e) use ($vertexURNStr){ return ($e->getVertexTO().urnStr() == $vertexURNStr);};
// 		};

// 		$edgesIN = null;
// 		$edgesOUT = null;
// 		if ($direction == GDirection::IN || $direction == GDirection::BOTH){
// 			$edges = $this->_edgesIN;
// 			if ($filter_e !=  null){ $edges = array_filter($edges,$filter_e); }
// 			if ($vertexURNStr != null){
// 				$edges =  array_filter($edges,$createFilterUrnTO($vertexURNStr));
// 			}
// 			if ($direction == GDirection::BOTH){$edgesIN = $edges;};
// 		}
// 		if ($direction == GDirection::OUT || $direction == GDirection::BOTH){
// 			$edges = $this->_edgesOUT;
// 			if ($filter_e !=  null){ $edges = array_filter($edges,$filter_e); }
// 			if ($vertexURNStr != null){
// 				$edges =  array_filter($edges,$createFilterUrnFROM($vertexURNStr));
// 			}
// 			if ($direction == GDirection::BOTH){$edgesOUT = $edges;};
// 		}
// 		if ( $direction == GDirection::BOTH){
// 			$edges = array_merge($edgesIN,$edgesOUT);
// 		}
// 		return $edges;

// 	}


	/**
	 *
	 * @param GEdge $edge
	 */
	public function _addEdge($edge){
		$v1 = $edge->getVertexFrom();
		$v2 = $edge->getVertexTO();
		if ($v1->id() == $this->id()){
			//$this->_verticesOUT[$v2->id()] = $v2;
			$this->_edgesOUT[$edge->id()] = $edge;
		}elseif ($v2->id() == $this->id()){
			//$this->_verticesIN[$v1->id()] = $v1;
			$this->_edgesIN[$edge->id()] = $edge;
		} else {
			throw new Exception ( 'CANOT ADD EDGE TO VERTEX' );
		}
	}

	public function _removeEdge($edge){
		$this->_removeEdgeWitId($edge->id());
	}

	public function _removeEdgeWitId($id){
		unset($this->_edgesOUT[$id]);
		unset($this->_edgesIN[$id]);
	}


	public function _clearEdges(){
		$this->_edgesIN = array();
		$this->_edgesOUT = array();
	}


// 	public function _removeEdges($elements = array()){
// 		if (!is_array($elements)){
// 			$elements = array($elements);
// 		}
// 		foreach ($this->_edgesIN as $e){
// 			if (in_array($e->element(), $elements)) {
// 				unset($this->_edgesIN[$e->id()]);
// 			}
// 		}
// 		foreach ($this->_edgesOUT as $e){
// 			if (in_array($e->element(), $elements)) {
// 				unset($this->_edgesOUT[$e->id()]);
// 			}
// 		}
// 	}



	public function isOrphan(){
		return empty($this->_edgesIN) && empty($this->_edgesOUT);
	}
	public function isRoot(){
		return empty($this->_edgesIN);
	}
	public function isLeaf(){
		return empty($this->_edgesOUT);
	}


	/**
	 * (non-PHPdoc)
	 * @see GVertex::hasEdge()
	 */
	public function hasEdge($direction,$element, $vertexURNStr){
		$e = $this->getFirstEdge($direction, $element,$vertexURNStr);
		return (! empty($e));
	}

	/**
	 * (non-PHPdoc)
	 * @see GVertex::getFirstEdge()
	 */
	public function getFirstEdge($direction,$element,$vertexURNStr = null){

		if (!empty($vertexURNStr) && is_object($vertexURNStr)){
			$vertexURNStr = $vertexURNStr->toString();
		}

		if (empty($element)){
			if ($direction == GDirection::OUT || $direction == GDirection::BOTH){
				foreach ($this->_edgesOUT as $e){
					if ($vertexURNStr == null || $vertexURNStr == $e->getVertexTO()->urnStr()) {
						return $e;
					}
				}
			}
			if ($direction == GDirection::IN || $direction == GDirection::BOTH){
				foreach ($this->_edgesIN as $e){
						if ($vertexURNStr == null || $vertexURNStr == $this->urnStr) {
							return $e;
						}
				}
			}
		} else {
			if ($direction == GDirection::OUT || $direction == GDirection::BOTH){
				foreach ($this->_edgesOUT as $e){
					if ($e->element() == $element){
						if ($vertexURNStr == null || $vertexURNStr == $e->getVertexTO()->urnStr()) {
							return $e;
						}
					}
				}
			}
			if ($direction == GDirection::IN || $direction == GDirection::BOTH){
				foreach ($this->_edgesIN as $e){
					if ($e->element() == $element){
						if ($vertexURNStr == null || $vertexURNStr == $this->urnStr()) {
							return $e;
						}
					}
				}
			}
		}
		return false;
	}




	public function getFirstVertex($direction,$element,$vertexURNStr = null){

		if ($direction == GDirection::BOTH){
			throw new Exception("DIRECTION BOTH NOT SUPPORTED");
		}
		$e = $this->getFirstEdge($direction, $element,$vertexURNStr);

		if ($e){
			if ($direction == GDirection::IN){
				return $e->getVertexFrom();
			}
			if ($direction == GDirection::OUT){
				return $e->getVertexTO();
			}
		}

		return null;
	}




	public function __toString(){
		$id = $this->urn()->toString();
		$pc = count($this->getAllProperties());
		$s = sprintf('v:[ %s',$id);

		if ($pc > 0){
		 $s .=	sprintf(' pc: %s',$pc);
		}
		$title = $this->getPropertyValue('dc:title:');
		if (! empty($title)){
			$s .=	sprintf(' (%s)',$title);
		}
		$s .=	"]";
		return $s;
	}


	/**
	 * overrides GNode._addProperty($p)
	 * @param GProperty $p
	 */
	public function _addProperty($p){

		if ($p->hasParent()) {
			$parentTreeId = $p->parent();
			foreach($this->_edgesOUT as $e){
				if ($e->hasTreeProperty($parentTreeId)){
// 					if ($e->treeId() == $parentTreeId){
// 						$p->clearParent();
// 					}
					$p->_decreaseLevel();
					$e->_addProperty($p);
					return;
				}
			}
		}
		parent::_addProperty($p);
	}


	public function reloadProperties(){
		$id = $this->persistenceId();
		if (!empty($id)){
			//echo("RUNTIME: LOAD VERTEX: $id\n");
			Log::info("RUNTIME: LOAD VERTEX: $id");
			$g = $this->graph();
			GGraphIO::addItemToGraph($g, $id);
		}
	}


	public function loadProperties(){
		if (!empty($this->_props) ){
			return;
		}
		$this->reloadProperties();
	}








}

