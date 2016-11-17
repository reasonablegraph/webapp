@section('content')

	<h2>ERROR</h2>
	<form method="GET" action="/prepo/edit_step1">
	<input type="hidden"  name="s" value="{{{$submit_id}}}" />
	<input type="submit"  name="PREV" value="STEP 1"/>
	</form>

@stop



