<?php

namespace BOILER_PLATE_NAMESPACE;

/**
 * BOILERPLATE EXTENSION
 *
 * Load the BOILERPLATE EXTENSION
 *
 * @class    BOILERPLATE_EXTENSION
 * @version  1.0.0
 * @package  BOILERPLATE_EXTENSION/Core
 * @category Class
 * @author   SlickRemix
 */
class BOILER_PLATE_CLASS {

	/**
	 * Folder and Main File Name of plugin.
	 *
	 * @var string
	 */
	public $rel_plugin_path = 'boilerplate-extension/boilerplate-extension.php';

	/**
	 * Name of the Text Domain. The constant is set below for this and should be used for text domain: CURRENT_PLUGIN_TEXT_DOMAIN .
	 *
	 * @var string
	 */
	public $text_domain = 'boilerplate-extension';



	/**
	 * Load Function
	 *
	 * Load up all our actions and filters.
	 *
	 * @since 1.0.0
	 */
	public static function load_plugin() {

		$plugin_loaded = new self();

		$gallery_main_post_type = 'ft_gallery';

		$albums_main_post_type = 'ft_gallery_albums';

		// Setup Constants for FT Gallery.
		self::setup_constants( $plugin_loaded );

		// Pre Plugin Checks (must be after setup_constants function).
		$plugin_loaded->pre_plugin_checks();

		// Include the files.
		self::includes();

		// Add Actions and Filters.
		$plugin_loaded->add_actions_filters();

		// System Info.
		System_Info::load();

		// Setup Plugin functions.
		Setup_Functions::load();

		// Core.
		Core_Functions::load();

		// Updater Init.
		$updater = new updater_init();
		$updater->plugin_updater_check_init();

		// Variables to define specific terms!
		$transient = 'ftg_slick_rating_notice_waiting';
		$option    = 'ftg_slick_rating_notice';
		$nag       = 'ftg_slick_ignore_rating_notice_nag';

		$plugin_loaded->ftg_check_nag_get( $_GET, $nag, $option, $transient );

		$plugin_loaded->ftg_maybe_set_transient( $transient, $option );

		$plugin_loaded->set_review_status( $option, $transient );

	}

