<?php include_once( dirname(__FILE__).'/common_header.php' ); ?>

<style type="text/css">

	.inside p {
		width: 70%;
	}

	a.right,
	input.button {
		float: right;
	}

</style>

<div class="wrap">
	<div class="icon32" style="background: url(<?php echo $wpl_plugin_url; ?>img/amazon-32x32.png) no-repeat;" id="wpl-icon"><br /></div>
	<!-- <h2><?php echo __('Tools','wpla') ?></h2> -->

	<?php include_once( dirname(__FILE__).'/tools_tabs.php' ); ?>
	<?php echo $wpl_message ?>


	<div style="width:640px;" class="postbox-container">
		<div class="metabox-holder">
			<div class="meta-box-sortables ui-sortable">
				
				<div class="postbox" id="UpdateToolsBox">
					<h3 class="hndle"><span><?php echo __('Tools','wpla'); ?></span></h3>
					<div class="inside">

						<!--
						<form method="post" action="<?php echo $wpl_form_action; ?>">
								<?php wp_nonce_field( 'wpla_tools_page' ); ?>
								<input type="hidden" name="action" value="update_amazon_orders_30" />
								<input type="hidden" name="days" value="30" />
								<input type="submit" value="<?php echo __('Update Amazon orders','wpla'); ?>" name="submit" class="button">
								<p><?php echo __('Load all orders within 30 days from Amazon.','wpla'); ?></p>
						</form>
						<br style="clear:both;"/>
						-->

						<form method="post" action="<?php echo $wpl_form_action; ?>">
								<?php wp_nonce_field( 'wpla_tools_page' ); ?>
								<input type="hidden" name="action" value="wpla_run_daily_schedule" />
								<input type="submit" value="<?php echo __('Run daily schedule','wpla'); ?>" name="submit" class="button">
								<p><?php echo __('Manually trigger the daily task schedule.','wpla'); ?></p>
						</form>
						<br style="clear:both;"/>

						<form method="post" action="<?php echo $wpl_form_action; ?>">
								<?php wp_nonce_field( 'wpla_tools_page' ); ?>
								<input type="hidden" name="action" value="wpla_run_update_schedule" />
								<input type="submit" value="<?php echo __('Run update schedule','wpla'); ?>" name="submit" class="button">
								<p><?php echo __('Manually run scheduled background tasks.','wpla'); ?></p>
						</form>
						<br style="clear:both;"/>

						<form method="post" action="<?php echo $wpl_form_action; ?>">
								<?php wp_nonce_field( 'wpla_tools_page' ); ?>
								<input type="hidden" name="action" value="wpla_run_autosubmit_fba_orders" />
								<input type="submit" value="<?php echo __('Run FBA autosubmission','wpla'); ?>" name="submit" class="button">
								<p><?php echo __('Submit recent matching WC orders (24h) to FBA.','wpla'); ?></p>
						</form>
						<!-- <br style="clear:both;"/> -->

					</div>
				</div> <!-- postbox -->

				<div class="postbox" id="DatabaseToolBox">
					<h3 class="hndle"><span><?php echo __('Database','wpla'); ?></span></h3>
					<div class="inside">

						<form method="post" action="<?php echo $wpl_form_action; ?>">
								<?php wp_nonce_field( 'wpla_tools_page' ); ?>
								<input type="hidden" name="action" value="wpla_refresh_minmax_prices_from_wc" />
								<input type="submit" value="<?php echo __('Refresh Min./Max. Prices','wpla'); ?>" name="submit" class="button">
								<p><?php echo __('Update minimum and maxmimum prices from WooCommerce.','wpla'); ?></p>
						</form>
						<br style="clear:both;"/>

						<form method="post" action="<?php echo $wpl_form_action; ?>">
								<?php wp_nonce_field( 'wpla_tools_page' ); ?>
								<input type="hidden" name="action" value="wpla_fix_stale_postmeta" />
								<input type="submit" value="<?php echo __('Fix stale postmeta records','wpla'); ?>" name="submit" class="button">
								<p><?php echo __('Clear wp_postmeta table from stale records without posts.','wpla'); ?></p>
						</form>
						<br style="clear:both;"/>

						<form method="post" action="<?php echo $wpl_form_action; ?>">
								<?php wp_nonce_field( 'wpla_tools_page' ); ?>
								<input type="hidden" name="action" value="wpla_remove_all_imported_products" />
								<input type="submit" value="<?php echo __('Remove all imported products','wpla'); ?>" name="submit" class="button">
								<p>
									<?php echo __('This will remove all products and listings that have been imported from Amazon.','wpla'); ?>
									<?php echo __('Only use this if you want to start from scratch!','wpla'); ?>
								</p>
						</form>
						<!-- <br style="clear:both;"/> -->

					</div>
				</div> <!-- postbox -->

				<?php if ( get_option('wpla_log_level') > 1 ): ?>
				<div class="postbox" id="DebuggingToolBox">
					<h3 class="hndle"><span><?php echo __('Debug Log','wpla'); ?></span></h3>
					<div class="inside">

						<form method="post" action="admin-ajax.php" target="_blank">
								<?php wp_nonce_field( 'wpla_tools_page' ); ?>
								<input type="hidden" name="action" value="wpla_tail_log" />
								<input type="submit" value="<?php echo __('View debug log','wpla'); ?>" name="submit" class="button">
								<p><?php echo __('Open logfile viewer in new tab','wpla'); ?></p>
						</form>
						<br style="clear:both;"/>

						<form method="post" action="<?php echo $wpl_form_action; ?>">
								<?php wp_nonce_field( 'wpla_tools_page' ); ?>
								<input type="hidden" name="action" value="wpla_clear_log" />
								<input type="submit" value="<?php echo __('Clear debug log','wpla'); ?>" name="submit" class="button">
								<p><?php echo __('Current log file size','wpla'); ?>: <?php echo round($wpl_log_size/1024/1024,1) ?> mb</p>
						</form>
						<!-- <br style="clear:both;"/> -->

					</div>
				</div> <!-- postbox -->
				<?php endif; ?>

				<?php #if ( get_option('wpla_log_level') > 5 ): ?>
				<div class="postbox" id="DeveloperToolBox" style="display:none;">
					<h3 class="hndle"><span><?php echo __('Debug','wpla'); ?></span></h3>
					<div class="inside">

						<form method="post" action="<?php echo $wpl_form_action; ?>">
								<?php wp_nonce_field( 'wpla_tools_page' ); ?>
								<input type="hidden" name="action" value="update_amazon_time_offset" />
								<input type="submit" value="<?php echo __('Test Amazon connection','wpla'); ?>" name="submit" class="button">
								<p><?php echo __('Test connection to Amazon API','wpla'); ?></p>
						</form>
						<br style="clear:both;"/>

						<form method="post" action="<?php echo $wpl_form_action; ?>">
								<?php wp_nonce_field( 'wpla_tools_page' ); ?>
								<input type="hidden" name="action" value="test_curl" />
								<input type="submit" value="<?php echo __('Test Curl / PHP connection','wpla'); ?>" name="submit" class="button">
								<p><?php echo __('Check availability of CURL php extension and show phpinfo()','wpla'); ?></p>
						</form>
						<br style="clear:both;"/>

					</div>
				</div> <!-- postbox -->
				<?php #endif; ?>

			</div>
		</div>
	</div>

	<br style="clear:both;"/>

	<?php if ( get_option('wpla_log_level') > 5 ): ?>
		<pre><?php print_r($wpl_debug); ?></pre>
	<?php endif; ?>

	<?php if ( @$_REQUEST['action'] == 'test_curl' ): ?>
		
		<?php if( extension_loaded('curl') ) : ?>
			cURL extension is loaded
			<pre>
				<?php $curl_version = curl_version(); print_r($curl_version) ?>
			</pre>

		<?php else: ?>
			cURL extension is not installed!
		<?php endif; ?>
		<br style="clear:both;"/>

		<?php
			// test for command line app
			echo "cURL command line version:<br><pre>";
			echo `curl --version`;
			echo "</pre>";
		?>
		<br style="clear:both;"/>

		<?php phpinfo() ?>
	<?php endif; ?>


</div>