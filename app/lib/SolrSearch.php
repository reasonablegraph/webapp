<?php


class SolrSearchFilter  extends SolrFilter implements  SolrQueryToken {

  public function __construct() {
    parent::__construct(SolrFilter::TYPE_AND);
  }

  private $params =array();
  public function addParam($k,$v){
    $this->params[$k]=$v;
    parent::addTokenMatch($k,$v);
  }

  public function hasParam($k){
    return isset($this->params[$k]);
  }

  public function getParam($k){
    return $this->params[$k];
  }

}



class SolrSearch {

  private $client;
  private $data;
  private $resultsPerPage;
  private $maxResults = 1000;
  private $start = 0;

  public function __construct($opts = null) {
    //Log::info("new SolrSeearch");
    $opts = empty($opts) ? array() : $opts;

    if (isset($opts['start'])) {
      $this->start = $opts['start'];
    }
    if (isset($opts['paging_limit'])) {
      $this->start = $opts['paging_limit'];
    } else {
      $this->resultsPerPage = Config::get('arc.PAGING_LIMIT');
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


  private function setupHighlighting($query) {
    return;
    // setup highlighting
    $hl = $query->getHighlighting();
    $hl->setUseFastVectorHighlighter(true);
    //TODO: NA METAFER8OUN SE CONFIG
    $hl->setFields(array('title_hl', 'secondary_titles_hl', 'descriptions_hl', 'subjects_hl', 'authors_hl', 'places_hl'));
    // 		$hl->setSimplePrefix('<b style="background-color:yellow">');
    // 		$hl->setSimplePostfix('</b>');
  }
//
//  /**
//   * @param $query
//   * @param $term
//   * @param SolrSearchFilter $filters
//   * @param null $start
//   * @return mixed
//   */
//  private function setupQuery($channel_opts, $query, $term, $filters, $start = null) {
//    PUtil::logRed(print_r($channel_opts,true));
//    $start = empty($start) ? $this->start : $start;
//    $resultQueryFlag = isset($channel_opts['result_query'])? $channel_opts['result_query'] : false;
//
//    $edismax = $query->getEDisMax();
//    $edismax->setQueryFields("catch_all_1^5.0 catch_all_2^3.0 catch_all_3^1.0");
//    $edismax->setPhraseBigramFields("catch_all_1^5.0 catch_all_2^3.0 catch_all_3^1.0");
//
//    if (!empty($filters)) {
//      $helper = $query->getHelper();
//      //PUtil::logRed(print_r($filters,true));
////      $filters_string = '';
////      $filter_AND='';
////      foreach ($filters as $fk => $fv){
////        $filters_string .= ($filter_AND . $fk . ':' . $helper->escapeTerm($fv));
////          $filter_AND=' AND ';
////      }
//      $context  = new SolariumQueryContext($query);
//      $filters_string = $filters->createQuery($context);
//      $query->createFilterQuery('filter_query')->setQuery($filters_string);
//      //PUtil::logYellow(sprintf('SOLRsetupQuery: %s',$filters_string));
//    }
//
//    if (empty($term)) {
//      $query->setQuery('*');
//    } else {
//      $query->setQuery($term);
//    }
//    $query->setRows($this->maxResults);
//    $query->setStart($start)->setRows($this->resultsPerPage);
//    //TODO: NA METAFER8OUN SE CONFIG
//    if ($resultQueryFlag) {
//      $query->setFields(array('id', 'opac1', 'label', 'form_type'));
//    } else {
//      $query->setFields(array('id', 'label', 'form_type'));
//    }
//    return $query;
//  }



  /**
   * @param array $channel_opts
   * @param string $term
   * @param SolrSearchFilter $filters
   * @param null $start
   * @return mixed
   */
  private function createQuery($channel_opts, $term, $filters, $start = null) {
    $query = $this->client->createSelect();
    //PUtil::logRed(print_r($channel_opts,true));
    $start = empty($start) ? $this->start : $start;
    $resultQueryFlag = isset($channel_opts['result_query'])? $channel_opts['result_query'] : false;

    $edismax = $query->getEDisMax();
    $edismax->setQueryFields("catch_all_1^5.0 catch_all_2^3.0 catch_all_3^1.0");
    $edismax->setPhraseBigramFields("catch_all_1^5.0 catch_all_2^3.0 catch_all_3^1.0");

    if (!empty($filters)) {
      $helper = $query->getHelper();
      $context  = new SolariumQueryContext($query);
      $filters_string = $filters->createQuery($context);
      $query->createFilterQuery('filter_query')->setQuery($filters_string);
      //PUtil::logYellow(sprintf('SOLRsetupQuery: %s',$filters_string));
    }

    if (empty($term)) {
      $query->setQuery('*');
      $query->addSort('create_dt', $query::SORT_DESC); //???
    } else {
      $query->setQuery($term);
    }
    $query->setRows($this->maxResults);
    $query->setStart($start)->setRows($this->resultsPerPage);
    //TODO: NA METAFER8OUN SE CONFIG - [ERROR]
//     if ($resultQueryFlag) {
      $query->setFields(array('id', 'opac1', 'label', 'form_type'));
//       $query->addSort('id', $query::SORT_ASC);
     // $query->addSort('id', $query::SORT_DESC); //???

//     } else {
//       $query->setFields(array('id', 'label', 'form_type'));
//     }

    return $query;
  }


  public function search($term, $filters, $start = null) {
    //$this->solr_search_manif($term, $filters, $start);
    //$this->solr_search_work($term, $filters, $start);
    $this->solr_search_all($term, $filters, $start);
    return $this->data;
  }


  private function mergeFaceteResults($results_1,$results_2){
//    $results = $resultset['M']->getFacetSet()->getFacet($facete_name);
//    $results_1 = $results;//
//    $results = $resultset['W']->getFacetSet()->getFacet($facete_name);
//    $results_2 = $results;
    //$results = array_merge($results_1,$results_2);
    $results = array();
    foreach ($results_1 as $v => $count) {
      $results[$v] = $count;
    }
    foreach ($results_2 as $v => $count) {
      if (isset($results[$v])) {
        $results[$v] += $count;
      } else {
        $results[$v] = $count;
      }
    }
    arsort($results);
    //$name = $facete_name;
    return $results;
  }


  /**
   * @param $facete_options
   * @param $term
   * @param SolrSearchFilter $filtersR
   * @param SolrSearchFilter $filtersF
   * @param null $start
   * @return array
   */
//$data = $solrSearch->search2($channels, $solr_facetes_def, $filters, $term, $start);
  public function search2($chanel_params, $facete_options, $filters, $term, $start = null) {
    $start = empty($start) ? $this->start : $start;

    $channel_names = array_keys($chanel_params);
    //PUtil::logRed("FILTERS-INIT: " . json_encode($filters, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    //PUtil::logRed("FACETE  OPTS: " . json_encode($facete_options));
    //SORT_ASC ->addSortField('name', Solarium_Query_Select::SORT_ASC)
    $sort_field = null;

    //if (!isset($filters['record_type'])){    }

    $this->data['moreFacetsNum'] = Config::get('arc.MORE_FACETS_NUM', 5);
    $this->data['subjectsResultsetNum'] = Config::get('arc.SUBJECTS_RESULTSET_NUM', 30);

    $sort_field = null; //$sort_field = 'create_dt';
    $this->data['start'] = $start;

    $recordTypeQueryValue = null;
    $recordTypeQuery = false;
    $resultsManifFlag = false;


    $query = array();
    $facetSet = array();
    foreach ($chanel_params as $ch=>$ch_params) {
        $q = $this->createQuery($chanel_params,$term,$filters[$ch],$start);
        $facetSet[$ch] = $q->getFacetSet();
        $query[$ch] = $q;
    }

    //SOLR DEBUG
    foreach ($channel_names as $ch){
      //PUtil::logRed("FILTERS-R: " . json_encode($filtersS['R'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
      PUtil::logYellow("FILTERS-" . $ch. ': '  . $filters[$ch]);
    }


    // add stats settings
    $stats = $query['R']->getStats();
    $stats->createField('num_of_manifestations');
    $stats->createField('num_of_digital_items');
    $stats->createField('num_of_books');
    $stats->createField('num_of_issues');


//    foreach ($channel_names as $ch) {
//      $facetSet[$ch] = $query[$ch]->getFacetSet();
//    }


    //'__form_type'=> array('solr_key'=>'form_type', 'channel'=>'A'),
    $form_type_facete = '__form_type';
    $facetSet['R']->createFacetField($form_type_facete)->setField('form_type');
    foreach ($facete_options as $facete_name => $facete_params) {
      $solr_key = $facete_params['solr_key'];
      if (isset($facete_params['channel'])){
        $ch = $facete_params['channel'];
        $fct = $facetSet[$ch]->createFacetField($solr_key)->setField($solr_key)->setMinCount(1);
        //$fct->setMinCount(1);
      }
//        $q = strtoupper($facete_params['q']);
//        PUtil::logBlue('create facete: ' . $facete_name . '   q: ' . $q .  '   k: ' . $solr_key);
//        $facetSet[$q]->createFacetField($solr_key)->setField($solr_key);
    }


//    if (!empty($sort_field)) {
//      //Log::info("ADD SORT CREATE_DT");
//      if ($resultsManifFlag) {
//        $query['M']->addSort('create_dt', $query['M']::SORT_DESC);
//      } else {
//        $query['W']->addSort('create_dt', $query['W']::SORT_DESC);
//      }
//    }

    $resultset = array();
    $resultfaceteSet = array();
    foreach ($chanel_params as $ch=>$ch_params) {
       // PUtil::log("SOLR SELECT:  " . $ch);
        $resultset[$ch] = $this->client->select($query[$ch]);
        $resultfaceteSet[$ch] = $resultset[$ch]->getFacetSet();
    }

    $form_types = array();
    $results_forms = $resultfaceteSet['R']->getFacet($form_type_facete);
    if (!empty($resultset)) {
      foreach ($results_forms as $v => $count) {
        //PUtil::logGreen("FT: " . $v .  ' : ' . $count);
        if ($count > 0) {
          $form_types[$v] = $count;
        }
      }
    }
    //PUtil::logGreen(print_r($form_types,true));
    $this->data['formTypes'] = $form_types;

    $facetes = array();
    foreach ($facete_options as $facete_name => $facete_params) {
      $solr_key = $facete_params['solr_key'];
//      if (!isset($facete_params['q'])) {
//        trigger_error("facete param q missing");
//      }
//      $q = strtoupper($facete_params['q']);

//      if ($facete_name == 'form_type'){
//        $results = $results_forms;
//      } else {
        $channel = isset($facete_params['channel']) ? $facete_params['channel'] : 'R';
        //PUtil::logBlue("F: " . $channel . ' : ' . $facete_name);
        $results = $resultfaceteSet[$channel]->getFacet($solr_key);
//      }
      //$results = ($facete_name == 'form_type') ? $results_forms : $resultfaceteSet[$channel]->getFacet($solr_key);

//      if (isset($facete_params['tr']) && $facete_params['tr']) {
//        $results_tmp = array();
//        foreach ($results as $v => $count) {
//          if ($facete_params['tr'] == 's') {
//            $results_tmp[trChoise($v . 's', $count)] = $count;
//          } else {
//            $results_tmp[trChoise($v, $count)] = $count;
//          }
//          //$results_tmp[$v] = $count;
//        }
//        $results = $results_tmp;
//      }


      $displayF =true;
      if (isset($facete_params['display_on_form_type'])) {
        $displayF = false;
        $don = $facete_params['display_on_form_type'];
        if(is_array($don)) {
          foreach ($don as $df) {
            if (isset($form_types[$df])) {
              $displayF = true;
              break;
            }
          }
        }
      }


      if ($displayF) {
        $facetes[$facete_name] = array('results' => $results, 'params' => $facete_params, 'name' => $facete_name);
//        PUtil::logGreen("FACETE ADD: " . $facete_name  .  '           :  ' . $solr_key);
//        foreach ($results as $value=>$count){
//          PUtil::log($value . ' : ' . $count);
//        }
      } else {
        PUtil::logRed("FACETE SKIP: " . $facete_name);
      }

    }
    $this->data['facetes'] = $facetes;

    // get stats results
    $statsResult = $resultset['R']->getStats();
    //num of manifestations && DigitalItems
    $numManifsFound = 0;
    $numDigitalItemsFound = 0;
    $numBooksFound = 0;
    $numIssuesFound = 0;
    foreach ($statsResult as $field) {
      if ($field->getName() == 'num_of_manifestations') {
        $numManifsFound = $field->getSum();
      }
      if ($field->getName() == 'num_of_digital_items') {
        $numDigitalItemsFound = $field->getSum();
      }
      if ($field->getName() == 'num_of_books') {
      	$numBooksFound = $field->getSum();
      }
      if ($field->getName() == 'num_of_issues') {
      	$numIssuesFound = $field->getSum();
      }


    }
    $this->data['numManifsFound'] = $numManifsFound;
    $this->data['numDigitalItemsFound'] = $numDigitalItemsFound;
    $this->data['numBooksFound'] = $numBooksFound;
    $this->data['numIssuesFound'] = $numIssuesFound;
    /////////////////////////////////

    $subjectsResultset = $this->_solr_search_similar_subjects($term);
    $this->data['subjectsResultset'] = $subjectsResultset;

//    foreach ($resultset as $ch=>$r){
//      PUtil::logGreen($ch);
//      PUtil::logGreen('---------------');
//      foreach ($r as $document){
//        $id = $document->id;
//        $label = $document->label;
//        $r = json_decode($document->opac1, true);
//        $ot = $r['obj_type'];
//        PUtil::logYellow($ch. " : " . $ot . ' : ' . $id . ' : ' . $label);
//      }
//      PUtil::logGreen('---------------');
//    }

    $res = $resultset['R'];

    //count
    $total_cnt = $res->getNumFound();
    $numPages = ceil($total_cnt / $this->resultsPerPage);
    $this->data['resultset'] = $res;
    $this->data['total_cnt'] = $total_cnt;
    $this->data['numPages'] = $numPages;


    // get highlighting results
    //$highlighting = $res->getHighlighting();
    //$this->data['highlighting'] = $highlighting;
    ///////////////////////////

    //$this->data['facete_options'] = $facete_options;

//    PUtil::logGreen('@solr_data ##############################################################');
//    foreach ($this->data as $k=>$v){
//      PUtil::logGreen($k);
//    }
//    PUtil::logGreen('@/solr_data ##############################################################');
//    PUtil::log("--------------------------------------------------------");
    return $this->data;

  }

}


