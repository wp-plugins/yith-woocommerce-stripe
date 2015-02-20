<?php
/**
 * Main class
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Stripe
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCSTRIPE' ) ) {
	exit;
} // Exit if accessed directly

if( ! class_exists( 'YITH_WCStripe' ) ){
	/**
	 * WooCommerce Stripe main class
	 *
	 * @since 1.0.0
	 */
	class YITH_WCStripe {
		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCStripe
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Stripe gateway id
		 *
		 * @var string Id of specific gateway
		 * @since 1.0
		 */
		public static $gateway_id = 'yith-stripe';

		/**
		 * Admin main class
		 *
		 * @var YITH_WCStripe_Admin
		 */
		public $admin = null;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCStripe
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/* === PLUGIN FW LOADER === */

		/**
		 * Loads plugin fw, if not yet created
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT' ) || ! defined( 'YIT_CORE_PLUGIN' ) ) {
				require_once( YITH_WCSTRIPE_DIR . '/plugin-fw/yit-plugin.php' );
			}
		}

		/**
		 * Constructor.
		 *
		 * @param array $details
		 *
		 * @return \YITH_WCStripe
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'after_setup_theme', array( $this, 'plugin_fw_loader' ), 1 );

			// includes
			include_once( 'class-yith-stripe-gateway.php' );

			// admin includes
			if ( is_admin() ) {
				$this->admin = include_once( 'class-yith-stripe-admin.php' );
			}

			// add filter to append wallet as payment gateway
			add_filter( 'woocommerce_payment_gateways', array( $this, 'add_to_gateways' ) );
		}

		/**
		 * Adds Stripe Gateway to payment gateways available for woocommerce checkout
		 *
		 * @param $methods array Previously available gataways, to filter with the function
		 *
		 * @return array New list of available gateways
		 * @since 1.0.0
		 */
		public function add_to_gateways( $methods ) {
			$methods[] = 'YITH_WCStripe_Gateway';

			return $methods;
		}
	}
}