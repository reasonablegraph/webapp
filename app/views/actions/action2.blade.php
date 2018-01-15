@section('content')
<?php auth_check_mentainer(); ?>

	<div class="log_action">
		<div class='admin item-title log'>
			<span>Staff Actions / Logs</span >
			<span class='now-time'>{{sprintf('Current time: %s', date('H:i:s \o\n d-m-Y e'))}}</span>
		</div>
<!-- 		<h1 class='admin item-title spool'>Staff Actions / Logs</h1> -->
		<div class="panel panel-primary">
			<div class="panel-files panel-body">
				<div id="metsearch">
					<form method="POST" class="form-inline" role="form">
						<div class="form-group">
							<label for="user" > {{tr('Select Username / Time')}}:</label>
							<?php PUtil::toSelect("username",$users_arr,$default_username) ?> <?php PUtil::toSelect("datetime",$datetime_arr,$default_datetime) ?>
						</div>
						<div class="fileUpload uploadbut">
							<span>{{tr('Search actions')}}</span>
							<input id="uploadBtn" class="upload" type="submit" value="Search_Metadata">
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	@if (!empty($results))
	<div class="log_action">
		<div class="panel panel-primary">
			<table class="table table-bordered table-condensed table-striped table-hover">
				<thead class="a_thead">
					<tr>
						<th colspan="1">
							<span class="a_shead">Time</span>
						</th>
						<th colspan="1">
							<span class="a_shead">Type</span>
						</th>
						<th colspan="1">
							<span class="a_shead">Url</span>
						</th>
						<th colspan="1">
							<span class="a_shead">Username</span>
						</th>
					</tr>
				</thead>
					<tbody>
							@foreach ($results as $k => $v)
								<tr>
			  					<td>&#160;{{sprintf('%s', date('H:i:s \o\n d-m-Y e', strtotime($v['last_event_ts'])))}}</td>
			  					<td>&#160;{{$v['type']}}</td>
			  					<td>&#160;{{$v['url']}}</td>
			  					<td>&#160;{{$v['user_name']}}</td>
								</tr>
							@endforeach
					</tbody>
			</table>
		</div>
	</div>
	@endif

@stop



