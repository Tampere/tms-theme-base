<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

use TMS\Theme\Base\Interfaces\Controller;

/**
 * Class Roles
 *
 * @package TMS\Theme\Base
 */
class Roles implements Controller {

    /**
     * Remove these capabilities from all Roles.
     *
     * @var array
     */
    private $remove_from_all = [
        'edit_themes',
        'edit_files',
        'update_plugins',
        'install_plugins',
        'update_themes',
        'install_themes',
        'delete_themes',
        'update_core',
    ];

    /**
     * Pages / page (default page type)
     *
     * @var string[]
     */
    private $pages_all_capabilities = [
        'edit_page',
        'read_page',
        'delete_page',
        'edit_pages',
        'edit_others_pages',
        'delete_pages',
        'publish_pages',
        'read_private_pages',
        'read',
        'delete_private_pages',
        'delete_published_pages',
        'delete_others_pages',
        'edit_private_pages',
        'edit_published_pages',
        'edit_pages',
    ];

    /**
     * Materials plugin / material-cpt.
     *
     * @var string[]
     */
    private $materials_all_capabilities = [
        'delete_material',
        'delete_materials',
        'delete_others_materials',
        'delete_private_materials',
        'delete_published_materials',
        'edit_material',
        'edit_materials',
        'edit_others_materials',
        'edit_private_materials',
        'edit_published_materials',
        'publish_materials',
        'read',
        'read_material',
        'read_private_materials',
    ];

    /**
     * Site settings / settings-cpt.
     *
     * @var array
     */
    private $site_settings_all_capabilities = [
        'edit_site_setting',
        'read_site_setting',
        'delete_site_setting',
        'edit_others_site_settings',
        'delete_site_settings',
        'publish_site_settings',
        'read_private_site_settings',
        'delete_private_site_settings',
        'delete_published_site_settings',
        'delete_others_site_settings',
        'edit_private_site_settings',
        'edit_published_site_settings',
        'edit_site_settings',
    ];

    /**
     * Hooks
     */
    public function hooks() : void {
        $this->modify_administrator_caps();

        add_filter(
            'map_meta_cap',
            \Closure::fromCallable( [ $this, 'add_unfiltered_html_capability' ] ),
            1,
            3
        );

        // If wp-geniem-roles is active.
        if ( class_exists( '\Geniem\Roles' ) ) {
            // Run Geniem roles functions here.
            $this->modify_admin_caps(); // These modifications are automatically added to superadmin.
            $this->modify_superadmin_caps();
            $this->modify_editor_caps();
            $this->modify_author_caps();
            $this->modify_contributor_caps();
            $this->modify_subscriber_caps();
        }
    }

    /**
     * Modify 'administrator' capabilities
     */
    public function modify_administrator_caps() : void {
        $admin_rights = [
            // Site settings
            'edit_site_setting'              => true,
            'read_site_setting'              => true,
            'delete_site_setting'            => true,
            'edit_others_site_settings'      => true,
            'delete_site_settings'           => true,
            'publish_site_settings'          => true,
            'read_private_site_settings'     => true,
            'delete_private_site_settings'   => true,
            'delete_published_site_settings' => true,
            'delete_others_site_settings'    => true,
            'edit_private_site_settings'     => true,
            'edit_published_site_settings'   => true,
            'edit_site_settings'             => true,

            // Materials
            'edit_material'                  => true,
            'read_material'                  => true,
            'delete_material'                => true,
            'edit_others_materials'          => true,
            'delete_materials'               => true,
            'publish_materials'              => true,
            'read_private_materials'         => true,
            'delete_private_materials'       => true,
            'delete_published_materials'     => true,
            'delete_others_materials'        => true,
            'edit_private_materials'         => true,
            'edit_published_materials'       => true,
            'edit_materials'                 => true,

            // Common
            'unfiltered_html'                => true,
        ];

        $admin = get_role( 'administrator' );

        if ( empty( $admin ) ) {
            return;
        }

        foreach ( $admin_rights as $cap => $grant ) {
            $admin->add_cap( $cap, $grant );
        }

        unset( $admin );
    }

    /**
     * Create and Modify Super Admin Name and Capabilities
     * Also known as: Verkon Pääkäyttäjä.
     */
    private function modify_superadmin_caps() : void {
        $role = \Geniem\Roles::get( 'super_administrator' );

        if ( ! ( $role instanceof \Geniem\Role ) ) {
            /**
             * Administrator role.
             *
             * @var \Geniem\Role|null $admin
             */
            $admin = \Geniem\Roles::get( 'administrator' );

            if ( ! ( $admin instanceof \Geniem\Role ) ) {
                return;
            }

            $role = \Geniem\Roles::create(
                'super_administrator',
                _x( 'Super Administrator', 'wp-geniem-roles', 'tms-theme-base' ),
                $admin->capabilities ?? []
            );
        }

        $role->add_caps( [
            'add_users',
            'create_sites',
            'create_users',
            'delete_sites',
            'manage_network',
            'manage_network_options',
            'manage_network_plugins',
            'manage_network_themes',
            'manage_network_users',
            'manage_sites',
        ] );

        $role->add_caps( $this->pages_all_capabilities );
        $role->add_caps( $this->site_settings_all_capabilities );
        $role->add_caps( $this->materials_all_capabilities );

        $role->remove_caps( $this->remove_from_all );

        apply_filters( 'tms/roles/super_administrator', $role );
    }

