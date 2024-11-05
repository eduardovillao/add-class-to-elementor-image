<?php
/**
 * Plugin Name: Add class to Elementor Image
 * Plugin URI: https://eduardovillao.me/wordpress-plugins/
 * Description: Simple plugin to add custom CSS class to Elementor image.
 * Author: EduardoVillao.me
 * Author URI: https://eduardovillao.me/
 * Version: 1.3.2
 * Requires at least: 5.5
 * Requires PHP: 7.0
 * Text Domain: add-class-to-elementor-image
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

/*
Add class to Elementor Image is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Add class to Elementor Image is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Add class to Elementor Image. If not, see http://www.gnu.org/licenses/gpl-2.0.txt.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add class to Elementor image
 *
 * @since 1.0
 */
final class ACEI_Init {

	/**
	 * Instance
	 *
	 * @since 1.0
	 *
	 * @access private
	 * @static
	 *
	 * @var ACEI_Init The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 *
	 * @access public
	 * @static
	 *
	 * @return ACEI_Init An instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Disable class cloning and throw an error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object. Therefore, we don't want the object to be cloned.
	 *
	 * @access public
	 * @since 1.6
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong.', 'add-class-to-elementor-image' ), '1.3.2' );
	}

	/**
	 * Disable unserializing of the class.
	 *
	 * @access public
	 * @since 1.6
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong.', 'add-class-to-elementor-image' ), '1.3.2' );
	}

    /**
	 * Constructor
	 *
	 * Private method for prevent instance outsite the class.
	 *
	 * @since 1.0
	 *
	 * @access private
	 */
	private function __construct() {
        add_action( 'plugins_loaded', [ $this, 'add_elementor_mod' ] );
	}

    /**
	 * Init Elemento mods
	 *
	 * Add Elementor mods if plugins exist and is loaded.
	 *
	 * @since 1.0
	 *
	 * @access public
	 */
    public function add_elementor_mod() {
        if ( ! did_action( 'elementor/loaded' ) ) {
			return;
		}

		add_action( 'elementor/element/image/section_image/before_section_end', [ $this, 'add_class_control' ], 10, 2 );
		add_filter( 'elementor/image_size/get_attachment_image_html', [ $this, 'add_custom_class' ], 10, 4 );
		add_action( 'elementor/element/image-box/section_image/before_section_end', [ $this, 'add_class_control' ], 10, 2 );
		add_action( 'wp_loaded', [ $this, 'add_elementor_pro_mod' ] );
	}

	/**
	 * Init Elementor PRO mods
	 *
	 * Add Elementor PRO mods if the plugin exist and is loaded.
	 *
	 * @since 1.0
	 *
	 * @access public
	 */
    public function add_elementor_pro_mod() {
		if ( ! did_action( 'elementor_pro/init' ) ) {
			return;
		}

		add_action( 'elementor/element/theme-post-featured-image/section_image/before_section_end', [ $this, 'add_class_control' ], 10, 2 );
	}

    /**
	 * Add class control
	 *
	 * Add custom control to image widget.
	 *
	 * @since 1.0
	 *
	 * @access public
	 */
    public function add_class_control( $image, $args) {
        $image->add_control(
            'cei_image_custom_class',
            [
                'label' => __( 'Custom Class', 'add-class-to-elementor-image' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __( 'your-custom-class-here', 'add-class-to-elementor-image' ),
            ]
        );
    }

    /**
	 * Add custom class
	 *
	 * Add custom class to HTML img.
	 *
	 * @since 1.0
	 *
	 * @access public
	 */
    public function add_custom_class( $html, $settings, $image_size_key, $image_key ) {
		if( ! isset( $settings['cei_image_custom_class'] ) && empty( $settings['cei_image_custom_class'] ) ) {
			return $html;
		}

        $attrClass = strpos( $html, "class=" );
		if ( $attrClass ) {
			return preg_replace( '/class="(.*)"/', 'class="' . $settings['cei_image_custom_class'] . ' \1"', $html );
		} else {
			return preg_replace( '/src="(.*)"/', 'class="' . $settings['cei_image_custom_class'] . '" src="\1"', $html );
		}
    }
}

ACEI_Init::instance();
