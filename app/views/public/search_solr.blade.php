@section('content')


<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.min.js"></script>
<link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.10.4/themes/redmond/jquery-ui.css">

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


function moreFacets(n) {
  $('.'+n).removeClass('hidden');
  $('#more-'+n).addClass('hidden');
}


function lessFacets(n) {
  $('.'+n).addClass('hidden');
  $('#more-'+n).removeClass('hidden');
}

// function clearForms(){
//	    $(':input').not(':button, :submit, :reset, :hidden, :checkbox, :radio').val('');
//	    $(':checkbox, :radio').prop('checked', false);
// }

</script>


<!-- <h3>SOLR SEARCH</h3> -->
@if( empty($list_mode) )
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
	                <label for="terms" class="col-md-2 control-label">{{tr('Term field')}}:</label>
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

@if (get_get('submit') !== null || ($total_cnt>0))
  <div class="row" role="main" aria-label="{{tr('search results')}}"  >
		<div class="col-md-9 side-left">
<!-- ----------------------------------------------------------------- SR-ONLY -------------------------------------------------------------------- -->
    <?php $input_param = Input::all();  ?>


     @if ($total_cnt>0 && !empty($input_param['fr']))
      <a id="filter-content"></a>
      <div class="sr-only" >
       @if (empty($input_param['record_type']) || empty($input_param['digital_item_types']))
       	<h2>{{tr('Narrow Search sr-only')}}</h2>
       @endif
			<ol>
       <!-- Object Type Filter -->
  		  @if (empty($input_param['record_type']))
				<li>
	       	{{tr('Object Type sr-only')}}
	          @foreach ($record_type_facet as $value => $count)
	            @if($count)
	            <?php
	             unset($input_param['start']); //link unset start
	             ?>
	             <a href="?{{ http_build_query(array_merge($input_param, array('record_type' => $value,'fr' => 0))) }}">{{trChoise($value.'s',$count)}} {{$count}}</a>
	            @endif
	          @endforeach
				</li>
	      @endif
				<!-- -->

				<!-- Digital Items Filter -->
  		  @if (empty($input_param['digital_item_types']) )
					<?php $count_active_digital_item = 0; ?>
					@foreach ($digital_item_types_facet as $value => $count)
						@if($count)
							<?php $count_active_digital_item++; ?>
						@endif
					@endforeach
  		   @if ($count_active_digital_item > 0)
					<li>
							{{tr('Digital item sr-only')}}
		          @foreach ($digital_item_types_facet as $value => $count)
		            @if($count)
		            <?php
		             unset($input_param['start']); //link unset start
		             ?>
		             <a href="?{{ http_build_query(array_merge($input_param, array('digital_item_types' => $value,'fr' => 0))) }}">{{tr($value)}} {{$count}}</a>
		            @endif
		          @endforeach
					</li>
  		    @else
						{{tr('Not found Digital item sr-only')}}
					@endif
	      @endif
			<!-- -->

		</ol>

		 <a href="?{{ http_build_query(array_merge($input_param, array('fr' => 0))) }}">{{tr('Hide filter sr-only')}}</a>

    </div>
   @endif

      <div class="result-header sr-only">

      	@if(empty($list_mode))
					{{trChoise('Found',$total_cnt)}} <strong>{{$total_cnt}}</strong> {{trChoise('result',$total_cnt)}}
					@if ($numManifsFound>0 && $numManifsFound > $total_cnt && $numDigitalItemsFound > 0)
						<span class="results_exp">({{trChoise('contain',$total_cnt)}} {{$numDigitalItemsFound}} {{trChoise('digital item',$numDigitalItemsFound)}} {{tr('for downloading')}} )</span>
					@endif

					@if ($numManifsFound>0 )
						({{trChoise('contain',$total_cnt)}} {{$numManifsFound}} {{trChoise('manifestation',$numManifsFound)}}
						@if ($numDigitalItemsFound > 0)
							{{tr('and')}} {{$numDigitalItemsFound}} {{trChoise('digital item',$numDigitalItemsFound)}}
						@endif
						)
					@endif
					,{{tr('total pages')}} {{$numPages}}.
				@else
					@if ($list_mode == "ppl")
						{{tr('Manifestations with publication place')}} "{{$f_label}}"
					@elseif( $list_mode == 'pub')
						{{tr('Manifestations with publisher')}} "{{$f_label}}"
					@elseif( $list_mode == 'subj')
						{{tr('Manifestations with subject')}} "{{$f_label}}"
					@endif
					({{$total_cnt}} {{trChoise('result',$total_cnt)}})
				@endif

          <div class="result-filter">
          @if ( Input::has('record_type') ||Input::has('digital_item_types') )
						{{tr('Criteria of search sr-only')}}:
					@endif

					@if ( Input::has('digital_item_types') || Input::has('record_type') )
						<ol>
	           @if (Input::has('record_type'))
	           <li>
		          	{{tr($input_param['record_type'])}}
					      <?php
								$input_copy = Input::all();
					      unset($input_copy['record_type']);
					      unset($input_copy['fr']);
					      unset($input_copy['start']); //reset facede unset start
					      ?>
				      	<a href="?{{ http_build_query($input_copy) }}">{{tr('Reset facetes sr-only')}}</a>
			      	</li>
	           @endif

	           @if (Input::has('digital_item_types'))
	           <li>
		          	{{tr($input_param['digital_item_types'])}}
			          <?php
								$input_copy = Input::all();
					      unset($input_copy['digital_item_types']);
					      unset($input_copy['fr']);
					      unset($input_copy['start']); //reset facede unset start
					      ?>
				      	<a href="?{{ http_build_query($input_copy) }}">{{tr('Reset facetes sr-only')}}</a>
			      	</li>
	           @endif
						</ol>
					@endif

						@if ( Input::has('digital_item_types') && Input::has('record_type') )
							<?php
								$input_copy = Input::all();
								unset($input_copy['record_type']);
								unset($input_copy['digital_item_types']);
								unset($input_copy['fr']);
								unset($input_copy['start']); //reset facede unset start
							?>
							<a href="?{{ http_build_query($input_copy) }}">{{tr('Reset all facetes sr-only')}}</a>
						@endif

         </div>

					<?php $input_param = Input::all();  ?>
					<?php $count_active_digital_item = 0; ?>
					@foreach ($digital_item_types_facet as $value => $count)
						@if($count)
							<?php $count_active_digital_item++; ?>
						@endif
					@endforeach

					@if ($total_cnt>0 && empty($input_param['fr']) && $count_active_digital_item > 0 && (empty($input_param['record_type']) || empty($input_param['digital_item_types']))  )
					<div>
						@if ( empty($input_param['record_type']) && empty($input_param['digital_item_types']) )
							<a href="?{{ http_build_query(array_merge($input_param, array('fr' => '1'))) }}#filter-content">{{tr('Display filter sr-only')}}</a>
						@elseif( empty($input_param['record_type']) || empty($input_param['digital_item_types']) )
							<a href="?{{ http_build_query(array_merge($input_param, array('fr' => '1'))) }}#filter-content">{{tr('Display filter more sr-only')}}</a>
						@endif
	 				</div>
					@endif
			</div>
