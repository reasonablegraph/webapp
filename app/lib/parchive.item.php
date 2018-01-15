<?php
/*
item_id            | 36
metadata_schema_id | 2
metadata_field_id  | 77
prefix             | ea
element            | collection
qualifier          | name
search_value       | Άλεφ
text_value         | Άλεφ
text_lang          | el_GR
metadata_value_id  | 346
*/

/*

 (
    [0] => ea:collection:place
    [1] => ea:edoc:Tagged
    [2] => ea:edoc:Pages
    [3] => ea:edoc:Encrypted
    [4] => ea:edoc:Page-size
    [5] => ea:pdf:Optimized
    [6] => ea:pdf:PDF-version
    [7] => dc:date:accessioned
    [8] => ea:edoc:Producer
    [9] => ea:edoc:Title
    [10] => ea:edoc:Creator
    [11] => ea:edoc:File-size
    [12] => dc:contributor:author
    [13] => dc:title:
    [15] => dc:subject:
    [16] => ea:subject:
    [17] => ea:date:orgissued
    [18] => ea:original:print
    [19] => ea:source:
    [20] => ea:source:print
    [21] => ea:publication:place
    [22] => ea:status:
    [23] => dc:description:provenance
    [24] => dc:date:available
    [25] => dc:date:issued
    [26] => dc:identifier:uri
    [27] => ea:collection:name

  115 | ea:language:original
  116 | ea:language:publication
  117 | ea:title:original
  120 | ea:contributor:translator
  121 | ea:contributor:editor



	@DocGroup(module="actor", group="php", comment="item - metadata")

 */

class ItemMetadataIterator implements Iterator {
	private $method=0;
	private $position = 0;
	/**
	 *
	 * @var ItemMetadata
	 */
	private $itemMetadata;
	public $elements = array(
		"ea:obj-type:",
		"dc:language:iso",
		"dc:identifier:issn",
		"dc:identifier:isbn",
		"dc:title:",
		"ea:subtitle:",
		"ea:title:uniform",
		"ea:issue:",
		"ea:issue:no",
		"ea:issue:label",
		"ea:issue:comment",
		"ea:website:url",
		"ea:website:url-base",
		"ea:date:captured",
		"dc:contributor:author",
		"ea:contributor:responsible",
		"ea:contributor:editor",
		"dc:contributor:editor",
		"ea:contributor:translator",
		"dc:contributor:illustrator",
		"dc:contributor:advisor",
		"dc:contributor:other",
		"ea:contributor-type:",
		"ea:person:name-titles",
		"dc:publisher:",
		"ea:name:_type",
		DataFields::ea_sn, //ARTIFACT
		DataFields::ea_call_number_ea, //ARTIFACT
		DataFields::ea_call_number_lcc, //ARTIFACT
		DataFields::ea_call_number_ddc, //ARTIFACT
		DataFields::ea_call_number_cc, //ARTIFACT
		"ea:date:orgissued",  //ARTIFACT, item
		"ea:date:start",
		"ea:date:end",
		"ea:publication:place",
		"dc:subject:",
		"ea:work:",
		"dc:description:abstract",
		DataFields::ea_material_type , //ARTIFACT
		DataFields::ea_size_pages,  //ARTIFACT
		DataFields::ea_artifact_location, //ARTIFACT
		DataFields::ea_owner, //ARTIFACT
		"dc:description:", //ARTIFACT, item
		'ea:call_number:ddc',
		"ea:url:related",
		"ea:url:origin",
		"ea:subject:",
		"ea:collection:name",
		"ea:collection:place",
		"ea:original:print",
		"ea:source:",
		"ea:source:print",
		"ea:size:",
		"ea:comment:",
		"ea:comment:internal",
		"ea:origin:comment",
		"ea:status:comment", //ARTIFACT, item
		DataFields::ea_status, //ARTIFACT, item
    );


		public function addElement($element){
			$this->elements[]= $element;
		}

		public function getElements(){
			return $this->elements;
		}

		public function setElements($elements){
			return $this->elements = $elements;
		}

		/**
		 *
		 * @param ItemMedata $itemMetadata
		 * @param number $method (1: ean to itemetadata den ine adio pros8eti ta klidia ston iterator)
		 */
    public function __construct($itemMetadata, $method=0) {
        $this->position = 0;
        $this->itemMetadata = $itemMetadata;
        $this->method = $method;
        if ($this->method  == 1){
					$elements_rest = array_diff($itemMetadata->getKeys(), $this->elements);
					$this->elements = array_merge($this->elements , $elements_rest);
        }

    }

    function rewind() {
        $this->position = 0;
    }

    function current() {
        $key = $this->key();
    	return $this->itemMetadata->getValueArraySK($key);
    }

    function key() {
    	return $this->elements[$this->position];
	}

    function next() {

        ++$this->position;
    }

    function valid() {
    	return (isset($this->elements[$this->position]));
    }
}




class MarcControl {

	private $marc_conf;

	public function __construct() {
	}

	private function loadMarcData(){
		if (empty($this->marc_conf)){
			$this->marc_conf=array();
			//$this->marc_conf=json_decode(file_get_contents(ROOT_DIR  . '/etc/marc-fields.json'),true);//FIXME: root_dir
		}
	}


	public function marcText2PatronText($key, $marc_text){ //TODO: implement $key
			$tmp = MarcUtil::parseMarcMnemonic($marc_text);
			if (! empty($tmp) &&  isset($tmp[0])){
				$marc = $tmp[0]->fields[0];
				return $this->marc2PatronText($key, $marc);
			}
			return null;
	}
	public function marc2PatronText($key, $marc_record){//TODO: implement $key
		$out = '';
		$sfs = $marc_record->subfields;
		$delimiter = '';
		foreach ($sfs as $sf){
			$out .= ($delimiter . trim($sf->data));
			$delimiter = ', ';
		}
		return $out;
	}


	public function isEmpty(){
		return empty($this->marc_conf);
	}

	public function getMarc($id){
		$this->loadMarcData();
		if (isset($this->marc_conf["marc"][$id])){
			return $this->marc_conf["marc"][$id];
		}
		return null;
	}

	public function findMarcId($key){
		$this->loadMarcData();
		foreach($this->marc_conf["marc"] as $marc_id => $marc){
			if (isset($marc["key"])){
				if ($marc["key"] == $key){
					return $marc_id;
				}
			}
		}
		return null;
	}

	public function findMarc($key){
		$mid = $this->findMarcId($key);
		if (!empty($mid)){
			return $this->getMarc($mid);
		}
		return null;
	}


// 	public  function validateMarcMnemonic($key, $val){
// 		//ARRAY
// 		if (is_array($val)){
// 			$arrValues = $val;
// 			$rep = array();
// 			if (!empty($arrValues)){
// 				$marc  = $this->findMarc($key);
// 				if (!empty($marc)){
// 					$subfield_data = $marc['subfields'];
// 					if (!empty($subfield_data)){
// 						foreach ($arrValues as $v){
// 							$str = $v[0];
// 							$errors = MarcUtil::validateMarcMnemonic($str, $subfield_data);
// 							$rep = array_merge($rep,$errors);
// 						}
// 					}
// 				}
// 			}
// 			return $rep;
// 		}

// 		//STRING
// 		$str = $val;
// 		$marc = $this->findMarc($key);
// 		if (!empty($marc)){
// 			$subfield_data = $marc['subfields'];
// 			if (!empty($subfield_data)){
// 				return MarcUtil::validateMarcMnemonic($str, $subfield_data);
// 			}
// 		}
// 		return array();
// 	}

}







class FieldControl {
	private $marc_control;
	private $field_conf;

	public function __construct() {
		$this->marc_control = new MarcControl();
	}

	private function loadFieldData(){
		if (empty($this->field_conf)){
			$this->field_conf=array(); //FIXME:ROOT_DIR
// 			$this->field_conf=json_decode(file_get_contents(ROOT_DIR  . '/etc/fields.json'),true);//FIXME: ROOT_DIR
// 			$this->field_conf= json_decode(file_get_contents(app_path().'/store/meta/fields.json'),true);
		}
	}


	public function getField2($key){
		$this->loadFieldData();
		$f = null;
		if (isset($this->field_conf['fields'][$key])){
			$f =  $this->field_conf['fields'][$key];
		}
		return $f;
	}


	public function getField($key){
		$this->loadFieldData();
		$f = null;
		if (isset($this->field_conf['fields'][$key])){
			$f =  $this->field_conf['fields'][$key];
			$mid = $f['marc'];
			if (!empty($mid)){
				$mdata = $this->marc_control->getMarc($mid);
				$f['marc_tag'] = $mid;
				$f['marc'] = $mdata;
			}
		}
		return $f;
	}



	public function getFieldData($key){
		$this->loadFieldData();
		if (isset($this->field_conf['fields'][$key])){
			return $this->field_conf['fields'][$key];
		}
		return null;
	}

	public function getMarcData($key){
		$df = getFieldData($key);
		if (empty($df) || !isset($df['marc'])) return null;
		$mid = $df['marc'];
		if (empty($mid)) return null;
		return $this->marc_control->getMarc($mid);
	}

	public function jdata2staff($key, $jdata){
		if(empty($jdata)){
			return null;
		}

 		if (isset($jdata['marc'])){
 			return($jdata['marc']);
 			// $out = '';
 			// $marc = $jdata['marc'];
 			// $sfs = $marc->subfields;
 			// $delimiter = '';
 			// foreach ($sfs as $sf){
 				// $out .= ($delimiter . '$' . $sf->identifier . ' ' . trim($sf->data));
				// $delimiter = ' ';
 			// }
 			// return $out;
 		}


		if (isset($jdata['json'])){
			$jd = $jdata['json'];
			return $jd;
		}

		return null;
	}


