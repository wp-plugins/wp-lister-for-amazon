<?php
/**
 * WPLA_CronActions
 *
 * This class contains action hooks that are usually trigger via wp_cron()
 * 
 */

class WPLA_CronActions {

	var $dblogger;
	var $lockfile;

	public function __construct() {

		// add main cron handler
		add_action('wpla_update_schedule', 		array( &$this, 'cron_update_schedule' ) );
		add_action('wpla_daily_schedule', 		array( &$this, 'cron_daily_schedule' ) );
		add_action('wpla_fba_report_schedule', 	array( &$this, 'cron_fba_report_schedule' ) );

 		// handle external cron calls
		add_action('wp_ajax_wplister_run_scheduled_tasks', array( &$this, 'cron_update_schedule' ) );
		add_action('wp_ajax_nopriv_wplister_run_scheduled_tasks', array( &$this, 'cron_update_schedule' ) );


		// add internal action hooks
		add_action('wpla_update_orders', 						array( &$this, 'action_update_orders' ) );
		add_action('wpla_update_reports', 						array( &$this, 'action_update_reports' ) );
		add_action('wpla_update_feeds', 						array( &$this, 'action_update_feeds' ) );
		add_action('wpla_submit_pending_feeds', 				array( &$this, 'action_submit_pending_feeds' ) );
		add_action('wpla_update_pricing_info',  				array( &$this, 'action_update_pricing_info' ) );
		add_action('wpla_update_missing_asins', 				array( &$this, 'action_update_missing_asins' ) );
		add_action('wpla_clean_log_table', 						array( &$this, 'action_clean_log_table' ) );
		add_action('wpla_clean_tables', 						array( &$this, 'action_clean_tables' ) );
		add_action('wpla_reprice_products', 					array( &$this, 'action_reprice_products' ) );
		add_action('wpla_autosubmit_fba_orders', 				array( &$this, 'action_autosubmit_fba_orders' ) );
		add_action('wpla_request_daily_quality_report', 		array( &$this, 'request_daily_quality_report' ) );
		add_action('wpla_request_daily_fba_report', 			array( &$this, 'request_daily_fba_report' ) );
		add_action('wpla_request_daily_inventory_report', 		array( &$this, 'request_daily_inventory_report' ) );
		add_action('wpla_request_daily_fba_shipments_report', 	array( &$this, 'request_daily_fba_shipments_report' ) );

		// add custom cron schedules
		add_filter( 'cron_schedules', array( &$this, 'cron_add_custom_schedules' ) );

		// init logger
		add_filter( 'init', array( &$this, 'init' ) );



	}

	function init() {
		// global $wpla_logger;
		// $this->logger = $wpla_logger;		
	}

	// run update schedule - called by wp_cron if activated
	public function cron_update_schedule() {
        WPLA()->logger->info("*** WP-CRON: cron_update_schedule()");

        // check if update is already running
        if ( ! $this->checkLock() ) {
	        WPLA()->logger->error("WP-CRON: already running! terminating execution...");
        	return;
        }

		// update reports - if waiting for processing results
		if ( get_option( 'wpla_reports_in_progress' ) )
			do_action('wpla_update_reports');

		// update feeds - if waiting for submission results
		if ( get_option( 'wpla_feeds_in_progress' ) )
			do_action('wpla_update_feeds');

        // update orders
		do_action('wpla_update_orders');

		// submit pending feeds - after processing orders!
		do_action('wpla_submit_pending_feeds');

        // update pricing info
		do_action('wpla_update_pricing_info');

        // update missing ASINs
		do_action('wpla_update_missing_asins');


		// clean up
		$this->removeLock();

		// store timestamp
		update_option( 'wpla_cron_last_run', time() );

        WPLA()->logger->info("*** WP-CRON: cron_update_schedule() finished");
	}

