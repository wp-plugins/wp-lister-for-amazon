<?php
/**
 * hooks to alter the WooCommerce backend
 */

class WPLA_WooBackendIntegration extends WPLA_Core {

	function __construct() {

		// custom column for products table
		add_filter( 'manage_edit-product_columns', array( &$this, 'wpla_woocommerce_edit_product_columns' ), 11 );
		add_action( 'manage_product_posts_custom_column', array( &$this, 'wpla_woocommerce_custom_product_columns' ), 3 );

		// custom column for orders table
		add_filter('manage_edit-shop_order_columns', array( &$this, 'wpla_woocommerce_edit_shop_order_columns' ), 11 );
		add_action('manage_shop_order_posts_custom_column', array( &$this, 'wpla_woocommerce_custom_shop_order_columns' ), 3 );

		// hook into save_post to mark listing as changed when a product is updated
		add_action( 'save_post', array( &$this, 'wpla_on_woocommerce_product_quick_edit_save' ), 20, 2 );
		add_action( 'save_post', array( &$this, 'wpla_on_woocommerce_product_bulk_edit_save' ), 20, 2 );

		// show messages when listing was updated from edit product page
		add_action( 'post_updated_messages', array( &$this, 'wpla_product_updated_messages' ), 20, 1 );

		// show errors for products and orders
		add_action( 'admin_notices', array( &$this, 'wpla_product_admin_notices' ), 20 );
		add_action( 'admin_notices', array( &$this, 'wpla_order_admin_notices' ), 20 );

		// custom views for products table
		add_filter( 'parse_query', array( &$this, 'wpla_woocommerce_admin_product_filter_query' ) );
		add_filter( 'views_edit-product', array( &$this, 'wpla_add_woocommerce_product_views' ) );

		// custom views for orders table
		add_filter( 'parse_query', array( &$this, 'wpla_woocommerce_admin_order_filter_query' ) );
		add_filter( 'views_edit-shop_order', array( &$this, 'wpla_add_woocommerce_order_views' ) );

		// submitbox actions
		add_action( 'post_submitbox_misc_actions', array( &$this, 'wpla_product_submitbox_misc_actions' ), 100 );
		add_action( 'woocommerce_process_product_meta', array( &$this, 'wpla_product_handle_submitbox_actions' ), 100, 2 );

		// hook into WooCommerce orders to create product objects for amazon listings (debug)
		// add_action( 'woocommerce_order_get_items', array( &$this, 'wpla_woocommerce_order_get_items' ), 10, 2 );
		add_filter( 'woocommerce_get_product_from_item', array( &$this, 'wpla_woocommerce_get_product_from_item' ), 10, 3 );

		// prevent WooCommerce from sending out notification emails when updating order status manually
		if ( get_option( 'wpla_disable_changed_order_emails', 1 ) ) {
			// add_filter( 'woocommerce_email_enabled_new_order', array( $this, 'check_order_email_enabled' ), 10, 2 ); // disabled as this would *always* prevent admin new order emails for Amazon orders
			add_filter( 'woocommerce_email_enabled_customer_completed_order', array( $this, 'check_order_email_enabled' ), 10, 2 );
			add_filter( 'woocommerce_email_enabled_customer_processing_order', array( $this, 'check_order_email_enabled' ), 10, 2 );		
		}

	}


	/**
	 * prevent WooCommerce from sending out notification emails when updating order status for Amazon orders manually
	 **/
	function check_order_email_enabled( $enabled, $order ){
		if ( ! is_object($order) ) return $enabled;

		// check if this order was imported from Amazon
		if ( get_post_meta( $order->id, '_wpla_amazon_order_id', true ) ) {
			return false;
		}

		return $enabled;
	}



	/**
	 * fix order line items
	 **/
	// add_filter('woocommerce_get_product_from_item', 'wpla_woocommerce_get_product_from_item', 10, 2 );

	function wpla_woocommerce_get_product_from_item( $_product, $item, $order ){
		// global $wpla_logger;

		// $wpla_logger->info('wpla_woocommerce_get_product_from_item - item: '.print_r($item,1));
		// $wpla_logger->info('wpla_woocommerce_get_product_from_item - _product: '.print_r($_product,1));
		// $wpla_logger->info('wpla_woocommerce_get_product_from_item - order: '.print_r($order,1));

		// if this is not a valid WC product object, post processing, email generation or refunds might fail
		if ( ! $_product ) {

			// check if this order was created by WP-Lister
			if ( get_post_meta( $order->id, '_wpla_amazon_order_id', true ) ) {

				// create a new amazon product object to allow email templates or other plugins to do $_product->get_sku() and more...
				$_product = new WC_Product_Amazon( $item['product_id'] );
				// $wpla_logger->info('wpla_woocommerce_get_product_from_item - NEW _product: '.print_r($_product,1));

			}

		}

		return $_product;
	}

	/**
	 * debug order line items
	 **/
	// add_filter('woocommerce_order_get_items', 'wpla_woocommerce_order_get_items', 10, 2 );

	function wpla_woocommerce_order_get_items( $items, $order ){
		global $wpla_logger;
		$wpla_logger->info('wpla_woocommerce_order_get_items - items: '.print_r($items,1));
		// $wpla_logger->info('wpla_woocommerce_order_get_items - order: '.print_r($order,1));
	}


	/**
	 * Columns for Products page
	 **/
	// add_filter('manage_edit-product_columns', 'wpla_woocommerce_edit_product_columns', 11 );

