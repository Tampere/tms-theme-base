<?php
/**
 * Define the single post class.
 */

use TMS\Theme\Base\PostType\BlogArticle;
use TMS\Theme\Base\Settings;

/**
 * The Single class.
 */
class SingleBlogArticle extends Single {

    /**
     * Get the blog title.
     *
     * @return string|null
     */
    public function blog_title() : ?string {
        return Settings::get_setting( 'blog_name' );
    }

    /**
     * Get the blog subtitle.
     *
     * @return string|null
     */
    public function blog_subtitle() : ?string {
        return Settings::get_setting( 'blog_subtitle' );
    }

    /**
     * Get the blog subtitle.
     *
     * @return string|null
     */
    public function blog_description() : ?string {
        return Settings::get_setting( 'blog_description' );
    }

    /**
     * Get the blog logo.
     *
     * @return string|null
     */
    public function blog_logo() : ?string {
        return Settings::get_setting( 'blog_logo' );
    }

    /**
     * Get the blog archive link.
     *
     * @return string
     */
    public function archive_link() : string {
        return get_post_type_archive_link( BlogArticle::SLUG );
    }

    /**
     * Get the blog authors.
     *
     * @return array
     */
    public function blog_authors() : ?array {
        $authors = get_field( 'authors' );

        if ( empty( $authors ) ) {
            return [];
        }

        return array_map( function ( $author ) {
            $author->featured_image = has_post_thumbnail( $author->ID )
                ? get_post_thumbnail_id( $author->ID )
                : null;

            return $author;
        }, $authors );
    }

    /**
     * Get comments markup.
     *
     * @return false|string
     */
    public function comments() {
        if ( ! comments_open( get_the_ID() ) ) {
            return false;
        }

        ob_start();
        comments_template();

        return ob_get_clean();
    }
}