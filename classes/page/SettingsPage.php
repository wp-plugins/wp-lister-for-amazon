<?php
/**
 * WPLA_SettingsPage class
 * 
 */

class WPLA_SettingsPage extends WPLA_Page {

	const slug = 'settings';

	public function onWpInit() {
		// parent::onWpInit();

		// custom (raw) screen options for settings page
		add_screen_options_panel('wpla_setting_options', '', array( &$this, 'renderSettingsOptions'), 'wp-lister_page_wpla-settings' );

		// Add custom screen options
		$load_action = "load-".$this->main_admin_menu_slug."_page_wpla-".self::slug;
		add_action( $load_action, array( &$this, 'addScreenOptions' ) );

		// network admin page
		add_action( 'network_admin_menu', array( &$this, 'onWpAdminMenu' ) ); 

	}

	public function onWpAdminMenu() {
		parent::onWpAdminMenu();

		add_submenu_page( self::ParentMenuId, $this->getSubmenuPageTitle( 'Settings' ), __('Settings','wpla'), 
						  'manage_amazon_options', $this->getSubmenuId( 'settings' ), array( &$this, 'onDisplaySettingsPage' ) );
	}

	function addScreenOptions() {
		// load styles and scripts for this page only
		add_action( 'admin_print_styles', array( &$this, 'onWpPrintStyles' ) );
		// add_action( 'admin_enqueue_scripts', array( &$this, 'onWpEnqueueScripts' ) );		
		// $this->categoriesMapTable = new CategoriesMapTable();
		add_thickbox();
	}
	
	public function handleSubmit() {
        // WPLA()->logger->debug("handleSubmit()");

		// save settings
		if ( $this->requestAction() == 'save_wpla_settings' ) {
			$this->saveSettings();
		}

		// save advanced settings
		if ( $this->requestAction() == 'save_wpla_advanced_settings' ) {
			$this->saveAdvancedSettings();
		}

		// save feed template / browse tree selection
		if ( $this->requestAction() == 'save_wpla_tpl_btg_settings' ) {
			$this->saveCategoriesSettings();
		}

		// remove feed template
		if ( $this->requestAction() == 'wpla_remove_tpl' ) {
			$this->removeCategoryFeed();
		}

		// save developer settings
		if ( $this->requestAction() == 'save_wpla_devsettings' ) {
			$this->saveDeveloperSettings();
		}

		// save license
		if ( $this->requestAction() == 'save_wpla_license' ) {
			$this->saveLicenseSettings();
		}

		// check license status
		if ( $this->requestAction() == 'wpla_check_license_status' ) {
			$this->checkLicenseStatus();
		}

		// force wp update check
		if ( $this->requestAction() == 'wpla_force_update_check') {				

			$update = $this->check_for_new_version();

			if ( $update && is_object( $update ) ) {

				if ( version_compare( $update->new_version, WPLA_VERSION ) > 0 ) {

					$this->showMessage( 
						'<big>'. __('Update available','wpla') . ' ' . $update->title . ' ' . $update->new_version . '</big><br><br>'
						. ( isset( $update->upgrade_notice ) ? $update->upgrade_notice . '<br><br>' : '' )
						. __('Please visit your WordPress Updates to install the new version.','wpla') . '<br><br>'
						. '<a href="update-core.php" class="button-primary">'.__('view updates','wpla') . '</a>'
					);

				} else {
					$this->showMessage( __('You are using the latest version of WP-Lister. That\'s great!','wpla') );
				}

			} else {

				$this->showMessage( 
					'<big>'. __('Check for updates was initiated.','wpla') . '</big><br><br>'
					. __('You can visit your WordPress Updates now.','wpla') . '<br><br>'
					. __('Since the updater runs in the background, it might take a little while before new updates appear.','wpla') . '<br><br>'
					. '<a href="update-core.php" class="button-primary">'.__('view updates','wpla') . '</a>'
				);

			}
            // delete_site_transient('update_plugins');
            // delete_transient('wpla_update_check_cache');
            // delete_transient('wpla_update_info_cache');

		}

	} // handleSubmit()
	

