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

if( ! class_exists( 'YITH_WCStripe_Admin' ) ){
	/**
	 * WooCommerce Stripe main class
	 *
	 * @since 1.0.0
	 */
	class YITH_WCStripe_Admin {

		/**
		 * @var string Premium version landing link
		 */
		protected $_premium_landing = 'http://yithemes.com/themes/plugins/yith-woocommerce-stripe/';

		/**
		 * @var string Plugin official documentation
		 */
		protected $_official_documentation = 'http://yithemes.com/docs-plugins/yith-woocommerce-stripe/';

		/**
		 * Constructor.
		 *
		 * @return \YITH_WCStripe_Admin
		 * @since 1.0.0
		 */
		public function __construct() {
			// register gateway panel
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

			// register panel
			$action = 'yith_wcstripe_gateway';
			if ( defined( 'YITH_WCSTRIPE_PREMIUM' ) ) {
				$action .= '_advanced';
			}
			add_action( $action . '_settings_tab', array( $this, 'print_panel' ) );

			// register pointer
			add_action( 'admin_init', array( $this, 'register_pointer' ) );

			//Add action links
			//add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 4 );
			//add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCSTRIPE_DIR . '/' . basename( YITH_WCSTRIPE_FILE ) ), array( $this, 'action_links' ) );
		}

		/**
		 * Register subpanel for YITH Stripe into YI Plugins panel
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function register_panel() {
			$args = array(
				'create_menu_page' => true,
				'parent_slug'   => '',
				'page_title'    => __( 'Stripe', 'yit' ),
				'menu_title'    => __( 'Stripe', 'yit' ),
				'capability'    => 'manage_options',
				'parent'        => '',
				'parent_page'   => 'yit_plugin_panel',
				'page'          => 'yith_wcstripe_panel',
				'admin-tabs'    => array( 'settings' => __( 'Settings', 'yit' ), ),
				'options-path'  => YITH_WCSTRIPE_INC . 'plugin-options'
			);

			/* === Fixed: not updated theme  === */
			if( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once( YITH_WCSTRIPE_DIR . 'plugin-fw/lib/yit-plugin-panel-wc.php' );
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/**
		 * Print custom tab of settings for Stripe subpanel
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_panel() {
			$panel_template = YITH_WCSTRIPE_DIR . '/templates/admin/settings-tab.php';

			if ( ! file_exists( $panel_template ) ) {
				return;
			}

			global $current_section;
			$current_section = 'yith_wcstripe_gateway';

			if ( defined( 'YITH_WCSTRIPE_PREMIUM' ) ) {
				$current_section .= '_advanced';
			}

			WC_Admin_Settings::get_settings_pages();

			if( ! empty( $_POST ) ) {
				$gateways = WC()->payment_gateways()->get_available_payment_gateways();
				$gateways['yith-stripe']->process_admin_options();
			}

			include_once( $panel_template );
		}

		/**
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $links | links plugin array
		 *
		 * @return   mixed Array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return mixed
		 * @use plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {

			$links[] = '<a href="' . admin_url( "admin.php?page=yith_stripe_panel" ) . '">' . __( 'Settings', 'yith-stripe' ) . '</a>';

			if ( defined( 'YITH_WCSTRIPE_FREE_INIT' ) ) {
				$links[] = '<a href="' . $this->_premium_landing . '" target="_blank">' . __( 'Premium Version', 'yith-stripe' ) . '</a>';
			}

			return $links;
		}

		/**
		 * plugin_row_meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $plugin_meta
		 * @param $plugin_file
		 * @param $plugin_data
		 * @param $status
		 *
		 * @return   Array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use plugin_row_meta
		 */
		public function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
			if ( ( defined( 'YITH_WCSTRIPE_INIT' ) && YITH_WCSTRIPE_INIT == $plugin_file ) ||
			     ( defined( 'YITH_WCSTRIPE_FREE_INIT' ) && YITH_WCSTRIPE_FREE_INIT == $plugin_file )
			) {
				$plugin_meta[] = '<a href="' . $this->_official_documentation . '" target="_blank">' . __( 'Plugin Documentation', 'yith-stripe' ) . '</a>';
			}

			return $plugin_meta;
		}

		/**
		 * Register the pointer for the settings page
		 *
		 * @since 1.0.0
		 */
		public function register_pointer() {
			if ( ! class_exists( 'YIT_Pointers' ) ) {
				include_once( '../plugin-fw/lib/yit-pointers.php' );
			}

			$args[] = array(
				'screen_id'  => 'plugins',
				'pointer_id' => 'yith_wcstripe_panel',
				'target'     => '#toplevel_page_yit_plugin_panel',
				'content'    => sprintf( '<h3> %s </h3> <p> %s </p>',
					__( 'YITH WooCommerce Stripe', 'yith-stripe' ),
					__( 'In the YIT Plugins tab you can find the YITH WooCommerce Stripe options. With this menu, you can access to all the settings of our plugins that you have activated.', 'yith-stripe' )
				),
				'position'   => array( 'edge' => 'left', 'align' => 'center' ),
				'init'       => defined( 'YITH_WCSTRIPE_PREMIUM' ) ? YITH_WCSTRIPE_INIT : YITH_WCSTRIPE_FREE_INIT
			);

			YIT_Pointers()->register( $args );
		}
	}
}

return new YITH_WCStripe_Admin();