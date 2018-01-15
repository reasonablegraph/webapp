@section('content')
<?php auth_check_mentainer(); ?>

    <div class="log_action">
    <div class='admin item-title log'>
      <span>Graph reset log</span >
      <span class='now-time'>{{sprintf('Current time: %s', date('H:i:s \o\n d-m-Y e'))}}</span>
    </div>

    <div class="panel panel-primary">
        <table class="table table-bordered table-condensed table-striped table-hover">
          <thead class="a_thead">
            <tr>
              <th colspan="1">
                <span class="a_shead">pid</span>
              </th>
              <th colspan="1">
                <span class="a_shead">Start date</span>
              </th>
              <th colspan="1">
                <span class="a_shead">End date</span>
              </th>
              <th colspan="1">
                <span class="a_shead">Status</span>
              </th>
              <th style="text-align: center;" colspan="1">
                <span class="a_shead">Message</span>
              </th>
            </tr>
          </thead>
            <tbody>

                @foreach ($results as $k => $v)
                  <tr>
                  <td>&#160;{{$v['pid']}}</td>
                  <td><div style="width: 160px;">&#160;{{sprintf('%s', date('H:i:s \o\n d-m-Y', strtotime($v['start_dt'])))}}</div></td>
                  <td><div style="width: 160px;">&#160;@if ($v['end_dt'] != null){{sprintf('%s', date('H:i:s \o\n d-m-Y', strtotime($v['end_dt'])))}}@else Open... @endif</div></td>
                  <td>&#160;@if ($v['status'] == 2) Closed @else In process...  @endif</td>
                  <td style="text-align: center; color:red;">@if ($v['status'] == 1)
                        <form method="POST" class="form-inline" role="form" action="/prepo/reset-log">
                        <button class="action-btn delete" name="close" type="submit" value="close">Close</button>
                        <input name="id" value={{$v['id']}} type="hidden">
                        </form>
                      @else
                        &#160;{{$v['error_msg']}}
                      @endif
                  </td>
                  </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="text-align:center; font-weight:bold;">
     {{PSnipets::admin_paging($limit, $offset, $total)}}
    </div>

  </div>

@stop
