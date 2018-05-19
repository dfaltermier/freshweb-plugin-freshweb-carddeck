<?php
 /** 
 * This class creates a meta box for our custom post type.
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
class FW_Carddeck_Shortcodes {
    
    function __construct()  {

        // Perform WP inits.
        add_action( 'init', array( $this, 'register_shortcodes') );

    }
    
    /**
     * Perform WP inits.
     *
     * @since  0.9.1
     */
    public function register_shortcodes() {

        add_shortcode( FW_CARDDECK_SHORTCODE_NAME, array( $this, 'process_shortcode_carddeck' ) );

    }

    /**
     * Process our FW_CARDDECK_SHORTCODE_NAME shortcode.
     *
     * Shortcode examples:
     *
     *   [fw_carddeck deck="primary" class="page-home-portfolio__carddeck" effect="none"]
     *
     *   [fw_carddeck deck="primary" class="page-home-portfolio__carddeck" effect="fade_all"
     *    fade_in_duration="2000"]
     *
     *   [fw_carddeck deck="primary" class="page-home-portfolio__carddeck" effect="fade_each"
     *    image_order="default" delay_between_images="100" fade_in_duration="2000"]
     *
     * Dependencies:
     *   1. jquery.waypoints.js
     *   2. jquery.animateCardDeck.js
     *
     * @since  0.9.1
     */
    public function process_shortcode_carddeck( $attrs ) {

        // Buffer output
        ob_start();

        $attrs = shortcode_atts(
            array(
                'deck'      => '',          // card deck [taxonomy term]
                'effect'    => 'fade_each', // One of: none, fade_all, or fade_each
                'fade_all'  => '',
                'fade_each' => '',
                'class'     => ''           // Optional CSS classnames
            ),
            $attrs,
            FW_CARDDECK_SHORTCODE_NAME
        );

        switch( $attrs['effect'] ) {

            case 'fade_all':
                $fade_all_options = array();

                // Remove all spaces from options string
                preg_replace('/\s+/', '', $attrs['fade_all']);

                // Create an associative array of options from string
                parse_str( strtr( $attrs['fade_all'], ':,', '=&' ), $fade_all_options );

                $fade_all_options = shortcode_atts(
                    array(
                        'fade_in_duration' => '2000',   // milliseconds
                        'start_opacity'    => '0.3'     // CSS opacity value
                    ),
                    $fade_all_options
                );

                $attrs['fade_all'] = $fade_all_options;
                break;

            case 'fade_each':
                $fade_each_options = array();

                // Remove all spaces from options string
                preg_replace('/\s+/', '', $attrs['fade_each']);

                // Create an associative array of options from string
                parse_str( strtr( $attrs['fade_each'], ':,', '=&' ), $fade_each_options );

                $fade_each_options = shortcode_atts(
                    array(
                        'image_order'          => 'default',
                        'delay_between_images' => '200',
                        'fade_in_duration'     => '600',   // milliseconds
                        'start_opacity'        => '0.3'    // CSS opacity value
                    ),
                    $fade_each_options
                );

                $attrs['fade_each'] = $fade_each_options;
                break;

            default:
                $attrs['effect'] = 'none';
                break;

        }

        // We'll use this value as a unique identifier for each carddeck instance.
        static $shortcode_id = 0;

        // Map card number to the image size.
        $card_sizes = array(
            '1'  => FW_CARDDECK_IMAGE_SIZE_XSMALL_NAME,
            '2'  => FW_CARDDECK_IMAGE_SIZE_XSMALL_NAME,
            '3'  => FW_CARDDECK_IMAGE_SIZE_SMALL_NAME,
            '4'  => FW_CARDDECK_IMAGE_SIZE_MEDIUM_NAME,
            '5'  => FW_CARDDECK_IMAGE_SIZE_XSMALL_NAME,
            '6'  => FW_CARDDECK_IMAGE_SIZE_SMALL_NAME,
            '7'  => FW_CARDDECK_IMAGE_SIZE_LARGE_NAME,
            '8'  => FW_CARDDECK_IMAGE_SIZE_SMALL_NAME,
            '9'  => FW_CARDDECK_IMAGE_SIZE_XSMALL_NAME,
            '10' => FW_CARDDECK_IMAGE_SIZE_MEDIUM_NAME,
            '11' => FW_CARDDECK_IMAGE_SIZE_MEDIUM_NAME,
            '12' => FW_CARDDECK_IMAGE_SIZE_XSMALL_NAME,
            '13' => FW_CARDDECK_IMAGE_SIZE_XSMALL_NAME,
            '14' => FW_CARDDECK_IMAGE_SIZE_SMALL_NAME,
            '15' => FW_CARDDECK_IMAGE_SIZE_XSMALL_NAME,
            '16' => FW_CARDDECK_IMAGE_SIZE_XSMALL_NAME,
            '17' => FW_CARDDECK_IMAGE_SIZE_LARGE_NAME,
            '18' => FW_CARDDECK_IMAGE_SIZE_LARGE_NAME,
            '19' => FW_CARDDECK_IMAGE_SIZE_SMALL_NAME,
            '20' => FW_CARDDECK_IMAGE_SIZE_SMALL_NAME,
            '21' => FW_CARDDECK_IMAGE_SIZE_MEDIUM_NAME
        );

        // Store all of our card images in html markup here.
        $card_images = array();

        /*
         * Initialize all cards with placeholder images. We'll overwrite these
         * images with the cards below.
         */
        for( $i = 1; $i <= count($card_sizes); $i++ ) {
            $card_images[$i] = 
                '<img class="fw-js-carddeck-image' .                 // For our JavaScript to hook to.
                ' fw-carddeck-image fw-carddeck-image-placeholder' . // Remaining styles for CSS.
                ' fw-carddeck-image-size-'. $card_sizes[strval( $i )] .
                ' fw-carddeck-image-position-' . $i . '"' .
                ' src="' . FW_CARDDECK_FRONT_IMAGES_URL . '/card-placeholder.600x390.png"' .
                ' alt="Portfolio image goes here." />';
        }

        // Prepare to query.
        $args = array(
            'post_type'      => FW_CARDDECK_POST_TYPE_ID,
            'posts_per_page' => FW_CARDDECK_POST_TYPE_META_FIELD_CARD_NUMBER_MAX,
            'post_status'    => 'publish'
        );

        // Get our cards for the given deck [taxonomy].
        if ( ! empty( $attrs['deck'] ) ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => FW_CARDDECK_TAXONOMY_DECK_ID,
                    'field'    => 'slug',
                    'terms'    => $attrs['deck']
                )
            );
        }

        // Perform our DB query.
        $query = new WP_Query( $args );

        if ( $query->have_posts() ) {

            while ( $query->have_posts() ) {
                $query->the_post();  

                $card_number = get_post_meta(
                    get_the_ID(),
                    FW_CARDDECK_POST_TYPE_META_FIELD_CARD_NUMBER_ID,
                    true
                );

                // Sanity check. Must have card number.
                if ( empty( $card_number ) ) {
                    continue;
                }

                // Sanity check. String must eval to a valid integer above 0 (error).
                if ( intval( $card_number ) < 1 ) {
                    continue;
                }

                // Sanity check. Our card number must be within the correct range to map to our layout.
                if ( ( intval($card_number) < FW_CARDDECK_POST_TYPE_META_FIELD_CARD_NUMBER_MIN ) ||
                     ( intval($card_number) > FW_CARDDECK_POST_TYPE_META_FIELD_CARD_NUMBER_MAX ) ) {
                    continue;
                }                

                // Get image html. Override our placeholder cards.
                if ( has_post_thumbnail() ) {

                    $img = get_the_post_thumbnail(
                        get_the_ID(),
                        $card_sizes[$card_number], // Maps the number to the image size we registered.
                        array(
                            'class' => 'fw-js-carddeck-image' .  // For our JavaScript to hook to.
                            ' fw-carddeck-image' .               // Remaining styles for CSS.
                            ' fw-carddeck-image-size-'. $card_sizes[$card_number] .
                            ' fw-carddeck-image-position-' . $card_number .
                            ' fw-carddeck-image-hidden'
                        )
                    );

                    $card_images[$card_number] = $img;

                }

            }

            wp_reset_postdata();
        }

        // Wrap our cards in a container. Assign a unique classname for this instance.
        echo '<section class="fw-carddeck fw-carddeck-' . $shortcode_id . ' ' . $attrs["class"] . '">' . "\n";

        /*
         * This empty image is used as a board on which we place all of the
         * image cards. All of the cards will be positioned 'absolute' so this
         * empty image will preserve the height and width of our display area
         * as the browser (view) is resized.
         */
        echo '<img class="fw-carddeck-map fw-carddeck-map-desktop"' .
            ' src="' . FW_CARDDECK_FRONT_IMAGES_URL . '/carddeck-placement-map-desktop-blank-min.png"' .
            //' src="' . FW_CARDDECK_ADMIN_IMAGES_URL . '/carddeck-placement-map-desktop-min.png"' .
            ' alt="Placeholder desktop image map." />';

        // Do the same for the mobile view.
        echo '<img class="fw-carddeck-map fw-carddeck-map-mobile"' .
            ' src="' . FW_CARDDECK_FRONT_IMAGES_URL . '/carddeck-placement-map-mobile-blank-min.png"' .
            // ' src="' . FW_CARDDECK_ADMIN_IMAGES_URL . '/carddeck-placement-map-mobile-min.png"' .
            ' alt="Placeholder mobile image map." />';

        // Cycle through and spit out the cards in html.
        foreach ( $card_images as $card_image ) {
            echo $card_image . "\n";
        }

        // Close up html.
        echo '</section>' . "\n";

        $js_parameters = '';
        $js_opacity    = '0';

        switch( $attrs['effect'] ) {

            case 'fade_all':
                $js_parameters = 
                    "effect: 'fadeAll'," .
                    "fadeAll: {fadeInDuration: '" . $attrs['fade_all']['fade_in_duration'] . "'}";
                $js_opacity = $attrs['fade_all']['start_opacity'];
                break;

            case 'fade_each':
                $js_parameters = 
                    "effect: 'fadeEach'," .
                    "fadeEach: {" .
                        "imageOrder: '"         . $attrs['fade_each']['image_order'] . "'," .
                        "delayBetweenImages: '" . $attrs['fade_each']['delay_between_images'] . "'," .
                        "fadeInDuration: '"     . $attrs['fade_each']['fade_in_duration'] . "'," .
                    "}";
                $js_opacity = $attrs['fade_each']['start_opacity'];
                break;

            case 'none':
            default:
                $js_parameters = "effect: 'none'";
                $js_opacity    = '1';
                break;

        }

        // Attach the JavaScript animation behavior to this card deck instance.   
        echo <<<END
            <script type="text/javascript">
             
                (function($) {
                    'use strict';

                    // Start with all images at the given opacity.
                    $('.fw-js-carddeck-image', '.fw-carddeck-{$shortcode_id}')
                        .css({opacity: '{$js_opacity}'});

                    $(function() {
                        // Use the jquery.waypoints.js library to trigger when the 
                        // carddeck scrolls into view.
                        var waypoints = $('.fw-carddeck-{$shortcode_id}').waypoint({
                            handler: function(direction) {                             
                                $('.fw-carddeck-{$shortcode_id}').jqAnimateCardDeck({
                                    {$js_parameters}
                                });
                            }, 
                            offset: 'bottom-in-view'
                        });
                    });
                })( jQuery );  
            </script>
END;

        // Increment our unique carddeck identifier for the next call to our shortcode.
        $shortcode_id++;

        // Return buffered output.
        $html = ob_get_contents();
        ob_end_clean();

        return $html;

    }

}
