<?php

class SearchController extends BaseController
{


  public function solr2_search() {
    // $input_all = Input::all();

    $m = 's';
    $submit = get_get('submit');
    $term = get_get('term');
    $l = get_get('l');
    $y = get_get('y');
    $a = get_get('a');
    $p = get_get('p');

    $hide_search_form = false;
    if (!empty($term)) {
      $hide_search_form = true;
    } else if (empty($term) && $submit) {
      $hide_search_form = true;
    }

    $emptyTermFlag = empty($term);
    //PUtil::logRed("TERM1: " . $term);


    $page = PUtil::reset_int(get_get("page"), 1) < 0 ? 1 : PUtil::reset_int(get_get("page"), 1);
    $limit = Config::get('arc.PAGING_LIMIT');
    $start = $page * $limit - $limit;
//     $start = PUtil::reset_int(get_get("start"), 0);


    $channels = Config::get('arc.SOLR_CHANNELS');
    $solr_queries_def = Config::get('arc.SOLR_QUERIES_DEF');
    $solr_facetes_def = Config::get('arc.SOLR_FACETES_DEF');
    $empty_term_search_token = Config::get('arc.EMPTY_TERM_SEARCH_TOKEN');
    $term_search_token = Config::get('arc.TERM_SEARCH_TOKEN');


//     $channels = array(
//     		'A'=>array(),
//     		'R'=>array('result_query'=>true),
//     		'W'=>array('filter'=>'object_type:auth-work', 'level'=>1),
//     		'M'=>array('filter'=>'(object_type:auth-manifestation OR form_type:periodic)', 'level'=>2),
//     );

//#Version2
//     $solr_facetes_def = array(
//       'cat_l1'=> array(
//         'solr_key'=>'a_categories_l1','channel'=>'R',
//         'level_overights'=>array(
//           0=>array('solr_key'=>'a_categories_l1','channel'=>'A' ),
//           1=>array('solr_key'=>'a_categories_l1', 'result_filter'=>'(object_type:auth-work OR object_type:auth-manifestation OR object_type:periodic )'),
//           2=>array('solr_key'=>'m_categories_l1'),
//         ),
//       ),
//       'cat_l2'=> array(
//         'solr_key'=>'a_categories_l2','channel'=>'R', 'display_on_facete' =>'cat_l1' ,
//         'level_overights'=>array(
//           0=>array('solr_key'=>'a_categories_l2','channel'=>'A' ),
//           1=>array('solr_key'=>'a_categories_l2', 'result_filter'=>'(object_type:auth-work OR object_type:auth-manifestation OR object_type:periodic )'),
//           2=>array('solr_key'=>'m_categories_l2'),
//         ),
//       ),
//       'form_type'=> array('solr_key'=>'form_type', 'channel'=>'R', 'tr'=>true ,
//         'level_overights'=>array( 0=>array('channel'=>'A'  )),
//       ),
//       'publisher'=> array('solr_key'=>'publishers', 'channel'=>'R' ,'set_level_to'=>2, 'result_filter'=>'(object_type:auth-manifestation OR form_type:periodic)', 'unset_input'=>array('form_type'=>'work'),
//         'level_overights'=>array(0=>array('channel'=>'M', ))
//       ),
//       'pubplace'=> array('solr_key'=>'pubplaces', 'channel'=>'R' ,'set_level_to'=>2, 'result_filter'=>'(object_type:auth-manifestation OR form_type:periodic)', 'unset_input'=>array('form_type'=>'work'),
//         'level_overights'=>array(0=>array('channel'=>'M', ))
//       ),
//       'author'=> array('solr_key'=>'authors', 'channel'=>'R' , 'result_filter'=>'(object_type:auth-work)', 'unset_input'=>array('form_type'=>'book'),
//       'level_overights'=>array(0=>array('channel'=>'W', ))
//       ),
//       'owner'=> array('solr_key'=>'lawyer','channel'=>'R','set_level_to'=>2,
//         'level_overights'=>array(0=>array('channel'=>'A', ))
//       ), //'result_filter'=>'(object_type:auth-manifestation)'
//     );

//      $solr_queries_def = array(
//        'publishers_ids'=>array('solr_key'=>'publishers_id', 'filter_var'=>FILTER_VALIDATE_INT, 'label_prefix'=>'manifestations with publisher','set_level_to'=>2 ),
//        'publication_places_ids'=>array('solr_key'=>'pubplaces_id', 'filter_var'=>FILTER_VALIDATE_INT, 'label_prefix'=>'manifestations issued under','set_level_to'=>2 ),
// //        'subjects_ids'=>array('solr_key'=>'subjects_id', 'filter_var'=>FILTER_VALIDATE_INT, 'label_prefix'=>'manifestations with subject' ),
//      );


//#Version1
    //'r_koko'=> array('solr_key'=>'form_type', 'channel'=>'R', 'tr'=>true,  ,'result_filter'=>'(form_type:issue)'),
    //'((object_type:auth-manifestation OR form_type:periodic) OR result_filter'=>'(form_type:issue))';
//
//      $solr_facetes_def2 = array(
//      //'record_type'=>array('solr_key'=>'record_type', 'channel'=>'A'),
//
////    'categories_l1'=> array('solr_key'=>'categories_l1','channel'=>'A'),
//      'w1_categories_l1'=> array('solr_key'=>'w_categories_l1','channel'=>'A'),
////      'w2_categories_l1'=> array('solr_key'=>'w_categories_l1','channel'=>'W'),
//      'm1_categories_l1'=> array('solr_key'=>'m_categories_l1','channel'=>'A'),
////      'm2_categories_l1'=> array('solr_key'=>'m_categories_l1','channel'=>'M'),
//
//
//  //    'categories_l2'=> array('solr_key'=>'categories_l2','channel'=>'A' ),//'display_on_facete' =>'categories_l1'
//      'w1_categories_l2'=> array('solr_key'=>'w_categories_l2','channel'=>'A' ),
////      'w2_categories_l2'=> array('solr_key'=>'w_categories_l2','channel'=>'W' ),
//      'm1_categories_l2'=> array('solr_key'=>'m_categories_l2','channel'=>'A' ),
////      'm2_categories_l2'=> array('solr_key'=>'m_categories_l2','channel'=>'M' ),
//
//
//      'form_type_all'=> array('solr_key'=>'form_type', 'channel'=>'A' , 'tr'=>true),
//      'form_type'=> array('solr_key'=>'form_type', 'channel'=>'R', 'tr'=>true, ),
//
//      //'w_publisher'=> array('solr_key'=>'publishers', 'channel'=>'W', 'link'=>'m_publisher'), //'filter'=>'object_type:auth-manifestation OR form_type:periodic'
//      'publisher'=> array('solr_key'=>'publishers', 'channel'=>'M'), //'filter'=>'object_type:auth-manifestation OR form_type:periodic'
//      'i_publisher'=> array('solr_key'=>'i_publishers', 'channel'=>'M','display_on_form_type'=>array('issue')),
//      //'w_pubplace'=> array('solr_key'=>'pubplaces', 'channel'=>'W', 'link'=>'m_pubplace'),
//      'pubplace'=> array('solr_key'=>'pubplaces', 'channel'=>'M'),
//      'i_pubplace'=> array('solr_key'=>'i_pubplaces', 'channel'=>'M','display_on_form_type'=>array('issue')),
//
//      //'r_author'=> array('solr_key'=>'authors', 'channel'=>'R'),
//      'w_author'=> array('solr_key'=>'authors', 'channel'=>'W'),
//      //'m_author'=> array('solr_key'=>'authors', 'channel'=>'M'),
//      'owner'=> array('solr_key'=>'lawyer','channel'=>'A'),
//
////      'subject'=>array('solr_keys'=>array('w'=>'w_subjects','m'=>'m_subjects')),
////      'authors'=>array('solr_keys'=>array('w'=>'w_authors','m'=>'m_authors')),
////      'subjects_all'=> array('rt'=>'_all', 'q'=>'a'),
////      'subjects'=> array('rt'=>'work' , 'q'=>'w'),
////      'subjects_manif' => array('rt'=>'manifestation', 'q'=>'m', 'display_on_form_type'=>array('book')),
////      'lawyer_with_ids'=> array('q'=>'m', 'display_on_form_type'=>array('book')),
////      'w_authors'=> array('q'=>'a'), //WORK
////      'm_authors'=> array('q'=>'a' , 'display_on_form_type'=>array('book')), //MANIF
////      //'publication_types'=> array('rt'=>'manifestation', 'q'=>'m', 'display_on_form_type'=>array('book')),
////      'w_publishers'=> array('q'=>'a', 'display_on_form_type'=>array('work')),
////      'w_pubplaces'=> array( 'q'=>'a', 'display_on_form_type'=>array('work')),
////      'm_publishers'=> array('q'=>'a', 'display_on_form_type'=>array('book')),
////      'm_pubplaces'=> array( 'q'=>'a', 'display_on_form_type'=>array('book')),
////      'languages'=> array('rt'=>'manifestation', 'q'=>'m', 'display_on_form_type'=>array('book')),
//      //List mode facetes
////      'publication_places_ids'=>array('q'=>'m', 'rt'=>'manifestation', 'list_mode'=>'list_id', 'label_prefix'=>'issued under'),
////      'publishers_ids'=>array('q'=>'m','rt'=>'manifestation', 'list_mode'=>'list_id', 'label_prefix'=>'with publisher'),
//      //'subjects_ids'=>array('q'=>'m', 'rt'=>'manifestation', 'list_mode'=>'list_id', 'label_prefix'=>'with subject'),
//
//    );

    $display_mode = 'normal';
    $list_id = null;
    $list_label_prefix = null;

    $filters=array();
    foreach ($channels as $ch=>$ch_params){
      $filter = new SolrSearchFilter();
      $filters[$ch] = $filter;
      if (isset($ch_params['filter'])){
        $filter->addTokenString($ch_params['filter']);
      }
      //$channels[$ch]['_filter'] = $filter;
    }


    $isFacetedQueryFlag = false;
    $level = 0;
    $result_token_sep = '';
    $result_token = '';
    $get_params = Input::all();
    //print_r($get_params);

    //LEVEL
    foreach ($get_params as $k=>$v) {
      $params = null;
      if (isset($solr_queries_def[$k])) {
        $params = $solr_queries_def[$k];
      } elseif (isset($solr_facetes_def[$k])) {
        $params = $solr_facetes_def[$k];
      }
      if (!empty($params)){
        $ch_level = isset($params['set_level_to']) ? $params['set_level_to'] : 1;
        if ($ch_level > $level) {
          $level = $ch_level;
        }
      }

    }


    foreach ($solr_facetes_def as $k => $params){
      if (isset($params['level_overights']) && isset($params['level_overights'][$level])){
        $overights = $params['level_overights'][$level];
        foreach ($overights as $pk=>$pv){
          $params[$pk]=$pv;
        }
        $solr_facetes_def[$k]=$params;
      }
      //SOLR DEBUG
      if (isset($params['solr_key'])){
        PUtil::logRed($k . ' solr_key: ' . $params['solr_key'] . ' :: ' . $params['channel']);
      };
    }


    $solr_query_flag = false;
    foreach ($get_params as $k=>$v){
      if (isset($solr_facetes_def[$k])) {
        $isFacetedQueryFlag = true;
        $params = $solr_facetes_def[$k];
//        if (isset($params['level_overights']) && isset($params['level_overights'][$level])){
//          $overights = $params['level_overights'][$level];
//          foreach ($overights as $pk=>$pv){
//            $params[$pk]=$pv;
//          }
//          $solr_facetes_def[$k]=$params;
//        }


        $ch_level = isset($params['set_level_to']) ? $params['set_level_to'] :1;
        if (isset($params['result_filter']) && $ch_level >= $level){
          $result_filter = $params['result_filter'];
          $result_token .= $result_token_sep . $result_filter ;
          $result_token_sep = ' OR ';
        }
//        if (isset($params['channel'])){
//          $ch = $params['channel'];
//          $channel = $channels[$ch];
//          if (isset($channel['filter']) && isset($channel['level'])) {
//            $ch_level =$channel['level'];
//            //$filters['R']->addTokenString($channel['filter']);
//            if ($ch_level > $level){
//              $level = $ch_level;
//              $result_token = $channel['filter'];
//            }
//          }
//        }
      } elseif (isset($solr_queries_def[$k])){
        $solr_query_flag  = true;
        $params = $solr_queries_def[$k];
        $solr_key = $params['solr_key'];
        if (isset($params['filter_var'])){
          $v = filter_var($v,$params['filter_var']);
        }
        if (! empty($v)) {
          $display_mode = 'list_id';
          $list_id = $v;
          $list_label_prefix = $params['label_prefix'];
          foreach ($filters as $filter) {
            //$filter->addTokenString($solr_key . ':[' . $v . ' TO '. $v . ']');
            $filter->addTokenMatch($solr_key, $v);
          }
        }
      }
    }

    $initQueryFlag = false;
    if (!$solr_query_flag) {
      if (!$emptyTermFlag && !$isFacetedQueryFlag) {
        //TODO: veltiosi gia ta periodika
        $filters['R']->addTokenString($term_search_token);
      } elseif ($emptyTermFlag && !$isFacetedQueryFlag) {
        $initQueryFlag = true;
        $filters['R']->addTokenString($empty_term_search_token);
      }
    }

    //SOLR DEBUG
    PUtil::logYellow("RESULT LEVEL:" . $level);
    PUtil::logYellow("RESULT TOKEN:" . $result_token);
    if (! empty($result_token)){
      $result_token = '(' . $result_token . ')';
      $filters['R']->addTokenString($result_token);
    }


    foreach ($solr_facetes_def as $facete_name => $facete_params){
      if (! isset($facete_params['solr_key'])){
        trigger_error("solr_key missing from facate " . $facete_name);
      }
      $solr_key = $facete_params['solr_key'];
      if (Input::has($facete_name)){
        $value =Input::get($facete_name);
        foreach ($channels as $ch=>$ch_params){
          $filters[$ch]->addParam($solr_key,$value);
        }
      }
    }

    $solrSearch = new SolrSearch();
    $data = $solrSearch->search2($channels, $solr_facetes_def, $filters, $term, $start);
    $formTypes = $data['formTypes'];

    $list_label = null;
    if ( $display_mode == 'list_id' && !empty($list_id)){
      //$list_label = trChoise($rt.'s',$data['total_cnt']).' '.tr($list_label_prefix) . ' <b>"' . PDao::getItemLabel($list_id).'"</b>';
      $list_label =  trChoise($list_label_prefix, $data['total_cnt'])  .'  <b>' . PDao::getItemLabel($list_id) .'</b>';
    }


    //PUtil::logRed("TERM2: " . $term);
    //SEARCH VAR
    $data['term'] = $term;
    $data['stype'] = $m;
    $data['l'] = $l;
    $data['y'] = $y;
    $data['a'] = $a;
    $data['p'] = $p;

    $data['page']  = $page;
    $data['limit']  = $limit;


    $blade_solr_search = Config::get('arc.BLADE_SOLR_SEARCH');
    $blade_solr_facete_conf = Config::get('arc.BLADE_SOLR_FACETE');
    $blade_solr_facete_sr_conf = Config::get('arc.BLADE_SOLR_FACETE_SR');

    $view = View::make('public.'.$blade_solr_search);
    $facete_names = array();
    $facete_names_sr = array(); #Sreen-Reader
    $facete_rs = $data['facetes'];

    $facete_names_top = array();

    foreach ($facete_rs as $facete_rs_key=>$facete) {
      $facete_params = $facete['params'];
      $facete_name_naked[] = $facete_rs_key;

//      if (isset($facete_params['display_on_init'])) {
//        $displayOnInitFlag = $facete_params['display_on_init'];
//        if ($displayOnInitFlag && !$initQueryFlag) {
//          PUtil::logRed("INIT##1");
//          continue;
//        }
//        if (!$displayOnInitFlag && $initQueryFlag) {
//          PUtil::logRed("INIT##2");
//          continue;
//        }
//      }

      if (isset($facete_params['display_on_facete']) && ! Input::has($facete_params['display_on_facete'])  && ! Input::has($facete_rs_key) ){
          continue;
      }

      $blade_solr_facete = Config::get('arc.BLADE_SOLR_FACETE_FOR_' . $facete_rs_key, $blade_solr_facete_conf);
      $blade_solr_facete_sr = Config::get('arc.BLADE_SOLR_FACETE_FOR_' . $facete_rs_key, $blade_solr_facete_sr_conf);

      $facete_lines = $facete['results'];
      $facete_name = $facete['name'];
      //PUtil::logGreen("NAME: " . $facete_name . ' KEY: ' . $facete_rs_key);

      $facete_link =isset($facete_params['link']) ? $facete_params['link'] : $facete_rs_key;

      $facete_name_blade = $facete_name . '_facete';
      $facete_name_blade_sr = $facete_name . '_facete_sr'; #Sreen-Reader

      //$facete_lines= (isset($data[$facete_name])) ?$data[$facete_name]: array();
      if (empty($facete_params['top_position'])){
      	$facete_names[] = $facete_name_blade;
      }else{
      	$facete_names_top[] = $facete_name_blade;
      }
      //$facete_names[] = $facete_name_blade;
      $facete_names_sr[] = $facete_name_blade_sr; #Sreen-Reader
      $facete_data = array(
        'facete_name' => $facete_rs_key,
        'facete_title' => $facete_rs_key, //$facete_name
        //'facete_title' => $facete_rs_key, //$facete_name
        'facete_link'=>$facete_link,
        'facete_lines' => $facete_lines,
        'facete_params'  => $facete_params,
        'moreFacetsNum' => Config::get('arc.MORE_FACETS_NUM', 5),
      );
      //Log::info(print_r($facete_data,true));
      $view->nest($facete_name_blade, 'public.'.$blade_solr_facete, $facete_data);
      $view->nest($facete_name_blade_sr, 'public.'.$blade_solr_facete_sr, $facete_data); #Sreen-Reader
    }

    $data['facetes']  = $facete_names;
    $data['facetes_top']  = $facete_names_top;
    $data['facetes_sr']  = $facete_names_sr; #Sreen-Reader
    $data['facetes_names']  = $facete_name_naked;
    $data['hide_search_form'] = $hide_search_form;
    $data['list_label'] = $list_label;
    $data['display_mode'] = $display_mode;
    $data['list_id'] = $list_id;
    $data['isFacetedQueryFlag'] = $isFacetedQueryFlag;

   // return $view->with($data);

//		$html = $view->with($data)->render();
//		return $html;

    PUtil::logRed('#########################################################################################');
    PUtil::logRed('#########################################################################################');
    $fview = View::make('layouts.drupal');
    $fview ->content = $view->with($data);
    return $fview;

  }


//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////

