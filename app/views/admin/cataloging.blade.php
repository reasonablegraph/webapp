@section('content')
<?php auth_check_mentainer(); ?>

<?php

drupal_add_js("/_assets/js/putil.js");

?>

<style>
div.col1 {
	padding-right:30px;
	float:left;
}
div.col2 {
	float:left;
	padding-right:30px;
	border-left:1px solid gray;
}
div.col1t {
	padding-right:30px;
	float:left;
}
div.col2t {
	float:left;
	padding-right:30px;
}
div.column_header {
	margin:0px;
	padding-left:4px;
	font-size:1.4em;
	background-color:#E8E8E8;
	border-bottom: 1px solid gray;
	clear: both;
}
div.columns {
	display:table-cell;
	border:1px solid gray;
}
div.columnst {
	display:table-cell;
}

table.create   {
    background:#E5E5E5;
}

.create tr th {
    border: 2px solid #fff;
    background:#ccc;
    padding: 6px !important;
    text-align: center;
}

.create tr td {
    border: 2px solid #fff;
    background:#E5E5E5;
    padding: 6px !important;
    text-align: center;
}

</style>



<div class="row">
	<div class="panel panel-default">
		<div class="panel-heading"><?= $m!='a' ? tr('Search in Repository') : tr('Advance search in Repository');?> <?=tr(Config::get('arc.INSTALLATION_LEGEND')) ?></div>
		<div class="panel-body">

			<form method="get" class="arch-sform  form-horizontal" role="form">
		<?php
			if ($m != 'a'){
			?>
			<!-- Simple specific -->
				<input type="hidden" name="m" value="s" />

				<div class="form-group">


				 <div class="col-md-10">
		   		 <label for="terms" class="col-md-2 control-label"><?=tr('Term field')?>:</label>
					 <input id="terms" class="col-md-10 form-search" type="text" name="t" value="<?php echo($ss)?>" placeholder="<?=tr('Import term')?>" />
			  </div>



				</div>

		<?php } else { ?>
			<!-- Advance specific -->
				<input type="hidden" name="m" value="a"/>

			<div class="form-group">
					<div class="col-md-10">
  					<label for="terms" class="col-md-2 control-label"><?=tr('Search all')?>: </label>
						<input id="terms" class="col-md-10 text form-search" type="text" name="tt" value="<?php echo($sss)?>" placeholder="<?=tr('Import term')?>"/>
					</div>
					<div class="col-md-2"> </div>
			</div>

			<div class="form-group">
		  	  <div class="col-md-10">
						<label for="title" class="col-md-2 control-label"><?=tr('Title')?>: </label>
  					<input id="title" class="col-md-4 text form-search" type="text" name="l" value="<?php echo($l)?>" placeholder="<?=tr('Import title')?>"/>
				    <label for="y" class="col-md-2 control-label"><?=tr('Year')?>: </label>
						<input type="text" class="col-md-4 text form-search" name="y" value="<?php echo($y)?>" placeholder="<?=tr('Import year')?>" />
					</div>
			 	<div class="col-md-2"> </div>
			</div>

			<div class="form-group">
		   <div class="col-md-10">
		    <label for="author" class="col-md-2 control-label"><?=tr('Author')?>: </label>
				<input id="author" class="col-md-4 text form-search" type="text" name="a" value="<?php echo($a)?>" placeholder="<?=tr('Import author')?>" />
			   <label for="isbn" class="col-md-2 control-label"><?=tr('ISBN')?>: </label>
				<input id="isbn" class="col-md-4 text form-search" type="text" name="p" value="<?php echo($p)?>" placeholder="<?=tr('Import isbn')?>" />
			 </div>
			 <div class="col-md-2"> </div>
			</div>

			<div class="form-group">
		   <div class="col-md-10">
		    <label for="subject" class="col-md-2 control-label"><?=tr('Subject')?>: </label>
				<input id="subject" class="col-md-4 text form-search" type="text" name="subj" value="<?php echo($subj)?>" placeholder="<?=tr('Import subject')?>" />
			   <label for="digital_type" class="col-md-2 control-label"><?=tr('Item type')?>: </label>
				<select  id="digital_type" class="col-md-4 text form-search">
						<option value="undefined"><?=tr('All types')?> </option>
						<option value="pdf">PDF</option>
						<option value="daisy"><?=tr('DAISY text')?></option>
						<option value="epub">EPUB</option>
						<option value="docx">DOCX</option>
						<option value="wma">WMA</option>
						<option value="mp3">MP3</option>
				</select>
			 </div>
			 <div class="col-md-2"> </div>
		 </div>

	<?php  } ?>


	<div class="form-group">

			<div class="col-md-10">
		   <div class="col-md-10  col-md-offset-2 search-buttons " >
				<button type="submit" value="search" class="btn btn-default" ><?=tr('Search')?></button>
				<button name="clear" value="clear" onclick="clearForm(this.form);" class="btn btn-default" ><?=tr('Clear')?></button>
			</div>
			</div>
			  <!-- <div class="col-md-7 col-md-offset-2">
				 <input type="submit" value="<?=tr('Search')?>" class="btn btn-default" /> <input type="button" name="clear" value="<?=tr('Clear')?>" onclick="pl.clearForm(this.form);"
							class="btn btn-default">
				</div>-->
			<div class="col-md-2">
					<?php if ($m == 's'){ ?>
						<?php if (variable_get('arc_search_display_advance_link')): ?>
							<a class="m_search_link" href="/prepo/new_item?m=a"><?=tr('Advance search')?></a>
						<?php endif ?>
						<?php  } else { ?>
							<a class="m_search_link" href="/prepo/new_item"><?=tr('Simple search')?></a>
						<?php  } ?>
		   </div>
	</div>


	    <!-- style="padding-left:10px; border: 1px solid; padding: 5px 10px; border-radius: 4px;
    cursor: pointer;
    display: inline-block;
    font-size: 14px;
    font-weight: normal;
    line-height: 1.42857;
    margin-bottom: 0;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
    " -->


			</form>
		</div>
	</div>
