<?php
class SearchLib2 {



	// @DocGroup(module="util", group="general", comment="item_list")
	public static function search_item_simple($params) {
	//public static function search_item_simple($search_string, $offset = null, $order = 5, $collection = 0, $y1 = null, $y2 = null, $lang = null,$ot=null) {

			//$rep = SearchLib2::search_item_simple($ss,$o,$r,$k,$y1,$y2,$sl,$ot);
		$search_string = $params['ss'];
		$offset = $params['o'];
		$order = $params['r'];



		Log::info("search_item_simple: " . $search_string);
		$dbh = dbconnect();

		$limit = Config::get('arc.PAGING_LIMIT');

		$next_offset = $offset + $limit;
		$prev_offset = $offset - $limit;
		if ($prev_offset < 0) {
			$prev_offset = 0;
		}

		$ot = $params['ot'];

		$params['qt']=1;
		$params['limit']=$limit;
		list ( $search_string, $order, $offset, $limit, $rows ) =SearchLib2::search_item_m($params);
		//SearchLib2::search_item_m(1, $search_string, $collection, $y1, $y2, $lang, $order, $offset, $limit,$ot);
		$result = $rows;
		$counters = array();
		if (empty($search_string) ) {


			$userName = get_user_name(); // echo("<<#2# $userName ##>> ");
			if (empty($userName)) {
				$status_c = sprintf(" (i.status='%s') ", Config::get('arc.ITEM_STATUS_FINISH'));
			} else {
				$status_c = sprintf(" (i.status='%s' OR i.status='%s') ", Config::get('arc.ITEM_STATUS_FINISH'), Config::get('arc.ITEM_STATUS_PRIVATE'));
			}
			if (Config::get('arc.ARCHIVE_SITE') > 0) {
				$status_c .= sprintf(' AND (site = %s OR site = 0) ', Config::get('arc.ARCHIVE_SITE'));
			}

			$default_item_opac_search = Config::get('arc.default_item_opac_search',array('auth-work'));
			$obj_type_c = implode("' OR obj_type ='", $default_item_opac_search );
// 			$SQL = sprintf("SELECT count(*) from dsd.item2 i WHERE (obj_type = 'auth-work') AND %s   ", $status_c);
			$SQL = sprintf("SELECT count(*) from dsd.item2 i WHERE (obj_type = '%s') AND %s   ",$obj_type_c, $status_c);

			$stmt = $dbh->prepare($SQL);
			$stmt->execute();
			$row = $stmt->fetch();
			$total_count = $row[0];


		} else {
			$params['qt']=2;
			$params['limit']=$limit;
			list ( $search_string, $order, $offset, $limit, $rows ) = SearchLib2::search_item_m($params);
			//SearchLib2::search_item_m(2, $search_string, $collection, $y1, $y2, $lang, $order, $offset, $limit,$ot);

					//2, $search_string, $collection, $y1, $y2, $lang, $order, $offset, $limit,$ot);
			//$total_count = $rows[0][0];
			$total_count = 0;
			foreach ($rows as $row){
				$count = $row['count'];
				$total_count += $count;
				$counters[$row['obj_type']] = $count;
			}


		}

		//$rep = array('method' => 'simple','limit' => $limit,'offset' => $offset,'next_offset' => $next_offset,'prev_offset' => $prev_offset,'order' => $order,'term' => $search_string,'collection' => $collection,'year1' => $y1,'year2' => $y2,'subjects' => null );
		$rep = array('method' => 'simple','limit' => $limit,'offset' => $offset,'next_offset' => $next_offset,'prev_offset' => $prev_offset,'order' => $order,'term' => $search_string,'subjects' => null );

		$rep['results'] = $result;
		$rep['total_cnt'] = $total_count;
		$rep['counters'] = $counters;

// 		$subjects_ok = ARRAY();
// 		$rep['subjects'] = $subjects_ok;
// 		// //

		return $rep;
	}

