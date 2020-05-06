<?php
/**
 * Plugin Name: SMNTCS Quantity Buttons for WooCommerce
 * Plugin URI: https://github.com/nielslange/smntcs-woocommerce-quantity-buttons
 * Description: Add quantity buttons to WooCommerce product page
 * Author: Niels Lange <info@nielslange.de>
 * Author URI: https://nielslange.de
 * Text Domain: smntcs-woocommerce-quantity-buttons
 * Domain Path: /languages/
 * Version: 1.16.1
 * Requires at least: 4.5
 * Tested up to: 5.4
 * Requires PHP: 5.6
 * License: GPL3+
 * License URI: https://www.gnu.org/licenses/gpl.html
 *
 * @category   Plugin
 * @package    WordPress
 * @subpackage SMNTCS Quantity Buttons for WooCommerce
 * @author     Niels Lange <info@nielslange.de>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Avoid direct plugin access
 *
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( '¯\_(ツ)_/¯' );
}

/**
 * Define plugin version number
 *
 * @since 1.0.0
 */
define( 'SMNTCSWCQB_VERSION', '1.16.1' );

/**
 * Show warning if WooCommerce is not active or WooCommerce version < 2.3
 *
 * @since 1.0.0
 */
add_action(
	'admin_notices',
	function () {
		global $woocommerce;

		if ( ! class_exists( 'WooCommerce' ) || version_compare( $woocommerce->version, '2.3', '<' ) ) {
			$class   = 'notice notice-warning is-dismissible';
			$message = __( 'SMNTCS Quantity Buttons for WooCommerce requires at least WooCommerce 2.3.', 'smntcs-woocommerce-quantity-buttons' );

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
		}
	}
);

/**
 * Load textdomain
 *
 * @since 1.0.0
 */
add_action(
	'plugins_loaded',
	function () {
		load_plugin_textdomain( 'smntcs-woocommerce-quantity-buttons', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
);

/**
 * Enqueue scripts and styles
 *
 * @since 1.0.0
 */
add_action(
	'wp_enqueue_scripts',
	function () {

		wp_enqueue_script( 'smntcswcqb-script', plugins_url( 'button-handler.min.js', __FILE__ ), array( 'jquery' ), SMNTCSWCQB_VERSION, true );
		wp_enqueue_style( 'smntcswcqb-style', plugins_url( 'style.css', __FILE__ ), null, SMNTCSWCQB_VERSION, 'screen' );

		$show_on_product_page = apply_filters( 'show_on_product_page', true );
		$show_on_cart_page    = apply_filters( 'show_on_cart_page', true );

		if ( false === $show_on_product_page && is_product() ) {
			wp_dequeue_script( 'smntcswcqb-script' );
			wp_dequeue_style( 'smntcswcqb-style' );
		}

		if ( false === $show_on_cart_page && is_cart() ) {
			wp_dequeue_script( 'smntcswcqb-script' );
			wp_dequeue_style( 'smntcswcqb-style' );
		}

	}
);

/**
 * Load WooCommerce template
 *
 * @since 1.0.0
 */
add_filter(
	'woocommerce_locate_template',
	function ( $template, $template_name, $template_path ) {

		$show_on_product_page = apply_filters( 'show_on_product_page', true );
		$show_on_cart_page    = apply_filters( 'show_on_cart_page', true );

		if ( false === $show_on_product_page && is_product() ) {
			return $template;
		}

		if ( false === $show_on_cart_page && is_cart() ) {
			return $template;
		}

		global $woocommerce;

		$_template     = $template;
		$plugin_path   = untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/template/';
		$template_path = ( ! $template_path ) ? $woocommerce->template_url : null;
		$template      = locate_template( array( $template_path . $template_name, $template_name ) );

		if ( ! $template && file_exists( $plugin_path . $template_name ) ) {
			$template = $plugin_path . $template_name;
		}

		if ( ! $template ) {
			$template = $_template;
		}

		return $template;
	},
	1,
	3
);

/**
 * Add theme support
 *
 * @since 1.12.0
 */
add_action(
	'wp_enqueue_scripts',
	function() {
		switch ( get_template() ) {
			case 'twentytwenty':
				wp_enqueue_style( 'custom-twentytwenty-style', plugins_url( 'themes/twentytwenty.css', __FILE__ ), null, SMNTCSWCQB_VERSION, 'screen' );
				break;
		}
	},
	11
);
