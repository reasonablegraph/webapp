<?php
class ReportControler extends BaseController {


	public function report1() {
		Log::info('report1');

		ArcApp::auth_check();

		//$dbh = prepareService();
		$dbh = dbconnect();



		$SQL="SELECT item_id FROM dsd.item2 WHERE status='finish' AND obj_type='auth-manifestation' ORDER BY dt_create desc";
		$stmt1 = $dbh->prepare($SQL);

		$SQL = "SELECT m.ref_item FROM dsd.metadatavalue2 m JOIN dsd.item2 i ON (m.ref_item = i.item_id) WHERE m.ref_item IS NOT NULL AND m.element = 'ea:work:authorWork' and m.item_id = ? AND i.status = 'finish'";
		$stmt2 = $dbh->prepare($SQL);

		$SQL = "SELECT count(*) FROM dsd.metadatavalue2 m JOIN dsd.item2 i ON (m.ref_item = i.item_id) WHERE m.ref_item IS NOT NULL AND m.element = 'ea:work:' and m.item_id = ? AND i.status = 'finish' AND NOT m.inferred";
		$stmt3 = $dbh->prepare($SQL);

// 		$SQL = "SELECT obj_type FROM dsd.metadatavalue2 m JOIN dsd.item2 i ON (m.ref_item = i.item_id) WHERE m.ref_item IS NOT NULL AND m.element = 'ea:artifact-of:' and m.item_id = ? AND i.status = 'finish' AND NOT m.inferred";
// 		$stmt3 = $dbh->prepare($SQL);


		$stmt1->execute();
		$items=array();
		while ($item_id = $stmt1->fetch()[0]){
			$basic = PDao::getItemBasic($item_id);


			$authors = array();
			$stmt2->bindParam(1, $item_id);
			$stmt2->execute();
			while ($author_id = $stmt2->fetch()[0]){
				$authors[] = PDao::getItemBasic($author_id);
			}

			$stmt3->bindParam(1, $item_id);
			$stmt3->execute();
			$wc = $stmt3->fetch()[0];

			$items[] = array('basic'=>$basic , 'authors'=>$authors, 'work_count' => $wc);


		}
		return Response::view('reports/report1',array('items'=>$items))->header('X-DRUPAL-DECORATE', 'NONE')->header('Content-Type', 'text/html');
	}



	public function downloading_logs() {
		Log::info('downloading logs');

		$datetime = get_post_get("datetime","1 months");
		$view = get_post_get("filter",1);

// 		echo $view;
// 		echo "<br>";
// 		$input_param = Input::all();
// 		echo "<pre>";  print_r($input_param); echo "</pre>";

		$o = PUtil::reset_int(get_get("o"),0);
		$o = $o < 0 ? 0 : $o;
		$limit = 30;

		ArcApp::auth_check();
		$data=array();
		$dbh = dbconnect();


// 		$SQL="SELECT v.name, v.action_time, v.item_label, v.item_id, v.remote_addr, v.user_agent, u.username
// 		FROM  dsd.download_log_view v
// 		JOIN  dsd.download_log d ON (d.id = v.id)
// 		JOIN  dsd.arc_user u ON (u.id = d.user_id)";
// 		$stmt2 = $dbh->prepare($SQL);
// 		$stmt2->execute();
// 		$total = count($stmt2->fetchAll());


		if ($view == 1){
			// ALL DOWNLOADS
			$SQL="SELECT  u.username, l.action_time, l.size, l.user_agent, l.remote_addr, b.name AS bitsream_label, i.item_id, i.label AS item_label
			FROM dsd.download_log l
			LEFT JOIN bitstream b ON l.bitstream_id = b.bitstream_id
			LEFT JOIN bundle2bitstream bb ON bb.bitstream_id = b.bitstream_id
			LEFT JOIN item2bundle ib ON ib.bundle_id = bb.bundle_id
			LEFT JOIN dsd.item2 i ON i.item_id = ib.item_id
			LEFT JOIN  dsd.arc_user u ON u.id = l.user_id";
			if ($datetime != "all"){
				$SQL.=" WHERE l.action_time > (now() - '$datetime'::interval)";
			}
			$SQL.=" ORDER BY 2 DESC";

			$stmt2 = $dbh->prepare($SQL);
			$stmt2->execute();
			$total = count($stmt2->fetchAll());
		}else if($view == 2){
			// GROUP BY BITSTREAM
			$SQL="SELECT l.bitstream_id, l.size, b.name AS bitsream_label, i.item_id, i.label AS item_label, i.user_create as creator, count(*) AS count
			FROM dsd.download_log l
			LEFT JOIN bitstream b ON l.bitstream_id = b.bitstream_id
			LEFT JOIN bundle2bitstream bb ON bb.bitstream_id = b.bitstream_id
			LEFT JOIN item2bundle ib ON ib.bundle_id = bb.bundle_id
			LEFT JOIN dsd.item2 i ON i.item_id = ib.item_id";
			if ($datetime != "all"){
				$SQL.=" WHERE l.action_time > (now() - '$datetime'::interval)";
			}
			$SQL.=" GROUP BY 1,2,3,4,5,6
			ORDER BY 7 DESC";

			$stmt3 = $dbh->prepare($SQL);
			$stmt3->execute();
			$total = count($stmt3->fetchAll());
		}else{
			// GROUP BY BITSTREAM
			$SQL="SELECT i.user_create as creator, count(*) AS count
			FROM dsd.download_log l
			LEFT JOIN bitstream b ON l.bitstream_id = b.bitstream_id
			LEFT JOIN bundle2bitstream bb ON bb.bitstream_id = b.bitstream_id
			LEFT JOIN item2bundle ib ON ib.bundle_id = bb.bundle_id
			LEFT JOIN dsd.item2 i ON i.item_id = ib.item_id";
			if ($datetime != "all"){
				$SQL.=" WHERE l.action_time > (now() - '$datetime'::interval)";
			}
			$SQL.=" GROUP BY 1
			ORDER BY 2 DESC";

			$stmt3 = $dbh->prepare($SQL);
			$stmt3->execute();
			$total = count($stmt3->fetchAll());
		}

		$SQL.=" OFFSET $o
		LIMIT $limit";
		$stmt = $dbh->prepare($SQL);
		$stmt->execute();
		$results = $stmt->fetchAll();

		$datetime_arr = array(
				'30 minutes'=> '30 minutes',
				'1 hours' => 'Hour',
				'1 days' => 'Day',
				'7 days' => 'Week',
				'1 months' => 'Month',
				'6 months' => '6 month',
				'1 year' => 'Year',
				'all' => 'All time',
		);

		$filter_arr = array(
				'1' => 'All downloads',
				'2' => 'Group by file',
				'3' => 'Group by owner',
		);

		$data['datetime_arr']=$datetime_arr;
		$data['default_datetime'] = empty($datetime)? '1 months' : $datetime;
		$data['filter_arr'] = $filter_arr;
		$data['default_filter'] = empty($view)? '1' : $view;
		$data['view'] = $view;
		$data['results'] = $results;
		$data['total']= $total;
		$data['limit']= $limit;
		$data['offset']= $o;

		return $this->show($data);
	}