	function wpla_woocommerce_edit_product_columns($columns){
		
		// $columns['listed_on_amazon'] = '<img src="'.WPLA_URL.'/img/amazon-dark-16x16.png" title="'.__('Listing status', 'wpla').'" />';		
		$columns['listed_on_amazon'] = '<img src="'.WPLA_URL.'/img/amazon-16x16.png" title="'.__('Listing status', 'wpla').'" />';		
		return $columns;
	}


	/**
	 * Custom Columns for Products page
	 **/
	// add_action('manage_product_posts_custom_column', 'wpla_woocommerce_custom_product_columns', 3 );

	function wpla_woocommerce_custom_product_columns( $column ) {
		global $post, $woocommerce, $the_product;

		if ( empty( $the_product ) || $the_product->id != $post->ID ) {
			$the_product = get_product( $post );
		}

		switch ($column) {
			case 'listed_on_amazon' :

				// default status is unmatched
				$status = 'unmatched';

				// $item_source = get_post_meta( $post->ID, '_amazon_item_source', true );
				// if ( ! $item_source ) return;
				// $asin = get_post_meta( $post->ID, '_wpla_asin', true );
				// $asin = $listingsModel->getASINFromPostID( $post->ID );
				// if ( $asin ) $status = 'online';

				// get listing item
				$listingsModel = new WPLA_ListingsModel();
				$item = $listingsModel->getItemByPostID( $post->ID );
				if ( ! $item ) {
					$listings = $listingsModel->getAllItemsByParentID( $post->ID );
					$item = $listings ? reset($listings) : false;
				}
				if ( $item ) {
					$asin   = $item->asin;	
					$status = $item->status;

					// get proper amazon_url
			        if ( $asin && $item->account_id ) {
			            $account = new WPLA_AmazonAccount( $item->account_id );
			            $market  = new WPLA_AmazonMarket( $account->market_id );
			            $amazon_url = 'http://www.'.$market->url.'/dp/'.$item->asin.'/';
			        }
				} 

				switch ($status) {
					case 'online':
					case 'changed':
						$amazon_url = isset($amazon_url) ? $amazon_url : 'http://www.amazon.com/dp/'.$asin;
						echo '<a href="'.$amazon_url.'" title="'.__('View on Amazon','wpla').'" target="_blank"><img src="'.WPLA_URL.'/img/amazon-16x16.png" alt="yes" /></a>';
						break;
					
					case 'matched':
					case 'prepared':
						echo '<img src="'.WPLA_URL.'/img/amazon-orange-16x16.png" class="tips" data-tip="'.__('This product is scheduled to be submitted to Amazon.','wpla').'" />';
						break;
					
					case 'failed':
						echo '<img src="'.WPLA_URL.'/img/amazon-red-16x16.png" class="tips" data-tip="'.__('There was a problem submitting this product to Amazon.','wpla').'" />';
						break;
					
					case 'unmatched':
						if ( $the_product->product_type == 'variable' ) {
							$msg = 'Variable products can only be matched on the edit product page where you need to select an ASIN for each variation.';
							echo '<a href="#" onclick="alert(\''.$msg.'\');return false;" title="'.__('Match on Amazon','wpla').'"><img src="'.WPLA_URL.'/img/search3.png" alt="match" /></a>';
						} elseif ( $the_product->post->post_status == 'draft' ) {
							$msg = 'This product is a draft. You need to publish your product before you can list it on Amazon.';
							echo '<a href="#" onclick="alert(\''.$msg.'\');return false;" title="'.__('Match on Amazon','wpla').'"><img src="'.WPLA_URL.'/img/search3.png" alt="match" /></a>';
						} else {
							$tb_url = 'admin-ajax.php?action=wpla_show_product_matches&id='.$post->ID.'&width=640&height=420';
							echo '<a href="'.$tb_url.'" class="thickbox" title="'.__('Match on Amazon','wpla').'"><img src="'.WPLA_URL.'/img/search3.png" alt="match" /></a>';
						}
						break;
					
					default:
						// echo '<img src="'.WPLA_URL.'/img/search3.png" title="unmatched" />';
						break;
				}

			break;

		} // switch ($column)

	}


	// hook into save_post to mark listing as changed when a product is updated
	function wpla_on_woocommerce_product_quick_edit_save( $post_id, $post ) {

		if ( !$_POST ) return $post_id;
		if ( is_int( wp_is_post_revision( $post_id ) ) ) return;
		if ( is_int( wp_is_post_autosave( $post_id ) ) ) return;
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
		// if ( !isset($_POST['woocommerce_quick_edit_nonce']) || (isset($_POST['woocommerce_quick_edit_nonce']) && !wp_verify_nonce( $_POST['woocommerce_quick_edit_nonce'], 'woocommerce_quick_edit_nonce' ))) return $post_id;
		if ( !current_user_can( 'edit_post', $post_id )) return $post_id;
		if ( $post->post_type != 'product' ) return $post_id;

		// global $woocommerce, $wpdb;
		// $product = self::getProduct( $post_id );

		// don't mark as changed when listing has been revised earlier in this request
		// if ( isset( $_POST['wpla_amazon_revise_on_update'] ) ) return;

		$lm = new WPLA_ListingsModel();
		$lm->markItemAsModified( $post_id );

		// Clear transient
		// $woocommerce->clear_product_transients( $post_id );
	}
	// add_action( 'save_post', 'wpla_on_woocommerce_product_quick_edit_save', 20, 2 );