	public function onDisplaySettingsPage() {
		$this->check_wplister_setup('settings');

        $default_tab = is_network_admin() ? 'license' : 'settings';
        $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : $default_tab;
        if ( 'categories' == $active_tab ) return $this->displayCategoriesPage();
        if ( 'developer'  == $active_tab ) return $this->displayDeveloperPage();
        if ( 'advanced'   == $active_tab ) return $this->displayAdvancedSettingsPage();
        if ( 'license'    == $active_tab ) return $this->displayLicensePage();
        if ( 'accounts'   == $active_tab ) return WPLA()->pages['accounts']->displayAccountsPage();

        // display general settings page by default
        $this->displayGeneralSettingsPage();
	}


	public function displayGeneralSettingsPage() {

		$aData = array(
			'plugin_url'				=> self::$PLUGIN_URL,
			'message'					=> $this->message,

			// 'amazon_markets'			=> WPLA_AmazonMarket::getAll(),

			'option_cron_schedule'		=> self::getOption( 'cron_schedule' ),
			'option_sync_inventory'     => self::getOption( 'sync_inventory' ),


			'fba_enabled'    				  => self::getOption( 'fba_enabled' ),
			'fba_enable_fallback' 		      => self::getOption( 'fba_enable_fallback' ),
			'fba_only_mode' 		          => self::getOption( 'fba_only_mode' ),
			'fba_fulfillment_center_id' 	  => self::getOption( 'fba_fulfillment_center_id', 'AMAZON_NA' ),
			'fba_report_schedule' 	  		  => self::getOption( 'fba_report_schedule', 'daily' ),

	
			'settings_url'				=> 'admin.php?page='.self::ParentMenuId.'-settings',
			'form_action'				=> 'admin.php?page='.self::ParentMenuId.'-settings',
		);
		$this->display( 'settings_page', $aData );
	}

	public function displayCategoriesPage() {

		$templates = WPLA_AmazonFeedTemplate::getAll();
		$active_templates = array();
		foreach ($templates as $template) {
			$tpl_name = $template->name == 'Offer' ? 'ListingLoader' : $template->name;
			$active_templates[] = $template->site_id.$tpl_name;
		}

		$aData = array(
			'plugin_url'				=> self::$PLUGIN_URL,
			'message'					=> $this->message,

			'file_index'				=> WPLA_FeedTemplateIndex::get_file_index(),
			'active_templates'          => $active_templates,
			'installed_templates'       => $templates,

			'settings_url'				=> 'admin.php?page='.self::ParentMenuId.'-settings',
			'form_action'				=> 'admin.php?page='.self::ParentMenuId.'-settings'.'&tab=categories'
		);
		$this->display( 'settings_tpl_btg', $aData );
	}

