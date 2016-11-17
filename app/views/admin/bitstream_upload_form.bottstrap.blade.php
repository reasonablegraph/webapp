<div class="row">
	<div class="col-sm-12">
		<form class="form-inline">
		  <div class="form-group">
			<label class="radio-inline"><?php echo tr('UPLOAD TYPE') ?>:</label>
			<label class="radio-inline">
				<input type="radio" name="form_type" value="file"  checked="checked"/> file	  
			  </label>
			<label class="radio-inline">
				<input type="radio" name="form_type" value="url" /> url
			</label>
		  </div>
		</form>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
	
		<div id="form_file" class="form-inline well">
		<FORM method="POST" id="form_file" enctype="multipart/form-data" action="<?=$action?>">
			<?php  if ($display_bundle):?>
				<div class="form-group">
			    	<label for="bundle"><?php echo tr('Bundle') ?>:</label>
					<?php
						$BUNDLE_MAP = Lookup::get_bitstream_bundles();
						PUtil::print_select("bundle",null,$BUNDLE_MAP, $bundle ,false,false,"form-control");
					?>
			  	</div>
			<?php endif;?>
			<?php if($display_seq_id): ?>
				<div class="form-group">
			    	<label for="seq_id"><?php echo tr('Seq ID') ?>:</label>
			  		<input type="text" name="seq_id" size="4" class="form-control">  
			  	</div>
			<?php endif;?>
				<div class="form-group">
			    	<label for="uploadedfile[]"><?php echo tr('Upload') ?>:</label>
			  		<input name="uploadedfile[]" class="bitstreamup form-control" type="file" multiple="multiple" />
			  	</div>
				<!-- <input name="uploadedfile[]" class="bitstreamup" type="file" multiple="multiple" /> --> 
				<input type="submit" name="send_file" value="<?php echo tr('Send') ?>" class="btn btn-default">				
		</form>
		</div>

		<div id="form_url" style="display:none" class="form-inline well">
		<FORM  method="POST" action="<?=$action?>">
		<?php
		if (isset($file_prefix)){
			printf('<input type="hidden" name="file_prefix" value="%s"/>',$file_prefix);
		}
		?>
		
		<?php  if ($display_bundle):?>
			<div class="form-group">
		    	<label for="bundle">Bundle:</label>
				<?php
					$BUNDLE_MAP = Lookup::get_bitstream_bundles();
					PUtil::print_select("bundle",null,$BUNDLE_MAP, $bundle ,false,false,"form-control");
				?>
		    </div>
		<?php endif;?>
		<?php if($display_seq_id): ?>
				<div class="form-group">
			    	<label for="seq_id"><?php echo tr('Seq ID') ?>:</label>
			  		<input type="text" name="seq_id" size="4" class="form-control">  
			  	</div>
		<?php endif;?>
				<div class="form-group">
			    	<label for="upload_url"><?php echo tr('Upload URL') ?>:</label>
			  		<input name="upload_url" type="text" size="30" class="form-control" />
			  	</div>
				<input type="submit" name="send_url" value="<?php echo tr('Send') ?>" class="btn btn-default">
		</form>
		</div>

	</div> <!-- end col-sm -->
</div> <!-- end ROW -->


<?php if($display_seq_id): ?>
<!-- 
<div class="row">
	<div class="col-sm-12 col-sm-offset-2">
		<span id="helpBlock" class="help-block">seq_id proeretiko.</span>
	</div> 
</div> 
 -->
<?php endif;?>


<script type="text/javascript">
jQuery('#form_url').hide();

jQuery('input[name=form_type]').click(function(e){
	//alert(jQuery('input[name=form_type]:checked').val());
	var radio_selection = jQuery('input[name=form_type]:checked').val();
	if (radio_selection == 'url'){
		jQuery('#form_file').hide();
		jQuery('#form_url').show();
	} else {
		jQuery('#form_file').show();
		jQuery('#form_url').hide();
	}
});
</script>


