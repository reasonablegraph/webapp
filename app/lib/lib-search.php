<?php
class SearchLib0 {
	
	/**
	 * advance search
	 * 
	 * @param unknown $dbh        	
	 * @param unknown $search_string        	
	 * @param string $collection        	
	 * @param string $offset        	
	 * @param string $year        	
	 * @param string $place        	
	 * @param string $title        	
	 * @param string $author        	
	 * @param number $order        	
	 * @param string $y1        	
	 * @param string $y2        	
	 * @param string $lang        	
	 * @return multitype:string NULL number Ambigous <string, number> unknown multitype:unknown
	 */
	
	// @DocGroup(module="search", group="php", comment="search_item")
	public static function search_item($search_string, $collection = null, $offset = null, $year = null, $place = null, $title = null, $author = null, $order = 5, $y1 = null, $y2 = null, $lang = null) {
		$dbh = dbconnect();
		
		Log::info("search_item");
		// $userName = get_user_name();
		$search_string = trim($search_string);
		// $fonitiko1 = fonitiko($dbh, $search_string);
		// $ok1 = '%' . $fonitiko1 .'%';
		
		$place = trim($place);
		// $fonitiko2 = fonitiko($dbh, $place);
		// $ok2 = '%' . $fonitiko2 .'%';
		
		$title = trim($title);
		// $fonitiko3 = fonitiko($dbh, $title);
		// $ok3 = '%' . $fonitiko3 .'%';
		
		$author = trim($author);
		// $fonitiko4 = fonitiko($dbh, $author);
		// $ok4 = '%' . $fonitiko4 .'%';
		
		if (! empty($collection)) {
			if ($collection <= 0) {
				$collection = 0;
			}
		}
		
		if (! empty($year)) {
			if ($year <= 1) {
				$year = 0;
			}
		}
		if (empty($y1)) {
			$y1 = 0;
		}
		if (empty($y2)) {
			$y2 = 0;
		}
		if ($y2 > 0 && $y2 < $y1) {
			$y1 = 0;
			$y2 = 0;
		}
		
		$empty_query = false;
		if (empty($search_string) && empty($place) && empty($title) && empty($author)) {
			$empty_query = true;
		}
		
		if (empty($order)) {
			$order = 5;
		}
		if ($order == 5 && $empty_query) {
			$order = 1;
		}
		
		$browse_query = false;
		if (! empty($collection) && empty($year) && empty($search_string) && empty($place) && empty($title) && empty($author) && empty($y1) && empty($y2)) 
		// && ($collection == 0 || $collection == DB_COLLECTION_PERIODIKA || $collection == DB_COLLECTION_WEB_SITES || $collection == DB_COLLECTION_EFIMERIDES)
		{
			$browse_query = true;
		}
		
		$limit = Config::get('arc.PAGING_LIMIT');
		$result = array();
		$next_offset = $offset + $limit;
		$prev_offset = $offset - $limit;
		if ($prev_offset < 0) {
			$prev_offset = 0;
		}
		$rep = array(
				'method' => 'advance',
				'limit' => $limit,
				'offset' => $offset,
				'next_offset' => $next_offset,
				'prev_offset' => $prev_offset,
				'term' => $search_string,
				'collection' => $collection,
				'year' => $year,
				'year1' => $y1,
				'year2' => $y2,
				'place' => $place,
				'title' => $title,
				'author' => $author,
				'order' => $order,
				'subjects' => null );
		
		// PARADIGMA FTS
		// SELECT i.item_id, substring(i.label,0,50), ts_rank_cd(i.fst, q) AS rank from dsd.item2 i, dsd.to_gr_tsquery('example') as q WHERE q @@ i.fst ;
		
		$ORDER_SQL = " i.dt_create desc, i.label ";
		if ($order == 2) {
			$ORDER_SQL = " i.label ";
		} else if ($order == 3) {
			$ORDER_SQL = " i.year desc, i.label ";
		} else if ($order == 4) {
			$ORDER_SQL = " i.year asc, i.label ";
		}
		
		$SQL1 = "SELECT  distinct " . Config::get('arc.ITEM_LIST_SQL_FIELDS');
		// . " distinct i.collection_label, i.label as title, i.year, i.place, i.archive_date, i.item_id, i.collection, i.dt_create, i.bibref, i.thumb, i.thumb1, i.thumb2, i.obj_type, i.issue_aggr, pages, i.issue_cnt "
		$SQL1 .= " FROM  dsd.item2 i ";
		$SQL2 = "SELECT count(*)  FROM dsd.item2 i ";
		if (! empty($search_string)) {
			$SQL1 .= " JOIN dsd.metadatavalue2 m ON ( i.item_id = m.item_id AND m.element  = 'dc:subject:' ) "; // AND m.metadata_field_id in (3,57)
			$SQL2 .= " JOIN dsd.metadatavalue2 m ON ( i.item_id = m.item_id AND m.element  = 'dc:subject:' ) "; // AND m.metadata_field_id in (3,57)
		}
		if (! empty($place)) {
			$SQL1 .= " JOIN dsd.metadatavalue2 m ON ( i.item_id = m.item_id AND m.element = 'ea:publication:place' ) "; // AND m.metadata_field_id in (3,57)
			$SQL2 .= " JOIN dsd.metadatavalue2 m ON ( i.item_id = m.item_id AND m.element = 'ea:publication:place' ) "; // AND m.metadata_field_id in (3,57)
		}
		if (! empty($author)) {
			// $SQL1 .= " JOIN dsd.metadatavalue2 a ON ( i.item_id = a.item_id AND a.metadata_field_id = " . DB_METADATA_FIELD_DC_AUTHOR . " ) ";# author
			// $SQL2 .= " JOIN dsd.metadatavalue2 a ON ( i.item_id = a.item_id AND a.metadata_field_id = " . DB_METADATA_FIELD_DC_AUTHOR . " ) ";# author
			$SQL1 .= " JOIN dsd.metadatavalue2 m ON ( i.item_id = m.item_id AND m.element = 'dc:contributor:' ) "; // AND m.metadata_field_id in (3,57)
			$SQL2 .= " JOIN dsd.metadatavalue2 m ON ( i.item_id = m.item_id AND m.element = 'dc:contributor:' ) "; // AND m.metadata_field_id in (3,57)
		}
		
		if (! empty($title)) {
			$SQL1 .= " JOIN dsd.metadatavalue2 m ON ( i.item_id = m.item_id AND m.element = 'dc:title:' ) ";
			$SQL2 .= " JOIN dsd.metadatavalue2 m ON ( i.item_id = m.item_id AND m.element = 'dc:title:' ) "; //
		}
		
		if (! empty($search_string)) {
			$SQL1 .= " , dsd.to_gr_tsquery(?) as q1 ";
			$SQL2 .= " , dsd.to_gr_tsquery(?) as q1 ";
		}
		
		if (! empty($place)) {
			$SQL1 .= " , dsd.to_gr_tsquery(?) as q2 ";
			$SQL2 .= " , dsd.to_gr_tsquery(?) as q2 ";
		}
		if (! empty($author)) {
			$SQL1 .= " , dsd.to_gr_tsquery(?) as q3 ";
			$SQL2 .= " , dsd.to_gr_tsquery(?) as q3 ";
		}
		if (! empty($title)) {
			$SQL1 .= " , dsd.to_gr_tsquery(?) as q4 ";
			$SQL2 .= " , dsd.to_gr_tsquery(?) as q4 ";
		}
		
		$SQL1 .= sprintf(" WHERE  status = '%s' AND in_archive ", Config::get('arc.ITEM_STATUS_FINISH'));
		$SQL2 .= sprintf(" WHERE  status = '%s' AND in_archive ", Config::get('arc.ITEM_STATUS_FINISH'));
		
		if (Config::get('arc.ARCHIVE_SITE') > 0) {
			$SQL1 .= sprintf(' AND (site = %s OR site = 0) ', Config::get('arc.ARCHIVE_SITE'));
			$SQL2 .= sprintf(' AND (site = %s OR site = 0) ', Config::get('arc.ARCHIVE_SITE'));
		}
		
		if ($collection > 0) {
			
			// $SQL1 .= " AND i.collection = ? ";
			// $SQL2 .= " AND i.collection = ? ";
			// $SQL1 .= " AND ? @@ i.fts_catalogs";
			// $SQL2 .= " AND ? @@ i.fts_catalogs";
			$filter_sql_token = get_menu_browse_filter_field($collection, 'SQL_TOKEN');
			if (! empty($filter_sql_token)) {
				$SQL1 .= sprintf(" AND ( %s )", $filter_sql_token);
				$SQL2 .= sprintf(" AND ( %s )", $filter_sql_token);
			}
			
			if ($browse_query) {
				$SQL1 .= " AND NOT issue_aggr ";
				$SQL2 .= " AND NOT issue_aggr ";
			}
		}
		
		if ($year > 0) {
			$SQL1 .= " AND year = ? ";
			$SQL2 .= " AND year = ? ";
		}
		
		if ($y1 > 0) {
			$SQL1 .= " AND year >= ? ";
			$SQL2 .= " AND year >= ? ";
		} elseif ($y1 == - 11) {
			$SQL1 .= " AND year is null ";
			$SQL2 .= " AND year is null ";
		}
		
		if ($y2 > 0) {
			$SQL1 .= " AND year <= ? ";
			$SQL2 .= " AND year <= ? ";
		}
		
		if (! empty($search_string)) {
			$SQL1 .= " AND q1 @@ m.text_value_fst";
			$SQL2 .= " AND q1 @@ m.text_value_fst ";
		}
		
		if (! empty($place)) {
			$SQL1 .= " AND q2 @@ m.text_value_fst";
			$SQL2 .= " AND q2 @@ m.text_value_fst ";
		}
		if (! empty($author)) {
			$SQL1 .= " AND q3 @@ m.text_value_fst";
			$SQL2 .= " AND q3 @@ m.text_value_fst ";
		}
		if (! empty($title)) {
			$SQL1 .= " AND q4 @@ m.text_value_fst";
			$SQL2 .= " AND q4 @@ m.text_value_fst ";
		}
		
		// if (! empty($fonitiko3)){
		// $SQL1 .= " AND i.title_search like ? ";
		// $SQL2 .= " AND i.title_search like ? ";
		// }
		
		// if (! empty($search_string)){
		// $SQL1 .= " AND m.text_value_search like ? ";
		// $SQL2 .= " AND m.text_value_search like ? ";
		//
		// }
		
		// if (! empty($author)){
		// $SQL1 .= " AND a.text_value_search like ? ";
		// $SQL2 .= " AND a.text_value_search like ? ";
		// }
		
		$SQL1 .= " ORDER by  " . $ORDER_SQL . " limit " . $limit . "  offset ? ";
		
		// echo("<pre>");
		// echo("COLLECTION: $collection \n");
		// echo($SQL1);
		// echo "\n===============================================\n";
		// echo($SQL2);
		// echo("</pre>");
		
		$stmt1 = $dbh->prepare($SQL1);
		$stmt2 = $dbh->prepare($SQL2);
		
		$i = 1;
		
		if (! empty($search_string)) {
			$stmt1->bindParam($i, $search_string);
			$stmt2->bindParam($i, $search_string);
			$i = $i + 1;
		}
		
		if (! empty($place)) {
			$stmt1->bindParam($i, $place);
			$stmt2->bindParam($i, $place);
			$i = $i + 1;
		}
		if (! empty($author)) {
			$stmt1->bindParam($i, $author);
			$stmt2->bindParam($i, $author);
			$i = $i + 1;
		}
		
		if (! empty($title)) {
			$stmt1->bindParam($i, $title);
			$stmt2->bindParam($i, $title);
			$i = $i + 1;
		}
		
		// if ($collection > 0){
		// $stmt1->bindParam($i, $collection);
		// $stmt2->bindParam($i, $collection);
		// $i = $i + 1;
		// }
		if ($year > 0) {
			$stmt1->bindParam($i, $year);
			$stmt2->bindParam($i, $year);
			$i = $i + 1;
		}
		
		if ($y1 > 0) {
			$stmt1->bindParam($i, $y1);
			$stmt2->bindParam($i, $y1);
			$i = $i + 1;
		}
		if ($y2 > 0) {
			$stmt1->bindParam($i, $y2);
			$stmt2->bindParam($i, $y2);
			$i = $i + 1;
		}
		
		// if (! empty($fonitiko2)){
		// $stmt1->bindParam($i, $place);
		// $stmt2->bindParam($i, $place);
		// $i = $i + 1;
		// }
		
		// if (! empty($fonitiko3)){
		// $stmt1->bindParam($i, $ok3);
		// $stmt2->bindParam($i, $ok3);
		// $i = $i + 1;
		// }
		
		// if (!empty($search_string)){
		// $stmt1->bindParam($i, $ok1);
		// $stmt2->bindParam($i, $ok1);
		// $i = $i + 1;
		// }
		
		// if (! empty($fonitiko4)){
		// $stmt1->bindParam($i, $ok4);
		// $stmt2->bindParam($i, $ok4);
		// $i = $i + 1;
		// }
		
		$stmt1->bindParam($i, $offset);
		
		$stmt1->execute();
		$result = $stmt1->fetchAll();
		
		$stmt2->execute();
		$row = $stmt2->fetch();
		$total_count = $row[0];
		
		// print_r($result);
		$rep['results'] = $result;
		$rep['total_cnt'] = $total_count;
		
		$subjects_ok = ARRAY();
		// sxetikes etiketes
		if (strlen(utf8_decode($search_string)) > 2) {
			
			$subjects = PDao::search_subject_db($search_string, 20, true);
			if (empty($subjects)) {
				$subjects = PDao::search_subject_db($search_string, 20, false);
			}
			
			if (count($subjects) == 1) {
				$subject = $subjects[0][0];
				$subjects = PDao::search_subject_relations($subject, 20);
				// array_unshift($subjects, ARRAY("0"=>$subject));
			}
			foreach ( $subjects as $s ) {
				$subjects_ok[$s[0]] = $s[0];
				if ($s[0] == $search_string) {
					$subjects_related = PDao::search_subject_relations($s[0], 20);
					foreach ( $subjects_related as $ss ) {
						$subjects_ok[$ss[0]] = $ss[0];
					}
				}
			}
		}
		
		$rep['subjects'] = $subjects_ok;
		// //////
		
		return $rep;
	}
	
