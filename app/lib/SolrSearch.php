<?php


class SolrFilters {

	private $options;
	private $filters_def;
	private $default_record_type;
	private $record_types = array();
	private $filters;
	private $empty_query_flag = false;

	function SolrFilters($options){
		$this->options = $options;
		$this->filters_def = $options['filters_def'];
		$this->default_record_type = $options['default_record_type'];
		$this->record_types = array_keys($this->filters_def);
	}

	function parseInput(){
			$record_type = null;
			foreach ($this->record_types as $rt){
				$filter_def = $this->filters_def['$rt'];
					foreach ($filter_def as $f) {
						if (Input::has($f)) {
							$record_type = $rt;
							break;
						}
					}
				if (!empty($record_type)) {
					brake;
				}
			}
			if (empty($record_type)) {
				$this->empty_query_flag = true;
				$record_type = $this->default_record_type;
			}
	}
	function getFilters($record_type){
	}
}

class SolrSearch {

	private $client;
	private $data;
	private $resultsPerPage;
	private $maxResults = 1000;
	private $start = 0;

	function SolrSearch($opts = null){
		Log::info("new SolrSeearch");
		$opts = empty($opts) ? array() : $opts;

		if (isset($opts['start'])) {
			$this->start = $opts['start'];
		}
		if (isset($opts['paging_limit'])) {
			$this->start = $opts['paging_limit'];
		} else {
			$this->resultsPerPage =  Config::get('arc.PAGING_LIMIT');
		}
		if (isset($opts['max_results'])) {
			$this->maxResults = $opts['max_results'];
		}

		$this->data = array();
		$this->data['resultsPerPage'] = $this->resultsPerPage;

		//SOLR connection configuration
		$config = array('endpoint' => PUtil::getSolrConfigEndPoints('opac'));
		$this->client = new Solarium\Client($config);

	}






	private function _solr_search_similar_subjects($term) {

		$maxRelevantSubjects = 10;
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// QUERY 2 - GET RELEVANT SUBJECTS
		$subjectsQuery = $this->client->createSelect();
		$subjectsEdismax = $subjectsQuery->getEDisMax();
		$subjectsEdismax->setQueryFields("catch_all_1^5.0 catch_all_2^3.0 catch_all_3^1.0");
		$subjectsEdismax->setPhraseBigramFields("catch_all_1^5.0 catch_all_2^3.0 catch_all_3^1.0");
		$subjectsQuery->setQuery($term);
		$subjectsQuery->setRows($maxRelevantSubjects);
		$subjectsQuery->setFields(array('id', 'opac1', 'is_subject'));
		$subjectsQuery->createFilterQuery('subjects_filter_query')->setQuery("is_subject:true");
		$subjectsResultset = $this->client->select($subjectsQuery);
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		return $subjectsResultset;
	}



	private function setupHighlighting($query){
		// setup highlighting
		$hl = $query->getHighlighting();
		$hl->setUseFastVectorHighlighter(true);
		$hl->setFields(array('title_hl', 'secondary_titles_hl', 'descriptions_hl', 'subjects_hl', 'authors_hl', 'places_hl'));
		// 		$hl->setSimplePrefix('<b style="background-color:yellow">');
		// 		$hl->setSimplePostfix('</b>');

	}

		private function setupQuery($query, $term,$filters, $start=null){
		$start = empty($start)?$this->start : $start;

		$edismax = $query->getEDisMax();
		$edismax->setQueryFields("catch_all_1^5.0 catch_all_2^3.0 catch_all_3^1.0");
		$edismax->setPhraseBigramFields("catch_all_1^5.0 catch_all_2^3.0 catch_all_3^1.0");

		if (!empty($filters)) {
			$filters_string = implode(" AND ", $filters);
			$query->createFilterQuery('filter_query')->setQuery($filters_string);
			//printf('<pre>%s</pre>',$filters_string);
		}

		if (empty($term)) {
			$query->setQuery('*');
		} else {
			$query->setQuery($term);
		}
		$query->setRows($this->maxResults);
		$query->setStart($start)->setRows($this->resultsPerPage);
		$query->setFields(array('id', 'opac1', 'label'));

	}

























	function search($term, $filters, $start = null) {
		//$this->solr_search_manif($term, $filters, $start);
		//$this->solr_search_work($term, $filters, $start);
		$this->solr_search_all($term, $filters, $start);
		return $this->data;
	}