<!-- ------------------------------------------------------------------------------------- --------------------------------------------------------------->


    <div id="tresults">
     <div class="result-header" aria-hidden="true" >

			@if(empty($list_mode))
					<!-- {{tr('search results')}} -->
					{{trChoise('Found',$total_cnt)}} <strong>{{$total_cnt}}</strong> {{trChoise('result',$total_cnt)}}
					@if ($numManifsFound>0 ) <!--  && $numManifsFound > $total_cnt -->
						<span class="results_exp">({{trChoise('contain',$total_cnt)}} {{$numManifsFound}} {{trChoise('manifestation',$numManifsFound)}}
						@if ($numDigitalItemsFound > 0)
							{{tr('and')}} {{$numDigitalItemsFound}} {{trChoise('digital item',$numDigitalItemsFound)}}
						@endif
						) &#9830; {{tr('total pages')}} {{$numPages}} </span>
					@endif
			@else
				@if ($list_mode == 'ppl')
					{{tr('Manifestations with publication place')}} <strong>"{{$f_label}}"</strong>
				@elseif( $list_mode == 'pub')
					{{tr('Manifestations with publisher')}} <strong>"{{$f_label}}"</strong>
				@elseif( $list_mode == 'subj')
					{{tr('Manifestations with subject')}} <strong>"{{$f_label}}"</strong>
				@endif
				<span class="results_exp">({{$total_cnt}} {{trChoise('result',$total_cnt)}})</span>
			@endif

          <div class="result-filter">
          @if (
								Input::has('record_type') ||
								Input::has('authors') ||
								Input::has('authors_with_ids') ||
								Input::has('subjects') ||
								Input::has('publication_places') ||
								Input::has('publication_places_with_ids') ||
								Input::has('publication_types') ||
								Input::has('publishers') ||
								Input::has('publishers_with_ids') ||
								Input::has('digital_item_types') ||
								Input::has('languages')
					)
						{{tr('Criteria of search')}}:
					@endif
           @if (Input::has('record_type'))
          	&#9830; <strong>{{tr($input_param['record_type'])}}</strong>
           @endif
           @if (Input::has('authors'))
          	&#9830; <strong>{{$input_param['authors']}}</strong>
           @endif
           @if (Input::has('authors_with_ids'))
          	&#9830; <strong>{{PUtil::explodeIdFacet($input_param['authors_with_ids'])}}</strong>
           @endif
           @if (Input::has('subjects'))
          	&#9830; <strong>{{$input_param['subjects']}}</strong>
           @endif
           @if (Input::has('publication_places'))
          	&#9830; <strong>{{$input_param['publication_places']}}</strong>
           @endif
           @if (Input::has('publication_places_with_ids'))
          	&#9830; <strong>{{PUtil::explodeIdFacet($input_param['publication_places_with_ids'])}}</strong>
           @endif
           @if (Input::has('publication_types'))
          	&#9830; <strong>{{tr($input_param['publication_types'])}}</strong>
           @endif
           @if (Input::has('publishers'))
          	&#9830; <strong>{{$input_param['publishers']}}</strong>
           @endif
           @if (Input::has('publishers_with_ids'))
          	&#9830; <strong>{{PUtil::explodeIdFacet($input_param['publishers_with_ids'])}}</strong>
           @endif
           @if (Input::has('digital_item_types'))
          	&#9830; <strong>{{tr($input_param['digital_item_types'])}}</strong>
           @endif
           @if (Input::has('languages'))
            &#9830; <strong>{{tr($input_param['languages'])}}</strong>
           @endif
        </div>

				<div class="result-filter" aria-hidden="true">
						<?php	$len = count($subjectsResultset);?>
						@if ($len>0)
							{{tr('Suggested topics')}}:
							@foreach ($subjectsResultset as $index => $document)
								@if ($index < $subjectsResultsetNum)
									<?php	 $r = json_decode($document->opac1, true); ?>
									<a href="{{UrlPrefixes::$item_opac}}{{$document->id}}">{{$r['title']}}</a>@if($index != $len - 1 && $index != $subjectsResultsetNum-1 ),
									@elseif ($index == $subjectsResultsetNum-1).<!-- cut flag -->
									@endif
								@endif
							@endforeach
						@endif
				</div>
		</div>

