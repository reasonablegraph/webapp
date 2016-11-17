<?php
class AdminCatalogingController extends BaseController {



	public function cataloging(){

		//$username = ArcApp::username();
		//App::setLocale($app->language);


		$l = get_get('l');//title
// 		$ss = get_get('t');//keyword from simple
		$sss = get_get('tt');//keyword from advance
		$c = PUtil::reset_int(get_get("c"),0);//silogi //katigoria
		$y = PUtil::reset_int(get_get("y"),null);//year
		$y1 = PUtil::reset_int(get_get("y1"),null);//year1
		$y2 = PUtil::reset_int(get_get("y2"),null);//year2
		$p = get_get('p');//place
		$o = PUtil::reset_int(get_get("o"),null);//offset
		$r = PUtil::reset_int(get_get("r"),5);//order
		$a = get_get('a');//author
		$subj = get_get('subj');//subject
		$m = get_get('m','s');//method (simple search,advance search)
		$d = PUtil::reset_int(get_get("d"),1);//display method (list,thumb1,thumb2)
		$sl = get_get('sl','0');
		#$k = get_get('k','0'); //katigoria
		$ot = get_get('ot',null);//object-type
		$k = $c;


		if( $o < 0 ){
			$o = 0;
		};

		$data = ARRAY();

		$data = array(
				'r'=>$r,
				'm'=>$m,
				'c'=>$c,
				'l'=>$l,
// 				'ss'=>$ss,
				'sss'=>$sss,
				'c'=>$c ,
				'y'=>$y ,
				'y1'=>$y1,
				'y2'=>$y2,
				'p'=>$p ,
				'o'=>$o ,
				'a'=>$a ,
				'subj'=>$subj,
				'd'=>$d ,
				'sl'=>$sl,
				'ot'=>$ot,
				'relation_work_wholepart_map' => Setting::get('relation_work_wholepart_map'),
		);



		$ss = trim(get_get('t'));
		$init_query_flag = empty($ss);

		//Log::info("#NEWITEM#1");
		//Log::info($ss);


		$con = dbconnect();

		//activity_log
		$search_text = get_get('t');
		if (!empty($search_text ) ){
			$user = ArcApp::username();
			if (empty($user)) {
				$user = 'Unknown';
			}
			$url = $_SERVER['REQUEST_URI'];
			$type_act = 'search';
			PDao::activity_log($type_act, $user, $url);
		}


		$results=array();


		$select_cols = 'i.item_id as id, i.jdata, i.obj_type, i.status , i.dt_update, i.obj_type, i.label, i.thumb,  user_create, user_update ';
		$init_limit = 30;
		$limit = 60;

		if ($init_query_flag){
			$SQL=sprintf ("
			SELECT %s
			FROM dsd.item2 i
			WHERE obj_type not in ('bitstream') AND obj_type not in ('digital-item') AND obj_type not in ('physical-item') AND i.status not in ('%s')
			ORDER BY i.dt_update desc offset ? LIMIT %s",$select_cols,Config::get('arc.ITEM_STATUS_ERROR'),$init_limit);
		} else {
			$SQL= sprintf ("
			SELECT  %s, ts_rank_cd(fts, q) AS rank
			FROM dsd.item2 i,
			dsd.to_gr_tsquery(?) as q
			WHERE  obj_type not in ('bitstream') AND obj_type not in ('digital-item') AND obj_type not in ('physical-item') AND q @@ i.fts  AND i.status not in ('%s')
			ORDER BY rank DESC  LIMIT %s", $select_cols,Config::get('arc.ITEM_STATUS_ERROR'),$limit);
		}

		Log::info('------------------------------------------------------');
		Log::info($SQL);
		Log::info('------------------------------------------------------');


		$stmt = $con->prepare($SQL);
		if (!$init_query_flag){
			$stmt->bindParam(1, $ss);
		}else{
			$stmt->bindParam(1, $o);
		}
		$stmt->execute();

		while ($r = $stmt->fetch(PDO::FETCH_ASSOC)){
			$status = $r['status'];
			$dt_update = new DateTime($r['dt_update']);
			if (empty($r['jdata'])){
				$jdata = ARRAY('opac1'=>ARRAY());
			} else {
				$jdata = json_decode($r['jdata'],true);
			}
			$rdata = array_merge(isset($jdata['opac1'])?$jdata['opac1'] : array(), $r);

			//$obj_type_display =  str_replace('auth-', '',$r['obj_type']);
			$rdata['obj_type_display'] = $r['obj_type'];
			$rdata['title']= $r['label'];
			$rdata['dt_update'] = $dt_update->format('d/m/Y H:i');

			$results[] = $rdata;
		}

		######## pagination #########
// 		if ($init_query_flag){
// 			$SQL=sprintf ("
// 			SELECT %s
// 			FROM dsd.item2 i
// 			WHERE obj_type not in ('bitstream') AND obj_type not in ('digital-item') AND i.status not in ('%s')",$select_cols,Config::get('arc.ITEM_STATUS_ERROR'));
// 			$stmt2 = $con->prepare($SQL);
// 			$stmt2->execute();
// 			$row = $stmt2->fetch();
// 			$total_count = $row[0];
// 		}
		#############################


// 		######## pagination #########
// 		if (!$init_query_flag){
// 			$total_count = count($results);
// 		}else{ echo "init";}

// 		$limit = Config::get('arc.PAGING_LIMIT');
// 		$offset = $o;
// 		$next_offset = $offset + $limit;
// 		$prev_offset = $offset - $limit;
// 		if ($prev_offset < 0) {
// 			$prev_offset = 0;
// 		}

// 		$paging_data = array(
// 				'total_cnt'=> $total_count,
// 				'limit'=> $limit,
// 				'offset'=> $offset,
// 				'prev_offset'=> $prev_offset,
// 				'next_offset'=> $next_offset,
// 		);

// 		$data['paging_data'] = $paging_data;
// 		#############################



		$data['limit'] = $init_limit;

		$data['results'] = $results;
		$data['ss'] = $ss;
		return $this->show($data);

	}







}
?>