<?php

/**
  * Did you know? you can modify this by copying the entire plugin folder (ajax-upload-file/)
  * into your child theme, and from there you can edit any file except the core files ( for 
  * core files, any changes made in your child theme won't be applicable )
  *
  * @since 0.1
  * @author Samuel Elh <samelh.com/contact/>
  */

$rand = rand(); // for multiple uses

?>

<?php do_action("afu_before_template_content"); ?>

<div class="afu-process-file" data-task="<?php echo $data_task; ?>">

	<input type="hidden" name="id" value="<?php echo esc_attr( $a['unique_identifier'] ); ?>" />
	<input type="file" name="afu_file" style="position:absolute;visibility:hidden;" id="afu_field_<?php echo $rand; ?>" />

	<?php do_action("afu_before_template_buttons"); ?>

	<label class="select" for="afu_field_<?php echo $rand; ?>">
		<i class="afuico afuico-upload-cloud"></i> <span data-text="<?php echo esc_attr( $a['select_file_button_value'] ); ?>"> <?php echo esc_attr( $a['select_file_button_value'] ); ?></span>
	</label>
	<label class="upload" disabled="disabled">
		<i class="afuico afuico-ok"></i> <?php echo esc_attr( $a['upload_button_value'] ); ?>
	</label>

	<?php if( ! $a["disallow_remove_button"] ) : ?>
	<label class="remove" disabled="disabled">
		<i class="afuico afuico-cancel"></i> <?php echo esc_attr( $a['remove_file_button_value'] ); ?>
	</label>
	<?php endif; ?>

	<?php do_action("afu_after_template_buttons"); ?>
	<?php wp_nonce_field('_afu_nonce', '_afu_nonce'); ?>

</div>

<?php do_action("afu_after_template_content"); ?>