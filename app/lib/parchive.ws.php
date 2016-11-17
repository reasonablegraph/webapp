<?php

class Tweets {




	public static function proc_tweet($t){

		//print_r($t);
		$data = json_encode($t);


		$t_id = $t['id_str'];
		$entities = $t['entities'];
		$user = $t['user'];
		$hashtags = $entities['hashtags'];
		$media = null;
		$media_url = null;
		if (isset($entities['media'])){
			$media = $entities['media'];
			$media_url = $media[0]['media_url'];
			//[expanded_url] => http://twitter.com/igeldard/status/356137318664519680/photo/1
			//[media_url_https] => https://pbs.twimg.com/media/BPFBASKCQAICbse.jpg
			//[url] => http://t.co/mxaxKytDqt
			//[type] => photo
			//[media_url] => http://pbs.twimg.com/media/BPFBASKCQAICbse.jpg

		}

		$u_name = $user['name'];
		$u_p_url = $user['profile_image_url'];
		$u_id = $user['id_str'];
		$u_followers_count = $user['followers_count'];

		$created_at = $t['created_at'];
		$favorite_count = $t['favorite_count'];
		$retweet_count =  $t['retweet_count'];
		$res_type  = $t['metadata']['result_type'];
		$lang =$t['metadata']['iso_language_code'];


		#Sun Jul 14 05:37:36 +0000 2013
		$date = DateTime::createFromFormat('D M d H:i:s T Y', $created_at);
		$tweet_dt = $date->format('Y-m-d H:i:s T');
		#echo "$created_at: "  . $tweet_dt . "\n";
		$retweeted_flag = 'false';
		if ($t['retweeted'] == 1){
			$retweeted_flag = 'true';
		}



		$status='finish';
		$dbh = dbconnect();


		$SQL ="SELECT count(*) FROM dsd.tweet WHERE t_id = ?";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $t_id);
		$stmt->execute();
		$r = $stmt->fetchAll();
		$c = $r[0]['count'];
		if ($c ==  0){


			list($upload_file_name, $upload_file_path,$upload_file_ext) =  get_file_from_url($u_p_url, 'avatar_', false);
			$tdir = avatar_create_dir();
 			$avatar =  $tdir . '/' . $t_id . '.' . $upload_file_ext;
 			$full_path = ROOT_DIR . 'avatar/' . $avatar;
 			$cmd = ' mv ' . $upload_file_path . ' ' . $full_path;

			$tmp = exec($cmd);


			$bundle_id = null;
 			if ($media_url != null){
 				list($upload_file_name, $upload_file_path,$upload_file_ext) =  get_file_from_url($media_url, 'tweet_', false);
 				$seq_id = null;
 				$item_id = TWEETS_ITEM;
 				list($bitstream_id,$bundle_id,$uuid) = upload_bitsteam( $upload_file_path,$upload_file_name, $upload_file_ext, $item_id,$seq_id, 'ORIGINAL');
 				$out="";
 				thumbs_generate_from_bitstream($dbh, $uuid, $out,false,false,true);
 			}


			$SQL = ' INSERT INTO dsd.tweet	(
				t_id ,
				user_thumb_url ,
				user_name ,
				user_id ,
				u_followers_count ,
				text ,
				retweets ,
				favorites ,
				lang ,
				tweet_dt ,
				filter_type ,
				data ,
				avatar,
				status,
				retweet,
				version)
				VALUES (?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,2)';
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $t_id);
			$stmt->bindParam(2, $u_p_url);
			$stmt->bindParam(3, $u_name);
			$stmt->bindParam(4, $u_id);
			$stmt->bindParam(5, $u_followers_count);
			$stmt->bindParam(6, $t['text']);
			$stmt->bindParam(7, $retweet_count);
			$stmt->bindParam(8, $favorite_count);
			$stmt->bindParam(9, $lang);
			$stmt->bindParam(10, $tweet_dt);
			$stmt->bindParam(11, $res_type);
			$stmt->bindParam(12, $data);
			$stmt->bindParam(13, $avatar);
			$stmt->bindParam(14, $status);
			$stmt->bindParam(15, $retweeted_flag);

			$stmt->execute();

			$SQL='INSERT INTO dsd.tweet_hashtag (tweet,hashtag) values (?,?)';
			$stmt = $dbh->prepare($SQL);
			foreach ($hashtags as $tag){
				$tn = strtolower($tag['text']);
				$stmt->bindParam(1, $t_id);
				$stmt->bindParam(2, $tn);
				$stmt->execute();
			// 				printf("tag  : %s \n",$tag['text']);
			}


			if (!empty($bitstream_id)){
				$SQL="INSERT INTO public.tweet2bitstream (tweet,bitstream_id) values (?,?);";
				$stmt = $dbh->prepare($SQL);
				$stmt->bindParam(1, $t_id);
				$stmt->bindParam(2, $bitstream_id);
				$stmt->execute();
			}


			printf("INSERT: %s\n",$t_id);

		} else {
			$SQL ="update dsd.tweet SET
					u_followers_count = ?,
					retweets =?, favorites =?, tweet_dt = ?, filter_type = ?, status = ?, update_dt = now()
					WHERE t_id=?";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $u_followers_count);
			$stmt->bindParam(2, $retweet_count);
			$stmt->bindParam(3, $favorite_count);
			$stmt->bindParam(4, $tweet_dt);
			$stmt->bindParam(5, $res_type);
			$stmt->bindParam(6, $status);
			$stmt->bindParam(7, $t_id);
			$stmt->execute();
			printf("UPDATE: %s \n",$t_id);
		}

		//$SQL ="update dsd.tweet SET retweets =? favorites =? tweet_dt = ? filter_type = ? date =? status = ? WHERE t_id=?";


