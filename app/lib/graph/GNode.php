<?php


//DOT1:  "ItemFlagsI" [ label = "ItemFlagsI\n (interface)"];
interface ItemFlagsI {
	public function setFlags($itemFlags);
	/**
	 * @return array
	 */
	public function getFlags();
	public function getFlagsJson();
	public function addFlag($flag);
	public function hasFlag($flag);
	public function removeFlags();
}


interface TmpAttributesI {

	public function getTmpAttribute($key);
	public function setTmpAttribute($key,$val);
	public function hasTmpAttribute($key,$val);
	public function getTmpAttributes();

}

interface AttributesI {

	public function getAttribute($key);
	public function setAttribute($key,$val);
	public function hasAttribute($key,$val);
	public function getAttributes();


}

//DOT1:  "GNode" [ label = "GNode\n (interface)"];
//DOT2:  "GNode" -> "ItemFlagsI"  [ label = "extends"];
interface GNode extends ItemFlagsI, TmpAttributesI, AttributesI {
	/**
	 * @return GURN
	 */
	public function urn();

	public function urnStr();
	/**
	 * @return string
	 */
	public function id();

	/**
	 * @return GGraph
	 */
	public function graph();


	public function persistenceId();


	/////////////////////////////////////////////////////////////
	//PROPERTIES (persistence)
	/////////////////////////////////////////////////////////////
	/**
	 * arrayof(element=>arrayof(GPValue))
	 *
	 * @return GPValue[][]
	 */
	 public function getAllProperties();

	 public function getPropertiesCount();
	 public function hasProperties();

	 /**
	 * @param string $element
	  * @return GPValue[]
	  */
	  public function getElementProperties($element);

	  /**
	  * @return GPValue[]
	  */
	  public function getProperties($element);

	  /**
	  * @return GPValue
	  */
	  public function getProperty($element, $idx=null);
	  public function getPropertyValue($element, $idx=null);


	  /**
	   * @see GPValueTree
	   * @param integer $treeId  (dsd.metadatavalue2(lid))
	   */
	  public function hasTreeProperty($treeId);
	  /**
	   * @see GPValueTree
	   * @param integer $treeId  (dsd.metadatavalue2(lid))
	   */
	  public function getTreeProperty($treeId);
	  /**
	   * @see GPValueTree
	   * treeId
	   * @param integer $treeId  (dsd.metadatavalue2(lid))
	   */
	  public function getChildProperties($treeId);


	  /**
	   *
	   * @param string $element
	   * @param integer $idx
	   * @param string $text_value
	   */
	  public function updatePropertyValue($element, $idx,$text_value);


	  public function isReadOnly();

}




class MapImpl {

	private $map = null;

	public function __construct($map = array()){
		$this->map = $map;
	}

	public function get($key){
		return isset($this->map[$key])  ? $this->map[$key] : null;
	}

	public function set($key,$val){
		$this->map[$key] = $val;
	}

	public function has($key,$val = null){
		if ($val != null){
			return isset($this->map[$key])  ? $this->map[$key] == $val : false;
		} else {
			return isset($this->map[$key]);
		}
	}

	public function getMap(){
		return $this->map;
	}


}


//DOT1:  "GNodeO" [ label = "GNodeO (class)"];
//DOT2:  "GNodeO" -> "GNode"  [ label = "implements"];
class GNodeO  implements GNode {

	/**
	 * @var GGraph
	 */
	private $_graph;
	private $_urn;
	private $_id;
	private $_persistenceId;
	//private $full_loaded_flag = false;
	private $tmpAttrs;
	private $attrs;



	/**
	 *
	 * @param GURN $urn
	 */
	public function __construct($graph,$urn,$_persistenceId = null){
		$this->_graph = $graph;
		if (empty($urn)){
				$this->_urn = GNodeURNUtil::createTmp();
		} else {
			$this->_urn = $urn;
		}
		$this->_id = $urn->nss();
		$this->_persistenceId = $_persistenceId;
		$this->tmpAttrs = new MapImpl();
		$this->attrs = new MapImpl();
	}







	public function persistenceId(){
		return $this->_persistenceId;
	}

	/**
	 * @return GURN
	 */
	public function urn(){
		return $this->_urn;
	}

	public function urnStr(){
		return $this->_urn->toString();
	}

	/**
	 * @return string
	 */
	public function id(){
		return $this->_id;
	}



