
<div class="edit-buttons">
	<button class="efbtn btn_save_fin" type="submit" name="save_finalize" value="save_finalize"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> {{tr('Save Finalize')}}</button>
	<button class="efbtn" type="submit" name="save" value="save"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> {{tr('Save')}}</button>
	<button class="efbtn b_tree"> <span class="glyphicon glyphicon-wrench" aria-hidden="true"></span> {{tr('Tree')}}</button>
	@if ($print_copy_cataloging)
		<button class="efbtn z3950"type="button"> <span class="glyphicon glyphicon-globe" aria-hidden="true"></span> Z39.50</button>
	@endif
	<button class="efbtn-r btn-close"  value="{{{UrlPrefixes::$cataloging}}}" > <span class="glyphicon glyphicon-off"></span> {{tr('Close')}}</button>
</div>