// 		printf("id   : %s \n",$t['id_str']);
// 		printf("uid  : %s \n",$u_id);
// 		printf("name : %s \n",$u_name);
// 		printf("purl : %s \n",$u_p_url);
// 		printf("uflc : %s \n",$u_followers_count);

// 		printf("type : %s \n",$res_type);
// 		printf("lang : %s \n",$lang);

// 		printf("rc   : %s \n",$retweet_count);
// 		printf("fvc  : %s \n",$favorite_count);
// 		printf("dt   : %s \n",$tweet_dt);

// 		printf("text : %s \n",$t['text']);
// 		foreach ($hashtags as $tag){
// 				printf("tag  : %s \n",$tag['text']);
// 		}
// 		printf("media: %s \n",$media_url);
// 		echo("===========================================\n");


	}


	public static function ws_tweets_add($data){
		#print_r($data);

		foreach ( $data as $t){
			Tweets::proc_tweet($t);
		}





	}

}

class Pws {



	public static function ws_move_to_folder(){
		auth_check_all();
		$userName = get_user_name();
		if (empty($userName)){
			echo("auth error");
			return;
		}
 		echo  "USER: $userName\n";
 		echo("\n============================\n");
 		print_r($_POST);
 		echo("\n============================\n");

		$move_flag = false;

		$items = get_post('items');
		$folder = get_post('folder');
		$folder_name = get_post('folder_name');
		$src_folder = get_post('src_folder',null);
		if (empty($items)){
			return "error missing data";
		}
		if (empty($src_folder)  || $src_folder == 'null'){
			$move_flag = false;
			$src_folder = null;
		} else {
			$move_flag = true;
		}


		if (empty($folder_name) && empty($folder)){
			return "error missing data(2)";
		}

		if (! empty($folder_name) && empty($folder)){

			$dbh = dbconnect();
			$item_id = null;

			$obj_type = DataFields::DB_OBJ_TYPE_SILOGI;
			//$item_id = insert_item($dbh, $userName);
			$item_id = PDao::insert_item($userName, $obj_type);
			echo ("NEW ITEM: " . $item_id . "\n");
			if (empty($item_id)){
				$dbh->rollback();
				echo ("ERROR NEW item_id ");
				return;
			}

			$title = $folder_name;
			#$title = str_replace('_',' ',$title);
			
			$status = DataFields::ITEM_STATUS_PRIVATE;
			$lang = 'el';

			update_metadata($dbh, $item_id, DataFields::dc_title ,ARRAY(ARRAY($title,null,null)));
			update_metadata($dbh, $item_id, DataFields::dc_language_iso ,ARRAY(ARRAY($lang,null,null)));
			update_metadata($dbh, $item_id, DataFields::ea_obj_type ,ARRAY(ARRAY($obj_type,null,null)));
			update_metadata($dbh, $item_id, DataFields::ea_status ,ARRAY(ARRAY($status,null,null)));
			$folder = $item_id;
		}

		

		$rel_type=DataFields::DB_item_realtion_type_member_of;
		$rel_type_id=DataFields::DB_item_realtion_type_member_of_id;
		$dbh = dbconnect();

		//error_log("FOLDER ".$folder);
		
		//$rel_type_label='member_of';
		foreach ($items as $item){
			//error_log("ITEM ".$item);
			//echo "cp item $item to $folder\n";
			$SQL = "SELECT dsd.move_to_folder(?,?) as rep";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $item);
			$stmt->bindParam(2, $folder);
			$stmt->execute();
			if ($move_flag && !empty($src_folder)){
				$SQL = "DELETE FROM dsd.item_relation WHERE item_1=? AND item_2=? AND rel_type=? ";
				$stmt = $dbh->prepare($SQL);
				$stmt->bindParam(1, $item);
				$stmt->bindParam(2, $folder);
				$stmt->bindParam(3, $rel_type_id);
				$stmt->execute();


				if ($folder == TRASH_FOLDER){
					$status='error';
					update_metadata($dbh, $item, DataFields::ea_status ,ARRAY(ARRAY($status,null,null)));
				}
			}
			$SQL = "SELECT dsd.touch_item(?,false,false) as rep";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $item);
			$stmt->execute();
		}
		
		$SQL = "SELECT dsd.touch_item(?,false,false) as rep";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $folder);
		$stmt->execute();
		

	}




	public static function ws_chk_bitstream() {

		http_auth_check();
		$userName = get_user_name();
		if (empty($userName)){
			echo("auth error");
			return;
		}
// 		echo  "USER: $userName\n";
//		if (! empty($_POST)){
//			echo("\n============================\n");
//			print_r($_POST);
//			echo("\n============================\n");
//			print_r($_FILES);
//			echo("\n============================\n");
//		}


		$file_name = trim(base64_decode(get_post("FILENAME",null)));

		$upload_file_name = $file_name;

		$upload_md5 = get_post("MD5",null);

		$dbh = dbconnect();

		//$SQL="SELECT count(*) from public.bitstream  where upper(name) = upper(?);";
		$SQL="SELECT count(*)
					FROM dsd.item2 i
					JOIN item2bundle ib ON i.item_id = ib.item_id
					JOIN bundle be ON ib.bundle_id = be.bundle_id
					JOIN bundle2bitstream bb ON ib.bundle_id = bb.bundle_id
					JOIN bitstream b ON bb.bitstream_id = b.bitstream_id
					WHERE status <> 'error' AND  upper(b.name) = upper(?)
					;";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1, $upload_file_name);
		$stmt->execute();
		$r = $stmt->fetchAll();
		$c = $r[0]['count'];
		if ($c >  0){
			echo "BITSTREAMS_WITH_FILENAME_FOUND\n";
			if ($c == 1 && ! empty($upload_md5)){
								$SQL="SELECT  b.bitstream_id, b.md5_org
									FROM dsd.item2 i
									JOIN item2bundle ib ON i.item_id = ib.item_id
									JOIN bundle be ON ib.bundle_id = be.bundle_id
									JOIN bundle2bitstream bb ON ib.bundle_id = bb.bundle_id
									JOIN public.bitstream b ON bb.bitstream_id = b.bitstream_id
									WHERE i.status <> 'error' AND  upper(b.name) = upper(?)
									;";
									$stmt = $dbh->prepare($SQL);
									$stmt->bindParam(1, $upload_file_name);
									$stmt->execute();
									$r = $stmt->fetchAll();
									$bitstream_id  = $r[0][0];
									$md5_org = $r[0][1];
									//if (empty($md5_org)){
									if (empty($md5_org) || $md5_org != $upload_md5){
										//echo "ADD MD5 to bitstream $bitstream_id\n";
										$SQL="update public.bitstream SET md5_org = ? WHERE bitstream_id = ?";
										$stmt = $dbh->prepare($SQL);
										$stmt->bindParam(1,$upload_md5 );
										$stmt->bindParam(2,$bitstream_id );
										$stmt->execute();
									}
			}
		} else{
			echo "BITSTREAMS_WITH_FILENAME_NOT_FOUND\n";
		}

	}

	public static function ws_upload() {

		http_auth_check();
		$userName = get_user_name();
		if (empty($userName)){
			echo("auth error");
			return;
		}
		echo  "USER: $userName\n";
		if (! empty($_POST)){
			echo("\n============================\n");
			print_r($_POST);
			echo("\n============================\n");
			print_r($_FILES);
			echo("\n============================\n");
		}


		$get_post_boolean = function($var_name, $def_val){
			if (is_bool($def_val)){
				$def_val = $def_val ? 1 : 0;
			}
			$tmp  = get_post($var_name,$def_val);
			if ($tmp == 'Y' || $tmp == 'y' || $tmp == 1 || $tmp == '1' ){
				return true;
			}
			if ($tmp == 'N' || $tmp == 'n' || $tmp == 0 || $tmp == '0' ){
				return false;
			}

			return false;
		};


		$status = get_post('STATUS','private');
		$obj_type = get_post('OBJ_TYPE','other');
		$lang = get_post("LANG",'el');
		$folder = get_post("FOLDER",null);
		$title = base64_decode(get_post("TITLE",null));
		$author = base64_decode(get_post("AUTHOR",null));
		$file_name = trim(base64_decode(get_post("FILENAME",null)));
		$send_same_file_name_flag = $get_post_boolean('SEND_SAME_FILE_NAME',true);
		$upload_md5 = get_post("MD5",null);
		if ($status == 'public'){
			$status  = 'finish';
		}


		if (empty($file_name)){
			echo("ERROR FILE_NAME MISING");
			return null;
		}

		$upload_file_name = null;
		$upload_file_path = null;
		$upload_file_ext = null;

		//echo("### $file_name ###");
		preg_match('/\.(\w+)$/', $file_name, $matches);
		$tmp_ext = $matches[1];
		if (! empty($tmp_ext)){
			$upload_file_name = $file_name;
			$upload_file_ext = $tmp_ext;
		}



		$uploadData = isset($_FILES['upload'])?$_FILES['upload'] : null ;
		if (! empty( $uploadData)){
			$upload_file_path = $uploadData['tmp_name'];
			//list($upload_file_name, $upload_file_path,$upload_file_ext)= extract_file_info_from_upload_form('upload');
		}

		echo("\n============================\n");
		echo "N: $upload_file_name\n";
		echo "P: $upload_file_path\n";
		echo "E: $upload_file_ext\n";
		echo "FLAGS:\n";
		echo "SEND_SAME_FILE_NAME: $send_same_file_name_flag\n";
		echo("\n============================\n");

		if ( empty($upload_file_name) || empty($upload_file_path) || empty($upload_file_ext)) {
			echo "ERROR: UPLOAD_FILE\n";
			return;
		}



		echo("\n");
		echo("###############################################\n");
		echo("\n");
		/////////////////////////////////////////////////////////////////////////////

		$dbh = dbconnect();
		$item_id = null;

		if (!  $send_same_file_name_flag){
			//$SQL="SELECT count(*) from public.bitstream  where upper(name) = upper(?);";
				$SQL="SELECT count(*)
				FROM dsd.item2 i
				JOIN item2bundle ib ON i.item_id = ib.item_id
				JOIN bundle be ON ib.bundle_id = be.bundle_id
				JOIN bundle2bitstream bb ON ib.bundle_id = bb.bundle_id
				JOIN bitstream b ON bb.bitstream_id = b.bitstream_id
				WHERE status <> 'error' AND  upper(b.name) = upper(?)
				;";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $upload_file_name);
			$stmt->execute();
			$r = $stmt->fetchAll();
			$c = $r[0]['count'];
			if ($c >  0){
				echo "upload stoped\n";
				echo "bitstream with file name: $upload_file_name allredy exists\n";
				echo "\n";
				return;
			}
		}


		//$item_id = insert_item($dbh, $userName);
		$item_id = PDAO::insert_item($userName, $obj_type);
		echo ("NEW ITEM: " . $item_id . "\n");
		if (empty($item_id)){
			$dbh->rollback();
			echo ("ERROR NEW item_id ");
			return;
		}

		if (empty($title)){
			$title = $upload_file_name;
		}
		$title = str_replace('_',' ',$title);


		update_metadata($dbh, $item_id, DataFields::dc_title ,ARRAY(ARRAY($title,null,null)));
		update_metadata($dbh, $item_id, DataFields::dc_language_iso ,ARRAY(ARRAY($lang,null,null)));
		update_metadata($dbh, $item_id, DataFields::ea_obj_type ,ARRAY(ARRAY($obj_type,null,null)));
		update_metadata($dbh, $item_id, DataFields::ea_status ,ARRAY(ARRAY($status,null,null)));

		if (!empty($author)){
			update_metadata($dbh, $item_id, DataFields::dc_contributor_author ,ARRAY(ARRAY($author,null,null)));
		}

		$seq_id = null;
		$bundle_name = 'ORIGINAL';
		$out = "";

		$bitstream_id = null;
		$bundle_id = null;
		$bitstream_uuid = null;

		echo("upload_bitstream\n");
		list($bitstream_id,$bundle_id,$bitstream_uuid,$out_bitstream) = upload_bitsteam($upload_file_path, $upload_file_name, $upload_file_ext, $item_id, $seq_id, $bundle_name);
		echo($out_bitstream);
		echo("\n");


		if (! empty($upload_md5)){
			$SQL="update public.bitstream SET md5_org = ? WHERE bitstream_id = ?";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1,$upload_md5 );
			$stmt->bindParam(2,$bitstream_id );
			$stmt->execute();
		}

		//$out="#thumb_generate for item\n";
		//$all_flag = true;
		//$parent_flag = true;
		//thumbs_generate_from_bitstream($dbh, $bitstream_uuid, $out,$all_flag,$parent_flag);
		//echo "$out\n";


		echo("#generated metadata\n");
		#generated_metadata
		insert_generated_metadata($dbh, $item_id,$userName);

		echo("#collection handling\n");
		#collection handling
		$collection_id =  insert_collection($dbh, $item_id,$obj_type);

		#echo("#bibref: ($vivliografiki_anafora)\n");
		#update_bibref($dbh, $item_id,$vivliografiki_anafora);

		echo("#touch\n");
		touch_item($dbh, $item_id);

		echo("#item basic works\n");
		$out="";
		item_basic_works($dbh, $item_id, $bitstream_uuid, $out);
		echo($out);




		if (! empty($folder)){
			echo("folder work:\n");
			$folder_label = get_item_label($folder);
			if (empty($folder_label)){
				echo ("CANOT INSERT TO FOLDER $folder FOLDER NOT FOUND\n");
			} else {
				echo ("insert to folder: $folder_label ($folder)\n");
				$rel_type=DataFields::DB_item_realtion_type_member_of;
				$rel_type_label='member_of';
				$errors = insert_relation_items($item_id, $folder, $rel_type_label);
				foreach ($errors as $k=>$v){
					echo("ERROR at connect to folder $folder : $v\n");
				}
			}
		}

		return null;
	}









	public static function ws_biblionet() {
		$isbn = get_get('isbn');
		$rep = array();
		$cmd = BIN_DIR . 'biblionet.sh ' . $isbn;
		$tmp = exec($cmd,$out,$status);
		$c = 0;
		foreach ($out as $l){
			$c ++;
			if (! empty($l)){
				switch ($c) {
					case 1:
						$rep['title'] = $l;
						break;
					case 2:
						$rep['author'] = $l;
						break;
					case 3:
						$rep['publisher'] = $l;
						break;
					case 4:
						$rep['year'] = $l;
						break;
					case 5:
						$rep['isbn'] = $l;
						break;
					case 6:
						$rep['img'] = $l;
						break;
					case 7:
						$rep['url'] = $l;
						break;
					default:
						$rep['headers'][] = $l;
					break;
				}
			}

		}
		return json_encode($rep);
	}



	public static function ws_libgen_batch($md5){
		$item_id = null;

		$dbh = dbconnect();

		if (!empty($md5)){
			$SQL ="SELECT md5.md5, i.item_id
		FROM public.bitstream_md5  md5
		JOIN public.bundle2bitstream bb ON (bb.bitstream_id = md5.bitstream)
		JOIN public.item2bundle ib ON ib.bundle_id = bb.bundle_id
		JOIN dsd.item2 i ON (i.item_id = ib.item_id)
		WHERE
		i.obj_type = 'book'
		AND md5.md5 = ?
		AND i.item_id NOT IN (SELECT item_id FROM dsd.libgen WHERE xml is not null )
		ORDER BY dt_create desc limit 1";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $md5);
			$stmt->execute();
			if ($r = $stmt->fetch()){
				$md5 = $r[0];
				$item_id = $r[1];
			} else {
				$md5 = null;
				$item_id = null;
			}
		} else {
			$SQL ="SELECT md5.md5, i.item_id
			FROM public.bitstream_md5  md5
			JOIN public.bitstream b ON (b.bitstream_id = md5.bitstream)
			JOIN public.bitstreamformatregistry fr ON (fr.bitstream_format_id = b.bitstream_format_id)
			JOIN public.bundle2bitstream bb ON (bb.bitstream_id = md5.bitstream)
			JOIN public.item2bundle ib ON ib.bundle_id = bb.bundle_id
			JOIN dsd.item2 i ON (i.item_id = ib.item_id)
			WHERE
			i.obj_type = 'book'
			AND (fr.mimetype = 'application/pdf' OR fr.mimetype = 'image/vnd.djvu')
			AND i.item_id NOT IN (SELECT item_id FROM dsd.libgen WHERE xml is not null )
			AND md5.md5 NOT IN (SELECT md5 FROM dsd.libgen WHERE md5 is not null)
			ORDER BY dt_create desc limit 1";
			$stmt = $dbh->prepare($SQL);
			$stmt->execute();
			if ($r = $stmt->fetch()){
				$md5 = $r[0];
				$item_id = $r[1];
			}
		}



		$rep = array();
		if (empty($md5)){
			$rep['error'] = "CANOT FIND MD5";
			return $rep;
		}

		$rep['error'] = "";
		$rep['item_id'] = $item_id;

		$rep['md5'] =$md5;

		$data =  Pws::ws_libgen($md5);
		$rep['http_status'] = $data['http_status'];
		$rep['http_verb'] = $data['verb'];


		if ($data['curl_status'] == 'error'){
			$rep['result'] = 'error';
			$rep['error'] = $data['curl_error'];
			return $rep;
		}
		$libgenxml = $data['xml'];


		$rep['title'] = null;
		$rep['url'] = null;
		$rep['img'] = null;
		$rep['isbn'] = null;
		$rep['author'] = null;
		$rep['publisher'] = null;
		$rep['year'] = null;
		$rep['descr'] = null;



		if (empty($md5)  || ! isset($data['title']) || empty($data['title'])){
			$rep['result'] = 'empty_libgen_response';

			#$SQL = "insert into dsd.libgen (item_id) values (?)";
			$SQL = "insert into dsd.libgen (md5) values (?)";
			$stmt = $dbh->prepare($SQL);
			$stmt->bindParam(1, $md5);
			$stmt->execute();


		} else {
			$rep['result'] = 'ok';

			update_metadata($dbh, $item_id, DataFields::dc_title ,ARRAY(ARRAY($data['title'],null,null)));
			update_metadata($dbh, $item_id, DataFields::dc_contributor_author,ARRAY(ARRAY($data['author'],null,null)));
			update_metadata($dbh, $item_id, DataFields::ea_date_orgissued ,ARRAY(ARRAY($data['year'],null,null)));
			update_metadata($dbh, $item_id, DataFields::dc_identifier_isbn ,ARRAY(ARRAY($data['isbn'],null,null)));
			update_metadata($dbh, $item_id, DataFields::dc_publisher ,ARRAY(ARRAY($data['publisher'],null,null)));
			update_metadata($dbh, $item_id, DataFields::dc_descrption ,ARRAY(ARRAY($data['descr'],null,null)));
			update_metadata($dbh, $item_id, DataFields::ea_origin_url ,ARRAY(ARRAY($data['url'] . "|libgen",null,null)));
			update_metadata($dbh, $item_id, DataFields::ea_url_related ,ARRAY(ARRAY($data['img'] . "|libgen-img",null,null)));

			$rep['title'] = $data['title'];
			$rep['url'] = $data['url'];
			$rep['img'] = $data['img'];
			$rep['isbn'] = $data['isbn'];
			$rep['author'] = $data['author'];
			$rep['publisher'] = $data['publisher'];
			$rep['year'] = $data['year'];
			$rep['descr'] = $data['descr'];

			try {
				$SQL = "DELETE FROM dsd.libgen WHERE md5 = ?";
				$stmt = $dbh->prepare($SQL);
				$stmt->bindParam(1, $md5);
				$stmt->execute();

				$SQL = "insert into dsd.libgen (item_id, xml, url, img, md5) values (?,?,?,?,?)";
				$stmt = $dbh->prepare($SQL);
				$stmt->bindParam(1, $item_id);
				$stmt->bindParam(2, $libgenxml);
				$stmt->bindParam(3, $data['url']);
				$stmt->bindParam(4, $data['img']);
				$stmt->bindParam(5, $data['md5']);
				$stmt->execute();




				$thumb1 = $data['img'];
				if (! empty($thumb1)){

					list($upload_file_name, $upload_file_path, $upload_file_ext) = get_file_from_url($thumb1, "libgen_", false);
					#echo("$upload_file_name\n");
					#echo("$upload_file_path\n");
					#echo("$upload_file_ext\n");
					if (!empty($upload_file_name) && !empty($upload_file_path) ){
						$bundle_name_original="ORIGINAL";
						$bundle_id_original = insert_bundle($dbh, $bundle_name_original);
						insert_item2bundle($dbh, $item_id, $bundle_id_original);

						$thumb_uuid = get_safe_uiid($dbh);
						$thumb_bitstream_id = create_bitstream($dbh, $thumb_uuid, $upload_file_path, $upload_file_name, 1, SPOOL_dir_ok);
						//echo "create thumb bitstream: $thumb_bitstream_id , $thumb_uuid , (1) , $upload_file_name \n";
						insert_bundle2bitstream($dbh, $bundle_id_original, $thumb_bitstream_id);
					}
				}



			} catch (PDOException $e){
				$error = $e->getMessage();
				echo("ERROR: $error\n");
				error_log( $error, 0);
			}




		}

		return $rep;
	}

