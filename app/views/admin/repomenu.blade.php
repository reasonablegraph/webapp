@section('content')
<?php auth_check_mentainer(); ?>
<?php

drupal_set_title ( "users menu" );
?>

<style>
div.col1 {
	padding-right: 30px;
	border-right: 1px solid gray;
	float: left;
}

div.col2 {
	float: left;
	padding-right: 30px;
}

div.col1t {
	padding-right: 30px;
	float: left;
}

div.col2t {
	float: left;
	padding-right: 30px;
}

div.column_header {
	margin: 0px;
	padding-left: 4px;
	font-size: 1.4em;
	background-color: #E8E8E8;
	border-bottom: 1px solid gray;
	clear: both;
}

div.columns {
	display: table-cell;
	border: 1px solid gray;
}

div.columnst {
	display: table-cell;
}
</style>

<?php
// echo("<pre>");
// print_r($GLOBALS);
// echo("</pre>");
/*
 * echo("<pre>");
 * global $user;
 * $uid = user_authenticate('maria', 'maria');
 * $user = user_load($uid);
 * echo("\n===========\n");
 * echo("$uid\n\n");
 * print_r($user);
 *
 * watchdog('user', 'Session opened for %name.', array('%name' => $user->name));
 * // Update the user table timestamp noting user has logged in.
 * // This is also used to invalidate one-time login links.
 * $user->login = REQUEST_TIME;
 * db_update('users')
 * ->fields(array('login' => $user->login))
 * ->condition('uid', $user->uid)
 * ->execute();
 * // Regenerate the session ID to prevent against session fixation attacks.
 * // This is called before hook_user in case one of those functions fails
 * // or incorrectly does a redirect which would leave the old session in place.
 * drupal_session_regenerate();
 * echo("\n===========\n");
 * echo("</pre>");
 */
?>
<?php if (PUtil::user_access_item_submiter() ): ?>
<ul>
	<li><a href="/prepo/spool"> pending spool </a></li>
</ul>
<ul>
	<li><a
		href="/archive/recent?s=<?=Config::get('arc.ITEM_STATUS_PENDING')?>"><?=Config::get('arc.ITEM_STATUS_PENDING')?></a>

</ul>

<?php else: ?>

<div class="row">
	<div class="col-sm-6">
		<div class="panel panel-default repo-admin">
			<div class="panel-heading">
				<h3 class="panel-title">Spools</h3>
			</div>
			<div class="panel-body">
				<div class="col-sm-8">
								<ul>
									<li><a href="/prepo/spool">Pending spool </a></li>
									<li><a href="/prepo/spool?d=2"> Submited spool</a></li>
								</ul>

						<?php
		$print_flag = variable_get ( 'arc_display_websites', 0 );
		if ($print_flag) :
			?>
			    <ul>
						<li>Web Sites
								<ul>
									<li><a href="/prepo/sites/spool">Pending spool</a></li>
								</ul>
							</li>
					</ul>
						<?php endif; ?>
				</div>
			</div>
	</div>



	<div class="panel panel-default repo-admin">
			<div class="panel-heading">
				<h3 class="panel-title">Metadata</h3>
			</div>
			<div class="panel-body">
				<div class="col-sm-8">
								<ul>
									<li><a href="/prepo/metadata_search">Metadata search </a></li>
									<li><a href="/prepo/metadata_stats"> Metadata stats</a></li>
								</ul>
				</div>
			</div>
	</div>


		<div class="panel panel-default repo-admin">
			<div class="panel-heading">
				<h3 class="panel-title">Reports</h3>
			</div>
			<div class="panel-body">
				<div class="col-sm-8">
								<ul>
									<li><a href="/prepo/report1"  target="_blank">Manifestations Report</a></li>
									<li><a href="/prepo/download-log" >Files Downloads Log</a></li>
									@if (variable_get('lemmas_report'))
										<li><a href="/prepo/researchers-report" >Researchers Report</a></li>
									@endif
								</ul>
				</div>
			</div>
		</div>

		<div class="panel panel-default repo-admin">
			<div class="panel-heading">
				<h3 class="panel-title">Actions / Logs</h3>
			</div>
			<div class="panel-body">
				<div class="col-sm-8">
								<ul>
									<li><a href="/prepo/action2">Staff Actions/Logs</a></li>
									<li><a href="/prepo/action1">Staff Actions ( last hour activity )</a></li>
									<li><a href="/prepo/reset-log">Reset graph log</a></li>
								</ul>
				</div>
			</div>
		</div>

