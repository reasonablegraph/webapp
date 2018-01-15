@section('content')

<div>
	<p><?= tr('A submission for this item is already active. Please try again later, this page will self-refresh every 10 seconds or press the following link to reload anyway.') ?></p>
	<p><a href="javascript:window.location.reload(true);">Refresh</a></p>
</div>

<script>
	setTimeout(function(){ window.location.reload(true); }, 10000);
</script>

@stop