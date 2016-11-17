<?php

	$bundle = null;
	$BUNDLE_MAP = null;
	$bundle_lock_internal = Config::get('arc.bundle_lock_internal',0);

		if (!$bundle_lock_internal){

			if (isset($obj_class)){
				if ($obj_class == 'digital-item'){
					$bundle = 'ORIGINAL';
				}else if ($obj_class == 'auth-manifestation'){
					$bundle = 'ORIGINAL';
					$BUNDLE_MAP = Lookup::get_bitstream_bundles_create_item();
				}
			}

			if (empty($bundle)){
				$bundle = 'INTERNAL';
			}
			if (empty($BUNDLE_MAP)){
				$BUNDLE_MAP = Lookup::get_bitstream_bundles();
			}

		}else{

			$bundle = 'INTERNAL';
			$BUNDLE_MAP = Lookup::get_bitstream_bundles_lock_internal();
// 			if (isset($obj_class)){
// 				if ($obj_class == 'digital-item'){
// 					$bundle = 'INTERNAL';
// 					$BUNDLE_MAP = Lookup::get_bitstream_bundles_digital_item();
// 				}else{
// 					$bundle = 'INTERNAL';
// 					$BUNDLE_MAP = Lookup::get_bitstream_bundles_lock_internal();
// 				}
// 			}

// 			if (empty($bundle)){
// 				$bundle = 'INTERNAL';
// 			}
// 			if (empty($BUNDLE_MAP)){
// 				$BUNDLE_MAP = Lookup::get_bitstream_bundles_lock_internal();
// 			}
		}

?>

<div class="row">

	<div class="col-sm-12">
		<form class="form-inline">
			<div class="form-group">
				<p class="form-control-static">
				<label><?= tr('Upload type'); ?>:</label>
				</p>
			</div>
			<div class="form-group">
				<label class="radio-inline"> <input type="radio" name="form_type"  value="file" checked="checked" /> file</label>
				<label class="radio-inline"> <input type="radio"   name="form_type" value="url" /> url 	</label>
			</div>
		</form>
	</div>

	<div class="col-sm-12">
		<div id="form_file">
			<form method="POST" id="form_file" enctype="multipart/form-data" action="<?=$action?>" class="form-inline">


				<?php if ($display_bundle):?>
					<div class="form-group bundle" >
						<label>Bundle:</label>
				<?php PUtil::print_select ( "bundle", null, $BUNDLE_MAP, $bundle, false );  ?>
					</div>
				<?php endif;?>

				<div class="form-group description">
					<label><?php echo tr('Thumb description') ?>:</label> <input class="form-control" type="text" name="thumb_desc" >
				</div>

								<?php if($display_seq_id): ?>
				 <div class="form-group seq_id">
					<label>Sequence:</label> <input class="form-control" type="text" name="seq_id" size="4">
				</div>
				<?php endif;?>



<!-- 			  <div class="form-group"> -->
<!-- 					<label>Upload:</label> <input class="form-control" name="uploadedfile[]" type="file" multiple="multiple" /> -->
<!-- 				</div> -->
<!-- 				<input type="submit" name="send_file" value="Send" class="btn btn-default"> -->



				<div class="form-group">
							<div class="fileUpload">
									<input id="uploadFile" name="uploadedfile[]" placeholder="Choose File"  type="file" multiple  />
							</div>
							<div class="fileUpload uploadbut">
									<span>Upload</span>
									<input id="uploadBtn" class="upload" type="submit" value="Send" name="send_file"/>
							</div>
				</div>


			</form>
		</div>



		<div id="form_url" style="display: none">
			<form method="POST" action="<?=$action?>" class="form-inline">
				<?php
				if (isset ( $file_prefix )) {
					printf ( '<input type="hidden" name="file_prefix" value="%s"/>', $file_prefix );
				}
				?>
				<?php  if ($display_bundle):?>
	 				<div class="form-group bundle">  <label>Bundle:</label> <?php  PUtil::print_select ( "bundle2", null, $BUNDLE_MAP, $bundle, false );  ?> </div>
				<?php endif;?>

					<div class="form-group description">
						<label><?php echo tr('Thumb description') ?>:</label> <input class="form-control" type="text" name="thumb_desc" size="10">
					</div>

				<?php if($display_seq_id): ?>
					 <div class="form-group seq_id">
							<label>Sequence:</label> <input type="text" name="seq_id" size="4" class="form-control">
					</div>
				<?php endif;?>
	 			<div class="form-group bit_url">
						<label>Upload url:</label> <input name="upload_url" type="text" size="30" class="form-control" />
				</div>
				<input type="submit" name="send_url" value="send" class="btn btn-default">
			</form>
		</div>
	</div>

</div>

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

<?php if($display_seq_id): ?>
<!--
<div style="text-align:left">
&nbsp; seq_id proeretiko
 -->
<?php endif;?>