<!-- 				<div class="rescnt row res-infobar"> -->
<!-- 					{{trChoise('Found',$total_cnt)}} <strong>{{$total_cnt}}</strong> {{trChoise('result',$total_cnt)}} -->
<!-- 			@if ($numManifsFound>0 ) <!--  && $numManifsFound > $total_cnt -->
<!-- 						({{trChoise('contain',$total_cnt)}} {{$numManifsFound}} {{trChoise('manifestation',$numManifsFound)}} -->
<!-- 						@if ($numDigitalItemsFound > 0) -->
<!-- 							{{tr('and')}} {{$numDigitalItemsFound}} {{trChoise('digital item',$numDigitalItemsFound)}} -->
<!-- 						@endif -->
<!-- 						) -->
<!-- 					@endif -->
<!-- 				</div> -->

				<div class="sr-only">{{tr('List of search results')}}:</div>
        <ol class="reslist itemlist">
          @foreach ($resultset as $document)
          <?php	 $r = json_decode($document->opac1, true); ?>
          <?php	 //echo '<pre>'; print_r($document); echo '</pre>'; ?>
            <li class="resitem">

            @if (isset($r['thumbs']['thumb']) && ! empty($r['thumbs']['thumb']))
              <span aria-hidden="true" class="thumb_bg_img" style="background-image:url({{{UrlPrefixes::$media}}}/{{$r['thumbs']['thumb']}});"></span>
            @endif


            @if($r['obj_type'] != 'auth-work' && $r['obj_type'] != 'auth-manifestation' && $r['obj_type'] != 'lemma' )
              {{tr($r['obj_type'])}}:
            @endif

            @if (isset($r['public_title']['title']))
              @if($r['obj_type'] == 'auth-work')  <?php $lnk_class='class="bold_slr"'; ?> @else <?php $lnk_class=null; ?> @endif
              <a href="{{UrlPrefixes::$item_opac}}{{$r['public_title']['id']}}" {{$lnk_class}} >
                  {{$r['public_title']['title']}}
            @else
              <a href="{{UrlPrefixes::$item_opac}}{{$r['id']}}">
              <!--  {{$r['title']}} -->
              {{$document->label}}
            @endif
              </a>

            @if(!empty($r['authors']))
              <br> <span class="label_solr">{{tr('Author')}}:
              @foreach ($r['authors'] as $index=>$author)
                  <a href="{{UrlPrefixes::$item_opac}}{{$author['id']}}">{{$author['name']}}</a>@if($index < count($r['authors'])-1),@endif
              @endforeach
              </span>
            @endif

            @if(isset($r['items']) && ! empty($r['items']))
              @if(user_access_login())
                 <br>
