<?php include_once( dirname(__FILE__).'/common_header.php' ); ?>

<style type="text/css">
	
	#variation-attributes-table,
	#variation-attributes-table select.select {
		width: 100%;
	}
	#variation-attributes-table th {
		width: 50%;
		text-align: left;
	}

	#variation-merger-table,
	#variation-merger-table input.text_input,
	#variation-merger-table select.select {
		width: 100%;
	}
	#variation-merger-table th {
		text-align: left;
	}

	#custom-shortcodes-table {
		width: 100%;
	}
	#custom-shortcodes-table input.text_input {
		width: 95%;
	}
	#custom-shortcodes-table th {
		text-align: left;
	}
	#custom-shortcodes-table td {
		vertical-align: top;
	}
	#custom-shortcodes-table textarea {
		height: 6em;
		width: 100%;
	}

	#custom-variation-fields-table {
		width: 100%;
	}
	#custom-variation-fields-table input.text_input {
		width: 95%;
	}
	#custom-variation-fields-table th {
		text-align: left;
	}

	#side-sortables .postbox input.text_input,
	#side-sortables .postbox select.select {
	    width: 50%;
	}
	#side-sortables .postbox label.text_label {
	    width: 45%;
	}
	#side-sortables .postbox p.desc {
	    margin-left: 5px;
	}

</style>

