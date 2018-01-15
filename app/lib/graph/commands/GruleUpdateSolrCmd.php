<?php

class VertexSolrWorkData {

	// TODO = replace arrays by ArrayObject

//	public $id;
//	public $object_type;
//	public $record_type;
//	public $status;
//	public $opac1;
//	public $title;
//	public $label;
//	public $secondaryTitles = array();
//	public $descriptions = array();
//	public $subjects = array();
//	public $subjects_manif = array();
//	public $subjects_ids = array();
//	public $authors = array();
//	public $authors_with_ids = array();
//	public $contributors = array();
//	public $languages = array();
//	public $publication_places = array();
//	public $publication_places_ids = array();
//	public $publication_places_with_ids = array();
//	public $publication_types = array();
//	public $publishers = array();
//	public $publishers_ids = array();
//	public $publishers_with_ids = array();
//	public $digital_item_types = array();
//	public $lawyer_with_ids = array();
//	public $categories_l1 = array();
//	public $categories_l2 = array();
//	public $num_of_manifestations;
//	public $num_of_digital_items;
//	public $is_subject;
//	public $create_dt;
//	public $form_type;


  /**
   * @var ArrayObject
   */
  private $data;

  public function __construct() {
    $this->data = new ArrayObject();
  }

  public static function createSolrValueId($name, $id){
    return ($name . '‡' . $id);
  }



  public function __set($name, $value) {
    //Log::info('__set '  . $name );
    $this->data[$name] = $value;

  }

  public function set($name,$value) {
    //PUtil::logGreen("set " . $name .  ' : ' . (is_object($value) ? "CLASS: " .get_class($value) : '_'));
    $this->data[$name] = $value;
  }


  public function add($name,$value,$id =null) {

    //PUtil::logGreen('@add:: ' . $name . ' ::: ' . $value . ' ::: ' . $id);

    $ev = empty($value);
    $ei = empty($id);
    if (!$ev && ! $ei){
      $value = VertexSolrWorkData::createSolrValueId($value, $id);
    } elseif ($ev && !$ei){
      $value = $id;
    } else if ($ev){
      return false;
    }
    //PUtil::logGreen("add " . $name .  ' : '  . (is_object($value) ? "CLASS: " .get_class($value) : '_'));
    $arr = null;
    if ($this->data->offsetExists($name)) {
      $arr = $this->data->offsetGet($name);
      if (!in_array($value, $arr)) {
        $arr[] = $value;
        $this->data->offsetSet($name, $arr);
        return true;
      }
    } else {
      $this->data->offsetSet($name, array($value));
      return true;
    }
    return false;
  }

  public function __get($name) {
    //Log::info('__get '  . $name);
    if ($this->data->offsetExists($name)) {
      return $this->data->offsetGet($name);
    }
    return array();
    //return new PArray();
//    if (property_exists($this, $name)) {
//      return $this->$name;
//    }
//    if ($this->data->offsetExists($name)) {
//      return  $this->data->offsetGet($name);
//    }
//    return null;
  }


  public function get($name){
    if ($this->data->offsetExists($name)) {
      return $this->data->offsetGet($name);
    }
    return null;
  }

  public function data(){
    return $this->data;
  }

  public function getNodeInfoData(){
		$rep = new ArrayObject();
		foreach ($this->data as $k=>$v){
      if ($k == 'opac1' || $k == 'opac2'||empty($v)){
        continue;
      }
		  $rep->offsetSet($k,$v);
    }
				return $rep;
	}

  public function getNodeInfoKeys(){
    //$rep = new ArrayObject();
    $rep = [];
    foreach ($this->data as $k=>$v){
      if ($k == 'opac1' || $k == 'opac2'||empty($v)){
        continue;
      }
      //$rep->offsetSet($k,$v);
      //$rep->append($k);
      $rep[] = $k;
    }
    return $rep;
  }


}

class GRuleUpdateSolrCmd implements GCommand {

	/**
	 *
	 * @var SolrControl
	 */
	private $solrCtrl;

	public function __construct($index_name, $context) {
		$this->solrCtrl = new SolrControl($index_name, $context);
	}

	/**
	 *
	 * @param GRuleContextR $context
	 */
	public function execute($context){
		/**
		 *
		 * @var $solrDataArray VertexSolrData[]
		 * */
		$solrDataArray = $context->get('SOLR_VERTEX_DATA');
		$this->solrCtrl->batchInsertUpdateSolrRecords($solrDataArray);
	}

}