<!--                  <span class="label label-info" aria-hidden="true">{{tr('Available files')}}:</span> -->
                 <span class="label_solr" aria-hidden="true">{{tr('Available files')}}:</span>
                 <span class="sr-only">{{tr('Available files for instant download')}}: </span>
                 @foreach ($r['items'] as $item )
                   @if(isset($item['id']))
                      @if( isset($item['label']) && !empty($item['label']) )
                        <span class="sdonwload label label-success"><a href="{{UrlPrefixes::$item_opac}}{{$item['id']}}/download/0">{{$item['label']}}</a></span>
                      @endif
                    @endif
                 @endforeach
                @endif
              @endif

            @if (isset($r['public_lines']))
              @if (count($r['public_lines']) > 0)
                  @if (count($r['public_lines']) == 1)
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

<!--         <div style ="font-size:11px;"> -->
<!--             @if($highlighting->getResult($document->id)) -->
<!--               @foreach ($highlighting->getResult($document->id) as $field => $highlight) -->
                <?php  // echo tr($field) . ': <span style ="font-size:11px;font-style: italic;">"' . implode(' (...) ', $highlight) . '..."</span><br/>'; ?>
<!--               @endforeach -->
<!--             @endif -->
<!--           </div> -->

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

            <div class="clearfix"></div>

            </li>
          @endforeach
          </ol>
      </div>
    </div>

    <div class="col-md-3  side-right" aria-hidden="true">
       <div class="facet-header">
        <span class="glyphicon glyphicon-filter facet-icon" aria-hidden="true"></span>{{tr('Narrow Search')}}
      </div>
      <div id="side-panel-institution" class="list-group facet">


<?php
// 	$i=0;
// 	$input_all = Input::all();
 ?>

	@if ($list_mode != 'ppl' && $list_mode != 'pub' && $list_mode != 'subj')
      <!-- Record Type Filter -->
      <div class="facet_box">

        <?php
	         $input_all = Input::all();
	       ?>
       	@if(isset($input_all['record_type']))
	          <?php  unset($input_all['record_type']); ?>
			       <div class="reset_facet_in"><a href="?{{ http_build_query($input_all) }}">[x]</a></div>
			  @endif
				<div class="list-group-item title resf" href="#side-record_type_facet" data-toggle="collapse" aria-expanded="true">{{tr('Object Type')}}</div>

	      <div id="side-record_type_facet" class="collapse in" aria-expanded="true">
	          <?php $i=0; ?>

	           <?php	//echo '<pre>'; print_r($record_type_facet); echo '</pre>'; ?>

	          @foreach ($record_type_facet as $value => $count)
	            @if($count)
	            <?php
	             unset($input_all['start']); //link unset start
	             $i++;
	             ?>
	             @if($i <= $moreFacetsNum || (count($record_type_facet) == $i && $i-1 == $moreFacetsNum) )
	               <div class="list-group-item-facet">
               @else
                 <div class="list-group-item-facet narrowRecordType hidden">
	             @endif
	             @if ( $value != 'manifestation')
	               <div class="left-flist"><a href="?{{ http_build_query(array_merge($input_all, array('record_type' => $value))) }}"> {{trChoise($value.'s',$count)}}</a></div>
	             @else
	               <div class="left-flist">{{trChoise($value.'s',$count)}}</div>
	             @endif
	               <div class="right-flist"><span class="badge" >{{$count}} </span></div>
	            </div>
             @endif
            @endforeach
            <div style="clear:both"> </div>

          @if($i > $moreFacetsNum && !(count($record_type_facet) == $i && $i-1 == $moreFacetsNum) )
            <a id="more-narrowRecordType" class="list-group-item" href="javascript:moreFacets('narrowRecordType')">{{tr('more')}} ...</a>
            <a class="list-group-item narrowRecordType hidden" href="javascript:lessFacets('narrowRecordType')">{{tr('less')}} ...</a>
          @endif

<!-- 	          @if(isset($input_all['record_type'])) -->
	          	<?php  //unset($input_all['record_type']); ?>
