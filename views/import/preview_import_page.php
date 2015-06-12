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

    /* checkbox column */
    .csv-table thead .check-column {
        text-align: center;
    }
    .csv-table .check-column {
        display:none;
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
								<?php echo sprintf( __('Your inventory report for account <b>%s</b> contains <b>%s products</b> in total.','wpla'), $wpl_account->title, count($wpl_report_summary->report_skus) ); ?><br>
								<?php echo __('First click on "Process Report" to update existing listings and products.','wpla'); ?><br>
							<?php endif; ?>
							<?php if ( $wpl_step == 3 ) : ?>
								<?php echo sprintf( __('Great, %s rows of your inventory report have been processed.','wpla'), count($wpl_report_summary->report_skus), $wpl_account->title ); ?><br>
								<?php echo __('Next, click "Import Products" to create missing products in WooCommerce.','wpla'); ?><br>
							<?php endif; ?>
						</p>

						<h4><?php echo __('Step 1: Update Listings and Products','wpla') ?></h4>
						<p>
                            <?php if ( $wpl_reports_update_woo_stock || $wpl_reports_update_woo_price ) : ?>
    							<?php echo sprintf( __('There are <b>%s new listings</b> which will be added to the import queue, <b>%s existing listings</b> and <b>%s existing products</b> will be updated.','wpla'), count($wpl_report_summary->listings_to_import), count($wpl_report_summary->listings_to_update), count($wpl_report_summary->products_to_update) ); ?>
                            <?php else : ?>
                                <?php echo sprintf( __('There are <b>%s new listings</b> which will be added to the import queue and <b>%s existing listings</b> will be updated.','wpla'), count($wpl_report_summary->listings_to_import), count($wpl_report_summary->listings_to_update) ); ?>
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
                                <?php echo __('Process full report','wpla'); ?>
                            </a>

                            <a id="btn_process_selected_report_rows" data-id="<?php echo $wpl_report_id ?>" class="button button-small wpl_job_button <?php echo $btn_class ?>" style="display:none;">
                                <?php echo __('Process selected rows','wpla'); ?>
                            </a>

                            <a id="btn_toggle_selection_mode" data-id="<?php echo $wpl_report_id ?>" class="button button-small wpl_job_button">
                                <?php echo __('Select rows to process','wpla'); ?>
                            </a>
						</p>

						<h4><?php echo __('Step 2: Import Products','wpla') ?></h4>
                        <?php // if ( count($wpl_report_summary->products_to_import) ) : ?>
						<?php if ( intval(@$wpl_status_summary->imported) ) : ?>
							<p>
                                <?php if ( $wpl_step == 3 ) : ?>
                                    <!-- step 3: show import queue status -->
                                    <?php echo sprintf( __('There are <b>%s items</b> in the import queue, waiting to be imported to WooCommerce.','wpla'), intval(@$wpl_status_summary->imported) ); ?>
                                <?php else : ?>
                                    <!-- step 2: show report summary info -->
                                    <?php echo sprintf( __('There are <b>%s new products</b> in this report which will be added to WooCommerce.','wpla'), count($wpl_report_summary->products_to_import) ); ?>
                                <?php endif; ?>
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
							<?php echo __('Products to be imported','wpla') .': '. count($wpl_report_summary->products_to_import) ?><br>
							<?php echo __('Products to be updated','wpla')  .': '. count($wpl_report_summary->products_to_update) ?><br>
							<?php echo __('Listings to be imported','wpla') .': '. count($wpl_report_summary->listings_to_import) ?><br>
							<?php echo __('Listings to be updated','wpla')  .': '. count($wpl_report_summary->listings_to_update) ?><br>
						</p>
						<p>
							<?php echo __('Click on "Start Import" to fetch product details from Amazon and add them to your website.','wpla'); ?><br>
						</p>
 						-->

					</div>
				</div> <!-- postbox -->

				<div class="postbox" id="ImportPreviewBox">
					<h3 class="hndle">
                        <span><?php echo __('Report Rows','wpla'); ?></span>
                        <div style="float:right;">
                            <input id="wpla_import_preview_search_box" type="text" placeholder="Filter by SKU, ASIN or name..." style="font-size: 12px; font-weight: normal; width:200px;">
                        </div>
                    </h3>
					<div class="inside">

                        <div id="wpla_import_preview_table_container">
                            <?php WPLA_ImportHelper::render_import_preview_table( $wpl_data_rows, $wpl_report_summary ) ?>
                        </div>

						<p>
							Note: This preview shows a maxmimum of 100 rows only.
						</p>

						<?php
							// echo "<pre>";print_r($wpl_report_summary);echo"</pre>";#die();
							// echo "<pre>";print_r($wpl_data_rows);echo"</pre>";#die();
						?>

					</div>
				</div> <!-- postbox -->


			</div>
		</div>
	</div>

	<br style="clear:both;"/>

</div>



<script type="text/javascript">
    jQuery( document ).ready( function () {

        var wpla_report_id = '<?php echo $wpl_report_id ?>';

        // disable Enter key in filter field
        // jQuery('#wpla_import_preview_search_box').keypress(function(event) { 
        //     setTimeout( wpla_update_preview, 1000 );
        //     return event.keyCode != 13; 
        // });

        // disable Enter key in filter field
        jQuery('#wpla_import_preview_search_box').change(function(event) { 
            wpla_update_preview();
        });

        // handle field filter changes
        function wpla_update_preview() {

            var query = jQuery('#wpla_import_preview_search_box').val();
            console.log(query);

            var params = {
                action: 'wpla_get_import_preview_table',
                report_id: wpla_report_id,
                query: query,
                nonce: 'TODO'
            };
            jQuery( "#wpla_import_preview_table_container" ).load( ajaxurl, params, function() {
                console.log('updated.');                
            });

        } // wpla_update_preview()



        // handle button "Select rows"
        jQuery('#btn_toggle_selection_mode').click(function(event) { 
            jQuery('#wpla_import_preview_table .check-column').toggle();
            jQuery('#btn_process_selected_report_rows').toggle();
            jQuery('#btn_process_amazon_report').toggle();
        });



    });

</script>


