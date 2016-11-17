<?php

//DOT1:  "GPValue" [ label = "GPValue\n (interface)"];
interface GPValue {
	public function value();
	/**
	 * @return string
	 */
	public function data();
	public function lang();
	public function weight();


	/**
	 *@return array
	 */
	public function dataArray();
	//public function jsonData();

	/**
	 * jsondata['data'];
	 * @return array
	 */
	public function jdata($key=null);

	/**
	 * jsondata['prps'];
	 * @return array
	 */
	public function prps($property = null);
	public function pnctn();




	/**
	 * to value se morfi json
	 * jsondata['json'];
	 * @return array
	 */
	public function valueJson();



}

//DOT1:  "GPValueGraph" [ label = "GPValueGraph\n (interface)"];
//DOT2:  "GPValueGraph" -> "GPValue"  [ label = "extends"];
interface GPValueGraph extends GPValue {
	public function refItem();
	public function hasRefItem();
	public function deps();
	public function inferred();
}


//DOT1:  "GPValueTree" [ label = "GPValueTree\n (interface)"];
//DOT2:  "GPValueTree" -> "GPValueGraph"  [ label = "extends"];
interface GPValueTree extends GPValueGraph {


	public function parent();
	/**
	 * dsd.metadatavalue2(lid)
	 */
	public function treeId();
	public function hasParent();
	//public function level();

}


//DOT1:  "GPElement" [ label = "GPElement\n (interface)"];
//DOT2:  "GPElement" -> "GPValue"  [ label = "extends"];
interface GPElement  extends GPValue {
	public function element();
}


//DOT1:  "GPropertyGraph" [ label = "GPropertyGraph\n (interface)"];
//DOT2:  "GPropertyGraph" -> "GPValueGraph"  [ label = "extends"];
//DOT2:  "GPropertyGraph" -> "GPElement"  [ label = "extends"];
interface GPropertyGraph extends GPValueGraph, GPElement  {
}

//DOT1:  "GPropertyTree" [ label = "GPropertyTree\n (interface)"];
//DOT2:  "GPropertyTree" -> "GPValueTree"  [ label = "extends"];
//DOT2:  "GPropertyTree" -> "GPElement"  [ label = "extends"];
interface GPropertyTree extends GPValueTree, GPElement  {
}

//DOT1:  "GPropertyItem" [ label = "GPropertyItem\n (interface)"];
//DOT2:  "GPropertyItem" -> "GPropertyTree"  [ label = "extends"];
interface GPropertyItem extends GPropertyTree {
	public function itemId();
	public function id();
}




//DOT1:  "AbstractGPValueO" [ label = "AbstractGPValueO\n (class)"];
abstract class AbstractGPValueO  {

	abstract function value();
	abstract function data();


	//public function jsonData(){
	public function dataArray(){
		$data = $this->data();
		if (!empty($data)){
			return json_decode($data, true);
		}
		return array();
	}


	public function jdata($key = null){
		$json = $this->dataArray();
		if (!empty($json) && isset($json['data'])){
			$props = $json['data'];
		} else {
			$props = array();
		}

		if (!empty($key)){
			if (isset($props[$key])){
				return $props[$key];
			} else {
				return null;
			}
		}
		return $props;

	}

	public function prps($property = null){
		$json = $this->dataArray();
		if (!empty($json) && isset($json['prps'])){
			$props = $json['prps'];
		} else {
			$props = array();
		}

		if (!empty($property)){
			if (isset($props[$property])){
				return $props[$property];
			} else {
				return null;
			}
		}
		return $props;
	}


	public function pnctn(){
		$tmp = $this->prps('pnctn');
		return empty($tmp) ?  $this->value() : $tmp;
	}

	public function valueJson(){
		$json = $this->dataArray();
		if (!empty($json) && isset($json['json'])){
			$props = $json['json'];
		} else {
			$props = array();
		}
		$props['text_value'] = $this->value();
		return $props;
	}

}


//DOT1:  "GPValueO" [ label = "GPValueO\n (class)"];
//DOT2:  "GPValueO" -> "AbstractGPValueO"  [ label = "extends"];
//DOT2:  "GPValueO" -> "GPValue"  [ label = "implements"];
class GPValueO  extends AbstractGPValueO implements GPValue {
	private $value;
	private $data;
	private $lang;
	private $weight;

	public function __construct($value,$data,$lang,$weight) {
		$this->value = $value;
		$this->data  = $data;
		$this->lang = $lang;
		$this->weight = $weight;
	}

	public function value(){
		return $this->value;
	}
	public function data(){
		return $this->data;
	}
	public function lang(){
		return $this->lang;
	}
	public function weight(){
		return $this->weight;
	}