<!-- 	      	    <div class="reset-facet reset-facet-li"> -->
<!--      	  	<a href="?{{ http_build_query($input_all) }}">{{tr('Reset facet')}}</a> --> <!-- Reset record type -->
<!-- 	      	    </div> -->
<!--  						@endif -->
	        </div>
        </div>
      <!-- -->
	@endif


      <!-- Authors with ids Filter -->
      @if($has_authors_with_ids)
       <div class="facet_box">
        <div class="list-group-item title" href="#side-authors_with_ids_facet" data-toggle="collapse" aria-expanded="true">
        {{tr('Authors')}}
        </div>
        <div id="side-authors_with_ids_facet" class="collapse in" aria-expanded="true">
        	<?php $i=0; ?>
          @foreach ($authors_with_ids_facet as $value => $count)
            @if($count)
              <?php
               $input_all = Input::all();
               unset($input_all['start']); //link unset start
              $i++;
              ?>
	            @if($i <= $moreFacetsNum || (count($authors_with_ids_facet) == $i && $i-1 == $moreFacetsNum) )
	             <div class="list-group-item-facet">
             @else
              <div class="list-group-item-facet narrowAuthorsWithIds hidden">
	            @endif
	               <div class="left-flist"><a href="?{{ http_build_query(array_merge($input_all, array('authors_with_ids' => $value))) }}">{{PUtil::explodeIdFacet($value)}}</a></div>
	               <div class="right-flist"><span class="badge" >{{$count}} </span></div>
	            </div>
            @endif
          @endforeach
          <div style="clear:both"> </div>

          @if($i > $moreFacetsNum && !(count($authors_with_ids_facet) == $i && $i-1 == $moreFacetsNum) )
            <a id="more-narrowAuthorsWithIds" class="list-group-item" href="javascript:moreFacets('narrowAuthorsWithIds')">{{tr('more')}} ...</a>
            <a class="list-group-item narrowAuthorsWithIds hidden" href="javascript:lessFacets('narrowAuthorsWithIds')">{{tr('less')}} ...</a>
          @endif

          @if(isset($input_all['authors_with_ids']))
	          <?php  unset($input_all['authors_with_ids']); ?>
	          <div class="reset-facet reset-facet-li">
	        	 <a href="?{{ http_build_query($input_all) }}">{{tr('Reset facet')}}</a> <!-- Reset authors-->
	          </div>
 				@endif
        </div>
       </div>
      @endif
      <!-- -->


      <!-- Subjects Filter -->
       @if($has_subjects)
	       <div class="facet_box">
	        <div class="list-group-item title" href="#side-subjects_facet" data-toggle="collapse" aria-expanded="true">
	        {{tr('Subjects')}}
	        </div>
	        <div id="side-subjects_facet" class="collapse in" aria-expanded="true">
	          <?php $i=0; ?>
	          @foreach ($subjects_facet as $value => $count)
	            @if($count)
	            <?php
	             $input_all = Input::all();
	             unset($input_all['start']); //link unset start
	             $i++;
	             ?>
	             @if($i <= $moreFacetsNum  || (count($subjects_facet) == $i && $i-1 == $moreFacetsNum) )
	              <div class="list-group-item-facet">
	             @else
	              <div class="list-group-item-facet narrowSubjects hidden">
		           @endif
		               <div class="left-flist"><a href="?{{ http_build_query(array_merge($input_all, array('subjects' => $value))) }}">{{$value}}</a></div>
		               <div class="right-flist"><span class="badge" >{{$count}} </span></div>
		            </div>
	            @endif
	          @endforeach
	          <div style="clear:both"> </div>

	          @if($i > $moreFacetsNum && !(count($subjects_facet) == $i && $i-1 == $moreFacetsNum) )
	            <a id="more-narrowSubjects" class="list-group-item" href="javascript:moreFacets('narrowSubjects')">{{tr('more')}} ...</a>
	            <a class="list-group-item narrowSubjects hidden" href="javascript:lessFacets('narrowSubjects')">{{tr('less')}} ...</a>
	          @endif

	         @if(isset($input_all['subjects']))
	          <?php  unset($input_all['subjects']); ?>
	          <div class="reset-facet reset-facet-li">
	          	<a href="?{{ http_build_query($input_all) }}">{{tr('Reset facet')}}</a> <!-- Reset subjects-->
	          </div>
	 				@endif
	        </div>
	       </div>
       @endif
      <!-- -->

			<div class="line-separator"></div>


			<!-- Subjects Manif Filter -->
				@if($has_subjects_m)
	       <div class="facet_box">
	        <div class="list-group-item title" href="#side-subjects_m_facet" data-toggle="collapse" aria-expanded="true">
	        {{tr('Manifestation subjects')}}
	        </div>
	        <div id="side-subjects_m_facet" class="collapse in" aria-expanded="true">
	          <?php $i=0; ?>
	          @foreach ($subjects_manif_facet as $value => $count)
	            @if($count)
	            <?php
	             $input_all = Input::all();
	             unset($input_all['start']); //link unset start
	             $i++;
	             ?>
	             @if($i <= $moreFacetsNum  || (count($subjects_manif_facet) == $i && $i-1 == $moreFacetsNum) )
	              <div class="list-group-item-facet">
	             @else
	              <div class="list-group-item-facet narrowSubjects_m hidden">
		           @endif
		               <div class="left-flist"><a href="?{{ http_build_query(array_merge($input_all, array('subjects_manif' => $value))) }}">{{$value}}</a></div>
		               <div class="right-flist"><span class="badge" >{{$count}} </span></div>
		            </div>
	            @endif
	          @endforeach
	          <div style="clear:both"> </div>

	          @if($i > $moreFacetsNum && !(count($subjects_manif_facet) == $i && $i-1 == $moreFacetsNum) )
	            <a id="more-narrowSubjects_m" class="list-group-item" href="javascript:moreFacets('narrowSubjects_m')">{{tr('more')}} ...</a>
	            <a class="list-group-item narrowSubjects_m hidden" href="javascript:lessFacets('narrowSubjects_m')">{{tr('less')}} ...</a>
	          @endif

	         @if(isset($input_all['subjects_manif']))
	          <?php  unset($input_all['subjects_manif']); ?>
	          <div class="reset-facet reset-facet-li">
	          	<a href="?{{ http_build_query($input_all) }}">{{tr('Reset facet')}}</a> <!-- Reset subjects-->
	          </div>
	 				@endif
	        </div>
	       </div>
       @endif
      <!-- -->


		<!-- Language Filter -->
		@if($has_languages)
     <div class="facet_box">
        <div class="list-group-item title" href="#side-languages_facet" data-toggle="collapse" aria-expanded="true">
        {{tr('Language')}}
        </div>
        <div id="side-languages_facet" class="collapse in" aria-expanded="true">
          <?php $i=0; ?>
          @foreach ($languages_facet as $value => $count)
            @if($count)
            <?php
             $input_all = Input::all();
             unset($input_all['start']); //link unset start
             $i++;
             ?>
             @if($i <= $moreFacetsNum || (count($languages_facet) == $i && $i-1 == $moreFacetsNum) )
	             <div class="list-group-item-facet">
             @else
              <div class="list-group-item-facet narrowLanguages hidden">
	           @endif
	               <div class="left-flist"><a href="?{{ http_build_query(array_merge($input_all, array('languages' => $value))) }}">{{tr($value)}}</a></div>
	               <div class="right-flist"><span class="badge" >{{$count}} </span></div>
	           </div>
            @endif
          @endforeach
          <div style="clear:both"> </div>

          @if($i > $moreFacetsNum && !(count($languages_facet) == $i && $i-1 == $moreFacetsNum) )
            <a id="more-narrowLanguages" class="list-group-item" href="javascript:moreFacets('narrowLanguages')">{{tr('more')}} ...</a>
            <a class="list-group-item narrowLanguages hidden" href="javascript:lessFacets('narrowLanguages')">{{tr('less')}} ...</a>
          @endif

         @if(isset($input_all['languages']))
          <?php  unset($input_all['languages']); ?>
          <div class="reset-facet reset-facet-li">
          	<a href="?{{ http_build_query($input_all) }}">{{tr('Reset facet')}}</a> <!-- Reset language -->
          </div>
 				@endif
        </div>
       </div>
      @endif
      <!-- -->


       <!-- Publication places with ids Filter -->
      @if($has_publication_places_with_ids)
       <div class="facet_box">
        <div class="list-group-item title" href="#side-publication_places_with_ids_facet" data-toggle="collapse" aria-expanded="true">
        {{tr('Publication places')}}
        </div>
        <div id="side-publication_places_with_ids_facet" class="collapse in" aria-expanded="true">
          <?php $i=0; ?>
          @foreach ($publication_places_with_ids_facet as $value => $count)
           @if($count)
            <?php
             $input_all = Input::all();
             unset($input_all['start']); //link unset start
             $i++;
             ?>
             @if($i <= $moreFacetsNum || (count($publication_places_with_ids_facet) == $i && $i-1 == $moreFacetsNum))
	             <div class="list-group-item-facet">
             @else
               <div class="list-group-item-facet narrowPublicationPlacesWithIds hidden">
	           @endif
	               <div class="left-flist"><a href="?{{ http_build_query(array_merge($input_all, array('publication_places_with_ids' => $value ))) }}">{{PUtil::explodeIdFacet($value)}}</a></div>
	               <div class="right-flist"><span class="badge" >{{$count}} </span></div>
	            </div>
           @endif
          @endforeach
          <div style="clear:both"> </div>

          @if($i > $moreFacetsNum && !(count($publication_places_with_ids_facet) == $i && $i-1 == $moreFacetsNum) )
            <a id="more-narrowPublicationPlacesWithIds" class="list-group-item" href="javascript:moreFacets('narrowPublicationPlacesWithIds')">{{tr('more')}} ...</a>
            <a class="list-group-item narrowPublicationPlacesWithIds hidden" href="javascript:lessFacets('narrowPublicationPlacesWithIds')">{{tr('less')}} ...</a>
          @endif

         @if(isset($input_all['publication_places_with_ids']))
          <?php  unset($input_all['publication_places_with_ids']); ?>
          <?php  unset($input_all['nomanif']); ?>
          <div class="reset-facet reset-facet-li">
          	<a href="?{{ http_build_query($input_all) }}">{{tr('Reset facet')}}</a> <!-- Reset publication places with ids-->
        	</div>
 				@endif
        </div>
       </div>
      @endif
      <!-- -->


      <!-- Publication Type Filter -->
      @if($has_publication_types)
       <div class="facet_box">
        <div class="list-group-item title" href="#side-publication_types_facet" data-toggle="collapse" aria-expanded="true">
        {{tr('Publication types')}}
        </div>
        <div id="side-publication_types_facet" class="collapse in" aria-expanded="true">
          <?php $i=0; ?>
          @foreach ($publication_types_facet as $value => $count)
            @if($count)
            <?php
             $input_all = Input::all();
             unset($input_all['start']); //link unset start
             $i++;
             ?>
             @if($i <= $moreFacetsNum || (count($publication_types_facet) == $i && $i-1 == $moreFacetsNum) )
	             <div class="list-group-item-facet">
             @else
               <div class="list-group-item-facet narrowPublicationTypes hidden">
	           @endif
	              <div class="left-flist"><a href="?{{ http_build_query(array_merge($input_all, array('publication_types' => $value))) }}">{{tr($value)}}</a></div>
	              <div class="right-flist"><span class="badge" >{{$count}} </span></div>
	            </div>
            @endif
          @endforeach
          <div style="clear:both"> </div>

          @if($i > $moreFacetsNum && !(count($publication_types_facet) == $i && $i-1 == $moreFacetsNum) )
            <a id="more-narrowPublicationTypes" class="list-group-item" href="javascript:moreFacets('narrowPublicationTypes')">{{tr('more')}} ...</a>
            <a class="list-group-item narrowPublicationTypes hidden" href="javascript:lessFacets('narrowPublicationTypes')">{{tr('less')}} ...</a>
          @endif

         @if(isset($input_all['publication_types']))
          <?php  unset($input_all['publication_types']); ?>
          <div class="reset-facet reset-facet-li">
          	<a href="?{{ http_build_query($input_all) }}">{{tr('Reset facet')}}</a> <!-- Reset publication types-->
        	</div>
 				@endif
        </div>
       </div>
      @endif
      <!-- -->


       <!-- Publishers With Ids Filter -->
      @if($has_publishers_with_ids)
       <div class="facet_box">
        <div class="list-group-item title" href="#side-publishers_with_ids_facet" data-toggle="collapse" aria-expanded="true">
        {{tr('Publishers')}}
        </div>
        <div id="side-publishers_with_ids_facet" class="collapse in" aria-expanded="true">
          <?php $i=0; ?>
          @foreach ($publishers_with_ids_facet as $value => $count)
            @if($count)
            <?php
             $input_all = Input::all();
             unset($input_all['start']); //link unset start
             $i++;
             ?>
             @if($i <= $moreFacetsNum || (count($publishers_with_ids_facet) == $i && $i-1 == $moreFacetsNum) )
	             <div class="list-group-item-facet">
             @else
               <div class="list-group-item-facet narrowPublishersWithIds hidden">
	           @endif
	               <div class="left-flist"><a href="?{{ http_build_query(array_merge($input_all, array('publishers_with_ids' => $value))) }}">{{PUtil::explodeIdFacet($value)}}</a></div>
	               <div class="right-flist"><span class="badge" >{{$count}} </span></div>
	            </div>
            @endif
          @endforeach
          <div style="clear:both"> </div>

          @if($i > $moreFacetsNum && !(count($publishers_with_ids_facet) == $i && $i-1 == $moreFacetsNum) )
            <a id="more-narrowPublishersWithIds" class="list-group-item" href="javascript:moreFacets('narrowPublishersWithIds')">{{tr('more')}} ...</a>
            <a class="list-group-item narrowPublishersWithIds hidden" href="javascript:lessFacets('narrowPublishersWithIds')">{{tr('less')}} ...</a>
          @endif

         @if(isset($input_all['publishers_with_ids']))
          <?php  unset($input_all['publishers_with_ids']); ?>
          	<div class="reset-facet reset-facet-li">
          <a href="?{{ http_build_query($input_all) }}">{{tr('Reset facet')}}</a> <!-- Reset publishers with ids -->
       	</div>
 				@endif
        </div>
       </div>
      @endif
      <!-- -->

    <!--  <div class="line-separator"></div>-->


     <!-- Digital Items Filter -->
