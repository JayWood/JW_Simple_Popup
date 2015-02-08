<?php
/**
 * Plugin Name: JW Simple Popup
 * Plugin URI:  http://plugish.com
 * Description: A simple popup plugin with shortcodes and template tags.
 * Version:     0.1.0
 * Author:      Jay Wood
 * Author URI:  http://plugish.com
 * Donate link: http://plugish.com
 * License:     GPLv2+
 * Text Domain: jw_simple_popup
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2015 Jay Wood (email : jay@plugish.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Built using grunt-wp-plugin
 * Copyright (c) 2013 10up, LLC
 * https://github.com/10up/grunt-wp-plugin
 */

include 'includes/admin-options.php';

/**
 * Main initiation class
 */
class Jw_Simple_Popup {

	const VERSION = '0.1.0';

	protected static $url  = '';
	protected static $path = '';

	/**
	 * Sets up our plugin
	 * @since  0.1.0
	 */
	public function __construct() {
		// Useful variables
		self::$url  = trailingslashit( plugin_dir_url( __FILE__ ) );
		self::$path = trailingslashit( dirname( __FILE__ ) );
	}

	public function hooks() {
		register_activation_hook( __FILE__, array( $this, '_activate' ) );
		register_deactivation_hook( __FILE__, array( $this, '_deactivate' ) );
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_init', array( $this, 'admin_hooks' ) );
	}

	/**
	 * Activate the plugin
	 */
	function _activate() {
		// Make sure any rewrite functionality has been loaded
		flush_rewrite_rules();
	}

	/**
	 * Deactivate the plugin
	 * Uninstall routines should be in uninstall.php
	 */
	function _deactivate() {

	}

	/**
	 * Init hooks
	 * @since  0.1.0
	 * @return null
	 */
	public function init() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'jw_simple_popup' );
		load_textdomain( 'jw_simple_popup', WP_LANG_DIR . '/jw_simple_popup/jw_simple_popup-' . $locale . '.mo' );
		load_plugin_textdomain( 'jw_simple_popup', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		add_action( 'wp_head', array( $this, 'custom_popup_css' ) );
		add_action( 'wp_footer', array( $this, 'popup_footer_code' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

	}

	public function enqueue_scripts(){
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style( 'jw_simple_popup-css', plugins_url( "assets/css/jw_simple_popup{$min}.css", __FILE__ ), null, self::VERSION );

		wp_enqueue_script( 'jquery_cookie', plugins_url( 'assets/js/vendor/jquery_cookie.js', __FILE__ ) );
		wp_enqueue_script( 'jw_simple_popup-js', plugins_url( "assets/js/jw_simple_popup{$min}.js", __FILE__ ), array( 'jquery', 'jquery_cookie' ), self::VERSION, true );

		wp_localize_script( 'jw_simple_popup-js', 'jwsp', array(
			'delay' => jw_simple_popup_get_option( 'delay' ),
			'modal' => jw_simple_popup_get_option( 'modal' ),
		) );
	}

	/**
	 * Hooks for the Admin
	 * @since  0.1.0
	 * @return null
	 */
	public function admin_hooks() {
	}

	/**
	 * Magic getter for our object.
	 *
	 * @param string $field
	 *
	 * @throws Exception Throws an exception if the field is invalid.
	 *
	 * @return mixed
	 */
	public function __get( $field ) {
		switch ( $field ) {
			case 'url':
			case 'path':
				return self::$field;
			default:
				throw new Exception( 'Invalid '. __CLASS__ .' property: ' . $field );
		}
	}

	public function custom_popup_css(){
		?>
		<style>
			.jw_simple_popup .overlay{
				background: <?php echo jw_simple_popup_get_option( 'bg_color' ); ?>
			}
			<?php echo jw_simple_popup_get_option( 'css' ); ?> 
		</style>
		<?php
	}

	public function popup_footer_code(){
		$popup_content = jw_simple_popup_get_option( 'content' );
		if ( empty( $popup_content ) ){
			return false;
		}
		?>
		<!-- JW Simple Popup CSS -->
		<div class="jw_simple_popup overlay">
			<div class="jw_simple_popup wrapper">
				<a href="" class="jwsp close-button"></a>
				<div class="jw_simple_popup inner">
					<?php echo do_shortcode( wpautop( $popup_content ) ); ?> 
				</div>
			</div>
		</div>
		<?php
	}

}

// init our class
$Jw_Simple_Popup = new Jw_Simple_Popup();
$Jw_Simple_Popup->hooks();