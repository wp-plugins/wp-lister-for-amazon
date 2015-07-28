<?php

class WPLA_OrdersImporter {
	
	var $account;
	// var $logger;
	public $result;
	public $updated_count = 0;
	public $imported_count = 0;

	const TABLENAME = 'amazon_orders';

	public function __construct() {
		// global $wpla_logger;
		// $this->logger = &$wpla_logger;
	}

	public function importOrder( $order, $account ) {
		global $wpdb;
		$table = $wpdb->prefix . self::TABLENAME;

		// if ( ! is_object($order) )
		// 	echo "<pre>order is not an object: ";print_r($order);echo"</pre>";die();

		$data = array(
			'order_id'             => $order->AmazonOrderId,
			'status'               => $order->OrderStatus,
			// pending orders are missing some details
			'total'                => isset( $order->OrderTotal->Amount ) ? $order->OrderTotal->Amount : '',
			'currency'             => isset( $order->OrderTotal->CurrencyCode ) ? $order->OrderTotal->CurrencyCode : '',
			'buyer_name'           => isset( $order->BuyerName ) ? $order->BuyerName : '',
			'buyer_email'          => isset( $order->BuyerEmail ) ? $order->BuyerEmail : '',
			'PaymentMethod'        => isset( $order->PaymentMethod ) ? $order->PaymentMethod : '',
			'ShippingAddress_City' => isset( $order->ShippingAddress->City ) ? $order->ShippingAddress->City : '',
			'date_created'         => $this->convertIsoDateToSql( $order->PurchaseDate ),
			'LastTimeModified'     => $this->convertIsoDateToSql( $order->LastUpdateDate ),
			'account_id'		   => $account->id,
			'details'			   => json_encode( $order )
		);

		// fetch order line items from Amazon - required for both new and updated orders
		$this->api     = new WPLA_AmazonAPI( $account->id );
		$items         = $this->api->getOrderLineItems( $order->AmazonOrderId );
		$data['items'] = maybe_serialize( $items );


		// check if order exists in WPLA
		if ( $id = $this->order_id_exists( $order->AmazonOrderId ) ) {

			// load existing order record from wp_amazon_orders 
			$ordersModel = new WPLA_OrdersModel();
			$wpla_order  = $ordersModel->getItem( $id );

			// check if order status was updated
			// if pending -> Canceled: revert stock reduction by processing history records
			// if pending -> Shipped / Unshipped: create WooCommerce order if enabled (done in createOrUpdateWooCommerceOrder())
			if ( $order->OrderStatus != $wpla_order['status'] ) {

				$old_order_status = $wpla_order['status'];
				$new_order_status = $order->OrderStatus;

				// add history record
				$history_message = "Order status has changed from ".$old_order_status." to ".$new_order_status;
				$history_details = array( 'id' => $id, 'new_status' => $new_order_status, 'old_status' => $old_order_status, 'LastTimeModified' => $data['LastTimeModified'] );
				$this->addHistory( $data['order_id'], 'order_status_changed', $history_message, $history_details );

				// if pending -> Canceled: revert stock reduction by processing history records
				if ( ( $old_order_status == 'Pending' ) && ( $new_order_status == 'Canceled' ) ) {

					// revert stock reduction
					$this->revertStockReduction( $wpla_order );

					// add history record
					$history_message = "Stock levels have been replenished";
					$history_details = array( 'id' => $id );
					$this->addHistory( $data['order_id'], 'revert_stock', $history_message, $history_details );

				}

			} // if status changed

			// update existing order
			$wpdb->update( $table, $data, array( 'order_id' => $order->AmazonOrderId ) );			
			$this->updated_count++;

			// TODO: update WooCommerce order!

			// add history record
			$history_message = "Order details were updated - ".$data['LastTimeModified'];
			$history_details = array( 'id' => $id, 'status' => $data['status'], 'LastTimeModified' => $data['LastTimeModified'] );
			$this->addHistory( $data['order_id'], 'order_updated', $history_message, $history_details );

		} else {

			// insert new order
			$wpdb->insert( $table, $data );
			$this->imported_count++;
			$id = $wpdb->insert_id;
			echo $wpdb->last_error;

			// add history record
			$history_message = "Order was added with status: ".$data['status'];
			$history_details = array( 'id' => $id, 'status' => $data['status'], 'LastTimeModified' => $data['LastTimeModified'] );
			$this->addHistory( $data['order_id'], 'order_inserted', $history_message, $history_details );

			// process ordered items - unless order has been cancelled
			if ( $data['status'] != 'Canceled') {
				foreach ($items as $item) {
					// process each item and reduce stock level
					$success = $this->processListingItem( $item, $order );
				}
			}

		} // if order does not exist



		return $id;
	}