<!--       <div class="facet_box"> -->
<!--         <div class="list-group-item title" href="#side-digital_item_types_facet" data-toggle="collapse" aria-expanded="true"> -->
<!--         {{tr('Digital Items')}} -->
<!--         </div> -->
<!--         <div id="side-digital_item_types_facet" class="collapse in" aria-expanded="true"> -->
          <?php
//           $i=0;
//           ?>
<!--           @foreach ($digital_item_types_facet as $value => $count) -->
<!--             @if($count) -->
            <?php
//              $input_all = Input::all();
//              unset($input_all['start']); //link unset start
//              $i++;
//              ?>
<!--              @if($i <= $moreFacetsNum || (count($digital_item_types_facet) == $i && $i-1 == $moreFacetsNum) ) -->
<!-- 	             <div class="list-group-item-facet"> -->
<!--              @else -->
<!--                <div class="list-group-item-facet narrowDigitalItemTypes hidden"> -->
<!-- 	           @endif -->
<!-- 	               <div class="left-flist"><a href="?{{ http_build_query(array_merge($input_all, array('digital_item_types' => $value))) }}">{{tr($value)}}</a></div> -->
<!-- 	               <div class="right-flist"><span class="badge" >{{$count}} </span></div> -->
<!-- 	            </div> -->
<!--             @endif -->
<!--           @endforeach -->
<!--       <div style="clear:both"> </div> -->