	// hook into save_post to mark listing as changed when a product is updated via bulk update
	function wpla_on_woocommerce_product_bulk_edit_save( $post_id, $post ) {

		if ( is_int( wp_is_post_revision( $post_id ) ) ) return;
		if ( is_int( wp_is_post_autosave( $post_id ) ) ) return;
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
		if ( ! isset( $_REQUEST['woocommerce_bulk_edit_nonce'] ) || ! wp_verify_nonce( $_REQUEST['woocommerce_bulk_edit_nonce'], 'woocommerce_bulk_edit_nonce' ) ) return $post_id;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return $post_id;
		if ( $post->post_type != 'product' ) return $post_id;

		// $lm = new WPLA_ListingsModel();
		// $lm->markItemAsModified( $post_id );
		do_action( 'wpla_product_has_changed', $post_id );

	}
	// add_action( 'save_post', 'wpla_on_woocommerce_product_bulk_edit_save', 10, 2 );


	// filter the products in admin based on amazon status
	// add_filter( 'parse_query', 'wpla_woocommerce_admin_product_filter_query' );
	function wpla_woocommerce_admin_product_filter_query( $query ) {
		global $typenow, $wp_query, $wpdb;

	    if ( $typenow == 'product' ) {

	    	// filter by amazon status
	    	if ( ! empty( $_GET['is_on_amazon'] ) ) {

	        	// find all products that are already on amazon
	        	$sql = "
	        			SELECT {$wpdb->prefix}posts.ID 
	        			FROM {$wpdb->prefix}posts 
					    LEFT JOIN {$wpdb->prefix}amazon_listings
					         ON ( {$wpdb->prefix}posts.ID = {$wpdb->prefix}amazon_listings.post_id )
						    WHERE {$wpdb->prefix}amazon_listings.status = 'online'
						       OR {$wpdb->prefix}amazon_listings.status = 'changed'
	        	";
	        	$post_ids_on_amazon = $wpdb->get_col( $sql );
	        	// echo "<pre>";print_r($post_ids_on_amazon);echo"</pre>";#die();

	        	// find all products that hidden from amazon
	        	/*
	        	$sql = "
	        			SELECT post_id 
	        			FROM {$wpdb->prefix}postmeta 
					    WHERE meta_key   = '_amazon_hide_from_unlisted'
					      AND meta_value = 'yes'
	        	";
	        	$post_ids_hidden_from_amazon = $wpdb->get_col( $sql );
	        	*/
	        	$post_ids_hidden_from_amazon = array();
	        	// echo "<pre>";print_r($post_ids_hidden_from_amazon);echo"</pre>";#die();


		    	if ( $_GET['is_on_amazon'] == 'yes' ) {

					// combine arrays
					$post_ids = array_diff( $post_ids_on_amazon, $post_ids_hidden_from_amazon );
		        	// echo "<pre>";print_r($post_ids);echo"</pre>";die();

		        	if ( is_array($post_ids) && ( sizeof($post_ids) > 0 ) ) {
			        	if ( ! empty( $query->query_vars['post__in'] ) ) {
				        	$query->query_vars['post__in'] = array_intersect( $query->query_vars['post__in'], $post_ids );
			        	} else {
				        	$query->query_vars['post__in'] = $post_ids;
			        	}
		        	}

		        } elseif ( $_GET['is_on_amazon'] == 'no' ) {

					// combine arrays
					$post_ids = array_merge( $post_ids_on_amazon, $post_ids_hidden_from_amazon );
		        	// echo "<pre>";print_r($post_ids);echo"</pre>";die();

		        	if ( is_array($post_ids) && ( sizeof($post_ids) > 0 ) ) {
			        	// $query->query_vars['post__not_in'] = $post_ids;
			        	$query->query_vars['post__not_in'] = array_merge( $query->query_vars['post__not_in'], $post_ids );
		        	}

		        	// $query->query_vars['meta_value'] 	= null;
		        	// $query->query_vars['meta_key'] 		= '_wpla_asin';

		        	// $query->query_vars['meta_query'] = array(
					// 	'relation' => 'OR',
					// 	array(
					// 		'key' => '_wpla_asin',
					// 		'value' => ''
					// 	),
					// 	array(
					// 		'key' => '_wpla_asin',
					// 		'value' => '',
					// 		'compare' => 'NOT EXISTS'
					// 	)
					// );

		        }
	        }

		}

	}

	// # debug final query
	// add_filter( 'posts_results', 'wpla_woocommerce_admin_product_filter_posts_results' );
	// function wpla_woocommerce_admin_product_filter_posts_results( $posts ) {
	// 	global $wp_query;
	// 	echo "<pre>";print_r($wp_query->request);echo"</pre>";#die();
	// 	return $posts;
	// }