	public function displayAdvancedSettingsPage() {
        $wp_roles = new WP_Roles();

        // check import folder
		$upload_dir   = wp_upload_dir();
        $basedir_name = self::getOption( 'import_images_basedir_name', 'imported/' );
		$images_dir   = $upload_dir['basedir'].'/'.$basedir_name;
		if ( ! is_dir($images_dir) ) mkdir( $images_dir );
		if ( ! is_dir($images_dir) ) {
			wpla_show_message('The folder for imported images <code>'.$images_dir.'</code> could not be created. Please check your folder permissions.','error');
		}


		$aData = array(
			'plugin_url'						=> self::$PLUGIN_URL,
			'message'							=> $this->message,

			'dismiss_imported_products_notice'	=> self::getOption( 'dismiss_imported_products_notice' ),
			'enable_missing_details_warning'  	=> self::getOption( 'enable_missing_details_warning' ),
			'enable_custom_product_prices'  	=> self::getOption( 'enable_custom_product_prices', 1 ),
			'enable_minmax_product_prices'  	=> self::getOption( 'enable_minmax_product_prices', 0 ),
			'enable_item_condition_fields'  	=> self::getOption( 'enable_item_condition_fields', 2 ),
			'enable_thumbs_column'  			=> self::getOption( 'enable_thumbs_column' ),
			'autofetch_listing_quality_feeds'  	=> self::getOption( 'autofetch_listing_quality_feeds', 1 ),
			'autofetch_inventory_report'  		=> self::getOption( 'autofetch_inventory_report', 0 ),
			'product_gallery_first_image'  		=> self::getOption( 'product_gallery_first_image' ),
			'product_gallery_fallback'  		=> self::getOption( 'product_gallery_fallback', 'none' ),
			'pricing_info_expiry_time'  		=> self::getOption( 'pricing_info_expiry_time', 24 ),
			'repricing_use_lowest_offer'  		=> self::getOption( 'repricing_use_lowest_offer', 0 ),
			'repricing_margin'  				=> self::getOption( 'repricing_margin', '' ),
			'import_parent_category_id'  		=> self::getOption( 'import_parent_category_id', '' ),
			'enable_variation_image_import'  	=> self::getOption( 'enable_variation_image_import', 1 ),
			'enable_gallery_images_import'  	=> self::getOption( 'enable_gallery_images_import', 1 ),
			'import_images_subfolder_level'  	=> self::getOption( 'import_images_subfolder_level', 0 ),
			'import_images_basedir_name'  	    => self::getOption( 'import_images_basedir_name', 'imported/' ),

			'default_matcher_selection'  	  	=> self::getOption( 'default_matcher_selection', 'title' ),
			'available_attributes' 			    => WPLA_ProductWrapper::getAttributeTaxonomies(),
			'variation_attribute_map'  	  		=> self::getOption( 'variation_attribute_map', array() ),
			'variation_merger_map'  	  		=> self::getOption( 'variation_merger_map', array() ),
			'custom_shortcodes'  	  			=> self::getOption( 'custom_shortcodes', array() ),
			'variation_meta_fields'  			=> self::getOption( 'variation_meta_fields', array() ),

			// 'hide_dupe_msg'					=> self::getOption( 'hide_dupe_msg' ),
			'allowed_html_tags'					=> self::getOption( 'allowed_html_tags', '<b><i>' ),
			'process_shortcodes'				=> self::getOption( 'process_shortcodes', 'off' ),
			'remove_links'						=> self::getOption( 'remove_links', 'default' ),
			'variation_title_mode'				=> self::getOption( 'variation_title_mode', 'default' ),
			'profile_editor_mode'				=> self::getOption( 'profile_editor_mode', 'default' ),
			'option_uninstall'					=> self::getOption( 'uninstall' ),

			'available_roles'                   => $wp_roles->role_names,
			'wp_roles'                          => $wp_roles->roles,

			'settings_url'						=> 'admin.php?page='.self::ParentMenuId.'-settings',
			'form_action'						=> 'admin.php?page='.self::ParentMenuId.'-settings'.'&tab=advanced'
		);
		$this->display( 'settings_advanced', $aData );
	}

	public function displayDeveloperPage() {

		$aData = array(
			'plugin_url'				=> self::$PLUGIN_URL,
			'message'					=> $this->message,

			'ajax_error_handling'		=> self::getOption( 'ajax_error_handling', 'halt' ),
			'disable_variations'		=> self::getOption( 'disable_variations', 0 ),
			'max_feed_size'			    => self::getOption( 'max_feed_size', 1000 ),
			'feed_encoding'			    => self::getOption( 'feed_encoding' ),
			'feed_currency_format'	    => self::getOption( 'feed_currency_format' ),
			'log_record_limit'			=> self::getOption( 'log_record_limit', 4096 ),
			'log_days_limit'			=> self::getOption( 'log_days_limit', 30 ),
			'feeds_days_limit'			=> self::getOption( 'feeds_days_limit', 90 ),
			'reports_days_limit'		=> self::getOption( 'reports_days_limit', 90 ),
			'orders_days_limit'			=> self::getOption( 'orders_days_limit', '' ),
			'text_log_level'			=> self::getOption( 'log_level' ),
			'option_log_to_db'			=> self::getOption( 'log_to_db' ),
			'show_browse_node_ids'		=> self::getOption( 'show_browse_node_ids' ),

			'settings_url'				=> 'admin.php?page='.self::ParentMenuId.'-settings',
			'form_action'				=> 'admin.php?page='.self::ParentMenuId.'-settings'.'&tab=developer'
		);
		$this->display( 'settings_dev', $aData );
	}

	public function displayLicensePage() {
	}





