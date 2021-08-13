<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF;

use \Geniem\ACF\Exception;
use \Geniem\ACF\Field;
use \Geniem\ACF\Group;
use \Geniem\ACF\RuleGroup;
use TMS\Theme\Base\ACF\Fields\BlogArticleSettingsTab;
use TMS\Theme\Base\ACF\Fields\ContactsSettingsTab;
use TMS\Theme\Base\ACF\Fields\FooterSettingsTab;
use TMS\Theme\Base\ACF\Fields\HeaderSettingsTab;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\PostType;

/**
 * Class SettingsGroup
 *
 * @package TMS\Theme\Base\ACF
 */
class SettingsGroup {

    /**
     * SettingsGroup constructor.
     */
    public function __construct() {
        add_action(
            'init',
            \Closure::fromCallable( [ $this, 'register_fields' ] )
        );
    }

    /**
     * Register fields
     */
    protected function register_fields() : void {
        try {
            $group_title = _x( 'Site settings', 'theme ACF', 'tms-theme-base' );

            $field_group = ( new Group( $group_title ) )
                ->set_key( 'fg_site_settings' );

            $rule_group = ( new RuleGroup() )
                ->add_rule( 'post_type', '==', PostType\Settings::SLUG );

            $field_group
                ->add_rule_group( $rule_group )
                ->set_position( 'normal' )
                ->set_hidden_elements(
                    [
                        'discussion',
                        'comments',
                        'format',
                        'send-trackbacks',
                    ]
                );

            $field_group->add_fields(
                apply_filters(
                    'tms/acf/group/' . $field_group->get_key() . '/fields',
                    [
                        new HeaderSettingsTab( '', $field_group->get_key() ),
                        new FooterSettingsTab( '', $field_group->get_key() ),
                        ( new Fields\ThemeColorTab( '', $field_group->get_key() ) ),
                        $this->get_map_fields( $field_group->get_key() ),
                        $this->get_social_media_sharing_fields( $field_group->get_key() ),
                        $this->get_404_fields( $field_group->get_key() ),
                        $this->get_archive_fields( $field_group->get_key() ),
                        $this->get_events_fields( $field_group->get_key() ),
                        $this->get_page_fields( $field_group->get_key() ),
                        $this->get_exception_notice_fields( $field_group->get_key() ),
                        new BlogArticleSettingsTab( '', $field_group->get_key() ),
                        new ContactsSettingsTab( '', $field_group->get_key() ),
                    ]
                )
            );

            $field_group = apply_filters(
                'tms/acf/group/' . $field_group->get_key(),
                $field_group
            );

            $field_group->register();
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTraceAsString() );
        }
    }

    /**
     * Get map fields
     *
     * @param string $key Field group key.
     *
     * @return Field\Tab
     * @throws Exception In case of invalid option.
     */
    protected function get_map_fields( string $key ) : Field\Tab {
        $strings = [
            'tab'             => 'Kartat',
            'map_placeholder' => [
                'title'        => 'Kartan placeholder-kuva',
                'instructions' => '',
            ],
            'map_button_text' => [
                'title'        => 'Kartan näyttämisen toimintakehoite',
                'instructions' => '',
            ],
        ];

        $tab = ( new Field\Tab( $strings['tab'] ) )
            ->set_placement( 'left' );

        $map_placeholder_field = ( new Field\Image( $strings['map_placeholder']['title'] ) )
            ->set_key( "${key}_map_placeholder" )
            ->set_name( 'map_placeholder' )
            ->set_return_format( 'id' )
            ->set_instructions( $strings['map_placeholder']['instructions'] );

        $map_button_text_field = ( new Field\Text( $strings['map_button_text']['title'] ) )
            ->set_key( "${key}_map_button_text" )
            ->set_name( 'map_button_text' )
            ->set_default_value( __( 'Open map', 'tms-theme-base' ) )
            ->set_instructions( $strings['map_button_text']['instructions'] );

        $tab->add_fields( [
            $map_placeholder_field,
            $map_button_text_field,
        ] );

        return $tab;
    }

    /**
     * Get social media sharing fields
     *
     * @param string $key Field group key.
     *
     * @return Field\Tab
     * @throws Exception In case of invalid option.
     */
    protected function get_social_media_sharing_fields( string $key ) : Field\Tab {
        $strings = [
            'tab'           => 'Sosiaalinen media',
            'some_channels' => [
                'title'        => 'Kanavat',
                'instructions' => 'Valitse käytössä olevat kanavat',
            ],
        ];

        $tab = ( new Field\Tab( $strings['tab'] ) )
            ->set_placement( 'left' );

        $some_channels_field = ( new Field\Checkbox( $strings['some_channels']['title'] ) )
            ->set_key( "${key}_some_channels" )
            ->set_name( 'some_channels' )
            ->set_choices( [
                'facebook'  => 'Facebook',
                'email'     => 'Sähköposti',
                'whatsapp'  => 'WhatsApp',
                'twitter'   => 'Twitter',
                'linkedin'  => 'LinkedIn',
                'vkontakte' => 'VKontakte',
                'line'      => 'LINE',
                'link'      => 'Linkki',
            ] )
            ->set_instructions( $strings['some_channels']['instructions'] );

        $tab->add_fields( [
            $some_channels_field,
        ] );

        return $tab;
    }

    /**
     * Get 404 page fields
     *
     * @param string $key Field group key.
     *
     * @return Field\Tab
     * @throws Exception In case of invalid option.
     */
    protected function get_404_fields( string $key ) : Field\Tab {
        $strings = [
            'tab'             => '404-sivu',
            '404_title'       => [
                'title'        => 'Otsikko',
                'instructions' => '',
            ],
            '404_description' => [
                'title'        => 'Kuvaus',
                'instructions' => '',
            ],
            '404_image'       => [
                'title'        => 'Kuva',
                'instructions' => '',
            ],
        ];

        $tab = ( new Field\Tab( $strings['tab'] ) )
            ->set_placement( 'left' );

        $title_field = ( new Field\Text( $strings['404_title']['title'] ) )
            ->set_key( "${key}_404_title" )
            ->set_name( '404_title' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['404_title']['instructions'] );

        $description_field = ( new Field\ExtendedWysiwyg( $strings['404_description']['title'] ) )
            ->set_key( "${key}_404_description" )
            ->set_name( '404_description' )
            ->set_tabs( 'visual' )
            ->set_toolbar(
                [
                    'bold',
                    'italic',
                    'link',
                    'pastetext',
                    'removeformat',
                ]
            )
            ->disable_media_upload()
            ->set_height( 200 )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['404_description']['instructions'] );

        $image_field = ( new Field\Image( $strings['404_image']['title'] ) )
            ->set_key( "${key}_404_image" )
            ->set_name( '404_image' )
            ->set_instructions( $strings['404_image']['instructions'] );

        $tab->add_fields( [
            $title_field,
            $description_field,
            $image_field,
        ] );

        return $tab;
    }

    /**
     * Get archive fields
     *
     * @param string $key Field group key.
     *
     * @return Field\Tab
     * @throws Exception In case of invalid option.
     */
    protected function get_archive_fields( string $key ) : Field\Tab {
        $strings = [
            'tab'                => 'Arkistot',
            'archive_use_images' => [
                'title'        => 'Kuvat käytössä',
                'instructions' => '',
            ],
            'archive_view_type'  => [
                'title'        => 'Listaustyyli',
                'instructions' => '',
            ],
        ];

        $tab = ( new Field\Tab( $strings['tab'] ) )
            ->set_placement( 'left' );

        $use_images_field = ( new Field\TrueFalse( $strings['archive_use_images']['title'] ) )
            ->set_key( "${key}_archive_use_images" )
            ->set_name( 'archive_use_images' )
            ->set_default_value( true )
            ->use_ui()
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['archive_use_images']['instructions'] );

        $view_type_field = ( new Field\Radio( $strings['archive_view_type']['title'] ) )
            ->set_key( "${key}_archive_view_type" )
            ->set_name( 'archive_view_type' )
            ->set_choices( [
                'grid' => 'Ruudukko',
                'list' => 'Lista',
            ] )
            ->set_default_value( 'grid' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['archive_view_type']['instructions'] );

        $tab->add_fields( [
            $use_images_field,
            $view_type_field,
        ] );

        return $tab;
    }

    /**
     * Get events fields
     * Get page fields
     *
     * @param string $key Field group key.
     *
     * @return Field\Tab
     * @throws Exception In case of invalid option.
     */
    protected function get_events_fields( string $key ) : Field\Tab {
        $strings = [
            'tab'                           => 'Tapahtumat',
            'events_default_image'          => [
                'title'        => 'Oletuskuva',
                'instructions' => '',
            ],
            'events_page'                   => [
                'title'        => 'Tapahtuma-sivu',
                'instructions' => 'Sivu, jolle on valittu Tapahtuma-sivupohja',
            ],
            'show_related_events_calendars' => [
                'title'        => 'Näytä muut sivuston tapahtumakalenterit',
                'instructions' => 'Tapahtumakalenterin yläosassa näytetään automaattisesti
                linkit muille saman sivuston sivuille, joilla on käytössä tapahtumakalenteri-sivupohja',
            ],
        ];

        $tab = ( new Field\Tab( $strings['tab'] ) )
            ->set_placement( 'left' );

        $image_field = ( new Field\Image( $strings['events_default_image']['title'] ) )
            ->set_key( "${key}_events_default_image" )
            ->set_name( 'events_default_image' )
            ->set_return_format( 'id' )
            ->set_instructions( $strings['events_default_image']['instructions'] );

        $events_page_field = ( new Field\PostObject( $strings['events_page']['title'] ) )
            ->set_key( "${key}_events_page" )
            ->set_name( 'events_page' )
            ->set_post_types( [ PostType\Page::SLUG ] )
            ->set_return_format( 'id' )
            ->set_instructions( $strings['events_page']['instructions'] );

        $show_event_calendars_field = ( new Field\TrueFalse( $strings['show_related_events_calendars']['title'] ) )
            ->set_key( "${key}_show_related_events_calendars" )
            ->set_name( 'show_related_events_calendars' )
            ->use_ui()
            ->set_default_value( false )
            ->set_instructions( $strings['show_related_events_calendars']['instructions'] );

        $tab->add_fields( [
            $image_field,
            $events_page_field,
            $show_event_calendars_field,
        ] );

        return $tab;
    }

    /**
     * Get page fields
     *
     * @param string $key Field group key.
     *
     * @return Field\Tab
     * @throws Exception In case of invalid option.
     */
    protected function get_page_fields( string $key ) : Field\Tab {
        $strings = [
            'tab'                       => 'Sisältösivut',
            'enable_sibling_navigation' => [
                'title'        => 'Rinnakkaissivujen navigointi',
                'instructions' => 'Esitetään sivujen alasivuilla ennen alatunnistetta.',
            ],
        ];

        $tab = ( new Field\Tab( $strings['tab'] ) )
            ->set_placement( 'left' );

        $display_siblings = ( new Field\TrueFalse( $strings['enable_sibling_navigation']['title'] ) )
            ->set_key( "${key}_enable_sibling_navigation" )
            ->set_name( 'enable_sibling_navigation' )
            ->set_default_value( false )
            ->use_ui()
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['enable_sibling_navigation']['instructions'] );

        $tab->add_fields( [
            $display_siblings,
        ] );

        return $tab;
    }

    /**
     * Get exception notice fields
     *
     * @param string $key Field group key.
     *
     * @return Field\Tab
     * @throws Exception In case of invalid option.
     */
    protected function get_exception_notice_fields( string $key ) : Field\Tab {
        $strings = [
            'tab'  => 'Poikkeusilmotus',
            'text' => [
                'title'        => 'Teksti',
                'instructions' => '',
            ],
        ];

        $tab = ( new Field\Tab( $strings['tab'] ) )
            ->set_placement( 'left' );

        $exception_text_field = ( new Field\Textarea( $strings['text']['title'] ) )
            ->set_key( "${key}_exception_text" )
            ->set_name( 'exception_text' )
            ->set_rows( 2 )
            ->set_wrapper_width( 50 )
            ->set_maxlength( 200 )
            ->set_instructions( $strings['text']['instructions'] );

        $tab->add_fields( [
            $exception_text_field,
        ] );

        return $tab;
    }
}

( new SettingsGroup() );
