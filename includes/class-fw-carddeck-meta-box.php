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
class FW_Carddeck_Meta_Box {
    
    function __construct()  {
        
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_meta_box' ), 10, 2 );

    }
    
    /**
     * Load meta box.
     *
     * @since  0.9.1
     */
    public function add_meta_box() {

        add_meta_box(
            FW_CARDDECK_POST_TYPE_META_BOX_ID,
            'Card Details',
            array( $this, 'render_meta_box' ),
            FW_CARDDECK_POST_TYPE_ID,
            'normal',
            'high'
        );

    }

    /**
     * Callback from add_meta_box() to render our meta box.
     *
     * @since  0.9.1
     */
    public function render_meta_box() {

        global $post;

        $this->meta_box_detail_fields( $post->ID );

    }

    /**
     * Display our meta box fields.
     *
     * @since  0.9.1
     *
     * @param  int  $post_id   Post id.
     */
    private function meta_box_detail_fields( $post_id ) {

        $card_number = get_post_meta( $post_id, FW_CARDDECK_POST_TYPE_META_FIELD_CARD_NUMBER_ID, true );

        ?>
        <?php wp_nonce_field( 'fw_card_save', 'fw_card_meta_box_nonce' ); ?>

        <table class="form-table">
            <tr>
                <th><label>Card Number</label></th>
                <td>
                    <input type="number" id="fw_carddeck_card_number" name="fw_carddeck_card_number" 
                           min="<?php echo FW_CARDDECK_POST_TYPE_META_FIELD_CARD_NUMBER_MIN; ?>"
                           max="<?php echo FW_CARDDECK_POST_TYPE_META_FIELD_CARD_NUMBER_MAX; ?>"
                           value="<?php echo esc_attr($card_number); ?>" />
                    <p class="description">
                        Each card within a given deck must have a unique number assigned
                        to it. The number represents the placement of the card within
                        the deck. See the card assignments in the map layout below.
                    </p>
                    <p class="description">
                        There are four sizes of cards:<br />
                        <ol>
                        <?php echo '<li><strong>Large</strong>: ' . FW_CARDDECK_IMAGE_SIZE_LARGE_X . 'px x ' . 
                              FW_CARDDECK_IMAGE_SIZE_LARGE_Y . 'px (Cards 7,17,18)</li>'; ?>
                        <?php echo '<li><strong>Medium</strong>: ' . FW_CARDDECK_IMAGE_SIZE_MEDIUM_X . 'px x ' . 
                              FW_CARDDECK_IMAGE_SIZE_MEDIUM_Y . 'px (Cards 4,10,11,21)</li>'; ?>
                        <?php echo '<li><strong>Small</strong>: ' . FW_CARDDECK_IMAGE_SIZE_SMALL_X . 'px x ' . 
                              FW_CARDDECK_IMAGE_SIZE_SMALL_Y . 'px (Cards 3,6,8,14,19,20)</li>'; ?>
                        <?php echo '<li><strong>XSmall</strong>: ' . FW_CARDDECK_IMAGE_SIZE_XSMALL_X . 'px x ' . 
                              FW_CARDDECK_IMAGE_SIZE_XSMALL_Y . 'px (Cards 1,2,5,9,12,13,15,16)</li>'; ?>
                        </ol>
                        Although you may use an image of any size, the aspect ratio of 0.65
                        should be preserved.
                    </p>
                    <img class="fw-carddeck-placement-map"
                        src="<?php echo FW_CARDDECK_ADMIN_IMAGES_URL . '/carddeck-placement-map-desktop-min.png'; ?>"
                        alt="Where would you like to place your image?" />
                </td>
            </tr> 
        </table>
        <?php
    }

    /**
     * Save our meta box fields.
     *
     * @since  0.9.1
     *
     * @param  int       $post_id   Post id.
     * @param  WP_Post   $post      Post object (https://developer.wordpress.org/reference/classes/wp_post/)
     */
    public function save_meta_box( $post_id, $post ) {
        
        if ( ! isset( $_POST['fw_card_meta_box_nonce'] ) ||
             ! wp_verify_nonce( $_POST['fw_card_meta_box_nonce'], 'fw_card_save' ) ) {
            return;
        }

        if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ||
             ( defined( 'DOING_AJAX') && DOING_AJAX ) ||
               isset( $_REQUEST['bulk_edit'] ) ) {
            return;
        }

        if ( isset( $post->post_type ) && 'revision' == $post->post_type ) {
            return;
        }

        if ( ! current_user_can( 'edit_posts', $post_id ) ) {
            return;
        }

        // Save meta data
        if ( isset( $_POST['fw_carddeck_card_number'] ) ) {

            $value = sanitize_text_field( trim( $_POST['fw_carddeck_card_number'] ) );

            update_post_meta( $post_id, FW_CARDDECK_POST_TYPE_META_FIELD_CARD_NUMBER_ID, $value );

        }

    }

}