	public function jdata2Patron($key, $jdata){
		if(empty($jdata)){
			return null;
		}
		if (isset($jdata['marc'])){
			$marc_str = $jdata['marc'];
			return $this->marc_control->marcText2PatronText($key,$marc_str);
			// $sfs = $marc->subfields;
			// $delimiter = '';
			// foreach ($sfs as $sf){
				// $out .= ($delimiter . trim($sf->data));
				// $delimiter = ', ';
			// }
			// return $out;
		}
		if (isset($jdata['json'])){//FIXME: metatropi staff jdata to patron
			$jd = $jdata['json'];
			if (isset($jd['z'])){
				$class = $jd['z'];
			} else {
				echo("UNKNOWN json data:");
				  echo("<pre>");
				  print_r($jd);
				  echo("</pre>");
				$class = null;
			}
			// if ($class == 'isis-record'){
					// echo("isis-record");
				 // print_r($jd);
			// }
			if ($class == 'date'){
				$y = $jd['y'];
				$m = $jd['m'];
				$d = $jd['d'];
				$out =$y;
				if (!empty($m)){  $out .= '-' . $m;}
				if (!empty($d)){  $out .= '-' . $d;}
				return $out;
			}

			if ($class == 'url'){
				$u = $jd['u'];
				$d = $jd['d'];
				$out ="$u|$d";
				return $out;
			}

			//$jd = $jdata['issue'];
			//$class = $jd['z'];
			if ($class == 'issue'){
				$n = $jd['n'];
				$l = $jd['l'];
				$t = $jd['t'];
				$out ='';
				$del = '';
				if (!empty($n)){  $out .= $n; $del = ', ';}
				if (!empty($l)){  $out .= $del . $l;  $del = ', ';}
				if (!empty($t)){  $out .= $del . $t;  }
				return $out;
			}


			//$jd = json_encode($jdata['json']);
			//return $jd;
		}

		return null;
	}


	public function staff2jdata($key, $ob){

		if ($ob == null || $ob == ''){
			return null;
		}
		if (is_array($ob)){
			$data = array('json' => $ob);
			return $data;
		}

		$txt = trim($ob);

		if (StaffUtil::isJsonText($txt)){
				$jdata = json_decode($txt,true);
				if ($jdata != null){
					$data = array('json' => $jdata);
					return $data;
				}
		}

		//if ($key != 'marc:title-statement:format-formula' && StaffUtil::isMarcText($txt)){
		if (StaffUtil::isMarcText($txt)){
			$f = $this->getField($key);
			$marc_tag = $f['marc_tag'];
			$marc_mnemonic_str = MarcUtil::normalizeMarcMnemonic($marc_tag,null ,$txt);
			$tmp = MarcUtil::parseMarcMnemonic($marc_mnemonic_str);
			if (! empty($tmp) &&  isset($tmp[0])){
				$marc = $tmp[0]->fields[0];
				//$data = array('marc' => $marc);
				$marc_str = MarcUtil::marc2staff($marc);
				$data = array('marc' => $marc_str);
				return $data;
			}

			return null;
		}
	}



	public function stafTextValue2Patron($key,$value){
		$rep = trim($value);
		if ($key == DataFields::dc_subject){
			$rep = trim($rep,'>');
			// $rep = str_replace('{gt}', ')', $rep);
			// $rep = str_replace('{lt}', '(', $rep);
			$rep = preg_replace('/\s+/', ' ', $rep);
			$rep = trim($rep);
		}
		return $rep;
		//return StaffUtil::fromMnem($rep);
	}

// 	public function normalizeStaffValueArray($key,&$arrayOfValues){
// 		foreach ($arrayOfValues as $k=>$v){
// 			$arrayOfValues[$k] = $this->normalizeStaffValue($k, $v);
// 		}
// 	}

	public  function validate($key, $val){
		$f = $this->getField($key);
		$marc = $f['marc'];
		$label = $f['label'];
		if (empty($marc)){
			return array();
		}
		//ARRAY
		if (is_array($val)){
			$arrValues = $val;
			$rep = array();
			if (!empty($arrValues)){
				if (!empty($marc)){
					$subfield_data = $marc['subfields'];
					if (!empty($subfield_data)){
						foreach ($arrValues as $v){
							$str = $v[6];
							$errors = MarcUtil::validateMarcMnemonic($str, $subfield_data,$label);
							return $errors;
							//$rep = array_merge($rep,$errors);
						}
					}
				}
			}
			return $rep;
		}

		//STRING
		$str = $val;
		if (!empty($marc)){
			$subfield_data = $marc['subfields'];
			if (!empty($subfield_data)){
				return MarcUtil::validateMarcMnemonic($str, $subfield_data);
			}
		}
		return array();
	}



	// 	//converts json-value to text_vaule
	//  	public function arrayForPatron($key, $valueArr){
	//  		$marc = $this->getMarcData($key);
	//  		if (empty($marc)) return null;
	//  	}
	// // 	public function priForStaff($key){

	// // 	}


}

/**
 *
 */
class ItemValue {
/**
 * @var string
 */
	private $key;

/**
 * @var array
 */
	private $value;

	/**
	 *
	 * @param array $value
	 * @param string $key
	 */
   public function __construct($value = null,$key = null) {
   	if ($value == null || (is_Array($value) && count($value) == 0) ){
   		$value = array(null,null,null,null,null,null,null,null,null,null);
   	}
   	$this->key = $key;
	 	$this->value = $value;
	 }

	 /**
	  *
	  * @param string $value
	  * @param string $key
	  * @return ItemValue
	  */
	public static function c($value = null,$key = null){
		return new ItemValue($value,$key);
	}


	public static function cKeyTextValue($key = null,$value = null,$weight=null){
		$iv = new ItemValue(null,$key);
		$iv->setTextValue($value);
		$iv->setWeight($weight);
		return $iv;
	}


/**
 * @return string
 */
	public function key(){
		return $this->key;
	}

	/**
	 * @param string $key
	 */
	public function setKey($key){
		$this->key = $key;
	}

	/**
	 *
	 * @return array
	 */
	public function valueArray(){
		return $this->value;
	}

	/**
	 * @return string
	 */
	public function textValue(){
		if (!isset($this->value[0])){
			return null;
		}
		return $this->value[0];
	}

	public function setTextValue($value){
		$this->value[0] = $value;
	}

	/**
	 * @return string
	 */
	public function lang(){
		if (!isset($this->value[1])){
			return null;
		}
		return $this->value[1];
	}

	public function setLang($lang){
		$this->value[1] = $lang;
	}


	public function databaseId(){
		if (!isset($this->value[2])){
			return null;
		}
		return $this->value[2];
	}

	public function setDAtabaseId($id){
		$this->value[2] = $id;
	}


	public function relation(){
		if (!isset($this->value[3])){
			return null;
		}
		return $this->value[3];
	}

	public function setRelation($relation){
		$this->value[3] = $relation;
	}

	public function refItem(){
		if (!isset($this->value[4])){
			return null;
		}
		return $this->value[4];
	}

	public function setRefItem($refItem){
		$this->value[4] = $refItem;
	}


	/**
	 * @return array
	 */
	public function data(){
		if (!isset($this->value[5])){
			return null;
		}
		return $this->value[5];
	}


	public function setData($data){
		$this->value[5] = $data;
	}

	public function getDataValue($key = null, $defVal = array()){
		if (empty($key)){
			if (!isset($this->value[5]) || $this->value[5] == null){
				return array();
			}
			return $this->value[5];
		}
		$data = $this->getDataArray(); //TODO
		if (isset($data[$key])){
			return $data[$key];
		}
		return defVal;
	}

	public function setDataValue($key, $value){
		if (!isset($this->value[5])){
			$this->value[5] = array();
		}
		$this->value[5][$key] = $value;
	}


	public function getJData(){
		return $this->getDataValue('data',array());
	}
	public function setToJData($k,$v){
		if (!isset($this->value[5])){
			$this->value[5] = array();
		}
		if (!isset($this->value[5]['data'])){
			$this->value[5]['data'] = array();
		}

		$this->value[5]['data'][$k] = $v;
	}


	public function recordId(){
		if (!isset($this->value[6])){
			return null;
		}
		return $this->value[6];
	}

	public function setRecordId($recordId){
		$this->value[6] = $recordId;
	}


	public function weight(){
		if (!isset($this->value[7])){
			return null;
		}
		return $this->value[7];
	}

	public function setWeight($weight){
		$this->value[7] = $weight;
	}

	public function linkId(){
		if (!isset($this->value[8])){
			return null;
		}
		return $this->value[8];
	}

	public function setLink($link){
		$this->value[8] = $link;
	}



	public function inferred(){
		if (!isset($this->value[9])){
			return null;
		}
		return $this->value[9];
	}

	public function setInferred($isInferred){
		$this->value[9] = $isInferred;
	}


}



/**
 *
 * @author kostas
 *
 *
 * //  0: text value
 * //  1: lang
 * //  2: database id: dsd.metadatavalue2(metadata_value_id)
 * //  3: relation
 * //  4: ref_item
 * //  5: json_data
 * //  6: record_id
 * //  7: weight
 * //  8: link (pointer) diktis st parent record_id
 *
 *
 */
interface ItemMetadataAccess {

