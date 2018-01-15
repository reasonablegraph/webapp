<?php


//DOT1:  "GEdge" [ label = "GEdge\n (interface)"];
//DOT2:  "GEdge" -> "GNode"  [ label = "extends"];
interface GEdge extends GNode {

	const OBJECT_TYPE = 'edge';

	public function element();
	public function setElement($element);

	public function label();
	public function setLabel($label);


	/**
	 * string representation of json data
	 * return string
	 */
	public function data();
	/**
	 * string representation of json data
	 * @param string $data
	 */
	public function setData($data);


	/**
	 * @return GVertex
	 */
	public function getVertexFrom();
	/**
	 * @return GVertex
	 */
	public function getVertexTO();


	/**
	 * @return boolean
	 */
	public function isInferred();


	public function  setInferred($isInferred = true);

	/**
	 * @var string
	 */
	public function vkey();
	public function getDependencies();
	public function addDependency($id);

	/**
	 * lid
	 * @return integer
	 */
	public function treeId();

	/**
	 * @return integer
	 */
	public function getParentTreeId();





}

//DOT1:  "GEdgeO" [ label = "GEdgeO\n (class)"];
//DOT2:  "GEdgeO" -> "GNodeO"  [ label = "extends"];
//DOT2:  "GEdgeO" -> "GEdge"  [ label = "implements"];
class GEdgeO extends GNodeO implements GEdge {


	//public $_trnUUID;

	/**
	 *
	 * @var string
	 */
	private $_element;

	private $_property;

	/**
	 * @var GVertex
	 */
	private $_from;
	/**
	 * @var GVertex
	 */
	private $_to;

	/**
	 * @var GProperty
	 */
	private $_persistenceProp = null;

	private $_deps = array();

	private $_inferred = null;

	private $_label = null;
	private $_treeId =  null;
	private $_parentTreeId = null;

	/**
	 *
	 * @var string
	 */
	private $_vkey = null;

	private $_data = null;

	/**
	 * @param GVertice $from
	 * @param GVertice $to
	 * @param string $element
	 * @param GProperty $persistenceProp
	 * @param GURN $urn
	 * @param GGraph $graph
	 */
	public function __construct($graph, $urn, $from,$to,$element,$inferred=false,$persistenceProp = null, $deps=null, $label = null, $treeId = null, $parentTreeId = null,$data = null) {
		//if ($inferred == null){ $inferred = false;};
		//if ($deps == null){ $deps = array();};

		$this->_from = $from;
		$this->_to = $to;
		$this->_element = $element;
		$this->_inferred = $inferred;
		$this->_persistenceProp = $persistenceProp;
		$this->_label = $label;
		$this->_parentTreeId = $parentTreeId;
		$this->_data = $data;

		if (!empty($deps)){
			$this->_deps = $deps;
		}
		//$this->_vkey = $element . '‡' . $from->urnStr() . '‡' . $to->urnStr();
		$this->_vkey = $urn->nss();
// 		if ($temporalType == GURN::TEMPORAL_TYPE_OLD){
// 			$urn = GURN::createOLDWithVKEY(vkey);
// 		}elseif ($temporalType == GURN::TEMPORAL_TYPE_NEW){
// 			$urn = GURN::createNEWWithVKEY(vkey);
// 		} else {
// 			$urn = GURN::createTMPWithVKEY(vkey);
// 		}
		//$this->_deps = $deps;
		if (!empty($persistenceProp)){
			parent::__construct($graph, $urn,$persistenceProp->id());
		} else {
			parent::__construct($graph, $urn);
		}

		if (! empty($treeId)){
			$this->_treeId = $treeId;
			//$this->_tree_properties[$treeId] = 0;
		}

	}



	/**
	 * overrides GNodeO.hasTreeProperty();
	 * (non-PHPdoc)
	 * @see GNodeO::hasTreeProperty()
	 */
	public function hasTreeProperty($treeId){
		if (!empty($this->_treeId) && $treeId == $this->_treeId){
			return true;
		}
		return parent::hasTreeProperty($treeId);
	}






	/**
	 * (non-PHPdoc)
	 * @see GEdge::getVertexFrom()
	 */
	public function getVertexFrom(){
		return $this->_from;
	}
	public function getVertexTo(){
		return $this->_to;
	}

	public function getDependencies(){
		return $this->_deps;
	}

	public function addDependency($id){
		$this->_deps[$id] = $id;
	}

	public function element(){
		return $this->_element;
	}

	public function setElement($element){
			$this->_element = $element;
			if (! empty($this->_persistenceProp)){
				$graph = $this->graph();
				$this->_persistenceProp->setElement($element);
				$graph->updateProperty($this->_persistenceProp);
			}
	}


	/**
	 *
	 * @return GPropertyO
	 */
	public function persistenceProp(){
		return $this->_persistenceProp;
	}

	public function __toString(){
		$id = $this->urnStr();
		$s = 'e:[';
		if ($this->isInferred()){
			$s .= 'D ';
		}
		$s .= $id;
// 		$pc = count($this->getAllProperties());
// 		if ($pc > 0){
// 			$s .=	sprintf(' pc: %s',$pc);
// 		}
		$s .=	"]";
		return $s;
	}

	public function  isInferred(){
		return $this->_inferred;
	}

	public function  setInferred($isInferred = true){
		return $this->_inferred = $isInferred;
	}


	public function vkey(){
		return $this->_vkey;
	}


	public function label(){
		return $this->_label;
	}


	public function setLabel($label){
		$this->_label = $label;
		if (! empty($this->_persistenceProp)){
					$graph = $this->graph();
					$this->_persistenceProp->setValue($label);
					$graph->updateProperty($this->_persistenceProp);
			}
	}


	public function treeId(){
		return $this->_treeId;
	}

	public function getParentTreeId(){
		return $this->_parentTreeId;
	}


	public function data(){
		return $this->_data;
	}
	/**
	 * string representation of json data
	 * @param string $data
	 */
	public function setData($data){
		$this->_data = $data;
		if (! empty($this->_persistenceProp)){
			$graph = $this->graph();
			$this->_persistenceProp->setData($data);
			$graph->updateProperty($this->_persistenceProp);
		}
	}



}