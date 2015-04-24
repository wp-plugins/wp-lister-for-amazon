<?php include_once( dirname(__FILE__).'/../common_header.php' ); ?>

<style type="text/css">

	a.right,
	input.button {
		float: right;
	}



    .csv-table td,
    .csv-table th {
        font-size: .8em;
        font-family: Helvetica Neue,Helvetica,sans-serif;
    }

    .csv-table {
        width: 100%;
        border: 1px solid #B0B0B0;
    }
    .csv-table tbody {
        /* Kind of irrelevant unless your .css is alreadt doing something else */
        margin: 0;
        padding: 0;
        border: 0;
        outline: 0;
        /*font-size: 100%;*/
        vertical-align: baseline;
        background: transparent;
    }
    .csv-table thead {
        text-align: left;
    }
    .csv-table thead th {
        background: -moz-linear-gradient(top, #F0F0F0 0, #DBDBDB 100%);
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #F0F0F0), color-stop(100%, #DBDBDB));
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#F0F0F0', endColorstr='#DBDBDB', GradientType=0);
        border: 1px solid #B0B0B0;
        color: #444;
        /*font-size: 16px;*/
        font-weight: bold;
        padding: 3px 10px;
    }
    .csv-table td {
        padding: 3px 10px;
    }
    .csv-table tr:nth-child(even) {
        background: #F2F2F2;
    }

</style>

<div class="wrap">
	<div class="icon32" style="background: url(<?php echo $wpl_plugin_url; ?>img/amazon-32x32.png) no-repeat;" id="wpl-icon"><br /></div>
	<h2>
		<?php if ( $wpl_step == 2 ) : ?>
			<?php echo __('Preview Import','wpla') ?> - <?php echo __('Step','wpla') . ' ' . ($wpl_step-1) ?>
		<?php endif; ?>
		<?php if ( $wpl_step == 3 ) : ?>
			<?php echo __('Import Products','wpla') ?> - <?php echo __('Step','wpla') . ' ' . ($wpl_step-1) ?>
		<?php endif; ?>
		<?php if ( $wpl_step == 4 ) : ?>
			<?php echo __('Import Process Finished','wpla') ?>
		<?php endif; ?>
	</h2>
	<?php echo $wpl_message ?>

    <?php
        // check if report has required default columns - seller-sku and asin/asin1
        $is_invalid_report = false;
        $first_row = reset($wpl_data_rows);
        if ( ! isset($first_row['seller-sku']) ) $is_invalid_report = true;
        // if ( ! isset($first_row['asin']) && ! isset($first_row['asin1']) ) $is_invalid_report = true;
    ?>
    <?php if ( $is_invalid_report ) : ?>
        <div id="message" class="error">
            <p>
                <b><?php echo __('Error: This report seems to use localized column headers and can not be processed.','wpla') ?></b>
            </p>
            <p>
                To change the default language used in reports, please log in to Seller Central, visit  
                <i>Settings &raquo; Account Info &raquo; Feed Processing Report Language &raquo; Edit</i> - and select <i>English (US)</i>.
            </p>
            <p>
                Then wait about 5-10 minutes for Amazon to update your settings before you request a new inventory report.
            </p>
        </div>
    <?php endif; ?>

	<div style="width:100%" class="postbox-container">
		<div class="metabox-holder">
			<div class="meta-box-sortables ui-sortable">

				<div class="postbox" id="RunImportBox">
					<h3 class="hndle"><span><?php echo __('Summary','wpla'); ?></span></h3>
					<div class="inside">
						<p>
							<?php if ( $wpl_step == 2 ) : ?>
								<?php echo sprintf( __('Your inventory report for account <b>%s</b> contains <b>%s products</b> in total.','wpla'), $wpl_account->title, count($wpl_summary->report_skus) ); ?><br>
								<?php echo __('First click on "Process Report" to update existing listings and products.','wpla'); ?><br>
							<?php endif; ?>
							<?php if ( $wpl_step == 3 ) : ?>
								<?php echo sprintf( __('Great, %s rows of your inventory report has been processed.','wpla'), count($wpl_summary->report_skus), $wpl_account->title ); ?><br>
								<?php echo __('Next, click "Import Products" to create missing products in WooCommerce.','wpla'); ?><br>
							<?php endif; ?>
						</p>

						<h4><?php echo __('Step 1: Update Listings and Products','wpla') ?></h4>
						<p>
                            <?php if ( $wpl_reports_update_woo_stock || $wpl_reports_update_woo_price ) : ?>
    							<?php echo sprintf( __('There are <b>%s new listings</b> which will be added to WP-Lister, <b>%s existing listings</b> and <b>%s existing products</b> will be updated.','wpla'), count($wpl_summary->listings_to_import), count($wpl_summary->listings_to_update), count($wpl_summary->products_to_update) ); ?>
                            <?php else : ?>
                                <?php echo sprintf( __('There are <b>%s new listings</b> which will be added to WP-Lister and <b>%s existing listings</b> will be updated.','wpla'), count($wpl_summary->listings_to_import), count($wpl_summary->listings_to_update) ); ?>
                            <?php endif; ?>
						</p>

                        <?php if ( $wpl_reports_update_woo_stock || $wpl_reports_update_woo_price ) : ?>
                        <p>
                            <?php if ( $wpl_reports_update_woo_stock && $wpl_reports_update_woo_price ) : ?>
                                <?php echo __('Existing products will have both price and quantity updated from this report.','wpla'); ?>
                            <?php elseif ( $wpl_reports_update_woo_stock ) : ?>
                                <?php echo __('Note: Existing WooCommerce products will have only the stock quantity updated - prices will not be updated.','wpla'); ?>
                            <?php elseif ( $wpl_reports_update_woo_price ) : ?>
                                <?php echo __('Note: Existing WooCommerce products will have only the price updated - stock levels will not be updated!','wpla'); ?>
                                (<?php echo __('not recommended','wpla'); ?>)
                            <?php endif; ?>
                        </p>
                        <?php endif; ?>

						<p>
							<?php $btn_class = $wpl_step == 2 ? 'button-primary' : 'button-secondary'; ?>
							<a id="btn_process_amazon_report" data-id="<?php echo $wpl_report_id ?>" class="button button-small wpl_job_button <?php echo $btn_class ?>">
								<?php echo __('Process Report','wpla'); ?>
							</a>
						</p>

						<h4><?php echo __('Step 2: Import Products','wpla') ?></h4>
						<?php if ( count($wpl_summary->products_to_import) ) : ?>
							<p>
								<?php echo sprintf( __('There are <b>%s new products</b> which will be added to WooCommerce.','wpla'), count($wpl_summary->products_to_import) ); ?>
							</p>
							<p>
								<?php $btn_class = $wpl_step == 3 ? 'button-primary' : 'button-secondary'; ?>
								<a id="btn_batch_create_products_reminder" class="button button-small wpl_job_button <?php echo $btn_class ?>">
									<?php echo __('Import / Update Products','wpla'); ?>
								</a>
							</p>
						<?php else: ?>
							<p>
								<?php echo __('All products from this report already exist in WooCommerce.','wpla'); ?>
							</p>
						<?php endif; ?>

						
						<!-- 
						<h4><?php echo __('Totals','wpla') ?></h4>
						<p>
							<?php echo __('Products to be imported','wpla') .': '. count($wpl_summary->products_to_import) ?><br>
							<?php echo __('Products to be updated','wpla')  .': '. count($wpl_summary->products_to_update) ?><br>
							<?php echo __('Listings to be imported','wpla') .': '. count($wpl_summary->listings_to_import) ?><br>
							<?php echo __('Listings to be updated','wpla')  .': '. count($wpl_summary->listings_to_update) ?><br>
						</p>
						<p>
							<?php echo __('Click on "Start Import" to fetch product details from Amazon and add them to your website.','wpla'); ?><br>
						</p>
 						-->

					</div>
				</div> <!-- postbox -->

				<div class="postbox" id="ImportPreviewBox">
					<h3 class="hndle"><span><?php echo __('Report Rows','wpla'); ?></span></h3>
					<div class="inside">

						<?php wpla_render_import_preview_table( $wpl_data_rows, $wpl_summary ) ?>

						<p>
							Note: This preview shows a maxmimum of 100 rows only.
						</p>

						<?php
							// echo "<pre>";print_r($wpl_summary);echo"</pre>";#die();
							// echo "<pre>";print_r($wpl_data_rows);echo"</pre>";#die();
						?>

					</div>
				</div> <!-- postbox -->


			</div>
		</div>
	</div>

	<br style="clear:both;"/>

</div>

<?php

function wpla_render_import_preview_table( $wpl_rows, $wpl_summary ) {
    if ( ! is_array($wpl_rows) || ( ! sizeof($wpl_rows) ) ) return; 
    $row_count = 0;
    ?>

    <table id="wpla_import_preview_table" class="csv-table">
        <thead>
        <tr>
            <th><?php echo __('Name','wpla') ?></th>
            <th><?php echo __('SKU','wpla') ?></th>
            <th><?php echo __('ASIN','wpla') ?></th>
            <th><?php echo __('Qty','wpla') ?></th>
            <th><?php echo __('Price','wpla') ?></th>
            <th><?php echo __('Listing will be...','wpla') ?></th>
            <th><?php echo __('Product will be...','wpla') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($wpl_rows as $row) : ?>
        <?php
        	$row_count++;
        	if ( $row_count > 100 ) continue;

        	// $listing_asin = $row['product-id'];
        	// $listing_asin   = isset( $row['asin1'] ) ? $row['asin1'] : $row['asin'];

            // special treatment for amazon.ca
            $row_asin = false;
            $row_asin = isset( $row['asin1'] ) ? $row['asin1'] : $row_asin;
            $row_asin = isset( $row['asin']  ) ? $row['asin']  : $row_asin;
            if ( ! $row_asin && isset($row['product-id']) ) {
                if ( $row['product-id-type'] == 1 ) {
                    $row_asin = $row['product-id'];
                }
            }
            $listing_asin = $row_asin;

        	$listing_exists = in_array( $listing_asin, $wpl_summary->listings_to_update ) ? true : false;
        	if ( $listing_exists ) $listing_asin = '<a href="admin.php?page=wpla&s='.$listing_asin.'" target="_blank">'.$listing_asin.'</a>';
            if ( ! isset($row['asin']) && ! $listing_asin ) $listing_asin = '<span style="color:darkred">No ASIN found in report!</span>';

        	$product_sku    = $row['seller-sku'];
        	$product_exists = in_array( $row['seller-sku'], $wpl_summary->products_to_update ) ? true : false;
        	if ( $product_exists ) $product_sku = '<a href="edit.php?post_type=product&s='.$product_sku.'" target="_blank">'.$product_sku.'</a>';
            if ( ! isset($row['seller-sku']) ) $product_sku = '<span style="color:darkred">Invalid Report - no SKU column found</span>';
        ?>
        <tr>
            <!-- <td><?php echo utf8_encode( $row['item-name'] ) ?></td> -->
            <td><?php echo $row['item-name'] ?></td>
            <td><?php echo $product_sku ?></td>
            <td><?php echo $listing_asin ?></td>
            <td style="text-align:right;">
                <?php 
                    if ( $row['quantity'] ) {
                        echo $row['quantity'];
                    } elseif ( isset($row['fulfillment-channel']) && ( $row['fulfillment-channel'] != 'DEFAULT' ) ) {
                        echo '<span style="color:silver">FBA</span>';
                    } else {
                        echo "&mdash;";
                    }
                ?>
            </td>
            <td style="text-align:right;">
                <?php echo number_format_i18n( floatval($row['price']), 2 ) ?>
            </td>
            <td>
            	<?php 
            		if ( $listing_exists ) {
            			echo "updated";
            		} else {
            			echo "imported";
            		}
            	?>
            </td>
            <td>
            	<?php 
            		if ( in_array( $row['seller-sku'], $wpl_summary->products_to_update ) ) {
            			echo "updated";
            		} else {
            			echo "imported";
            		}
            	?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php
} // wpla_render_import_preview_table()
?>