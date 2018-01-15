
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

		<div class="row"  id="searchfconteiner"  <?php echo $hide_search_form ?  'aria-hidden="true"' : 'aria-hidden="false"'; ?>>
		  <div class="panel panel-default" role="search" aria-label="{{tr('search form')}}" >
		    <div class="panel-heading" ><h1>
		      @if( $stype!='a')
		        {{tr(Config::get('arc.SEARCH_FORM_LEGEND'))}}
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
	                <label for="terms" class="col-md-1 control-label">{{tr('Term field')}}:</label>
	                <input class="col-md-6 form-search" name="term" type="text"  value="{{$term}}" placeholder="{{tr('Import term')}}"  />
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