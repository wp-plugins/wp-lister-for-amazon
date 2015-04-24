<?php
/**
 * globally available functions
 */


// custom tooltips
function wpla_tooltip( $desc ) {
	if ( defined('WPLISTER_RESELLER_VERSION') ) $desc = apply_filters( 'wpla_tooltip_text', $desc );
	if ( defined('WPLISTER_RESELLER_VERSION') && apply_filters( 'wplister_reseller_disable_tooltips', false ) ) return;
    echo '<img class="help_tip" data-tip="' . esc_attr( $desc ) . '" src="' . WPLA_URL . '/img/help.png" height="16" width="16" />';
}

// un-CamelCase string
function wpla_spacify( $str ) {
	return preg_replace('/([a-z])([A-Z])/', '$1 $2', $str);
}

// make logger available in static methods
function wpla_logger_start_timer($key) {
	global $wpla_logger;
	$wpla_logger->startTimer($key);
}
function wpla_logger_end_timer($key) {
	global $wpla_logger;
	$wpla_logger->endTimer($key);
}

// get instance of WP-Lister object (singleton)
function WPLA() {
	// global $wplister_amazon;
	// return $wplister_amazon;
	return WPLA_WPLister::get_instance();
}

// show admin message (since 0.9.4.2)
function wpla_show_message( $message, $type = 'info', $params = null ) {
	WPLA()->messages->add_message( $message, $type, $params );
}

// register custom shortcode to be used in listing profiles
function wpla_register_profile_shortcode( $shortcode, $title, $callback ) {

	WPLA()->shortcodes[ $shortcode ] = array(
		'slug'       => $shortcode,
		'title'      => $title,
		'callback'   => $callback,
		'content'    => false,
	);

}