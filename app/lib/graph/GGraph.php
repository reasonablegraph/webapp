<?php
//use Rhumsaa\Uuid\Uuid;

interface ReadOnlyStrategy {
	/**
	 * @param GVertex $v
	 * @return boolean
	 */
	public function canSetReadonly($v);

}


interface GGraph extends ReadOnlyStrategy {

	/**
	 *
	 * @param String vkey
	 * @return GEdge
	 */
	public function getEdge($vkey);

	/**
	 *
	 * @param String $urnStr || GURN urn
	 * @return GVertex
	 */
	public function getVertex($urnStr);

	/**
	 * @param long $id
	 * @return GVertex
	 */
	public function getVertexByPersisteceId($id);

	/**
	 * @return GVertex[]
	 */
	public function getVertices();

/**
 * @return GVertex[]
 */
	public function &getRefernceToVertices();

	/**
	 *
	 * @param unknown $ot
	 */
	public function &getRefernceToVerticesByOT($ot);

	/**
	 * @param callable $filter
	 * @return GVertex[]
	 */
	public function filterVertices($filter);

	/**
	 * @return GEdge[]
	 */
	public function getEdges();

	/**
	 * @return GEdge[]
	 */
	public function getInferredEdges();

	/**
	 * @return GEdge[]
	 */
	public function getFactEdges();


	public function countVertices();
	public function countEdges();

	/**
	 *
	 * @param string $urnStr
	 */
	public function removeVertex($urnStr,$syncPersistence);

	/**
	 *
	 * @param string||Gedge $vkey
	 */
	public function removeEdge($vkey, $syncPersistence = false);
	/**
	 *
	 * @param string[]|| GEdge[] $vkeys
	 */
	public function removeEdges($vkeys);
	public function removeInferredEdges();

	/**
	 *
	 * @return GVertex[]
	 */
	public function getRoots($includeOrphans = true);

	/**
	 *
	 * @return GVertex[]
	 */
	public function getLeafs($includeOrphans = true);
	public function nextSequenceValue();

	/**
	 * Breadth-first traverse (ana epipedo)
	 *
	 * @param GVertex $root
	 * @param
	 *        	string[] elements
	 * @param int $maxDistance
	 * @param callable $handler
	 */
	public function traverseBF($root, $maxDistance, $handler, $elements = null, $direction = GDirection::OUT, $vertexFilter = null,$traverseInferred = true);

	/**
	 * Depth-first traverse (ana klado)
	 *
	 * @param
	 *        	string[] elements
	 * @param GVertex $root
	 * @param callable $handler
	 */
	public function traverseDF($root, $handler, $elements = null,$direction = GDirection::OUT );

	/**
	 *
	 * @param String $v1UrnStr
	 * @param STring $v2UrnStr
	 * @param string $element
	 * @param GURN[] $deps
	 * @return GEdge
	 */
	public function addEdge($v1UrnStr, $v2UrnStr, $element, $derivative = true ,$deps = null,$label=null);


	//public function uuid();

	/**
	 *
	 * @param GPropertyItem $p
	 */
	public function updateProperty($p);


	public function renameEdge($vkey,$newElement);

}




class ReadOnlyStrategyDefault implements ReadOnlyStrategy {
	public function canSetReadonly($v){
		return ($v->getObjectType() != 'subject-chain');
	}
}

class ReadOnlyStrategyNoSubjectChain implements ReadOnlyStrategy {
	public function canSetReadonly($v){
		return ($v->getObjectType() != 'subject-chain');
	}
}


class GGraphO implements GGraph {

	private $propertyIndex = array(); //MAP

	//private $_uuid;
	/**
	 *vertex Map
	 *map key: urn , $v->urnStr();
	 * @var GVertex[]
	 *
	 */
	private $memV = array();

	/**
	 * Edges Map
	 * map key: vkey , $edge->vkey();
	 * @var GEdge[]
	 *
	 */
	private $memE = array();

	/**
	 *
	 * @param GVertex[] $vertices
	 * @param GEdge[] $edges
	 */
	public function GGraphO() {
		$readOnlyStrategyConfigClass = Config::get('arc_rules.READONLY_STRATEGY');
		if (empty($readOnlyStrategyConfigClass)){
			$this->readOnlyStrategy = new ReadOnlyStrategyDefault();
		} else {
			$this->readOnlyStrategy = new $readOnlyStrategyConfigClass();
		}
	//	$this->_uuid = Uuid::uuid1();
		//$this->readOnlyStrategy = new ReadOnlyStrategyDefault();
	}


