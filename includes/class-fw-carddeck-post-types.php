<?php
 /** 
 * This class creates the fw-carddeck custom post type and registers the associated
 * taxonomies.
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
class FW_Carddeck_Post_Types {
    
    function __construct()  {
        
        add_action( 'init', array( $this, 'register_post_types' ) );
        add_action( 'init', array( $this, 'register_taxonomies' ) );

        // Add additional columns to our table.
        add_filter(
            'manage_fw_carddeck_posts_columns',
            array( $this, 'add_carddeck_columns' )
        );

        add_action(
            'manage_fw_carddeck_posts_custom_column',
            array( $this, 'populate_carddeck_columns' ),
            10,
            2
        );

        // Make columns sortable.
        add_filter(
            'manage_edit-fw_carddeck_sortable_columns',
            array( $this, 'sort_carddeck_columns' )
        );

        // Add a select menu at the top of the CPT table so posts can be filtered by taxonomies.
        add_action( 'restrict_manage_posts', array( $this, 'add_taxonomy_filters' ) );

    }

    /**
     * Register our post type.
     *
     * @since  0.9.1
     *
     */
    public function register_post_types() {

        global $menu;

        /*
         * We would like to place our Post Type menu option as close to 'Posts' as possible since
         * we are similar as a 'custom' post type. All menu options are registered with a
         * menu_position in a pecking order where 'Posts' has a menu_position of '5' and the
         * other menu options are listed in increasing menu_position order (e.g.: 10, 15, ...)
         * down the vertical menu. The lower the menu_position number, the higher you stay in 
         * the vertical menu.
         *
         * We will attempt to position ourselves as close to the 'Posts' menu option ('5')
         * as possible without choosing a number that is already taken by another menu option.
         * If, for some reason, our Post Type menu option fails to display, it may be the rare
         * case that another plugin is conflicting their menu_position with ours. WordPress
         * will only choose one plugin to occupy that spot, so if we lose out, look for this
         * to be the problem.
         *
         * See https://codex.wordpress.org/Function_Reference/register_post_type#menu_position
         */
        $menu_position = 6; // Start under the 'Posts' menu option of '5'.
        while ( isset( $menu[$menu_position] ) ) {
            $menu_position++;
        }

        $labels =  array(
            'name'               => 'Cards',
            'singular_name'      => 'Card',
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New Card',
            'edit_item'          => 'Edit Card',
            'new_item'           => 'New Card',
            'all_items'          => 'All Cards',
            'view_item'          => 'View Card',
            'search_items'       => 'Search Cards',
            'not_found'          => 'No Card Found',
            'not_found_in_trash' => 'No Card Found In Trash',
            'menu_name'          => 'Cards'
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'menu_position'      => $menu_position,
            'menu_icon'          => 'dashicons-format-gallery',
            'rewrite'            => array(
                'slug' => 'card', // Customize the permastruct slug.
                'feeds' => false  // Whether the feed permastruct should be built for this post type. 
            ),
            'has_archive'        => 'true',
            'hierarchical'       => true,
            'supports'           => array( 'title', 'thumbnail', 'editor', 'excerpt', 'revisions', 'author' )
        );

        register_post_type( FW_CARDDECK_POST_TYPE_ID, $args );
        
    }

    /**
     * Register taxonomies
     *
     * @since  0.9.1
     */
    public function register_taxonomies() {

        /** Series */
        $labels = array(
            'name'          => 'Decks',
            'singular_name' => 'Deck',
            'search_items'  => 'Search Decks',
            'all_items'     => 'All Decks',
            'parent_item'   => 'Parent Deck',
            'edit_item'     => 'Edit Deck',
            'update_item'   => 'Update Deck',
            'add_new_item'  => 'Add New Deck',
            'new_item_name' => 'New Deck',
            'menu_name'     => 'Decks',
            'not_found'     => 'No deck found.'
        );

        $args = array(
            'hierarchical' => true,
            'labels'       => $labels,
            'show_ui'      => true,
            'query_var'    => FW_CARDDECK_TAXONOMY_DECK_ID,
            'rewrite'      => array(
                'slug'         => FW_CARDDECK_TAXONOMY_DECK_ID,
                'with_front'   => false,
                'hierarchical' => true
            )
        );

        register_taxonomy( FW_CARDDECK_TAXONOMY_DECK_ID, array( FW_CARDDECK_POST_TYPE_ID ), $args );

    }

    /**
     * Configure the given list of table columns with our own.
     *
     * @since   0.9.1
     *
     * @param   array  $columns  List of column ids and labels.
     * @return  array            Same list.
     */
    public function add_carddeck_columns( $columns ) {
  
        // Remove these first, then add back in the order we want below.
        unset( $columns['author'] );
        unset( $columns['date'] );

        $columns = array_merge(
            $columns,
            array(
                FW_CARDDECK_TAXONOMY_DECK_ID => 'Deck',
                'fw_carddeck_card_number'    => 'Card Number',
                'featured_image'             => 'Image',
                'author'                     => 'Author',
                'date'                       => 'Date'
            )
        );

        return $columns;

    }

    /**
     * Make our columns sortable.
     *
     * @since  0.9.1
     *
     * @param   array  $columns  List of column ids and labels.
     * @return  array            Same list.
     */
    function sort_carddeck_columns( $columns ) {

        $columns[FW_CARDDECK_TAXONOMY_DECK_ID] = FW_CARDDECK_TAXONOMY_DECK_ID;
        return $columns;

    }

    /**
     * Switch on the given column id and display an appropriate string
     * in our CPT table.
     *
     * @since  0.9.1
     *
     * @param  string  $column    Column id for the value to fetch. See add_carddeck_columns().
     * @param  int     $post_id   Post id.
     */
    public function populate_carddeck_columns( $column, $post_id  ) {

        switch ( $column ) {

            case FW_CARDDECK_TAXONOMY_DECK_ID:
                echo $this->get_carddeck_deck( $post_id );
                break;
            
            case 'fw_carddeck_card_number':
                echo $this->get_carddeck_card_number( $post_id );
                break;

            case 'featured_image' :
                echo $this->get_thumbnail_image_html( $post_id );
                break;

            default:
                echo '';
                break;

        }
    }

    /**
     * Returns the deck name associated with the given post id. 
     *
     * @since   0.9.1
     *
     * @param   int     $post_id   Post id.
     * @return  string             deck name.
     */
    public function get_carddeck_deck( $post_id ) {

        $terms = get_the_terms( $post_id, FW_CARDDECK_TAXONOMY_DECK_ID );

        if ( !empty( $terms ) ) {
            foreach( $terms as $term ) {
                return $term->name;
            }
        } else {
            return '';
        }
        
    }

    /**
     * Returns the card number associated with the given post id. 
     *
     * @since   0.9.1
     *
     * @param   int     $post_id   Post id.
     * @return  string             Card number.
     */
    public function get_carddeck_card_number( $post_id ) {

        $card_number = get_post_meta( $post_id, FW_CARDDECK_POST_TYPE_META_FIELD_CARD_NUMBER_ID, true );
        return $card_number;

    }

    /**
     * Builds and returns an image html string with a thumbnail view of the post's
     * featured image. 
     *
     * @since   0.9.1
     *
     * @param   int      $post_id  Post id.
     * @param   string   $classes  Optional. Space separated list of classes to attach to image html.
     * @return  string             Image html associated with the given post id or empty string.
     */
    public function get_thumbnail_image_html( $post_id, $classes = "" ) {

        $image_id = get_post_thumbnail_id( $post_id );

        if ( ! empty( $image_id ) ) {
            $img_html = wp_get_attachment_image(
                $image_id, 
                'thumbnail', 
                false, 
                array( 'class' => 'fw-carddeck-featured-thumbnail ' . esc_attr( $classes ) )
            );
            return $img_html;
        }

        return '';

    }

    /**
     * Action for displaying one or more select menus on our 'All Cards' page.
     * Each menu contains the list of terms for one taxonomy. The selected term
     * will act as a filter when the [WordPress] Filter button is clicked.
     *
     * Portions of code taken from Mike Hemberger's example at:
     * http://thestizmedia.com/custom-post-type-filter-admin-custom-taxonomy/
     *
     * @since  0.9.1
     */
    public function add_taxonomy_filters() {
        global $typenow;

        if ( $typenow !== FW_CARDDECK_POST_TYPE_ID ) {
            return;
        }

        // An array of all the taxonomies you want to display. Use the taxonomy slug.
        $taxonomy_slugs = array( FW_CARDDECK_TAXONOMY_DECK_ID );

        foreach ( $taxonomy_slugs as $taxonomy_slug ) {

            $selected  = isset($_GET[$taxonomy_slug]) ? $_GET[$taxonomy_slug] : '';
            $taxonomy_obj   = get_taxonomy( $taxonomy_slug );
            $taxonomy_label = strtolower( $taxonomy_obj->label );

            wp_dropdown_categories(array(
                'show_option_all' => __("All $taxonomy_label" ),
                'taxonomy'        => $taxonomy_slug,
                'name'            => $taxonomy_slug,
                'orderby'         => 'name',
                'selected'        => $selected,
                'show_count'      => true,
                'hide_empty'      => true,
                'value_field'     => 'slug'
            ));

        }

    }

}
