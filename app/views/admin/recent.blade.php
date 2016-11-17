@section('content')
<?php auth_check_mentainer(); ?>
<div class="arch-wrap">
<?php

##@##drupal_add_css(ARCHIVE_ASSETS_PATH . 'css/recent.css');

$lang = get_lang();
#echo("LANG:$lang");

$s = get_get('s');
$t = get_get('t');
$f = get_get('f');
$ft = get_get('ft');

$orphan_flag = (!empty($f) && $f=='or' ? true : false);

$org_user = (!empty($ft) && $ft=='ur' ? true : false);

$edit_flag = false;
$admin_flag = user_access_admin();

if (user_access_mentainer() || $admin_flag){
	$edit_flag = true;
}

$status = Config::get('arc.ITEM_STATUS_FINISH');
if (PUtil::user_access_item_submiter()){
	if ($s == Config::get('arc.ITEM_STATUS_FINISH')){
		$status = Config::get('arc.ITEM_STATUS_FINISH');
	}
	if ($s == Config::get('arc.ITEM_STATUS_PENDING')){
		$status = Config::get('arc.ITEM_STATUS_PENDING');
		$edit_flag = true;
	}
	if ($s == Config::get('arc.ITEM_STATUS_INCOMPLETE')){
		$status = Config::get('arc.ITEM_STATUS_INCOMPLETE');
	}
}
//if (user_access(Config::get('arc.PERMISSION_VIEW_ITEMS_ALL_STATUS'))){
if (ArcApp::has_permission(Permissions::$VIEW_ITEMS_ALL_STATUS)){
	if ($s == Config::get('arc.ITEM_STATUS_FINISH')){
		$status = Config::get('arc.ITEM_STATUS_FINISH');
	}
	if ($s == Config::get('arc.ITEM_STATUS_ERROR')){
		$status = Config::get('arc.ITEM_STATUS_ERROR');
	}
	if ($s == Config::get('arc.ITEM_STATUS_INCOMPLETE')){
		$status = Config::get('arc.ITEM_STATUS_INCOMPLETE');
	}
	if ($s == Config::get('arc.ITEM_STATUS_PENDING')){
		$status = Config::get('arc.ITEM_STATUS_PENDING');
	}
	if ($s == Config::get('arc.ITEM_STATUS_PRIVATE')){
		$status = Config::get('arc.ITEM_STATUS_PRIVATE');
	}
	if ($s == Config::get('arc.ITEM_STATUS_INTERNAL')){
		$status = Config::get('arc.ITEM_STATUS_INTERNAL');
	}
	if ($s == 'direct_only'){
		$status = 'direct_only';
	}

	if ($s == Config::get('arc.ITEM_STATUS_HIDDEN')){
		$status = Config::get('arc.ITEM_STATUS_HIDDEN');
	}

	if ($s == 'all'){
		$status = 'all';
	}
	if ($s == 'all-dev'){
		$status = 'all-dev';
	}

}

$tok = null;
if (! empty($t) && $admin_flag){
	$dbh = dbconnect();
	$SQL="select 1 from dsd.obj_type where name=?";
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $t);
	$stmt->execute();
	if ($stmt->fetch()){
		$tok = $t;
	}
}