	protected function saveSettings() {

		// TODO: check nonce
		if ( isset( $_POST['wpla_option_cron_schedule'] ) ) {

			self::updateOption( 'cron_schedule',					$this->getValueFromPost( 'option_cron_schedule' ) );
			self::updateOption( 'sync_inventory',					$this->getValueFromPost( 'option_sync_inventory' ) );
			self::updateOption( 'create_orders',					$this->getValueFromPost( 'option_create_orders' ) );
			self::updateOption( 'create_customers',					$this->getValueFromPost( 'option_create_customers' ) );
			self::updateOption( 'new_order_status',					$this->getValueFromPost( 'option_new_order_status' ) );
			self::updateOption( 'shipped_order_status',				$this->getValueFromPost( 'option_shipped_order_status' ) );

			self::updateOption( 'fetch_orders_filter', 		        $this->getValueFromPost( 'fetch_orders_filter' ) );	
			self::updateOption( 'disable_new_order_emails', 		$this->getValueFromPost( 'disable_new_order_emails' ) );	
			self::updateOption( 'disable_processing_order_emails', 	$this->getValueFromPost( 'disable_processing_order_emails' ) );
			self::updateOption( 'disable_completed_order_emails', 	$this->getValueFromPost( 'disable_completed_order_emails' ) );
			self::updateOption( 'disable_changed_order_emails', 	$this->getValueFromPost( 'disable_changed_order_emails' ) );
			self::updateOption( 'disable_new_account_emails', 		$this->getValueFromPost( 'disable_new_account_emails' ) );
			self::updateOption( 'create_orders_without_email', 		$this->getValueFromPost( 'create_orders_without_email' ) );
			self::updateOption( 'auto_complete_sales', 				$this->getValueFromPost( 'auto_complete_sales' ) );
			self::updateOption( 'default_shipping_provider', 		$this->getValueFromPost( 'default_shipping_provider' ) );
			self::updateOption( 'default_shipping_service_name', 	$this->getValueFromPost( 'default_shipping_service_name' ) );
			self::updateOption( 'orders_tax_rate_id', 				$this->getValueFromPost( 'orders_tax_rate_id' ) );
			self::updateOption( 'orders_fixed_vat_rate', 			$this->getValueFromPost( 'orders_fixed_vat_rate' ) );
			self::updateOption( 'orders_default_payment_title', 	$this->getValueFromPost( 'orders_default_payment_title' ) );
			self::updateOption( 'fba_enabled', 						$this->getValueFromPost( 'fba_enabled' ) );
			self::updateOption( 'fba_autosubmit_orders', 			$this->getValueFromPost( 'fba_autosubmit_orders' ) );
			self::updateOption( 'fba_enable_fallback', 				$this->getValueFromPost( 'fba_enable_fallback' ) );
			self::updateOption( 'fba_only_mode', 					$this->getValueFromPost( 'fba_only_mode' ) );
			self::updateOption( 'fba_default_delivery_sla', 		$this->getValueFromPost( 'fba_default_delivery_sla' ) );
			self::updateOption( 'fba_default_order_comment', 		$this->getValueFromPost( 'fba_default_order_comment' ) );
			self::updateOption( 'fba_default_notification', 		$this->getValueFromPost( 'fba_default_notification' ) );
			self::updateOption( 'fba_fulfillment_center_id', 		$this->getValueFromPost( 'fba_fulfillment_center_id' ) );
			self::updateOption( 'fba_report_schedule', 				$this->getValueFromPost( 'fba_report_schedule' ) );

			$this->handleCronSettings( $this->getValueFromPost( 'option_cron_schedule' ) );
			$this->handleFbaCronSettings( $this->getValueFromPost( 'fba_report_schedule' ) );
			$this->showMessage( __('Settings saved.','wpla') );
		}
	}

	protected function saveCategoriesSettings() {

		// TODO: check nonce
		// if ( isset( $_POST['wpla_option_uninstall'] ) ) {
			// echo "<pre>";print_r($_POST);echo"</pre>";#die();

			foreach ( $_POST as $key => $value ) {

				// parse key
				if ( substr( $key, 0, 8 ) != 'wpla_cat' ) continue;
				list( $dummy, $site_code, $category ) = explode('-', $key );

				$helper = new WPLA_FeedTemplateHelper();
				$filecount = $helper->importTemplatesForCategory( $category, $site_code );
				// $this->showMessage('Feed data for '.$category.' ('.$site_code.') was refreshed - '.$filecount.' files were updated.');
				$this->showMessage('Feed data for '.$category.' ('.$site_code.') was refreshed.');

			}

			$this->showMessage( __('Selected categories were updated.','wpla') );
		// }
	}

	protected function removeCategoryFeed() {
		$tpl_id = $_GET['tpl_id'];

		// TODO: check nonce
		if ( ! $tpl_id ) return;

		$helper = new WPLA_FeedTemplateHelper();
		$helper->removeFeedTemplate( $tpl_id );

		$this->showMessage( __('Selected feed template was removed.','wpla') );
	}


