@include('includes.edit_step3-flashes')

<?php auth_check_mentainer(); ?>

<?php
$app = App::make('arc');
if ($app->arcfe == 'testrg') {
	echo('<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"/>');
}
?>

<?php
   //lock edit form submitter
   $item_user_create = $item['user_create'];
   $user = ArcApp::username();
   $is_admin = ArcApp::user_access_admin();
   $edit_lock_owner = Config::get('arc.owner_edit_form_lock',0);
   $edit_link = true;
   if ( $edit_lock_owner && $item_user_create!= $user && !$is_admin){
   	$edit_link = false;
   }

   $json = json_decode($item['jdata'], true);
   if(!empty($json['label'])){
   	$label= $json['label'];
   }else{
   	$label = $item['label'];
   }


 ?>

@include('includes.step3-links')

<style>
tr.secondary td {
	font-size:11px;
}
</style>

<h1  class="admin item-title">{{{$label}}}</h1>

	<div class="panel panel-primary">
<table class="table table-bordered table-condensed">
<thead class="a_thead"><tr><th colspan="8"><span class="a_shead">Basics</span></th></tr></thead>
	<!-- <tr><td colspan="1">Label</td><td colspan="2"> {{{$item['label']}}}</td></tr>-->
	<tr >
		<td><strong>Type:</strong> {{{tr($item['obj_type'])}}}</td>
		<td><strong>ID:</strong> {{{$item_id}}}</td>
		<td><strong>Status:</strong> {{{$item['status']}}}</td>
	</tr>
<!-- 	<tr class="secondary"> -->
	<tr>
		<td colspan="1"><strong>Create:</strong> {{{ (new DateTime($item['dt_create']))->format('d/m/Y H:i') }}}</td>
		<td colspan="1"><strong>Update:</strong> {{{ (new DateTime($item['dt_update']))->format('d/m/Y H:i') }}}</td>
		<td colspan="1"><strong>Submitted by:</strong> {{$item['user_create']}} @if(!empty($item['user_org']))({{$item['user_org']}})@endif</td>
	</tr>
	<!-- <tr><td>{{{tr('title')}}}</td><td> {{{$item['title']}}}</td></tr> -->
</table>
	</div>

{{-- @if (count($relations) >0) --}}
	@include('includes.item-relations')
{{--@else
<h3>{{{tr('no relations')}}}</h3>
@endif --}}


	@include('includes.bitstreams')


<?php

$action = sprintf ( '?i=%s', $item_id );
$display_bundle = true;
$display_seq_id = true;

$demo_enable = Config::get('arc.demo_enable',0);
?>

@if ($edit_link)
	 @if (!$demo_enable || $is_admin)
		<div class="panel panel-primary">
		  <div class="a_thead a_bitstream">
			 <?php echo tr('Upload bitstream');?>
		  </div>
		  <div class="panel-body bitstream">
				@include('admin.bitstream_upload_form', ['obj_class' =>  $item ['obj_class']])
		  </div>
		</div>
	@else
		<div class="panel panel-primary">
		  <div class="a_thead a_bitstream">
			 <?php echo tr('Upload bitstream');?>
		  </div>
		  <div class="panel-body bitstream demo">
				Upload functionality has been disabled for this demo account for security reasons.
				<br>Please get in contact with the admins (<a href="mailto:info@reasonablegraph.org">info@reasonablegraph.org</a>) to provide you with an account with full rights.
		  </div>
		</div>
	@endif

@endif

@if ($item['obj_class'] == 'auth-manifestation' && $edit_link)
	<div class="row btnadd">
	<div class="col-sm-12">
		<a href="{{{UrlPrefixes::$item_edit_step1}}}?aft=1&afti={{{$item_id}}}"
			class="btn btn-primary"> <span class="glyphicon glyphicon-plus"
			aria-hidden="true"></span> Add item
		</a>
	</div>
	</div>
@endif

@include('includes.step3-links')




<?php
// echo("<pre>");
// print_r($item);
// print_r($relations);
// echo("</pre>");
?>