	// @DocGroup(module="search", group="php", comment="search_item_m (simple search)")
	public static function search_item_m($params) {
	//public static function search_item_m($qt = 1, $search_string, $collection = 0, $y1 = null, $y2 = null, $lang = null, $order = 5, $offset = null, $limit=null,$ot=null) {
		$qt= $params['qt'];

		$order = $params['r'];
		$offset = $params['o'];
		$limit=$params['limit'];

		$ot = $params['ot'];

		$search_string = $params['ss'];
		$method = $params['method'];

		$contributor = isset($params['a']) ?  $params['a'] : null;
		$isbn = isset($params['p']) ?  $params['p'] : null;
		if (!empty($isbn)){
			$isbn = str_replace('-','',$isbn);
			$isbn = str_replace(' ','',$isbn);
		}
		//Log::info("ISBN: " . $isbn);


		//SEARCH  STRING ISBN
		if (preg_match('/[\d\-XxΧχ]+/',$search_string)){
			$search_string = str_replace('-','',$search_string);
		}


		Log::info("search_item_m: " . $qt . ' : ' . $search_string);
		$dbh = dbconnect();

		if (empty($limit)){
			$limit = 100;
		}

		$result_query = true;
		if ($qt == 2) {
			$result_query = false;
		}

		$userName = get_user_name(); // echo("<<#2# $userName ##>> ");
		                             // if (empty($userName)){
		                             // $status_c = sprintf(" i.status in ('%s','%s') ",ITEM_STATUS_FINISH,'hidden');
		                             // } else {
		                             // $status_c = sprintf(" i.status in ('%s','%s','%s') ",ITEM_STATUS_FINISH, ITEM_STATUS_PRIVATE,'hidden');
		                             // }

		$search_string = $search_string == null ? "" : $search_string;
		$search_string = trim($search_string);
		$search_string = str_replace('(', ' ', $search_string);
		$search_string = str_replace(')', ' ', $search_string);
		$search_string = str_replace('!', ' ', $search_string);
		$search_string = str_replace('*', ' ', $search_string);


		$empty_query = (empty($search_string) || $search_string == '' ) ? true : false;
		Log::info("::: " . $search_string . "==" . $empty_query ? ' EMPTY' : ' NOT EMPTY');

		if (empty($order)) {
			$order = 5;
		}

		if ($order == 5 && $empty_query) {
			$order = 1;
		}

		if (empty($offset)) {
			$offset = 0;
		}


// 		if (empty($y1)) {
// 			$y1 = 0;
// 		}
// 		if (empty($y2)) {
// 			$y2 = 0;
// 		}
// 		if ($y2 > 0 && $y2 < $y1) {
// 			$y1 = 0;
// 			$y2 = 0;
// 		}

// 		$year_query = false;
// 		if ($y1 > 0 || $y2 > 0) {
// 			$year_query = true;
// 		}

// 		$browse_query = false;
// 		// if ($empty_query && empty($y1) && empty($y2)){
// 		if ($empty_query) {
// 			// &&
// 			// ($collection == 0 || $collection == DB_COLLECTION_PERIODIKA || $collection == DB_COLLECTION_WEB_SITES || $collection == DB_COLLECTION_EFIMERIDES)){
// 			$browse_query = true;
// 		}

//		$index_query = false;
// 		$index_query = false;
// 		if ($browse_query && empty($collection)) {
// 			$index_query = true;
// 		}

// 		$remove_so = true;
// 		if ($year_query) {
// 			$remove_so = false;
// 		}


	//	Log::info("RESULT QUERY : $result_query \n");
		//Log::info("EMPTY QUERY : $empty_query \n");
//		Log::info("BROWSE QUERY : $browse_query \n");
//		Log::info("INDEX QUERY : $index_query \n");
//		Log::info("YEAR QUERY : $year_query \n");

// 		if (false){
// 			echo("<pre>");
// 			if ($empty_query){ echo("EMPTY QUERY : $empty_query \n"); }
// 			if ($browse_query){echo("BROWSE QUERY : $browse_query \n");}
// 			if ($index_query){echo("INDEX QUERY : $index_query \n");}
// 			if ($year_query){echo("YEAR QUERY : $year_query \n");}
// 			echo("</pre>");
// 		}

		// $ranked_query = false;
		$ORDER_SQL = " i.dt_create desc, i.label ";
		if ($order == 2) {
			$ORDER_SQL = " i.label ";
		} else if ($order == 3) {
			$ORDER_SQL = " i.year desc, i.label ";
		} else if ($order == 4) {
			$ORDER_SQL = " i.year asc, i.label ";
		} else if ($order == 5) {
			if (! $empty_query) {
				// $ranked_query = true;
				$ORDER_SQL = " rank DESC, i.weight, i.label ";
			} else {
				$ORDER_SQL = " i.weight, i.label ";
			}
		}

		if (empty($userName)) {
			$status_c = sprintf(" i.status = '%s' ", Config::get('arc.ITEM_STATUS_FINISH'));

// 			if ($browse_query) {
// 				$status_c = sprintf(" i.status = '%s' ", Config::get('arc.ITEM_STATUS_FINISH'));
// 			} else {
// 				$status_c = sprintf(" i.status in ('%s','%s') ", Config::get('arc.ITEM_STATUS_FINISH'), 'hidden');
// 			}
		} else {
			$status_c = sprintf(" i.status in ('%s','%s') ", Config::get('arc.ITEM_STATUS_FINISH'), Config::get('arc.ITEM_STATUS_PRIVATE'));

// 			if ($browse_query) {
// 				$status_c = sprintf(" i.status in ('%s','%s') ", Config::get('arc.ITEM_STATUS_FINISH'), Config::get('arc.ITEM_STATUS_PRIVATE'));
// 			} else {
// 				$status_c = sprintf(" i.status in ('%s','%s','%s') ", Config::get('arc.ITEM_STATUS_FINISH'), Config::get('arc.ITEM_STATUS_PRIVATE'), 'hidden');
// 			}
		}

// 		$browse_filters = get_browse_filter();
// 		echo("<pre>");
// 		 print_r($browse_filters);
// 		echo("</pre>");
// 		$menu_lines = get_menu_lines();

		$FILTER_SQL = '';
		// if ($collection > 0){
		// $FILTER_SQL .= " AND ? @@ i.fts_catalogs";
		// }

// 		if (Config::get('arc.ARCHIVE_SITE') > 0) {
// 			$FILTER_SQL .= sprintf(' AND (site = %s OR site = 0) ', Config::get('arc.ARCHIVE_SITE'));
// 		}

// 		if ($collection > 0) {
// 			$tmp_arr = get_menu_browse_filter($collection);
// 			if (isset($tmp_arr['SQL_TOKEN'])) {
// 				$filter_sql_token = $tmp_arr['SQL_TOKEN'];
// 			}
// 			if (! empty($filter_sql_token)) {
// 				$FILTER_SQL .= sprintf(' AND ( %s ) ', $filter_sql_token);
// 			}
// 		}

// 		if ($y1 > 0) {
// 			$FILTER_SQL .= " AND year >= ? ";
// 		} elseif ($y1 == - 11) {
// 			$FILTER_SQL .= " AND year is null ";
// 		}
// 		if ($y2 > 0) {
// 			$FILTER_SQL .= " AND year <= ? ";
// 		}

// 		$LANG_SQL = "";
// 		if (! empty($lang)) {
// 			$LANG_SQL = " AND i.lang = ? ";
// 		}




// 		if (!empty($ot)) {
// 					$FILTER_SQL .= " AND obj_type = ? ";
// 		}

 		if (!empty($contributor)){
 			$FILTER_SQL .= ' AND dsd.to_gr_tsquery(?) @@ ' .PgFtsConstants::COLUMN_CONTRIBUTOR;
 		}

 		if (!empty($isbn)){
 			$FILTER_SQL .= ' AND dsd.to_gr_tsquery(?) @@ ' .PgFtsConstants::COLUMN_ISBN;
 		}



// 		SELECT  i.item_id as id, i.jdata, i.obj_type, i.status , i.dt_update, i.obj_type, i.label, i.thumb,  user_create, user_update ,
//ts_rank_cd(fts, q) AS rank
// 		FROM dsd.item2 i,
// 		dsd.to_gr_tsquery(?) as q
// 		WHERE  obj_type not in ('bitstream') AND q @@ i.fts
// 		ORDER BY rank DESC  LIMIT 60


		if ($result_query) {
			if (! $empty_query) {
				Log::info("#Q1 RESULT + NOT EMPTY");
				$SQL = 'SELECT  ';
				$SQL .= Config::get('arc.ITEM_LIST_SQL_FIELDS');
				$SQL .= " ,ts_rank_cd(fts2, q) AS rank ";
				$SQL .= " FROM dsd.item2 i ";
				$SQL .= " ,dsd.to_gr_tsquery(?) as q ";
				$SQL .= " WHERE i.opac_flag ";
				$SQL .= sprintf(" AND %s ", $status_c);
				$SQL .= $FILTER_SQL;
				$SQL .= " AND q @@ i.fts2  ";

				if (!empty($ot)){
					$SQL .= " AND (i.obj_type = '$ot') ";
				}else{
					//restric res obj_type
					$default_item_opac_search = Config::get('arc.item_opac_search',array('auth-work','auth-expression','auth-manifestation','auth-person','auth-organization','auth-family','auth-place','auth-concept','auth-event','auth-genre','auth-object','auth-object_collection','subject-chain','auth-general'));
					$obj_type_c = implode("' OR obj_type ='", $default_item_opac_search );
					$SQL .= " AND (i.obj_type = '$obj_type_c') ";
				}


				$SQL .= "ORDER BY  " . $ORDER_SQL . " limit " . $limit . "  offset ? ";

			} else {
				Log::info("#Q2 RESULT & EMPTY");
				$SQL = 'SELECT  ';
				$SQL .= Config::get('arc.ITEM_LIST_SQL_FIELDS');
				$SQL .= ' FROM dsd.item2 i ';
				$SQL .= " WHERE i.opac_flag ";
				if ($FILTER_SQL == ''){
					$default_item_opac_search = Config::get('arc.default_item_opac_search',array('auth-work'));
					$obj_type_c = implode("' OR obj_type ='", $default_item_opac_search );
					$SQL .= " AND (i.obj_type = '$obj_type_c') ";
// 					$SQL .= " AND (i.obj_type = 'auth-work') ";
				}
				$SQL .= sprintf("AND %s ", $status_c);
				$SQL .= $FILTER_SQL;
				$SQL .= "  ORDER BY  " . $ORDER_SQL . " limit " . $limit . "  offset ? ";
			}
		} else {
			if (! $empty_query) {
				Log::info("#Q3");
				$SQL = 'SELECT  i.obj_type, count(i.obj_type) as count';
				$SQL .= " FROM dsd.item2 i ";
				$SQL .= " ,dsd.to_gr_tsquery(?) as q ";
				$SQL .= " WHERE i.opac_flag ";
				$SQL .= sprintf(" AND %s ", $status_c);
				$SQL .= $FILTER_SQL;
				$SQL .= " AND q @@ i.fts2  ";

				if (!empty($ot)){
					$SQL .= " AND (i.obj_type = '$ot') ";
				}else{
					//restric res obj_type
					$default_item_opac_search = Config::get('arc.item_opac_search',array('auth-work','auth-expression','auth-manifestation','auth-person','auth-organization','auth-family','auth-place','auth-concept','auth-event','auth-genre','auth-object','auth-object_collection','subject-chain','auth-general'));
					$obj_type_c = implode("' OR obj_type ='", $default_item_opac_search );
					$SQL .= " AND (i.obj_type = '$obj_type_c') ";
				}


				$SQL .= 'GROUP BY 1';
			} else {
				Log::info("#Q4");
				$SQL = 'SELECT  i.obj_type, count(i.obj_type) as count ';
				$SQL .= " FROM dsd.item2 i ";
				$SQL .= " WHERE i.opac_flag ";
				$SQL .= sprintf(" %s ", $status_c);
				$SQL .= $FILTER_SQL;
				//$SQL .= $LANG_SQL;
				$SQL .= 'GROUP BY 1';
			}
		}

	//	Log::info($SQL);


// 		SELECT  i.obj_type, count(i.obj_type)
// 		FROM dsd.item2 i
// 		,dsd.to_gr_tsquery('carrol') as q
// 		WHERE i.opac_flag
// 		AND  i.status in ('finish','private','hidden')
// 		AND (site = 1 OR site = 0)
// 		AND q @@ i.fts2
// 		GROUP BY 1;


		// ##############################
		// # DEBUG
		// ##############################

		if (false) {
			echo ("<pre>");

			echo ("1.SEARCH SQL:\n");
			echo ($SQL);
			echo ("\n------------------\n");

			if (! $empty_query) {
				echo ("search_string: " . $search_string . "\n");
			}
// 			if ($collection > 0) {
// 				echo ("collection: " . $collection . "\n");
// 			}
// 			if ($y1 > 0) {
// 				echo ("y1: " . $y1 . "\n");
// 			}
// 			if ($y1 > 0) {
// 				echo ("y2: " . $y2 . "\n");
// 			}

			// print_r($lang);
			if (! empty($lang)) {
				echo ("lang: $lang  ");
			}
			if ($result_query) {
				echo ("offset: " . $offset);
			}
			echo ("</pre>");
		}

		$stmt = $dbh->prepare($SQL);

		$i = 1;
		if (! $empty_query) {
			$stmt->bindParam($i, $search_string);
			$i = $i + 1;

		}
		// if ($collection > 0){
		// $stmt->bindParam($i, $collection);
		// $i = $i + 1;
		// }

// 		if ($y1 > 0) {
// 			$stmt->bindParam($i, $y1);
// 			$i = $i + 1;
// 		}
// 		if ($y2 > 0) {
// 			$stmt->bindParam($i, $y2);
// 			$i = $i + 1;
// 		}

// 		if (! empty($lang)) {
// 			$stmt->bindParam($i, $lang);
// 			$i = $i + 1;
// 		}

// 		if (!empty($ot)) {
//  			$stmt->bindParam($i, $ot);
//  			$i = $i + 1;
// 		}


		if (!empty($contributor)){
			$stmt->bindParam($i, $contributor);
			$i = $i + 1;
		}

		if (!empty($isbn)){
			$stmt->bindParam($i, $isbn);
			$i = $i + 1;
		}


		if ($result_query) {
			$stmt->bindParam($i, $offset);
		}
		$stmt->execute();

		if ($qt == 1){
			$result = array();
			while ($r = $stmt->fetch(PDO::FETCH_ASSOC)){
				$status = $r['status'];
				$dt_update = new DateTime($r['dt_update']);
				if (empty($r['jdata'])){
					$jdata = ARRAY('opac1'=>ARRAY());
				} else {
					$jdata = json_decode($r['jdata'],true);
				}

// 				$manifestations_all  = isset($jdata['manifestations']) ? $jdata['manifestations'] : array();
// 				if (isset($jdata['expressions'])){
// 					foreach ($jdata['expressions'] as $expr){
// 						if (isset($expr['manifestations'])){
// 							$manifestations_all = array_merge($manifestations_all,$expr['manifestations']);
// 						}
// 					}
// 				}

				if (!isset($r['public_title'])){
					$r['public_title'] = array(
					);
				}
				if(!isset($r['public_title']['title'])){
					$r['public_title']['title'] = $r['title'];
				}
				if(!isset($r['public_title']['id'])){
					$r['public_title']['id'] = $r['item_id'];
				}

				$rdata = array_merge($jdata['opac1'], $r);
				$rdata['obj_type_display'] = $r['obj_type'];
				$rdata['label']= $r['title'];
				$rdata['id']= $r['item_id'];

				//$rdata['manifestations_all'] = $manifestations_all;

				$rdata['dt_update'] = $dt_update->format('d/m/Y H:i');

				$result[] = $rdata;
			}
		} else {
			$result = $stmt->fetchAll();
		}

		return array($search_string,$order,$offset,$limit,$result );
	}
}

?>