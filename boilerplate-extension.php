<?php
/**
 * BOILERPLATE EXTENSION
 *
 * This class is what initiates the Feed Them Gallery class
 *
 * Plugin Name: BOILERPLATE EXTENSION
 * Plugin URI: https://www.slickremix.com/
 * Description: This is a BOILERPLATE EXTENSION for creating extensions for SlickRemix plugins.
 * Version: 1.0.0
 * Author: SlickRemix
 * Author URI: https://www.slickremix.com/
 * Text Domain: boilerplate-extension
 * Domain Path: /languages
 * Requires at least: WordPress 4.7.0
 * Tested up to: WordPress 5.2.2
 * Stable tag: 1.0.0
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * WC requires at least: 3.0.0
 * WC tested up to: 3.6.5
 *
 * @version  1.0.0
 * @package  BOILERPLATE_EXTENSION/Core
 * @copyright   Copyright (c) 2012-2019 SlickRemix
 *
 * Need Support? https://www.slickremix.com/my-account
 */



/*
 *  TO MAKE NEW PLUGIN/EXTENSION OUT OF THIS BOILERPLATE.
 * 1 - 'Find and Replace' all BOILER_PLATE_NAMESPACE with plugin namespace.
 * 2 - 'Find and Replace' BOILER_PLATE_CLASS with main plugin class name.
 * 3 - Change 'plugin path' variable on class-load-plugin.php file.
 * 4 - Change 'Text Domain' name for the plugin on class-load-plugin.php file this changes it accross the entire plugin.Í
 */

// Doing this ensure's any js or css changes are reloaded properly. Added to enqueued css and js files throughout.
define( 'BOILERPLATE_CURRENT_VERSION', '1.0.0' );


// Require file for plugin loading.
require_once __DIR__ . '/class-load-plugin.php';

// Load the Plugin from the main class!
BOILER_PLATE_NAMESPACE\BOILER_PLATE_CLASS::load_plugin();