	// @DocGroup(module="util", group="general", comment="item_list")
	public static function search_item_simple($search_string, $offset = null, $order = 5, $collection = 0, $y1 = null, $y2 = null, $lang = null) {
		Log::info("search_item_simple");
		$dbh = dbconnect();
		
		$limit = Config::get('arc.PAGING_LIMIT');
		
		$next_offset = $offset + $limit;
		$prev_offset = $offset - $limit;
		if ($prev_offset < 0) {
			$prev_offset = 0;
		}
		
		list ( $search_string, $collection, $y1, $y2, $order, $offset, $limit, $rows ) = SearchLib::search_item_m(1, $search_string, $collection, $y1, $y2, $lang, $order, $offset, $limit);
		$result = $rows;
		
		if (empty($search_string) && empty($collection) && empty($y1) && empty($y2)) {
			
			$userName = get_user_name(); // echo("<<#2# $userName ##>> ");
			if (empty($userName)) {
				$status_c = sprintf(" (i.status='%s') ", Config::get('arc.ITEM_STATUS_FINISH'));
			} else {
				$status_c = sprintf(" (i.status='%s' OR i.status='%s') ", Config::get('arc.ITEM_STATUS_FINISH'), Config::get('arc.ITEM_STATUS_PRIVATE'));
			}
			if (Config::get('arc.ARCHIVE_SITE') > 0) {
				$status_c .= sprintf(' AND (site = %s OR site = 0) ', Config::get('arc.ARCHIVE_SITE'));
			}
			
			$SQL = sprintf("SELECT count(*) from dsd.item2 i WHERE %s", $status_c);
			$stmt = $dbh->prepare($SQL);
			$stmt->execute();
			$row = $stmt->fetch();
			$total_count = $row[0];
		} else {
			list ( $search_string, $collection, $y1, $y2, $order, $offset, $limit, $rows ) = SearchLib::search_item_m(2, $search_string, $collection, $y1, $y2, $lang, $order, $offset, $limit);
			$total_count = $rows[0][0];
		}
		
		$rep = array('method' => 'simple','limit' => $limit,'offset' => $offset,'next_offset' => $next_offset,'prev_offset' => $prev_offset,'order' => $order,'term' => $search_string,'collection' => $collection,'year1' => $y1,'year2' => $y2,'subjects' => null );
		
		$rep['results'] = $result;
		$rep['total_cnt'] = $total_count;
		$subjects_ok = ARRAY();
		// sxetikes etiketes
		if (strlen(utf8_decode($search_string)) > 2) {
			
			$subjects = PDao::search_subject_db($search_string, 20, true);
			if (empty($subjects)) {
				$subjects = PDao::search_subject_db($search_string, 20, false);
			}
			
			if (count($subjects) == 1) {
				$subject = $subjects[0][0];
				$subjects = PDao::search_subject_relations($subject, 20);
				array_unshift($subjects, ARRAY("0" => $subject ));
			}
			foreach ( $subjects as $s ) {
				$subjects_ok[$s[0]] = $s[0];
				if ($s[0] == $search_string) {
					$subjects_related = PDao::search_subject_relations($s[0], 20);
					foreach ( $subjects_related as $ss ) {
						$subjects_ok[$ss[0]] = $ss[0];
					}
				}
			}
		}
		
		$rep['subjects'] = $subjects_ok;
		// //
		
		return $rep;
	}
	
