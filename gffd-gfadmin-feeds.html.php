<?php
	//Pull in the glossary, we're gonna need it.
	global $gffd_glossary;
?>
<div class="wrap" style="clear:both">
	<br style="clear:both">

	<?php if(!gffd_get_forms()){ ?>
		<p>You don't have any forms yet? <a href="admin.php?page=gf_new_form">Go build some!</a></p>
	<?php }else{ ?>
		<div id="gform_tab_group" class="gform_tab_group vertical_tabs">
			<ul id="gform_tabs" class="gform_tabs">

				<li class="<?php gffd_request_match_class_active('form_id', '', 'active', true); ?> gffd-active-feed">
					<a href="<?php gffd_feed_admin_url(true); ?>">
						<strong><?php _e('Help'); ?></strong>
					</a>
				</li>

				<li class="gffd-tab-header gffd-active-header">
					<a href="#"><strong><?php _e('Active'); ?></strong></a>
				</li>

				<div class="gffd-active-feeds gffd-side-tab-boxes">
					<?php foreach(gffd_get_forms() as $form){ ?>
						<?php if(gffd_feed_is_active($form->id)){ ?>
							<li class="<?php gffd_request_match_class_active('form_id', $form->id, 'active', true); ?> gffd-active-feed gffd-feed-link">
								<a href="<?php gffd_feed_admin_url('&form_id='.$form->id, true); ?>"><?php _e($form->title); ?></a>
							</li>
						<?php } ?>
					<?php } ?>
					</div>

				<li class="gffd-tab-header gffd-inactive-header"><a href="#"><strong><?php _e('Inactive'); ?></strong></a></li>
				<div class="gffd-inactive-feeds gffd-side-tab-boxes">
					<?php foreach(gffd_get_forms() as $form){ ?>
						<?php if(!gffd_feed_is_active($form->id)){ ?>
							<li class="<?php gffd_request_match_class_active('form_id', $form->id, 'active', true); ?> gffd-inactive-feed" gffd-feed-link>
								<a href="<?php gffd_feed_admin_url('&form_id='.$form->id, true); ?>"><?php _e($form->title); ?></a>
							</li>
						<?php } ?>
					<?php } ?>
				</div>
			</ul>
			<div id="gform_tab_container" class="gform_tab_container" style="min-height: 308px;">
				<div class="gform_tab_content" id="tab_settings">
					<?php 

					//If a form_id is requested, let's edit that form feed
					if(gffd_admin_is_requested('form_id')){ 


						// Let's keep the single feed edit HTML
						// on it's own file.
						include "gffd-gfadmin-feed.html.php";

					// If a form is not selected, show some help
					// information.
					}else{ ?>
						<h2><?php _e(gffd_glossary('plugin_name') . " Feed Configuration"); ?> </h2>
						<p>
							<?php _e("A form with a configured feed will send it's
							data to the ".gffd_glossary('service_name')." for processing any payments set up in the form.
							In order to get any form to use the ".gffd_glossary('service_name')." for payment processing,
							you must configure it's feed to ".gffd_glossary('service_name')."."); ?>
						</p>

						<h3>Configuring a New Feed</h3>
						<p>
							<?php _e("To configure a form's feed, just select it from the left.
							If the form already has a feed, it will be under the <strong>active</strong> section.
							If a form is not configured with a feed, you can select it from the <strong>inactive</strong>
							section on the left, and once you configure a feed, it will move to the active section."); ?>
						</p>

						<h3>Editing a Form's Current Feed</h3>
						<p>
							<?php _e("To edit any configured form's feed, just select the form from the
							<strong>active</strong> section to the left to modify it's feed settings."); ?>
						</p>

						<div class="hr-divider"></div>

						<p class="description">
							Brought to you by
							<a href="http://profiles.wordpress.org/aubreypwd/">Aubrey Portwood</a>
							of <a href="http://excion.co">Excion</a>.
						</p>

					<?php } ?>
				</div>
				<br style="clear:both">
			</div>
		</div>



	<?php } ?>

</div>