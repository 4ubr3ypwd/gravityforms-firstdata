<form method="post" action="">
	<h3><?php _e(gffd_glossary('plugin_name')); ?></h3>
	<table class="form-table">
		<?php // Get the settings to interate through.?>
		<?php $gffd_admin_settings=gffd_admin_settings(); ?>
		<?php // There should at lesat be 2 settings.?>
		<?php foreach($gffd_admin_settings as $setting_key => $setting){ ?>
			<tr valign="top">
					<th scope="row">
					<?php if(!isset($setting['checkbox_label']) || $setting['checkbox_label']!=true){ ?>
						<label for="<?php echo $setting_key; ?>">
							<?php _e( $setting['label'] ); ?>
						</label>
					<?php } ?>
					</th>
					<td>
						<p>
							<<?php echo $setting['html_tag']; ?>
							<?php if(isset($setting['html_type'])){ ?>
								type="<?php echo $setting['html_type']; ?>"
							<?php } ?> 
								name="<?php echo $setting_key; ?>" 
								id="<?php echo $setting_key; ?>" 
	
								<?php if($setting['html_type']!='checkbox'){ ?>
									value="<?php echo gffd_admin_get_setting($setting_key); ?>"
								<?php }elseif(gffd_admin_get_setting($setting_key)){ ?>
									checked="checked" data-value="<?php echo gffd_admin_get_setting($setting_key); ?>"
								<?php } ?>
							>
							<?php if(isset($setting['html_close']) && $setting['html_close']==true){ ?>
								</<?php echo $setting['html_tag']; ?>>
							<?php } ?>
	
							<?php if(isset($setting['checkbox_label']) && $setting['checkbox_label']==true){ ?>
								<? _e($setting['label']); ?>
							<?php } ?>

							<?php if(isset($setting['description'])){ ?>
						</p>
						<p class="description">
							<small>
								<?php _e($setting['description']); ?>
							</small>
						</p>
						<?php } ?>
					</td>
			</tr>
		<?php } ?>
	</table>

	<p class="submit">
		<input type="submit" name="gffd_admin_submit" class="button-primary" value="<?php _e("Save"); ?>">
	</p>
</form>