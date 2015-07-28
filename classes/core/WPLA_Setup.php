<?php

class WPLA_Setup extends WPLA_Core {
	
	// check if setup is incomplete and display next step
	public function checkSetup( $page = false ) {
		global $pagenow;

		// check if cURL is loaded
		if ( ! self::isCurlLoaded() ) return false;

		// check for windows server
		// if ( self::isWindowsServer() ) return false;
		self::isWindowsServer( $page );

		// create folders if neccessary
		// if ( self::checkFolders() ) return false;

		// check for updates
		self::checkForUpdates();

		// check if cron is working properly
		self::checkCron();

		// check if PHP, WooCommerce and WP are up to date
		self::checkVersions();

		// check for multisite installation
		// if ( self::checkMultisite() ) return false;

		// setup wizard
		// if ( self::getOption('amazon_token') == '' ) {
		if ( ( '1' == self::getOption('setup_next_step') ) && ( $page != 'settings') ) {
		
			$msg1 = __('You have not linked WP-Lister to your Amazon account yet.','wpla');
			$msg2 = __('To complete the setup procedure go to %s and follow the instructions.','wpla');
			$link = '<a href="admin.php?page=wpla-settings">'.__('Settings','wpla').'</a>';
			$msg2 = sprintf($msg2, $link);
			$msg = "<p><b>$msg1</b></p><p>$msg2</p>";
			wpla_show_message($msg);
		
			// update_option('wpla_setup_next_step', '0');
		
		}

		
		// db upgrade
		WPLA_UpgradeHelper::upgradeDB();

		// clean db
		// self::cleanDB();
	
	} // checkSetup()