	protected function saveAdvancedSettings() {

		// TODO: check nonce
		if ( isset( $_POST['wpla_option_uninstall'] ) ) {

			// self::updateOption( 'process_shortcodes', 	$this->getValueFromPost( 'process_shortcodes' ) );
			// self::updateOption( 'remove_links',     	$this->getValueFromPost( 'remove_links' ) );
			// self::updateOption( 'default_image_size',   $this->getValueFromPost( 'default_image_size' ) );
			// self::updateOption( 'hide_dupe_msg',    	$this->getValueFromPost( 'hide_dupe_msg' ) );

			self::updateOption( 'default_matcher_selection', 		$this->getValueFromPost( 'default_matcher_selection' ) );
			self::updateOption( 'dismiss_imported_products_notice', $this->getValueFromPost( 'dismiss_imported_products_notice' ) );
			self::updateOption( 'enable_missing_details_warning', 	$this->getValueFromPost( 'enable_missing_details_warning' ) );
			self::updateOption( 'enable_custom_product_prices', 	$this->getValueFromPost( 'enable_custom_product_prices' ) );
			self::updateOption( 'enable_minmax_product_prices', 	$this->getValueFromPost( 'enable_minmax_product_prices' ) );
			self::updateOption( 'enable_item_condition_fields', 	$this->getValueFromPost( 'enable_item_condition_fields' ) );
			self::updateOption( 'enable_thumbs_column', 			$this->getValueFromPost( 'enable_thumbs_column' ) );
			self::updateOption( 'autofetch_listing_quality_feeds', 	$this->getValueFromPost( 'autofetch_listing_quality_feeds' ) );
			self::updateOption( 'autofetch_inventory_report', 		$this->getValueFromPost( 'autofetch_inventory_report' ) );
			self::updateOption( 'product_gallery_first_image', 		$this->getValueFromPost( 'product_gallery_first_image' ) );
			self::updateOption( 'product_gallery_fallback', 		$this->getValueFromPost( 'product_gallery_fallback' ) );
			self::updateOption( 'pricing_info_expiry_time', 		$this->getValueFromPost( 'pricing_info_expiry_time' ) );
			self::updateOption( 'enable_auto_repricing', 			$this->getValueFromPost( 'enable_auto_repricing' ) );
			self::updateOption( 'repricing_use_lowest_offer', 		$this->getValueFromPost( 'repricing_use_lowest_offer' ) );
			self::updateOption( 'repricing_margin', 	            $this->getValueFromPost( 'repricing_margin' ) );
			self::updateOption( 'import_parent_category_id', 		$this->getValueFromPost( 'import_parent_category_id' ) );
			self::updateOption( 'enable_variation_image_import', 	$this->getValueFromPost( 'enable_variation_image_import' ) );
			self::updateOption( 'enable_gallery_images_import', 	$this->getValueFromPost( 'enable_gallery_images_import' ) );
			self::updateOption( 'import_images_subfolder_level', 	$this->getValueFromPost( 'import_images_subfolder_level' ) );
			self::updateOption( 'import_images_basedir_name', 		trailingslashit( $this->getValueFromPost( 'import_images_basedir_name' ) ) );

			self::updateOption( 'uninstall',						$this->getValueFromPost( 'option_uninstall' ) );
			self::updateOption( 'allowed_html_tags',				$this->getValueFromPost( 'allowed_html_tags' ) );
			self::updateOption( 'process_shortcodes',				$this->getValueFromPost( 'process_shortcodes' ) );
			self::updateOption( 'remove_links',						$this->getValueFromPost( 'remove_links' ) );
			self::updateOption( 'variation_title_mode',				$this->getValueFromPost( 'variation_title_mode' ) );
			self::updateOption( 'profile_editor_mode',				$this->getValueFromPost( 'profile_editor_mode' ) );

			$this->saveVariationAttributeMap();
			$this->saveVariationMergerMap();
			$this->saveCustomShortcodes();
			$this->saveCustomVariationMetaFields();
			$this->savePermissions();

			$this->showMessage( __('Settings saved.','wpla') );
		}
	}

