<?php
 /** 
 * This file is invoked when the frontend is viewed.
 *
 * Loads all the necessary CSS, JavaScript, and PHP files.
 *
 * @package    FreshWeb_Card_Deck
 * @subpackage Functions
 * @copyright  Copyright (c) 2017, freshwebstudio.com
 * @link       https://freshwebstudio.com
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.9.1
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class wrapper for all methods.
 *
 * @since 0.9.1
 */
class FW_Carddeck_Front {
    
    function __construct()  {
        
        // Load scripts and stylesheets.
        add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );

    }

    /**
     * Enqueue our scripts and stylesheets.
     *
     * @since 0.9.1
     *
     */
    public function register_scripts() {

        wp_register_script(
            'fw_jquery_waypoints_script',
            FW_CARDDECK_FRONT_JS_URL . '/jquery.waypoints.js',
            array( 'jquery' ),
            FW_CARDDECK_VERSION,
            true
        );
        wp_enqueue_script( 'fw_jquery_waypoints_script' );

        wp_register_script(
            'fw_jquery_animate_carddeck_script',
            FW_CARDDECK_FRONT_JS_URL . '/jquery.animateCardDeck.js',
            array( 'jquery', 'fw_jquery_waypoints_script' ),
            FW_CARDDECK_VERSION,
            true
        );
        wp_enqueue_script( 'fw_jquery_animate_carddeck_script' );

        wp_register_style(
            'fw_carddeck_styles',
            FW_CARDDECK_FRONT_CSS_URL . '/style.css', 
            array(), 
            FW_CARDDECK_VERSION
        );
        wp_enqueue_style( 'fw_carddeck_styles' );
        
    }

}