	public function graph(){
		return $this->_graph;
	}



	///////////////////////////////////////////////////
	//TMPATTRIBUTES
	///////////////////////////////////////////////////

	public function getTmpAttribute($key){
		return $this->tmpAttrs->get($key);
	}

	public function setTmpAttribute($key,$val){
		$this->tmpAttrs->set($key, $val);
	}

	public function hasTmpAttribute($key,$val = null){
		return $this->tmpAttrs->has($key,$val);
	}
	public function getTmpAttributes(){
		return $this->tmpAttrs->getMap();
	}


	///////////////////////////////////////////////////
	//ATTRIBUTES
	///////////////////////////////////////////////////
	public function _setAttributes($attrs){
		$this->attrs = new MapImpl($attrs);
	}

	public function getAttribute($key){
		return $this->attrs->get($key);
	}

	public function setAttribute($key,$val){
		$this->attrs->set($key, $val);
	}

	public function hasAttribute($key,$val = null){
		return $this->attrs->has($key,$val);
	}
	public function getAttributes(){
		return $this->attrs->getMap();
	}


	//////////////////////////////////////////////////////////////////////////
	// PROPERTIES
	//////////////////////////////////////////////////////////////////////////
	/**
	 * arrayof(element=>arrayof(GPValue))
	 *
	 * @var GProperty[][]
	 */
	 private $_props = array();

	 protected $_tree_properties = array(); // MAP[treeId]=0  SET OF treeIDs pou anikoun sto edge


	 /**
	 *
	  * @param GProperty $p
	   */
	   public function _addProperty($p){
	    //Log::debug("***addProperty " . $this->_urn . ' _addProperty: ' . $p);
	   	if (method_exists($p,'treeId') && method_exists($p,'level')) {
		   	$pTreeId =  $p->treeId();
		   	$level = $p->level();
		   	if (!empty($pTreeId) && ! empty($level) && $level > 1) {
		   		//Log::debug("***addTreeProperty " . $this->_urn . ' _addProperty: ' . $level);
		   		$this->_tree_properties[$pTreeId] = $p;
		   		return;
		   	}
	   	}
		  $el = $p->element();
		  $this->addProperty($el,$p);

	   }

	   public function hasTreeProperty($treeId){
	   	return isset($this->_tree_properties[$treeId]);
	   }

	   public function getTreeProperty($treeId){
	   	return isset($this->_tree_properties[$treeId]) ? $this->_tree_properties[$treeId] : null;
	   }


	   public function  getChildProperties($treeId){
	   	$rep = array();
	   	foreach ($this->_tree_properties as $tid => $p){
	   		if ($p->parent() == $treeId){
	   			$rep[] = $p;
	   		}
	   	}
	   	return $rep;
	   }

	   /**
	   * @param string $element
	   * @param string $prop
	   */
	   public function addPropertyValue($element, $value, $data=null,$lang = null, $weight = null){
	   //Log::debug("*** " . $this->_urn . ' addPropertyValue: ' . $element . " : " . $value);
	   	$v = new GPValueO($value,$data,$lang,$weight);
	   	return $this->addProperty($element, $v);
	   }

	   	/**
	   	* @param string $element
	   	* @param GPValue $prop
	   	*/
	   	public function addProperty($element, $value){
	   	//Log::debug("*** " . $this->_urn . ' addProperty: ' . $element . " : " . $value);
	   		if (!isset($this->_props[$element])){
	   		$this->_props[$element] = array();
	   		}
	   		$this->_props[$element][] = $value;

	   		// 		$rep = null;
	   		// 		$w = $value->weight();
	   		// 		if (! empty($w)){
	   		// 			if (isset($arr[$w])){
	   		// 				$rep = $arr[$w];
	   		// 			}
	   		// 			$arr[$w] = $value;
	   		// 		} else {
	   		// 			$arr[] = $value;
	   		// 		}
	   		// 		return $rep;
	   	}

	   	public function getAllProperties(){
	   	return $this->_props;
	   	}

	   	public function getPropertiesCount(){
	   	return count($this->_props);
	   	}

	   	public function hasProperties(){
	   		return  !empty($this->_props);
	   	}

	   	public function clearProperty($element){
	   		unset($this->_props[$element]);
	   	}

	   	public function resetPropertyValue($element,$value){
	   		$this->clearProperty($element);
	   		$this->addPropertyValue($element, $value);
	   	}