	private function solr_search_all($term, $filters, $start=null) {
		$start = empty($start)?$this->start : $start;


		//SORT_ASC ->addSortField('name', Solarium_Query_Select::SORT_ASC)
		$sort_field = null;
		$init_record_type = null;
		if (isset($filters['record_type'])) {
			$init_record_type = $filters['record_type'];
		} else {
			$init_record_type = 'all';
		}
		$query_manif_flag = ($init_record_type == 'all' || $init_record_type == 'record_type:"manifestation"' || $init_record_type == 'record_type:"work"');

		$this->data['moreFacetsNum'] = Config::get('arc.MORE_FACETS_NUM', 5);
		$this->data['subjectsResultsetNum'] = Config::get('arc.SUBJECTS_RESULTSET_NUM', 30);
		$this->data['start'] = $start;

		if (empty($term)){
			$sort_field = 'create_dt';
		}

		$record_type  = isset($filters['record_type'])?$filters['record_type'] : 'record_type:"work"';



		$search_manif = ($record_type =='record_type:"manifestation"');

		//printf("<pre> INIT RECORD_TYPE: %s\n RECORD_TYPE: %s\n MANIF_QUERY: %s\n SEARCH_MANIF:%s </pre>",$init_record_type,$record_type, $query_manif_flag ? 'TRUE':'FALSE', $search_manif ? 'TRUE':'FALSE');

		$filtersW = array_values($filters);
		unset($filters['record_type']);
		unset($filters['-record_type']);
		$filtersM = array_values($filters);
		$filtersM[] =  'record_type:"manifestation"';

		$queryW = $this->client->createSelect();
		$queryM = $this->client->createSelect();

		$this->setupQuery($queryW,$term,$filtersW,$start);
		$this->setupQuery($queryM,$term,$filtersM,$start);

		// add stats settings
		$stats = $queryW->getStats();
		$stats->createField('num_of_manifestations');
		$stats->createField('num_of_digital_items');

		// create facets
		$facetSetW = $queryW->getFacetSet();
		$facetSetM = $queryM->getFacetSet();

		$facetSetW->createFacetField('record_type')->setField('record_type');

		$facetSetW->createFacetField('authors')->setField('authors');
		$facetSetW->createFacetField('authors_with_ids')->setField('authors_with_ids');
		$facetSetW->createFacetField('subjects')->setField('subjects');

		$facetSetM->createFacetField('subjects_manif')->setField('subjects_manif');
		$facetSetM->createFacetField('publication_places')->setField('publication_places');
		$facetSetM->createFacetField('publication_places_with_ids')->setField('publication_places_with_ids');
		$facetSetM->createFacetField('publication_types')->setField('publication_types');
		$facetSetM->createFacetField('publishers')->setField('publishers');
		$facetSetM->createFacetField('publishers_with_ids')->setField('publishers_with_ids');
		$facetSetM->createFacetField('digital_item_types')->setField('digital_item_types');
		$facetSetM->createFacetField('languages')->setField('languages');


		if ($search_manif) {
			$this->setupHighlighting($queryM);
		} else {
			$this->setupHighlighting($queryW);
		}

		if (!empty($sort_field)){
			Log::info("ADD SORT CREATE_DT");
			$queryW->addSort('create_dt', $queryW::SORT_DESC);
			$queryM->addSort('create_dt', $queryM::SORT_DESC);
		}
		// get results
		$resultsetW = $this->client->select($queryW);
		if ($query_manif_flag ) {
			$resultsetM = $this->client->select($queryM);
		}


		$facetHasResults = function($facet){
			foreach ($facet as $r) {
				if ($r > 0) { return 1; };
			}
			return null;
		};



		$record_type_facet = $resultsetW->getFacetSet()->getFacet('record_type');
		$authors_facet = $resultsetW->getFacetSet()->getFacet('authors');
		$authors_with_ids_facet = $resultsetW->getFacetSet()->getFacet('authors_with_ids');
		$subjects_facet = $resultsetW->getFacetSet()->getFacet('subjects');
		$this->data['record_type_facet'] = $record_type_facet;
		$this->data['authors_facet'] = $authors_facet;
		$this->data['authors_with_ids_facet'] = $authors_with_ids_facet;
		$this->data['subjects_facet'] = $subjects_facet;

		$has_subjects = $facetHasResults($subjects_facet);
		$has_authors = $facetHasResults($authors_facet);
		$has_authors_with_ids = $facetHasResults($authors_with_ids_facet);
		$this->data['has_subjects'] = $has_subjects;
		$this->data['has_authors'] = $has_authors;
		$this->data['has_authors_with_ids'] = $has_authors_with_ids;


		if ($query_manif_flag) {
			$subjects_manif_facet = $resultsetM->getFacetSet()->getFacet('subjects_manif');
			$publication_places_facet = $resultsetM->getFacetSet()->getFacet('publication_places');
			$publication_places_with_ids_facet = $resultsetM->getFacetSet()->getFacet('publication_places_with_ids');
			$publication_types_facet = $resultsetM->getFacetSet()->getFacet('publication_types');
			$publishers_facet = $resultsetM->getFacetSet()->getFacet('publishers');
			$publishers_with_ids_facet = $resultsetM->getFacetSet()->getFacet('publishers_with_ids');
			$digital_item_types_facet = $resultsetM->getFacetSet()->getFacet('digital_item_types');
			$languages_facet = $resultsetM->getFacetSet()->getFacet('languages');
			$this->data['subjects_manif_facet'] = $subjects_manif_facet;
			$this->data['publication_places_facet'] = $publication_places_facet;
			$this->data['publication_places_with_ids_facet'] = $publication_places_with_ids_facet;
			$this->data['publication_types_facet'] = $publication_types_facet;
			$this->data['publishers_facet'] = $publishers_facet;
			$this->data['publishers_with_ids_facet'] = $publishers_with_ids_facet;
			$this->data['digital_item_types_facet'] = $digital_item_types_facet;
			$this->data['languages_facet'] = $languages_facet;

			$has_subjects_m = $facetHasResults($subjects_manif_facet);
			$has_publication_places = $facetHasResults($publication_places_facet);
			$has_publication_places_with_ids = $facetHasResults($publication_places_with_ids_facet);
			$has_publication_types = $facetHasResults($publication_types_facet);
			$has_publishers = $facetHasResults($publishers_facet);
			$has_publishers_with_ids = $facetHasResults($publishers_with_ids_facet);
			$has_digital_item_types = $facetHasResults($digital_item_types_facet);
			$has_languages = $facetHasResults($languages_facet);
			$this->data['has_subjects_m'] = $has_subjects_m;
			$this->data['has_publication_places'] = $has_publication_places;
			$this->data['has_publication_places_with_ids'] = $has_publication_places_with_ids;
			$this->data['has_publication_types'] = $has_publication_types;
			$this->data['has_publishers'] = $has_publishers;
			$this->data['has_publishers_with_ids'] = $has_publishers_with_ids;
			$this->data['has_digital_item_types'] = $has_digital_item_types;
			$this->data['has_languages'] = $has_languages;

		} else {
			$this->data['has_subjects_m'] = false;
			$this->data['has_publication_places'] = false;
			$this->data['has_publication_places_with_ids'] = false;
			$this->data['has_publication_types'] = false;
			$this->data['has_publishers'] = false;
			$this->data['has_publishers_with_ids'] = false;
			$this->data['has_digital_item_types'] = false;
			$this->data['has_languages'] = false;

			$this->data['subjects_manif_facet'] = array();
			$this->data['publication_places_facet'] = array();
			$this->data['publication_places_with_ids_facet'] = array();
			$this->data['publication_types_facet'] = array();
			$this->data['publishers_facet'] = array();
			$this->data['publishers_with_ids_facet'] = array();
			$this->data['digital_item_types_facet'] = array();
			$this->data['languages_facet'] = array();


		}


		// get stats results
		$statsResult = $resultsetW->getStats();
		//num of manifestations && DigitalItems
		$numManifsFound = 0;
		$numDigitalItemsFound = 0;
		foreach ($statsResult as $field) {
			if ($field->getName() == 'num_of_manifestations') {
				$numManifsFound = $field->getSum();
			}
			if ($field->getName() == 'num_of_digital_items') {
				$numDigitalItemsFound = $field->getSum();
			}
		}
		$this->data['numManifsFound'] = $numManifsFound;
		$this->data['numDigitalItemsFound'] = $numDigitalItemsFound;
		/////////////////////////////////


		$subjectsResultset = $this->_solr_search_similar_subjects($term);
		$this->data['subjectsResultset'] = $subjectsResultset;
		if ($search_manif){
			$resultset = $resultsetM;
		} else {
			$resultset = $resultsetW;
		}
		//count
		$total_cnt = $resultset->getNumFound();
		$numPages = ceil($total_cnt / $this->resultsPerPage);
		$this->data['resultset'] = $resultset;
		$this->data['total_cnt'] = $total_cnt;
		$this->data['numPages'] = $numPages;


		// get highlighting results
		$highlighting = $resultset->getHighlighting();
		$this->data['highlighting'] = $highlighting;
		///////////////////////////


	}



}
