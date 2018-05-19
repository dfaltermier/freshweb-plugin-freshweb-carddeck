<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * Plugin Name:    Freshweb Card Deck
 * Plugin URI:     https://freshwebstudio.beanstalkapp.com
 * Description:    Create and manage an image card deck spread out on a flat surface.
 * Version:        1.0.1
 * Author:         Freshweb Studio
 * Author URI:     https://freshwebstudio.com
 * Text Domain:    freshweb-carddeck
 * License:        GNU General Public License v2 or later
 * License URI:    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * 
 * @package    FreshWeb_Card_Deck
 * @subpackage Functions
 * @copyright  Copyright (c) 2017, freshwebstudio.com
 * @link       https://freshwebstudio.com
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.9.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-fw-carddeck.php';

/* 
 * Activate plugin.
 *
 * When adding custom post types and taxonomies, we must flush the 
 * rewrite rules or else the user may see a "Page Not Found" error.
 * Be sure to register the CPT and taxonomies before flushing!
 * Why do this? See https://codex.wordpress.org/Function_Reference/flush_rewrite_rules
 *
 * @since 0.9.1
 */
function fw_carddeck_activation() {
    
    // Register the Sermon post type.
    require_once FW_CARDDECK_INCLUDES_DIR . '/class-fw-carddeck-post-types.php';
    $post_types = new FW_Carddeck_Post_Types;
    $post_types->register_post_types();
    $post_types->register_taxonomies(); // Necessary? Does this add rewrite rules?
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'fw_carddeck_activation' );

/* 
 * Deactivate plugin.
 *
 * @since 0.9.1
 */
function fw_carddeck_deactivation() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'fw_carddeck_deactivation' );

/**
 * Begin execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks, then kicking off the
 * plugin from this point in the file does not affect the page life cycle.
 *
 * @since 0.9.1
 */
function fw_carddeck_run() {
    $plugin = new FW_Carddeck();
    $plugin->run();
}

fw_carddeck_run();
