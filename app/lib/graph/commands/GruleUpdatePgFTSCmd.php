<?php



class VertextFtsData {


	private $id;

	/**
	 *
	 * @var FTSData
	 */
	private $stuff;

	/**
	 *
	 * @var FTSData
	 */
	private $opac;

	/**
	 *
	 * @var FTSData
	 */
	private $contributor;

	/**
	 *
	 * @var FTSData
	 */
	private $isbn;


	/**
	 *
	 *gia xrisi sto advance
	 *
	 * @var FTSData
	 */
	private $label;



	/**
	 *
	 * @var FTSData
	 */
	private $subject;




	private $data = array();

	public function __construct() {
	}


	public function __set($name, $value) {
		//Log::info('__set '  . $name );

		if (property_exists($this, $name)) {
			$this->$name = $value;
		} else {
			$this->data[$name] = $value;
		}

	}

	public function __get($name) {
		//Log::info('__get '  . $name);
		$rep = null;
		if (property_exists($this, $name)) {
			$rep = $this->$name;
		} else if (array_key_exists($name, $this->data)) {
			$rep =  $this->data[$name];
		}
		if (empty($rep)){
			$rep = new FTSData();
		}
		$this->__set($name,$rep);
		return $rep;
	}

}



class GRuleUpdatePgFTSCmd  implements  GCommand {
/**
 *
 * @var FTSControl
 */
	private $ftsCtrl;

	public function __construct($table, $column_id) {
		$this->ftsCtrl = new FTSControl($table, $column_id, null);
	}

	/**
	 *
	 * @param GRuleContextR $context
	 */
	public function execute($context){
		//$context->addDebugMessage("GRuleUpdatePgFTSCmd exec");
		/** @var $ftsDataArray VertextFtsData[] */
		$ftsDataArray = $context->get('FTS_VERTEX_DATA');


		foreach ($ftsDataArray as $id=>$vdata){
			//$context->addDebugMessage('#UPDATE FTS v: ' . $id  . " #A: " . $vdata->opac->fts_a . " #B: " . $vdata->opac->fts_b);
			$this->ftsCtrl->updateFTSCol(PgFtsConstants::COLUMN_SUBJECT, $id, $vdata->subject);
			$this->ftsCtrl->updateFTSCol(PgFtsConstants::COLUMN_STUFF, $id, $vdata->stuff);
			$this->ftsCtrl->updateFTSCol(PgFtsConstants::COLUMN_OPAC, $id, $vdata->opac);
			$this->ftsCtrl->updateFTSCol(PgFtsConstants::COLUMN_CONTRIBUTOR, $id, $vdata->contributor);
			$this->ftsCtrl->updateFTSCol(PgFtsConstants::COLUMN_ISBN, $id, $vdata->isbn);
			$this->ftsCtrl->updateFTSCol(PgFtsConstants::COLUMN_LABEL, $id, $vdata->label);
		}

	}

}




class GRuleUpdateSolrFTSCmd  implements  GCommand {
	/**
	 *
	 * @var SolrFTSControl
	 */
	private $ftsCtrl;

	public function __construct() {
		$this->ftsCtrl = new SolrFTSControl();
	}

	/**
	 *
	 * @param GRuleContextR $context
	 */
	public function execute($context){
		//$context->addDebugMessage("GRuleUpdatePgFTSCmd exec");
		/** @var $ftsDataArray VertextFtsData[] */
		$ftsDataArray = $context->get('FTS_VERTEX_DATA');

		foreach ($ftsDataArray as $id=>$vdata){
			$context->addDebugMessage('#UPDATE SOLR-FTS v: ' . $id );
			$this->ftsCtrl->updateFTSRecord(SolrFTSControl::RECORDTYPE_SUBJECT,$id,$vdata->subject);
			$this->ftsCtrl->updateFTSRecord(SolrFTSControl::RECORDTYPE_STUFF,$id,$vdata->stuff);
			$this->ftsCtrl->updateFTSRecord(SolrFTSControl::RECORDTYPE_CONTRIBUTOR,$id,$vdata->contributor);
			$this->ftsCtrl->updateFTSRecord(SolrFTSControl::RECORDTYPE_ISBN,$id,$vdata->isbn);
			$this->ftsCtrl->updateFTSRecord(SolrFTSControl::RECORDTYPE_LABEL,$id,$vdata->label);

		}

	}

}


//abstract class GRuleUpdatePgFTSCmdBase implements  GCommand {
//
//	private $itemId;
//	private $fts_data;
//	private $ftsCtrl;
//
//	public function __construct($table, $column_id, $colum_fts, $itemId, $fts_data) {
//		$this->itemId = $itemId;
//		$this->fts_data = $fts_data;
//		$this->ftsCtrl = new FTSControl($table, $column_id, $colum_fts);
//	}
//
//	/**
//	 *
//	 * @param GRuleContextR $context
//	 */
//	public function execute($context){
//		if (!empty($this->fts_data)){
//			$this->ftsCtrl->updateFTS($this->itemId, $this->fts_data);
//		}
//	}
//
//}



// class GRuleUpdatePgFTSStuffCmd extends GRuleUpdatePgFTSCmdBase implements  GCommand {

// 	public function __construct($itemId, $fts_data) {
// 		parent::__construct('dsd.item2','item_id', 'fts', $itemId, $fts_data);
// 	}

// }



// class GRuleUpdatePgFTSOpacCmd extends GRuleUpdatePgFTSCmdBase implements  GCommand {
// 	public function __construct($itemId, $fts_data) {
// 		parent::__construct('dsd.item2','item_id', 'fts2', $itemId, $fts_data);
// 	}
// }



// class GRuleUpdatePgFTSAdvContributorCmd extends GRuleUpdatePgFTSCmdBase implements  GCommand {
// 	public function __construct($itemId, $fts_data) {
// 		parent::__construct('dsd.item2','item_id', PgFtsConstants::COLUMN_CONTRIBUTOR, $itemId, $fts_data);
// 	}
// }

// class GRuleUpdatePgFTSAdvISBNCmd extends GRuleUpdatePgFTSCmdBase implements  GCommand {
// 	public function __construct($itemId, $fts_data) {
// 		parent::__construct('dsd.item2','item_id', PgFtsConstants::COLUMN_ISBN, $itemId, $fts_data);
// 	}
// }



?>