public static function ws_libgen($md5){
	$url = "http://www.libgen.org/book/index.php?md5=" . $md5;
	//$url = "http://127.0.0.1/libgen.xml";
//	phpinfo();
	//http://www.libgen.org/book/index.php?md5=26ECD2385270415B1E3C6B401BE1BCAE

	$rep = array();

	//$curl_log = fopen('php://output', 'w+')'
	//$curl_log = fopen("/tmp/curl.log", 'w+');
	$curl_log = fopen("php://memory", 'r+');
	//$curl_log = fopen("php://temp", 'r+');


 	$ch = curl_init();
 	$timeout = 35;
 	curl_setopt($ch, CURLOPT_URL, $url);
 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
 	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
 	curl_setopt($ch, CURLOPT_VERBOSE, true);
 	curl_setopt($ch, CURLOPT_STDERR, $curl_log);
 	//curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:16.0) Gecko/20100101 Firefox/16.0');
 	$data = curl_exec($ch);

 	$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$rep['http_status'] = $http_status;

 	if(curl_errno($ch))
 	{
 		//echo 'Curl error: ' . curl_error($ch);
 		$rep['curl_status'] = 'error';
 		$rep['curl_error'] = 'curl_error($ch)';
 	}
 	$rep['curl_status'] = 'ok';

 	curl_close($ch);

 	rewind($curl_log);
 	$verb = fread($curl_log, 10000 );
 	$rep['verb'] = $verb;
 	fclose($curl_log);



	$xml = simplexml_load_string($data);
	//var_dump($xml->book->title);
	$title = (String) $xml->book->title;
	$year = (String) $xml->book->year;
	$isbn = (String) $xml->book->isbn;
	$author = (String) $xml->book->authors;
	$img =  (String) $xml->book->coverurl;
	$publisher = (String) $xml->book->publisher;
	$descr = (String) $xml->book->descr;

	$tmp = intval($year);
	if (empty($tmp) || $tmp < 1200){
		$year = null;
	}

	if (! empty($img)){
			if (PUtil::strBeginsWith($img, "../")){
				$img = "http://www.libgen.org" . substr($img,2);
			} else if (PUtil::strBeginsWith($img, "/")){
				$img = "http://www.libgen.org" . $img;
			}
	}

	//$e = $data;
	//$e = preg_replace("/\ +/"," ",$data);
	//$e = base64_encode($data);


	$rep['title'] = $title;
	$rep['year'] = $year;
	$rep['isbn'] = $isbn;
	$rep['author'] = $author;
	$rep['img'] = $img;
	$rep['publisher'] =  $publisher;
	$rep['url'] = $url;
	$rep['descr'] = $descr;
	$rep['xml'] = $data;
	return $rep;
}













}
?>
