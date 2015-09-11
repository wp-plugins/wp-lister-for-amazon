<?php
/**
 * WPLA_ListingQueryHelper class
 *
 * provides static methods to query the amazon_listings table
 * 
 */

class WPLA_ListingQueryHelper {

    const TABLENAME = 'amazon_listings';



    // get all items eligible for having their price matched to the lowest price (up or down)
    // items need to have a min_price, max_price and a lowest price set
    static function getItemsWithMinMaxAndLowestPrice() {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLENAME;
        // $repricing_margin    = floatval( get_option('wpla_repricing_margin') );

        $items = $wpdb->get_results("
            SELECT id, post_id, price, min_price, max_price, lowest_price, compet_price, buybox_price, loffer_price, has_buybox, sku
            FROM $table
            WHERE min_price > 0
              AND max_price > 0
              AND lowest_price IS NOT NULL
              AND lowest_price > 0
        ");
        // AND price > ( lowest_price - $repricing_margin )

        return $items;
    }

    // get all items with min/max prices but without lowest price - where price is lower than max_price
    static function getItemsWithoutLowestPriceButPriceLowerThanMaxPrice() {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLENAME;

        $items = $wpdb->get_results("
            SELECT id, post_id, price, min_price, max_price, lowest_price, compet_price, buybox_price, loffer_price, has_buybox, sku
            FROM $table
            WHERE min_price > 0
              AND max_price > 0
              AND ( lowest_price = 0  OR  lowest_price IS NULL )
              AND price < max_price
        ");

        return $items;
    }






    // get all items due for a pricing update - by account_id
    // called by WPLA_CronActions::action_update_pricing_info()
    static function getItemsDueForPricingUpdateForAcccount( $account_id, $limit = 20 ) {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLENAME;

        // check expiry time - return empty array if updates are off
        $hours       = get_option( 'wpla_pricing_info_expiry_time', 24 );
        if ( ! $hours || ! is_numeric($hours) ) return array();

        $n_hours_ago = date('Y-m-d H:i:s', time() - 3600 * $hours );
        $items = $wpdb->get_results( $wpdb->prepare("
            SELECT *
            FROM $table
            WHERE       account_id = %d
              AND           status = 'online'
              AND             asin IS NOT NULL
              AND ( product_type <> 'variable' OR product_type IS NULL )
              AND ( pricing_date  < %s         OR pricing_date IS NULL )
            ORDER BY pricing_date ASC
            LIMIT %d
        ", 
        $account_id,
        $n_hours_ago,
        $limit
        ), OBJECT_K);

            // doesn't work if PHP and MySQL use different time zones...
            //AND pricing_date < DATE_SUB( NOW(), INTERVAL 1 HOUR )

        return $items;
    }


    // find items which are linked to a product which does not exist in WooCommerce
    static function findMissingProducts() {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLENAME;

        $items = $wpdb->get_results("
            SELECT al.id, al.post_id, al.listing_title, al.sku, al.asin, al.price, al.quantity 
            FROM $table al
            LEFT JOIN {$wpdb->posts} p ON al.post_id = p.ID
            WHERE p.ID IS NULL
            ORDER BY id DESC
        ", OBJECT_K);
        // echo "<pre>";print_r($items);echo"</pre>";#die();

        return $items;
    } // findMissingProducts()


} // class WPLA_ListingQueryHelper