</div>



	<div class="col-sm-6">
		<div class="panel panel-default repo-admin">
			<div class="panel-heading">
				<h3 class="panel-title">Items Lists</h3>
			</div>
			<div class="panel-body">
				<div class="col-sm-6">
					<ul>
						<li><a href="/archive/recent?s=all">all</a></li>

						<li><a
							href="/archive/recent?s=<?=Config::get('arc.ITEM_STATUS_INCOMPLETE')?>"><?=Config::get('arc.ITEM_STATUS_INCOMPLETE')?></a></li>

						<li><a
							href="/archive/recent?s=<?=Config::get('arc.ITEM_STATUS_ERROR')?>"><?=Config::get('arc.ITEM_STATUS_ERROR')?></a></li>

						<li><a
							href="/archive/recent?s=<?=Config::get('arc.ITEM_STATUS_PENDING')?>"><?=Config::get('arc.ITEM_STATUS_PENDING')?></a></li>

						<li><a
							href="/archive/recent?s=<?=Config::get('arc.ITEM_STATUS_PRIVATE')?>"><?=Config::get('arc.ITEM_STATUS_PRIVATE')?></a></li>

						<li><a
							href="/archive/recent?s=<?=Config::get('arc.ITEM_STATUS_HIDDEN')?>"><?=Config::get('arc.ITEM_STATUS_HIDDEN')?></a></li>

						<li><a href="/archive/recent?s=direct_only">direct_only</a>

						<li><a
							href="/archive/recent?s=<?=Config::get('arc.ITEM_STATUS_INTERNAL')?>"><?=Config::get('arc.ITEM_STATUS_INTERNAL')?></a></li>
					</ul>
				</div>
				<div class="col-sm-6">
					<ul>
						<li><a href="/archive/recent?s=all-dev">all (dev)</a></li>
						<li><a href="/prepo/node_stats">all types</a></li>
<?php
	$print_flag = variable_get ( 'arc_display_articles', 0 );
	if ($print_flag) :
		?>
						<li><a href="/archive/recent?t=article">articles</a></li>
<?php endif;?>
<?php
	$print_flag = variable_get ( 'arc_display_notes', 0 );
	if ($print_flag) :
		?>
						<li><a href="/archive/recent?t=note">notes</a></li>
<?php endif;?>
						<li><a href="/prepo/all_users_items">all users entries</a></li>
						<li><a href="/prepo/user_items"> user entries</a></li>
						<li><a href="/archive/recent?t=digital-item"> items</a></li>
						<li><a href="/archive/recent?t=digital-item&f=or"> all orphans items</a></li>
						<li><a href="/archive/recent?t=digital-item&f=or&ft=org"> provider's orphans items</a></li>

<!-- 						<li><a href="/archive/recent?t=bitstream"> bitstreams</a> -->
						<li><a href="/prepo/submits"> submits</a></li>
						<li><a href="/prepo/submitsactive"> active submits</a></li>
						<li><a href="/prepo/submitserror"> errored submits</a></li>
<!-- 						<li><a href="/archive/recent?t=silogi"> collections (folders)</a> -->
					</ul>

				</div>
			</div>
		</div>

	</div>
</div>


	@include('includes.repomenu-links')


<div class="row">
	<div class="col-sm-12">


<?php if (false): ?>

<ul class="list-group">
	<li class="list-group-item"><a href="/prepo/subjects/relation">tag relations</a></li>
	<li class="list-group-item"><a href="/prepo/merge_subjects">merge tags</a></li>
	<li class="list-group-item"><a href="/prepo/items/relation">item relations</a></li>

<?php
		$print_flag = variable_get ( 'arc_display_serials', 0 );
		if ($print_flag) :
			?>
<li class="list-group-item"><a href="/prepo/serials_np">serials not in serials collection</a></li>
<?php endif;?>

<li class="list-group-item"><a href="/prepo/metadata_stats">metadata stats</a></li>
	<li class="list-group-item"><a href="/prepo/metadata_search">metadata search</a></li>
	<li class="list-group-item"><a href="/prepo/subject_stats">subjects stats</a></li>
</ul>



<?php
		$print_flag = variable_get ( 'arc_display_artifacts', 0 );
		if ($print_flag) :
			?>
<ul class="list-group">
	<li class="list-group-item"><a href="/prepo/artifacts_list">artifacts list</a></li>
	<li class="list-group-item"><a href="/prepo/artifacts_stats">artifacts stats</a></li>
</ul>
<?php endif;?>

<?php
		$print_flag = variable_get ( 'arc_firefox_ext_install_link', 0 );
		if ($print_flag) :
			?>
<br />
<ul class="list-group">
	<li class="list-group-item"><a href="/prepo/firefoxext"> firefox extension install</a></li>
</ul>
<?php endif;?>

<?php endif;?>

<!-- <br /> -->
<?php  if (ArcApp::has_permission(Permissions::$ADMIN )): ?>
<!--   <ul class="list-group"> -->
<!-- 	<li class="list-group-item"><a href="/prepo/elements_item_ref">item refs</a></li> -->
<?php
		$print_flag = variable_get ( 'arc_display_advance', 0 );
		if ($print_flag) :
			?>
<!--  <li><a href="/prepo/isis">isis</a></li> -->
<!-- 	<li class="list-group-item"><a href="/prepo/menu_advance">advance menu</a></li> -->
<?php endif;?>
<!-- </ul> -->
<?php endif;?>



<?php endif;?>

<!-- <ul class="list-group"> -->
<!-- 	<li class="list-group-item"><a href="/prepo/update_folder_thumbs">update folder thumbnails</a> -->
<!-- 	</li> -->
<!-- </ul> -->
<!--
<h2>edit item</h2>
<form method="get" action="/prepo/edit_step2">
<input type="text" name="i"/>
<input type="submit" value="view"/>
</form>
-->



	</div>

</div>


@stop