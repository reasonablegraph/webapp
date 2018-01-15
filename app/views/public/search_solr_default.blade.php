@section('content')

	<?php
		$blade_solr_search_form = Config::get('arc.BLADE_SOLR_SEARCH_FORM');
		$blade_solr_line_item = Config::get('arc.BLADE_SOLR_LINE_ITEM');
		$blade_solr_result_header = Config::get('arc.BLADE_SOLR_RESULT_HEADER');
	?>
	<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
	<script src="//code.jquery.com/ui/1.10.4/jquery-ui.min.js"></script>
	<link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.10.4/themes/redmond/jquery-ui.css">
	<script src="<?php echo Config::get('arc.ARCHIVE_ASSETS_PATH');?>vendor/jquery-colorbox/jquery.colorbox.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo Config::get('arc.ARCHIVE_ASSETS_PATH');?>vendor/jquery-colorbox/example5/colorbox.css">

	<script>
	$(document).ready(function(){
		$(".colorbox-load").colorbox({rel:'nofollow'});
	});

	function moreFacets(n) {
	  $('.'+n).removeClass('hidden');
	  $('#more-'+n).addClass('hidden');
	}

	function lessFacets(n) {
	  $('.'+n).addClass('hidden');
	  $('#more-'+n).removeClass('hidden');
	}
	</script>

	@include('public.'.$blade_solr_search_form, ['term' => $term])

<!-- 	<div class="row facetes_h" aria-hidden="true"> -->
					<?php
// 						foreach ($facetes_top as $facete){
// 							echo $$facete;
// 						}
 					?>
<!-- 		</div> -->

	@if (get_get('submit') !== null || ($total_cnt>0))
		<div class="row" role="main" aria-label="{{tr('search results')}}"  >
			<div class="col-md-9 side-left">
				<div id="tresults">
					@include('public.solr.result-header-sr-only')	 {{--	 #To define --> SR-filtering & results-headers ##### --}}
					@include('public.'.$blade_solr_result_header)
					<ol class="reslist itemlist">
						@foreach ($resultset as $document)
							@include('public.'.$blade_solr_line_item, ['document' => $document])
						@endforeach
					</ol>
					<br>
					<?php  PSnipets::solrPagination($page, $total_cnt, $limit);  ?>
					<?php //PSnipets::solr_paging_text($numPages,$resultsPerPage); ?>
					<?php //echo '<div aria-hidden="true">'; PSnipets::solr_paging_number($numPages,$resultsPerPage); echo '</div>'; ?>

				</div>
			</div>
			{{-- FACETES --}}
			<div class="col-md-3  side-right" aria-hidden="true">
				@if (!empty($facetes_top))
					<div id="side-panel-institution" class="list-group facet">
						<?php
								foreach ($facetes_top as $facete){
									echo $$facete;
								}
							?>
					</div>
				@endif

				<div class="facet-header">
					<span class="glyphicon glyphicon-filter facet-icon" aria-hidden="true"></span> {{tr('Narrow Search')}}
				</div>
				<div id="side-panel-institution" class="list-group facet">
				<?php
						foreach ($facetes as $facete){
							echo $$facete;
						}
					?>
				@include('public.solr.reset-all-facete')
			  </div>
			</div>
		</div>

		@if($hide_search_form )
			<div class="pager sr-only">{{tr('End of search')}}, <a href="{{{UrlPrefixes::$search_solr}}}?m={{$stype}}">{{tr('return to search page')}}</a></div>
		@endif

	@endif

@stop