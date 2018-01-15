<?php
class FTSData {


	public $id = null;
	public $record_type = null;
	public $object_type = null;
	public $status = null;
	/* @type string */
	public $create_date = null;
	/* @type integer */
	public $item_id = null;
	public $opac = null;
	public $title = null;
	public $flags = null;


	public $fts_a = '';
	public $fts_b = null;
	public $fts_c = null;
	public $fts_d = null;

	private $fts_sep = ' ';


	public function __construct() {
	}

	public function addA($str) {
		if (empty($str) || $str == '') {
			return;
		}
		$this->fts_a = $this->fts_a  . $this->fts_sep . $str;
	}
	public function addB($str) {
		if (empty($str) || $str == '') {
			return;
		}
		if (empty($this->fts_b)) {
			$this->fts_b = '';
		}
		$this->fts_b = $this->fts_b  . $this->fts_sep . $str;
	}
	public function addC($str) {
		if (empty($str) || $str == '') {
			return;
		}
		if (empty($this->fts_c)) {
			$this->fts_c = '';
		}
		$this->fts_c = $this->fts_c  . $this->fts_sep . $str;
	}
	public function addD($str) {
		if (empty($str) || $str == '') {
			return;
		}
		if (empty($this->fts_d)) {
			$this->fts_d = '';
		}
		$this->fts_d = $this->fts_d  . $this->fts_sep . $str;
	}


}

class FTSControl {
	private $table;
	private $col_id;
	private $col_fts;


	public function __construct($table, $column_id, $colum_fts) {
		$this->table = $table;
		$this->col_id = $column_id;
		$this->col_fts = $colum_fts;
	}


	/**
	 * @param $column_fts
	 * @param $id
	 * @param VertextFtsData $fts_data
	 * @return mixed
	 */
	public function updateFTSCol($column_fts, $id, $fts_data) {

		//$con = dbconnect();
		$con = prepareService();

		$fts_a = $fts_data->fts_a;
		$fts_b = $fts_data->fts_b;
		$fts_c = $fts_data->fts_c;
		$fts_d = $fts_data->fts_d;

		$fts_q = "setweight(dsd.to_gr_tsvector(?), 'A')";
		if (! empty($fts_b)) {
			$fts_q .= " || setweight(dsd.to_gr_tsvector(?), 'B')";
		}
		if (! empty($fts_c)) {
			$fts_q .= " || setweight(dsd.to_gr_tsvector(?), 'C')";
		}
		if (! empty($fts_d)) {
			$fts_q .= " || setweight(dsd.to_gr_tsvector(?), 'D')";
		}

		$SQL = sprintf('UPDATE %s SET %s  = %s WHERE %s = ?', $this->table, $column_fts, $fts_q, $this->col_id);
		//Log::info($SQL);
		$stmt = $con->prepare($SQL);

		$stmt->bindParam(1, $fts_a);
		//Log::info("a: " . $fts_a);
		$c = 2;
		if (!empty($fts_b)) {
			//Log::info("b: ".  $fts_b);
			$stmt->bindParam($c, $fts_b);
			$c += 1;
		}
		if (!empty($fts_c)) {
			//Log::info("c: " . $fts_c);
			$stmt->bindParam($c, $fts_c);
			$c += 1;
		}
		if (!empty($fts_d)) {
			//Log::info("d: " .$fts_d);
			$stmt->bindParam($c, $fts_d);
			$c += 1;
		}
		//Log::info(": ".  $this->col_id . ' = ' . $id);
		$stmt->bindParam($c, $id, PDO::PARAM_INT);
		$stmt->execute();
		return $count = $stmt->rowCount();
	}


	public function updateFTS($id, $fts_data) {
		return $this->updateFTSCol($this->col_fts, $id, $fts_data);
	}



}




class SolrFTSControl {

	const  RECORDTYPE_SUBJECT = 'SUBJECT';
	const  RECORDTYPE_STUFF = 'STUFF';
	const  RECORDTYPE_CONTRIBUTOR = 'CONTRIBUTOR';
	const  RECORDTYPE_ISBN = 'ISBN';
	const  RECORDTYPE_LABEL = 'LABEL';


	private $context;
	private $client;



	public function __construct($context=null) {
		$this->context = $context;
		$this->client = new Solarium\Client(array( 'endpoint' => PUtil::getSolrConfigEndPoints('staff') ));
	}