	/**
	 * @var ReadOnlyStrategy
	 */
	private $readOnlyStrategy;

	/**
	 * @param ReadOnlyStrategy $readOnlyStrategy
	 */
	public function setReadOnlyStrategy($readOnlyStrategy){
		$this->readOnlyStrategy = $readOnlyStrategy;
	}

	/**
	 * @param GVertex $v
	 * @return bool
	 */
	function canSetReadonly($v){
		return $this->readOnlyStrategy->canSetReadonly($v);
	}


// 	/**
// 	 *
// 	 * @var GEdge[] key: vkey
// 	 */
// 	private $memPE = array();

	/**
	 *
	 * @param GURN $urn
	 * @return GVertexO
	 */
	private function _createVertex($urn) {
		$v = new GVertexO($this, $urn);
		$this->memV[$urn->toString()] = $v;
		return $v;
	}
	/**
	 *
	 * @param GURN $urn
	 * @return GVertexO
	 */
	public function createVertex($urn) {
		return $this->_createVertex($urn);
	}

	/**
	 *
	 * @param GURN $urn
	 * @return GVertexO
	 */
	private function getVertexOrCreate($urn) {
		$id = $urn->toString();
		if (isset($this->memV[$id])) {
			$v = $this->memV[$id];
			$v->setTmpAttribute('_gvoc', 0);
			return $v;
		}
		return $this->_createVertex($urn);
	}