	// run daily schedule - called by wp_cron
	public function cron_daily_schedule() {
        WPLA()->logger->info("*** WP-CRON: cron_daily_schedule()");

		// request daily FBA inventory report
		if ( get_option( 'wpla_fba_enabled' ) )
			do_action('wpla_request_daily_fba_report');

		// request daily inventory report
		if ( get_option( 'wpla_autofetch_inventory_report' ) )
			do_action('wpla_request_daily_inventory_report');

		// // request FBA shipments report
		// if ( get_option( 'wpla_fba_autosubmit_orders' ) )
		// 	do_action('wpla_request_daily_fba_shipments_report');

		// request daily listing quality report
		if ( get_option( 'wpla_autofetch_listing_quality_feeds', 1 ) )
			do_action('wpla_request_daily_quality_report');

		// clean tables
		do_action('wpla_clean_log_table');
		do_action('wpla_clean_tables');

		// store timestamp
		update_option( 'wpla_daily_cron_last_run', time() );

        WPLA()->logger->info("*** WP-CRON: cron_daily_schedule() finished");
	}

	// run FBA report schedule - called by wp_cron
	public function cron_fba_report_schedule() {
        WPLA()->logger->info("*** WP-CRON: cron_fba_report_schedule()");

		// request FBA shipments report
		if ( get_option( 'wpla_fba_enabled' ) )
			do_action('wpla_request_daily_fba_shipments_report');

		// store timestamp
		update_option( 'wpla_fba_report_cron_last_run', time() );

        WPLA()->logger->info("*** WP-CRON: cron_fba_report_schedule() finished");
	}


	// fetch missing ASINs - called by do_action()
	public function action_update_missing_asins() {
        WPLA()->logger->info("do_action: wpla_update_missing_asins");

		$accounts      = WPLA_AmazonAccount::getAll();
		$listingsModel = new WPLA_ListingsModel();
		$batch_size    = 10; // update 10 items at a time

		foreach ($accounts as $account ) {

			$account_id    = $account->id;
			$listings      = $listingsModel->getAllOnlineWithoutASIN( $account_id, 10, OBJECT_K );
			if ( empty($listings) ) continue;

			// process one listing at a time (for now)
			foreach ( $listings as $listing ) {
				WPLA()->logger->info('fetching ASIN for SKU '.$listing->sku.' ('.$listing->id.') - type: '.$listing->product_type );
	
				$api    = new WPLA_AmazonAPI( $account->id );
				$result = $api->getMatchingProductForId( $listing->sku, 'SellerSKU' );

				if ( $result->success )  {

					if ( ! empty( $result->product->ASIN ) ) {

						// update listing ASIN
						$listingsModel->updateWhere( array( 'id' => $listing->id ), array( 'asin' => $result->product->ASIN ) );
						WPLA()->logger->info('new ASIN for listing #'.$listing->id . ': '.$result->product->ASIN );

					} else {

						// this is what happens when new products are listed but Amazon fails to assign an ASIN
						// in which case the user might have to contact Amazon seller support...
						$error_msg  = sprintf( __('There was a problem fetching product details for %s.','wpla'), $listing->sku );
						WPLA()->logger->error( $error_msg . ' - empty product data!');

						$error_msg  .= ' This SKU does not seem to exist in your inventory on Seller Central. You can try to submit this listing again, but you may have to report this to Amazon Seller Support.';

						// // build history array (overwrite)
						// $history = array (
						//   	'errors' => array (
						// 	    array (
						// 			'original-record-number' => '0',
						// 			'sku'                    => $listing->sku,
						// 			'error-code'             => '42',
						// 			'error-type'             => 'Error',
						// 			'error-message'          => $error_msg,
						// 	    ),
						// 	),
						//   	'warnings' => array(),
						// );

						// load existing history data - or init new history array
						$history = maybe_unserialize( $listing->history );
						if ( ! is_array($history) || ! isset($history['errors'] ) ) {
							$history = array (
								'errors'   => array(),
								'warnings' => array(),
							);
						}

						// add custom error to history array
						$error_42 = array (
							'original-record-number' => '0',
							'sku'                    => $listing->sku,
							'error-code'             => '42',
							'error-type'             => 'Error',
							'error-message'          => $error_msg,
						);
						$history['errors'][] = $error_42;

						// mark as failed - and include error message
						$listingsModel->updateWhere( array( 'id' => $listing->id ), array( 'status' => 'failed', 'history' => serialize($history) ) );

					}

				} elseif ( $result->Error->Message ) {
					$errors  = sprintf( __('There was a problem fetching product details for %s.','wpla'), $listing->sku ) .'<br>Error: '. $result->Error->Message;
					WPLA()->logger->error( $errors );
				} else {
					$errors  = sprintf( __('There was a problem fetching product details for %s.','wpla'), $listing->sku );
					WPLA()->logger->error( $errors );
				}
	
			} // foreach listing

		} // each account

	} // action_update_missing_asins ()


