<?php
/**
 * WPLA_SkuGenPage class
 * 
 */

class WPLA_SkuGenPage extends WPLA_Page {

	const slug = 'tools';

	public function onWpInit() {

		// Add custom screen options
		$load_action = "load-".$this->main_admin_menu_slug."_page_wpla-".self::slug;
		add_action( $load_action, array( &$this, 'addScreenOptions' ) );
		
		// $this->handleSubmitOnInit();
	}

	function addScreenOptions() {
		if ( ! isset($_GET['tab']) || $_GET['tab'] != 'skugen' ) return;
		
		if ( ( isset($_GET['action']) ) && ( $_GET['action'] == 'edit' ) ) {
			// on edit page render developers options
			add_screen_options_panel('wpla_developer_options', '', array( &$this, 'renderDeveloperOptions'), 'toplevel_page_wpla' );

		} else {

			// render table options
			$option = 'per_page';
			$args = array(
		    	'label' => 'Listings',
		        'default' => 20,
		        'option' => 'listings_per_page'
		        );
			add_screen_option( $option, $args );
			$this->skugenTable = new WPLA_SkuGenTable();

		}

	    // add_thickbox();
		// wp_enqueue_script( 'thickbox' );
		// wp_enqueue_style( 'thickbox' );

	}
	

	public function handleActions() {
        // WPLA()->logger->debug("handleActions()");
	}
	
	public function generateNewSKUs( $product_ids ) {

		foreach ($product_ids as $post_id) {
			$new_sku = WPLA_SkuGenerator::generateNewSKU( $post_id );
			update_post_meta( $post_id, '_sku', $new_sku );
		}

        $this->showMessage( count($product_ids) . ' product SKUs have been updated.');
	}
	
	public function saveSkuGenOptions() {

		update_option( 'wpla_skugen_mode_simple', 	 $_REQUEST['wpla_skugen_mode_simple'] );
		update_option( 'wpla_skugen_mode_variation', $_REQUEST['wpla_skugen_mode_variation'] );
		update_option( 'wpla_skugen_mode_case', 	 $_REQUEST['wpla_skugen_mode_case'] );

        $this->showMessage( 'SKU generator options were saved.');
	}
	

	public function displaySkuGenPage() {

		// handle actions and show notes
		// $this->handleActions();

		// handle button
		if ( $this->requestAction() == 'wpla_generate_all_missing_skus' ) {
			$product_ids = WPLA_SkuGenerator::getAllProductIDsWithoutSKU();
			$this->generateNewSKUs( $product_ids );
		}

		// handle bulk action
		if ( $this->requestAction() == 'wpla_bulk_generate_skus' ) {
			$this->generateNewSKUs( $_REQUEST['product'] );
		}

		// save options
		if ( $this->requestAction() == 'wpla_save_skugen_options' ) {
			$this->saveSkuGenOptions();
		}


	    // create table and fetch items to show
	    $this->skugenTable = new WPLA_SkuGenTable();
	    $this->skugenTable->prepare_items();

		$active_tab = 'skugen';
		$aData = array(
			'plugin_url'				=> self::$PLUGIN_URL,
			'message'					=> $this->message,

			'skugenTable'				=> $this->skugenTable,
			'default_account'			=> get_option( 'wpla_default_account_id' ),
			'skugen_mode_simple'		=> get_option( 'wpla_skugen_mode_simple' ),
			'skugen_mode_variation'		=> get_option( 'wpla_skugen_mode_variation' ),
			'skugen_mode_case'			=> get_option( 'wpla_skugen_mode_case' ),

			'tools_url'				    => 'admin.php?page='.self::ParentMenuId.'-tools',
			'form_action'				=> 'admin.php?page='.self::ParentMenuId.'-tools'.'&tab='.$active_tab
		);
		$this->display( 'tools_skugen', $aData );
	} // displaySkuGenPage()


} // WPLA_SkuGenPage
