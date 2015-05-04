<form action="" method="post" id="gffd-gf-feed-form">
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">
					<label>Feed Activation</label>
				</th>
				<td>
					<label for="gffd_feed_active">
						<input type="checkbox" name="gffd_feed_active" id="gffd_feed_active" <?php
							if( gffd_feeds_get_form_feed_settings( gffd_request( 'form_id' ), 'as_object' )->feed_active == 'active' ){
								?> checked="checked" <?php
							}
						?>>

						<?php _e( "Use " . gffd_glossary( 'service_name' ) . " to process payments on this form." ); ?>
					</label>

					<?php if( gffd_feeds_get_form_feed_settings( gffd_request( 'form_id' ), 'as_object' )->feed_active == 'active' ){ ?>
						<span class="gffd-feed-tag active">
							<?php _e("Currently Active"); ?>
						</span>
					<?php } else { ?>
						<span class="gffd-feed-tag inactive"><?php _e("Currently Inactive"); ?></span>
					<?php } ?>

					<p class="description">
						<?php _e("By checking the option above, you are telling " . gffd_glossary( 'service_name' )
						. " to process payments associated with this form."); ?>
					</p>
				</td>
			</tr>

			<tr><td colspan="2"><div class="hr-divider"></div></td></tr>

			<tr>
				<td colspan="2">
					<h2 id="gffd_feed_settings_area">Feed</h2>
					<p class="description">
						<?php _e("The below fields are required to perform a purchase.
						Please select the field from your form that will be <em>fed</em>
						into " . gffd_glossary( 'service_name' ) . ". " ); ?>
					</p>
				</td>
			</tr>

			<tr valign="top" class="gffd_header">
				<th scope="row"><strong>Required Field</strong></th>
				<td><strong>Where will the data come from?</strong></td>
			</tr>

			<?php foreach(gffd_is_array(gffd_get_purchase_field_requirements()) as $required_field){ ?>
				<tr valign="top">
					<th scope="row">
						<?php _e($required_field['label']); ?>
					</th>
					<td>
						<select class="feed-dropdown" id="feed-dropdown-<?php echo $required_field['gffd_index']; ?>" name="gffd_form_feed_indexes[<?php echo $required_field['gffd_index']; ?>]" style="width:300px;">
							<option value=""><?php _e("Choose a source for this data"); ?></option>
							<option value="">--</option>
							<?php foreach(gffd_is_array(gffd_get_form_fields(gffd_get_form(gffd_request('form_id')))) as $form_field){ ?>
								<option <?php
							if(
								// The setting is there (may not be)
								is_object(
									gffd_feeds_get_form_feed_settings(
										gffd_request( 'form_id' ),
										'as_object'
									)->feed_indexes
								)

								// The setting is already set
								&& gffd_feeds_get_form_feed_settings(
									gffd_request( 'form_id' ),
									'as_object'
							   	)->feed_indexes->$required_field[ 'gffd_index' ] == $form_field[0]
							){ ?>
									selected="selected"
								<?php } ?> value="<?php echo $form_field[0]; ?>"><?php echo $form_field[1]; ?></option>
							<?php } ?>
						</select><br>
						<small class="description"><?php _e($required_field['meta']); ?></small>
					</td>
				</tr>
			<?php } ?>

		</tbody>
	</table>

	<p class="submit">
		<input type="submit" name="gffd-feed-admin-edit-submit" id="gffd-feed-admin-edit-submit" class="button-primary" value="<?php _e("Save"); ?>">
	</p>
</form>