	// fetch lowest prices - called by do_action()
	public function action_update_pricing_info() {
        WPLA()->logger->info("do_action: wpla_update_pricing_info");

		$accounts = WPLA_AmazonAccount::getAll();
		// $listingsModel = new WPLA_ListingsModel();
		$batch_size = 200; // 10 requests per batch (for now - maximum should be 20 requests = 400 items / max. 600 items per minute)

		foreach ($accounts as $account ) {

			$account_id    = $account->id;
			$listings      = WPLA_ListingQueryHelper::getItemsDueForPricingUpdateForAcccount( $account_id, $batch_size );
			$listing_ASINs = array();
			WPLA()->logger->info( sprintf( '%s items with outdated pricing info found for account %s.', sizeof($listings), $account->title ) );

			// build array of ASINs
        	foreach ($listings as $listing) {
        		// skip duplicate ASINs - they throw an error from Amazon
        		if ( in_array( $listing->asin, $listing_ASINs ) ) continue;

       			$listing_ASINs[] = $listing->asin;        			
        	}
        	if ( empty($listing_ASINs) ) continue;


        	// process batches of 20 ASINs
        	for ($page=0; $page < 10; $page++) { 
        		$page_size = 20;

        		// splice ASINs
        		$offset = $page * $page_size;
        		$ASINs_for_this_batch = array_slice( $listing_ASINs, $offset, $page_size );
        		if ( empty($ASINs_for_this_batch) ) continue;

        		// run update
	        	$this->update_pricing_info_for_asins( $ASINs_for_this_batch, $account_id );

				WPLA()->logger->info( sprintf( '%s ASINs had their pricing info updated - account %s.', sizeof($listing_ASINs), $account->title ) );
        	}


		} // each account

	} // action_update_pricing_info ()


	// fetch and process lowest price info for up to 20 ASINs
	public function update_pricing_info_for_asins( $listing_ASINs, $account_id ) {
		$listingsModel = new WPLA_ListingsModel();

    	// fetch Buy Box pricing info and process result
		$api    = new WPLA_AmazonAPI( $account_id );
		$result = $api->getCompetitivePricingForId( $listing_ASINs );
		$listingsModel->processBuyBoxPricingResult( $result );

		// return if lowest offers are disabled
		// if ( ! get_option('wpla_repricing_use_lowest_offer') ) return;

    	// fetch Lowest Offer info and process result
		$api    = new WPLA_AmazonAPI( $account_id );
		$result = $api->getLowestOfferListingsForASIN( $listing_ASINs );
		$listingsModel->processLowestOfferPricingResult( $result );

	} // update_pricing_info_for_asins ()


	// apply lowest prices - called by do_action()
	public function action_reprice_products() {
	} // action_reprice_products ()


	// auto submit FBA orders - called by do_action()
	public function action_autosubmit_fba_orders() {
	} // action_autosubmit_fba_orders ()


	// fetch new orders - called by do_action()
	public function action_update_orders() {
        WPLA()->logger->info("do_action: wpla_update_orders");

		$accounts = WPLA_AmazonAccount::getAll();
		$importer = new WPLA_OrdersImporter();

		foreach ($accounts as $account ) {

			$api = new WPLA_AmazonAPI( $account->id );

			// get date of last order
			$om = new WPLA_OrdersModel();
			$lastdate = $om->getDateOfLastOrder();
			WPLA()->logger->info('getDateOfLastOrder() returned: '.$lastdate);

			$days = isset($_REQUEST['days']) && $_REQUEST['days'] ? $_REQUEST['days'] : false;
			if ( ! $lastdate && ! $days ) $days = 1;

			// get orders
			$orders = $api->getOrders( $lastdate, $days );
			// echo "<pre>";print_r($orders);echo"</pre>";#die();

			if ( is_array( $orders ) ) {

				// run the import
				$success = $importer->importOrders( $orders, $account );

				$msg  = sprintf( __('%s order(s) were processed for account %s.','wpla'), sizeof($orders), $account->title );
				if ( $importer->updated_count  > 0 ) $msg .= "\n".'Updated orders: '.$importer->updated_count ;
				if ( $importer->imported_count > 0 ) $msg .= "\n".'Created orders: '.$importer->imported_count;					
				WPLA()->logger->info( $msg );
				$this->showMessage( nl2br($msg),0,1 );

			} elseif ( $orders->Error->Message ) {
				$msg = sprintf( __('There was a problem downloading orders for account %s.','wpla'), $account->title ) .' - Error: '. $orders->Error->Message;
				WPLA()->logger->error( $msg );
				$this->showMessage( nl2br($msg),1,1 );
			} else {
				$msg = sprintf( __('There was a problem downloading orders for account %s.','wpla'), $account->title );
				WPLA()->logger->error( $msg );
				$this->showMessage( nl2br($msg),1,1 );
			}

		}
		$this->message = '';

	} // action_update_orders()


