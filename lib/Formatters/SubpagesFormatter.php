<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Formatters;

use TMS\Theme\Base\Interfaces\Formatter;
use TMS\Theme\Base\PostType\Page;
use WP_Query;

/**
 * Class SubpagesFormatter
 *
 * @package TMS\Theme\Base\Formatters
 */
class SubpagesFormatter implements Formatter {

    /**
     * Define formatter name
     */
    const NAME = 'Subpages';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/layout/subpages/data',
            [ $this, 'format' ]
        );

        add_filter(
            'tms/acf/block/subpages/data',
            [ $this, 'format' ]
        );
    }

    /**
     * Format layout or block data
     *
     * @param array $data ACF data.
     *
     * @return array
     */
    public function format( array $data ) : array {
        $item_classes = [
            'has-background-' . $data['background_color'],
            'has-text-' . $data['background_color'] . '-invert',
        ];

        if ( $data['display_image'] ) {
            $item_classes[] = 'has-background-image';
            $item_classes[] = 'has-background-cover';
            $item_classes[] = 'is-relative';
        }

        $data['item_classes'] = implode( ' ', $item_classes );
        $data['subpages']     = $this->get_subpages( $data['display_image'] );
        $data['icon_classes'] = $data['background_color'] === 'primary'
            ? 'is-primary-invert'
            : 'is-primary-light';

        return $data;
    }

    /**
     * Get current page subpages.
     *
     * @param bool $include_thumbnail If true, attempt to include post thumbnail.
     *
     * @return array
     */
    private function get_subpages( bool $include_thumbnail ) : array {
        $args = [
            'post_type'              => Page::SLUG,
            'posts_per_page'         => 100,
            'post_parent'            => get_the_ID(),
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
            'no_found_rows'          => true,
            'fields'                 => 'ids',
        ];

        $wp_query = new WP_Query( $args );

        if ( ! $wp_query->have_posts() ) {
            return [];
        }

        return array_map( function ( $item ) use ( $include_thumbnail ) {
            return [
                'title'    => get_the_title( $item ),
                'url'      => get_the_permalink( $item ),
                'image_id' => $include_thumbnail && has_post_thumbnail( $item )
                    ? get_post_thumbnail_id( $item )
                    : false,
            ];
        }, $wp_query->posts );
    }
}
