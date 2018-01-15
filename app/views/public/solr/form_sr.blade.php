
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

	            <div class="form-group">
	                <div class="col-md-10">
	                <label for="terms" class="col-md-2 control-label">{{tr('Term field1')}}:</label>
	                <input class="col-md-10 form-search" name="term" type="text"  value="{{$term}}" placeholder="{{tr('Import term')}}"  />

	              </div>
	                <div class="col-md-2">

	                <?php /*
	                  <?php if ($display_lang_select_flag): ?>
	                    <?php  if ($m != 'a'): ?>
	                      <label class="element-invisible" for="select_lang"><?=tr('Γλωσσα')?>: </label>
	                      <select name="sl" class="title form-control" id="select_lang">
	                        <option value="0" <?php  if ($sl == '0'){echo 'selected="selected"'; } ?> ><?=tr('Επιλογή Γλώσσας')?></option>
	                        <option value="el" <?php if ($sl == 'el'){echo 'selected="selected"'; } ?> ><?=tr('Ελληνικά')?></option>
	                        <option value="en" <?php if ($sl == 'en'){echo 'selected="selected"'; } ?> ><?=tr('English')?></option>
	                      </select>

	                    <?php endif; ?>
	                    <?php endif; ?>
	                  */ ?>
	              </div>
	            </div>
	      @else
	          <!-- Advance specific -->
	            <input type="hidden" name="m" value="a"/>

	            <div class="form-group">
	              <div class="col-md-10">
	                <label for="terms" class="col-md-2 control-label">{{tr('Search all')}}: </label>

	                <input class="col-md-10 text form-search" type="text" name="term" value="{{$term}}" placeholder="{{tr('Import term')}}"/>
	              </div>
	               <div class="col-md-2"> </div>
	            </div>

	            <div class="form-group">
	                <div class="col-md-10">
	                <label for="title" class="col-md-2 control-label">{{tr('Title')}}: </label>
	                <input class="col-md-4 text form-search" type="text" name="l" value="{{$l}}" placeholder="{{tr('Import title')}}"/>
	                <label for="y" class="col-md-2 control-label">{{tr('Year')}}: </label>
	                <input type="text" class="col-md-4 text form-search" name="y" value="{{$y}}" placeholder="{{tr('Import year')}}" />
	              </div>
	               <div class="col-md-2"> </div>
	            </div>

	            <div class="form-group">
	               <div class="col-md-10">
	                <label for="author" class="col-md-2 control-label">{{tr('Author')}}: </label>
	                <input class="col-md-4 text form-search" type="text" name="a" value="{{$a}}" placeholder="{{tr('Import author')}}" />
	                 <label for="isbn" class="col-md-2 control-label">{{tr('ISBN')}}: </label>
	                <input class="col-md-4 text form-search" type="text" name="p" value="{{$p}}" placeholder="{{tr('Import isbn')}}" />
	              </div>
	              <div class="col-md-2"> </div>
	            </div>

	            <div class="form-group">
	               <div class="col-md-10">
	                <label for="subject" class="col-md-2 control-label">{{tr('Subject')}}: </label>
	                <input class="col-md-4 text form-search" type="text" name="subj" value="{{$subj}}" placeholder="{{tr('Import subject')}}" />
	                 <label for="digital_type" class="col-md-2 control-label">{{tr('Item type')}}: </label>
	                <select  id="digital_type" class="col-md-4 text form-search">
	                    <option value="undefined">{{tr('All types')}}</option>
	                    <option value="pdf">PDF</option>
	                    <option value="daisy">{{tr('DAISY text')}}</option>
	                    <option value="epub">EPUB</option>
	                    <option value="docx">DOCX</option>
	                    <option value="wma">WMA</option>
	                    <option value="mp3">MP3</option>
	                </select>
	              </div>
	              <div class="col-md-2"> </div>
	            </div>
	      @endif

	      <div class="form-group">
	        <div class="col-md-10 "> <!-- <div class="col-md-7 col-md-offset-2">-->
	           <div class="col-md-10  col-md-offset-2 search-buttons">
	            <button name="submit" type="submit" value="search" class="btn btn-default" >{{tr('Search')}}</button>
	            <button name="clear" value="clear" class="clear_form btn btn-default"  >{{tr('Clear')}}</button>
	          </div>
	        </div>
	        <div class="col-md-2">
	        @if ($stype != 's')
	          @if (variable_get('arc_search_display_advance_link'))
	        <!--    <a class="m_search_link"  href="{{{UrlPrefixes::$search_solr}}}?m=a&term={{$term}}">{{tr('Advance search')}}</a> -->
	          @endif
	        @else
	        	@if (!variable_get('SOLR_SEARCH_AS_DEFAULT'))
	            <a class="m_search_link" href="/archive/search?&t=<?=$term?>">{{tr('Simple search')}}</a>
	        	@endif
	        @endif
	        </div>
	      </div>

	      </form>
	    </div>
	  </div>
	</div>
@endif