	   	public function getElementProperties($element){
	   		return isset($this->_props[$element]) ? $this->_props[$element] :array();
	   	}

	   	public function getProperties($element){
	   		if (!isset($this->_props[$element])){
	   			return null;
	   		}

	   		return $this->_props[$element];
	   	}

	   	/**
	   	 * (non-PHPdoc)
	   	 * @see GNode::getProperty()
	   	 */
	   	public function getProperty($element, $idx=null){
	   		//return isset($this->_props[$element]) ? $this->_props[$element] :null;
	   		if (!isset($this->_props[$element])){
	   			return null;
	   		}
	   		if (empty($idx)){
	   			return reset($this->_props[$element]);
	   		}

	   		if (isset($this->_props[$element][$idx])){
	   			return  $this->_props[$element][$idx];
	   		}

	   		Log::info(print_r($this->_props,true));
	   		throw new Exception("node.getPrperty: canot find $element $idx");
	   	}

	   	/**
	   	 * (non-PHPdoc)
	   	 * @see GNode::getPropertyValue()
	   	 */
	   	public function getPropertyValue($element, $idx=null){
	   		$p = $this->getProperty($element,$idx);
	   		return empty($p) ? null : $p->value();
	   	}



			private $updates_tracker = array();
			public function _getUpdatesTracker(){
				return $this->updates_tracker;
			}

		public function isReadOnly(){
			return ($this->hasTmpAttribute('_READONLY'));
		}
		public function setReadOnly(){
			$this->setTmpAttribute('_READONLY',true);
		}

		public function updatePropertyValue($element, $idx, $text_value){
			if ($this->isReadOnly()){ return; };

			$p = $this->getProperty($element,$idx);
				/* @var $p GPropertyO */
				if (!empty($p)){
					$p->setValue($text_value);
					$this->_graph->updateProperty($p);
				}
		}


	   	/**
	   	 * @param string $element
	   	 * @param string $prop
	   	 */
	   	public function removePropertyValue($element, $value){
	   		$c = 0;
	   		if (isset($this->_props[$element])){
	   			$arr = $this->_props[$element];

	   			foreach ($arr as $k=>$p){
	   				/*@var $p GPValue */
	   				if ($p->value() == $value){
	   					$c+=1;
	   					unset($arr[$k]);
	   				}
	   			}
	   		}

	   		return $c;
	   	}



	   ///////////////////////////////////////////////////////////////////////////////////////////
	   //// ITEM FLAGS  des class ItemFlags parakato
	   ///////////////////////////////////////////////////////////////////////////////////////////

	   	private $itemFlags = array();

	   	public function removeFlags(){
	   		$this->itemFlags = array();
	   	}


	   	public function setFlags($itemFlags){
	   		$this->itemFlags = $itemFlags;
	   	}

	   	public function getFlags(){
	   		return $this->itemFlags;
	   	}

	   	public function getFlagsJson(){
				if (!empty($this->itemFlags)){
					return json_encode($this->itemFlags);
				}
				return null;
	   	}

	   	public function addFlag($flag) {
	   		if (! $this->hasFlag($flag)){
	   			$this->itemFlags[] = $flag;
	   		}
	   	}

	   	public function hasFlag($flag) {
	   		foreach ($this->itemFlags as $f) {
	   			if ($flag == $f){
	   				return true;
	   			}
	   		}
	   return false;
	   }


}





class ItemFlags  implements ItemFlagsI {

	private $itemFlags;

	public function __construct($itemFlags=null) {
		if (empty($itemFlags)){
			$this->itemFlags = array();
		} else {
			$this->itemFlags = $itemFlags;
		}
	}

	public function removeFlags(){
		$this->itemFlags = array();
	}

	public function setFlags($itemFlags){
		$this->itemFlags = $itemFlags;
	}


	public function getFlags(){
		return $this->itemFlags;
	}

	public function getFlagsJson(){
		if (!empty($this->itemFlags)){
			return json_encode($this->itemFlags);
		}
		return null;
	}


	public function addFlag($flag) {
		if (! $this->hasFlag($flag)){
			$this->itemFlags[] = $flag;
		}
	}

	public function hasFlag($flag) {
		foreach ($this->itemFlags as $f) {
			if ($flag == $f){
				return true;
			}
		}
		return false;
	}


}