	protected function savePermissions() {

		// don't update capabilities when options are disabled
		if ( ! apply_filters( 'wpla_enable_capabilities_options', true ) ) return;

    	$wp_roles = new WP_Roles();
    	$available_roles = $wp_roles->role_names;

    	// echo "<pre>";print_r($wp_roles);echo"</pre>";die();

		$wpl_caps = array(
			'manage_amazon_listings'  => __('Manage Amazon Listings','wpla'),
			'manage_amazon_options'   => __('Manage Amazon Settings','wpla'),
			// 'prepare_amazon_listings' => __('Prepare Listings','wpla'),
			// 'publish_amazon_listings' => __('Publish Listings','wpla'),
		);

		// echo "<pre>";print_r($_POST['wpl_permissions']);echo"</pre>";die();
		$permissions = $_POST['wpla_permissions'];

		foreach ( $available_roles as $role => $role_name ) {

			// admin permissions can't be modified
			if ( $role == 'administrator' ) continue;

			// get the the role object
			$role_object = get_role( $role );

			foreach ( $wpl_caps as $capability_name => $capability_title ) {

				if ( isset( $permissions[ $role ][ $capability_name ] ) ) {

					// add capability to this role
					$role_object->add_cap( $capability_name );

				} else {

					// remove capability from this role
					$role_object->remove_cap( $capability_name );

				}
			
			}

		}

	} // savePermissions()

	protected function saveCustomShortcodes() {

		$shortcode_slug    = $_REQUEST['shortcode_slug'];
		$shortcode_title   = $_REQUEST['shortcode_title'];
		$shortcode_content = $_REQUEST['shortcode_content'];

		$custom_shortcodes = array();
		for ($i=0; $i < sizeof($shortcode_slug); $i++) { 
			$key     = $shortcode_slug[$i];
			$title   = $shortcode_title[$i];
			$content = $shortcode_content[$i];
			if ( $key && $title ) {
				$custom_shortcodes[ $key ] = array(
					'title'   => $title,
					'slug'    => $key,
					'content' => $content,
				);
			}
		}

		self::updateOption( 'custom_shortcodes', $custom_shortcodes );
	}

	protected function saveCustomVariationMetaFields() {

		$varmeta_key    = $_REQUEST['varmeta_key'];
		$varmeta_label  = $_REQUEST['varmeta_label'];

		$variation_meta_fields = array();
		for ($i=0; $i < sizeof($varmeta_key); $i++) { 
			$key     = sanitize_key( $varmeta_key[$i] );
			$label   = $varmeta_label[$i];
			if ( $key && $label ) {
				$variation_meta_fields[ $key ] = array(
					'label'  => $label,
					'key'    => $key,
				);
			}
		}

		self::updateOption( 'variation_meta_fields', $variation_meta_fields );
	}

	protected function saveVariationAttributeMap() {

		$varmap_woocom = $_REQUEST['varmap_woocom'];
		$varmap_amazon = $_REQUEST['varmap_amazon'];

		$variation_attribute_map = array();
		for ($i=0; $i < sizeof($varmap_woocom); $i++) { 
			$key = $varmap_woocom[$i];
			$val = $varmap_amazon[$i];
			if ( $key && $val ) {
				$variation_attribute_map[ $key ] = $val;
			}
		}

		self::updateOption( 'variation_attribute_map', 	$variation_attribute_map );
	}

	protected function saveVariationMergerMap() {

		$varmerge_woo1 = $_REQUEST['varmerge_woo1'];
		$varmerge_woo2 = $_REQUEST['varmerge_woo2'];
		$varmerge_amaz = $_REQUEST['varmerge_amaz'];
		$varmerge_glue = $_REQUEST['varmerge_glue'];

		$variation_merger_map = array();
		for ($i=0; $i < sizeof($varmerge_woo1); $i++) { 
			$val1 = $varmerge_woo1[$i];
			$val2 = $varmerge_woo2[$i];
			$val3 = $varmerge_amaz[$i];
			if ( $val1 && $val2 && $val3 ) {
				$variation_merger_map[] = array(
					'woo1' => $varmerge_woo1[$i],
					'woo2' => $varmerge_woo2[$i],
					'amaz' => $varmerge_amaz[$i],
					'glue' => $varmerge_glue[$i],
				);
			}
		}
		// echo "<pre>saving: ";print_r($variation_merger_map);echo"</pre>";#die();

		self::updateOption( 'variation_merger_map', 	$variation_merger_map );
	}



	protected function saveLicenseSettings() {
	} // saveLicenseSettings()

	protected function handleChangedUpdateChannel() {
	}

	protected function check_for_new_version() {
	}

	protected function checkLicenseStatus() {
	} // checkLicenseStatus()