	// @DocGroup(module="search", group="php", comment="search_item_m (simple search)")
	public static function search_item_m($qt = 1, $search_string, 
			// $subject = null, #$year = null, #$place = null, #$title = null, #$author = null,
			$collection = 0, $y1 = null, $y2 = null, $lang = null, $order = 5, $offset = null, $limit) {
		
		Log::info("search_item_m");
		$dbh = dbconnect();
		
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
		
		$empty_query = $search_string == '' ? true : false;
		
		if (empty($order)) {
			$order = 5;
		}
		
		if ($order == 5 && $empty_query) {
			$order = 1;
		}
		
		if (empty($offset)) {
			$offset = 0;
		}
		
		if (empty($y1)) {
			$y1 = 0;
		}
		if (empty($y2)) {
			$y2 = 0;
		}
		if ($y2 > 0 && $y2 < $y1) {
			$y1 = 0;
			$y2 = 0;
		}
		
		$year_query = false;
		if ($y1 > 0 || $y2 > 0) {
			$year_query = true;
		}
		
		$browse_query = false;
		// if ($empty_query && empty($y1) && empty($y2)){
		if ($empty_query) {
			// &&
			// ($collection == 0 || $collection == DB_COLLECTION_PERIODIKA || $collection == DB_COLLECTION_WEB_SITES || $collection == DB_COLLECTION_EFIMERIDES)){
			$browse_query = true;
		}
		
		$index_query = false;
		if ($browse_query && empty($collection)) {
			$index_query = true;
		}
		
		$remove_so = true;
		if ($year_query) {
			$remove_so = false;
		}
		
		// if (true){
		// echo("<pre>");
		// echo("EMPTY QUERY : $empty_query \n");
		// echo("BROWSE QUERY : $browse_query \n");
		// echo("INDEX QUERY : $index_query \n");
		// echo("YEAR QUERY : $year_query \n");
		// echo("REMOVE SO : $remove_so \n");
		// echo("</pre>");
		// }
		
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
			if ($browse_query) {
				$status_c = sprintf(" i.status = '%s' ", Config::get('arc.ITEM_STATUS_FINISH'));
			} else {
				$status_c = sprintf(" i.status in ('%s','%s') ", Config::get('arc.ITEM_STATUS_FINISH'), 'hidden');
			}
		} else {
			if ($browse_query) {
				$status_c = sprintf(" i.status in ('%s','%s') ", Config::get('arc.ITEM_STATUS_FINISH'), Config::get('arc.ITEM_STATUS_PRIVATE'));
			} else {
				$status_c = sprintf(" i.status in ('%s','%s','%s') ", Config::get('arc.ITEM_STATUS_FINISH'), Config::get('arc.ITEM_STATUS_PRIVATE'), 'hidden');
			}
		}
		