	// update submitted reports - called by do_action()
	public function action_update_reports() {
        WPLA()->logger->info("do_action: wpla_update_reports");

		$accounts = WPLA_AmazonAccount::getAll();

		foreach ($accounts as $account ) {

			$api = new WPLA_AmazonAPI( $account->id );

			// get report requests
			$reports = $api->getReportRequestList();

			if ( is_array( $reports ) )  {

				// run the import
				// $this->processReportsRequestList( $reports, $account );

				// process report request list
				WPLA_AmazonReport::processReportsRequestList( $reports, $account );

				$msg  = sprintf( __('%s report request(s) were found for account %s.','wpla'), sizeof($reports), $account->title );
				WPLA()->logger->info( $msg );
				$this->showMessage( nl2br($msg),0,1 );

			} elseif ( $reports->Error->Message ) {
				$msg = sprintf( __('There was a problem fetching report requests for account %s.','wpla'), $account->title ) .' - Error: '. $reports->Error->Message;
				WPLA()->logger->error( $msg );
				$this->showMessage( nl2br($msg),1,1 );
			} else {
				$msg = sprintf( __('There was a problem fetching report requests for account %s.','wpla'), $account->title );
				WPLA()->logger->error( $msg );
				$this->showMessage( nl2br($msg),1,1 );
			}

		}

	} // action_update_reports()


	// update submitted feeds - called by do_action()
	public function action_update_feeds() {
        WPLA()->logger->info("do_action: wpla_update_feeds");

		$accounts = WPLA_AmazonAccount::getAll();

		foreach ($accounts as $account ) {

			$api = new WPLA_AmazonAPI( $account->id );

			// get feed submissions
			$feeds = $api->getFeedSubmissionList();

			if ( is_array( $feeds ) )  {

				// run the import
				WPLA_AmazonFeed::processFeedsSubmissionList( $feeds, $account );

				$msg  = sprintf( __('%s feed submission(s) were found for account %s.','wpla'), sizeof($feeds), $account->title );
				WPLA()->logger->info( $msg );
				$this->showMessage( nl2br($msg),0,1 );

			} elseif ( $feeds->Error->Message ) {
				$msg = sprintf( __('There was a problem fetching feed submissions for account %s.','wpla'), $account->title ) .' - Error: '. $feeds->Error->Message;
				WPLA()->logger->error( $msg );
				$this->showMessage( nl2br($msg),1,1 );
			} else {
				$msg = sprintf( __('There was a problem fetching feed submissions for account %s.','wpla'), $account->title );
				WPLA()->logger->error( $msg );
				$this->showMessage( nl2br($msg),1,1 );
			}

		}

	} // action_update_feeds()


	// submit pending feeds - called by do_action()
	public function action_submit_pending_feeds() {
        WPLA()->logger->info("do_action: wpla_submit_pending_feeds");

		$accounts = WPLA_AmazonAccount::getAll();

		foreach ($accounts as $account ) {

			// refresh feeds
			WPLA_AmazonFeed::updatePendingFeedForAccount( $account );

			// get pending feeds for account
			$feeds = WPLA_AmazonFeed::getAllPendingFeedsForAccount( $account->id );
	        WPLA()->logger->info("found ".sizeof($feeds)." pending feeds for account {$account->id}");

			foreach ($feeds as $feed) {

				$autosubmit_feeds = array(
					'_POST_FLAT_FILE_PRICEANDQUANTITYONLY_UPDATE_DATA_',	// Price and Quantity Update Feed
					'_POST_FLAT_FILE_LISTINGS_DATA_',						// Flat File Listings Feed
					'_POST_FLAT_FILE_FULFILLMENT_DATA_',					// Order Fulfillment Feed
					'_POST_FLAT_FILE_FULFILLMENT_ORDER_REQUEST_DATA_',		// Flat File FBA Shipment Injection Fulfillment Feed
					'_POST_FLAT_FILE_INVLOADER_DATA_',						// Inventory Loader Feed (Product Removal)
				);

				if ( ! in_array( $feed->FeedType, $autosubmit_feeds ) ) {
			        WPLA()->logger->info("skipped pending feed {$feed->id} ({$feed->FeedType}) for account {$account->id} - autosubmit disabled for feed type");
			        continue;
				}

				// submit feed
				$feed->submit();

		        WPLA()->logger->info("submitted pending feed {$feed->id} ({$feed->FeedType}) for account {$account->id}");
			}

		}

	} // action_submit_pending_feeds()