	// add custom view to woocommerce products table
	// add_filter( 'views_edit-product', 'wpla_add_woocommerce_product_views' );
	function wpla_add_woocommerce_product_views( $views ) {
		global $wp_query;

		if ( ! current_user_can('edit_others_pages') ) return $views;

		// On Amazon
		// $class = ( isset( $wp_query->query['is_on_amazon'] ) && $wp_query->query['is_on_amazon'] == 'no' ) ? 'current' : '';
		$class = ( isset( $_REQUEST['is_on_amazon'] ) && $_REQUEST['is_on_amazon'] == 'yes' ) ? 'current' : '';
		$query_string = esc_url_raw( remove_query_arg(array( 'is_on_amazon' )) );
		$query_string = add_query_arg( 'is_on_amazon', urlencode('yes'), $query_string );
		$views['listed_on_amazon'] = '<a href="'. $query_string . '" class="' . $class . '">' . __('On Amazon', 'wpla') . '</a>';

		// Not on Amazon
		$class = ( isset( $_REQUEST['is_on_amazon'] ) && $_REQUEST['is_on_amazon'] == 'no' ) ? 'current' : '';
		$query_string = esc_url_raw( remove_query_arg(array( 'is_on_amazon' )) );
		$query_string = add_query_arg( 'is_on_amazon', urlencode('no'), $query_string );
		$views['unlisted_on_amazon'] = '<a href="'. $query_string . '" class="' . $class . '">' . __('Not on Amazon', 'wpla') . '</a>';

		// debug query
		// $views['unlisted'] .= "<br>".$wp_query->request."<br>";

		return $views;
	}




	/**
	 * Output product update options.
	 *
	 * @access public
	 * @return void
	 */
	// add_action( 'post_submitbox_misc_actions', 'wpla_product_submitbox_misc_actions', 100 );
	function wpla_product_submitbox_misc_actions() {
		global $post;
		global $woocommerce;

		if ( $post->post_type != 'product' )
			return;

		// handle variable products differently
		$_product = get_product( $post->ID );
		if ( $_product->product_type == 'variable' ) {
			return $this->display_submitbox_for_variable_product( $_product );
		}

		// if product has been imported from amazon...
		// $this->wpla_product_submitbox_imported_status();
		// echo "<pre>";print_r($post->ID);echo"</pre>";

		// check listing status
		$listingsModel = new WPLA_ListingsModel();
		$status = $listingsModel->getStatusFromPostID( $post->ID );
		if ( ! in_array($status, array('online','changed','prepared','matched','submitted','failed') ) ) return;
		// echo "<pre>";print_r($status);echo"</pre>";

		// get item
		$item = $listingsModel->getItemByPostID( $post->ID );
		if ( ! $item ) return;

		// warn when changing the SKU for a published item
		if ( in_array($status, array('online','changed','submitted') ) ) $this->add_js_to_prevent_changing_sku();

        // show locked indicator
        // if ( @$item->locked ) {
        //     $tip_msg = 'This listing is currently locked.<br>Only inventory changes will be updated, other changes will be ignored.';
        //     $img_url = WPLA_URL . '/img/lock-1.png';
        //     $locktip = '<img src="'.$img_url.'" style="height:11px; padding:0;" class="tips" data-tip="'.$tip_msg.'"/>&nbsp;';
        // } 

		// get proper amazon_url
        if ( $item->asin && $item->account_id ) {
            $account = new WPLA_AmazonAccount( $item->account_id );
            $market  = new WPLA_AmazonMarket( $account->market_id );
            $amazon_url = 'http://www.'.$market->url.'/dp/'.$item->asin.'/';
        } else {
			$amazon_url = 'http://www.amazon.com/dp/'.$item->asin; 
        }

		?>
		
		<style type="text/css">
		</style>

		<div class="misc-pub-section" id="wpla-submit-options">
			<!-- <input type="hidden" name="wpla_amazon_listing_id" value="<?php echo $item->id ?>" /> -->

			<?php _e( 'Amazon listing is', 'wpla' ); ?>
				<b><?php echo $item->status; ?></b>
				<?php if ( ! in_array( $item->status, array('prepared','failed') ) ) : ?>
				<a href="<?php echo $amazon_url ?>" target="_blank" style="float:right;">
					<?php echo __('View', 'wpla') ?>
				</a>
				<?php endif; ?>
			<br>

		</div>
		<?php
	} // wpla_product_submitbox_misc_actions()

	// display submitbox content for variable product
	function display_submitbox_for_variable_product( $_product ) {
		global $post;
		global $woocommerce;

		// get all listings for this post_id
		$listingsModel = new WPLA_ListingsModel();
		$listings = $listingsModel->getAllItemsByParentID( $post->ID );

		// warn when changing the SKU for a published item
		if ( ! empty($listings) ) $this->add_js_to_prevent_changing_sku();

		?>

		<div class="misc-pub-section" id="wpla-submit-options">

			<?php _e( 'Variations on Amazon', 'wpla' ); ?>:
				<b><?php echo sizeof($listings); ?></b>
				<a href="#" style="float:right;" onclick="jQuery('#wpla-submit-listings-table').slideToggle();return false;">
					<?php echo __('Show', 'wpla') ?>
				</a>
			<br>

		</div>

		<div class="misc-pub-section" id="wpla-submit-listings-table" style="display:none">

			<table style="width:99%">
				<tr>
					<th>SKU</th>
					<th>ASIN</th>
					<th>Status</th>
				</tr>
				<?php foreach ( $listings as $item ) : ?>

					<?php 
				
						// get proper amazon_url
				        if ( $item->asin && $item->account_id ) {
				            $account = new WPLA_AmazonAccount( $item->account_id );
				            $market  = new WPLA_AmazonMarket( $account->market_id );
				            $amazon_url = 'http://www.'.$market->url.'/dp/'.$item->asin.'/';
				        } else {
							$amazon_url = 'http://www.amazon.com/dp/'.$item->asin; 
				        }

					?>

					<tr>
						<td>
							<a href="admin.php?page=wpla&amp;s=<?php echo urlencode($item->sku) ?>" target="_blank">
								<?php echo $item->sku ?>
							</a>
						</td>
						<td>
							<?php if ( $item->asin ) : ?>
							<a href="<?php echo $amazon_url ?>" target="_blank">
								<?php echo $item->asin ?>
							</a>
							<?php else : ?>
								&mdash;
							<?php endif; ?>
						</td>
						<td>
							<i><?php echo $item->status ?></i>
						</td>
					</tr>

				<?php endforeach; ?>
			</table>

		</div>

		<?php
	} // display_submitbox_for_variable_product()

