<?php

class SearchController extends BaseController
{


	function solr_search(){
		Log::info("#SearchController@solr_search");

		//SEARCH VAR
		$m = get_get('m');
		if (empty($m)) {
			$m = 's';
		}

		$submit = get_get('submit');
		$term = get_get('term');
		$l = get_get('l');
		$y = get_get('y');
		$a = get_get('a');
		$p = get_get('p');
		$subj = get_get('subj');
		$display_lang_select_flag = variable_get('arc_search_display_lang_select');
		$publication_places_ids = get_get('publication_places_ids');
		$publishers_ids = get_get('publishers_ids');
		$subjects_ids = get_get('subjects_ids');

		$list_mode = null;
		$f_label = null;
		if (!empty($publication_places_ids)){
			$list_mode = 'ppl';
			$f_label = PDao::getItemLabel($publication_places_ids);
		}else if(!empty($publishers_ids)){
			$list_mode = 'pub';
			$f_label = PDao::getItemLabel($publishers_ids);
		}else if(!empty($subjects_ids)){
			$list_mode = 'subj';
			$f_label = PDao::getItemLabel($subjects_ids);
		}


		$hide_search_form = false;
		if (!empty($term)) {
			$hide_search_form = true;
		} else if (empty($term) && $submit) {
			$hide_search_form = true;
		}

		// pagination
		$start = PUtil::reset_int(get_get("start"), 0);


		$solr_filters_def = array(
			'manifestation'=>array('subjects_manif','publication_places','publication_places_with_ids','publication_types','publishers','publishers_with_ids','digital_item_types','languages'),
//			'person'=>array(),
//			'organization'=>array(),
//			'concept'=>array(),
//			'place'=>array(),
//			'event'=>array(),
			'work'=>array('record_type','authors','subjects'),
		);


		$f_rectype = Input::has('record_type');
		$f_authors = Input::has('authors');
		$f_authors_with_ids = Input::has('authors_with_ids');
		$f_subjects = Input::has('subjects');
		$f_subjects_m = Input::has('subjects_manif');
		$f_subjects_ids = Input::has('subjects_ids');
		$f_pubplaces = Input::has('publication_places');
		$f_pubplaces_ids = Input::has('publication_places_ids');
		$f_pubplaces_with_ids = Input::has('publication_places_with_ids');
		$f_pubtypes = Input::has('publication_types');
		$f_publishers = Input::has('publishers');
		$f_publishers_ids = Input::has('publishers_ids');
		$f_publishers_with_ids = Input::has('publishers_with_ids');
		$f_digtypes = Input::has('digital_item_types');
		$f_langs = Input::has('languages');

		$has_filter = $f_rectype || $f_authors || $f_authors_with_ids ||  $f_subjects || $f_subjects_m || $f_subjects_ids || $f_pubplaces || $f_pubplaces_ids || $f_pubplaces_with_ids || $f_pubtypes || $f_publishers || $f_publishers_ids || $f_publishers_with_ids || $f_digtypes || $f_langs;

		// add filters in case the page has been resubmitted by facet selection
		$filters_w = array();

		$addFilter = function ($solrkey, $value) use (&$filters_w) {
			//$filters_w[] = sprintf('%s:"%s"',$solrkey,$value);
			$f = sprintf('%s:"%s"', $solrkey, $value);;
			$filters_w[$solrkey] = $f;
		};

		if (!$has_filter) {
			$addFilter('-record_type', 'manifestation');
		} else {
			$record_type = null;
			if ($f_pubplaces || $f_pubplaces_ids || $f_pubplaces_with_ids || $f_publishers || $f_publishers_ids || $f_publishers_with_ids || $f_subjects_ids || $f_pubtypes || $f_langs || $f_subjects_m) {
				$record_type = 'manifestation';
			} else {
				$record_type = Input::get('record_type');
			}

			if (empty($record_type)) {
				$record_type = 'work';
			}

			if (!empty($record_type)) {
				$addFilter('record_type', $record_type);
			}

			if ($f_pubplaces) {
				$addFilter('publication_places', Input::get('publication_places'));
			}
			if ($f_pubplaces_ids) {
				$addFilter('publication_places_ids', Input::get('publication_places_ids'));
			}
			if ($f_pubplaces_with_ids) {
				$addFilter('publication_places_with_ids', Input::get('publication_places_with_ids'));
			}
			if ($f_authors) {
				$addFilter('authors', Input::get('authors'));
			}
			if ($f_authors_with_ids) {
				$addFilter('authors_with_ids', Input::get('authors_with_ids'));
			}
			if ($f_subjects) {
				$addFilter('subjects', Input::get('subjects'));
			}
			if ($f_subjects_m) {
				$addFilter('subjects_manif', Input::get('subjects_manif'));
			}
			if ($f_subjects_ids) {
				$addFilter('subjects_ids', Input::get('subjects_ids'));
			}
			if ($f_pubtypes) {
				$addFilter('publication_types', Input::get('publication_types'));
			}
			if ($f_publishers) {
				$addFilter('publishers', Input::get('publishers'));
			}
			if ($f_publishers_ids) {
				$addFilter('publishers_ids', Input::get('publishers_ids'));
			}
			if ($f_publishers_with_ids) {
				$addFilter('publishers_with_ids', Input::get('publishers_with_ids'));
			}
			if ($f_digtypes) {
				$addFilter('digital_item_types', Input::get('digital_item_types'));
			}
			if ($f_langs) {
				$addFilter('languages', Input::get('languages'));
			}
		}

		//echo('<pre>');print_r($filters_w); echo('</pre>');

		$solrSearch = new SolrSearch();
		$data = $solrSearch->search($term, $filters_w, $start);

		//SEARCH VAR
		$data['term'] = $term;
		$data['stype'] = $m;
		$data['l'] = $l;
		$data['y'] = $y;
		$data['a'] = $a;
		$data['p'] = $p;
		$data['subj'] = $subj;
		$data['hide_search_form'] = $hide_search_form;
		$data['list_mode'] = $list_mode;
		$data['f_label'] = $f_label;
		$data['display_lang_select_flag'] = $display_lang_select_flag;

		//RESULTS VAR
		$data['relation_work_wholepart_map'] = Setting::get('relation_work_wholepart_map');

		return $this->show($data);

	}




	function search() {

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


}