<!--           @if($i > $moreFacetsNum && !(count($digital_item_types_facet) == $i && $i-1 == $moreFacetsNum) ) -->
<!--             <a id="more-narrowDigitalItemTypes" class="list-group-item" href="javascript:moreFacets('narrowDigitalItemTypes')">{{tr('more')}} ...</a> -->
<!--             <a class="list-group-item narrowDigitalItemTypes hidden" href="javascript:lessFacets('narrowDigitalItemTypes')">{{tr('less')}} ...</a> -->
<!--           @endif -->

<!--          @if(isset($input_all['digital_item_types']) ) -->
          <?php
//           unset($input_all['digital_item_types']);
//           ?>
<!--           <div class="reset-facet reset-facet-li"> -->
          <!--	<a href="?{{ http_build_query($input_all) }}">{{tr('Reset facet')}}</a> --> <!-- Reset digital Items-->
<!--         	</div> -->
<!--  				@endif -->
<!--         </div> -->
<!--        </div> -->
      <!-- -->

      </div>

      <div class="reset-facet">
      <?php
			$input_copy = Input::all();
      unset($input_copy['record_type']);
      unset($input_copy['authors']);
      unset($input_copy['authors_with_ids']);
      unset($input_copy['subjects']);
      unset($input_copy['subjects_manif']);
      unset($input_copy['publication_places']);
//       unset($input_copy['publication_places_ids']);
      unset($input_copy['publication_places_with_ids']);
      unset($input_copy['publication_types']);
      unset($input_copy['publishers']);
      unset($input_copy['publishers_with_ids']);
      unset($input_copy['digital_item_types']);
      unset($input_copy['nomanif']);
      unset($input_copy['start']); //reset facede unset start
      ?>
      <a href="?{{ http_build_query($input_copy) }}">{{tr('Reset all facetes')}}</a>
      </div>
    </div>

  </div>

	<?php PSnipets::solr_paging_text($numPages,$resultsPerPage);  ?>
	<div  aria-hidden="true" ><?php PSnipets::solr_paging_number($numPages,$resultsPerPage);  ?></div>


  @if($hide_search_form )
      <div class="pager">{{tr('End of search')}}, <a href="{{{UrlPrefixes::$search_solr}}}?m={{$stype}}">{{tr('return to search page')}}</a></div>
  @endif


@endif

@stop
