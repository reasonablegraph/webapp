<?php

use Solarium\Plugin\BufferedAdd\Event\Events;
use Solarium\Plugin\BufferedAdd\Event\PreFlush as PreFlushEvent;


class SolrControl2{


	//private $client;
	private $buffer;

	public function __construct($configEndPoint = 'opac') {
		$batchsize = Config::get('arc.SOLR_BATCHSIZE',50);
		$config = array( 'endpoint' => PUtil::getSolrConfigEndPoints($configEndPoint) );
		$client = new Solarium\Client($config);
		$this->buffer = $client->getPlugin('bufferedadd');
		$this->buffer->setBufferSize($batchsize);
	}

	public function insertData($data){
		//Log::info(print_r($data,true));

		// flushes automatically when is full
		return $this->buffer->createDocument(array(
			'id' => $data['id'],
			'object_type' => $data['object_type'],
			'record_type' => $data['record_type'],
			'status' => isset($data['status'])?$data['status']:null,
			'opac1' => isset($data['opac1'])? $data['opac1']:null,
			'title' => isset($data['title'])? $data['title']:null,
			'label' => isset($data['label'])? $data['label']:null,
			'secondary_titles' => isset($data['secondaryTitles'])? $data['secondaryTitles']:null,
			'descriptions' => isset($data['descriptions'])? $data['descriptions']:null,
			'subjects' =>isset($data['subjects'])? $data['subjects']:null,
			'subjects_manif' =>isset($data['subjects_manif'])? $data['subjects_manif']:null,
			'subjects_ids' =>isset($data['subjects_ids'])? $data['subjects_ids']:null,
			'authors' =>isset($data['authors'])? $data['authors']:null,
			'authors_with_ids' =>isset($data['authors_with_ids'])? $data['authors_with_ids']:null,
			'contributors' =>isset($data['contributors'])? $data['contributors']:null,
			'languages' =>isset($data['languages'])? $data['languages']:null,
			'publication_places' =>isset($data['publication_places'])? $data['publication_places']:null,
			'publication_places_ids' =>isset($data['publication_places_ids'])? $data['publication_places_ids']:null,
			'publication_places_with_ids' =>isset($data['publication_places_with_ids'])? $data['publication_places_with_ids']:null,
			'publication_types' =>isset($data['publication_types'])? $data['publication_types']:null,
			'publishers' =>isset($data['publishers'])? $data['publishers']:null,
			'publishers_ids' =>isset($data['publishers_ids'])? $data['publishers_ids']:null,
			'publishers_with_ids' =>isset($data['publishers_with_ids'])? $data['publishers_with_ids']:null,
			'digital_item_types' =>isset($data['digital_item_types'])? $data['digital_item_types']:null,
			'num_of_manifestations' =>isset($data['num_of_manifestations'])? $data['num_of_manifestations']:null,
			'num_of_digital_items' =>isset($data['num_of_digital_items'])? $data['num_of_digital_items']:null,
			'is_subject' =>isset($data[''])? $data['is_subject']:null,
			'create_dt' =>isset($data[''])? $data['create_dt']:null,

		));
	}
//
//	public function insertData($data){
//		//Log::info(print_r($data,true));
//		$this->buffer->createDocument($this->createDoc($data));
//	}
	public function flush(){
		$this->buffer->flush();
	}
	public function commit(){
		$this->buffer->flush();
		$this->buffer->commit();

	}

}





class SolrControl {

	private $indexName;
	private $context;
	private $config;

	public function __construct($index_name, $context) {

		$this->indexName = $index_name;
		$this->context = $context;
		$this->config = array( 'endpoint' => PUtil::getSolrConfigEndPoints('opac') );
	}





