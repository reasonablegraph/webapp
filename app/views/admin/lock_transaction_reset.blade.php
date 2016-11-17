@section('content')

<?php
	auth_check_mentainer();
	drupal_set_title ( "users menu" );

	$reset_lock = $_REQUEST['reset_lock_transaction'];
?>


	<div class="row reset-box">
	<h1 class="admin item-title spool">Reset Lock Transaction</h1>
	@if (!$reset_lock)
		<form method="post">
				<div class="resetbtn">
					<span>Reset Lock Transaction</span>
					<input class="upload" type="submit" name="reset_lock_transaction" value="reset_lock_transaction" onClick="return confirm('Are you sure?')"/>
				</div>
		</form>
	@else
		<div class="valid_msg">The Lock Transaction have been reset</div>
	@endif
	</div>
	@include('includes.repomenu-links')

@stop