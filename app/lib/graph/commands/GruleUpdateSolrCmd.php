<?php

class VertexSolrWorkData {

	// TODO = replace arrays by ArrayObject

	public $id;
	public $object_type;
	public $record_type;
	public $status;
	public $opac1;
	public $title;
	public $label;
	public $secondaryTitles = array();
	public $descriptions = array();
	public $subjects = array();
	public $subjects_manif = array();
	public $subjects_ids = array();
	public $authors = array();
	public $authors_with_ids = array();
	public $contributors = array();
	public $languages = array();
	public $publication_places = array();
	public $publication_places_ids = array();
	public $publication_places_with_ids = array();
	public $publication_types = array();
	public $publishers = array();
	public $publishers_ids = array();
	public $publishers_with_ids = array();
	public $digital_item_types = array();
	public $num_of_manifestations;
	public $num_of_digital_items;
	public $is_subject;
	public $create_dt;

	public function __construct() {}


	public function arrayValue(){
		$rep = array();

		$class_vars = get_class_vars(get_class($this));
		foreach ($class_vars as $name => $defval) {
			if ($name == 'opac1'){
				continue;
			}
			$value=$this->$name;
			if (!empty($value)) {
				$rep[$name] = $value;
			}
		}
		return $rep;

//		return array(
//			'id'=>$this->id,
//			'object_type'=>$this->object_type,
//			'record_type'=>$this->record_type,
//			'status'=>$this->status,
//			#'opac1'=>$this->opac1,
//			'title'=>$this->title,
//			'label'=>$this->label,
//			'secondaryTitles'=>$this->secondaryTitles,
//			'descriptions'=>$this->descriptions,
//			'subjects'=>$this->subjects,
//			'subjects_manif'=>$this->subjects_manif,
//			'authors'=>$this->authors,
//			'authors_with_ids'=>$this->authors_with_ids,
//			'contributors'=>$this->contributors,
//			'languages'=>$this->languages,
//			'publication_places'=>$this->publication_places,
//			'publication_places_ids'=>$this->publication_places_ids,
//			'publication_places_with_ids'=>$this->publication_places_with_ids,
//			'publication_types'=>$this->publication_types,
//			'publishers'=>$this->publishers,
//			'publishers_with_ids'=>$this->publishers_with_ids,
//			'digital_item_types'=>$this->digital_item_types,
//			'num_of_manifestations'=>$this->num_of_manifestations,
//			'num_of_digital_items'=>$this->num_of_digital_items,
//			'is_subject'=>$this->is_subject,
//		);
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

?>