</div>




<?php
	//NEW ITEMS MENU
	$conf = null;
// 	if (! empty($username)){
// 		$conf = Config::get('arc_new_items.' . $username);
// 	}
	$projectName = Config::get('arc.PROJECT_NAME');
	$newItems = Config::get('arc.new_items');
	$commands_set = Config::get('arc.commands_set');

	if (!empty($newItems)){
		$conf = Config::get('arc_new_items.'.$newItems,array());
	}elseif (!empty($commands_set)){
		$conf = Config::get('arc_new_items.'.$commands_set,array());
	}elseif (!empty($projectName)){
		$conf = Config::get('arc_new_items.'.$projectName,array());
	}

	if (empty($conf)){
		$conf = Config::get('arc_new_items._DEFAULT_',array());
	}

	///////////
	$is_admin = ArcApp::user_access_admin();

	if($is_admin){
		if (isset($conf['admin'])){
			$new_entities = $conf['admin'];
		}else{
			$new_entities = $conf['staff'];
		}
	}else{
		$new_entities = $conf['staff'];
	}
	///////////


	foreach($new_entities as $i=>$l){
				echo("<table class='create table table-bordered'>");
				printf('<thead><tr><th colspan="12" style="text-align: center;">%s</th></tr></thead>',tr($i));
				foreach($l as $ii=>$ll){
								echo("<tr>");
								foreach($ll as $obj_type=>$label){
									echo("<td><a href='/prepo/edit_step1?br=2&rd=$obj_type'>$label</a></td>");
								}
								echo("</tr>");
				}
				echo("</table>");
				echo("<br>");
	}


// 	foreach($conf as $user=>$ent_array){
// 				foreach($ent_array as $i=>$l){
// 					echo("<table class='create table table-bordered'>");
// 					printf('<thead><tr><th colspan="12" style="text-align: center;">%s</th></tr></thead>',tr($i));
// 					foreach($l as $ii=>$ll){
// 						echo("<tr>");
// 						foreach($ll as $obj_type=>$label){
// 							echo("<td><a href='/prepo/edit_step1?br=2&rd=$obj_type'>$label</a></td>");
// 						}
// 						echo("</tr>");
// 					}
// 					echo("</table>");
// 					echo("<br>");
// 				}
// 	}


?>




<table class="table table-striped table-bordered">
	<thead>
		<th style="text-align: center; background: #E5E5E5; padding: 6px !important;" colspan="12">
			<?php
			$total_cnt = count($results);
			if ($total_cnt == 0){
				printf("%s.",tr('No entries found'));
			}else{ ?>
				{{trChoise('Found',$total_cnt)}} <strong>{{$total_cnt}}</strong> {{trChoise('Entry',$total_cnt)}}:
			<?php
			} ?>
		</th>
	</thead>

<?php
//lock edit form submitter
	$user = ArcApp::username();
	$is_admin = ArcApp::user_access_admin();
	$edit_lock_owner = Config::get('arc.owner_edit_form_lock',0);
?>

@include('includes.cataloging-table')



<?php
if (empty($ss)){
			echo('</tbody>');
			echo('<tfoot><tr><th colspan="4" style="text-align: center;">');
			$new_rec = $o - $limit;
			if ($new_rec >= 0){
				printf('[<a href="%s?o=%s"> %s </a>]&nbsp&nbsp',UrlPrefixes::$cataloging, $new_rec, tr('Newer records'));
			}
			$old_rec = $o + $limit;
			if(! ($total_cnt < $limit)){
				printf('[<a href="%s?o=%s"> %s </a>]',UrlPrefixes::$cataloging, $old_rec, tr('Older records'));
			}
			echo("</th></tr></tfoot>\n");
}
	?>




</table>

<?php
// PSnipetss::block_pagingBlock($results,$m,$paging_data);
?>


<?php
//echo("<pre>");print_r($results); echo("</pre>");
?>

@stop