		$browse_filters = get_browse_filter();
		// echo("<pre>");
		// print_r($browse_filters);
		// echo("</pre>");
		$menu_lines = get_menu_lines();
		
		$FILTER_SQL = "";
		// if ($collection > 0){
		// $FILTER_SQL .= " AND ? @@ i.fts_catalogs";
		// }
		
		if (Config::get('arc.ARCHIVE_SITE') > 0) {
			$FILTER_SQL .= sprintf(' AND (site = %s OR site = 0) ', Config::get('arc.ARCHIVE_SITE'));
		}
		
		$issue_aggr_flag = true;
		if ($collection > 0) {
			$tmp_arr = get_menu_browse_filter($collection);
			if (isset($tmp_arr['SQL_TOKEN'])) {
				$filter_sql_token = $tmp_arr['SQL_TOKEN'];
			}
			if (isset($tmp_arr['ISSUE_AGGR'])) {
				$issue_aggr_flag = $tmp_arr['ISSUE_AGGR'];
			}
			if (! empty($filter_sql_token)) {
				$FILTER_SQL .= sprintf(' AND ( %s ) ', $filter_sql_token);
			}
		}
		
		if ($y1 > 0) {
			$FILTER_SQL .= " AND year >= ? ";
		} elseif ($y1 == - 11) {
			$FILTER_SQL .= " AND year is null ";
		}
		if ($y2 > 0) {
			$FILTER_SQL .= " AND year <= ? ";
		}
		if ($issue_aggr_flag && (! $index_query) && ($browse_query && (empty($lang) || $lang == 'el'))) {
			$FILTER_SQL .= " AND NOT i.issue_aggr ";
		}
		
