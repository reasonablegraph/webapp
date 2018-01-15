@section('content')
<?php auth_check_mentainer(); ?>


		<div class="log_report">
		<h1 class='admin item-title spool'>All {{$user}} entries</h1>

		<div class="log_action">
		<div class="panel panel-primary">
			<div class="panel-files panel-body">
				<div id="metsearch">
					<form method="POST" class="form-inline" role="form">
						<div class="form-group">
							<label for="user" > {{tr('Select Username')}}:</label>
							<?php PUtil::toSelect("username",$users_arr,$user) ?>
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

		<div class="panel panel-primary">
			<table class="table table-bordered table-condensed table-striped table-hover">
				<thead class="a_thead">
					<tr>
						<th colspan="1">
							<span class="a_shead">Object type</span>
						</th>
						<th colspan="1">
							<span class="a_shead">Number</span>
						</th>
					</tr>
				</thead>
					<tbody>
							@foreach ($results as $k => $v)
										<tr>
					  					<td>&#160; <a href="/archive/recent?t={{$v['obj_type']}}&ft=user&usr={{$user}}" >
					  					{{tr("object type: ".$v['obj_type'])}}</a></td>
					  					<td style="text-align:right" >&#160;{{$v['count']}}</td>
										</tr>
							@endforeach
					</tbody>
			</table>
		</div>

	</div>



@stop
