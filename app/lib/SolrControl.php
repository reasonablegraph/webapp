<?php

use Solarium\Plugin\BufferedAdd\Event\Events;
use Solarium\Plugin\BufferedAdd\Event\PreFlush as PreFlushEvent;

class SolrControl {

	private $indexName;
	private $context;
	private $config;

	public function __construct($index_name, $context) {

		$this->indexName = $index_name;
		$this->context = $context;
		$this->config = array( 'endpoint' => PUtil::getSolrConfigEndPoints('opac') );
	}

	public function batchInsertUpdateSolrRecords(&$solrDataArray) {

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

			foreach ($solrDataArray as $id=>$vdata){

				$doc = array(
					'id' => $id,
					'object_type' => $vdata->object_type,
					'record_type' => $vdata->record_type,
					'status' => $vdata->status,
					'opac1' => $vdata->opac1,
					'title' => $vdata->title,
					'label' => $vdata->label,
					'secondary_titles' => $vdata->secondaryTitles,
					'descriptions' => $vdata->descriptions,
					'subjects' => $vdata->subjects,
					'subjects_manif' => $vdata->subjects_manif,
					'subjects_ids' => $vdata->subjects_ids,
					'authors' => $vdata->authors,
					'authors_with_ids' => $vdata->authors_with_ids,
					'contributors' => $vdata->contributors,
					'languages' => $vdata->languages,
					'publication_places' => $vdata->publication_places,
					'publication_places_ids' => $vdata->publication_places_ids,
					'publication_places_with_ids' => $vdata->publication_places_with_ids,
					'publication_types' => $vdata->publication_types,
					'publishers' => $vdata->publishers,
					'publishers_ids' => $vdata->publishers_ids,
					'publishers_with_ids' => $vdata->publishers_with_ids,
					'digital_item_types' => $vdata->digital_item_types,
					'num_of_manifestations' => $vdata->num_of_manifestations,
					'num_of_digital_items' => $vdata->num_of_digital_items,
					'is_subject' => $vdata->is_subject,
					'create_dt' => $vdata->create_dt,
				);


				Log::info('CreateDocument: '.$doc['id'].'|'.$doc['object_type'].'|'.$doc['record_type']);

				// flushes automatically when is full
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

?>
