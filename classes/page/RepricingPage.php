<?php
/**
 * WPLA_RepricingPage class
 * 
 */

class WPLA_RepricingPage extends WPLA_Page {

	const slug = 'repricing';

	public function onWpInit() {

		// Add custom screen options
		// add_action( "load-toplevel_page_wpla", array( &$this, 'addScreenOptions' ) );
		add_action( "load-amazon_page_wpla-tools", array( &$this, 'addScreenOptions' ) );
		
		// $this->handleSubmitOnInit();
	}

	function addScreenOptions() {
		if ( isset($_GET['tab']) && $_GET['tab'] != 'repricing' ) return;
		
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
			$this->repricingTable = new WPLA_RepricingTable();

		}

	    // add_thickbox();
		// wp_enqueue_script( 'thickbox' );
		// wp_enqueue_style( 'thickbox' );

	}
	

	public function handleActions() {
        // $this->logger->debug("handleActions()");
	}
	
	public function resubmitPnqUpdateForSelectedItems() {

		$item_ids = $_REQUEST['listing'];

		foreach ( $item_ids as $listing_id ) {
			$data = array( 'pnq_status' => 1 );
			WPLA_ListingsModel::updateWhere( array( 'id' => $listing_id ), $data );
		}

        $this->showMessage( count($item_ids) . ' product prices have been scheduled for resubmission.');
	}
	
	public function applyLowestPricesToSelectedItems() {

		$item_ids = $_REQUEST['listing'];
		$items = WPLA_ListingsModel::getItems( $item_ids );
		if ( empty($items) ) return;

        $changed_product_ids1 = WPLA_RepricingHelper::adjustLowestPriceForProducts( $items, true );
        $changed_product_ids2 = WPLA_RepricingHelper::resetProductsToMaxPrice( $items, true );

        $changed_product_ids  = array_merge( $changed_product_ids1, $changed_product_ids2 );
        $this->showMessage( count($changed_product_ids) . ' of ' . count($items) . ' product prices have been updated.');
	}
	
	public function applyLowestPricesToAllItems() {

		$changed_product_ids = WPLA_RepricingHelper::repriceProducts();

        $this->showMessage( count($changed_product_ids) . ' product prices have been updated.');
	}
	
	public function applyMinMaxPrices() {

		$item_ids = $_REQUEST['item_ids'] ? explode( ',', $_REQUEST['item_ids'] ) : array();
		WPLA_MinMaxPriceWizard::updateMinMaxPrices( $item_ids );

        $this->showMessage( count($item_ids) . ' minimum and maximum prices have been updated.');
	}
	

	public function displayRepricingPage() {

		// handle actions and show notes
		// $this->handleActions();

		if ( $this->requestAction() == 'wpla_apply_lowest_price_to_all_items' ) {
			$this->applyLowestPricesToAllItems();
		}

		if ( $this->requestAction() == 'wpla_resubmit_pnq_update' ) {
			$this->resubmitPnqUpdateForSelectedItems();
		}

		if ( $this->requestAction() == 'wpla_bulk_apply_lowest_prices' ) {
			$this->applyLowestPricesToSelectedItems();
		}

		if ( $this->requestAction() == 'wpla_bulk_apply_minmax_prices' ) {
			$this->applyMinMaxPrices();
		}

		// handle bulk action - get_compet_price
		if ( $this->requestAction() == 'get_compet_price' ) {
			WPLA()->pages['listings']->get_compet_price();
			WPLA()->pages['listings']->get_lowest_offers();
		}

		if ( $this->requestAction() == 'wpla_resubmit_all_failed_prices' ) {
			$lm = new WPLA_ListingsModel();
			$items = $lm->getWhere( 'pnq_status', -1 );
			foreach ( $items as $item ) {
				// set pnq status to changed (1)
				$lm->updateWhere( array( 'id' => $item->id ), array( 'pnq_status' => 1 ) );
			}
			$this->showMessage( sprintf( __('%s failed prices were schedules for resubmission.','wpla'), count($items) ) );
		}


	    // create table and fetch items to show
	    $this->repricingTable = new WPLA_RepricingTable();
	    $this->repricingTable->prepare_items();

		$active_tab = 'repricing';
		$aData = array(
			'plugin_url'				=> self::$PLUGIN_URL,
			'message'					=> $this->message,

			'listingsTable'				=> $this->repricingTable,
			'default_account'			=> get_option( 'wpla_default_account_id' ),

			'tools_url'				    => 'admin.php?page='.self::ParentMenuId.'-tools',
			'form_action'				=> 'admin.php?page='.self::ParentMenuId.'-tools'.'&tab='.$active_tab
		);
		$this->display( 'tools_repricing', $aData );
	}


} // WPLA_RepricingPage