	/**
	 *
	 * //  SINGLE VALUE FIELDS:
	 * //  0: text value
	 * //  1: lang
	 * //  2: database id: dsd.metadatavalue2(metadata_value_id)
	 * //  3: relation
	 * //  4: ref_item
	 * //  5: json_data
	 * //  6: record_id
	 * //  7: weight
	 * //  8: link (pointer) diktis st parent record_id
	 *
	 * @param string $key
	 * @param number $index
	 * @return array
	 */
	public function getArrayValue($key, $index =  0);

	/**
	 *
	 * @param string $key
	 * @param number $index
	 * @return string
	 */
	public function getTextValue($key, $index =  0);
	/**
	 *
	 * @param string $key
	 * @param string $link
	 * @return multitype:array |multitype:
	 */
	public function getArrayValues($key, $link = null);
	/**
	 *
	 * @param string $key
	 * @param boolean $addKey
	 * @return multitype:|multitype:unknown
	 */
	public function getArrayValuesByKey($key, $addKey = false);

	/**
	 *
	 * @param string $key
	 * @param ItemValue $itemValue
	 */
	public function addItemValue($key, $itemValue);
	public function addItemValueNK($itemValue);

	/**
	 * @return ItemValue[]
	 */
	public function getItemValues($key, $link = null);

	/**
	 * @return ItemValue[]
	 */
	public function getItemValuesByKey($key);

	/**
	 * @return ItemValue
	 */
	public function getFirstItemValue($key, $link = null);
	public function getFirstItemValueOrEmpty($key, $link = null);

	/**
	 * @return ItemValue[]
	 */
	public function getEdgeItemValues();

	public function dump();
	public function dumpLaravelLog();

}








/**
 *
 * Array(
 *  [dc:subject:] => Array (
 *    [0] => ARRAY( [0] => 'subject1', [1] => 'el_GR', [2] =>, [3] => , [4] =>, [5] =>, [6] =>), //SINGLE VALUE
 *    [1] => ARRAY( [0] => 'subject2', [1] => 'el_GR', [2] =>, [3] => , [4] =>, [5] =>, [6] =>), //SINGLE VALUE
 *    [2] => ARRAY( [0] => 'subject3', [1] => 'el_GR', [2] =>, [3] => , [4] =>, [5] =>, [6] =>)  //SINGLE VALUE
 *  ),
 *  [dc:title:] => Array(...)
 * )
 *
 *
 * //$value, $lang, $vid,$relation,$ref_item, $jdata, $client_id, $weight, $link
 *  //SINGLE VALUE FIELDS:
 *   0: text value
 *   1: lang
 *   2: database id:    dsd.metadatavalue2(metadata_value_id)
 *   3: relation
 *   4: ref_item
 *   5: json_data
 *   6: record_id
 *   7: weight
 *   8: link (pointer) diktis st parent record_id
 *   9: inferred
 *  10: level
 *
 */
class ItemMetadata implements ItemMetadataAccess {
		//public $values_norm = ARRAY();
    public $values = ARRAY();

    private $marc_control;
    private $field_control;
    private $msg_info = array();
    private $msg_warn = array();
    private $msg_err  = array();

		private $_data = array();

    public function __construct($values = null, $msg_err=null,$msg_warn = null , $msg_info = null) {
			if (! empty($values)){
				$this->values = $values;
			}
			// if (! empty($values_norm)){
				// $this->values_norm = $values_norm;
			// }

			if (! empty($msg_err)){
				$this->msg_err = $msg_err;
			}
			if (! empty($msg_warn)){
				$this->msg_warn = $msg_warn;
			}
			if (! empty($msg_info)){
				$this->msg_info = $msg_info;
			}

			$this->marc_control = new MarcControl();
			$this->field_control = new FieldControl();

		}


		public function get($key){
			if (! isset($this->_data[$key])){
				return null;
			}
			return $this->_data[$key];
		}
		public function put($key,$value){
			$this->_data[$key] = $value;
		}
		public function data(){
			$this->_data;
		}


		/**
		 *
		 * @param  ItemMetadata $itemMetadata
		 * @return ItemMetadata
		 */
		public static function s($itemMetadata){
			return $itemMetadata;
		}

		/*
		 * @return boolean
		 */
		public function isEmpty(){
			return count($this->values) ==0;
		}

		/**
		 *
		 * //  SINGLE VALUE FIELDS:
		 * //  0: text value
		 * //  1: lang
		 * //  2: database id: dsd.metadatavalue2(metadata_value_id)
		 * //  3: relation
		 * //  4: ref_item
		 * //  5: json_data
		 * //  6: record_id
		 * //  7: weight
		 * //  8: link (pointer) diktis st parent record_id
		 * //  9: inferred
     * // 10: level
		 *
		 * @param string $key
		 * @param number $index
		 * @return array
		 */
		public function getArrayValue($key, $index =  0){
			if ($index >= 0){
				if (isset($this->values[$key])){
					if (isset($this->values[$key][$index])){
						return $this->values[$key][$index];
					}
				}

			}
			return null;
		}
		/**
		 *
		 * @param string $key
		 * @param number $index
		 * @return string
		 */
		public function getTextValue($key, $index =  0){
			if ($index >= 0){
				if (isset($this->values[$key])){
					if (isset($this->values[$key][$index])){
						if (isset($this->values[$key][$index][0])){
							return $this->values[$key][$index][0];
						}
					}
				}
			}
			return null;
		}

		/**
		 *
		 * @param string $key
		 * @param string $link
		 * @return multitype:array |multitype:
		 */
		public function getArrayValues($key, $link = null){
			if (isset($this->values[$key])){
				$rep = array();
				foreach ($this->values[$key] as $value){
					if ($value[8] == $link){
						$rep[] = $value;
					}
				}
				return $rep;
			}
			return array();
		}

		/**
		 *
		 * @param string $key
		 * @param boolean $addKey
		 * @return multitype:|multitype:unknown
		 */
		public function getArrayValuesByKey($key, $addKey = false){

// 			if (isset($this->values[$key])){
// 				return $this->values[$key];
// 			}
// 			return array();

			if (!isset($this->values[$key])){
				return array();
			}
			if (! $addKey){
				return $this->values[$key];
			}

			$rep = array();
			$kvalues = $this->values[$key];
			foreach ($kvalues as $k => $v) {
				$v['key'] = $key;
				$rep[] = $v;
			}
			return $rep;



		}




		/**
		 *
		 * @param string $key
		 * @param ItemValue $itemValue
		 */
		public function addItemValue($key, $itemValue) {
			//$itemValue->setKey($key);
			if (empty($key)){
				$this->values[$itemValue->key()][] = $itemValue->valueArray();
			}else{
				$this->values[$key][] = $itemValue->valueArray();
			}
		}

		/**
		 * @param ItemValue $itemValue
		 */

		public function addItemValueNK($itemValue) {
				$this->values[$itemValue->key()][] = $itemValue->valueArray();
		}

		/**
		 * @return ItemValue[]
		 */
		public function getItemValues($key, $link = null){
			if (isset($this->values[$key])){
				$rep = array();
				foreach ($this->values[$key] as $value){
					if ($value[8] == $link){
						$rep[] = new ItemValue($value,$key);
					}
				}
				return $rep;
			}
			return array();
		}


		/**
		 * @return ItemValue[]
		 */
		public function getEdgeItemValues(){
				$rep = array();
				foreach ($this->values as $element=>$arrval){
					foreach ($arrval as $value){
						//if (!empty($value[4]) && empty($value[8])){//  4: ref_item 8: link (pointer) diktis st parent record_id
						if (!empty($value[4]) ){//  4: ref_item 8: link (pointer) diktis st parent record_id
							$rep[] = new ItemValue($value,$element);
						}
					}
				}
				return $rep;
		}


		/**
		 * @return ItemValue[]
		 */
		public function getItemValuesByKey($key){
			if (isset($this->values[$key])){
				$rep = array();
				foreach ($this->values[$key] as $value){
					$rep[] = new ItemValue($value,$key);
				}
				return $rep;
			}
			return array();
		}

		/**
		 * @return ItemValue
		 */
		public function getFirstItemValue($key, $link = null){
			if (isset($this->values[$key])){
				foreach ($this->values[$key] as $value){
					if ($value[8] == $link){
						return new ItemValue($value,$key);
					}
				}
			}
			return null;
		}

		/**
		 * @return ItemValue
		 */
		public function getFirstItemValueOrEmpty($key, $link = null){
			if (isset($this->values[$key])){
				foreach ($this->values[$key] as $value){
					if ($value[8] == $link){
						return new ItemValue($value,$key);
					}
				}
			}
			return new ItemValue(null,$key);
		}












	public function createKey($prefix,$element,$qualifier = null){
		$key = $prefix . ":" . $element . ":" . $qualifier;
		return $key;
	}


	// /**
	 // *
	 // * @param String $key
	 // * @return array
	 // *
	 // */
	// public static function splitKey($key){
		// $k = null;
		// $g = null;
		// $pos = strrpos($key, "@");
		// if ($pos === false) {
			// $k = $key;
		// } else {
			// $k = substr($key,0,($pos));
			// $g = substr($key,$pos+1);
		// }
		// return array($k,$g);
	// }

	// public function normalizeKeys(){
		// $values = array();
		// foreach ($this->values as $key=>$v){
			// list($k,$g) = ItemMetadata::splitKey($key);
			// $vals = array();
			// foreach($v as $val){
				// $tmp = $val;
				// $tmp[7] = $g;
				// $vals[] = $tmp;
			// }
			// if (!isset($values[$key])){
				// $values[$key] = array();
			// }
			// $values[$k] = $vals;
//
		// }
		// return new ItemMetadata($values,$this->msg_err,$this->msg_warn,$this->msg_info);
//
	// }


