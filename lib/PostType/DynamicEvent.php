<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\PostType;

use \TMS\Theme\Base\Interfaces\PostType;

/**
 * Settings
 *
 * The settings post type is used to create translatable site settings.
 *
 * There's only one post created in this post type and it can be removed only
 * by super admin. Others can modify, so the settings are updated.
 *
 * @package TMS\Theme\Base\PostType
 */
class DynamicEvent implements PostType {

    /**
     * This defines the slug of this post type.
     */
    public const SLUG = 'dynamic-event-cpt';

    /**
     * Cache key
     */
    const LINK_LIST_CACHE_KEY = 'dynamic-event-link-list';

    /**
     * This defines what is shown in the url. This can
     * be different than the slug which is used to register the post type.
     *
     * @var string
     */
    private $url_slug = '';

    /**
     * Define the CPT description
     *
     * @var string
     */
    private $description = '';

    /**
     * This is used to position the post type menu in admin.
     *
     * @var int
     */
    private $menu_order = 40;

    /**
     * This defines the CPT icon.
     *
     * @var string
     */
    private $icon = 'dashicons-heart';

    /**
     * Constructor
     */
    public function __construct() {
        $this->url_slug    = _x( 'dynamic-event', 'theme CPT slugs', 'tms-theme-base' );
        $this->description = _x( 'dynamic-event', 'theme CPT', 'tms-theme-base' );
    }

    /**
     * Add hooks and filters from this controller
     *
     * @return void
     */
    public function hooks() : void {
        add_action( 'init', \Closure::fromCallable( [ $this, 'register' ] ), 15 );
        add_action(
            'save_post_' . static::get_post_type(),
            \Closure::fromCallable( [ $this, 'clear_cache' ] )
        );
        add_action(
            'trash_post',
            \Closure::fromCallable( [ $this, 'trash_post_callback' ] )
        );
    }

    /**
     * Get post type slug
     *
     * @return string
     */
    public function get_post_type() : string {
        return static::SLUG;
    }

    /**
     * This registers the post type.
     *
     * @return void
     */
    private function register() {
        $labels = [
            'name'                  => 'Dynaamiset tapahtumat',
            'singular_name'         => 'Dynaaminen tapahtuma',
            'menu_name'             => 'Dynaamiset tapahtumat',
            'name_admin_bar'        => 'Dynaamiset tapahtumat',
            'archives'              => 'Arkistot',
            'attributes'            => 'Ominaisuudet',
            'parent_item_colon'     => 'Vanhempi:',
            'all_items'             => 'Kaikki',
            'add_new_item'          => 'Lisää uusi',
            'add_new'               => 'Lisää uusi',
            'new_item'              => 'Uusi',
            'edit_item'             => 'Muokkaa',
            'update_item'           => 'Päivitä',
            'view_item'             => 'Näytä',
            'view_items'            => 'Näytä kaikki',
            'search_items'          => 'Etsi',
            'not_found'             => 'Ei löytynyt',
            'not_found_in_trash'    => 'Ei löytynyt roskakorista',
            'featured_image'        => 'Kuva',
            'set_featured_image'    => 'Aseta kuva',
            'remove_featured_image' => 'Poista kuva',
            'use_featured_image'    => 'Käytä kuvana',
            'insert_into_item'      => 'Aseta julkaisuun',
            'uploaded_to_this_item' => 'Lisätty tähän julkaisuun',
            'items_list'            => 'Listaus',
            'items_list_navigation' => 'Listauksen navigaatio',
            'filter_items_list'     => 'Suodata listaa',
        ];

        $rewrite = [
            'slug'       => static::SLUG,
            'with_front' => true,
            'pages'      => true,
            'feeds'      => true,
        ];

        $args = [
            'label'               => $labels['name'],
            'description'         => '',
            'labels'              => $labels,
            'supports'            => [ 'title', 'editor', 'thumbnail', 'revisions' ],
            'hierarchical'        => true,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => $this->menu_order,
            'menu_icon'           => $this->icon,
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'rewrite'             => $rewrite,
            'capability_type'     => 'dynamic_event',
            'map_meta_cap'        => true,
            'show_in_rest'        => true,
        ];

        register_post_type( static::SLUG, $args );
    }

    /**
     * Called when post is trashed.
     *
     * @param int $post_id WP_Post ID.
     */
    protected function trash_post_callback( $post_id ) : void {
        if ( $this->get_post_type() === get_post_type( $post_id ) ) {
            $this->clear_cache();
        }
    }

    /**
     * Clear cache
     */
    protected function clear_cache() : void {
        wp_cache_delete( self::LINK_LIST_CACHE_KEY );
    }

    /**
     * Get link list
     *
     * @return array
     */
    public static function get_link_list() : array {
        $cache_key      = self::LINK_LIST_CACHE_KEY;
        $dynamic_events = wp_cache_get( $cache_key );

        if ( ! empty( $dynamic_events ) ) {
            return $dynamic_events;
        }

        $the_query = new \WP_Query( [
            'post_type'      => DynamicEvent::SLUG,
            'posts_per_page' => - 1,
            'fields'         => 'ids',
            'no_found_rows'  => true,
            'post_status'    => 'publish',
        ] );

        $dynamic_events = [];

        if ( ! $the_query->have_posts() ) {
            return $dynamic_events;
        }

        foreach ( $the_query->posts as $dynamic_event_id ) {
            $api_id = get_field( 'event', $dynamic_event_id );

            if ( $api_id ) {
                $dynamic_events[ $api_id ] = get_permalink( $dynamic_event_id );
            }
        }

        wp_cache_set( $cache_key, $dynamic_events, '', HOUR_IN_SECONDS * 2 );

        return $dynamic_events;
    }
}