	// request listing quality reports for all active accounts
	public function request_daily_quality_report() {

		$report_type = '_GET_MERCHANT_LISTINGS_DEFECT_DATA_';
		$accounts    = WPLA_AmazonAccount::getAll();

		foreach ($accounts as $account ) {

			$api = new WPLA_AmazonAPI( $account->id );

			// request report - returns request list as array on success
			$reports = $api->requestReport( $report_type );

			if ( is_array( $reports ) )  {

				// process the result
				WPLA_AmazonReport::processReportsRequestList( $reports, $account, true );

			} elseif ( $reports->Error->Message ) {
			} else {
			}

		} // foreach account

	} // request_daily_quality_report()


	// request FBA inventory reports for all active accounts
	public function request_daily_fba_report() {

		$report_type = '_GET_AFN_INVENTORY_DATA_';
		$accounts    = WPLA_AmazonAccount::getAll();

		foreach ($accounts as $account ) {

			$api = new WPLA_AmazonAPI( $account->id );

			// request report - returns request list as array on success
			$reports = $api->requestReport( $report_type );

			if ( is_array( $reports ) )  {

				// process the result
				WPLA_AmazonReport::processReportsRequestList( $reports, $account, true );

			} elseif ( $reports->Error->Message ) {
			} else {
			}

		} // foreach account

	} // request_daily_fba_report()


	// request FBA fulfilled shipment reports for all active accounts
	public function request_daily_fba_shipments_report() {

		$report_type = '_GET_AMAZON_FULFILLED_SHIPMENTS_DATA_';
		$accounts    = WPLA_AmazonAccount::getAll();

		foreach ($accounts as $account ) {

			$api = new WPLA_AmazonAPI( $account->id );

			// request report - returns request list as array on success
			$reports = $api->requestReport( $report_type );

			if ( is_array( $reports ) )  {

				// process the result
				WPLA_AmazonReport::processReportsRequestList( $reports, $account, true );

			} elseif ( $reports->Error->Message ) {
			} else {
			}

		} // foreach account

	} // request_daily_fba_shipments_report()


	// request merchant inventory reports for all active accounts
	public function request_daily_inventory_report() {

		$report_type = '_GET_MERCHANT_LISTINGS_DATA_';
		$accounts    = WPLA_AmazonAccount::getAll();

		foreach ($accounts as $account ) {

			$api = new WPLA_AmazonAPI( $account->id );

			// request report - returns request list as array on success
			$reports = $api->requestReport( $report_type );

			if ( is_array( $reports ) )  {

				// process the result
				WPLA_AmazonReport::processReportsRequestList( $reports, $account, true );

			} elseif ( $reports->Error->Message ) {
			} else {
			}

		} // foreach account

	} // request_daily_inventory_report()





	
	public function action_clean_log_table() {
		global $wpdb;
		// if ( get_option('wpla_log_to_db') == '1' ) {
			$days_to_keep = get_option( 'wpla_log_days_limit', 30 );
			$wpdb->query('DELETE FROM '.$wpdb->prefix.'amazon_log WHERE timestamp < DATE_SUB(NOW(), INTERVAL '.$days_to_keep.' DAY )');
		// }
	} // action_clean_log_table()

