@section('content')
<?php auth_check_mentainer(); ?>

    <div class="log_action">
    <div class='admin item-title log'>
      <span>Files downloads log</span >
      <span class='now-time'>{{sprintf('Current time: %s', date('H:i:s \o\n d-m-Y e'))}}</span>
    </div>

    <div class="panel panel-primary">
      <div class="panel-files panel-body">
        <div id="metsearch">
          <form method="POST" class="form-inline" role="form" action="/prepo/download-log">
            <div class="form-group">
              <label for="user" > {{tr('Select Time')}}:</label>
              <?php PUtil::toSelect("datetime",$datetime_arr,$default_datetime) ?>&#160;
              <label for="filter" > {{tr('Filter')}}:</label>
               <?php PUtil::toSelect("filter",$filter_arr,$default_filter) ?>&#160;
            </div>
            <div class="fileUpload uploadbut">
              <span>{{tr('Search log')}}</span>
              <input id="uploadBtn" class="upload" type="submit" value="search_log">
            </div>
          </form>
          <span class="cnt_log">{{tr('Total Entries')}}: <span class="badge">{{$total}}</span> </span>
        </div>
      </div>
    </div>

    <div class="panel panel-primary">
      @if($view == 1)
        <table class="table table-bordered table-condensed table-striped table-hover">
          <thead class="a_thead">
            <tr>
              <th colspan="1">
                <span class="a_shead">Date</span>
              </th>
              <th colspan="1">
                <span class="a_shead">File name</span>
              </th>
              <th colspan="1">
                <span class="a_shead">Size</span>
              </th>
              <th colspan="1">
                <span class="a_shead">Digital item</span>
              </th>
              <th colspan="1">
                <span class="a_shead">Username</span>
              </th>
  <!-- 						<th colspan="1"> -->
  <!-- 							<span class="a_shead">IP</span> -->
  <!-- 						</th> -->
              <th colspan="1">
                <span class="a_shead">Agent</span>
              </th>
            </tr>
          </thead>
            <tbody>
                @foreach ($results as $k => $v)
                <tr>
                  <td><div style="width: 160px;">&#160;{{sprintf('%s', date('H:i:s \o\n d-m-Y', strtotime($v['action_time'])))}}</div></td>
                  <td style="color: red;" >&#160;{{PUtil::truncate_chars($v['bitsream_label'], 40)}}</td>
                  <td>&#160;{{PUtil::formatSizeBytes($v['size'])}}</td>
                  <td>&#160;<a href="{{UrlPrefixes::$item_opac}}{{$v['item_id']}}">{{$v['item_label']}}</a></td>
                  <td>&#160;{{$v['username']}}</td>
  <!-- 			  					<td>&#160;{{$v['remote_addr']}}</td> -->
                  <td>&#160;{{$v['user_agent']}}</td>
                  </tr>
                @endforeach
            </tbody>
        </table>

      @elseif($view == 2)
        <table class="table table-bordered table-condensed table-striped table-hover">
          <thead class="a_thead">
            <tr>
              <th colspan="1">
                <span class="a_shead">File name</span>
              </th>
              <th colspan="1">
                <span class="a_shead">Size</span>
              </th>
              <th colspan="1">
                <span class="a_shead">Digital item</span>
              </th>
              <th colspan="1">
                <span class="a_shead">Downloads</span>
              </th>
               <th colspan="1">
                <span class="a_shead">Owner</span>
              </th>
            </tr>
          </thead>
            <tbody>
                @foreach ($results as $k => $v)
                <tr>
                  <td style="color: red;" >&#160;{{PUtil::truncate_chars($v['bitsream_label'], 40)}}</td>
                  <td>&#160;{{PUtil::formatSizeBytes($v['size'])}}</td>
                  <td>&#160;<a href="{{UrlPrefixes::$item_opac}}{{$v['item_id']}}">{{$v['item_label']}}</a></td>
                  <td>&#160;{{$v['count']}}</td>
                  <td>&#160;{{$v['creator']}}</td>
                  </tr>
                @endforeach
            </tbody>
        </table>


      @else
        <table class="table table-bordered table-condensed table-striped table-hover">
          <thead class="a_thead">
            <tr>
              <th colspan="1">
                <span class="a_shead">Owner</span>
              </th>
               <th colspan="1">
                <span class="a_shead">Downloads</span>
              </th>
            </tr>
          </thead>
            <tbody>
                @foreach ($results as $k => $v)
                <tr>
                  <td>&#160;{{$v['creator']}}</td>
                  <td>&#160;{{$v['count']}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
      @endif
    </div>

    <div style="text-align:center; font-weight:bold;">
     {{PSnipets::admin_paging($limit, $offset, $total)}}
    </div>

  </div>

@stop