	// public function getValuesMapForPrefix($prefix){
		// $rep = array();
		// foreach($this->values as $key => $value){
			// $pos = stripos($key, $prefix);
			// if ($pos !== false && $pos == 0){
				// $rep = array_merge($rep, array($key=>$this->values[$key]));
			// }
		// }
		// return $rep;
	// }


	public function getNextClientId(){
		$max = 1;
		foreach ($this->values as $key => $kvalues) {
			foreach ($kvalues as $k => $v) {
				if (! empty($v[6]) && $v[6] > $max){
					$max = $v[6];
				}
			}
		}
		return ($max+1);
	}



	private function getTreesFromRootValues($root_values){

		$f1 = function($idata, $root,&$t) use (&$f1){
			//	echo ("F1: " . $root[0] . "\n");
			$t[] = $root;
			$link_id = $root[6];
			$values = $idata->getValuesByLink($link_id);
			foreach($values as $v){
				$f1($idata, $v,$t);
			}
		};
		$trees = array();
		foreach($root_values as $v){
			$t = array();
			$f1($this,$v,$t);
			$trees[] = $t;
		};
		return $trees;
	}

	public function getTreesByKeyLink($key,$link_id){
		$root_values = $this->getValuesByKeyLink($key,$link_id,true);
		return $this->getTreesFromRootValues($root_values);
	}
	public function getTreesByKey($key){
		$root_values = $this->getValuesByKey($key,true);
		return $this->getTreesFromRootValues($root_values);
	}


	public function getNextRW(){
		$max1 = 1;
		$max2 = 1;
		foreach ($this->values as $key => $kvalues) {
			foreach ($kvalues as $k => $v) {
				if (! empty($v[6]) && $v[6] > $max1){
					$max1 = $v[6];
				}
				if (! empty($v[7]) && $v[7] > $max2){
					$max2 = $v[7];
				}

			}
		}
		return array(($max1+1),($max2+1));
	}

/**
 * arguments:  KEY_PREFIX1, KEY_PREFIX2 ...
 *@deprecated
 *
 */
	public function getValuesByKeyPrefix(){
		$numargs = func_num_args();
		$arg_list = func_get_args();
		$rep = array();
		foreach ($this->values as $key => $kvalues) {
		  for ($i = 0; $i < $numargs; $i++) {
				if (PUtil::strBeginsWith($key, $arg_list[$i])){
					foreach ($kvalues as $k => $v) {
						$v['key'] = $key;
						$rep[] = $v;
					}
				}
			}
		}
		return $rep;
	}

	/**
	 * @deprecated  DES getArrayValuesByKey
	 * @param unknown $key
	 * @param string $addKey
	 * @return multitype:|multitype:unknown
	 */
	public function getValuesByKey($key,$addKey = false){
		$rep = array();
		if (!isset($this->values[$key])){
			return array();
		}
		$kvalues = $this->values[$key];
		if (! $addKey){
			return $kvalues;
		}
		foreach ($kvalues as $k => $v) {
			$v['key'] = $key;
			$rep[] = $v;
		}
		return $rep;
	}





	/**
	 * @deprecated
	 * @param unknown $link_id
	 * @return multitype:unknown
	 */
	public function getValuesByLink($link_id){
		$rep = array();
		foreach ($this->values as $key => $kvalues) {
			foreach ($kvalues as $k => $v) {
				if ($v[8] == $link_id){
					$v['key'] = $key;
					$rep[] = $v;
				}
			}
		}
		return $rep;
	}

/**
 * @deprecated
 * @param unknown $key
 * @param unknown $link_id
 * @param string $addKey
 * @return multitype:Ambigous <unknown, multitype:, multitype:unknown, multitype:, multitype:unknown >
 */
	public function getValuesByKeyLink($key, $link_id,$addKey=false){
		$rep = array();
		$kvalues = $this->getValuesByKey($key,false);
		foreach ($kvalues as $k => $v) {
			if ($v[8] == $link_id){
				if ($addKey){
					$v['key'] = $key;
				}
				$rep[] = $v;
			}
		}
		return $rep;
	}

	/**
	 * @deprecated
	 * @param unknown $key
	 * @param unknown $link_id
	 * @param string $addKey
	 * @return Ambigous <unknown, multitype:, multitype:unknown, multitype:, multitype:unknown >
	 */
	public function getFirstValueByKeyLink($key, $link_id,$addKey=false){
		$kvalues = $this->getValuesByKey($key,false);
		foreach ($kvalues as $k => $v) {
			if ($v[8] == $link_id){
				if ($addKey){
					$v['key'] = $key;
				}
				return $v;
			}
		}
	}


/**
 * @deprecated
 * @param unknown $cid
 * @return unknown|NULL
 */
	public function getValuebyClientId($cid){
		foreach ($this->values as $key => $kvalues) {
			foreach ($kvalues as $k => $v) {
				if ($v[6] == $cid){
					$v['key'] = $key;
					return $v;
				}
			}
		}
		return null;
	}


	public function updateRefItem($cid,$ref_item){
		//echo("updateRefItem: " + $cid + " , " + $ref_item);
		foreach ($this->values as $key => $kvalues) {
			foreach ($kvalues as $k => $v) {
				if ($v[6] == $cid){
					$old = $v[4];
					$this->values[$key][$k][4] =$ref_item;
					return $old;
				}
			}
		}
		return null;
	}



	public function deleteByClientId($cid){
		foreach ($this->values as $key => $kvalues) {
			foreach ($kvalues as $idx => $v) {
				if ($v[6] == $cid){
					$old = $v;
					unset($this->values[$key][$idx]);
					return $old;
				}
			}
		}

		return null;
	}


	public function deleteByKey($key){
		unset($this->values[$key]);
	}




	 //  SINGLE VALUE FIELDS:
	 //  0: text value
	 //  1: lang
	 //  2: database id: dsd.metadatavalue2(metadata_value_id)
	 //  3: relation //DEPRECATED
	 //  4: ref_item
	 //  5: json_data
	 //  6: record_id
	 //  7: weight
	 //  8: link (pointer) diktis st parent record_id
	 //  9: inferred
	#
	# kanei append ena value se ena key
	#
	# IMPORTAND METHOD
	public function addValueSK($key, $value=null, $lang=null, $vid=null, $relation=null, $ref_item=null, $jdata=null, $record_id=null,$weight=null,$link=null,$inferred =false) {
		//Log::info("addValueSK: " . $key . " : " . $inferred);
		 $this->values[$key][] = ARRAY($value, $lang, $vid,$relation,$ref_item, $jdata, $record_id, $weight, $link, $inferred);
	}

	// kanei prwta delete to key kai meta append ena value se ena key
	public function addValueSK_DBK($key, $value=null, $lang=null, $vid=null, $relation=null, $ref_item=null, $jdata=null, $record_id=null,$weight=null,$link=null,$inferred =false) {
		$this->deleteByKey($key);
		$this->addValueSK($key, $value, $lang, $vid, $relation, $ref_item, $jdata, $record_id, $weight, $link, $inferred);
	}


	// #
	// # kanei append ena value se ena key
	// public function addStaffValueSK($key, $staff=null) {
		// $v = $this->field_control->normalizeStaffValue($key, $staff);
		// $value = $staff;
		// $staff = null;
		// $lang = "el_GR";
		// $vid = null;
		// $relation = null;
		// $ref_item =  null;
		// $jdata = null;
		// if (StaffUtil::isStaffOnlyText($v)){
			// $jdata = $this->field_control->staff2jdata($key, $v);
			// if (!empty($jdata)){
				// $staff = $this->field_control->jdata2staff($key, $jdata);
				// $value = $this->field_control->jdata2Patron($key, $jdata);
			// }
		// }
		// $this->addValueSK($key, $value, $lang, $vid, $relation, $ref_item,$jdata, $staff,null,null);
	// }


	public function resetSK($key){
		$this->values[$key] = ARRAY();
	}