	public function batchInsertUpdateSolrRecords($solrDataArray) {
    PUtil::logBlue("batchInsertUpdateSolrRecords");
    PUtil::logBlue(": " . get_class($solrDataArray));
		$batchsize = Config::get('arc.SOLR_BATCHSIZE',50);

		try {

			$client = new Solarium\Client($this->config);
			$buffer = $client->getPlugin('bufferedadd');
			$buffer->setBufferSize($batchsize);

			$client->getEventDispatcher()->addListener(
				Events::PRE_FLUSH,
				function (PreFlushEvent $event) {
					Log::info('Flushing buffer (' . count($event->getBuffer()) . 'docs)');
				}
			);

			/* @var $vdata VertexSolrWorkData */
			foreach ($solrDataArray as $id=>$vdata){
			  $doc=$vdata->data()->getArrayCopy();
			  if (isset($doc['secondaryTitles'])) {
          $doc['secondary_titles'] = $doc['secondaryTitles'];
          unset($doc['secondaryTitles']);
        }

//				$doc = array(
//					'id' => $id,
//					'object_type' => $vdata->object_type,
//					'record_type' => $vdata->record_type,
//					'status' => $vdata->status,
//					'opac1' => $vdata->opac1,
//					'title' => $vdata->title,
//					'label' => $vdata->label,
//					'secondary_titles' => $vdata->secondaryTitles,
//					'descriptions' => $vdata->descriptions,
//					'subjects' => $vdata->subjects,
//					'subjects_manif' => $vdata->subjects_manif,
//					'subjects_ids' => $vdata->subjects_ids,
//					'authors' => $vdata->authors,
//					'authors_with_ids' => $vdata->authors_with_ids,
//					'contributors' => $vdata->contributors,
//					'languages' => $vdata->languages,
//					'publication_places' => $vdata->publication_places,
//					'publication_places_ids' => $vdata->publication_places_ids,
//					'publication_places_with_ids' => $vdata->publication_places_with_ids,
//					'publication_types' => $vdata->publication_types,
//					'publishers' => $vdata->publishers,
//					'publishers_ids' => $vdata->publishers_ids,
//					'publishers_with_ids' => $vdata->publishers_with_ids,
//					'digital_item_types' => $vdata->digital_item_types,
//					'lawyer_with_ids' => $vdata->lawyer_with_ids,
//					'categories_l1' => $vdata->categories_l1,
//					'categories_l2' => $vdata->categories_l2,
//					'num_of_manifestations' => $vdata->num_of_manifestations,
//					'num_of_digital_items' => $vdata->num_of_digital_items,
//					'is_subject' => $vdata->is_subject,
//					'create_dt' => $vdata->create_dt,
//          'form_type' => $vdata->form_type
//				);


				//Log::info('CreateDocument: '.$doc['id'].'|'.$doc['object_type'].'|'.$doc['record_type']);

				//Log::info(print_r($doc,true));
				// flushes automatically when is full
        //Putil::logGreen(print_r($doc,true));
				$buffer->createDocument($doc);

			}

			// flush remaining records if any
			$buffer->flush();

			// commit
			$buffer->commit();

		} catch (Exception $e) {
			Log::error('SOLR-CONTROL ERROR: ' . $e->getMessage());
      Log::info($e);
		}

	}

// 	public function batchInsertUpdateSolrRecords(&$solrDataArray) {

// 		$batchsize = Config::get('arc.SOLR_BATCHSIZE',50);

// 		try {

// 			$client = new Solarium\Client($this->config);
// 			$update = $client->createUpdate();

// 			$counter = 0;
// 			$solrDocumentsArray = array();

// 			foreach ($solrDataArray as $id=>$vdata){

// 				$counter++;
// 				$doc = $update->createDocument();

// 				$doc->id = $id;
// 				$doc->object_type = $vdata->object_type;
// 				$doc->record_type = $vdata->record_type;
// 				$doc->status = $vdata->status;
// 				$doc->opac1 = $vdata->opac1;
// 				$doc->title = $vdata->title;
// 				$doc->secondary_titles = $vdata->secondaryTitles;
// 				$doc->descriptions = $vdata->descriptions;
// 				$doc->subjects = $vdata->subjects;
// 				$doc->authors = $vdata->authors;
// 				$doc->authors_with_ids = $vdata->authors_with_ids;
// 				$doc->contributors = $vdata->contributors;
// 				$doc->languages = $vdata->languages;
// 				$doc->publication_places = $vdata->publication_places;
// 				$doc->publication_places_with_ids = $vdata->publication_places_with_ids;
// 				$doc->publication_types = $vdata->publication_types;
// 				$doc->publishers = $vdata->publishers;
// 				$doc->digital_item_types = $vdata->digital_item_types;
// 				$doc->num_of_manifestations = $vdata->num_of_manifestations;
// 				$doc->num_of_digital_items = $vdata->num_of_digital_items;
// 				$doc->is_subject = $vdata->is_subject;

// 				$update->addDocument($doc);
// 				// Log::info("SOLR: added vertex " . $id);

// 				if ($counter == $batchsize) {
// 					// batch commit
// 					$update->addCommit();
// 					$result = $client->update($update);
// 					Log::info("SOLR: COMMITED BATCH OF SIZE " . $counter . " TO INDEX");
// 					// reset
// 					$counter = 0;
// 					$update = $client->createUpdate();
// 				}

// 			}

// 			if ($counter != 0) {
// 				// commit
// 				$update->addCommit();
// 				$result = $client->update($update);
// 				Log::info("SOLR: COMMITED BATCH OF SIZE " . $counter . " TO INDEX");
// 			}

// 			Log::info("SOLR: FINISHED UPDATING INDEX");

// 		} catch (Exception $e) {
// 			Log::error('SOLR-CONTROL ERROR: ' . $e->getMessage());
// 			Log::info($e);

// 		}

// 	}

}