	/**
	 * Setup Constants
	 *
	 * Setup plugin constants for plugin
	 *
	 * @since 1.0.0
	 */
	private static function setup_constants( $plugin_loaded ) {
		// Makes sure the plugin is defined before trying to use it.
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		// Plugin Basename.
		if ( ! defined( __NAMESPACE__ . '\CURRENT_PLUGIN_BASENAME' ) ) {
			define( __NAMESPACE__ . '\CURRENT_PLUGIN_BASENAME', $plugin_loaded->rel_plugin_path );
		}

		// Plugins Absolute Path. (Needs to be after BASENAME constant to work).
		if ( ! defined( __NAMESPACE__ . '\CURRENT_PLUGIN_ABS_PATH' ) ) {
			define( __NAMESPACE__ . '\CURRENT_PLUGIN_ABS_PATH', plugin_dir_path( __DIR__ ) . CURRENT_PLUGIN_BASENAME );
		}

		// Plugin version. (Needs to be after BASENAME and ABS_PATH constants to work).
		if ( ! defined( __NAMESPACE__ . '\CURRENT_PLUGIN_VERSION' ) ) {
			$plugin_data    = get_plugin_data( CURRENT_PLUGIN_ABS_PATH );
			$plugin_version = $plugin_data['Version'];

			// Creates a Namespaced Constant in the Global Scope for this plugin.
			define( __NAMESPACE__ . '\CURRENT_PLUGIN_VERSION', $plugin_version );
		}

		// Plugin Folder Path.
		if ( ! defined( '\CURRENT_PLUGIN_PATH' ) ) {
			define( __NAMESPACE__ . '\CURRENT_PLUGIN_PATH', plugins_url() );
		}

		// Plugin Directory Path.
		if ( ! defined( __NAMESPACE__ . '\CURRENT_PLUGIN_FOLDER_DIR' ) ) {
			define( __NAMESPACE__ . '\CURRENT_PLUGIN_FOLDER_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Translation String Name.
		if ( ! defined( __NAMESPACE__ . '\CURRENT_PLUGIN_TEXT_DOMAIN' ) ) {
			define( __NAMESPACE__ . '\CURRENT_PLUGIN_TEXT_DOMAIN', $plugin_loaded->text_domain );
		}

		// Premium Plugin Directoy Path.
		if ( is_plugin_active( 'feed-them-gallery-premium/feed-them-gallery-premium.php' ) && ! defined( 'FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR' ) ) {
			define( 'FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR', WP_PLUGIN_DIR . '/feed-them-gallery-premium/feed-them-gallery-premium.php' );
		}
	}

	/**
	 * Create Instance of Feed Them Gallery
	 *
	 * @since 1.0.0
	 */
	public function pre_plugin_checks() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		// Third check the php version is not less than 5.2.9
		// Make sure php version is greater than 5.3!
		if ( function_exists( 'phpversion' ) ) {
			$phpversion = PHP_VERSION;
		}
		$phpcheck = '5.2.9';
		if ( $phpversion > $phpcheck ) {
			// Add actions.
			add_action( 'init', array( $this, 'ft_gallery_action_init' ) );
			// end if php version check.
		} else {
			// if the php version is not at least 5.3 do action.
			deactivate_plugins( CURRENT_PLUGIN_BASENAME );
			if ( $phpversion < $phpcheck ) {
				add_action( 'admin_notices', array( $this, 'ft_gallery_required_php_check1' ) );
			}
		}

		// Uncomment this to test. PHP check.
		// add_action( 'admin_notices', array( $this, 'ft_gallery_required_php_check1' ) );.
	}

	/**
	 * Includes Files
	 *
	 * Include files needed for Feed Them Gallery
	 *
	 * @since 1.0.0
	 */
	private static function includes() {

		// Admin Pages.
		include CURRENT_PLUGIN_FOLDER_DIR . 'admin/system-info.php';

		// Setup Functions Class.
		include CURRENT_PLUGIN_FOLDER_DIR . 'includes/setup-functions-class.php';

		// Core Functions Class.
		include CURRENT_PLUGIN_FOLDER_DIR . 'includes/core-functions-class.php';

		/*
		 * if ( is_plugin_active( 'feed-them-gallery-premium/feed-them-gallery-premium.php' ) ) {

			$ftgp_current_version = defined( 'FTGP_CURRENT_VERSION' ) ? FTGP_CURRENT_VERSION : '';

			if ( FTGP_CURRENT_VERSION > '1.0.5' ) {
				// Tags/Taxonomies for images.
				include FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR . 'includes/taxonomies/media-taxonomies.php';
			}
		}
		*/

		// Updater Classes.
		include CURRENT_PLUGIN_FOLDER_DIR . 'updater/updater-license-page.php';
		include CURRENT_PLUGIN_FOLDER_DIR . 'updater/updater-check-class.php';
		include CURRENT_PLUGIN_FOLDER_DIR . 'updater/updater-check-init.php';
	}

	/**
	 * Add Action Filters
	 *
	 * Load up all our styles and js.
	 *
	 * @since 1.0.0
	 */
	public function add_actions_filters() {
		register_activation_hook( __FILE__, array( $this, 'ftg_activate' ) );
		add_action( 'admin_notices', array( $this, 'ft_gallery_display_install_notice' ) );
		add_action( 'admin_notices', array( $this, 'ft_gallery_display_update_notice' ) );
		add_action( 'upgrader_process_complete', array( $this, 'ft_gallery_upgrade_completed', 10, 2 ) );

		// Include our own Settings link to plugin activation and update page.
		add_filter( 'plugin_action_links_' . CURRENT_PLUGIN_BASENAME, array( $this, 'ft_gallery_free_plugin_actions' ), 10, 4 );

		// Include Leave feedback, Get support and Plugin info links to plugin activation and update page.
		add_filter( 'plugin_row_meta', array( $this, 'ft_gallery_leave_feedback_link' ), 10, 2 );

		if ( is_plugin_active( 'feed-them-gallery-premium/feed-them-gallery-premium.php' ) && is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			/* AJAX add to cart variable  */
			add_action( 'wp_ajax_woocommerce_add_to_cart_variable_rc', array( $this, 'woocommerce_add_to_cart_variable_rc_callback_ftg' ) );
			add_action( 'wp_ajax_nopriv_woocommerce_add_to_cart_variable_rc', array( $this, 'woocommerce_add_to_cart_variable_rc_callback_ftg' ) );
		}

		// FT Gallery Activation Function.
		register_activation_hook( __FILE__, array( $this, 'ft_gallery_plugin_activation' ) );

		// Load plugin options.
		add_action( 'admin_init', array( $this, 'set_plugin_timezone' ) );
	}

	/**
	 * This function runs when WordPress completes its upgrade process
	 *
	 * It iterates through each plugin updated to see if ours is included
	 *
	 * @param array $upgrader_object Array The upgrader object.
	 * @param array $options Array The options.
	 * @since 1.0.0
	 */
	public function ft_gallery_upgrade_completed( $upgrader_object, $options ) {
		// The path to our plugin's main file.
		$our_plugin = CURRENT_PLUGIN_BASENAME;
		// If an update has taken place and the updated type is plugins and the plugins element exists.
		if ( 'update' === $options['action'] && 'plugin' === $options['type'] && isset( $options['plugins'] ) ) {
			// Iterate through the plugins being updated and check if ours is there.
			foreach ( $options['plugins'] as $plugin ) {
				if ( $plugin === $our_plugin ) {
					// Set a transient to record that our plugin has just been updated.
					set_transient( 'ftgallery_updated', 1 );
				}
			}
		}
	}

	/**
	 * Show a notice to anyone who has just updated this plugin
	 *
	 * This notice shouldn't display to anyone who has just installed the plugin for the first time
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_display_update_notice() {
		// Check the transient to see if we've just updated the plugin.
		if ( get_transient( 'ftgallery_updated' ) ) {
			echo sprintf(
				esc_html__( '%1$sThanks for updating Feed Them Social. We have deleted the cache in our plugin so you can view any changes we have made.%2$s', CURRENT_PLUGIN_TEXT_DOMAIN ),
				'<div class="notice notice-success updated is-dismissible"><p>',
				'</p></div>'
			);
			delete_transient( 'ftgallery_updated' );
		}
	}

	/**
	 * Show a notice to anyone who has just installed the plugin for the first time
	 *
	 * This notice shouldn't display to anyone who has just updated this plugin
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_display_install_notice() {
		// Check the transient to see if we've just activated the plugin.
		if ( get_transient( 'ftgallery_activated' ) ) {

			echo sprintf(
				esc_html__( '%1$sThanks for installing Feed Them Gallery. To get started please view our %2$sSettings%3$s page.%4$s', CURRENT_PLUGIN_TEXT_DOMAIN ),
				'<div class="notice notice-success updated is-dismissible"><p>',
				'<a href="' . esc_url( 'edit.php?post_type=ft_gallery&page=ft-gallery-settings-page' ) . '">',
				'</a>',
				'</p></div>'
			);
			// Delete the transient so we don't keep displaying the activation message.
			delete_transient( 'ftgallery_activated' );
		}
	}

	/**
	 * Run this on activation
	 *
	 * Set a transient so that we know we've just activated the plugin
	 *
	 * @since 1.0.0
	 */
	public function ftg_activate() {
		set_transient( 'ftgallery_activated', 1 );
	}

	/**
	 * FT Gallery Action Init
	 *
	 * Loads language files
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_action_init() {
		// Localization.
		load_plugin_textdomain( CURRENT_PLUGIN_TEXT_DOMAIN, false, CURRENT_PLUGIN_BASENAME . '/languages' );
	}

	/**
	 * FT Gallery Required php Check
	 *
	 * Are they running proper PHP version
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_required_php_check1() {
		echo sprintf(
			esc_html__( '%1$sWarning:%2$s Your php version is %3$s. You need to be running at least 5.3 or greater to use this plugin. Please upgrade the php by contacting your host provider. Some host providers will allow you to change this yourself in the hosting control panel too.%4$sIf you are hosting with BlueHost or Godaddy and the php version above is saying you are running 5.2.17 but you are really running something higher please %5$sclick here for the fix%6$s. If you cannot get it to work using the method described in the link please contact your host provider and explain the problem so they can fix it.%7$s', CURRENT_PLUGIN_TEXT_DOMAIN ),
			'<div class="error"><p><strong>',
			'</strong>',
			PHP_VERSION,
			'<br/><br/>',
			'<a href="' . esc_url( 'https://wordpress.org/support/topic/php-version-difference-after-changing-it-at-bluehost-php-config?replies=4' ) . '" target="_blank">',
			'</a>',
			'</p></div>'
		);
	}

	/**
	 * FT Gallery Plugin Actions
	 *
	 * Loads links in the Plugins page in WordPress Dashboard
	 *
	 * @param string $actions What action to take.
	 * @return mixed
	 * @since 1.0.0
	 */
	public function ft_gallery_free_plugin_actions( $actions ) {
		array_unshift(
			$actions,
			sprintf(
				esc_html__( '%1$sSettings%2$s | %3$sSupport%4$s', CURRENT_PLUGIN_TEXT_DOMAIN ),
				'<a href="' . esc_url( 'edit.php?post_type=ft_gallery&page=ft-gallery-settings-page' ) . '">',
				'</a>',
				'<a href="' . esc_url( 'https://www.slickremix.com/support/' ) . '">',
				'</a>'
			)
		);
		return $actions;
	}

	/**
	 * FT Gallery Leave Feedback Link
	 *
	 * Link to add feedback for plugin
	 *
	 * @param string $links The link to show.
	 * @param string $file The file basename.
	 * @return mixed
	 * @since 1.0.0
	 */
	public function ft_gallery_leave_feedback_link( $links, $file ) {
		if ( CURRENT_PLUGIN_BASENAME === $file ) {
			$links['feedback'] = sprintf(
				esc_html__( '%1$sRate Plugin%2$s', CURRENT_PLUGIN_TEXT_DOMAIN ),
				'<a href="' . esc_url( 'https://wordpress.org/support/plugin/feed-them-gallery/reviews/' ) . '" target="_blank">',
				'</a>'
			);

			// $links['support'] = '<a href="http://www.slickremix.com/support-forum/forum/feed-them-gallery-2/" target="_blank">' . __('Get support', 'feed-them-premium') . '</a>';
			// $links['plugininfo']  = '<a href="plugin-install.php?tab=plugin-information&plugin=feed-them-premium&section=changelog&TB_iframe=true&width=640&height=423" class="thickbox">' . __( 'Plugin info', 'gd_quicksetup' ) . '</a>';
		}
		return $links;
	}

	/**
	 * FT Gallery Plugin Activation
	 *
	 * Loads options upon FT Gallery Activation
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_plugin_activation() {
		// we add an db option to check then delete the db option after activation and the cache has emptied.
		// the delete_option is on the feed-them-functions.php file at the bottom of the function ftg_clear_cache_script.
		add_option( 'BOILER_PLATE_Activated_Plugin', 'feed-them-gallery' );

	}

	/**
	 * Set Plugin TimeZone
	 *
	 * Load plugin options on activation check
	 *
	 * @since 1.0.0
	 */
	public function set_plugin_timezone() {

		if ( is_admin() && 'feed-them-gallery' === get_option( 'BOILER_PLATE_Activated_Plugin' ) ) {

			// Options List.
			$activation_options = array(
				'ft-gallery-date-and-time-format' => 'one-day-ago',
				'ft-gallery-timezone'             => 'America/New_York',
			);

			foreach ( $activation_options as $option_key => $option_value ) {
				// We don't use update_option because we only want this to run for options that have not already been set by the user.
				add_option( $option_key, $option_value );
			}
		}
	}

	/**
	 * FTG Set Review Transient
	 *
	 * Set a transient if the notice has not been dismissed or has not been set yet
	 *
	 * @param string $transient Check the transient exists or not.
	 * @param string $option The option to check for.
	 * @return mixed
	 * @since 1.0.8
	 */
	public function ftg_maybe_set_transient( $transient, $option ) {
		$ftg_rating_notice_waiting = get_transient( $transient );
		$notice_status             = get_option( $option, false );

		if ( ! $ftg_rating_notice_waiting && ! ( 'dismissed' === $notice_status || 'pending' === $notice_status ) ) {
			$time = 2 * WEEK_IN_SECONDS;
			set_transient( $transient, 'ftg-review-waiting', $time );
			update_option( $option, 'pending' );
		}
	}

	/**
	 * FTG Review Check
	 *
	 * Checks $_GET to see if the nag variable is set and what it's value is
	 *
	 * @param string $get See what the $_GET url is.
	 * @param string $nag See if we are nagging 1 or 0.
	 * @param string $option The option to check for.
	 * @param string $transient Check the transient exists or not.
	 * @since 1.0.8
	 */
	public function ftg_check_nag_get( $get, $nag, $option, $transient ) {

		if ( isset( $_GET[ $nag ] ) ) {
			if ( 1 === $get[ $nag ] ) {
				update_option( $option, 'dismissed' );
			} elseif ( 'later' === $get[ $nag ] ) {
				$time = 2 * WEEK_IN_SECONDS;
				set_transient( $transient, 'ftg-review-waiting', $time );
				update_option( $option, 'pending' );
			}
		}
	}

	/**
	 * Set Review Status
	 *
	 * Checks to see what the review status is.
	 *
	 * @param string $option The option to check for.
	 * @param string $transient Check the transient exists or not.
	 * @since 1.0.8
	 */
	public function set_review_status( $option, $transient ) {
		$notice_status = get_option( $option, false );

		// Only display the notice if the time offset has passed and the user hasn't already dismissed it!.
		if ( 'ftg-review-waiting' !== get_transient( $transient ) && 'dismissed' !== $notice_status ) {
			add_action( 'admin_notices', array( $this, 'ftg_rating_notice_html' ) );
		}
		// Uncomment this for testing the notice.
		// add_action( 'admin_notices', array( $this, 'ftg_rating_notice_html' ) );.
	}

	/**
	 * FTG Ratings Notice
	 *
	 * Generates the html for the admin notice
	 *
	 * @since 1.0.8
	 */
	public function ftg_rating_notice_html() {
		// Only show to admins.
		if ( current_user_can( 'manage_options' ) ) {
			global $current_user;
			$user_id = $current_user->ID;
			/* Has the user already clicked to ignore the message? */
			if ( ! get_user_meta( $user_id, 'ftg_slick_ignore_rating_notice' ) ) {
				?>
				<div class="ftg_notice ftg_review_notice">
					<img src="<?php echo esc_url( plugins_url( 'feed-them-gallery/admin/css/ft-gallery-logo.png' ) ); ?>" alt="Feed Them Gallery">
					<div class='ftg-notice-text'>
						<p><?php echo esc_html( 'It\'s great to see that you\'ve been using our Feed Them Gallery plugin for a while now. Hopefully you\'re happy with it!  If so, would you consider leaving a positive review? It really helps support the plugin and helps others discover it too!', CURRENT_PLUGIN_TEXT_DOMAIN ); ?></p>
						<p class="ftg-links">
							<a class="ftg_notice_dismiss" href="<?php echo esc_url( 'https://wordpress.org/support/plugin/feed-them-gallery/reviews/#new-post' ); ?>" target="_blank"><?php echo esc_html__( 'Sure, I\'d love to', CURRENT_PLUGIN_TEXT_DOMAIN ); ?></a>
							<a class="ftg_notice_dismiss" href="<?php echo esc_url( add_query_arg( 'ftg_slick_ignore_rating_notice_nag', '1' ) ); ?>"><?php echo esc_html__( 'I\'ve already given a review', CURRENT_PLUGIN_TEXT_DOMAIN ); ?></a>
							<a class="ftg_notice_dismiss" href="<?php echo esc_url( add_query_arg( 'ftg_slick_ignore_rating_notice_nag', 'later' ) ); ?>"><?php echo esc_html__( 'Ask me later', CURRENT_PLUGIN_TEXT_DOMAIN ); ?> </a>
							<a class="ftg_notice_dismiss" href="<?php echo esc_url( 'https://wordpress.org/support/plugin/feed-them-gallery/#new-post' ); ?>" target="_blank"><?php echo esc_html__( 'Not working, I need support', CURRENT_PLUGIN_TEXT_DOMAIN ); ?></a>
							<a class="ftg_notice_dismiss" href="<?php echo esc_url( add_query_arg( 'ftg_slick_ignore_rating_notice_nag', '1' ) ); ?>"><?php echo esc_html__( 'No thanks', CURRENT_PLUGIN_TEXT_DOMAIN ); ?></a>
						</p>

					</div>
				</div>

				<?php
			}
		}
	}

	/**
	 * FT Gallery System Version
	 *
	 * Returns current plugin version (Must be outside the final class to work)
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public static function ft_gallery_check_version() {

		$plugin_data = get_plugin_data( PLUGIN_ABS_PATH );

		return $plugin_data['Version'];
	}
}