	#
	# vazi se ena key to array me tis times me default dedomena
	#
	public function setValueSK($key, $valueORarrayOfValues) {
		Log::info('setValueSK');
		$old_values = $this->getValueArraySK($key);
		$this->resetSK($key);

		if (empty($valueORarrayOfValues)){
			$this->addValueSK($key, null,null,null,null,null,null);
			return;
		}


		$values = is_array($valueORarrayOfValues)? $valueORarrayOfValues : array($valueORarrayOfValues);
		foreach ($values as $k => $v) {
			$lang = null;
			$relation = null;
			$ref_item = null;
			$jdata = null;
			$weight = null;
			$link = null;
			$client_id = null;
			$inferred = false;
			if (! empty($old_values)){
				foreach ($old_values as $ov) {
					if ($ov[0] == $v){
						$lang = $ov[1];
						$relation = $ov[3];
						$ref_item = $ov[4];
						$jdata = isset($ov[5]) ? $ov[5]:null;
						$client_id = isset($ov[6]) ? $ov[6]:null;
						$weight = isset($ov[7]) ? $ov[7]:null;
						$link = isset($ov[8]) ? $ov[8]:null;
						$inferred = isset($ov[9]) ? $ov[9]:null;
						break;
					}
				}
			}
			$this->addValueSK($key, $v,$lang,null,$relation,$ref_item,$jdata,$client_id,$weight,$link,$inferred);
		}
	}




/**
 *
 * IMPORTAND
 * @param unknown $key
 * @param unknown $valueORarrayOfValues
 *
 */
	// public function setStaffValueSK($key, $valueORarrayOfValues) {
		// echo("<pre>");
		// print_r($key);
		// print_r($valueORarrayOfValues);
//
		// $g = null;
//
		// //list($k,$g) = ItemMetadata::splitKey($key);
//
		// $old_values = $this->getValueArraySK($key);
		// $this->resetSK($key);
//
		// if (empty($valueORarrayOfValues)){
			// $this->addValueSK($key, null,null,null,null,null,null);
			// return;
		// }
//
		// $values = is_array($valueORarrayOfValues)? $valueORarrayOfValues : array($valueORarrayOfValues);
		// foreach ($values as $k => $v) {
			// $value = $this->field_control->normalizeStaffValue($key, $v);
			// $staff = null;
			// $lang = 'el_GR';
			// $jdata = null;
			// $index = 0;
			// echo("V:\n");
			// var_dump($value);
			// if (StaffUtil::isStaffOnlyText($v)){
				// echo("STAFF-ONLY1: $v\n");
				// $index = 6;
				// $jdata = $this->field_control->staff2jdata($key, $v);
				// echo("STAFF-ONLY2: \n");
				// print_r($jdata);
				// if (!empty($jdata)){
					// $staff = $this->field_control->jdata2staff($key, $jdata);
					// $value = $this->field_control->jdata2Patron($key, $jdata);
				// }
			// }
			// $relation = null;
			// $ref_item = null;
			// if (! empty($old_values)){
				// echo("OLD VALUES\n");
				// print_r($old_values);
				// foreach ($old_values as $ov) {
					// echo("#1 $index\n");
					// echo("#2 " . $ov[$index] ."\n");
					// echo("#3 $v\n");
					// if ($ov[$index] == $v){
							// $relation = $ov[3];
							// $ref_item = $ov[4];
							// break;
					// }
				// }
			// }
			// echo("#1 ADDValUE $key");
			// print_r($value);
			// echo("#2");
			// print_r($staff);
//
			// $this->addValueSK($key, $value,$lang,null,$relation,$ref_item,$jdata,$staff,null,$g);
		// }
		// echo("</pre>");
	// }






/**
 * (xrisimopoiite apo to data-entry
 * ola ta dedomena gia to klidi dinonte mazi
 * )
 *
 *
 * singleValue:  map:
 *  i  ID
 *  l  link (tree anchestor pointer)
 *  v  value
 *  g  language
 *  r  relation
 *  f  ref-item
 *  w   weight
 *
 * IMPORTAND
 * @param unknown $key
 * @param unknown $valueORarrayOfValues
 *
 */
	public function replaceValuesFromClient($key, $arrayOfValues) {
		//if ($key == 'ea:subj:general'){
		//	Log::info("replaceValuesFromClient " . $key . " :: " . print_r($arrayOfValues,true));
		//}
		if (substr( $key, 0, 4 ) === 'trn:' || substr( $key, 0, 4 ) === 'tmp:' || substr( $key, 0, 8 ) === 'reverse:'){
			return;
		}
		if (! PUtil::strContains($key, ':')){
			error_log("WRONG KEY: "+$key);
			return;
		}
		$old_values = $this->getValueArraySK($key);

// 		echo("<pre>");
// 		echo("## $key #\n");
// 		print_r($arrayOfValues);
// 		echo("##\n");
// 		print_r($old_values);
// 		echo("</pre>");


		$this->resetSK($key);

    //            1   2      3     4     5        6          7         8       9       10
    //addValueSK(key, value, lang, vid, relation, ref_item, jdata , client_id ,weight, link) {
		if (empty($arrayOfValues)){
			$this->addValueSK($key, null,null,null,null,null,null);
			return;
		}
		foreach ($arrayOfValues as $k => $str) {
			$v = is_array($str) ? $str : json_decode($str,true);
			if (! isset($v['s'])){$v['s'] = null;};
			if (! isset($v['f'])){$v['f'] = null;};
			if (! isset($v['l'])){$v['l'] = null;};
			if (! isset($v['w'])){$v['w'] = null;};

			$inferred = false;
			if (isset($v['e']) && ! empty($v['e'])){
				$inferred =  $v['e'];
			} else {
				if (isset($old_values[$k]) && isset($old_values[$k][9])){
					$inferred = $old_values[$k][9];
				}
			}


			//print_r($v);
			$tmp = $v['v']; // MPOrEI NA EINA ARRAY (json)

			$jdata = null;
			$value = null;
			//if (  StaffUtil::isStaffOnly($tmp)){
			if ($key != 'marc:title-statement:format-formula' && StaffUtil::isStaffOnly($tmp)){
				//echo("STAFF ONLY");
				$jdata = $this->field_control->staff2jdata($key, $tmp);
				$value = $this->field_control->jdata2Patron($key, $jdata);
			} else {
				$props = isset($v['p']) ? $v['p'] : null;
				if (!empty($props)){
					$jdata = array('prps'=>$props);
				}
				$value = $this->field_control->stafTextValue2Patron($key, $tmp);
			}

			$data = isset($v['d']) ? $v['d'] : null;
			if (! empty($data)){
				if (empty($data)){$jdata = array();}
				$jdata['data'] = $data;
			}

			if ($value != null){
			//	printf('ADD: %s : %s : %s : %s ',$v['i'], $v['l'], $key, $value );echo("\n");
				//$this->addValueSK($key, $value,$v['g'],null,$v['r'],$v['f'],$jdata,$v['i'],$v['w'],$v['l']);
				$this->addValueSK($key, $value,$v['g'],$v['s'],null,$v['f'],$jdata,$v['i'],$v['w'],$v['l'],$inferred);
			}
		}

		//echo("</pre>");
	}




	// 			[i] => i13
	// 			[k] => tmp:sect_status
	// 			[v] => open
	// 			[l] =>
	// 			[g] =>
	// 			[f] =>
	// 			[w] => 38
	// 			[s] =>
	// 			[p] =>
	// 			[ks] =>
	// 			[d] =>
	public function replaceValuesFromClientModels($arrayOfModelsValues) {
		//Log::info('replaceValuesFromClientModels');
		//Log::info(print_r($arrayOfModelsValues,true));
		$map = array();
		foreach($arrayOfModelsValues as $m){
			$key = $m['k'];
			if (is_string($key)){
				if (! isset($map[$key])){
					$map[$key] = array();
				}
				$map[$key][] = array('i'=>$m['i'], 'l'=>$m['l'], 'v'=>$m['v'], 'g'=>$m['g'], 'r'=>null, 'f'=>$m['f'], 'w'=>$m['w'], 'p'=>$m['p'], 'd'=>$m['d'] );
			} else {
				Log::info("??? " . print_r($m,true));
			}
		}

		foreach($map as $k => $v){
			$this->replaceValuesFromClient($k, $v);
		}
	}



	private $staff_weight = 0;
	private $staff_record_id = 0;
	public function setStafRecordId($sri){
		$this->staff_record_id = $sri;
	}
/*
/**
 * MAP KEYS:
 *    value       :(text value)
 *    props       :props (jdata)
 *    lang
 *    vid        :database id:    dsd.metadatavalue2(metadata_value_id)
 *    relation
 *    ref_item
 *    record_id
 *    weight
 *    link      : (pointer) diktis st parent record_id
 */
	public function addStaffValueSK($key,$staff_value,$value_map=null) {
		Log:info("addStaffValueSK");
		if (empty($staff_value)){return null;}
		if ($value_map== null){ $value_map = array();}
		$record_id = null;
		if (isset($value_map['record_id'])){
			$record_id = $value_map['record_id'];
			if ($this->staff_record_id < $record_id){
				$this->staff_record_id = $record_id + 1;
			}
		} else {
			$this->staff_record_id++;
			$record_id = $this->staff_record_id;
		}
		$weight = null;
		if (isset($value_map['weight'])){
			$weight = $value_map['weight'];
		} else {
			$this->staff_weight++;
			$weight = $this->staff_record_id;
		}
		$props = isset($value_map['props']) ? $value_map['props'] : null;
		$lang = isset($value_map['lang']) ? $value_map['lang'] : null;
		$vid = isset($value_map['vid']) ? $value_map['vid'] : null;
		$relation  = isset($value_map['relation']) ? $value_map['relation'] : null;
		$ref_item  = isset($value_map['ref_item']) ? $value_map['ref_item'] : null;
		$link  = isset($value_map['link']) ? $value_map['link'] : null;
		$inferred  = isset($value_map['inferred']) ? $value_map['inferred'] : false;

			$tmp = $staff_value;
			$jdata = null;
			$value = null;
			if ($key != 'marc:title-statement:format-formula' && StaffUtil::isStaffOnly($tmp)){
				//echo("##DEBUG#1#STAFF ONLY: $tmp\n");
				$jdata = $this->field_control->staff2jdata($key, $tmp);
				$value = $this->field_control->jdata2Patron($key, $jdata);
			} else {
				if (!empty($props)){
					$jdata = array('prps'=>$props);
				}
				$value = $this->field_control->stafTextValue2Patron($key, $tmp);
			}

			//$value = str_replace("\n", "¶", $value);
			if ($value != null){
				//$key, $value=null, $lang='el_GR', $vid=null, $relation=null, $ref_item=null, $jdata=null, $record_id=null,$weight=null,$link=null
				 $this->addValueSK($key, $value,$lang,$vid,$relation,$ref_item,$jdata,$record_id,$weight,$link,$inferred);
			}
			return $record_id;
	}

















/**
 *staff : recordId
 *grp: link to staff (recortdId)
 *
 */
// 	public function addValueFromDBText($prefix,$element,$qualifier, $value, $lang, $vid, $relation=null, $ref_item=null, $data=null,$staff=null,$weight=null,$grp = null, $inferred = false) {
// 		$key = $this->createKey($prefix,$element,$qualifier);
// 		$jdata = json_decode($data,true);
// 		//if (!empty($jdata)){if (isset($jdata['prps'])){}}
// 		$this->addValueSK($key, $value, $lang, $vid, $relation, $ref_item,$jdata,$staff,$weight,$grp,$inferred);
// 	}

