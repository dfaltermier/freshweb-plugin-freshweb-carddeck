<?php
 /** 
 * Bootstrapping class.
 *
 * All of our plugin dependencies are initalized here.
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
 * Class wrapper for all methods.
 *
 * @since 0.9.1
 */
class FW_Carddeck {
    
    function __construct()  { 
    }

    /**
     * Run our initialization.
     *
     * @since 0.9.1
     */
    public function run() {

        $this->setup_constants();
        $this->includes();

        // Hook any WP init actions here.
        add_action( 'init', array( $this, 'initialize_wp' ) );

    }

    /**
     * Hook any WP init actions here.
     *
     * @since 0.9.1
     */
    public function initialize_wp() {

        /*
         * Each [image] card should be at least 600px x 390px. For all
         * sizes, the aspect ration should be preserved at 0.65.
         */
        if ( function_exists( 'add_image_size' ) ) { 
            add_image_size( 
                FW_CARDDECK_IMAGE_SIZE_LARGE_NAME,
                FW_CARDDECK_IMAGE_SIZE_LARGE_X,
                FW_CARDDECK_IMAGE_SIZE_LARGE_Y,
                false
            ); // 0.65
            add_image_size( 
                FW_CARDDECK_IMAGE_SIZE_MEDIUM_NAME,
                FW_CARDDECK_IMAGE_SIZE_MEDIUM_X,
                FW_CARDDECK_IMAGE_SIZE_MEDIUM_Y,
                false
            ); // 0.65
            add_image_size( 
                FW_CARDDECK_IMAGE_SIZE_SMALL_NAME,
                FW_CARDDECK_IMAGE_SIZE_SMALL_X,
                FW_CARDDECK_IMAGE_SIZE_SMALL_Y,
                false
            ); // 0.65
            add_image_size( 
                FW_CARDDECK_IMAGE_SIZE_XSMALL_NAME,
                FW_CARDDECK_IMAGE_SIZE_XSMALL_X,
                FW_CARDDECK_IMAGE_SIZE_XSMALL_Y,
                false
            ); // 0.65
        }
    }