	// if product has been imported from amazon...
	function wpla_product_submitbox_imported_status() {
		global $post;
		global $woocommerce;

		$item_source = get_post_meta( $post->ID, '_amazon_item_source', true );
		if ( ! $item_source ) return;

		$amazon_id = get_post_meta( $post->ID, '_wpla_asin', true );

		// get ViewItemURL - fall back to generic url on amazon.com
		$listingsModel = new WPLA_ListingsModel();
		// $amazon_url = $listingsModel->getViewItemURLFromPostID( $post->ID );
		$amazon_url = false;
		if ( ! $amazon_url ) $amazon_url = 'http://www.amazon.com/dp/'.$amazon_id;

		?>

		<div class="misc-pub-section" id="wpla-submit-options">

			<?php _e( 'This product was imported from', 'wpla' ); ?>
				<!-- <b><?php echo $item->status; ?></b> &nbsp; -->
				<a href="<?php echo $amazon_url ?>" target="_blank" style="float:right;">
					<?php echo __('Amazon', 'wpla') ?>
				</a>
			<br>

		</div>
		<?php
	} // wpla_product_submitbox_imported_status()


	// handle submitbox options
	// add_action( 'woocommerce_process_product_meta', 'wpla_product_handle_submitbox_actions', 100, 2 );
	function wpla_product_handle_submitbox_actions( $post_id, $post ) {
		global $oWPL_WPLister;
		global $wpla_logger;

	} // save_meta_box()