	public function addValueFromDBTextSK($key, $value, $lang, $vid, $relation=null, $ref_item=null, $data=null,$staff=null,$weight=null,$grp = null,$inferred = false) {
		$jdata = json_decode($data,true);
		$this->addValueSK($key, $value, $lang, $vid, $relation, $ref_item,$jdata,$staff,$weight,$grp,$inferred);
	}


	public function getObjectType(){
		return $this->getValueTextSK(DataFields::ea_obj_type);
	}
/**
 *
 * @deprecated
 * //SINGLE VALUE FIELDS:
 *   0: text value
 *   1: lang
 *   2: database id:    dsd.metadatavalue2(metadata_value_id)
 *   3: relation
 *   4: ref_item
 *   5: json_data
 *   6: record_id
 *   7: weight
 *   8: link (pointer) diktis st parent record_id
 *
 *  @return array
 */
	public function getValueSK($key, $index =  0){
		if ($index >= 0){
			if (isset($this->values[$key])){
				if (isset($this->values[$key][$index])){
					return $this->values[$key][$index];
				}
			}
			// if (isset($this->values_norm[$key])){
				// if (isset($this->values_norm[$key][$index])){
					// return $this->values_norm[$key][$index];
				// }
			// }

		}
		return null;
	}

/**
 *  @deprecated
 */
		public function getValueTextSK($key, $index =  0){
			if ($index >= 0){
				if (isset($this->values[$key])){
					if (isset($this->values[$key][$index])){
						if (isset($this->values[$key][$index][0])){
							return $this->values[$key][$index][0];
						}
					}
				}
				// if (isset($this->values_norm[$key])){
					// if (isset($this->values_norm[$key][$index])){
						// if (isset($this->values_norm[$key][$index][0])){
							// return $this->values_norm[$key][$index][0];
						// }
					// }
				// }

			}
			return null;
		}


/**
 * @deprecated
 */
	public function getValue($prefix ,$element, $qualifier = null, $index =  0){
		$key = $this->createKey($prefix,$element,$qualifier);
		return $this->getValueSK($key,$index);
	}

/**
 * @deprecated
 */
	public function getValueText($prefix ,$element, $qualifier = null, $index =  0){
		$key = $this->createKey($prefix,$element,$qualifier);
		return $this->getValueTextSK($key,$index);
	}

/*
	* example:
	* Array (
	*  [0] => ARRAY( [0] => 'subject1', [1] => 'el_GR', [2] =>, [3] => , [4] =>, [5] =>, [6]=>),
	*  [1] => ARRAY( [0] => 'subject2', [1] => 'el_GR', [2] =>, [3] => , [4] =>, [5] =>, [6]=>),
	*  [2] => ARRAY( [0] => 'subject3', [1] => 'el_GR', [2] =>, [3] => , [4] =>, [5] =>, [6]=>)
	*  )
	*
	*  ARRAY OF ARRAY:
	*   0: text value
	*   1: lang
	*   2: database id:    dsd.metadatavalue2.metadata_value_id
	*   3: relation
	*   4: ref_item
	*   5: json_data
	*   6: client id
	*   7: weight
  *   8: link
  *
  *  #IMPORTAND
	*/
	public function getValueArraySK($key){
		if (isset($this->values[$key])){
			return $this->values[$key];
		}

		// if (isset($this->values_norm[$key])){
			// return $this->values_norm[$key];
		// }

		return null;
	}



/**
 * epistrefei oles tis staff times gia to klidi $key
 * se morfi array(map())
 *
 * single value: map(i=, v=, ...);
 * (1) i  id client
 * (2) v  VALUE-FOR-CLIENT   (JSON OR TEXT(simple or marc or html) )
 * (3) g  lang
 * (4) r  relation//DEPRACTED
 * (5) f  ref item
 * (6) w  weight
 * (7) l  link (tree anchestor pointer)
 *     p  props
 *     s  server id
 *     e  inferred
 * IMPORTAND
 */
	public function getClientValues($key){
		$my_arr = array();
		if (isset($this->values[$key])){
			$my_arr = $this->values[$key];
			$rep = array();
			foreach ($my_arr as $rv){
				$jd = isset($rv[5])? $rv[5]:null;//$rv[5];
				$v = null;
				if (!empty($jd) && isset($jd['json'])){
					$v = $jd['json'];
				}
				if (!empty($jd) && isset($jd['marc'])){
					$v = $jd['marc'];
				}

				if ($v == null){
					$v = ClientJsonValueUtil::patronValue2JsonClientValue($key, $rv[0]);
				}

				$p = null;
				if (!empty($jd) && isset($jd['prps'])){
					$p = $jd['prps'];
				}
				$d = null;
				if (!empty($jd) && isset($jd['data'])){
					$d = $jd['data'];
				}

				$w = isset($rv[7])? $rv[7]:null;
				$l = isset($rv[8])? $rv[8]:null;
				$g = isset($rv[1])? $rv[1]:null;
				$r = isset($rv[3])? $rv[3]:null;
				$f = isset($rv[4])? $rv[4]:null;
				$i = isset($rv[6])? $rv[6]:null;
				$s = isset($rv[2])? $rv[2]:null;
				$e = isset($rv[9])? $rv[9]:null;

					// echo("<pre>\n");
					// print_r($rv);
					// echo("</pre>\n");
//JAVASRIPT
  // i : id;
  // k : key;
  // v : value;
  // l : link;
  // g : lang
  // r : relation
  // f : refitem
  // w : weight
  // s : sid
  // p : props

//DB *  //SINGLE VALUE FIELDS:
 // *   0: text value
 // *   1: lang
 // *   2: database id:    dsd.metadatavalue2(metadata_value_id)
 // *   3: relation
 // *   4: ref_item
 // *   5: json_data
 // *   6: record_id
 // *   7: weight
 // *   8: link (pointer) diktis st parent record_id

				//echo("#2 $i >  $v   #\n");
				$rep[] = array('i'=>$i,'l'=>$l, 'v'=>$v,'w'=>$w, 'g'=>$g, 'r'=>$r, 'f'=>$f, 's'=>$s, 'p'=>$p, 'd'=>$d, 'e'=>$e );
				//Log::info("##" . print_r($rep,true));
			}

			return $rep;
		}

		return array(null);
	}


// /**
 // * epistrefei oles tis staff times gia to klidi $key
 // * se morfi text
 // * array(txt)
 // *
 // */
	// public function getStaffTextValueArraySK($key){
		// $my_arr = array();
		// if (isset($this->values[$key])){
			// $my_arr = $this->values[$key];
			// $rep = array();
			// foreach ($my_arr as $value){
				// $v = $value[6];
				// if (empty($v)){$v = $value[0];}
				// $rep[] = $v;
			// }
			// return $rep;
		// }
//
		// return array(null);
	// }

/**
 * @deprecated
 * @param unknown $key
 * @return multitype:unknown |multitype:NULL
 */
	public function getTextValueArraySK($key){
		$my_arr = array();
		if (isset($this->values[$key])){
			$my_arr = $this->values[$key];
			$rep = array();
			foreach ($my_arr as $value){
				$v = $value[0];
				//if (empty($v)){$v = $value[0];}
				$rep[] = $v;
			}
			return $rep;
		}

		return array(null);
	}


	public function hasKey($key){
		return isset($this->values[$key]);
	}
	/**
	 * @deprecated
	 */
	public function getPatronTextValue($key){
		$pv = $this->getPatronValue($key);
		return $pv == null ? null : $pv->textValue();
	}

/**
 * @deprecated
 * get value for patron
 *
 * @param string
 * @return ItemValue
 *
 */
	public function getPatronValue($key){
		if (!isset($this->values[$key])){
			return null;
		}
		$tmp = $this->values[$key];
		if (!isset($tmp[0])){
			return null;
		}
		return new ItemValue($tmp[0],$key);
	}





	/**
	 * @deprecated
	 * @param unknown $key
	 * @return multitype:|multitype:unknown
	 */
	public function getPatronTextValues($key){
		if (!isset($this->values[$key])){
			return array();
		}
			// echo("<pre>");
			// print_r($this->values[$key]);
			// echo("</pre>");
		$rep = array();
		foreach ($this->values[$key] as $value){
			$rep[] = $value[0];
// 				$pv = new ItemValue($value,$key);
// 				$rep[] = $pv->textValue();
		}
		return $rep;

	}




	/**
	 *  @deprecated
	 */
	public function getValueArr($prefix ,$element, $qualifier = null){
		$key = $this->createKey($prefix,$element,$qualifier);
		return $this->getValueArrSK($key);
	}


	public function getKeys(){
		return array_keys ($this->values);
	}

/**
 *
 * @param ARRRAY $arr
 * @return ItemMetadata
 */
	public  static function fromArray($arr){
		$rep = new ItemMetadata($arr);
		return $rep;
	}
	/**
	 * @return string
	 */
	public  function toJson(){
		return json_encode($this->values);
	}