    /**
     * Modify Admin Name and Capabilities
     * Also known as: Pääkäyttäjä.
     */
    private function modify_admin_caps() : void {
        /**
         * Administrator role.
         *
         * @var \Geniem\Role|null $role
         */
        $role = \Geniem\Roles::get( 'administrator' );

        if ( ! ( $role instanceof \Geniem\Role ) ) {
            return;
        }

        $role->add_caps( $this->pages_all_capabilities );
        $role->add_caps( $this->site_settings_all_capabilities );

        $role->remove_caps( $this->remove_from_all );

        $role = apply_filters( 'tms/roles/admin', $role );
        $role->rename( _x( 'Administrator', 'wp-geniem-roles', 'tms-theme-base' ) );
    }

    /**
     * Modify Editor Name and Capabilities.
     * Also known as: Site Manager / Sivustovastaava.
     */
    private function modify_editor_caps() : void {
        /**
         * Editor role.
         *
         * @var \Geniem\Role|null $role
         */
        $role = \Geniem\Roles::get( 'editor' );

        if ( ! ( $role instanceof \Geniem\Role ) ) {
            return;
        }

        $role->add_caps( $this->pages_all_capabilities );
        $role->add_caps( $this->materials_all_capabilities );
        $role->add_caps( $this->site_settings_all_capabilities );

        $role->remove_caps( $this->remove_from_all );

        $role = apply_filters( 'tms/roles/editor', $role );
        $role->rename( _x( 'Site Manager', 'wp-geniem-roles', 'tms-theme-base' ) );
    }

    /**
     * Modify Author Name and Capabilities.
     * Also known as: Toimittaja.
     */
    private function modify_author_caps() : void {
        /**
         * Author role.
         *
         * @var \Geniem\Role|null $role
         */
        $role = \Geniem\Roles::get( 'author' );

        if ( ! ( $role instanceof \Geniem\Role ) ) {
            return;
        }

        $role->add_caps( [ 'edit_others_posts', 'publish_posts', 'read_private_posts' ] );
        $role->add_caps( $this->pages_all_capabilities );
        $role->add_caps( $this->materials_all_capabilities );

        $role->remove_caps( $this->remove_from_all );

        $role = apply_filters( 'tms/roles/author', $role );
        $role->rename( _x( 'Author', 'wp-geniem-roles', 'tms-theme-base' ) );
    }

    /**
     * Modify Contributor Name and Capabilities.
     * Also known as: Avustaja.
     */
    private function modify_contributor_caps() : void {
        /**
         * Contributor role.
         *
         * @var \Geniem\Role|null $role
         */
        $role = \Geniem\Roles::get( 'contributor' );

        if ( ! ( $role instanceof \Geniem\Role ) ) {
            return;
        }

        // Materials plugin / material-cpt
        $role->add_caps( [
            'delete_material',
            'delete_materials',
            'edit_material',
            'edit_materials',
            'read',
            'read_material',
            'read_private_materials',
        ] );

        $role->add_caps( [
            'edit_page',
            'read_page',
            'delete_page',
            'edit_pages',
            'edit_others_pages',
            'read_private_pages',
            'read',
            'delete_private_pages',
            'delete_published_pages',
            'delete_others_pages',
            'edit_private_pages',
            'edit_published_pages',
            'edit_pages',
        ] );

        $role->remove_caps( $this->remove_from_all );

        $role = apply_filters( 'tms/roles/contributor', $role );
        $role->rename( _x( 'Contributor', 'wp-geniem-roles', 'tms-theme-base' ) );
    }

    /**
     * Modify Subscriber Name and Capabilities.
     * Also known as: Tilaaja.
     */
    private function modify_subscriber_caps() : void {
        /**
         * Subscriber role.
         *
         * @var \Geniem\Role|null $role
         */
        $role = \Geniem\Roles::get( 'subscriber' );

        if ( ! ( $role instanceof \Geniem\Role ) ) {
            return;
        }

        $role = apply_filters( 'tms/roles/subscriber', $role );
        $role->rename( _x( 'Subscriber', 'wp-geniem-roles', 'tms-theme-base' ) );
    }

    /**
     * Enable unfiltered_html capability for Editors and Administrators.
     *
     * @param array  $caps    The user's capabilities.
     * @param string $cap     Capability name.
     * @param int    $user_id The user ID.
     *
     * @return array  $caps    The user's capabilities, with 'unfiltered_html' potentially added.
     */
    protected function add_unfiltered_html_capability( $caps, $cap, $user_id ) : array {
        if (
            $cap === 'unfiltered_html' &&
            ( user_can( $user_id, 'administrator' ) || user_can( $user_id, 'editor' ) )
        ) {
            $caps = [ 'unfiltered_html' ];
        }

        return $caps;
    }
}
