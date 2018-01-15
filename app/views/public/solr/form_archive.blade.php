@if( $display_mode == 'normal' )
	<script type="text/javascript">
	$(function () {
	    $('input[name="term"]').autocomplete({
	        source: '/prepo/solr_suggest',
	        minLength: 2
	    });

	    $(".clear_form").click(function() {
	        $(this).closest('form').find("input[type=text], textarea").val("");
	    });
	});
	</script>

  <style>
  label {
    display: inline-block;
    width: 5em;
  }

  .term-tooltip{
    max-width: 600px;
    border:1px #333 solid;
    padding: 10px;
  }
  </style>

		<div class="row"  id="searchfconteiner"  <?php echo $hide_search_form ?  'aria-hidden="true"' : 'aria-hidden="false"'; ?>>
		  <div class="panel panel-default" role="search" aria-label="{{tr('search form')}}" >
		    <div class="panel-heading" ><h1>
		      @if( $stype!='a')
		        {{tr('Search in Repository')}}
		      @else
		        {{tr('Advance search in Repository')}}
		      @endif
		       {{tr(Config::get('arc.INSTALLATION_LEGEND'))}}
		    </h1></div>

		    <div class="panel-body">
		      <form method="get" class="arch-sform  form-horizontal" >

		      @if( $stype != 'a')
	            <!-- Simple specific -->
	            <input type="hidden" name="m" value="s"/>
	            <div class="form-group simple">
	               <div class="col-md-12">
	                <div class="col-md-8">
		                <span class="col-md-4 term-label">{{tr('Search with keywords')}}:</span>
		                <input class="col-md-8 form-search" name="term" id="term"  type="text"  value="{{$term}}" placeholder="{{tr('Import term')}}" title="{{tr('Type a writer name or book title')}}" />
	                </div>
	                <div class="col-md-4  col-md-offset-0 form-search-buttons">
	                   <button name="submit" type="submit" value="search" class="btn btn-default" >{{tr('Search')}}</button>
	                   <button name="clear" value="clear" class="clear_form btn btn-default"  >{{tr('Clear')}}</button>
	               </div>

	              </div>
	            </div>
	      @endif

	      </form>
	    </div>
	  </div>
	</div>
@endif