		$REMOVE_SO_SQL = "";
		if ($remove_so) {
			$REMOVE_SO_SQL = " AND NOT m.so ";
		}
		
		$LANG_SQL = "";
		if (! empty($lang)) {
			$LANG_SQL = " AND i.lang = ? ";
		}
		
		if ($result_query) {
			if (! $empty_query) {
				$SQL = 'SELECT  ';
				$SQL .= Config::get('arc.ITEM_LIST_SQL_FIELDS');
				$SQL .= " ,max(rank) as rank ";
				$SQL .= " FROM ( ";
				$SQL .= " SELECT i.*, m.text_value, m.metadata_field_id as mfid, m.text_value_fst, m.so,q, dsd.metadata_field_rank2(m.element) as rank ";
				$SQL .= " FROM dsd.item2 i ";
				$SQL .= " JOIN DSD.metadatavalue2 m ON (m.item_id = i.item_id) ";
				$SQL .= " ,dsd.to_gr_tsquery(?) as q ";
				$SQL .= " WHERE ";
				$SQL .= " element in ( 'dc:contributor:author','dc:contributor:','dc:identifier:isbn','dc:subject:','dc:title:','marc:title-statement:title','ea:title:uniform' ) ";
				$SQL .= " AND m.ref_item is null ";
				$SQL .= sprintf("AND %s ", $status_c);
				$SQL .= $REMOVE_SO_SQL;
				$SQL .= " AND q @@ m.text_value_fst  ";
				$SQL .= $FILTER_SQL;
				$SQL .= $LANG_SQL;
				$SQL .= " ) as i " . Config::get('arc.ITEM_LIST_SQL_GROUP_BY');
				$SQL .= "  ORDER BY  " . $ORDER_SQL . " limit " . $limit . "  offset ? ";
			} else {
				$SQL = 'SELECT  ';
				$SQL .= Config::get('arc.ITEM_LIST_SQL_FIELDS');
				$SQL .= " FROM dsd.item2 i ";
				$SQL .= " WHERE ";
				$SQL .= sprintf(" %s ", $status_c);
				$SQL .= $FILTER_SQL;
				$SQL .= $LANG_SQL;
				$SQL .= "  ORDER BY  " . $ORDER_SQL . " limit " . $limit . "  offset ? ";
			}
		} else {
			if (! $empty_query) {
				$SQL = 'SELECT  count(distinct i.item_id)';
				$SQL .= " FROM dsd.item2 i ";
				$SQL .= " JOIN DSD.metadatavalue2 m ON (m.item_id = i.item_id) ";
				$SQL .= " ,dsd.to_gr_tsquery(?) as q ";
				$SQL .= " WHERE ";
				$SQL .= " element in ( 'dc:contributor:author','dc:contributor:','dc:identifier:isbn','dc:subject:','dc:title:','marc:title-statement:title','ea:title:uniform' ) ";
				$SQL .= " AND m.ref_item is null ";
				$SQL .= $REMOVE_SO_SQL;
				$SQL .= " AND q @@ m.text_value_fst  ";
				$SQL .= $FILTER_SQL;
				$SQL .= $LANG_SQL;
				// $SQL .= " ";
			} else {
				$SQL = 'SELECT  COUNT(*) ';
				$SQL .= " FROM dsd.item2 i ";
				$SQL .= " WHERE ";
				$SQL .= sprintf(" %s ", $status_c);
				$SQL .= $FILTER_SQL;
				$SQL .= $LANG_SQL;
			}
		}
		
		// ##############################
		// # DEBUG
		// ##############################
		
		if (true) {
			echo ("<pre>");
			
			echo ("2.SEARCH SQL:\n");
			echo ($SQL);
			echo ("\n------------------\n");
			
			if (! $empty_query) {
				echo ("search_string: " . $search_string . "\n");
			}
			if ($collection > 0) {
				echo ("collection: " . $collection . "\n");
			}
			if ($y1 > 0) {
				echo ("y1: " . $y1 . "\n");
			}
			if ($y1 > 0) {
				echo ("y2: " . $y2 . "\n");
			}
			
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
		
		if ($y1 > 0) {
			$stmt->bindParam($i, $y1);
			$i = $i + 1;
		}
		if ($y2 > 0) {
			$stmt->bindParam($i, $y2);
			$i = $i + 1;
		}
		
		if (! empty($lang)) {
			$stmt->bindParam($i, $lang);
			$i = $i + 1;
		}
		
		if ($result_query) {
			$stmt->bindParam($i, $offset);
		}
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
		return array($search_string,$collection,$y1,$y2,$order,$offset,$limit,$result );
	}
}

?>