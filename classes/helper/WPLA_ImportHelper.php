<?php

class WPLA_ImportHelper {
	
	var $account;
	// var $logger;
	public $result;
	public $message = '';
	public $lastError;
	public $lastPostID;
	public $updated_count = 0;
	public $imported_count = 0;
	public $request_count = 0;

	const TABLENAME = 'amazon_listings';

	public function __construct() {
		// global $wpla_logger;
		// $this->logger = &$wpla_logger;
	}



	public static function analyzeReportForPreview( $report ) {
		$summary = new stdClass();

        $data_rows    = $report->get_data_rows();
        $report_asins = self::getAllASINsInReport( $data_rows );
        $report_skus  = self::getAllSKUsInReport( $data_rows );

        $wpla_asins   = self::getAllASINsInWPLA();
        $woocom_skus  = self::getAllSKUsInWooCom();

		// compare ASINs
		$summary->listings_to_update = array_intersect( $report_asins, $wpla_asins );
		$summary->listings_to_import = array_diff     ( $report_asins, $wpla_asins );

		// compare SKUs
		$summary->products_to_update = array_intersect( $report_skus, $woocom_skus );
		$summary->products_to_import = array_diff     ( $report_skus, $woocom_skus );

		// include raw data as well
		$summary->report_asins = $report_asins;
		$summary->report_skus  = $report_skus;
		// $summary->woocom_skus  = $woocom_skus;
		// $summary->wpla_asins   = $wpla_asins;

		// echo "<pre>";print_r($summary);echo"</pre>";die();
		return $summary;
	}