	// clean database of old log records
	// TODO: hook this into daily cron schedule (DONE!)
	public function cleanDB() {
		global $wpdb;

		if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'wpla-settings' ) && ( self::getOption('log_to_db') == '1' ) ) {
			$days_to_keep = self::getOption( 'log_days_limit', 30 );		
			// $delete_count = $wpdb->get_var('SELECT count(id) FROM '.$wpdb->prefix.'amazon_log WHERE timestamp < DATE_SUB(NOW(), INTERVAL 1 MONTH )');
			$delete_count = $wpdb->get_var('SELECT count(id) FROM '.$wpdb->prefix.'amazon_log WHERE timestamp < DATE_SUB(NOW(), INTERVAL '.$days_to_keep.' DAY )');
			if ( $delete_count ) {
				$wpdb->query('DELETE FROM '.$wpdb->prefix.'amazon_log WHERE timestamp < DATE_SUB(NOW(), INTERVAL '.$days_to_keep.' DAY )');
				// $this->showMessage( __('Log entries cleaned: ','wpla') . $delete_count );
			}
		}
	}



	// check if cURL is loaded
	public function isCurlLoaded() {

		if( ! extension_loaded('curl') ) {
			wpla_show_message("
				<b>Required PHP extension missing</b><br>
				<br>
				Your server doesn't seem to have the <a href='http://www.php.net/curl' target='_blank'>cURL</a> php extension installed.<br>
				cURL ist required by WP-Lister to be able to talk with Amazon.<br>
				<br>
				On a recent debian based linux server running PHP 5 this should do the trick:<br>
				<br>
				<code>
					apt-get install php5-curl <br>
					/etc/init.d/apache2 restart
				</code>
				<br>
				<br>
				You'll require root access on your server to install additional php extensions!<br>
				If you are on a shared host, you need to ask your hoster to enable the cURL php extension for you.<br>
				<br>
				For more information on how to install the cURL php extension on other servers check <a href='http://stackoverflow.com/questions/1347146/how-to-enable-curl-in-php' target='_blank'>this page on stackoverflow</a>.
			",'error');
			return false;
		}

		return true;
	}

	// check server is running windows
	public function isWindowsServer( $page ) {

		if ( $page != 'settings' ) return;

		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {

			wpla_show_message("
				<b>Warning: Server requirements not met - this server runs on windows.</b><br>
				<br>
				WP-Lister currently only supports unixoid operating systems like Linux, FreeBSD and OS X.<br>
				Support for windows servers is still experimental and should not be used on production sites!
			",'warn');
			return true;
		}

		return false;
	}

	// check if WP_Cron is working properly
	public function checkCron() {

		$cron_interval  = get_option( 'wpla_cron_schedule' );
		$next_scheduled = wp_next_scheduled( 'wpla_update_schedule' ) ;
		if ( 'external' == $cron_interval ) $cron_interval = false;

		if ( $cron_interval && ! $next_scheduled ) {

			wpla_show_message( 
				'<p>'
				. '<b>Warning: WordPress Cron Job has been disabled - scheduled WP-Lister tasks are not executed!</b>'
				. '<br><br>'
				. 'The task schedule has been reset just now in order to automatically fix this.'
				. '<br><br>'
				. 'If this message does not disappear, please visit the <a href="admin.php?page=wpla-settings&tab=settings">Settings</a> page and click <i>Save Settings</i> or contact support.'
				. '</p>'
			,'warn');

			// this should fix it:
			wp_schedule_event( time(), $cron_interval, 'wpla_update_schedule' );

		}

		// schedule daily event if not set yet
		if ( ! wp_next_scheduled( 'wpla_daily_schedule' ) ) {
			wp_schedule_event( time(), 'daily', 'wpla_daily_schedule' );
		}

		// schedule FBA Shipment report request - if not set yet
		if ( ! wp_next_scheduled( 'wpla_fba_report_schedule' ) ) {
			$schedule = get_option( 'wpla_fba_report_schedule', 'daily' );
			wp_schedule_event( time(), $schedule, 'wpla_fba_report_schedule' );
		}

	}

	// check versions
	public function checkVersions() {

		// WP-Lister for eBay 1.6+
		if ( defined('WPLISTER_VERSION') && version_compare( WPLISTER_VERSION, '1.6', '<') ) {
			wpla_show_message( 
				'<p>'
				. '<b>Warning: Your version of WP-Lister for eBay '.WPLISTER_VERSION.' is not fully compatible with WP-Lister for Amazon.</b>'
				. '<br><br>'
				. 'To prevent any issues, please update to WP-Lister for eBay 1.6 or better.'
				. '</p>'
			,'warn');
		}

		// check if WooCommerce is up to date
		$required_version    = '2.2.4';
		$woocommerce_version = defined('WC_VERSION') ? WC_VERSION : WOOCOMMERCE_VERSION;
		if ( version_compare( $woocommerce_version, $required_version ) < 0 ) {
			wpla_show_message("
				<b>Warning: Your WooCommerce version is outdated.</b><br>
				<br>
				WP-Lister requires WooCommerce $required_version to be installed. You are using WooCommerce $woocommerce_version.<br>
				You should always keep your site and plugins updated.<br>
			",'warn');
		}

		// PHP 5.3+
		if ( version_compare(phpversion(), '5.3', '<')) {
			wpla_show_message( 
				'<p>'
				. '<b>Warning: Your PHP version '.phpversion().' is outdated.</b>'
				. '<br><br>'
				. 'Your server should have PHP 5.3 or better installed.'
				. ' '
				. 'Please contact your hosting support and ask them to update your PHP version.'
				. '</p>'
			,'warn');
		}

	}


	// checks for multisite network
	public function checkMultisite() {

		if ( is_multisite() ) {

			// check for network activation
			if ( ! function_exists( 'is_plugin_active_for_network' ) )
				require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

			if ( function_exists('is_network_admin') && is_plugin_active_for_network( plugin_basename( WPLA_PATH.'/wp-lister-amazon.php' ) ) )
				wpla_show_message("network activated!");
			else
				wpla_show_message("not network activated!");


			// $this->showMessage("
			// 	<b>Multisite installation detected</b><br>
			// 	<br>
			// 	This is a site network...<br>
			// ");
			return true;
		}

		return false;
	}


	// check for updates
	public function checkForUpdates() {
	}
	


}

