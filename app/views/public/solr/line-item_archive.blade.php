<?php
  $relation_work_wholepart_map = Setting::get('relation_work_wholepart_map');
  $id = $document->id;
  $label = $document->label;
  $r = json_decode($document->opac1, true);
  $ft = $document->form_type;
  //echo '<pre>'; print_r($document); echo '</pre>';
?>
        <li class="resitem">
            @if (isset($r['thumbs']['small']) && ! empty($r['thumbs']['small']))
             {{--<!-- <span aria-hidden="true" class="thumb_bg_img" style="background-image:url({{{UrlPrefixes::$media}}}/{{$r['thumbs']['small'][0]}});"></span>  -->--}}
             <span aria-hidden="true" class="sch_thumb_img" >
               <a class="group colorbox-load cboxElement" rel="gal" href="{{{UrlPrefixes::$media}}}/{{$r['thumbs']['big'][0]}}" title="{{$r['thumbs']['description']}}" >
                 <img src="{{{UrlPrefixes::$media}}}/{{$r['thumbs']['icon_small'][0]}}" alt="{{$r['thumbs']['description']}}">
              </a>
             </span>
            @endif

            @if($ft != 'work' && $ft != 'lemma' )
              <b>{{tr($ft)}}:</b>
            @endif

            @if (isset($r['public_title']['title']))
              @if($r['obj_type'] == 'auth-work') <?php $lnk_class='class="bold_slr"'; ?>
                 @if (Config::get('arc.SOLR_DEV',0))
                   <a href="{{UrlPrefixes::$item_opac}}{{$r['public_title']['id']}}" {{$lnk_class}}> {{$r['public_title']['title']}}
                 @else
                   <span {{$lnk_class}} >{{$r['public_title']['title']}}</span>
                   @if(user_access_mentainer() && isset($r['completion_level']) && $r['completion_level'] == 1 )
                      &nbsp;<a href="{{{UrlPrefixes::$item_edit}}}{{{$r['id']}}}"><span class="old_rec glyphicon glyphicon-wrench"></span></a>
                      <!--  <span style="color:red" class="glyphicon glyphicon-pushpin"></span> -->
                    @endif
                 @endif
              @else
                 <a href="{{UrlPrefixes::$item_opac}}{{$r['public_title']['id']}}" >
                {{$r['public_title']['title']}}
                @if(user_access_mentainer() && isset($r['completion_level']) && $r['completion_level'] == 1)
                  <a href="{{{UrlPrefixes::$item_edit}}}{{{$r['id']}}}"><span class="old_rec glyphicon glyphicon-wrench"></span></a>>
                @endif
              @endif

            @else
              <a href="{{UrlPrefixes::$item_opac}}{{$r['id']}}">
              {{--<!--  {{$r['title']}} -->--}}
              {{$document->label}}
               @if(user_access_mentainer() && isset($r['completion_level']) && $r['completion_level'] == 1)
                 &nbsp;<a href="{{{UrlPrefixes::$item_edit}}}{{{$r['id']}}}"><span class="old_rec glyphicon glyphicon-wrench"></span></a>
               @endif
            @endif
              </a>

            @if(!empty($r['authors']))
              <br> <span class="label_solr">{{tr('Author')}}:
              @foreach ($r['authors'] as $index=>$author)
                  &nbsp;<a href="{{UrlPrefixes::$item_opac}}{{$author['id']}}">{{$author['name']}}</a>@if($index < count($r['authors'])-1),@endif
              @endforeach
              </span>
            @endif

            @if(!empty($r['publishers']))
              <br> <span class="label_solr">{{tr('Publisher')}}:
              @foreach ($r['publishers'] as $index=>$publisher)
                  <a href="{{UrlPrefixes::$item_opac}}{{$publisher['id']}}">{{$publisher['name']}}</a>@if($index < count($r['publishers'])-1),@endif
              @endforeach
              </span>
            @endif

            @if(!empty($r['issues_num']))
              <br> <span class="label_solr">{{tr('Number of issues')}}: {{$r['issues_num']}}</span>
            @endif

            @if(!empty($r['time_range']))
              <br> <span class="label_solr">{{tr('Periodic time range')}}: {{$r['time_range']}}</span>
            @endif

            @if(!empty($r['location']))
            <?php $location_num = count($r['location']); $ii = 0; ?>
              <br> <span class="label_solr">{{tr('Location in library')}}:
                 @foreach ($r['location'] as $loc)
                  {{$loc}}@if(++$ii !== $location_num), @endif
                 @endforeach
              </span>
            @endif

            @if(isset($r['items']) && ! empty($r['items']))
              @if(user_access_login())
                 <br>
{{--<!--                  <span class="label label-info" aria-hidden="true">{{tr('Available files')}}:</span> -->--}}
                 <span class="label_solr" aria-hidden="true">{{tr('Available copies')}}:</span>
                 <span class="sr-only">{{tr('Available files for instant download')}}: </span>
                 @foreach ($r['items'] as $item )
                   @if(isset($item['id']))
                      @if( isset($item['label']) && !empty($item['label']) )
                          @if( $item['title']== 'digital-item' )
                           <span class="sdonwload label label-success"><a href="{{UrlPrefixes::$item_opac}}{{$item['id']}}/download/0">{{$item['label']}}</a></span>
                          @else
                           <span class="sdonwload label label-success"><a href="{{UrlPrefixes::$item_opac}}{{$item['id']}}">{{$item['label']}}</a></span>
                          @endif

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

                    @if(user_access_mentainer() && isset($m['completion_level']) && $m['completion_level'] == 1)
                      &nbsp;<a href="{{{UrlPrefixes::$item_edit}}}{{{$m['id']}}}"><span class="old_rec glyphicon glyphicon-wrench"></span></a>
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
                        @if(user_access_mentainer() && isset($m['completion_level']) && $m['completion_level'] == 1)
                          &nbsp;<a href="{{{UrlPrefixes::$item_edit}}}{{{$m['id']}}}"><span class="old_rec glyphicon glyphicon-wrench"></span></a>
                        @endif
                      </li>
                      @endforeach
                    </ol>
                  @endif
              @endif
            @endif


{{--<!--         <div style ="font-size:11px;"> -->--}}
{{--<!--             @if($highlighting->getResult($document->id)) -->--}}
{{--<!--               @foreach ($highlighting->getResult($document->id) as $field => $highlight) -->--}}
                <?php  // echo tr($field) . ': <span style ="font-size:11px;font-style: italic;">"' . implode(' (...) ', $highlight) . '..."</span><br/>'; ?>
{{--<!--               @endforeach -->--}}
{{--<!--             @endif -->--}}
{{--<!--           </div> -->--}}

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

           @if (Config::get('arc.SOLR_DEV',0))
              <div class="otype" style="float: right"  >{{$ft}}   {{$id}}</div>
           @endif





            <div class="clearfix"></div>
      </li>
