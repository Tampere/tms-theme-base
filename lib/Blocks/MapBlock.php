<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Blocks;

use Geniem\ACF\Block;
use TMS\Theme\Base\ACF\Fields\ImageFields;
use TMS\Theme\Base\ACF\Fields\MapFields;

/**
 * Class MapBlock
 *
 * @package TMS\Theme\Base\Blocks
 */
class MapBlock extends BaseBlock {

    /**
     * The block name (slug, not shown in admin).
     *
     * @var string
     */
    const NAME = 'map';

    /**
     * The block acf-key.
     *
     * @var string
     */
    const KEY = 'map';

    /**
     * The block icon
     *
     * @var string
     */
    protected $icon = 'location-alt';

    /**
     * Create the block and register it.
     */
    public function __construct() {
        $this->title = 'Karttaupote';

        parent::__construct();
    }

    /**
     * Create block fields.
     *
     * @return array
     */
    protected function fields() : array {
        $group = new MapFields( $this->title, self::NAME );

        $group->remove_field( 'title' );
        $group->remove_field( 'description' );
        $group->get_field( 'embed' )->set_wrapper_width( 100 );

        return apply_filters(
            'tms/block/' . self::KEY . '/fields',
            $group->get_fields(),
            self::KEY
        );
    }

    /**
     * This filters the block ACF data.
     *
     * @param array  $data       Block's ACF data.
     * @param Block  $instance   The block instance.
     * @param array  $block      The original ACF block array.
     * @param string $content    The HTML content.
     * @param bool   $is_preview A flag that shows if we're in preview.
     * @param int    $post_id    The parent post's ID.
     *
     * @return array The block data.
     */
    public function filter_data( $data, $instance, $block, $content, $is_preview, $post_id ) : array {
        $data = self::add_filter_attributes( $data, $instance, $block, $content, $is_preview, $post_id );

        return apply_filters( 'tms/acf/block/' . self::KEY . '/data', $data );
    }
}