	public function action_clean_tables() {
		global $wpdb;

		// clean feeds table (date_created)
		$days_to_keep = get_option( 'wpla_feeds_days_limit', 90 );
		if ( $days_to_keep ) {
			$wpdb->query('DELETE FROM '.$wpdb->prefix.'amazon_feeds WHERE date_created < DATE_SUB(NOW(), INTERVAL '.$days_to_keep.' DAY )');
		}

		// clean reports table (SubmittedDate)
		$days_to_keep = get_option( 'wpla_reports_days_limit', 90 );
		if ( $days_to_keep ) {
			$wpdb->query('DELETE FROM '.$wpdb->prefix.'amazon_reports WHERE SubmittedDate < DATE_SUB(NOW(), INTERVAL '.$days_to_keep.' DAY )');
		}

		// clean orders table (date_created)
		$days_to_keep = get_option( 'wpla_orders_days_limit', '' );
		if ( $days_to_keep ) {
			$wpdb->query('DELETE FROM '.$wpdb->prefix.'amazon_orders WHERE date_created < DATE_SUB(NOW(), INTERVAL '.$days_to_keep.' DAY )');
		}

	} // action_clean_log_table()


	public function checkLock() {

		// get full path to lockfile
		$uploads        = wp_upload_dir();
		$lockfile       = $uploads['basedir'] . '/' . 'wpla_sync.lock';
		$this->lockfile = $lockfile;

		// skip locking if lockfile is not writeable
		if ( ! is_writable( $lockfile ) && ! is_writable( dirname( $lockfile ) ) ) {
	        WPLA()->logger->error("lockfile not writable: ".$lockfile);
	        return true;
		}

		// create lockfile if it doesn't exist
		if ( ! file_exists( $lockfile ) ) {
			$ts = time();
			file_put_contents( $lockfile, $ts );
	        WPLA()->logger->info("lockfile created at TS $ts: ".$lockfile);
	        return true;
		}

		// lockfile exists - check TS
		$ts = (int) file_get_contents($lockfile); 

		// check if TS is outdated (after 10min.)
		if ( $ts < ( time() - 600 ) ) { 
	        WPLA()->logger->info("stale lockfile found for TS ".$ts.' - '.human_time_diff( $ts ).' ago' );

	        // update lockfile 
			$ts = time();
			file_put_contents( $lockfile, $ts ); 
	        
	        WPLA()->logger->info("lockfile updated for TS $ts: ".$lockfile);
	        return true;
		} else { 
			// process is still alive - can not run twice
	        WPLA()->logger->info("SKIP CRON - sync already running with TS ".$ts.' - '.human_time_diff( $ts ).' ago' );
			return false; 
		} 

		return true;
	} // checkLock()

	public function removeLock() {
		if ( file_exists( $this->lockfile ) ) {
			unlink( $this->lockfile );
	        WPLA()->logger->info("lockfile was removed: ".$this->lockfile);
		}
	}

	public function cron_add_custom_schedules( $schedules ) {
		$schedules['five_min'] = array(
			'interval' => 60 * 5,
			'display' => 'Once every five minutes'
		);
		$schedules['ten_min'] = array(
			'interval' => 60 * 10,
			'display' => 'Once every ten minutes'
		);
		$schedules['fifteen_min'] = array(
			'interval' => 60 * 15,
			'display' => 'Once every fifteen minutes'
		);
		$schedules['thirty_min'] = array(
			'interval' => 60 * 30,
			'display' => 'Once every thirty minutes'
		);
		$schedules['three_hours'] = array(
			'interval' => 60 * 60 * 3,
			'display' => 'Once every three hours'
		);
		$schedules['six_hours'] = array(
			'interval' => 60 * 60 * 6,
			'display' => 'Once every six hours'
		);
		$schedules['twelve_hours'] = array(
			'interval' => 60 * 60 * 12,
			'display' => 'Once every twelve hours'
		);
		return $schedules;
	}
			
	public function showMessage($message, $errormsg = false, $echo = false) {		

		// don't output message when doing cron
		if ( defined('DOING_CRON') && DOING_CRON ) return;

		if ( defined('WPLISTER_RESELLER_VERSION') ) $message = apply_filters( 'wpla_tooltip_text', $message );

		$class = ($errormsg) ? 'error' : 'updated fade';
		$class = ($errormsg == 2) ? 'updated update-nag' : $class; 	// warning
		$message = '<div id="message" class="'.$class.'" style="display:block !important"><p>'.$message.'</p></div>';
		if ($echo) {
			echo $message;
		} else {
			$this->message .= $message;
		}
	}
	
}
// $WPLA_CronActions = new WPLA_CronActions();