<div class="wrap amazon-page">
	<div class="icon32" style="background: url(<?php echo $wpl_plugin_url; ?>img/amazon-32x32.png) no-repeat;" id="wpl-icon"><br /></div>
          
	<?php include_once( dirname(__FILE__).'/settings_tabs.php' ); ?>		
	<?php echo $wpl_message ?>

	<form method="post" id="settingsForm" action="<?php echo $wpl_form_action; ?>">

	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">

			<div id="postbox-container-1" class="postbox-container">
				<div id="side-sortables" class="meta-box">


					<!-- first sidebox -->
					<div class="postbox" id="submitdiv">
						<!--<div title="Click to toggle" class="handlediv"><br></div>-->
						<h3><span><?php echo __('Update','wpla'); ?></span></h3>
						<div class="inside">

							<div id="submitpost" class="submitbox">

								<div id="misc-publishing-actions">
									<div class="misc-pub-section">
										<p><?php echo __('This page contains some advanced options for special use cases.','wpla') ?></p>
									</div>
								</div>

								<div id="major-publishing-actions">
									<div id="publishing-action">
										<input type="hidden" name="action" value="save_wpla_advanced_settings" >
										<input type="submit" value="<?php echo __('Save Settings','wpla'); ?>" id="save_settings" class="button-primary" name="save">
									</div>
									<div class="clear"></div>
								</div>

							</div>

						</div>
					</div>

					<?php if ( ( ! is_multisite() ) || ( is_main_site() ) ) : ?>
					<div class="postbox" id="UninstallSettingsBox">
						<h3 class="hndle"><span><?php echo __('Uninstall on removal','wpla') ?></span></h3>
						<div class="inside">

							<label for="wpl-option-uninstall" class="text_label"><?php echo __('Uninstall','wpla'); ?>:</label>
							<select id="wpl-option-uninstall" name="wpla_option_uninstall" title="Uninstall" class=" required-entry select">
								<option value="0" <?php if ( $wpl_option_uninstall != '1' ): ?>selected="selected"<?php endif; ?>><?php echo __('No','wpla'); ?></option>
								<option value="1" <?php if ( $wpl_option_uninstall == '1' ): ?>selected="selected"<?php endif; ?>><?php echo __('Yes','wpla'); ?></option>
							</select>
							<p class="desc" style="display: block;">
								<?php echo __('Enable this to completely remove listings, orders and settings when removing the plugin.','wpla'); ?><br><br>
							</p>

						</div>
					</div>
					<?php endif; ?>

				</div>
			</div> <!-- #postbox-container-1 -->





			<!-- #postbox-container-3 -->
			<?php if ( ( ! is_multisite() || is_main_site() ) && apply_filters( 'wpla_enable_capabilities_options', true ) ) : ?>
			<div id="postbox-container-3" class="postbox-container">
				<div class="meta-box-sortables ui-sortable">
					
					<div class="postbox" id="PermissionsSettingsBox">
						<h3 class="hndle"><span><?php echo __('Roles and Capabilities','wpla') ?></span></h3>
						<div class="inside">

							<?php
								$wpl_caps = array(
									'manage_amazon_listings'  => __('Manage Amazon Listings','wpla'),
									'manage_amazon_options'   => __('Manage Amazon Settings','wpla'),
									// 'prepare_amazon_listings' => __('Prepare Listings','wpla'),
									// 'publish_amazon_listings' => __('Publish Listings','wpla'),
								);
							?>

							<table style="width:100%">
                            <?php foreach ($wpl_available_roles as $role => $role_name) : ?>
                            	<tr>
                            		<th style="text-align: left">
		                                <?php echo $role_name; ?>
		                            </th>

		                            <?php foreach ($wpl_caps as $cap => $cap_name ) : ?>
                            		<td>
		                                <input type="checkbox" 
		                                    	name="wpla_permissions[<?php echo $role ?>][<?php echo $cap ?>]" 
		                                       	id="wpla_permissions_<?php echo $role.'_'.$cap ?>" class="checkbox_cap" 
		                                       	<?php if ( isset( $wpl_wp_roles[ $role ]['capabilities'][ $cap ] ) ) : ?>
		                                       		checked
		                                   		<?php endif; ?>
		                                       	/>
		                                       	<label for="wpla_permissions_<?php echo $role.'_'.$cap ?>">
				                               		<?php echo $cap_name; ?>
				                               	</label>
			                            </td>
		                            <?php endforeach; ?>

		                        </tr>
                            <?php endforeach; ?>
                        	</table>


						</div>
					</div>

				</div>
			</div> <!-- #postbox-container-3 -->
			<?php endif; ?>


			<!-- #postbox-container-2 -->
			<div id="postbox-container-2" class="postbox-container">
				<div class="meta-box-sortables ui-sortable">
					
					<div class="postbox" id="UISettingsBox">
						<h3 class="hndle"><span><?php echo __('User Interface','wpla') ?></span></h3>
						<div class="inside">

							<label for="wpl-default_matcher_selection" class="text_label">
								<?php echo __('Default matching query','wpla') ?>
                                <?php wpla_tooltip('Select which product property to use by default when matching products on Amazon.') ?>
							</label>
							<select id="wpl-default_matcher_selection" name="wpla_default_matcher_selection" class=" required-entry select">
								<option value="title" <?php if ( $wpl_default_matcher_selection == 'title' ): ?>selected="selected"<?php endif; ?>><?php echo __('Title','wpla') ?></option>
								<option value="sku"   <?php if ( $wpl_default_matcher_selection == 'sku' ):   ?>selected="selected"<?php endif; ?>><?php echo __('SKU','wpla') ?></option>
				                <?php foreach ($wpl_available_attributes as $attribute) : ?>
									<option value="<?php echo $attribute->label ?>"   <?php if ( $wpl_default_matcher_selection == $attribute->label ):   ?>selected="selected"<?php endif; ?>><?php echo $attribute->label ?></option>
				                <?php endforeach; ?>
							</select>

							<label for="wpl-dismiss_imported_products_notice" class="text_label">
								<?php echo __('Imported products notice','wpla') ?>
                                <?php wpla_tooltip('If you dismissed this message you can enable it here again - but it will always show when you filter for "Imported" listings.') ?>
							</label>
							<select id="wpl-dismiss_imported_products_notice" name="wpla_dismiss_imported_products_notice" class=" required-entry select">
								<option value="0" <?php if ( $wpl_dismiss_imported_products_notice != '1' ): ?>selected="selected"<?php endif; ?>><?php echo __('Always show when there are listings to be imported','wpla'); ?></option>
								<option value="1" <?php if ( $wpl_dismiss_imported_products_notice == '1' ): ?>selected="selected"<?php endif; ?>><?php echo __('Only show when viewing imported listings','wpla'); ?></option>
							</select>

							<label for="wpl-enable_missing_details_warning" class="text_label">
								<?php echo __('Warn when product details are missing','wpla') ?>
                                <?php wpla_tooltip('This will show a warning when you create or update a product which is missing required details like SKU, price or quantity.') ?>
							</label>
							<select id="wpl-enable_missing_details_warning" name="wpla_enable_missing_details_warning" class=" required-entry select">
								<option value="0" <?php if ( $wpl_enable_missing_details_warning != '1' ): ?>selected="selected"<?php endif; ?>><?php echo __('No','wpla'); ?></option>
								<option value="1" <?php if ( $wpl_enable_missing_details_warning == '1' ): ?>selected="selected"<?php endif; ?>><?php echo __('Yes','wpla'); ?></option>
							</select>

							<label for="wpl-enable_thumbs_column" class="text_label">
								<?php echo __('Enable listing thumbnails','wpla') ?>
                                <?php wpla_tooltip('Enable this to show product thumbnails on the listings page. Disabled by default to save screen estate.') ?>
							</label>
							<select id="wpl-enable_thumbs_column" name="wpla_enable_thumbs_column" class="required-entry select">
								<option value="0" <?php if ( $wpl_enable_thumbs_column != '1' ): ?>selected="selected"<?php endif; ?>><?php echo __('No','wpla'); ?> (<?php _e('default','wpla'); ?>)</option>
								<option value="1" <?php if ( $wpl_enable_thumbs_column == '1' ): ?>selected="selected"<?php endif; ?>><?php echo __('Yes','wpla'); ?></option>
							</select>

							<label for="wpl-enable_custom_product_prices" class="text_label">
								<?php echo __('Enable custom price field','wpla') ?>
                                <?php wpla_tooltip('If do not use custom prices in Amazon and prefer less options when editing a product, you can disable the custom price fields here.') ?>
							</label>
							<select id="wpl-enable_custom_product_prices" name="wpla_enable_custom_product_prices" class=" required-entry select">
								<option value="0" <?php if ( $wpl_enable_custom_product_prices == '0' ): ?>selected="selected"<?php endif; ?>><?php echo __('No','wpla'); ?></option>
								<option value="1" <?php if ( $wpl_enable_custom_product_prices == '1' ): ?>selected="selected"<?php endif; ?>><?php echo __('Yes','wpla'); ?> (<?php _e('default','wpla'); ?>)</option>
								<option value="2" <?php if ( $wpl_enable_custom_product_prices == '2' ): ?>selected="selected"<?php endif; ?>><?php echo __('Hide for variations','wpla'); ?></option>
							</select>

							<label for="wpl-enable_minmax_product_prices" class="text_label">
								<?php echo __('Enable min. / max. price fields','wpla') ?>
                                <?php wpla_tooltip('If do not use minimum and maximum prices in Amazon and prefer less options when editing a product, you can disable these fields here.') ?>
							</label>
							<select id="wpl-enable_minmax_product_prices" name="wpla_enable_minmax_product_prices" class=" required-entry select">
								<option value="0" <?php if ( $wpl_enable_minmax_product_prices == '0' ): ?>selected="selected"<?php endif; ?>><?php echo __('No','wpla'); ?> (<?php _e('default','wpla'); ?>)</option>
								<option value="1" <?php if ( $wpl_enable_minmax_product_prices == '1' ): ?>selected="selected"<?php endif; ?>><?php echo __('Yes','wpla'); ?></option>
								<option value="2" <?php if ( $wpl_enable_minmax_product_prices == '2' ): ?>selected="selected"<?php endif; ?>><?php echo __('Hide for variations','wpla'); ?></option>
							</select>

						</div>
					</div>

					<div class="postbox" id="RepricingSettingsBox">
						<h3 class="hndle"><span><?php echo __('Repricing Tool','wpla') ?></span></h3>
						<div class="inside">

							<label for="wpl-pricing_info_expiry_time" class="text_label">
								<?php echo __('Update lowest price info','wpla') ?>
                                <?php wpla_tooltip('Select the time after which the lowest price information is refreshed from Amazon for listings with status "online".') ?>
							</label>
							<select id="wpl-pricing_info_expiry_time" name="wpla_pricing_info_expiry_time" class=" required-entry select">
								<option value=""   <?php if ( $wpl_pricing_info_expiry_time == ''   ): ?>selected="selected"<?php endif; ?>><?php echo __('Off','wpla'); ?></option>
								<option value=".1" <?php if ( $wpl_pricing_info_expiry_time == '.1' ): ?>selected="selected"<?php endif; ?>><?php echo __('Every 6 min.','wpla'); ?></option>
								<option value=".2" <?php if ( $wpl_pricing_info_expiry_time == '.2' ): ?>selected="selected"<?php endif; ?>><?php echo __('Every 12 min.','wpla'); ?></option>
								<option value=".5" <?php if ( $wpl_pricing_info_expiry_time == '.5' ): ?>selected="selected"<?php endif; ?>><?php echo __('Every 30 min.','wpla'); ?></option>
								<option value="1"  <?php if ( $wpl_pricing_info_expiry_time == '1'  ): ?>selected="selected"<?php endif; ?>><?php echo __('Every hour','wpla'); ?></option>
								<option value="2"  <?php if ( $wpl_pricing_info_expiry_time == '2'  ): ?>selected="selected"<?php endif; ?>><?php echo sprintf( __('Every %s hours','wpla'), 2);  ?></option>
								<option value="3"  <?php if ( $wpl_pricing_info_expiry_time == '3'  ): ?>selected="selected"<?php endif; ?>><?php echo sprintf( __('Every %s hours','wpla'), 3);  ?></option>
								<option value="6"  <?php if ( $wpl_pricing_info_expiry_time == '6'  ): ?>selected="selected"<?php endif; ?>><?php echo sprintf( __('Every %s hours','wpla'), 6);  ?></option>
								<option value="12" <?php if ( $wpl_pricing_info_expiry_time == '12' ): ?>selected="selected"<?php endif; ?>><?php echo sprintf( __('Every %s hours','wpla'), 12); ?></option>
								<option value="24" <?php if ( $wpl_pricing_info_expiry_time == '24' ): ?>selected="selected"<?php endif; ?>><?php echo sprintf( __('Every %s hours','wpla'), 24); ?></option>
							</select>


							<label for="wpl-repricing_use_lowest_offer" class="text_label">
								<?php echo __('Upprice based on','wpla') ?>
                                <?php wpla_tooltip('Select whether only the Buy Box price should be checked - or whether the lowest offer / next competitor price should be used when you already have the Buy Box.') ?>
							</label>
							<select id="wpl-repricing_use_lowest_offer" name="wpla_repricing_use_lowest_offer" class=" required-entry select">
								<option value="0" <?php if ( $wpl_repricing_use_lowest_offer == '0' ): ?>selected="selected"<?php endif; ?>><?php echo __('Buy Box only','wpla'); ?> (Default)</option>
								<option value="1" <?php if ( $wpl_repricing_use_lowest_offer == '1' ): ?>selected="selected"<?php endif; ?>><?php echo __('Boy Box and Lowest Offer','wpla'); ?> (beta)</option>
							</select>

							<label for="wpl-text-repricing_margin" class="text_label">
								<?php echo __('Repricing undercut','wpla'); ?>
                                <?php wpla_tooltip('Enter the amount you want to stay below your competitors lowest price.<br>Example: 0.01') ?>
							</label>
							<input type="text" name="wpla_repricing_margin" id="wpl-text-repricing_margin" value="<?php echo $wpl_repricing_margin; ?>" placeholder="0.00" class="text_input" />

						</div>
					</div>

					<div class="postbox" id="ImportSettingsBox">
						<h3 class="hndle"><span><?php echo __('Import','wpla') ?></span></h3>
						<div class="inside">

							<label for="wpl-enable_variation_image_import" class="text_label">
								<?php echo __('Import variation images','wpla') ?>
                                <?php wpla_tooltip('Variation images are imported by default. If you get timeout errors when importing large variable products from Amazon, you might have to disable this or increase your <code>max_execution_time</code> PHP setting.') ?>
							</label>
							<select id="wpl-enable_variation_image_import" name="wpla_enable_variation_image_import" class=" required-entry select">
								<option value="1" <?php if ( $wpl_enable_variation_image_import == '1' ): ?>selected="selected"<?php endif; ?>><?php echo __('Yes','wpla'); ?> (Default)</option>
								<option value="0" <?php if ( $wpl_enable_variation_image_import == '0' ): ?>selected="selected"<?php endif; ?>><?php echo __('No','wpla'); ?></option>
							</select>

							<label for="wpl-enable_gallery_images_import" class="text_label">
								<?php echo __('Import additional images','wpla') ?>
                                <?php wpla_tooltip('All product images are imported by default. If you get timeout errors when importing large variable products from Amazon, you might have to only import the main image or increase your <code>max_execution_time</code> PHP setting.') ?>
							</label>
							<select id="wpl-enable_gallery_images_import" name="wpla_enable_gallery_images_import" class=" required-entry select">
								<option value="1" <?php if ( $wpl_enable_gallery_images_import == '1' ): ?>selected="selected"<?php endif; ?>><?php echo __('Yes','wpla'); ?> (Default)</option>
								<option value="0" <?php if ( $wpl_enable_gallery_images_import == '0' ): ?>selected="selected"<?php endif; ?>><?php echo __('No, only import main image','wpla'); ?></option>
							</select>

							<label for="wpl-import_images_subfolder_level" class="text_label">
								<?php echo __('Create image subfolders','wpla') ?>
                                <?php wpla_tooltip('If you import a large number of products, enable this option to lower the number of images per folder.') ?>
							</label>
							<select id="wpl-import_images_subfolder_level" name="wpla_import_images_subfolder_level" class=" required-entry select">
								<option value="0" <?php if ( $wpl_import_images_subfolder_level == '0' ): ?>selected="selected"<?php endif; ?>><?php echo __('No subfolders','wpla'); ?> (Default)</option>
								<option value="1" <?php if ( $wpl_import_images_subfolder_level == '1' ): ?>selected="selected"<?php endif; ?>><?php echo __('1 level','wpla'); ?></option>
								<option value="2" <?php if ( $wpl_import_images_subfolder_level == '2' ): ?>selected="selected"<?php endif; ?>><?php echo __('2 levels','wpla'); ?></option>
							</select>

							<label for="wpl-text-import_images_basedir_name" class="text_label">
								<?php echo __('Image base folder','wpla'); ?>
                                <?php wpla_tooltip('This folder will be created in /wp-content/uploads/ and will hold images imported from Amazon.') ?>
							</label>
							<input type="text" name="wpla_import_images_basedir_name" id="wpl-text-import_images_basedir_name" value="<?php echo $wpl_import_images_basedir_name; ?>" placeholder="imported/" class="text_input" />

						</div>
					</div>

					<div class="postbox" id="ReportSettingsBox">
						<h3 class="hndle"><span><?php echo __('Reports','wpla') ?></span></h3>
						<div class="inside">

							<label for="wpl-autofetch_listing_quality_feeds" class="text_label">
								<?php echo __('Fetch listing quality data','wpla') ?>
                                <?php wpla_tooltip('Automatically request and process a daily listing quality report.') ?>
							</label>
							<select id="wpl-autofetch_listing_quality_feeds" name="wpla_autofetch_listing_quality_feeds" class=" required-entry select">
								<option value="1" <?php if ( $wpl_autofetch_listing_quality_feeds == '1' ): ?>selected="selected"<?php endif; ?>><?php echo __('Yes','wpla'); ?></option>
								<option value="0" <?php if ( $wpl_autofetch_listing_quality_feeds == '0' ): ?>selected="selected"<?php endif; ?>><?php echo __('No','wpla'); ?></option>
							</select>

							<label for="wpl-autofetch_inventory_report" class="text_label">
								<?php echo __('Process daily inventory report','wpla') ?>
                                <?php wpla_tooltip('Automatically request and process a daily inventory report and update WooCommerce products using the current import options.') ?>
							</label>
							<select id="wpl-autofetch_inventory_report" name="wpla_autofetch_inventory_report" class=" required-entry select">
								<option value="1" <?php if ( $wpl_autofetch_inventory_report == '1' ): ?>selected="selected"<?php endif; ?>><?php echo __('Yes','wpla'); ?></option>
								<option value="0" <?php if ( $wpl_autofetch_inventory_report == '0' ): ?>selected="selected"<?php endif; ?>><?php echo __('No','wpla'); ?></option>
							</select>

						</div>
					</div>

					<div class="postbox" id="OtherSettingsBox">
						<h3 class="hndle"><span><?php echo __('Misc Options','wpla') ?></span></h3>
						<div class="inside">

							<label for="wpl-product_gallery_first_image" class="text_label">
								<?php echo __('Gallery images','wpla') ?>
                                <?php wpla_tooltip('If your product gallery in WooCommerce contains the features image as the first image, you can choose to skip the first image to avoid duplicate images on Amazon.') ?>
							</label>
							<select id="wpl-product_gallery_first_image" name="wpla_product_gallery_first_image" class=" required-entry select">
								<option value=""     <?php if ( $wpl_product_gallery_first_image != 'skip' ): ?>selected="selected"<?php endif; ?>><?php echo __('Use all gallery images','wpla'); ?> (Default)</option>
								<option value="skip" <?php if ( $wpl_product_gallery_first_image == 'skip' ): ?>selected="selected"<?php endif; ?>><?php echo __('Skip first gallery image','wpla'); ?></option>
							</select>

							<label for="wpl-process_shortcodes" class="text_label">
								<?php echo __('Shortcode processing','wpla'); ?>
                                <?php wpla_tooltip('Enable this to run your product description through the usual WordPress content filters which enables you to use shortcodes in your product description on Amazon.<br>If a plugin causes trouble by adding unwanted HTML to your description on eBay, you should try the default setting "off".') ?>
							</label>
							<select id="wpl-process_shortcodes" name="wpla_process_shortcodes" class="required-entry select">
								<option value="off"          <?php if ( $wpl_process_shortcodes == 'off' ):          ?>selected="selected"<?php endif; ?>><?php echo __('Off','wpla'); ?> (Default)</option>
								<option value="the_content"  <?php if ( $wpl_process_shortcodes == 'the_content' ):  ?>selected="selected"<?php endif; ?>><?php echo __('Process shortcodes','wpla'); ?> - the_content()</option>
								<option value="do_shortcode" <?php if ( $wpl_process_shortcodes == 'do_shortcode' ): ?>selected="selected"<?php endif; ?>><?php echo __('Process shortcodes','wpla'); ?> - do_shortcode()</option>
								<option value="remove_all"   <?php if ( $wpl_process_shortcodes == 'remove_all' ):   ?>selected="selected"<?php endif; ?>><?php echo __('Remove all shortcodes from description','wpla'); ?></option>
							</select>
							<p class="desc" style="display: block;">
								<?php echo __('Enable this if you want to use or remove WordPress shortcodes in your product description.','wpla'); ?><br>
							</p>

							<label for="wpl-remove_links" class="text_label">
								<?php echo __('Link handling','wpla'); ?>
                                <?php wpla_tooltip('Should links within the product description be replaced with plain text?') ?>
							</label>
							<select id="wpl-remove_links" name="wpla_remove_links" class="required-entry select">
								<option value="default"   <?php if ( $wpl_remove_links == 'default'   ): ?>selected="selected"<?php endif; ?>><?php echo __('Remove all links from description','wpla'); ?></option>
								<option value="allow_all" <?php if ( $wpl_remove_links == 'allow_all' ): ?>selected="selected"<?php endif; ?>><?php echo __('Allow all links','wpla'); ?></option>
							</select>
							<p class="desc" style="display: block;">
								<?php echo __('Links are removed from product descriptions by default.','wpla'); ?>
							</p>

							<label for="wpl-variation_title_mode" class="text_label">
								<?php echo __('Variation title','wpla'); ?>
                                <?php wpla_tooltip('This option controls whether attribute values will show up in variation listing titles.') ?>
							</label>
							<select id="wpl-variation_title_mode" name="wpla_variation_title_mode" class="required-entry select">
								<option value="default"   <?php if ( $wpl_variation_title_mode == 'default'   ): ?>selected="selected"<?php endif; ?>><?php echo __('Default','wpla'); ?></option>
								<option value="parent"    <?php if ( $wpl_variation_title_mode == 'parent'    ): ?>selected="selected"<?php endif; ?>><?php echo __('Use parent title without attributes','wpla'); ?></option>
							</select>

							<label for="wpl-profile_editor_mode" class="text_label">
								<?php echo __('Profile editor mode','wpla'); ?>
                                <?php wpla_tooltip('Expert mode will enable you to edit quantity and image fields in your listing profile.<br><br>Please leave this option at <i>Default</i> unless told otherwise by support.') ?>
							</label>
							<select id="wpl-profile_editor_mode" name="wpla_profile_editor_mode" class="required-entry select">
								<option value="default"   <?php if ( $wpl_profile_editor_mode == 'default'   ): ?>selected="selected"<?php endif; ?>><?php echo __('Default','wpla'); ?></option>
								<option value="expert"    <?php if ( $wpl_profile_editor_mode == 'expert'    ): ?>selected="selected"<?php endif; ?>><?php echo __('Expert Mode (show all hidden fields)','wpla'); ?></option>
							</select>

							<label for="wpl-text-allowed_html_tags" class="text_label">
								<?php echo __('Allowed HTML tags','wpla'); ?>
                                <?php wpla_tooltip('These HTML tags will be allowed in the listing description sent to Amazon - all other tags will be removed.') ?>
							</label>
							<input type="text" name="wpla_allowed_html_tags" id="wpl-text-allowed_html_tags" value="<?php echo $wpl_allowed_html_tags; ?>" class="text_input" />
							<p class="desc" style="display: block;">
								Default: <code>&lt;b&gt;&lt;i&gt;</code>
							</p>

						</div>
					</div>







					<div class="postbox adv_options" id="VariationAttributesBox">
						<h3 class="hndle"><span><?php echo __('Map Variation Attributes','wpla'); ?></span></h3>
						<div class="inside">

							<table id="variation-attributes-table">
								<tr>
									<th><?php echo __('WooCommerce attribute','wpla'); ?></th>
									<th><?php echo __('Amazon attribute','wpla'); ?></th>
								</tr>
								
								<?php

									$wpl_varmap_woocom = array_keys  ( $wpl_variation_attribute_map );
									$wpl_varmap_amazon = array_values( $wpl_variation_attribute_map );

								?>

								<?php for ($i=0; $i < sizeof($wpl_variation_attribute_map); $i++) : ?> 
								<tr>
									<td>
										<select name="varmap_woocom[]" class="select">
											<option value=""      <?php if ( $wpl_varmap_woocom[$i] == ''      ): ?>selected="selected"<?php endif; ?>>-- <?php echo __('Select WooCommerce attribute','wpla'); ?> --</option>
							                <?php foreach ($wpl_available_attributes as $attribute) : ?>
												<option value="<?php echo $attribute->label ?>"   <?php if ( $wpl_varmap_woocom[$i] == $attribute->label ):   ?>selected="selected"<?php endif; ?>><?php echo $attribute->label ?></option>
							                <?php endforeach; ?>
										</select>
									</td>
									<td>
										<select name="varmap_amazon[]" class="select">
											<option value=""      				<?php if ( @$wpl_varmap_amazon[$i] == ''      				): ?>selected="selected"<?php endif; ?>>-- <?php echo __('Select Amazon attribute','wpla'); ?> --</option>
											<option value="Size"  				<?php if ( @$wpl_varmap_amazon[$i] == 'Size'  				): ?>selected="selected"<?php endif; ?>><?php echo 'Size' ?></option>
											<option value="Color" 				<?php if ( @$wpl_varmap_amazon[$i] == 'Color' 				): ?>selected="selected"<?php endif; ?>><?php echo 'Color' ?></option>
											<option value="Material" 			<?php if ( @$wpl_varmap_amazon[$i] == 'Material' 			): ?>selected="selected"<?php endif; ?>><?php echo 'Material' ?></option>
											<option value="Flavor"				<?php if ( @$wpl_varmap_amazon[$i] == 'Flavor' 				): ?>selected="selected"<?php endif; ?>><?php echo 'Flavor' ?></option>
											<option value="Scent"				<?php if ( @$wpl_varmap_amazon[$i] == 'Scent' 				): ?>selected="selected"<?php endif; ?>><?php echo 'Scent' ?></option>
											<option value="DisplayWidth" 		<?php if ( @$wpl_varmap_amazon[$i] == 'DisplayWidth' 		): ?>selected="selected"<?php endif; ?>><?php echo 'DisplayWidth' ?></option>
											<option value="DisplayHeight" 		<?php if ( @$wpl_varmap_amazon[$i] == 'DisplayHeight' 		): ?>selected="selected"<?php endif; ?>><?php echo 'DisplayHeight' ?></option>
											<option value="DisplayLength" 		<?php if ( @$wpl_varmap_amazon[$i] == 'DisplayLength' 		): ?>selected="selected"<?php endif; ?>><?php echo 'DisplayLength' ?></option>
											<option value="DisplayWeight" 		<?php if ( @$wpl_varmap_amazon[$i] == 'DisplayWeight' 		): ?>selected="selected"<?php endif; ?>><?php echo 'DisplayWeight' ?></option>
											<option value="ItemPackageQuantity" <?php if ( @$wpl_varmap_amazon[$i] == 'ItemPackageQuantity' ): ?>selected="selected"<?php endif; ?>><?php echo 'ItemPackageQuantity' ?></option>
										</select>
									</td>
								</tr>
								<?php endfor; ?>
								<tr>
									<td>
										<select name="varmap_woocom[]" class="select">
											<option value=""      <?php if ( @$wpl_varmap_woocom[$i] == ''      ): ?>selected="selected"<?php endif; ?>>-- <?php echo __('Select WooCommerce attribute','wpla'); ?> --</option>
							                <?php foreach ($wpl_available_attributes as $attribute) : ?>
												<option value="<?php echo $attribute->label ?>"   <?php if ( @$wpl_varmap_woocom[$i] == $attribute->label ):   ?>selected="selected"<?php endif; ?>><?php echo $attribute->label ?></option>
							                <?php endforeach; ?>
										</select>
									</td>
									<td>
										<select name="varmap_amazon[]" class="select">
											<option value=""      				<?php if ( @$wpl_varmap_amazon[$i] == ''      				): ?>selected="selected"<?php endif; ?>>-- <?php echo __('Select Amazon attribute','wpla'); ?> --</option>
											<option value="Size"  				<?php if ( @$wpl_varmap_amazon[$i] == 'Size'  				): ?>selected="selected"<?php endif; ?>><?php echo 'Size' ?></option>
											<option value="Color" 				<?php if ( @$wpl_varmap_amazon[$i] == 'Color' 				): ?>selected="selected"<?php endif; ?>><?php echo 'Color' ?></option>
											<option value="Material" 			<?php if ( @$wpl_varmap_amazon[$i] == 'Material' 			): ?>selected="selected"<?php endif; ?>><?php echo 'Material' ?></option>
											<option value="Flavor"				<?php if ( @$wpl_varmap_amazon[$i] == 'Flavor' 				): ?>selected="selected"<?php endif; ?>><?php echo 'Flavor' ?></option>
											<option value="Scent"				<?php if ( @$wpl_varmap_amazon[$i] == 'Scent' 				): ?>selected="selected"<?php endif; ?>><?php echo 'Scent' ?></option>
											<option value="DisplayWidth" 		<?php if ( @$wpl_varmap_amazon[$i] == 'DisplayWidth' 		): ?>selected="selected"<?php endif; ?>><?php echo 'DisplayWidth' ?></option>
											<option value="DisplayHeight" 		<?php if ( @$wpl_varmap_amazon[$i] == 'DisplayHeight' 		): ?>selected="selected"<?php endif; ?>><?php echo 'DisplayHeight' ?></option>
											<option value="DisplayLength" 		<?php if ( @$wpl_varmap_amazon[$i] == 'DisplayLength' 		): ?>selected="selected"<?php endif; ?>><?php echo 'DisplayLength' ?></option>
											<option value="DisplayWeight" 		<?php if ( @$wpl_varmap_amazon[$i] == 'DisplayWeight' 		): ?>selected="selected"<?php endif; ?>><?php echo 'DisplayWeight' ?></option>
											<option value="ItemPackageQuantity" <?php if ( @$wpl_varmap_amazon[$i] == 'ItemPackageQuantity' ): ?>selected="selected"<?php endif; ?>><?php echo 'ItemPackageQuantity' ?></option>
										</select>
									</td>
								</tr>
							</table>

							<p class="x-desc" style="display: block;">
								<?php echo __('If you are using non-standard attributes for variations, you can map them to standard attributes supported by Amazon.','wpla'); ?>
							</p>

						</div>
					</div> <!-- postbox -->



					<div class="postbox adv_options" id="MergeVariationAttributesBox">
						<h3 class="hndle"><span><?php echo __('Merge Variation Attributes','wpla'); ?></span></h3>
						<div class="inside">

							<table id="variation-merger-table">
								<tr>
									<th><?php echo __('1st WooCommerce attribute','wpla'); ?></th>
									<th>&nbsp;</th>
									<th><?php echo __('2nd WooCommerce attribute','wpla'); ?></th>
									<th>&nbsp;</th>
									<th><?php echo __('Amazon attribute','wpla'); ?></th>
								</tr>
								
								<?php
									// $wpl_variation_merger_map = array();

									// rebuild separate arrays
									$wpl_varmerge_woo1 = array();
									$wpl_varmerge_woo2 = array();
									$wpl_varmerge_amaz = array();
									$wpl_varmerge_glue = array();
									foreach ($wpl_variation_merger_map as $key => $row) {
										$wpl_varmerge_woo1[] = $row['woo1'];
										$wpl_varmerge_woo2[] = $row['woo2'];
										$wpl_varmerge_amaz[] = $row['amaz'];
										$wpl_varmerge_glue[] = $row['glue'];
									}

								?>

								<?php for ($i=0; $i < sizeof($wpl_variation_merger_map); $i++) : ?> 
								<tr>
									<td>
										<select name="varmerge_woo1[]" class="select">
											<option value=""      <?php if ( @$wpl_varmerge_woo1[$i] == ''      ): ?>selected="selected"<?php endif; ?>>-- <?php echo __('Select WooCommerce attribute','wpla'); ?> --</option>
							                <?php foreach ($wpl_available_attributes as $attribute) : ?>
												<option value="<?php echo $attribute->label ?>"   <?php if ( @$wpl_varmerge_woo1[$i] == $attribute->label ):   ?>selected="selected"<?php endif; ?>><?php echo $attribute->label ?></option>
							                <?php endforeach; ?>
										</select>
									</td>
									<td style="width:3em;">
										<input type="text" name="varmerge_glue[]" value="<?php echo @$wpl_varmerge_glue[$i]; ?>" class="text_input" />
									</td>
									<td>
										<select name="varmerge_woo2[]" class="select">
											<option value=""      <?php if ( @$wpl_varmerge_woo2[$i] == ''      ): ?>selected="selected"<?php endif; ?>>-- <?php echo __('Select WooCommerce attribute','wpla'); ?> --</option>
							                <?php foreach ($wpl_available_attributes as $attribute) : ?>
												<option value="<?php echo $attribute->label ?>"   <?php if ( @$wpl_varmerge_woo2[$i] == $attribute->label ):   ?>selected="selected"<?php endif; ?>><?php echo $attribute->label ?></option>
							                <?php endforeach; ?>
										</select>
									</td>
									<td>&nbsp;</td>
									<td>
										<select name="varmerge_amaz[]" class="select">
											<option value=""      <?php if ( @$wpl_varmerge_amaz[$i] == ''      ): ?>selected="selected"<?php endif; ?>>-- <?php echo __('Select Amazon attribute','wpla'); ?> --</option>
											<option value="Size"  <?php if ( @$wpl_varmerge_amaz[$i] == 'Size'  ): ?>selected="selected"<?php endif; ?>><?php echo 'Size' ?></option>
											<option value="Color" <?php if ( @$wpl_varmerge_amaz[$i] == 'Color' ): ?>selected="selected"<?php endif; ?>><?php echo 'Color' ?></option>
										</select>
									</td>
								</tr>
								<?php endfor; ?>
								<tr>
									<td>
										<select name="varmerge_woo1[]" class="select">
											<option value=""      <?php if ( @$wpl_varmerge_woo1[$i] == ''      ): ?>selected="selected"<?php endif; ?>>-- <?php echo __('Select WooCommerce attribute','wpla'); ?> --</option>
							                <?php foreach ($wpl_available_attributes as $attribute) : ?>
												<option value="<?php echo $attribute->label ?>"   <?php if ( @$wpl_varmerge_woo1[$i] == $attribute->label ):   ?>selected="selected"<?php endif; ?>><?php echo $attribute->label ?></option>
							                <?php endforeach; ?>
										</select>
									</td>
									<td style="width:3em;">
										<input type="text" name="varmerge_glue[]" value="<?php echo @$wpl_varmerge_glue[$i]; ?>" class="text_input" />
									</td>
									<td>
										<select name="varmerge_woo2[]" class="select">
											<option value=""      <?php if ( @$wpl_varmerge_woo2[$i] == ''      ): ?>selected="selected"<?php endif; ?>>-- <?php echo __('Select WooCommerce attribute','wpla'); ?> --</option>
							                <?php foreach ($wpl_available_attributes as $attribute) : ?>
												<option value="<?php echo $attribute->label ?>"   <?php if ( @$wpl_varmerge_woo2[$i] == $attribute->label ):   ?>selected="selected"<?php endif; ?>><?php echo $attribute->label ?></option>
							                <?php endforeach; ?>
										</select>
									</td>
									<td>&nbsp;</td>
									<td>
										<select name="varmerge_amaz[]" class="select">
											<option value=""      <?php if ( @$wpl_varmerge_amaz[$i] == ''      ): ?>selected="selected"<?php endif; ?>>-- <?php echo __('Select Amazon attribute','wpla'); ?> --</option>
											<option value="Size"  <?php if ( @$wpl_varmerge_amaz[$i] == 'Size'  ): ?>selected="selected"<?php endif; ?>><?php echo 'Size' ?></option>
											<option value="Color" <?php if ( @$wpl_varmerge_amaz[$i] == 'Color' ): ?>selected="selected"<?php endif; ?>><?php echo 'Color' ?></option>
										</select>
									</td>
								</tr>
							</table>

							<p class="x-desc" style="display: block;">
								<?php echo __('Example: You sell blankets with Length and Width which need to be merged to Size on Amazon.','wpla'); ?>
							</p>

						</div>
					</div> <!-- postbox -->




					<div class="postbox adv_options" id="CustomShortcodesBox">
						<h3 class="hndle"><span><?php echo __('Custom Shortcodes','wpla'); ?></span></h3>
						<div class="inside">

							<table id="custom-shortcodes-table">
								<tr>
									<th><?php echo __('Shortcode Title','wpla'); ?></th>
									<th><?php echo __('Shortcode Content','wpla'); ?></th>
								</tr>
								
								<?php
									// add empty record
									$wpl_custom_shortcodes[] = array(
										'title'   => '',
										'slug'    => '',
										'content' => '',
									);

								?>

								<?php foreach ( $wpl_custom_shortcodes as $key => $shortcode ) : ?> 
								<tr>
									<td>
										<input type="text" name="shortcode_title[]" value="<?php echo $shortcode['title']; ?>" class="text_input" placeholder="My shortcode" />
										<br>
										<input type="text" name="shortcode_slug[]" value="<?php echo $shortcode['slug']; ?>" class="text_input" placeholder="my-shortcode"/>
									</td>
									<td>
										<textarea name="shortcode_content[]" placeholder="Enter your text or copy and paste some HTML"><?php echo $shortcode['content']; ?></textarea>
									</td>
								</tr>
								<?php endforeach; ?>

							</table>

							<p class="x-desc" style="display: block;">
								<?php echo __('Create custom profile shortcodes from text or HTML snippets.','wpla'); ?>
							</p>

						</div>
					</div> <!-- postbox -->


					<div class="postbox adv_options" id="CustomVariationMetaBox">
						<h3 class="hndle"><span><?php echo __('Custom Variation Meta','wpla'); ?></span></h3>
						<div class="inside">

							<table id="custom-variation-fields-table">
								<tr>
									<th><?php echo __('Field Label','wpla'); ?></th>
									<th><?php echo __('Meta Key','wpla'); ?></th>
								</tr>
								
								<?php
									// add empty record
									$wpl_variation_meta_fields[] = array(
										'label'  => '',
										'key'    => '',
									);

								?>

								<?php foreach ( $wpl_variation_meta_fields as $key => $varmeta ) : ?> 
								<tr>
									<td>
										<input type="text" name="varmeta_label[]" value="<?php echo $varmeta['label']; ?>" class="text_input" placeholder="My custom field" />
									</td>
									<td>
										<input type="text" name="varmeta_key[]" value="<?php echo $varmeta['key']; ?>" class="text_input" placeholder="my-custom-field"/>
									</td>
								</tr>
								<?php endforeach; ?>

							</table>

							<p class="x-desc" style="display: block;">
								<?php echo __('Add custom meta fields which will be editable for each variation separately.','wpla'); ?>
							</p>
							<p class="x-desc" style="display: block;">
								<?php echo __('These meta fields will be available as product properties in the profile editor.','wpla'); ?>
							</p>

						</div>
					</div> <!-- postbox -->




				<?php // if ( ( is_multisite() ) && ( is_main_site() ) ) : ?>
				<?php if ( false ) : ?>
				<p>
					<b>Warning:</b> Deactivating WP-Lister on a multisite network will remove all settings and data from all sites.
				</p>
				<?php endif; ?>


				</div> <!-- .meta-box-sortables -->
			</div> <!-- #postbox-container-1 -->



		</div> <!-- #post-body -->
		<br class="clear">
	</div> <!-- #poststuff -->

	</form>


</div>