	protected function saveLicenseSettingsV2() {
	} // saveLicenseSettingsV2()



	
	protected function saveDeveloperSettings() {

		// TODO: check nonce
		if ( isset( $_POST['wpla_option_log_to_db'] ) ) {

			self::updateOption( 'log_level',			$this->getValueFromPost( 'text_log_level' ) );
			self::updateOption( 'log_to_db',			$this->getValueFromPost( 'option_log_to_db' ) );
			self::updateOption( 'sandbox_enabled',		$this->getValueFromPost( 'option_sandbox_enabled' ) );
			self::updateOption( 'ajax_error_handling',	$this->getValueFromPost( 'ajax_error_handling' ) );
			self::updateOption( 'disable_variations',	$this->getValueFromPost( 'disable_variations' ) );
			self::updateOption( 'max_feed_size',		$this->getValueFromPost( 'max_feed_size' ) );
			self::updateOption( 'feed_encoding',		$this->getValueFromPost( 'feed_encoding' ) );
			self::updateOption( 'feed_currency_format',	$this->getValueFromPost( 'feed_currency_format' ) );
			self::updateOption( 'log_record_limit',		$this->getValueFromPost( 'log_record_limit' ) );
			self::updateOption( 'log_days_limit',		$this->getValueFromPost( 'log_days_limit' ) );
			self::updateOption( 'feeds_days_limit',		$this->getValueFromPost( 'feeds_days_limit' ) );
			self::updateOption( 'reports_days_limit',	$this->getValueFromPost( 'reports_days_limit' ) );
			self::updateOption( 'orders_days_limit',	$this->getValueFromPost( 'orders_days_limit' ) );
			self::updateOption( 'show_browse_node_ids',	$this->getValueFromPost( 'show_browse_node_ids' ) );
	

			$this->showMessage( __('Settings updated.','wpla') );

		}
	}
	



	protected function handleCronSettings( $schedule ) {
        WPLA()->logger->info("handleCronSettings( $schedule )");

        // remove scheduled event
	    $timestamp = wp_next_scheduled(  'wpla_update_schedule' );
    	wp_unschedule_event( $timestamp, 'wpla_update_schedule' );

    	if ( $schedule == 'external' ) return;

		if ( !wp_next_scheduled( 'wpla_update_schedule' ) ) {
			wp_schedule_event( time(), $schedule, 'wpla_update_schedule' );
		}
        
	}

	protected function handleFbaCronSettings( $schedule ) {
        WPLA()->logger->info("handleFbaCronSettings( $schedule )");

        // remove scheduled event
	    $timestamp = wp_next_scheduled(  'wpla_fba_report_schedule' );
    	wp_unschedule_event( $timestamp, 'wpla_fba_report_schedule' );

		if ( !wp_next_scheduled( 'wpla_fba_report_schedule' ) ) {
			wp_schedule_event( time(), $schedule, 'wpla_fba_report_schedule' );
		}
        
	}

    function get_tax_rates() {
    	global $wpdb;

		$rates = $wpdb->get_results( "SELECT tax_rate_id, tax_rate_country, tax_rate_state, tax_rate_name, tax_rate_priority FROM {$wpdb->prefix}woocommerce_tax_rates ORDER BY tax_rate_name" );

		return $rates;
    }

	public function onWpPrintStyles() {

		// jqueryFileTree
		// wp_register_style('jqueryFileTree_style', self::$PLUGIN_URL.'/js/jqueryFileTree/jqueryFileTree.css' );
		// wp_enqueue_style('jqueryFileTree_style'); 

	}

	public function onWpEnqueueScripts() {

		// jqueryFileTree
		// wp_register_script( 'jqueryFileTree', self::$PLUGIN_URL.'/js/jqueryFileTree/jqueryFileTree.js', array( 'jquery' ) );
		// wp_enqueue_script( 'jqueryFileTree' );

	}

	public function renderSettingsOptions() {
		?>
		<div class="hidden" id="screen-options-wrap" style="display: block;">
			<form method="post" action="" id="dev-settings">
				<h5>Show on screen</h5>
				<div class="metabox-prefs">
						<label for="dev-hide">
							<input type="checkbox" onclick="jQuery('#DeveloperToolBox').toggle();return false;" value="dev" id="dev-hide" name="dev-hide" class="hide-column-tog">
							Developer options
						</label>
					<br class="clear">
				</div>
			</form>
		</div>
		<?php
	}

}
