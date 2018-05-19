<?php
 /** 
 * This file is invoked when the admin backend is viewed.
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
class FW_Carddeck_Admin {
    
    function __construct()  { 
        
        // Load scripts and stylesheets.
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

    }

    /**
     * Enqueue our scripts and stylesheets.
     *
     * @since 0.9.1
     *
     */
    public function enqueue_scripts() {

        global $typenow;
        
        /*
         * Enqueue our stylesheet only if we're on our CPT pages.
         *
         * Note: $typenow seems to work on any action called after 'admin_init'.
         */
        if ( FW_CARDDECK_POST_TYPE_ID === $typenow ) {

            wp_enqueue_style(
                'fw_carddeck_admin_styles',
                FW_CARDDECK_ADMIN_CSS_URL . '/style.css',
                array(), 
                FW_CARDDECK_VERSION
            );

        }

    }

}