	/**
	 * @param $id
	 * @param string $recordType
	 * @param VertextFtsData $fts_data
	 */
	public function updateFTSRecord($recordType, $id,$fts_data) {
	  //PUtil::logRed("updateFTSRecord");
		$client = $this->client;
		$update = $client->createUpdate();
		$doc = $update->createDocument();

//id: string
//record_type: string
//fts_a: string
//fts_b: string
//fts_c: string
//fts_d: string
//flags: array[string]
//object_type: string
//status: string
//create_date: date
//title: string
//item_id: int
//opac1 : string

		Log::info("XXXXXXXXX ID:" . $id);
		$SOLR_ID = $id . '-' . $recordType;
		$doc->id = $SOLR_ID;   //REQUIRED
		$doc->record_type = $recordType; //REQUIRED
		$doc->object_type = $fts_data->object_type;//REQUIRED
		$doc->status = $fts_data->status;      //REQUIRED
		$doc->create_date = $fts_data->create_date; //REQUIRED
		$doc->item_id = $fts_data->item_id; //REQUIRED

		$doc->opac1 = json_encode($fts_data->opac);   //REQUIRED?
		$doc->title = $fts_data->title;

		$doc->fts_a = $fts_data->fts_a;
		$doc->fts_b = $fts_data->fts_b;
		$doc->fts_c = $fts_data->fts_c;
		$doc->fts_d = $fts_data->fts_d;
		$doc->flags = $fts_data->flags;



		$update->addDocument($doc);
		$update->addCommit();
		$result = $client->update($update);

	}







//
	public function batchInsertUpdateSolrRecords(&$solrDataArray) {
    PUtil::logRed("FTSCONTROL batchInsertUpdateSolrRecords ???????????????????????????????");
		$batchsize = 50;

		try {

			$client = new Solarium\Client($this->config);
			$update = $client->createUpdate();

			$counter = 0;
			$solrDocumentsArray = array();

			foreach ($solrDataArray as $id=>$vdata){

				$counter++;

				$doc = $update->createDocument();
				$doc->id = $id;
				$doc->object_type = $vdata->object_type;
				$doc->record_type = $vdata->record_type;
				$doc->status = $vdata->status;
				$doc->opac1 = $vdata->opac1;
				$doc->title = $vdata->title;
				$doc->secondary_titles = $vdata->secondaryTitles;
				$doc->descriptions = $vdata->descriptions;
				$doc->subjects = $vdata->subjects;
				$doc->authors = $vdata->authors;
				$doc->authors_with_ids = $vdata->authors_with_ids;
				$doc->contributors = $vdata->contributors;
				$doc->languages = $vdata->languages;
				$doc->publication_places = $vdata->publication_places;
				$doc->publication_places_with_ids = $vdata->publication_places_with_ids;
				$doc->publication_types = $vdata->publication_types;
				$doc->publishers = $vdata->publishers;
				$doc->digital_item_types = $vdata->digital_item_types;
				$doc->num_of_manifestations = $vdata->num_of_manifestations;
				$doc->num_of_digital_items = $vdata->num_of_digital_items;
				$doc->is_subject = $vdata->is_subject;

// 				$solrDocumentsArray[] = $doc;
				$update->addDocument($doc);

				// Log::info("SOLR: added vertex " . $id);

				if ($counter == $batchsize) {
					// commit
// 					$update->addDocuments($solrDocumentsArray);
					$update->addCommit();
					$result = $client->update($update);
					Log::info("SOLR: COMMITED BATCH OF SIZE " . $counter . " TO INDEX");
					// reset
					$counter = 0;
// 					$solrDocumentsArray = array();
					$update = $client->createUpdate();
				}

			}

			if ($counter != 0) {

				$update->addCommit();
				$result = $client->update($update);
				Log::info("SOLR: COMMITED BATCH OF SIZE " . $counter . " TO INDEX");

// 				$update->addDocuments($solrDocumentsArray);
// 				$update->addCommit();
// 				$result = $client->update($update);
// 				Log::info("SOLR: COMMITED BATCH OF SIZE " . $counter . " TO INDEX");
			}

			Log::info("SOLR: FINISHED UPDATING INDEX");

		} catch (Exception $e) {
			Log::error('SOLR-CONTROL ERROR: ' . $e->getMessage());
			Log::info($e);

		}

	}




}