	// revert stock reduction by processing history records
	function revertStockReduction( $wpla_order ) {
		global $wpdb;

		if ( ! is_array( $wpla_order['history'] ) ) return;

		foreach ( $wpla_order['history'] as $history_record ) {
			
			// filter reduce_stock actions
			if ( $history_record->action != 'reduce_stock' ) continue;

			// make sure purchased qty was recorded (since 0.9.2.8)
			$details = $history_record->details;
			if ( ! isset( $details['qty_purchased'] ) ) continue;
			$quantity_purchased = $details['qty_purchased'];

			// handle non-FBA quantity
			if ( isset( $details['quantity'] ) && isset( $details['sku'] ) ) {

				// get listing item
				$lm = new WPLA_ListingsModel();
				$listing = $lm->getItemBySKU( $details['sku'] );

				// update quantity for FBA orders
				$quantity      = $listing->quantity      + $quantity_purchased;
				$quantity_sold = $listing->quantity_sold - $quantity_purchased;

				$wpdb->update( $wpdb->prefix.'amazon_listings', 
					array( 
						'quantity'  => $quantity,
						'quantity_sold' => $quantity_sold 
					), 
					array( 'sku' => $details['sku'] ) 
				);

			}

			// handle FBA quantity
			if ( isset( $details['fba_quantity'] ) && isset( $details['sku'] ) ) {

				// get listing item
				$lm = new WPLA_ListingsModel();
				$listing = $lm->getItemBySKU( $details['sku'] );

				// update quantity for FBA orders
				$fba_quantity  = $listing->fba_quantity  + $quantity_purchased;
				$quantity_sold = $listing->quantity_sold - $quantity_purchased;

				$wpdb->update( $wpdb->prefix.'amazon_listings', 
					array( 
						'fba_quantity'  => $fba_quantity,
						'quantity_sold' => $quantity_sold 
					), 
					array( 'sku' => $details['sku'] ) 
				);

			}

			// handle WooCommerce quantity
			if ( isset( $details['product_id'] ) ) {

				// increase product stock
				$post_id = $details['product_id'];
				$newstock = WPLA_ProductWrapper::increaseStockBy( $post_id, $quantity_purchased, $wpla_order['order_id'] );
				WPLA()->logger->info( 'increased product stock for #'.$post_id.' by '.$quantity_purchased.' - new qty: '.$newstock );

				// notify WP-Lister for eBay (and other plugins)
				do_action( 'wpla_inventory_status_changed', $post_id );

			}


		} // each history record

	} // revertStockReduction()

	// update listing sold quantity and status
	function processListingItem( $item, $order ) {
		global $wpdb;

		// abort if item data is invalid
		if ( ! isset( $item->ASIN ) && ! isset( $item->QuantityOrdered ) ) {
			$history_message = "Error fetching order line items - request throttled?";
			$history_details = array();
			$this->addHistory( $order->AmazonOrderId, 'request_throttled', $history_message, $history_details );
			return false;
		}

		$order_id           = $order->AmazonOrderId;
		$asin               = $item->ASIN;
		$sku                = $item->SellerSKU;
		$quantity_purchased = $item->QuantityOrdered;
		
		// get listing item
		$lm = new WPLA_ListingsModel();
		$listing = $lm->getItemBySKU( $sku );

		// skip if this listing does not exist in WP-Lister
		if ( ! $listing ) {
			$history_message = "Skipped unknown SKU {$sku} ({$asin})";
			$history_details = array( 'sku' => $sku, 'asin' => $asin );
			$this->addHistory( $order_id, 'skipped_item', $history_message, $history_details );
			return true;
		}


		// handle FBA orders
		if ( $order->FulfillmentChannel == 'AFN' ) {

			// update quantity for FBA orders
			$fba_quantity  = $listing->fba_quantity  - $quantity_purchased;
			$quantity_sold = $listing->quantity_sold + $quantity_purchased;

			$wpdb->update( $wpdb->prefix.'amazon_listings', 
				array( 
					'fba_quantity'  => $fba_quantity,
					'quantity_sold' => $quantity_sold 
				), 
				array( 'sku' => $sku ) 
			);

			// add history record
			$history_message = "FBA quantity reduced by $quantity_purchased for listing {$sku} ({$asin}) - FBA stock $fba_quantity ($quantity_sold sold)";
			$history_details = array( 'fba_quantity' => $fba_quantity, 'sku' => $sku, 'asin' => $asin, 'qty_purchased' => $quantity_purchased, 'listing_id' => $listing->id );
			$this->addHistory( $order_id, 'reduce_stock', $history_message, $history_details );

		} else {

			// update quantity for non-FBA orders
			$quantity_total = $listing->quantity      - $quantity_purchased;
			$quantity_sold  = $listing->quantity_sold + $quantity_purchased;
			$wpdb->update( $wpdb->prefix.'amazon_listings', 
				array( 
					'quantity'      => $quantity_total,
					'quantity_sold' => $quantity_sold 
				), 
				array( 'sku' => $sku ) 
			);

			// add history record
			$history_message = "Quantity reduced by $quantity_purchased for listing {$sku} ({$asin}) - new stock: $quantity_total ($quantity_sold sold)";
			$history_details = array( 'newstock' => $quantity_total, 'sku' => $sku, 'asin' => $asin, 'qty_purchased' => $quantity_purchased, 'listing_id' => $listing->id );
			$this->addHistory( $order_id, 'reduce_stock', $history_message, $history_details );

		}



		// mark listing as sold when last item is sold
		// if ( $quantity_total == 0 ) {
		// 	$wpdb->update( $wpdb->prefix.'amazon_listings', 
		// 		array( 'status' => 'sold', 'date_finished' => $data['date_created'], ), 
		// 		array( 'sku' => $sku ) 
		// 	);
		// 	WPLA()->logger->info( 'marked item '.$sku.' as SOLD ');
		// }



		return true;
	} // processListingItem()



