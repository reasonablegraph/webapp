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

		$("#term").tooltip({
			position: { my: "left top+5", at: "left bottom" },
			tooltipClass : "term-tooltip",
		});

		$("#facet-header").tooltip({
			position: { my: "left+10 top-78", at: "left top" },
			tooltipClass : "facet-header-tooltip",
		});

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

	<style>
	.facet-header-tooltip{
	 border:1px #333 solid;
	}
	</style>

	@include('public.'.$blade_solr_search_form, ['term' => $term])

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
					<?php //PSnipets::solr_paging_text($numPages,$resultsPerPage);?>
					<?php //PSnipets::solr_paging_number($numPages,$resultsPerPage);?>

				</div>
			</div>
			{{-- FACETES --}}
			<div class="col-md-3  side-right" aria-hidden="true">


				<div id="facet-header" class="facet-header" title="{{tr('Limit search results using one or more filters')}}" >
					<span class="glyphicon glyphicon-filter facet-icon" aria-hidden="true"></span> {{tr('Filter specialization')}}
				</div>
				<div id="side-panel-institution" class="list-group facet">
				<?php
						foreach ($facetes as $facete){
							echo $$facete;
						}
					?>
				@include('public.solr.reset-all-facete')
			  </div>


			  <div id="side-panel-institution" class="list-group facet">
					<?php
							foreach ($facetes_top as $facete){
								echo $$facete;
							}
						?>
				</div>



			</div>
		</div>


	@endif

@stop