if ($status == Config::get('arc.ITEM_STATUS_FINISH')){
	//$edit_flag = user_access(Config::get('arc.PERMISSION_VIEW_ITEMS_ALL_STATUS')) ? true : false;
	$edit_flag = ArcApp::has_permission(Permissions::$VIEW_ITEMS_ALL_STATUS)? true : false;
}

	$o = PUtil::reset_int(get_get("o"),0);
	$dbh = dbconnect();
	$limit = 40;

	if (!empty($tok)){
		if ($orphan_flag){
			if($org_user){
				$user = ArcApp::user();
				$flags = 'ORPHAN';
				if (!empty($user)){
					$org_id = $user['org_id'];
					$organization_flag = "ORG:$org_id";
					$flags_str = sprintf("ARRAY['%s','%s']",$flags, $organization_flag);
					$SQL=sprintf("SELECT %s FROM dsd.item2 i WHERE obj_type = '%s' AND flags @> %s order by i.dt_create desc, i.label offset ? limit %s",Config::get('arc.ITEM_LIST_SQL_FIELDS'), $tok, $flags_str,  $limit);
				}else{
					$flags_str = sprintf("ARRAY['%s']",$flags);
					$SQL=sprintf("SELECT %s FROM dsd.item2 i WHERE obj_type = '%s' AND flags @> %s order by i.dt_create desc, i.label offset ? limit %s",Config::get('arc.ITEM_LIST_SQL_FIELDS'), $tok, $flags_str,  $limit);
				}
			}else{
				$flags = 'ORPHAN';
				$flags_str = sprintf("ARRAY['%s']",$flags);
				$SQL=sprintf("SELECT %s FROM dsd.item2 i WHERE obj_type = '%s' AND flags @> %s order by i.dt_create desc, i.label offset ? limit %s",Config::get('arc.ITEM_LIST_SQL_FIELDS'), $tok, $flags_str,  $limit);
			}
		}else{
			$SQL=sprintf("SELECT %s FROM dsd.item2 i WHERE obj_type = '%s' order by i.dt_create desc, i.label offset ? limit %s",Config::get('arc.ITEM_LIST_SQL_FIELDS'), $tok, $limit);
		}
	} elseif ($status == 'all'){
 		$SQL=sprintf("SELECT %s FROM  dsd.item2 i  WHERE status <> '%s' order by i.dt_create desc, i.label offset ? limit %s"
 				,Config::get('arc.ITEM_LIST_SQL_FIELDS'), Config::get('arc.ITEM_STATUS_INTERNAL'), $limit);

//$SQL=sprintf("SELECT* FROM  dsd.item2 i order by i.dt_create desc, i.label offset ? limit %s", $limit);

	} elseif ($status == 'all-dev'){
		$SQL=sprintf("SELECT %s FROM  dsd.item2 i  order by i.dt_create desc, i.label offset ? limit %s"
		,Config::get('arc.ITEM_LIST_SQL_FIELDS'), $limit);
	} else {
		$SQL=sprintf("SELECT %s FROM  dsd.item2 i WHERE status='%s' order by i.dt_create desc, i.label offset ? limit %s"
		,Config::get('arc.ITEM_LIST_SQL_FIELDS'),$status, $limit);
	}
	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $o);
	$stmt->execute();

	$members = 	$stmt->fetchAll();


	$obj_type_names = PUtil::get_object_type_names($dbh);

//	echo('<div class="clear bdown">&nbsp;</div>');

	echo('<table id="members" class="table table-striped table-bordered">');
	echo('<thead>');
	if ($status == Config::get('arc.ITEM_STATUS_FINISH')){
		printf('<tr><th colspan="4" style="text-align: center;">&nbsp;%s</th></tr>',tr('Πρόσφατες καταχωρήσεις'));
	} else {
		printf('<tr><th colspan="4" style="text-align: center;">&nbsp; %s (%s)</th></tr>',tr('Πρόσφατες καταχωρήσεις'),$status);
	}
	echo('</thead>');
	echo('<tbody valign="top">');
	echo("\n");

// 	PUtil::item_list($members, $obj_type_names,$edit_flag);
	 ?>
	 @include('includes.recent-table')
	 <?php

	echo('</tbody>');
	echo('<tfoot><tr><th colspan="4" style="text-align: center;">');
#	printf('[<a href="/archive/search?m=s">Περισσότερες καταχωρίσεις</a>]');
	$o = $o + $limit-1;
	if ($status == Config::get('arc.ITEM_STATUS_FINISH')){
		printf('[<a href="/archive/recent?t=%s&o=%s">%s </a>]',urlencode($tok),$o,tr('Παλαιότερες καταχωρίσεις'));
	}else{
		printf('[<a href="/archive/recent?o=%s&s=%s&t=%s">%s </a>]',$o,$status, urlencode($tok), tr('Παλαιότερες καταχωρίσεις'));
	}
	echo("</th></tr></tfoot>\n");
	echo('</table>');


?>
</div>

@stop