	public static function getAllSKUsInWooCom() {
		global $wpdb;
		$table = $wpdb->postmeta;

		$result = $wpdb->get_col("
			SELECT meta_value FROM $table
			WHERE meta_key = '_sku'
			  AND NOT meta_value = ''
		");
		return $result;
	} 

	public static function getAllASINsInWPLA() {
		global $wpdb;
		$table = $wpdb->prefix . self::TABLENAME;

		$result = $wpdb->get_col("
			SELECT asin FROM $table
			WHERE NOT asin IS NULL
		");
		return $result;
	} 

	public static function getAllASINsInReport( $rows ) {
		$ASINs = array();
		foreach ($rows as $row) {
			$row_asin = false;
			$row_asin = isset( $row['asin1'] ) ? $row['asin1'] : $row_asin;
			$row_asin = isset( $row['asin']  ) ? $row['asin']  : $row_asin;

			// special treatment for amazon.ca
			if ( ! $row_asin && isset($row['product-id']) ) {
				if ( $row['product-id-type'] == 1 ) {
					$row_asin = $row['product-id'];
				}
			}
			if ( ! $row_asin ) continue;

			// if ( ! in_array($row_asin, $ASINs) ) // poor performance on big arrays
			if ( ! isset( $ASINs[ $row_asin ] ) )
				$ASINs[ $row_asin ] = 1;
		}
		return array_keys( $ASINs );
	} 

	public static function getAllSKUsInReport( $rows ) {
		$SKUs = array();
		foreach ($rows as $row) {
			$row_sku = $row['seller-sku'];
			// if ( ! in_array($row_sku, $SKUs) )  // poor performance on big arrays
			if ( ! isset( $SKUs[ $row_sku ] ) )
				$SKUs[ $row_sku ] = 1;
		}
		return array_keys( $SKUs );
	} 


	// process single report page - called from WPLA_AjaxHandler
	public static function ajax_processReportPage( $job, $task, $single_sku_mode = false ) {

		// init
		$report = new WPLA_AmazonReport( $task['id'] );
		// $account = WPLA_AmazonAccount::getAccount( $report->account_id );
		// $api     = new WPLA_AmazonAPI( $account->id );

		// get CSV data
        $rows = $report->get_data_rows();

        if ( $single_sku_mode ) {

			// slice single row with matching SKU
			$selected_rows = array();
			foreach ( $rows as $row ) {
				if ( $row['seller-sku'] == $task['sku'] ) {
					$selected_rows[] = $row;
				}
			}
			$rows = $selected_rows;

        } else {

			// slice rows array according to limits
			$from_row = $task['from_row'];
			$to_row   = $task['to_row'];
			$rows     = array_slice( $rows, $from_row - 1, $to_row - $from_row + 1, true );

        }


		// _GET_AFN_INVENTORY_DATA_
       	if ( $report->ReportType == '_GET_AFN_INVENTORY_DATA_' ) {
			return self::processFBAReportPage( $report, $rows, $job, $task );
			die();
       	}

		// _GET_MERCHANT_LISTINGS_DEFECT_DATA_
       	if ( $report->ReportType == '_GET_MERCHANT_LISTINGS_DEFECT_DATA_' ) {
			return self::processQualityReportPage( $report, $rows, $job, $task );
			die();
       	}

		// _GET_MERCHANT_LISTINGS_DATA_
       	if ( $report->ReportType == '_GET_MERCHANT_LISTINGS_DATA_' ) {
			return self::processInventoryReportPage( $report, $rows, $job, $task );
			die();
       	}

		echo "Unknown report type: ".$report->ReportType;
		die();
	} // ajax_processReportPage()



	// process single merchant inventory report page
	public static function processInventoryReportPage( $report, $rows, $job, $task ) {

        // process rows
		$lm             = new WPLA_ListingsModel();
		$ProductBuilder = new WPLA_ProductBuilder();

		// $update_woo_products_from_reports = get_option( 'wpla_update_woo_products_from_reports' ) == '1' ? true : false;
		$reports_update_woo_stock         = get_option( 'wpla_reports_update_woo_stock'    , 1 ) == 1 ? true : false;
		$reports_update_woo_price         = get_option( 'wpla_reports_update_woo_price'    , 1 ) == 1 ? true : false;
		$reports_update_woo_condition     = get_option( 'wpla_reports_update_woo_condition', 1 ) == 1 ? true : false;
		$update_woo_products_from_reports = $reports_update_woo_stock || $reports_update_woo_price || $reports_update_woo_condition;

		foreach ($rows as $report_row) {
			$existing_item = $lm->updateItemFromReportCSV( $report_row, $report->account_id );
			if ( $existing_item && $update_woo_products_from_reports ) {
				$ProductBuilder->updateProductFromItem( $existing_item, $report_row );
			}
		}

		//
		// debug
		//
		$msg  = ''.$lm->imported_count.' items were added to the import queue and '.$lm->updated_count.' existing listings were updated.<br>';
		$msg  = "<div class='updated'><p>$msg</p></div>";

		// send debug data as error...
		$error = new stdClass();
		$error->code  		= 10001;
		$error->HtmlMessage	= $msg;
		$errors  = array( $error );

		$success = true;
		// $errors  = '';


		// build response
		$response = new stdClass();
		$response->job  	= $job;
		$response->task 	= $task;
		$response->errors   = $errors;
		$response->success  = $success;

		$response->imported_count = $lm->imported_count;
		$response->updated_count  = $lm->updated_count;

		return $response;
	} // processInventoryReportPage()



	// process single FBA report page
	public static function processFBAReportPage( $report, $rows, $job, $task ) {
		$listingsModel = new WPLA_ListingsModel();
		$errors = array();

		// get default fulfillment center ID
		$fba_default_fcid = get_option( 'wpla_fba_fulfillment_center_id', 'AMAZON_NA' );


		// if fallback is enabled, clear FBA data before processing first page
		$account_id          = $report->account_id;
		$fba_enable_fallback = get_option( 'wpla_fba_enable_fallback', 0 );

		if ( $task['from_row'] == 1 && $fba_enable_fallback ) {
	
			// reset FBA info for all items using this account
			$update_data = array(
				'fba_quantity' => null,
				'fba_fcid'     => null,
			);
			$listingsModel->updateWhere( array( 'account_id' => $account_id ), $update_data );
		}


        // process rows
		if ( is_array($rows) )
		foreach ($rows as $row) {

			// skip error rows (single element array)
			if ( sizeof($row) <= 1 ) {
				$error = new stdClass();
				$error->HtmlMessage = strip_tags( reset($row) );
				$errors[] = $error;
				continue;
			} 

			$asin          = $row['asin'];
			$sku           = $row['seller-sku'];
			$fba_quantity  = $row['Quantity Available'];
			$fba_condition = $row['Warehouse-Condition-code'];

			// skip rows if condition is UNSELLABLE
			if ( $fba_condition == 'UNSELLABLE' ) continue;

			// if fallback enabled, skip rows with zero quantity
			if ( $fba_quantity == 0 && $fba_enable_fallback ) continue;
			

			$update_data = array(
				'fba_quantity' => $fba_quantity,
				'fba_fcid'     => $fba_default_fcid,
			);

			// update listings table - by SKU
			// if ( $asin ) $listingsModel->updateWhere( array( 'asin' => $asin ), $update_data );
			if ( $sku  ) $listingsModel->updateWhere( array( 'sku'  => $sku  ), $update_data );

			// update quantity in WooCommerce - only if current stock level is less than FBA quantity
			if ( $listing_item = $listingsModel->getItemBySKU( $sku ) ) {

				$post_id = $listing_item->post_id;
				if ( $post_id ) {

					// update stock level - if lower than FBA
					$woo_stock = get_post_meta( $post_id, '_stock', true );
					if ( $woo_stock < $fba_quantity ) {
						update_post_meta( $post_id, '_stock', $fba_quantity );
						$woo_stock = $fba_quantity;

						// // update out of stock attribute
						// if ( $fba_quantity > 0 ) {
						// 	update_post_meta( $post_id, '_stock_status', 'instock' );
						// } else {
						// 	update_post_meta( $post_id, '_stock_status', 'outofstock' );
						// }

					}

					// update stock status based on actual stock - if required
					$woo_stock_status   = get_post_meta( $post_id, '_stock_status', true );
					$new_stock_status   = $woo_stock > 0 ? 'instock' : 'outofstock';
					if ( $new_stock_status != $woo_stock_status ) {
						update_post_meta( $post_id, '_stock_status', $new_stock_status );
					}

				} // if $post_id

			} // if $listing_item

		} // foreach report row

		// build response
		$response = new stdClass();
		$response->job  	= $job;
		$response->task 	= $task;
		$response->errors   = $errors;
		$response->success  = true;

		return $response;
	} // processFBAReportPage()


	// process single Quality report page
	public static function processQualityReportPage( $report, $rows, $job, $task ) {
		$listingsModel = new WPLA_ListingsModel();

		// reset quality info for all products using this account
		$account_id = $report->account_id;
		$update_data = array(
			'quality_status' => null,
			'quality_info'   => null,
		);
		$listingsModel->updateWhere( array( 'account_id' => $account_id ), $update_data );


        // process rows
		foreach ($rows as $row) {

			$asin         = $row['asin'];
			$sku          = $row['sku'];

			$quality_info = array(
				'sku'           => $row['sku'],
				'product-name'  => $row['product-name'],
				'asin'          => $row['asin'],
				'field-name'    => $row['field-name'],
				'alert-type'    => $row['alert-type'],
				'current-value' => $row['current-value'],
				'last-updated'  => $row['last-updated'],
				'alert-name'    => $row['alert-name'],
				'status'        => $row['status'],
				'explanation'   => $row['explanation'],
				'ts'            => time(),
			);

			$update_data = array(
				// 'quality_status' => $row['status'],
				'quality_status' => $row['alert-name'],
				'quality_info'   => serialize( $quality_info ),
			);

			if ( $asin ) $listingsModel->updateWhere( array( 'asin' => $asin ), $update_data );
			if ( $sku  ) $listingsModel->updateWhere( array( 'sku'  => $sku  ), $update_data );
		}

		// build response
		$response = new stdClass();
		$response->job  	= $job;
		$response->task 	= $task;
		$response->errors   = '';
		$response->success  = true;

		return $response;
	} // processQualityReportPage()


	// convert item-condition to condition_type: 11 => New
	public static function convertNumericConditionIdToType( $condition_id ) {

		$map = array( 
			1  => 'UsedLikeNew'           ,
			2  => 'UsedVeryGood'          ,
			3  => 'UsedGood'              ,
			4  => 'UsedAcceptable'        ,
			5  => 'CollectibleLikeNew'    ,
			6  => 'CollectibleVeryGood'   ,
			7  => 'CollectibleGood'       ,
			8  => 'CollectibleAcceptable' ,
			10 => 'Refurbished'           ,
			11 => 'New'					  ,
		);
		$amazon_condition_type = isset( $map[ $condition_id ] ) ? $map[ $condition_id ] : '';

		return $amazon_condition_type;		
	}


	// render import preview table
	public static function render_import_preview_table( $wpl_rows, $wpl_report_summary, $wpl_query = false, $wpl_pagenum = false ) {
	    if ( ! is_array($wpl_rows) || ( ! sizeof($wpl_rows) ) ) return; 
	    $row_count = 0;

	    include( WPLA_PATH . '/views/import/preview_import_table.php');

	} // render_import_preview_table()


} // class WPLA_ImportHelper