	// add order history entry
	function addHistory( $order_id, $action, $msg, $details = array(), $success = true ) {
		global $wpdb;

		$table = $wpdb->prefix . self::TABLENAME;

		// build history record
		$record = new stdClass();
		$record->action  = $action;
		$record->msg     = $msg;
		$record->details = $details;
		$record->success = $success;
		$record->time    = time();

		// load history
		$history = $wpdb->get_var( "
			SELECT history
			FROM $table
			WHERE order_id = '$order_id'
		" );

		// init with empty array
		$history = maybe_unserialize( $history );
		if ( ! $history ) $history = array();

		// prevent fatal error if $history is not an array
		if ( ! is_array( $history ) ) {
			WPLA()->logger->error( "invalid history value in OrdersImporter::addHistory(): ".$history);

			// build history record
			$rec = new stdClass();
			$rec->action  = 'reset_history';
			$rec->msg     = 'Corrupted history data was cleared';
			$rec->details = array();
			$rec->success = 'ERROR';
			$rec->time    = time();

			$history = array();
			$history[] = $record;
		}

		// add record
		$history[] = $record;

		// update history
		$history = serialize( $history );
		$wpdb->query( "
			UPDATE $table
			SET history = '$history'
			WHERE order_id = '$order_id'
		" );

	}


	/*
	// decrease stock quantity for WooCommerce product
	static function decreaseStockBy( $post_id, $by, $VariationSpecifics = array(), $order_id = false ) {

		if ( count( $VariationSpecifics ) == 0 ) {
			$product = self::getProduct( $post_id );
		} else {
			$variation_id = self::findVariationID( $post_id, $VariationSpecifics );
			$product = self::getProduct( $variation_id, true );

			// add history record
			if ( $order_id ) {
				$om = new WPLA_OrdersModel();
				// $history_message = "Stock reduced by $by for variation #$variation_id";
				// $history_details = array( 'variation_id' => $variation_id );
				// $om->addHistory( $order_id, 'reduce_stock', $history_message, $history_details );			
			}

		}
		if ( ! $product ) return false;

		// patch backorders product config unless backorders were enabled in settings
		if ( $product->backorders_allowed() ) {
			if ( get_option( 'wpla_allow_backorders', 0 ) == 1 ) {
				$product->backorders = 'no';
			} elseif ( $order_id ) {
				$om = new WPLA_OrdersModel();
				// $history_message = "Warning: backorders are enabled for product #$post_id";
				// $history_details = array( 'post_id' => $post_id );
				// $om->addHistory( $order_id, 'backorders_allowed', $history_message, $history_details );							
			}
		}

		// check if stock management is enabled for product
		if ( $product->managing_stock() ) {		
			// if yes, call reduce_stock()
			$stock = $product->reduce_stock( $by );
		}

		// // check if stock management is enabled for product
		// if ( ! $product->managing_stock() && ! $product->backorders_allowed() ) {		
		// 	// if not, just mark it as out of stock
		// 	update_post_meta($product->id, '_stock_status', 'outofstock');
		// 	$stock = 0;
		// } else {
		// 	// if yes, call reduce_stock()
		// 	$stock = $product->reduce_stock( $by );
		// }

		return $stock;
	}	
	*/

	public function importOrders( $orders, $account ) {

		// $this->api     = new WPLA_AmazonAPI( $account->id );
		// $this->account = $account;

		foreach ( $orders as $order ) {
			$this->importOrder( $order, $account );
		}

	}

	public function importOrderItems( $items, $order_id ) {
		global $wpdb;
		$table = $wpdb->prefix . self::TABLENAME;

		// echo "<pre>";print_r($order_id);echo"</pre>";#die();
		// echo "<pre>";print_r($items);echo"</pre>";#die();

		$data = array(
			'items'			   => maybe_serialize( $items )
		);

		$wpdb->update( $table, $data, array( 'order_id' => $order_id ) );
		echo $wpdb->last_error;
	}

	function order_id_exists( $order_id ) {
		global $wpdb;
		$table = $wpdb->prefix . self::TABLENAME;

		$id = $wpdb->get_var( "
			SELECT id
			FROM $table
			WHERE order_id = '$order_id'
		" );

		return $id;
	}

	// convert 2013-02-14T08:00:58.000Z to 2013-02-14 08:00:58
	public function convertIsoDateToSql( $iso_date ) {
		$search = array( 'T', '.000Z' );
		$replace = array( ' ', '' );
		$sql_date = str_replace( $search, $replace, $iso_date );
		return $sql_date;
	}



}