	public function __toString(){

		return $this->value;
	}


}

//DOT1:  "GPropertyO" [ label = "GPropertyO\n (class)"];
//DOT2:  "GPropertyO" -> "AbstractGPValueO"  [ label = "extends"];
//DOT2:  "GPropertyO" -> "GPropertyItem"  [ label = "implements"];
class GPropertyO extends AbstractGPValueO implements GPropertyItem {

		private $row;

		private function __construct() {

		}

		public static function withRow($row) {
			$instance = new self();
			$instance->row = $row;
			return $instance;
		}


		/**
		 *
		 * @param unknown $element
		 * @param GPValue $value
		 * @return GPropertyO
		 */
		public static function withGPValue( $element,$value) {
			$instance = new self();
			$instance->row = array('element'=>$element, 'text_value'=>$value->value(), 'data'=>$value->data(), 'text_lang'=>$value->lang(),'weight'=>$value->weight());
			return $instance;
		}



		public static function withEV( $element,$value,$data=null,$lang=null,$weight=null) {
			$instance = new self();
			$instance->row = array('element'=>$element, 'text_value'=>$value, 'data'=>$data, 'text_lang' => $lang,'weight'=>$weight);
			return $instance;
		}

		public static function withGraph($element,$value,$data,$lang,$weight, $refItem, $inferred, $deps) {
			$instance = new self();
			$instance->row = array('element'=>$element, 'text_value'=>$value, 'data'=>$data, 'text_lang' => $lang,'weight'=>$weight,
			'ref_item'=>$refItem, 'inferred'=>$inferred, 'deps'=>$deps);
			return $instance;
		}

		public static function withTree($element,$value,$data, $lang,$weight, $refItem, $inferred, $deps,$parent,$treeId) {
			$instance = new self();
			$instance->row = array('element'=>$element, 'text_value'=>$value, 'data'=>$data, 'text_lang' => $lang,'weight'=>$weight,
			'ref_item'=>$refItem, 'inferred'=>$inferred, 'deps'=>$deps,
			'link'=>$parent,'treeId' => $treeId );
			return $instance;
		}

// 	metadata_value_id,item_id,element,ref_item,text_value,text_lang,link,lid FROM dsd.metadatavalue2 WHERE (%s) %s ',$PQ1,$PQ2
// 	$p = new GProperty($row['element'],$row['text_value'],$row['text_lang'],null,$row['lid'], $row['link'],$row['ref_item'],$row['metadata_value_id']);
//	metadata_value_id,item_id,element,ref_item,text_value,text_lang,link,lid

		public function id(){
			return $this->row['metadata_value_id'];
		}

		public function setValue($text_value){
			$this->row['text_value'] = $text_value;
		}

		public function value(){
			return $this->row['text_value'];
		}
		public function data(){
			return isset($this->row['data']) ?  $this->row['data'] : null;
		}
		public function setData($data){
			$this->row['data'] = $data;
		}

		public function lang(){
			return isset($this->row['text_lang']) ?  $this->row['text_lang'] : null;
		}
// 		public function clearParent(){
// 			unset($this->row['link']);
// 		}
		public function parent(){
			return isset($this->row['link']) ?  $this->row['link'] : null;
		}
		public function refItem(){
			return isset($this->row['ref_item']) ?  $this->row['ref_item'] : null;
		}
		public function treeId(){
			return isset($this->row['lid']) ?  $this->row['lid'] : null;
			return $this->row['lid'];
		}
		public function itemId(){
			return isset($this->row['item_id']) ?  $this->row['item_id'] : null;
		}
		public function deps(){
			return isset($this->row['deps']) ?  $this->row['deps'] : array();
		}
		public function inferred(){
			return  (isset($this->row['inferred']) && $this->row['inferred']);
		}
 		public function element(){
 			return $this->row['element'];
 		}
 		public function setElement($element){
 			return $this->row['element'] = $element;
 		}
		public function hasRefItem(){
			return  (isset($this->row['ref_item']) && !empty($this->row['ref_item']));
		}
		public function hasParent(){
			return  (isset($this->row['link']) && !empty($this->row['link']));
		}

		public function weight(){
			return isset($this->row['weight']) ?  $this->row['weight'] : null;
		}


		public function _decreaseLevel(){
			if (isset($this->row['level'])){
				$level = $this->row['level'];
				if ($level > 1){
					$level -=1;
				}
				$this->row['level'] = $level;
			}
		}
		public function level(){
			return isset($this->row['level']) ?  $this->row['level'] : null;
		}

		public function __toString(){
			$s = implode(', ', $this->row);
			return $s;
		}


}






