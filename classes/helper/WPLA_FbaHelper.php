<?php

class WPLA_FbaHelper {
	
    static public function getRecentOrders() {

        // fetch orders - WC2.2+
        $orders = get_posts( array(
            'post_type'   => 'shop_order',
            'post_status' => array( 'wc-processing', 'wc-completed' ),

            'posts_per_page'   => -1,
            'orderby'          => 'post_modified_gmt',
            'order'            => 'ASC',

            'date_query' => array(
                array(
                    'column' => 'post_modified_gmt',
                    'after'  => '1 day ago',
                ),
            ),

        ) );

        return $orders;
    } // getRecentOrders()


    // create a new FBA submission feed for order
    static public function submitOrderToFBA( $post_id ) {

        // make sure we don't submit the same order twice (just a precaution)
        $status = get_post_meta( $post_id, '_wpla_fba_submission_status', true );
        if ( $status && $status != 'failed' ) return false; // should never happen - it might as well read: die('you are doing it wrong');

        // create FBA feed
        $feed = new WPLA_AmazonFeed();
        $feed->updateFbaSubmissionFeed( $post_id );

        // mark order as submitted (pending)
        update_post_meta( $post_id, '_wpla_fba_submission_status',   'pending' );

        $response = new stdClass();
        $response->success = true;

        return $response;
    } // submitOrderToFBA()


    // check if an order can be fulfilled via FBA
    // parameter: $post - a wp post object or post_id of an order
    static public function orderCanBeFulfilledViaFBA( $post ) {

        // make sure we have a wp post object
        if ( is_numeric($post) ) $post = get_post( $post );

        // check if this is an order created by WP-Lister for Amazon
        $amazon_order_id = get_post_meta( $post->ID, '_wpla_amazon_order_id', true );
        if ( $amazon_order_id ) return 'Order was placed on Amazon';

        // check if this order has already been submitted to FBA
        $submission_status = get_post_meta( $post->ID, '_wpla_fba_submission_status', true );
        if ( $submission_status == 'pending' ) {
            return __('This order is going to be submitted to Amazon and will be fulfilled via FBA.', 'wpla');
        }
        if ( $submission_status == 'success' ) {
            return __('This order has been successfully submitted to Amazon and will be fulfilled via FBA.', 'wpla');
        }
        if ( $submission_status == 'shipped' ) {
            return __('This order has been fulfilled by Amazon.', 'wpla');
        }
        if ( $submission_status == 'failed' ) {
            return __('<b>There was a problem submitting this order to be fulfilled by Amazon!</b>', 'wpla');
        }

        // skip cancelled and pending orders
        if ( ! in_array( $post->post_status, array( 'wc-processing', 'wc-completed', 'wc-on-hold' ) ) ) {
            return __('Order status is neither processing nor completed not on hold.', 'wpla');
        }

        // check if FBA is enabled (not really required)
        // if ( !  get_option( 'wpla_fba_enabled' ) ) return 'FBA support is disabled.';


        // get order and order items
        if ( ! function_exists('wc_get_order') ) return;
        $_order      = wc_get_order( $post->ID );
        $order_items = $_order->get_items();

        // check if destination country matches fulfillment center
        $fba_default_fcid = get_option( 'wpla_fba_fulfillment_center_id', 'AMAZON_NA' );
        if ( 'AMAZON_NA' == $fba_default_fcid ) {
            $allowed_countries = array( 'US' );
            if ( ! in_array( $_order->shipping_country, $allowed_countries ) ) {
                return __('Shipping destination is not within the US.', 'wpla');
            }
        } elseif ( 'AMAZON_EU' == $fba_default_fcid ) {
            $allowed_countries = array( 'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GR', 'HU', 'HR', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK' );
            if ( ! in_array( $_order->shipping_country, $allowed_countries ) ) {
                return __('Shipping destination is not within the EU.', 'wpla');
            }
        }

        // check if ordered items are available on FBA
        $items_available_on_fba     = array();
        $count_not_available_on_fba = 0;
        $lm = new WPLA_ListingsModel();
        foreach ( $order_items as $item ) {

            // skip tax and shipping rows
            if ( $item['type'] != 'line_item' ) continue;

            // find amazon listing
            $post_id = $item['variation_id'] ? $item['variation_id'] : $item['product_id'];
            $listing = $lm->getItemByPostID( $post_id );
            if ( ! $listing ) {
                $count_not_available_on_fba++;
                continue;
            }

            // check FBA inventory
            $fba_quantity = $listing->fba_quantity;
            if ( $fba_quantity > 0 ) {
                $listing->purchased_qty = $item['qty'];
                $items_available_on_fba[] = $listing;
            } else {
                $count_not_available_on_fba++;
            }

        } // each order line item


        if ( empty( $items_available_on_fba ) ) {
            $msg  = __('This order can not be fulfilled by Amazon.', 'wpla');
            return $msg;         
        }

        if ( $count_not_available_on_fba > 0 ) {
            $msg  = __('This order can not be fulfilled by Amazon.', 'wpla') . ' ';
            $msg .= __('Not all purchased items are currently available on FBA.', 'wpla');
            return $msg;         
        }

        // this order can be filfilled via FBA - return array of items
        return $items_available_on_fba;

    } // orderCanBeFulfilledViaFBA()


} // class WPLA_FbaHelper