	/**
	 * (non-PHPdoc)
	 * @param String vkey
	 * @see GGraph::getEdge()
	 */
	public function getEdge($vkey) {
		return isset($this->memE[$vkey]) ? $this->memE[$vkey] : null;
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see GGraph::getVertex()
	 */
	public function getVertex($urnStr) {
		if (is_object($urnStr)){  $urnStr = $urnStr->toString(); }
		return isset($this->memV[$urnStr]) ? $this->memV[$urnStr] : null;
	}

	public function getVertexByPersisteceId($id){
		if (empty($id)){ return null; }
		return $this->getVertex(GURN::createOLDWithId($id));
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see GGraph::getVertices()
	 */
	public function getVertices() {
		return $this->memV;
	}

/**
 *
 * {@inheritDoc}
 * @see GGraph::getRefernceToVertices()
 */
	public function &getRefernceToVertices(){
		return $this->memV;
	}

	/**
	 * (non-PHPdoc)
	 * @see GGraph::filterVertices()
	 */
	public function filterVertices($filter = null) {
		return array_filter($this->memV, $filter);
	}


	/**
	 * (non-PHPdoc)
	 *
	 * @see GGraph::getEdges()
	 */
	public function getEdges() {
		return $this->memE;
	}

	/**
	 * (non-PHPdoc)
	 * @see GGraph::getInferredEdges()
	 */
	public function getInferredEdges(){
		return array_filter($this->memE, function($e){
			/* @var $e GEdge */
			return $e->isInferred();
		});
	}
	/**
	 * (non-PHPdoc)
	 * @see GGraph::getFactEdges()
	 */
	public function getFactEdges(){
		return array_filter($this->memE, function($e){
			/* @var $e GEdge */
			return ! $e->isInferred();
		});
	}



	public function removeVertex($urnStr, $syncPersistence = false){
		//Putil.logRed("removeVertex: " .$urnStr . ' SYNC: ' . ($syncPersistence?'TRUE':'FALSE'));
		$v = $this->getVertex($urnStr);
		if ($v->isReadOnly()){
			Log::info("READONLY SKIP REMOVE VERTEX (2): " .$urnStr);
			return;
		}
		if (!empty($v)){
			$edges = $v->getEdges(null);
			foreach ($edges as $e){
				$this->removeEdge($e->vkey());
			}
			unset($this->memV[$urnStr]);

			if ($syncPersistence){
				$id = $v->persistenceId();
				if (!empty($id)){
					Log::info("vertices_updates_tracker ADD: " .$id . ' : ' . $urnStr);
					$this->vertices_updates_tracker[$id] = array('vertex'=>$v,'method'=>'delete');
				}
			}
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see GGraph::removeEdge()
	 */
	public function removeEdge($vkey, $syncPersistence = false){
		if (is_object($vkey)){
			$vkey = $vkey->vkey();
		}
		if (isset($this->memE[$vkey])){
			$this->_removeEdge($this->memE[$vkey],$syncPersistence);
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see GGraph::removeEdges()
	 */
	public function removeEdges($vkeys){
		foreach ($vkeys as $vkey){
			$this->removeEdge($vkey);
		}
	}


	/**
	 *
	 * @param GEdgeO $e
	 */
	private function _removeEdge($e, $syncPersistence = false){
		$e->getVertexFrom()->_removeEdge($e);
		$e->getVertexTo()->_removeEdge($e);
		unset($this->memE[$e->vkey()]);

		if ($syncPersistence){

			/* @var $pprod GProperty */
			$pprod = $e->persistenceProp();
			if (!empty($pprod)){
				$id = $pprod->id();
				if (empty($id)){ throw new Exception("ERROR PROPERTY WITHOUT ID");}
				$this->updates_tracker[$id] = array('prop'=>$pprod,'method'=>'delete');
			}
		}
	}

	public function  removeInferredEdges(){
		$des = $this->getInferredEdges();
		foreach ($des as $e){
			$vf = $e->getVertexFrom();
			if (!$vf->isReadOnly()) {
				$this->_removeEdge($e);
			}
		}
	}

	// /**
	// *
	// * @param unknown $vertexId
	// * @param unknown $vertexId
	// * @param String $element
	// */
	// public function _addEdge($vertexId, $vertexId, $element){

	// }

	/**
	 * (non-PHPdoc)
	 *
	 * @see GGraph::getRoots()
	 */
	public function getRoots($includeOrphans = true) {
		if ($includeOrphans) {
			return array_filter($this->memV, function ($v) {
				return $v->isRoot();
			});
		}
		return array_filter($this->memV, function ($v) {
			return $v->isRoot() && ! $v->isLeaf();
		});
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see GGraph::getLeafs()
	 */
	public function getLeafs($includeOrphans = true) {
		if ($includeOrphans) {
			return array_filter($this->memV, function ($v) {
				return $v->isLeaf();
			});
		}
		return array_filter($this->memV, function ($v) {
			return $v->isLeaf() && ! $v->isRoot();
		});
	}
	private $seq = 0;
	public function nextSequenceValue() {
		return ++ $this->seq;
	}
	private $tseq = 0;
	private function nextTSequenceValue() {
		return ++ $this->tseq;
	}
	private $eseq = 0;
	/**
	 * sequence gia edges
	 *
	 * @return number
	 */
	private function nextESequenceValue() {
		return ++ $this->eseq;
		// return PDao::nextval('dsd.metadatavalue2_id_seq');
	}

	/**
	 * (non-PHPdoc)
	 *
	 *
	 * #let s be the source node
	 * frontier = new Queue()
	 * mark root visited (set root.distance = 0)
	 * frontier.push(root)
	 * while frontier not empty {
	 * Vertex v = frontier.pop()
	 * for each successor v' of v {
	 * if v' unvisited {
	 * frontier.push(v')
	 * mark v' visited (v'.distance = v.distance + 1)
	 * }
	 * }
	 * }
	 *
	 * @see GGraph::traverseBF()
	 */
	public function traverseBF($root, $maxDistance, $handler, $elements = null, $direction=GDirection::OUT, $vertexFilter = null, $traverseInferred = true) {
		//Log::info("#0# traverseBF: " . $root->id());
// 		if ($direction == GDirection::BOTH){
// 			throw new Exception('traverseBF ERROR DIRECITON: GDirection::BOTH');
// 		}
		$g = $this;
		$cKey = function ($key) use($g) {
			$nsv = $g->nextTSequenceValue();
			return ('__TB' . $nsv . '-' . $key);
		};
		$distanceKey = $cKey('DISTANCE');
		//Log::info("DISTANCE-KEY: " . $distanceKey);

		if (empty($vertexFilter)){
			$vertexFilter = function($c, $s, $e, $distance){
				return true;
			};
		}

		$c = 0;
		$frontier = array();
		$root->setTmpAttribute($distanceKey, 0);
		$frontier[] = $root; // PUSH
		$frontierNotEmpty = true;
		while ( $frontierNotEmpty ) {
			/* @var $v GVertex  */
			$v = array_pop($frontier); // POP //$v = array_shift($frontier); // SHIFT
			if (empty($v)) {
				$frontierNotEmpty = false;
				break;
			}
			$distance = $v->getTmpAttribute($distanceKey) + 1;
			if (! empty($maxDistance) && $distance > $maxDistance) {
				 //Log::info("MAX REACHED: " . $distance);
				continue;
			}

			$successorEdges = $v->getEdges($direction, $elements);
			//Log::info( "#1# V: " . $v->id() .  ' edge-count: ' . count($successorEdges));
			foreach ( $successorEdges as $e ) {
			  if (!$traverseInferred && $e->isInferred()){
			    continue;
        }
				$vdirection = ($direction == GDirection::BOTH) ?$e->getTmpAttribute('DIRECTION')  :$direction;
				$s = ($vdirection == GDirection::OUT) ? $e->getVertexTO() : $e->getVertexFrom();
				if (!$vertexFilter($c, $s, $e, $distance)){
					//Log::info( "#2# VERTEXFILTER SKIP VERTEX V: " . $s->id());
					continue;
				}
				if (! $s->hasTmpAttribute($distanceKey)) { // if s unvisited
					//Log::info( "#2# V: " . $s->id() . " P (" . $v->id() . ") D: " . $distance);
					$frontier[] = $s; // PUSH // array_unshift($frontier,$s);
					$s->setTmpAttribute($distanceKey, $distance); // mark s visited
					$c += 1;
					$hr = $handler($c, $s, $e, $distance);
					if ($hr != null && ! $hr) {  $frontierNotEmpty = false;  break; }
				}
			}
		}
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see GGraph::traverseDF()
	 */
	public function traverseDF($root, $handler, $elements = null, $direction = GDirection::OUT) {
// 		if ($direction == GDirection::BOTH){
// 			throw new Exception('traverseDF ERROR DIRECITON: GDirection::BOTH');
// 		}

		$g = $this;
		$cKey = function ($key) use($g) {
			$nsv = $g->nextTSequenceValue();
			return ('__TD' . $nsv . '-' . $key);
		};
		$visitedKey = $cKey('VISITED');
		// $colorKey = $cKey('COLOR');

		$c = 0;
		$dfs = function ($edge, $v) use(&$c, &$handler, $visitedKey, &$dfs, $elements, $direction,&$root) {
			/* @var $v GVertex  */
			$v->setTmpAttribute($visitedKey, 1);
			// $v->setTmpAttribute($colorKey, 1);//GRAY

			if ($c > 0) {
				$handler($c, $v, $edge, $root);
			}
			$c += 1;
 			$successorEdges = $v->getEdges($direction, $elements);
 			foreach ( $successorEdges as $e ) {
 				$s = $e->getVertexTO();
 				if (! $s->hasTmpAttribute($visitedKey)) {
 					$dfs($e, $s);
 				}
 			}
// 			// $v->setTmpAttribute($colorKey, 2);//BLACK

		};

		$dfs(null, $root);
	}

	/**
	 *
	 * @param long $vertexId
	 * @param GPropertyItem $p
	 * @param long $vertextId
	 */
	public function _addProperty($p) {
		//Log::info("** GRAPH  _addProperty: $p");
		$prid = $p->id();
		if (!empty($prid) && isset($this->propertyIndex[$prid])){
			//Log::info("_addProperty SKIP: $prid\n");
			return null;
		} else {
			$this->propertyIndex[$prid] = $prid;
		}
		$vertexId = $p->itemId();
		$v1 = $this->getVertexOrCreate(GURN::createOLDWithId($vertexId));

		//if ($p->hasRefItem() && ! $p->hasParent()) {
		if ($p->hasRefItem()) {
			//Log::info("** GRAPH  _add Edge Property: $p");
			$v2 = $this->getVertexOrCreate(GURN::createOLDWithId($p->refItem()));
			$vkey = GURN::createVKEY($p->element(), $v1->urnStr(), $v2->urnStr(),$p->parent());
			$eurn = GURN::createOLDWithVKEY($vkey);

			$e = new GEdgeO($this, $eurn, $v1, $v2, $p->element(), $p->inferred(), $p,null,$p->value(), $p->treeId(),$p->parent(),$p->data());
			$v1->_addEdge($e);
			$v2->_addEdge($e);
			$this->memE[$vkey] = $e;
// 		} else {
// 				$v1->_addProperty($p);
		}
		$v1->_addProperty($p);
		return $v1;
	}


	public function renameEdge($vKey, $newElement){
		Log::info("@@: renameEdge: " . $vKey . ' : ' . $newElement);
		/* @var $edge GEdgeO */
		$edge = $this->memE[$vKey];
// 		$pp = $edge->persistenceProp();
// 		if (empty($pp)){

// 		}

		$vFrom = $edge->getVertexFrom();
		if ($vFrom->isReadOnly()){
			Log::info("READONLY SKIP (1)");
			return;
		}
		$vTo = $edge->getVertexTO();
		$nvkey = GURN::createVKEY($newElement,$vFrom->urnStr(), $vTo->urnStr());
		$nurn  = GURN::createNEWWithVKEY($nvkey);
		$pprop = $edge->persistenceProp();
		$pprop->setElement($newElement);
		$ne = new GEdgeO($this,$nurn , $vFrom, $vTo, $edge->element(), $edge->isInferred(),$pprop,$edge->getDependencies(),$edge->label(),$edge->treeId(),$edge->getParentTreeId(), $edge->data());
		$ne->setElement($newElement);//update updates_tracker
		$vFrom->_removeEdge($edge);
		$vTo->_removeEdge($edge);
		unset($this->memE[$vKey]);
		$vFrom->_addEdge($ne);
		$vTo->_addEdge($ne);
		$this->memE[$ne->vkey()]  =$ne;

	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see GGraph::addEdge()
	 */
	public function addEdge($v1UrnStr, $v2UrnStr, $element, $derivative = true, $deps = null,$label=null) {
		//Log::info("@@: addEdge: " . $v1UrnStr . ' --[' . $element . ']--> ' . $v2UrnStr . ' I: ' . ($derivative?'T':'F') );
		if (!isset($this->memV[$v1UrnStr]) || !isset($this->memV[$v2UrnStr])){
			throw new Exception("CANOT ADD EDGE $v1UrnStr -> $v2UrnStr");
		}
		$v1 = $this->memV[$v1UrnStr];
		$v2 = $this->memV[$v2UrnStr];

		if ($v1->hasEdge(GDirection::OUT, $element, $v2UrnStr)){
			return null;
		}


		//Log::info(" addEdge:  $v1 $v2 $element");
		$vkey = GURN::createVKEY($element,$v1UrnStr,$v2UrnStr);
		$urn = GURN::createNEWWithVKEY($vkey);
		$e = new GEdgeO($this,$urn , $v1, $v2, $element, $derivative,null,$deps,$label);
		$v1->_addEdge($e);
		$v2->_addEdge($e);
		$this->memE[$e->vkey()] = $e;
		//$this->memE[$e->urnStr()] = $e;
		if (!$derivative){
			$this->inserts_tracker[] =array('edge'=>$e);
		}
		return $e;
	}

// 	public function uuid(){
// 		return $this->_uuid;
// 	}

	public function countVertices(){
		return count($this->memV);
	}
	public function countEdges(){
		return count($this->memE);
	}


// 	private $deletes_tracker = array();
// 	public function _getDeletesTracker(){
// 		return $this->deletes_tracker;
// 	}

	private $inserts_tracker = array();
	public function _getInsertTracker(){
		return $this->inserts_tracker;
	}

	private $updates_tracker = array();
	public function _getUpdatesTracker(){
		return $this->updates_tracker;
	}


	private $vertices_updates_tracker = array();
	public function _getVerticesUpdatesTracker(){
		return $this->vertices_updates_tracker;
	}
	/**
	 *
	 * @param GPropertyO $p
	 */
	public function updateProperty($p){
		if (empty($p)){
			return;
		}
		$key = $p->id();
		if (!empty($key)){
			//PUtil::logInfo('UPDATE PROPERTY: ' . $p->element() . ' :: ' . $p->itemId() . " :: " . $p->value());
			$this->updates_tracker[$key ] = array('prop'=>$p);
		} else {
			Log::info("updatePropertyValue error canot update property " . $p);
		}
	}



	private $memOTV = array();

	public function &getRefernceToVerticesByOT($ot){
		$vertices = &$this->memOTV;
		if (empty($vertices[$ot])){
			$vertices[$ot] = array();
			foreach ($this->memV as $v){
				if ($v->getObjectType() == $ot){
					$vertices[$ot][] = $v;
				}
			}
		}
		return $vertices[$ot];
	}


}


class GraphFilter {
	private $vertices;


	/**
	 *
	 * @param GGraph $graph
	 */
	public function __construct($graph) {
		$this->vertices = &$graph->getRefernceToVertices();
	}





}
