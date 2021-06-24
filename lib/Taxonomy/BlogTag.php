<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Taxonomy;

use \TMS\Theme\Base\Interfaces\Taxonomy;
use TMS\Theme\Base\PostType\BlogArticle;
use TMS\Theme\Base\Traits\Categories;

/**
 * This class defines the taxonomy.
 *
 * @package TMS\Theme\Base\Taxonomy
 */
class BlogTag implements Taxonomy {

    use Categories;

    /**
     * This defines the slug of this taxonomy.
     */
    const SLUG = 'blog-tag';

    /**
     * Add hooks and filters from this controller
     *
     * @return void
     */
    public function hooks() : void {
        add_action( 'init', \Closure::fromCallable( [ $this, 'register' ] ), 15 );
    }

    /**
     * This registers the post type.
     *
     * @return void
     */
    private function register() {
        $labels = [
            'name'                       => 'Avainsanat',
            'singular_name'              => 'Avainsana',
            'menu_name'                  => 'Avainsanat',
            'all_items'                  => 'Kaikki avainsanat',
            'new_item_name'              => 'Lisää uusi avainsana',
            'add_new_item'               => 'Lisää uusi avainsana',
            'edit_item'                  => 'Muokkaa avainsanaa',
            'update_item'                => 'Päivitä avainsana',
            'view_item'                  => 'Näytä avainsana',
            'separate_items_with_commas' => \__( 'Separate departments with commas', 'tms-theme-base' ),
            'add_or_remove_items'        => \__( 'Add or remove departments', 'tms-theme-base' ),
            'choose_from_most_used'      => \__( 'Choose from most used departments', 'tms-theme-base' ),
            'popular_items'              => \__( 'Popular departments', 'tms-theme-base' ),
            'search_items'               => 'Etsi avainsana',
            'not_found'                  => 'Ei tuloksia',
            'no_terms'                   => 'Ei tuloksia',
            'items_list'                 => 'Avainsanat',
            'items_list_navigation'      => 'Avainsanat',
        ];

        $args = [
            'labels'            => $labels,
            'hierarchical'      => false,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => false,
            'show_tagcloud'     => false,
            'show_in_rest'      => true,
        ];

        register_taxonomy( self::SLUG, [ BlogArticle::SLUG ], $args );
    }
}