	public function search() {

		Log::info("#SearchController@search");

		$l = get_get('l');//title
		$ss = get_get('t');//keyword from simple
		$sss = get_get('tt');//keyword from advance
		$c = PUtil::reset_int(get_get("c"), 0);//silogi //katigoria
		$y = PUtil::reset_int(get_get("y"), null);//year
		$y1 = PUtil::reset_int(get_get("y1"), null);//year1
		$y2 = PUtil::reset_int(get_get("y2"), null);//year2
		$p = get_get('p');//ISBN
		$o = PUtil::reset_int(get_get("o"), null);//offset
		$r = PUtil::reset_int(get_get("r"), 5);//order
		$a = get_get('a');//author
		$subj = get_get('subj');//subject
		$m = get_get('m', 's');//method (simple search,advance search)
		$d = PUtil::reset_int(get_get("d"), 1);//display method (list,thumb1,thumb2)
		$sl = get_get('sl', '0');
		#$k = get_get('k','0'); //katigoria
		$ot = get_get('ot', null);//object-type
		$k = $c;

		$lang = get_lang();
		#echo("LANG:$lang");

		//Log::info("METHOD: " . $m);
		Log::info(print_r($_GET, true));

		if ($y == 0) {
			$y = null;
		}
		if (!empty($y1) || !empty($y2)) {
			$y = null;
		}

		if ($c < 0) {
			$c = 0;
		}


		#####################################################################################
		### FETCH DATA
		#####################################################################################
		$rep = null;

		$params = array(
			'o' => $o,
			'r' => $r,
			'a' => $a,
			'p' => $p,
		);

		if ($m == 'a') {//ADVANCE SEARCH
			$params['method'] = 'adv';
			$params['ss'] = $sss;
			$params['ot'] = $ot;
			$rep = SearchLib2::search_item_simple($params);

// 			$search_string = $params['ss'];
// 		$offset = $params['o'];
// 		$order = $params['r'];

			//$rep = SearchLib2::search_item_simple($ss,$o,$r,$k,$y1,$y2,$sl,$ot);
// 			$rep = SearchLib2::search_item($sss,$c,$o,$y,$p,$l,$a,$r,$y1,$y2);
			#$rep =  search_item_simple($dbh,$ss,$o,$r,$c,$y1,$y2);
		} else {
			$params['method'] = 'simple';
			$params['ss'] = $ss;
			$params['ot'] = $ot;

			$rep = SearchLib2::search_item_simple($params);
			//$rep = SearchLib2::search_item_simple($ss,$o,$r,$k,$y1,$y2,$sl,$ot);
		}

		$results = $rep['results'];
		$r = $rep['order'];
		$total_cnt = $rep['total_cnt'];
		$counters = $rep['counters'];


		$paging_data = array(
			'total_cnt' => $rep['total_cnt'],
			'limit' => $rep['limit'],
			'offset' => $rep['offset'],
			'prev_offset' => $rep['prev_offset'],
			'next_offset' => $rep['next_offset'],
		);


		$data = array(
			'results' => $results,
			'r' => $r,
			'm' => $m,
			'c' => $c,
			'l' => $l,
			'ss' => $ss,
			'sss' => $sss,
			'c' => $c,
			'y' => $y,
			'y1' => $y1,
			'y2' => $y2,
			'p' => $p,
			'o' => $o,
			'a' => $a,
			'subj' => $subj,
			'd' => $d,
			'sl' => $sl,
			'ot' => $ot,
			'lang' => $lang,
			'total_cnt' => $total_cnt,
			'counters' => $counters,
			'paging_data' => $paging_data,
			'relation_work_wholepart_map' => Setting::get('relation_work_wholepart_map'),
		);

		ArcApp::template('public.search_a');
		//Log::info("TEMPLATE: " . 	ArcApp::template());
		return $this->show($data);

	}




//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////












}