	/**
	 * @param unknown $json_str
	 * @return ItemMetadata
	 */
	public static function fromJson($json_str){
		$arr = json_decode($json_str,true);
		$ob = self::fromArray($arr);
		return $ob;
	}




	private function addError($msg){
		$this->msg_err[] = $msg;
	}
	private function addInfo($msg){
		$this->msg_info[] = $msg;
	}
	private function addWarn($msg){
		$this->msg_warn[] = $msg;
	}
	private function clearMessages(){
		$this->msg_err = array();
		$this->msg_info = array();
		$this->msg_warn = array();
	}



	public function hasErrors(){
		return (!empty($this->msg_err));
	}

	public function hasMessages(){
// 		if (!empty($this->msg_err) return true;
// 		if (!empty($this->msg_warn) return true;
// 		if (!empty($this->msg_info) return true;
// 		return false;
		return ( (!empty($this->msg_err)) || (!empty($this->msg_info)) || (!empty($this->msg_warn))  );
	}

	public function getErrors(){
		return $this->msg_err;
	}

	public function getInfos(){
		return $this->msg_info;
	}

	public function getWarnings(){
		return $this->msg_warn;
	}

	// public function populate(){
			// $this->validate();
			// if ($this->hasErrors()){
				// return;
			// }
//
		// $data_change_flag = false;
		// //echo("<pre>");
		// $messages = array();
		// $cbuf = array();
		// $tags = array();
		// $valar = $this->getValueArraySK(DataFields::dc_subject);
		// if (!empty($valar)){
			// foreach ($valar as $i => $tmp){
				// $val = $tmp[0];
				// if (empty($val)){
					// continue;
				// }
				// $val = trim($val);
				// $needle = strpos($val,'>');
				// if ($needle==1) {
					// $val = substr($val,1);
				// }
				// if ($needle>1) {
					// $msg='';
					// $delimiter = '';
					// $vtags = explode(">", $val);
					// $c = 0;
					// foreach ($vtags as $tag){
						// $tag =trim($tag);
						// $check = $tag;
						// if (!array_key_exists($check,$cbuf)){
							// $c +=1;
							// $cbuf[$check]=null;
							// $msg .= $delimiter . $tag;
							// $delimiter = ', ';
							// $tags[] = $tag;
						// }
					// }
					// if ($c>0){
						// $msg = "exapand subject ($val) to " . $msg;
						// $messages[] = $msg;
					// } else {
						// $this->addError("subject ($val) apears many times");
					// }
				// }
			// }
		// }
//
//
		// $ok = true;
		// $cbuf = array();
		// foreach ($tags as $tag){
			// $check = strtolower($tag);
			// if (array_key_exists($check,$cbuf)){
				// $this->addError("subject: $tag exists in multiple forms");
				// $ok = false;
			// } else {
				// $cbuf[$check]=null;
			// }
		// }
		// if ($ok){
			// $old_tags =$this->getTextValueArraySK(DataFields::dc_subject);
			// $tags_ok = array();
			// foreach ($tags as $tag){
				// if (array_search($tag,$old_tags) === false){
					// $tags_ok[] = $tag;
				// }
			// }
//
			// if (!empty($tags_ok)){
				// foreach ($messages as $msg){
					// $this->addInfo($msg);
				// }
			// }
			// foreach ($tags_ok as $tag){
				// $data_change_flag = true;
				// $this->addStaffValueSK(DataFields::dc_subject,$tag);
			// }
//
			// //$ttt =$this->getTextValueArraySK(DataFields::dc_subject);
//
		// }
		// //echo("</pre>");
		// if($data_change_flag){
			// $this->priv_validate(false);
		// }
		// return $data_change_flag;
	// }


	private function validateMarcField($key){
		$rep = true;
		$msgs = $this->field_control->validate($key,$this->getValueArraySK($key));
		if (empty($msgs)) return;
		foreach ($msgs as $msg){
			$severity = $msg[1];
			if ($severity == 'error' || $severity == 'err'){
				$rep = false;
				$this->addError($msg[0]);

			}elseif  ($severity == 'warning' || $severity == 'warn'){
				$this->addWarn($msg[0]);
			}else {
				$this->addInfo($msg[0]);
			}
		}
		return $rep;
	}


	private function validate_artifact(){

		$artifact_of = $this->getValueTextSK('ea:artifact-of:');
		if ( PUtil::isEmpty($artifact_of)  || !PUtil::chk_int($artifact_of)){
			$this->addError('artifact-of error');
		}

		$dbh = dbconnect();
		$sn = null;
		$sn1 = $this->getValueTextSK(DataFields::ea_sn_prefix);
		$sn1 = strtoupper($sn1);
		$sn2 = $this->getValueTextSK(DataFields::ea_sn_suffix);
		if ( !PUtil::isEmpty($sn2)  && !PUtil::chk_int($sn2)){
			$this->addError('sn suffix must be integer');
		} else {
			$sn2 = PUtil::extract_int($sn2);
			if (PUtil::isEmpty($sn2)){
				if (PUtil::isEmpty($sn1)){
					$SQL = 'SELECT coalesce(max(sn_suff),0)+1 FROM dsd.artifacts WHERE sn_pref is null';
					$stmt = $dbh->prepare($SQL);
				} else {
					$SQL = 'SELECT coalesce(max(sn_suff),0)+1 FROM dsd.artifacts WHERE sn_pref = ?';
					$stmt = $dbh->prepare($SQL);
					$stmt->bindParam(1,$sn1);
				}
				$stmt->execute();
				$r = $stmt->fetch();
				$sn2 = $r[0];
				if (! PUtil::isEmpty($sn2)){
					$this->setValueSK(DataFields::ea_sn_suffix,$sn2);
				}
			}
		}

		if (PUtil::isEmpty($sn2)){
			$errors[] = array("SN REQUIRED",'error');
		} else {
			$key = DataFields::ea_sn;
			if (! PUtil::isEmpty($sn1)){
				$sn = Putil::coalesceConcatWithSeperator(' ',$sn1,$sn2);
			} else {
				$sn = $sn2;
			}
			$this->setValueSK($key,$sn);
		}

		$obj_type = $this->getValueTextSK(DataFields::ea_obj_type);
		if($obj_type == 'artifact'){
			$cnflag = true;
			if (PUtil::isEmpty($this->getValueTextSK(DataFields::ea_call_number_prefix))){
				$this->addError('call number prefix missing');
				$cnflag = false;
			}
			$tmp = $this->getValueTextSK(DataFields::ea_call_number_main);
			if (!PUtil::isEmpty($tmp) && !PUtil::chk_int($tmp)){
				$this->addError('call number midle component must be integer');
				 $cnflag = false;
			 }

			if ($cnflag){
				$call_number = null;
				$cn1 = $this->getValueTextSK(DataFields::ea_call_number_prefix);
				$cn2 = PUtil::extract_int($this->getValueTextSK(DataFields::ea_call_number_main));
				$cn3 = $this->getValueTextSK(DataFields::ea_call_number_suffix);

				if (PUtil::isEmpty($cn2) && ! PUtil::isEmpty($cn1)){
					$SQL = 'SELECT coalesce(max(call_number_sn),0)+1 FROM dsd.artifacts WHERE call_number_pref =?';
					$stmt = $dbh->prepare($SQL);
					$stmt->bindParam(1,$cn1);
					$stmt->execute();
					$r = $stmt->fetch();
					$cn2 = $r[0];
					if (! PUtil::isEmpty($cn2)){
						$this->setValueSK(DataFields::ea_call_number_main,$cn2);
					}

				}

				if (!PUtil::isEmpty($cn1)){
					$call_number = Putil::coalesceConcatWithSeperator(' ' ,$cn1,$cn2,$cn3);
					$key = DataFields::ea_call_number_ea;
					$this->setValueSK($key,$call_number);
				} else {
					$errors[] =  array("CALL NUMBER REQUIRED\n",'error');
				}
			}


		} else  if($obj_type == 'artifact1'){

			$cn1 = trim($this->getValueTextSK('ea:call_number:part-a'));
			$cn2 = trim($this->getValueTextSK('ea:call_number:ddc'));
			$cn3 = trim($this->getValueTextSK('ea:call_number:part-c'));
			$cn4 = trim($this->getValueTextSK('ea:call_number:part-d'));

			if (!PUtil::isEmpty($cn1) && !PUtil::isEmpty($cn2) && !PUtil::isEmpty($cn3)){
				$call_number = implode('/', array($cn1,$cn2,$cn3));
				if (!PUtil::isEmpty($cn4)){
					$call_number = implode(',', array($call_number,$cn4));
				}
				$key = DataFields::ea_call_number_ea;
				$this->setValueSK($key,$call_number);
			} else {
				$errors[] =  array("CALL NUMBER REQUIRED\n",'error');
			}
		}


	}

/**
 *
 */
	public function generate(){

		$c = 0;
// 		$em =  Lookup::getRelationElementsWithParentMap();
// 		foreach($em as $parent => $elements ){
// 			$this->deleteByKey($parent);
// 			foreach($elements as $el ){
// 				$vals = $this->getArrayValuesByKey($el);
// 				foreach ($vals as $val) {
// 					$c+=1;
// 					//echo("ADD $parent : $val[0] \n");
// 					$this->addValueSK($parent,$val[0],$val[1]);
// 				}
// 			}
// 		}
		return $c;
	}

