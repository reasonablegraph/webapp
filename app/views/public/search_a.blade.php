@section('content')

<?php


$display_lang_select_flag = variable_get('arc_search_display_lang_select');
$term_search_flag = false;
if ( !empty($ss) || !empty($sss) ){
	$term_search_flag = true;
}


?>
<?php

drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'js/search.js');

if (user_access_admin()){
	drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'js/list_edit.js');
}
if ($m == "a") {
	drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'js/search_m.js');
} else {
	drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'js/search_s.js');
}

//use League\Url\UrlImmutable;
if (Config::get('arc.LOAD_JS')){
	# laravel jquery
	drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/autocomplete/jquery-ui/external/jquery/jquery.js');
}

drupal_add_js(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/jquery-ui/jquery-ui.min.js');
drupal_add_css(Config::get('arc.ARCHIVE_ASSETS_PATH').'vendor/jquery-ui/themes/smoothness/jquery-ui.min.css');

?>


<script>
jQuery(document).ready(function($) {

    var $container = $('#thl_3');
    var $container2 = $('#thl_4');

    // initialize Masonry after all images have loaded
    $container.imagesLoaded( function() {
      $container.masonry();
    });
    $container2.imagesLoaded( function() {
      $container2.masonry();
    });

});
</script>





{{-- ########################################################################################### --}}
{{-- ########################################################################################### --}}
{{-- HTML                                                                                        --}}
{{-- ########################################################################################### --}}
{{-- ########################################################################################### --}}


<div class="row"  id="searchfconteiner"  <?php echo $term_search_flag ?  'aria-hidden="true"' : 'aria-hidden="false"'; ?>>


<div class="panel panel-default" role="search" aria-label="<?=tr('search form') ?>" >
  <div class="panel-heading" ><h1>
  <?= $m!='a' ? tr('Search in Repository') : tr('Advance search in Repository');?> <?=tr(Config::get('arc.INSTALLATION_LEGEND')) ?></h1></div>

  <div class="panel-body">

<form method="get" class="arch-sform  form-horizontal" >
	<input type="hidden" name="lang" value="<?=$lang?>"/>

	<?php
	if ($m != 'a'){
		$terms_class ="sterm_long";
		if ($display_lang_select_flag){
			$terms_class ="sterm";
		}

	?>
		<!-- Simple specific -->
		<input type="hidden" name="m" value="s"/>



		<div class="form-group">
		    <div class="col-md-10">
		    <label for="terms" class="col-md-2 control-label"><?=tr('Term field')?>:</label>
				<input  class="col-md-10  <?=$terms_class?> form-search" type="text" name="t" value="<?php echo($ss)?>" placeholder="<?=tr('Import term')?>" />
			</div>
		    <div class="col-md-2">

					<?php if ($display_lang_select_flag): ?>
						<?php  if ($m != 'a'): ?>
							<?php  /*
							<label for="c"><?=tr('Είδος')?>: </label>
							<select name="c" class="title" id="select_idos">
							  <option value="0" <?php if ($c == 0){echo 'selected="selected"'; } ?> ><?=tr('Oλα')?></option>
							  <option value="3" <?php if ($c == 3){echo 'selected="selected"'; } ?> ><?=tr('Περιοδικά')?></option>
							  <option value="4" <?php if ($c == 4){echo 'selected="selected"'; } ?> ><?=tr('Εφημερίδες')?></option>
							  <option value="5" <?php if ($c == 5){echo 'selected="selected"'; } ?> ><?=tr('Μπροσούρες')?></option>
							  <option value="8" <?php if ($c == 8){echo 'selected="selected"'; } ?> ><?=tr('Βιβλία')?></option>
							  <option value="9" <?php if ($c == 9){echo 'selected="selected"'; } ?> ><?=tr('web-sites')?></option>
							  <option value="13" <?php if ($c == 13){echo 'selected="selected"'; } ?> ><?=tr('Συλλογές')?></option>
							</select>
							*/
							?>
							<label class="element-invisible" for="select_lang"><?=tr('Γλωσσα')?>: </label>
							<select name="sl" class="title form-control" id="select_lang">
							  <option value="0" <?php  if ($sl == '0'){echo 'selected="selected"'; } ?> ><?=tr('Επιλογή Γλώσσας')?></option>
							  <option value="el" <?php if ($sl == 'el'){echo 'selected="selected"'; } ?> ><?=tr('Ελληνικά')?></option>
							  <option value="en" <?php if ($sl == 'en'){echo 'selected="selected"'; } ?> ><?=tr('English')?></option>
							</select>

						<?php endif; ?>
						<?php endif; ?>
			</div>
		</div>


	<?php } else { ?>
	<!-- Advance specific -->
		<input type="hidden" name="m" value="a"/>

		<div class="form-group">
		  <div class="col-md-10">
  			<label for="terms" class="col-md-2 control-label"><?=tr('Search all')?>: </label>

				<input class="col-md-10 text form-search" type="text" name="tt" value="<?php echo($sss)?>" placeholder="<?=tr('Import term')?>"/>
			</div>
			 <div class="col-md-2"> </div>
		</div>

<?php
/*
		<div class="clear">&nbsp;</div>

		<div class="ui-widget selem">
			<label for="c"><?=tr('Είδος')?>: </label>
			<select name="c" class="title">
			  <option value="0" <?php if ($c == 0){echo 'selected="selected"'; } ?> ><?=tr('Ολα')?></option>
			  <option value="3" <?php if ($c == 3){echo 'selected="selected"'; } ?> ><?=tr('Περιοδικά')?></option>
			  <option value="4" <?php if ($c == 4){echo 'selected="selected"'; } ?> ><?=tr('Εφημερίδες')?></option>
			  <option value="8" <?php if ($c == 8){echo 'selected="selected"'; } ?> ><?=tr('Βιβλία')?></option>
			  <option value="9" <?php if ($c == 9){echo 'selected="selected"'; } ?> ><?=tr('web-sites')?></option>
			  <option value="13" <?php if ($c == 13){echo 'selected="selected"'; } ?> ><?=tr('Συλλογές')?></option>
			</select>
		</div>
*/
?>

		<div class="form-group">
		    <div class="col-md-10">
				<label for="title" class="col-md-2 control-label"><?=tr('Title')?>: </label>
  			<input class="col-md-4 text form-search" type="text" name="l" value="<?php echo($l)?>" placeholder="<?=tr('Import title')?>"/>
		    <label for="y" class="col-md-2 control-label"><?=tr('Year')?>: </label>
				<input type="text" class="col-md-4 text form-search" name="y" value="<?php echo($y)?>" placeholder="<?=tr('Import year')?>" />
			</div>
			 <div class="col-md-2"> </div>
		</div>



		<div class="form-group">
		   <div class="col-md-10">
		    <label for="author" class="col-md-2 control-label"><?=tr('Author')?>: </label>
				<input class="col-md-4 text form-search" type="text" name="a" value="<?php echo($a)?>" placeholder="<?=tr('Import author')?>" />
			   <label for="isbn" class="col-md-2 control-label"><?=tr('ISBN')?>: </label>
				<input class="col-md-4 text form-search" type="text" name="p" value="<?php echo($p)?>" placeholder="<?=tr('Import isbn')?>" />
			</div>
			<div class="col-md-2"> </div>
		</div>

		<div class="form-group">
		   <div class="col-md-10">
		    <label for="subject" class="col-md-2 control-label"><?=tr('Subject')?>: </label>
				<input class="col-md-4 text form-search" type="text" name="subj" value="<?php echo($subj)?>" placeholder="<?=tr('Import subject')?>" />
			   <label for="digital_type" class="col-md-2 control-label"><?=tr('Item type')?>: </label>
				<select  id="digital_type" class="col-md-4 text form-search">
						<option value="undefined"><?=tr('All types')?></option>
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
		   <div class="col-md-10 "> <!-- <div class="col-md-7 col-md-offset-2">-->
		   <div class="col-md-10  col-md-offset-2 search-buttons">
				<button type="submit" value="search" class="btn btn-default" ><?=tr('Search')?></button>
				<button name="clear" value="clear" onclick="clearForm(this.form);" class="btn btn-default" ><?=tr('Clear')?></button>
			</div>
			</div>
			<div class="col-md-2">
				<?php if ($m == 's'){ ?>
					<?php if (variable_get('arc_search_display_advance_link')): ?>
						<!-- <a class="m_search_link"  href="/archive/search?m=a&lang=<?=$lang?>"><?=tr('Advance search')?></a>-->
						<a class="m_search_link"  href="/archive/search_s<?php echo $term_search_flag ?  "?&term=$ss&submit=search" : null ;?>"><?=tr('Advance search')?></a>
					<?php endif ?>
				<?php  } else { ?>
					<a class="m_search_link" href="/archive/search?lang=><?=$lang?>"><?=tr('Simple search')?></a>
				<?php  } ?>
		   </div>
		</div>


    </form>
  </div>
</div>



</div>


{{-- ########################################################################################### --}}
{{-- ########################################################################################### --}}



	<div class="row sfilters" role="main" aria-label="{{tr('search results')}}" >
	<div class="col-md-12">
	<h2>{{tr('search results')}}.</h2>
	</div>
	</div>



	<div class="row">
	<div id="tresults" >
	<div class="rescnt row res-infobar">
	<?php
		PSnipets::block_kritiria($total_cnt,$c,$m,$sss,$ss,$y,$p,$o,$l,$a,$d,$r,$y1,$y2,$sl,$lang,$counters,$display_lang_select_flag,$ot);
	?>
	</div>


		<ol class="reslist itemlist">
		@foreach ($results as $r)

		<?php   //echo '<pre>'; print_r($r); echo '</pre>'; ?>
			<?php // Log::info(print_r($r,true)); ?>

			<li class="resitem">

			@if(isset($r['thumb']) && ! empty($r['thumb']))
				<span aria-hidden="true" class="thumb_bg_img" style="background-image:url(/media/{{$r['thumb']}});"></span>
			@endif


			@if($r['obj_type'] != 'auth-work')
				{{tr($r['obj_type'])}}:
			@endif
			{{-- $r['public_title'] iparxi egiimena --}}
			<a href="{{UrlPrefixes::$item_opac}}{{$r['public_title']['id']}}"><strong>
			<?php $jdata = json_decode($r['jdata'],true); ?>
			@if (isset($jdata['opac1']['public_title']['title']))
						{{$jdata['opac1']['public_title']['title']}}
			@else
						{{$r['public_title']['title']}}
			@endif
			 </strong></a><br/>

			@if (isset($r['public_lines']))
			<?php  //echo"<pre>"; print_r($r); echo"</pre>"; ?>
				<?php $plcnt = count($r['public_lines']); ?>
				@if ($plcnt > 0)
						@if ($plcnt == 1)
						<span class="res_l">{{tr('Manifestation of work')}}: </span>
						 {{-- tetrimeno foreach plcnt == 1 --}}
							@foreach ($r['public_lines'] as $m)
							<span class="res_l"><a href="{{UrlPrefixes::$item_opac}}{{$m['id']}}">{{$m['title']}}</a>
							@if(isset($m['items']) && ! empty($m['items']))
							@if(user_access_login())
							<span class="sr-only">{{tr('Available files for instant download')}}: </span>
								@foreach ($m['items']['type'] as $index => $type)
									@if (isset($m['items']['object-type']))
											@if ($m['items']['object-type'][$index] == 'digital-item')
												@if(isset($type) && ! empty($type))
													[<a href="{{UrlPrefixes::$item_opac}}{{$m['items']['id'][$index]}}/download/0">{{$type}}</a>]
												@endif
											@endif
									@endif
								@endforeach
							@endif
							@endif
							</span>
							@endforeach
						@else
						<span class="res_l">{{tr('Manifestations of work')}}: </span>
							<ol type="1">
								@foreach ($r['public_lines'] as $m)
									<li><a href="{{UrlPrefixes::$item_opac}}{{$m['id']}}">{{$m['title']}}</a>
									@if(isset($m['items']) && ! empty($m['items']))
									@if(user_access_login())
									<span class="sr-only">{{tr('Available files for instant download')}}: </span>
										@foreach ($m['items']['type'] as $index => $type)
											@if (isset($m['items']['object-type']))
												@if ($m['items']['object-type'][$index] == 'digital-item')
													@if(isset($type) && ! empty($type))
														[<a href="{{UrlPrefixes::$item_opac}}{{$m['items']['id'][$index]}}/download/0">{{$type}}</a>]
													@endif
												@endif
											@endif
										@endforeach
									@endif
									@endif
								</li>
								@endforeach
							</ol>
						@endif
				@endif
			@endif

			@if (isset($r['individual_works']))
				 @foreach ($r['individual_works'] as $iw)
						@if(isset($iw['id']) && ! empty($iw['id']))
						<span class="res_l">{{$relation_work_wholepart_map['ea:relation:containedInIndividual']}}:</span>
						<span class="res_l"><a href="{{UrlPrefixes::$item_opac}}{{$iw['id']}}">{{$iw['label']}}</a></span>
						@endif
				 @endforeach
			@endif

			@if (isset($r['contained_in_contribution']))
				 @foreach ($r['contained_in_contribution'] as $cw)
						@if(isset($cw['id']) && !empty($cw['id']))
						<span class="res_l">{{$relation_work_wholepart_map['ea:relation:containedInContributions']}}:</span>
						<span class="res_l"><a href="{{UrlPrefixes::$item_opac}}{{$cw['id']}}">{{$cw['label']}}</a></span>
						@endif
				 @endforeach
			@endif

			@if (isset($r['contained_in_document']))
				 @foreach ($r['contained_in_document'] as $dw)
						@if(isset($dw['id']) && !empty($dw['id']))
						<span class="res_l">{{$relation_work_wholepart_map['ea:relation:containedInDocuments']}}:</span>
						<span class="res_l"><a href="{{UrlPrefixes::$item_opac}}{{$dw['id']}}">{{$dw['label']}}</a></span>
						@endif
				 @endforeach
			@endif

			<div class="clearfix"></div>

			</li>
		@endforeach
		</ol>






</div>
</div>


<?php
PSnipets::block_pagingBlock($results,$m,$paging_data);
?>

<?php
	if($term_search_flag){
				echo "<div class=\"pager\">".tr('End of search').", <a href=\"/archive/search\">".tr('return to search page')."</a></div>";
	}
?>
<hr/>







@stop