<?php
class ActionControler extends BaseController {


	public function action1() {
		Log::info('action1');

		ArcApp::auth_check();
		$dbh = dbconnect();
		$SQL="SELECT l.user_name , l.event_ts, l.type , l.url FROM dsd.activity_log l
					JOIN (SELECT user_name, max(event_ts) AS last_event_ts FROM dsd.activity_log  WHERE event_ts > (now() - '60 minutes'::interval) GROUP BY 1 ORDER BY 2 DESC) AS tmp
					ON (tmp.user_name = l.user_name AND tmp.last_event_ts = l.event_ts)
					ORDER BY 2 DESC";

		$stmt = $dbh->prepare($SQL);
		$stmt->execute();

		$results=array();

		while ( $row = $stmt->fetch() ) {
			$user_name = $row['user_name'];
			$last_event_ts = $row['event_ts'];
			$type = $row['type'];
			$url = urldecode($row['url']);
			$results[] = array('user_name'=>$user_name , 'last_event_ts'=>$last_event_ts, 'type' => $type, 'url' => $url);
		}

		$data['results'] = $results;
		return $this->show($data);
	}



	public function action2() {
		Log::info('action2');

		ArcApp::auth_check();
		$dbh = dbconnect();

		$username = get_post("username");
		$datetime = get_post("datetime");

		if (! empty($_POST)) {
		$results = array();
			if (!empty($username)){
				if (!empty($datetime)){
// 					$SQL="SELECT user_name, event_ts, type, url FROM dsd.activity_log  WHERE event_ts > (now() - '$datetime'::interval)  AND user_name= ? ORDER BY 2 DESC";
					$SQL="SELECT user_name, event_ts, type, url FROM dsd.activity_log  WHERE event_ts > (now() - '$datetime'::interval)";
					$SQL.= ($username == 'all_users') ? null : " AND user_name= '$username'";
					$SQL.= " ORDER BY 2 DESC";
					$stmt = $dbh->prepare($SQL);
// 					$stmt->bindParam(1, $username);
					$stmt->execute();
					while ( $row = $stmt->fetch() ) {
						$user_name = $row['user_name'];
						$last_event_ts = $row['event_ts'];
						$type = $row['type'];
						$url = urldecode($row['url']);
						$results[] = array('user_name'=>$user_name , 'last_event_ts'=>$last_event_ts, 'type' => $type, 'url' => $url);
					}
					$data['results'] = $results;
				}
			}
		}

		//staff-depositor-administrator
		$SQL="SELECT u.username FROM dsd.arc_user u JOIN dsd.arc_user_roles r	ON (u.username = r.username) WHERE role_id = 5 OR  role_id = 6 OR  role_id = 3 ORDER BY 1 ASC";
		$stmt = $dbh->prepare($SQL);
		$stmt->execute();
		$users_arr = array();
		$users_arr['all_users'] = 'All users';
		while ( $row = $stmt->fetch() ) {
			$username_opt = $row['username'];
			$users_arr["$username_opt"] = $username_opt;
			}

		$data['users_arr'] = $users_arr;

		$datetime_arr = array(
				'30 minutes'=> '30 minutes',
				'1 hours' => 'hour',
				'1 days' => 'day',
				'7 days' => 'week',
				'1 months' => 'month',
		);

		$datetime_default = '1 hours';

		$data['datetime_arr']=$datetime_arr;
		$data['default_username'] = empty($username)? 'all_users' : $username;
		$data['default_datetime'] = empty($datetime)? $datetime_default : $datetime;

		return $this->show($data);
	}


}