    /**
     * Setup plugin constants.
     *
     * @since  0.9.1
     * @access private
     */
    private function setup_constants() {

        /*
         * Set true if plugin is to be detected by theme writers as activated.
         *
         * Theme writers: Use this defined variable to determine if plugin is installed
         * and activated. False means No, True means yes.
         */
        if ( ! defined( 'FW_CARDDECK_IS_ACTIVATED' ) ) {
            define( 'FW_CARDDECK_IS_ACTIVATED', true );
        }     

        // Plugin version.
        if ( ! defined( 'FW_CARDDECK_VERSION' ) ) {
            define( 'FW_CARDDECK_VERSION', '0.9.1' );
        }

        // Plugin Folder Path (without trailing slash)
        if ( ! defined( 'FW_CARDDECK_PLUGIN_DIR' ) ) {
            define( 'FW_CARDDECK_PLUGIN_DIR', dirname( __DIR__ ) );
        }

        // Includes Folder Path (without trailing slash)
        if ( ! defined( 'FW_CARDDECK_INCLUDES_DIR' ) ) {
            define( 'FW_CARDDECK_INCLUDES_DIR', FW_CARDDECK_PLUGIN_DIR . '/includes' );
        }

        // Plugin Folder URL (without trailing slash)
        if ( ! defined( 'FW_CARDDECK_PLUGIN_URL' ) ) {
            define( 'FW_CARDDECK_PLUGIN_URL', untrailingslashit( plugin_dir_url( __DIR__ ) ) );
        }

        // Includes Folder URL (without trailing slash)
        if ( ! defined( 'FW_CARDDECK_INCLUDES_URL' ) ) {
            define( 'FW_CARDDECK_INCLUDES_URL', FW_CARDDECK_PLUGIN_URL . '/includes' );
        }

        // Admin CSS Folder URL (without trailing slash)
        if ( ! defined( 'FW_CARDDECK_ADMIN_CSS_URL' ) ) {
            define( 'FW_CARDDECK_ADMIN_CSS_URL', FW_CARDDECK_PLUGIN_URL . '/admin/css' );
        }

        // Admin images Folder URL (without trailing slash)
        if ( ! defined( 'FW_CARDDECK_ADMIN_IMAGES_URL' ) ) {
            define( 'FW_CARDDECK_ADMIN_IMAGES_URL', FW_CARDDECK_PLUGIN_URL . '/admin/images' );
        }

        // Front CSS Folder URL (without trailing slash)
        if ( ! defined( 'FW_CARDDECK_FRONT_CSS_URL' ) ) {
            define( 'FW_CARDDECK_FRONT_CSS_URL', FW_CARDDECK_PLUGIN_URL . '/front/css' );
        }

        // Front JS Folder URL (without trailing slash)
        if ( ! defined( 'FW_CARDDECK_FRONT_JS_URL' ) ) {
            define( 'FW_CARDDECK_FRONT_JS_URL', FW_CARDDECK_PLUGIN_URL . '/front/js' );
        }

        // Front images Folder URL (without trailing slash)
        if ( ! defined( 'FW_CARDDECK_FRONT_IMAGES_URL' ) ) {
            define( 'FW_CARDDECK_FRONT_IMAGES_URL', FW_CARDDECK_PLUGIN_URL . '/front/images' );
        }

        /*
         * Define CPT and taxonomy names in one place globally.
         */
        if ( ! defined( 'FW_CARDDECK_POST_TYPE_ID' ) ) {
            define( 'FW_CARDDECK_POST_TYPE_ID', 'fw_carddeck' );
        }

        if ( ! defined( 'FW_CARDDECK_TAXONOMY_DECK_ID' ) ) {
            define( 'FW_CARDDECK_TAXONOMY_DECK_ID', 'fw_carddeck_deck' );
        }

        if ( ! defined( 'FW_CARDDECK_POST_TYPE_META_BOX_ID' ) ) {
            define( 'FW_CARDDECK_POST_TYPE_META_BOX_ID', 'fw_carddeck_details' );
        }

        if ( ! defined( 'FW_CARDDECK_POST_TYPE_META_FIELD_CARD_NUMBER_ID' ) ) {
            define( 'FW_CARDDECK_POST_TYPE_META_FIELD_CARD_NUMBER_ID', '_fw_carddeck_card_number' );
        }

        // Define the range allowed for card numbers.
        if ( ! defined( 'FW_CARDDECK_POST_TYPE_META_FIELD_CARD_NUMBER_MIN' ) ) {
            define( 'FW_CARDDECK_POST_TYPE_META_FIELD_CARD_NUMBER_MIN', 1 );
        }

        if ( ! defined( 'FW_CARDDECK_POST_TYPE_META_FIELD_CARD_NUMBER_MAX' ) ) {
            define( 'FW_CARDDECK_POST_TYPE_META_FIELD_CARD_NUMBER_MAX', 21 );
        }

        /*
         * Define shortcode names in one place globally.
         */
        if ( ! defined( 'FW_CARDDECK_SHORTCODE_NAME' ) ) {
            define( 'FW_CARDDECK_SHORTCODE_NAME', 'fw_carddeck' );
        }

        /*
         * Define image sizes
         */
        if ( ! defined( 'FW_CARDDECK_IMAGE_SIZE_LARGE_NAME' ) ) {
            define( 'FW_CARDDECK_IMAGE_SIZE_LARGE_NAME', 'fw_carddeck_large' );
        }
        if ( ! defined( 'FW_CARDDECK_IMAGE_SIZE_LARGE_X' ) ) {
            define( 'FW_CARDDECK_IMAGE_SIZE_LARGE_X', 600 );
        }
        if ( ! defined( 'FW_CARDDECK_IMAGE_SIZE_LARGE_Y' ) ) {
            define( 'FW_CARDDECK_IMAGE_SIZE_LARGE_Y', 390 );
        }

        if ( ! defined( 'FW_CARDDECK_IMAGE_SIZE_MEDIUM_NAME' ) ) {
            define( 'FW_CARDDECK_IMAGE_SIZE_MEDIUM_NAME', 'fw_carddeck_medium' );
        }
        if ( ! defined( 'FW_CARDDECK_IMAGE_SIZE_MEDIUM_X' ) ) {
            define( 'FW_CARDDECK_IMAGE_SIZE_MEDIUM_X', 480 );
        }
        if ( ! defined( 'FW_CARDDECK_IMAGE_SIZE_MEDIUM_Y' ) ) {
            define( 'FW_CARDDECK_IMAGE_SIZE_MEDIUM_Y', 312 );
        }

        if ( ! defined( 'FW_CARDDECK_IMAGE_SIZE_SMALL_NAME' ) ) {
            define( 'FW_CARDDECK_IMAGE_SIZE_SMALL_NAME', 'fw_carddeck_small' );
        }
        if ( ! defined( 'FW_CARDDECK_IMAGE_SIZE_SMALL_X' ) ) {
            define( 'FW_CARDDECK_IMAGE_SIZE_SMALL_X', 300 );
        }
        if ( ! defined( 'FW_CARDDECK_IMAGE_SIZE_SMALL_Y' ) ) {
            define( 'FW_CARDDECK_IMAGE_SIZE_SMALL_Y', 195 );
        }

        if ( ! defined( 'FW_CARDDECK_IMAGE_SIZE_XSMALL_NAME' ) ) {
            define( 'FW_CARDDECK_IMAGE_SIZE_XSMALL_NAME', 'fw_carddeck_xsmall' );
        }
        if ( ! defined( 'FW_CARDDECK_IMAGE_SIZE_XSMALL_X' ) ) {
            define( 'FW_CARDDECK_IMAGE_SIZE_XSMALL_X', 220 );
        }
        if ( ! defined( 'FW_CARDDECK_IMAGE_SIZE_XSMALL_Y' ) ) {
            define( 'FW_CARDDECK_IMAGE_SIZE_XSMALL_Y', 143 );
        }

    }

    /**
     * Include required files.
     *
     * @since  0.9.1
     * @access private
     */
    private function includes() {

        if ( is_admin() )  {
            require_once FW_CARDDECK_INCLUDES_DIR . '/class-fw-carddeck-admin.php';
            $admin = new FW_Carddeck_Admin;
        }
        else {
            require_once FW_CARDDECK_INCLUDES_DIR . '/class-fw-carddeck-front.php';
            $front = new FW_Carddeck_Front;
        }

        require_once FW_CARDDECK_INCLUDES_DIR . '/class-fw-carddeck-post-types.php';
        $post_types = new FW_Carddeck_Post_Types;

        require_once FW_CARDDECK_INCLUDES_DIR . '/class-fw-carddeck-meta-box.php';
        $meta_boxes = new FW_Carddeck_Meta_Box;

        require_once FW_CARDDECK_INCLUDES_DIR . '/class-fw-carddeck-shortcodes.php';
        $shortcodes = new FW_Carddeck_Shortcodes;

    }

}