	private function priv_validate($clear_msgs){
		//echo('<pre>');
		//print_r($this->values);
		//echo('</pre>');

		  // //SINGLE VALUE FIELDS:
 // *   0: text value
 // *   1: lang
 // *   2: database id:    dsd.metadatavalue2(metadata_value_id)
 // *   3: relation
 // *   4: ref_item
 // *   5: json_data
 // *   6: record_id
 // *   7: weight
 // *   8: link (pointer) diktis st parent


		// echo("<pre>");
			// print_r($this->values);
		// echo("</pre>");

	// 			&& ($key == "ea:source:" || $key == "ea:source:print" || $key == "ea:original:print" )))


		if($clear_msgs){
			$this->clearMessages();
		}
		$obj_type = $this->getValueTextSK(DataFields::ea_obj_type);
		if (empty($obj_type)){
				$this->addError('mising obj_type');
				return;
		}

		$obj_class = PDao::get_obj_class_from_obj_type($obj_type);
		if (empty($obj_class)){
				$this->addError('ERROR obj_type');
				return;
		}
		// $obj_class = null;
		// $SQL = 'select  obj_class from dsd.obj_type where name=?';
		// $stmt = $dbh->prepare($SQL);
		// $stmt->bindParam(1,$obj_type);
		// $stmt->execute();
		// if ($r = $stmt->fetch()){
			// $obj_class = $r[0];
		// } else {
				// $this->addError('ERROR obj_type');
				// return;
		// }



		foreach ($this->values as $key => $kvalues) {
			foreach ($kvalues as $k => $v) {
				$link_id = $v[8];
				if (! empty($link_id)){
					$parent = $this->getValuebyClientId($link_id);
					if ($parent == null){
						$record_id = $v[6];
						$this->deleteByClientId($record_id);
					}
				}
			}
		}


		//if ($obj_type <> 'artifact'){
		$title = $this->getValueText('dc','title');
		if (empty($title)){

			$this->addError('mising title');
		}

		if ($obj_class == 'artifact'){
			$this->validate_artifact();
			return;
		};


		$publisher = $this->getValueSK('dc:publisher:');
		if (!empty($publisher)){
			$tmp_arr = $this->getValuesByLink($publisher[6]);
			if (empty($publisher[4]) && count($tmp_arr) == 0 ){
			 $this->addWarn("publisher is not full connected");
			}
		}

		$pub_place = $this->getValueSK('ea:publication:place');
		if (!empty($pub_place)){
			//print_r($pub_place);
			$tmp_arr = $this->getValuesByLink($pub_place[6]);
			if (empty($pub_place[4]) && count($tmp_arr) == 0 ){
				$this->addWarn("publication place is not full connected");
			}
		}

		//$this->validateMarcField('ea:title:uniform');

	//FIXME:  isue number validation
			// $issue = $this->getValueText('ea','issue','no');
			// if(! empty($issue)){
				// if (  ! preg_match('/^\d+$/', $issue)) {
					// $this->addError('issue number error:[' . $issue .']');
				// }
			// }



			// $contributors = $this->getValuesByKeyPrefix('dc:contributor:','ea:contributor:');
			// foreach ($contributors as  $c) {
				// $name = $c[0];
				// if (empty($name)){
					// continue;
				// }
				// $record_id = $c[6];
				// //$key = $c['key'];
				// $ctype = $this->getFirstValueByKeyLink('ea:contributor-type:', $record_id);
				// // if (empty($ctype) && $obj_type == 'book' && strpos($name, ",")  !== false){
				// // }
				// if (empty($ctype)){
						// $this->addError("please add contributor type to contributor [ $name ]");
				// }
//
				// if (!empty($ctype) && $ctype[0] == 'person_s' && strpos($name, ",")  === false){
						// $this->addError("contributor [ $name ] has type 'Person (Surname fist)' but comma not found");
				// }
//
				// // if (!empty($ctype) && $ctype[0] != 'person_s' && strpos($name, ",")  !== false){
						// // $this->addWarn("contributor [ $name ] with comma in name found in different type than 'Person (Surname fist)'");
				// // }
			// }

			// $ctags =$this->getTextValueArraySK(DataFields::dc_subject);
			// $tag_buff = array();
			// foreach ($ctags as $tag){
				// $check = strtolower(trim($tag));
				// if (empty($check)){
					// continue;
				// }
				// if (array_search($check,$tag_buff) === false){
					// $tag_buff[] = $check;
				// } else {
					// $this->addError("subject ($tag) apears many times");
				// }
			// }

		//}


	}

/**
 *
 */
	public function validate(){
		$this->priv_validate(true);
	}

	public function setTreeLevels(){
		$val_array = array();
		foreach ($this->values as $key => &$values) {
			foreach ( $values as &$val ) {
				$lid = $val[6];
				if (!empty($lid)){
					if (empty($val[8])){
						$val[10] = 0;
					} else {
						$val[10] = null;
					}
					$val_array[$lid] = &$val;
				}
			}
		}
		$finish_flag = false;
		$i = 0;
		while (! $finish_flag && $i < 10){
			$i++;
			$finish_flag = true;
			foreach ($val_array as $idx => &$val) {
				$lid = $val[8];
				if (!empty($lid) && isset($val_array[$lid])){
					$parent = $val_array[$lid];
					$old_level = $parent[10];
					if ($old_level !== null){
						if ($old_level == 0){
							$val_array[$lid][10] = 1;
							$old_level = 1 ;
						}
						$new_level = $old_level + 1;
						$val_array[$idx][10] = $new_level;
						//Log::info("SET LEVEL: " . $new_level . " TO: " . $val[8]);
					} else {
						$finish_flag = false;
					}
				}
			}
		}
		if ($i > 1){
			Log::info("TREE LEVEL LOOP COUNTER: " . $i);
		}
	}

	public function dump(){
		echo('<table border="1">');
		echo("<tr>");
		echo('<th>key </th>');
		echo('<th>value</th>');
		echo('<th>lng </th>');
		echo('<th>dbid </th>');
		echo('<th>rel </th>');
		echo('<th>ref </th>');
		echo('<th>json </th>');
		echo('<th>recid </th>');
		echo('<th>w </th>');
		echo('<th>link</th>');
		echo("</tr>\n");
		foreach ($this->values as $k=>$vals){
			foreach ($vals as $i=>$v){
				printf('<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
				$k,$v[0],$v[1],$v[2],$v[3],$v[4],print_r($v[5],true),print_r($v[6],true),$v[7],$v[8]);
			}
			echo("\n");
		}
		echo("</table>");

	}

	public function dumpLaravelLog(){
		foreach ($this->values as $k=>$vals){
			foreach ($vals as $i=>$v){
				$msg = sprintf('%s | %s | %s | %s | %s | %s | %s | %s | %s | %s',
				$k,$v[0],$v[1],$v[2],$v[3],$v[4],json_encode($v[5]),json_encode($v[6]),$v[7],$v[8]);
				Log::info($msg);
			}
		}

	}





}

/*
bitstream_id            | 25
bitstream_format_id     | 4
name                    | gi kai eleutheria_03.pdf
size_bytes              | 12320344
checksum                | dfb0045c88a56c853e1a5e15b584e170
checksum_algorithm      | MD5
description             |
user_format_description |
source                  | /upload/gi kai eleutheria_03.pdf
internal_id             | 25013206935078437392757506055775621077
deleted                 | f
store_number            | 0
sequence_id             | 1
create_dt               | 2011-08-25 15:06:35.59937
*/
/*
class bitstream {
	public $mimetype;
	public $name;
	public $size_bytes;
	public $checksum;
	public $checksum_algorithm;
	public $description;
	public $user_format_description;
	public $internal_id;
	public $store_number;
	public $sequence_id;
	public $bundle;
}
*/

class ItemExportData {
	public $itemBasicData;
	#file| idx | idxf | ttype | auto_gen
	public $thumbs = array();
	public $bitstreams = array();
	public $bundles = array();
}



class ItemBasicData
{
	public $bibref;
	public $item_metadata;
	public $uuid;


	function toArray(){
		$arr = array(
		"uuid"=>$this->uuid,
		"bibref"=>$this->bibref,
		"metadata"=>$this->item_metadata->values
		);
		return $arr;
	}


	function toJson(){
		 return json_encode($this->toArray());
	}
	function toXml(){
		include("XML/Serializer.php");

		$serializer = new XML_Serializer();
		$arr = array($this->bibref,$this->item_metadata->values);
		$arr = array($this->bibref,$this->item_metadata->values);
		$result	 = $serializer->serialize($this->toArray());
		if ($result != true){
			return null;
		}
		return $serializer->getSerializedData();
	}


	#######################
	## static
	#######################
	public static function fromArray($arr){
		$rep = new ItemBasicData();
		$rep->uuid  = $arr["uuid"];
		$rep->bibref = $arr["bibref"];
		$rep->item_metadata = $arr['metadata'];
		return $rep;
	}
	public static function fromJson($json_str){
		$arr = json_decode($json_str,true);
		$ob = self::fromArray($arr);
		return $ob;
	}




}


#Array
#(
#	[dc:contributor:author] => Array
#		(
#			[0] => Array
#				(
#					[0] => author...
#					[1] =>
#					[2] => 1575
#				)
#
#		)
#
#
#	[dc:subject:] => Array
#		(
#			[0] => Array
#				(
#					[0] => koko
#					[1] => el_GR
#					[2] => 1560
#				)
#
#			[1] => Array
#				(
#					[0] => lala
#					[1] => el_GR
#					[2] => 1561
#				)
#		)
#
#)






