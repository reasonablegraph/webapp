@section('content')
<?php auth_check_mentainer(); ?>

<?php //echo "<pre>"; print_r($results); echo "</pre>"; ?>



		<div class="log_report">
		<h1 class='admin item-title spool'>All {{$user}} items</h1>

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
					  					<td>&#160; <a href="/archive/recent?t={{$v['obj_type']}}&ft=user" >
					  					{{tr("object type: ".$v['obj_type'])}}</a></td>
					  					<td style="text-align:right" >&#160;{{$v['count']}}</td>
										</tr>
							@endforeach


					</tbody>
			</table>
		</div>

	</div>


@stop