	public function reset_graph_logs() {
		// 		Log::info('reset_graph_logs');
		ArcApp::auth_check();
		$user = ArcApp::username();
		$results=array();
		$datetime = '1 months';

		$o = PUtil::reset_int(get_get("o"),0);
		$o = $o < 0 ? 0 : $o;
		$limit = 20;

		if (!empty($_POST)) {
			$id = get_post("id");
			PDao::update_long_run($id,2,"Warning: manual close!");
		}

		$dbh = dbconnect();
		$SQL = "SELECT id, pid, start_dt, end_dt, status, error_msg FROM public.long_run ";
		$SQL.= "WHERE start_dt > (now() - '$datetime'::interval) OR status = 1 ";

		$stmt2 = $dbh->prepare($SQL);
		$stmt2->execute();
		$total = count($stmt2->fetchAll());

		$SQL.= "ORDER BY start_dt DESC ";
		$SQL.=" OFFSET $o LIMIT $limit";

		$stmt = $dbh->prepare($SQL);
		$stmt->execute();
		$results  = $stmt->fetchAll();

		$data['results'] = $results;
		$data['total'] = $total;
		$data['limit']= $limit;
		$data['offset']= $o;

		return $this->show($data);
	}


	public function user_items() {
// 		Log::info('user_items');
		ArcApp::auth_check();
		$user = ArcApp::username();
		$results=array();

		$dbh = dbconnect();
		$SQL="select obj_type,count(*) as count from dsd.item2 WHERE user_create = ? group by 1 order by 2 desc;";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $user);
		$stmt->execute();
		$results  = $stmt->fetchAll();

		$data['results'] = $results;
		$data['user'] = $user;
		return $this->show($data);

	}


	public function all_user_items() {
		// 		Log::info('all_user_items');
		ArcApp::auth_check();
		$dbh = dbconnect();
// 		$username = get_post("username");
		$username = empty(get_post("username"))? 'users' : get_post("username");

		//staff-depositor-administrator
		$SQL="SELECT u.username FROM dsd.arc_user u
					JOIN dsd.arc_user_roles r	ON (u.username = r.username)
					WHERE role_id = 5 OR  role_id = 6 OR  role_id = 3
					ORDER BY 1 ASC";
		$stmt = $dbh->prepare($SQL);
		$stmt->execute();
		$users_arr = array();
		$users_arr['users'] = 'All users';
		while ( $row = $stmt->fetch() ) {
			$username_opt = $row['username'];
			$users_arr["$username_opt"] = $username_opt;
		}

		$SQL="SELECT obj_type,count(*) as count FROM dsd.item2 WHERE status = 'finish'";
		if ( !empty($username) && $username != 'users'){
		$SQL.="AND user_create = '$username'";
		}
		$SQL.="GROUP BY 1 ORDER BY 2 DESC";

		$stmt = $dbh->prepare($SQL);
		$stmt->execute();
		$results  = $stmt->fetchAll();

		$data['users_arr'] = $users_arr;
// 		$data['default_username'] = empty($username)? 'users' : $username;
		$data['results'] = $results;
		$data['user'] = $username;

		return $this->show($data);
	}


}



