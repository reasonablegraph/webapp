	<div class="reset-facet">
		<?php
			$input_reset = Input::all();
			foreach ($facetes_names as $name){
				unset($input_reset[$name]);
			}
			unset($input_reset['start']);
			unset($input_reset['page']);
			//unset($input_reset['nomanif']);
		?>

		@if($isFacetedQueryFlag)
		<a href="?{{ http_build_query($input_reset) }}">{{tr('Reset all facetes')}}</a>
		@endif
	</div>