	// warn when changing the SKU for a published item
	function add_js_to_prevent_changing_sku() {
		global $post;
        wc_enqueue_js("

			jQuery( document ).ready( function () {
				
				// simple / parent product SKU
				var parent_sku_el = jQuery('#_sku');
	 			parent_sku_el.data('oldVal', parent_sku_el.val() );
				parent_sku_el.change(function() {
		            var oldValue = jQuery(this).data('oldVal');
					var response = confirm('This item is currently listed on Amazon and should be deleted from Seller Central before listing it again as a different SKU. Are you sure you want to change its SKU?');
					if ( ! response ) jQuery(this).val( oldValue );
				});

        		// variation SKUs
				jQuery('#variable_product_options_inner .woocommerce_variable_attributes td.sku input').change(function() {
					alert('This item is listed on Amazon. You should not change its SKU without deleting the listing first.');
					// return false;
				});

			});	

	    ");
	} // add_js_to_prevent_changing_sku()



	function wpla_product_updated_messages( $messages ) {
		global $post, $post_ID;

		// show errors later
		// add_action( 'admin_notices', array( &$this, 'wpla_product_updated_notices' ), 20 );

		// $success = $update_results[ $post_ID ]->success;
		// $errors  = $update_results[ $post_ID ]->errors;

		// add message
		// if ( $success )
		// 	$messages['product'][1] = sprintf( __( 'Product and Amazon listing were updated. <a href="%s">View Product</a>', 'wpla' ), esc_url( get_permalink($post_ID) ) );

		return $messages;
	}


	function wpla_product_admin_notices() {
		global $post, $post_ID;
		if ( ! $post ) return;
		if ( ! $post_ID ) return;
		if ( ! $post->post_type == 'product' ) return;
		$errors_msg = '';

		// warn about missing details
        $this->checkForMissingData( $post );
        $this->checkForInvalidData( $post );

		// get listing item
		$lm = new WPLA_ListingsModel();
		$listing = $lm->getItemByPostID( $post_ID );
		if ( ! $listing ) return;

		// parse history
		$history = maybe_unserialize( $listing->history );
		if ( empty($history) && ( $listing->product_type != 'variable' ) ) return;
		// echo "<pre>";print_r($history);echo"</pre>";#die();

        // show errors and warning on online and failed items only
        if ( ! in_array( $listing->status, array( 'online', 'failed' ) ) ) return;


		// process errors and warnings
        $tips_errors   = array();
        $tips_warnings = array();
        if ( is_array( $history ) ) {
            foreach ( $history['errors'] as $feed_error ) {
                $tips_errors[]   = WPLA_FeedValidator::formatAmazonFeedError( $feed_error );
            }
            foreach ( $history['warnings'] as $feed_error ) {
                $tips_warnings[] = WPLA_FeedValidator::formatAmazonFeedError( $feed_error );
            }
        }
        if ( ! empty( $tips_errors ) ) {
            $errors_msg .= 'Amazon returned the following error(s) when this product was submitted.'.' ';
            $errors_msg .= '(Status: ' . $listing->status .')<br>';
            $errors_msg .= '<small style="color:darkred">'.join('<br>',$tips_errors).'</small>';
        }

        // check variations for errors
        if ( $listing->product_type == 'variable' ) {

            $variations_msg = $errors_msg ? '<br><br>' : '';
            $variations_msg .= '<small><a href="#" onclick="jQuery(\'#variation_error_container\').slideToggle();return false;" class="button button-small">'.'Show errors for all variations'.'</a></small>';
            $variations_msg .= '<div id="variation_error_container" style="display:none">';
            $variations_have_errors = false;

        	$child_items = $lm->getAllItemsByParentID( $post_ID );
        	foreach ($child_items as $child) {

				$history = maybe_unserialize( $child->history );
		        $tips_errors   = array();
		        
		        if ( is_array( $history ) ) {
	                foreach ( $history['errors'] as $feed_error ) {
	                    $tips_errors[]   = WPLA_FeedValidator::formatAmazonFeedError( $feed_error );
	                }
	                // foreach ( $history['warnings'] as $feed_error ) {
	                //     $tips_warnings[] = WPLA_FeedValidator::formatAmazonFeedError( $feed_error );
	                // }
		        }
		        if ( ! empty( $tips_errors ) ) {
		            $variations_msg .= 'Errors for variation '.$child->sku.':'.'<br>';
		            $variations_msg .= '<small style="color:darkred">'.join('<br>',$tips_errors).'</small><br><br>';
		            $variations_have_errors = true;
		        }
        		
        	}
            $variations_msg .= '</div>';

            if ( $variations_have_errors ) $errors_msg .= $variations_msg;
        }

        if ( $errors_msg )
            self::showMessage( $errors_msg, 1, 1 );

	} // wpla_product_admin_notices()

   
	function wpla_order_admin_notices() {
		global $post, $post_ID;
		if ( ! $post ) return;
		if ( ! $post_ID ) return;
		if ( ! $post->post_type == 'shop_order' ) return;
		$errors_msg = '';


		// check for problems with FBA / MCF submission

        // show errors and warning on failed items only
        $submission_status = get_post_meta( $post->ID, '_wpla_fba_submission_status', true );
        if ( ! in_array( $submission_status, array( 'failed' ) ) ) return;

		// parse result
        $submission_result = maybe_unserialize( get_post_meta( $post->ID, '_wpla_fba_submission_result', true ) );
		if ( empty($submission_result) ) return;
		// echo "<pre>";print_r($submission_result);echo"</pre>";#die();
		$history = $submission_result;

		// process errors and warnings
        $tips_errors   = array();
        $tips_warnings = array();
        if ( is_array( $history ) ) {
            foreach ( $history['errors'] as $feed_error ) {
                $tips_errors[]   = WPLA_FeedValidator::formatAmazonFeedError( $feed_error );
            }
            foreach ( $history['warnings'] as $feed_error ) {
                $tips_warnings[] = WPLA_FeedValidator::formatAmazonFeedError( $feed_error );
            }
        }
        if ( ! empty( $tips_errors ) ) {
            $errors_msg .= 'Amazon returned the following error(s) when this order was submitted to be fulfilled via FBA.'.'<br>';
            $errors_msg .= '<small style="color:darkred">'.join('<br>',$tips_errors).'</small>';
        }

        if ( $errors_msg )
            self::showMessage( $errors_msg, 1, 1 );

	} // wpla_order_admin_notices()


    // check if required details are set
    function checkForMissingData( $post ) {
    	global $page;
		if ( 'product' != $post->post_type ) return;
		if ( 'auto-draft' == $post->post_status ) return;
	    if ( ! get_option( 'wpla_enable_missing_details_warning' ) ) return;

		$product                  = get_product( $post );
		$missing_fields           = array();    	
		$missing_variation_fields = array();    	

		// SKU
		if ( ! $product->sku )
			$missing_fields[] = 'SKU';

		// check product type
		if ( $product->product_type == 'variable' ) {
			// variable product

			// get variations
			$variation_ids = $product->get_children();
			foreach ( $variation_ids as $variation_id ) {
				$_product = get_product( $variation_id );
				$var_info = " (#$variation_id)";

				// Price
				if ( ! $_product->regular_price )
					$missing_variation_fields[] = __('Price','wpla') . $var_info;

				// Sale Price Dates
				// if ( $_product->sale_price ) {
				// 	if ( ! get_post_meta( $variation_id, '_sale_price_dates_from', true ) )
				// 		$missing_variation_fields[] = __('Sale start date','wpla') . $var_info;
				// 	if ( ! get_post_meta( $variation_id, '_sale_price_dates_to', true ) )
				// 		$missing_variation_fields[] = __('Sale end date','wpla') . $var_info;
				// }

			} // foreach variation


		} elseif ( $product->product_type == 'simple' ) {
			// simple product

			// Quantity
			if ( ! $product->stock )
				$missing_fields[] = __('Quantity','wpla');

			// Price
			if ( ! $product->regular_price )
				$missing_fields[] = __('Price','wpla');

			// Sale Price Dates
			// if ( $product->sale_price ) {
			// 	if ( ! get_post_meta( $post->ID, '_sale_price_dates_from', true ) )
			// 		$missing_fields[] = __('Sale start date','wpla');
			// 	if ( ! get_post_meta( $post->ID, '_sale_price_dates_to', true ) )
			// 		$missing_fields[] = __('Sale end date','wpla');
			// }

		} // simple product

		// show warning
		$errors_msg = '';
		if ( ! empty($missing_fields) ) {
			$errors_msg .= __('This product is missing the following fields required to be listed on Amazon:','wpla') .' <b>'. join($missing_fields, ', ') . '</b><br>';
		}
		if ( ! empty($missing_variation_fields) ) {
			$errors_msg .= __('Some variations are missing the following fields required to be listed on Amazon:','wpla') .' <b>'. join($missing_variation_fields, ', ') . '</b><br>';
		}
		if ( ! empty($errors_msg) ) {
            self::showMessage( $errors_msg, 2, 1 );
		}


	} // checkForMissingData()



    // check if UPC / EAN and SKU are valid
    function checkForInvalidData( $post ) {
    	global $page;
		if ( 'product' != $post->post_type ) return;
		if ( 'auto-draft' == $post->post_status ) return;
	    if ( ! get_option( 'wpla_enable_missing_details_warning' ) ) return;

		$product             = get_product( $post );
		$invalid_product_ids = array();    	
		$invalid_skus        = array();    	

		// SKU
		if ( $product->sku && ! WPLA_FeedValidator::isValidSKU( $product->sku ) ) {
			$invalid_skus[] = $product->sku;
		}

		// UPC / EAN
		$amazon_product_id = get_post_meta( $product->id, '_amazon_product_id', true );
		if ( $amazon_product_id && ! WPLA_FeedValidator::isValidEANorUPC( $amazon_product_id ) ) {
			$invalid_product_ids[] = $amazon_product_id;
		}

		// variable product
		if ( $product->product_type == 'variable' ) {

			// get variations
			$variation_ids = $product->get_children();
			foreach ( $variation_ids as $variation_id ) {
				$_product = get_product( $variation_id );
				$var_info = " (#$variation_id)";

				// SKU
				if ( $_product->sku && ! WPLA_FeedValidator::isValidSKU( $_product->sku ) ) {
					$invalid_skus[] = $_product->sku . $var_info;
				}

				// UPC / EAN
				$amazon_product_id = get_post_meta( $variation_id, '_amazon_product_id', true );
				if ( $amazon_product_id && ! WPLA_FeedValidator::isValidEANorUPC( $amazon_product_id ) ) {
					$invalid_product_ids[] = $amazon_product_id . $var_info;
				}

			} // foreach variation

		} // variable product

		// show warning
		$errors_msg = '';
		if ( ! empty($invalid_skus) ) {
			$errors_msg .= __('Warning: This SKU is not valid:','wpla') .' <b>'. join($invalid_skus, ', ') . '</b> - only letters, numbers, dashes and underscores are allowed.<br>';
		}
		if ( ! empty($invalid_product_ids) ) {
			$errors_msg .= __('Warning: This product ID does not seem to be a valid UPC / EAN:','wpla') .' <b>'. join($invalid_product_ids, ', ') . '</b><br>';
			$errors_msg .= __('Valid UPCs have 12 digits, EANs have 13 digits.','wpla') . '<br>';
		}
		if ( ! empty($errors_msg) ) {
            self::showMessage( $errors_msg, 2, 1 );
		}

	} // checkForInvalidData()










	/**
	 * Columns for Orders page
	 **/
	function wpla_woocommerce_edit_shop_order_columns($columns){
		// $columns['wpla_amazon'] = '<img src="'.WPLA_URL.'/img/amazon-16x16.png" title="'.__('Placed on Amazon', 'wpla').'" />';		
		if ( WPLA_LIGHT ) return $columns;
				
        return $new_columns;
	}

	function wpla_woocommerce_custom_shop_order_columns( $column ) {
		global $post, $woocommerce;

		switch ($column) {
			case 'wpl_order_src' :

				$amazon_order_id = get_post_meta( $post->ID, '_wpla_amazon_order_id', true );

				if ( $amazon_order_id ) {

					// get order details
					$om      = new WPLA_OrdersModel();
					$order   = $om->getOrderByOrderID( $amazon_order_id );
					$account = $order ? WPLA_AmazonAccount::getAccount( $order->account_id ) : false;

					$tooltip = 'This order was placed on Amazon.';
					if ( $account ) $tooltip .= '<br>('.$account->title.')';
					echo '<img src="'.WPLA_URL.'img/amazon-orange-16x16.png" style="width:16px;vertical-align:middle;padding:0;" class="tips" data-tip="'.$tooltip.'" />';		

					// check for FBA
			        if ( $order ) {
			        	$order_details = json_decode( $order->details );
				        if ( is_object( $order_details ) && ( $order_details->FulfillmentChannel == 'AFN' ) ) {
							echo '<small style="font-size:10px;">'.'FBA'.'</small>';
				        }
			        }

				} // if amazon order


				// show submission status if it exists - for non-amazon orders as well
		        if ( $submission_status = get_post_meta( $post->ID, '_wpla_submission_result', true ) ) {
			        if ( $submission_status == 'success' ) {
						echo '<br><img src="'.WPLA_URL.'img/icon-success-32x32.png" style="width:12px;vertical-align:middle;padding:0;" class="tips" data-tip="This order was marked as shipped on Amazon" />';		
			        } else {
						$history     = maybe_unserialize( $submission_status );
						$error_count = is_array( $history ) ? sizeof(@$history['errors']) : false;
			            if ( $error_count ) {
							echo '<br><img src="'.WPLA_URL.'img/error.gif" style="vertical-align:middle;padding:0;" class="tips" data-tip="There was a problem - this order could not be marked as shipped on Amazon!" />';		
			            }
			        }
		        }

				// show FBA submission status if it exists - for non-amazon orders as well
		        if ( $submission_status = get_post_meta( $post->ID, '_wpla_fba_submission_status', true ) ) {
			        if ( $submission_status == 'success' ) {
						echo '<img src="'.WPLA_URL.'img/icon-success-32x32.png" style="width:12px;vertical-align:middle;padding:0;" class="tips" data-tip="This order was successfully submitted to be fulfilled by Amazon." />';		
			        } elseif ( $submission_status == 'shipped' ) {
						echo '<img src="'.WPLA_URL.'img/icon-success-32x32.png" style="width:12px;vertical-align:middle;padding:0;" class="tips" data-tip="This order has been fulfilled by Amazon." />';		
			        } else {
						$history     = maybe_unserialize( get_post_meta( $post->ID, '_wpla_fba_submission_result', true ) );
						$error_count = is_array( $history ) ? sizeof(@$history['errors']) : false;
			            if ( $error_count ) {
							echo '<img src="'.WPLA_URL.'img/error.gif" style="vertical-align:middle;padding:0;" class="tips" data-tip="There was a problem submitting this order to be fulfilled by Amazon!" />';		
			            }
			        }
		        }

			break;

		} // switch ($column)

	} // wpla_woocommerce_custom_shop_order_columns()


	// add custom view to woocommerce orders table
	// add_filter( 'views_edit-order', 'wpla_add_woocommerce_order_views' );
	function wpla_add_woocommerce_order_views( $views ) {
		global $wp_query;

		if ( ! current_user_can('edit_others_pages') ) return $views;

		// On Amazon
		// $class = ( isset( $wp_query->query['is_from_amazon'] ) && $wp_query->query['is_from_amazon'] == 'no' ) ? 'current' : '';
		$class = ( isset( $_REQUEST['is_from_amazon'] ) && $_REQUEST['is_from_amazon'] == 'yes' ) ? 'current' : '';
		$query_string = esc_url_raw( remove_query_arg(array( 'is_from_amazon' )) );
		$query_string = add_query_arg( 'is_from_amazon', urlencode('yes'), $query_string );
		$views['from_amazon'] = '<a href="'. $query_string . '" class="' . $class . '">' . __('Placed on Amazon', 'wpla') . '</a>';

		// Not on Amazon
		$class = ( isset( $_REQUEST['is_from_amazon'] ) && $_REQUEST['is_from_amazon'] == 'no' ) ? 'current' : '';
		$query_string = esc_url_raw( remove_query_arg(array( 'is_from_amazon' )) );
		$query_string = add_query_arg( 'is_from_amazon', urlencode('no'), $query_string );
		$views['not_from_amazon'] = '<a href="'. $query_string . '" class="' . $class . '">' . __('Not placed on Amazon', 'wpla') . '</a>';

		// debug query
		// $views['unlisted'] .= "<br>".$wp_query->request."<br>";

		return $views;
	}	

	// filter the orders in admin based on amazon status
	// add_filter( 'parse_query', 'wpla_woocommerce_admin_order_filter_query' );
	function wpla_woocommerce_admin_order_filter_query_v1( $query ) {
		global $typenow, $wp_query, $wpdb;

	    if ( $typenow == 'shop_order' ) {

	    	// filter by amazon status
	    	if ( ! empty( $_GET['is_from_amazon'] ) ) {

	        	// find all orders that are imported from amazon
	        	$sql = "
	        			SELECT DISTINCT post_id 
	        			FROM {$wpdb->prefix}postmeta 
					    WHERE meta_key = '_wpla_amazon_order_id'
	        	";
	        	$post_ids = $wpdb->get_col( $sql );
	        	// echo "<pre>";print_r($post_ids);echo"</pre>";#die();


		    	if ( $_GET['is_from_amazon'] == 'yes' ) {

		        	if ( is_array($post_ids) && ( sizeof($post_ids) > 0 ) ) {
			        	$query->query_vars['post__in'] = $post_ids;
		        	}

		        } elseif ( $_GET['is_from_amazon'] == 'no' ) {

		        	if ( is_array($post_ids) && ( sizeof($post_ids) > 0 ) ) {
			        	// $query->query_vars['post__not_in'] = $post_ids;
			        	$query->query_vars['post__not_in'] = array_merge( $query->query_vars['post__not_in'], $post_ids );
		        	}


		        }
	        }

		}

	} // wpla_woocommerce_admin_order_filter_query_v1()


	// filter the orders in admin based on ebay status
	// add_filter( 'parse_query', 'wplister_woocommerce_admin_order_filter_query' );
	function wpla_woocommerce_admin_order_filter_query( $query ) {
		global $typenow, $wp_query, $wpdb;

	    if ( $typenow == 'shop_order' ) {

	    	// filter by ebay status
	    	if ( ! empty( $_GET['is_from_amazon'] ) ) {

		    	if ( $_GET['is_from_amazon'] == 'yes' ) {

		        	$query->query_vars['meta_query'][] = array(
						'key'     => '_wpla_amazon_order_id',
						'compare' => 'EXISTS'
					);

		        } elseif ( $_GET['is_from_amazon'] == 'no' ) {

		        	$query->query_vars['meta_query'][] = array(
						'key'     => '_wpla_amazon_order_id',
						'compare' => 'NOT EXISTS'
					);

		        }

	        }

		}

	} // wpla_woocommerce_admin_order_filter_query()



} // class WPLA_WooBackendIntegration
// $WPLA_WooBackendIntegration = new WPLA_WooBackendIntegration();
